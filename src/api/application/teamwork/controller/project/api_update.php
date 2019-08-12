<?php
// +----------------------------------------------------------------------
// | Developer [ AUTO CREATE API CODE TEMPLATE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 https://developer.uim.site All rights reserved.
// +----------------------------------------------------------------------
// | uim_api/application/open/controller/userCenter/group/api_list.php
// +----------------------------------------------------------------------

namespace app\open\controller\userCenter\group;
use think\Controller;

class api_allGroup extends Controller
{

    // +----------------------------------------------------------------------
    // | API: 获取所有团队列表（open.userCenter.group.list.1_0） by 周俊
    // +----------------------------------------------------------------------
    // | Description:
    // | 返回所有团队列表；
    // +----------------------------------------------------------------------
    // | Author: 周俊
    // +----------------------------------------------------------------------
    // | DateTime: 2018-10-26 16:36:5
    // +----------------------------------------------------------------------
    public function v_1_0($args)
    {
        // 输入结构：
        /* $args =
         {
             //页码
             "page" : 2,
             //每页显示条数
             "limit" : 10,
             //关键字
             "keywords" : "加密算法",
         }*/


        $response = []; // 定义返回数组
        $where = [];
        // 一、判断传入参数
        if(!USER_ID){
            return [
                '_error_number' => 12,
                '_error_code' => 'authentication failed',
                '_error_description' =>  '登录失效'
            ];
        }
        $page= isset($args['page'])&&is_int($args['page']) ? $args['page'] : 1;
        $limit= isset($args['limit'])&&is_int($args['limit']) ? $args['limit'] : 15;
        if($limit > config('max_limit')){
            return [
                '_error_number' => 11,
                '_error_code' => 'input args error',
                '_error_description' => '获取的条数不能大于50',
            ];
        }
        if (isset($args['keywords']) && !empty($args['keywords'])) {
            $where['group_name'] = ["like", "%{$args['keywords']}%"];
        }
        // 二、逻辑处理
        $field = 'group_id,group_name, create_time, group_description, user_id, head_portrait,status';
        $order = 'group_id desc';
        $response['group_list'] = db('group')->where($where)->page($page,$limit)->order($order)->field($field)->select();
        $response['page_info']=[
            'page'=> $page,
            'limit'=> $limit,
            'total'=> db('group')->where($where)->count(),
        ];
        // 三、执行与返回
        return $response;

        // 输出结构：
        /* $response =
         {
             //团队列表
             "group_list" : [
                 {
                     //团队ID
                     "group_id" : 1,
                     //所属团队名称
                     "group_name" : "技术开发",
                     //群组头像
                     "head_portrait" : 2,
                     //状态
                     "status" : 0,
                     //用户编号
                     //对应user表主键
                     "user_id" : 1,
                 },
             ],
             //分页信息
             "page_info" : {
             //页码
             "page" : 2,
                 //每页显示条数
                 "limit" : 10,
                 //总条数
                 "total" : 360,
             },
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
    }
}

