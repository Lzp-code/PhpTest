<?php


class RSA
{
    //这里是RSA不限制字符长度的加密例子

    public static function encrypt($string, $publicKey)
    {
        $key = openssl_pkey_get_public($publicKey);
        $details = openssl_pkey_get_details($key);
        $block_size = $details['bits'] / 8 - 11;
        $blocks = str_split($string, $block_size);
        $result = '';
        foreach ($blocks as $b) {
            if (openssl_public_encrypt($b, $encrypted, $key)) {
                $result .= $encrypted;
            }
        }
        return base64_encode($result);
    }

    public static function decrypt($string, $privateKey, $base64 = false)
    {
        $data = base64_decode($string);
        $key = openssl_pkey_get_private($privateKey);
        $details = openssl_pkey_get_details($key);
        $block_size = $details['bits'] / 8;
        $blocks = str_split($data, $block_size);
        $result = '';
        foreach ($blocks as $b) {
            if (openssl_private_decrypt($b, $decrypted, $key)) {
                $result .= $decrypted;
            }
        }
        return $base64 ? base64_encode($result) : $result;
    }
}

$pub = '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArewiiOX2YkBlBVI7i201
y8HUDV2ji1hLFB+SRcatmMDbb1Pj9CEdTfuz6JJ1IHovhdXK4TVOdsq4zetf5uTp
+dJ8TWLggf31YyWQhyfFq+pY9TwKKZEBRq5kZQftSgR6t+r088T2xqovNtPDjSkW
3EhezyllxrBycgFyyp306eQrg0f5A7ABR+leGOCuiqaHHzhEWN3gaRNjUAgNPngj
1nRYK6b0nWSkoRqB5/MMM5avPxytrEOjZUy/dH20deJS219JD6/NqofxD8I8FFR4
zRQEx7qCIqXrKOFCwg6TDzf5Zwlkz15HPDyHlalo2YQfFwNFpuXtjA0dnqzD0zb3
YQIDAQAB
-----END PUBLIC KEY-----';
$pk = '-----BEGIN PRIVATE KEY-----
MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCt7CKI5fZiQGUF
UjuLbTXLwdQNXaOLWEsUH5JFxq2YwNtvU+P0IR1N+7PoknUgei+F1crhNU52yrjN
61/m5On50nxNYuCB/fVjJZCHJ8Wr6lj1PAopkQFGrmRlB+1KBHq36vTzxPbGqi82
08ONKRbcSF7PKWXGsHJyAXLKnfTp5CuDR/kDsAFH6V4Y4K6KpocfOERY3eBpE2NQ
CA0+eCPWdFgrpvSdZKShGoHn8wwzlq8/HK2sQ6NlTL90fbR14lLbX0kPr82qh/EP
wjwUVHjNFATHuoIipeso4ULCDpMPN/lnCWTPXkc8PIeVqWjZhB8XA0Wm5e2MDR2e
rMPTNvdhAgMBAAECggEAJpFXsy3zriQDguOSar/EDzQjVvdt3eetdn/tyuVc96PE
xXI/+ZIiUnm/kpJvwMz3nuEjBT/x72vTAW7xrF0U+Z5QjESh7pGnid35p88NCauF
IJS42DDcrJTdlH3mg+RsZj6HJUuHQdTZdXoOQk5bUGwIAj524FGef2OM1hujBP6D
gwEbR8sfYqDAROcGqecQHKCi09lFbL0pwoYy2MOMzNBykbqB5AhGOVzjKyDYEwef
Nntt3oacMmdnbuHkVjYKOzWjNtECUn1wkK6pS6nOO6Yzgy1QFmPv+yWHJvrV85cC
9LwNPtXTfcPVxfqQNFOu4ovy26tBFOQpC5ADR94twQKBgQDWbbMU6vJnbLiACzG+
AEDrFxAEHmUMBhhPF4i+hPugqBknAiF1LOB9s5FaRZukITPmZMX0y5aSLBI/63Y1
zE8z4m9ou5RN6UzykMLL+3OBJTBy3Q0Oazh99UT3RUCbwIiM0b3/FGfx7331nQ/m
w3yS/OnNqV1RGMpgcHAyL/B6KQKBgQDPpBOk5T6g2pJr6e/pY+CmjbOdCKMeXqwe
kPYCT5zchLN0V9AELWHZ2cUJaAkIkKf47DBruXBV2gao7Pi2PHXRvlV9f/uPee3E
xPF8tDBuTZGaqiyX0Kbc4Tc/3sekuGlggPLvTl0ukkhzmyDJ7BuJYpspVAEMwu3w
68YcN76qeQKBgQCrciqtnu0SJKugNVMgR7OgRGBZ6rOAWZ82HesH6ewHGGCEAAAg
YDFeUCT3uJApOyL0I77ja6SIWxR8ZxetDB6HrZTGeLSrVs5fY79cuUAxEjsanAPE
c5ZHn8P0sTpnThnf/hOb0AUMPCDKMTp1l/gSzoViGvixztCBK2WJuyrbgQKBgQCi
5e8/Y1YR7cgHf11ndaLuJ6cs4HTQQ6e2xzUpNPo0Cqua77VTQaNNvMoXChZkMNCG
ug12xeG6iLTG3Dp3BdHM/gyly704n4iI4ZUup1KDhrlfZHhdliUCjnHA2u83bDHH
swJj+c+i1MKgZ6h+oYws6T4fWzcDov33D5G524XqyQKBgDKlb4vqv2jQ/e0A3E3k
eBKLlNATBHt+TBh9OD3/q4613uIncjxT7r0S7lIif7+yVQJBDizBiSG4AFcoGOsz
SorKM6JQ7frHM6R5X2fPtuP2ODGIyxpZ+V7bR/9cBdbpbd/ps1YqluApmfoSVh+d
6+v81FipLf6lp02BVSEJ41LR
-----END PRIVATE KEY-----';
$str = '2014年至2020年，我国先后迎回七批716位在韩中国人民志愿军烈士遗骸，并安葬于沈阳抗美援朝烈士陵园。9月2日，我国将迎回第八批在韩中国人民志愿军烈士遗骸。一个有希望的民族不能没有英雄，一个有前途的国家不能没有先锋。英雄是中华民族的脊梁，英雄的事迹和精神都是激励我们前行的强大力量。每一次迎回烈士遗骸，都会让我们回到那抗美援朝的壮烈时代，烈士遗骸是时代的化石，是历史的见证；每一次英灵回归故里，英雄精神都会让我们心潮澎湃，伟大的抗美援朝精神是我们永远的宝贵精神财富。烈士英灵回归故里，英雄精神激荡时代。烈士英灵回归故里，我们以最高礼仪。庄严的时刻，敬慕的氛围，我们用崇尚的心迎接烈士遗骸。在每一名礼兵心里，这是庄重的托起，眼神里是肃穆，是庄重，是对志愿军的崇高敬意；在每一个国人心里，这是严肃的仰视，这是崇敬的感情。最高的礼仪是对英灵的告，最高的敬意是对英雄的赞美。山河已无恙，英雄可归矣。《1950他们正年轻》将在9月3日上映，片中有一位老战士，临终前的愿望是找到自己的战友遗体，带回中国。英雄的遗愿让我们落泪，不止老战士，我们对牺牲的每个战士念念不忘，我们一直在做接他们回家的工作，我们一直努力在寻找烈士亲人。这是对英烈的崇尚，这是对英灵的担当，这是对抗美援朝的敬仰。不忘历史，启迪今天。以史为鉴，铸剑为犁。';
$encrypted = RSA::encrypt($str, $pub);
$decrypted = RSA::decrypt($encrypted, $pk);
var_dump($decrypted);









