<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 9:19 AM
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class OfficialReceptV extends Model
{
    public static function getList($page, $size, $time_begin, $time_end, $department,
                                   $username, $status, $meal_type)
    {
        $time_end = addDay(1, $time_end);
        $list = self::whereBetweenTime('create_time', $time_begin, $time_end)
            ->where('state',CommonEnum::STATE_IS_OK)
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
            ->where(function ($query) use ($meal_type) {
                if ($meal_type && $meal_type != "全部") {
                    $query->where('meal_type', '=', $meal_type);
                }
            })
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])
            ->toArray();
        return $list;


    }

    public static function export($time_begin, $time_end, $department,
                                         $username, $status, $meal_type)
    {
        $time_end = addDay(1, $time_end);
        $list = self::whereBetweenTime('create_time', $time_begin, $time_end)
            ->where('state',CommonEnum::STATE_IS_OK)
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
            ->where(function ($query) use ($meal_type) {
                if ($meal_type && $meal_type != "全部") {
                    $query->where('meal_type', '=', $meal_type);
                }
            })
            ->field('create_time,username,department,phone,product,content,
            member,accompany,table_number,meal_space,meal_type,meals,meal_date,status')
            ->order('create_time desc')
            ->select()->toArray();
        return $list;


    }
}