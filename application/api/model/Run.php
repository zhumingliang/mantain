<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/20
 * Time: 4:11 PM
 */

namespace app\api\model;


use think\Model;

class Run extends Model
{
    //protected $table='c';
    protected $table_type = 'AccessControlT';

    public function process()
    {
        return $this->hasMany('RunLog',
            'run_id', 'id');
    }

    public function flow()
    {
        return $this->belongsTo($this->table_type, 'from_id', 'id');

    }

    public static function getComplete($table)
    {
        $list = self::where('status', '=', 0)
            ->with(['flow',
                'process' => function ($query) {
                    $query->with(['admin'=>function ($query) {
                        $query->field('id,username');
                    }])->where('btn', 'ok');
                }
            ])
            ->where('from_table', $table)
            ->field('id,from_table,from_id,uid,status')
            ->order('id desc')
            ->select();
        return $list;

    }

}