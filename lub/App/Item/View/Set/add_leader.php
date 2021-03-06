<?php if (!defined('LUB_VERSION')) exit(); ?>
<form class="form-horizontal" action="{:U('Item/Set/add_leader',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
<div class="bjui-pageContent">
	 <div class="form-group">
    <label class="col-sm-2 control-label">名称:</label>
    <input type="text" name="name" class="form-control required" data-rule="required;" size="40" placeholder="名称">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">电话:</label>
    <input type="text" name="phone" class="form-control required" data-rule="required;" size="25" placeholder="电话">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">email:</label>
    <input type="text" name="email" class="form-control required" data-rule="required;" size="40" placeholder="email">
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">类型:</label>
    <select name="type" class="required" data-toggle="selectpicker" data-rule="required">
      <option value="3">短信+邮件</option>
      <option value="2">邮件</option>
      <option value="1">短信</option>
  </select>
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