<?php
class IndexController extends PlatfromController{
    public function index(){
        $this->display("index");
    }
    public function top(){
        $this->assign("name",$_SESSION['USER_INFO']['username']);
        $this->display("top");
    }
    public function menu(){
        $this->display("menu");
    }
    public function main(){
        $this->display("main");
    }
}