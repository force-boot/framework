<?php
/**
 * Created by KiloPHPFrameWork
 * User: xiejiawei<print_f@hotmail.com>
 * Date: 2020/4/16
 * Time: 12:57
 */

namespace kilophp;

use FastRoute\Dispatcher;

use FastRoute\RouteCollector;

use function FastRoute\simpleDispatcher;

use Exception;

/**
 * 框架路由类
 * @package kilophp
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Route
{
    /**
     * @var object 路由调度器
     */
    public static $dispatcher = null;

    /**
     * @var string 当前请求uri
     */
    public static $uri;

    /**
     * @var string 当前请求类型
     */
    public static $method;

    /**
     * @var array 路由规则
     */
    public static $routeRule = [];

    /**
     * 递归导入路由规则，增加对route目录下二级目录的支持
     * @static
     * @access public
     * @param $dir string 导入目录
     */
    public static function import(string $dir)
    {
        foreach (scandir($dir) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir($dir . $file)) {
                self::import($dir . $file);
            } else {
                include $dir . DS . $file;
            }
        }
    }

    /**
     * 添加路由分组
     * @static
     * @access public
     * @param string $prefix 前缀
     * @param array $rule 路由规则
     * @param string $method 请求类型，默认GET
     */
    public static function group(string $prefix, array $rule, string $method = 'GET')
    {
        foreach ($rule as $k => $v) {
            self::parseRoute($method, $prefix . $k, $v);
        }
    }

    /**
     * 添加路由
     * @static
     * @access public
     * @param $method string 请求类型
     * @param $route string|array 路由规则
     * @param string $handler 处理地址
     */
    public static function add(string $method, $route, string $handler = '')
    {
        self::parseRoute(strtoupper($method), $route, $handler);
    }

    /**
     * 添加GET方式路由
     * @static
     * @access public
     * @param $route string|array
     * @param $handler string
     */
    public static function get($route, string $handler = '')
    {
        self::parseRoute('GET', $route, $handler);
    }

    /**
     * 添加POST方式路由
     * @static
     * @access public
     * @param $route string|array
     * @param $handler string
     */
    public static function post($route, string $handler = '')
    {
        self::parseRoute('POST', $route, $handler);
    }

    /**
     * 添加put方式路由
     * @static
     * @access public
     * @param $route string|array
     * @param $handler string
     */
    public static function put($route, string $handler)
    {
        self::parseRoute('PUT', $route, $handler);
    }

    /**
     * 添加delete方式路由
     * @static
     * @access public
     * @param $route string|array
     * @param $handler string
     */
    public static function delete($route, string $handler)
    {
        self::parseRoute('DELETE', $route, $handler);
    }

    /**
     * 添加patch方式路由
     * @static
     * @access public
     * @param $route string|array
     * @param $handler string
     */
    public static function patch($route, string $handler)
    {
        self::parseRoute('PATCH', $route, $handler);
    }

    /**
     * 添加head方式路由
     * @static
     * @access public
     * @param $route string|array
     * @param $handler string
     */
    public static function head($route, string $handler)
    {
        self::parseRoute('HEAD', $route, $handler);
    }

    /**
     * 获取当前所有路由规则
     * @static
     * @access public
     * @return array
     */
    public static function getRouteData()
    {
        return self::$routeRule;
    }

    /**
     * 查找路由规则
     * @param string $route
     * @static
     * @access public
     * @return bool
     */
    public static function findByRoute(string $route)
    {
        foreach (self::getRouteData() as $value) {
            if (strtolower(trim($value['route'], '/')) == strtolower($route)) {
                return true;
            } else {
                continue;
            }
        }
    }

    /**
     * 解析route规则
     * @static
     * @access private
     * @param $method string
     * @param $route string|array
     * @param $handler string
     * @return bool|array
     */
    private static function parseRoute(string $method, $route, string $handler)
    {
        if (is_array($route) && empty($handler)) {
            foreach ($route as $rule => $handler) {
                self::$routeRule[] = [
                    'method' => $method,
                    'route' => $rule,
                    'handler' => $handler
                ];
            }
        } else if (is_string($route) && !empty($handler)) {
            self::$routeRule[] = [
                'method' => $method,
                'route' => $route,
                'handler' => $handler
            ];
        } else {
            return false;
        }
        //添加路由规则
        return self::addRouteRule();
    }

    /**
     * 添加路由规则
     * @static
     * @access private
     * @return array
     */
    private static function addRouteRule()
    {
        self::$dispatcher = simpleDispatcher(function (RouteCollector $r) {
            foreach (self::$routeRule as $row) {
                $r->addRoute($row['method'], $row['route'], $row['handler']);
            }
        });
        return self::$routeRule;
    }

    /**
     * 初始化路由 并获取路由信息
     * @static
     * @access public
     * @throws Exception
     */
    public static function init()
    {
        self::$method = Request::instance()->method();
        self::$uri = '/' . Request::instance()->path();
        if (is_null(self::$dispatcher)) {
            $routeInfo[0] = Dispatcher::NOT_FOUND;
        } else {
            $routeInfo = self::$dispatcher->dispatch(self::$method, self::$uri);
        }
        //保存路由信息
        Request::instance()->routeInfo($routeInfo);
    }
}