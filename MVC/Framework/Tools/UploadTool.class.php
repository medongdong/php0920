<?php

/**
 * 文件上传工具类
 */
class UploadTool
{
    private $max_size;//保存文件大小
    private $allow_types;//保存允许上传的类型
    private $error;//保存上传时的错误信息
    /**
     * UploadTool constructor.
     * @param $max_size
     * @param $allow_type
     */
    public function __construct()
    {
        $this->max_size = empty($max_size)?$GLOBALS['config']['upload']['max_size']:$max_size;
        $this->allow_types = empty($allow_types)?$GLOBALS['config']['upload']['allow_types']:$allow_types;
    }
    /**
     * @param $fileinfo 上传文件信息
     * @param $path 上传文件路径
     * $this->uploadone($FILES['name'],"goods/") 单文件上传
     */
    public function uploadone($fileinfo,$path=''){
        //判断是否上传成功
        if($fileinfo['error'] != 0){
            $this->error = "上传失败";
            return false;
        }
        //判断上传文件大小
        if($fileinfo['size'] > $this->max_size){
            $this->error = "上传文件已超出指定大小!";
            return false;
        }
        //判断上传文件类型
        if(!in_array($fileinfo['type'],$this->allow_types)){
            $this->error = "上传文件类型不满足需求";
            return false;
        }
        //判断是否为上传文件
        if(!is_uploaded_file($fileinfo['tmp_name'])){
            $this->error = "不是上传文件";
            return false;
        }
        //处理文件名
        $filename = uniqid().strrchr($fileinfo['name'],'.');//生成一个新的文件名字 uniqid()获取一个带前缀、基于当前时间微秒数的唯一ID。
        //文件目录
        $dir = UPLOADS_PATH.$path.date('Ymd');
        if(!is_dir($dir)){  //如果不是一个目录
            mkdir($dir,0777,true); //true:: 递归创建目录
        }
        $filepath = $path.date('Ymd')."/".$filename;   //相对路径
        //移动文件
        if(!move_uploaded_file($fileinfo['tmp_name'],UPLOADS_PATH.$filepath)){//UPLOADS_PATH.$filepath 绝对路径
            $this->error = "移动文件失败";
            return false;
        }
        return  $filepath;
    }
    /**
     * @param $files 上传同名的多个文件
     * @param $path
     * @return array|bool
     */
    public function uploadMore($fileinfos,$path){
        $filepaths = [];//保存上传成功后的文件路径
        foreach($fileinfos['error'] as $key=>$error){
            if($error!=0){
                continue;
            }
            //构建出每个上传文件信息
            $fileinfo = [];
            $fileinfo['name'] = $fileinfos['name'][$key];
            $fileinfo['type'] = $fileinfos['type'][$key];
            $fileinfo['tmp_name'] = $fileinfos['tmp_name'][$key];
            $fileinfo['size'] = $fileinfos['size'][$key];
            $fileinfo['error'] = $error;
            //将文件信息传递给$fileinfo
            $filepath = $this->uploadone($fileinfo,$path);
            if($filepath !== false){
                $filepaths[] = $filepath;
            }else{
                return false;
            }
        }
        return $filepaths;
    }
    //获取错误信息
    public function getError(){
        return $this->error;
    }
}