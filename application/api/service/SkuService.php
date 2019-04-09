<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/12/25
 * Time: 7:23 PM
 */

namespace app\api\service;


use app\api\model\BorrowSkuT;
use app\api\model\BorrowT;
use app\api\model\CategoryT;
use app\api\model\CollarUseT;
use app\api\model\SkuApplyV;
use app\api\model\SkuImgT;
use app\api\model\SkuStockV;
use app\api\model\SkuT;
use app\api\model\StockV;
use app\api\model\UnitT;
use app\api\model\UseSkuT;
use app\lib\enum\CommonEnum;
use app\lib\enum\UserEnum;
use app\lib\exception\FlowException;
use app\lib\exception\OperationException;
use app\lib\exception\ParameterException;
use app\lib\exception\SkuException;
use think\Db;
use think\Exception;


class SkuService extends BaseService
{

    /**
     * 保存导入数据
     * @param $skus
     * @throws OperationException
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public function uploadSku($skus)
    {
        $date = $this->saveExcel($skus);
        $date = $this->prefixData($date);
        $sku = new SkuT();
        $res = $sku->saveAll($date);
        if (!$res) {
            throw new OperationException();
        }
    }

    /**
     * 新增用品
     * @param $params
     * @throws Exception
     */
    public function save($params)
    {
        Db::startTrans();
        try {
            $res = SkuT::create($params);
            if (!$res) {
                Db::rollback();
                throw new OperationException();
            }
            if (isset($params['imgs']) && strlen($params['imgs'])) {
                $imgs = $params['imgs'];
                $relation = [
                    'name' => 'sku_id',
                    'value' => $res->id
                ];
                $imgs_res = self::saveImageRelation($imgs, $relation);
                if (!$imgs_res) {
                    Db::rollback();
                    throw new SkuException(
                        ['code' => 401,
                            'msg' => '创建用品图片关联失败',
                            'errorCode' => 60002
                        ]
                    );
                }

            }

            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }

    }

    /**
     * 修改用品信息
     * @param $params
     * @throws Exception
     */
    public function updateSku($params)
    {
        Db::startTrans();
        try {
            if (isset($params['imgs']) && strlen($params['imgs'])) {
                $imgs = $params['imgs'];
                unset($params['imgs']);
                $relation = [
                    'name' => 'sku_id',
                    'value' => $params['id']
                ];
                $imgs_res = self::saveImageRelation($imgs, $relation);
                if (!$imgs_res) {
                    Db::rollback();
                    throw new SkuException(
                        ['code' => 401,
                            'msg' => '创建用品图片关联失败',
                            'errorCode' => 60002
                        ]
                    );
                }

            }

            $res = SkuT::update($params, ['id' => $params['id']]);
            if (!$res) {
                Db::rollback();
                throw new OperationException(
                    ['code' => 401,
                        'msg' => '用品修改失败',
                        'errorCode' => 600012
                    ]
                );
            }
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }


    }

    /**
     * 保存申请和图片关联
     * @param $imgs
     * @param $relation
     * @return bool
     * @throws \Exception
     */
    private static function saveImageRelation($imgs, $relation)
    {
        $data = ImageService::ImageHandel($imgs, $relation);
        $demandImgT = new SkuImgT();
        $res = $demandImgT->saveAll($data);
        if (!$res) {
            return false;
        }
        return true;

    }

