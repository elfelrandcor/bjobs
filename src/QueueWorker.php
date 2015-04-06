<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace JuriyPanasevich\BJobs;

use JuriyPanasevich\BJobs\Exception\JobException;
use JuriyPanasevich\BJobs\Exception\WorkerException;

class QueueWorker {

    protected $name;
    protected $sleep;
    protected $maxTries;

    private $logger;

    /** @var Queue */
    private $queue;

    public function __construct(Queue $queue, $logger) {
        $this->setQueue($queue);
        $this->setLogger($logger);
    }

    public function run($name, $memory = 128, $sleep = 3, $maxTries = 0) {
        $this->setName($name);
        $this->setSleep($sleep);
        $this->setMaxTries($maxTries);
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
     * @return mixed
     */
    public function getSleep() {
        return $this->sleep;
    }

    /**
     * @param mixed $sleep
     */
    public function setSleep($sleep) {
        $this->sleep = $sleep;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->name = $name;
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
}