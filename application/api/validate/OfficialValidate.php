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
        'member' => 'require|isPositiveInteger',
        'table_number' => 'require|isPositiveInteger',
        'product' => 'require|isNotEmpty',
        'content' => 'require|isNotEmpty',
        'meals' => 'require|isNotEmpty',
        'meal_space' => 'require|isNotEmpty',
        'meal_date' => 'require|isNotEmpty',
    ];

}