<?php
//缓存设置
return [
    'cache' => [
        // 驱动方式 支持File Memcache Memcached Redis Lite，不同驱动配置项也有差异，请查看文档获取帮助
        'type' => 'File',
        // 缓存保存目录
        'path' => CACHE_PATH,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],
];