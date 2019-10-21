<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/29
 * Time: 9:36 AM
 */

namespace app\api\model;


use think\Model;

class CollarUseT extends Model
{
    public function admin()
    {
        return $this->belongsTo('AdminT', 'admin_id', 'id');
    }

    public static function info($id)
    {
        return self::where('id', $id)
            ->with('admin')
            ->find();

    }
}