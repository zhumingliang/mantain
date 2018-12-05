<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/5
 * Time: 9:31 AM
 */

namespace app\api\validate;


class RecreationalValidate extends BaseValidate
{
    protected $rule = [
        'unit' => 'require|isNotEmpty',
        'space' => 'require|isNotEmpty',
        'time_begin' => 'require|isNotEmpty',
        'time_end' => 'require|isNotEmpty',
        'user_count' => 'require|isPositiveInteger'
    ];




}