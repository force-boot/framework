<?php
/**
 * Created by KiloFrameWork
 * User: xiejiawei<print_f@hotmail.com>
 * Date: 2020/4/8
 * Time: 13:23
 */

namespace kilophp;

use kilophp\exception\Handler;

use Whoops;

/**
 * 框架错误处理
 * @package kilophp
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Error
{
    /**
     * @var object $run
     */
    public static $run;

    /**
     * 初始化框架错误处理
     * @static
     * @access public
     */
    public static function init()
    {
        self::$run = new Whoops\Run();
        //注册异常处理
        self::register();
    }

    /**
     * 注册whoops异常处理
     * @static
     * @access public
     */
    private static function register()
    {
        if (APP_DEBUG) { //调试模式下，框架异常和错误都是whoops扩展处理
            $handle = new Whoops\Handler\PrettyPageHandler();
        } else { //转交给框架自定义处理，只记录日志，和输出错误页面
            $handle = new Handler();
        }
        self::$run->pushHandler($handle);
        self::$run->register();
    }
}