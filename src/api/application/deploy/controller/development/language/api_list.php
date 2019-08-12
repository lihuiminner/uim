<?php

namespace app\deploy\controller\development\language;
use think\Controller;

class api_list extends Controller
{
    public function v_1_0($args)
    {
        $response = []; // 定义返回数组
        
        // 一、判断传入参数
        
        // 二、逻辑处理
        $response['language_list'] = db('development_language')->select();
        
        // 三、执行与返回
        return $response;
    }
}

