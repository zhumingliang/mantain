<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/3
 * Time: 4:15 PM
 */

namespace app\api\service;


use app\api\model\AccessControlV;

class AccessService extends BaseService
{
    public function getList($page,$size,$time_begin, $time_end, $department, $username, $status, $access)
    {

        $list = AccessControlV::getList($page,$size,$time_begin, $time_end, $department, $username, $status, $access);
        return $list;
    }


    public function export($time_begin, $time_end, $department, $username, $status, $access)
    {

        $list = AccessControlV::export($time_begin, $time_end, $department, $username, $status, $access);
        $list=$this->prefixStatus($list);
        $header = array(
            '日期',
            '申请人',
            '部门',
            '职务',
            '人员类型',
            '开通功能',
            '工作截止时间（外来人员）',
            '状态',
        );
        $file_name = '门禁权限列表导出' . '-' . date('Y-m-d', time()) . '.csv';
        $this->put_csv($list, $header, $file_name);


    }

}