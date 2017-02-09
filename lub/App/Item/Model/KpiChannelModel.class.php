<?php
// +----------------------------------------------------------------------
// | LubTMP 考核模型
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Item\Model;
use Common\Model\Model;
class KpiChannelModel extends Model{
	protected $_auto = array(
        array('update_time', 'time', 3, 'function')
    );
	public function insert($data='',$product_id = '',$channel_id = '')
	{
		if(empty($data) || empty($product_id) || empty($channel_id)){
			return false;
		}
		$add = array(
			'product_id'=>	$product_id,
			'score'		=>	$data['score'],
			'crm_id'	=>	$channel_id,
			'quota'		=>	$data['quota'],
			'task'		=>	$data['task'],
			'status'	=>	'1',
			'create_time'=>	time(),
			'update_time'=> time(),
		);
		return $this->add($add);
	}
}
