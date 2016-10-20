<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/16
 * Time: 12:30
 * 图片工具类
 */
class ImageTool
{
    private $error;
    //保存创建画布的函数,文件类型与对应的函数映射
    private $createFuns = [
        "image/png"=>"imagecreatefrompng",
        "image/jpeg"=>"imagecreatefromjpeg",
        "image/gif"=>"imagecreatefromgif"
    ];
    //保存图片输出函数,文件类型与对应的函数映射
    private $outFuns = [
        "image/png"=>"imagepng",
        "image/jpeg"=>"imagejpeg",
        "image/gif"=>"imagegif",
    ];

    /**
     * @param $src_path 原图片的路径
     * @param $max_width 缩略图宽
     * @param $max_height 缩略图高
     * @param int $type 处理方式  1: 补白  2: 裁剪  3.xxxx
     * @return bool|string
     */
    public function thumb($src_path,$max_width,$max_height,$type =1){
        //根据大图片的路径生成一个小图片的路径
        $pathinfo = pathinfo($src_path);//pathinfo()返回文件路径信息
        $small_path = $pathinfo['dirname'].'/'.$pathinfo['filename']."_{$max_width}x{$max_height}.".$pathinfo['extension'];
        $src_path = UPLOADS_PATH.$src_path;//原图片的绝对路径

        if(!is_file($src_path)){
            $this->error = "原文件不存在";
            return false;
        }
        //准备原图片和目标图片对象
        $imagesize = getimagesize($src_path);
        list($src_width,$src_height) = $imagesize;//原图片大小
        $mime_type = $imagesize['mime'];//得到原图片的mime类型
        $createFun = $this->createFuns[$mime_type];//可变函数
        $src_img = $createFun($src_path);//原图片对象
        //创建目标图片
        $thumb_img = imagecreatetruecolor($max_width,$max_height);
        //补白(将目标图片背景变成白色)
        switch($type){
            case 1:
                $white = imagecolorallocate($thumb_img,255,255,255);
                imagefill($thumb_img,0,0,$white);
            break;
        }
        //计算缩放大小
        $scale = max($src_width/$max_width,$src_height/$max_height);//计算出最大的缩放比例
        $width = $src_width/$scale;//原图压缩后的宽
        $height = $src_height/$scale;//原图压缩后的高
        //进行缩放
        imagecopyresampled($thumb_img,$src_img,($max_width-$width)/2,($max_height-$height)/2,0,0,$width,$height,$src_width,$src_height);

        //保存目标图片
        $outFun = $this->outFuns[$mime_type];
        $outFun($thumb_img,UPLOADS_PATH.$small_path);//以绝对路径的形式输出
        return $small_path;//返回相对路径
    }
    public function getError(){
        return $this->error;
    }
}