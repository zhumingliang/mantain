<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/5
 * Time: 10:32 AM
 */

namespace app\api\model;


use think\Model;

class MeetingplaceT extends Model
{

    public function users()
    {
        return $this->hasMany('MeetingplaceReceptT',
            'mp_id', 'id');
    }

    public function detail()
    {
        return $this->hasMany('ReceptDetailT',
            'mp_id', 'id');
    }


}