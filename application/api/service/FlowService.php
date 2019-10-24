<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/11
 * Time: 11:38 AM
 */

namespace app\api\service;


use app\api\model\AccessControlT;
use app\api\model\AdminT;
use app\api\model\BorrowT;
use app\api\model\BuffetT;
use app\api\model\CarT;
use app\api\model\CollarUseT;
use app\api\model\Flow;
use app\api\model\FlowProcess;
use app\api\model\HotelT;
use app\api\model\MeetingReceptT;
use app\api\model\RepairV;
use app\api\model\Run;
use app\api\model\RunProcess;
use app\common\controller\Admin;
use app\lib\enum\CommonEnum;
use app\lib\exception\FlowException;
use think\Db;
use workflow\workflow;

class FlowService
{
    private $repair_role = [
        '水电', '电器维修', '家具维修', '门窗五金', '电子设备'
    ];

    private $select_role = [
        '信息中心', '电工组组长'
    ];

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
     * @param $wf_fid
     * @param $wf_type
     * @param $status
     * @return array|int
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function btn($wf_fid, $wf_type, $status)
    {
        switch ($status) {
            case 0:
                return [
                    'btn' => 2,
                    'repair' => 1
                ];
                break;
            case 1:
                $repair = 1;
                $workflow = new workflow();
                $flowinfo = $workflow->workflowInfo($wf_fid, $wf_type);
                $role = Token::getCurrentTokenVar('role');
                $user = explode(",", $flowinfo['status']['sponsor_ids']);
                $user_text = $flowinfo['status']['sponsor_text'];
                if ($flowinfo['status']['auto_person'] == 3 || $flowinfo['status']['auto_person'] == 4) {
                    if (in_array(Token::getCurrentUid(), $user)) {
                        //$st = 1;
                        return [
                            'btn' => 1,
                            'repair' => $repair
                        ];
                    }
                }
                if ($flowinfo['status']['auto_person'] == 5) {
                    if (in_array($role, $user)) {
                        if (in_array($user_text, $this->repair_role)) {
                            $repair = 2;
                            //跟进人员

                        } else if (in_array($user_text, $this->select_role)) {
                            $repair = 3;
                            //信息中心/电工组组长人员
                        }
                    }
                    return [
                        'btn' => 1,
                        'repair' => $repair
                    ];

                }


                return [
                    'btn' => 2,
                    'repair' => $repair
                ];
                break;
            default:
                return [
                    'btn' => 2,
                    'repair' => 1
                ];
        }
    }

