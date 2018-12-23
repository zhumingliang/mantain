<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/5
 * Time: 10:54 AM
 */

namespace app\api\service;


use app\api\model\MeetingReceptUserT;
use app\api\model\MeetingplaceT;
use app\api\model\MeetingplaceV;
use app\api\model\MeetingReceptDetailT;
use app\lib\exception\FlowException;
use app\lib\exception\OperationException;
use think\Db;
use think\Exception;

class MeetingPlaceService extends BaseService
{
    /**
     * @param $params
     * @throws Exception
     */
    public function save($params)
    {
        Db::startTrans();
        try {
            //新增基本信息
            $mp = MeetingplaceT::create($params);
            if (!$mp) {
                throw new OperationException();

            }
            /* //新增接待对象
             $users = $params['users'];
             if (strlen($users)) {
                 $users_res = $this->saveUsers($mp->id, $users);
                 if (!$users_res) {
                     Db::rollback();
                     throw new OperationException(
                         ['code' => 401,
                             'msg' => '新增接待对象失败',
                             'errorCode' => 40001
                         ]);

                 }
             }
             //新增接待明细
             $detail = $params['detail'];
             if (strlen($detail)) {
                 $detail_res = $this->saveDetail($mp->id, $detail);
                 if (!$detail_res) {
                     Db::rollback();
                     throw new OperationException(
                         ['code' => 401,
                             'msg' => '新增接待明细失败',
                             'errorCode' => 40001
                         ]);

                 }
             }*/

            //启动工作流
            $check_res = (new FlowService())->saveCheck($mp->id, 'meetingplace_t');
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

    public function getList($time_begin, $time_end, $department, $username, $status, $page, $size)
    {
        $list = MeetingplaceV::getList($page, $size, $time_begin, $time_end, $department, $username, $status);
        return $list;


    }

    public function export($time_begin, $time_end, $department, $username, $status)
    {
        $list = MeetingplaceV::export($time_begin, $time_end, $department, $username, $status);
        $list = $this->prefixStatus($list);
        $header = array(
            '日期',
            '姓名',
            '使用单位',
            '使用时间',
            '申请使用事由',
            '场地名称',
            '场地用途',
            '状态'
        );
        $file_name = '预约申请—教育培训—会场预订—导出' . '-' . date('Y-m-d', time()) . '.csv';
        $this->put_csv($list, $header, $file_name);

    }

    private function getUserCount($users)
    {
        $users_arr = explode('A', $users);
        return count($users_arr);

    }

    private function getMoney($detail)
    {
        $detail_arr = explode('A', $detail);
        $money = 0;
        foreach ($detail_arr as $k => $v) {
            $d = explode(',', $v);
            $money += $d[3];

        }
        return $money;
    }


}