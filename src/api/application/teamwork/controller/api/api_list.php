<?php

namespace app\teamwork\controller\api;
use think\Controller;

class api_list extends Controller
{
    public function v_1_0($args)
    {
        
        $response = []; // 定义返回数组

        // 一、判断传入参数
        if(!isset($args['module_id']) || !$args['module_id']){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '非法操作，module_id'
            ];
        }
        if($args['module_id'] && !teamwork_module_participant($args['module_id'])){
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '没有权限',
            ];
        }
        
        
        // 二、逻辑处理

        
        // 三、执行与返回
        $response['api_list'] = db('teamwork_api')
            ->where('module_id', $args['module_id'])
            ->order('api_code ASC, api_version ASC')
            ->select();
        

        // 三、执行与返回
        return $response;


    }
}

