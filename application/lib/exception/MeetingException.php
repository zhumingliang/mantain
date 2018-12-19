<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/19
 * Time: 8:50 AM
 */

namespace app\lib\exception;


class MeetingException extends BaseException
{
    public $code = 401;
    public $msg = '查看指定会议室不存在';
    public $errorCode = 80001;
}