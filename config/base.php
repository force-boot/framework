<?php
/**
 * 框架基础配置文件
 */
return [
    'base' => [
        //配置系统时区
        'default_timezone' => 'PRC',

        //设置字符集
        'charset' => 'utf-8',

        //默认全局过滤方法 用逗号分隔多个
        'default_filter' => '',

        //默认路由配置文件夹，默认在根目录/route 该目录下的所有文件都会被加载，包括二级目录
        'default_route_path' => ROUTE_PATH,

        // 默认跳转页面对应的模板文件
        'dispatch_success_tmpl' => TPL_PATH . 'default_jump.tpl',
        'dispatch_error_tmpl' => TPL_PATH . 'default_jump.tpl',

        //非调试模式下，系统错误页面
        'dafault_error_tmpl' => TPL_PATH . 'default_error.tpl'
    ]
];