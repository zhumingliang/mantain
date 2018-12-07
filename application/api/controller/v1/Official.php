<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 9:19 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\OfficialReceptT;
use app\api\service\OfficialService;
use app\api\validate\OfficialValidate;
use app\api\service\Token as TokenService;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;
use app\lib\exception\SuccessMessage;

class Official extends BaseController
{
    /**
     * @api {POST} /api/v1/official/save  16-CMS-预约申请—公务接待-围餐预定
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  公务接待-围餐预定
     * @apiExample {post}  请求样例:
     *    {
     *       "phone": "18956225230",
     *       "cuisine": "粤菜",
     *       "member": 3,
     *       "table_number": 1,
     *       "product": "考察",
     *       "content": "业务规范",
     *       "meal": "中餐",
     *       "meal_type": "工作餐",
     *       "meal_space": "饭堂",
     *       "time_begin": "2018-12-30 09：00",
     *       "time_end": "2018-12-30 12：00",
     *     }
     * @apiParam (请求参数说明) {String} phone 联系人电话
     * @apiParam (请求参数说明) {String} cuisine  菜式
     * @apiParam (请求参数说明) {String} time_begin   开始时间
     * @apiParam (请求参数说明) {String} time_end   结束时间
     * @apiParam (请求参数说明) {int} member  人数
     * @apiParam (请求参数说明) {int} table_number  桌数
     * @apiParam (请求参数说明) {String} product  项目
     * @apiParam (请求参数说明) {String} content  内容
     * @apiParam (请求参数说明) {int} meal  餐次
     * @apiParam (请求参数说明) {String} meal_space  就餐地点
     * @apiParam (请求参数说明) {String} meal_type  餐类
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
        (new OfficialValidate())->goCheck();
        $admin_id = TokenService::getCurrentUid();
        $params = $this->request->param();
        $params['admin_id'] = $admin_id;
        $params['status'] = CommonEnum::SAVE;
        $id = OfficialReceptT::create($params);
        if (!$id) {
            throw new OperationException();
        }
        return json(new SuccessMessage());

    }


    /**
     * @api {GET} /api/v1/official/list 17-预约申请—公务接待-围餐预定申请列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  围餐预定申请列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/official/list?department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=0&meal=全部&meal_type=全部&page=1&size=20
     * @apiParam (请求参数说明) {String}  department 部门/默认传入全部
     * @apiParam (请求参数说明) {String}  username 申请人/默认传入全部
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {int}  status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过；3 | 获取全部
     * @apiParam (请求参数说明) {String}  meal 餐次/默认传入全部
     * @apiParam (请求参数说明) {String}  meal_type 餐类/默认传入全部
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json}返回样例:
     * {"total":1,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":1,"username":"朱明良","content":"业务规范","create_time":"2018-12-06 09:51:17","cuisine":"粤菜","meal":"3","meal_type":"2","member":3,"phone":"18956225230","product":"考察","table_number":1,"time_begin":"2018-12-10 09:00:00","time_end":"2018-12-10 19:00:00","status":0,"admin_id":1}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 申请id
     * @apiSuccess (返回参数说明) {String} create_time 日期
     * @apiSuccess (返回参数说明) {String} username 申请人
     * @apiSuccess (返回参数说明) {String} department 部门
     * @apiSuccess (返回参数说明) {String} phone 联系人电话
     * @apiSuccess (返回参数说明) {String} cuisine  菜式
     * @apiSuccess (返回参数说明) {String} time_begin   开始时间
     * @apiSuccess (返回参数说明) {String} time_end   结束时间
     * @apiSuccess (返回参数说明) {int} member  人数
     * @apiSuccess (返回参数说明) {int} table_number  桌数
     * @apiSuccess (返回参数说明) {String} product  项目
     * @apiSuccess (返回参数说明) {String} content  内容
     * @apiSuccess (返回参数说明) {int} meal  餐次
     * @apiSuccess (返回参数说明) {String} meal_type  餐类
     * @apiSuccess (返回参数说明) {int} status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过
     * @apiSuccess (返回参数说明) {int} admin_id  发起人id
     *
     * @param $time_begin
     * @param $time_end
     * @param $department
     * @param $username
     * @param $status
     * @param $meal
     * @param $meal_type
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     */
    public function getList($time_begin, $time_end, $department, $username, $status,
                            $meal, $meal_type, $page = 1, $size = 20)
    {
        $list = (new OfficialService())->getList($page, $size, $time_begin, $time_end, $department, $username, $status, $meal, $meal_type);
        return json($list);
    }

    /**
     * @api {GET} /api/v1/official/export 28-预约申请—公务接待-围餐预定申请列表-导出
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  围餐预定申请列表-导出
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/official/export?department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=0&meal=全部&meal_type=全部
     * @apiParam (请求参数说明) {String}  department 部门/默认传入全部
     * @apiParam (请求参数说明) {String}  username 申请人/默认传入全部
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {int}  status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过；3 | 获取全部
     * @apiParam (请求参数说明) {String}  meal 餐次/默认传入全部
     * @apiParam (请求参数说明) {String}  meal_type 餐类/默认传入全部
     * @param $time_begin
     * @param $time_end
     * @param $department
     * @param $username
     * @param $status
     * @param $meal
     * @param $meal_type
     * @return \think\response\Json
     */
    public function export($time_begin, $time_end, $department, $username, $status,
                            $meal, $meal_type)
    {
         (new OfficialService())->export( $time_begin, $time_end, $department, $username, $status, $meal, $meal_type);
        return json(new SuccessMessage());
    }

}