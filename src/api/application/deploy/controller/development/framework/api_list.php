<?php

namespace app\deploy\controller\development\framework;
use think\Controller;

class api_list extends Controller
{
    public function v_1_0($args)
    {
        $response = []; // 定义返回数组
        
        // 一、判断传入参数
        
        // 二、逻辑处理
        $response['framework_list'] = db('development_framework')
            ->where('language_id', $args['language_id'])
            ->field('framework_id,framework_name,description')
            ->select();
        
        // 三、执行与返回
        return $response;

    }
}

