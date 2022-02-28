<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 2019/11/5
 * Time: 14:30
 */

namespace app\index\logic;
use org\ElasticSearchApi;
use org\Response;
use think\Db;

class Es
{
    /**
     *  路由：es/delOneOrganize   get
     *  Es删除组织
     * @author weijian.chen
     */
    public function del($id){
        if(empty($id)) return false;

        $elastic = new ElasticSearchApi('organization', 'organization');
        $elastic->delete_document($id);
    }


    /**
     * 路由： es/updateOneOrganize   get
     * 在ES内更新单条组织信息
     * @author weijian.chen
     */
    public function update($id){
        if(empty($id)) return false;
        $org_info = Db::name('organization')->where('id','=',$id)->find();

        if(empty($org_info)){
            return Response::response(1001,[],'组织信息不存在');
        }
        unset($org_info['date']);
        $res = (new ElasticSearchApi('organization', 'organization'))->index_document($org_info, 'id');
    }

    /**
     * 路由：es/addOneOrganize get
     * 添加新的组织到ES内
     * @author weijian.chen
     */
    public function add($id){
        if(empty($id)) return false;
        $org_info = Db::name('organization')->where('id','=',$id)->find();

        if(empty($org_info)){
            return Response::response(1001,[],'组织信息不存在');
        }
        unset($org_info['date']);

        $elastic = new ElasticSearchApi('organization', 'organization');
        $result = $elastic->index_document($org_info,'id');
    }


    /**
     * 路由： es/delOrganizeIndex
     * 删除索引
     * @author weijian.chen
     */
    public function delIndex(){
        $elastic = new ElasticSearchApi('organization', 'organization');
        $result = $elastic->delete_index();
        return Response::response(0, $result);
    }


    /**
     * 路由：es/createOrgIndex
     * 创建索引
     * @author weijian.chen
     */
    public function createIndex()
    {
        $attrList = [
            'id' => [
                'type' => 'long',
            ],
            'type' => [
                'type' => 'byte',
            ],
            'code' => [
                'type' => 'text'
            ],
            'name' => [
                'type' => 'text',
                "analyzer" => "ik_max_word",
                "search_analyzer" => "ik_max_word"
            ],
            'full_name' => [
                'type' => 'text',
                "analyzer" => "ik_max_word",
                "search_analyzer" => "ik_max_word"
            ],
            'photo' => [
                'type' => 'text'
            ],
            'industry' => [
                'type' => 'short'
            ],
            'organize_type' => [
                'type' => 'short'
            ],
            'address' => [
                'type' => 'text'
            ],
            'person' => [
                'type' => 'text',
            ],
            'contact' => [
                'type' => 'text',
            ],
            'email' => [
                'type' => 'text'
            ],
            'date' => [
                'type' => 'text',
            ],
            'registration_authority' => [
                'type' => 'text',
            ],
            'service_area' => [
                'type' => 'text',
            ],
            'pid' => [
                'type' => 'long'
            ],
            'node_path' => [
                'type' => 'text',
            ],
            'nodetype' => [
                'type' => 'byte',
            ],
            'level' => [
                'type' => 'byte',
            ],
            'create_time' => [
                'type' => 'integer',
            ],
            'update_time' => [
                'type' => 'integer',
            ],
            'role_id' => [
                'type' => 'integer',
            ],
            'is_deleted' => [
                'type' => 'byte',
            ],
            'higher_authority' => [
                'type' => 'text',
            ],
            'total_member' => [
                'type' => 'integer',
            ],
            'is_flow' => [
                'type' => 'byte',
            ],
            'otype' => [
                'type' => 'byte',
            ],
            'is_organs' => [
                'type' => 'byte',
            ],
            'organs_name' => [
                'type' => 'text',
            ],
            'organs_type' => [
                'type' => 'short',
            ],
            'province' => [
                'type' => 'integer',
            ],
            'city' => [
                'type' => 'integer',
            ],
            'area' => [
                'type' => 'integer',
            ],
            'street' => [
                'type' => 'integer',
            ],
            'dissolution' => [
                'type' => 'byte',
            ],
            'import_time' => [
                'type' => 'integer',
            ]
        ];

        $elastic = new ElasticSearchApi('organization', 'organization');
        $result = $elastic->create_mappings_index($attrList);
        return Response::response(0,['res'=>$result]);
    }

}