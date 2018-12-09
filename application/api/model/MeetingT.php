<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/3
 * Time: 10:49 AM
 */

namespace app\api\model;


use think\Model;

class MeetingT extends Model
{
    public static function getMeetingList($page, $size, $time_begin, $time_end, $address, $theme)
    {
        $time_end = addDay(1, $time_end);
        $list = self::whereBetweenTime('create_time', $time_begin, $time_end)
            ->where(function ($query) use ($address) {
                if ($address && $address != "全部") {
                    $query->where('address', '=', $address);
                }
            })
            ->where(function ($query) use ($theme) {
                if ($theme && $theme != "全部") {
                    $query->where('theme', 'like', $theme);
                }
            })
            ->hidden(['meals'])
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])
            ->toArray();
        return $list;


    }

}