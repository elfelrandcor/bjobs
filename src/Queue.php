<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace JuriyPanasevich\BJobs;

use JuriyPanasevich\BJobs\Interfaces\QueueInterface;

abstract class Queue implements QueueInterface {
    protected $name, $data;

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