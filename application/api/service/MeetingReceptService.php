<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 11:07 PM
 */

namespace app\api\service;


use app\api\model\MeetingReceptMealT;
use app\api\model\MeetingReceptT;
use app\api\model\MeetingReceptV;
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
            $mp = MeetingReceptT::create($params);
            if (!$mp) {
                throw new OperationException(
                    ['code' => 401,
                        'msg' => '新增基本信息失败',
                        'errorCode' => 40001
                    ]);

            }
            //新增接待对象
            $meals = $params['meals'];
            if (strlen($meals)) {
                $meals_res = $this->saveMeals($mp->id, $meals);
                if (!$meals_res) {
                    Db::rollback();
                    throw new OperationException(
                        ['code' => 401,
                            'msg' => '新增接待对象失败',
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


    private
    function saveMeals($mr_id, $meals)
    {
        $users_arr = explode('A', $meals);
        $list = [];
        foreach ($users_arr as $k => $v) {
            $user = explode(',', $v);
            $list[$k] = [
                'meal_date' => $user[0],
                'meal_type' => $user[1],
                'count' => $user[2],
                'address' => $user[3],
                'money' => $user[4],
                'mr_id' => $mr_id
            ];

        }

        $res = (new MeetingReceptMealT())->saveAll($list);
        return $res;
    }

    public function getList($time_begin, $time_end, $department, $username, $status, $page, $size)
    {
        $list = MeetingReceptV::getList($page, $size, $time_begin, $time_end, $department, $username, $status);
        return $list;
    }


    public function export($time_begin, $time_end, $department, $username, $status)
    {
        $list = MeetingReceptV::export($time_begin, $time_end, $department, $username, $status);
        $list = $this->prefixStatus($list);
        $header = array(
            '日期',
            '公务时间',
            '申请人',
            '部门',
            '公务活动项目',
            '来访单位',
            '领队人',
            '职务',
            '级别',
            '厅级人数',
            '处级人数',
            '处级以下人数',
            '男',
            '女',
            '使用会场',
            '会议时间',
            '会议人数',
            '拟住宿酒店',
            '用餐信息（用餐日期,餐次,用餐人数,就餐地点,费用）',
            '拟陪同人员名单',
            '状态',
        );
        $file_name = '预约申请—会议、接待—导出' . '-' . date('Y-m-d', time()) . '.csv';
        $this->put_csv($list, $header, $file_name);

    }


}