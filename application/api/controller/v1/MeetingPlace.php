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

    /**
     * @api {GET} /api/v1/meeting/place/list 13-预约申请—教育培训—会场预订列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  教育培训—会场预订列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/meeting/place/list?department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=0&page=1&size=20
     * @apiParam (请求参数说明) {String}  department 部门/默认传入全部
     * @apiParam (请求参数说明) {String}  username 申请人/默认传入全部
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {int}  status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过；3 | 获取全部
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json}返回样例:
     * {"total":2,"per_page":"20","current_page":1,"last_page":1,"data":[{"create_time":"2018-12-06 00:47:10","id":17,"admin_id":1,"username":"朱明良","department":"办公室","apply_date":"2018-12-30","letter_title":"公函标题","letter_size":"asasassa1231231","meals_count":10,"status":0,"user_count":2,"money":200},{"create_time":"2018-12-05 22:45:19","id":16,"admin_id":1,"username":"朱明良","department":"办公室","apply_date":"2018-12-30","letter_title":"公函标题","letter_size":"asasassa1231231","meals_count":10,"status":0,"user_count":0,"money":0}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 申请id
     * @apiSuccess (返回参数说明) {String} apply_date 日期
     * @apiSuccess (返回参数说明) {String} letter_title 公函标题
     * @apiSuccess (返回参数说明) {String} letter_size 公函字号
     * @apiSuccess (返回参数说明) {int} meals_count 陪餐人数
     * @apiSuccess (返回参数说明) {int} user_count   接待人数
     * @apiSuccess (返回参数说明) {int} money   费用合计
     * @apiSuccess (返回参数说明) {int} status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过
     * @apiSuccess (返回参数说明) {int} admin_id  发起人id
     *
     * @param $time_begin
     * @param $time_end
     * @param $department
     * @param $username
     * @param $status
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     */
    public function getList($time_begin, $time_end, $department, $username, $status, $page = 1, $size = 20)
    {
        $list = (new MeetingPlaceService())->getList($time_begin, $time_end, $department, $username, $status, $page, $size);
        return json($list);
    }

    /**
     * @api {GET} /api/v1/meeting/place/export 13-预约申请—教育培训—会场预订列表-导出报表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  教育培训—会场预订列表-导出报表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/meeting/place/export?department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=0
     * @apiParam (请求参数说明) {String}  department 部门/默认传入全部
     * @apiParam (请求参数说明) {String}  username 申请人/默认传入全部
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {int}  status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过；3 | 获取全部
     * @param $time_begin
     * @param $time_end
     * @param $department
     * @param $username
     * @param $status
     * @return \think\response\Json
     */
    public function export($time_begin, $time_end, $department, $username, $status)
    {
        $list = (new MeetingPlaceService())->export($time_begin, $time_end, $department, $username, $status);
        return json($list);
    }

}