<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/18
 * Time: 3:17 PM
 */

namespace app\api\service;


use app\api\model\AdminT;
use app\api\model\Role;
use app\lib\exception\OperationException;
use app\lib\exception\ParameterException;

class AdminService
{
    public static function getAdminRoleID($role_name)
    {
        $role = Role::where('name', $role_name)
            ->find();
        return $role->id;

    }


    public static function updateRole($role_id, $admin_id)
    {
        if (!strlen($admin_id)) {
            throw new ParameterException();

        }
        $admin = new AdminT();
        $admin_arr = explode(',', $admin_id);
        $list = array();
        for ($i = 0; $i < count($admin_arr); $i++) {

            $list[] = ['id' => $admin_arr[$i], 'role' => $role_id];
        }
        $res = $admin->saveAll($list);
        if (!$res) {
            throw new OperationException();
        }

    }


    public static function updatePost($mobile, $post)
    {
        $res = AdminT::update(['post' => $post], ['phone' => $mobile]);
        if (!$res) {
            throw new OperationException();
        }

    }


    public static function updateDepartment($mobile, $department)
    {
        $res = AdminT::update(['department' => $department], ['phone' => $mobile]);
        if (!$res) {
            throw new OperationException();
        }

    }

}