    /**
     * 保存excel并获取excel中数据
     * @param $skus
     * @return array
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    private function saveExcel($skus)
    {
        $path = dirname($_SERVER['SCRIPT_FILENAME']) . '/static/excel';
        if (!is_dir($path)) {
            mkdir(iconv("UTF-8", "GBK", $path), 0777, true);
        }

        $info = $skus->move($path);
        $file_name = $info->getPathname();
        $result_excel = $this->import_excel($file_name);
        return $result_excel;

    }

    private function prefixData($result_excel)
    {
        $return_data = array();
        $categors = (new CategoryService())->getCategorys();
        $units = UnitT::getList();
        foreach ($result_excel as $k => $v) {
            //获取所有分组
            if ($k > 1 && !empty(preg_replace('# #', '', $v[0]))) {
                $params['c_id'] = empty($v[1]) ? 0 : $this->prefixValue($v[1], $categors);
                $params['code'] = empty($v[2]) ? 0 : $v[2];
                $params['name'] = $v[3];
                $params['unit_id'] = empty($v[4]) ? 0 : $this->prefixValue($v[4], $units);
                $params['pack'] = empty($v[5]) ? 0 : $v[5];
                $params['count'] = empty($v[6]) ? 0 : $v[6];
                $params['format'] = empty($v[7]) ? 0 : $v[7];
                $params['use_type'] = empty($v[8]) ? 0 : $v[8];
                $params['min'] = empty($v[9]) ? 0 : $v[9];
                $params['max'] = empty($v[10]) ? 0 : $v[10];
                $params['alert'] = empty($v[11]) ? 2 : $v[11];
                $params['admin_id'] = Token::getCurrentUid();
                $params['state'] = CommonEnum::STATE_IS_OK;

                array_push($return_data, $params);


            }

        }

        return $return_data;

    }

    private function prefixValue($value, $data)
    {
        if (!count($data)) {
            return 0;
        }

        foreach ($data as $k => $v) {
            if ($v['name'] == $value) {
                return $v['id'];
            }
        }

        return 0;
    }

    /**
     * @param $file
     * @return array
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    private function import_excel($file)
    {
        // 判断文件是什么格式
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        ini_set('max_execution_time', '0');

        // 判断使用哪种格式
        if ($extension == 'xlsx') {
            $objReader = new \PHPExcel_Reader_Excel2007();
            $objPHPExcel = $objReader->load($file);
        } else if ($extension == 'xls') {
            $objReader = new \PHPExcel_Reader_Excel5();
            $objPHPExcel = $objReader->load($file);
        } else if ($extension == 'csv') {
            $PHPReader = new \PHPExcel_Reader_CSV();

            //默认输入字符集
            $PHPReader->setInputEncoding('GBK');

            //默认的分隔符
            $PHPReader->setDelimiter(',');

            //载入文件
            $objPHPExcel = $PHPReader->load($file);
        }
        $sheet = $objPHPExcel->getSheet(0);
        // 取得总行数
        $highestRow = $sheet->getHighestRow();
        // 取得总列数
        $highestColumn = $sheet->getHighestColumn();
        //循环读取excel文件,读取一条,插入一条
        $data = array();
        //从第一行开始读取数据
        for ($j = 1; $j <= $highestRow; $j++) {
            //从A列读取数据
            for ($k = 'A'; $k <= $highestColumn; $k++) {
                // 读取单元格
                $data[$j][] = $objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue();
            }
        }
        return $data;
    }

    /**
     * 获取实时库存
     * @param $sku_id
     * @param $type
     * @param $count
     * @return float|int
     * @throws \think\exception\DbException
     */
    public function getStock($sku_id, $type, $count)
    {
        if ($type == 2) {
            $count = 0 - $count;
        }
        $stock_old = SkuStockV::getSkuStock($sku_id);
        $stock = $stock_old + $count;
        $sku = array();
        $info = array(
            'sku_id' => $sku_id,
            'sku_stock' => $stock
        );
        array_push($sku, $info);
       // $this->checkStockToSendMsg($sku);
        return $stock;
    }

    /**
     * @param $sku
     * @return int
     * @throws SkuException
     * @throws \think\exception\DbException
     */
    private function checkStockToSendMsg($sku)
    {

        foreach ($sku as $k => $v) {
            $sku_id = $v['sku_id'];
            $stock = $v['sku_stock'];
            $info = SkuT::get($sku_id);
            if (!$info){
                throw new SkuException([
                    'msg'=>'指定sku_id不存在'
                ]);
            }
            if ($info->alert == 1) {
                $min = $info->min;
                $max = $info->max;
                if ($stock < $min) {
                    $this->sendMsgWithStock($info->name, $stock, 1, $min);
                    return 1;
                }
                if ($stock > $max) {
                    $this->sendMsgWithStock($info->name, $stock, 2, $max);
                    return 1;
                }
            }
        }


    }

    /**
     * 发送库存提醒
     * @param $name
     * @param $stock
     * @param $type
     * @param $stander
     */
    private function sendMsgWithStock($name, $stock, $type, $stander)
    {
        if ($type == 1) {
            //库存不足
            $msg = "当前库存的%s量为%s瓶，低于最低警示数量%s瓶，请及时采购物品。";
        } else {
            $msg = "当前库存的%s量为%s瓶，高于最高警示数量%s瓶，请及时处理。";

        }
        $msg = sprintf($msg, $name, $stock, $stander);
        $user_ids = AdminService::getUserIdWithRole('管理员');
        (new MsgService())->sendMsg($user_ids, $msg);


    }

    public function getStockList($category, $order_number, $category_id, $page, $size)
    {
        return StockV::getList($category, $order_number, $category_id, $page, $size);
    }

    public function exportStock($category, $order_number, $category_id)
    {
        $list = StockV::exportStock($category, $order_number, $category_id);
        $header = array(
            '物品名称',
            '类别',
            '库存',
            '入库',
            '单价',
            '入库日期',
            '最高警示数量',
            '最低警示数量',
            '操作员'
        );
        $file_name = '入库记录—导出' . '-' . date('Y-m-d', time()) . '.csv';
        $this->put_csv($list, $header, $file_name);

    }

