<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/2
 * Time: 3:32 PM
 */

namespace app\api\service;


use app\api\model\FeedbackImgT;
use app\api\model\FeedbackT;
use app\api\model\RepairImgT;
use app\api\model\RepairMachineT;
use app\api\model\RepairOtherT;
use app\api\model\RepairV;
use app\lib\enum\CommonEnum;
use app\lib\exception\FlowException;
use app\lib\exception\OperationException;
use app\lib\exception\SkuException;
use think\Db;
use think\Exception;

class RepairService extends BaseService
{

    /**
     * @param $params
     * @throws Exception
     */
    public function save($params)
    {
        Db::startTrans();
        try {
            $params['admin_id'] = Token::getCurrentUid();
            $params['status'] = CommonEnum::SAVE;
            $params['state'] = CommonEnum::STATE_IS_OK;
            $params['role'] = "a";
            $type = $params['type'];
            if ($type == CommonEnum::MACHINE) {
                $table_model = new RepairMachineT();
                $table_name = "repair_machine_t";
            } else {
                $table_model = new RepairOtherT();
                $table_name = "repair_other_t";
            }
            $repair = $table_model->create($params);
            $repair_id = $repair->id;
            if (!$repair_id) {
                throw new OperationException();
            }
            //处理图片
            if (isset($params['imgs']) && strlen($params['imgs'])) {
                $imgs = $params['imgs'];
                $relation = [
                    'name' => 'r_id',
                    'value' => $repair_id
                ];
                $imgs_res = self::saveImageRelation($imgs, $relation, $type);
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


            //启动工作流
            $check_res = (new FlowService())->saveCheck($repair_id, $table_name);
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


    /**
     * 保存申请和图片关联
     * @param $imgs
     * @param $type
     * @param $relation
     * @return bool
     * @throws \Exception
     */
    public static function saveImageRelationForFeedback($imgs, $relation, $type)
    {
        $data = ImageService::ImageHandelWithType($imgs, $relation, $type);
        $demandImgT = new FeedbackImgT();
        $res = $demandImgT->saveAll($data);
        if (!$res) {
            return false;
        }
        return true;

    }

    public static function saveImageRelation($imgs, $relation, $type)
    {
        $data = ImageService::ImageHandelWithType($imgs, $relation, $type);
        $demandImgT = new RepairImgT();
        $res = $demandImgT->saveAll($data);
        if (!$res) {
            return false;
        }
        return true;

    }


    public function getList($page, $size, $time_begin, $time_end, $department, $username, $status)
    {
        $list = RepairV::getList($page, $size, $time_begin, $time_end, $department, $username, $status);
        return $list;
    }

    public function export($time_begin, $time_end, $department, $username, $status)
    {
        $list = RepairV::export($time_begin, $time_end, $department, $username, $status);
        $list = $this->prefixStatus($list);
        $header = array(
            '日期',
            '申请人',
            '部门',
            '物品名称',
            '具体门牌位置',
            '详细描述',
            '反馈说明',
            '状态',
        );
        $file_name = '报修列表-导出' . '-' . date('Y-m-d', time()) . '.csv';
        $this->put_csv($list, $header, $file_name);


    }


}