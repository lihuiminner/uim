<?php

namespace app\user\controller\group\member;
use think\Controller;

class api_list extends Controller
{
    public function v_1_0($args)
    {
        
        $response = []; // 定义返回数组
        
        // 一、判断传入参数
        
        if(!isset($args['group_id']) || !$args['group_id']){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '非法操作，group_id'
            ];
        }
        if(!group_member($args['group_id'], ACCOUNT_ID)){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '非法操作group_member'
            ];
        }
        $response['group_member_list'] = db('user_group_member')
            ->where('group_id', $args['group_id'])
            ->select();
            
        // 二、逻辑处理
        
        // 三、执行与返回
        return $response;

    }
}

