<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

// 判断用户是否属于指定团队的成员
function group_member($group_id, $account_id){
    if(!$group_id || !$account_id) return false;
    return db('user_group_member')->where(['group_id'=>$group_id,'account_id'=>$account_id])->value('member_id');
}

// 获取账户显示名称
function account_name($account_id){
    $account = db('user_account')->find($account_id);
    // todo 必须实名
    if($account['identity_id']){
        $identity = db('user_identity')->find($account['identity_id']);
        return $identity['last_name'] . $identity['first_name'];
    }else{
        return $account['login_name'] ? $account['login_name'] : $account['mobile'];
    }
}

// 判断接口权限
function api_power($power, $check = API_ID){
    if(!$power){
        return false;
    }elseif(is_array($power)){
        return !array_diff(explode(',', $check), $power);
    }elseif($power == '*'){
        return true;
    }else{
        return !array_diff(explode(',', $check), explode(',', $power));
    }
}

// 验证用户是否参与项目，并且拥有指定权限
function teamwork_project_participant($project_id, $account_id = ACCOUNT_ID, $power_ids = API_ID){
    // 获取当前用户在项目中的权限
    $participant_power = db('teamwork_project_participant')->where([
        'project_id' => $project_id,
        'account_id' => $account_id,
        'status' => 1, // todo enum
    ])->value('power');
    if(!$participant_power){
        return false;
    }else if($power_ids && !api_power($participant_power, $power_ids)){
        return false;
    }else{
        return true;
    }
}
// 验证用户是否参与模块，并且拥有指定权限
function teamwork_module_participant($module_id, $account_id = ACCOUNT_ID, $power_ids = API_ID){
    // 获取当前用户在模块中的权限
    $participant_power = db('teamwork_module_participant')->where([
        'module_id' => $module_id,
        'account_id' => $account_id,
        'status' => 1, // todo enum
    ])->value('power');
    if(!$participant_power){
        return false;
    }else if($power_ids && !api_power($participant_power, $power_ids)){
        return false;
    }else{
        return true;
    }
}


function redis_publish($config_redis, $key, $value) {

    if(!class_exists('\Redis')) return [
        'error' => 'not found class: redis.'
    ];
    $redis = new \Redis();
    if($redis->pconnect($config_redis['ip'], $config_redis['port']) === false){
        return [
            'error' => 'can not connect to redis server.'
        ];
    }
    if(isset($config_redis['password']) && $config_redis['password'] && $redis->auth($config_redis['password']) === false){
        return [
            'error' => 'redis auth fail.'
        ];
    }
    if($redis->publish($key, $value)){
        return [
            'error' => false
        ];
    }else{
        return [
            'error' => 'redis publish fail.'
        ];
    }

}

function redis_lpush($config_redis, $key, $value) {

    if(!class_exists('\Redis')) return [
        'error' => 'not found class: redis.'
    ];
    $redis = new \Redis();
    if($redis->pconnect($config_redis['ip'], $config_redis['port']) === false){
        return [
            'error' => 'can not connect to redis server.'
        ];
    }
    if(isset($config_redis['password']) && $config_redis['password'] && $redis->auth($config_redis['password']) === false){
        return [
            'error' => 'redis auth fail.'
        ];
    }
    if($redis->lpush($key, $value)){
        return [
            'error' => false
        ];
    }else{
        return [
            'error' => 'redis lpush fail.'
        ];
    }

}