<?php
class Framework{
    public static function run(){
        //spl_autoload_register()注册类的自动加载函数
        //spl_autoload_register("Framework::userAutoload");
        spl_autoload_register(array("Framework","userAutoload"));
        //spl_autoload_register(array(self,"userAutoload"));
        //不能改变其顺序
        self::initPath();
        self::intoConfig();
        self::intoRequestParams();
        self::classMapping();
        self::dispache();

    }
    //初始化目录定义路径常量
    public static function initPath(){
        //$_SERVER['SCRIPT_FILENAME'] 运行文件所在路径
        defined("DS") or define("DS",DIRECTORY_SEPARATOR);//DIRECTORY_SEPARATOR php预定义目录分隔符常量
        defined("ROOT_PATH") or define("ROOT_PATH",dirname($_SERVER['SCRIPT_FILENAME']).DS);//项目根目录
        defined("APP_PATH") or define("APP_PATH",ROOT_PATH."Application".DS);//Application目录
        defined("FRAME_PATH") or define("FRAME_PATH",ROOT_PATH."Framework".DS);//Framework目录
        defined("CONFIG_PATH") or define("CONFIG_PATH",APP_PATH."Config".DS);//Config目录
        defined("CONTROLLER_PATH") or define("CONTROLLER_PATH",APP_PATH."Controller".DS);//Controller目录
        defined("MODEL_PATH") or define("MODEL_PATH",APP_PATH."Model".DS);//Model目录
        defined("VIEW_PATH") or define("VIEW_PATH",APP_PATH."View".DS);//View目录
        defined("TOOLS_PATH") or define("TOOLS_PATH",FRAME_PATH."Tools".DS);//Tools目录
        defined("UPLOADS_PATH") or define("UPLOADS_PATH",ROOT_PATH."Uploads".DS);//Uploads目录

    }
    //加载配置文件
    public static function intoConfig(){
        //加载配置文件
        $GLOBALS['config'] = require CONFIG_PATH."application.config.php";
    }
    //初始化请求参数
    public static function intoRequestParams(){
        $p = isset($_GET['p']) ? $_GET['p'] : $GLOBALS['config']['default']['platform'];
        $c = isset($_GET['c']) ? $_GET['c'] : $GLOBALS['config']['default']['controller'];
        $a = isset($_GET['a']) ? $_GET['a'] : $GLOBALS['config']['default']['action'];

        defined("CURRENT_CONTROLLER_PATH") or define("CURRENT_CONTROLLER_PATH",CONTROLLER_PATH.$p.DS);//当前控制器平台目录
        defined("CURRENT_VIEW_PATH") or define("CURRENT_VIEW_PATH",VIEW_PATH.$p.DS.$c.DS);//当前控制器所对应的视图文件夹平台目录
        defined("PLATFORM_NAME") or define("PLATFORM_NAME",$p);
        defined("CONTROLLER_NAME") or define("CONTROLLER_NAME",$c);
        defined("ACTION_NAME") or define("ACTION_NAME",$a);
    }
    //请求分发（根据请求参数调用控制器中的方法执行）
    public static function dispache(){
        $controller_name = CONTROLLER_NAME."Controller";
        $controller = new $controller_name;
        $action_name = ACTION_NAME;
        $controller->$action_name();
    }
    //映射框架代码中的类和类文件
    public static function classMapping(){
        $GLOBALS['classMapping'] = [//特殊类和类路径的映射
            'DB'=>TOOLS_PATH."DB.class.php",//DB类
            'Model'=>FRAME_PATH."Model.php",//基础模型类
            'Controller'=>FRAME_PATH."Controller.php"//基础控制器类
        ];
    }
    //自动加载类文件
    public static function userAutoload($class_name){
        if(isset($GLOBALS['classMapping'][$class_name])){//加载特殊类
            require $GLOBALS['classMapping'][$class_name];
        }elseif(substr($class_name,-10)=="Controller"){//加载控制器类
            require CURRENT_CONTROLLER_PATH.$class_name.".class.php";
        }elseif(substr($class_name,-5)=="Model"){//加载模型类
            require MODEL_PATH.$class_name.".php";
        }elseif(substr($class_name,-4)=="Tool"){//加载工具类
            require TOOLS_PATH.$class_name.".class.php";
        }
    }
}
