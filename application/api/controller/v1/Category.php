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
use app\api\validate\CategoryValidate;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;
use app\lib\exception\SuccessMessage;

class Category extends BaseController
{
    /**
     * @api {POST}  /api/v1/category/save 52-用品管理-新增分类
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  用品管理-新增分类
     * @apiExample {post}  请求样例:
     *    {
     *       "name": "打印机耗材",
     *       "code": "a1231213",
     *       "order": "1",
     *       "remark": "备注",
     *     }
     * @apiParam (请求参数说明) {String} name    分类名称
     * @apiParam (请求参数说明) {String} code    分类编号
     * @apiParam (请求参数说明) {String} order    序号
     * @apiParam (请求参数说明) {String} remark   备注
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
        \app\api\service\Token::getCurrentUid();
        (new CategoryValidate())->goCheck();
        $params = $this->request->param();
        $params['state'] = CommonEnum::STATE_IS_OK;
        $id = CategoryT::create($params);
        if (!$id) {
            throw  new OperationException();
        }
        return json(new SuccessMessage());

    }


    /**
     * @api {POST} /api/v1/category/handel  53-用品管理-删除分类
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  删除分类
     * @apiExample {POST}  请求样例:
     * {
     * "id": 1,
     * }
     * @apiParam (请求参数说明) {int} id 分类id
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     */
    public function handel()
    {
        $params = $this->request->param();
        $id = CategoryT::update(['state' => CommonEnum::STATE_IS_FAIL], ['id' => $params['id']]);
        if (!$id) {
            throw new OperationException(['code' => 401,
                'msg' => '删除操作失败',
                'errorCode' => 40002
            ]);
        }
        return json(new SuccessMessage());

    }


    /**
     * @api {POST} /api/v1/category/update 54-用品管理-修改分类
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  管理员修改分类
     * @apiExample {post}  请求样例:
     *    {
     *       "id": 1,
     *       "name": "打印机耗材",
     *       "code": "a1231213",
     *       "order": "1",
     *       "remark": "备注",
     *     }
     * @apiParam (请求参数说明) {int} id    分类id
     * @apiParam (请求参数说明) {String} name    分类名称
     * @apiParam (请求参数说明) {String} code    分类编号
     * @apiParam (请求参数说明) {String} order    序号
     * @apiParam (请求参数说明) {String} remark   备注
     * @apiSuccessExample {json} 返回样例:
     * {"msg": "ok","error_code": 0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return \think\response\Json
     * @throws OperationException
     */
    public function update()
    {
        $params = $this->request->param();
        $id = CategoryT::update($params, ['id' => $params['id']]);
        if (!$id) {
            throw new OperationException(['code' => 401,
                'msg' => '修改分类失败',
                'errorCode' => 120003
            ]);

        }
        return json(new  SuccessMessage());


    }


    /**
     * @api {GET} /api/v1/category/list 55-用品管理-获取分类列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取分类列表:下拉列表时，page 和 size不用传
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/category/list?page=1&size=20
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiParam (请求参数说明) {String} name 分类名称
     * @apiParam (请求参数说明) {String} code 编号
     * @apiParam (请求参数说明) {String} order 序号
     * @apiSuccessExample {json} 返回样例:
     * {"total":3,"per_page":100,"current_page":1,"last_page":1,"data":[{"id":3,"name":"本子","code":"a1111113","order":"1","remark":null},{"id":2,"name":"笔","code":"a1111112","order":"1","remark":null},{"id":1,"name":"打印机耗材","code":"a1111111","order":"1","remark":null}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} id 分类id
     * @apiSuccess (返回参数说明) {String} name 分类名称
     * @apiSuccess (返回参数说明) {String}  code 编号
     * @apiSuccess (返回参数说明) {String} order 序号
     * @apiSuccess (返回参数说明) {String} remark 备注
     * @param $page
     * @param $size
     * @param $name
     * @param $code
     * @param $order
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function getList($name = '', $code = '', $order = '', $page = 1, $size = 100)
    {
        $list = CategoryT::where('state', '=', CommonEnum::STATE_IS_OK)
            ->where(function ($query) use ($name) {
                if ($name) {
                    $query->where('name', 'like', $name);
                }
            })
            ->where(function ($query) use ($code) {
                if ($code) {
                    $query->where('code', 'like', $code);
                }
            })
            ->where(function ($query) use ($order) {
                if ($order) {
                    $query->where('order', 'like', $order);
                }
            })
            ->hidden(['create_time', 'update_time', 'state'])
            ->order('create_time desc')
            ->paginate($size, false, ['page' => $page]);
        return json($list);
    }


    /**
     * @api {GET} /api/v1/category  56-用品管理-指定分类信息
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  指定分类信息
     * http://mengant.cn/api/v1/category?id=1
     * @apiParam (请求参数说明) {int} id  分类id
     * @apiSuccessExample {json} 返回样例:
     * {"id":1,"name":"打印机耗材","code":"a1111111","order":"1","remark":null}
     * @apiSuccess (返回参数说明) {int} id 分类id
     * @apiSuccess (返回参数说明) {String} name 分类名称
     * @apiSuccess (返回参数说明) {String}  code 编号
     * @apiSuccess (返回参数说明) {String} order 序号
     * @apiSuccess (返回参数说明) {String} remark 备注
     * @param $id
     * @return \think\response\Json
     * @throws \think\Exception\DbException
     */
    public function getTheCategory($id)
    {
        $category = CategoryT::get($id)
            ->hidden(['create_time', 'update_time', 'state']);
        return json($category);
    }




}