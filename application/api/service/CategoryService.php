<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/26
 * Time: 9:16 AM
 */

namespace app\api\service;


use app\api\model\CategoryT;
use app\lib\enum\CommonEnum;

class CategoryService
{
    public function getCategorys()
    {
        $cagegoys = CategoryT::where('state', CommonEnum::STATE_IS_OK)
            ->select();
        return $cagegoys;

    }

}