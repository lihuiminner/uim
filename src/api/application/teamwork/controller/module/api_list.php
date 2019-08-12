<?php

namespace app\teamwork\controller\module;
use think\Controller;

class api_list extends Controller
{
    public function v_1_0($args)
    {
        
        $response = []; // 定义返回数组

        // 一、判断传入参数
        if(!isset($args['parent_id'])){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '非法操作，parent_id'
            ];
        }
        if(!isset($args['project_id']) || !$args['project_id']){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '非法操作，project_id'
            ];
        }
        if($args['parent_id'] && !teamwork_module_participant($args['parent_id'])){
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '没有权限',
            ];
        }
        if(!teamwork_project_participant($args['project_id'])){
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '没有权限',
            ];
        }
        
        $where = [
            'project_id' => $args['project_id'],
            'parent_id' => $args['parent_id'],
        ];
        
        
        // 二、逻辑处理
        $response['module_list'] = db('teamwork_module')
            ->where($where)
            ->order('order_index DESC, module_id DESC')
            ->select();

        // foreach($response['module_list'] as &$v){
        //     $v['leader_name'] = account_name($v['leader']);
        // }

        // 三、执行与返回
        return $response;


    }
}

