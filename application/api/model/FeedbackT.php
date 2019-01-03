<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/3
 * Time: 11:26 AM
 */

namespace app\api\model;


use think\Model;

class FeedbackT extends Model
{
    protected $hidden=['create_time','update_time','state'];


    public function imgUrl()
    {
        return $this->belongsTo('ImgT',
            'img_id', 'id');
    }
}