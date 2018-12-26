<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/26
 * Time: 9:27 AM
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class UnitT extends Model
{
    public static function getList()
    {
        $list = self::where('state', '=', CommonEnum::STATE_IS_OK)
            ->field('id,name')
            ->select();
        return $list;

    }

}