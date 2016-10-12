<?php
/**
 * Created by PhpStorm.
 * User: forzi
 * Date: 10.10.2016
 * Time: 23:28
 */



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
