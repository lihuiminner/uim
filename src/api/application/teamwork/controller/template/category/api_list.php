<?php

namespace app\teamwork\controller\template\category;
use think\Controller;

class api_list extends Controller
{
    public function v_1_0($args)
    {
        
        $response = []; // 定义返回数组

        // 一、判断传入参数
        if(!isset($args['project_id']) || !isset($args['parent_id']) || !isset($args['type']) || !in_array($args['type'], [1,2,3,4])){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '非法操作'
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
            'type' => $args['type'],
        ];
        
        
        // 二、逻辑处理

        
        // 三、执行与返回
        $response['category_list'] = db('teamwork_template_category')->where($where)->select();
        

        // 三、执行与返回
        return $response;


    }
}

