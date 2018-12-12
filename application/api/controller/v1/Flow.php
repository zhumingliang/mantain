<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/11
 * Time: 4:35 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\FlowService;
use app\lib\exception\SuccessMessage;
use think\Db;
use think\response\Json;
use workflow\workflow;

class Flow extends BaseController
{
    /**
     * @api {POST} /api/v1/flow/check/pass  35-CMS-流程审核-通过
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  流程审核-通过
     * @apiExample {post}  请求样例:
     *    {
     *       "check_con": "同意",
     *       "flow_id": 1,
     *       "flow_process":1,
     *       "npid": 1,
     *       "run_id": 1,
     *       "run_process": 1,
     *       "wf_fid": 1,
     *       "wf_type": "access_control_t",
     *       "submit_to_save": "ok",
     *
     *     }
     * @apiParam (请求参数说明) {String} check_con   审核意见
     * @apiParam (请求参数说明) {String} wf_type   流程类别：门禁申请：access_control_t；公务接待：official_recept_t
     * @apiParam (请求参数说明) {String} submit_to_save   审批类别：ok：同意；back ： 不同意
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @param $check_con
     * @param $flow_id
     * @param $flow_process
     * @param $npid
     * @param $run_id
     * @param $run_process
     * @param $wf_fid
     * @return \think\response\Json
     */
    public function checkPass($check_con, $flow_id, $flow_process, $npid, $run_id, $run_process, $wf_fid, $wf_type,$submit_to_save='ok')
    {
        /**
         *
         * art
         * btodo
         * check_con    委屈委屈
         * flow_id    13
         * flow_process    106
         * npid    107
         * run_id    20
         * run_process    35
         * sing_st    0
         * submit_to_save    ok
         * wf_backflow
         * wf_fid    13
         * wf_singflow
         * wf_title    2
         * wf_type    news
         */
        $data = [
            'art' => "",
            'btodo' => "",
            'check_con' => $check_con,
            'flow_id' => $flow_id,
            'flow_process' => $flow_process,
            'npid' => $npid,
            'run_id' => $run_id,
            'run_process' => $run_process,
            'sing_st' => 0,
            'submit_to_save' => $submit_to_save,
            'wf_fid' => $wf_fid,
            'wf_singflow' => "",
            'wf_backflow' => "",
            'wf_title' => 2,
            'wf_type' => $wf_type,
        ];
        $res = (new FlowService())->check($data);
        if ($res) {
            return json(new SuccessMessage());
        }


    }

    /**
     * @api {GET} /api/v1/flow/info 34-获取预约申请—查看审核
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取预约申请—查看审核
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/flow/info?wf_fid=22&wf_type=access_control_t
     * @apiParam (请求参数说明) {String}  wf_fid  列表id
     * @apiParam (请求参数说明) {String} wf_type   流程类别：门禁申请：access_control_t；公务接待：official_recept_t
     * @apiSuccessExample {json}返回样例:
     * {"check":1,"info":{"sing_st":0,"flow_id":2,"run_id":12,"status":{"id":12,"uid":1,"run_id":12,"run_flow":2,"run_flow_process":4,"parent_flow":0,"parent_flow_process":0,"run_child":0,"remark":"","is_receive_type":0,"auto_person":5,"sponsor_text":"局长,普通员工,机服中心,机服主管局长,机服负责人,管理员,车队,部门主管局长,部门主管领导,部门局领导,部门负责人","sponsor_ids":"36,27,33,35,34,26,37,31,30,29,28","is_sponsor":0,"is_singpost":0,"is_back":0,"status":0,"js_time":1544516328,"bl_time":0,"jj_time":0,"is_del":0,"updatetime":0,"dateline":1544516328},"flow_process":4,"run_process":12,"flow_name":"门禁权限","process":{"id":4,"process_name":"申请","process_type":"is_one","process_to":"5","auto_person":5,"auto_sponsor_ids":"","auto_role_ids":"36,27,33,35,34,26,37,31,30,29,28","auto_sponsor_text":"","auto_role_text":"局长,普通员工,机服中心,机服主管局长,机服负责人,管理员,车队,部门主管局长,部门主管领导,部门局领导,部门负责人","range_user_ids":"","range_user_text":"","is_sing":1,"sign_look":0,"is_back":1,"todo":"局长,普通员工,机服中心,机服主管局长,机服负责人,管理员,车队,部门主管局长,部门主管领导,部门局领导,部门负责人"},"nexprocess":{"id":5,"process_name":"部门负责人审核","process_type":"is_step","process_to":"6","auto_person":5,"auto_sponsor_ids":"","auto_role_ids":"28","auto_sponsor_text":"","auto_role_text":"部门负责人","range_user_ids":"","range_user_text":"","is_sing":1,"sign_look":0,"is_back":1,"todo":"部门负责人"},"preprocess":["退回制单人修改"],"singuser":[{"id":1,"username":"朱明良","role":1},{"id":2,"username":"部门负责人","role":28},{"id":3,"username":"机服中心","role":33},{"id":4,"username":"机服中心负责人","role":34}],"log":[{"id":2,"uid":1,"from_id":22,"from_table":"access_control_t","run_id":12,"run_flow":0,"content":"同意","dateline":1544516328,"btn":"Send","art":"","user":"朱明良"}]}}
     * @apiSuccess (返回参数说明) {int} check 是否显示审批按钮 ：1 | 显示；2 | 不显示

     * @param $wf_fid
     * @param $wf_type
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfo($wf_fid, $wf_type)
    {
        //检查流程是否走完
        $result = Db::name('run')->where('from_id', 'eq', $wf_fid)
            ->where('from_table', 'eq', $wf_type)
            ->where('is_del', 'eq', 0)
            ->find();
        if ($result['status'] == 1) {
            //已经完成审核
            $check = 2;
            $info = (new FlowService())->getInfoForComplete($wf_fid, $wf_type);

        } else {
            $check = (new FlowService())->btn($wf_fid, $wf_type, $status = 1);
            $info = (new FlowService())->getInfo($wf_fid, $wf_type);


        }
        return json([
            'check' => $check,
            'info' => $info
        ]);


    }

}