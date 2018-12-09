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


}