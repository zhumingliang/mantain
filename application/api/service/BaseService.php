<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/7
 * Time: 9:59 AM
 */

namespace app\api\service;


class BaseService
{
    /**
     * 导出数据到CSV文件
     * @param array $list 数据
     * @param array $title 标题
     * @param string $filename CSV文件名
     */
    public function put_csv($list, $title, $filename)
    {
        $file_name = $filename;
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . $file_name);
        header('Cache-Control: max-age=0');
        $file = fopen('php://output', "a");
        $limit = 1000;
        $calc = 0;
        foreach ($title as $v) {
            $tit[] = iconv('UTF-8', 'GB2312//IGNORE', $v);
        }
        fputcsv($file, $tit);
        foreach ($list as $v) {

            $calc++;
            if ($limit == $calc) {
                ob_flush();
                flush();
                $calc = 0;
            }
            foreach ($v as $t) {
                $t = is_numeric($t) ? $t . "\t" : $t;
                $tarr[] = iconv('UTF-8', 'GB2312//IGNORE', $t);
            }
            fputcsv($file, $tarr);
            unset($tarr);
        }
        unset($list);
        fclose($file);
        exit();
    }

    public function prefixStatus($list)
    {
        $status = [-1 => '回退修改', 0 => '保存中', 1 => '流程中', 2 => '通过'];
        if (count($list)) {
            foreach ($list as $k => $v) {
                $list[$k]['status']=$status[$v['status']];
            }

        }
        return $list;

    }


}