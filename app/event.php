<?php
// 事件定义文件
return [
    'bind'      => [
    ],

    'listen'    => [
        'AppInit'  => [
            \app\service\listener\RunService::class

        ],
        'HttpRun'  => [],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],
        'crontab' => [
            \app\service\crontab\CollectThreadCron::class
        ],
        'swoole.init' => [
        ],
        'swoole.workerStart' => [
            \app\service\crontab\CronStart::class
        ],
    ],

    'subscribe' => [
    ],
];
