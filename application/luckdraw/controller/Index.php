<?php


namespace app\luckdraw\controller;




use app\luckdraw\logic\LuckdrwaLogic;
use think\Controller;
use think\Db;
use think\Exception;

class Index extends Controller
{
    //抽奖算法
    public function GetLuckdrwa()
    {
        $Luckdrwa = (new LuckdrwaLogic())->GetLuckdrwa();

        return json(['code'=>0,'data'=>$Luckdrwa,'msg'=>'抽奖成功！']);
    }
}