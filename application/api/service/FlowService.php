<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/11
 * Time: 11:38 AM
 */

namespace app\api\service;


use app\api\model\Flow;
use app\lib\exception\FlowException;
use think\Db;
use workflow\workflow;

class FlowService
{

    /*正式发起工作流*/
    public function statr_save($data)
    {
        //$data = $this->request->param();
        //Array
        //(
        //    [wf_type] => news
        //    [wf_fid] => 16
        //    [wf_id] => 5
        //    [new_type] => 0
        //    [check_con] => 同意
        //)

        $uid = Token::getCurrentUid();
        $workflow = new workflow();
        $flow = $workflow->startworkflow($data, $uid);
        return $flow['code'];
    }

    /**
     * 获取当前角色-审核-状态
     * @param $wf_fid
     * @param $wf_type
     * @param $status
     * @return int|string
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function btn($wf_fid, $wf_type, $status)
    {
        $id = Db::name('log')->insertGetId(['msg' => $wf_fid . '-' . $status]);

        switch ($status) {
            case 0:
                return 2;
                break;
            case 1:
                $st = 0;
                $workflow = new workflow();
                $flowinfo = $workflow->workflowInfo($wf_fid, $wf_type);
                $user = explode(",", $flowinfo['status']['sponsor_ids']);
                if ($flowinfo['status']['auto_person'] == 3 || $flowinfo['status']['auto_person'] == 4) {
                    if (in_array($this->uid, $user)) {
                        $st = 1;
                    }
                }
                if ($flowinfo['status']['auto_person'] == 5) {
                    if (in_array(Token::getCurrentTokenVar('role'), $user)) {
                        $st = 1;
                    }
                }


                if ($st == 1) {
                    return 1;
                } else {
                    return 2;
                }
                break;
            default:
                return '';
        }
    }

    /**
     * 用户发起流程并进行初步审核
     * @param $wf_fid
     * @param string $wf_type
     * @return int
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function saveCheck($wf_fid, $wf_type)
    {
        $wf_id = $this->getWfId($wf_type);
        if (!$wf_id) {
            return -1;
        }
        //启动工作流
        $flow_date = [
            'wf_type' => $wf_type,
            'wf_id' => $wf_id,
            'wf_fid' => $wf_fid,
            'new_type' => 0,
            'check_con' => '同意',
        ];
        $res = $this->statr_save($flow_date);
        if (!$res == 1) {
            return -1;
        }

        $info = $this->getInfo($wf_fid, $wf_type);
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
            'wf_type' => $wf_type,
        ];
        $res = $this->check($data);
        return $res;
    }


    /**
     * 获取指定类别的流程id
     * @param $wf_type
     * @return int|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getWfId($wf_type)
    {
        $flow = Flow::where('type', $wf_type)
            ->where('status', '=', 0)
            ->find();
        if ($flow) {
            return $flow->id;
        }
        return -1;


    }

    /**
     * 审核流程
     * @param $data
     * @return int
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function check($data)
    {

        $workflow = new workflow();
        $submit_to_save = $data['submit_to_save'];
        if ($submit_to_save == 'cancel') {
            $this->flowCancel($data['wf_fid'], $data['wf_type']);
            return 1;

        } else {
            $workflow->workdoaction($data, Token::getCurrentUid());
            return 1;
        }

    }


    private function flowCancel($wf_fid, $wf_type)
    {
        $res = Db::name($wf_type)->where('id', $wf_fid)->update(['state' => 2]);
        if (!$res) {
            throw new FlowException(
                [
                    'code' => 401,
                    'msg' => '取消流程失败',
                    'errorCode' => 70002
                ]
            );
        }


    }

    /**
     * 流程未完成时-获取流程信息
     * @param $wf_fid
     * @param $wf_type
     * @return array
     */
    public function getInfo($wf_fid, $wf_type)
    {
        $info = ['wf_title' => input('wf_title'), 'wf_fid' => $wf_fid, 'wf_type' => $wf_type];
        $workflow = new workflow();
        $flowinfo = $workflow->workflowInfo($wf_fid, $wf_type);
        return $flowinfo;

    }

    /**
     * 流程完成式-获取流程信息
     * @param $wf_fid
     * @param $wf_type
     * @return array
     */
    public function getInfoForComplete($wf_fid, $wf_type)
    {
        $workflow = new workflow();
        $flowinfo = $workflow->workflowInfoForComplete($wf_fid, $wf_type);
        return $flowinfo;
    }

}