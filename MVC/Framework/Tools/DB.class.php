<?php

/**
 * Class DB
 */
class DB{
    private $host;
    private $root;
    private $password;
    private $dbname;
    private $charset;
    private $port;

    private static $instance;
    /**
     * 保存数据库链接
     * @var
     */
    private $link;
    private function __construct($config){
        $this->host = isset($config['host']) ? $config['host'] : '127.0.0.1';
        $this->root = isset($config['root']) ? $config['root'] : 'root';
        $this->password = $config['password'];
        $this->dbname = $config['dbname'];
        $this->charset = isset($config['charset']) ? $config['charset'] : 'utf8';
        $this->port = isset($config['port']) ? $config['port'] : 3306;
        /**
         * 链接到数据库
         */
        $this->connect();
        /**
         * 设置编码
         */
        $this->setCharset();
    }

    /**
     * 专门用户创建单例对象
     */
    public static function getInstance($config){
        if ( !self::$instance instanceof self ) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }
    /**
     * 链接数据库
     */
    private function connect(){
        $this->link = mysqli_connect($this->host,$this->root,$this->password,$this->dbname,$this->port);
        if ($this->link === false) {//如果链接失败，提示错误信息
            die(
                "链接失败！错误代码".mysqli_connect_errno()."<br/>".
                "错误信息".mysqli_connect_error()
            );
        }
    }
    /**
     * 设置编码
     */
    private function setCharset(){
        $result = mysqli_set_charset($this->link,$this->charset);
        if ($result === false) {//设置编码失败
            die(
                "设置编码失败！错误代码".mysqli_errno($this->link)."<br/>".
                "错误信息".mysqli_error($this->link)
            );
        }
    }
    /**
     * 专业执行sql语句
     */
    public function query($sql){
        //执行sql
        $result = mysqli_query($this->link,$sql);
        if ($result === false) {
            die(
                "执行sql失败！错误代码".mysqli_errno($this->link)."<br/>".
                "错误信息".mysqli_error($this->link)
            );
        }
        //必须把结果返回
        return $result;
    }

    /**
     * 专业用于获取多行数据
     * @param $sql
     */
    public function fetchAll($sql){
        //1.执行sql获取结果
        $result = $this->query($sql);
        //2.从结果集中取出数据
        $rows = [];
        while($row = mysqli_fetch_assoc($result)){
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * 专业执行sql获取一条记录
     * @param $sql 语句
     */
    public function fetchRow($sql){
        /*        //1.执行sql
                    $result = $this->query($sql);
                //2.获取结果集中的数据
                    $row = mysqli_fetch_assoc($result);
                    return $row;*/
        //1.执行sql
        $rows = $this->fetchAll($sql);
        //2.获取结果
        return empty($rows) ? null : $rows[0];
    }

    /**
     * 专业执行sql获取一行一列的结果
     * @param $sql
     */
    public function fetchColumn($sql){
        /*        //1.执行sql
                    $result = $this->query($sql);
                //2.获取结果
                    $row = mysqli_fetch_row($result);
                    return $row[0];*/
        //1.执行sql
        $row = $this->fetchRow($sql);
        //2.获取结果
        return empty($row) ? null : array_values($row)[0];
    }
    /**
     * 释放链接
     */
    public function __destruct(){
        /*$result = mysqli_close($this->link);//成功时返回 TRUE， 或者在失败时返回 FALSE。
        if ($result === false) {
            die(
                "释放链接失败！错误代码".mysqli_errno($this->link)."<br/>".
                "错误信息".mysqli_error($this->link)
            );
        }*/
    }

    /**
     * 当对象被序列化时,对象会自动调用__sleep函数,该函数返回的数组参数中包含哪些属性被序列化
     */
    public function __sleep(){
        return ['host','root','password','dbname','port','charset'];
    }

    /**
     * 对象重新初始化
     */
    public function __wakeup(){
        $this->connect();
        $this->setCharset();
    }

    /**
     * 私有的克隆方法
     */
    private function __clone(){

    }
    /**
     *
     * @return int|string 得到最后生成id值
     */
    public function last_insert_id(){
        return mysqli_insert_id($this->link);
    }
}
