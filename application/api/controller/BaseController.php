<?php

namespace app\api\controller;


use think\Controller;

class BaseController extends Controller
{
    function _empty(){
        header("HTTP/1.0 404 Not Found");
    }
}