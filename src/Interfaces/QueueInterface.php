<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace JuriyPanasevich\BJobs\Interfaces;


interface QueueInterface {

    /**
     * @param JobInterface $job
     * @return boolean
     */
    public function push(JobInterface $job);

    /**
     * @return JobInterface
     */
    public function pop();

    /**
     * @param JobInterface $job
     * @return bool
     */
    public function remove(JobInterface $job);

}