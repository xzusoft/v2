<?php
// +----------------------------------------------------------------------
// | LubRDF 状态码
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
namespace Libs\Service;

class ReturnCode
{
    /**
     * @param $code
     * @param $data
     * @param $msg
     */
    static public $return_code = [
        '0'    => 'ok',
        '1000' => '操作失败',
        '1001' => '操作成功',
        '1002' => '你想做什么呢', //非法的请求方式 非ajax
        '1003' => '请求参数错误', //如参数不完整,类型不正确
        '1004' => '请先登陆再访问', //未登录 或者 未授权
        '1005' => '请求授权不符', ////非法的请求  无授权查看
        '1006' => '数据加载失败', //
        '1007' => '上传失败',
        '1009' => '签名错误',
        '1010' => '数据不存在', //
        '1018' => '金额校验失败',
        '1020' => '验证码输入不正确', //
        '1021' => '用户账号或密码错误', //
        '1022' => '用户账号被禁用', //
        '1023' => '订单创建失败',
        '1024' => '创建微信支付订单失败',
        '1025' => '未找到订单',
        '1026' => '未找到销售计划或销售计划已过期',
        '1027' => '库存不足',

        '1030' => '产品已下架',
        '1031' => '该产品已停止通过API接口下单',
        '1035' => '不被支持的支付方式',
    ];
}