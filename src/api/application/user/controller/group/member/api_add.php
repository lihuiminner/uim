<?php
// +----------------------------------------------------------------------
// | Developer [ AUTO CREATE API CODE TEMPLATE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 https://developer.uim.site All rights reserved.
// +----------------------------------------------------------------------
// | uim_api/application/open/controller/userCenter/group/api_addMember.php
// +----------------------------------------------------------------------

namespace app\open\controller\userCenter\group;
use think\Controller;

class api_addMember extends Controller
{

    // +----------------------------------------------------------------------
    // | API: 添加团队成员（open.userCenter.group.addMember.1_0） by 周俊
    // +----------------------------------------------------------------------
    // | Description: 
    // | 对应表：group_member；
    // | 将user_ids序列中的用户添加为群组成员；
    // | 需要判断user_ids中所有用户编号是否真实存在，且群组中不允许对同一成员重复添加操作；
    // +----------------------------------------------------------------------
    // | Author: 周俊
    // +----------------------------------------------------------------------
    // | DateTime: 2018-10-26 13:34:41
    // +----------------------------------------------------------------------
    public function v_1_0($args)
    {
        // 输入结构：
        /*$args =
        {
            //团队编号
            "group_id" : 1,
            //用户编号序列
            //多个user_id，使用英文逗号间隔
            "user_ids" : "1,2,3,4",
        }*/
        
        
        $response = []; // 定义返回数组
        
        // 一、判断传入参数
        if(!USER_ID){
            return [
                '_error_number' => 12,
                '_error_code' => 'authentication failed',
                '_error_description' =>  '登录失效'
            ];
        }
        if(!isset($args['group_id']) || empty($args['group_id'])){
            return [
                '_error_number' => 11,
                '_error_code' => 'input args error',
                '_error_description' => '团队不可为空',
            ];
        }
        if(!isset($args['user_ids']) || empty($args['user_ids'])){
            return [
                '_error_number' => 11,
                '_error_code' => 'input args error',
                '_error_description' => '成员不可为空',
            ];
        }
        $user_ids = explode(',',$args['user_ids']);
        $user_count = db('user')->where('user_id','in',$user_ids)->count();
        if(count($user_ids)!= $user_count){
            return [
                '_error_number' => 12,
                '_error_code' => 'authentication failed',
                '_error_description' => '包含非法用户',
            ];
        }
        $member_group = ['group_id'=>$args['group_id'],'user_id'=>['in',$user_ids]];
        $group_id = db('group_member')->where($member_group)->value('group_id');
        if($group_id){
            return [
                '_error_number' => 11,
                '_error_code' => 'input args error',
                '_error_description' => '成员不可重复添加',
            ];
        }
        // 二、逻辑处理
        foreach($user_ids as &$v) {
            $member_data = [
                'group_id' => $args['group_id'],
                'user_id' => $v,
                'relation_name' => db('user')->where('user_id', $v)->value('user_name'),
                'join_time' => date('Y-m-d H:i:s'),
            ];
          db('group_member')->insert($member_data, '', true);
        }

        // 三、执行与返回
        return $response;

        // 输出结构：
       /* $response =
        {
        }*/
        
        //
        // 错误列表：
        // return [
        //        '_error_number' => 11,
        //        '_error_code' => 'input args error',
        //        '_error_description' => '传入参数错误',
        // ];
        // return [
        //        '_error_number' => 12,
        //        '_error_code' => 'authentication failed',
        //        '_error_description' => '身份验证失败',
        // ];
        // return [
        //        '_error_number' => 13,
        //        '_error_code' => 'operation failed',
        //        '_error_description' => '操作失败',
        // ];
        // return [
        //        '_error_number' => 17,
        //        '_error_code' => 'no permission',
        //        '_error_description' => '没有权限',
        // ];
    }
}

