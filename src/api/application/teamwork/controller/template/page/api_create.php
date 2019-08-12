<?php

namespace app\teamwork\controller\template\page;
use think\Controller;

class api_create extends Controller
{
    public function v_1_0($args)
    {
        
        $response = []; // 定义返回数组
        
        // 一、判断传入参数
        if(!isset($args['category_id']) || !$args['category_id']){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '非法操作'
            ];
        }
        if(!isset($args['template_name']) || !$args['template_name']){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '参数错误：template_name'
            ];
        }
        if(!isset($args['template_content']) || !$args['template_content']){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '参数错误：template_content'
            ];
        }

        $category = db('teamwork_template_category')->where('category_id', $args['category_id'])->find();
        if(!$category){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '非法操作，分类不存在'
            ];
        }
        if(!teamwork_project_participant($category['project_id'])){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '没有权限'
            ];
        }


        
        // 二、逻辑处理
        
        $template_id = db('teamwork_template_page')->insert([
            'project_id' => $category['project_id'],
            'category_id' => $args['category_id'],
            // 'template_code' => $template_code,
            'template_name' => $args['template_name'],
            'template_content' => $args['template_content'],
            'creator' => ACCOUNT_ID,
            'create_time' => DATE_TIME,
            'status' => 1, // todo enum
        ], '', true);

        if(!$template_id){
            return [
                '_error_code' => 13,
                '_error_message' => 'operation failed',
                '_error_description' => '操作失败',
            ];
        }
        
        $response['template_id'] = $template_id;


        // 三、执行与返回
        return $response;
    }
}

