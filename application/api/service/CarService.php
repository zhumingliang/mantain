<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 10:27 AM
 */

namespace app\api\service;


use app\api\model\CarT;
use app\api\model\CarV;
use app\lib\exception\FlowException;
use app\lib\exception\OperationException;
use think\Db;
use think\Exception;

class CarService extends BaseService
{
    public function save($params)
    {
        Db::startTrans();
        try {
            $car = CarT::create($params);
            if (!$car->id) {
                throw new OperationException();
            }
            //启动工作流
            $check_res = (new FlowService())->saveCheck($car->id, 'car_t');
            if (!$check_res == 1) {
                Db::rollback();
                throw new FlowException();
            }
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }

    }

    public function getList($page, $size, $time_begin, $time_end, $department, $username, $status, $reason)
    {
        $list = CarV::getList($page, $size, $time_begin, $time_end, $department, $username, $status, $reason);
        $list=(new FlowService())->checkListStatus($list,'car_t');
        return $list;

    }

    public function export($time_begin, $time_end, $department, $username, $status, $reason)
    {
        $list = CarV::getListForReport($time_begin, $time_end, $department, $username, $status, $reason);
        $list = $this->prefixStatus($list);
        $header = array(
            '日期',
            '申请人',
            '部门',
            '用车时间',
            '目的地',
            '人数',
            '用车原因',
            '出行人员',
            '状态',
        );
        $file_name = '用车申请导出' . '-' . date('Y-m-d', time()) . '.csv';
        $this->put_csv($list, $header, $file_name);

    }
}