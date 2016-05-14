<?php
/**
 * @author Juriy Panasevich <panasevich@worksolutions.ru>
 */

namespace JuriyPanasevich\BJobs\Redis;

use JuriyPanasevich\BJobs\Exception\QueueException;
use JuriyPanasevich\BJobs\Interfaces\JobInterface;
use JuriyPanasevich\BJobs\Queue;
use Predis\Client;

class RedisQueue extends Queue {

    protected $client;

    public function __construct(Config $config) {
        $this->client = new Client([
            'scheme' => $config->getScheme(),
            'host'   => $config->getHost(),
            'port'   => $config->getPort(),
        ], $config->getOptions());
        $this->setName($config->getName());
    }

    public function push(JobInterface $job) : bool {
        if (!$this->getName()) {
            throw new QueueException('Cannot push into nameless queue');
        }
        $value = $this->serializeJobToArray($job);
        $value = json_encode($value);
        return (boolean)$this->client->rpush($this->getName(), [$value]);
    }

    public function pop() : JobInterface {
        $stored = $this->client->lpop($this->getName());
        $stored = json_decode($stored, true);

        return $this->restoreJob($stored);
    }
}