<?php
/**
 * Created by PhpStorm.
 * User: forzi
 * Date: 07.10.2016
 * Time: 22:12
 */

namespace stradivari\request;

use stradivari\class_array\ClassArray;
use stradivari\request\exceptions\CanNotSet;
use stradivari\request\exceptions\RequestException;

class ConsoleRequest {
    const SAVE_SPACE = '##space##';

    protected $commandLine = '';
    protected $arguments;
    protected $exec = '';
    protected $longOpts;
    protected $shortOpts;
    protected $slashOpts;
    protected $params;
    protected $request;

    protected $optsStartSymbol = ['--', '-', '\\'];
    protected $optsDelimiterSymbol = [' ', '=', ':'];

    public function __construct($command = '') {
        if (is_string($command)) {
            $this->commandLine = $command;
            $this->explodeCommandLine();
            $this->params = new ClassArray();
        } else {
            throw new RequestException();
        }
    }

    protected function explodeCommandLine() {
        $this->commandLine = trim(\stradivari\left_cut($this->commandLine, $this->exec));

        preg_match_all('/((?<!\\\\)\\".*?(?<!\\\\)\\")/', $this->commandLine, $matches);
        $this->commandLine = $this->saveSpaces($this->commandLine, $matches[0]);

        preg_match_all('/[-\/][a-zA-Z0-9]+( [^-\/]|=).+?( |$)/', $this->commandLine, $matches);
        $this->commandLine = $this->saveSpaces($this->commandLine, $matches[0]);

        $arguments = $this->commandLine ? explode(" ", $this->commandLine) : [];
        array_walk($arguments, function(&$val) {
            $val = str_replace(static::SAVE_SPACE, " ", $val);
        });

        $this->explodeArguments($arguments);
    }

    protected function saveSpaces($str, $matches) {
        if ($matches) {
            foreach ($matches as $match) {
                $match = trim($match);
                $toReplace = str_replace(" ", static::SAVE_SPACE, $match);
                $str = str_replace($match, $toReplace, $str);
            }
        }
        return $str;
    }

    protected function explodeArguments($arguments) {
        $this->arguments = $this->arguments ? $this->arguments->init($arguments) : new ConsoleRequestArguments($this, $arguments);
        $this->argumentsChanged();
    }

    protected function explodeArgument($argument) {
        $hasDelimiter = false;
        foreach ($this->optsDelimiterSymbol as $search) {
            if (strpos($argument, $search) !== false) {
                $hasDelimiter = true;
                break;
            }
        }
        if (!$hasDelimiter) {
            return $argument;
        }
        foreach ($this->optsStartSymbol as $symbol) {
            if (strpos($argument, $symbol) === false) {
                continue;
            }
            $delimiterPos = null;
            $delimiter = null;
            foreach ($this->optsDelimiterSymbol as $currentDelimiter) {
                $currentPos = strpos($argument, $currentDelimiter);
                if ($currentPos !== false && ($delimiterPos == null || $currentPos < $delimiterPos)) {
                    $delimiterPos = $currentPos;
                    $delimiter = $currentDelimiter;
                }
            }
            $arr = explode($delimiter, $argument, 2);
            return $arr[0] . $delimiter . '"' . addslashes(stripslashes(trim($arr[1], '"'))) . '"';
        }
        return '"' . addslashes(stripslashes(trim($argument, '"'))) . '"';
    }

    protected function implodeCommandLine() {
        $this->commandLine = $this->exec;
        $this->commandLine .= ' ' . implode(' ', $this->arguments->toArray());
        $this->commandLine = trim($this->commandLine);
    }

    public function argumentsChanged() {
        foreach ($this->arguments as $key => $val) {
            $this->arguments[$key] = $this->explodeArgument($val);
        }
        $this->implodeCommandLine();

        $this->shortOpts = $this->shortOpts ? $this->shortOpts->init() : new ConsoleRequestOpts($this->arguments, '-');
        $this->longOpts = $this->longOpts ? $this->longOpts->init() : new ConsoleRequestOpts($this->arguments, '--');
        $this->slashOpts = $this->slashOpts ? $this->slashOpts->init() : new ConsoleRequestOpts($this->arguments, '/');
    }

    public function __get($name) {
        if ($name == 'request') {
            return array_merge($this->slashOpts->toArray(), $this->shortOpts->toArray(), $this->longOpts->toArray());
        }
        if ($name == 'extendedRequest') {
            return array_merge($this->slashOpts->toArray(), $this->shortOpts->toArray(), $this->longOpts->toArray(), $this->params->toArray());
        }
        return $this->$name;
    }

    public function __set($name, $value) {
        if ($name == 'commandLine') {
            $this->commandLine = $value;
            $this->explodeCommandLine();
            return;
        }
        if ($name == 'exec') {
            $this->exec = $value;
            $this->implodeCommandLine();
            return;
        }
        if ($name == 'arguments') {
            $this->explodeArguments($value);
            return;
        }
        if ($name == 'params') {
            $this->params->init($value);
            return;
        }
        if (in_array($name, ['shortOpts', 'longOpts', 'slashOpts'])) {
            $this->$name->init($value);
            return;
        }
        throw new CanNotSet;
    }

    public function __toString() {
        return $this->commandLine;
    }

    public function toString() {
        return $this->commandLine;
    }
}
