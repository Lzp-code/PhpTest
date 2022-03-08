<?php
/**
 * User: Gkh
 * Date: 2021/11/25
 * Time: 11:04
 */

namespace app\common;

abstract class ApiCode
{
    public const OK = 0;

    public const NOT_FOUND = 4040;              // 无效调用
    public const INVALID_PARAMETER = 4000;      // 无效参数，参数校验错误
    public const MUST_IN_WECHAT = 4001;         // 必须微信环境
    public const MUST_NOT_IN_WECHAT = 4002;     // 必须非微信环境

    public const INVALID_TOKEN = 4010;          // 无效令牌，如果使用令牌
    public const NOT_WECHAT_AUTH = 4011;        // 用户微信未授权
    public const NOT_LOGIN = 4012;              // 用户未登录
    public const NEED_PHONENUMBER = 4013;       // 需要先获取手机号
    public const NEED_SMS_VERIFY = 4014;        // 需要短信验证
    public const NEED_REALNAME = 4015;          // 需要实名认证
    public const UNAUTHORIZED = 4030;           // 权限不足

    public const BAD_SIGNATURE = 4020;          // 签名错误

    public const SERVICE_ERROR = 5000;          // 其他系统错误
    public const WECHAT_API_ERROR = 5100;       // 微信接口错误


    //对接工行团费支付接口状态码 9开头
    const FEE_ERROR = 9999;                     //未知错误
    const FEE_AUTHCHECK_FAIL = 9001;            //身份验证失败
    const FEE_REQUEST_TIMEOUT = 9002;           //接口请求超时
    const FEE_USERMESS_NOTFOUND = 9003;         //找不到该用户的应交团费信息
    const FEE_ORDER_CREATE_FAIL = 9004;         //订单生成失败
    const FEE_ORDER_NOTFOUND = 9005;            //订单信息不存在
    const FEE_ORDER_UPDATE_FAIL = 9006;         //订单修改状态失败
    const FEE_ORDER_IS_CLEAR = 9007;            //订单已清分
    const FEE_ORDER_CLEAR_FAIL = 9008;          //订单清分失败
    const FEE_NEED_REFUND = 9100;               //订单需要退款
}