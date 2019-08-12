<?php

namespace app\teamwork\controller\module;
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
        // todo 后期将项目分为全开放、内部开放、不开放；全开放则可以有外部人员参与，内部开放则不限制内部人员访问，不开放则仅对参与人可见
        
        
        // 二、逻辑处理

        
        // 三、执行与返回
        $response['module_list'] = db('teamwork_module')->where($where)->select();

        foreach($response['module_list'] as &$v){
            $v['leader_name'] = account_name($v['leader']);
        }
        

        // 三、执行与返回
        return $response;


    }
}

