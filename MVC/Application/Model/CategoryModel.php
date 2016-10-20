<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/1
 * Time: 18:30
 */
class CategoryModel extends Model
{
    /**
     * @param int $parent_id 根据父分类的id查询所有的子分类数据
     * @return array
     */
    public function getList($parent_id = 0)
    {
        //1.获取说有商品分类数据
        $rows = $this->getAll();
        //2.对数据进行排序
        return $this->getChildren($rows,$parent_id, 0);
    }

    private function getChildren(&$rows, $parent_id = 0, $deep = 0)
    {
        static $Chindrens = [];//使用一个静态局部变量存放所有儿子的数组
        foreach ($rows as $Chind) {
            if ($Chind['parent_id'] == $parent_id) {
                $Chind['name_text'] = str_repeat("&nbsp;&nbsp;",$deep*2).$Chind['name'];
                //str_repeat("字符串",重复次数) 重复一个字符串
                $Chindrens[] = $Chind;
                $this->getChildren($rows, $Chind['id'], $deep + 1);
            }
        }
        return $Chindrens;
    }

    /**
     *父类上的删除方法不满足需求，需要重写
     */
    public function deleteByPk($id)
    {
        //a.删除的节点下面不能存在子节点
        /**
         * 子节点的数量大于0，表明有子节点，不能删除
         */
        $count = $this->getCount("parent_id={$id}");
        if($count>0){
            $this->error = "当前节点下面存在子节点，不能删除！";
            return false;
        }
        parent::deleteByPk($id);
    }

    /**
     *2, 优化商品分类的添加
     */
    public function insertData($data)
    {
        //a.分类名称不能为空
        if(empty($data['name'])){
            $this->error = "商品分类不能为空";
            return false;
        }
        //b.同级分类的分类名称不能重名
        /**
         * 如果统计$data['name']的数量，如果大于0,不满足要求
         */
        $count = $this->getCount("parent_id={$data['parent_id']} and name='{$data['name']}'");
        if ($count > 0) {
            $this->error = '同级分类下已经存在该分类';
            return false;
        }
        return parent::insertData($data);
    }

    /**
     * 根据数据:
     */
    public function update($new_data)
    {
        //a.分类名称不能为空
        if(empty($new_data['name'])){
            $this->error = "商品分类不能为空";
            return false;
        }
        //b.修改后不能与同级分类的其他分类名称重复
        $count = $this->getCount("parent_id={$new_data['parent_id']} and name='{$new_data['name']}' and id<>{$new_data['id']}");//<>为mysql语句中的不等于
        if($count>0){
            $this->error = '同级分类下已经存在该分类';
            return false;
        }
        //c.不能修改到自己的子孙分类下面以及自己下面
        //得到当前分类的所有的子孙分类
        $child = $this->getList($new_data['id']);
        //子孙的id
        $ids = array_column($child,"id");//array_column()返回数组中指定的列
        //自己的id
        $ids[] = $new_data['id'];
        if(in_array($new_data['parent_id'],$ids)){//in_array()检查数组中是否存在某个值
            $this->error = "不能做自己和子分类的子分类";
            return false;
        }
        parent::updateData($new_data);
    }

}