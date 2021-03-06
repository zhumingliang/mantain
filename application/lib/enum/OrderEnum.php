<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/18
 * Time: 下午11:25
 */

namespace app\lib\enum;


class OrderEnum
{

    const DEMAND_SHOP_TAKING = 1;

    const DEMAND_SHOP_CONFIRM = 2;

    const DEMAND_SHOP_COMPLETE = 3;

    const DEMAND_NORMAL_TAKING = 1;

    const DEMAND_NORMAL_PAY = 2;

    const DEMAND_NORMAL_CONFIRM = 3;

    const DEMAND_NORMAL_COMMENT = 4;

    const DEMAND_NORMAL_COMPLETE = 5;


    const SERVICE_SHOP_CONFIRM = 1;

    const SERVICE_SHOP_BEGIN = 2;

    const SERVICE_SHOP_ING = 3;

    const SERVICE_SHOP_COMPLETE = 4;

    const SERVICE_NORMAL_BOOKING = 1;

    const SERVICE_NORMAL_PAY = 2;

    const SERVICE_NORMAL_CONFIRM = 3;

    const SERVICE_NORMAL_COMMENT = 4;

    const SERVICE_NORMAL_COMPLETE = 5;


}