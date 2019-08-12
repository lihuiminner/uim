<?php

namespace app\user\controller\group;
use think\Controller;

class api_list extends Controller
{
    public function v_1_0($args)
    {
        
        $response = []; // 定义返回数组
        
        // 一、判断传入参数
        $joined_group = db('user_group_member')
            ->where('account_id', ACCOUNT_ID)
            ->column('group_id');
            
        // 二、逻辑处理
        $response['group_list'] = db('user_group')
            ->where('group_id', 'in', $joined_group)
            ->select();
        
        // 三、执行与返回
        return $response;

    }
}

