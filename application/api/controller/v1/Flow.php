<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/11
 * Time: 4:35 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\AdminT;
use app\api\model\DepartmentT;
use app\api\model\FeedbackT;
use app\api\model\LogT;
use app\api\model\Run;
use app\api\service\FlowService;
use app\api\service\RepairService;
use app\lib\exception\FlowException;
use app\lib\exception\MeetingException;
use app\lib\exception\SkuException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\TokenException;
use think\Db;
use workflow\workflow;

class Flow extends BaseController
{
    /**
     * @api {POST} /api/v1/flow/check/pass  35-CMS-流程审核-通过/不同意/取消
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  流程审核-通过（（repair=1 时：报修-电脑/打印机和报修-其他 用该接口 审核））
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
     *       "submit_to_save": "ok"
     *     }
     * @apiParam (请求参数说明) {String} check_con   审核意见
     * @apiParam (请求参数说明) {String} wf_type   流程类别：
     * 门禁申请：access_control_t；
     * 场地使用-文体活动：space_recreational_t；
     * 场地使用-功能室：space_multi_t；
     * 教育培训—会场预订:meetingplace_t;
     * 公务接待-围餐预定:meeting_recept_t;
     * 公务接待-自助餐预定:buffet_t;
     * 公务接待-酒店预定:hotel_t;
     * 公务用车:car_t
     * 用品借用:borrow_t
     * 用品领用:collar_use_t
     * 报修-电脑/打印机:repair_machine_t
     * 报修-其他:repair_other_t
     * @apiParam (请求参数说明) {String} submit_to_save   审批类别：ok：同意；back ：不同意；cancel:取消
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     */
    public function checkPass($flow_id, $flow_process, $npid, $run_id, $run_process, $wf_fid, $wf_type, $submit_to_save = 'ok', $check_con = "同意")
    {
        $params = $this->request->param();
        if (isset($params['borrow_return'])) {
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
                'wf_backflow' => $submit_to_save == 'ok' ? "" : 0,
                'wf_title' => 2,
                'wf_type' => $wf_type,
                'first' => 2,
                'borrow_return' => $params['borrow_return']
            ];
        } else
            if (isset($params['car_info'])) {
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
                    'wf_backflow' => $submit_to_save == 'ok' ? "" : 0,
                    'wf_title' => 2,
                    'wf_type' => $wf_type,
                    'first' => 2,
                    'car_info' => $params['car_info'],
                    'type' => $params['type'],
                    'driver' => $params['driver']
                ];

            } else {
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
                    'wf_backflow' => $submit_to_save == 'ok' ? "" : 0,
                    'wf_title' => 2,
                    'wf_type' => $wf_type,
                    'first' => 2,
                ];
            }
        $res = (new FlowService())->check($data);
        if ($res) {
            return json(new SuccessMessage());
        }
    }


    /**
     * @param $flow_id
     * @param $flow_process
     * @param $npid
     * @param $run_id
     * @param $run_process
     * @param $wf_fid
     * @param $wf_type
     * @param int $repair
     * @param int $type
     * @param string $feedback
     * @param string $imgs
     * @param string $submit_to_save
     * @param string $check_con
     * @return \think\response\Json
     * @throws TokenException
     * @throws \think\Exception
     * @api {POST} /api/v1/flow/check/pass/repair  85-报修流程审核-通过
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  报修流程审核-通过
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
     *       "repair": 2,
     *       "type": "a",
     *       "feedback": "已修好",
     *       "imgs": 1,2,3
     *     }
     * @apiParam (请求参数说明) {String} check_con   审核意见/默认同意
     * @apiParam (请求参数说明) {String} wf_type   流程类别
     * @apiParam (请求参数说明) {int} repair   由info接口返回：2 | 跟进人员处理操作；3 | 电工组选择跟进人员操作
     * @apiParam (请求参数说明) {int} type  跟进人员类别（repair=3时传入该参数：a-水电;b-电器维修;c-家具维修;d-门窗五金;e-电子设备）
     * @apiParam (请求参数说明) {String} feedback 反馈 （repair=2时传入该参数）
     * @apiParam (请求参数说明) {String} imgs 反馈图片 （repair=2时传入该参数）
     * 报修-电脑/打印机:repair_machine_t
     * 报修-其他:repair_other_t
     * @apiParam (请求参数说明) {String} submit_to_save   审批类别：ok：同意
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     */
    public function checkPassForRepair($flow_id, $flow_process, $npid, $run_id, $run_process,
                                       $wf_fid, $wf_type,
                                       $repair = 0, $type = "a", $feedback = '',
                                       $imgs = '', $submit_to_save = 'ok', $check_con = "同意")
    {
        if ($repair == 3) {
            $update_res = Db::name($wf_type)->where('id', $wf_fid)->update(['role' => $type]);
            if (!$update_res) {
                throw new FlowException(
                    [
                        'code' => 401,
                        'msg' => '流程审批失败：选择跟进人员处理失败',
                        'errorCode' => 70009
                    ]
                );
            }

            $nex = Db::name('flow_process')->find($flow_process);
            $nex_pid = explode(",", $nex['process_to']);
            $out_condition = json_decode($nex['out_condition'], true);
            if (count($nex_pid) >= 2) {
                //多个审批流
                foreach ($out_condition as $key => $val) {
                    $where = implode(",", $val['condition']);
                    //根据条件寻找匹配符合的工作流id
                    $info = Db::name($wf_type)->where($where)->where('id', 'eq', $wf_fid)->find();
                    if ($info) {
                        $npid = $key; //获得下一个流程的id
                        break;
                    }
                }
            }


        }

        if ($repair == 2) {
            //跟进人员处理
            $repair_type = $wf_type == "repair_machine_t" ? 1 : 2;
            $feedback_res = FeedbackT::create(['feedback' => $feedback, 'repair_id' => $wf_fid,
                'admin_id' => \app\api\service\Token::getCurrentUid(),
                'type' => $repair_type]);
            if (!$feedback_res) {
                throw new FlowException(
                    [
                        'code' => 401,
                        'msg' => '流程审批失败：处理反馈失败',
                        'errorCode' => 70009
                    ]
                );
            }

            //处理图片
            if (strlen($imgs)) {
                $relation = [
                    'name' => 'r_id',
                    'value' => $feedback_res->id
                ];
                $imgs_res = RepairService::saveImageRelationForFeedback($imgs, $relation, $repair_type);
                if (!$imgs_res) {
                    throw new FlowException(
                        ['code' => 401,
                            'msg' => '创建关联反馈关联失败',
                            'errorCode' => 60002
                        ]
                    );
                }

            }


        }


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
            'wf_backflow' => $submit_to_save == 'ok' ? "" : 0,
            'wf_title' => 2,
            'wf_type' => $wf_type,
            'first' => 2,
        ];
        $res = (new FlowService())->check($data);
        if ($res) {
            return json(new SuccessMessage());
        }


    }

    /**
     * @param $wf_fid
     * @param $wf_type
     * @return Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @api {GET} /api/v1/flow/info 34-获取预约申请—查看审核
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取预约申请—查看审核
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/flow/info?wf_fid=22&wf_type=access_control_t
     * @apiParam (请求参数说明) {String}  wf_fid  列表id
     * @apiParam (请求参数说明) {String} wf_type   流程类别：
     * 门禁申请：access_control_t；
     * 场地使用-文体活动：space_recreational_t；
     * 场地使用-功能室：space_multi_t；
     * 教育培训—会场预订:meetingplace_t;
     * 公务接待-围餐预定:meeting_recept_t;
     * 公务接待-自助餐预定:buffet_t;
     * 公务接待-酒店预定:hotel_t;
     * 公务用车:car_t
     * 用品借用:borrow_t
     * 用品领用:collar_use_t
     * 报修-电脑/打印机:repair_machine_t
     * 报修-其他:repair_other_t
     * @apiSuccessExample {json}返回样例:
     * {"check":1,"cancel":1,"repair":1,"info":{"sing_st":0,"flow_id":2,"run_id":12,"status":{"id":12,"uid":1,"run_id":12,"run_flow":2,"run_flow_process":4,"parent_flow":0,"parent_flow_process":0,"run_child":0,"remark":"","is_receive_type":0,"auto_person":5,"sponsor_text":"局长,普通员工,机服中心,机服主管局长,机服负责人,管理员,车队,部门主管局长,部门主管领导,部门局领导,部门负责人","sponsor_ids":"36,27,33,35,34,26,37,31,30,29,28","is_sponsor":0,"is_singpost":0,"is_back":0,"status":0,"js_time":1544516328,"bl_time":0,"jj_time":0,"is_del":0,"updatetime":0,"dateline":1544516328},"flow_process":4,"run_process":12,"flow_name":"门禁权限","process":{"id":4,"process_name":"申请","process_type":"is_one","process_to":"5","auto_person":5,"auto_sponsor_ids":"","auto_role_ids":"36,27,33,35,34,26,37,31,30,29,28","auto_sponsor_text":"","auto_role_text":"局长,普通员工,机服中心,机服主管局长,机服负责人,管理员,车队,部门主管局长,部门主管领导,部门局领导,部门负责人","range_user_ids":"","range_user_text":"","is_sing":1,"sign_look":0,"is_back":1,"todo":"局长,普通员工,机服中心,机服主管局长,机服负责人,管理员,车队,部门主管局长,部门主管领导,部门局领导,部门负责人"},"nexprocess":{"id":5,"process_name":"部门负责人审核","process_type":"is_step","process_to":"6","auto_person":5,"auto_sponsor_ids":"","auto_role_ids":"28","auto_sponsor_text":"","auto_role_text":"部门负责人","range_user_ids":"","range_user_text":"","is_sing":1,"sign_look":0,"is_back":1,"todo":"部门负责人"},"preprocess":["退回制单人修改"],"singuser":[{"id":1,"username":"朱明良","role":1},{"id":2,"username":"部门负责人","role":28},{"id":3,"username":"机服中心","role":33},{"id":4,"username":"机服中心负责人","role":34}],"log":[{"id":2,"uid":1,"from_id":22,"from_table":"access_control_t","run_id":12,"run_flow":0,"content":"同意","dateline":1544516328,"btn":"Send","art":"","user":"朱明良"}]}}
     * @apiSuccess (返回参数说明) {int} check 是否显示审批按钮 ：1 | 显示；2 | 不显示
     * @apiSuccess (返回参数说明) {int} cancel 是否有取消权限 ：1 | 有；2 | 无
     * @apiSuccess (返回参数说明) {int} repair 报修提交类型 ：1 | 无需特殊处理；2 | 跟进人员处理操作；3 | 电工组选择跟进人员操作
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
            $repair = 1;
            $cancel = 2;
            $info = (new FlowService())->getInfoForComplete($wf_fid, $wf_type);

        } else {
            $cancel = (new FlowService())->checkCancelAccess($wf_fid, $wf_type);
            $check_res = (new FlowService())->getFlowStatus($wf_fid, $wf_type);
            $check = $check_res['check'];
            $repair = $check_res['repair'];
            $info = $check_res['info'];
        }
        return json([
            'cancel' => $cancel,
            'check' => $check,
            'repair' => $repair,
            'info' => $info
        ]);


    }

    /**
     * @param $wf_type
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     * @api {GET} /api/v1/flow/complete 59-微信-我的-历史记录
     * @apiGroup  WX
     * @apiVersion 1.0.1
     * @apiDescription 微信-我的-历史记录
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/flow/complete?wf_type=access_control_t&page=1&size=10
     * @apiParam (请求参数说明) {String}  wf_type 流程类别：
     * 门禁申请：access_control_t；
     * 场地使用-文体活动：space_recreational_t；
     * 场地使用-功能室：space_multi_t；
     * 教育培训—会场预订:meetingplace_t;
     * 公务接待-围餐预定:meeting_recept_t;
     * 公务接待-自助餐预定:buffet_t;
     * 公务接待-酒店预定:hotel_t;
     * 公务用车:car_t
     * 报修-电脑/打印机:repair_machine_t
     * 报修-其他:repair_other_t
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json}返回样例:
     * {"total":1,"per_page":10,"current_page":1,"last_page":1,"data":[{"id":120,"from_table":"access_control_t","from_id":77,"uid":1,"status":0,"flow":{"id":77,"admin_id":1,"access":"301号科室","deadline":"2018-12-24 00:00:00","status":1,"create_time":"2018-12-23 17:56:23","update_time":"2018-12-23 17:56:23","user_type":"干部职工","state":1,"members":null,"source":"pc"},"process":[{"id":261,"uid":1,"from_id":77,"from_table":"access_control_t","run_id":120,"run_flow":0,"content":"同意","dateline":1545558984,"btn":"ok","art":"","admin":{"id":1,"username":"朱明良"}},{"id":262,"uid":2,"from_id":77,"from_table":"access_control_t","run_id":120,"run_flow":0,"content":"1","dateline":1545559027,"btn":"ok","art":"","admin":{"id":2,"username":"部门负责人"}},{"id":263,"uid":3,"from_id":77,"from_table":"access_control_t","run_id":120,"run_flow":0,"content":"2222","dateline":1545559041,"btn":"ok","art":"","admin":{"id":3,"username":"机服中心"}}]}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {Obj} flow 申请信息（具体字段含义参考对应申请信息）
     * @apiSuccess (返回参数说明) {Obj} process 申请流程信息（具体字段含义参考其他接口）
     */
    public function getComplete($wf_type, $page = 1, $size = 10)
    {
        $list = (new Run())->getComplete($wf_type, $page, $size);
        return json($list);

    }

    /**
     * @param $wf_type
     * @return \think\response\Json
     * @api {GET} /api/v1/flow/ready 60-微信-我的-待办
     * @apiGroup  WX
     * @apiVersion 1.0.1
     * @apiDescription 微信-我的-待办
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/flow/ready?wf_type=access_control_t
     * @apiParam (请求参数说明) {String}  wf_type 流程类别：
     * 门禁申请：access_control_t；
     * 场地使用-文体活动：space_recreational_t；
     * 场地使用-功能室：space_multi_t；
     * 教育培训—会场预订:meetingplace_t;
     * 公务接待-围餐预定:meeting_recept_t;
     * 公务接待-自助餐预定:buffet_t;
     * 公务接待-酒店预定:hotel_t;
     * 公务用车:car_t
     * 用品借用:borrow_t
     * 用品领用:collar_use_t
     * 报修:repair_t
     * @apiSuccessExample {json}返回样例:
     * [{"id":120,"from_table":"access_control_t","from_id":77,"uid":1,"status":0,"btn":"cancel","flow":{"id":77,"admin_id":1,"access":"301号科室","deadline":"2018-12-24 00:00:00","status":1,"create_time":"2018-12-23 17:56:23","update_time":"2018-12-23 17:56:23","user_type":"干部职工","state":1,"members":null,"source":"pc"},"process":[{"id":261,"uid":1,"from_id":77,"from_table":"access_control_t","run_id":120,"run_flow":0,"content":"同意","dateline":1545558984,"btn":"ok","art":"","admin":{"id":1,"username":"朱明良"}},{"id":262,"uid":2,"from_id":77,"from_table":"access_control_t","run_id":120,"run_flow":0,"content":"1","dateline":1545559027,"btn":"ok","art":"","admin":{"id":2,"username":"部门负责人"}},{"id":263,"uid":3,"from_id":77,"from_table":"access_control_t","run_id":120,"run_flow":0,"content":"2222","dateline":1545559041,"btn":"ok","art":"","admin":{"id":3,"username":"机服中心"}}]}]
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {String} btn cancel:显示取消按钮；check:显示审核按钮
     * @apiSuccess (返回参数说明) {Obj} flow 申请信息（具体字段含义参考对应申请信息）
     * @apiSuccess (返回参数说明) {Obj} process 申请流程信息（具体字段含义参考其他接口）
     */
    public function getReady($wf_type)
    {
        $list = (new Run())->getReady($wf_type);
        return json($list);
    }


}