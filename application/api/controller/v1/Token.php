<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/5/27
 * Time: 上午9:53
 */

namespace app\api\controller\v1;


use app\api\model\FormidT;
use app\api\model\TestT;
use app\api\model\UserT;
use app\api\service\AdminToken;
use app\api\service\UserToken;
use app\api\service\UserTokenService;
use app\api\validate\TokenGet;
use app\lib\enum\CommonEnum;
use app\lib\exception\SuccessMessage;
use app\lib\exception\TokenException;
use think\Controller;
use think\facade\Cache;
use think\facade\Request;

class Token extends Controller
{
    /**
     * @api {GET} /api/v1/token/admin  1-CMS获取登陆token
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription  后台用户登录
     * @apiExample {post}  请求样例:
     *    {
     *       "account": "18956225230",
     *       "pwd": "a123456"
     *     }
     * @apiParam (请求参数说明) {String} account  账号
     * @apiParam (请求参数说明) {String} pwd   用户密码
     * @apiSuccessExample {json} 返回样例:
     * {"u_id":1,"username":"朱明良","account":"admin","role":1,"token":"7488c7a7b1f79ed99b319f141637519c"}
     * @apiSuccess (返回参数说明) {int} u_id 用户id
     * @apiSuccess (返回参数说明) {String} username 用户名称
     * @apiSuccess (返回参数说明) {String} account 账号
     * @apiSuccess (返回参数说明) {int} role 用户角色：暂定
     * @apiSuccess (返回参数说明) {String} token 口令令牌，每次请求接口需要放在header里传入，有效期 2 hours
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function getAdminToken()
    {
        $account = $this->request->param('account');
        $pwd = $this->request->param('pwd');
        (new TokenGet())->scene('pc')->goCheck();
        $at = new AdminToken($account, $pwd);
        $token = $at->get();
        return json($token);
    }

    /**
     * @api {GET} /api/v1/token/login/out  2-CMS退出登陆
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription CMS退出当前账号登陆。
     * @apiExample {get}  请求样例:
     * http://maintain.mengant.cn/api/v1/token/loginOut
     * @apiSuccessExample {json} 返回样例:
     *{"msg":"ok","errorCode":0}
     * @apiSuccess (返回参数说明) {int} error_code 错误码： 0表示操作成功无错误
     * @apiSuccess (返回参数说明) {String} msg 信息描述
     *
     * @return \think\response\Json
     */
    public function loginOut()
    {
        $token = \think\facade\Request::header('token');
        Cache::rm($token);
        return json(new SuccessMessage());
    }


    /**
     * @api {GET} /api/v1/token/user 42-微信端获取授权token
     * @apiGroup  WX
     * @apiVersion 1.0.1
     * @apiDescription  微信端获取授权token
     * @apiExample {get}  请求样例:
     * http://mengant.cn/api/v1/token/user?code=mdksk
     * @apiParam (请求参数说明) {String} code  微信code
     * {"u_id":1,"username":"朱明良","account":"admin","role":1,"token":"7488c7a7b1f79ed99b319f141637519c"}
     * @apiSuccess (返回参数说明) {int} u_id 用户id
     * @apiSuccess (返回参数说明) {String} username 用户名称
     * @apiSuccess (返回参数说明) {String} account 账号
     * @apiSuccess (返回参数说明) {int} role 用户角色：暂定
     * @apiSuccess (返回参数说明) {String} token 口令令牌，每次请求接口需要放在header里传入，有效期 2 hours
     * @return \think\response\Json
     * @throws TokenException
     */
    public function getWXToken()
    {
        if (isset($_GET['code'])) {
            $token = (new UserTokenService())->getToken($_GET['code']);
            return json($token);

        } else {
            //跳转进行code获取
            $codeUrl = config('wx.wx_code_url');
            $appID = config('wx.app_id');
            $WEB_HOST = config('setting.domain') . Request::path();
            $codeUrl = sprintf($codeUrl, $appID, urlencode($WEB_HOST));
            Header("Location: $codeUrl");
        }


    }

}
