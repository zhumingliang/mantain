<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/30
 * Time: 1:39 AM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\AdminJoinT;
use app\api\model\AdminT;
use app\api\model\AdminV;
use app\api\model\Role;
use app\api\model\UserT;
use app\api\service\AdminService;
use app\api\validate\AdminValidate;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\TokenException;


class Admin extends BaseController
{

    /**
     * @api {POST} /api/v1/admin/username/update  3-用户-修改登录账号
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  后台用户修改用户名
     * @apiExample {post}  请求样例:
     *    {
     *       "pwd": "a123456",
     *       "account": "修改名字"
     *     }
     * @apiParam (请求参数说明) {String} pwd   密码
     * @apiParam (请求参数说明) {String} account  登录账号
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @param $pwd
     * @param $username
     * @return Json
     * @throws TokenException
     * @throws \think\Exception
     */
    public function updateUserName($pwd, $account)
    {
        $id = \app\api\service\Token::getCurrentUid();
        $admin = AdminT::where('id', $id)->find();
        if (sha1($pwd) != $admin->pwd) {
            throw new TokenException([
                'code' => 401,
                'msg' => '密码不正确',
                'errorCode' => 30002
            ]);
        }

        $res = AdminT::update(['account' => $account], ['id' => $id]);
        if (!$res) {
            throw new TokenException(
                [
                    'code' => 401,
                    'msg' => '修改密码失败',
                    'errorCode' => 30003

                ]
            );

        }

        return json(new SuccessMessage());

    }

    /**
     * @api {POST} /api/v1/admin/pwd/update  4-CMS-用户-修改密码
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  后台用户修改账号密码
     * @apiExample {post}  请求样例:
     *    {
     *       "new_pwd": "aaaaaa",
     *       "old_pwd": "a123456"
     *     }
     * @apiParam (请求参数说明) {String} new_pwd   新密码
     * @apiParam (请求参数说明) {String} old_pwd   旧密码
     *
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     *
     * @param $old_pwd
     * @param $new_pwd
     * @return Json
     * @throws TokenException
     * @throws \think\Exception
     */
    public function updatePwd($old_pwd, $new_pwd)
    {
        $id = \app\api\service\Token::getCurrentUid();
        $admin = AdminT::where('id', $id)->find();

        if (sha1($old_pwd) != $admin->pwd) {
            throw new TokenException([
                'code' => 401,
                'msg' => '密码不正确',
                'errorCode' => 30002
            ]);
        }

        $res = AdminT::update(['pwd' => sha1($new_pwd)], ['id' => $id]);
        if (!$res) {
            throw new TokenException(
                [
                    'code' => 401,
                    'msg' => '修改密码失败',
                    'errorCode' => 30003

                ]
            );

        }
        return json(new SuccessMessage());


    }

    /**
     * @api {POST} /api/v1/admin/save  41-CMS-新增用户信息
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  饭堂系统同步用户信息
     * @apiExample {post}  请求样例:
     *    {
     *       "phone": "18956225230",
     *       "username": "张三",
     *       "card": "123456",
     *       "type": "正式员工",
     *       "department": "机关服务中心"
     *     }
     * @apiParam (请求参数说明) {String} phone   手机号
     * @apiParam (请求参数说明) {String} username   用户名
     * @apiParam (请求参数说明) {String} card   卡号
     * @apiParam (请求参数说明) {String} type   员工类型
     * @apiParam (请求参数说明) {String} department   部门
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @return \think\response\Json
     * @throws OperationException
     */
    public function save()
    {
        $params = $this->request->param();
        $params['account'] = $params['phone'];
        $params['pwd'] = sha1('a111111');
        //$params['role'] = AdminService::getAdminRoleID($params['role']);
        $res = AdminT::create($params);
        if (!$res) {
            throw new OperationException();
        }
        return json(new SuccessMessage());
    }

