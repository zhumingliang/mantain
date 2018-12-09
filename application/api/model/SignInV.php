<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/10
 * Time: 2:04 AM
 */

namespace app\api\model;


use think\Model;

class SignInV extends Model
{
    public function getCreateTimeAttr($value)
    {
        return date("Y-m-d", strtotime($value));
    }

    public static function getList($time_begin, $time_end,
                                   $department, $username,
                                   $address, $theme, $page, $size)
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
            ->where(function ($query) use ($address) {
                if ($address && $address != "全部") {
                    $query->where('address', '=', $address);
                }
            })
            ->where(function ($query) use ($theme) {
                if ($theme) {
                    $query->where('theme', 'like', $theme);
                }
            })
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])
            ->toArray();
        return $list;

    }

    public static function export($time_begin, $time_end,
                                  $department, $username,
                                  $address, $theme)
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
            ->where(function ($query) use ($address) {
                if ($address && $address != "全部") {
                    $query->where('status', '=', $address);
                }
            })
            ->where(function ($query) use ($theme) {
                if ($theme) {
                    $query->where('theme', 'like', $theme);
                }
            })
            ->order('create_time desc')
            ->select()
            ->toArray();
        return $list;

    }


}