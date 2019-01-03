<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/2
 * Time: 4:20 PM
 */

namespace app\api\model;


use think\Model;

class RepairImgT extends Model
{

    protected $hidden=['create_time','update_time','state'];


    public function imgUrl()
    {
        return $this->belongsTo('ImgT',
            'img_id', 'id');
    }
}