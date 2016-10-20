<?php
class SessionDBTool{
    private $db;
    public function __construct(){
        //强制关闭session
        session_write_close();
        //php重写session存储机制的代码是当前对象中的方法
        session_set_save_handler(
            array($this,"open"),
            array($this,"close"),
            array($this,"read"),
            array($this,"write"),
            array($this,"destroy"),
            array($this,"gc")
        );
        //开启session机制
        @session_start();
    }
    public function open($savePath,$sessionName){
        //打开数据库连接
        $this->db = DB::getInstance($GLOBALS['config']['db']);
    }
    public function close(){
        //关闭数据空间
    }
    //读取数据
    public function read($sessionId){
        $sql = "select sess_data from session where sess_id='{$sessionId}' limit 1";
        $sess_data = $this->db->fetchColumn($sql);
        return empty($sess_data) ? '' : $sess_data;
    }
    //写数据
    public function write($sessionId,$data){
        $sql = "insert into session values('{$sessionId}','{$data}',unix_timestamp())
                on duplicate key update sess_data='{$data}',last_modified=unix_timestamp()";//unix_timestamp()获取时间戳
                //on duplicate key 当主键重复时进行更新
        $this->db->query($sql);
    }
    //根据主键PHPSESSID删除数据
    public function destroy($sessionId){
        $sql = "delete from session where sess_id = '{$sessionId}'";
        return $this->db->query($sql);
    }

    /**
     * 垃圾回收机制启动时,删除垃圾数据
     * 判断条件: 修改时间 + 有效时间 < 当前时间
     */
    public function gc($lifetime){
        $sql = "delete from session where last_modified+{$lifetime} < unix_timestamp()";
        return $this->db->query($sql);
    }
}