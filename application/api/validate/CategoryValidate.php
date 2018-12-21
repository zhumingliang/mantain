<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/21
 * Time: 10:16 AM
 */

namespace app\api\validate;


class CategoryValidate extends BaseValidate
{
    protected $rule = [
        'name' => 'require|isNotEmpty',
        'code' => 'require|isNotEmpty',
        'order' => 'require|isNotEmpty',
    ];

}