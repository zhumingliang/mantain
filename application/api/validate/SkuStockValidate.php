<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/27
 * Time: 10:36 PM
 */

namespace app\api\validate;


class SkuStockValidate extends BaseValidate
{

    protected $rule = [
        'sku_id' => 'require|isPositiveInteger',
        'count' => 'require|isPositiveInteger',
        'all_count' => 'require|isPositiveInteger',
        'type' => 'require|in:1,2',
        'stock_code' => 'require',
        'price' => 'require',
        'all_money' => 'require',
    ];

}