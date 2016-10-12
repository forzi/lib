<?php
/**
 * Created by PhpStorm.
 * User: forzi
 * Date: 09.10.2016
 * Time: 22:55
 */

namespace stradivari\request;


class ConsoleRequestOpts extends \stradivari\class_array\ClassArray {
    protected $optsStartSymbol;
    protected $generalStorage;
    protected $optsDelimiterSymbol = ['-' => ' ', '--' => '=', '/' => ':'];
    protected $map = [];

    public function __construct(ConsoleRequestArguments $data, $optsStartSymbol) {
        $this->generalStorage = $data;
        $this->optsStartSymbol = $optsStartSymbol;
        parent::__construct();
    }

    public function init(array $arguments = []) {
        $data = $this->generalStorage;
        $optsStartSymbol = $this->optsStartSymbol;
        $this->storage = [];
        $this->map = [];
        foreach ($data as $key => $val) {
            $nakedVal = ltrim($val, $optsStartSymbol);
            if (strlen($val) - strlen($nakedVal) == strlen($optsStartSymbol)) {
                $delimiterPos = null;
                $delimiter = null;
                foreach ($this->optsDelimiterSymbol as $currentDelimiter) {
                    $currentPos = strpos($nakedVal, $currentDelimiter);
                    if ($currentPos !== false && ($delimiterPos == null || $currentPos < $delimiterPos)) {
                        $delimiterPos = $currentPos;
                        $delimiter = $currentDelimiter;
                    }
                }
                if ($delimiter === null) {
                    $this->storage[$nakedVal] = true;
                    $this->map[$nakedVal] = $key;
                } else {
                    $arr = explode($delimiter, $nakedVal, 2);
                    $this->storage[$arr[0]] = stripslashes(trim($arr[1], '"'));
                    $this->map[$arr[0]] = $key;
                }
            }
        }
        if ($arguments) {
            $toDelete = array_diff_key($this->storage, $arguments);
            foreach ($toDelete as $key => $val) {
                $this->offsetUnset($key);
            }
            foreach ($arguments as $key => $val) {
                $this->offsetSet($key, $val);
            }
        }
        return $this;
    }

    public function offsetSet($key, $value) {
        parent::offsetSet($key, $value);
        if ($key === null || !isset($this->map[$key])) {
            if ($value === true) {
                $this->generalStorage[] = $this->optsStartSymbol . $key;
            } else {
                $this->generalStorage[] = $this->optsStartSymbol . $key . $this->optsDelimiterSymbol[$this->optsStartSymbol] . $value;
            }
        } else {
            if ($value === true) {
                $this->generalStorage[$this->map[$key]] = $this->optsStartSymbol . $key;
            } else {
                $this->generalStorage[$this->map[$key]] = $this->optsStartSymbol . $key . $this->optsDelimiterSymbol[$this->optsStartSymbol] . $value;
            }
        }
    }

    public function offsetUnset($key) {
        parent::offsetUnset($key);
        if ($key === null || !isset($this->map[$key])) {
            return;
        }
        unset($this->generalStorage[$this->map[$key]]);
    }

}
