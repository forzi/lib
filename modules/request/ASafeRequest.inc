<?php
/**
 * Created by PhpStorm.
 * User: forzi
 * Date: 12.10.2016
 * Time: 14:41
 */

namespace stradivari\request;

use stradivari\class_array\ClassArray;

abstract class ASafeRequest {
    protected $request;

    public function __construct($request) {
        $this->request = $request;
    }

    public function __get($name) {
        $value = $this->request->$name;
        if ($name == 'params') {
            return $value;
        }
        return $value instanceof ClassArray ? $value->toArray() : $value;
    }

    public function __set($name, $val) {
        if ($name == 'params') {
            $this->request->params = $val;
            return;
        }
        throw new exceptions\CanNotSet;
    }
}
