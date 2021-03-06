<style type="text/css" media="screen">
  .batch{display: none;}

</style>
<form class="form-horizontal" action="{:U('Item/Product/planadd',array('menuid'=>$menuid));}" method="post" data-toggle="validate">
  <div class="bjui-pageContent">
    
  <if condition="$pinfo['type'] eq '1' ">
    <div class="form-group">
      <label class="col-sm-2 control-label">添加方式:</label>
      <input type="radio" name="addType" value="one" checked>  自定义添加
      <input type="radio" name="addType" value="batch">  模板添加
    </div>
    <div class="one">
    <table class="table table-condensed table-hover">
      <tbody>
        <tr>
          <td><label for="j_dialog_operation" class="control-label x90">销售日期:</label>
            <input type="text" data-toggle="datepicker" name="plantime" class="required" value="{$plantime}" data-rule="required"></td>
          <td><label for="j_dialog_code" class="control-label x85">场次：</label>
            <select name="games" id="games" data-toggle="selectpicker">
              <option value="1" selected>第一场</option>
              <option value="2">第二场</option>
              <option value="3">第三场</option>
              <option value="4">第四场</option>
              <option value="5">第五场</option>
            </select></td>
        </tr>
        <tr>
          <td><label for="j_dialog_operation" class="control-label x90">开始时间:</label>
            <input type="text" data-toggle="datepicker" data-pattern='HH:mm:ss' name="starttime" value="{$proconf.plan_start_time}"></td>
          <td><label for="j_dialog_operation" class="control-label x90">结束时间:</label>
            <input type="text" data-toggle="datepicker" data-pattern='HH:mm:ss' name="endtime" value="{$proconf.plan_end_time}"></td>
        </tr>
      </tbody>
    </table>
    </div>
    <div class="batch" style="display: none;">
      <table class="table table-striped">
        <tbody>
        <tr>
          <td><label for="j_dialog_operation" class="control-label x90">销售日期:</label>
            <input type="text" data-toggle="datepicker" name="plantime" class="required batch-input" disabled="true" value="{$plantime}" data-rule="required"></td>
          <td><label for="j_dialog_code" class="control-label x85">场次：</label>
            <select name="tplid" data-toggle="selectpicker">
              <volist name="tplplan" id="vo">
              <option value="{$vo.id}">第{$vo.number}场 {$vo.start}-{$vo.end}</option>
              </volist>
            </select></td>
        </tr>
        </tbody>
      </table>
    </div>
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingOne">
          <h4 class="panel-title"> <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne"> 座椅区域 </a> </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
          <div class="panel-body">
            <volist name="seat" id="vo" mod="3">
              <input type="checkbox" checked="checked" data-toggle="icheck" name="seat[]" value="{$vo.id}" data-label="{$vo.name}（座椅数{$vo.num}个）">
              <eq name="mod" value="3"><br/></eq>
            </volist>
              <input type="hidden" name="template_id" value="{$pinfo['template_id']}">
          </div>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingTwo">
          <h4 class="panel-title"> <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo"> 价格政策 </a> </h4>
        </div>
        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
          <div class="panel-body">
            <volist name="group" id="group" >
              <fieldset>
                <legend>{$group.name}</legend>
                <volist name="group['TicketType']" id="type" mod="3">
                  <input type="checkbox" checked="checked" data-toggle="icheck" name="ticket[]" value="{$type.id}" data-label="{$type.name}（价格：{$type.discount}）">
                  <eq name="mod" value="2"><br/></eq>
                </volist>
              </fieldset>
            </volist>
          </div>
        </div>
      </div>
    </div>
  <elseif condition="in_array($pinfo['type'],['2','4'])" />
    <div class="form-group">
      <label class="col-sm-2 control-label">添加方式:</label>
      <input type="radio" name="addType" value="one" checked>  逐条添加
      <input type="radio" name="addType" value="batch">  批量添加
    </div>
    <!--批量新增-->
    <div class="batch" style="display: none;">
    <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <td colspan="2"><label for="j_dialog_operation" class="control-label x90">销售日期:</label>
            <input type="text" data-toggle="datepicker" name="plantime" class="required batch" value="{$plantime}" data-rule="required"> 至 <input type="text" data-toggle="datepicker" name="plantimes" class="required batch" value="" disabled data-rule="required">
          </td>
        </tr>
        <tr>
          <td><label for="j_dialog_operation" class="control-label x90">开园时间:</label>
            <input type="text" class="batch" data-toggle="datepicker" data-pattern='HH:mm:ss' name="starttime" value="{$proconf.plan_start_time}"></td>
          <td><label for="j_dialog_operation" class="control-label x90">闭园时间:</label>
            <input type="text" class="batch" data-toggle="datepicker" data-pattern='HH:mm:ss' name="endtime" value="{$proconf.plan_end_time}"></td>
        </tr>
        <tr>
          <td><label for="j_dialog_operation" class="control-label x90">销售配额:</label>
            <input type="text" name="quotas" class="batch-input" disabled data-rule="required" value="{$proconf.quotas}" size="5">
          </td>
          <td><label for="j_dialog_operation" class="control-label x90">渠道配额:</label>
            <input type="text" name="quota" class="batch-input" disabled data-rule="required" value="{$proconf.quota}" size="5">
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <label for="j_dialog_operation" class="control-label x90">开始售票:</label>
            <input class="batch-input" type="radio" name="start" value="1" checked disabled>  是
            <input class="batch-input" type="radio" name="start" value="0" disabled>  否
          </td>
        </tr>
      </tbody>
    </table>
    </div>
    <!--单条新增-->
    <div class="one">
    <table id="tabledit2" class="table table-bordered table-hover table-striped table-top" data-toggle="tabledit" data-initnum="0" data-action="#" data-single-noindex="true">
            <thead>
                <tr data-idname="plan[#index#][id]">
                    <th title="No."><input type="text" name="plan[#index#][no]" class="no" data-rule="required" value="1" size="2"></th>
                    <th title="销售日期" align="center"><input type="text" name="plan[#index#][plantime]" data-rule="required" class="j_custom_issuedate" data-toggle="datepicker" value="{$plantime}" size="10"></th>
                    <th title="开园时间" align="center"><input type="text" name="plan[#index#][starttime]" data-pattern='HH:mm:ss' data-rule="required" class="j_custom_issuedate" data-toggle="datepicker" value="{$proconf.plan_start_time}" size="10"></th>
                    <th title="闭园时间" align="center"><input type="text" name="plan[#index#][endtime]" data-pattern='HH:mm:ss' data-rule="required" class="j_custom_indate"  data-toggle="datepicker" value="{$proconf.plan_end_time}" size="10"></th>
                    <th title="销售配额" align="center"><input type="text" name="plan[#index#][quotas]" data-rule="required" value="{$proconf.quotas}" size="5"></th>
                    <th title="渠道配额" align="center"><input type="text" name="plan[#index#][quota]" data-rule="required" value="{$proconf.quota}" size="5"></th>
                    <th title="" data-addtool="true" width="100">
                        <a href="javascript:;" class="btn btn-red row-del" data-confirm-msg="确定要删除该行信息吗？">删</a>
                    </th>
                </tr>
            </thead>
            <tbody>
              
            </tbody>
    </table>
    </div>
  
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingTwo">
          <h4 class="panel-title"> <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo"> 价格政策 </a> </h4>
        </div>
        <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
          <div class="panel-body">
            <volist name="group" id="group">
              <fieldset>
                <legend>{$group.name}</legend>
                <volist name="group['TicketType']" id="type" mod='3'>
                  <input type="checkbox" checked="checked" data-toggle="icheck" name="ticket[]" value="{$type.id}" data-label="{$type.name}（价格：{$type.discount}）">
                  <eq name='mod' value="3"><br /></eq>
                </volist>
              </fieldset>
            </volist>
          </div>
        </div>
      </div>
    </div>

  <elseif condition="$pinfo['type'] eq '3'" />
    <table class="table table-striped table-bordered">
      <tbody>
        <tr>
          <td width="80px">销售日期:</td>
          <td><input type="text" data-toggle="datepicker" value="{$plantime}" name="plantime"></td>
          <td width="80px"></td>
          <td></td>
        </tr>
        
      </tbody>
    </table>
    <table id="tabledit1" class="table table-bordered table-hover table-striped table-top" data-toggle="tabledit" data-initnum="0" data-action="#" data-single-noindex="true">
            <thead>
                <tr data-idname="plan[#index#][id]">
                    <th title="No."><input type="text" name="plan[#index#][no]" class="no" data-rule="required" value="1" size="2"></th> 
                    
                    <th title="开始时间"><input type="text" name="plan[#index#][starttime]" data-pattern='HH:mm:ss' data-rule="required" class="j_custom_issuedate" data-toggle="datepicker" value="" size="10"></th>
                    <th title="结束时间"><input type="text" name="plan[#index#][endtime]" data-pattern='HH:mm:ss' data-rule="required" class="j_custom_indate"  data-toggle="datepicker" value="{$proconf.plan_end_time}" size="10"></th>
                    <th title="销售配额"><input type="text" name="plan[#index#][quotas]" data-rule="required" value="{$proconf.quotas}" size="5"></th>
                    <th title="渠道配额"><input type="text" name="plan[#index#][quota]" data-rule="required" value="{$proconf.quota}" size="5"></th>
                    <th title="工具类型"><select name="plan[#index#][tooltype]" data-toggle="selectpicker">
                      <volist name="tooltype" id="vo">
                        <option value="{$vo.id}">{$vo.title}</option>
                      </volist>
                    </select>
                    </th>
                    <th title="" data-addtool="true" width="100">
                        <a href="javascript:;" class="btn btn-red row-del" data-confirm-msg="确定要删除该行信息吗？">删</a>
                    </th>
                </tr>
            </thead>
            <tbody>
              
            </tbody>
        </table>
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingTwo">
              <h4 class="panel-title"> <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo"> 价格政策 </a> </h4>
            </div>
            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
              <div class="panel-body">
                <volist name="group" id="group">
                  <fieldset>
                    <legend>{$group.name}</legend>
                    <volist name="group['TicketType']" id="type" mod='3'>
                      <input type="checkbox" checked="checked" data-toggle="icheck" name="ticket[]" value="{$type.id}" data-label="{$type.name}（价格：{$type.discount}）">
                      <eq name='mod' value="3"><br /></eq>
                    </volist>
                  </fieldset>
                </volist>
              </div>
            </div>
          </div>
        </div>
  </if>
  </div>
  <input name="product_id" value="{$pinfo['id']}" type="hidden">
  <input name="product_type" value="{$pinfo['type']}" type="hidden">
  <div class="bjui-pageFooter">
    <ul>
      <li>
        <button type="button" class="btn-close" data-icon="close">取消</button>
      </li>
      <li>
        <button type="submit" class="btn-default" data-icon="save">保存</button>
      </li>
    </ul>
  </div>
</form>
<script type="text/javascript">

$('input[type=radio][name=addType]').change(function() {
    if (this.value == 'batch') {
      $('.batch-input').attr('disabled',false);
      $('.batch').attr('disabled',false);
      $('.batch').css('display','block')
      $('.one').css('display','none')
    }
    else if (this.value == 'one') {
      $('.batch-input').attr('disabled',true);
      $('.batch').attr('disabled',true);
      $('.batch').css('display','none')
      $('.one').css('display','block')
    }
});
//自动有效日期
$(document).on('afterchange.bjui.datepicker', '.j_custom_issuedate', function(e, data) {
    var pattern = 'yyyy-MM-dd'
    var start   = end = data.value
    var $end    = $(this).closest('tr').find('.j_custom_indate')
    
    if ($end.length) {
        end.setFullYear(start.getFullYear() + 10)
        end.setDate(start.getDate() - 1)
        $end.val(end.formatDate(pattern))
    }
})
</script>