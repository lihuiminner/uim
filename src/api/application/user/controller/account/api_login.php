<?php

namespace app\user\controller\account;
use think\Controller;

class api_login extends Controller
{
    public function v_1_0($args)
    {

        $response = []; // 定义返回数组

        // 一、判断传入参数
        if (!$args['user_name'] || !$args['login_password']) {
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '参数错误',
            ];
        }

        // $where['user_name|mobile|email'] = $args['user_name'];
        $where['mobile'] = $args['user_name'];

        $user_account = db('user_account')->where($where)->find();
        if(!$user_account){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' => '手机号未注册',
            ];
        }

        // 找到用户后先验证login_password是否正确，再验证是否授权APP_ID；
        if($user_account['login_password'] != $args['login_password']){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' => '用户名密码错误',
            ];
        }
        
        // 二、逻辑处理
        
        // 验证是否授权APP_ID
        $auth_id = db('user_app_auth')->where([
            'account_id' => $user_account['account_id'],
            'app_id' => APP_ID,
            'status' => 1, // todo enum
        ])->value('auth_id');

        if(!$auth_id){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' => '未授权APP_ID',
            ];
        }

        //根据APP_ID获取应用表中的role_ids字段，
        $role_ids = db('deploy_app')->where('app_id', APP_ID)->value('role_ids');

        // 查询用户在该应用下绑定的角色
        $role_bind = db('user_role_bind')->where([
            'account_id' => $user_account['account_id'],
            'status'     => 1, // todo enum
            'role_id'    => ['in', $role_ids],
        ])->column('role_id');

        // 判断用户在该应用下是否绑定了角色
        if(!$role_bind){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' => '未分配角色',
            ];
        }

        // 三、执行与返回
        
        // 查询该用户在这个应用这绑定的角色列表
        $role_list = db('config_role')->where([
            'project_id' => PROJECT_ID,
            'status' => 1, // todo enum
            'role_id' => ['in', $role_bind],
        ])->field('role_id, role_name')->select();

        $response['role_list'] = $role_list;

        // 创建session
        $account_id = $user_account['account_id'];
        
        $session_id = db('user_session')->insert([
            'app_id' =>APP_ID,
            'create_ip' =>CLIENT_IP,
            'client_id' =>CLIENT_ID,
            'account_id' => $account_id,
            'create_time' =>DATE_TIME,
            'update_time' =>DATE_TIME,
        ], '', true);

        // 客户端不一定完全执行该接口的后续缓存操作，因此本接口中亦无须使用事务
        db('requset_client')->where('client_id', CLIENT_ID)->limit(1)->update([
            'session_id' => $session_id,
        ]);
        
        if(!$session_id){
            return [
                '_error_code' => 14,
                '_error_message' => 'no changed',
                '_error_description' => '数据库错误',
            ];
        }
        
        // todo: 三方登录

        $response['id'] = $session_id * 1;
        $response['account_id'] = $account_id * 1;
        $response['head_portrait'] = $user_account['head_portrait'];
        if($user_account['identity_id']){
            $identity = db('user_identity')->find($user_account['identity_id']);
            $response['first_name'] = $identity['first_name'];
            $response['last_name'] = $identity['last_name'];
            $response['sex'] = $identity['sex'];
        } else {
            $response['first_name'] = '';
            $response['last_name'] = '';
            $response['sex'] = '';
        }

        return $response;
    }
}

