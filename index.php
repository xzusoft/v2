<?php
// +----------------------------------------------------------------------
// | Lubs 入口文件
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.leubao.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhoujing <admin@leubao.com>
// +----------------------------------------------------------------------
// 检测PHP环境
if (version_compare(PHP_VERSION, '5.3.0', '<')) {
	header("Content-type: text/html; charset=utf-8");
    die('PHP环境不支持，使用本系统需要 PHP > 5.3.0 版本才可以~ !');
}

/*当前目录路径*/
define('SITE_PATH', getcwd() . '/');
//项目路径
define('PROJECT_PATH', SITE_PATH . 'Lub/');
// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', true);
// 应用公共目录
define('COMMON_PATH', PROJECT_PATH . 'Common/');
// 定义应用目录
define('APP_PATH', PROJECT_PATH . 'App/');
//应用运行缓存目录
define("RUNTIME_PATH", SITE_PATH . "#runtime/");
require './vendor/autoload.php';
// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';