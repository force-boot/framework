<?php
/**
 * Created by KiloFrameWork
 * User: xiejiawei<print_f@hotmail.com>
 * Date: 2020/4/12
 * Time: 22:43
 */

namespace kilophp;

use \Exception;

/**
 * 框架日志类
 * @package kilophp
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Log
{
    /**
     * @var object|null 当前日志驱动实例
     */
    private static $driver = null;

    /**
     * 初始化日志
     * @static
     * @access public
     * @return object|bool
     * @throws Exception
     */
    public static function init()
    {
        //获取当前驱动方式
        $driver = empty(Config::get('log.type')) ? 'File' : ucwords(Config::get('log.type'));
        $class = "kilophp\\log\\driver\\" . $driver;
        if (class_exists($class)) {
            //实例化驱动，并传入当前日志配置
            self::$driver = new $class(Config::get('log'));
        } else {
            throw new Exception(lang('no_log_driver', $driver));
        }
        return self::$driver;
    }


    /**
     * 保存日志
     * @param $log string 写入日志内容
     * @static
     * @access public
     * @return mixed
     * @throws Exception
     */
    public static function save(string $log)
    {
        if (is_null(self::$driver)) {
            self::init();
        }
        self::$driver->save($log);
    }
}