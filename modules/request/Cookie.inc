<?php
/**
 * Created by PhpStorm.
 * User: forzi
 * Date: 11.10.2016
 * Time: 11:05
 */

namespace stradivari\request;

use stradivari\class_array\ClassArray;

class Cookie extends ClassArray {
    protected $headers;

    public function __construct(Headers $headers) {
        parent::__construct();
        $this->headers = $headers;
        $this->init();
    }
    public function init(array $data = null) {
        $this->storage = [];
        $curData = $data !== null ? $data : $this->headers['cookie'];
        if ($curData) {
            $cookies = explode(';', $curData);
            foreach ($cookies as $val) {
                $val = trim($val);
                $val = explode('=', $val, 2);
                $this->storage[$val[0]] = $val[1];
            }
        }
        if ($data) {
            $this->headers['cookie'] = "{$this}";
        }
        return $this;
    }
    public function __toString() {
        $arr = [];
        foreach ($this->storage as $key => $val) {
            $arr[] = "{$key}={$val}";
        }
        return implode('; ', $arr);
    }

}