    /**
     * 获取指定流程信息
     * @param $wf_fid
     * @param $wf_type
     * @return array
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function getFlowStatus($wf_fid, $wf_type)
    {
        $workflow = new workflow();
        $flowinfo = $workflow->workflowInfo($wf_fid, $wf_type);
        $checkRole = $this->getCheckRole($flowinfo);
        return [
            'check' => $checkRole['check'],
            'repair' => $checkRole['repair'],
            'info' => $flowinfo,
        ];

    }

    /**
     * 获取当前用户对流程处理权限
     * @param $flowinfo
     * @return array
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    private function getCheckRole($flowinfo)
    {
        $repair = 1;
        $role = Token::getCurrentTokenVar('role');
        $user = explode(",", $flowinfo['status']['sponsor_ids']);
        $user_text = $flowinfo['status']['sponsor_text'];
        if ($flowinfo['status']['auto_person'] == 3 || $flowinfo['status']['auto_person'] == 4) {
            if (in_array(Token::getCurrentUid(), $user)) {
                return [
                    'check' => 1,
                    'repair' => $repair
                ];
            }
        }
        if ($flowinfo['status']['auto_person'] == 5) {
            if (in_array($role, $user)) {
                if (in_array($user_text, $this->repair_role)) {
                    $repair = 2;
                    //跟进人员

                } else if (in_array($user_text, $this->select_role)) {
                    $repair = 3;
                    //信息中心/电工组组长人员
                }

                return [
                    'check' => 1,
                    'repair' => $repair
                ];
            }

        }

        return [
            'check' => 2,
            'repair' => $repair
        ];
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
            'check_con' => '发起申请成功',
        ];
        $res = $this->statr_save($flow_date);
        if (!($res == 1)) {
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
            'first' => 1
        ];
        $res = $this->check($data);
        if ($wf_type == "borrow_t") {
            $this->updateFlowForBorrow($wf_id);
        }
        return $res;
    }

    private function updateFlowForBorrow($flow_id)
    {
        $data = [
            'auto_sponsor_ids' => Token::getCurrentUid(),
            'auto_sponsor_text' => Token::getCurrentTokenVar('username')
        ];
        FlowProcess::update($data, ['flow_id' => $flow_id, 'auto_person' => 4]);

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
            $this->flowCancel($data['wf_fid'], $data['wf_type'], $data['run_id']);
            return 1;
        } else {
            if ($data['first'] != 1) {
                $this->checkAccess($data['run_id'], $data['wf_type'], $data['wf_fid']);
            }
            if (isset($data['borrow_return'])) {
                BorrowT::update(['borrow_return' => $data['borrow_return']], ['id' => $data['wf_fid']]);
                unset($data['borrow_return']);
            }
            if (isset($data['car_info'])) {
                CarT::update(['car_info' => $data['car_info'], 'type' => $data['type'], 'driver' => $data['driver']], ['id' => $data['wf_fid']]);
                unset($data['car_info']);
                unset($data['type']);
                unset($data['driver']);
            }
            $workflow->workdoaction($data, Token::getCurrentUid());
            //检测并发送数据
            $this->checkComplete($data['npid'], $data['wf_type'], $data['wf_fid'], $data['run_id']);
            return 1;
        }
    }


    public function checkListStatus($list, $wf_type, $repair = false, $sku = false)
    {
        $data = $list['data'];
        if (!count($data)) {
            return $list;
        }
        foreach ($data as $k => $v) {
            if ($repair || $sku) {
                if ($repair) {
                    $data[$k]['check'] = $this->checkInfoStatus($v['id'], $v['wf_type']);
                }
                if ($sku) {
                    $data[$k]['check'] = $this->checkInfoStatus($v['id'], $v['type']);
                }
            } else {
                $data[$k]['check'] = $this->checkInfoStatus($v['id'], $wf_type);
            }
        }
        $list['data'] = $data;
        return $list;

    }


    /**
     * 检测指定流程是否需要审批
     * @param $wf_fid
     * @param $wf_type
     * @return int|mixed
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function checkInfoStatus($wf_fid, $wf_type)
    {
        //检查流程是否走完
        $result = Db::name('run')->where('from_id', 'eq', $wf_fid)
            ->where('from_table', 'eq', $wf_type)
            ->where('is_del', 'eq', 0)
            ->find();
        if ($result['status'] == 1) {
            //已经完成审核
            return 2;
        }
        $check_res = $this->getFlowStatus($wf_fid, $wf_type);
        return $check_res['check'];

    }


    private function checkComplete($npid, $wf_type, $wf_fid, $run_id)
    {
        if ($npid == '') {
            if ($wf_type == "meeting_recept_t") {
                $this->sendMsgForRecept($wf_fid);
            } else
                if ($wf_type == "car_t") {
                    $this->sendMsgForCar($wf_fid);
                } else
                    if ($wf_type == "repair_machine_t" || $wf_type == "repair_other_t") {
                        $this->sendMsgForRepair($wf_fid, $wf_type, $run_id);
                    } else
                        if ($wf_type == "access_control_t") {
                            $this->sendMsgForAccess($wf_fid);
                        } else
                            if ($wf_type == "space_recreational_t" || $wf_type == "space_multi_t" ||
                                $wf_type == "meetingplace_t") {
                                $this->sendMsgForBookingPlace($wf_type, $wf_fid);

                            } else if ($wf_type == "buffet_t" || $wf_type == "collar_use_t" || $wf_type == "borrow_t" || $wf_type == "hotel_t") {
                                $this->sendMsgForDinnerAndProductAndHotel($wf_type, $wf_fid);
                            }

        }

    }

    //围餐预订、自助餐预订、物品领用、物品借用--推给凌丽珠
    private function sendMsgForDinnerAndProductAndHotel($wf_type, $wf_fid)
    {
        if ($wf_type == "buffet_t") {
            $info = BuffetT::info($wf_fid);
            if ($info) {
                $msg = "%s于%s申请自助餐,公务时间:%s-%s,来访单位：%s；公务活动项目:%s";
                $msg = sprintf($msg, $info->admin->username, $info->create_time, $info->time_begin, $info->time_end,
                    $info->unit, $info->project);
                $msg .= "请机服中心相关人员进行跟进。";
            }
        } else if ($wf_type == "collar_use_t") {
            $info = CollarUseT::info($wf_fid);
            if ($info) {
                $msg = "%s于%s发起%s申请";
                $msg = sprintf($msg, $info->admin->username, $info->create_time, "领用");
                $msg .= "请机服中心相关人员进行跟进。";
            }
        } else if ($wf_type == "hotel_t") {
            $info = HotelT::info($wf_fid);
            if ($info) {
                $msg = "来访单位： %s拟入住酒店： %s";
                $msg = sprintf($msg, $info->admin->username, $info->create_time, "领用");
                $msg .= "请机服中心相关人员进行跟进。";
            }
        } else {
            $info = BorrowT::info($wf_fid);
            if ($info) {
                $msg = "%s于%s发起%s申请";
                $msg = sprintf($msg, $info->admin->username, $info->create_time, "借用");
                $msg .= "请机服中心相关人员进行跟进。";
            }
        }

        //$user = AdminT::getUserIdWithUserName("凌丽珠");
        $user = AdminT::getUserIdWithUserName("黄锐芝");
        if (strlen($user)) {
            (new MsgService())->sendMsg($user, $msg);
        }


    }

    //预定场地-发送信息给指定用户：李景文,凌丽珠
    private function sendMsgForBookingPlace($wf_type, $wf_fid)
    {
        $info = Db::table('maintain_' . $wf_type)->where('id', $wf_fid)->find();
        if ($info) {
            $admin = AdminT::get($info['admin_id']);
            $msg = "%s于%s申请场地:%s。";
            $msg = sprintf($msg, $admin->username, $info['create_time'], $wf_type == "meetingplace_t" ? $info['space'] : $info['place']);
            $msg .= "请机服中心相关人员进行跟进。";
            // $user = AdminT::getUserIdWithUserName("李景文,凌丽珠");
            $user = AdminT::getUserIdWithUserName("黄锐芝");
            if (strlen($user)) {
                (new MsgService())->sendMsg($user, $msg);
            }

        }
    }

    /**
     * 权限控制-发送通知
     */
    private function sendMsgForAccess($wf_fid)
    {
        $info = AccessControlT::get($wf_fid);
        $user = AdminT::get($info->admin_id);
        $msg = "%s的%s于%s申请开通的功能为:%s，开通人员类型是：%s，工作截止时间为:%s，名单有：%s，请负责门禁系统的人员及时为其开通相关权限。";
        $msg = sprintf($msg, $user->department, $user->username, $info->create_time, $info->access, $info->user_type,
            $info->deadline, $info->members);
        $users = AdminService::getUserIdWithRole("门禁权限管理员");
        if (strlen($users)) {
            (new MsgService())->sendMsg($users, $msg);
        }

        //发送给张海滨,凌丽珠
        $msg .= "请机服中心相关人员进行跟进。";
        // $user = AdminT::getUserIdWithUserName("张海滨,凌丽珠");
        $user = AdminT::getUserIdWithUserName("黄锐芝");
        if (!empty($user)) {
            (new MsgService())->sendMsg($user, $msg);
        }
    }

