<?php
// +----------------------------------------------------------------------
// | LubTMP 订单处理
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Controller;
use Libs\Service\Operate;
use Common\Controller\ManageBase;
use Libs\Service\Order;
use Common\Model\Model;
use Payment\Common\PayException;
use Payment\Client\Charge;
use Payment\Client\Query;
use Libs\Service\CheckStatus;
class OrderController extends ManageBase{	
	function _initialize(){
	 	parent::_initialize();
	}
	/*==================================华丽分割线  1 添加窗口订单====================================*/
	/*选座提交
	* @param $order_type int 1 窗口散客/团队选座 2 快捷散客/团队选座 3 政企订单收款
	*/
	function seatPost(){
		if(IS_POST){
			$pinfo = $_POST['info'];
			$plan = I('get.plan',0,'intval');
			$type = I('get.type',1,'intval');
			$uInfo = \Manage\Service\User::getInstance()->getInfo();//读取当前登录用户信息
			$type = '1'.$type;
			$order = new Order();
			$run = $order->rowSeat($pinfo,$type,$uInfo);
			//判断支付方式，微信支付和支付宝支付中断执行
			if($run != false){
				//支付方式影响返回结果
				// if(in_array($run['is_pay'],array('4','5'))){
				// 	$forwardUrl = U('Item/Order/public_payment',array('sn'=>$run['sn'],'plan'=>$plan,'is_pay'=>$run['is_pay'],'money'=>$run['money'],'order_type'=>1));
				// 	$title = "网银支付";
				// 	$width = '600';
				// 	$height = '400';
				// 	$pageId = 'payment';
				// }else{
				// 	$forwardUrl = U('Item/Order/drawer',array('sn'=>$run['sn'],'genre'=>1,'plan_id'=>$plan));
				// 	$title = "门票打印";
				// 	$width = '213';
				// 	$height = '208';
				// 	$pageId = 'print';
				// }
				$forwardUrl = U('Item/Order/drawer',array('sn'=>$run['sn'],'genre'=>1,'plan_id'=>$plan));
					$title = "门票打印";
					$width = '213';
					$height = '208';
					$pageId = 'print';
				$return = array(
					'statusCode' => '200',
					'title'		 =>	$title,
					'width'		 =>	$width,
					'height'	 =>	$height,
					'pageid' 	 => $pageId,
					'dialog'	 =>	true,
					'refresh'	 => 'work_seat',
					'forwardUrl' => $forwardUrl,
				);
				$message = "下单成功!单号".$run;
				D('Item/Operationlog')->record($message, 200);//记录售票员日报表
			}else{
				$return = array(
					'statusCode' => '300',
					'forwardUrl' => '',
					'message' => $order->error
				);
				$message = "下单失败!";
				D('Item/Operationlog')->record($message, 300);//记录售票员日报表
			}			
			//记录订单信息
			die(json_encode($return));
		}
	}
	
