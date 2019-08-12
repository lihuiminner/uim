<?php

namespace app\teamwork\controller\project;
use think\Controller;

class api_list extends Controller
{
    public function v_1_0($args)
    {
        
        $response = []; // 定义返回数组

        // 一、判断传入参数
        
        if(!group_member($args['group_id'], ACCOUNT_ID)){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '非法操作'
            ];
        }
        
        
        // 二、逻辑处理

        
        // 三、执行与返回
        $response['project_list'] = db('teamwork_project')->where('group_id', $args['group_id'])->select();

        foreach($response['project_list'] as &$v){
            $v['leader_name'] = account_name($v['leader']);
            $v['development_language_name'] = db('development_language')->where('language_id', $v['development_language_id'])->value('language_name');
            $v['development_framework_name'] = db('development_framework')->where('framework_id', $v['development_framework_id'])->value('framework_name');
        }
        

        // 三、执行与返回
        return $response;


    }
}

