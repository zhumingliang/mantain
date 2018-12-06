<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 10:27 AM
 */

namespace app\api\service;


use app\api\model\CarV;

class CarService
{
    public function getList($page,$size,$time_begin, $time_end, $department, $username, $status, $reason)
    {
        $list = CarV::getList($page,$size,$time_begin, $time_end, $department, $username, $status, $reason);
        return $list;


    }
}