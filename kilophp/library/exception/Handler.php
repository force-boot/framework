<?php
/**
 * Created by KiloPHPFrameWork
 * User: xiejiawei<print_f@hotmail.com>
 * Date: 2020/4/18
 * Time: 12:25
 */

namespace kilophp\exception;

use kilophp\Log;

/**
 * 框架自定义错误处理，当调试模式关闭时调用
 * @package kilophp\exception
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Handler extends \Whoops\Handler\Handler
{

    /**
     * 错误处理
     * @access public
     * @return int|null
     * @throws \Exception
     */
    public function handle()
    {
        //记录错误日志，前提开启了日志功能
        if (config('log.type')){
            $log = [
                'code' => $this->getCode(),
                'message' => $this->getMessage(),
                'file' => $this->getFile(),
                'line' => $this->getLine(),
            ];
            $message = "";
            foreach ($log as $k => $value) {
                $message .= "{$k}：{$value}" . PHP_EOL;
            }
            Log::save($message);
        }
        //输出框架默认错误页面
        include config('base.dafault_error_tmpl');
        return Handler::QUIT;
    }

    /**
     * 获取错误消息
     * @access public
     * @return string
     */
    public function getMessage()
    {
        return $this->getException()->getMessage();
    }

    /**
     * 获取错误行号
     * @access public
     * @return int
     */
    public function getLine()
    {
        return $this->getException()->getLine();
    }

    /**
     * 获取错误文件
     * @access public
     * @return string
     */
    public function getFile()
    {
        return $this->getException()->getFile();
    }

    /**
     * 获取Trace
     * @access public
     * @return string|array
     */
    public function getTrace()
    {
        return $this->getException()->getTrace();
    }


    /**
     * 获取错误code
     * @access public
     * @return int
     */
    public function getCode()
    {
        return $this->getException()->getCode();
    }
}