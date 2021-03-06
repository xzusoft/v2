<?php
// +----------------------------------------------------------------------
// | LubTMP 接口服务
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;
use Libs\Service\Checkin;
class Api extends \Libs\System\Service {
    
    /**
     * 读取产品及价格信息
     * @param $proId array 产品ID集合
     */
    private function product($proId = false){
    	foreach ($proId as $key=>$val){
    		$info[$val] = D('Item/Product')->where(array('id'=>$val))->relation(true)->find();
    	}
    	return $info;
    }
    
    /**
     * 根据计划读取票型价格
     * @param $planid int 计划ID 
     */
    function planType($planid = false){
    	$info = D('Item/Plan')->where(array('id'=>$planid,'status'=>'2'))->find();
    	return $info;
    }

    /**
    * 获取销售计划
    * @param $product string 产品id 集合
    * @param $param array 当前APP的全部信息
    * return array   
    */
    function plans($param = null, $uinfo = '', $datetime = '', $sealeTicket = []){
        if(empty($param)){return false;}
        $product = $param['product'];
        $proArr = explode(',', $product);
        foreach ($proArr as $k=>$v){
            $list[$v] = M('Product')->where(array('id'=>$v,'status'=>1))->field('name as productname')->select();
            if($list[$v] != false){
                if(empty($datetime)){
                    $where = array('product_id'=>$v,'status'=>2);
                }else{
                    $where = array('product_id'=>$v,'status'=>2,'plantime'=>$datetime);
                }
                $list[$v]['plan'] = M('Plan')->where($where)->order('plantime ASC,games ASC')->field(array('id,plantime,product_type,starttime,endtime,games,param,product_id,quotas,seat_table'))->limit(7)->select();
            }
        }
        $type = '1';
        if(isset($uinfo['user']['guide']) && $uinfo['user']['guide'] > 0){
            $type = '2';
        }
        $list = array_filter($list);
        //重构参数信息 获取票价信息根据场次读取价格分组信息
        foreach ($list as $key => $value) {
            
            foreach ($value['plan'] as $ke => $valu) {
                if((int)$valu['product_type'] === 1){
                    $valu['param'] = area_price(unserialize($valu['param']),$valu['seat_table'],$param['group']['price_group'],$param['scene'], $sealeTicket);
                    $valu['title'] = planShow($valu['id'],4,1);
                    $valu['product_id'] = $valu['product_id'];
                    $valu['num'] = M(ucwords($valu['seat_table']))->where(array('status'=>array('in','0')))->count();
                    $plan[] = $valu;
                }else{
                    $ticketParam = pullprice($valu['id'],$type,0,$param['scene'],$param['group']['price_group'], 1, $sealeTicket);
                    if(!empty($ticketParam)){
                        $valu['param'] = $ticketParam;
                        $where = array('plan_id'=>$valu['id'],'product_id'=>$valu['product_id'],'status'=>array('in','2,99,66'));
                        $number = D('Scenic')->where($where)->count();
                        $area_num = $valu['quotas'] - $number;
                        $area_nums = $number;
                        //TODO $param['group']['price_group'] 梦里老家  强制为70

                        $valu['param'] = pullprice($valu['id'],$type,0,$param['scene'],$param['group']['price_group'], 1, $sealeTicket);

                        $valu['title'] = planShow($valu['id'],5,1);
                        $valu['product_id'] = $valu['product_id'];
                        $valu['num'] = $area_num;
                        $plan[] = $valu;
                    }
                    
                }  
            }
            
            $value['plan'] = $plan;
            $info = $value;
        }
        return $info;
    }
    //获取场次及相关信息
    function get_plan($product_id,$pinfo,$ginfo)
    {   
        $map['product_id'] = $product_id;
        $map['status'] = 2;//状态必为售票中 
    
        if(in_array($ginfo,['1','2','3'])){
            $info = explode('-', $pinfo['plan']);
            $map['plantime'] = (int)$info[0] ? (int)$info[0] : $today;
            $map['games'] = (int)$info[1] ? (int)$info[1] : 1 ;
            $plan = M('Plan')->where($map)->field('id,param,seat_table,product_type,plantime,starttime,endtime,games')->find();
            $param = unserialize($plan['param']);
        }else{
            $plantime = strtotime($pinfo['plan']);
            $map['plantime'] = $plantime;
            $plan = M('Plan')->where($map)->field('id,param,seat_table,product_type,quotas,plantime,starttime,endtime,games')->select();
        }
        
        //拉取坐席
        if($ginfo == '1'){
            foreach ($param['seat'] as $k => $v) {
                $area[] = array(
                    'id'    =>  $v,
                    'name'  =>  areaName($v,1),
                    'number'=>  areaSeatCount($v,1),
                    'num'   =>  area_count_seat($plan['seat_table'],array('status'=>'0','area'=>$v),1),
                    'nums'  =>  area_count_seat($plan['seat_table'],array('status'=>array('in','2,66,99'),'area'=>$v),1),//已售出
                    'numb'  =>  area_count_seat($plan['seat_table'],array('status'=>array('in','66'),'area'=>$v),1),//预定数
                    'cnum'  =>  area_count_seat($plan['seat_table'],array('status'=>array('in','99'),'area'=>$v),1),//已检票
                ); 
            }
            $sale = array(
                'nums'  =>  area_count_seat($plan['seat_table'],array('status'=>array('in','2,66,99')),1),
                'numb'  =>  area_count_seat($plan['seat_table'],array('status'=>array('in','66')),1),
                'money' =>  format_money(M('Order')->where(array('status'=>array('in','1,7,9'),'plan_id'=>$plan['id']))->sum('money')),
            );
            $return = array(
                'statusCode' => '200',
                'plan'  => $plan['id'],
                'area'  => $area,
                'sale'  => $sale,
            );
        }
        //剧院返回区域和已售数量
        if($ginfo == '3'){
            foreach ($param['seat'] as $k => $v) {
                $area[] = array(
                    'id'    =>  $v,
                    'name'  =>  areaName($v,1),
                    'number'=>  areaSeatCount($v,1),
                    'num'   =>  area_count_seat($plan['seat_table'],array('status'=>'0','area'=>$v),1),
                    'nums'  =>  area_count_seat($plan['seat_table'],array('status'=>array('in','2,66,99'),'area'=>$v),1),//已售出
                ); 
            }
            $return = array(
                'statusCode' => '200',
                'plan'  => planShow($plan['id'],1,1),
                'area'  => $area
            );
        }
        //拉取漂流的销售情况
        if($ginfo == '4'){
            foreach ($plan as $k => $v) {
                $param = unserialize($v['param']);
                $where = [
                    'plan_id'=>$v['id'],
                    //'product_id'=>$product_id,
                    'status'=>['in','2,99,66']
                ];
                switch ($v['product_type']) {
                    case '2':
                        $tooltype = '00';
                        $name = date('H:i',$v['starttime']).'-'.date("H:i",$v['endtime']);

                        break;
                    case '3':
                        $tooltype = tooltype($param['tooltype'],1);
                        $name = '[第'.$v['games'].'趟-'.$tooltype.'] '. date('H:i',$v['starttime']).'-'.date("H:i",$v['endtime']);
                        
                        break;
                }
                $number = D($v['seat_table'])->where($where)->count();
                //获取当前可售数量
                $nums = $v['quotas'] - $number;
                $area[] = array(
                    'tooltype' => $tooltype,
                    'name'  => $name,
                    'number'=>  $v['quotas'],
                    'num'   =>  $number,
                    'nums'  =>  $nums,
                );
            }
            $return = array(
                'statusCode' => '200',
                'plan'  => $plan,
                'area'  => $area
            );
        }
        //拉取小商品
        if($ginfo == '2'){
            foreach ($param['goods'] as $k => $v) {
                $info = goodsInfo($product_id,'',$v,1);
                $number = array(
                    'number'=>  '1',//已售出
                ); 
                $goods[] = array_merge($info,$number);
            }
            if(empty($goods)){
                $goods = 'null';
            }
            $return = array(
                'statusCode' => '200',
                'plan'  => $plan['id'],
                'goods' => $goods           
            );
        }
        //设置session
        session('plan',$plan);
        return $return;
    }
    function get_trust_plan($product_id,$pinfo,$ginfo)
    {   
        $map['product_id'] = $product_id;
        $map['status'] = 2;//状态必为售票中
    
        if(in_array($ginfo,['1','2','3'])){
            $info = explode('-', $pinfo['plan']);
            $map['plantime'] = (int)$info[0] ? (int)$info[0] : $today;
            $map['games'] = (int)$info[1] ? (int)$info[1] : 1 ;
            $plan = M('Plan')->where($map)->field('id,param,seat_table,product_type,plantime,starttime,endtime,games')->find();
            $param = unserialize($plan['param']);
        }else{
            $plantime = strtotime($pinfo['plan']);
            $map['plantime'] = $pinfo['plan'];//$plantime;
            $plan = M('Plan')->where($map)->field('id,param,seat_table,product_type,quotas,plantime,starttime,endtime,games')->select();
        }
        
        //拉取坐席
        if($ginfo == '1'){
            foreach ($param['seat'] as $k => $v) {
                $area[] = array(
                    'id'    =>  $v,
                    'name'  =>  areaName($v,1),
                    'number'=>  areaSeatCount($v,1),
                    'num'   =>  area_count_seat($plan['seat_table'],array('status'=>'0','area'=>$v),1),
                    'nums'  =>  area_count_seat($plan['seat_table'],array('status'=>array('in','2,99'),'area'=>$v),1),//已售出
                    'numb'  =>  area_count_seat($plan['seat_table'],array('status'=>array('in','66'),'area'=>$v),1),//预定数
                    'cnum'  =>  area_count_seat($plan['seat_table'],array('status'=>array('in','99'),'area'=>$v),1),//已检票
                ); 
            }
            $sale = array(
                'nums'  =>  area_count_seat($plan['seat_table'],array('status'=>array('in','2,99')),1),
                'numb'  =>  area_count_seat($plan['seat_table'],array('status'=>array('in','66')),1),
                'money' =>  format_money(M('Order')->where(array('status'=>array('in','1,7,9'),'plan_id'=>$plan['id']))->sum('money')),
            );
            $return = array(
                'statusCode' => '200',
                'plan'  => $plan['id'],
                'area'  => $area,
                'sale'  => $sale,
            );
        }
        //剧院返回区域和已售数量
        if($ginfo == '3'){
            foreach ($param['seat'] as $k => $v) {
                $area[] = array(
                    'id'    =>  $v,
                    'name'  =>  areaName($v,1),
                    'number'=>  areaSeatCount($v,1),
                    'num'   =>  area_count_seat($plan['seat_table'],array('status'=>'0','area'=>$v),1),
                    'nums'  =>  area_count_seat($plan['seat_table'],array('status'=>array('in','2,99'),'area'=>$v),1),//已售出
                ); 
            }
            $return = array(
                'statusCode' => '200',
                'plan'  => planShow($plan['id'],1,1),
                'area'  => $area
            );
        }
        //拉取漂流的销售情况
        if($ginfo == '4'){
            foreach ($plan as $k => $v) {
                $param = unserialize($v['param']);
                $where = [
                    'plan_id'=>$v['id'],
                   // 'product_id'=>$product_id,
                    'status'=>['in','2,99,66']
                ];
                switch ($v['product_type']) {
                    case '2':
                        $tooltype = '00';
                        $name = date('H:i',$v['starttime']).'-'.date("H:i",$v['endtime']);

                        break;
                    case '3':
                        $tooltype = tooltype($param['tooltype'],1);
                        $name = '[第'.$v['games'].'趟-'.$tooltype.'] '. date('H:i',$v['starttime']).'-'.date("H:i",$v['endtime']);
                        
                        break;
                }
                $number = D($v['seat_table'])->where($where)->count();
                //获取当前可售数量
                $nums = $v['quotas'] - $number;
                $area[] = array(
                    'tooltype' => $tooltype,
                    'name'  => $name,
                    'number'=>  $v['quotas'],
                    'num'   =>  $nums,
                    'nums'  =>  $number
                );
            }
            $return = array(
                'statusCode' => '200',
                'plan'  => $plan,
                'area'  => $area
            );
        }
        //拉取小商品
        if($ginfo == '2'){
            foreach ($param['goods'] as $k => $v) {
                $info = goodsInfo($product_id,'',$v,1);
                $number = array(
                    'number'=>  '1',//已售出
                ); 
                $goods[] = array_merge($info,$number);
            }
            if(empty($goods)){
                $goods = 'null';
            }
            $return = array(
                'statusCode' => '200',
                'plan'  => $plan['id'],
                'goods' => $goods           
            );
        }
        //设置session
        return $return;
    }
    /**
     * 时间场次验证
     * $plan 检票场次
     */
    function timeCheck($plan){
        if(empty($plan)){ return false;}
        //获取系统日期
        $datetime = date('Ymd');
        //日期
        $plantime = date('Ymd',$plan['plantime']);
        //检票基准时间
        $starttime = date('H:i',$plan['starttime']);
        //检票时间
        $start = date('H:i',strtotime("$starttime -40 minute"));
        $end = date('H:i',strtotime("$starttime +50 minute"));
        if($datetime == $plantime){
            //判断日期
            $totime = date('H:i');
            if($start <= $totime && $totime <= $end){
                //判断时间
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
    //验证APP 信息
    function check_app($appid,$appkey){
        $info = D('Api/App')->where(array('appid'=>$appid,'status'=>1))->relation(true)->field('id,appid,appkey,product,crm_id,is_pay')->find();
        $str = $info['appid'].$info['id'].$info['appkey'];
        $md5_key = md5($str);
        if($md5_key == $appkey){
            //查询所属分组的相关信息
            $crm = F('Crm');
            $info['groupid'] = $crm[$info['crm_id']]['groupid'];
            if(empty($info['groupid']) || empty($info['crm_id'])){
                return false;
            }else{
                $info['group'] = M('CrmGroup')->where(array('id'=>$info['groupid']))->field('id,price_group,type,product_id,settlement')->find();
                $info['scene'] = '5';
                //dump($info);
                return $info; 
            }
            
        }else{
            return false;
        }
    }
}