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

class Index
{

    //错误捕获与处理
    public function errorTest(){
        $IndexLogic = new IndexLogic();
        $params = Request::only(['id'=>null], 'get');

        validate(IndexValidate::class)//此处验证走到按照在config/app.php 的参数 exception_handle，走到app/ExceptionHandle的render方法
            ->scene('check')
            ->check($params);

        $data = $IndexLogic->errorTest();
        return json($data);
    }

    //关联查询（->with）
    public function getUserInfo(){
        $UsersModel = new UsersModel();
        $params = Request::only(['page'=>1,'rows'=>15,'id'=>null,'idcard'=>null,'realname'=>null], 'get');
        $field = 'id,idcard,mobile,realname,birthday,create_time,photo';
        $data = $UsersModel->getUserIndex($params,$field,false);
        return json($data);
    }

    //关联查询（->belongsTo）
    public function getChildrenWeb(){
        $id = 22851;
        $UsersModel = new UsersModel();
        $data = $UsersModel->getChildrenWeb($id);
        return json($data);
    }

    //关联查询（->hasWhere）
    public function getChildrenWebHasWhere(){
        $id = 5451748;
        $UsersModel = new UsersModel();
        $data = $UsersModel->getChildrenWebHasWhere($id);
        return json($data);
    }

    //查询ES
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

    //查询ES
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

    //写入ES
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

    //推送队列
    public function pushQueue()
    {
        $data = array();
        $data['question'] = random_int(1,999);
        $data['update_time'] = date('Y-m-d H:i:s', time());
        $data['create_time'] = date('Y-m-d H:i:s', time());

        $job = 'app\index\job\LzpTestQueue';
        Queue::push($job, $data,'LzpTestQueue');
        return json(['code'=>0,'data'=>$data,'msg'=>'push成功！']);

//        在Linux查看任务数量，可以在终端输入：jobs -l
        
        //根目录执行
        //work模式——启动一个work进程执行消息队列
        //php think queue:work --queue LzpTestQueue

        //--queue  helloJobQueue  //要处理的队列的名称
        //--daemon            //是否循环执行，加上此参数则循环执行，如果不加该参数，则该命令处理完下一个消息就退出
        //--delay  0         //如果本次任务执行抛出异常且任务未被删除时，设置其下次执行前延迟多少秒,默认为0
        //--force            //系统处于维护状态时是否仍然处理任务，并未找到相关说明
        //--memory 128       //该进程允许使用的内存上限，以 M 为单位
        //--sleep  3         //如果队列中无任务，则sleep多少秒后重新检查(work+daemon模式)或者退出(listen或非daemon模式)
        //--tries  2          //如果任务已经超过尝试次数上限，则触发‘任务尝试次数超限’事件，默认为0


        //listen模式——创建一个父进程，由此父进程创建work子进程执行队列
        //php think queue:listen --queue LzpTestQueue

        //--queue  helloJobQueue    //监听的队列的名称
        //--delay  0         //如果本次任务执行抛出异常且任务未被删除时，设置其下次执行前延迟多少秒,默认为0
        //--memory 128       //该进程允许使用的内存上限，以 M 为单位
        //--sleep  3         //如果队列中无任务，则多长时间后重新检查，daemon模式下有效
        //--tries  0         //如果任务已经超过重发次数上限，则进入失败处理逻辑，默认为0
        //--timeout 6        //创建的work子进程的允许执行的最长时间，以秒为单位


    }








}
