<?php

namespace app\teamwork\controller\project;
use think\Controller;

class api_create extends Controller
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

        if (!isset($args['project_name']) || !$args['project_name']) {
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '参数错误：project_name',
            ];
        }
        
        if (!isset($args['description'])) {
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '参数错误：description',
            ];
        }
        
        if (!isset($args['development_language_id']) || !db('development_language')->find($args['development_language_id'])) {
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '参数错误：development_language_id',
            ];
        }
        
        if (!isset($args['development_framework_id']) || !db('development_framework')->find($args['development_framework_id'])) {
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '参数错误：development_framework_id',
            ];
        }
        
        $has_project_name = db('teamwork_project')->where([
            'group_id' => $args['group_id'],
            'project_name' => $args['project_name'],
        ])->value('project_id');

        if($has_project_name){
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '项目名称已存在',
            ];
        }
        
        // 二、逻辑处理
        
        db()->startTrans();
        
        $project_id = db('teamwork_project')->insert([
            'project_name' => $args['project_name'],
            'description' => $args['description'],
            'leader' => ACCOUNT_ID,
            'group_id' => $args['group_id'],
            'create_time' => DATE_TIME,
            'update_time' => DATE_TIME,
            'status' => 1, // todo enum
            'development_language_id' => $args['development_language_id'],
            'development_framework_id' => $args['development_framework_id'],
        ], '', true);

        $participant_id = db('teamwork_project_participant')->insert([
            'project_id' => $project_id,
            'account_id' => ACCOUNT_ID,
            'join_time' => DATE_TIME,
            'status' => 1, // todo enum
            'power' => '*', // *表示所有权限
            'last_log_id' => 0,
        ], '', true);

        if(!$project_id || !$participant_id){
            db()->rollback();
            return [
                '_error_code' => 13,
                '_error_message' => 'operation failed',
                '_error_description' => '操作失败',
            ];
        }
        db()->commit();
        
        $response['project_id'] = $project_id;

        // 三、执行与返回
        return $response;
    }
}

