<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 11:06 PM
 */

namespace app\api\model;


use think\Model;

class MeetingReceptT extends Model
{
    public function meals()
    {
        return $this->hasMany('MeetingReceptMealT',
            'mr_id', 'id');
    }
}