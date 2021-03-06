<?php
// +----------------------------------------------------------------------
// | LubTMP 短信服务
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
class Sms extends \Libs\System\Service {
	/*订票订单短信
	 @param $info 短信内容
	 @param $type 模板类型
	*/
	function order_msg($info,$type = '1'){
		 $title=$info['title'];//订单信息
		 $num = $info['num'];
		 $sn=$info['sn'];//10位$remark订单详情
		 $remark = $info['remark'];
		 $crminfo = $info['crminfo'];
		 switch ($type) {
		 	case '1':
		 		$msg = $title.$remark;
		 		$message = urlencode("您已购".$msg."，单号".$sn."，请到茶博园游客中心票务处或南门牌坊票务处自助取票【印象大红袍】");
		 		break;
		 	case '3':
		 		$datetime = date('Y年m月d日H：i：s');
		 		$datetime = substr($datetime,2);
		 		$message = urlencode("您购买的".$title."印象大红袍门票，单号为".$sn."，于".$datetime."出票。【印象大红袍】");
		 		break;
		 	case '4':
		 		//整场退票
		 		$message = urlencode("您购买".$title."印象大红袍演出门票".$num."张，因天气等不可抗力因素取消，请联系您的购买渠道办理退票。详询5208888。【印象大红袍】");
		 		break;
		 	case '6':
		 		//代订订单短信模板
		 		$msg = $title.$remark;
		 		$message = urlencode("您预定".$msg."，单号".$sn."，请到茶博园游客中心票务处或南门牌坊票务处付款取票【印象大红袍】");
		 		break;
		 	case '7':
		 		//领导短信
		 		//$area = $info['area'];
		 		//$channel = $info['channel'];
				// $message = urlencode($title."共".$num."张，其中".$area."。".$channel."。【印象大红袍】");
				 //{字符}共{数字}张。【印象大红袍】
				 $message = urlencode($title."共".$num."张。【印象大红袍】");
				break;
			case '8':
		 		//活动短信
		 		$msg = $title.$remark;
				$message = urlencode("您已预定".$msg."，订单号".$sn."，请演出前到入场口刷身份证入园【印象大红袍】");
				break;
		 	case '11':
		 		//低余额提醒
		 		$message = urlencode("尊敬的用户".$title.",您的账户余额已不足".$money."元,请您及时充值。【印象大红袍】");
		 		break;
		 	default:
		 		$message = urlencode("您已购买".$title."印象大红袍演出门票".$num."张，订单号".$sn."，请凭订单号到景区指定窗口兑换门票。【印象大红袍】");
		 		break;
		 }
		 $parameter = "mobile=".$info['phone']."&content=".$message;
		 $status = Sms::SAEsendmsg($parameter);
		 Sms::local_sms($info['phone'],$sn, urldecode($message),$status,$type);
		 return $status;
	}	/*系统错误报警短息*/
	function err_msg($info){
		 $title=$info['title'];//产品名称
		 $ream = $info['rema'];//错误说明
		 $code = $info['code'];//错误代码
		 //$message = urlencode("【数字承德】警告".$title."门票".$num."张订单号".$sn."，请您凭订单号在景区售票处兑换纸质门票！");
		 $message = urlencode("警告".$title."执行".$rema."时出错，错误代码".$code."【印象大红袍】");
		 $parameter = "mobile=".$info['phone']."&content=".$message;
		 return Sms::SAEsendmsg($parameter);
	}
	/*短信发送本地记录
	* @param $phone 目标号码
	* @param $content 短信内容
	* @param $status 短信发送状态
	* @param $type 短信类型
	*/
	function local_sms($phone,$sn = null,$content,$status,$type){
		M('SmsLog')->add(array(
			'order_sn'=> $sn,
			'phone' => $phone,
			'content'=>$content,
			'status'=>$status,
			'type'=>$type,
			'createtime'=>time(),
			));
		return true;
	}
	/*sae bechtech
	 * accesskey ：用户接入KEY 
	 * secretkey ：用户接入密钥 
	 * mobile ：目的手机号，多条请用英文逗号隔开，最多 100 个号码 
	 * content ：发送内容，如果含有空格，百分数等特殊内容，请用编码进行传送，最多 67 个文字（ 1 个英文或数字也算 1 个文字）
	 * http://sms.bechtech.cn/Api/send/data/json?accesskey=xxx&secretkey=yyy&mobile=您的手机号码&content=abc 
	 * */
	private function SAEsendmsg($parameter){
		$url ="http://sms.bechtech.cn/Api/send/data/json?accesskey=3247&secretkey=26a610de8e744558768d8429dcd13140e5445b12&".$parameter;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
		$res = curl_exec( $ch );
		curl_close( $ch );
	   // $res  = curl_error( $ch );
		//var_dump($url);
		return $res;	
	}
}