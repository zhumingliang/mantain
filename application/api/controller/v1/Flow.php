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
     *       "flow_process":1,
     *     }
     * @apiParam (请求参数说明) {String} check_con   审核意见
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
    public function checkPass($check_con,$flow_id,$flow_process,$npid,$run_id,$run_process,$wf_fid)
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
            'submit_to_save' => "ok",
            'wf_fid' => $wf_fid,
            'wf_singflow' => "",
            'wf_backflow' => "",
            'wf_title' => 2,
            'wf_type' => "access_control_t",
        ];
        $res = (new FlowService())->check($data);
        return json($res);

    }

    public function getInfo($wf_fid = 22, $wf_type = "access_control_t")
    {
        $info = ['wf_title' => input('wf_title'), 'wf_fid' => $wf_fid, 'wf_type' => $wf_type];

        $workflow = new workflow();
        $flowinfo = $workflow->workflowInfo($wf_fid, $wf_type);



    }

}