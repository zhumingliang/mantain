<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/24
 * Time: 11:28 AM
 */

namespace app\api\service;


use app\api\model\HotelT;
use app\api\model\HotelV;
use app\lib\exception\FlowException;
use app\lib\exception\OperationException;
use think\Db;
use think\Exception;

class HotelService extends BaseService
{
    public function save($params)
    {
        Db::startTrans();
        try {
            //新增基本信息
            $mr = HotelT::create($params);
            if (!$mr) {
                throw new OperationException(
                    ['code' => 401,
                        'msg' => '新增基本信息失败',
                        'errorCode' => 40001
                    ]);
            }
            //启动工作流
            $check_res = (new FlowService())->saveCheck($mr->id, 'hotel_t');
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

    public function getList($page, $size, $time_begin, $time_end, $department, $username, $status)
    {

        $list = HotelV::getList($page, $size, $time_begin, $time_end, $department, $username, $status);
        $list=(new FlowService())->checkListStatus($list,'hotel_t');
        return $list;
    }


    public function export($time_begin, $time_end, $department, $username, $status)
    {
        $list = HotelV::export($time_begin, $time_end, $department, $username, $status);
        $list = $this->prefixStatus($list);
        $header = array(
            '日期',
            '申请人',
            '部门',
            '公务时间',
            '来访单位',
            '拟入住酒店',
            '男',
            '女',
            '单人房',
            '双人房',
            '人员名单',
            '状态',
        );
        $file_name = '预约申请—酒店预定—导出' . '-' . date('Y-m-d', time()) . '.csv';
        $this->put_csv($list, $header, $file_name);

    }


}