<?php
use Swoole\WebSocket\Server;
//创建websocket服务器对象，监听0.0.0.0:9502端口
$ws = new Server("0.0.0.0", 9502);

//监听WebSocket连接打开事件
$ws->on('open', function ($ws, $request) {

});

//监听WebSocket消息事件
$ws->on('message', function ($ws, $frame) {
    // var_dump($frame);
    $info = json_decode($frame->data,true);
    if($info['type']=='login'){
        $redis=new Swoole\Coroutine\Redis();
        $key="online_list";
        $redis->connect('127.0.0.1',6379);
        $list=$redis->get($key);
        $userlist=json_decode($list,true);
        if(empty($userlist)){
            $userlist=[];
        }
        $userlist[]=[
            'client_id'=>$frame->fd,
            'username'=>$info['con']
        ];
        $str=json_encode($userlist,JSON_UNESCAPED_UNICODE);
        $redis->set($key,$str);
        $message=[
            'is_me'=>1,
            'username'=>$info['con']
        ];
        $res=json_encode($message,JSON_UNESCAPED_UNICODE);
        $ws->push($frame->fd, $res);
        foreach($userlist as $key=>$val){
            if($frame->fd != $val['client_id']){
                $message=[
                    'is_me'=>0,
                    'username'=>$info['con']
                ];
                $res=json_encode($message,JSON_UNESCAPED_UNICODE);
                $ws->push($val['client_id'], $res);
            }
        }
    }

});

//监听WebSocket连接关闭事件
$ws->on('close', function ($ws, $fd) {
    echo "client-{$fd} is closed\n";
});

$ws->start();
?>