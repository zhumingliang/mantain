<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 10:15 AM
 */

namespace app\api\service;


use app\api\model\OfficialReceptV;

class OfficialService
{
    public function getList($page,$size,$time_begin, $time_end, $department, $username, $status, $meal,$meal_type)
    {
        $list = OfficialReceptV::getList($page,$size,$time_begin, $time_end, $department, $username, $status, $meal,$meal_type);
        return $list;


    }

}