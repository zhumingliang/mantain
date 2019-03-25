<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019-03-25
 * Time: 21:50
 */

namespace app\api\controller;


use think\Controller;

class Index extends Controller
{
    public function index()
    {

        $this->redirect('http://jmswj.e-irobot.com:1035/taxs/index.html');
    }

}