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
use app\api\model\SkuStockT;
use app\api\model\SkuStockV;
use app\api\model\SkuT;
use app\api\service\SkuService;
use app\api\validate\SkuStockValidate;
use app\index\controller\News;
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
    public function upload()
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
     * @api {POST} /api/v1/sku 73-用品管理-获取指定用品信息
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取指定用品信息
     * @apiExample {post}  请求样例:
     * {
     * "id": 1,
     * }
     * @apiParam (请求参数说明) {int} id    SKUid
     * @apiSuccessExample {json} 返回样例:
     * {"id":1,"c_id":1,"code":"a1231213","name":"洗手液","unit_id":1,"pack":"箱","count":24,"format":"500ml","use_type":"使用","min":100,"max":500,"alert":1,"imgs":[{"sku_id":1,"img_id":1,"img_url":{"url":"http:\/\/1.png"}},{"sku_id":1,"img_id":2,"img_url":{"url":"httpL\/\/2.png"}},{"sku_id":1,"img_id":3,"img_url":{"url":"http:\/\/3.png"}}]}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {int} id    用品id
     * @apiSuccess (返回参数说明) {int} c_id    分类id
     * @apiSuccess (返回参数说明) {String} code    编号
     * @apiSuccess (返回参数说明) {String} name    名称
     * @apiSuccess (返回参数说明) {int} unit_id   单位id
     * @apiSuccess (返回参数说明) {String} pack   进货包装
     * @apiSuccess (返回参数说明) {int} count   拆箱比
     * @apiSuccess (返回参数说明) {String} format   规格
     * @apiSuccess (返回参数说明) {String} use_type   使用方式
     * @apiSuccess (返回参数说明) {int} alert   是否启动库存警示功能：1 | 启用；2 | 不启用
     * @apiSuccess (返回参数说明) {int} min   最低警示数量
     * @apiSuccess (返回参数说明) {int} max   最高警示数量
     * @apiSuccess (返回参数说明) {String} imgs   物品图片信息
     * @apiSuccess (返回参数说明) {String} url   图片地址
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
    public function SkuImageHandel($id)
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

    /**
     * @api {POST}  /api/v1/sku/save 75-用品管理-新增库存
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  用品管理-新增用品
     * @apiExample {post}  请求样例:
     *    {
     *       "sku_id":1,
     *       "count": 1,
     *       "all_count": 20,
     *       "type": 1,
     *       "stock_code":"11111",
     *       "price": 1.11,
     *       "all_money": 22.2
     *     }
     * @apiParam (请求参数说明) {int} sku_id    用品id
     * @apiParam (请求参数说明) {int} count    入库数量
     * @apiParam (请求参数说明) {int} all_count    入库数量最小存储单位数量：入库数量*拆箱比
     * @apiParam (请求参数说明) {int} type   入库类别：1 | 新增；2 | 减少
     * @apiParam (请求参数说明) {String} stock_code  入库单编号
     * @apiParam (请求参数说明) {int} price   进货单价
     * @apiParam (请求参数说明) {int} all_money  进货总金额：进货单价*入库数量最小存储单位数量
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return \think\response\Json
     * @throws OperationException
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function stockSave()
    {
        (new SkuStockValidate())->goCheck();
        $params = $this->request->param();
        $params['state'] = CommonEnum::STATE_IS_OK;
        $params['admin_id'] = \app\api\service\Token::getCurrentUid();
        $params['stock'] = (new SkuService())->getStock($params['sku_id']);
        $res = SkuStockT::create($params);
        if (!$res) {
            throw  new OperationException();
        }
        return json(new SuccessMessage());


    }

    /**
     * @api {GET} /api/v1/stock/list 76-用品管理-获取库存记录列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取库存记录列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/stock/list?category=''&order_number=''&category_id=''&page=1&size=20
     * @apiParam (请求参数说明) {String}  category 类别名称
     * @apiParam (请求参数说明) {String}  order_number 类别排序
     * @apiParam (请求参数说明) {String}  category_id 类别id
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json}返回样例:
     * {"total":2,"per_page":10,"current_page":1,"last_page":1,"data":[{"id":2,"sku_id":1,"sku_name":"洗手液","category_name":"打印机耗材","stock":0,"all_count":20,"stock_date":"2018-12-28","min":100,"max":500,"admin_name":"打印机耗材","create_time":"2018-12-27 23:27:26","state":1,"order_number":"1","category_id":1,"price":1.11},{"id":1,"sku_id":1,"sku_name":"洗手液","category_name":"打印机耗材","stock":0,"all_count":20,"stock_date":"2018-12-28","min":100,"max":500,"admin_name":"打印机耗材","create_time":"2018-12-27 23:26:23","state":1,"order_number":"1","category_id":1,"price":1.11}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 入库记录ID
     * @apiSuccess (返回参数说明) {String} sku_name  物品名称
     * @apiSuccess (返回参数说明) {String} category_name  类别
     * @apiSuccess (返回参数说明) {String} stock  库存
     * @apiSuccess (返回参数说明) {String} all_count  入库
     * @apiSuccess (返回参数说明) {String} price  单价
     * @apiSuccess (返回参数说明) {String} stock_date  入库日期
     * @apiSuccess (返回参数说明) {String} min  最高警示数量
     * @apiSuccess (返回参数说明) {String} max  最低警示数量
     * @apiSuccess (返回参数说明) {String} admin_name  操作员
     *
     * @param $category
     * @param $order_number
     * @param $category_id
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     */
    public function getStockList($category, $order_number, $category_id, $page = 1, $size = 10)
    {
        $list = (new SkuService())->getStockList($category, $order_number, $category_id, $page, $size);

        return json($list);


    }

    /**
     * @api {POST} /api/v1/stock/handel  77-用品管理-删除入库记录
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  用品管理-删除入库记录
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * }
     * @apiParam (请求参数说明) {int} id 入库id
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $id
     * @return \think\response\Json
     * @throws OperationException
     */
    public function stockHandel($id)
    {
        $id = SkuStockT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $id]);
        if (!$id) {
            throw new OperationException(['code' => 401,
                'msg' => '删除操作失败',
                'errorCode' => 40002
            ]);
        }
        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/stock/export 78-用品管理-入库记录-导出
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  入库记录-导出
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/stock/export?category=''&order_number=''&category_id=''
     * @apiParam (请求参数说明) {String}  category 类别名称
     * @apiParam (请求参数说明) {String}  order_number 类别排序
     * @apiParam (请求参数说明) {String}  category_id 类别id
     *
     * @param $category
     * @param $order_number
     * @param $category_id
     * @return \think\response\Json
     */
    public function exportStock($category, $order_number, $category_id)
    {
        (new SkuService())->exportStock($category, $order_number, $category_id);
        return json(new SuccessMessage());
    }

    /**
     * @api {POST}  /api/v1/collar/use/save 79-用品管理-领用申请
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  用品管理-领用申请
     * @apiExample {post}  请求样例:
     *    {
     *       "sku_id":1,
     *       "count": 20,
     *       "time_begin": "2018-12-28",
     *       "time_end": "2018-12-30",
     *       "type": 1
     *     }
     * @apiParam (请求参数说明) {int} sku_id    用品id
     * @apiParam (请求参数说明) {int} count    数量
     * @apiParam (请求参数说明) {String} time_begin    领用日期
     * @apiParam (请求参数说明) {String} time_end   归还日期： 借用物品需填写
     * @apiParam (请求参数说明) {int} type   使用方式：1 | 借用；2 | 领用
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @return \think\response\Json
     * @throws \app\lib\exception\ParameterException
     * @throws \app\lib\exception\TokenException
     * @throws \think\Exception
     */
    public function collarUseSave()
    {
        $params = $this->request->param();
        $params['state'] = CommonEnum::STATE_IS_OK;
        $params['status'] = CommonEnum::READY_RECORD;
        $params['admin_id'] = \app\api\service\Token::getCurrentUid();
        (new SkuService())->collarUseSave($params);
        return json(new SuccessMessage());


    }

    /**
     * @api {GET} /api/v1/sku/apply/list 81-用品管理-领用列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 用品管理-领用列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/sku/apply/list?department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=1&page=1&size=20&sku=黑色签字笔&category=笔&type=borrow_t
     * @apiParam (请求参数说明) {String}  department 部门/默认传入全部
     * @apiParam (请求参数说明) {String}  username 申请人/默认传入全部
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {String}  category 分类
     * @apiParam (请求参数说明) {String}  type 使用方式/默认传入全部：borrow_t 借用；collar_use_t 领用
     * @apiParam (请求参数说明) {String}  sku 用品名称
     * @apiParam (请求参数说明) {int}  status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过；3 | 获取全部
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json}返回样例:
     * {"total":2,"per_page":"20","current_page":1,"last_page":1,"data":[{"id":3,"create_time":"2018-12-29 17:02:28","username":"朱明良","department":"办公室","phone":"18956225230","sku":"洗手液","category":"打印机耗材","format":"500ml","count":10,"type":"borrow_t","time_begin":"2018-12-29","time_end":"2018-12-30","admin_id":1,"state":1,"status":1},{"id":2,"create_time":"2018-12-29 17:02:17","username":"朱明良","department":"办公室","phone":"18956225230","sku":"洗手液","category":"打印机耗材","format":"500ml","count":10,"type":"borrow_t","time_begin":"2018-12-29","time_end":"2018-12-30","admin_id":1,"state":1,"status":1}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 申请id
     * @apiSuccess (返回参数说明) {String} create_time 日期
     * @apiSuccess (返回参数说明) {String} username 申请人
     * @apiSuccess (返回参数说明) {String} department 部门
     * @apiSuccess (返回参数说明) {String} phone 手机号码
     * @apiSuccess (返回参数说明) {String} sku  品名
     * @apiSuccess (返回参数说明) {String} category  类别
     * @apiSuccess (返回参数说明) {String} format  规格型号
     * @apiSuccess (返回参数说明) {int} count  数量
     * @apiSuccess (返回参数说明) {String} type  使用方式
     * @apiSuccess (返回参数说明) {String} time_begin   领用日期
     * @apiSuccess (返回参数说明) {String} time_end   归还日期（借用
     * @apiSuccess (返回参数说明) {int} status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过
     * @apiSuccess (返回参数说明) {int} admin_id  发起人id
     * @param $time_begin
     * @param $time_end
     * @param string $department
     * @param string $username
     * @param string $status
     * @param string $type
     * @param string $sku
     * @param string $category
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     */
    public function getListForApply($time_begin, $time_end, $department = "全部", $username = "全部", $status = "3", $type = "全部",
                                    $sku = '', $category = '', $page = 1, $size = 20)
    {
        $list = (new SkuService())->getListForApply($page, $size, $time_begin, $time_end, $department, $username, $status, $type, $sku, $category);
        return json($list);

    }

    /**
     * @api {GET} /api/v1/sku/apply/export 82-用品管理-领用列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 用品管理-领用列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/sku/apply/export?department=全部&username=朱明良&time_begin=2018-10-01&time_end=2018-12-31&status=1&sku=黑色签字笔&category=笔&type=borrow_t
     * @apiParam (请求参数说明) {String}  department 部门/默认传入全部
     * @apiParam (请求参数说明) {String}  username 申请人/默认传入全部
     * @apiParam (请求参数说明) {String}  time_begin 开始时间
     * @apiParam (请求参数说明) {String}  time_end 截止时间
     * @apiParam (请求参数说明) {String}  category 分类
     * @apiParam (请求参数说明) {String}  type 使用方式/默认传入全部：borrow_t 借用；collar_use_t 领用
     * @apiParam (请求参数说明) {String}  sku 用品名称
     * @apiParam (请求参数说明) {int}  status 流程状态：-1 | 不通过；0 | 保存中；1 | 流程中； 2 | 通过；3 | 获取全部
     * @param $time_begin
     * @param $time_end
     * @param string $department
     * @param string $username
     * @param string $status
     * @param string $type
     * @param string $sku
     * @param string $category
     * @return \think\response\Json
     */
    public function exportApply($time_begin, $time_end, $department = "全部", $username = "全部", $status = "3", $type = "全部",
                                $sku = '', $category = '')
    {
        (new SkuService())->exportApply($time_begin, $time_end, $department, $username, $status, $type, $sku, $category);
        return json(new SuccessMessage());

    }

    /**
     * @api {GET} /api/v1/sku/list 83-用品管理-获取用品列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  用品管理-获取用品列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/sku/list?c_id=1&name=
     * @apiParam (请求参数说明) {int}  c_id 类别id
     * @apiParam (请求参数说明) {String}  name 用品名称/默认为空
     * @apiSuccessExample {json}返回样例:
     * [{"id":1,"name":"洗手液","category":"打印机耗材","stock":"20"}]
     * @apiSuccess (返回参数说明) {int} id 用品 Id
     * @apiSuccess (返回参数说明) {String} name  用品名称
     * @apiSuccess (返回参数说明) {String} category  类别
     * @apiSuccess (返回参数说明) {String} stock  库存
     * @param $c_id
     * @param string $name
     * @return \think\response\Json
     */
    public function getSkuList($c_id, $name = '')
    {
        $list = SkuStockV::getList($c_id, $name);
        return json($list);

    }

    /**
     * @api {GET} /api/v1/sku/list/use 89-用品管理-领用申请-获取用品列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  用品管理-获取用品列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/sku/list/use
     * @apiSuccessExample {json}返回样例:
     * [{"id":1,"name":"洗手液","use_type":"使用"}]
     * @apiSuccess (返回参数说明) {int} id 用品 Id
     * @apiSuccess (返回参数说明) {String} name  用品名称
     * @apiSuccess (返回参数说明) {String} use_type  使用类别（用户发起申请需要匹配此参数和用户选择使用方式是否一致）
     * @return \think\response\Json
     */
    public function getSkuForUse()
    {
        $list = SkuT::getListForUse();
        return json($list);
    }


}