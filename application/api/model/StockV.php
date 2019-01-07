<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/28
 * Time: 12:43 AM
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class StockV extends Model
{
    public static function getList($category, $order_number, $category_id, $page, $size)
    {
        $list = self::where('state', CommonEnum::STATE_IS_OK)
            ->where(function ($query) use ($category) {
                if ($category && $category != "") {
                    $query->where('category_name', '=', $category);
                }
            })
            ->where(function ($query) use ($order_number) {
                if ($order_number && $order_number != "") {
                    $query->where('order_number', '=', $order_number);
                }
            })
            ->where(function ($query) use ($category_id) {
                if ($category_id && $category_id != "") {
                    $query->where('category_id', '=', $category_id);
                }
            })
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])
            ->toArray();
        return $list;


    }


    public static function exportStock($category, $order_number, $category_id)
    {
        $list = self::where('state', CommonEnum::STATE_IS_OK)
            ->where(function ($query) use ($category) {
                if ($category && $category != "") {
                    $query->where('category_name', '=', $category);
                }
            })
            ->where(function ($query) use ($order_number) {
                if ($order_number && $order_number != "") {
                    $query->where('order_number', '=', $order_number);
                }
            })
            ->where(function ($query) use ($category_id) {
                if ($category_id && $category_id != "") {
                    $query->where('category_id', '=', $category_id);
                }
            })
            ->field('sku_name,category_name,stock,all_count,price,stock_date,max,min,admin_name')
            ->order('create_time desc')
            ->select();
        return $list;


    }


}