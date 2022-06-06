<?php 





class Rsa
{
    private static $private_key = <<<EOD
-----BEGIN PRIVATE KEY-----
MIIBUwIBADANBgkqhkiG9w0BAQEFAASCAT0wggE5AgEAAkEAg/gysKG8CKCF0B8bxzJoPEpw6KiA2hA4l5T0ssor6srFc8Ia38O9vkUCyI88X5ZLGIveG5fs6j/pMpCtQP6OpwIDAQABAkAGoIETI5wIpt7xW46OizZ2yJow5L1LtgLRgdZj4Akiiie2BD8N3yYYHLQ+h+QVHXTj46PKn8ZsQ/R4/u8r7lehAiEAvbnCmAZQzy8iTOmab/izrHcJPaR1G19yHUpS33E/DlECIQCyEZcxKbXVBj6byzAGbmODiKeZ1TceFRTWVahIVoO3dwIgK7F9LC/AKobLWnUuGP1ou55KZYTbZ2tqx24XedgF0pECIAmXXPdu8bZZscGefiW6iG2rTKvCikd6hzbMQlYzIsEdAiB177/ktn1aMkp/jRKK4S3B0wCdUQBGYn7wOAHxl+U6Ww==
-----END PRIVATE KEY-----
EOD;

    private static $public_key = <<<EOD
-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAIP4MrChvAighdAfG8cyaDxKcOiogNoQOJeU9LLKK+rKxXPCGt/Dvb5FAsiPPF+WSxiL3huX7Oo/6TKQrUD+jqcCAwEAAQ==
-----END PUBLIC KEY-----
EOD;

    /**
     * 私钥加密
     *
     * @param string $data
     * @return string
     */
    public function privEncrypt($data = ''): string
    {
        $private = openssl_pkey_get_private(self::$private_key);

        openssl_private_encrypt($data, $encrypted, $private);

        $encrypted = $encrypted ? base64_encode($encrypted) : '';

        return $encrypted;
    }

    /**
     * 公钥加密
     *
     * @param string $data
     * @return string
     */
    public function publicEncrypt($data = ''): string
    {
        $public = openssl_pkey_get_public(self::$public_key);

        openssl_public_encrypt($data, $encrypted, $public);

        $encrypted = $encrypted ? base64_encode($encrypted) : '';

        return $encrypted;
    }

    /**
     * 私钥解密
     *
     * @param string $encrypted
     * @return mixed|string
     */
    public function privDecrypt($encrypted = ''): string
    {
        $private = openssl_pkey_get_private(self::$private_key);

        $encrypted = base64_decode($encrypted);

        $res = openssl_private_decrypt($encrypted, $decrypted, $private);

        return $res ? $decrypted : '';
    }

    /**
     * 公钥解密
     *
     * @param string $encrypted
     * @return string
     */
    public function publicDecrypt($encrypted = ''): string
    {
        $public = openssl_pkey_get_public(self::$public_key);

        $encrypted = base64_decode($encrypted);

        $res = openssl_public_decrypt($encrypted, $decrypted, $public);

        return $res ? $decrypted : '';
    }

}




$data="0c3f349c94f544f396ce0d06ccc28922";
echo "原始数据：".$data."\n";
echo "<br>";
// 加密
$result =$this->publicEncrypt($data);
echo "加密后数据：".$result."\n";
echo "<br>";
// 解密
$result2 = $this->privDecrypt($result);
echo "解密后数据".$result2;
echo "<br>";


 ?>