    /**
     * 围餐流程走完给厨房发送通知
     */
    private function sendMsgForRecept($wf_fid)
    {
        $info = MeetingReceptT::get($wf_fid);
        $msg = "%s于%s在我局进行围餐预订，就餐总人数为%s人，陪同人员有:%s，请相关饭堂人员提前备餐。";
        $msg = sprintf($msg, $info->unit, $info->meal_time, $info->meeting_count + $info->accompany_count, $info->accompany);
        $users = AdminService::getUserIdWithRole("厨房");
        (new MsgService())->sendMsg($users, $msg);

        //推送给凌丽珠
        $msg .= "请机服中心相关人员进行跟进。";
        // $user = AdminT::getUserIdWithUserName("凌丽珠");
        $user = AdminT::getUserIdWithUserName("黄锐芝");
        if (!empty($user['user_id'])) {
            (new MsgService())->sendMsg($user['user_id'], $msg);
        }
    }

    /**
     * 结束流程给申请用车用户发送信息
     */
    private function sendMsgForCar($wf_fid)
    {
        $info = CarT::get($wf_fid);
        $msg = "您申请的%s出发的用车已通过审核，车辆信息为:%s，%s，请提前联系司机出车。";
        $msg = sprintf($msg, $info->create_time, $info->car_info, $info->driver);
        $users = AdminService::getUserIdWithID($info->admin_id);
        if (strlen($users)) {
            (new MsgService())->sendMsg($users, $msg);
        }

        //发送给李康,凌丽珠
        $msg .= "请机服中心相关人员进行跟进。";
        // $user = AdminT::getUserIdWithUserName("李康,凌丽珠");
        $user = AdminT::getUserIdWithUserName("黄锐芝");
        if (!empty($user)) {
            (new MsgService())->sendMsg($user, $msg);
        }
    }

