<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/3
 * Time: 4:15 PM
 */

namespace app\api\service;


use app\api\model\AccessControlT;
use app\api\model\AccessControlV;
use app\lib\exception\FlowException;
use app\lib\exception\OperationException;
use think\Db;
use think\Exception;

class AccessService extends BaseService
{
    public function save($params)
    {
        Db::startTrans();
        try {
            $access = AccessControlT::create($params);
            if (!$access->id) {
                throw new OperationException();
            }
            //启动工作流
            $flow_date = [
                'wf_type' => 'access_control_t',
                'wf_id' => 3,
                'wf_fid' => $access->id,
                'new_type' => 0,
                'check_con' => '同意',
            ];
            $res = (new FlowService())->statr_save($flow_date);
            if (!$res == 1) {
                Db::rollback();
                throw new FlowException();
            }
            //保存流程
            $check_res = $this->saveCheck($access->id);

            if (!$check_res == 1) {
                Db::rollback();
                throw new FlowException();
            }

            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }

    }


    private function saveCheck($wf_fid, $wf_type = "access_control_t")
    {

        $info = (new FlowService())->getInfo($wf_fid, $wf_type);
        $data = [
            'art' => "",
            'btodo' => "",
            'check_con' => "同意",
            'flow_id' => $info['flow_id'],
            'flow_process' => $info['flow_process'],
            'npid' => $info['nexprocess']['id'],
            'run_id' => $info['run_id'],
            'run_process' => $info['run_process'],
            'sing_st' => 0,
            'submit_to_save' => "ok",
            'wf_fid' => $wf_fid,
            'wf_singflow' => "",
            'wf_backflow' => "",
            'wf_title' => 2,
            'wf_type' => "access_control_t",
        ];
        $res = (new FlowService())->check($data);
        return $res;
    }

    public function getTheFlow($wf_fid, $wf_type = "access_control_t")
    {
        $check = (new FlowService())->btn($wf_fid, $wf_type, $status = 1);
        $info = (new FlowService())->getInfo($wf_fid, $wf_type);

        return [
            'check' => $check,
            'info' => $info
        ];


    }


    public function getList($page, $size, $time_begin, $time_end, $department, $username, $status, $access)
    {

        $list = AccessControlV::getList($page, $size, $time_begin, $time_end, $department, $username, $status, $access);
        return $list;
    }


    public function export($time_begin, $time_end, $department, $username, $status, $access)
    {

        $list = AccessControlV::export($time_begin, $time_end, $department, $username, $status, $access);
        $list = $this->prefixStatus($list);
        $header = array(
            '日期',
            '申请人',
            '部门',
            '职务',
            '人员类型',
            '开通功能',
            '工作截止时间（外来人员）',
            '状态',
        );
        $file_name = '门禁权限列表导出' . '-' . date('Y-m-d', time()) . '.csv';
        $this->put_csv($list, $header, $file_name);


    }

}