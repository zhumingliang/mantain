<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/24
 * Time: 9:28 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\AdminService;
use app\api\service\BuffetService;
use app\api\service\Token as TokenService;
use app\lib\enum\CommonEnum;
use app\lib\exception\SuccessMessage;

class Buffet extends BaseController
{
    /**
     * @api {POST} /api/v1/buffet/save  61—公务接待-自助餐预定
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  公务接待-自助餐预定申请
     * @apiExample {post}  请求样例:
     *    {
     *       "unit": "税务局",
     *       "project": "考察",
     *       "time_begin": 2018-12-25,
     *       "time_end": "2018-12-26",
     *       "time_end": "2018-12-26",
     *       "count": 1,
     *       "meals": "2018-10-12,早餐,3,200A2018-10-12,中餐,3,200"
     *     }
     * @apiParam (请求参数说明) {String} unit 来访单位
     * @apiParam (请求参数说明) {String} project  目的地
     * @apiParam (请求参数说明) {String} time_begin  开始时间
     * @apiParam (请求参数说明) {String} time_end   结束时间
     * @apiParam (请求参数说明) {int} count 人数
     * @apiParam (请求参数说明) {int} meals  订餐信息：数据格式：就餐日期,餐次,用餐人数,费用A就餐日期,餐次,用餐人数,费用
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     */
    public function save()
    {
        $admin_id = TokenService::getCurrentUid();
        $params = $this->request->param();
        $params['admin_id'] = $admin_id;
        $params['status'] = CommonEnum::SAVE;
        $params['state'] = CommonEnum::STATE_IS_OK;
        $params['jifu'] = AdminService::checkUserJiFu();
        $params['source'] = TokenService::getCurrentTokenVar('category');
        (new BuffetService())->save($params);
        return json(new SuccessMessage());

    }


    /**
     * @api {GET} /api/v1/buffet/list 62-公务接待-自助餐预定列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 公务接待-自助餐预定列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/buffet/list?department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=1&page=1&size=20
     * @apiParam (请求参数说明) {String}  department 部门/默认传入全部
     * @apiParam (请求参数说明) {String}  username 申请人/默认传入全部
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {int}  status 流程状态：-1 ; 不通过；0 ; 保存中；1 ;流程中； 2 ;通过；3 ; 获取全部
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json}返回样例:
     * {"total":1,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":2,"create_time":"2018-12-24 10:25:48","status":1,"state":1,"unit":"税务局","project":"考察","time_begin":"2018-12-26","time_end":"2018-12-27","username":"朱明良","department":"办公室"}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 申请id
     * @apiSuccess (返回参数说明) {String} create_time 日期
     * @apiSuccess (返回参数说明) {String} username 申请人
     * @apiSuccess (返回参数说明) {String} department 部门
     * @apiSuccess (返回参数说明) {String} unit 用车时间
     * @apiSuccess (返回参数说明) {String} project  目的地
     * @apiSuccess (返回参数说明) {String} time_begin 开始时间
     * @apiSuccess (返回参数说明) {int} count 人数
     * @apiSuccess (返回参数说明) {String} time_end  用车原因
     * @apiSuccess (返回参数说明) {string} meals  订餐信息：数据格式：就餐日期,餐次,用餐人数,费用A就餐日期,餐次,用餐人数,费用
     * @apiSuccess (返回参数说明) {int} status 流程状态：-1 ; 不通过；0 ; 保存中；1 ;流程中； 2 ; 通过
     * @apiSuccess (返回参数说明) {int} admin_id  发起人id
     */
    public function getList($time_begin, $time_end, $department, $username, $status,
                            $page = 1, $size = 20)
    {
        $department = AdminService::checkUserRole($department);
        $list = (new BuffetService())->getList($page, $size, $time_begin, $time_end, $department, $username, $status);
        return json($list);
    }

    /**
     * @api {GET} /api/v1/buffet/export 63-公务接待-自助餐预定-导出
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 公务接待-自助餐预定
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/buffet/export?department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=1
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
        (new BuffetService())->export($time_begin, $time_end, $department, $username, $status);
        return json(new SuccessMessage());

    }

}