<?php
/**
 * Created by PhpStorm.
 * User: forzi
 * Date: 12.10.2016
 * Time: 15:06
 */

namespace stradivari\singleton;

trait TSingleton {
    protected static $obj;

    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {
        throw new exceptions\SingletonException;
    }

    public static function getInstance() {
        if (!static::$obj) {
            static::$obj = new static;
        }
        return static::$obj;
    }

}