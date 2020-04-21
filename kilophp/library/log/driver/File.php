<?php
/**
 * Created by KiloFrameWork
 * User: xiejiawei<print_f@hotmail.com>
 * Date: 2020/4/5
 * Time: 11:49
 */

namespace kilophp\log\driver;

use \Exception;

/**
 * 文件日志类
 * @package kilophp\log\driver
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class File
{
    /**
     * 日志配置
     * @access public
     * @var array|mixed
     */
    public $config = [
        'type' => 'File',
        'path' => LOG_PATH,
        'storage_time' => 0
    ];

    /**
     * File constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }
        if (!empty($this->config['storage_time'])) {
            $this->checkTime();
        }
    }

    /**
     * 保存日志
     * @access public
     * @param mixed $message
     * @return mixed
     * @throws \Exception
     */
    public function save(string $message)
    {
        $log_file = $this->getLogFile();
        $path = dirname($log_file);//获取日志存放目录
        !is_dir($path) && mkdir($path, 0755, true);
        try {
            return file_put_contents($log_file, date("Y-m-d H:i:s") . "：" . PHP_EOL . $message . PHP_EOL, FILE_APPEND);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $log_file);
        }
    }

    /**
     * 删除日志
     * @access public
     * @param string $logFile 删除的文件或目录
     * @return mixed|void
     */
    public function delete(string $logFile)
    {
        if (is_dir($logFile)) {
            foreach (scandir($logFile) as $file) {
                if ($file == "." || $file == "..") {
                    continue;
                }
                if (is_dir($logFile . DS . $file)) {
                    $this->delete($logFile . DS . $file);
                } else {
                    unlink($logFile . DS . $file);
                }
            }
            rmdir($logFile);
        } else {
            file_exists($logFile) && unlink($logFile);
        }
    }

    /**
     * 获取日志文件名
     * @access private
     * @return string
     */
    private function getLogFile()
    {
        $filename = date('Ymd') . DS . date('d') . '.log';
        return $this->config['path'] . $filename;
    }

    /**
     * 如果开启了日志保存时间，该方法会查找符合条件的日志并删除
     * @access private
     */
    private function checkTime()
    {
        if (is_dir($this->config['path']) && $path = $this->config['path']) {
            foreach (scandir($path) as $dir) {
                if ($dir == "." || $dir == "..") {
                    continue;
                }
                if (is_dir($this->config['path'] . DS . $dir) && ($dir + $this->config['storage_time']) <= date('Ymd')) {
                    $this->delete($path . DS . $dir);
                }
            }
        }
    }

}