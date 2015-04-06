<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace JuriyPanasevich\BJobs\Interfaces;


interface JobInterface {

    public function __invoke();
}