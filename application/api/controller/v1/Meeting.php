<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/3
 * Time: 10:28 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\AdminT;
use app\api\model\MeetingRoomT;
use app\api\model\MeetingT;
use app\api\service\MeetingService;
use app\api\service\MsgService;
use app\api\validate\MeetingValidate;
use app\api\service\Token as TokenService;
use app\index\controller\News;
use app\lib\enum\CommonEnum;
use app\lib\exception\MeetingException;
use app\lib\exception\OperationException;
use app\lib\exception\SuccessMessage;


class Meeting extends BaseController
{
    /**
     * @api {POST} /api/v1/meeting/save  6-CMS-新增会议
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  新增会议
     * @apiExample {post}  请求样例:
     *    {
     *       "meeting_date": "2018-12-30",
     *       "address": "101会议室",
     *       "card": "a1",
     *       "time_begin": "2018-12-30 09：00",
     *       "time_end": "2018-12-30 09：30",
     *       "meeting_begin": "2018-12-30 10：00",
     *       "meeting_end": "2018-12-30 12：00",
     *       "theme": "全体职工大会",
     *       "outline": "年终总结",
     *       "remark": "必须参加",
     *       "host": "办公室",
     *       "push": "稽查局,财务管理科"
     *
     *     }
     * @apiParam (请求参数说明) {String} meeting_date   日期
     * @apiParam (请求参数说明) {String} address   签到地点
     * @apiParam (请求参数说明) {String} card   会议室签到机编号
     * @apiParam (请求参数说明) {String} time_begin   签到开始时间
     * @apiParam (请求参数说明) {String} time_end   签到截止时间
     * @apiParam (请求参数说明) {String} meeting_begin   会议开始时间
     * @apiParam (请求参数说明) {String} meeting_begin   会议截止时间
     * @apiParam (请求参数说明) {String} theme   申请使用是由
     * @apiParam (请求参数说明) {String} host   主办部门
     * @apiParam (请求参数说明) {String} push   推送部门：多个用逗号隔开
     * @apiParam (请求参数说明) {String} remark   备注
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     */
    public function save()
    {
        (new MeetingValidate())->goCheck();
        $admin_id = TokenService::getCurrentUid();
        $params = $this->request->param();
        $params['admin_id'] = $admin_id;
        $params['state'] = CommonEnum::STATE_IS_OK;
        $params['source'] = TokenService::getCurrentTokenVar('category');
        $id = MeetingT::create($params);
        if (!$id) {
            throw new OperationException();
        }
        $username = \app\api\service\Token::getCurrentTokenVar('username');
        $msg = "%s于%s发起会议申请\n，会议主题:%s;\n会议地点:%s；\n会议时间:%s-%s。\n";
        $msg = sprintf($msg, $username, date('Y-m-d H:i:s'), $params['theme'], $params['address'], $params['meeting_begin'], $params['meeting_end']);
        $msg .= "请机服中心相关人员进行跟进。";
       // $user = AdminT::getUserIdWithUserName("黄锐芝");
        $user = AdminT::getUserIdWithUserName("李景文,凌丽珠");
        if (!empty($user)) {
            (new MsgService())->sendMsg($user, $msg);
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/meeting/update  7-CMS-修改会议
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  修改会议
     * @apiExample {post}  请求样例:
     *    {
     *       "id": "1",
     *       "meeting_date": "2018-12-30",
     *       "address": "101会议室",
     *       "card": "a1",
     *       "time_begin": "2018-12-30 09：00",
     *       "time_end": "2018-12-30 09：30",
     *       "meeting_begin": "2018-12-30 10：00",
     *       "theme": "全体职工大会"
     *       "remark": "必须参加",
     *       "host": "办公室",
     *       "push": "稽查局,财务管理科",
     *     }
     * @apiParam (请求参数说明) {int} id   会议id
     * @apiParam (请求参数说明) {String} meeting_date   日期
     * @apiParam (请求参数说明) {String} address   签到地点
     * @apiParam (请求参数说明) {String} card   会议室签到机编号
     * @apiParam (请求参数说明) {String} time_begin   签到开始时间
     * @apiParam (请求参数说明) {String} time_end   签到截止时间
     * @apiParam (请求参数说明) {String} meeting_begin   会议开始时间
     * @apiParam (请求参数说明) {String} theme  申请使用是由
     * @apiParam (请求参数说明) {String} host   主办部门
     * @apiParam (请求参数说明) {String} push   推送部门：多个用逗号隔开
     * @apiParam (请求参数说明) {String} remark   备注
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     */
    public function update($id)
    {
        TokenService::getCurrentUid();
        (new MeetingValidate())->goCheck();
        $params = $this->request->param();
        $id = MeetingT::update($params, ['id' => $id]);
        if (!$id) {
            throw new OperationException(
                [
                    'code' => 401,
                    'msg' => '更新操作失败',
                    'errorCode' => 40002
                ]
            );
        }
        return json(new SuccessMessage());
    }

    /**
     * @api {POST} /api/v1/meeting/delete  8-CMS-删除会议
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  删除会议
     * @apiExample {post}  请求样例:
     *    {
     *       "id": "1"
     *     }
     * @apiParam (请求参数说明) {int} id   会议id
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     */
    public function delete($id)
    {
        TokenService::getCurrentUid();
        $id = MeetingT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $id]);
        if (!$id) {
            throw new OperationException(
                [
                    'code' => 401,
                    'msg' => '删除操作失败',
                    'errorCode' => 40003
                ]
            );
        }
        return json(new SuccessMessage());
    }

