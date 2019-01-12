<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/3
 * Time: 上午1:58
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\CategoryT;
use app\api\model\UnitT;
use app\api\validate\CategoryValidate;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;
use app\lib\exception\SuccessMessage;

class Unit extends BaseController
{
    /**
     * @api {POST} /api/v1/unit/save  67-用品管理-新增单位
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  用品管理-新增单位
     * @apiExample {post}  请求样例:
     *    {
     *       "name": "打印机耗材"
     *     }
     * @apiParam (请求参数说明) {String} name    单位名称
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @param $name
     * @return \think\response\Json
     * @throws OperationException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function save($name)
    {
        $params['name'] = $name;
        $params['state'] = CommonEnum::STATE_IS_OK;
        $params['admin_id'] = \app\api\service\Token::getCurrentUid();
        $id = UnitT::create($params);
        if (!$id) {
            throw  new OperationException();
        }
        return json(new SuccessMessage());

    }


    /**
     * @api {GET} /api/v1/unit/list 68-用品管理-获取单位列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取分类列表
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/unit/list
     * @apiSuccessExample {json} 返回样例:
     * [{"id":1,"name":"支"}]
     * @apiSuccess (返回参数说明) {int} id 单位id
     * @apiSuccess (返回参数说明) {String} name 单位名称
     * @return \think\response\Json
     */
    public function getList()
    {
        $list = UnitT::getList();
        return json($list);
    }


}