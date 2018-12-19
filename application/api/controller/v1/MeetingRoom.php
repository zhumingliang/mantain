<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/18
 * Time: 8:34 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\MeetingRoomT;
use app\api\service\MeetingRoomService;
use app\lib\enum\CommonEnum;

class MeetingRoom extends BaseController
{
    /**
     * @api {GET} /api/v1/meeting/rooms 39-获取会议室列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  功能室使用申请-获取会议室列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/meeting/rooms
     * @apiSuccessExample {json}返回样例:
     * [{"id":1,"name":"一楼报告厅","count":"不定（可自由安排会场）","function":"投影","card":null},{"id":2,"name":"二楼报告厅","count":"258个座位","function":"电子屏、视频、电视","card":"a3"},{"id":3,"name":"202会议室","count":"32人（侧：24人）","function":"无纸化电脑（32台）、电子屏、视频、电视","card":"a1"},{"id":4,"name":"203小会议室","count":"30人","function":"投影、电子屏","card":"a2"},{"id":5,"name":"205会客室","count":"12人（可加至16人）","function":"沙发","card":null},{"id":6,"name":"多功能培训室","count":"40人","function":"电子屏、投影","card":null},{"id":7,"name":"职工书屋","count":"不定","function":"投影","card":null}]
     * @apiSuccess (返回参数说明) {int} id 会议室id
     * @apiSuccess (返回参数说明) {String} count 会场座位数量信息
     * @apiSuccess (返回参数说明) {String} function 会场功能
     * @apiSuccess (返回参数说明) {String} card 会议室签到机编号
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getRooms()
    {
        $rooms = MeetingRoomT::where('state', CommonEnum::STATE_IS_OK)
            ->field('id,name,count,function,card')
            ->select();
        return json($rooms);

    }

    /**
     * @api {GET} /api/v1/meeting/room/check 40-检测会议室在指定时间内是否可以被使用
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  检测会议室在指定时间内是否可以被使用
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/meeting/room/check?name=会议室1&time_begin=2018-12-22 10:00&time_end=2018-12-22 12:00
     * @apiParam (请求参数说明) {String}  name 会议室名称
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiSuccessExample {json}返回样例:
     * {"res":1}
     * @apiSuccess (返回参数说明) {int} res 状态：1 | 可以被预定；2 | 不可以
     * @param $type
     * @param $name
     * @param $time_begin
     * @param $time_end
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkRooms($name, $time_begin, $time_end)
    {
        $res = (new MeetingRoomService())->checkRoom($name, $time_begin, $time_end);
        return json([
            'res' => $res
        ]);

    }

}