    /**
     * @api {GET} /api/v1/admin/list 46-获取用户列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取用户列表
     * @apiExample {get} 请求样例:
     * {"total":440,"per_page":"2","current_page":1,"last_page":220,"data":[{"id":1,"username":"朱明良","phone":"18956225230","post":null,"role":null,"state":1,"department":"办公室","account":"admin"},{"id":2,"username":"部门负责人","phone":"13725305169","post":null,"role":"机服中心负责人","state":1,"department":"办公室","account":"1"}]}
     * @apiParam (请求参数说明) {String}  department 部门/默认传入全部
     * @apiParam (请求参数说明) {String}  post 职务/默认传入全部
     * @apiParam (请求参数说明) {String}  username  姓名
     * @apiParam (请求参数说明) {String}  phone  手机号
     * @apiParam (请求参数说明) {int} page 当前页码
     * @apiParam (请求参数说明) {int} size 每页多少条数据
     * @apiSuccessExample {json}返回样例:
     * {"total":11,"per_page":20,"current_page":1,"last_page":1,"data":[{"id":1,"username":"朱明良","phone":"18956225230","role":null,"state":1,"department":"办公室","account":"admin"},{"id":2,"username":"部门负责人","phone":"1","role":"部门负责人","state":1,"department":"测试1","account":"1"},{"id":3,"username":"机服中心","phone":"2","role":"机服中心管理员","state":1,"department":"","account":"2"},{"id":4,"username":"机服中心负责人","phone":"3","role":"机服中心负责人","state":1,"department":"","account":"3"},{"id":5,"username":"部门主管领导","phone":"4","role":"部门主管领导","state":1,"department":"","account":"4"},{"id":6,"username":"普通员工","phone":"5","role":"普通员工","state":1,"department":"","account":"5"},{"id":7,"username":"部门局领导","phone":"6","role":"分管部门局领导","state":1,"department":"","account":"6"},{"id":8,"username":"部门主管局长","phone":"7","role":"部门主管局长","state":1,"department":"","account":"7"},{"id":9,"username":"机服主管局长","phone":"8","role":"机服主管局长","state":1,"department":"","account":"8"},{"id":10,"username":"局长","phone":"9","role":"局长","state":1,"department":"","account":"9"},{"id":11,"username":"队长","phone":"10","role":"车队","state":1,"department":"","account":"10"}]}
     * @apiSuccess (返回参数说明) {int} total 数据总数
     * @apiSuccess (返回参数说明) {int} per_page 每页多少条数据
     * @apiSuccess (返回参数说明) {int} current_page 当前页码
     * @apiSuccess (返回参数说明) {int} last_page 最后页码
     * @apiSuccess (返回参数说明) {int} id 用户id
     * @apiSuccess (返回参数说明) {String} username 姓名
     * @apiSuccess (返回参数说明) {String} phone 手机号
     * @apiSuccess (返回参数说明) {String} account 账号
     * @apiSuccess (返回参数说明) {String} post 职务
     * @apiSuccess (返回参数说明) {String} role 角色
     * @apiSuccess (返回参数说明) {String} department 部门
     *
     * @param $username
     * @param $phone
     * @param $department
     * @param $post
     * @param int $page
     * @param int $size
     * @return \think\response\Json
     */
    public function getList($username, $phone, $department, $post, $page = 1, $size = 20)
    {
        $list = AdminV::getList($username, $phone, $department, $post, $page, $size);
        return json($list);

    }

