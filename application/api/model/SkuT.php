<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/25
 * Time: 7:24 PM
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class SkuT extends Model
{

    public function imgs()
    {
        return $this->hasMany('SkuImgT',
            'sku_id', 'id');
    }

    public static function getSkuInfo($id)
    {
        $info = self::where('id', '=', $id)
            ->with([
                'imgs' => function ($query) {
                    $query->with(['imgUrl'])
                        ->where('state', '=', 1);
                }
            ])
            ->hidden(['state', 'create_time', 'update_time', 'admin_id'])
            ->find();
        return $info;

    }

    public static function getListForUse()
    {
        $list = self::where('state', CommonEnum::STATE_IS_OK)
            ->field('id,name,use_type')
            ->select();
        return $list;

    }


}