    /**
     * @api {POST} /api/v1/meeting/sign/in  29-CMS-会议签到
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  会议签到
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 13,
     *       "mobile": 18956225230,
     *       "card": "a1"
     *     }
     * @apiParam (请求参数说明) {int} id  会议id
     * @apiParam (请求参数说明) {String} card  考勤机设备号
     * @apiParam (请求参数说明) {String} mobile  用户手机号
     * @apiSuccessExample {json} 返回样例:
     * {"all":33,"sign_in":1}
     * @apiSuccess (返回参数说明) {int} all 应签到人数
     * @apiSuccess (返回参数说明) {int} sign_in 实签到人数
     */
    public function signIn($id, $card, $mobile)
    {
        $info = (new MeetingService())->signIn($id, $card, $mobile);
        return json($info);
    }

    /**
     * @api {GET} /api/v1/meeting/list 30-会议列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  会议列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/meeting/list?address=全部&time_begin=2018-10-01&time_end=2018-12-31&theme=全体会议&page=1&size=20&host=办公室
     * @apiParam (请求参数说明) {String}  address 签到地点
     * @apiParam (请求参数说明) {String}  theme 会议主题
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {String}  host 部门/默认传入"全部"
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json}返回样例:
     * {"total":1,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":1,"meeting_date":"2018-12-10","address":"101会议室","time_begin":"2018-12-09 18:39:13","time_end":"2018-12-09 19:39:27","meeting_begin":"2018-12-09 09:30:00","theme":"全体会议","outline":"年终总结","create_time":"2018-12-03 10:56:47","update_time":"2018-12-03 10:56:47","remark":"全体都有","state":0}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 会议id
     * @apiSuccess (返回参数说明) {String} create_time 日期
     * @apiSuccess (返回参数说明) {String} theme 申请使用是由
     * @apiSuccess (返回参数说明) {String} address 签到地点
     * @apiSuccess (返回参数说明) {String} time_begin 签到开始时间
     * @apiSuccess (返回参数说明) {String} time_end 签到截止时间
     * @apiSuccess (返回参数说明) {String} meeting_begin   签到截止时间
     * @apiSuccess (返回参数说明) {String} host   主办部门
     * @apiSuccess (返回参数说明) {String} push   推送部门
     * @apiSuccess (返回参数说明) {String} remark   备注
     */
    public function getMeetingList($time_begin, $time_end, $address = "全部", $theme = "全部", $host = "全部", $page = 1, $size = 20)
    {
        TokenService::getCurrentUid();
        $list = (new MeetingService())->getMeetingList($time_begin, $time_end, $address, $theme, $page, $size, $host);
        return json($list);
    }

