<?php

/**
 * 验证码工具类
 */
class CaptchaTool{
    //生成随机码
    private static function makeCode($num){
        $chars = "123456789ABCDEFJHGKLMNPQRSTUVWXYZ";
        $chars = str_shuffle($chars);//str_shuffle()打乱字符串
        $random_code = substr($chars,0,$num);//截取前$num位字符
        return $random_code;
    }
    //生成一个指定长度的验证码
    public static function generate($num = 6){
        //告知浏览器发送给它的数据是图片数据
        header('Content-Type:image/jpeg;charset=utf-8');
        //背景随机变化
        $imagefile = TOOLS_PATH."captcha/captcha_bg".mt_rand(1,5).".jpg";
        list($width,$height) = getimagesize($imagefile);//获取图片大小
        $image = imagecreatefromjpeg($imagefile);//得到图片资源
        //在图片上加上白色边框
        $white = imagecolorallocate($image,255,255,255);
        imagerectangle($image,0,0,$width-1,$height-1,$white);//在图片上画一个矩形

        //生成一个随机的字符串
        $random_code =  self::makeCode($num);

        //将文字写在图片上
        $black = imagecolorallocate($image,0,0,0);
        imagestring($image,5,$width/3,$height/8,$random_code,mt_rand(0,1)?$white:$black);
        //>>向图片上添加点或者线干扰视线
        for($i=0;$i<100;++$i){//加点
            $color = imagecolorallocate($image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imagesetpixel($image , mt_rand(0,$width) , mt_rand(0,$height) ,$color);
        }
        for($i=0;$i<3;++$i){//加线
            $color = imagecolorallocate($image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
            imageline($image,mt_rand(0,$width) , mt_rand(0,$height),mt_rand(0,$width) , mt_rand(0,$height),$color);
        }
        new SessionDBTool();
        $_SESSION['random_code'] = $random_code;//将随机字符串保存到session中
        imagejpeg($image);//以jpg格式输出图片
        imagedestroy($image);//释放图片资源

    }
    //对用户输入的验证码进行验证
    public static function check($captcha){
        new SessionDBTool();
        $random_code = $_SESSION['random_code'];
        return  strtolower($captcha)==strtolower($random_code);//strtolower()将字符串变为小写
    }
}