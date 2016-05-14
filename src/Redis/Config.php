<?php
/**
 * @author Juriy Panasevich <panasevich@worksolutions.ru>
 */

namespace JuriyPanasevich\BJobs\Redis;


class Config {

    protected
        $name,
        $scheme = 'tcp',
        $host = '127.0.0.1',
        $port = 6379,
        $options = []
    ;

    public function getName() : string {
        return $this->name;
    }


    public function setName(string $name) {
        $this->name = $name;
        return $this;
    }

    public function getScheme() : string {
        return $this->scheme;
    }

    public function setScheme(string $scheme) {
        $this->scheme = $scheme;
        return $this;
    }

    /**
     * @return string
     */
    public function getHost() : string {
        return $this->host;
    }

    public function setHost(string $host) {
        $this->host = $host;
        return $this;
    }

    public function getPort() : integer {
        return $this->port;
    }
    
    public function setPort(integer $port) {
        $this->port = $port;
        return $this;
    }
    
    public function getOptions() : array {
        return $this->options;
    }
    
    public function setOptions(array $options) {
        $this->options = $options;
    }
}