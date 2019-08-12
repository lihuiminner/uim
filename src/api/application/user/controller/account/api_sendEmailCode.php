<?php

namespace app\user\controller\account;
use think\Controller;

class api_sendEmailCode extends Controller
{
    public function v_1_0($args)
    {
        
        $response = []; // 定义返回数组
        // 一、判断传入参数
        if (!isset($args['email'])) {
            return [
                '_error_number' => 11,
                '_error_code' => 'input args error',
                '_error_description' => '请传入email参数',
            ];
        }
        $is_exist = db("user")->where("email", $args['email'])->value("account_id");
        if ($is_exist) {
            return [
                '_error_number' => 16,
                '_error_code' => 'illegal operation',
                '_error_description' => '该邮箱已注册',
            ];
        }
        // 二、逻辑处理
        //当日向该邮箱发送邮件次数小于等于5
        if (db("user_check_code_email")->where("email", $args['email'])->whereTime('send_time', 'today')->count() >= 4) {
            return [
                '_error_number' => 16,
                '_error_code' => 'illegal operation',
                '_error_description' => '该邮箱发送邮件次数大于5',
            ];
        }
        if (db("log_api_request")->where("client_ip", CLIENT_IP)->where("method", "open.userCenter.register.sendEmailCode")->whereTime('req_time', 'today')->count() >= 9) {
            return [
                '_error_number' => 16,
                '_error_code' => 'illegal operation',
                '_error_description' => '当前IP当日调用本接口次数小于大于10',
            ];
        }
        // 三、执行与返回

        // todo 应当允许用户上传代码文件
        // require_once "Smtp.class.php";

        $email_data = [
            'code' => $code,
            'send_time' => date("Y-m-d H:i:s"),
            'email' => $args['email'],
            'app_id' => APP_ID,
            'client_uuid' => CLIENT_UUID
        ];
        $response['email_check_sn'] = db("user_check_code_email")->insert($email_data, '', true);
        $response['_account_id'] = $args['email'];
        $response['_email_code'] = [
            'code' => $code
        ];

        return $response;
    }
}

