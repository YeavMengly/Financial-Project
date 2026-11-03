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

if (!function_exists('loadRunUnlimitedTime')) {

    function loadRunUnlimitedTime(int $minutes = 0): void
    {
        if ($minutes === 0) {
            // Unlimited
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', '0');
            set_time_limit(0);
        } else {
            $seconds = $minutes * 60;

            ini_set('memory_limit', '2G');
            ini_set('max_execution_time', (string) $seconds);

            set_time_limit($seconds);
        }
    }
}
