<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 10:26 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\CarT;
use app\api\service\CarService;
use app\api\validate\CarValidate;
use app\api\service\Token as TokenService;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;
use app\lib\exception\SuccessMessage;

class Car extends BaseController
{
    /**
     * @api {POST} /api/v1/car/save  18-CMS-预约申请—公务用车申请
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  公务用车申请
     * @apiExample {post}  请求样例:
     *    {
     *       "apply_date": "2018-12-10 09：00",
     *       "address": "五邑大学",
     *       "count": 3,
     *       "reason": "考察",
     *       "members": "张三,李四"
     *     }
     * @apiParam (请求参数说明) {String} apply_date 用车时间
     * @apiParam (请求参数说明) {String} address  目的地
     * @apiParam (请求参数说明) {String} count   人数
     * @apiParam (请求参数说明) {String} reason   用车原因
     * @apiParam (请求参数说明) {int} members  出行人员
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     *
     * @return \think\response\Json
     * @throws OperationException
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function save()
    {
        (new CarValidate())->goCheck();
        $admin_id = TokenService::getCurrentUid();
        $params = $this->request->param();
        $params['admin_id'] = $admin_id;
        $params['status'] = CommonEnum::SAVE;
        $id = CarT::create($params);
        if (!$id) {
            throw new OperationException();
        }
        return json(new SuccessMessage());

    }


    /**
     * @api {GET} /api/v1/car/list 19-预约申请—预约申请—公务用车申请列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 公务用车申请列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/car/list?department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=0&reason=全部&page=1&size=20
     * @apiParam (请求参数说明) {String}  department 部门/默认传入全部
     * @apiParam (请求参数说明) {String}  username 申请人/默认传入全部
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {int}  status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过；3 | 获取全部
     * @apiParam (请求参数说明) {String}  reason 用车原因/默认传入全部
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json}返回样例:
     * {"total":1,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":1,"username":"朱明良","address":"五邑大学","apply_date":"2018-12-10 09:00:00","count":3,"members":"3","status":0,"admin_id":1,"department":"办公室","create_time":"2018-12-06 10:56:41","reason":"考察"}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 申请id
     * @apiSuccess (返回参数说明) {String} create_time 日期
     * @apiSuccess (返回参数说明) {String} username 申请人
     * @apiSuccess (返回参数说明) {String} department 部门
     * @apiSuccess (返回参数说明) {String} apply_date 用车时间
     * @apiSuccess (返回参数说明) {String} address  目的地
     * @apiSuccess (返回参数说明) {String} count   人数
     * @apiSuccess (返回参数说明) {String} reason   用车原因
     * @apiSuccess (返回参数说明) {int} members  出行人员
     * @apiSuccess (返回参数说明) {int} status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过
     * @apiSuccess (返回参数说明) {int} admin_id  发起人id
     *
     * @param $time_begin
     * @param $time_end
     * @param $department
     * @param $username
     * @param $status
     * @param $reason
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     */
    public function getList($time_begin, $time_end, $department, $username, $status,
                            $reason, $page = 1, $size = 20)
    {
        $list = (new CarService())->getList($page, $size, $time_begin, $time_end, $department, $username, $status, $reason);
        return json($list);
    }


    /**
     * @api {GET} /api/v1/car/export 22-预约申请—预约申请—公务用车申请列表-导出
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 公务用车申请列表-导出
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/car/export?department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=0&reason=全部
     * @apiParam (请求参数说明) {String}  department 部门/默认传入全部
     * @apiParam (请求参数说明) {String}  username 申请人/默认传入全部
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {int}  status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过；3 | 获取全部
     * @apiParam (请求参数说明) {String}  reason 用车原因/默认传入全部
     * @param $time_begin
     * @param $time_end
     * @param $department
     * @param $username
     * @param $status
     * @param $reason
     */
    public function export($time_begin, $time_end, $department, $username, $status,
                            $reason)
    {
         (new CarService())->export($time_begin, $time_end, $department, $username, $status, $reason);
    }
}