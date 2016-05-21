<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace JuriyPanasevich\BJobs\Interfaces;


interface QueueStorageInterface {
    
    public function serialize(JobInterface $job);
    
    public function unserialize($raw) : JobInterface;
}