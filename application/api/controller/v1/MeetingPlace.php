<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/5
 * Time: 10:29 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\MeetingplaceT;
use app\api\service\AdminService;
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
     *       "unit": "税务局",
     *       "place": "202会议室",
     *       "purpose": "会议",
     *       "reason": 开会,
     *       "time_begin": "2018-12-22 09:00",
     *       "time_end": "2018-12-22 12:00",
     *     }
     * @apiParam (请求参数说明) {String} unit   申请单位
     * @apiParam (请求参数说明) {String} place  场地名称
     * @apiParam (请求参数说明) {String} purpose   用途
     * @apiParam (请求参数说明) {int} reason   使用事由
     * @apiParam (请求参数说明) {String} time_begin   开始时间
     * @apiParam (请求参数说明) {String} time_end   结束时间
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
        $params['state'] = CommonEnum::STATE_IS_OK;
        $params['jifu'] = AdminService::checkUserJiFu();
        $params['source'] =TokenService::getCurrentTokenVar('category');
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
     * {"total":1,"per_page":20,"current_page":1,"last_page":1,"data":[{"id":36,"create_time":"2018-12-22 22:00:29","username":"部门负责人","unit":"税务局","time_begin":"2018-12-22 09:00:00","time_end":"2018-12-22 12:00:00","reason":"开会","place":"202会议室","state":1,"status":1,"purpose":"会议"}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 申请id
     * @apiSuccess (返回参数说明) {String} create_time 日期
     * @apiSuccess (返回参数说明) {String} username 申请人
     * @apiSuccess (返回参数说明) {String} unit   申请单位
     * @apiSuccess (返回参数说明) {String} place  场地名称
     * @apiSuccess (返回参数说明) {String} purpose   用途
     * @apiSuccess (返回参数说明) {int} reason   使用事由
     * @apiSuccess (返回参数说明) {String} time_begin   开始时间
     * @apiSuccess (返回参数说明) {String} time_end   结束时间
     * @apiSuccess (返回参数说明) {int} status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过
     * @param $time_begin
     * @param $time_end
     * @param $department
     * @param $username
     * @param $status
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     */
    public function getList($time_begin, $time_end, $department = '全部', $username = '全部', $status = 3, $page = 1, $size = 20)
    {
        $department = AdminService::checkUserRole($department);
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
    public function export($time_begin, $time_end, $department = '全部', $username = '全部', $status = 3)
    {
        $department = AdminService::checkUserRoleWithGet($department);
        (new MeetingPlaceService())->export($time_begin, $time_end, $department, $username, $status);
        return json(new SuccessMessage());
    }

    /**
     * @api {GET} /api/v1/meeting/place 36-预约申请—教育培训—会场预订-详情
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  教育培训—会场预订列表-详情
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/meeting/place?id=17
     * @apiParam (请求参数说明) {String}  id 申请id
     * @apiSuccessExample {json}返回样例:
     * {"id":36,"unit":"税务局","place":"202会议室","purpose":"会议","create_time":"2018-12-22 22:00:29","update_time":"2018-12-22 22:00:29","admin_id":2,"status":1,"reason":"开会","state":1,"time_begin":"2018-12-22 09:00:00","time_end":"2018-12-22 12:00:00"}
     * @apiSuccess (返回参数说明) {int} id 申请id
     * @apiSuccess (返回参数说明) {String} create_time 日期
     * @apiSuccess (返回参数说明) {String} username 申请人
     * @apiSuccess (返回参数说明) {String} unit   申请单位
     * @apiSuccess (返回参数说明) {String} place  场地名称
     * @apiSuccess (返回参数说明) {String} purpose   用途
     * @apiSuccess (返回参数说明) {int} reason   使用事由
     * @apiSuccess (返回参数说明) {String} time_begin   开始时间
     * @apiSuccess (返回参数说明) {String} time_end   结束时间
     * @apiSuccess (返回参数说明) {int} status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过
     * @param $id
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMeetingPlace($id)
    {
        $obj = MeetingplaceT::where('id', $id)
            ->find();
        return json($obj);


    }

}