    /**
     * 报修流程-发送给每一个用户推送信息
     */
    private function sendMsgForRepair($wf_fid, $wf_type, $run_id)
    {
        $info = RepairV::where('wf_type', $wf_type)->where('id', $wf_fid)->find();
        $msg = "%s的%s于%s报修的在%s的%s已处理，反馈结果为:%s。";
        $msg = sprintf($msg, $info->department, $info->username, $info->create_time, $info->address, $info->name, $info->feedback);
        $users = AdminService::getUserIdWithRunProcess($run_id);
        if (strlen($users)) {
            (new MsgService())->sendMsg($users, $msg);
        }

        //推送给张灵学,凌丽珠
        $msg .= "请机服中心相关人员进行跟进。";
        // $user = AdminT::getUserIdWithUserName("张灵学,凌丽珠");
        $user = AdminT::getUserIdWithUserName("黄锐芝");
        if (strlen($user)) {
            (new MsgService())->sendMsg($user, $msg);
        }

    }

    /* private function sendMsgToUser($wf_type)
     {
         if ($wf_type==""){

         }
     }*/

    /**
     * @param $run_id
     * @param $wf_type
     * @param $wf_fid
     * @throws FlowException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function checkAccess($run_id, $wf_type, $wf_fid)
    {
        $info = RunProcess::where('run_id', $run_id)
            ->where('status', '=', 0)
            ->find();
        if (!$info) {
            throw new FlowException(
                [
                    'code' => 401,
                    'msg' => '流程审批失败：流程已完成，不能再次审核',
                    'errorCode' => 70008
                ]
            );
        }
        if ($info->auto_person == 5) {
            $roles = explode(',', $info->sponsor_ids);
            $role = Token::getCurrentTokenVar('role');
            if (!in_array($role, $roles)) {
                throw new FlowException(
                    [
                        'code' => 401,
                        'msg' => '流程审批失败：当前审批步骤已被审核，无需再次审核',
                        'errorCode' => 70009
                    ]
                );

            }
        }


        if ($wf_type == "borrow_t" && $info->auto_person == 3 && ($info->sponsor_ids == Token::getCurrentUid())) {
            //借用修改归还时间
            BorrowT::update(['actual_time' => date("Y-m-d H:i:s")], ['id' => $wf_fid]);
        }
    }

    /**
     * 取消流程
     * @param $wf_fid
     * @param $wf_type
     * @param $run_id
     * @throws FlowException
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    private function flowCancel($wf_fid, $wf_type, $run_id)
    {
        $res = Db::name($wf_type)->where('id', $wf_fid)->update(['state' => 2]);
        if (!$res) {
            throw new FlowException(
                [
                    'code' => 401,
                    'msg' => '取消流程申请失败',
                    'errorCode' => 70002
                ]
            );
        }
        $run_res = Run::update(['is_del' => CommonEnum::COMPLETE], ['id' => $run_id]);
        if (!$run_res) {
            throw new FlowException(
                [
                    'code' => 401,
                    'msg' => '取消流程失败',
                    'errorCode' => 70003
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

    /**
     * 判断当前用户有无取消流程权限
     * @param $wf_fid
     * @param $wf_type
     * @return int
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkCancelAccess($wf_fid, $wf_type)
    {
        $u_id = Token::getCurrentUid();
        $count = Db::name($wf_type)->where('id', 'eq', $wf_fid)
            ->where('admin_id', 'eq', $u_id)
            ->find();
        return $count ? 1 : 2;


    }

}