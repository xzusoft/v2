<?php
// +----------------------------------------------------------------------
// | LubTMP 会员模型
// +----------------------------------------------------------------------
// | Copyright (c) 2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Crm\Model;
use Think\Model;
class MemberModel extends Model{
	 protected $_auto = array (
        array('create_time', 'time', 1, 'function'),
        array('update_time', 'time', 3, 'function'),
        array('verify', 'genRandomString', 1, 'function'), 
        array('status', '1'),
     );
}