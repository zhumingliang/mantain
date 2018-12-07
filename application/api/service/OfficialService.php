<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 10:15 AM
 */

namespace app\api\service;


use app\api\model\OfficialReceptV;

class OfficialService extends BaseService
{
    public function getList($page,$size,$time_begin, $time_end, $department, $username, $status, $meal,$meal_type)
    {
        $list = OfficialReceptV::getList($page,$size,$time_begin, $time_end, $department, $username, $status, $meal,$meal_type);
        return $list;
    }

    public function export($time_begin, $time_end, $department, $username, $status, $meal,$meal_type)
    {
        $list = OfficialReceptV::export($time_begin, $time_end, $department, $username, $status, $meal,$meal_type);
        $list=$this->prefixStatus($list);
        $header = array(
            '日期',
            '申请人',
            '部门',
            '联系人电话',
            '菜式',
            '人数',
            '桌数',
            '就餐地点',
            '项目',
            '业务内容',
            '餐次',
            '餐类',
            '开始时间',
            '结束时间',
            '状态',
        );
        $file_name = '预约申请—公务接待-围餐预定导出' . '-' . date('Y-m-d', time()) . '.csv';
        $this->put_csv($list, $header, $file_name);


    }

}