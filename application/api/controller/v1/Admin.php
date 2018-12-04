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
use app\api\model\UserT;
use app\api\validate\AdminValidate;
use app\lib\enum\CommonEnum;
use app\lib\enum\UserEnum;
use app\lib\exception\OperationException;
use app\lib\exception\SuccessMessage;
use app\lib\exception\TokenException;
use think\Db;
use think\Exception;
use think\response\Json;
use app\api\service\Token as TokenService;

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


}