<?php


namespace app\newexportdata\logic;


use app\study\model\ExportFile;
use think\Exception;
use think\facade\Log;

class Comm
{
    protected static $perSize = 50000;

    public static function setSize($val){
        self::$perSize = $val;
    }

    /**
     * 异步分块数据导出拆分生成压缩文件
     */
    public static function downLoadZip($Model,$field,$className,$fileID){
            $nowdate =  date('Y-m-d');
            set_time_limit(0);
            ini_set('memory_limit', '1024M');

            //获取总数，分页循环处理
            $accessNum = $Model->count();
            $fileNameArr = array();
            $perSize = self::$perSize;
            $pages   = ceil($accessNum / $perSize);
            $dir = config('common.export_zip_path').'/ExportData/'.$nowdate.'/';

            if(!is_dir($dir)){
                mkdir($dir,0777,true);
                chmod($dir,0777);
            }

            for($i = 1; $i <= $pages; $i++) {
                $columns = $field;//重新实例化第一行 不然乱码
                $filename = $dir.time().random_int(1,100).'导出数据.csv';
                $fp = fopen($filename, 'w'); //生成临时文件
                mb_convert_variables('GBK', 'UTF-8', $columns);
                fputcsv($fp, $columns);//将数据格式化为CSV格式并写入到output流中
                $fileNameArr[] = $filename;
                $db_data = $Model->page($i,$perSize)->select();
                foreach($db_data as $key => $value) {
                    $rowData = call_user_func($className."::setData",$value);
                    //获取每列数据，转换处理成需要导出的数据
                    //需要格式转换，否则会乱码
                    mb_convert_variables('GBK', 'UTF-8', $rowData);
                    fputcsv($fp, $rowData);
                }
                //每生成一个文件关闭
                fclose($fp);
                //释放变量的内存
                unset($db_data);
            }
            //进行多个文件压缩
            $zip = new \ZipArchive();
            $zipFileName = time().random_int(1,100).'导出数据.zip';
            $filename = $dir.$zipFileName;
            $zip->open($filename, \ZipArchive::CREATE);   //打开压缩包
            foreach ($fileNameArr as $file) {
                $zip->addFile($file, basename($file));   //向压缩包中添加文件
            }
            $zip->close();  //关闭压缩包
            foreach ($fileNameArr as $file) {
                if(file_exists($file))
                {
                    @unlink($file); //删除csv临时文件
                }
            }
            //修改文件生成状态并更新文件路径
            ExportFile::where('id',$fileID)->update(['file_path'=>$nowdate.'/'.$zipFileName,'status'=>1,'update_time'=>time()]);
            return true;
    }


    /**
     * 异步分块数据导出拆分生成压缩文件
     */
    public static function downLoadZipNew($Model,$field,$className,$fileID,$where = []){
        $nowdate =  date('Y-m-d');
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        self::setSize(5000);
        //获取总数，分页循环处理
        $accessNum = $Model->where($where)->count();
        if($accessNum <= 0){
            ExportFile::where('id',$fileID)->update(['status'=>-1,'error_msg'=>'导出数据为空','update_time'=>time()]);
            return  false;
        }
        $fileNameArr = array();
        $perSize = self::$perSize;
        $pages   = ceil($accessNum / $perSize);
        $dir = config('common.upload_protected_path').'/ExportData/'.$nowdate.'/';
        if(!is_dir($dir)){
            mkdir($dir,0777,true);
            chmod($dir,0777);
        }

        for($i = 1; $i <= $pages; $i++) {
            $columns = $field;//重新实例化第一行 不然乱码
            $filename = $dir.time().random_int(1,100).'导出数据.csv';
            $fp = fopen($filename, 'w'); //生成临时文件
            mb_convert_variables('GBK', 'UTF-8', $columns);
            fputcsv($fp, $columns);//将数据格式化为CSV格式并写入到output流中
            $fileNameArr[] = $filename;
            $db_data = $Model->where($where)->page($i,$perSize)->select();
            foreach($db_data as $key => $value) {
                $rowData = call_user_func($className."::setDataCsv",$value);
                //获取每列数据，转换处理成需要导出的数据
                //需要格式转换，否则会乱码
                mb_convert_variables('GBK', 'UTF-8', $rowData);
                fputcsv($fp, $rowData);
            }
            //每生成一个文件关闭
            fclose($fp);
            //释放变量的内存
            unset($db_data);
        }
        //进行多个文件压缩
        $zip = new \ZipArchive();
        $zipFileName = time().random_int(1,100).'导出数据.zip';
        $filename = $dir.$zipFileName;
        $zip->open($filename, \ZipArchive::CREATE);   //打开压缩包
        foreach ($fileNameArr as $file) {
            $zip->addFile($file, basename($file));   //向压缩包中添加文件
        }
        $zip->close();  //关闭压缩包
        foreach ($fileNameArr as $file) {
            if(file_exists($file))
            {
                @unlink($file); //删除csv临时文件
            }
        }
        //修改文件生成状态并更新文件路径
        ExportFile::where('id',$fileID)->update(['file_path'=>$nowdate.'/'.$zipFileName,'status'=>1,'update_time'=>time()]);
        return true;
    }

    /**
     * @param  string  $csvFileName [description] 文件名
     * @param  array   $dataArr     [description] 数据
     * @param  array  $haderText   [description] 标题
     * 生成要下载的CSV文件
     * @author weijianchen
     */
    public static function downLoadCsv($fileID,$dataArr,$haderText=''){
            if(!$dataArr){
                ExportFile::where('id',$fileID)->update(['status'=>-1,'error_msg'=>'导出数据为空','update_time'=>time()]);
                return false;
            }
            $nowdate =  date('Y-m-d');
            $filePath = config('common.upload_protected_path').'/ExportData/'.$nowdate.'/';
            if(!is_dir($filePath)){
                mkdir($filePath,0777,true);
                chmod($filePath,0777);
            }

            $filename = time().random_int(100,999).'导出数据.csv';

            //        判断是否定义头标题
            $string="\xEF\xBB\xBF";
            if(!empty($haderText)){
                foreach ($haderText as $key => $value) {
                    $haderText[$key] = $value??iconv('utf-8','gb2312',$value);
                }
                $string .= implode(",",$haderText)."\n"; //用英文逗号分开
            }

            if(!empty($dataArr)){
                foreach ($dataArr as $key => $value) {
                    foreach ($value as $k => $val) {
                        $value[$k]= $value[$k]??iconv('utf-8','gb2312',$value[$k]);
                    }
                    $string .= implode(",",$value)."\n"; //用英文逗号分开
                }
            }


            file_put_contents($filePath.$filename, $string);
            //修改文件生成状态并更新文件路径
            ExportFile::where('id',$fileID)->update(['file_path'=>$nowdate.'/'.$filename,'status'=>1,'update_time'=>time()]);
            return true;
    }

}