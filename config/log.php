<?php
/**
 * 系统日志配置
 */
return [
    'log' => [
        //日志记录方式，默认只支持File 留空则不记录系统运行日志
        'type' => 'File',
        //日志保存目录 默认在runtime/logs
        'path' => LOG_PATH,
        //日志保存时间 0为永久保存，单位是天
        'storage_time' => 1
    ],
];