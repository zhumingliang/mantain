<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/24
 * Time: 11:14 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\AdminService;
use app\api\service\HotelService;
use app\api\service\Token as TokenService;
use app\lib\enum\CommonEnum;
use app\lib\exception\SuccessMessage;

class Hotel extends BaseController
{
    /**
     * @api {POST} /api/v1/hotel/save  64—公务接待-酒店预定
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  公务接待-酒店预定申请
     * @apiExample {post}  请求样例:
     *    {
     *       "unit": "税务局",
     *       "time_begin": 2018-12-25,
     *       "time_end": "2018-12-26",
     *       "hotel": "瑞金大酒店",
     *       "guesthouse": "**招待所",
     *       "male": 1,
     *       "female": 2,
     *       "single_room":1,
     *       "double_room": 2,
     *       "members": "张三,李四"
     *     }
     * @apiParam (请求参数说明) {String} unit 来访单位
     * @apiParam (请求参数说明) {String} time_begin   开始时间
     * @apiParam (请求参数说明) {String} time_end   结束时间
     * @apiParam (请求参数说明) {String} hotel   酒店
     * @apiParam (请求参数说明) {String} guesthouse  招待所
     * @apiParam (请求参数说明) {int} male   男性人数
     * @apiParam (请求参数说明) {int} female   女性人数
     * @apiParam (请求参数说明) {int} single_room   单间数
     * @apiParam (请求参数说明) {int} double_room   双人间数
     * @apiParam (请求参数说明) {String} members  人员名单
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
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
        (new HotelService())->save($params);
        return json(new SuccessMessage());

    }


    /**
     * @api {GET} /api/v1/hotel/list 65-公务接待-酒店预定列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 公务接待-酒店预定列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/hotel/list?department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=1&page=1&size=20
     * @apiParam (请求参数说明) {String}  department 部门/默认传入全部
     * @apiParam (请求参数说明) {String}  username 申请人/默认传入全部
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {int}  status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过；3 | 获取全部
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json}返回样例:
     * {"total":1,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":1,"state":1,"status":1,"create_time":"2018-12-24 15:11:13","username":"朱明良","department":"办公室","unit":"税务局","time_begin":"2018-12-25","time_end":"2018-12-26","hotel":"瑞金大酒店","male":1,"female":3,"single_room":1,"double_room":2,"members":"张三，李四"}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 申请id
     * @apiSuccess (返回参数说明) {String} create_time 日期
     * @apiSuccess (返回参数说明) {String} username 申请人
     * @apiSuccess (返回参数说明) {String} department 部门
     * @apiSuccess (返回参数说明) {String} unit 来访单位
     * @apiSuccess (返回参数说明) {String} time_begin   开始时间
     * @apiSuccess (返回参数说明) {String} time_end   结束时间
     * @apiSuccess (返回参数说明) {String} hotel   酒店
     * @apiSuccess (返回参数说明) {String} guesthouse   招待所
     * @apiSuccess (返回参数说明) {int} male   男性人数
     * @apiSuccess (返回参数说明) {int} female   女性人数
     * @apiSuccess (返回参数说明) {int} single_room   单间数
     * @apiSuccess (返回参数说明) {int} double_room   双人间数
     * @apiSuccess (返回参数说明) {String} members  人员名单
     * @apiSuccess (返回参数说明) {int} status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过
     * @apiSuccess (返回参数说明) {int} admin_id  发起人id
     */
    public function getList($time_begin, $time_end, $department, $username, $status,
                            $page = 1, $size = 20)
    {
        $department = AdminService::checkUserRole($department);
        $list = (new HotelService())->getList($page, $size, $time_begin, $time_end, $department, $username, $status);
        return json($list);
    }

    /**
     * @api {GET} /api/v1/hotel/export 66-公务接待-酒店预定-导出
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 公务接待-酒店预定-导出
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/hotel/export?department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=1
     * @apiParam (请求参数说明) {String}  department 部门/默认传入全部
     * @apiParam (请求参数说明) {String}  username 申请人/默认传入全部
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {int}  status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过；3 | 获取全部
     */
    public function export($time_begin, $time_end, $department, $username, $status)
    {
        $department = AdminService::checkUserRoleWithGet($department);
        (new HotelService())->export($time_begin, $time_end, $department, $username, $status);
        return json(new SuccessMessage());

    }


}