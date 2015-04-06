<?php
use JuriyPanasevich\BJobs\Interfaces\JobInterface;
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
     * @return \JuriyPanasevich\BJobs\Interfaces\JobInterface
     */
    public function pop() {
        return new QueueTest__Job();
    }

    /**
     * @param \JuriyPanasevich\BJobs\Interfaces\JobInterface $job
     * @return bool
     */
    public function remove(JobInterface $job) {
        return true;
    }
}

class QueueTest__Job extends Job {

    public function __invoke() {
        return true;
    }
}