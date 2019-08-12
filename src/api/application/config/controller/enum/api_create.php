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

        if (!isset($args['module_name']) || !$args['module_name']) {
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '参数错误：module_name',
            ];
        }
        if (!isset($args['module_code']) || !$args['module_code']) {
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '参数错误：module_code',
            ];
        }
        
        if (!isset($args['description'])) {
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '参数错误：description',
            ];
        }
        


        $parent_id = 0;
        $project_id = 0;
        $api_power = '';

        // 传入参数 parent_id 与 project_id 二选一
        if(isset($args['parent_id']) && $args['parent_id']){
            
            $parent_id = $args['parent_id'];

            $module_participant = db('teamwork_module_participant')->where([
                'module_id' => $parent_id,
                'account_id' => ACCOUNT_ID,
                'status' => 1, // todo enum
            ])->field('project_id, power')->find();
            // 获取当前用户在上级模块中的权限
            if($module_participant){
                $api_power = $module_participant['power'];
                $project_id = $module_participant['project_id'];
            }
        }else{
            if (!isset($args['project_id']) || !$args['project_id']) {
                return [
                    '_error_code' => 11,
                    '_error_message' => 'input args error',
                    '_error_description' => '参数错误：project_id',
                ];
            }

            $project_id = $args['project_id'];

            // 获取当前用户在项目（或上级模块）中的权限
            $api_power = db('teamwork_project_participant')->where([
                'project_id' => $project_id,
                'account_id' => ACCOUNT_ID,
                'status' => 1, // todo enum
            ])->value('power');
        }

        if(!api_power($api_power)){
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '没有权限',
            ];
        }
        
        $has_module_name = db('teamwork_module')->where([
            'project_id' => $args['project_id'],
            'module_name|module_code' => ['in', [$args['module_name'], $args['module_code']]],
        ])->value('module_id');

        if($has_module_name){
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '模块名称或代号已存在',
            ];
        }


        
        // 二、逻辑处理

        // todo 未来可将接口传入参数的验证统一使用js写一遍，同时用于前端交互验证和socket 服务端验证（做成配置式，下拉选择类型，是否必须，当其他键符合条件时为必须或非必须）
        
        db()->startTrans();
        
        $module_id = db('teamwork_module')->insert([
            'project_id' => $project_id,
            'parent_id' => $parent_id,
            'module_code' => $args['module_code'],
            'module_name' => $args['module_name'],
            'description' => $args['description'],
            'leader' => ACCOUNT_ID,
            'create_time' => DATE_TIME,
            'update_time' => DATE_TIME,
            'status' => 1, // todo enum
        ], '', true);

        $participant_id = db('teamwork_module_participant')->insert([
            'project_id' => $project_id,
            'module_id' => $module_id,
            'account_id' => ACCOUNT_ID,
            'join_time' => DATE_TIME,
            'status' => 1, // todo enum
            'power' => '*', // *表示所有权限
            'last_log_id' => 0,
        ], '', true);

        if(!$module_id || !$participant_id){
            db()->rollback();
            return [
                '_error_code' => 13,
                '_error_message' => 'operation failed',
                '_error_description' => '操作失败',
            ];
        }
        db()->commit();
        
        $response['module_id'] = $module_id;

        // 三、执行与返回
        return $response;
    }
}

