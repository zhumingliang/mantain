<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/7
 * Time: 11:03 AM
 */

namespace app\api\controller\v1;


use app\api\service\MsgService;
use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {
        $nex = Db::name('flow_process')->find(68);
        $nex_pid = explode(",", $nex['process_to']);
        $out_condition = json_decode($nex['out_condition'], true);
        if (count($nex_pid) >= 2) {
            //多个审批流
            foreach ($out_condition as $key => $val) {
                $where = implode(",", $val['condition']);
                //根据条件寻找匹配符合的工作流id
                $info = Db::name("repair_machine_t")->where($where)->where('id', 'eq', 15)->find();
                if ($info) {
                    $nexprocessid = $key; //获得下一个流程的id
                    echo $nexprocessid;
                    break;
                }
            }
        }

        //(new MsgService())->sendMsg("ZhuMingLiang", "测试");
    }

}