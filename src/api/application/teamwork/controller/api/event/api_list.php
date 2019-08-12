<?php

namespace app\teamwork\controller\api\event;
use think\Controller;

class api_list extends Controller
{
    public function v_1_0($args)
    {
        
        $response = []; // 定义返回数组

        // 一、判断传入参数
        if(!isset($args['project_id'])){
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
        
        // 获取当前用户在项目中的权限
        $api_power = db('teamwork_project_participant')->where([
            'project_id' => $args['project_id'],
            'account_id' => ACCOUNT_ID,
            'status' => 1, // todo enum
        ])->value('power');

        if(!api_power($api_power)){
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '没有权限',
            ];
        }
        
        $where = [
            'project_id' => $args['project_id'],
        ];
        
        if(isset($args['event_name']) && $args['event_name']){
            $where['event_name'] = ['like', '%' . $args['event_name'] . '%'];
        }
        
        
        // 二、逻辑处理

        
        // 三、执行与返回
        $response['event_list'] = db('teamwork_api_event')->where($where)->select();
        return $response;


    }
}

