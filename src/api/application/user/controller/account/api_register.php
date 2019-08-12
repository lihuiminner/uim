<?php

namespace app\user\controller\account;
use think\Controller;

class api_register extends Controller
{
    public function v_1_0($args)
    {

        $response = []; // 定义返回数组

        // 一、判断传入参数
        if(!isset($args['mobile_check_sn']) || !$args['mobile_check_sn'] || !isset($args['mobile_check_code']) || !$args['mobile_check_code']){
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '参数错误',
            ];
        }
        
        // 二、逻辑处理
        $mobile_is_exist = db('user_account')->where('mobile', $args['mobile'])->value('account_id');
        if($mobile_is_exist){
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '该手机号已存在',
            ];
        }
        $mobile_check_code = db('user_check_code_mobile')->field('code,mobile,check_time,client_id')->find($args['mobile_check_sn']);
        if(!$mobile_check_code || $mobile_check_code['check_time'] || $mobile_check_code['mobile'] != $args['mobile'] || $mobile_check_code['client_id'] != CLIENT_ID){
            return [
                '_error_code' => 14,
                '_error_message' => 'no changed',
                '_error_description' => '非法操作',
            ];
        }
        
        if($mobile_check_code['code'] != $args['mobile_check_code']){
            return [
                '_error_code' => 14,
                '_error_message' => 'no changed',
                '_error_description' => '手机验证码错误',
            ];
        }
        db('user_check_code_mobile')->where('code_id', $args['mobile_check_sn'])->limit(1)->update([
            'check_time' => DATE_TIME,
        ]);

        // 三、执行与返回
        db()->startTrans();
        $user_account = [
            'mobile'=>$args['mobile'],
            'login_password'=>$args['login_password'],
            'head_portrait'=>'open.uim.site/avatar/default.png',
            'status'=>1,//User::status()->ENABLED,
            'identity_id' => 0, //未实名
        ];
        $account_id = db('user_account')->insert($user_account,'',true) * 1;
        if(!$account_id){
            db()->rollback();
            return [
                '_error_code' => 14,
                '_error_message' => 'no changed',
                '_error_description' => '数据库错误',
            ];
        }
        
        $app_auth_id = db('user_app_auth')->insert([
            'app_id'=>APP_ID,
            'account_id'=>$account_id,
            'create_time' =>DATE_TIME,
            'status'=>1,//UserAppAuth::status()->ENABLED,
            'project_id'=>PROJECT_ID
        ], '', true);
        
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

        
        if(!$app_auth_id || !$session_id){
            db()->rollback();
            return [
                '_error_code' => 14,
                '_error_message' => 'no changed',
                '_error_description' => '数据库错误',
            ];
        }
        
        db()->commit();
        // todo: 后期由第三方先申请注册邀请码（附带角色），跳转到uim用户中心进行注册

        $response['id'] = $session_id * 1;
        $response['account_id'] = $account_id * 1;
        $response['head_portrait'] = $user_account['head_portrait'];
        $response['first_name'] = '';
        $response['last_name'] = '';
        $response['sex'] = '';
        $response['role_list'] = [];

        return $response;
    }
}

