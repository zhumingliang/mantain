<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/5/31
 * Time: 下午10:14
 */

namespace app\api\service;


use think\Exception;
use think\facade\Cache;
use zml\tp_tools\Curl;

class AccessTokenAuth
{
    private $tokenUrl;
    const TOKEN_CACHED_KEY = 'access_token_auth';
    const TOKEN_EXPIRE_IN = 7000;

    function __construct($code)
    {
        $url = config('wx.access_token_auth_url');
        $url = sprintf($url, config('wx.app_id'), config('wx.app_secret'), $code);
        $this->tokenUrl = $url;
    }

    // 建议用户规模小时每次直接去微信服务器取最新的token
    // 但微信access_token接口获取是有限制的 2000次/天
    public function get()
    {
        return $this->getFromWxServer();
        /*        $cache_token = $this->getFromCache();
                if (!$cache_token) {
                    return $this->getFromWxServer();
                } else {
                    return $cache_token;
                }*/
    }

    private function getFromCache()
    {
        $token = cache(self::TOKEN_CACHED_KEY);
        if ($token) {
            return $token;
        }
        return null;
    }


    /**
     * @return mixed
     * @throws Exception
     */
    private function getFromWxServer()
    {

        $token = Curl::get($this->tokenUrl);
        $token = json_decode($token, true);
        if (!$token) {
            throw new Exception('获取AccessToken异常');
        }
        if (!empty($token['errcode'])) {
            throw new Exception($token['errmsg']);
        }
        $this->saveToCache($token);
        return $token;
    }

    private function saveToCache($token)
    {
        cache(self::TOKEN_CACHED_KEY, $token, self::TOKEN_EXPIRE_IN);
    }

}