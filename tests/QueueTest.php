<?php
require_once __DIR__ . '/../vendor/autoload.php';

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
        $this->assertEquals(true, $queue->pushOn('test', new QueueTest__Job()));

        $this->assertEquals(2, count($queue->jobs));
    }

    public function testStoreScalarParameters() {
        $queue = new QueueTest__Queue();
        $job = new QueueTest__Job(1, 'string', 3);
        $this->assertEquals(true, $queue->pushOn('test', $job));

        /** @var QueueTest__Job $job */
        $job = $queue->pop();
        $this->assertEquals(1, $job->getPublic());
        $this->assertEquals('string', $job->getProtected());
        $this->assertEquals(null, $job->getPrivate()); //приватные не сохраняем
    }

    public function testStoreDTO() {
        $queue = new QueueTest__Queue();
        $params = new \JuriyPanasevich\BJobs\ParamsObject();
        $params->addParam('test', 'test')->addParam('test2', true);

        $job = new QueueTest__Job($params);
        $this->assertEquals(true, $queue->pushOn('test', $job));

        /** @var QueueTest__Job $job */
        $job = $queue->pop();
        /** @var \JuriyPanasevich\BJobs\ParamsObject $restored */
        $restored = $job->getPublic();
        $this->assertTrue($restored instanceof \JuriyPanasevich\BJobs\ParamsObject);
        $this->assertEquals('test', $restored->getParam('test'));
        $this->assertEquals(true, $restored->getParam('test2'));
    }

    public function testExecute() {
        $queue = new QueueTest__Queue();
        $params = new \JuriyPanasevich\BJobs\ParamsObject();
        $params->addParam('test', 'test')->addParam('test2', true);

        $job = new QueueTest__Job($params);
        $queue->pushOn('test', $job);
        $job = $queue->pop();

        $this->assertEquals(true, $job());
    }
}

class QueueTest__Queue extends Queue {

    public $jobs = [];
    protected $storage;

    public function __construct() {
        $this->storage = new QueueTest__Storage(new \JuriyPanasevich\BJobs\Redis\Config('test'));
    }

    public function push(JobInterface $job) : bool {
        $this->jobs[] = $this->storage->serialize($job);
        return true;
    }

    public function pop() : JobInterface {
        $job = array_pop($this->jobs);
        return $this->storage->unserialize($job);
    }
}

class QueueTest__Job extends Job {

    public $public;
    protected $protected;
    private $private;

    public function __construct($public = null, $protected = null, $private = null) {
        $this->public = $public;
        $this->protected = $protected;
        $this->private = $private;
    }

    public function __invoke() {
        return true;
    }

    public function getDateRelease() {
        return new DateTime();
    }

    public function setDateRelease($date) {
        return $this;
    }

    /**
     * @return null
     */
    public function getProtected() {
        return $this->protected;
    }

    /**
     * @return null
     */
    public function getPrivate() {
        return $this->private;
    }

    /**
     * @return null
     */
    public function getPublic() {
        return $this->public;
    }
}

class QueueTest__Storage extends \JuriyPanasevich\BJobs\Redis\Storage {

}