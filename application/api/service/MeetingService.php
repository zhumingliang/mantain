<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/9
 * Time: 6:08 PM
 */

namespace app\api\service;


use app\api\model\MeetingRoomT;
use app\api\model\MeetingT;
use app\api\model\SignInT;
use app\api\model\SignInV;

class MeetingService extends BaseService
{
    public function signIn($card, $mobile)
    {
        $m_id = $this->checkMeeting($card);
        $data = [
            'card' => $card,
            'phone' => $mobile,
            'm_id' => $m_id
        ];
        if (!$this->checkSign($m_id, $mobile)) {
            SignInT::create($data);
        }
    }


    private function checkSign($m_id, $phone)
    {
        $count = SignInT::where('m_id', $m_id)
            ->where('phone', $phone)
            ->count();
        return $count;

    }

    private function checkMeeting($card)
    {
        $meeting = MeetingT::where('card', $card)->where('time_begin', '<= time', date("Y-m-d H:i:s"))
            ->where('time_end', '>= time', date("Y-m-d H:i:s"))
            ->find();
        if ($meeting) {
            return $meeting->id;
        }
        return 0;
    }


    public function getMeetingList($time_begin, $time_end, $address, $theme, $page, $size, $host)
    {
        $list = MeetingT::getMeetingList($time_begin, $time_end, $address, $theme, $page, $size, $host);
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
            '主办部门',
            '推送部门',
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

    public function getSignInListForWx($meeting_date, $page, $size)
    {
        $phone =Token::getCurrentTokenVar('phone');
        $list = SignInV::getListForWx($meeting_date,$phone, $page, $size);
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