    /**
     * @param $time_begin
     * @param $time_end
     * @param $address
     * @param $theme
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @api {GET} /api/v1/meeting/export 31-会议-导出报表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 会议-导出报表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/meeting/export?address=全部&time_begin=2018-10-01&time_end=2018-12-31&theme=全体会议&host=办公室
     * @apiParam (请求参数说明) {String}  address 签到地点
     * @apiParam (请求参数说明) {String}  theme 会议主题
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {String}  host 部门/默认传入"全部"
     */
    public function exportMeeting($time_begin, $time_end, $address, $theme)
    {
        TokenService::getCurrentUidWithGet();
        (new MeetingService())->exportMeeting($time_begin, $time_end, $address, $theme);
        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/meeting/sign/in/list 32-会议签到列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  会议签到列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/meeting/sign/in/list?address=全部&time_begin=2018-10-01&time_end=2018-12-31&theme=全体会议&page=1&size=20&department=全部&username=朱明良
     * @apiParam (请求参数说明) {String}  address 签到地点
     * @apiParam (请求参数说明) {String}  theme 会议主题
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {String}  department 部门/默认传入全部
     * @apiParam (请求参数说明) {String}  username 申请人/默认传入全部
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json}返回样例:
     * {"total":1,"per_page":"20","current_page":1,"last_page":1,"data":[{"create_time":"2018-12-09","username":"朱明良","phone":"18956225230","department":"办公室","address":"101会议室","sign_time":"2018-12-09 18:40:09","theme":"全体会议","remark":"全体都有"}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 会议id
     * @apiSuccess (返回参数说明) {String} create_time 日期
     * @apiSuccess (返回参数说明) {String} username 姓名
     * @apiSuccess (返回参数说明) {String} phone 手机号
     * @apiSuccess (返回参数说明) {String} department 部门
     * @apiSuccess (返回参数说明) {String} theme 会议主题
     * @apiSuccess (返回参数说明) {String} sign_time 签到时间
     * @apiSuccess (返回参数说明) {String} address 签到地点
     * @apiSuccess (返回参数说明) {String} remark   备注
     */
    public function getSignInList($time_begin, $time_end,
                                  $department, $username,
                                  $address, $theme, $page = 1, $size = 20)
    {
        TokenService::getCurrentUid();
        $list = (new MeetingService())->getSignInList($time_begin, $time_end,
            $department, $username,
            $address, $theme, $page, $size);
        return json($list);
    }

    /**
     * @param $time_begin
     * @param $time_end
     * @param $department
     * @param $username
     * @param $address
     * @param $theme
     * @return \think\response\Json
     * @api {GET} /api/v1/meeting/sign/in/export 33-会议签到列表-导出
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  会议签到列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/meeting/sign/in/export?address=全部&time_begin=2018-10-01&time_end=2018-12-31&theme=全体会议&department=全部&username=朱明良
     * @apiParam (请求参数说明) {String}  address 签到地点
     * @apiParam (请求参数说明) {String}  theme 会议主题
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {String}  department 部门/默认传入全部
     * @apiParam (请求参数说明) {String}  username 申请人/默认传入全部
     */

    public function exportSignIn($time_begin, $time_end,
                                 $department, $username,
                                 $address, $theme)
    {
        TokenService::getCurrentUidWithGet();
        (new MeetingService())->exportSignIn($time_begin, $time_end,
            $department, $username,
            $address, $theme);
        return json(new SuccessMessage());

    }

    /**
     * @param $card
     * @return \think\response\Json
     * @throws MeetingException
     * @api {GET} /api/v1/meeting/info 43-获取指定会议室会议信息
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取指定会议室会议信息
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/meeting/info?card=a1
     * @apiParam (请求参数说明) {String}  card  签到机设备号
     * @apiSuccessExample {json}返回样例:
     * {"id":13,"time_begin":"18:01","time_end":"23:01","meeting_begin":"18:01","push":"办公室","all":18,"sign_in":0}
     * @apiSuccess (返回参数说明) {int} id 会议id
     * @apiSuccess (返回参数说明) {String} time_begin 签到开始时间
     * @apiSuccess (返回参数说明) {String} time_end 签到截止时间
     * @apiSuccess (返回参数说明) {String} meeting_begin 会议开始时间
     * @apiSuccess (返回参数说明) {int} all 需要签到总人数
     * @apiSuccess (返回参数说明) {int} sign_in 已经签到人数
     *
     */
    public function getInfoForMC($card)
    {
        $info = (new MeetingService())->getInfoForMC($card);
        return json($info);

    }

    /**
     * @param $meeting_date
     * @param $page
     * @param $size
     * @param $meeting_date
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     * @api {GET} /api/v1/meeting/sign/in/list/wx 49-微信端-获取会议签到列表
     * @apiGroup  WX
     * @apiVersion 1.0.1
     * @apiDescription  会议签到列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/meeting/sign/in/list/wx?meeting_date=2018-10-01&page=1&size=20
     * @apiParam (请求参数说明) {String}  meeting_date 会议时间
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json}返回样例:
     * {"total":1,"per_page":"20","current_page":1,"last_page":1,"data":[{"meeting_date":"2018-12-20","theme":"test","sign_time":"2018-12-19 15:41:19","time_begin":"2018-12-19 15:00:00","time_end":"2018-12-19 19:30:00","meeting_begin":"2018-12-20 09:30:00"}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页
     * @apiSuccess (返回参数说明) {String} sign_time 签到时间
     * @apiSuccess (返回参数说明) {String} meeting_date 会议日期
     * @apiSuccess (返回参数说明) {String} meeting_begin 会议开始时间
     * @apiSuccess (返回参数说明) {String} theme 会议主题
     * @apiSuccess (返回参数说明) {String} time_begin 签到开始时间
     * @apiSuccess (返回参数说明) {String} time_end 签到截止时间
     */
    public function getSignInListForWx($meeting_date, $page = 1, $size = 10)
    {
        TokenService::getCurrentUid();
        $list = (new MeetingService())->getSignInListForWx($meeting_date, $page, $size);
        return json($list);

    }

    /**
     * @param $id
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @api {GET} /api/v1/meeting 50-获取指定会议室会议信息
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取指定会议室会议信息
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/meeting?id=1
     * @apiParam (请求参数说明) {String}  id  会议id
     * @apiSuccessExample {json}返回样例:
     * {"id":8,"meeting_date":"2018-12-20","address":"202会议室","time_begin":"2018-12-19 15:00:00","time_end":"2018-12-19 19:30:00","theme":"test","outline":"test","create_time":"2018-12-19 09:08:18","update_time":"2018-12-19 09:08:18","remark":"test","state":1,"meeting_end":"2018-12-20 12:00:00","meeting_begin":"2018-12-20 09:30:00","card":"a1","host":null,"push":null}
     * @apiSuccess (返回参数说明) {int} id 会议id
     * @apiSuccess (返回参数说明) {String} meeting_date   日期
     * @apiSuccess (返回参数说明) {String} address   签到地点
     * @apiSuccess (返回参数说明) {String} card   会议室签到机编号
     * @apiSuccess (返回参数说明) {String} time_begin   签到开始时间
     * @apiSuccess (返回参数说明) {String} time_end   签到截止时间
     * @apiSuccess (返回参数说明) {String} meeting_begin   会议开始时间
     * @apiSuccess (返回参数说明) {String} meeting_begin   会议截止时间
     * @apiSuccess (返回参数说明) {String} theme   会议主题
     * @apiSuccess (返回参数说明) {String} outline   内容概要
     * @apiSuccess (返回参数说明) {String} host   主办部门
     * @apiSuccess (返回参数说明) {String} push   推送部门：多个用逗号隔开
     * @apiSuccess (返回参数说明) {String} remark   备注
     */
    public function getMeetingInfo($id)
    {
        TokenService::getCurrentUid();
        $info = MeetingT::where('id', $id)
            ->hidden(['create_time,update_time,state'])
            ->find();
        return json($info);

    }

    /**
     * @api {GET} /api/v1/meeting/push 91-检测需要推送通知的会议并推送
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取指定会议室会议信息
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/meeting/push
     */
    public function checkMeetingPush()
    {
        (new MeetingService())->checkMeetingPush();
    }


}