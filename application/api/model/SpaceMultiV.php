<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 1:25 AM
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class SpaceMultiV extends Model
{
    public static function getList($page, $size, $time_begin, $time_end, $department,
                                   $username, $status, $space)
    {
        $time_end = addDay(1, $time_end);
        $list = self::whereBetweenTime('create_time', $time_begin, $time_end)
            ->where('state',CommonEnum::STATE_IS_OK)
            ->where(function ($query) use ($department) {
                if ($department && $department != "全部") {
                    $query->where('department', 'in', $department);
                }
            })
            ->where(function ($query) use ($username) {
                if ($username && $username != "全部") {
                    $query->where('username', '=', $username);
                }
            })
            ->where(function ($query) use ($status) {
                if ($status != 3) {
                    $query->where('status', '=', $status);
                }
            })
            ->where(function ($query) use ($space) {
                if ($space && $space != "全部") {
                    $query->where('FIND_IN_SET("' . $space . '", space)');
                }
            })
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])
            ->toArray();
        return $list;


    }


    /**
     * @param $time_begin
     * @param $time_end
     * @param $department
     * @param $username
     * @param $status
     * @param $space
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function export($time_begin, $time_end, $department,
                                   $username, $status, $space)
    {
        $time_end = addDay(1, $time_end);
        $list = self::whereBetweenTime('create_time', $time_begin, $time_end)
            ->where('state',CommonEnum::STATE_IS_OK)
            ->where(function ($query) use ($department) {
                if ($department && $department != "全部") {
                    $query->where('department', 'in', $department);
                }
            })
            ->where(function ($query) use ($username) {
                if ($username && $username != "全部") {
                    $query->where('username', '=', $username);
                }
            })
            ->where(function ($query) use ($status) {
                if ($status != 3) {
                    $query->where('status', '=', $status);
                }
            })
            ->where(function ($query) use ($space) {
                if ($space && $space != "全部") {
                    $query->where('FIND_IN_SET("' . $space . '", space)');
                }
            })
            ->field('create_time,username,unit,CONCAT_WS("-",time_begin,time_end) as use_time,reason,space,status')
            ->order('create_time desc')
            ->select()
            ->toArray();
        return $list;


    }

}