<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/18
 * Time: 11:14 PM
 */

namespace app\api\service;


use app\api\model\LogT;
use think\Exception;
use zml\tp_tools\Curl;

class UserTokenService
{
    public function getToken($code)
    {
        $userId = $this->getUserId($code);
        $this->getUserInfo($userId);


    }

    private function getUserId($code)
    {
        $token = (new AccessToken())->get();
        $user_info_url = sprintf(config('wx.user_info_url'), $token, $code);
        $user_info = Curl::get($user_info_url);
        if (!$token) {
            throw new Exception('获取用户信息异常');
        }
        if (!empty($token['errcode'])) {
            throw new Exception($token['errmsg']);
        }
        $user_info = json_decode($user_info);
        $userId = $user_info['UserId'];
        return $userId;

    }

    private function getUserInfo($userId)
    {
        $token = (new AccessToken())->get();
        $url = sprintf(config('wx.user_info_url'), $token, $userId);
        $user_info = Curl::get($url);
        if (!$token) {
            throw new Exception('获取用户信息异常');
        }
        if (!empty($token['errcode'])) {
            throw new Exception($token['errmsg']);
        }
        LogT::create(['msg' => $user_info]);
        $user_info = json_decode($user_info);
        print_r($user_info);

    }

}