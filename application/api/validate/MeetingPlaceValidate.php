<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/5
 * Time: 10:30 AM
 */

namespace app\api\validate;


class MeetingPlaceValidate extends BaseValidate
{
    protected $rule = [
        'apply_date' => 'require|isNotEmpty',
        'letter_size' => 'require|isNotEmpty',
        'letter_title' => 'require|isNotEmpty',
        'meals_count' => 'require|isNotEmpty',
        'users' => 'require|isNotEmpty',
        //'detail' => 'require|isNotEmpty'
    ];

}