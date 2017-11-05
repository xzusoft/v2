<?php
// +----------------------------------------------------------------------
// | LubTMP  会员管理
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Crm\Controller;
use Common\Controller\ManageBase;
use Common\Model\Model;
class MemberController extends ManageBase{
	protected function _initialize() {
		parent::_initialize();
	}
	//会员管理
	function index()
	{
		$map['status'] = '1';
		$list = M('MemberType')->where($map)->order('status DESC,id DESC')->field('id,title')->select();
		$this->assign('data',$list)->display();
		//加载类型
		//根据类型读取列表
	}
	public function lists(){
		$groupid = I('id');    //客户分组id
		$type = I('type');
		$name = I('name');
		$level = I('level');//级别
		$status = I('status');
		$product_id = get_product('id');
		//$map["id"] = $groupid;
		//$map =  array('id'=>$groupid,'type'=>$type,'product_id'=>$product_id);
		$map =  array('groupid'=>$groupid);
		/*搜索查询*/
		if(!empty($name)){
			if($type <> '2'){
				$map['name'] = array("like","%".$name."%");
			}else{
				$map['nickname'] = array("like","%".$name."%");
			}
			$this->assign("name",$name);
		}
		$map['level'] = $level ? $level : '16';
		if(!empty($status)){
			$map['status'] = $status;
		}
		/*搜索END查询级别END*/
		if($type == '1'  || $type == '3'){
			//企业
			$db = "Crm";
		}elseif ($type == '4') {
			//个人
			$db = "User";
		}
		$this->basePage($db,$map,array('status'=>"DESC","id"=>"DESC"));
		$this->assign ('groupid',$groupid)
			 ->assign('type',$type)
			 ->assign('map',$map)
			 ->display();	
	}
	//会员类型
	public function types()
	{
		$this->basePage('MemberType','','id DESC,status DESC');
		$this->display();
	}
	public function add_type(){
		if(IS_POST){
			$pinfo = I('post.');
			$model = D('Crm/MemberType');
			if($model->insert($pinfo)){
				$this->srun('新增成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('新增失败!');
			}			
		}else{
			$this->display();
		}
	}
	public function del_type()
	{
		$id = I('get.id',0,intval);
		if($id == '1'){
			$this->erun('系统内置类型禁止删除!');
		}
		if(!empty($id)){
			$map = array("id"=>$id);
			$model = D('Item/MemberType');
			//停用状态的数据，删除会直接删除
			if($model->where($map)->getField('status') == '0'){
				$del = $model->where($map)->delete();
			}else{
				$del = $model->where($map)->setField('status','0');
			}
			if($del){
				$this->srun("删除成功!", array('tabid'=>$this->menuid.MODULE_NAME));
			}else {
				$this->erun('删除失败!');
			}
		}else{
			$this->erun('参数错误!');
		}
	}
	//会员列表
	public function member()
	{
		$model = D('Item/Member');
		$info = $model->where($map)->select();
		$this->display();
	}
	//新增会员
	public function add_member()
	{
		if(IS_POST){

		}else{
			$this->display();
		}
	}
	//类型配置
	public function config()
	{
		if(IS_POST){
			$map = array("id"=>$id);
			$model = D('Item/MemberType');
			$info = $model->where($map)->find('rule');
			$info['rule'] = json_decode($info['rule'],true);
			$rule = [

			];
			$info['rule']['year'] = 
			$status = $model->where($map)->setField('rule',json_encode($rule));
			//rule
		}else{
			$id = I('get.id',0,intval);
			if(!empty($id)){
				$map = array("id"=>$id);
				$model = D('Item/MemberType');
				$info = $model->where($map)->find();
				$this->assign('data',$info)->display();
			}else{
				$this->erun('参数错误!');
			}
		}
	}
	//类型详情
	public function public_type_info()
	{
		$id = I('get.id',0,intval);
		if(!empty($id)){
			$map = array("id"=>$id);
			$model = D('Crm/MemberType');
			$info = $model->where($map)->find();
			$this->display();
		}else{
			$this->erun('参数错误!');
		}
	}
	public function year()
	{
		$groupid = I('id');    //客户分组id
		$type = I('type');
		$name = I('name');
		$level = I('level');//级别
		$status = I('status');
		$product_id = get_product('id');
		//$map["id"] = $groupid;
		//$map =  array('id'=>$groupid,'type'=>$type,'product_id'=>$product_id);
		$map =  array('groupid'=>$groupid);
		/*搜索查询*/
		if(!empty($name)){
			if($type <> '2'){
				$map['name'] = array("like","%".$name."%");
			}else{
				$map['nickname'] = array("like","%".$name."%");
			}
			$this->assign("name",$name);
		}
		$map['level'] = $level ? $level : '16';
		if(!empty($status)){
			$map['status'] = $status;
		}
		/*搜索END查询级别END*/
		if($type == '1'  || $type == '3'){
			//企业
			$db = "Crm";
		}elseif ($type == '4') {
			//个人
			$db = "User";
		}
		$this->basePage($db,$map,array('status'=>"DESC","id"=>"DESC"));
		$this->assign ('groupid',$groupid)
			 ->assign('type',$type)
			 ->assign('map',$map)
			 ->display();
	}
	/**
	 * 年卡设置
	 * @Company  承德乐游宝软件开发有限公司
	 * @Author   zhoujing      <zhoujing@leubao.com>
	 * @DateTime 2017-11-06
	 * @return   [type]        [description]
	 */
	public function config_year()
	{
		$map = array("id"=>'1');
		$model = D('Crm/MemberType');
		$info = $model->where($map)->find();
		$info['rule'] = json_decode($info['rule'],true);
		if(IS_POST){
			$pinfo = I('post.');
			$rule = [
				'area' => $pinfo['area'],
				'day'  => (int)$pinfo['day'],
				'datetime' => [
					'starttime' => date('Ymd',strtotime($pinfo['starttime'])),
					'endtime'	=> date('Ymd',strtotime($pinfo['endtime']))
				],
				'overdue'	=> date('Ymd',strtotime($pinfo['overdue'])),//过期时间
			];
			$data = [
				'update_time' => time(),
				'money'		  => $pinfo['money'],
				'rule'		  => json_encode($rule),
			];
			if($model->where($map)->save($data)){
				$this->srun('配置成功!', array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun('配置失败!');
			}
		}
		$url = $this->config['siteurl'].'card/apply.html';
		$this->assign('url',$url)
			->assign('data',$info)
			->display();
	}
	//会员详情
	public function public_member()
	{
		$id = I('get.id',0,intval);
		if(!empty($id)){
			$map = array("id"=>$id);
			$model = D('Crm/Member');
			$info = $model->where($map)->find();
			$this->display();
		}else{
			$this->erun('参数错误!');
		}
	}
}