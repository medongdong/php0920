<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/16
 * Time: 22:23
 * 分页工具类
 */
class PageTool
{
    /**
     * @param $url 链接的url地址
     * @param $count 总条数
     * @param $page 当前页码
     * @param $pageSize 每页多少条
     * @return string 返回的分页工具条的html
     */
    public static function show($url,$count,$page,$pageSize){
        $totalPage = ceil($count/$pageSize);//总页数
        $pre_page = $page-1<1 ? 1 :$page-1 ;//上一页
        $next_page = $page+1> $totalPage ? $totalPage :$page + 1 ;//下一页
        $pageHtml = <<<PAGEHTML
<div id="turn-page">
    总计 <span id="totalRecords">{$count}</span>
    个记录分为 <span id="totalPages">{$totalPage}</span>
    页当前第 <span id="pageCurrent">{$page}</span>
    页，每页 <span id="pageSize">{$pageSize}</span> 条
            <span id="page-link">
                <a href="{$url}&page=1">第一页</a>
                <a href="{$url}&page={$pre_page}">上一页</a>
                <a href="{$url}&page={$next_page}">下一页</a>
                <a href="{$url}&page={$totalPage}">最末页</a>
            </span>
</div>
PAGEHTML;
        return $pageHtml;
    }
}