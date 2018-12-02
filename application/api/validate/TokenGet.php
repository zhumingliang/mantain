<?php
/**
 * Created by PhpStorm.
 * User: zhumingliang
 * Date: 2018/3/20
 * Time: 下午2:00
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    protected $rule = [
        'code' => 'require|isNotEmpty',
        'pwd' => 'require|isNotEmpty',
        'account' => 'require|isNotEmpty'
    ];

    protected $message = [
        'code' => '微信端获取Token，需要code',
        'account' => '登录操作，需要账号',
        'pwd' => '登录操作，需要密码'
    ];

    protected $scene = [
        'wx' => ['code'],
        'pc' => ['account', 'pwd'],
    ];

}