<?php
/**
 * @author Juriy Panasevich <panasevich@worksolutions.ru>
 */

namespace JuriyPanasevich\BJobs\Redis;

use JuriyPanasevich\BJobs\Exception\QueueException;
use JuriyPanasevich\BJobs\Interfaces\JobInterface;
use JuriyPanasevich\BJobs\Interfaces\QueueStorageInterface;
use JuriyPanasevich\BJobs\ParamsObject;
use Predis\Client;
use ReflectionClass;

class Storage implements QueueStorageInterface {

    protected $client;

    public function __construct(Config $config) {
        $this->client = new Client([
            'scheme' => $config->getScheme(),
            'host'   => $config->getHost(),
            'port'   => $config->getPort(),
        ], $config->getOptions());
    }

    public function store($name, $value) {
        return (boolean)$this->client->rpush($name, [$value]);
    }

    public function get($name) {
        return $this->client->lpop($name);
    }

    public function serialize(JobInterface $job) : string {
        $reflectionProperties = (new ReflectionClass($job))->getProperties();
        $props = [];
        foreach ($reflectionProperties as $reflectionProperty) {
            if ($reflectionProperty->isPrivate()) {
                continue;
            }
            $reflectionProperty->setAccessible(true);
            $paramValue = $reflectionProperty->getValue($job);
            if (is_null($paramValue) || $paramValue instanceof \Closure) {
                continue;
            }
            if (is_scalar($paramValue)) {
                $props[$reflectionProperty->getName()] = $paramValue;
            }
            if ($paramValue instanceof ParamsObject) {
                $props[$reflectionProperty->getName()] = ['className' => get_class($paramValue), 'serialized' => serialize($paramValue)];
            }
            if (!$props[$reflectionProperty->getName()]) {
                throw new QueueException(sprintf('Передан необрабатываемый параметр `%s`', $reflectionProperty->getName()));
            }
        }
        return json_encode(['className' => get_class($job), 'props' => $props]);
    }

    public function unserialize($raw) : JobInterface {
        if (!$raw) {
            throw new QueueException('Нет данных для восстановления задачи');
        }
        $raw = json_decode($raw, true);
        $class = new ReflectionClass($raw['className']);
        $props = $raw['props'];
        $args = [];
        if (($constructor = $class->getConstructor()) && ($parameters = $constructor->getParameters())) {
            $args = array_map(function (\ReflectionParameter $p) use ($props) {
                $value = false;
                if (isset($props[$p->getName()])) {
                    $value = $props[$p->getName()];

                    if (is_array($value)
                        && array_key_exists('className', $value)
                        && (is_subclass_of($value['className'], 'JuriyPanasevich\BJobs\ParamsObject') || $value['className'] === 'JuriyPanasevich\BJobs\ParamsObject')) {

                        if (!array_key_exists('serialized', $value)) {
                            throw new QueueException('Нет данных сериализации DTO-объекта');
                        }
                        /** @var ParamsObject $object */
                        $object = unserialize($value['serialized']);
                        $value = $object;
                    }
                }
                return $value ?: ($p->isDefaultValueAvailable() ? $p->getDefaultValue() : null);
            }, $parameters);
        }
        return $class->newInstanceArgs($args);
    }
}