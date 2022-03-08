<?php
/**
 * Created by PhpStorm.
 * User: Gkh
 * Date: 2020/2/13
 * Time: 23:22
 */

namespace app\common\utils;


class NetUtils
{
    static public function getIPArea($ip)
    {
        $datatype = 'json';
        $url = 'https://api.ip138.com/ip/?ip=' . $ip . '&datatype=' . $datatype;
        $header = array('token:0b95718271e3ee68b1616523720d367d');
        $data = NetUtils::getData($url, $header);
        if (!empty($data)) {
            $data = json_decode($data, true);
            return !empty($data) && !empty($data->data) ? json_encode($data->data, JSON_UNESCAPED_UNICODE) : '[]';
        }
    }

    static private function getData($url, $header)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $handles = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode !== 200) {
            return false;
        }
        return $handles;
    }
}