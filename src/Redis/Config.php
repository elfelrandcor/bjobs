<?php
/**
 * @author Juriy Panasevich <panasevich@worksolutions.ru>
 */

namespace JuriyPanasevich\BJobs\Redis;


use JuriyPanasevich\BJobs\ParamsObject;

class Config extends ParamsObject {

    public function __construct($name) {
        $this->setParams([
            'name' => $name,
            'scheme' => 'tcp',
            'host' => '127.0.0.1',
            'port' => 6379,
            'options' => [],
        ]);
    }

    public function getName() : string {
        return $this->getParam('name');
    }

    public function setName(string $name) {
        $this->addParam('name', $name);
        return $this;
    }

    public function getScheme() : string {
        return $this->getParam('scheme');
    }

    public function setScheme(string $scheme) {
        $this->addParam('scheme', $scheme);
        return $this;
    }
    
    public function getHost() : string {
        return $this->getParam('host');
    }

    public function setHost(string $host) {
        $this->addParam('host', $host);
        return $this;
    }

    public function getPort() : integer {
        return $this->getParam('port');
    }
    
    public function setPort(integer $port) {
        $this->addParam('port', $port);
        return $this;
    }
    
    public function getOptions() : array {
        return $this->getParam('options');
    }
    
    public function setOptions(array $options) {
        $this->addParam('options', $options);
        return $this;
    }
}