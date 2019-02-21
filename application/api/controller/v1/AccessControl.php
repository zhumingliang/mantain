<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/3
 * Time: 9:18 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\AccessControlT;
use app\api\service\AccessService;
use app\api\service\AdminService;
use app\api\service\FlowService;
use app\api\validate\AccessControlValidate;
use app\api\service\Token as TokenService;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;
use app\lib\exception\SuccessMessage;

class AccessControl extends BaseController
{
    /**
     * @api {POST} /api/v1/access/save  5-CMS-新增预约申请—门禁权限
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  预约申请—门禁权限
     * @apiExample {post}  请求样例:
     *    {
     *       "access": "资料室",
     *       "deadline": "2018-12-30"
     *       "user_type": "干部职工"
     *       "members": "张三-副主任,李四-主任"
     *     }
     * @apiParam (请求参数说明) {String} access   申请功能
     * @apiParam (请求参数说明) {String} deadline   截止时间
     * @apiParam (请求参数说明) {String} user_type   人员类型
     * @apiParam (请求参数说明) {String} members   申请人员名单-职务，数据格式：姓名-职务,姓名-职务;多个人员信息用逗号隔开
     *
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
        (new AccessControlValidate())->goCheck();
        $admin_id = TokenService::getCurrentUid();
        $params = $this->request->param();
        $params['admin_id'] = $admin_id;
        $params['jifu'] = AdminService::checkUserJiFu();
        $params['status'] = CommonEnum::SAVE;
        $params['state'] = CommonEnum::STATE_IS_OK;
        $params['source'] =TokenService::getCurrentTokenVar('category');
        (new AccessService())->save($params);
        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/access/list 9-获取预约申请—门禁权限列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取预约申请—门禁权限列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/access/list?department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=0&access=全部&page=1&size=20
     * @apiParam (请求参数说明) {String}  department 部门/默认传入全部
     * @apiParam (请求参数说明) {String}  username 申请人/默认传入全部
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {int}  status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过；3 | 获取全部
     * @apiParam (请求参数说明) {String}  access 开通功能
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json}返回样例:
     * {"total":1,"per_page":20,"current_page":1,"last_page":1,"data":[{"id":1,"create_time":"2018-12-03 10:26:55","username":"朱明良","department":"办公室","role_name":"管理员","members":"张三-局长","user_type":"干部职工","access":"资料室,会议室","deadline":"2018-12-30 00:00:00","status":0,"admin_id":1}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 申请id
     * @apiSuccess (返回参数说明) {String} create_time 日期
     * @apiSuccess (返回参数说明) {String} username 申请人
     * @apiSuccess (返回参数说明) {String} department 部门
     * @apiSuccess (返回参数说明) {String} role_name 职务
     * @apiSuccess (返回参数说明) {String} user_type 人员类型
     * @apiSuccess (返回参数说明) {String} access 开通功能
     * @apiSuccess (返回参数说明) {String} members 申请人员名单-职务
     * @apiSuccess (返回参数说明) {String} deadline 工作截止时间
     * @apiSuccess (返回参数说明) {int} status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过
     * @apiSuccess (返回参数说明) {int} admin_id  发起人id
     *
     * @param $time_begin
     * @param $time_end
     * @param $department
     * @param $username
     * @param $status
     * @param $access
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     */
    public function getList($time_begin, $time_end, $department, $username, $status,
                            $access, $page = 1, $size = 20)
    {
        $check = AdminService::checkUserRole();
        if ($check['res']) {
            $department = $check['department'];
        }
        $list = (new AccessService())->getList($page, $size, $time_begin, $time_end, $department, $username, $status, $access);
        return json($list);
    }


    /**
     * @api {GET} /api/v1/access/export 26-获取预约申请—门禁权限列表-导出列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取预约申请—门禁权限列表-导出列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/access/export?department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=0&access=全部
     * @apiParam (请求参数说明) {String}  department 部门/默认传入全部
     * @apiParam (请求参数说明) {String}  username 申请人/默认传入全部
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {int}  status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过；3 | 获取全部
     * @apiParam (请求参数说明) {String}  access 开通功能
     * @param $time_begin
     * @param $time_end
     * @param $department
     * @param $username
     * @param $status
     * @param $access
     * @return \think\response\Json
     */
    public function export($time_begin, $time_end, $department, $username, $status,
                           $access)
    {
        (new AccessService())->export($time_begin, $time_end, $department, $username, $status, $access);
        return json(new SuccessMessage());
    }

    public function getTheAccess($id)
    {
        $info = (new AccessService())->getTheFlow($id);
        return json($info);
    }


    /**
     * @api {GET} /api/v1/access/list/wx 51-微信端—门禁权限列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取预约申请—门禁权限列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/access/list?department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=0&access=全部&page=1&size=20
     * @apiParam (请求参数说明) {String}  department 部门/默认传入全部
     * @apiParam (请求参数说明) {String}  username 申请人/默认传入全部
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {int}  status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过；3 | 获取全部
     * @apiParam (请求参数说明) {String}  access 开通功能
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json}返回样例:
     * {"total":1,"per_page":20,"current_page":1,"last_page":1,"data":[{"id":1,"create_time":"2018-12-03 10:26:55","username":"朱明良","department":"办公室","role_name":"管理员","user_type":"干部职工","access":"资料室,会议室","deadline":"2018-12-30 00:00:00","status":0,"admin_id":1}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 申请id
     * @apiSuccess (返回参数说明) {String} create_time 日期
     * @apiSuccess (返回参数说明) {String} username 申请人
     * @apiSuccess (返回参数说明) {String} department 部门
     * @apiSuccess (返回参数说明) {String} role_name 职务
     * @apiSuccess (返回参数说明) {String} user_type 人员类型
     * @apiSuccess (返回参数说明) {String} access 开通功能
     * @apiSuccess (返回参数说明) {String} deadline 工作截止时间
     * @apiSuccess (返回参数说明) {int} status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过
     * @apiSuccess (返回参数说明) {int} admin_id  发起人id
     *
     * @param $type
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     */
    public function getListForWx($type = 1, $page = 1, $size = 20)
    {
        $list = (new AccessService())->getListForWX($type,$page, $size);
        return json($list);
    }


}