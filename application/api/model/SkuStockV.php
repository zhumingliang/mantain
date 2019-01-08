<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/2
 * Time: 1:55 AM
 */

namespace app\api\model;


use think\Model;

class SkuStockV extends Model
{
    public static function getList($c_id, $name)
    {
        $list = self::where('c_id', $c_id)
            ->where(function ($query) use ($name) {
                if ($name && $name != "") {
                    $query->where('name', 'like', '%' . $name . '%');
                }
            })
            ->field('id,name,category,sum(count) as stock')
            ->group('id')
            ->select();
        return $list;

    }


    public static function getListWithCategory()
    {
        $list = self::field('id,name,c_id,category,sum(count) as stock')
            ->group('id,c_id')
            ->select();
        return $list;


    }


    public static function getSkuStock($id)
    {
        $count = self::where('id', $id)
            ->sum('count');

        return $count;

    }
}