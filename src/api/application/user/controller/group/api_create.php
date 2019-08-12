<?php

namespace app\user\controller\group;
use think\Controller;

class api_create extends Controller
{
    public function v_1_0($args)
    {
        
        $response = []; // 定义返回数组
        
        // 一、判断传入参数
        if(!ACCOUNT_ID){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '登录失效'
            ];
        }
        if (!isset($args['group_type']) || !$args['group_type'] || !in_array($args['group_type'], [1,2,3,4,5])) {
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '参数错误：group_type',
            ];
        }
        if (!isset($args['group_name']) || !$args['group_name']) {
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '参数错误：group_name',
            ];
        }
        $parent_id = isset($args['parent_id']) ? $args['parent_id'] : 0;
        if($parent_id){
            // todo 判断当前用户在该组织中的权限
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '暂不支持创建下级组织',
            ];
        }

        $has_group_name = db('user_group')->where([
            'parent_id' => $parent_id,
            'group_name' => $args['group_name'],
        ])->value('group_id');

        if($has_group_name){
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '团队名称已存在',
            ];
        }

        // 二、逻辑处理
        db()->startTrans();
        
        $group_id = db('user_group')->insert([
            'parent_id' => $parent_id,
            'group_type' => $args['group_type'],
            'group_name' => $args['group_name'],
            'leader' => ACCOUNT_ID,
            'create_time' => DATE_TIME,
            'status' => 1, // todo enum
            'description' => $args['description'],
            'head_portrait' => 'open.uim.site/avatar/default.png',
            'member_count' => 1,
            'child_group_count' => 0,
        ], '', true);

        $member_id = db('user_group_member')->insert([
            'group_id' => $group_id,
            'account_id' => ACCOUNT_ID,
            'join_time' => DATE_TIME,
            'relation_name' => '',
            'last_send_message_id' => 0,
            'last_read_message_id' => 0,
        ], '', true);

        if(!$group_id || !$member_id){
            db()->rollback();
            return [
                '_error_code' => 13,
                '_error_message' => 'operation failed',
                '_error_description' => '操作失败',
            ];
        }
        db()->commit();
        
        $response['group_id'] = $group_id;

        // 三、执行与返回
        return $response;
    }
}

