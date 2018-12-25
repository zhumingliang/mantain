<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/25
 * Time: 7:23 PM
 */

namespace app\api\service;


use think\facade\Request;

class SkuService extends BaseService
{
    /**
     * @param $skus Request
     */
    public function uploadSku($skus)
    {
        $info = $skus->move(ROOT_PATH . 'public' . DS . 'uploads');


    }

    private function saveExcel($skus)
    {

    }

}