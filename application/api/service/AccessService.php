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
use app\lib\enum\CommonEnum;
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
            $check_res = (new FlowService())->saveCheck($access->id, 'access_control_t');
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

    public function getList($page, $size, $time_begin, $time_end, $department, $username, $status, $access)
    {

        $list = AccessControlV::getList($page, $size, $time_begin, $time_end, $department, $username, $status, $access);
        $list=(new FlowService())->checkListStatus($list,'access_control_t');
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
            '申请人员名单-职务',
            '人员类型',
            '开通功能',
            '工作截止时间（外来人员）',
            '状态',
        );
        $file_name = '门禁权限列表导出' . '-' . date('Y-m-d', time()) . '.csv';
        $this->put_csv($list, $header, $file_name);


    }

}