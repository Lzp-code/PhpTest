<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2020/8/10
 * Time: 13:57
 */

namespace app\common\command;


use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\Exception;
use think\exception\PDOException;
use think\facade\Log;

class UpdateSyncLog extends Command
{
    protected function configure()
    {
        $this->setName('UpdateSyncLog')->setDescription('更新Tidb');
    }

    private function getSqlState(PDOException $exception) {
        $sqlstate = $exception->getData()['PDO Error Info']['SQLSTATE'];
        return empty($sqlstate) ? '' : $sqlstate;
    }

    protected function execute(Input $input, Output $output)
    {
        \think\facade\Log::init(['type' => 'File', 'level' => ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info']]);
        $output->writeln('更新开始');
        Log::close();
        while (1){
            //TODO: 改成分页
             Db::name('sync_log')
                ->where('status','=',0)
                ->chunk(1000, function($datas) {

                    foreach ($datas as $key => $data) {
                        try {
                            $_insertData = json_decode($data['data'], 1);
                            if ($data['action'] == 'insert') {
                                try {
                                    Db::connect('hebei_tidb_databases')
                                        ->table($data['tb_name'])
                                        ->insert($_insertData);
                                } catch (PDOException $exception) {
                                    $sqlstate = $this->getSqlState($exception);
                                    if ($sqlstate === '23000') { // 重复, 检查一下tidb是否同样的代码
                                        Db::connect('hebei_tidb_databases')
                                            ->table($data['tb_name'])
                                            ->where('id', '=', $data['tb_id'])
                                            ->update($_insertData);
                                    } else {
                                        throw $exception;
                                    }
                                }
                            } elseif ($data['action'] == 'update') {
                                $count = Db::connect('hebei_tidb_databases')
                                    ->table($data['tb_name'])
                                    ->where('id', '=', $data['tb_id'])
                                    ->update($_insertData);
                                if ($count === 0) {
                                    //为了避免更新了数据没有变
                                    try {
                                        Db::connect('hebei_tidb_databases')
                                            ->table($data['tb_name'])
                                            ->insert($_insertData);
                                    } catch (PDOException $exception) {
                                        $sqlstate = $this->getSqlState($exception);
                                        if ($sqlstate === '23000') { // 重复, 检查一下tidb是否同样的代码
                                            Db::connect('hebei_tidb_databases')
                                                ->table($data['tb_name'])
                                                ->where('id', '=', $data['tb_id'])
                                                ->update($_insertData);
                                        } else {
                                            throw $exception;
                                        }
                                    }
//                                    Db::connect('hebei_tidb_databases')
//                                        ->table($data['tb_name'])
//                                        ->insert($_insertData);
                                }
                            } elseif ($data['action'] == 'delete') {
                                Db::connect('hebei_tidb_databases')
                                    ->table($data['tb_name'])
                                    ->where('id', '=', $data['tb_id'])
                                    ->delete();
                            }
                            Db::name('sync_log')->where('id', '=', $data['id'])->update([
                                'status' => 1,
                                'last_modified_time' => date('Y-m-d H:i:s', time())
                            ]);
                        } catch (Exception $e) {
                            var_dump($e->getMessage());
                            Log::write($e->getMessage(), 'error');
                        }
                    }
                });
        }
    }
}