    /**
     * @param $params
     * @throws Exception
     * @throws ParameterException
     */
    public function collarUseSave($params)
    {
        $sku_id = $params['sku_id'];
        $sku_count = $params['sku_count'];

        $id_arr = explode(',', $sku_id);
        $count_arr = explode(',', $sku_count);

        if (count($id_arr) != count($count_arr)) {
            throw  new SkuException(
                ['code' => 401,
                    'msg' => '参数错误，商品id数量和商品数量参数位不匹配',
                    'errorCode' => 60002
                ]
            );

        }

        $sku = array();
        for ($i = 0; $i < count($id_arr); $i++) {
            $data = [
                'sku_id' => $id_arr[$i],
                'sku_count' => $count_arr[$i],
                'sku_stock' => $this->checkStock($id_arr[$i], $count_arr[$i])
            ];
            array_push($sku, $data);

        }
        $type = $params['type'];
        if ($type == CommonEnum::BORROW) {
            $params['borrow_return'] = 2;
            $this->saveBorrow($params, $sku);

        } else if ($type == CommonEnum::COLLAR_USE) {
            $this->saveCollarUse($params, $sku);

        } else {
            throw new ParameterException();
        }


       // $this->checkStockToSendMsg($sku);


    }

    private function checkStock($sku_id, $count)
    {
        $stock = SkuStockV::getSkuStock($sku_id);
        if ($stock - $count < 0) {
            throw  new SkuException(
                ['code' => 401,
                    'msg' => '领用失败,库存不足',
                    'errorCode' => 60002
                ]
            );
        }

        return $stock - $count;
    }

    private function saveBorrow($params, $sku)
    {

        Db::startTrans();
        try {
            //新增基本信息
            $params['actual_time'] = $params['time_end'];
            $res = BorrowT::create($params);
            if (!$res) {
                throw new OperationException();
            }
            //保存明细
            foreach ($sku as $k => $v) {
                $sku[$k]['b_id'] = $res->id;
            }
            $id = (new BorrowSkuT())->saveAll($sku);
            if (!$id) {
                throw new OperationException([
                    'msg' => "新增用品借用明细失败"
                ]);
            }
            //启动工作流
            $check_res = (new FlowService())->saveCheck($res->id, 'borrow_t');
            if (!$check_res == 1) {
                Db::rollback();
                throw new FlowException();
            }
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }


    }

    private function saveCollarUse($params, $sku)
    {

        Db::startTrans();
        try {
            //新增基本信息
            $res = CollarUseT::create($params);
            if (!$res) {
                throw new OperationException();
            }
            //保存明细
            foreach ($sku as $k => $v) {
                $sku[$k]['c_id'] = $res->id;
            }
            $id = (new UseSkuT())->saveAll($sku);
            if (!$id) {
                throw new OperationException([
                    'msg' => "新增用品借用明细失败"
                ]);
            }

            //启动工作流
            $check_res = (new FlowService())->saveCheck($res->id, 'collar_use_t');
            if (!$check_res == 1) {
                Db::rollback();
                throw new FlowException();
            }
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }


    }

    public function getListForApply($page, $size, $time_begin, $time_end, $department, $username, $status, $type, $sku, $category)
    {

        $list = SkuApplyV::getList($page, $size, $time_begin, $time_end, $department, $username, $status, $type, $sku, $category);
        $list=(new FlowService())->checkListStatus($list,'',false,true);
        return $list;
    }

    public function exportApply($time_begin, $time_end, $department, $username, $status, $type, $sku, $category)
    {

        $list = SkuApplyV::export($time_begin, $time_end, $department, $username, $status, $type, $sku, $category);
        $list = $this->prefixStatus($list);
        $header = array(
            '日期',
            '申请人',
            '部门',
            '手机号码',
            '品名',
            '类别',
            '规格型号',
            '数量',
            '使用方式',
            '领用日期',
            '归还日期（借用）',
            '实际归还日期',
            '状态',
        );
        $file_name = '用品管理-用品领用—导出' . '-' . date('Y-m-d', time()) . '.csv';
        $this->put_csv($list, $header, $file_name);
    }

    public function getNav()
    {
        $category = CategoryT::where('state', CommonEnum::STATE_IS_OK)
            ->field('id as c_id,name as category')
            ->select();

        $skus = SkuStockV::getListWithCategory();

        if (count($category) && count($skus)) {
            $category = $this->prefixSkuStock($category, $skus);
        }

        return $category;
    }

    private function prefixSkuStock($category, $skus)
    {
        foreach ($category as $k => $v) {
            $sku = array();
            foreach ($skus as $k2 => $v2) {
                if ($v2['c_id'] == $v['c_id']) {
                    array_push($sku, $skus[$k2]);
                    unset($skus[$k2]);
                }

            }
            $category[$k]['skus'] = $sku;

        }
        return $category;

    }

    public function getSkuStockList($id, $page, $size)
    {
        return StockV::getListForSku($id, $page, $size);

    }


}