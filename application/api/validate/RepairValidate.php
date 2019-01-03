<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/2
 * Time: 3:33 PM
 */

namespace app\api\validate;


class RepairValidate extends BaseValidate
{
    protected $rule = [
        'name' => 'require|isNotEmpty',
        'address' => 'require|isNotEmpty',
        'remark' => 'require|isNotEmpty',
        'type' => 'require|in:1,2',
    ];

}