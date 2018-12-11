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
    public function check()
    {
        //wf_type/access_control_t/wf_title/2/wf_fid/22
        $data = [
            'wf_type' => "access_control_t",
            'wf_title' => 2,
            'wf_fid' => 22
        ];
        $res = (new FlowService())->check($data);
        echo $res;

    }

    public function getInfo($wf_fid=22,$wf_type="access_control_t")
    {
        $info = ['wf_title' => input('wf_title'), 'wf_fid' => $wf_fid, 'wf_type' => $wf_type];

        $workflow = new workflow();
        $flowinfo = $workflow->workflowInfo($wf_fid, $wf_type);

        print_r($flowinfo);

    }

}