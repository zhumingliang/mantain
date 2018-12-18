<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/18
 * Time: 11:14 PM
 */

namespace app\api\service;


use app\api\model\LogT;
use zml\tp_tools\Curl;

class UserTokenService
{
    public function getToken($code)
    {
        $token = (new AccessToken())->get();
        $user_info_url = sprintf(config('wx.user_info_url'), $token, $code);

        $user_info = Curl::get($user_info_url);
        LogT::create(['msg' => $user_info]);


    }

}