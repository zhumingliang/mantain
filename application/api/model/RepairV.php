<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/3
 * Time: 4:12 PM
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class RepairV extends Model
{
    public static function getList($page, $size, $time_begin, $time_end, $department,
                                   $username, $status)
    {
        $time_end = addDay(1, $time_end);
        $list = self::whereBetweenTime('create_time', $time_begin, $time_end)
            ->where('state', CommonEnum::STATE_IS_OK)
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
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])
            ->toArray();
        return $list;


    }

    public static function export($time_begin, $time_end, $department,
                                  $username, $status)
    {
        $time_end = addDay(1, $time_end);
        $list = self::whereBetweenTime('create_time', $time_begin, $time_end)
            ->where('state', CommonEnum::STATE_IS_OK)
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
            ->field('create_time,username,department,name,address,remark,
            feedback,status')
            ->order('create_time desc')
            ->select()->toArray();
        return $list;


    }
}