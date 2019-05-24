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

    public function borrowSku()
    {
        return $this->hasMany('BorrowSkuT',
            'b_id', 'id');
    }

    public function useSku()
    {
        return $this->hasMany('UseSkuT',
            'c_id', 'id');
    }

    public static function getList($page, $size, $time_begin, $time_end, $department, $username, $status, $type, $sku, $category)
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
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])
            ->toArray();
        return $list;


    }


    public static function export($time_begin, $time_end, $department, $username, $status, $type, $sku, $category)
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
            ->field('id,create_time,username,department,phone,"" as sku,type as sub_type,IF(type="borrow_t","借用","领用") as type
           ,time_begin,time_end,actual_time,status')
            ->order('create_time desc')
            ->select()
            ->toArray();
        return $list;


    }


}