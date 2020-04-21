<?php
/**
 * 模板设置
 */
return [
    'template' => [
        //目前内置twig模板引擎
        'type' => 'twig',
        //是否缓存模板,默认跟随APP_DEBUG常量配置
        'debug' => APP_DEBUG,
        //模板文件后缀
        'suffix' => '.html',
        //模板缓存编译目录
        'compile_dir' => TEMP_PATH,
    ],
];