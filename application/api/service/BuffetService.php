<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/24
 * Time: 9:29 AM
 */

namespace app\api\service;


use app\api\model\BuffetMealT;
use app\api\model\BuffetT;
use app\api\model\BuffetV;
use app\lib\exception\FlowException;
use app\lib\exception\OperationException;
use think\Db;
use think\Exception;

class BuffetService extends BaseService
{
    public function save($params)
    {
        Db::startTrans();
        try {
            //新增基本信息
            $mr = BuffetT::create($params);
            if (!$mr) {
                throw new OperationException(
                    ['code' => 401,
                        'msg' => '新增基本信息失败',
                        'errorCode' => 40001
                    ]);
            }

            //新增订餐信息
            $meals = $params['meals'];
            if (strlen($meals)) {
                $users_res = $this->saveMeals($mr->id, $meals);
                if (!$users_res) {
                    Db::rollback();
                    throw new OperationException(
                        ['code' => 401,
                            'msg' => '订餐信息失败',
                            'errorCode' => 40001
                        ]);

                }
            }

            //启动工作流
            $check_res = (new FlowService())->saveCheck($mr->id, 'buffet_t');
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
                'money' => $user[3],
                'mr_id' => $mr_id
            ];

        }

        $res = (new BuffetMealT())->saveAll($list);
        return $res;
    }

    public function getList($page, $size, $time_begin, $time_end, $department, $username, $status)
    {

        $list = BuffetV::getList($page, $size, $time_begin, $time_end, $department, $username, $status);
        return $list;
    }


    public function export($time_begin, $time_end, $department, $username, $status)
    {
        $list = BuffetV::export($time_begin, $time_end, $department, $username, $status);
        $list = $this->prefixStatus($list);
        $header = array(
            '日期',
            '申请人',
            '部门',
            '公务时间',
            '来访单位',
            '公务活动项目',
            '订餐信息(就餐日期,餐次,用餐人数，费用)',
            '状态',
        );
        $file_name = '预约申请—自助餐预定—导出' . '-' . date('Y-m-d', time()) . '.csv';
        $this->put_csv($list, $header, $file_name);

    }


}