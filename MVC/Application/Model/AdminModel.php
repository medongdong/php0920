<?php
class AdminModel extends Model{
    /**
     * 如果子类中重写父类的构造方法，必须显示的调用父类方法 parent::__construct();
     */
    public function __construct(){
        parent::__construct();
    }
    /**
     * 重写管理员数据添加数据到数据库的功能
     */
    public function insertData($data)
    {
        //用户名不能为空
        if(empty($data['username'])){
            $this->error = "用户名不能为空！";
            return false;
        }
        //email不能为空
        if(empty($data['email'])){
            $this->error = "邮箱不能为空！";
            return false;
        }
        //密码不能为空
        if(empty($data['password'])){
            $this->error = "密码不能为空！";
            return false;
        }
        //密码必须和确认密码一致
        if($data['password'] != $data['repassword']){
            $this->error = "输入的密码不一致！";
            return false;
        }
        $data['add_time'] = time();
        /**
         * 将$data中的密码取出来，md5加密后重新放到数组中
         */
        $data['password'] = md5($data['password']);
        return parent::insertData($data);
    }
    /**
     * 更新数据
     * @param 必须包含主键的值 $new_data
     */
    public function update($new_data){
        if(empty($new_data['username'])){
            $this->error = "用户名不能为空！";
            return false;
        }
        if(empty($new_data['email'])){
            $this->error = "邮箱不能为空！";
            return false;
        }
        if(!empty($new_data['oldpassword'])){//如果填写了旧密码，才修改密码
            //1.新密码不能为空
            if(empty($new_data['password'])){
                $this->error = "密码不能为空！";
                return false;
            }
            //2.新密码和新确认密码要一致
            if($new_data['password'] != $new_data['repassword']){
                $this->error = "输入的密码不一致！";
                return false;
            }
            //3,旧密码必须和数据库一致
            $old_password_DB = $this->getColumn("password","id={$new_data['id']}");
            if(md5($new_data['oldpassword']) != $old_password_DB){
                $this->error = "旧密码输入有误！";
                return false;
            }
            //将密码取出后md5加密后在添加$new_data中
            $new_data['password'] = md5($new_data['password']);
        }else{//如果没有填写旧密码，就不修改密码
            unset($new_data['password']);//删除密码字段
        }
        parent::updateData($new_data);
    }
    public function check($username,$password){
        $password = md5($password);
        $sql = "select * from admin where username='{$username}' and password='{$password}' limit 1";
        $re = $this->db->fetchRow($sql);
        if(empty($re)){
            $this->error = "账号密码错误！";
            return false;
        }
        return $re;
    }
    //根据cookie内容查询用户信息
    public function checkByCookie($id,$password){
        $sql = "select * from admin where id={$id}";
        $re = $this->db->fetchRow($sql);
        if(empty($re)){//是否有该账号密码
            return false;
        }else{
            $password_in_db = md5($re['password']."abcde");
            if($password == $password_in_db){//判断账号密码是否正确
                return $re;//账号密码正确
            }else{
                return false;
            }
        }
    }
}
