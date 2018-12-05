<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 1:08 AM
 */

namespace app\api\validate;


class MultiValidate extends BaseValidate
{
    protected $rule = [
        'unit' => 'require|isNotEmpty',
        'space' => 'require|isNotEmpty',
        'time_begin' => 'require|isNotEmpty',
        'time_end' => 'require|isNotEmpty',
        'reason' => 'require|isNotEmpty',
    ];

}