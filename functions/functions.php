<?php
/**
 * Created by PhpStorm.
 * User: forzi
 * Date: 10.10.2016
 * Time: 23:28
 */

if (!function_exists('d')) {
    function d($message = '', $break = 1) {
		#throw new Exception();
        if ( $message !== '' ) {
            echo '<pre>' . print_r($message, 1) . '</pre>' . "\n";
        }
        if ( $break ) {
            die();
        }
    }
}

if (!function_exists('getallheaders'))
{
    function getallheaders()
    {
        $headers = '';
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

