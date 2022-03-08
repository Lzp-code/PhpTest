<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/17
 * Time: 15:44
 */

namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;
use Potting\ElasticSearchApi;

class UpdateEsOrgInfo extends Command
{
    protected function configure()
    {
        $this->setName('UpdateEsOrgInfo')->setDescription('update_org_info');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('组织 数据更新开始');

        Db::name('organization')->field('date',true)->chunk(1000,function($org_info){
            $elastic = new ElasticSearchApi('organization', 'organization');
            dump($elastic->bulk_index_document($org_info, 'id'));
        });

        $output->writeln('组织 数据更新结束');
    }




}