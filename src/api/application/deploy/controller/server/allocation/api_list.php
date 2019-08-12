<?php

namespace app\deploy\controller\server\allocation;
use think\Controller;

class api_list extends Controller
{
    public function v_1_0($args)
    {
        
        $response = []; // 定义返回数组

        // 一、判断传入参数

        $where = [];

        if(isset($args['line_id']) && $args['line_id']){
            $where['line_id'] = $args['line_id'];
        }

        if(isset($args['type']) && $args['type']){
            $where['line_id'] = $args['line_id'];
        }
        
        
        // 二、逻辑处理

        
        // 三、执行与返回
        $response['allocation_list'] = db('deploy_server_allocation')->where($where)->select();
        

        // 三、执行与返回
        return $response;


    }
}