    /**
     * @api {GET} /api/v1/role/list 47-获取用户角色列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  获取用户列表
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/role/list
     * @apiSuccessExample {json}返回样例:
     * [{"id":26,"name":"管理员"},{"id":27,"name":"普通员工"},{"id":28,"name":"部门负责人"},{"id":29,"name":"分管部门局领导"},{"id":30,"name":"部门主管领导"},{"id":31,"name":"部门主管局长"},{"id":33,"name":"机服中心管理员"},{"id":34,"name":"机服中心负责人"},{"id":35,"name":"机服主管局长"},{"id":36,"name":"局长"},{"id":37,"name":"车队"}]
     * @apiSuccess (返回参数说明) {int} id 用户id
     * @apiSuccess (返回参数说明) {String} name 角色名称
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getRoleList()
    {
        $list = Role::where('status', CommonEnum::STATE_IS_OK)
            ->field('id,name')
            ->select();
        return json($list);
    }

    /**
     * @api {POST} /api/v1/admin/role/update  48-更新用户角色
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 更新用户角色
     * @apiExample {post}  请求样例:
     *    {
     *       "role_id": 20,
     *       "admin_id": 1,2,3,4
     *     }
     * @apiParam (请求参数说明) {int} role_id   角色id
     * @apiParam (请求参数说明) {String} admin_id   用户uid，多个用逗号隔开。
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $role_id
     * @param $admin_id
     * @return \think\response\Json
     * @throws OperationException
     * @throws \app\lib\exception\ParameterException
     */
    public function updateRole($role_id, $admin_id)
    {
        AdminService::updateRole($role_id, $admin_id);
        return json(new SuccessMessage());
    }

    /**
     * @api {POST} /api/v1/admin/post/update  57-更新用户职务
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription 更新用户职务
     * @apiExample {post}  请求样例:
     *    {
     *       "mobile": 18956225230,
     *       "post":科长
     *     }
     * @apiParam (请求参数说明) {String} mobile   手机号
     * @apiParam (请求参数说明) {String} post   职务
     * @apiSuccessExample {json} 返回样例:
     * {"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误代码 0 表示没有错误
     * @apiSuccess (返回参数说明) {String} msg 操作结果描述
     * @param $mobile
     * @param $post
     * @return \think\response\Json
     * @throws OperationException
     */
    public function updatePost($mobile, $post)
    {
        AdminService::updatePost($mobile, $post);
        return json(new SuccessMessage());
    }

    /**
     * @api {GET} /api/v1/admins/access 58-新增门禁权限-获取用户列表
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  新增门禁权限-获取用户列表(由于用户太多，我默认返回50条数据)
     * @apiExample {get} 请求样例:
     * http://maintain.mengant.cn/api/v1/admins/access?key=''
     * @apiParam (请求参数说明) {String}  key  关键字
     * @apiSuccessExample {json}返回样例:
     * [{"id":43,"username":"张先林","post":"副主任"},{"id":54,"username":"林泉鑫","post":"协税员"},{"id":56,"username":"林超凡","post":"科长"},{"id":85,"username":"林健生","post":"科员"},{"id":111,"username":"林 虹","post":"科员"},{"id":135,"username":"林里清","post":"科员"},{"id":146,"username":"林惠洁","post":"科员"},{"id":165,"username":"林枫浩","post":"科员"},{"id":171,"username":"林炜超","post":"科员"},{"id":210,"username":"林国标","post":"副主任科员"},{"id":243,"username":"林苗秀","post":"协税员"},{"id":251,"username":"林英杰","post":"正科长"},{"id":262,"username":"林创龙","post":"副主任科员"},{"id":322,"username":"林力新","post":"协税员"},{"id":339,"username":"林振宇","post":"事业干部"},{"id":356,"username":"林杰军","post":"科员"},{"id":364,"username":"林均杰","post":"事业干部"},{"id":425,"username":"林耀汉","post":"协税员"},{"id":430,"username":"冯恒林","post":"协税员"},{"id":440,"username":"林祖德","post":"协税员"}]
     * @apiSuccess (返回参数说明) {int} id 用户id
     * @apiSuccess (返回参数说明) {String} username 姓名
     * @apiSuccess (返回参数说明) {String} post 职务
     * @param string $key
     * @return \think\response\Json
     */
    public function getListForAccess($key = '')
    {
        $admins = AdminT::getAdminsForAccess($key);
        return json($admins);


    }


}