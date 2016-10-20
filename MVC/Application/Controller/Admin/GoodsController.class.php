<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/15
 * Time: 12:50
 * 商品添加功能
 */
class GoodsController extends PlatfromController
{
    public function index(){
        //查询出商品分类
        $categoryModel = new CategoryModel();
        $categorys = $categoryModel->getList();
        $this->assign("categorys",$categorys);
        //查询出品牌分类
        $brandModel = new BrandModel();
        $brands = $brandModel->getAll();
        $this->assign("brands",$brands);
        //接收用户输入的页数,默认为第一页
        $page = isset($_GET['page'])?$_GET['page']:1;
        $goodsModel = new GoodsModel();
        /**
         * 返回一个包含以下内容的数组:
         * pageResult => [
         *  "rows"=>当前页的列表数据,
         *  "pageHtml"=>分页工具条的html
         *]
         */
        $pageResult = $goodsModel->getpageResult($page);
        $this->assign($pageResult);
        $this->display("index");
    }
    public function add(){
        if($_SERVER['REQUEST_METHOD']=='POST'){
            //处理文件上传
            $uploadTool = new UploadTool();
            $logo = $uploadTool->uploadone($_FILES['logo'],"goods/");//文件上传后的相对路径
            if($logo === false){
                $this->redirect("index.php?p=Admin&c=Goods&a=add","上传失败!原因:".$uploadTool->getError(),3);
            }
            $_POST['logo'] = $logo;//将上传后的路径保存到POST中

            //处理图片缩略图
            $imageTool = new ImageTool();
            $thumb_logo = $imageTool->thumb($logo,50,50);//图片缩略后的文件路径 相对路径
            if($thumb_logo === false){
                $this->redirect("index.php?p=Admin&c=Index&a=index",$imageTool->getError(),3);
            }
            $_POST['thumb_logo'] = $thumb_logo;//将图片缩略后的文件路径保存到POST中

            //处理数据
            $goodsModel = new GoodsModel();
            $goods_id = $goodsModel->add($_POST);

            //处理相册数据
            $image_paths = $uploadTool->uploadMore($_FILES['path'],"gallery/");
            if($image_paths !== false){
                $img_intros = $_POST['img_intro'];
                $urls = $_POST['url'];
                foreach($image_paths as $key=>$image_path){
                    $gallery = ['img_intro'=>$img_intros[$key],'path'=>$image_path,'url'=>$urls[$key],'goods_id'=>$goods_id];
                    $galleryModel = new GalleryModel();
                    $galleryModel->insertData($gallery);
                }
            }
            $this->redirect("index.php?p=Admin&c=Goods&a=index");
        }else{
            //查询出商品分类
            $categoryModel = new CategoryModel();
            $categorys = $categoryModel->getList();
            $this->assign("categorys",$categorys);
            //查询出品牌分类
            $brandModel = new BrandModel();
            $brands = $brandModel->getAll();
            $this->assign("brands",$brands);
            $this->display("add");
        }
    }
    public function edit(){//修改数据
        if($_SERVER['REQUEST_METHOD']=='POST'){
            if($_FILES['logo']['name']){//如果上传了文件就处理上传文件
                //处理文件上传
                $uploadTool = new UploadTool();
                $logo = $uploadTool->uploadone($_FILES['logo'],"goods/");//文件上传后的相对路径
                if($logo === false){
                    $this->redirect("index.php?p=Admin&c=Goods&a=add","上传失败!原因:".$uploadTool->getError(),3);
                }
                $_POST['logo'] = $logo;//将上传后的路径保存到POST中
                //处理图片缩略图
                $imageTool = new ImageTool();
                $thumb_logo = $imageTool->thumb($logo,50,50);//图片缩略后的文件路径 相对路径
                if($thumb_logo === false){
                    $this->redirect("index.php?p=Admin&c=Index&a=index",$imageTool->getError(),3);
                }
                $_POST['thumb_logo'] = $thumb_logo;//将图片缩略后的文件路径保存到POST中
            }else{//如果没有上传文件就保存老的图片
                $oldimg = $_POST['oldimg'];
                $_POST['logo'] = $oldimg;//将上传后的路径保存到POST中
            }
            //处理数据
            $goodsModel = new GoodsModel();
            $goodsModel->edit($_POST);
            $this->redirect("index.php?p=Admin&c=Goods&a=index");
        }else{
            //查询出商品列表
            $goodsModel = new GoodsModel();
            $row = $goodsModel->getByPkResult($_GET['id']);
            $this->assign($row);
            //查询出商品分类
            $categoryModel = new CategoryModel();
            $categorys = $categoryModel->getList();
            $this->assign("categorys",$categorys);
            //查询出品牌分类
            $brandModel = new BrandModel();
            $brands = $brandModel->getAll();
            $this->assign("brands",$brands);
            $this->display("edit");
        }
    }
    public function delete(){//删除一条数据
        $id = $_GET['id'];
        $goodsModel = new GoodsModel();
        $goodsModel->deleteByPk($id);
        $this->redirect("index.php?p=Admin&c=Goods&a=index");
    }
}