	/**
	 * 出票方式  订单出票
	 */
	function drawer(){
		if(IS_POST){
			$pinfo = I('post.');
			$this->assign('data',$pinfo);//传递参数
			$this->display();
		}else{
			$ginfo = I('get.');//获取订单号
			if(empty($ginfo)){
				$this->erun('参数错误!');
			}
			//会员卡临时凭证
			if((int)$ginfo['genre'] === 6){
				$return = $this->member_ticket($ginfo);

				$get_ticket_url = U('Item/Order/print_year',['id'=>$ginfo['id'],'genre'=>6]);
			}
			//常规门票打印
			if((int)$ginfo['genre'] === 1){
				$return = $this->default_ticket($ginfo);
				$get_ticket_url = U('Item/Order/printTicket',['sn'=>$ginfo['sn'],'plan_id'=>$ginfo['plan_id'],'genre'=>1]);
				
			}
			if(load_redis('get','lock_'.$ginfo['sn'])){
				$this->erun('订单锁定中~');
			}//dump($ginfo);
			$this->assign('get_ticket_url',$get_ticket_url);
			if($return['status']){
				$this->display($return['tpl']);
			}

		}	
	}
	//门票打印
	function printTicket(){
		$ginfo = I('get.');
		//订单状态校验
		$order_type = order_type($ginfo['sn']);

		$user = $ginfo['user'] ? $ginfo['user'] : 0;
		if(load_redis('get','lock_'.$ginfo['sn'])){
			$return = array(
				'status' => '2',
				'message' => '订单锁定中~'
			);
			die(json_encode($return));
		}
		//判断订单状态是否可执行此项操作
		if(in_array($order_type['status'], array('0','2','3','7','8','11'))){
			$return = array(
				'status' => '2',
				'message' => '订单状态不允许此项操作!'
			);
			die(json_encode($return));
		}
		//判断是否是二次打印
		if($order_type['status'] == '9' && empty($user)){
			$return = array(
				'status' => '2',
				'message' => '订单已打印!',
				'info'	=>  $ginfo,
			);
			die(json_encode($return));
		}
		$plan = F('Plan_'.$ginfo['plan_id']);
		if(empty($plan)){
			$plan = D('Plan')->where(['id'=>$ginfo['plan_id']])->field('id,product_type,seat_table,encry,starttime,endtime,product_id')->find();
		}
		if(empty($plan)){
			$return = array(
				'status' => '0',
				'message' => '订单读取失败!',
				'info'	=>  0,
			);
			die(json_encode($return));
		}
		/** 订单状态校验 
		$checkOrder = new CheckStatus();
		if(!$checkOrder->OrderCheckStatus($ginfo['sn'],2103)){
			$this->erun($checkOrder->error,array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
		}*/
		//更新门票打印状态
		$model = new Model();
		$model->startTrans();
		$table = $plan['seat_table'];
		//判断是否打印已检门票
		if((int)$this->procof['print_has'] === 1){
			$where = array('order_sn'=>$ginfo['sn'],'status'=>2);
		}else{
			$where = array('order_sn'=>$ginfo['sn']);
		}
		$list = M(ucwords($table))->where($where)->select();
		if($ginfo['type'] == '1'){
			//一人一票
			$orderId = D('Order')->where(['order_sn'=>$ginfo['sn']])->getField('id');

			//读取门票列表
			foreach ($list as $k=>$v){
				$info[] = re_print($plan['id'],$plan['encry'],$v,$plan['product_id'],$orderId, $ginfo['type']);
			}
		}else{
			//一单一票
			//读取订单信息  日期时间  人数  单价 10元/人
			$map['order_sn'] = $ginfo['sn'];
			$oinfo = D('Item/Order')->where($map)->relation(true)->find();
			//打票员名称
	        if($this->procof['print_user'] == '1'){
	            $info_user = \Manage\Service\User::getInstance()->username; 
	        }
	        //入场时间
	        if($this->procof['print_field'] == '1'){
				$statrtime = $plan['starttime'];
	            $end = date('H:i',strtotime("$statrtime -5 minute"));
	            $start = date('H:i',strtotime("$end -30 minute"));
	            $info_field = $start .'-'. $end;
	        }
	        //打印客源地 TODO
	        if($this->procof['print_to_guest'] == '1'){
	        	$oParam = unserialize($oinfo['info']);
	        	$guest_area = '';
	        }
			//判断是否是单一票型 
			foreach ($list as $k=>$v){
				$num[$v['price_id']]['number'] += 1;
				$sale = unserialize($v['sale']);
				$print = $v['print'] ? $v['print'] : '1';
				//$sn = \Libs\Service\Encry::encryption($plan['id'],$ginfo['sn'],$plan['encry'],$v['area'],$v['seat'],$print,$v['id'])."&".$oinfo['id']."^#";
				$sn = \Libs\Service\Encry::toQrData($v['id'],$oinfo['id'],$plan['id'],$print,$ginfo['type']);
				$info[$v['price_id']] = array(
					'discount'		=>	$sale['discount'],
					'field'			=>	$info_field,
					'games'			=>	$sale['games'],
					'plantime'		=>	date('Y-m-d',$plan['plantime']),//planShow($ginfo['plan_id'],1,2),
					'starttime'     =>  date('H:i',$plan['starttime']),
					'endtime'		=>	date('H:i',$plan['endtime']),
					'price'			=>	$sale['price'],
					'priceName'		=>	$sale['priceName'],
					'product_name' 	=>	$sale['product_name'],
					'remark'		=>	$sale['remark'],
					'remark_type'	=>	$sale['remark_type'],
					'sn'			=>	$sn,
					'sns'			=>	$ginfo['sn'],
					'user'			=>	$info_user,
					'number'		=>	$num[$v['price_id']]['number'],
					'guest_area'	=>	$guest_area
				);
			}
		}
		//更新门票打印状态
		$up_print = $model->table(C('DB_PREFIX'). $table)->where(array('order_sn'=>$ginfo['sn']))->setInc('print',1);	
		//判断订单类型
		$order_type = order_type($ginfo['sn']);
		//判断订单状态
		if($order_type['status'] == '9'){
			//二次打印处理
			$up_order = true;
			$type = '2';
		}else{
			//更新订单状态
			$up_order = $model->table(C('DB_PREFIX'). 'order')->where(array('order_sn'=>$ginfo['sn']))->setField('status',9);
			//渠道订单发送取票短信
			if($order_type['type'] == '2' || $order_type['type'] == '4'){
				$this->to_sms($order_type['user_id'],$ginfo['sn'],$order_type['plan_id']);
			}
			$type = '1';
		}
		if($up_print && $up_order){
			//记录打印日志
			print_log($ginfo['sn'],$user,$type,$order_type['channel_id'],'',count($list),1);
			$model->commit();//提交事务
			$return = array(
				'status' => '1',
				'message' => '订单读取成功!',
				'info'	=> $info ? $info : 0,
			);
		}else{
			$model->rollback();//事务回滚
			$return = array(
				'status' => '0',
				'message' => '订单读取失败1!',
				'info'	=>  0,
			);
		}
		die(json_encode($return));
	}
	/*发送取票短信
	*@param $user_id 下单人id 
	*@param $order_sn 订单号
	*@param $plan_id 销售计划id
	*/
	function to_sms($user_id,$order_sn,$plan_id){
		//根据售票员id获取售票员信息 当前登录用户 渠道订单打印完成后是否给下单人发送短信
		if($this->procof['print_sms'] == '1'){
			$name = \Item\Service\Partner::getInstance()->nickname;
			$user = M('User')->where(array('id'=>$user_id))->field('phone')->find();
			$info = array('phone'=>$user['phone'],'title'=>planShows($plan_id),'sn'=>$order_sn,'user'=>$name);
			\Libs\Service\Sms::order_msg($info,3);
		}
		return true;
	}
	
