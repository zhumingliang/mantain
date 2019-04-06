<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 11:07 PM
 */

namespace app\api\service;


use app\api\model\MeetingReceptDetailT;
use app\api\model\BuffetMealT;
use app\api\model\MeetingReceptT;
use app\api\model\MeetingReceptUserT;
use app\api\model\MeetingReceptV;
use app\lib\exception\FlowException;
use app\lib\exception\OperationException;
use think\Db;
use think\Exception;

class MeetingReceptService extends BaseService
{

    public function save($params)
    {
        Db::startTrans();
        try {
            //新增基本信息
            $params['money'] = $this->getMoney($params['detail']);
            $mp = MeetingReceptT::create($params);
            if (!$mp) {
                throw new OperationException(
                    ['code' => 401,
                        'msg' => '新增基本信息失败',
                        'errorCode' => 40001
                    ]);
            }

            //新增接待对象
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
            }


            //启动工作流
            $check_res = (new FlowService())->saveCheck($mp->id, 'meeting_recept_t');
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

    private
    function saveUsers($mp_id, $users)
    {
        $users_arr = explode('A', $users);
        $list = [];
        foreach ($users_arr as $k => $v) {
            $user = explode(',', $v);
            $list[$k] = [
                'unit' => $user[0],
                'name' => $user[1],
                'post' => $user[2],
                'mp_id' => $mp_id
            ];

        }

        $res = (new MeetingReceptUserT())->saveAll($list);
        return $res;
    }

    private
    function saveDetail($mp_id, $detail)
    {
        $detail_arr = explode('A', $detail);
        $list = [];
        foreach ($detail_arr as $k => $v) {
            $d = explode(',', $v);
            $list[$k] = [
                'recept_time' => $d[0],
                'content' => $d[1],
                'address' => $d[2],
                'money' => $d[3],
                'mp_id' => $mp_id
            ];

        }
        $res = (new MeetingReceptDetailT())->saveAll($list);
        return $res;
    }


    private function getMoney($detail)
    {
        if (!strlen($detail)) {
            return 0;

        }
        $detail_arr = explode('A', $detail);
        $money = 0;
        foreach ($detail_arr as $k => $v) {
            $d = explode(',', $v);
            $money += $d[3];
        }
        return $money;
    }


    public function getList($time_begin, $time_end, $department, $username, $status, $page, $size)
    {
        $list = MeetingReceptV::getList($page, $size, $time_begin, $time_end, $department, $username, $status);
        $list=(new FlowService())->checkListStatus($list,'meeting_recept_t');
        return $list;
    }


    public function export($time_begin, $time_end, $department, $username, $status)
    {
        $list = MeetingReceptV::export($time_begin, $time_end, $department, $username, $status);
        $list = $this->prefixStatus($list);
        $header = array(
            '日期',
            '申请人',
            '部门',
            '公函字号',
            '公函标题',
            '陪餐人数',
            '来访单位',
            '领队人',
            '职务',
            '级别',
            '厅级人数',
            '处级人数',
            '处级以下人数',
            '会议人数',
            '陪同人员名单',
            '接待对象(单位,姓名,职务)',
            '接待明细(时间,项目,地点,费用)',
            '费用合计',
            '状态',
        );
        $file_name = '公务接待-围餐预定列表—导出' . '-' . date('Y-m-d', time()) . '.csv';
        $this->put_csv($list, $header, $file_name);

    }


}