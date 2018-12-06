<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 10:27 AM
 */

namespace app\api\validate;


class CarValidate extends BaseValidate
{
    protected $rule = [
        'apply_date' => 'require|isNotEmpty',
        'count' => 'require|isPositiveInteger',
        'reason' => 'require|isNotEmpty',
        'members' => 'require|isNotEmpty',
        'address' => 'require|isNotEmpty',
    ];
}