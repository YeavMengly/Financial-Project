<?php

use Hashids\Hashids;

if (! function_exists('arrayCheck')) {
    function arrayCheck($key, $array): bool
    {
        return is_array($array) && count($array) > 0 && array_key_exists($key, $array) && ! empty($array[$key]) && $array[$key] != 'null';
    }
}
if (! function_exists('getArrayValue')) {
    function getArrayValue($key, $array, $default = null)
    {
        return arrayCheck($key, $array) ? $array[$key] : $default;
    }
}

if (! function_exists('encode_params')) {
    function encode_params($params)
    {
        $hashids = new Hashids('Doc2%24^s', 10);
        return $hashids->encode($params);
    }
}

if (!function_exists('decode_params')) {
    function decode_params($params)
    {
        $hashids = new Hashids('Doc2%24^s', 10);
        return $hashids->decode($params);
    }
}


if (! function_exists('hasPermission')) {

    function hasPermission($key_word): bool
    {
        if (in_array($key_word, auth()->user()->permissions ?? []) || auth()->user()->role_id == 1) {
            return true;
        }

        return false;
    }
}

if (!function_exists('formatDateTime')) {
    function formatDateTime($datetime)
    {
        return Date("d-M-Y h:i A", strtotime($datetime));
    }
}
