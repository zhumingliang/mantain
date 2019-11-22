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
        'unit' => 'require|isNotEmpty',
        'place' => 'require|isNotEmpty',
        'reason' => 'require|isNotEmpty',
        'time_begin' => 'require|isNotEmpty',
        'time_end' => 'require|isNotEmpty'
    ];

}