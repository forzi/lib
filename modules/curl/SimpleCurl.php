<?php
namespace common\modules\curl;

class SimpleCurl {
    public static $cookieFile = '';
    public static $cookieJar = '';
    protected static $responseHeaders;
    protected static $defaultCurlSetoptArray = [
        CURLOPT_CERTINFO		=> true,
        CURLOPT_FTP_USE_EPRT	=> true,
        CURLOPT_FTP_USE_EPSV	=> true,
        CURLOPT_RETURNTRANSFER	=> true,
        CURLOPT_SSL_VERIFYHOST	=> false,
        CURLINFO_HEADER_OUT		=> true,
        //CURLOPT_BUFFERSIZE		=> 1024,
        CURLOPT_HTTP_VERSION	=> CURL_HTTP_VERSION_1_0,
        CURLOPT_FTPSSLAUTH		=> CURLFTPAUTH_DEFAULT,
        CURLOPT_HTTPAUTH		=> CURLAUTH_ANY,
        CURLOPT_HEADERFUNCTION	=> 'static::responseHeaderHandler',
        CURLOPT_FOLLOWLOCATION	=> true,
        CURLOPT_CONNECTTIMEOUT	=> 3,
        CURLOPT_TIMEOUT			=> 3,
        //CURLOPT_POST			=> true
    ];

    public static function __callStatic($name, $arguments) {
        return call_user_func_array('static::execute', static::prepareArguments($name, $arguments));
    }

    protected static function prepareArguments($method, $arguments) {
        array_unshift($arguments, strtoupper($method));
        return $arguments;
    }

    protected static function execute($method, Array $curlSetoptArray = []) {
        static::$defaultCurlSetoptArray[CURLOPT_CUSTOMREQUEST] = $method;
        if ( $method != 'GET' && $method != 'HEAD' ) {
            $curlSetoptArray[CURLOPT_POST] = true;
        }
        static::$defaultCurlSetoptArray[CURLOPT_NOBODY] = $method == 'HEAD';
        static::$responseHeaders = '';

        $curlSetoptArray[CURLOPT_COOKIEFILE] = static::$cookieFile;
        $curlSetoptArray[CURLOPT_COOKIEJAR] = static::$cookieJar;
        $curlSetoptArray += static::$defaultCurlSetoptArray;

        $curl = curl_init();
        foreach ( $curlSetoptArray as $key => $value ) {
            curl_setopt($curl, $key, $value);
        }
        $curl_result = curl_exec($curl);
        $curl_info = curl_getinfo($curl);
        $curl_errno = curl_errno($curl);
        $curl_error = curl_error($curl);
        curl_close($curl);

        return array(
            'status_code' => $curl_info['http_code'],
            'headers' => static::$responseHeaders,
            'body' => $curl_result,
            'curl_info' => $curl_info,
            'error' => array(
                'code' => $curl_errno,
                'text' => $curl_error
            )
        );
    }

    protected static function responseHeaderHandler($curl, $header) {
        static::$responseHeaders .= $header;
        return strlen($header);
    }

}
