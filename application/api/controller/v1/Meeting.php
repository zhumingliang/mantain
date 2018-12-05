<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/3
 * Time: 10:28 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\MeetingT;
use app\api\validate\MeetingValidate;
use app\api\service\Token as TokenService;
use app\lib\enum\CommonEnum;
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
     *       "time_begin": "09：00",
     *       "time_end": "09：30",
     *       "meeting_begin": "10：00",
     *       "theme": "全体职工大会",
     *       "outline": "年终总结",
     *       "remark": "必须参加"
     *     }
     * @apiParam (请求参数说明) {String} meeting_date   日期
     * @apiParam (请求参数说明) {String} address   签到地点
     * @apiParam (请求参数说明) {String} time_begin   签到开始时间
     * @apiParam (请求参数说明) {String} time_end   签到截止时间
     * @apiParam (请求参数说明) {String} meeting_begin   会议开始时间
     * @apiParam (请求参数说明) {String} theme   会议主题
     * @apiParam (请求参数说明) {String} outline   内容概要
     * @apiParam (请求参数说明) {String} remark   备注
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws OperationException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function save()
    {
        (new MeetingValidate())->goCheck();
        $admin_id = TokenService::getCurrentUid();
        $params = $this->request->param();
        $params['admin_id'] = $admin_id;
        $params['state'] = CommonEnum::STATE_IS_OK;
        $id = MeetingT::create($params);
        if (!$id) {
            throw new OperationException();
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
     *       "time_begin": "09：00",
     *       "time_end": "09：30",
     *       "meeting_begin": "10：00",
     *       "theme": "全体职工大会",
     *       "outline": "年终总结",
     *       "remark": "必须参加"
     *     }
     * @apiParam (请求参数说明) {int} id   会议id
     * @apiParam (请求参数说明) {String} meeting_date   日期
     * @apiParam (请求参数说明) {String} address   签到地点
     * @apiParam (请求参数说明) {String} time_begin   签到开始时间
     * @apiParam (请求参数说明) {String} time_end   签到截止时间
     * @apiParam (请求参数说明) {String} meeting_begin   会议开始时间
     * @apiParam (请求参数说明) {String} theme   会议主题
     * @apiParam (请求参数说明) {String} outline   内容概要
     * @apiParam (请求参数说明) {String} remark   备注
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @param $id
     * @return \think\response\Json
     * @throws OperationException
     * @throws \app\lib\exception\ParameterException
     */
    public function update($id)
    {
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
     * @param $id
     * @return \think\response\Json
     * @throws OperationException
     */
    public function delete($id)
    {
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

    public function signIn()
    {

    }

}