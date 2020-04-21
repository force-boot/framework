<?php
/**
 * Created by KiloFrameWork
 * User: xiejiawei<print_f@hotmail.com>
 * Date: 2020/3/29
 * Time: 19:23
 */

use kilophp\Config;

use kilophp\Lang;

use kilophp\Log;

use kilophp\View;

use kilophp\Request;

use kilophp\App;

use kilophp\Session;

use kilophp\Cookie;

use kilophp\Cache;

if (!function_exists('config')) {
    /**
     * 获取和设置配置参数
     * @param string|array $name 参数名
     * @param mixed $value 参数值
     * @return mixed
     */
    function config($name = '', $value = null)
    {
        if (is_null($value) && is_string($name)) {
            return 0 === strpos($name, '?') ? Config::has(substr($name, 1)) : Config::get($name);
        } else {
            return Config::set($name, $value);
        }
    }
}

if (!function_exists('log')) {
    /**
     * 写入日志
     * @param $message
     * @return mixed
     * @throws Exception
     */
    function log(string $message)
    {
        return Log::save($message);
    }
}

if (!function_exists('view')) {
    /**
     * 渲染模板输出，支持数组赋值变量
     * @param $view string 视图文件名
     * @param $context array 赋值变量，必须是数组
     * @return mixed
     */
    function view(string $view, array $context = [])
    {
        View::show($view, $context);
    }
}

if (!function_exists('lang')) {
    /**
     * 获取语言配置
     * @param $name string 配置项
     * @param $replace string 替换转义符内容
     * @return mixed
     * @throws Exception
     */
    function lang(string $name, string $replace = '')
    {
        return Lang::get($name, $replace);
    }
}

if (!function_exists('request')) {
    /**
     * 获取当前Request对象实例
     * @return Request
     */
    function request()
    {
        return Request::instance();
    }
}

if (!function_exists('model')) {
    /**
     * 实例化模型
     * @param $name string 模型名称
     * @param $module string 应用为空则获取当前
     * @return object
     */
    function model(string $name, string $module = '')
    {
        return App::model($name, $module);
    }
}

if (!function_exists('controller')) {
    /**
     * 实例化控制器
     * @param $name string 模型名称
     * @param $module string 应用 为空则获取当前
     * @return object
     */
    function controller(string $name, string $module = '')
    {
        return App::controller($name, $module);
    }
}

if (!function_exists('dump')) {
    /**
     * 浏览器友好的变量输出
     * @param mixed $var 变量
     * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
     * @param string $label 标签 默认为空
     * @param boolean $strict 是否严谨 默认为true
     * @return void|string
     */
    function dump($var, bool $echo = true, $label = null, bool $strict = true)
    {
        $label = (null === $label) ? '' : rtrim($label) . ' ';
        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            } else {
                $output = $label . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            echo($output);
            return null;
        } else {
            return $output;
        }
    }
}
if (!function_exists('input')) {
    /**
     * 获取输入数据 支持默认值和过滤
     * @param string $key 获取的变量名
     * @param mixed $default 默认值
     * @param string $filter 过滤方法
     * @return mixed
     */
    function input($key = '', $default = null, $filter = '')
    {
        if (0 === strpos($key, '?')) {
            $key = substr($key, 1);
            $has = true;
        }
        if ($pos = strpos($key, '.')) {
            // 指定参数来源
            list($method, $key) = explode('.', $key, 2);
            if (!in_array($method, ['get', 'post', 'put', 'patch', 'delete', 'route', 'param', 'request', 'session', 'cookie', 'server', 'env', 'path', 'file'])) {
                $key = $method . '.' . $key;
                $method = 'param';
            }
        } else {
            // 默认为自动判断
            $method = 'param';
        }
        if (isset($has)) {
            return request()->has($key, $method, $default);
        } else {
            return request()->$method($key, $default, $filter);
        }
    }
}

