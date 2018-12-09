<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/9
 * Time: 6:08 PM
 */

namespace app\api\service;


use app\api\model\MeetingT;
use app\api\model\SignInT;
use app\api\model\SignInV;

class MeetingService extends BaseService
{
    public function signIn($card)
    {
        $data = [
            'card' => $card,
            'm_id' => $this->checkMeeting()
        ];
        SignInT::create($data);

    }

    private function checkMeeting()
    {
        $meeting = MeetingT::where('time_begin', '<= time', date("Y-m-d H:i:s"))
            ->where('time_end', '>= time', date("Y-m-d H:i:s"))
            ->find();
        if ($meeting) {
            return $meeting->id;
        }
        return 0;
    }


    public function getMeetingList($time_begin, $time_end, $address, $theme, $page, $size)
    {
        $list = MeetingT::getMeetingList($time_begin, $time_end, $address, $theme, $page, $size);
        return $list;

    }

    public function exportMeeting($time_begin, $time_end, $address, $theme)
    {
        $list = MeetingT::exportMeeting($time_begin, $time_end, $address, $theme);

        $header = array(
            '日期',
            '会议主题',
            '会议概要',
            '签到地点',
            '签到开始时间',
            '签到截止时间',
            '签到截止时间',
            '备注'
        );
        $file_name = '会议列表—导出' . '-' . date('Y-m-d', time()) . '.csv';
        $this->put_csv($list, $header, $file_name);

    }


    public function getSignInList($time_begin, $time_end,
                                  $department, $username,
                                  $address, $theme, $page, $size)
    {
        $list = SignInV::getList($time_begin, $time_end,
            $department, $username,
            $address, $theme, $page, $size);
        return $list;
    }

    public function exportSignIn($time_begin, $time_end,
                                 $department, $username,
                                 $address, $theme)
    {
        $list = SignInV::export($time_begin, $time_end,
            $department, $username,
            $address, $theme);

        $header = array(
            '日期',
            '姓名',
            '手机号',
            '部门',
            '签到地点',
            '签到时间',
            '会议主题',
            '备注'
        );
        $file_name = '会议签到列表—导出' . '-' . date('Y-m-d', time()) . '.csv';
        $this->put_csv($list, $header, $file_name);

    }


}