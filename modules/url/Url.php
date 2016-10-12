<?
namespace stradivari\url;

use stradivari\class_array\ClassArray;

class Url {
    const DEFAULT_SCHEME = 'http';
    const ENCODE_URL = 1;
    const DECODE_URL = -1;

    protected $scheme;
    protected $host;
    protected $port;
    protected $user;
    protected $pass;
    protected $path;
    protected $query;
    protected $fragment;
    protected $part;

    protected $url;

    protected $get;

    public function __construct($url = '') {
        $this->parseArguments(parse_url($url));
    }

    protected function parseArguments(array $arguments) {
        $this->scheme   = isset($arguments['scheme']) && $arguments['scheme'] ? $arguments['scheme'] : '';
        $this->host     = isset($arguments['host']) && $arguments['host'] ? $arguments['host'] : '';
        $this->port     = isset($arguments['port']) && $arguments['port'] ? $arguments['port'] : '';
        $this->user     = isset($arguments['user']) && $arguments['user'] ? $arguments['user'] : '';
        $this->pass     = isset($arguments['pass']) && $arguments['pass'] ? $arguments['pass']  : '';
        $this->path     = isset($arguments['path']) && $arguments['path'] ? $arguments['path'] : '';
        $this->query    = isset($arguments['query']) && $arguments['query'] ? $arguments['query'] : '';
        $this->fragment = isset($arguments['fragment']) && $arguments['fragment'] ? $arguments['fragment'] : '';
        $this->part     = $this->query ? $this->path . '?' . $this->query : $this->path;
        $this->get = $this->get ? $this->get->init() : new GetParams($this);
    }

    protected function getUrl($encode = 0) {
        $scheme         = $this->scheme ? $this->scheme . '://' : '';

        $host           = $this->host;

        $port           = $this->port ? ':' . $this->port : '';

        $user           = $this->user;
        $user           = $this->textEncode($user, [], $encode);

        $pass           = $this->pass;
        $pass           = $this->textEncode($pass, [], $encode);
        $pass           = $pass ? ':' . $pass : '';
        $pass           = ($this->user || $pass) ? $pass . '@' : '';

        $path           = $this->path;
        $path           = $this->textEncode($path, ['/'], $encode);

        $query          = $this->query;
        $query          = $this->textEncode($query, ['=', '&'], $encode);
        $query          = $query ? '?' . $query : '';

        $fragment       = $this->fragment;
        $fragment       = $this->textEncode($fragment, [], $encode);
        $fragment       = $fragment ? '#' . $fragment : '';

        return $scheme . $user . $pass . $host . $port . $path . $query . $fragment;
    }

    public function __get($name) {
        if ($name == 'url') {
            return $this->getUrl();
        }
        return $this->$name;
    }

    public function __set($name, $value) {
        if ($name == 'url') {
            $this->parseArguments(parse_url($value));
            return;
        }
        if ($name == 'part') {
            $questionPosition = strpos($value, '?');
            if ($questionPosition === false) {
                $this->path = $value;
                $this->query = '';
            } else {
                $this->path = substr($value, 0, $questionPosition);
                $this->query = substr($value, $questionPosition + 1);
            }
        }
        if ($name == 'get') {
            if ($value instanceof ClassArray) {
                $value = $value->toArray();
            }
            $this->__set('query', http_build_query($value));
            return;
        } else {
            $this->$name = $value;
        }
        $this->part  = $this->query ? $this->path . '?' . $this->query : $this->path;
        $this->get = $this->get ? $this->get->init() : new GetParams($this);
    }

    public function __toString() {
        return $this->getUrl();
    }

    public function toString() {
        return $this->getUrl();
    }

    public function toArray() {
        return parse_url($this->getUrl());
    }

    protected function textEncode($text, $ignore = [], $urlEncode = 0) {
        if ($urlEncode == 0) {
            return $text;
        }
        foreach ($ignore as $key => $value) {
            $text = str_replace($value, "var__{$key}__var", $text);
        }
        $newText = urldecode($text);
        for ( ; strlen($newText) != strlen($text); ) {
            $text = $newText;
            $newText = urldecode($text);
        }
        $text = $urlEncode == static::ENCODE_URL ? rawurlencode($text) : $text;
        foreach ($ignore as $key => $value) {
            $text = str_replace("var__{$key}__var", $value, $text);
        }
        return $text;
    }

    public function urlEncode() {
        $this->__set('url', $this->getUrl(static::ENCODE_URL));
        return $this;
    }

    public function urlDecode() {
        $this->__set('url', $this->getUrl(static::DECODE_URL));
        return $this;
    }

}
