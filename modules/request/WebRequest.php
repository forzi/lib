<?php
/**
 * Created by PhpStorm.
 * User: forzi
 * Date: 07.10.2016
 * Time: 22:12
 */

namespace stradivari\request;

use stradivari\class_array\ClassArray;
use stradivari\date_time\exception\CanNotSet;
use stradivari\url\Url;

class WebRequest {
    protected $headers;
    protected $cookie;
    public $server;
    public $files;
    public $body;
    protected $url;
    protected $get;
    public $post = [];
    protected $request;
    protected $params;

    public function __construct() {
        $this->url = new Url;
        $this->headers = new Headers();
        $this->params = new ClassArray();
    }

    public function __get($name) {
        if ($name == 'request') {
            return array_merge($this->headers->cookie->toArray(), $this->url->get->toArray(), $this->post);
        }
        if ($name == 'extendedRequest') {
            return array_merge($this->headers->cookie->toArray(), $this->url->get->toArray(), $this->post, $this->params->toArray());
        }
        if ($name == 'get') {
            return $this->url->get;
        }
        if ($name == 'cookie') {
            return $this->headers->cookie;
        }
        return $this->$name;
    }

    public function __set($name, $value) {
        if ($name == 'request') {
            throw new CanNotSet;
        }
        if ($name == 'params') {
            $this->params->init($value);
            return;
        }
        if ($name == 'get') {
            $this->url->get = $value;
            return;
        }
        if ($name == 'url') {
            $this->url->url = "{$value}";
            return;
        }
        if ($name == 'headers') {
            if ($value instanceof ClassArray) {
                $value = $value->toArray();
            }
            $this->headers->init($value);
            return;
        }
        if ($name == 'cookie') {
            $this->headers->cookie = $value;
            return;
        }
        $this->$name = $value;
    }
}
