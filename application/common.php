<?php
/**
 * Pms
 * 公共方法
 * @2018年01月
 * @Gwb
 */

use think\facade\Session;
use think\Db;
use PHPMailer\PHPMailer\PHPMailer;


function ids_parse($str,$dot_tmp=',')
{
    if(!$str) return '';
    if(is_array($str))
    {
        $idarr = $str;
    }else
    {
        $idarr = explode(',',$str);
    }
    $idarr = array_unique($idarr);
    $dot = '';
    $idstr ='';
    foreach($idarr as $id)
    {
        $id = intval($id);
        if($id>0)
        {
            $idstr.=$dot.$id;
            $dot = $dot_tmp;
        }
    }
    if(!$idstr) $idstr=0;
    return $idstr;
}
// 数组保存到文件
function arr2file($filename, $arr='')
{
    if(is_array($arr)){
        $con = var_export($arr,true);
    } else{
        $con = $arr;
    }
    $con = "<?php\nreturn $con;\n?>";//\n!defined('IN_MP') && die();\nreturn $con;\n
    write_file($filename, $con);
}
function get_commonval($table,$id,$val)
{
    return Db($table)->where('id',$id)->value($val);
}
//文件写入
function write_file($l1, $l2='')
{
    $dir = dirname($l1);
    if(!is_dir($dir)){
        mkdirss($dir);
    }
    return @file_put_contents($l1, $l2);
}

//对象转化数组
function obj2arr($obj)
{
    return json_decode(json_encode($obj),true);
}
/**
 * ajax数据返回，规范格式
 */
function msg_return($msg = "操作成功！", $code = 0,$data = [],$redirect = 'parent',$alert = '', $close = false, $url = '')
{
    $ret = ["code" => $code, "msg" => $msg, "data" => $data];
    $extend['opt'] = [
        'alert'    => $alert,
        'close'    => $close,
        'redirect' => $redirect,
        'url'      => $url,
    ];
    $ret = array_merge($ret, $extend);
    return Response::create($ret, 'json');
}
/**
 * get_rolename 获取角色名
 */
function get_rolename($roleid)
{
    return Db('role')->where('id',$roleid)->value('name');
}


function getRandChar($length)
{
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol) - 1;

    for ($i = 0;
         $i < $length;
         $i++) {
        $str .= $strPol[rand(0, $max)];
    }

    return $str;
}

/**
 * 创建guid
 * @return string
 */
function guid()
{
    mt_srand((double)microtime() * 10000);
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = chr(45);
    $uuid = substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12);
    return $uuid;
}


/**
 * base64转图片
 * @param $base64
 * @return string
 * @throws Exception
 */
function base64toImg($base64)
{
    try {
        // $base64 = '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';

        if (empty($base64)) {
            return '';
        }
        $path = dirname($_SERVER['SCRIPT_FILENAME']) . '/static/imgs';
        if (!is_dir($path)) {
            mkdir(iconv("UTF-8", "GBK", $path), 0777, true);
        }
        $img = base64_decode($base64);
        $name = guid();
        $imgUrl = $path . '/' . $name . '.jpg';
        $a = file_put_contents($imgUrl, $img);
        $imgUrl2 = 'static/imgs/' . $name . '.jpg';
        return $a ? $imgUrl2 : '';
    } catch (Exception $e) {
        throw $e;
    }

}


/**
 * 日期加上指定天数
 * @param $count
 * @param $time_old
 * @return false|string
 */
function addDay($count, $time_old)
{
    $time_new = date('Y-m-d', strtotime('+' . $count . ' day',
        strtotime($time_old)));
    return $time_new;

}

/**
 * 日期减去指   定天数
 * @param $count
 * @param $time_old
 * @return false|string
 */
function reduceDay($count, $time_old)
{
    $time_new = date('Y-m-d', strtotime('-' . $count . ' day',
        strtotime($time_old)));
    return $time_new;

}

/**
 * 生成订单号
 * @return string
 */
function makeOrderNo()
{
    $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
    $orderSn =
        $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date(
            'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
            '%02d', rand(0, 99));
    return $orderSn;
}

/**
 * 日期加上指定时间
 * @param $count
 * @param $time_old
 * @param $time_type
 * @return false|string
 */
function addTime($count, $time_old, $time_type)
{
    $time_new = date('Y-m-d H:i:s', strtotime('+' . $count . $time_type,
        strtotime($time_old)));
    return $time_new;

}

function reduceTime($count, $time_old, $time_type)
{
    $time_new = date('Y-m-d H:i:s', strtotime('-' . $count . $time_type,
        strtotime($time_old)));
    return $time_new;

}


