<?php
/**
 * User: Gkh
 * Date: 2021/11/25
 * Time: 11:04
 */

namespace app\common\utils;

use think\response\Json;
use think\response\Jsonp;

class ApiUtils
{
    public static function baseURL()
    {
        $baseURL = config('public.baseURL');
        return empty($baseURL) ? request()->domain(false) : $baseURL;
    }

    public static function parseBoolean($value)
    {
        return in_array($value, [true, false, 0, 1, '0', '1'], true);
    }

    public static function buildQueryString($param)
    {
        return http_build_query($param, '', '&', PHP_QUERY_RFC3986);
    }

    public static function injectionResponse($response, $key, $value)
    {
        if ($response instanceof Json || $response instanceof Jsonp) {
            $content = $response->getData();
            if (is_array($content)) {
                CommonUtils::fuzzySensitive($value);
                if (isset($content['data']) && $content['data'] !== null) {
                    if (is_array($content['data']))
                        $content['data'][$key] = $value;
                    else if (is_object($content['data']))
                        $content['data']->$key = $value;
                } else {
                    $content['data'] = [$key => $value];
                }
                $response->data($content);
            }
        }
    }
}