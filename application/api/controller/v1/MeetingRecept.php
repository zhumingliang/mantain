<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 11:05 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\MeetingReceptT;
use app\api\service\AdminService;
use app\api\service\MeetingReceptService;
use app\api\validate\MeetingReceptValidate;
use app\api\service\Token as TokenService;
use app\lib\enum\CommonEnum;
use app\lib\exception\SuccessMessage;

class MeetingRecept extends BaseController
{
    /**
     * @api {POST} /api/v1/meeting/recept/save  20-CMS-新增—公务接待-围餐预定
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  公务接待-围餐预定
     * @apiExample {post}  请求样例:
     *    {
     *       "unit": "税务局",
     *       "leader": "张科长",
     *       "post": "科长",
     *       "grade": "科级",
     *       "departmental": 1,
     *       "section": 1,
     *       "under_section": 10,
     *       "meal_time": "2019-10-10 09:00",
     *       "meeting_count": 20,
     *       "accompany": "张三，李四",
     *       "accompany_count":2,
     *       "letter_size": "公函字号",
     *       "letter_title": "公函标题",
     *       "users": "单位,姓名,职务A单位,姓名,职务",
     *       "detail": "时间,项目,地点,费用A时间,项目,地点,费用",
     *     }
     * @apiParam (请求参数说明) {String} unit  来访单位
     * @apiParam (请求参数说明) {String} leader  领队
     * @apiParam (请求参数说明) {String} post  职务
     * @apiParam (请求参数说明) {String} grade  级别
     * @apiParam (请求参数说明) {String} departmental  厅级人数
     * @apiParam (请求参数说明) {String} section  处级人数
     * @apiParam (请求参数说明) {String} under_section  处级以下人数
     * @apiParam (请求参数说明) {String} meeting_count   会议人数
     * @apiParam (请求参数说明) {String} letter_size  公函字号
     * @apiParam (请求参数说明) {String} letter_title   公函标题
     * @apiParam (请求参数说明) {String} accompany   陪同人员名单
     * @apiParam (请求参数说明) {String} accompany_count   陪餐人数
     * @apiParam (请求参数说明) {String} users   接待对象，数据格式注意按照规定格式提交：单位,姓名,职务A单位,姓名,职务
     * @apiParam (请求参数说明) {String} detail   接待明细，数据格式注意按照规定格式提交：时间,项目,地点,费用A时间,项目,地点,费用
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function save()
    {
        //  (new MeetingReceptValidate())->goCheck();
        $admin_id = TokenService::getCurrentUid();
        $params = $this->request->param();
        $params['admin_id'] = $admin_id;
        $params['status'] = CommonEnum::SAVE;
        $params['state'] = CommonEnum::STATE_IS_OK;
        $params['jifu'] = AdminService::checkUserJiFu();
        $params['source'] =TokenService::getCurrentTokenVar('category');
        (new MeetingReceptService())->save($params);
        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/meeting/recept/list 21-公务接待-围餐预定列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  公务接待-围餐预定列表
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
     * {"total":1,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":19,"state":1,"status":2,"create_time":"2018-12-23 13:22:21","username":"机服中心","department":"办公室","letter_size":"公函字号","letter_title":"公函标题","unit":"税务局","leader":"张科长","post":"科长","grade":"科级","accompany":"张三，李四"}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 申请id
     * @apiSuccess (返回参数说明) {String} create_time  日期
     * @apiSuccess (返回参数说明) {String} username  申请人
     * @apiSuccess (返回参数说明) {String} department  部门
     * @apiSuccess (返回参数说明) {String} unit  来访单位
     * @apiSuccess (返回参数说明) {String} leader  领队
     * @apiSuccess (返回参数说明) {String} post  职务
     * @apiSuccess (返回参数说明) {String} grade  级别
     * @apiSuccess (返回参数说明) {String} letter_size  公函字号
     * @apiSuccess (返回参数说明) {String} letter_title   公函标题
     * @apiSuccess (返回参数说明) {String} accompany   陪同人员名单
     * @apiSuccess (返回参数说明) {int} status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过
     * @apiSuccess (返回参数说明) {int} admin_id  发起人id
     *
     * @param $time_begin
     * @param $time_end
     * @param string $department
     * @param string $username
     * @param $status
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     */
    public function getList($time_begin, $time_end, $department = '全部', $username = '全部', $status = 3, $page = 1, $size = 20)
    {
        $department = AdminService::checkUserRole($department);
        $list = (new MeetingReceptService())->getList($time_begin, $time_end, $department, $username, $status, $page, $size);
        return json($list);
    }

    /**
     * @api {GET} /api/v1/meeting/recept/export 27-公务接待-围餐预定列表-导出
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
        $department = AdminService::checkUserRoleWithGet($department);
        (new MeetingReceptService())->export($time_begin, $time_end, $department, $username, $status);
        return json(new SuccessMessage());
    }

    /**
     * @api {GET} /api/v1/meeting/recept 37-公务接待-围餐预定列表-详情
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  预约申请—会议、接待-详情
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/meeting/recept?id=6
     * @apiParam (请求参数说明) {String}  id 申请id
     * @apiSuccessExample {json}返回样例:
     * {"id":19,"admin_id":3,"status":2,"unit":"税务局","leader":"张科长","post":"科长","grade":"科级","departmental":1,"section":1,"under_section":10,"create_time":"2018-12-23 13:22:21","update_time":"2018-12-23 13:22:21","state":1,"letter_title":"公函标题","letter_size":"公函字号","meeting_count":20,"users":[{"unit":"单位","name":"姓名","post":"职务"},{"unit":"单位","name":"姓名","post":"职务"}],"detail":[{"recept_time":"0000-00-00","content":"项目","address":"地点","money":100},{"recept_time":"0000-00-00","content":"项目","address":"地点","money":100}],"accompany_count":2,"accompany":"张三，李四"}
     * @apiSuccess (返回参数说明) {int} id 申请id
     * @apiSuccess (返回参数说明) {String} unit  来访单位
     * @apiSuccess (返回参数说明) {String} leader  领队
     * @apiSuccess (返回参数说明) {String} post  职务
     * @apiSuccess (返回参数说明) {String} grade  级别
     * @apiSuccess (返回参数说明) {String} departmental  厅级人数
     * @apiSuccess (返回参数说明) {String} section  处级人数
     * @apiSuccess (返回参数说明) {String} under_section  处级以下人数
     * @apiSuccess (返回参数说明) {String} meeting_count   会议人数
     * @apiSuccess (返回参数说明) {String} letter_size  公函字号
     * @apiSuccess (返回参数说明) {String} letter_title   公函标题
     * @apiSuccess (返回参数说明) {String} accompany   陪同人员名单
     * @apiSuccess (返回参数说明) {String} accompany_count   陪餐人数
     * @apiSuccess (返回参数说明) {String} users   接待对象
     * @apiSuccess (返回参数说明) {String} detail   接待明细
     * @param $id
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getMeetingRecept($id)
    {
        $obj = MeetingReceptT::where('id', $id)
            ->with(['users', 'detail'])
            ->find();
        return json($obj);
    }


}