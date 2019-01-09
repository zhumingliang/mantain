<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/7
 * Time: 11:03 AM
 */

namespace app\api\controller\v1;


use app\api\service\MsgService;
use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {
        $a='';
          echo strlen($a) ;

        //(new MsgService())->sendMsg("ZhuMingLiang", "测试");
    }

}