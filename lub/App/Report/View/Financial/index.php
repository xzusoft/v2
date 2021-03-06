<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
<!--Page -->
<form id="pagerForm" data-toggle="ajaxsearch" action="{:U('Report/Financial/index',array('menuid'=>$menuid));}" method="post">
  <input type="hidden" name="pageSize" value="{$numPerPage}">             
  <input type="hidden" name="pageCurrent" value="{$currentPage}">
  <!--条件检索 s--> 
  <div class="bjui-searchBar">
    <label>统计日期:</label>
    <input type="text" size="11" name="starttime" data-toggle="datepicker" value="{$starttime}">
    &nbsp;
    <select class="required" name="work" data-toggle="selectpicker">
      <option value="1" <if condition="$work eq '1'">selected</if>>含工作票</option>
      <option value="2" <if condition="$work eq '2'">selected</if>>不含工作票</option>
      <option value="3" <if condition="$work eq '3'">selected</if>>仅含工作票</option>
    </select>
    &nbsp;
    <select class="required" name="type" data-toggle="selectpicker">
      <option value="1" <if condition="$type eq '1'">selected</if>>明细</option>
      <option value="2" <if condition="$type eq '2'">selected</if>>汇总</option>
    </select>
    &nbsp;
    <select class="required" name="is_check" data-toggle="selectpicker">
      <option value="1" <if condition="$is_check eq '1'">selected</if>>结算价核算</option>
      <option value="2" <if condition="$is_check eq '2'">selected</if>>净收入核算</option>
    </select>
    &nbsp;
    <input type="hidden" name="ticket.id" value="{$ticket_id}">
    <input type="text" name="ticket.name" readonly value="{$ticket_name}" size="17" data-toggle="lookup" data-url="{:U('Manage/Index/public_get_price',array('ifadd'=>1));}" data-group="ticket" data-width="600" data-height="445" data-title="票型名称" placeholder="票型名称">
    <button type="submit" class="btn-default" data-icon="search">查询</button>&nbsp;
    <a class="btn btn-orange" href="javascript:;" data-toggle="reloadsearch" data-clear-query="true" data-icon="undo">清空查询</a>
    <div class="pull-right">
        <a type="button" class="btn btn-default" onclick="$(this).navtab('refresh');" data-placement="left" data-toggle="tooltip" rel="reload" title="刷新当前页"><i class="fa fa-refresh"></i></a>
        <a type="button" class="btn btn-primary" href="{:U('Report/Exprot/export_execl',$export_map);}" data-type='get' data-toggle="doexport" data-confirm-msg="确定要根据当前条件导出数据吗？"><i class="fa fa-file-excel-o"> 导出Execl</i></a>

    </div>
  </div>
  <!--检索条件 e-->
</form>
<!--Page end-->
</div>

<div class="bjui-pageContent tableContent" id="w_scenic_print">
<if condition="$type eq '1'">
  <table data-toggle="tablefixed" data-width="100%" data-nowrap="true">
    <thead>
      <tr>
        <th align="center" width="115px">订单号</th>
        <th align="center">所属计划</th>
        <th align="center">订单(场景)类型</th>
        <th align="center">渠道商</th>
        <th align="center">下单人</th>
        <th align="center">区域</th>
        <th align="center">票型</th>
        <th align="center">票面单价</th>
        <th align="center">结算单价</th>
        <th align="center">数量</th>
        <th align="center">票面金额</th>
        <th align="center">结算金额</th>
        <th align="center">差额</th>
      </tr>
    </thead>
    <tbody id="report-list">
    <volist name="data" id="vo">
      <tr data-id="{$vo.order_sn}" data-num="{$vo.number}" data-money="{$vo.money}" data-moneys="{$vo.moneys}" data-subsidy="{$vo.subsidy}">
        <td><a data-toggle="dialog" href="{:U('Item/Work/orderinfo',array('sn'=>$vo['order_sn']))}" data-width="900" data-height="600" data-title="订单详情">{$vo.order_sn}</a></td>
        <td>{$vo.plan_id|planShow}</td>
        <td>{$vo.addsid|addsid}({$vo.type|channel_type})</td>
        <td><if condition="$vo['type'] eq 1">散客<else />{$vo.channel_id|crmName}</if></td>
        <td align="center">{$vo.user_id|userName}</td>
        <td>{$vo.area|areaName}</td>
        <td>{$vo.price_id|ticketName}</td>
        <td align="right">{$vo.price}</td>
        <td align="right">{$vo.discount}</td>
        <td align="center">{$vo.number}</td>
        <td align="right">{$vo.money}</td>
        <td align="right">{$vo.moneys}</td>
        <td align="right">{$vo.subsidy}</td>
       </tr>
    </volist>
     <tr>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
      <td align="right">当前页合计:</td>
      <td id="sub-scenic-num" align="center">0</td>
      <td id="sub-scenic-money" align="right">0.00</td>
      <td id="sub-scenic-moneys" align="right">0.00</td>
      <td id="sub-scenic-subsidy" align="right">0.00</td>
     </tr>
    </tbody>
  </table>
