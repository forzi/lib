<?php
/**
 * Created by PhpStorm.
 * User: forzi
 * Date: 09.10.2016
 * Time: 22:55
 */

namespace stradivari\request;


class ConsoleRequestArguments extends \stradivari\class_array\ClassArray {
    protected $consoleRequest;

    public function __construct(ConsoleRequest $consoleRequest, $data = null) {
        $this->consoleRequest = $consoleRequest;
        $this->init($data);
    }

    public function init($data) {
        $this->storage = [];
        parent::__construct($data);
        return $this;
    }

    public function offsetSet($key, $value) {
        parent::offsetSet($key, $value);
        $backtrace = debug_backtrace();
        $calledClass = isset($backtrace[1]['class']) ? $backtrace[1]['class'] : null;
        if ($calledClass != get_class($this->consoleRequest)) {
            $this->consoleRequest->argumentsChanged();
        }
    }

    public function offsetUnset($key) {
        parent::offsetUnset($key);
        $backtrace = debug_backtrace();
        $calledClass = isset($backtrace[1]['class']) ? $backtrace[1]['class'] : null;
        if ($calledClass != get_class($this->consoleRequest)) {
            $this->consoleRequest->argumentsChanged();
        }
    }

} 