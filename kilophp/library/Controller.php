<?php
/**
 * Created by KiloFrameWork
 * User: xiejiawei<print_f@hotmail.com>
 * Date: 2020/4/8
 * Time: 14:49
 */

namespace kilophp;

/**
 * 基础控制器类
 * Class BaseController
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.2
 */
abstract class Controller
{
    /**
     * @access protected
     * @var object|null 模板引擎对象
     */
    protected $view = null;

    /**
     * @var string 当前访问的控制器
     */
    protected $controller;

    /**
     * @var string 当前调用的方法
     */
    protected $action;

    /**
     * @var array 赋值变量
     */
    protected $data = [];

    /**
     * Controller constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        //获取模板引擎实例
        $this->view = View::instance();
        //当前控制器
        $this->controller = request()->controller();
        //当前方法
        $this->action = request()->action();
    }

    /**
     * 赋值模板变量 支持数组
     * @param $name
     * @param null|string $value
     * @return mixed|$this
     */
    protected function assign($name, $value = null): Controller
    {
        if (is_array($name)) {
            $this->data = $name;
        } else {
            $this->data[$name] = $value;
        }
        return $this;
    }

    /**
     * 渲染模板输出 支持数组赋值
     * @param string $template
     * @param array $data
     * @return mixed
     */
    protected function display(string $template = '', array $data = [])
    {
        if (!empty($data)) {
            $this->data = array_merge($this->data, $data);
        }
        return $this->view->show($template, $this->data);
    }

    /**
     * 设置不存在的属性时，赋值模板变量
     * @param $name
     * @param $value
     */
    public
    function __set($name, string $value)
    {
        $this->assign($name, $value);
    }

    /**
     * 操作完成提示跳转
     * @param int $code 提示类型
     * @param string $msg 提示信息
     * @param int $wait 跳转时间
     * @param mixed $url 跳转的地址
     */
    protected
    function jump(int $code, string $msg, string $url, int $wait = 3)
    {
        $this->assign([
            'code' => $code,
            'msg' => $msg,
            'url' => $url,
            'wait' => $wait,
        ]);
        $tpl = $code == 1 ? Config::get('base.dispatch_success_tmpl') : Config::get('base.dispatch_error_tmpl');
        include $tpl;
        die();
    }
}
