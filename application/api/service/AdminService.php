<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/18
 * Time: 3:17 PM
 */

namespace app\api\service;


use app\api\model\AdminT;
use app\api\model\AdminV;
use app\api\model\Role;
use app\api\model\RunProcess;
use app\api\model\UserDepartmentT;
use app\lib\enum\CommonEnum;
use app\lib\exception\OperationException;
use app\lib\exception\ParameterException;
use function PHPSTORM_META\elementType;

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

    public static function getUserIdWithID($u_id)
    {
        $user = AdminT::get($u_id);
        return $user->user_id;


    }

    public static function getUserIdWithPhone($phone)
    {
        $user = AdminT::where('phone', $phone)->find();
        return $user->user_id;


    }

    public static function getUserIdWithDepartment($department)
    {
        $users = AdminT::where('department', 'in', $department)
            ->where('state', CommonEnum::STATE_IS_OK)
            ->field('user_id')->select();
        return self::preUsers($users);
    }


    public static function getUserIdWithRole($role)
    {
        $users = AdminV::where('role', 'in', $role)->field('user_id')
            ->where('state', CommonEnum::STATE_IS_OK)
            ->select()->toArray();

        return self::preUsers($users);


    }


    public static function getUserIdWithRunProcess($run_id)
    {
        $info = RunProcess::where('run_id', $run_id)->field('uid')->select();
        if (!count($info)) {
            return '';
        }
        $ids = self::preRunId($info);
        $users = AdminT::where('id', 'in', $ids)
            ->field('user_id')->select();
        return self::preUsers($users);

    }

    public static function getUserIdWithMeeting($department)
    {
        $users = AdminV::getAdminsForMeeting($department);
        return self::preUsers($users);
    }


    private static function preUsers($users)
    {
        $res = array();
        if ($users) {
            foreach ($users as $k => $v) {
                array_push($res, $v['user_id']);
            }

        }

        if (!count($res)) {
            return '';
        }
        return implode('|', $res);

    }

    private static function preRunId($info)
    {
        $res = array();
        if ($info) {
            foreach ($info as $k => $v) {
                array_push($res, $v['uid']);
            }

        }

        if (!count($res)) {
            return '';
        }
        return implode(',', $res);

    }


    public static function checkUserJiFu()
    {
        $department = Token::getCurrentTokenVar('department');
        if ($department == "机关服务中心") {
            return 1;
        }
        return 2;

    }


    public static function checkUserRole($department)
    {
        $role = Token::getCurrentTokenVar('role');
        $role_info = Role::where('id', $role)->find();
        if ($role_info['name'] == "局长" || $role_info['name'] == "机服中心管理员" || $role_info['name'] == "机服中心负责人") {
            return $department;
        } elseif ($role_info['name'] == "分管部门局领导") {
            return $department == "全部" ? self::getUserDepartment(Token::getCurrentUid()) : $department;
        } else {
            return Token::getCurrentTokenVar('department');
        }
    }


    public static function checkUserRoleWithGet($department)
    {
        $role = Token::getCurrentTokenVarWithGet('role');
        $role_info = Role::where('id', $role)->find();
        if ($role_info['name'] == "局长" || $role_info['name'] == "机服中心管理员" || $role_info['name'] == "机服中心负责人") {
            return $department;
        } elseif ($role_info['name'] == "分管部门局领导") {
            return $department == "全部" ? self::getUserDepartment(Token::getCurrentUidWithGet()) : $department;
        } else {
            return Token::getCurrentTokenVarWithGet('department');
        }
    }


    private static function getUserDepartment($u_id)
    {
        $department = UserDepartmentT::where('u_id', $u_id)->field('d_name')->select()->toArray();
        $departments = '';
        if (count($department)) {
            $arr = array();
            foreach ($department as $k => $v) {
                array_push($arr, $v['d_name']);

            }
            $departments = implode(',', $arr);
            return $departments;
        } else {
            return $departments;
        }

    }

    public function bindDepartment($u_id, $d_name)
    {
        $name_arr = explode(',', $d_name);
        $list = array();
        for ($i = 0; $i < count($name_arr); $i++) {
            $data = [
                'u_id' => $u_id,
                'd_name' => $name_arr[$i],
                'state' => CommonEnum::STATE_IS_OK
            ];
            array_push($list, $data);
        }
        $AD = new UserDepartmentT();
        $res = $AD->saveAll($list);
        if (!$res) {
            throw new OperationException();
        }
    }

    public function getDepartments()
    {
        $role = Token::getCurrentTokenVar('role');
        $role_info = Role::where('id', $role)->find();
        if ($role_info['name'] == "局长" || $role_info['name'] == "机服中心管理员" || $role_info['name'] == "机服中心负责人") {
            return UserDepartmentT::where('state', '=', 1)
                ->field('d_name as name')
                ->select()
                ->toArray();
        }
        if ($role_info['name'] == "分管部门局领导") {
            return UserDepartmentT::where('u_id', Token::getCurrentUid())
                ->where('state', '=', 1)
                ->field('d_name as name')
                ->select()
                ->toArray();
        } else {
            return [
                0 => [
                    'name' => Token::getCurrentTokenVar('department')
                ]
            ];
        }

    }

}