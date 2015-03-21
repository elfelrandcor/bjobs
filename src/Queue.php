<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace JuriyPanasevich\BJobs;

use JuriyPanasevich\BJobs\Interfaces\IJob;
use JuriyPanasevich\BJobs\Interfaces\IQueue;

abstract class Queue implements IQueue {

    public function pushOn($name, $job, $data = null) {
        return $this->push($job, $data, $name);
    }

    /**
     * @param IJob $job
     * @param mixed $data
     * @param string $name
     * @return boolean
     */
    public function push($job, $data = null, $name = null) {
        return $job->save();
    }
}