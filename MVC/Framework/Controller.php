<?php
class Controller{
    private $data = [];
    protected function display($template){
        //extract()是将关联数组的键作为变量的名字，关联数组的值作为变量的值
        extract($this->data);
        require CURRENT_VIEW_PATH.$template.".html";
    }
    protected function assign($key,$value=''){
        if(is_array($key)){
            $this->data = array_merge($this->data,$key);//array_merge()合并数组
        }else{
            $this->data[$key] = $value;
        }
    }
    protected function redirect($url,$msg='',$time=0){//跳转功能
        if(headers_sent()){//headers_sent()判断是否发送header信息
            if($time == 0){
                echo <<<JS
                <script type='text/javascript' >
                    location.href = "{$url}";
                </script>
JS;
            }else{
                echo "<h1>{$msg}</h1>";
                $times = $time*1000;
                echo <<<JS
              <script type='text/javascript'>
                window.setTimeout(function(){
                     window.location.href = "{$url}";
                },$times);
              <script>
JS;
            }
        }else{
            //header没有发送
            if($time==0){
                //立即跳转
                header("Location: $url");
            }else{
                //等待跳转
                echo "<h1>{$msg}</h1>";
                header("Refresh: {$time};$url");
            }
        }
        exit;
    }
}
