<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 11:07 PM
 */

namespace app\api\validate;


class MeetingReceptValidate extends BaseValidate
{
    protected $rule = [
        'apply_date' => 'require|isNotEmpty',
        'project' => 'require|isNotEmpty',
        'unit' => 'require|isNotEmpty',
        'leader' => 'require|isNotEmpty',
        'post' => 'require|isNotEmpty',
        'grade' => 'require|isNotEmpty',
        'departmental' => 'require|isNotEmpty',
        'section' => 'require|isNotEmpty',
        'under_section' => 'require|isNotEmpty',
        'male' => 'require|isNotEmpty',
        'female' => 'require|isNotEmpty',
        'meeting_place' => 'require|isNotEmpty',
        'meeting_date' => 'require|isNotEmpty',
        'meeting_count' => 'require|isNotEmpty',
        'hotel' => 'require|isNotEmpty',
        'accompany' => 'require|isNotEmpty',
        'meals' => 'require|isNotEmpty',
    ];

}