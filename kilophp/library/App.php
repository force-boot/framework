<?php
/**
 * Created by KiloFrameWork
 * User: xiejiawei<print_f@hotmail.com>
 * Date: 2020/3/28
 * Time: 5:49
 */

namespace kilophp;

use Exception;

use FastRoute\Dispatcher;

/**
 * 框架初始类
 * Class App
 * @final
 * @package kilophp
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.2
 */
final class App
{
    /**
     * @var array 存储实例
     */
    private static $instance = [];

    /**
     * 执行应用
     * @static
     * @access public
     * @throws Exception
     */
    public static function run()
    {
        //初始化全局错误处理
        Error::init();
        //加载框架配置文件
        Config::load(CONFIG_PATH);
        //导入路由规则
        Route::import(Config::get('base.default_route_path'));
        //初始化路由
        Route::init();
        //设置系统时区
        date_default_timezone_set(Config::get('base.default_timezone'));
        //设置字符集
        header('Content-type:text/html;charset=' . Config::get('base.charset'));
        //清理类实例化
        self::clearInstance();
        //URL调度
        self::dispath();
    }

    /**
     * URL调度
     * @static
     * @access private
     * @throws Exception
     */
    private static function dispath()
    {
        //获取当前路由信息
        $routeInfo = request()->routeInfo();
        if ($routeInfo[0] == Dispatcher::FOUND) {//匹配到路由规则
            request()->paserParam($routeInfo[2]);//解析变量
            request()->parseUrl($routeInfo[1]);//解析地址
        } else if ($routeInfo[0] == Dispatcher::METHOD_NOT_ALLOWED) {//处理非法请求
            header("Allow: " . join(',', $routeInfo[1]));
            header("HTTP/1.0 405 Method Not Allowed");
            return false;
        }
        $app = request()->app(); //获取当前应用
        $controller = request()->controller(); //获取当前控制器
        $action = request()->action(); //获取当前方法
        //设置应用目录
        define('APP_DIR', APP_PATH . $app . DS);
        if (!is_dir(APP_DIR)) {//判断应用是否存在
            throw new Exception(lang('module_not_exist', $app));
        }
        //设置视图目录 按数组顺序查找
        Config::set('view.path', [
            APP_DIR . 'view' . DS . strtolower($controller) . DS,
            APP_DIR . 'view' . DS,
        ]);
        //加载应用配置文件
        Config::load(APP_DIR . 'config');
        //初始化database组件
        Database::init();
        //实例化控制器
        $controller = self::controller($controller, $app);
        //执行方法
        if (method_exists($controller, $action)) {
            return $controller->$action();
        } else {
            throw new Exception(lang('method_not_exist', $action));
        }
    }

    /**
     * 生成URL
     * @static
     * @param string $url
     * @param array|string $vars 支持数组和字符串
     * @param bool $domain 是否添加域名
     * @access public
     * @return array|string
     */
    public static function url(string $url, $vars = null, $domain = false): string
    {
        $request = Request::instance();
        $depr = '/';
        //如果匹配到了路由规则，就直接返回
        if (Route::findByRoute($url = trim($url, $depr))) {
            return $depr . $url . '.' . config('url.html_suffix');
        }
        $url = explode($depr, trim($url, $depr));
        $count = count($url);
        if ('' == $url[0]) { //为空获取当前
            $url = $depr . $request->app() . $depr . $request->controller() . $depr . $request->action();
        } else if ($count == 1) {//方法
            $url = $depr . $request->app() . $depr . $request->controller() . $depr . $url[0];
        } elseif ($count == 2) { //控制器/方法
            $url = $depr . $request->app() . $depr . join($depr, $url);
        } elseif ($count >= 3) { //应用/控制器/方法/...
            $url = $depr . join($depr, $url);
        }
        if (!is_null($vars)) {
            // 解析参数
            if (is_string($vars)) {
                parse_str($vars, $vars);
            }
            $vars = $depr . join($depr, $vars);
        }
        $url .= $vars . '.' . config('url.html_suffix');
        //判断是否添加域名
        if (is_string($domain)) {
            $url = $domain . $url;
        } else if (false != $domain) {
            $url = $request->domain() . $url;
        }
        return $url;
    }

    /**
     * 实例化模型
     * @static
     * @access public
     * @param $name string 模型名称
     * @param string $module 应用
     * @return object
     * @throws Exception
     */
    public static function model(string $name, string $module = '')
    {
        return self::loader($name, 'model', $module);
    }

    /**
     * 实例化控制器
     * @static
     * @access public
     * @param $name string 控制器名称
     * @param string $module 应用
     * @return  object
     * @throws Exception
     */
    public static function controller(string $name, string $module = '')
    {
        return self::loader($name, 'controller', $module);
    }

    /**
     * 实例化装载器
     * @static
     * @access public
     * @param $name string 类名
     * @param $mode string 类型
     * @param $app string 应用
     * @return object
     * @throws Exception
     */
    public static function loader(string $name, string $mode, string $app = '')
    {
        $app = empty($app) ? request()->app() : $app;
        $id = $app . $name . $mode;
        $class = "app\\{$app}\\" . strtolower($mode) . "\\" . ucwords($name);
        if (!isset(self::$instance[$id])) {
            if (class_exists($class)) {
                self::$instance[$id] = new $class();
            } else {
                throw new Exception(lang($mode . '_not_exist', $name));
            }
        }
        return self::$instance[$id];
    }

    /**
     * 清理类的实例化
     * @static
     * @access private
     */
    private static function clearInstance()
    {
        self::$instance = [];
    }
}
