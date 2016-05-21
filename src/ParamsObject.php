<?php
/**
 * @author Juriy Panasevich <juriy.panasevich@gmail.com>
 */

namespace JuriyPanasevich\BJobs;


class ParamsObject {

    protected $_params = [];

    public function getParams() : array {
        return $this->_params;
    }

    public function setParams(array $params) {
        $this->_params = $params;
        return $this;
    }

    public function addParam(string $name, $value = null) {
        $this->_params[$name] = $value;
        return $this;
    }

    public function getParam($name) {
        return $this->_params[$name];
    }
}