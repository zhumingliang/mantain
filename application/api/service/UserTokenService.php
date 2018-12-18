<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/18
 * Time: 11:14 PM
 */

namespace app\api\service;


use app\api\model\AdminT;
use app\api\model\LogT;
use app\lib\exception\TokenException;
use think\facade\Cache;
use think\Exception;
use zml\tp_tools\Curl;

class UserTokenService extends Token
{
    public function getToken($code)
    {
        $userId = $this->getUserId($code);
        $admin = AdminT::where('user_id', $userId)->find();
        if (!$admin) {
            $mobile = $this->getMobile($userId);
            $admin = AdminT::where('phone', $mobile)->find();
            AdminT::update(['phone' => $mobile], ['user_id' => $userId]);
            if (!$admin) {
                throw  new TokenException(
                    [
                        'code' => 401,
                        'msg' => '系统中不存在该用户，请先上传用户',
                        'errorCode' => 200011]
                );
            }

            /**
             * 获取缓存参数
             */
            $cachedValue = $this->prepareCachedValue($admin);
            /**
             * 缓存数据
             */
            $token = $this->saveToCache('', $cachedValue);
            return $token;

        }


    }

    private function saveToCache($key, $cachedValue)
    {
        $key = empty($key) ? self::generateToken() : $key;
        $value = json_encode($cachedValue);
        $expire_in = config('setting.token_expire_in');
        $request = Cache::set($key, $value, $expire_in);


        if (!$request) {
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 20002
            ]);
        }

        $cachedValue['token'] = $key;
        unset($cachedValue['phone']);
        unset($cachedValue['type']);
        unset($cachedValue['department']);
        unset($cachedValue['card']);
        return $cachedValue;
    }

    private function getUserId($code)
    {
        $token = (new AccessToken())->get();
        $user_info_url = sprintf(config('wx.user_id_url'), $token, $code);
        $user_info = Curl::get($user_info_url);
        if (!$token) {
            throw new Exception('获取用户信息异常');
        }
        if (!empty($token['errcode'])) {
            throw new Exception($token['errmsg']);
        }
        $user_info = json_decode($user_info);
        $userId = $user_info->UserId;
        return $userId;

    }

    private function getMobile($userId)
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

        $user_info = json_decode($user_info);
        return $user_info->mobile;

    }

    private function prepareCachedValue($admin)
    {
        $cachedValue = [
            'u_id' => $admin->id,
            'phone' => $admin->phone,
            'username' => $admin->username,
            'card' => $admin->card,
            'type' => $admin->type,
            'account' => $admin->account,
            'role' => $admin->role,
            'department' => $admin->department,
            'category' => 'wx'
        ];

        return $cachedValue;
    }

}