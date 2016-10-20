<?php
class PlatfromController extends Controller{
    public function __construct(){
        $re = $this->checkLogin();
        if($re == false){
            $this->redirect("index.php?p=Admin&c=Login&a=login","请登录！",3);
        }
    }
    private function checkLogin(){
        new SessionDBTool();
        if(!isset($_SESSION['USER_INFO'])){
            if(isset($_COOKIE['id']) && isset($_COOKIE['password'])){
                $id = $_COOKIE['id'];
                $password = $_COOKIE['password'];
                $adminModel = new AdminModel();
                $re = $adminModel->checkByCookie($id,$password);
                if($re!==false){//正确登录
                    $_SESSION['USER_INFO'] = $re;
                    return true;
                }
            }
            return false;
        }
        return true;
    }
}
