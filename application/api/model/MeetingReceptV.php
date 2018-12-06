<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 11:06 PM
 */

namespace app\api\model;


use think\Model;

class MeetingReceptV extends Model
{
    public static function getList($page, $size, $time_begin, $time_end, $department,
                                   $username, $status)
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
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])
            ->toArray();
        return $list;


    }

}