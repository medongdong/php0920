<?php
//生成验证码
class CaptchaController extends Controller{
    public function index(){
        //需要生成一个验证码图片发送给浏览器
        CaptchaTool::generate(3);
    }
}