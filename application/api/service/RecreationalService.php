<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/5
 * Time: 10:17 AM
 */

namespace app\api\service;


use app\api\model\RecreationalV;
use app\api\model\SpaceRecreationalT;
use app\lib\exception\FlowException;
use app\lib\exception\OperationException;
use think\Db;
use think\Exception;

class RecreationalService extends BaseService
{

    public function save($params)
    {
        Db::startTrans();
        try {
            //新增基本信息
            $mp = SpaceRecreationalT::create($params);
            if (!$mp) {
                throw new OperationException(
                    ['code' => 401,
                        'msg' => '新增基本信息失败',
                        'errorCode' => 40001
                    ]);

            }

            //启动工作流
            $check_res = (new FlowService())->saveCheck($mp->id, 'space_recreational_t');
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

    public function getList($page,$size,$time_begin, $time_end, $department, $username, $status, $space)
    {
        $list = RecreationalV::getList($page,$size,$time_begin, $time_end, $department, $username, $status, $space);
        $list=(new FlowService())->checkListStatus($list,'space_recreational_t');
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