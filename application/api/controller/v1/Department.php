<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/20
 * Time: 12:38 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\DepartmentT;
use app\lib\enum\CommonEnum;

class Department extends BaseController
{
    /**
     * @api {GET} /api/v1/department/list 44-获取部门列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取部门列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/department/list
     * @apiSuccessExample {json}返回样例:
     * [{"id":1,"name":"局长室"},{"id":2,"name":"稽查局"},{"id":3,"name":"办公室"},{"id":4,"name":"法制科"},{"id":5,"name":"货物和劳务税科"},{"id":6,"name":"所得税科"},{"id":7,"name":"财产和行为税科"},{"id":8,"name":"社会保险费和非税收入科"},{"id":9,"name":"收入核算科"},{"id":10,"name":"征收管理科"},{"id":11,"name":"国际税收管理科"},{"id":12,"name":"税收经济分析科"},{"id":13,"name":"税收风险管理局"},{"id":14,"name":"财务管理科"},{"id":15,"name":"督察内审科"},{"id":16,"name":"人事教育科"},{"id":17,"name":"考核考评科"},{"id":18,"name":"机关党委"},{"id":19,"name":"老干部科"},{"id":20,"name":"系统党建工作科"},{"id":21,"name":"纪检组"},{"id":22,"name":"第一税务分局"},{"id":23,"name":"第一税务分局（重点税源企业税收服务和管理局）"},{"id":24,"name":"第二税务分局"},{"id":25,"name":"第一稽查局"},{"id":26,"name":"第二稽查局"},{"id":27,"name":"信息中心"},{"id":28,"name":"机关服务中心"},{"id":29,"name":"纳税服务中心（税收宣传中心）"}]
     * @apiSuccess (返回参数说明) {int} id 部门id
     * @apiSuccess (返回参数说明) {String} name 部门名称
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList()
    {
        $list = DepartmentT::where('state', CommonEnum::STATE_IS_OK)
            ->field('id,name')
            ->select();
        return json($list);
    }

}