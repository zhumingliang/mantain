<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/25
 * Time: 7:25 PM
 */

namespace app\api\model;

class ImgT extends BaseModel
{
    protected $hidden=['create_time','update_time','id'];
    public function getUrlAttr($value, $data){
        return $this->prefixImgUrl($value, $data);
    }

}