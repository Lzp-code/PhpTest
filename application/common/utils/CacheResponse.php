<?php
/**
 * Created by PhpStorm.
 * User: Gkh
 * Date: 2020/2/13
 * Time: 23:22
 */

namespace app\common\utils;

class CacheResponse
{
    static public function has($key, $prefix = '', $suffix = '', $contentType = null)
    {
        $data = cache($key);
        if (empty($data))
            return null;
        $clientEtag = !empty($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : null;
        if ($clientEtag !== null && $clientEtag === $data['etag']) {
            return CacheResponse::outResponse(response('', 304), $data['etag'], $data['lifeTime'], $contentType);
        }
        return CacheResponse::outContent($prefix . $data['content'] . $suffix, $data['etag'], $data['lifeTime'], $contentType);
    }

    static protected function outResponse($response, $etag, $lifeTime, $contentType)
    {
        $response->eTag($etag);
        $response->cacheControl('max-age=' . $lifeTime . ', must-revalidate');
        $response->header('Pragma', 'cache');
        if ($contentType !== null) {
            $response->contentType($contentType);
        }
        return $response;
    }

    static protected function outContent($content, $etag, $lifeTime, $contentType)
    {
        return CacheResponse::outResponse(response($content), $etag, $lifeTime, $contentType);
    }

    static public function cacheResponse($key, $content, $lifeTime, $prefix = '', $suffix = '', $contentType = null, $saveCacheOnly = false)
    {
        $data = [
            'content' => $content,
            'etag' => '"' . md5($content) . '"',
            'lifeTime' => $lifeTime];
        cache($key, $data, $lifeTime);
        return $saveCacheOnly ? null : CacheResponse::outContent($prefix . $data['content'] . $suffix, $data['etag'], $lifeTime, $contentType);
    }

}