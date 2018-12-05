<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/6
 * Time: 1:35 AM
 */

namespace app\api\service;


use app\api\model\SpaceMultiV;

class MultiService
{
    public function getList($time_begin, $time_end, $department, $username, $status, $page, $size, $space)
    {
        $list = SpaceMultiV::getList($page, $size, $time_begin, $time_end, $department, $username, $status, $space);
        return $list;


    }
}