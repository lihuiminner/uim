<?php

namespace app\deploy\controller\app;
use think\Controller;

class api_list extends Controller
{
    public function v_1_0($args)
    {
        
        $response = []; // 定义返回数组

        // 一、判断传入参数
        if(!isset($args['project_id']) || !isset($args['parent_id'])){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '非法操作'
            ];
        }

        $project = db('teamwork_project')->field('group_id,status')->where('project_id', $args['project_id'])->find();
        if(!$project || !group_member($project['group_id'], ACCOUNT_ID)){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '非法操作'
            ];
        } else if($project['status'] == 4){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '项目状态异常' // todo enum.text
            ];
        }
        
        $where = [
            'project_id' => $args['project_id'],
            'parent_id' => $args['parent_id'],
        ];
        
        
        // 二、逻辑处理

        
        // 三、执行与返回
        $response['app_list'] = db('deploy_app')->where($where)->select();

        foreach($response['app_list'] as &$v){
            $v['leader_name'] = account_name($v['leader']);
        }
        

        // 三、执行与返回
        return $response;


    }
}

