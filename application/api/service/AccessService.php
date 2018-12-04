<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/3
 * Time: 4:15 PM
 */

namespace app\api\service;


use app\api\model\AccessControlV;

class AccessService
{
    public function getList($page,$size,$time_begin, $time_end, $department, $username, $status, $access)
    {

        $list = AccessControlV::getList($page,$size,$time_begin, $time_end, $department, $username, $status, $access);
        return $list;


    }

}