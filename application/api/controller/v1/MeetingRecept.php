<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 11:05 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\MeetingReceptService;
use app\api\validate\MeetingReceptValidate;
use app\api\service\Token as TokenService;
use app\lib\enum\CommonEnum;
use app\lib\exception\SuccessMessage;

class MeetingRecept extends BaseController
{
    /**
     * @api {POST} /api/v1/meeting/recept/save  20-CMS-新增预约申请—会议、接待
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  预约申请—会议、接待
     * @apiExample {post}  请求样例:
     *    {
     *       "apply_date": "2018-12-30",
     *       "project": "公务活动项目",
     *       "unit": "税务局",
     *       "leader": "张科长",
     *       "post": "科长",
     *       "grade": "科级",
     *       "departmental": 1,
     *       "section": 1,
     *       "under_section": 10,
     *       "male": 6,
     *       "female": 6,
     *       "meeting_place": "1号会议室",
     *       "meeting_date": "2018-12-30 09 ：00",
     *       "meeting_count": 20,
     *       "hotel": "阳光国际大酒店",
     *       "accompany": "张三，李四",
     *       "meals": "用餐日期,餐次,用餐人数,就餐地点,费用A用餐日期,餐次,用餐人数,就餐地点,费用",
     *     }
     * @apiParam (请求参数说明) {String} apply_date   申请日期
     * @apiParam (请求参数说明) {String} project  公务活动项目
     * @apiParam (请求参数说明) {String} unit  来访单位
     * @apiParam (请求参数说明) {String} leader  领队
     * @apiParam (请求参数说明) {String} post  职务
     * @apiParam (请求参数说明) {String} grade  级别
     * @apiParam (请求参数说明) {String} departmental  厅级人数
     * @apiParam (请求参数说明) {String} section  处级人数
     * @apiParam (请求参数说明) {String} under_section  处级以下人数
     * @apiParam (请求参数说明) {String} male   男性人数
     * @apiParam (请求参数说明) {String} female   女性人数
     * @apiParam (请求参数说明) {String} meeting_place   会议地点
     * @apiParam (请求参数说明) {String} meeting_date   会议时间
     * @apiParam (请求参数说明) {String} meeting_count   会议人数
     * @apiParam (请求参数说明) {int} hotel   酒店
     * @apiParam (请求参数说明) {String} accompany   陪同人员名单
     * @apiParam (请求参数说明) {String} meals   用餐明细，数据格式注意按照规定格式提交：用餐日期,餐次,用餐人数,就餐地点,费用A用餐日期,餐次,用餐人数,就餐地点,费用
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
        (new MeetingReceptValidate())->goCheck();
        $admin_id = TokenService::getCurrentUid();
        $params = $this->request->param();
        $params['admin_id'] = $admin_id;
        $params['status'] = CommonEnum::SAVE;
        (new MeetingReceptService())->save($params);
        return json(new SuccessMessage());

    }


    /**
     * @api {GET} /api/v1/meeting/recept/list 21-预约申请—会议、接待列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  预约申请—会议、接待列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/meeting/recept/list?department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=0&page=1&size=20
     * @apiParam (请求参数说明) {String}  department 部门/默认传入全部
     * @apiParam (请求参数说明) {String}  username 申请人/默认传入全部
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {int}  status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过；3 | 获取全部
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json}返回样例:
     * {"total":1,"per_page":"20","current_page":1,"last_page":1,"data":[{"create_time":"2018-12-07 00:13:49","id":6,"admin_id":1,"username":"朱明良","department":"办公室","apply_date":"2018-12-10","unit":"税务局","count":20,"leader":"张科长","project":"公务活动","meeting_date":"2018-12-30 00:00:00","meeting_place":"1号会议室","status":0}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 申请id
     * @apiSuccess (返回参数说明) {String} apply_date 日期
     * @apiSuccess (返回参数说明) {String} unit 来访单位
     * @apiSuccess (返回参数说明) {int} count 人数
     * @apiSuccess (返回参数说明) {int} leader 领队
     * @apiSuccess (返回参数说明) {int} project   公务活动
     * @apiSuccess (返回参数说明) {int} meeting_date   会议时间
     * @apiSuccess (返回参数说明) {int} meeting_place   会议地点
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
        $list = (new MeetingReceptService())->getList($time_begin, $time_end, $department, $username, $status, $page, $size);
        return json($list);
    }

    /**
     * @api {GET} /api/v1/meeting/recept/export 27-预约申请—会议、接待列表-导出
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  预约申请—会议、接待列表-导出
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/meeting/recept/export?department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=0
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
        (new MeetingReceptService())->export($time_begin, $time_end, $department, $username, $status);
        return json(new SuccessMessage());
    }


}