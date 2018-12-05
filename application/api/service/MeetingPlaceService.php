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
use app\api\model\ReceptDetailT;
use app\lib\exception\OperationException;
use think\Db;
use think\Exception;

class MeetingPlaceService
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

    private function saveUsers($mp_id, $users)
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


    private function saveDetail($mp_id, $detail)
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