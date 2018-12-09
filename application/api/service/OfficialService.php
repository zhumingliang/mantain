<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 10:15 AM
 */

namespace app\api\service;


use app\api\model\OfficialMealT;
use app\api\model\OfficialReceptT;
use app\api\model\OfficialReceptV;
use app\lib\exception\OperationException;
use think\Db;
use think\Exception;

class OfficialService extends BaseService
{
    public function save($params)
    {
        Db::startTrans();
        try {
            //新增基本信息
            $mp = OfficialReceptT::create($params);
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
                'meal_type' => $user[0],
                'cuisine' => $user[1],
                'mr_id' => $mr_id
            ];

        }

        $res = (new OfficialMealT())->saveAll($list);
        return $res;
    }


    public function getList($page, $size, $time_begin, $time_end, $department, $username, $status, $meal_type)
    {
        $list = OfficialReceptV::getList($page, $size, $time_begin, $time_end, $department, $username, $status, $meal_type);
        return $list;
    }

    public function export($time_begin, $time_end, $department, $username, $status, $meal_type)
    {
        $list = OfficialReceptV::export($time_begin, $time_end, $department, $username, $status, $meal_type);
        $list = $this->prefixStatus($list);
        $header = array(
            '日期',
            '申请人',
            '部门',
            '联系人电话',
            '项目',
            '业务内容',
            '人数',
            '桌数',
            '就餐地点',
            '餐类',
            '围餐信息（餐类,菜式）',
            '就餐时间',
            '状态',
        );
        $file_name = '预约申请—公务接待-围餐预定导出' . '-' . date('Y-m-d', time()) . '.csv';
        $this->put_csv($list, $header, $file_name);


    }

}