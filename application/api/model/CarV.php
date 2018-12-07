<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 10:26 AM
 */

namespace app\api\model;


use think\Model;

class CarV extends Model
{

    public static function getList($page, $size, $time_begin, $time_end, $department,
                                   $username, $status, $reason)
    {
        $time_end = addDay(1, $time_end);
        $list = self::whereBetweenTime('create_time', $time_begin, $time_end)
            ->where(function ($query) use ($department) {
                if ($department && $department != "全部") {
                    $query->where('department', '=', $department);
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
            ->where(function ($query) use ($reason) {
                if ($reason && $reason != "全部") {
                    $query->where('meal', '=', $reason);
                }
            })
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])
            ->toArray();
        return $list;


    }


    public static function getListForReport($time_begin, $time_end, $department,
                                            $username, $status, $reason)
    {
        $time_end = addDay(1, $time_end);
        $list = self::whereBetweenTime('create_time', $time_begin, $time_end)
            ->where(function ($query) use ($department) {
                if ($department && $department != "全部") {
                    $query->where('department', '=', $department);
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
            ->where(function ($query) use ($reason) {
                if ($reason && $reason != "全部") {
                    $query->where('meal', '=', $reason);
                }
            })
            ->order('create_time desc')
            ->field('create_time,username,department,apply_date,address,count,reason,members,status')
            ->select()->toArray();
        return $list;


    }

}