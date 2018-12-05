<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/5
 * Time: 10:17 AM
 */

namespace app\api\service;


use app\api\model\RecreationalV;

class RecreationalService
{
    public function getList($page,$size,$time_begin, $time_end, $department, $username, $status, $space)
    {
        $list = RecreationalV::getList($page,$size,$time_begin, $time_end, $department, $username, $status, $space);
        return $list;


    }

}