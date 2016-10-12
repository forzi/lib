<?php
/**
 * Created by PhpStorm.
 * User: forzi
 * Date: 11.10.2016
 * Time: 11:01
 */

namespace stradivari\request;

use stradivari\class_array\ClassArray;

class Headers extends ClassArray {
    protected $cookie;

    public function __construct(array $data = []) {
        parent::__construct();
        $this->cookie = new Cookie($this);
        $this->init($data);
    }

    public function init(array $data = []) {
        $this->storage = [];
        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $arr = explode(":", $val, 2);
                $key = $arr[0];
                $val = trim($arr[1]);
            }
            $key = strtolower($key);
            $this->storage[$key] = $val;
        }
        $this->cookie->init();
        return $this;
    }

    public function offsetGet($key) {
        return parent::offsetGet(strtolower($key));
    }

    public function offsetSet($key, $val) {
        parent::offsetSet(strtolower($key), $val);
        $this->cookie->init();
    }

    public function offsetUnset($key) {
        parent::offsetunset(strtolower($key));
        $this->cookie->init();
    }

    public function __get($name) {
        if ($name == 'cookie') {
            return $this->cookie;
        }
        return parent::__get($name);
    }

    public function __set($name, $value) {
        if ($value instanceof ClassArray) {
            $value = $value->toArray();
        }
        $this->cookie->init($value);
    }
}
