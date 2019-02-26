<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/2
 * Time: 3:26 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\FeedbackImgT;
use app\api\model\RepairImgT;
use app\api\service\AdminService;
use app\api\service\RepairService;
use app\api\validate\RepairValidate;
use app\lib\exception\SuccessMessage;
use think\Db;

class Repair extends BaseController
{
    /**
     * @api {POST} /api/v1/repair/save  84-报修申请—新增
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  报修申请—新增
     * @apiExample {post}  请求样例:
     *    {
     *       "name": "电灯",
     *       "address": "202会议室",
     *       "remark": "不亮",
     *       "type": 1,
     *       "imgs": "1,2,3"
     *     }
     * @apiParam (请求参数说明) {String} name 物品名称
     * @apiParam (请求参数说明) {String} address  具体门牌位置
     * @apiParam (请求参数说明) {String} remark   详细描述
     * @apiParam (请求参数说明) {int} type   是否电子设备：1 | 是；2 | 否
     * @apiParam (请求参数说明) {String} imgs  图片id，多张用逗号隔开
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \think\Exception
     */
    public function save()
    {
        (new RepairValidate())->goCheck();
        $params = $this->request->param();
        (new RepairService())->save($params);
        return json(new SuccessMessage());
    }

    /**
     * @api {POST} /api/v1/repair/check/info 85-报修-跟进人员反馈-获取指定报修信息
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取指定用品信息
     * @apiExample {post}  请求样例:
     * {
     * "wf_fid": 1,
     * "wf_type": "repair_machine_t",
     * }
     * @apiParam (请求参数说明) {int} wf_fid  报修申请id
     * @apiParam (请求参数说明) {String} wf_type  报修类别：repair_machine_t | 电子设备报修；repair_other_t 其他报修
     * @apiSuccessExample {json} 返回样例:
     * {"name":"电灯","address":"202会议室"}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} name  物品名称
     * @apiSuccess (返回参数说明) {String} address 具体门牌位置
     * @param $wf_fid
     * @param $wf_type
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfo($wf_fid, $wf_type)
    {
        $info = Db::name($wf_type)->where('id', $wf_fid)->field('name,address')->find();
        return json($info);
    }

    /**
     * @api {GET} /api/v1/repair/list 86-报修-列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  报修-列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/repair/list?department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=0&page=1&size=20
     * @apiParam (请求参数说明) {String}  department 部门/默认传入全部
     * @apiParam (请求参数说明) {String}  username 申请人/默认传入全部
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {int}  status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过；3 | 获取全部
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json}返回样例:
     * {"total":2,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":3,"create_time":"2019-01-03 10:00:54","username":"朱明良","department":"办公室","name":"电灯","address":"202会议室","remark":"不亮","feedback":null,"state":1,"status":1,"wf_type":"repair_other_t","admin_id":1,"f_id":null},{"id":2,"create_time":"2019-01-03 09:44:02","username":"朱明良","department":"办公室","name":"电灯","address":"202会议室","remark":"不亮","feedback":null,"state":1,"status":1,"wf_type":"repair_machine_t","admin_id":1,"f_id":null}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 申请id
     * @apiSuccess (返回参数说明) {String} create_time 日期
     * @apiSuccess (返回参数说明) {String} username 申请人
     * @apiSuccess (返回参数说明) {String} department 部门
     * @apiSuccess (返回参数说明) {String} name 报修物品
     * @apiSuccess (返回参数说明) {int} address  具体门牌位置
     * @apiSuccess (返回参数说明) {int} remark  详情
     * @apiSuccess (返回参数说明) {int} feedback  反馈
     * @apiSuccess (返回参数说明) {String} wf_type  报修类别：repair_machine_t | 电子设备报修；repair_other_t 其他报修
     * @apiSuccess (返回参数说明) {int} status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过
     * @apiSuccess (返回参数说明) {int} admin_id  发起人id
     * @apiSuccess (返回参数说明) {int} f_id  反馈id
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
        $department = AdminService::checkUserRole($department);
        $list = (new RepairService())->getList($page, $size, $time_begin, $time_end, $department, $username, $status);
        return json($list);
    }

    /**
     * @api {GET} /api/v1/repair/export 87-报修列表-导出
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  报修列表-导出
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/repair/export?department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=0
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
        $department = AdminService::checkUserRole($department);
        (new RepairService())->export($time_begin, $time_end, $department, $username, $status);
        return json(new SuccessMessage());
    }

    /**
     * @api {GET} /api/v1/repair/image 88-报修-列表-获取图片
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取指定用品信息
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/repair/image?id=1&wf_type=1&image_type=1
     * @apiParam (请求参数说明) {int} id  报修申请id/反馈id
     * @apiParam (请求参数说明) {int} wf_type  报修类别：1 | 电子设备报修；2| 其他报修
     * @apiParam (请求参数说明) {int} image_type  图片类别：1 | 报修图片；2| 反馈图片
     * @apiSuccessExample {json} 返回样例:
     * [{"id":3,"r_id":2,"img_id":1,"type":1,"img_url":{"url":"http:\/\/maintain.mengant.cn\/http:\/\/1.png"}},{"id":4,"r_id":2,"img_id":2,"type":1,"img_url":{"url":"http:\/\/maintain.mengant.cn\/httpL\/\/2.png"}}]
     * @apiSuccess (返回参数说明) {String} url 图片地址
     * @param $id
     * @param $wf_type
     * @param $image_type
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getImage($id, $wf_type, $image_type)
    {
        if ($image_type == 1) {
            //获取报修图片
            $imgs = RepairImgT::with('imgUrl')->where('r_id', $id)
                ->where('type', $wf_type)
                ->select();

        } else {
            //获取反馈图片
            $imgs = FeedbackImgT::with('imgUrl')->where('r_id', $id)
                ->select();

        }
        return json($imgs);
    }


}