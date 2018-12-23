<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/20
 * Time: 4:11 PM
 */

namespace app\api\model;


use app\api\service\Token;
use think\Db;
use think\Model;

class Run extends Model
{
    //protected $table='c';
    protected $table_type = '';

    public function process()
    {
        return $this->hasMany('RunLog',
            'run_id', 'id');
    }

    /*  public function flow()
      {
          return $this->belongsTo($this->table_type, 'from_id', 'id');

      }*/

    public function getComplete($table, $page, $size)
    {
        //$this->table_type = $this->getTableName($table);
        //获取用户发起或者参与的已完成的流程
        $uid = Token::getCurrentUid();
        $sql = "(parent_id=" . $uid . "  AND status=1) OR (parent_id<>" . $uid . ")";
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
            ->with([
                'process' => function ($query) {
                    $query->with(['admin' => function ($query) {
                        $query->field('id,username');
                    }])->where('btn', 'ok');
                }
            ])
            ->field('id,from_table,from_id,uid,status')
            ->order('id desc')
            ->paginate($size, false, ['page' => $page])->toArray();
        $data = $list['data'];
        if (count($data)) {
            foreach ($data as $k => $v) {
                $data[$k]['flow'] = Db::name($table)->where('id', $v['from_id'])->find();
            }
            $list['data'] = $data;
        }


        return $list;

    }

    public function getReady($table)
    {
        // $this->table_type = $this->getTableName($table);

        //获取用户发起未完成
        $uid = Token::getCurrentUid();
        $ready = FlowCompleteV::where('from_table', $table)
            ->where('parent_id', $uid)
            ->where('uid', $uid)
            ->where('status', '=', 0)
            ->field('run_id')
            ->select();
        //获取到自己角色审核的流程
        $role = Token::getCurrentTokenVar('role');
        $toReady = RunReadyV::where('from_table', $table)
            ->select();

        $run_arr = array();
        foreach ($toReady as $k => $v) {
            $arr = explode(',', $v['sponsor_ids']);
            if (in_array($role, $arr)) {
                array_push($run_arr, $v['run_id']);
            }

        }
        $run_ids = implode(',', $run_arr);
        $run_ids = strlen($run_ids) ? $run_ids . ',' : '';
        foreach ($ready as $k => $v) {
            $run_ids .= $v['run_id'] . ',';
        }
        if (strlen($run_ids) > 1) {
            $run_ids = substr($run_ids, 0, -1);
        }
        if (!strlen($run_ids)) {
            return [
            ];

        }
        $list = self::whereIn('id', $run_ids)
            ->with([
                'process' => function ($query) {
                    $query->with(['admin' => function ($query) {
                        $query->field('id,username');
                    }])->where('btn', 'ok');
                }
            ])
            ->field('id,from_table,from_id,uid,status')
            ->order('id desc')
            ->select();

        if (count($list)) {
            foreach ($list as $k => $v) {

                $list[$k]['flow'] = Db::name($table)->where('id', $v['from_id'])->find();

                if ($v['uid'] == $uid) {
                    $list[$k]['btn'] = 'cancel';
                } else {
                    $list[$k]['btn'] = 'check';
                }
            }
        }
        return $list;

    }

    private function getTableName($table)
    {
        $arr = explode('_', $table);
        $name = array();
        foreach ($arr as $v) {
            array_push($name, ucfirst($v));
        }
        return implode('', $name);

    }


}