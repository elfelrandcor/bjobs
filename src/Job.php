<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace JuriyPanasevich\BJobs;


use JuriyPanasevich\BJobs\Interfaces\IJob;

abstract class Job implements IJob {

    abstract public function __invoke();
}