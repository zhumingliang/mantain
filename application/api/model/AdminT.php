<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/3
 * Time: 12:14 AM
 */


namespace app\api\model;

use app\lib\enum\CommonEnum;
use think\Model;

class AdminT extends Model
{
    public static function getAdminsForAccess($key)
    {
        $list = self::where('state', CommonEnum::STATE_IS_OK)
            ->where(function ($query) use ($key) {
                if ($key) {
                    $query->whereLike('username|post', '%' . $key . '%');
                }
            })
            ->field('id,username,post')
            ->limit(0, 50)
            ->select();

        return $list;

    }

    public static function getUserIdWithUserName($username)
    {
        $user = self::whereIn('username', $username)
            ->field('user_id')->select()->toArray();
        $user_ids = array();
        foreach ($user as $k => $v) {
            array_push($user_ids, $v['user_id']);
        }
        return implode('|', $user_ids);

    }


}