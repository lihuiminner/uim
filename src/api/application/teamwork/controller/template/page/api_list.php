<?php

namespace app\teamwork\controller\template\page;
use think\Controller;

class api_list extends Controller
{
    public function v_1_0($args)
    {
        
        $response = []; // 定义返回数组

        // 一、判断传入参数
        if(!isset($args['category_id'])){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '非法操作'
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
        $where = [
            'project_id' => $category['project_id'],
            'category_id' => $args['category_id'],
        ];
        
        
        // 二、逻辑处理

        
        // 三、执行与返回
        $response['template_list'] = db('teamwork_template_page')->where($where)->select();
        

        // 三、执行与返回
        return $response;


    }
}

