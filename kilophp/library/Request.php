<?php
/**
 * Created by KiloPHPFrameWork
 * User: xiejiawei<print_f@hotmail.com>
 * Date: 2020/4/15
 * Time: 23:16
 */

namespace kilophp;

/**
 * Class Request
 * @package kilophp
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Request
{
    /**
     * @var object 对象实例
     */
    protected static $instance = null;

    /**
     * @var string 当前请求类型
     */
    protected $method;

    /**
     * @var string pathinfo
     */
    protected $pathinfo;

    /**
     * @var string pathinfo 不含后缀
     */
    protected $path;

    /**
     * @var string 域名
     */
    protected $domain;

    /**
     * @var string 当前访问应用
     */
    protected $app;

    /**
     * @var string 当前访问控制器
     */
    protected $controller;

    /**
     * @var string 当前访问方法
     */
    protected $action;

    /**
     * @var array 当前路由信息
     */
    protected $routeInfo = [];

    /**
     * @var mixed 全局过滤规则
     */
    protected $filter = [
        'addslashes',
        'htmlspecialchars',
        'strip_tags'
    ];

    /**
     * @var array|mixed cookie
     */
    protected $cookie;

    /**
     * @var array|mixed session
     */
    protected $session;

    /**
     * @var false|string php://input
     */
    protected $input;

    /**
     * 构造函数
     * @access private
     */
    protected function __construct()
    {
        //获取地址栏url信息
        $this->path();
        //解析url信息
        $this->parseUrl($this->path);
        //如果存在配置，将替换系统默认的
        if (!empty($filter = Config::get('base.default_filter'))) {
            //读取过滤参数配置
            $this->filter = $filter;
        }
        // 保存 php://input
        $this->input = file_get_contents('php://input');
    }

    /**
     * 获取实例
     * @access public
     * @return Request
     */
    public static function instance(): Request
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * 解析路由Url
     * @param $url string
     * @access public
     */
    public function parseUrl(string $url = '')
    {
        $url_arr = explode('/', trim($url, '/'));
        $app = !empty($url_arr[0]) ? $url_arr[0] : Config::get('url.default_app');
        $controller = isset($url_arr[1]) ? $url_arr[1] : Config::get('url.default_controller');
        $action = isset($url_arr[2]) ? $url_arr[2] : Config::get('url.default_action');
        $this->app($app)->controller($controller)->action($action);
        unset($url_arr[0], $url_arr[1], $url_arr[2]);
        // 解析剩余的URL参数
        if (isset($url_arr[3]) && 0 == count($url_arr) % 2) {
            $var = array();
            preg_replace_callback('/(\w+)\/([^\/]+)/', function ($match) use (&$var) {
                $var[$match[1]] = strip_tags($match[2]);
            }, implode('/', $url_arr));
            $_GET = array_merge($var, $_GET);
        }
    }

    /**
     * 解析请求参数
     * @access public
     * @param array $param
     * @return array
     */
    public function paserParam(array $param): array
    {
        $_GET = array_merge($param, $_GET);
        return $_GET;
    }

    /**
     * 获取当前请求的路由信息
     * @access public
     * @param array $route 路由名称
     * @return array
     */
    public function routeInfo(array $route = [])
    {
        if (!empty($route)) {
            $this->routeInfo = $route;
        } else {
            return $this->routeInfo;
        }
    }

    /**
     * 设置或获取当前包含协议的域名
     * @access public
     * @param string $domain 域名
     * @return string
     */
    public function domain(string $domain = null)
    {
        if (!is_null($domain)) {
            $this->domain = $domain;
            return $this;
        } elseif (!$this->domain) {
            $this->domain = $this->scheme() . '://' . $this->host();
        }
        return $this->domain;
    }

    /**
     * 当前请求的host
     * @access public
     * @param bool $strict true 仅仅获取HOST
     * @return string
     */
    public function host(bool $strict = false): string
    {
        if (isset($_SERVER['HTTP_X_REAL_HOST'])) {
            $host = $_SERVER['HTTP_X_REAL_HOST'];
        } else {
            $host = $_SERVER['HTTP_HOST'];
        }
        return true === $strict && strpos($host, ':') ? strstr($host, ':', true) : $host;
    }

    /**
     * 当前URL地址中的scheme参数
     * @access public
     * @return string
     */
    public function scheme(): string
    {
        return $this->isSsl() ? 'https' : 'http';
    }

    /**
     * 设置获取GET参数
     * @access public
     * @param string|array $name 变量名
     * @param mixed $default 默认值
     * @param string|array $filter 过滤方法
     * @return mixed
     */
    public function get($name = '', $default = null, $filter = '')
    {
        return $this->input($_GET, $name, $default, $filter);
    }

    /**
     * 获取POST参数
     * @access public
     * @param string $name 变量名
     * @param mixed $default 默认值
     * @param string|array $filter 过滤方法
     * @return mixed
     */
    public function post($name = '', $default = null, $filter = '')
    {
        return $this->input($_POST, $name, $default, $filter);
    }

    /**
     * 获取PUT参数
     * @access public
     * @param string|array $name 变量名
     * @param mixed $default 默认值
     * @param string|array $filter 过滤方法
     * @return mixed
     */
    public function put($name = '', $default = null, $filter = '')
    {
        parse_str($this->input, $content);
        return $this->input($content, $name, $default, $filter);
    }

    /**
     * 获取DELETE参数
     * @access public
     * @param string|array $name 变量名
     * @param mixed $default 默认值
     * @param string|array $filter 过滤方法
     * @return mixed
     */
    public function delete($name = '', $default = null, $filter = '')
    {
        return $this->put($name, $default, $filter);
    }

    /**
     * 获取PATCH参数
     * @access public
     * @param string|array $name 变量名
     * @param mixed $default 默认值
     * @param string|array $filter 过滤方法
     * @return mixed
     */
    public function patch($name = '', $default = null, $filter = '')
    {
        return $this->put($name, $default, $filter);
    }

    /**
     * 获取变量 支持过滤和默认值
     * @param array $data 数据源
     * @param string|false $name 字段名
     * @param mixed $default 默认值
     * @param string|array $filter 过滤函数
     * @return mixed
     */
    public function input($data = [], $name = '', $default = null, $filter = '')
    {
        if (false === $name) {
            // 获取原始数据
            return $data;
        }
        $name = (string)$name;
        if ('' != $name) {
            // 解析name
            if (strpos($name, '/')) {
                list($name, $type) = explode('/', $name);
            } else {
                $type = 's';
            }
            // 按.拆分成多维数组进行判断
            foreach (explode('.', $name) as $val) {
                if (isset($data[$val])) {
                    $data = $data[$val];
                } else {
                    // 无输入数据，返回默认值
                    return $default;
                }
            }
            if (is_object($data)) {
                return $data;
            }
        }

        // 解析过滤器
        $filter = $this->getFilter($filter, $default);

        if (is_array($data)) {
            array_walk_recursive($data, [$this, 'filterValue'], $filter);
            reset($data);
        } else {
            $this->filterValue($data, $name, $filter);
        }

        if (isset($type) && $data !== $default) {
            // 强制类型转换
            $this->typeCast($data, $type);
        }
        return $data;
    }

    /**
     * 获取当前请求的php://input
     * @access public
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * 生成请求令牌
     * @access public
     * @param string $name 令牌名称
     * @param mixed $type 令牌生成方法
     * @return string
     */
    public function token($name = '__token__', $type = 'md5')
    {
        $type = is_callable($type) ? $type : 'md5';
        $token = call_user_func($type, $_SERVER['REQUEST_TIME_FLOAT']);
        if ($this->isAjax()) {
            header($name . ': ' . $token);
        }
        Session::set($name, $token);
        return $token;
    }

    /**
     * 获取request变量
     * @param string $name 数据名称
     * @param string $default 默认值
     * @param string|array $filter 过滤方法
     * @return mixed
     */
    public function request($name = '', $default = null, $filter = '')
    {
        return $this->input($_REQUEST, $name, $default, $filter);
    }


    /**
     * 获取session数据
     * @access public
     * @param string|array $name 数据名称
     * @param string $default 默认值
     * @param string|array $filter 过滤方法
     * @return mixed
     */
    public function session($name = '', $default = null, $filter = '')
    {
        if (empty($this->session)) {
            $this->session = Session::get();
        }
        if (is_array($name)) {
            return $this->session = array_merge($this->session, $name);
        }
        return $this->input($this->session, $name, $default, $filter);
    }

    /**
     * 获取cookie参数
     * @access public
     * @param string|array $name 数据名称
     * @param string $default 默认值
     * @param string|array $filter 过滤方法
     * @return mixed
     */
    public function cookie($name = '', $default = null, $filter = '')
    {
        if (empty($this->cookie)) {
            $this->cookie = Cookie::get();
        }
        if (is_array($name)) {
            return $this->cookie = array_merge($this->cookie, $name);
        } elseif (!empty($name)) {
            $data = Cookie::has($name) ? Cookie::get($name) : $default;
        } else {
            $data = $this->cookie;
        }

        // 解析过滤器
        $filter = $this->getFilter($filter, $default);

        if (is_array($data)) {
            array_walk_recursive($data, [$this, 'filterValue'], $filter);
            reset($data);
        } else {
            $this->filterValue($data, $name, $filter);
        }
        return $data;
    }

    /**
     * 获取server参数
     * @access public
     * @param string|array $name 数据名称
     * @param string $default 默认值
     * @param string|array $filter 过滤方法
     * @return mixed
     */
    public function server($name = '', $default = null, $filter = '')
    {
        return $this->input($_SERVER, false === $name ? false : strtoupper($name), $default, $filter);
    }

    /**
     * 设置或获取当前的过滤规则
     * @param mixed $filter 过滤规则
     * @return mixed
     */
    public function filter($filter = null)
    {
        if (is_null($filter)) {
            return $this->filter;
        } else {
            $this->filter = $filter;
        }
    }

    protected function getFilter($filter, $default)
    {
        if (is_null($filter)) {
            $filter = [];
        } else {
            $filter = $filter ?: $this->filter;
            if (is_string($filter) && false === strpos($filter, '/')) {
                $filter = explode(',', $filter);
            } else {
                $filter = (array)$filter;
            }
        }

        $filter[] = $default;
        return $filter;
    }

    /**
     * 递归过滤给定的值
     * @param mixed $value 键值
     * @param mixed $key 键名
     * @param array $filters 过滤方法+默认值
     * @return mixed
     */
    private function filterValue(&$value, $key, $filters)
    {
        $default = array_pop($filters);
        foreach ($filters as $filter) {
            if (is_callable($filter)) {
                // 调用函数或者方法过滤
                $value = call_user_func($filter, $value);
            } elseif (is_scalar($value)) {
                if (false !== strpos($filter, '/')) {
                    // 正则过滤
                    if (!preg_match($filter, $value)) {
                        // 匹配不成功返回默认值
                        $value = $default;
                        break;
                    }
                } elseif (!empty($filter)) {
                    // filter函数不存在时, 则使用filter_var进行过滤
                    // filter为非整形值时, 调用filter_id取得过滤id
                    $value = filter_var($value, is_int($filter) ? $filter : filter_id($filter));
                    if (false === $value) {
                        $value = $default;
                        break;
                    }
                }
            }
        }
        return $this->filterExp($value);
    }

    /**
     * 过滤表单中的表达式
     * @param string $value
     * @return void
     */
    public function filterExp(&$value)
    {
        // 过滤查询特殊字符
        if (is_string($value) && preg_match('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT LIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOT EXISTS|NOTEXISTS|EXISTS|NOT NULL|NOTNULL|NULL|BETWEEN TIME|NOT BETWEEN TIME|NOTBETWEEN TIME|NOTIN|NOT IN|IN)$/i', $value)) {
            $value .= ' ';
        }
        // TODO 其他安全过滤
    }

    /**
     * 强制类型转换
     * @param string $data
     * @param string $type
     * @return mixed
     */
    private function typeCast(&$data, $type)
    {
        switch (strtolower($type)) {
            // 数组
            case 'a':
                $data = (array)$data;
                break;
            // 数字
            case 'd':
                $data = (int)$data;
                break;
            // 浮点
            case 'f':
                $data = (float)$data;
                break;
            // 布尔
            case 'b':
                $data = (boolean)$data;
                break;
            // 字符串
            case 's':
            default:
                if (is_scalar($data)) {
                    $data = (string)$data;
                } else {
                    throw new \InvalidArgumentException('variable type error：' . gettype($data));
                }
        }
    }

    /**
     * 设置或者获取当前的应用名
     * @access public
     * @param string $app 应用名
     * @return string|Request
     */
    public function app(string $app = null)
    {
        if (!is_null($app)) {
            $this->app = $app;
            return $this;
        } else {
            return $this->app ?: '';
        }
    }

    /**
     * 设置或者获取当前的控制器名
     * @access public
     * @param string $controller 控制器名
     * @return string|Request
     */
    public function controller(string $controller = null)
    {
        if (!is_null($controller)) {
            $this->controller = $controller;
            return $this;
        } else {
            return $this->controller ?: '';
        }
    }

    /**
     * 设置或者获取当前的操作名
     * @access public
     * @param string $action 操作名
     * @return string|Request
     */
    public function action(string $action = null)
    {
        if (!is_null($action) && !is_bool($action)) {
            $this->action = $action;
            return $this;
        } else {
            $name = $this->action ?: '';
            return true === $action ? $name : strtolower($name);
        }
    }

    /**
     * 获取当前请求URL的pathinfo信息（含URL后缀）
     * @access public
     * @return string
     */
    public function pathinfo()
    {
        if (is_null($this->pathinfo)) {
            if (isset($_GET[Config::get('url.var_pathinfo')])) {
                // 判断URL里面是否有兼容模式参数
                $_SERVER['PATH_INFO'] = $_GET[Config::get('url.var_pathinfo')];
                unset($_GET[Config::get('url.var_pathinfo')]);
            } elseif (IS_CLI) {
                // CLI模式下 index.php module/controller/action/params/...
                $_SERVER['PATH_INFO'] = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '';
            }
            // 分析PATHINFO信息
            if (!isset($_SERVER['PATH_INFO'])) {
                foreach (Config::get('url.pathinfo_fetch') as $type) {
                    if (!empty($_SERVER[$type])) {
                        $_SERVER['PATH_INFO'] = (0 === strpos($_SERVER[$type], $_SERVER['SCRIPT_NAME'])) ?
                            substr($_SERVER[$type], strlen($_SERVER['SCRIPT_NAME'])) : $_SERVER[$type];
                        break;
                    }
                }
            }
            $this->pathinfo = empty($_SERVER['PATH_INFO']) ? '/' : ltrim($_SERVER['PATH_INFO'], '/');
        }
        return $this->pathinfo;
    }

    /**
     * 获取当前请求URL的pathinfo信息(不含URL后缀)
     * @access public
     * @return string
     */
    public function path()
    {
        if (is_null($this->path)) {
            $suffix = Config::get('url.html_suffix');
            $pathinfo = $this->pathinfo();
            if (false === $suffix) {
                // 禁止伪静态访问
                $this->path = $pathinfo;
            } elseif ($suffix) {
                // 去除正常的URL后缀
                $this->path = preg_replace('/\.(' . ltrim($suffix, '.') . ')$/i', '', $pathinfo);
            } else {
                // 允许任何后缀访问
                $this->path = preg_replace('/\.' . $this->ext() . '$/i', '', $pathinfo);
            }
        }
        return $this->path;
    }

    /**
     * 是否存在某个请求参数
     * @access public
     * @param string $name 变量名
     * @param string $type 变量类型
     * @param bool $checkEmpty 是否检测空值
     * @return mixed
     */
    public function has($name, $type = 'param', $checkEmpty = false)
    {
        if (empty($this->$type)) {
            $param = $this->$type();
        } else {
            $param = $this->$type;
        }
        // 按.拆分成多维数组进行判断
        foreach (explode('.', $name) as $val) {
            if (isset($param[$val])) {
                $param = $param[$val];
            } else {
                return false;
            }
        }
        return ($checkEmpty && '' === $param) ? false : true;
    }


    /**
     * 当前URL的访问后缀
     * @access public
     * @return string
     */
    public function ext()
    {
        return pathinfo($this->pathinfo(), PATHINFO_EXTENSION);
    }

    /**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    public function ip(int $type = 0, bool $adv = true)
    {
        $type = $type ? 1 : 0;
        static $ip = null;
        if (null !== $ip) {
            return $ip[$type];
        }
        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }

                $ip = trim($arr[0]);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }

    /**
     * 检测是否使用手机访问
     * @access public
     * @return bool
     */
    public function isMobile(): bool
    {
        if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
            return true;
        } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
            return true;
        } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
            return true;
        } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 当前的请求类型
     * @access public
     * @param bool $method true 获取原始请求类型
     * @return string
     */
    public function method(bool $method = false): string
    {
        if (true === $method) {
            return $_SERVER['REQUEST_METHOD'] ?: 'GET';
        } elseif (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $this->method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        } else {
            $this->method = $_SERVER['REQUEST_METHOD'] ?: 'GET';
        }
        return $this->method;
    }

    /**
     * 是否为GET请求
     * @access public
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->method() == 'GET';
    }

    /**
     * 是否为POST请求
     * @access public
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->method() == 'POST';
    }

    /**
     * 是否为PUT请求
     * @access public
     * @return bool
     */
    public function isPut(): bool
    {
        return $this->method() == 'PUT';
    }

    /**
     * 是否为DELTE请求
     * @access public
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->method() == 'DELETE';
    }

    /**
     * 是否为HEAD请求
     * @access public
     * @return bool
     */
    public function isHead(): bool
    {
        return $this->method() == 'HEAD';
    }

    /**
     * 是否为PATCH请求
     * @access public
     * @return bool
     */
    public function isPatch(): bool
    {
        return $this->method() == 'PATCH';
    }

    /**
     * 是否为OPTIONS请求
     * @access public
     * @return bool
     */
    public function isOptions(): bool
    {
        return $this->method() == 'OPTIONS';
    }

    /**
     * 是否为cli
     * @access public
     * @return bool
     */
    public function isCli(): bool
    {
        return PHP_SAPI == 'cli';
    }

    /**
     * 是否为cgi
     * @access public
     * @return bool
     */
    public function isCgi(): bool
    {
        return strpos(PHP_SAPI, 'cgi') === 0;
    }

    /**
     * 当前是否Ajax请求
     * @access public
     * @return bool
     */
    public function isAjax(): bool
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 当前是否ssl
     * @access public
     * @return bool
     */
    public function isSsl(): bool
    {
        if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
            return true;
        } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
            return true;
        }
        return false;
    }

    /**
     * 禁止clone
     * @access private
     */
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }
}