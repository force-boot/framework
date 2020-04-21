<?php
/**
 * URL配置
 */
return [
    'url' => [
        //默认访问应用
        'default_app' => 'home',
        //默认控制器
        'default_controller' => 'Index',
        //默认方法
        'default_action' => 'index',
        // PATHINFO变量名 用于兼容模式
        'var_pathinfo' => 's',
        // 兼容PATH_INFO获取
        'pathinfo_fetch' => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
        //伪静态后缀 留空允许全部后缀，false关闭
        'html_suffix' => 'html',
    ]
];