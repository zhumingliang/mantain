<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/18
 * Time: 4:24 PM
 */

return [
    'app_id' => 'wwffcc8e626d73e98f',

    'app_secret' => 'cqJl6DmpJKktD4zGn6DhcCObrchr6QLWYw2HHvyo5fM',

    'wx_code_url' => 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=SCOPE&state=STATE#wechat_redirect',


    'access_token_url' => "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=%s&corpsecret=%s",

    'user_id_url' => "https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=%s&code=%s",

    'user_info_url' => "https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=%s&userid=%s",

];