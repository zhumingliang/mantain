<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/4
 * Time: 12:44 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\SpaceRecreationalT;
use app\api\service\AdminService;
use app\api\service\RecreationalService;
use app\api\validate\RecreationalValidate;
use app\api\service\Token as TokenService;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;
use app\lib\exception\SuccessMessage;

class Recreational extends BaseController
{

    /**
     * @api {POST} /api/v1/recreational/save  10-CMS-预约申请—场地使用—文体活动场地
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  预约申请—场地使用—文体活动场地
     * @apiExample {post}  请求样例:
     *    {
     *       "unit": "行政",
     *       "space": "篮球场",
     *       "time_begin": "2018-12-30 09：00",
     *       "time_end": "2018-12-30 12：00",
     *       "user_count": 10
     *     }
     * @apiParam (请求参数说明) {String} unit 申请单位
     * @apiParam (请求参数说明) {String} space  场地名称
     * @apiParam (请求参数说明) {String} time_begin   使用开始时间
     * @apiParam (请求参数说明) {String} time_end   使用结束时间
     * @apiParam (请求参数说明) {int} user_count   使用人数
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function save()
    {
        (new RecreationalValidate())->goCheck();
        $admin_id = TokenService::getCurrentUid();
        $params = $this->request->param();
        $params['admin_id'] = $admin_id;
        $params['status'] = CommonEnum::SAVE;
        $params['state'] = CommonEnum::STATE_IS_OK;
        $params['source'] =TokenService::getCurrentTokenVar('category');
        (new RecreationalService())->save($params);
        return json(new SuccessMessage());

    }


    /**
     * @api {GET} /api/v1/recreational/list 11-预约申请—场地使用—文体活动场地申请列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  文体活动场地申请列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/recreational/list?department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=0&space=全部&page=1&size=20
     * @apiParam (请求参数说明) {String}  department 部门/默认传入全部
     * @apiParam (请求参数说明) {String}  username 申请人/默认传入全部
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {int}  status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过；3 | 获取全部
     * @apiParam (请求参数说明) {String}  space 申请场地
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json}返回样例:
     * {"total":1,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":1,"username":"朱明良","unit":"行政","time_begin":"2018-12-30 09:00:00","time_end":"2018-12-30 12:00:00","user_count":10,"space":"篮球场","status":0,"admin_id":1,"department":"办公室","create_time":"2018-12-05 10:00:47"}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 申请id
     * @apiSuccess (返回参数说明) {String} create_time 日期
     * @apiSuccess (返回参数说明) {String} username 申请人
     * @apiSuccess (返回参数说明) {String} department 部门
     * @apiSuccess (返回参数说明) {String} unit 申请单位
     * @apiSuccess (返回参数说明) {String} space  场地名称
     * @apiSuccess (返回参数说明) {String} time_begin   使用开始时间
     * @apiSuccess (返回参数说明) {String} time_end   使用结束时间
     * @apiSuccess (返回参数说明) {int} status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过
     * @apiSuccess (返回参数说明) {int} user_count   使用人数
     * @apiSuccess (返回参数说明) {int} admin_id  发起人id
     *
     * @param $time_begin
     * @param $time_end
     * @param $department
     * @param $username
     * @param $status
     * @param $space
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     */
    public function getList($time_begin, $time_end, $department, $username, $status,
                            $space, $page = 1, $size = 20)
    {
        $department = AdminService::checkUserRole($department);
        $list = (new RecreationalService())->getList($page, $size, $time_begin, $time_end, $department, $username, $status, $space);
        return json($list);
    }


    /**
     * @api {GET} /api/v1/recreational/export 25-预约申请—场地使用—文体活动场地申请列表-导出报表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  文体活动场地申请列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/recreational/export?department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=0&space=全部
     * @apiParam (请求参数说明) {String}  department 部门/默认传入全部
     * @apiParam (请求参数说明) {String}  username 申请人/默认传入全部
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {int}  status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过；3 | 获取全部
     * @apiParam (请求参数说明) {String}  space 申请场地
     * @param $time_begin
     * @param $time_end
     * @param $department
     * @param $username
     * @param $status
     * @param $space
     * @return \think\response\Json
     */
    public function export($time_begin, $time_end, $department, $username, $status,
                           $space)
    {
        $department = AdminService::checkUserRole($department);
        (new RecreationalService())->export($time_begin, $time_end, $department, $username, $status, $space);
        return json(new SuccessMessage());
    }


}