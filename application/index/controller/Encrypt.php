<?php
namespace app\index\controller;

use app\index\logic\Es;
use app\index\logic\Index as IndexLogic;

use app\index\model\Users as UsersModel;
use org\ElasticSearchApi;
use think\Db;
use think\db\Query;
use think\facade\Config;
use org\Excel;
use org\PicCompress;
use think\facade\Request;
use think\Queue;
use app\index\validate\Index as IndexValidate;

class Encrypt
{

    private static $AesKey = "25b18a8144f242569bd37dc8eba3b309";//AES的key
    /**
     * 与java等的aes/ecb/pcks5加密一样效果
     */
    function AesEncrypt() {//AES加密是一种对称式加密，即加密和解密所需秘钥是相同的，不会限制加密字符串的长度
        $key = self::$AesKey;
        $data = '这里是一个测试内容';
        $key = substr(openssl_digest(openssl_digest($key, 'sha1', true), 'sha1', true), 0, 16);
        echo base64_encode(openssl_encrypt($data, 'AES-128-ECB', $key, OPENSSL_PKCS1_PADDING));//OPENSSL_PKCS1_PADDING 不知道为什么可以与PKCS5通用,未深究
    }

    /**
     * 可以解密java等的aes/ecb/pcks5加密的内容
     */
    function AesDecrypt() {//此方法的data即为上方“AesEncrypt”方程的aes加密结果，可经此方程解密出原data
        $key = self::$AesKey;
        $data = 'kFrwvzMAnaPS+N24p9TyDtIzXW5w7ovgi7wAEZc2r1s=';
        $key = substr(openssl_digest(openssl_digest($key, 'sha1', true), 'sha1', true), 0, 16);
        echo openssl_decrypt(base64_decode($data), 'AES-128-ECB', $key, OPENSSL_PKCS1_PADDING);//OPENSSL_PKCS1_PADDING 不知道为什么可以与PKCS5通用,未深究
    }





















}
