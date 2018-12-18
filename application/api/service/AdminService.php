<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/18
 * Time: 3:17 PM
 */

namespace app\api\service;


use app\api\model\Role;

class AdminService
{
    public static function getAdminRoleID($role_name)
    {
        $role = Role::where('name', $role_name)
            ->find();
        return $role->id;

    }

}