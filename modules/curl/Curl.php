<?php
namespace common\helpers\curl;

class Curl extends SimpleCurl {
    protected static $arguments = array(
        'url' => '',
        'proxy' => '',
        'get' => [],
        'post' => [],
        'files' => [],
        'cookie' => '',
        'headers' => [],
        'autoredirects' => true,
        'timeout' => 3,
        'curl_setopt_array' => [],
    );

    public static function __callStatic($name, $arguments) {
        $arguments = isset($arguments[0]) ? $arguments[0] : [];
        $arguments = array_merge(static::$arguments, $arguments);
        $curlSetoptArray = $arguments['curl_setopt_array'];
        $curlSetoptArray = static::setUrl($curlSetoptArray, $arguments['url'], $arguments['get']);
        $curlSetoptArray = static::setProxy($curlSetoptArray, $arguments['proxy']);
        $curlSetoptArray = static::setBody(
            $curlSetoptArray,
            $arguments['post'],
            $arguments['files'],
            $arguments['headers']
        );
        if ( $arguments['headers'] ) {
            $curlSetoptArray[CURLOPT_HTTPHEADER] = $arguments['headers'];
        }
        if ( $arguments['cookie'] ) {
            $curlSetoptArray[CURLOPT_COOKIE] = $arguments['cookie'];
        }
        $curlSetoptArray[CURLOPT_FOLLOWLOCATION] = $arguments['autoredirects'];
        $curlSetoptArray[CURLOPT_TIMEOUT] = $arguments['timeout'];
        return parent::__callStatic($name, [$curlSetoptArray]);
    }

    protected static function setBody($curlSetoptArray, $post, $files, &$headers) {
        foreach ( $post as $key => $value ) {
            if ( is_string($value) && strpos($value, '@') === 0 ) {
                $files[$key] = $value;
                unset($post[$key]);
            }
        }
        if ( $post ) {
            $post = http_build_query($post);
            $post = explode('&', $post);
        }

        $bodyArray = [];
        foreach ( $post as $value ) {
            $value = explode('=', $value);
            $key = urldecode($value[0]);
            $value = urldecode($value[1]);
            $bodyArray[] = implode("\r\n", [
                "Content-Disposition: form-data; name=\"{$key}\"",
                "",
                $value
            ]);
        }
        foreach ( $files as $key => $value ) {
            if ( strpos($value, '@') === 0 ) {
                $fileName = ltrim($value, '@');
                $value = file_get_contents($fileName);
                $fileInfo = pathinfo($fileName);
                $fileName = $fileInfo['basename'];
            } else {
                $fileName = $key;
            }
            $bodyArray[] = implode("\r\n", [
                "Content-Disposition: form-data; name=\"{$key}\"; filename=\"{$fileName}\"",
                "Content-Type: application/octet-stream",
                "",
                $value
            ]);
        }

        $bodyStr = '';
        if ( $bodyArray ) {
            $boundary = static::getBondary();
            for ( ; preg_grep("/{$boundary}/", $bodyArray); $boundary = static::getBondary() );
            $bodyStr = implode("\r\n--{$boundary}\r\n", $bodyArray);
            $bodyStr = "\r\n--{$boundary}\r\n" . $bodyStr . "\r\n--{$boundary}--\r\n";
            $headers[] = "Content-Type: multipart/form-data; boundary={$boundary}";
        }

        if ( $bodyStr ) {
            $curlSetoptArray[CURLOPT_POSTFIELDS] = $bodyStr;
        }

        return $curlSetoptArray;
    }

    protected static function getBondary() {
        return md5(openssl_random_pseudo_bytes(20) . microtime());
    }

    protected static function setUrl($curlSetoptArray, $url, $get) {
        try {
            $url = new Url($url);
            $url = $url->urlEncode();
        } catch ( \Exception $e ) {
            return $curlSetoptArray;
        }
        $get = http_build_query($get);
        $url['query'] = trim("{$url['query']}&{$get}", '&');
        $curlSetoptArray[CURLOPT_URL] = $url['url'];
        if ( $url['user'] || $url['pass'] ) {
            $curlSetoptArray[CURLOPT_USERPWD] = "{$url['user']}:{$url['pass']}";
        }
        return $curlSetoptArray;
    }

    protected static function setProxy($curlSetoptArray, $proxy) {
        try {
            $proxy = new Url($proxy);
            $proxy = $proxy->urlEncode();
        } catch ( \Exception $e ) {
            return $curlSetoptArray;
        }
        if ( strtolower($proxy['scheme']) == 'socks5' ) {
            $curlSetoptArray[CURLOPT_PROXYTYPE] = CURLPROXY_SOCKS5;
        } else if ( strtolower($proxy['scheme']) == 'socks4' ) {
            $curlSetoptArray[CURLOPT_PROXYTYPE] = CURLPROXY_SOCKS4;
        } else {
            $curlSetoptArray[CURLOPT_PROXYTYPE] = CURLPROXY_HTTP;
        }
        if ( $proxy['user'] || $proxy['pass'] ) {
            $curlSetoptArray[CURLOPT_PROXYUSERPWD] = "{$proxy['user']}:{$proxy['pass']}";
        }
        return $curlSetoptArray;
    }

}