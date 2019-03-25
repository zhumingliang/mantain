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

       $this->redirect('http://jmswj.e-irobot.com:1035/taxs/index.html#/login');
    }

}