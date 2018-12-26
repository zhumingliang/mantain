<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2018/9/18
 * Time: 下午11:07
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\model\ImgT;
use app\api\model\VillageRecordT;
use app\api\service\ImageService;
use app\api\validate\ImageValidate;
use app\lib\enum\CommonEnum;
use app\lib\exception\ImageException;
use app\lib\exception\SuccessMessage;
use WxMsg\WXBizDataCrypt;

class Image extends BaseController
{
    /**
     * @api {POST} /api/v1/image/upload  70-上传用品图片
     * @apiGroup  CMS
     * @apiVersion 1.0.1
     * @apiDescription
     * @apiSuccessExample {json} 返回样例:
     *{"id":17}
     * @apiSuccess (返回参数说明) {int} id 图片id
     */
    public function upload()
    {
        $file = request()->file('file');
        $res = ImageService::saveImageFromWX($file);
        return json([
            'id' => $res
        ]);
    }



}