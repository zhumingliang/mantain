<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/3
 * Time: 9:24 AM
 */

namespace app\api\validate;


class AccessControlValidate extends BaseValidate
{
    protected $rule = [
        'access' => 'require|isNotEmpty',
        //'deadline' => 'require|isNotEmpty',
        'user_type' => 'require|isNotEmpty',
    ];

}