if (!function_exists('token')) {
    /**
     * 生成表单令牌
     * @param string $name 令牌名称
     * @param mixed  $type 令牌生成方法
     * @return string
     */
    function token($name = '__token__', $type = 'md5')
    {
        $token = Request::instance()->token($name, $type);
        return '<input type="hidden" name="' . $name . '" value="' . $token . '" />';
    }
}

if (!function_exists('url')) {

    /**
     * 生成URL
     * @param string $url
     * @param array|string $vars 支持数组和字符串
     * @param $domain bool 是否添加域名
     * @return array|string
     */
    function url(string $url, $vars = null, bool $domain = false): string
    {
        return App::url($url, $vars, $domain);
    }
}

if (!function_exists('session')) {
    /**
     * Session管理
     * @param string|array $name session名称，如果为数组表示进行session设置
     * @param mixed $value session值
     * @param string $prefix 前缀
     * @return mixed
     */
    function session($name, $value = '', $prefix = null)
    {
        if (is_array($name)) {
            // 初始化
            Session::init($name);
        } elseif (is_null($name)) {
            // 清除
            Session::clear('' === $value ? null : $value);
        } elseif ('' === $value) {
            // 判断或获取
            return 0 === strpos($name, '?') ? Session::has(substr($name, 1), $prefix) : Session::get($name, $prefix);
        } elseif (is_null($value)) {
            // 删除
            return Session::delete($name, $prefix);
        } else {
            // 设置
            return Session::set($name, $value, $prefix);
        }
    }
}

if (!function_exists('cookie')) {
    /**
     * Cookie管理
     * @param string|array $name cookie名称，如果为数组表示进行cookie设置
     * @param mixed $value cookie值
     * @param mixed $option 参数
     * @return mixed
     */
    function cookie($name, $value = '', $option = null)
    {
        if (is_array($name)) {
            // 初始化
            Cookie::init($name);
        } elseif (is_null($name)) {
            // 清除
            Cookie::clear($value);
        } elseif ('' === $value) {
            // 获取
            return 0 === strpos($name, '?') ? Cookie::has(substr($name, 1), $option) : Cookie::get($name, $option);
        } elseif (is_null($value)) {
            // 删除
            return Cookie::delete($name);
        } else {
            // 设置
            return Cookie::set($name, $value, $option);
        }
    }
}

if (!function_exists('cache')) {
    /**
     * 缓存管理
     * @param mixed $name 缓存名称，如果为数组表示进行缓存设置
     * @param mixed $value 缓存值
     * @param mixed $options 缓存参数
     * @param string $tag 缓存标签
     * @return mixed
     */
    function cache($name, $value = '', $options = null, $tag = null)
    {
        if (is_array($options)) {
            // 缓存操作的同时初始化
            $cache = Cache::connect($options);
        } elseif (is_array($name)) {
            // 缓存初始化
            return Cache::connect($name);
        } else {
            $cache = Cache::init();
        }

        if (is_null($name)) {
            return $cache->clear($value);
        } elseif ('' === $value) {
            // 获取缓存
            return 0 === strpos($name, '?') ? $cache->has(substr($name, 1)) : $cache->get($name);
        } elseif (is_null($value)) {
            // 删除缓存
            return $cache->rm($name);
        } elseif (0 === strpos($name, '?') && '' !== $value) {
            $expire = is_numeric($options) ? $options : null;
            return $cache->remember(substr($name, 1), $value, $expire);
        } else {
            // 缓存数据
            if (is_array($options)) {
                $expire = isset($options['expire']) ? $options['expire'] : null; //修复查询缓存无法设置过期时间
            } else {
                $expire = is_numeric($options) ? $options : null; //默认快捷缓存设置过期时间
            }
            if (is_null($tag)) {
                return $cache->set($name, $value, $expire);
            } else {
                return $cache->tag($tag)->set($name, $value, $expire);
            }
        }
    }
}
if (!function_exists('in_array_case')) {
    /**
     * 不区分大小写的in_array实现
     * @param $value
     * @param $array
     * @return bool
     */
    function in_array_case($value, $array)
    {
        return in_array(strtolower($value), array_map('strtolower', $array));
    }
}
