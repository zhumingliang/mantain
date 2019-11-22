<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/3
 * Time: 10:43 AM
 */

namespace app\api\validate;


class MeetingValidate extends BaseValidate
{
    protected $rule = [
        'meeting_date' => 'require|isNotEmpty',
        'address' => 'require|isNotEmpty',
        'time_begin' => 'require|isNotEmpty',
        'time_end' => 'require|isNotEmpty',
        'meeting_begin' => 'require|isNotEmpty',
        'theme' => 'require|isNotEmpty'
    ];

}