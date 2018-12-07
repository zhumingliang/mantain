<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/5
 * Time: 10:17 AM
 */

namespace app\api\service;


use app\api\model\RecreationalV;

class RecreationalService extends BaseService
{
    public function getList($page,$size,$time_begin, $time_end, $department, $username, $status, $space)
    {
        $list = RecreationalV::getList($page,$size,$time_begin, $time_end, $department, $username, $status, $space);
        return $list;
    }


    public function export($time_begin, $time_end, $department, $username, $status, $space)
    {
        $list = RecreationalV::export($time_begin, $time_end, $department, $username, $status, $space);
        $list=$this->prefixStatus($list);
        $header = array(
            '日期',
            '申请人',
            '使用单位',
            '使用时间',
            '使用人数',
            '场地名称',
            '状态',
        );
        $file_name = '文体活动场地导出' . '-' . date('Y-m-d', time()) . '.csv';
        $this->put_csv($list, $header, $file_name);

    }

}