<?php
/**
 * User: Gkh
 * Date: 2021/11/25
 * Time: 11:04
 */

namespace app\common\utils;

class CommonUtils
{
    public static function startsWith($haystack, $needle): bool
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    public static function endWith($haystack, $needle): bool
    {
        $length = strlen($needle);
        if ($length === 0)
            return true;
        return (substr($haystack, -$length) === $needle);
    }

    public static function getBytes($str): array
    {
        return array_values(unpack("C*", $str));
        /*
                $len = strlen($str);
                $bytes = array();
                for ($i = 0; $i < $len; $i++) {
                    if (ord($str[$i]) >= 128) {
                        $byte = ord($str[$i]) - 256;
                    } else {
                        $byte = ord($str[$i]);
                    }
                    $bytes[] = $byte;
                }
                return $bytes;
        */
    }

    public static function toString($bytes): string
    {
        return vsprintf(str_repeat('%c', count($bytes)), $bytes);
        /*
                $str = '';
                foreach ($bytes as $ch) {
                    $str .= chr($ch);
                }
                return $str;
        */
    }

    public static function fuzzySensitive(&$arr)
    {
        foreach ($arr as $key => &$item) {
            if (is_array($item)) {
                CommonUtils::fuzzySensitive($item);
            } else if ($key === 'mobile' && !empty($item)) {
                $item = getHideMobile($item);
            } else if ($key === 'idcard' && !empty($item)) {
                $item = getHideMobile($item);
            }
        }
        if (is_array($arr)) {
            if (isset($arr['password'])) unset($arr['password']);
            if (isset($arr['salt'])) unset($arr['salt']);
        } else if (is_object($arr)) {
            if (isset($arr->password)) unset($arr->password);
            if (isset($arr->salt)) unset($arr->salt);
        }
    }

    public static function getRandomString($length): string
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($length * 2);
            if ($bytes !== false)
                return substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $length);
        }
        $libs = str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
        $randStr = '';
        for ($i = 0; $i < $length; $i++) {
            $randStr .= $libs[mt_rand(0, 61)];
        }
        return substr(str_shuffle(uniqid($randStr, true)), 0, $length);
    }

    public static function copyIfExists($source, $keys, $sourcePrefix = null, $destPrefix = null, array &$result = null): array
    {
        if ($result === null) {
            $result = [];
        }
        foreach ($keys as $key => $value) {
            $key = is_string($key) ? $key : $value;
            $key = $sourcePrefix === null ? $key : $sourcePrefix . $key;
            $value = $destPrefix === null ? $value : $destPrefix . $value;
            if (is_array($source)) {
                if (isset($source[$key]))
                    $result[$value] = $source[$key];
            } else if (is_object($source)) {
                if (isset($source->$key))
                    $result[$value] = $source->$key;
            }
        }
        return $result;
    }
}