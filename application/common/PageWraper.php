<?php
/**
 * Created by PhpStorm.
 * User: Gkh
 * Date: 2020/2/13
 * Time: 20:16
 */

namespace app\common;

class PageWraper
{
    public $data;
    public $pageCount;
    public $pageNum;
    public $pageSize;
    public $totalCount;
    public $offset;

    public static function init(int $count, int $size, int $page): PageWraper
    {
        $wraper = new PageWraper();
        $wraper->pageSize = $size = $size > 200 ? 200 : ($size < 1 ? 1 : $size);
        $wraper->totalCount = $count;
        if ($count) {
            $wraper->pageCount = $pageCount = intval(($count - 1) / $size) + 1;
            $wraper->pageNum = $page > $pageCount ? $pageCount : ($page < 1 ? 1 : $page);
            $wraper->offset = ($wraper->pageNum - 1) * $size;
        } else {
            $wraper->pageCount = 0;
            $wraper->pageNum = 0;
            $wraper->offset = 0;
        }
        $wraper->data = null;
        return $wraper;
    }
}