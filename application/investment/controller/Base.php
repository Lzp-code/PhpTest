<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/4
 * Time: 16:53
 */

namespace app\investment\controller;


use think\Controller;
use think\Request;


class Base extends Controller
{

    //复利计算
    public function compoundInterest(Request $request)
    {
        $param = $request->only(['first','year','rate']);//投入金额、年、年利率

        //一次投入的复利算法
        $rate = 1+$param['rate'];
        $pow = pow($rate,$param['year']-1);
        $end = $param['first'] * $pow;
        var_dump($end);


        //每年均等投入的复利算法
        $rete_denominator = pow($rate,$param['year'])-1;
        $end = $param['first'] * ($rete_denominator/$param['rate']);
        var_dump($end);
    }


















}