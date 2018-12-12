<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/11
 * Time: 11:38 AM
 */

namespace app\api\service;


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

    public function btn($wf_fid, $wf_type, $status)
    {
        $url = url("/index/flow/do_check/", ["wf_type" => $wf_type, "wf_title" => '2', 'wf_fid' => $wf_fid]);
        $url_star = url("/index/flow/start/", ["wf_type" => $wf_type, "wf_title" => '2', 'wf_fid' => $wf_fid]);

        $id = Db::name('log')->insertGetId(['msg' => $wf_fid . '-' . $status]);

        switch ($status) {
            case 0:
                return 2;
                //return '<span class="btn  radius size-S" onclick=layer_show(\'发起工作流\',"' . $url_star . '","450","350")>发起工作流</span>';
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
                    // return '<span class="btn  radius size-S" onclick=layer_show(\'审核\',"'.$url.'","850","650")>审核</span>';
                } else {
                    return 2;// '<span class="btn  radius size-S">无权限</span>';
                }
                break;
            default:
                return '';
        }
    }

    public function check($data)
    {
        $workflow = new workflow();
        $flowinfo = $workflow->workdoaction($data, Token::getCurrentUid());

        return 1;
    }

    public function getInfo($wf_fid, $wf_type)
    {
        $info = ['wf_title' => input('wf_title'), 'wf_fid' => $wf_fid, 'wf_type' => $wf_type];
        $workflow = new workflow();
        $flowinfo = $workflow->workflowInfo($wf_fid, $wf_type);
        return $flowinfo;

    }


    public function getInfoForComplete($wf_fid, $wf_type)
    {
        $workflow = new workflow();
        $flowinfo = $workflow->workflowInfoForComplete($wf_fid, $wf_type);
        return $flowinfo;
    }

}