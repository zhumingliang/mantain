<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/25
 * Time: 7:24 PM
 */

namespace app\api\model;


use think\Model;

class SkuImgT extends Model
{
    protected $hidden=['id','create_time','update_time','state','s_id'];

    public function imgUrl()
    {
        return $this->belongsTo('ImgT',
            'img_id', 'id');
    }

}