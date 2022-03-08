<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2019/11/29
 * Time: 16:33
 */

namespace app\common\command;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class OrganizationArea extends Command
{
    protected function configure()
    {
        $this->setName('OrganizationArea')->setDescription('批量更新组织地区');
    }

    protected function execute(Input $input, Output $output)
    {

            $re = Db::name('organization')->where('level','=',3)->where('province','=',0)->select();
            foreach ($re as $key =>$value) {
                foreach ($this->getArea(2, 218) as $kk => $vv) {
                    $is= false;
                    $is = strpos($value['full_name'], $vv);
                    if ($is!==false) {
                        $res = Db::name('organization')->where('node_path', 'like', $value['node_path'] . $value['id'] . ',%')->update(
                            ['city' => $kk
                            ]);
                        $res = Db::name('organization')->where('id', '=', $value['id'])->update(
                            ['city' => $kk
                            ]
                        );

                      echo 1111;
                        $re4 = Db::name('organization')->where('level', '=',4)->where('pid','=',$value['id'])->select();

                        foreach ($re4 as $key4 => $value4) {
                            foreach ($this->getArea(3, $kk) as $kk4 => $vv4) {
                                $is= false;
                                $is = strpos($value4['full_name'], $vv4);
                                if ($is!==false) {
                                    $res4 = Db::name('organization')->where('node_path', 'like', $value4['node_path'] . $value4['id'] . ',%')->update(
                                        ['area' => $kk4
                                        ]);
                                    $res4 = Db::name('organization')->where('id', '=', $value4['id'])->update(
                                        ['area' => $kk4
                                        ]
                                    );
                                    echo 2222;
                                    $re5 = Db::name('organization')->where('level', '>',4)->where('node_path', 'like', $value4['node_path'] . $value4['id'] . ',%')->select();
                                    foreach ($re5 as $key5 => $value5) {
                                        foreach ($this->getArea(4, $kk4) as $kk5 => $vv5) {
                                            $is= false;
                                            $is = strpos($value5['full_name'], $vv5);
                                            if ($is!==false) {
                                                $res5 = Db::name('organization')->where('node_path', 'like', $value5['node_path'] . $value5['id'] . ',%')->update(
                                                    ['street' => $kk5
                                                    ]);
                                                $res5 = Db::name('organization')->where('id', '=', $value5['id'])->update(
                                                    ['street' => $kk5
                                                    ]
                                                );

                                                echo 3333;
                                                continue;
                                            }
                                        }
                                    }

                                }
                            }
                        }
                    }
                }
            }

    }

    public function getArea($type,$pid){
       $re = Db::name('area')->field('id,name')->where(['level'=>$type,'topid'=>218,'parentid'=>$pid])->select();
       $kk = array_column($re,'name','id');
       return $kk;
    }

}