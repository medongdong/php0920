<?php
class Model{
    protected $db;
    protected $error;
    protected $table_name = '';//存储表名
    private $fields = [];//存储当前模型对应的表的字段信息
    public function __construct(){
        $this->db = DB::getInstance($GLOBALS['config']['db']);
        $this->initField();
    }
    public function getError(){//获取错误信息
        return $this->error;
    }
    //获取真实的表名
    private function table(){
        if(empty($this->table_name)){
            //从类的名字上获取表名
            $class_name = get_class($this);//get_class(对象)返回对象的类名
            $class_name = substr($class_name,0,-5);//去掉Model,得到类名
            $this->table_name = strtolower($class_name);//变成小写,得到逻辑表名
        }
        return '`'.$GLOBALS['config']['db']['prefix'].$this->table_name.'`';//拼接表前缀得到真实表名
    }

    /**
     * 查询出当前子类对象对应表的字段信息
     *$fields["pk"=>"id",  //主键
              "name",
              "logo" ]
     */
    private function initField(){
        $sql = "desc {$this->table()}";//查询表结构
        $rows = $this->db->fetchAll($sql);
        foreach($rows as $row){
            if($row['Key'] == 'PRI'){
                $this->fields['pk'] = $row['Field'];//将主键名存入键名为pk的fields数组中
            }else{
                $this->fields[] = $row['Field'];//将表中其他字段也存入fields数组中
            }
        }
    }

    /**
     * @param string $condition 根据条件查询一部分数据
     * @return array|null|二维数组
     */
    public function getAll($condition=''){
        $sql = "select * from {$this->table()}";
        if(!empty($condition)){
            $sql .= " where ".$condition;
        }
        $rows = $this->db->fetchAll($sql);
        return $rows;
    }
    /**
     *
     * @param $pk 根据主键的值删除一行数据
     */
    public function deleteByPk($pk){
        $sql = "delete from {$this->table()} where {$this->fields['pk']}={$pk}";
        $this->db->query($sql);
    }
    /**
     *
     * @param $condition 根据条件进行删除指定的数据
     */
    public function deleteByCondition($condition){
        $sql="delete from {$this->table()} where {$condition}";
        $this->db->query($sql);
    }
    /**
     *
     * @param $pk 根据主键的值得到一行数据
     * @return null|一维数组
     */
    public function getByPk($pk){
        $sql = "select * from {$this->table()} where {$this->fields['pk']}={$pk}";
        return $this->db->fetchRow($sql);
    }
    /**
     * 忽略不合法的数据
     * @param $data  &$data一定要使用引用赋值
     */
    private function ignoreErrorField(&$data){
        //$data中的键不在fields中,删除$data中键对应的值
        foreach($data as $key=>$value){
            if(!in_array($key,$this->fields)){  //判断键是否是表中的字段
                unset($data[$key]); //删除key在data中的值
            }
        }
    }

    /**
     *
     * @param $data 根据data数据进行动态拼装出insert语句并且保存数据保存到数据库中
     */
    public function insertData($data){
        //将data中不属于表中的数据删除
        $this->ignoreErrorField($data);
        $sql = "insert into {$this->table()} set ";
        $values = [];
        foreach($data as $k=>$v){
            $values[] = "`{$k}` = '{$v}'";//将字段和值放在$values[]数组中
        }
        $sql .= implode(",",$values);  //通过,连接起来
        $this->db->query($sql);
        return $this->db->last_insert_id();//返回得到最后生成id值
    }
    /**
     *
     * 前提:  data必须有主键的值
     * 根据data数据进行动态拼装出update语句并且保存数据保存到数据库中
     * @param $data 根据条件修改一条数据
     */
    public function updateData($data,$condition=''){
    	//将data中不属于表中的数据删除
        $this->ignoreErrorField($data);
        $sql = "update {$this->table()} set ";
        $values = [];
        foreach($data as $k=>$v){
            $values[] = "`{$k}` = '{$v}'";//将字段和值放在$values[]数组中
        }
        $sql .= implode(",",$values);  //通过,连接起来
        //将主键作为条件
        if(empty($condition)){
            $sql.=" where {$this->fields['pk']} = {$data[$this->fields['pk']]}";
        }else{
            $sql .= " where ".$condition;
        }
        $this->db->query($sql);
    }

    /**
     * @param $condition 根据条件获取一行数据
     * @return array|mixed|null|一维数组
     */
    public function getRow($condition){
        $sql = "select * from {$this->table()} where {$condition} limit 1";
        return $this->db->fetchRow($sql);
    }

    /**
     * @param $field
     * @param $condition 根据条件获取一行数据中的一个字段的值
     * @return mixed|null
     */
    public function getColumn($field,$condition){
        $sql = "select {$field} from {$this->table()} where {$condition} limit 1";
        return $this->db->fetchColumn($sql);
    }

    /**
     * @param string $condition 根据条件统计总条数
     * @return mixed|null
     */
    public function getCount($condition=''){
        $sql = "select count(*) from {$this->table()}";
        if(!empty($condition)){
            $sql .= " where ".$condition;
        }
        return $this->db->fetchColumn($sql);
    }
}
