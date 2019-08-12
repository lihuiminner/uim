<?php

namespace app\config\controller\power;
use think\Controller;

class api_create extends Controller
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

        if (!isset($args['power_name']) || !$args['power_name']) {
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '参数错误：power_name',
            ];
        }
        if (!isset($args['power_code']) || !$args['power_code']) {
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '参数错误：power_code',
            ];
        }
        if (!isset($args['order_index']) || !is_int($args['order_index'])) {
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '参数错误：order_index',
            ];
        }
        if (!isset($args['ext_data'])) {
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '参数错误：ext_data',
            ];
        }
        
        if (!isset($args['description'])) {
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '参数错误：description',
            ];
        }
        

        // 获取当前用户在项目（或上级模块）中的权限
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
        
        $has_power_name = db('config_power')->where([
            'project_id' => $args['project_id'],
            'parent_id' => $args['parent_id'],
            'power_name|power_code' => ['in', [$args['power_name'], $args['power_code']]],
        ])->value('power_id');

        if($has_power_name){
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '权限名称或代码已存在',
            ];
        }


        
        // 二、逻辑处理

        // todo 未来可将接口传入参数的验证统一使用js写一遍，同时用于前端交互验证和socket 服务端验证（做成配置式，下拉选择类型，是否必须，当其他键符合条件时为必须或非必须）
        

        db()->startTrans();

        $power_id = db('config_power')->insert([
            'project_id' => $args['project_id'],
            'parent_id' => $args['parent_id'],
            'power_name' => $args['power_name'],
            'power_code' => $args['power_code'],
            'ext_data' => $args['ext_data'],
            'description' => $args['description'],
            'order_index' => $args['order_index'],
            'creator' => ACCOUNT_ID,
            'create_time' => DATE_TIME,
            'status' => 1, // todo enum
            'children_count' => 0,
        ], '', true);

        if(!$power_id){
            db()->rollback();
            return [
                '_error_code' => 13,
                '_error_message' => 'operation failed',
                '_error_description' => '操作失败',
            ];
        }

        if($args['parent_id'] != 0) {
            db('config_power')->where('power_id',$args['parent_id'])->limit(1)->setInc('children_count', 1);
        }
        
        db()->commit();

        $response['power_id'] = $power_id;

        // 三、执行与返回
        return $response;
    }
}

