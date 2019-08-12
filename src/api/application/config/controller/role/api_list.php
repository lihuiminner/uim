<?php

namespace app\config\controller\role;
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
                '_error_description' =>  '非法操作2'
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
            'status' => 1, // todo enum
        ];
        // todo 判断是否有权限查看
        
        
        // 二、逻辑处理

        
        // 三、执行与返回
        $response['role_list'] = db('config_role')->where($where)->select();
        

        // 三、执行与返回
        return $response;


    }
}

