<?php
class GoodsModel extends Model{
    public function add($data){
        //处理商品状态的数据
        $status = 0;//初始化商品的状态:0   二进制: 000
        //第一位表示精品:000 | 001 = 001=>1 第二位表示新品:000 | 010 = 010=>2 第三位表示热品:000 | 100 = 100=>4
        if(isset($data['status'])){
            foreach($data['status'] as $v){
                $status = $status | $v;
            }
        }
        $data['status'] = $status;
        $data['add_time'] = time();//添加时间
        $data['update_time'] = time();//修改时间
        return parent::insertData($data);
    }
    //根据页码得到当前页中需要的数据
    public function getpageResult($page){
        //准备每页多少条数据
        $pageSize = 4;
        $start = ($page-1)*$pageSize;
        $rows = parent::getAll("1=1 limit {$start},{$pageSize}");
        foreach($rows as &$row){//&$row引用赋值
            $row['is_on_sale'] = $row['is_on_sale'] ? "yes" : "no";
            $row['is_bast'] = ($row['status'] & 1) ? "yes" : "no";
            $row['is_new'] = ($row['status'] & 2) ? "yes" : "no";
            $row['is_hot'] = ($row['status'] & 4) ? "yes" : "no";
        }
        $count = parent::getCount();//记录总条数
        //准备分页工具条的html
        $pageHtml = PageTool::show("index.php?p=Admin&c=Goods&a=index",$count,$page,$pageSize);
        return ['rows'=>$rows,'pageHtml'=>$pageHtml];
    }
    public function getByPkResult($id){
        $row = parent::getByPk($id);
        $row['is_on_sale'] = $row['is_on_sale'] ? "yes" : "no";
        $row['is_bast'] = ($row['status'] & 1) ? "yes" : "no";
        $row['is_new'] = ($row['status'] & 2) ? "yes" : "no";
        $row['is_hot'] = ($row['status'] & 4) ? "yes" : "no";
        return $row;
    }
    public function edit($data){
        //处理商品状态的数据
        $status = 0;//初始化商品的状态:0   二进制: 000
        //第一位表示精品:000 | 001 = 001=>1 第二位表示新品:000 | 010 = 010=>2 第三位表示热品:000 | 100 = 100=>4
        if(isset($data['status'])){
            foreach($data['status'] as $v){
                $status = $status | $v;
            }
        }
        $data['status'] = $status;
        $data['update_time'] = time();//修改时间
        parent::updateData($data);
    }
}
