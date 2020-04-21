<?php
/**
 * Created by KiloFrameWork
 * User: xiejiawei<print_f@hotmail.com>
 * Date: 2020/4/3
 * Time: 17:49
 */
namespace kilophp\vendor;

/**
 * 验证码类
 * Class Captcha
 * @package kilophp\Vendor
 * @author XieJiaWei<print_f@hotmail.com>
 * @version 1.0.0
 */
class Captcha
{
    private $codelen;    //验证码长度
    private $code;        //验证码字符串
    private $width;        //图片的宽度
    private $height;    //图片的高度
    private $fontsize;    //字号大小
    private $fontfile;    //字体文件
    private $img;        //图像资源


    /**
     * Captcha constructor.
     * @param int $codelen
     * @param int $width
     * @param int $height
     * @param int $fontsize
     */
    public function __construct($codelen = 4, $width = 85, $height = 22, $fontsize = 18)
    {
        $this->codelen = $codelen;
        $this->width = $width;
        $this->height = $height;
        $this->fontsize = $fontsize;
        //字体文件必须是绝对路径
        $this->fontfile = ROOT_PATH . "public" . DS . "static/Admin" . DS . "Images" . DS . "msyh.ttf";

        $this->createCode();    //生成随机的验证码字符串
        $this->createImg();        //创建画布
        $this->createBg();        //画布背景
        $this->createFont();    //写入文本
        $this->outPut();        //输出图像
    }

    /**
     * 生成随机验证码
     * @access private
     */
    private function createCode()
    {
        $arr1 = array_merge(range('a', 'z'), range(0, 9), range('A', 'Z'));
        shuffle($arr1); //打乱数组
        $arr2 = array_rand($arr1, 4); //随机取4个下标
        $str = "";
        foreach ($arr2 as $index) {
            $str .= $arr1[$index];
        }
        $this->code = $str;
    }

    /**
     * 创建画布
     * @access private
     */
    private function createImg()
    {
        $this->img = imagecreatetruecolor($this->width, $this->height);
    }

    /**
     * 绘制图像背景
     * @access private
     */
    private function createBg()
    {
        //分配颜色
        $color1 = imagecolorallocate($this->img, mt_rand(200, 250), mt_rand(200, 250), mt_rand(200, 255));
        //绘制带背景的矩形
        imagefilledrectangle($this->img, 0, 0, $this->width, $this->height, $color1);
        //绘制像数点
        for ($i = 1; $i <= 200; $i++) {
            $color3 = imagecolorallocate($this->img, mt_rand(0, 250), mt_rand(0, 250), mt_rand(50, 255));
            imagesetpixel($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), $color3);
        }
        //绘制线段
        for ($i = 1; $i < 10; $i++) {
            $color4 = imagecolorallocate($this->img, mt_rand(0, 250), mt_rand(0, 250), mt_rand(50, 255));
            imageline($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $color4);
        }
    }

    /**
     * 写入文本到图像
     * @access private
     */
    private function createFont()
    {
        $color2 = imagecolorallocate($this->img, mt_rand(0, 250), mt_rand(0, 250), mt_rand(50, 255));
        imagettftext($this->img, $this->fontsize, 0, 10, 20, $color2, $this->fontfile, $this->code);
    }

    /**
     * 输出图像资源
     * @access private
     */
    private function outPut()
    {
        header("content-type:image/png");
        imagepng($this->img);
        imagedestroy($this->img);
    }

    /**
     * 获取验证码
     * @access public
     * @return string
     */
    public function getCode()
    {
        return strtolower($this->code);
    }
}