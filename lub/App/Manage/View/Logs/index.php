<?php if (!defined('LUB_VERSION')) exit(); ?>
<!--Page -->
<div class="bjui-pageHeader">
<Managetemplate file="Common/Nav"/>
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Manage/Logs/index',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">       
  <input type="hidden" name="orderField" value="${param.orderField}">         
  <input type="hidden" name="orderDirection" value="${param.orderDirection}">
  <!--条件检索 s-->
  <div class="bjui-searchBar">
    <label>日期:</label>
    <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
    <label>至</label>
    <input type="text" size="11" name="endtime" data-toggle="datepicker"  value="{$endtime}">
    &nbsp;
    <label>&nbsp;类型:</label>
    <select name="status" data-toggle="selectpicker">
        <option value="">全部</option>
        <option value="0" <if condition="$where['status'] eq '0'">selected</if>>失败</option>
        <option value="1" <if condition="$where['status'] eq '1'">selected</if>>成功</option>
    </select>
    
    <input type="text" value="{$where['uid']}" name="uid" class="form-control" size="10" placeholder="用户ID">&nbsp;
    <input type="text" value="{$where['loginip']}" name="loginip" class="form-control" size="10" placeholder="IP">&nbsp;
    <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
  </div>
  <!--检索条件 e-->
</form>
<!--Page end-->
</div>
<div class="bjui-pageContent tableContent">
  <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
    <thead>
      <tr>
        <th>ID</th>
        <th>用户ID</th>
        <th align="center">状态</th>
        <th align="center">说明</th>
        <th align="center">GET</th>
        <th align="center">时间</th>
        <th align="center">IP</th>
      </tr>
    </thead>
    <tbody>
    <volist name="data" id="vo">
      <tr data-id="{$vo.id}">

            <td align="center">{$vo.id}</td>
              <td align="center">{$vo.uid}</td>
              <td align="center"><if condition="$vo['status'] eq '1'">success<else/>error</if></td>
              <td>{$vo.info}</td>
              <td>{$vo.get}</td>
              <td align="center">{$vo.time|date="Y-m-d H:i:s",###}</td>
              <td align="center">{$vo.ip}</td>
       </tr>
    </volist>
     
    </tbody>
  </table>
</div>
<div class="bjui-pageFooter">
  <div class="pages">
    <span>共 {$totalCount} 条</span>
  </div>
  <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div>
</div>