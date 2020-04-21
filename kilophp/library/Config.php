<?php
/**
 * Created by KiloFrameWork
 * User: xiejiawei<print_f@hotmail.com>
 * Date: 2020/4/8
 * Time: 12:49
 */

namespace kilophp;

/**
 * class Config
 * @package kilophp
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.2
 */
class Config
{
    /**
     * @var array 配置参数
     */
    private static $config = [];


    /**
     * 加载配置文件,支持目录和加载目录下的二级目录配置文件
     * @access public
     * @param string $file 配置文件目录或文件名
     * @param string $name 配置名（如设置即表示二级配置）
     * @return mixed
     */
    public static function load(string $file, string $name = ''): array
    {
        if (is_dir($file)) {
            foreach (scandir($file) as $row) {
                if ($row == '.' || $row == '..') {
                    continue;
                }
                if (!is_dir($file . DS . $row)) {
                    self::set(include $file . DS . $row, $name);
                } else {
                    self::load($file . $row . DS);
                }
            }
        } else {
            if (file_exists($file)) {
                self::set(include $file, $name);
            }
        }
        return self::$config;
    }


    /**
     * 获取配置参数 为空则获取所有配置
     * @access public
     * @param string $name 配置参数名（支持二级配置 . 号分割）
     * @return mixed
     */
    public static function get(string $name = null)
    {
        // 无参数时获取所有
        if (empty($name) && isset(self::$config)) {
            return self::$config;
        }
        // 非二级配置时直接返回
        if (!strpos($name, '.')) {
            $name = strtolower($name);
            return isset(self::$config[$name]) ? self::$config[$name] : null;
        }

        // 二维数组设置和获取支持
        $name = explode('.', $name, 2);
        $name[0] = strtolower($name[0]);

        return isset(self::$config[$name[0]][$name[1]]) ?
            self::$config[$name[0]][$name[1]] :
            null;
    }

    /**
     * 检测配置是否存在
     * @access public
     * @param string $name 配置参数名（支持二级配置 . 号分割）
     * @return bool
     */
    public static function has(string $name)
    {
        if (!strpos($name, '.')) {
            return isset(self::$config[strtolower($name)]);
        }

        // 二维数组设置和获取支持
        $name = explode('.', $name, 2);
        return isset(self::$config[strtolower($name[0])][$name[1]]);
    }

    /**
     * 设置配置参数 name 为数组则为批量设置
     * @access public
     * @param string|array $name 配置参数名（支持二级配置 . 号分割）
     * @param mixed $value 配置值
     * @return mixed
     */
    public static function set($name, $value = null)
    {
        // 字符串则表示单个配置设置
        if (is_string($name)) {
            if (!strpos($name, '.')) {
                self::$config[strtolower($name)] = $value;
            } else {
                // 二维数组
                $name = explode('.', $name, 2);
                self::$config[strtolower($name[0])][$name[1]] = $value;
            }

            return $value;
        }

        // 数组则表示批量设置
        if (is_array($name)) {
            if (!empty($value)) {
                self::$config[$value] = isset(self::$config[$value]) ?
                    array_merge(self::$config[$value], $name) :
                    $name;

                return self::$config[$value];
            }

            return self::$config = array_merge(
                self::$config, array_change_key_case($name)
            );
        }

        // 为空直接返回已有配置
        return self::$config;
    }
}