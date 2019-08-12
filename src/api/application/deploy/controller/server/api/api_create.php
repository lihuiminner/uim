<?php

namespace app\deploy\controller\server\api;
use think\Controller;

class api_create extends Controller
{
    public function v_1_0($args)
    {
        
        $response = []; // 定义返回数组
        
        // 一、判断传入参数
        if(!isset($args['project_id'])){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '非法操作'
            ];
        }

        $project = db('teamwork_project')->field('group_id,status')->where('project_id', $args['project_id'])->find();
        // 必须是项目负责人才可以为项目创建服务器
        if(!$project){// || !$project['leader'] != ACCOUNT_ID
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '非法操作2'
            ];
        } else if($project['status'] == 4){
            return [
                '_error_code' => 12,
                '_error_message' => 'authentication failed',
                '_error_description' =>  '项目状态异常' // todo enum.text
            ];
        }
        
        if (!isset($args['line_id'])) {
            return [
                '_error_code' => 11,
                '_error_message' => 'input args error',
                '_error_description' => '参数错误：line_id',
            ];
        }

        
        // 二、逻辑处理
        $server_id = db('deploy_server_api')->insert([
            'group_id' => $project['group_id'],
            'project_id' -> $args['project_id'],
            'buy_time' => DATE_TIME,
            'end_time' => date('Y-m-d', strtotime(DATE_TIME . ' +' . $args['months'] . ' month')),
        ], '', true);

        // todo 使用额外机制写入command
        
        $command = [
            'command_content' => 'docker run --name create_by_node_docker_id_1999 -t -i -d -v /home/data:/home/data -p 19996:19996 centos /usr/sbin/init',
            'create_time' => DATE_TIME,
            'status' => 1, // todo enum
        ];

        $command_id = db('devops_command')->insert($command, '', true);

        if(!$command_id){
            return [
                '_error_code' => 13,
                '_error_message' => 'operation failed',
                '_error_description' => '操作失败',
            ];
        }
        
        $command['command_id'] = $command_id;

        $response['lpush'] = redis_lpush(config('redis'), 'devops_command_1', json_encode($command));
        $response['publish'] = redis_publish(config('redis'), 'devops_command_1', $command_id);

        // 三、执行与返回
        return $response;
    }
}

