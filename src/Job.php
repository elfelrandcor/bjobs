<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace JuriyPanasevich\BJobs;


use JuriyPanasevich\BJobs\Interfaces\JobInterface;

abstract class Job implements JobInterface {

    /** @var integer */
    protected $tries;

    abstract public function __invoke();

    /**
     * @return $this
     */
    public function incrementTries() {
        $this->tries++;
        return $this;
    }

    public function getTries() {
        return $this->tries;
    }
}