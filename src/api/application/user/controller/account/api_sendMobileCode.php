<?php

namespace app\user\controller\account;
use think\Controller;

class api_sendMobileCode extends Controller
{
    public function v_1_0($args)
    {

        $response = []; // 定义返回数组

        // 一、判断传入参数
        if(!isset($args['mobile']) || !$args['mobile'] || mb_strlen($args['mobile']) != 11){
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => 'mobile参数错误',
            ];
        }
        $account_id = db('user_account')->where('mobile', $args['mobile'])->value('account_id');
        if($account_id){
            return [
                '_error_code' => 16,
                '_error_message' => 'illegal operation',
                '_error_description' => '该手机号已注册',
            ];
        }
        
        // 二、逻辑处理
        // todo 考虑如何实现对某个接口的全网单位时间段调用次数限制：开启限制字段，由socket服务器判断并全网计数
        
        // 三、执行与返回
        // $response['_smsCode'] = '用户注册';

        // 动作处理时的手机号
        $response['_mobile'] = $args['mobile'];
        // 动作处理时的验证码、短信签名
        $response['_sms_param'] = [
            'code' => rand(100000, 999999),
            'product' => APP_NAME, // todo 默认为注册来源？或者在添加绑定角色时使用短信码进行确认
        ];
        $response['mobile_check_sn'] = db('user_check_code_mobile')->insert([
            'code'       => $response['_sms_param']['code'],
            'send_time'  => DATE_TIME,
            'mobile'     => $args['mobile'],
            'app_id'     => APP_ID,
            'client_id'=> CLIENT_ID
        ], '', true);

        return $response;
    }
}

