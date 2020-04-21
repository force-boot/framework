<?php
/**
 * Created by KiloFrameWork
 * User: xiejiawei<print_f@hotmail.com>
 * Date: 2020/3/24
 * Time: 9:43
 */

define('KILO_VER', 'v1.0.0');//框架版本号
defined('APP_DEBUG') or define('APP_DEBUG', false); // 是否开启调试模式
define('START_TIME', microtime(true));
define('START_MEM', memory_get_usage());
define('DS', DIRECTORY_SEPARATOR);//动态目录分隔符
define('ROOT_PATH', dirname(realpath(dirname($_SERVER['SCRIPT_FILENAME']))) . DS);//应用根目录
defined('APP_PATH') or define('APP_PATH', ROOT_PATH . "app" . DS); //默认项目目录
define('CONFIG_PATH', ROOT_PATH . "config" . DS); //项目配置目录
define('KILO_PATH', ROOT_PATH . 'kilophp' . DS);//框架核心目录
define('RUNTIME_PATH', ROOT_PATH . 'runtime' . DS); // 系统运行缓存目录
define('LIB_PATH', KILO_PATH . 'library' . DS); // 系统核心类库目录
define('VENDOR_PATH', KILO_PATH . 'vendor' . DS); // 第三方类库目录
define('HELPER_PATH', LIB_PATH . 'helper' . DS); // 助手函数目录
define('TEMP_PATH', RUNTIME_PATH . 'temp' . DS); // 模板编译目录
define('LANG_PATH', KILO_PATH . "language" . DS); // 系统语言存放目录
define('TPL_PATH', KILO_PATH . "template" . DS); // 系统模板存放目录
define('LOG_PATH', RUNTIME_PATH . 'logs' . DS); // 系统日志存放目录
define('CACHE_PATH', RUNTIME_PATH . 'cache' . DS);// 缓存存放目录
define('ROUTE_PATH', ROOT_PATH . 'route' . DS); // 路由规则存放目录
define('EXT', '.php');//默认文件后缀

// 环境常量
define('IS_CLI', PHP_SAPI == 'cli' ? true : false);
define('IS_WIN', strpos(PHP_OS, 'WIN') !== false);

//引入自动加载
require ROOT_PATH . 'vendor' . DS . 'autoload.php';

//初始化框架
kilophp\App::run();

