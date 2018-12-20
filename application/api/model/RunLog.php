<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/20
 * Time: 4:37 PM
 */

namespace app\api\model;


use think\Model;

class RunLog extends Model
{
    public function admin()
    {
        return $this->belongsTo('AdminT',
            'uid', 'id');
    }

}