<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/3
 * Time: 10:49 AM
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class MeetingT extends Model
{

    public static function getMeetingList($time_begin, $time_end, $address, $theme, $page, $size, $host)
    {
        $time_end = addDay(1, $time_end);
        $list = self::whereBetweenTime('create_time', $time_begin, $time_end)
            ->where('state', CommonEnum::STATE_IS_OK)
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
            ->where(function ($query) use ($host) {
                if ($host && $host != "全部") {
                    $query->where('host', 'like', $host);
                }
            })
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page])
            ->toArray();
        return $list;
    }


    public static function exportMeeting($time_begin, $time_end, $address, $theme)
    {
        $time_end = addDay(1, $time_end);
        $list = self::whereBetweenTime('create_time', $time_begin, $time_end)
            ->where('state', CommonEnum::STATE_IS_OK)
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
            ->field('create_time,theme,outline,address,time_begin,time_end,
            meeting_begin,host,push,remark')
            ->order('create_time desc')
            ->select()
            ->toArray();
        return $list;
    }

    public static function getMeetingToPush()
    {
        $time_end = addTime(15, date('Y-m-d H:i:s'), 'minute');
        $time_begin = date('Y-m-d H:i:s');
        $list = self::whereBetweenTime('meeting_begin', $time_begin, $time_end)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->where('pushed', 1)
            ->order('meeting_begin desc')
            ->select();
        return $list;
    }


}