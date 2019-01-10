<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/9
 * Time: 6:08 PM
 */

namespace app\api\service;


use app\api\model\AdminT;
use app\api\model\MeetingRoomT;
use app\api\model\MeetingT;
use app\api\model\SignInT;
use app\api\model\SignInV;
use app\lib\enum\CommonEnum;
use app\lib\exception\MeetingException;

class MeetingService extends BaseService
{
    public function signIn($id, $card, $mobile)
    {
        $this->checkRoom($card);
        $meeting = $this->checkMeeting($id);
        $department = $meeting->push . ',' . $meeting->host;
        $this->checkUser($mobile, $department);
        $data = [
            'card' => $card,
            'phone' => $mobile,
            'm_id' => $id
        ];
        if ($this->checkSign($id, $mobile)) {
            throw new MeetingException(
                [
                    'code' => 401,
                    'msg' => '您已签到，无需重复签到',
                    'errorCode' => 80000
                ]
            );
        }
        $res = SignInT::create($data);
        if (!$res) {
            throw  new MeetingException([
                'code' => 401,
                'msg' => '签到失败',
                'errorCode' => 80008
            ]);
        }

        $this->sendMsg(date('Y-m-d H:i'), $meeting->address, $mobile);

        return [
            'all' => $this->getMeetingMembers($department),
            'sign_in' => $this->getSignInMembers($id)

        ];

    }

    private function sendMsg($sign_date, $address, $phone)
    {
        $msg = "您%s于%s签到成功！";
        $msg = sprintf($msg, $sign_date, $address);
        $user_id = AdminService::getUserIdWithPhone($phone);
        if ($user_id) {
            (new MsgService())->sendMsg($user_id, $msg);

        }
    }


    private function checkSign($m_id, $phone)
    {
        $count = SignInT::where('m_id', $m_id)
            ->where('phone', $phone)
            ->count();

        return $count;

    }

    private function checkMeeting($id)
    {
        $meeting = MeetingT::get($id);
        if (!$meeting) {
            throw new MeetingException(
                [
                    'code' => 401,
                    'msg' => '签到会议不存在',
                    'errorCode' => 80002
                ]
            );

        }

        if (time() < strtotime($meeting->time_begin)) {
            throw new MeetingException(
                ['code' => 401,
                    'msg' => '签到未开始，请耐心等候！',
                    'errorCode' => 80009
                ]
            );
        }

        if (time() > strtotime($meeting->time_end)) {
            throw new MeetingException(
                ['code' => 401,
                    'msg' => '签到已结束！',
                    'errorCode' => 800010
                ]
            );

        }
        return $meeting;
    }


    private function checkRoom($card)
    {
        $room = MeetingRoomT::where('card', $card)->find();
        if (!$room) {
            throw new MeetingException(
                [
                    'code' => 401,
                    'msg' => '没有和签到机匹配的会议室',
                    'errorCode' => 80004
                ]
            );
        }
    }

    private function checkUser($mobile, $department)
    {
        $count = AdminT::where('phone', $mobile)
            ->whereIn('department', $department)
            ->count('id');
        if (!$count) {
            throw new MeetingException(
                [
                    'code' => 401,
                    'msg' => '签到失败，您不在本次会议名单之中！',
                    'errorCode' => 80004
                ]
            );
        }

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
        $phone = Token::getCurrentTokenVar('phone');
        $list = SignInV::getListForWx($meeting_date, $phone, $page, $size);
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


    public function getInfoForMC($card)
    {
        $room = MeetingRoomT::where('card', $card)
            ->find();
        if (!$room) {
            throw new MeetingException([
                'code' => 401,
                'msg' => '签到机和会议室绑定关系不存在',
                'errorCode' => 80007
            ]);
        }
        //获取这个会议室当天的会议情况
        $meeting = MeetingT::where('state', CommonEnum::STATE_IS_OK)
            ->where('address', $room->name)
            ->where('meeting_date', '=', date('Y-m-d'))
            ->where('meeting_begin', '>=', date('Y-m-d H:i'))
            ->order('create_time desc')
            ->field('id,DATE_FORMAT(time_begin,"%H:%i") as time_begin,DATE_FORMAT(time_end,"%H:%i") as time_end, DATE_FORMAT(meeting_begin,"%H:%i") as meeting_begin,push,host')
            ->find();

        if ($meeting) {
            $meeting['all'] = $this->getMeetingMembers($meeting->push . ',' . $meeting->host);
            $meeting['sign_in'] = $this->getSignInMembers($meeting->id);
        } else {
            $meeting['all'] = 0;
            $meeting['sign_in'] = 0;
        }

        return $meeting;

    }

    private function getMeetingMembers($push)
    {
        $count = AdminT::where('state', CommonEnum::STATE_IS_OK)
            ->whereIn('department', $push)
            ->count('id');
        return $count;

    }

    private function getSignInMembers($id)
    {
        $count = SignInT::where('m_id', $id)
            ->count('id');
        return $count;

    }

    public function checkMeetingPush()
    {
        $meeting = MeetingT::getMeetingToPush();
        if (count($meeting)) {
            foreach ($meeting as $k => $v) {
                $msg = "%s在%s举办会议：%s。会议时间为%s到%s，签到时间为%s到%s，请相关参会人员按时在:%s进行签到。";
                $msg = sprintf($msg, $v['host'], $v['meeting_date'], $v['theme'],
                    $v['meeting_begin'], $v['meeting_end'], $v['time_begin'], $v['time_end'], $v['address']);
                $user_ids = AdminService::getUserIdWithMeeting($v['push']);
                (new MsgService())->sendMsg($user_ids, $msg);
                MeetingT::update(['pushed' => 2], ['id' => $v['id']]);
            }

        }

    }


}
