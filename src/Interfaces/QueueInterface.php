<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace JuriyPanasevich\BJobs\Interfaces;


interface QueueInterface {
    
    public function push(JobInterface $job) : bool ;
    
    public function pop() : JobInterface;
}