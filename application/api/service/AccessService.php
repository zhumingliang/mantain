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
                'wf_id' => 2,
                'wf_fid' => $access->id,
                'new_type' => 0,
                'check_con' => '同意',
            ];
            $res = (new FlowService())->statr_save($flow_date);
            if ($res == 1) {
                Db::commit();
            } else {
                Db::rollback();
                throw new FlowException();
            }

        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }

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