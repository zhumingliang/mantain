<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/20
 * Time: 4:11 PM
 */

namespace app\api\model;


use app\api\service\Token;
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

    public static function getComplete($table, $page, $size)
    {

        //获取用户发起或者参与的已完成的流程
        $uid = Token::getCurrentUid();
        $sql = "(parent_id=" . $uid . "  AND status=0) OR (parent_id<>" . $uid . ")";
        $complete = FlowCompleteV::where('uid', $uid)
            ->where('from_table', $table)
            ->whereRaw($sql)
            ->field('run_id')
            ->select();
        $run_ids = '';
        foreach ($complete as $k => $v) {
            $run_ids .= $v['run_id'] . ',';
        }
        if (strlen($run_ids) > 1) {
            $run_ids = substr($run_ids, 0, -1);
        }
        if (!strlen($run_ids)) {
            return [
                'total' => 0,
                'per_page' => $size,
                'current_page' => $page,
                'last_page' => 0,
                'data' => []
            ];

        }
        $list = self::whereIn('id', $run_ids)
            ->with(['flow',
                'process' => function ($query) {
                    $query->with(['admin' => function ($query) {
                        $query->field('id,username');
                    }])->where('btn', 'ok');
                }
            ])
            ->field('id,from_table,from_id,uid,status')
            ->order('id desc')
            ->paginate($size, false, ['page' => $page]);
        return $list;

    }

}