	/**
	 * 二次打印密码确认
	 */
	function to_print(){
		if(IS_POST){
			//密码验证
			$pinfo = I('post.');
			$user = M('Pwd')->where(array('id'=>$pinfo['user']))->find();
			if($user['password'] == md5($pinfo['password'])){
				session('author',$user['id']);
				$return = array(
					'statusCode' => '200',
					'rel'	 => 'page1',
					'title'	=> '门票打印',
					'popup'	=>	'dialog',
					'forward' => U('Item/Order/drawer',array('sn'=>$pinfo['sn'],'plan_id'=>$pinfo['plan_id'],'user'=>$user['id'],'act'=>$pinfo['act'],'genre'=>$pinfo['genre'])),
				);
				die(json_encode($return));
			}else{
				$this->erun("授权失败!");
			}
		}else{
			$ginfo = I('get.');
			$author = session('author');
			if(empty($author)){
				$user = M('Pwd')->where(array('status'=>1))->select();
				$this->assign('user',$user);
			}
			$this->assign('data',$ginfo)->display();
		}	
	}
	//年卡临时凭证
	public function member_ticket($ginfo)
	{
		$member = D('Member')->where(['id'=>$ginfo['id'],'status'=>1])
			->field('icreate_time,update_time',true)
			->find();
		if(empty($member)){
			$this->erun("未找到有效会员卡!");
		}
		//读取会员卡类型并读取模板
		$memType = F('MemGroup');
		if(empty($memType[$member['group_id']])){
			D('Crm/MemberType')->mem_group_cache();
			$memType = F('MemGroup');
		}
		//读取打印模板
		$this->getPrintTpl($memType[$member['group_id']]['print_tpl']);
		//传递参数
		$this->assign('data',$ginfo);
		return ['status'=>true,'tpl'=>'drawer',''];
	}
	//常规门票打印
	public function default_ticket($ginfo)
	{
		if(check_sn($ginfo['sn'],$ginfo['plan_id'])){
			$order_type = order_type($ginfo['sn']);
			if($order_type['is_print'] == '1'){
				$this->erun("预定成功,电子凭证入园,无需要打印纸质门票!");
			}
			//判断订单状态是否可执行此项操作
			if(in_array($order_type['status'], array('0','2','3','7','8','11'))){
				$this->erun("订单状态不允许此项操作!");
			}else{
				//加载当前打印模板 活动订单的打印模板
				if(!empty($ginfo['act'])){
					//获取活动指定打印模板
					$actPrint = D('Activity')->where(['id'=>$ginfo['act']])->getField('print_tpl');
					if(empty($actPrint)){$actPrint = $this->procof['print_tpl'];}
				}else{
					//读取默认模板
					$actPrint = $this->procof['print_tpl'];
				}
				$this->getPrintTpl($actPrint);
				//传递参数
				$this->assign('data',$ginfo);
				if($order_type['status'] == '9'){
					if(empty($ginfo['user'])){
						$user = M('Pwd')->where(array('status'=>1))->select();
						$this->assign('user',$user);
						return ['status'=>true,'tpl'=>'to_print'];
					}
				}
				return ['status'=>true,'tpl'=>'drawer'];
			}
		}else{
			$this->erun("订单已过期，无法出票!");
		}
	}
	public function getPrintTpl($actPrint)
	{
		//读取模板渲染
		$printTpl = D('Printer')->where(['id'=>$actPrint])->find();
		if(empty($printTpl)){
			$this->erun("未找到打印模板!");
		}
		$this->assign('printTpl',$printTpl);
	}
	//打印临时凭证
	function print_year(){
		//读取信息
		$id = I('get.id',0,intval);
		$user = $ginfo['user'] ? $ginfo['user'] : 0;
		if(empty($id)){
			$this->erun('参数错误!');
		}

		$info = D('Crm/Member')->where(['id'=>$id,'status'=>1])->field('id,idcard,verify,number')->find();
		if(empty($info)){
			$this->erun('未找到有效年票!');
		}
		
		$time = time();
		$sn = 'Y'.$time;
		$data = [
			'sn'		=>	$sn,
			'thetype'	=>	'2',//凭证类型
			'member_id'	=>	$info['id'],//凭证数据
			'datetime'	=>  strtotime(date('Y-m-d')),//入园日期时间
			'password'	=>	$this->yearPassword($info,$time),
			'status'	=>	1,
			'user_id'	=>  get_user_id(),
			'update_time'=>$time
		];
		//事务
		$logId = D('MemberLog')->add($data);
		//更新入园次数
		D('Item/Member')->where(['id'=>$id])->setInc('number',1);
		$code = $time.$logId.'^YK#';
		$info = [
			'sn'		=>	$code,
			'sns'		=>	$sn,
			'price'		=>	'会员卡',
			'plantime'	=>	date('Y-m-d'),
			'number'	=>	1,
		];
		$return = array(
			'status' => '1',
			'message' => '订单读取成功!',
			'info'	=> $info ? $info : 0,
		);
		//记录打印日志
		print_log($sn,$user,'1',0,'',1,1);
		die(json_encode($return));
	}
	public function yearPassword($info,$time)
	{
		return md5($info['id'].$info['verify'].$info['number'].$time);
	}
/*=============================华丽分割线  2 添加快捷订单====================================*/
	/*快捷售票*/
	function quickPost(){
		$pinfo = $_POST['info'];
		$plan = I('get.plan',0,'intval');
		$type = I('get.type',1,'intval');
		$uInfo = \Manage\Service\User::getInstance()->getInfo();//读取当前登录用户信息
		$type = '6'.$type;
		$order = new Order();
		$run = $order->quick($pinfo,$type,$uInfo);
		if($run != false){
			//支付方式影响返回结果 TODO 暂停前台微信支付宝收款
			// if(in_array($run['is_pay'],array('4','5'))){
			// 	$forwardUrl = U('Item/Order/public_payment',array('sn'=>$run['sn'],'plan'=>$plan,'is_pay'=>$run['is_pay'],'money'=>$run['money'],'order_type'=>2,'act'=>$run['act']));
			// 	$title = "网银支付";
			// 	$width = '600';
			// 	$height = '400';
			// 	$pageId = 'payment';
			// }else{
			// 	$forwardUrl = U('Item/Order/drawer',array('sn'=>$run['sn'],'plan_id'=>$plan,'act'=>$run['act'],'genre'=>1));
			// 	$title = "门票打印";
			// 	$width = '213';
			// 	$height = '208';
			// 	$pageId = 'print';
			// }
			$forwardUrl = U('Item/Order/drawer',array('sn'=>$run['sn'],'plan_id'=>$plan,'act'=>$run['act'],'genre'=>1));
				$title = "门票打印";
				$width = '213';
				$height = '208';
				$pageId = 'print';
			//禁止打印
			
			$return = array(
				'statusCode' => '200',
				'title'		 =>	$title,
				'width'		 =>	$width,
				'height'	 =>	$height,
				'pageid' 	 => $pageId,
				'dialog'	 =>	true,
				'refresh'	 => 'work_quick',
				'forwardUrl' => $forwardUrl,
			);
			$message = "下单成功!单号".$run;
			D('Item/Operationlog')->record($message, 200);//记录售票员日报表
		}else{
			$return = array(
				'statusCode' => '300',
				'forwardUrl' => '',
				'message' => $order->error
			);
			$message = "下单失败!";
			D('Item/Operationlog')->record($message, 300);//记录售票员日报表
		}			
		//记录订单信息
		die(json_encode($return));
	}
	//处理刷卡支付
	function public_payment(){
		if(IS_POST){
			$pinfo = $_POST['info'];
			$info = json_decode($pinfo,true);
			//pos 收费 现金支付
			if($info['order_type'] == '3'){
				//政企订单收款
				$where = array('order_sn'=>$info['sn'],'status'=>array('in','1'));
			}else{
				$where = array('order_sn'=>$info['sn'],'status'=>array('in','6,11'));
			}
			$oinfo = D('Item/Order')->where($where)->relation(true)->find();
			if(empty($info) || empty($oinfo)){die(json_encode(array('statusCode' => '400','msg' => $oinfo)));}
            //判断订单类型 scene 1选座订单6快捷售票
			if($info['pay_type'] == '1' || $info['pay_type'] == '6' || $info['pay_type'] == '3'){
				$return = $this->sweep_pay_seat($info,$oinfo);
			}else{
				$product = product_name($oinfo['product_id'],1);
				//构造支付订单数据
				$payData = [
				    "order_no"	=> $info['sn'],
				    "amount"	=> $oinfo['money'],// 单位为元 ,最小为0.01
				    "timeout_express" => time() + 600,// 表示必须 600s 内付款
				    "client_ip"	=> get_client_ip(),
				    "subject"	=> $product."门票",
				    "body"		=> $product."门票",//planShow($oinfo['plan_id'],1,1).$product."门票",
				    "show_url"  => 'http://www.leubao.com/',// 支付宝手机网站支付接口 该参数必须上传 。其他接口忽略
				    "extra_param"	=> '',
				    "terminal_id" => "web",// 终端设备号(门店号或收银设备ID) 默认值 web
				    "auth_code"	=> "",//s授权码
				];
			}
			if($info['pay_type'] == '4'){
				//支付宝支付
				$this->alipay_code($payData);
				$return = array(
					'statusCode' => '200'
				);
			}
			if($info['pay_type'] == '5'){
				//微信支付
				$result = $this->weixin_code($info['paykey'],$payData);
				if($result['errCode'] == USERPAYING){
					//用户支付中，需要输入密码
					$return = array(
						'statusCode' => '300',
						'message'=>'['.$result['errCode'].']'.$result['errMsg']
					);
				}elseif($result['result_code'] == 'SUCCESS' && $result['return_code'] == 'SUCCESS'){
					//更新支付日志，写入待排座队列
					$uppaylog = array('status'=>1,'out_trade_no'=>$result['transaction_id']);
                	$paylog = D('Manage/Pay')->where(array('order_sn'=>$result['out_trade_no'],'type'=>2))->save($uppaylog);
					$return = $this->sweep_pay_seat($info,$oinfo);
				}else{
					$return = array(
						'statusCode' => '400',
						'message'=>'['.$result['errCode'].']'.$result['errMsg']
					);
					error_insert($result['errMsg']);
				}
			}
			die(json_encode($return));
		}else{
			$ginfo = I('get.');
			$this->assign('ginfo',$ginfo)->display('payment');
		}
	}
	//支付宝扫码支付 当面付
	function alipay_code($odata)
	{	
	}
	//微信扫码支付
	function weixin_code($paykey,$payData)
	{	
		$config = load_payment('wx_bar');
		//记录支付日志
		//payLog();
		try {
			$payData['auth_code'] = $paykey;
			$payData['sub_appid'] =  $config['sub_appid'];
            $payData['sub_mch_id'] =  $config['sub_mch_id'];
		    $ret = Charge::run('wx_bar', $config, $payData);
		} catch (PayException $e) {
			//dump($e);
		    echo $e->errorMessage();
		    exit;
		}
		
		return $ret;
	}
	/*轮询支付日志查询支付结果*/
	function public_payment_results(){
		$ginfo = I('get.');
		//pos 收费 现金支付
		if($ginfo['order_type'] == '3'){
			//政企订单收款
			$where = array('order_sn'=>$ginfo['sn'],'status'=>array('in','1'));
		}else{
			$where = array('order_sn'=>$ginfo['sn'],'status'=>array('in','6,11'));
		}
		$oinfo = D('Item/Order')->where($where)->relation(true)->find();
        if(empty($oinfo)){die(json_encode(array('statusCode' => '300','msg' => '订单获取失败')));}
		if($ginfo['pay_type'] == '5'){
			$pay = & load_wechat('Pay',$oinfo['product_id']);
			$query = $pay->queryOrder($ginfo['sn']);
			if ($query['result_code'] == 'SUCCESS' && $query['return_code'] == 'SUCCESS' && $query['trade_state'] == 'SUCCESS') {
	            $param = array(
            		'seat_type' => $ginfo['seat_type'],
            		'pay_type'  => $ginfo['pay_type'],
            		'order_type'=> $ginfo['order_type']
            	);
            	$return = $this->sweep_pay_seat($param,$oinfo);
            	$hit = (int)S('pay'.$ginfo['sn'])+1;
            	if((int)$hit >= 5){
					$return = array(
						'statusCode' => '400',
						'message' => '订单创建遇到严重错误,无法继续执行,已经执行退款程序,请提醒客户查收',
					);
					//微信
					\Libs\Service\Refund::weixin_refund($ginfo['sn'],$oinfo['product_id'],$oinfo['money']);
					S('pay'.$ginfo['sn'],null);
				}
				S('pay'.$ginfo['sn'],$hit);
			}elseif($query['result_code'] == 'SUCCESS' && $query['return_code'] == 'SUCCESS'){
				$return = array(
					'statusCode' => '300',
					'message'	=>	'等待客户付款...'
				);
			}else{
				$return = array(
					'statusCode' => '400',
					'message'=>'['.$result['errCode'].']'.$result['errMsg']
				);
				error_insert($pay->errMsg.[$pay->errCode]);
			}
		}
		die(json_encode($return));
	}
	//刷卡支付排座
	function sweep_pay_seat($info,$oinfo)
	{
		if($info['order_type'] == '3' && $oinfo['status'] == '1'){
			//政企窗口收款,判断是否已经收款
			if(check_collection_pay($info['sn'])){
				collection_log($oinfo,$info['pay_type']);
				//更新订单支付方式
				D('Order')->where(['order_sn'=>$info['sn']])->setField('pay',$info['pay_type']);
			}
			$run = true;
		}else{
			$run = Order::sweep_pay_seat($info,$oinfo);
		}
		if($run != false){
			//支付方式影响返回结果
			$return = array(
				'statusCode' => '200',
				'title'		 =>	"门票打印",
				'width'		 =>	'213',
				'height'	 =>	'208',
				'dialog'	 =>	true,
				'pageid'	 => 'print',
				'forwardUrl' => U('Item/Order/drawer',array('sn'=>$oinfo['order_sn'],'plan_id'=>$oinfo['plan_id'],'genre'=>1)),
			);
			$message = "支付成功!单号".$run;
			D('Item/Operationlog')->record($message, 200);//记录售票员日报表
		}else{
			$return = array(
				'statusCode' => '300',
				'message'=>'['.$result['errCode'].']'.$result['errMsg']
			);
			$message = "支付失败!";
			D('Item/Operationlog')->record($message, 300);//记录售票员日报表
		}
		return $return;
	}

/*======================分割线3 景区门票订单=======================================*/
	function scenicPost(){
		$info = $_POST['info'];
		$sn = Order::scenic($info);
		$plan = session('plan');
		if($sn != false){
			$return = array(
				'statusCode' => '200',
				'forwardUrl' => U('Item/Order/drawer',array('sn'=>$sn,'plan_id'=>$plan['id'],'genre'=>1)),
			);
			$message = "下单成功!单号".$sn;
			D('Item/Operationlog')->record($message, 200);//记录售票员日报表
		}else{
			$return = array(
				'statusCode' => '300',
				'forwardUrl' => U('Item/Order/drawer',array('sn'=>$sn)),
			);
			$message = "下单失败!";
			D('Item/Operationlog')->record($message, 300);//记录售票员日报表
		}
		//记录订单信息
		die(json_encode($return));
	}
/*======================分割线4 @大红袍 =============================================================*/
	/**
	 * 预订单处理 type 1    同意排座 2审核 4拒绝 退款
	 */
	function pay_no_seat(){
		if(IS_POST){
			$pinfo = I('post.');
			$oinfo = Operate::do_read('Order',0,array('order_sn'=>$pinfo['sn']),'','',true);
			if(empty($pinfo) || empty($oinfo)){$this->erun("参数错误!");}
			switch ((int)$pinfo['action']) {
				case 1:
					$order = new Order();
					$status = $order->add_seat($oinfo);
					$msg = '订单已通过审核';
					break;
				case 2:
					//使用控座模板设置座位
					if(!isset($pinfo['control']) || empty($pinfo['control'])){
						$status = false;
					}else{
						$order = new Order();
						$status = $order->up_control_seat($pinfo, $oinfo);
					}
					$msg = '订单已通过审核';
					break;
				case 4:
					//不同意退款
					$status = \Libs\Service\Refund::arefund($oinfo);
					$msg = '订单未通过审核';
					break;
			}
			//返回结果
			if($status != false){
				//判断是否需要推送通知
				if($oinfo['addsid'] === 8){
					\Trust\Service\Wisdom::confirm_order($pinfo['sn'], 1,$msg);
				}
				$this->srun('操作成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
			}else{
				$this->erun("操作失败!");
			}
		}else{
			$ginfo = I('get.');
			$oinfo = Operate::do_read('Order',0,array('order_sn'=>$ginfo['id']),'','',true);
			if(empty($ginfo) || empty($oinfo)){$this->erun("参数错误!");}
			if($oinfo['type'] == '6'){
				$this->erun("政企订单不支持此项操作!");
			}
			//拉取所有特殊控座模板
			$control = D('ControlSeat')->where(['status' => 1,'type'=>2,'product_id'=>$oinfo['product_id']])->field('id,name,num')->select();
			$this->assign('data', $oinfo)->assign('control', $control)->display();
		}
			
	}
	
	/**
	 * 政企订单处理   排座但不付费
	 */
	function gov(){
		if(IS_POST){
			if(I('get.type') == '1'){
				//根据订单号
				$sn = I('post.sn');
				$map = array(
					'product_id'=>\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE'),
					'order_sn' => array('like','%'.$sn.'%'),
				);
			}else{
				//取票人手机
				$map = array(
					'product_id'=>\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE'),
					'phone' =>I('post.phone'),
				);
			}
			$lists = Operate::do_read('Order',1,$map,'createtime desc','',true);
			foreach ($lists as $k=>$v){
				$list[$k]=$v;
				$list[$k]['info']= unserialize($v['info']);
			}
		}else{
			//默认加载当天未出票订单
			//获取销售计划
			$plan_time = strtotime(date('Y-m-d',time()));
			$plan = Operate::do_read('Plan',1,array('plantime'=>$plan_time),'',array('id'));
			$map = array(
				'product_id'	=>	\Libs\Util\Encrypt::authcode($_SESSION['lub_proId'], 'DECODE'),
				'plan_id'		=>	array('in',implode(',',array_column($plan['id'], 'id'))),
			);
			$lists = Operate::do_read('Order',1,$map,'createtime desc','',true);
			foreach ($lists as $k=>$v){
				$list[$k]=$v;
				$list[$k]['info']= unserialize($v['info']);
			}
		}
		$this->assign('data',$list)
			->display();
	}
	/**
	 * 政企订单排座
	 */
	function row_seat(){
		if(IS_POST){
			$pinfo = $_POST['info'];
			$order = new Order();
			$run = $order->govSeat($pinfo);
			if($run != false){
				$return = array(
					'statusCode' => '200',
					'message'	 => "排座成功",
					'dialog'	 =>	false,
					'refresh'	=> '348Item'		
				);
				$message = "排座成功!单号";
				if($oinfo['addsid'] === 8){
					\Trust\Service\Wisdom::confirm_order($pinfo['sn'], 1,'订单已通过审核');
				}
				D('Item/Operationlog')->record($message, 200);//记录售票员日报表
			}else{
				$return = array(
					'statusCode' => '300',
					'message'	=> 	"排座失败!".$order->error,
				);
				$message = "排座失败!";
				D('Item/Operationlog')->record($message, 300);//记录售票员日报表
			}
			die(json_encode($return));
		}else{
			$ginfo = I('get.');
			if(empty($ginfo)){$this->erun('参数错误!');}
			$map = array(
				'product_id'=>get_product('id'),
			  //'status'=>6,//只查询未出票的订单
				'order_sn' => $ginfo['id'],
			);
			if($ginfo['plan_id']){
				$data = array(
					'area' => $ginfo['aid'],
					'plan_id' => $ginfo['plan_id'],
					'sn'	=>	$ginfo['id'],
					'num'	=>	$ginfo['num'],
					'statusCode' => '300',
					'forwardUrl' => U('Item/Order/drawer',array('sn'=>$sn,'genre'=>1)),
				);
				die(json_encode($return));
			}else{
				$list = Operate::do_read('Order',0,$map,'','',true);
				$info = unserialize($list['info']);
				foreach ($info['data']['area'] as $key => $value) {
					$area[]=array(
						'area' => $value['areaId'],
						'num'  => $value['num'],
						'priceid' => $value['priceid'],
						'price' => $value['price'],
					);
				}
				//只支持单个区域
				if(count($info['data']['area']) === (int)1){
					$this->assign('data',$list)
						->assign('area',$area)
						->assign('num',$num)
						->assign('plan',$list['plan_id'])
						->display();
				}else{
					$this->erun("非常抱歉该功能目前只支持单个区域!");
				}
			}
		}
	}
	/**
	 * 加载根据区域加载座位
	 */
	function public_seats(){
		$ginfo = I('get.');
		if(empty($ginfo)){$this->erun('参数错误!');}
		$map = array(
			'product_id'=>get_product('id'),
			'order_sn' => $ginfo['sn'],
		);
		$list = Operate::do_read('Order',0,$map,'','',true);
		if($list['status'] == '1' || $list['status'] == '9' ){
			$this->erun("该订单已完成排座，或已打印,请从订单管理中查询此订单!");
		}else{
			$info = unserialize($list['info']);
			foreach ($info['data']['area'] as $key => $value) {
				if($ginfo['num'] == $value['num']){
					$ginfo['priceid'] = $value['priceid'];
					$ginfo['price'] = $value['price'];
				}
			}
			//加载座椅
			$info = Operate::do_read('Area',0,array('id'=>$ginfo['area'],'status'=>1),'','id,name,face,is_mono,seats,num,template_id');
			$info['seats'] = unserialize($info['seats']);
			$this->assign('data',$info)
				->assign('ginfo',$ginfo)
				->assign('area',$area)
				->assign('plan',F('Plan_'.$ginfo['plan']))
				->display('seats');
		}
	}
	/**
	 * 根据区域加载座位信息   区域页面打开时  先加载座椅模板   然后加载售出情况   页面打开时  每个10分无刷新更新页面
	 * 散客售票 type 1 团队售票 2
	 */
	function seatsee(){
		$area = I('get.area',0,'intval');
		$planid = I('get.plan',0,'intval');
		$type = I('get.type',1,'intval');
		$plan = session('plan');
		if(empty($plan) || $plan['id'] <> $planid){
			$this->erun("参数错误!");
		}else{
			//加载座椅
			$info = Operate::do_read('Area',0,array('id'=>$area,'status'=>1),'','id,name,face,is_mono,seats,num,template_id');
			$info['seats'] = unserialize($info['seats']);
			//选中区域价格和座椅信息
			$tictype = pullprice($plan['id'],$type,$area,1);
			$this->assign('price',$tictype);
			$this->assign('data',$info)
				->assign('area',$area)
				->assign('plan',$plan)
				->assign('type',$type)
				->display();
		}
	}
	//身份证查询
	public function query_card()
	{
		$model = D('IdcardLog');
		$map['idcard'] = I('idcard');
		$info = $model->where($map)->find();
		$this->assign('data',$info);
		$this->display();
	}
	//标记完结
	public function public_tag_status($sn='')
	{
		$status = D('Order')->where(['order_sn'=>$sn,'status'=>1])->setField('status',9);
		if($status){
			$this->srun('标记成功',array('tabid'=>$this->menuid.MODULE_NAME,'closeCurrent'=>true));
		}else{
			$this->srun('标记失败',array('tabid'=>$this->menuid.MODULE_NAME));
		}
	}
	
}