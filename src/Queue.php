<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace JuriyPanasevich\BJobs;

use JuriyPanasevich\BJobs\Exception\QueueException;
use JuriyPanasevich\BJobs\Interfaces\JobInterface;
use JuriyPanasevich\BJobs\Interfaces\QueueInterface;
use ReflectionClass;

abstract class Queue implements QueueInterface {
    protected $name, $data;

    public function pushOn(string $name, Job $job, $data = []) : bool {
        $this->setName($name)
            ->setData($data);
        return $this->push($job);
    }

    public function getName() : string {
        return $this->name;
    }

    public function setName(string $name) {
        $this->name = $name;
        return $this;
    }

    public function getData() : array {
        return $this->data;
    }

    public function setData(array $data) {
        $this->data = $data;
        return $this;
    }

    public function serializeJobToArray(JobInterface $job) : array {
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
            if ($paramValue instanceof QueueJobParamsObject) {
                $props[$reflectionProperty->getName()] = ['className' => get_class($paramValue), 'serialized' => serialize($paramValue)];
            }
            if (!$props[$reflectionProperty->getName()]) {
                throw new QueueException(sprintf('Передан необрабатываемый параметр `%s`', $reflectionProperty->getName()));
            }
        }
        return ['className' => get_class($job), 'props' => $props];
    }

    public function restoreJob(array $params) : JobInterface {
        if (!$params) {
            throw new QueueException('Нет данных для восстановления задачи');
        }
        $class = new ReflectionClass($params['className']);
        $props = $params['props'];
        $args = [];
        if (($constructor = $class->getConstructor()) && ($parameters = $constructor->getParameters())) {
            $args = array_map(function (\ReflectionParameter $p) use ($props) {
                $value = false;
                if (isset($props[$p->getName()])) {
                    $value = $props[$p->getName()];

                    if (is_array($value)
                        && array_key_exists('className', $value)
                        && (is_subclass_of($value['className'], 'JuriyPanasevich\BJobs\QueueJobParamsObject') || $value['className'] === 'JuriyPanasevich\BJobs\QueueJobParamsObject')) {

                        if (!array_key_exists('serialized', $value)) {
                            throw new QueueException('Нет данных сериализации DTO-объекта');
                        }
                        /** @var QueueJobParamsObject $object */
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