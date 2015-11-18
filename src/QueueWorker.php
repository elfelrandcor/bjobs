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
    protected $sleep;
    protected $maxTries;

    /** @var Logger */
    private $logger;

    /** @var Queue */
    private $queue;

    public function __construct(Queue $queue, Logger $logger) {
        $this->setQueue($queue);
        $this->setLogger($logger);
    }

    public function run($delay, $memory, $sleep, $maxTries) {
        $this->setMaxTries($maxTries);
        $this->setDelay($delay);

        while(true) {
            $job = false;
            try {
                if ($job = $this->pop()) {
                    $this->process($job);
                }
            } catch (\Exception $e) {
                $this->logError($e->getMessage());
            }
            if ($this->memoryExceeded($memory)) {
                $this->stop();
            }
            if (!$job) {
                sleep($sleep);
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

    /**
     * @param Job $job
     * @return bool
     */
    public function process(Job $job) {
        try {
            $job();
        } catch (JobException $e) {
            $this->logError($e->getMessage());
            if (!$maxTries = $this->getMaxTries()) {
                return true;
            }
            $job->incrementTries();
            if ($job->getTries() > $maxTries) {
                $this->logError(sprintf('Превышено количество попыток выполнения %s', $maxTries));
                return false;
            }
            $date = $job->getDateRelease();
            $date->add(new \DateInterval(sprintf('PT%sS', $this->getDelay())));
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

    /**
     * @return mixed
     */
    public function getMaxTries() {
        return $this->maxTries;
    }

    /**
     * @param mixed $maxTries
     */
    public function setMaxTries($maxTries) {
        $this->maxTries = $maxTries;
    }

    /**
     * @param mixed $logger
     */
    private function setLogger($logger) {
        $this->logger = $logger;
    }

    /**
     * @param Queue $queue
     */
    private function setQueue($queue) {
        $this->queue = $queue;
    }

    protected function getQueue() {
        return $this->queue;
    }

    protected function getLogger() {
        return $this->logger;
    }

    private function setDelay($delay) {
        $this->delay = $delay;
    }

    /**
     * @return integer
     */
    public function getDelay() {
        return $this->delay;
    }
}