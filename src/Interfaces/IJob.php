<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace JuriyPanasevich\BJobs\Interfaces;


interface IJob {
    /**
     * @return IJob
     */
    public function find();

    /**
     * @return boolean
     */
    public function save();

    /**
     * @return boolean
     */
    public function delete();
}