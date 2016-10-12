<?php
/**
 * Created by PhpStorm.
 * User: forzi
 * Date: 10.10.2016
 * Time: 21:09
 */

namespace stradivari\url;

use stradivari\class_array\ClassArray;

class GetParams extends ClassArray {
    protected $url;

    public function __construct(Url $url = null) {
        $this->url = $url;
        $this->init();
    }

    public function init() {
        parse_str($this->url->query, $data);
        return parent::init($data);
    }

    public function offsetSet($key, $value) {
        parent::offsetSet($key, $value);
        if ($this->url) {
            $data = $this->toArray();
            $this->url->query = http_build_query($data);
        }
    }

    public function offsetUnset($key) {
        parent::offsetUnset($key);
        if ($this->url) {
            $data = $this->toArray();
            $this->url->query = http_build_query($data);
        }
    }
}
