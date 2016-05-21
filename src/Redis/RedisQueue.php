<?php
/**
 * @author Juriy Panasevich <panasevich@worksolutions.ru>
 */

namespace JuriyPanasevich\BJobs\Redis;

use JuriyPanasevich\BJobs\Exception\QueueException;
use JuriyPanasevich\BJobs\Interfaces\JobInterface;
use JuriyPanasevich\BJobs\Queue;

class RedisQueue extends Queue {

    protected $storage;

    public function __construct(Config $config) {
        $this->storage = new Storage($config);
        $this->setName($config->getName());
    }

    public function push(JobInterface $job) : bool {
        if (!$this->getName()) {
            throw new QueueException('Cannot push into nameless queue');
        }
        return $this->storage->store($this->getName(), $this->storage->serialize($job));
    }

    public function pop() : JobInterface {
        return $this->storage->unserialize($this->storage->get($this->getName()));
    }
}