<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/1
 * Time: 4:16 PM
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class SkuApplyV extends Model
{
    public static function getList($page, $size, $time_begin, $time_end, $department, $username, $status,$type, $sku, $category)
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
            ->where(function ($query) use ($username) {
                if ($username && $username != "全部") {
                    $query->where('username', '=', $username);
                }
            })
            ->where(function ($query) use ($type) {
                if ($type && $type != "全部") {
                    $query->where('type', '=', $type);
                }
            })
            ->where(function ($query) use ($status) {
                if ($status != 3) {
                    $query->where('status', '=', $status);
                }
            })
            ->where(function ($query) use ($sku) {
                if ($sku && $sku != "") {
                    $query->where('sku', 'like', "%" . $sku . "%");
                }
            })
            ->where(function ($query) use ($category) {
                if ($category && $category != "") {
                    $query->where('category', 'like', "%" . $category . "%");
                }
            })
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])
            ->toArray();
        return $list;


    }


    public static function export($time_begin, $time_end, $department, $username, $status,$type, $sku, $category)
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
            ->where(function ($query) use ($type) {
                if ($type && $type != "全部") {
                    $query->where('type', '=', $type);
                }
            })
            ->where(function ($query) use ($status) {
                if ($status != 3) {
                    $query->where('status', '=', $status);
                }
            })
            ->where(function ($query) use ($sku) {
                if ($sku && $sku != "") {
                    $query->where('sku', 'like', "%" . $sku . "%");
                }
            })
            ->where(function ($query) use ($category) {
                if ($category && $category != "") {
                    $query->where('category', 'like', "%" . $category . "%");
                }
            })
            ->field('create_time,username,department,phone,sku,category,format,count,IF(type="borrow_t","借用","领用") as type
           ,time_begin,time_end,actual_time,status')
            ->order('create_time desc')
            ->select()
            ->toArray();
        return $list;


    }


}