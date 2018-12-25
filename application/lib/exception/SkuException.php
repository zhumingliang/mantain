<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/25
 * Time: 7:40 PM
 */

namespace app\lib\exception;


class SkuException extends BaseException
{
    public $code = 401;
    public $msg = '导入数据不能为空';
    public $errorCode = 90001;

}