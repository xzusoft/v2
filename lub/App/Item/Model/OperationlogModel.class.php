<?php

// +----------------------------------------------------------------------
// | LubTMP 前台操作日志
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@chengde360.com>
// +----------------------------------------------------------------------

namespace Item\Model;

use Common\Model\Model;

class OperationlogModel extends Model {

    //array(填充字段,填充内容,[填充条件,附加规则])
    protected $_auto = array(
        array('time', 'time', 1, 'function'),
        array('ip', 'get_client_ip', 3, 'function'),
    );

    /**
     * 记录日志
     * @param type $message 说明
     */
    public function record($message, $status = 0) {
        $fangs = 'GET';
        if (IS_AJAX) {
            $fangs = 'Ajax';
        } else if (IS_POST) {
            $fangs = 'POST';
        }
        $data = array(
            'uid' => \Libs\Util\Encrypt::authcode($_SESSION['lub_userid'], 'DECODE'),
            'status' => $status,
            'info' => "提示语：{$message}<br/>模块：" . MODULE_NAME . ",控制器：" . CONTROLLER_NAME . ",方法：" . ACTION_NAME . "<br/>请求方式：{$fangs}",
            'get' => $_SERVER['HTTP_REFERER'],
        	'scena'	=>	'2',//日志场景
        );
        $this->create($data);
        return $this->add() !== false ? true : false;
    }

    /**
     * 删除一个月前的日志
     * @return boolean
     */
    public function deleteAMonthago() {
        $status = $this->where(array("time" => array("lt", time() - (86400 * 30))))->delete();
        return $status !== false ? true : false;
    }

}