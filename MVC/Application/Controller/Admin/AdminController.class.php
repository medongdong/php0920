<?php
class AdminController extends PlatfromController{
    public function index(){
        $admin_model = new AdminModel();
        $rows = $admin_model->getAll();
        $this->assign("rows",$rows);
        $this->display("index");
    }
    public function remove(){
        $id = $_GET['id'];
        $admin_model = new AdminModel();
        $admin_model->deleteByPk($id);
        $this->redirect("index.php?a=Admin&c=Admin&a=index");
    }
    public function add(){
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $data = $_POST;
            //2.处理数据
            //调用模型，上面有一个方法可以将数据保存到数据库
            $adminModel = new AdminModel();
            $re = $adminModel->insertData($data);//将数据保存到数据库中
            if($re === false){
                $this->redirect("index.php?p=Admin&c=Admin&a=add",'添加管理员失败！原因：'.$adminModel->getError(),3);
            }else{
                //3.显示页面
                $this->redirect("index.php?p=Admin&c=Admin&a=index");
            }
        }else{
            $this->display("add");
        }
    }
    public function edit(){
        if($_SERVER['REQUEST_METHOD'] == "POST"){//根据id更新一条数据
            //1.接收请求参数
            $data = $_POST;
            $adminModel = new AdminModel();
            $re = $adminModel->update($data);
            if($re === false){
                $this->redirect("index.php?p=Admin&c=Admin&a=edit",'修改失败！原因：'.$adminModel->getError(),3);
            }else{
                $this->redirect("index.php?p=Admin&c=Admin&a=index");
            }
        }else{//显示修改页面
            $id = $_GET['id'];
            $adminModel = new AdminModel();
            $row = $adminModel->getByPk($id);//根据主键id查询一条数据
            $this->assign($row);
            $this->display("edit");
        }
    }
}
