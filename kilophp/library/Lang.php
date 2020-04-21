<?php
/**
 * Created by KiloFrameWork
 * User: xiejiawei<print_f@hotmail.com>
 * Date: 2020/4/5
 * Time: 10:49
 */

namespace kilophp;

use Exception;

/**
 * 加载框架语言配置
 * @package kilophp
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Lang
{
    /**
     * @var array 语言数据
     */
    private static $lang = [];

    /**
     * 加载语言文件
     * @access public
     * @param $file mixed
     * @return array|mixed
     * @throws Exception
     */
    public static function load(string $file = '')
    {
        $file = !empty($file) ? $file : Config::get('lang.default_lang');
        if ($file_path = self::checkFile(LANG_PATH, $file)) {
            self::$lang = include $file_path;
        } else {
            throw new Exception('Language pack file not found!');
        }
    }

    /**
     * 获取语言配置内容
     * Lang::get('module_not_exist','admin'); admin应用不存在
     * @access public
     * @param $name string
     * @param $replace string
     * @return mixed
     * @throws Exception
     */
    public static function get(string $name, string $replace = ''): string
    {
        if (empty(self::$lang)) {
            self::load();
        }
        return str_replace("%s%", $replace, self::$lang[$name]);
    }

    /**
     * 获取语言定义(不区分大小写)
     * @access public
     * @param string|null $name 语言变量
     * @return mixed
     */
    public static function has($name)
    {
        return isset(self::$lang[strtolower($name)]);
    }


    /**
     * 验证语言文件
     * @access private
     * @param $dir
     * @param $name
     * @return bool
     */
    public static function checkFile(string $dir, string $name)
    {
        if (!is_dir($dir)) {
            return false;
        }
        foreach (scandir($dir) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            return $file == $name ? $dir . $name : false;
        }
    }
}