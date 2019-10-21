<?php
/**
 * Created by PhpStorm.
 * User: mingliang
 * Date: 2019/1/7
 * Time: 10:29 AM
 */

namespace app\api\service;


use app\api\model\LogT;
use think\Exception;
use zml\tp_tools\Curl;

class MsgService
{
    private $msgUrl;
    private $agentid;

    public function __construct()
    {
        $url = config('wx.msg_url');
        $url = sprintf($url, (new AccessToken())->get());
        $this->msgUrl = $url;
        $this->agentid = config('wx.app_agent_id');
    }

    public function sendMsg($touser, $content)
    {
        $data = $this->preData($touser, $content);
        $res = Curl::post($this->msgUrl, $data);
        $res_obj = json_decode($res);
        if ($res_obj->errcode != 0) {
            LogT::create(['msg' => $res]);
        }
    }




    /**
     * 处理模板消息数据
     * @param $touser
     * @param $content
     * @param string $msgtype
     * @return false|string
     */
    private function preData($touser, $content, $msgtype = 'text')
    {
        $data = [
            "touser" => $touser,
            "toparty" => "",
            "totag" => "",
            "msgtype" => $msgtype,
            "agentid" => $this->agentid,
            "text" => [
                "content" => $content
            ]
        ];

        return json_encode($data);

    }


}