<?php

namespace app\teamwork\controller\template\page;
use think\Controller;

class api_update extends Controller
{
    public function v_1_0($args)
    {
        
        $response = []; // 定义返回数组
        
        // 一、判断传入参数
        if(!isset($args['template_id']) || !$args['template_id']){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '非法操作'
            ];
        }
        // if(!isset($args['template_name']) || !$args['template_name']){
        //     return [
        //         '_error_code' => 12,
        //         '_error_message' => 'authentication failed',
        //         '_error_description' =>  '参数错误：template_name'
        //     ];
        // }
        if(!isset($args['template_content']) || !$args['template_content']){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '参数错误：template_content'
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
        
        db('teamwork_template_page')
        ->where('page_template_id', $args['template_id'])
        ->limit(1)->update([
            // 'template_name' => $args['template_name'],
            'template_content' => $args['template_content'],
        ]);


        // 三、执行与返回
        return $response;
    }
}

