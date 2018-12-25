<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/25
 * Time: 7:22 PM
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\SkuService;
use app\lib\exception\SkuException;

class Sku extends BaseController
{
    public function uploadSku()
    {
        $file_excel = request()->file('sku');
        if (is_null($file_excel)) {
            throw  new SkuException();
        }
        (new SkuService())->uploadSku($file_excel);
    }




}