<else />
<div class="visible-print-block w900">
    <h3 align="center">{$export_map['product_id']|productName}景区日报表</h3>
    <span class="pull-left mb10">统计日期：{$starttime}</span>
    <span class="pull-right mb10">打印时间:<?php echo date('Y年m月d日 H:i:s');?></span>
  </div>
  <volist name="data" id="vo" key="k">

  <table class="table table-bordered w900">
  <if condition="$k eq 1">
  
  </if>
  <tbody>
    <tr>
      <td colspan="8">{$vo.plan|planshow}</td>
    </tr>
    <tr>
      <th align="center" width="160px">票型名称</th>
      <th align="center" width="70px">票面单价</th>
      <th align="center" width="70px">结算单价</th>
      <th align="center" width="50px">数量</th>
      <th align="center" width="100px">票面金额</th>
      <th align="center" width="100px">结算金额</th>
      <th align="center" width="100px">差额</th>
      <th align="center" width="100px">备注</th>
    </tr>
    <volist name="vo['price']" id="item">
    <tr>
      <td align="center">{$item.price_id|ticketName}</td>
      <td align="right">{$item.price}</td>
      <td align="right">{$item.discount}</td>
      <td align="center">{$item.number}</td>
      <td align="right">{$item.money|format_money}</td>
      <td align="right">{$item.moneys|format_money}</td>
      <td align="right">{$item.rebate|format_money}</td>
      <td></td>
    </tr>
    </volist>
    <tr class="subtotal" data-num="{$vo.number}" data-money="{$vo.money}" data-moneys="{$vo.moneys}" data-subsidy="{$vo.rebate}">
      <td></td>
      <td></td>
      <td align="right">小计:</td>
      <td align="center">{$vo.number}</td>
      <td align="right">{$vo.money|format_money}</td>
      <td align="right">{$vo.moneys|format_money}</td>
      <td align="right">{$vo.rebate|format_money}</td>
      <td></td>
    </tr>
    </tbody>
  </table>
  </volist>
  <table class="table table-bordered w900">
    <tr>
      <td width="160px"></td>
      <td width="70px"></td>
      <td align="right" width="70px"><strong>合计:</strong></td>
      <td align="center" width="50px" id="sub-scenic-num">0</td>
      <td width="100px" id="sub-scenic-money" align="right">0.00</td>
      <td width="100px" id="sub-scenic-moneys" align="right">0.00</td>
      <td width="100px" id="sub-scenic-subsidy" align="right">0.00</td>
      <td width="100px"></td>
    </tr>
  </table>
</if>
</div>
<div class="bjui-pageFooter">
<if condition="$type eq '1'">
    <div class="pages">
      <span>共 {$totalCount} 条</span>
    </div>
    <div class="pagination-box" data-toggle="pagination" data-total="{$totalCount}" data-page-size="{$numPerPage}" data-page-current="{$currentPage}"> </div>
<else />
<div class="bjui-pageFooter">
    <ul>
        <li><button type="button" class="btn-close" data-icon="close">关闭</button></li>
        <li><a type="button" class="btn btn-info" href="javascript:$.printBox('w_scenic_print')"><i class="fa fa-print"> 打印报表</i></a></li>
    </ul>
</div>
</if>
</div>
<script type="text/javascript">
$(document).ready(function() {
  var sub_num = 0,
      sub_money = 0,
      sub_moneys = 0,
      sub_subsidy = 0;
  <if condition="$type eq '1'">  
  $('#report-list tr').each(function(i){
    if($(this).data('num') != null){
      sub_num += parseInt($(this).data('num'));
      sub_money += parseFloat($(this).data('money'));
      sub_moneys += parseFloat($(this).data('moneys'));
      sub_subsidy += parseFloat($(this).data('subsidy'));
    }
  });
  <else />
  $("#w_scenic_print .subtotal").each(function(i) {
    if($(this).data('num') != null){
      sub_num += parseInt($(this).data('num'));
      sub_money += parseFloat($(this).data('money'));
      sub_moneys += parseFloat($(this).data('moneys'));
      sub_subsidy += parseFloat($(this).data('subsidy'));
    }
  });
  </if>
  sub_money = sub_money.toFixed(2);
  sub_moneys = sub_moneys.toFixed(2);
  sub_subsidy = sub_subsidy.toFixed(2);
  $("#sub-scenic-num").html(sub_num);
  $("#sub-scenic-money").html(sub_money);
  $("#sub-scenic-moneys").html(sub_moneys);
  $("#sub-scenic-subsidy").html(sub_subsidy);
});
</script>