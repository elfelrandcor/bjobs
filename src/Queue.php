<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace JuriyPanasevich\BJobs;

use JuriyPanasevich\BJobs\Interfaces\QueueInterface;

abstract class Queue implements QueueInterface {
    protected $name, $data, $storage;

    public function pushOn(string $name, Job $job, $data = []) : bool {
        $this->setName($name)
            ->setData($data);
        return $this->push($job);
    }

    public function getName() : string {
        return $this->name;
    }

    public function setName(string $name) {
        $this->name = $name;
        return $this;
    }

    public function getData() : array {
        return $this->data;
    }

    public function setData(array $data) {
        $this->data = $data;
        return $this;
    }

    
}