<?php


namespace app\common\lib;


/**
 * @deprecated
 */
abstract class ApiCode
{
    //接口返回状态码(可参考豆瓣api http://www.doubanapi.com/)
    const SUCCESS = 0;                        //请求成功
    const INTERNAL_SERVER_ERROR = 999;        //未知错误
    const INVALID_PARAMETER = 1002;           //请求参数有误



    //红色坐标打卡专用状态码 8开头
    const LANDMARK_DISTANCE_TOO_LONG = 8001;  //距离过长
    const LANDMARK_CLOCKED_IN = 8002;  //已经打过卡
    const LANDMARK_WECHATAUTH_FAIL = 8003;  //获取微信授权信息失败




    const CREATED = 201;//创建|修改|更新成功
    const ACCEPTED = 202;//异步返回的成功
    const UNAUTHORIZED = 401;//无权限
    const FORBIDDEN = 403;//被禁止访问
    const NOT_FOUND = 404;//找不到路由
    const SERVICE_ERROR = 500;//程序错误
    const ERROR = 999;//失败(未知错误)
}