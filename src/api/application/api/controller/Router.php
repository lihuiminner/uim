<?php
namespace app\api\controller;

class Router
{
    public function index()
    {
        /* 
         * ---------------------------------------------------------------------
         * 
         * 【公共传入参数】
         * 
         * app_id           int                平台分配给应用的App_ID
         * client_ip        int                客户端ip
         * client_id        int                客户端标识 todo rem //
         * account_id       int                用户id  非必填
         * args             string             传入目标api的参数，json字符串形式
         * 
         * ---------------------------------------------------------------------
         * 
         * 【公共响应参数】
         * 
         * _c/code             请求失败返回的错误码，0表示无报错
         * _m/message          api报错时返回信息
         * _i/information      api报错详情
         * 
         * ---------------------------------------------------------------------
         */
        

        //请求开始时间
        $start_time = microtime(true);
        
        //获取所有GET POST变量
        $param = request()->post();
        

        $err = false;

        define('REQ_ID', $param['req_id']);
        define('ACCOUNT_ID', isset($param['account_id']) ? $param['account_id'] : 0);
        //客户端ip
        define('CLIENT_IP', isset($param['client_ip']) ? $param['client_ip'] : '');
        define('CLIENT_ID', $param['client_id']); // todo 如果是服务器端调用，仍然为32位md5串？，该串由服务器负责人申请/系统指定
        define('API_ID', isset($param['api_id']) ? $param['api_id'] : 0);
        define('APP_ID', isset($param['app_id']) ? $param['app_id'] : 0);
        define('APP_NAME', isset($param['app_name']) ? $param['app_name'] : '');
        define('PROJECT_ID', isset($param['project_id']) ? $param['project_id'] : 0);

        define('DATE_TIME', date('Y-m-d H:i:s'));

        //api运行开始时间
        $api_start_time = microtime(true);

        // 运行api
        $response = $this->run_api($param);

        //api结束运行开始时间
        $api_end_time = microtime(true);
        $response['exec_time'] = round($api_end_time - $api_start_time, 3) * 1000;

        return json($response);
    }
    

    function run_api($param){
        // 要调用的api
        $method = explode('.', $param['code']);


        // api版本号 - 只允许包含英文、数字与下划线
        $verion_code = array_pop($method);

        // api所属模块
        $module = array_shift($method);

        // 拼接命名空间
        $class_path = $method;
        array_pop($class_path);
        $namespace = 'app\\' . $module . '\\controller\\' . implode('\\', $class_path) . '\\api_' . $method[count($method) - 1];

        // 尝试反射
        try {
            $class = new \ReflectionClass($namespace);
            $function = 'v_' . $verion_code;
            if($class->hasMethod($function)){

                // 创建实例
                $instance = $class->newInstance();

                // 尝试调用api，得到api返回结构
                $result = $instance->$function(json_decode($param['args'],true));
                if(is_array($result)){
                    $special_key = [
                        '_error_code' => 'code',
                        '_error_message' => 'msg',
                        '_error_description' => 'description',
                    ];
                    $response = [
                        'code' => 0,
                    ];
                    foreach($result as $k => &$v){
                        if(substr($k, 0, 1) == '_'){
                            if(isset($special_key[$k])){
                                $response[$special_key[$k]] = $v;
                            }else{
                                $response[$k] = $v;
                            }
                            unset($result[$k]);
                        }
                    }
                    // api成功返回数组结构
                    // todo 验证返回结构是否与api文档定义一致
                    // todo debug模式下检查api实际返回与文档定义输出结构差异，是否有多余返回、缺少返回、或类型不正确
                    // 将api返回结果装入公共响应数组中
                    if($response['code'] === 0) {
                        $response[$param['code']] = $result;
                    }
                    return $response;
                } else {
                    // API返回类型错误
                    return [
                        'code' => 10,
                        'msg' => 'api return type error',
                        'description' => 'API返回类型错误：' . gettype($result),
                    ];
                }
            } else {
                // 指定api版本不存在
                return [
                    'code' => 9,
                    'msg' => 'api version is not found',
                    'description' => 'API版本["' . $function . '"]不存在',
                ];
            }
        } catch (\Exception $e) {
            //类不存在，或api函数内部报错
            return [
                'code' => 8,
                'msg' => 'reflection class error',
                'description' => '调用类失败：' . $e->getMessage() . ',code:' . $e->getCode() . ',line:' . $e->getLine() . ',file:' . $e->getFile(),
            ];
        }
    }

}
