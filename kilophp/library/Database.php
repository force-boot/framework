<?php
/**
 * Created by KiloFrameWork
 * User: xiejiawei<print_f@hotmail.com>
 * Date: 2020/4/8
 * Time: 15:49
 */

namespace kilophp;

use Illuminate\Database\Capsule\Manager;

/**
 * class database
 * @package kilophp
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Database
{

    /**
     * @var Manager 保存实例
     */
    private static $instance = [];

    /**
     * Database constructor.
     */
    private function __construct(){}

    /**
     * 初始化
     * @access public
     * @param array $config
     * @param bool $reset
     * @static
     * @return Manager
     */
    public static function init($config = [], bool $reset = false)
    {
        if (false == $reset) {
            $id = md5(serialize($config));
        }
        if (!isset(self::$instance[$id]) || true == $reset) {
            $capsule = new Manager();
            $option = self::paserConfig($config);
            foreach ($option as $linkId => $connection) {
                if (is_array($connection)) {
                    $capsule->addConnection($connection, $linkId);
                }else{
                    $capsule->addConnection($option);
                }
            }
            $capsule->setAsGlobal();
            $capsule->bootEloquent();
            if (true == $reset) {
                $id = md5(serialize($config));
            }
            self::$instance[$id] = $capsule;
        }
        return self::$instance[$id];
    }

    /**
     * 解析配置参数
     * @param array $config
     * @static
     * @access private
     * @return array|mixed
     */
    private static function paserConfig($config = [])
    {
        if (empty($config)) {
            return Config::get('connections');
        } elseif (is_string($config)) {
            return Config::get($config);
        } else {
            return $config;
        }
    }

    /**
     * 清除连接实例
     * @access public
     * @return void
     */
    public static function reset()
    {
        self::$instance = null;
    }

    /**
     * 禁止 clone
     * @access private
     */
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * 静态调用组件方法
     * @param $method
     * @param $param
     * @static
     * @access public
     * @return mixed
     */
    public static function __callStatic($method, $param)
    {
        // TODO: Implement __callStatic() method.
        return call_user_func_array([self::init(), $method], $param);
    }
}