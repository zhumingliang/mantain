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

    public function user()
    {
        return $this->belongsTo('AdminV',
            'uid', 'id');
    }

    public static function RunLogToReport($wf_fid, $wf_type)
    {

        $run_log = self::where('from_id', 'eq', $wf_fid)
            ->where('from_table', 'eq', $wf_type)
            ->with(['user' => function ($query) {
                $query->field('id,username,phone,role,department');
            }])
            ->field('uid,content')
            ->select();

        return $run_log;
    }

}