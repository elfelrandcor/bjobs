<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace JuriyPanasevich\BJobs;

use JuriyPanasevich\BJobs\Exception\JobException;
use JuriyPanasevich\BJobs\Exception\WorkerException;
use JuriyPanasevich\Logger\Logger;

class QueueWorker {

    protected $delay;
    protected $maxTries;
    /** @var  QueueRunParams */
    protected $params;

    /** @var Logger */
    private $logger;

    /** @var Queue */
    private $queue;

    public function __construct(Queue $queue, Logger $logger) {
        $this->setQueue($queue);
        $this->setLogger($logger);
    }

    public function run(QueueRunParams $params) {
        $this->setParams($params);

        while(true) {
            $job = false;
            try {
                if ($job = $this->pop()) {
                    $this->process($job);
                }
            } catch (\Exception $e) {
                $this->logError($e->getMessage());
            }
            if ($this->memoryExceeded($this->params->getMemory())) {
                $this->stop();
            }
            if (!$job) {
                sleep($this->params->getSleep());
            }
        }
    }

    /**
     * @return bool|Job
     * @throws WorkerException
     */
    public function pop() {
        /** @var Queue $queue */
        $queue = $this->getQueue();
        /** @var Job $job */
        if (!$job = $queue->pop()) {
            return false;
        }
        if (!is_callable($job)) {
            throw new WorkerException('Работа не может быть выполнена, объект не callable');
        }
        return $job;
    }

    public function process(Job $job) : bool {
        try {
            $job();
        } catch (JobException $e) {
            $this->logError($e->getMessage());
            if (!$maxTries = $this->params->getMaxTries()) {
                return true;
            }
            $job->incrementTries();
            if ($job->getTries() > $maxTries) {
                $this->logError(sprintf('Превышено количество попыток выполнения %s', $maxTries));
                return false;
            }
            $date = $job->getDateRelease();
            $date->add(new \DateInterval(sprintf('PT%sS', $this->params->getDelay())));
            $job->setDateRelease($date);
            if ($this->getQueue()->push($job)) {
                $this->log('Задача поставлена в очередь на повторение');
            }
            return true;
        }
        $this->logDone(sprintf('Задача была завершена'));
        return true;
    }

    public function memoryExceeded($memoryLimit) {
        return (memory_get_usage() / 1024 / 1024) >= $memoryLimit;
    }

    public function stop() {
        $this->log('Stopped');
        die;
    }

    public function log($message) {
        $this->getLogger()->log($message);
    }

    public function logError($message) {
        $this->getLogger()->log($message);
    }

    public function logDone($message) {
        $this->getLogger()->log($message);
    }

    private function setLogger(Logger $logger) {
        $this->logger = $logger;
        return $this;
    }

    protected function getLogger() : Logger {
        return $this->logger;
    }

    private function setQueue(Queue $queue) {
        $this->queue = $queue;
        return $this;
    }

    protected function getQueue() : Queue {
        return $this->queue;
    }

    public function setParams(QueueRunParams $params) {
        $this->params = $params;
        return $this;
    }
}