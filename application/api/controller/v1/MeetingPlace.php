<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/5
 * Time: 10:29 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\MeetingPlaceService;
use app\api\validate\MeetingPlaceValidate;
use app\api\service\Token as TokenService;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;
use app\lib\exception\SuccessMessage;

class MeetingPlace extends BaseController
{

    /**
     * @api {POST} /api/v1/meeting/place/save  12-CMS-新增预约申请—教育培训—会场预订
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  教育培训—会场预订
     * @apiExample {post}  请求样例:
     *    {
     *       "apply_date": "2018-12-30",
     *       "letter_size": "公函字号",
     *       "letter_title": "公函标题",
     *       "meals_count": 10,
     *       "users": "单位,姓名,职务A单位,姓名,职务",
     *       "detail": "时间,项目,地点,费用A时间,项目,地点,费用",
     *     }
     * @apiParam (请求参数说明) {String} apply_date   接待时间
     * @apiParam (请求参数说明) {String} letter_size  公函字号
     * @apiParam (请求参数说明) {String} letter_title   公函标题
     * @apiParam (请求参数说明) {int} meals_count   陪餐人数
     * @apiParam (请求参数说明) {String} users   接待对象，数据格式注意按照规定格式提交：单位,姓名,职务A单位,姓名,职务
     * @apiParam (请求参数说明) {String} detail   接待明细，数据格式注意按照规定格式提交：时间,项目,地点,费用A时间,项目,地点,费用
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function save()
    {
        (new MeetingPlaceValidate())->goCheck();
        $admin_id = TokenService::getCurrentUid();
        $params = $this->request->param();
        $params['admin_id'] = $admin_id;
        $params['status'] = CommonEnum::SAVE;
        (new MeetingPlaceService())->save($params);
        return json(new SuccessMessage());

    }

}