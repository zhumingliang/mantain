<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/3
 * Time: 4:31 PM
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class AccessControlV extends Model
{

    public static function getList($page, $size, $time_begin, $time_end, $department,
                                   $username, $status, $access)
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
            ->where(function ($query) use ($access) {
                if ($access && $access != "全部") {
                    $query->where('FIND_IN_SET("' . $access . '", access)');
                }
            })
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])
            ->toArray();
        return $list;


    }


    public static function export($time_begin, $time_end, $department,
                                  $username, $status, $access)
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
            ->where(function ($query) use ($access) {
                if ($access && $access != "全部") {
                    $query->where('FIND_IN_SET("' . $access . '", access)');
                }
            })
            ->field('create_time,username,department,role_name,members,user_type,access,deadline,status')
            ->order('create_time desc')
            ->select()
            ->toArray();
        return $list;
    }

    public static function infoToReport($id)
    {
        $access = self::where('id', $id)
            ->field('id,create_time,username,access,user_type,members,deadline,status,apply_unit,borrow_unit')
            ->find();
        return $access;

    }

}