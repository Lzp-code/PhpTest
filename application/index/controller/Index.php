<?php
namespace app\index\controller;

use app\index\logic\Es;
use app\index\logic\Index as IndexLogic;

use org\ElasticSearchApi;
use think\Db;
use think\db\Query;
use think\facade\Config;
use org\Excel;
use org\PicCompress;
use think\facade\Request;
use think\Queue;

class Index
{


    public function getUserInfo()
    {



        $id = 5451748;

        //查询用户基本信息
        $user = Db::name('users')->where('id',$id)->find();
        print_r($user);exit();

        //写入Es
//        $esOpen = Config::get('system.ES_OPEN');
//        if($esOpen == 1 ){
//            (new Es())->update($id);
//        }

//        echo 1111;
    }

    public function SearchUser(){
        $params = Request::only(['page'=>1,'rows'=>15,'id','idcard','mobile','realname','birthday','create_time','photo'], 'get');
        $condition = (new IndexLogic())->getWhere($params);


        $ES = new ElasticSearchApi('users','users');
        $sum = $ES->getCount($condition);
        $total = $sum>1000?1000:$sum;
        $totalPage = ceil($total / $params['rows']);
        $field = ['id','idcard','mobile','realname','birthday','create_time','photo'];
        $offset = ($params['page'] - 1) * $params['rows'] <= 0 ? 0 : ($params['page'] - 1) * $params['rows'];
        $data = $ES->boolQuery($condition, $field, $offset, $params['rows'],'id','desc');

        print_r($sum);
        print_r($data);

//        $get = $ES->get_one_document(5451748,null,null);//根据id获得单条数据
//        print_r($get);
    }






    public function getOrganization()
    {

//        (new Es())->createIndex();
//        print_r(11111);
//        exit();
        set_time_limit(0);


        $id = 100023780;

        //查询用户基本信息
        $Organization = Db::name('organization')->field('id,name')->where('id','<',$id)->select();
//        print_r($Organization);exit();

        //写入Es
        $esOpen = Config::get('system.ES_OPEN');
        if($esOpen == 1 ){
            foreach($Organization as $k =>$val){
                (new Es())->update($val['id']);
            }
        }

        echo 55555;
    }



    public function SearchOrganization(){
        $params = Request::only(['page'=>1,'rows'=>15,'id','idcard','mobile','realname','birthday','create_time','photo'], 'get');
        $condition = (new IndexLogic())->getWhere($params);


        $ES = new ElasticSearchApi('organization','organization');
        $sum = $ES->getCount($condition);
        $total = $sum>1000?1000:$sum;
        $totalPage = ceil($total / $params['rows']);
        $field = ['id','idcard','mobile','realname','birthday','create_time','photo'];
        $offset = ($params['page'] - 1) * $params['rows'] <= 0 ? 0 : ($params['page'] - 1) * $params['rows'];
        $data = $ES->boolQuery($condition, $field, $offset, $params['rows'],'id','desc');

        print_r($sum);
        print_r($data);

//        $get = $ES->get_one_document(5451748,null,null);//根据id获得单条数据
//        print_r($get);
    }








    //导出excel
    public function exportExcel(){
        $fields = 'infor_title,infor_type,infor_auth,organize_id,edit_time,read_count,status';
        $data = Db::name('information')->field($fields)->where('id','<',30)->select();
        $head = ['新闻标题', '栏目', '发布者','发布组织','最后操作时间','浏览数','审核状态'];
        $fileName = '资讯导出测试';
        Excel::exportExcel($fileName,$head,$data);
    }


    //导入excel
    public function importExcel(){
        $fileName = 'young-instructor.xls';//先通过上传文件的接口上传文件，然后获取到文件名称
        $filePath = Config::get("app.upload_protected_path") . "/Excel/" . $fileName;//拼写文件完整路径
        $data = Excel::import($filePath,502, "N");//获得Excel文件的内容后，自行按需求逻辑处理
        print_r($data);
    }


    //上传图片并压缩
    public function uploadPictureMin(){
        $save_name = '1.png';//先通过上传文件的接口上传文件，然后获取到文件名称

        //将原图压缩
        $origin_save_name = Config::get('app.upload_picture_path').'/'.$save_name;
        $PicCompress = (new PicCompress($origin_save_name,1))->compressImg($origin_save_name);//将原图压缩保存到原位置


        //小图压缩
        $mini_save_name =Config::get("app.upload_mini_picture_path") . "/" .date('Ymd').'/'.$save_name;//小图要保存的位置
        $pos = strrpos($mini_save_name,'/'); //找到最后一个字符串出现位置
        $new_dir = substr($mini_save_name,0,$pos).'/';  //截取字符串
        if(!is_dir($new_dir)){
            $dir_res = mkdir($new_dir,0777,true);
            chmod($new_dir,0777);
        }
        $PicCompress = (new PicCompress($origin_save_name,0))->compressImg($mini_save_name);//将原图压缩保存到$mini_save_name
    }


    //生成压缩文件
    public function exportZip()
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $nowDate = date('Y-m-d');
        $perSize = 500;//每页500条
        $dir = Config::get("app.export_zip_path") . '/' . $nowDate . '/';//拼写文件保存路径
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }

        $fileNewArr = array();
        $headerText = ['id', '身份证号', '电话号码', '姓名'];

        $userSql = Db::name('users')->field('id,idcard,mobile,realname')->where('is_delete', 0);

        $accessCount = $userSql->count();//先获取符合条件的数据总数
        $pages = ceil($accessCount / $perSize);

        for ($i = 1; $i <= $pages; $i++) {
            $columns = $headerText;
            $filename = $dir . '第' . $i . '个用户表压缩数据导出测试' . time() . random_int(1, 100) . '.csv';
            $fp = fopen($filename, 'w');//写入方式打开，清除文件内容，如果文件不存在则尝试创建之
            mb_convert_variables('GBK', 'UTF-8', $columns);
            fputcsv($fp, $columns);//将行格式化为 CSV 并写入一个打开的文件
            $fileNewArr[] = $filename;
            $db_data = $userSql->page($i, $perSize)->select();
            foreach ($db_data as $key => $value) {
                mb_convert_variables('GBK', 'UTF-8', $value);
                fputcsv($fp, $value);//将行格式化为 CSV 并写入一个打开的文件
            }
            fclose($fp);
            unset($db_data);
        }

        $zip = new \ZipArchive();
        $zipFileName = time() . random_int(1, 100) . '用户表压缩数据导出测试.zip';
        $zipName = $dir . $zipFileName;
        $zip->open($zipName, \ZipArchive::CREATE);//打开压缩包
        foreach ($fileNewArr as $file) {
            $zip->addFile($file, basename($file));   //向压缩包中添加文件
        }
        $zip->close();//关闭压缩包
        foreach ($fileNewArr as $file) {//删除csv临时文件
            if (file_exists($file)) {
                @unlink($file);
            }
        }
    }

}
