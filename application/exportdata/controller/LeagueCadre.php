<?php


namespace app\newexportdata\controller;


use app\newexportdata\logic\Comm;
use app\regiment\model\LeagueCadre as LeagueCadreModel;
use app\regiment\logic\CadreLogic;
use app\study\logic\Study;
use think\Db;
use think\Exception;

class LeagueCadre implements exportDataInterface
{
    protected static $data = [];

    public static function setData($data = []){
        self::$data = $data;
        self::handleData();
    }

    public static function handleData(){
        $headerText = ['姓名','年龄','是否党员','团籍所在支部','岗位状态','所在组织','部门','职务','大学习本期已学'];
        try{
            $where = CadreLogic::whereArr(self::$data['params']);
            $ModelList = LeagueCadreModel::alias('lc')
                ->leftJoin('users u','lc.userid = u.id')
                ->leftJoin('organization o','o.id = lc.leagueid')
                ->leftJoin('league_member lm','lc.userid = lm.userid and lm.is_delete=0')
                ->field('lc.id,
                    lc.userid,
                    o.name,
                    u.id as uid,
                    u.realname,
                    u.idcard,
                    lc.is_on,
                    lc.record_rank,
                    lc.department_cn,
                    o.id as oid,
                    u.is_party_member,
                    lc.cadre_type,
                    lm.leagueid as lmleagueid,
                    lm.league_status as lmleague_status,
                    lm.identity as lmidentity');
            Comm::downLoadZipNew($ModelList,$headerText,self::$data['className'],self::$data['file_id'],$where);
        }catch (\Exception $e){
            echo $e->getMessage();
            Db::name('export_file')->where('id',self::$data['file_id'])->update(['status'=>-1,'error_msg'=>$e->getMessage()]);
        }
    }

    public static function setDataCsv($data){
        $data['is_on_cn'] = getCommonConfig('tag',false)[$data['is_on']];
        $data['age'] = getIdCardInfo($data['idcard'])['age'];
        $lmorgname = Db::name('organization')->field('name')->where('id',$data['lmleagueid'])->find();
        $name = (isset($lmorgname['name']) ? $lmorgname['name'] : '--');
        $lmorgname = ($data['lmleague_status'] == 95) ? $name.'(待审核)' : $name;
        $lmorgname = ($data['lmidentity'] == 1) ? '身份选择错误' : $lmorgname;
        $data['lmorgname'] = $lmorgname;
        $data['isStudy'] = Study::userIsStudy($data['uid']);
        $result = [
            'realname'=>$data['realname'],
            'age'=>$data['age'],
            'is_party_member'=>($data['is_party_member'] == 1)?'是':'否',
            'lmorgname'=>$data['lmorgname'],
            'is_on_cn'=>$data['is_on_cn'],
            'name'=>$data['name'],
            'department_cn'=>$data['department_cn'],
            'record_rank'=>$data['record_rank'],
            'isStudy'=>$data['isStudy'],
        ];
        return $result;
    }
}