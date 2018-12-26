<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/20
 * Time: 1:55 AM
 */

namespace app\api\model;


use app\lib\enum\CommonEnum;
use think\Model;

class AdminV extends Model
{

    public static function getList($username, $phone, $department, $post, $page, $size)
    {
        $list = self::where('state', CommonEnum::STATE_IS_OK)
            ->where(function ($query) use ($department) {
                if ($department && $department != "全部") {
                    $query->where('department', '=', $department);
                }
            })
            ->where(function ($query) use ($username) {
                if ($username && $username != "全部") {
                    $query->where('username', '=', $username);
                }
            })
            ->where(function ($query) use ($phone) {
                if ($phone && $phone != "全部") {
                    $query->where('phone', '=', $phone);
                }
            })
            ->where(function ($query) use ($post) {
                if ($post && $post != "全部") {
                    $query->where('role', 'like', '%' . $post . '%');
                }
            })
            ->paginate($size, false, ['page' => $page])
            ->toArray();
        return $list;


    }


}