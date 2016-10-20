<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/1
 * Time: 18:28
 */
class CategoryController extends PlatfromController
{
    //设计一个URL index.php?p=Admin&c=Category&a=index 实现一个分类列表的展示
    public function index(){
        //1.接收请求数据
        //2.处理数据
        //调用模型，期望模型上有个一个方法可以查询所有数据
        $categoryModel = new CategoryModel();
        $rows = $categoryModel->getList();
        //3.显示页面
        $this->assign('rows', $rows);//assign("key","value");通过指定的key将 value值分配到页面上.在页面上通过key取得value值
        $this->display('index');//display("模板名称"); 调用指定的模板显示
    }
    //设计一个URL index.php?p=Admin&c=Category&a=add 展示添加商品分类
    public function add(){
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            //1.接收请求数据
            $data = $_POST;
            //2.处理数据
            //将提交数据保存到数据库
            $categoryModel = new CategoryModel();
            $re = $categoryModel->insertData($data);
            if($re === false){
                $this->redirect("index.php?p=Admin&c=Category&a=add","添加分类失败！错误原因：".$categoryModel->getError(),3);
            }else{
                //3.显示页面
                $this->redirect("index.php?p=Admin&c=Category&a=index");//跳转到指定的地址.
            }
        }else{//get方式接收展示添加商品分类页面
            //1.接收请求数据
            //2.处理数据
            //获取所有商品分类
            $categoryModel = new CategoryModel();
            $rows = $categoryModel->getList();
            //3.显示页面
            $this->assign('rows', $rows);
            $this->display('add');
        }
    }
    //设计一个URL index.php?p=Admin&c=Category&a=delete&id= 根据id删除一条商品分类
    public function delete(){
        //1.接收请求数据
        $id = $_GET['id'];
        //2.处理数据
        //将提交数据保存到数据库
        $categoryModel = new CategoryModel();
        $re = $categoryModel->deleteByPk($id);
        if($re === false){
            $this->redirect("index.php?p=Admin&c=Category&a=index","删除失败！错误原因：".$categoryModel->getError(),3);
        }else{
            //3.显示页面
            $this->redirect("index.php?p=Admin&c=Category&a=index");
        }
    }
    //设计一个URL  index.php?p=Admin&c=Category&a=edit&id=1 实现回显修改页面
    public function edit(){
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            //1.接收请求数据
            $data = $_POST;
            //2.处理数据
            $categoryModel = new CategoryModel();
            $re = $categoryModel->update($data);
            if ($re === false) {
                $this->redirect("index.php?p=Admin&c=Category&a=edit&id=".$data['id'],"编辑失败！错误原因：".$categoryModel->getError(),3);
            }else{
                //2.显示页面
                $this->redirect("index.php?p=Admin&c=Category&a=index");
            }
        }else{
            //1.接收请求数据
            $id = $_GET['id'];
            //2.处理数据
            //根据id查询出一条数据
            $categoryModel = new CategoryModel();
            $row = $categoryModel->getByPk($id);
            //展示下拉列表，并且默认选择上级分类
            $rows = $categoryModel->getList();
            //3.显示页面
            //分配是当前id对应的数据
            $this->assign($row);
            //所有分类数据
            $this->assign("rows",$rows);
            $this->display('edit');
        }
    }
}