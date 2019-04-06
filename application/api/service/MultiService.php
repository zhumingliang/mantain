<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 1:35 AM
 */

namespace app\api\service;


use app\api\model\SpaceMultiT;
use app\api\model\SpaceMultiV;
use app\lib\exception\FlowException;
use app\lib\exception\OperationException;
use think\Db;
use think\Exception;

class MultiService extends BaseService
{
    public function save($params)
    {
        Db::startTrans();
        try {
            $mulit = SpaceMultiT::create($params);
            if (!$mulit->id) {
                throw new OperationException();
            }

            //启动工作流
            $check_res = (new FlowService())->saveCheck($mulit->id, 'space_multi_t');
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


    public function getList($time_begin, $time_end, $department, $username, $status, $page, $size, $space)
    {
        $list = SpaceMultiV::getList($page, $size, $time_begin, $time_end, $department, $username, $status, $space);
        $list=(new FlowService())->checkListStatus($list,'space_multi_t');
        return $list;


    }

    public function export($time_begin, $time_end, $department, $username, $status, $space)
    {
        $list = SpaceMultiV::export($time_begin, $time_end, $department, $username, $status, $space);
        $list = $this->prefixStatus($list);
        $header = array(
            '日期',
            '姓名',
            '使用单位',
            '使用时间',
            '申请使用事由',
            '场地名称',
            '状态',
        );
        $file_name = '功能室申请导出' . '-' . date('Y-m-d', time()) . '.csv';
        $this->put_csv($list, $header, $file_name);

    }
}