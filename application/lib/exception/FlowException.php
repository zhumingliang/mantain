<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/10/30
 * Time: 1:56 AM
 */

namespace app\lib\exception;


class FlowException extends BaseException
{
    public $code = 401;
    public $msg = '发起流程失败';
    public $errorCode = 70001;

}