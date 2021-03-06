<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Manage/Api/add',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	<div class="form-group">
    <label class="col-sm-2 control-label">接口名称:</label>
    <input type="text" name="name" class="form-control required" data-rule="required;" size="40" placeholder="应用名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">接口地址:</label>
    <input type="text" name="url" class="form-control required" data-rule="required;" size="40" placeholder="例:api.php?m=api&a=checkIn">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">支付方式:</label>
    <select name="auth" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="1">需要授权</option>
      <option value="0">无需授权</option>
    </select>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">接口描述:</label>
    <textarea name="remark"></textarea>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">状态:</label>
    <select name="status" class="required" data-toggle="selectpicker" data-rule="required">
	    <option value="">状态</option>
	    <option value="1">启用</option>
	    <option value="0">禁用</option>
	  </select>
  </div>
</div>
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">取消</button></li>
        <li><button type="submit" class="btn-default" data-icon="save">保存</button></li>
    </ul>
</div>
</form>