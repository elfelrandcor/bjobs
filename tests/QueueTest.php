<?php
use JuriyPanasevich\BJobs\Job;
use JuriyPanasevich\BJobs\Queue;

/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

class QueueTest extends PHPUnit_Framework_TestCase {

    public function testPush() {
        $queue = new QueueTest__Queue();
        $this->assertEquals(true, $queue->pushOn('test', new QueueTest__Job()));

        $this->markTestIncomplete();
    }

}

class QueueTest__Queue extends Queue {

    /**
     * @return \JuriyPanasevich\BJobs\Interfaces\IQueue
     */
    public function find() {
        return $this;
    }

    /**
     * @return boolean
     */
    public function save() {
        return true;
    }

    /**
     * @return boolean
     */
    public function delete() {
        return true;
    }

    /**
     * @param callable $job
     * @param mixed $data
     * @param mixed $name
     * @return boolean
     */
    public function push($job, $data = null, $name = null) {
        return true;
    }
}

class QueueTest__Job extends Job {

    public function __invoke() {
        return true;
    }
}