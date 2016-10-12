<?php
/**
 * Created by PhpStorm.
 * User: forzi
 * Date: 07.10.2016
 * Time: 22:49
 */

namespace stradivari;

if (!function_exists('right_cut')) {
    function right_cut($str = '', $to_cut = '') {
        $cut_len = strlen($to_cut);
        $str_len = strlen($str);
        if ( substr($str, -$cut_len) == $to_cut ) {
            $str = substr($str, 0, $str_len - $cut_len);
        }
        return $str;
    }

}

if (!function_exists('left_cut')) {
    function left_cut($str = '', $to_cut = '') {
        $cut_len = strlen($to_cut);
        $str_len = strlen($str);
        if ( substr($str, 0, $cut_len) == $to_cut ) {
            $str = substr($str, $cut_len);
        }
        return $str;
    }
}

if (!function_exists('cut')) {
    function cut($str = '', $to_cut = '') {
        $str = right_cut($str, $to_cut);
        $str = left_cut($str, $to_cut);
        return $str;
    }
}
