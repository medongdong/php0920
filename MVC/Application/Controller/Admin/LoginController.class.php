<?php
class LoginController extends Controller{
    public function login(){
        $this->display("login");
    }
    public function check(){
        new SessionDBTool();//自定义session存储机制,session入库
        //接收用户输入的验证码
        $captcha = $_POST['captcha'];
        if(!CaptchaTool::check($captcha)){
            $this->redirect("index.php?p=Admin&c=Login&a=login","验证码输入错误!",3);
        }
        //接收用户请求参数
        $username = $_POST['username'];
        $password = $_POST['password'];
        //处理用户数据
        $adminModel = new AdminModel();
        $re = $adminModel->check($username,$password);
        if($re !== false){
            $_SESSION['USER_INFO'] = $re;
            $remember = $_POST['remember'];
            if($remember==1){
                $password = md5($re['password']."abcde");
                setcookie("id",$re['id'],time()+60*60*24,"/");
                setcookie("password",$password,time()+60*60*24,"/");
            }
            $this->redirect("index.php?p=Admin&c=Index&a=index");
        }else{
            $this->redirect("index.php?p=Admin&c=Login&a=login","登录失败!".$adminModel->getError(),3);
        }
    }
    public function loginout(){//退出功能
        new SessionDBTool();
        //清空session
        session_unset();
        session_destroy();
        //清空cookie
        setcookie("id",null,-1,"/");
        setcookie("password",null,-1,"/");
        $this->redirect("index.php?p=Admin&c=Login&a=login");
    }
}