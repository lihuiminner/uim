<?php

namespace app\deploy\controller\line;
use think\Controller;

class api_list extends Controller
{
    public function v_1_0($args)
    {
        
        $response = []; // 定义返回数组

        // 一、判断传入参数
        
        
        // 二、逻辑处理

        
        // 三、执行与返回
        $response['line_list'] = db('deploy_line')->select();

        // foreach($response['line_list'] as &$v){
        //     $v['leader_name'] = account_name($v['leader']);
        // }
        

        // 三、执行与返回
        return $response;


    }
}

