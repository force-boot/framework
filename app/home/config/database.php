<?php
return [
    //数据库配置
    'connections' => [
        //这是默认的数据库
        'default' => [
            'driver' => 'mysql', //数据库类型
            'host' => '127.0.0.1', //主机名
            'database' => '', //数据库名
            'username' => '', //用户名
            'password' => '', //密码
            'charset' => 'utf8', //字符集
            'collation' => 'utf8_unicode_ci', //排序规则
            'prefix' => '', //数据库前缀
        ]
    ],
];