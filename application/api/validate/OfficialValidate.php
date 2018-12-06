<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 9:25 AM
 */

namespace app\api\validate;


class OfficialValidate extends BaseValidate
{
    protected $rule = [
        'phone' => 'require|isNotEmpty',
        'cuisine' => 'require|isNotEmpty',
        'member' => 'require|isPositiveInteger',
        'table_number' => 'require|isPositiveInteger',
        'product' => 'require|isNotEmpty',
        'content' => 'require|isNotEmpty',
        'meal' => 'require',
        'meal_type' => 'require|isNotEmpty',
        'time_begin' => 'require|isNotEmpty',
        'time_end' => 'require|isNotEmpty',
    ];

}