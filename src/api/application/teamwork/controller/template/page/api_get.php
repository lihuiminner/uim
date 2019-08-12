<?php

namespace app\teamwork\controller\template\page;
use think\Controller;

class api_get extends Controller
{
    public function v_1_0($args)
    {
        
        $response = []; // 定义返回数组

        // 一、判断传入参数
        if(!isset($args['template_id'])){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '非法操作'
            ];
        }

        $template = db('teamwork_template_page')->where('page_template_id', $args['template_id'])->find();
        if(!$template){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '非法操作，模板不存在'
            ];
        }
        if(!teamwork_project_participant($template['project_id'])){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '没有权限'
            ];
        }
        
        
        // 二、逻辑处理

        
        // 三、执行与返回
        $response = $template;
        

        // 三、执行与返回
        return $response;


    }
}

