<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/25
 * Time: 7:22 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\SkuImgT;
use app\api\model\SkuT;
use app\api\service\SkuService;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;
use app\lib\exception\SkuException;
use app\lib\exception\SuccessMessage;

class Sku extends BaseController
{
    /**
     * @api {POST}  /api/v1/sku/upload 69-用品管理-基本资料-批量导入
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  用file控件上传excel ，文件名称为：sku
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @throws SkuException
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \app\lib\exception\OperationException
     */
    public function uploadSku()
    {
        $file_excel = request()->file('sku');
        if (is_null($file_excel)) {
            throw  new SkuException();
        }
        (new SkuService())->uploadSku($file_excel);
        return json(new SuccessMessage());
    }

    /**
     * @api {POST}  /api/v1/sku/save 71-用品管理-新增用品
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  用品管理-新增用品
     * @apiExample {post}  请求样例:
     *    {
     *       "c_id":1,
     *       "code": "a1231213",
     *       "name": "洗手液",
     *       "unit_id": 1,
     *       "pack": 箱,
     *       "count": 24,
     *       "format": "500ml",
     *       "use_type": 使用,
     *       "alert": 1,
     *       "min": 1,
     *       "max": 1,
     *       "imgs": 1,2,3,
     *     }
     * @apiParam (请求参数说明) {int} c_id    分类id
     * @apiParam (请求参数说明) {String} code    编号
     * @apiParam (请求参数说明) {String} name    名称
     * @apiParam (请求参数说明) {int} unit_id   单位id
     * @apiParam (请求参数说明) {String} pack   进货包装
     * @apiParam (请求参数说明) {int} count   拆箱比
     * @apiParam (请求参数说明) {String} format   规格
     * @apiParam (请求参数说明) {String} use_type   使用方式
     * @apiParam (请求参数说明) {int} alert   是否启动库存警示功能：1 | 启用；2 | 不启用
     * @apiParam (请求参数说明) {int} min   最低警示数量
     * @apiParam (请求参数说明) {int} max   最高警示数量
     * @apiParam (请求参数说明) {String} imgs   上传物品图片：多个用逗号隔开；（上传接口为71接口）
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */

    public function save()
    {
        $params = $this->request->param();
        $params['state'] = CommonEnum::STATE_IS_OK;
        $params['admin_id'] = \app\api\service\Token::getCurrentUid();
        (new SkuService())->save($params);
        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/sku/update  72-用品管理-修改用品信息
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  用品管理-修改用品信息（修改时，只有字段发生改变才需要传入）
     * @apiExample {post}  请求样例:
     *    {
     *       "id":1,
     *       "c_id":1,
     *       "code": "a1231213",
     *       "name": "洗手液",
     *       "unit_id": 1,
     *       "pack": 箱,
     *       "count": 24,
     *       "format": "500ml",
     *       "use_type": 使用,
     *       "alert": 1,
     *       "min": 1,
     *       "max": 1,
     *       "imgs": 1,2,3,
     *     }
     * @apiParam (请求参数说明) {int} c_id    用品id
     * @apiParam (请求参数说明) {int} c_id    分类id
     * @apiParam (请求参数说明) {String} code    编号
     * @apiParam (请求参数说明) {String} name    名称
     * @apiParam (请求参数说明) {int} unit_id   单位id
     * @apiParam (请求参数说明) {String} pack   进货包装
     * @apiParam (请求参数说明) {int} count   拆箱比
     * @apiParam (请求参数说明) {String} format   规格
     * @apiParam (请求参数说明) {String} use_type   使用方式
     * @apiParam (请求参数说明) {int} alert   是否启动库存警示功能：1 | 启用；2 | 不启用
     * @apiParam (请求参数说明) {int} min   最低警示数量
     * @apiParam (请求参数说明) {int} max   最高警示数量
     * @apiParam (请求参数说明) {String} imgs   上传物品图片：多个用逗号隔开；（上传接口为71接口）
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function update()
    {
        $params = $this->request->param();
        (new SkuService())->updateSku($params);
        return json(new SuccessMessage());
    }

    /**
     * 73-用品管理-获取指定用品信息
     * @param $id
     * @return \think\response\Json
     */
    public function getTheSku($id)
    {
        $info = SkuT::getSkuInfo($id);
        return json($info);
    }

    /**
     * @api {POST} /api/v1/sku/image/handel  74-用品管理-删除指定用关联图片
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  删除指定用关联图片
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * }
     * @apiParam (请求参数说明) {int} id 图片用品关联id
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $id
     * @return \think\response\Json
     * @throws OperationException
     */
    public function ShopImageHandel($id)
    {
        $res = SkuImgT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $id]);
        if (!$res) {
            throw new  OperationException(
                ['code' => 401,
                    'msg' => '用品图片删除操作失败',
                    'errorCode' => 600023
                ]
            );
        }
        return json(new SuccessMessage());

    }

    public function getList()
    {

    }


}