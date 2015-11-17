<?php
use JuriyPanasevich\BJobs\Interfaces\JobInterface;
use JuriyPanasevich\BJobs\Job;
use JuriyPanasevich\BJobs\Journal\QueueJournal;
use JuriyPanasevich\BJobs\Queue;
use JuriyPanasevich\BJobs\QueueWorker;
use JuriyPanasevich\Logger\Logger;

/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

class QueueTest extends PHPUnit_Framework_TestCase {

    public function testPush() {
        $queue = new QueueTest__Queue();
        $this->assertEquals(true, $queue->pushOn('test', new QueueTest__Job()));

        $queue = new QueueTest__Queue('test');

        $journal = new QueueJournal();
        $journal->setEntity($queue);

        $logger = new Logger($journal);

//        $worker = new QueueWorker($queue, $logger);
//        $worker->run();

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

    /**
     * @param JobInterface $job
     * @return boolean
     */
    public function push(JobInterface $job) {
        return true;
    }
}

class QueueTest__Job extends Job {

    public function __invoke() {
        return true;
    }

    public function getDateRelease() {
        return new DateTime();
    }

    public function setDateRelease($date) {
        return $this;
    }
}