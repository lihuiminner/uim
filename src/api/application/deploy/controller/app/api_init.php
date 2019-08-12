<?php

namespace app\deploy\controller\app;
use think\Controller;

class api_init extends Controller
{
    public function v_1_0($args)
    {

        $response = []; // 定义返回数组

        // 一、判断传入参数
        
        // 二、逻辑处理
            
        $response['last_cache_id'] = db('deploy_cache_update')
            ->where('app_id', APP_ID)
            ->max('update_id') * 1;
            
        $js_code = db('deploy_app_init_code')
            ->where('app_id', APP_ID)
            ->order('order_index ASC')
            ->column('js_code');

        $response['js_code'] = implode(';', $js_code);

        // 三、执行与返回
        return $response;
    }
}

