<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2019/10/9
 * Time: 16:41
 */

namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class UpdateAdmin extends Command
{
    protected function configure()
    {
        $this->setName('UpdateAdmin')->setDescription('批量更新管理员');
    }

    protected function execute(Input $input, Output $output)
    {

        $cursor = Db::name('organization')
            ->alias('o')
            ->field('o.id,o.name,o.code,a.id as dd')
            ->leftJoin('hbgqt_admin a','o.id = a.organize_id')
            ->where('a.id','null')
            ->cursor();

        foreach($cursor as $user){
            $data = [];
            $data['organize_id'] = $user['id'];
            $data['name'] = $user['name'];
            $data['account'] = $user['code'];

            $pass = 'hbgqt666';
            $salt = substr(uniqid(),3);
            $password = strtoupper(md5($pass . $salt));

            $data['password'] = $password;
            $data['salt'] = $salt;
            $data['is_admin'] = 1;
            $data['create_time'] =time();
            Db::name('admin')->insert($data);
            dump('ok');
        }
         dump('okd');
    }
}