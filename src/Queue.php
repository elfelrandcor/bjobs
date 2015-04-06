<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace JuriyPanasevich\BJobs;

use JuriyPanasevich\BJobs\Exception\QueueException;
use JuriyPanasevich\BJobs\Interfaces\JobInterface;
use JuriyPanasevich\BJobs\Interfaces\QueueInterface;

abstract class Queue implements QueueInterface {
    protected $name;
    protected $data;

    /**
     * @param string $name
     * @param Job $job
     * @param mixed $data
     * @return bool
     */
    public function pushOn($name, Job $job, $data = null) {
        $this->setName($name)
            ->setData($data);
        return $this->push($job);
    }

    /**
     * @param JobInterface $job
     * @return bool
     * @throws QueueException
     */
    public function push(JobInterface $job) {
        if (!$this->getName()) {
            throw new QueueException('Cannot push into nameless queue');
        }
        return true;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setData($data) {
        $this->data = $data;
        return $this;
    }
}