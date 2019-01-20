<?php
// 本类由系统自动生成，仅供测试用途
namespace app\index\Controller;
use app\lib\exception\SuccessMessage;
use think\Controller;
use think\Db;
use workflow\workflow;
use think\facade\Session;

class Flow extends Controller {
	
	protected function initialize()
    {
       $this->uid = session('uid');
	   $this->role = session('role');
    }
	/*流程监控*/
	public function index($map = [])
	{
		$workflow = new workflow();
		$flow = $workflow->worklist();
		$this->assign('list', $flow);
		return $this->fetch();
		
	}
	public function btn($wf_fid,$wf_type,$status)
	{
		$url = url("/index/flow/do_check/",["wf_type"=>$wf_type,"wf_title"=>'2','wf_fid'=>$wf_fid]);
		$url_star = url("/index/flow/start/",["wf_type"=>$wf_type,"wf_title"=>'2','wf_fid'=>$wf_fid]);

        $id=Db::name('log')->insertGetId(['msg'=>$wf_fid.'-'.$status]);

        switch ($status)
		{
		case 0:
		  return '<span class="btn  radius size-S" onclick=layer_show(\'发起工作流\',"'.$url_star.'","450","350")>发起工作流</span>';
		  break;
		case 1:
			$st = 0;
			$workflow = new workflow();
			$flowinfo = $workflow->workflowInfo($wf_fid,$wf_type);
			$user = explode(",", $flowinfo['status']['sponsor_ids']);
				if($flowinfo['status']['auto_person']==3||$flowinfo['status']['auto_person']==4){
					if (in_array($this->uid, $user)) {
						$st = 1;
					}
				}
				if($flowinfo['status']['auto_person']==5){
					if (in_array($this->role, $user)) {
						$st = 1;
					}
				}


			if($st != 1){
				 return '<span class="btn  radius size-S" onclick=layer_show(\'审核\',"'.$url.'","850","650")>审核</span>';
				}else{
				 return '<span class="btn  radius size-S">无权限</span>';
			}
		
		 
		  break;
		default:
		  return '';
		}
	}
	public function status($status)
	{
		switch ($status)
		{
		case 0:
		  return '<span class="label radius">保存中</span>';
		  
		  break;
		case 1:
		  return '<span class="label radius" >流程中</span>';
		  break;
		case 2:
		  return '<span class="label label-success radius" >审核通过</span>';
		  break;
		default: //-1
		  return '<span class="label label-danger radius" >退回修改</span>';
		}
		
	}
	
    /*发起流程，选择工作流*/
	public function start()
	{
		$wf_type = input('wf_type');
		$info = ['wf_type'=>input('wf_type'),'wf_title'=>input('wf_title'),'wf_fid'=>input('wf_fid')];
		$workflow = new workflow();
		$flow = $workflow->getWorkFlow($wf_type);
		$this->assign('flow',$flow);
		$this->assign('info',$info);
		return $this->fetch();
	}
	/*正式发起工作流*/
	public function statr_save()
	{
		$data = $this->request->param();
	//Array
        //(
        //    [wf_type] => news
        //    [wf_fid] => 16
        //    [wf_id] => 5
        //    [new_type] => 0
        //    [check_con] => 同意
        //)

		$workflow = new workflow();
		$flow = $workflow->startworkflow($data,$this->uid);
		if($flow['code']==1){
			return msg_return('Success!');
		}
	}
	/*工作流状态信息*/
	public function get_flowinfo()
	{
		$wf_type = input('wf_type');
		$wf_fid = input('wf_fid');
		$workflow = new workflow();
		$flowinfo = $workflow->workflowInfo($wf_fid,$wf_type);
		
	}
	public function do_check()
	{
		$wf_fid = input('wf_fid');
		$wf_type = input('wf_type');
		$info = ['wf_title'=>input('wf_title'),'wf_fid'=>$wf_fid,'wf_type'=>$wf_type];

		$workflow = new workflow();
		$flowinfo = $workflow->workflowInfo($wf_fid,$wf_type);

		//echo  json_encode($flowinfo);


		$this->assign('info',$info);
		$this->assign('flowinfo',$flowinfo);
		return $this->fetch();
	}
	public function do_check_save()
	{
        /**
         *
         * art
        btodo
        check_con	委屈委屈
        flow_id	13
        flow_process	106
        npid	107
        run_id	20
        run_process	35
        sing_st	0
        submit_to_save	ok
        wf_backflow
        wf_fid	13
        wf_singflow
        wf_title	2
        wf_type	news
         */
		$data = $this->request->param();
		$workflow = new workflow();
		$flowinfo = $workflow->workdoaction($data,$this->uid);
		//print_r($flowinfo);
	}
	public function ajax_back(){
		$pid = input('back_id');
		$run_id = input('run_id');
		$workflow = new workflow();
		$flowinfo = $workflow->getprocessinfo($pid,$run_id);
		return $flowinfo;
	}
	 public function upindex()
    {
        return $this->fetch('upload');
    }
	public function upload()
    {
        $files = $this->request->file('file');
        $insert = [];
        foreach ($files as $file) {
            $path = \Env::get('root_path') . '/public/uploads/';
            $info = $file->move($path);
            if ($info) {
                $data[] = $info->getSaveName();
            } else {
                $error[] = $file->getError();
            }
        }
        return msg_return($data,0,$info->getInfo('name'));
    }
}