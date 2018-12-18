<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/18
 * Time: 8:42 AM
 */

namespace app\api\service;


use app\api\model\MeetingRoomV;

class MeetingRoomService
{
    /**
     * 检查会议室是否可以使用
     * @param $name
     * @param $time_begin
     * @param $time_end
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkRoom($name, $time_begin, $time_end)
    {

        //获取指定会议室所有未完成流程申请
        $list = MeetingRoomV::where('room', $name)
            ->order('create_time desc')
            ->select();

        if (!count($list)) {
            return 1;
        }
        $this->prefix($list, $time_begin, $time_end);
    }

    private function prefix($list, $time_begin, $time_end)
    {
        $res = 1;
        foreach ($list as $k => $v) {
            if ($this->checkTime(strtotime($v['time_begin']), strtotime($v['time_end']),
                    strtotime($time_begin), strtotime($time_end)) == 2) {
                $res = 2;
                break;

            }

        }
        return $res;


    }

    private function checkTime($use_begin, $use_end, $time_begin, $time_end)
    {
        if ($time_begin <= $use_begin && $time_end <= $use_end) {
            return 2;
        }

        if ($time_begin < $use_end && $time_end > $use_end) {
            return 2;
        }

        if ($time_begin > $use_begin && $time_end < $use_end) {
            return 2;
        }
        return 1;

    }

}