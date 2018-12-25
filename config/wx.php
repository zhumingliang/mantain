<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/18
 * Time: 4:24 PM
 */

return [
    'app_id' => 'wwe1e15b3f8a6d5a90',

    'app_secret' => 'ZF-3iBv-qrn96WEIWAtRUWHdcQp7kCw1HkWb7YIEmrI',

    'wx_code_url' => 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=SCOPE&state=STATE#wechat_redirect',


    'access_token_url' => "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=%s&corpsecret=%s",

    'user_id_url' => "https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=%s&code=%s",

    'user_info_url' => "https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=%s&userid=%s",

];