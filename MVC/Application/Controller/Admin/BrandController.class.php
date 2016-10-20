<?php
class BrandController extends PlatfromController{
    public function index(){
        $brand_model = new BrandModel();
        $rows = $brand_model->getAll();
        $this->assign("rows",$rows);
        $this->display("index");
    }
    public function remove(){
        $id = $_GET['id'];
        $brand_model = new BrandModel();
        $brand_model->deleteByPk($id);
        $this->redirect("index.php?a=Admin&c=Brand&a=index");
    }
    public function add(){
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $data = $_POST;
            $brand_model = new BrandModel();
            $brand_model->insertData($data);
            $this->redirect("index.php?a=Admin&c=Brand&a=index");
        }else{
            $this->display("add");
        }
    }
    public function edit(){
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $data = $_POST;
            $brand_model = new BrandModel();
            $brand_model->updateData($data);
            $this->redirect("index.php?a=Admin&c=Brand&a=index");
        }else{
            $id = $_GET['id'];
            $brand_model = new BrandModel();
            $row = $brand_model->getByPk($id);
            $this->assign($row);
            $this->display("edit");
        }
    }
}
