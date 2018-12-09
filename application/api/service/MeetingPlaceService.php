<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/5
 * Time: 10:54 AM
 */

namespace app\api\service;


use app\api\model\MeetingplaceReceptT;
use app\api\model\MeetingplaceT;
use app\api\model\MeetingplaceV;
use app\api\model\ReceptDetailT;
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
            $params['user_count'] = $this->getUserCount($params['users']);
            $params['money'] = $this->getMoney($params['detail']);
            $mp = MeetingplaceT::create($params);
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
            '申请人',
            '部门',
            '陪餐人数',
            '公函字号',
            '公函标题',
            '接待对象（单位,姓名,职务）',
            '接待明细（时间,项目,地点,费用）',
            '总费用',
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

        $res = (new MeetingplaceReceptT())->saveAll($list);
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
        $res = (new ReceptDetailT())->saveAll($list);
        return $res;
    }

}