<?php if (!defined('LUB_VERSION')) exit(); ?>
<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<Managetemplate file="Home/Public/cssjs"/>
</head>
<body>
<div class="container">
<Managetemplate file="Home/Public/menu"/>
<!--内容主体区域 start-->
<div class="main row">
  <!--面包屑导航-->
  <ol class="breadcrumb">
    <li><a href="{:U('Home/Index/index');}">首页</a></li>
    <li><a href="{:U('Home/Index/product');}">售票</a></li>
    <li class="active">{$info['pid']|product_name}</li>
  </ol>
  <input type="hidden" id="channel_id" value="{$uinfo['cid']}"/>
  <div class="input-group col-md-5">
    <span class="input-group-addon" id="basic-addon1">请选择销售日期</span>
    <input size="16" type="text" value="{$plantime}" readonly class="form-control form_date" id="plantime" name="plantime">
  </div>
  <!-- Nav tabs -->
  <ul class="nav nav-tabs mt20" role="tablist" id="tablelist">
  </ul>
  <div class="row">
  <!-- Tab panes -->
  <div class="tab-content col-md-7 mt20" id="pageContent">
    <div class="table-responsive" id="ticketType">
      <table class="table table-bordered table-hover table-condensed">
        <thead>
          <tr>
            <td align="center">票型名称</td>
            <td align="center">单价</td>
            <td align="center">可售</td>
          </tr>
        </thead>
        <tbody id="tro" style="cursor: pointer;">
        </tbody>
      </table>
    </div>
    <div class="panel panel-success"><div class="panel-heading">订单备注</div>
    <div class="panel-body"><textarea class="form-control" name="remark" id="remark" rows="2" placeholder="请输入订单备注.."></textarea></div>
    </div>
  </div>
  <div class="col-md-5 mt20">
      <div class="panel panel-warning">
        <div class="panel-heading">
          <h3 class="panel-title"><span class="glyphicon glyphicon-list-alt"></span> 订单信息</h3>
        </div>
        <div class="panel-body">
          <p>说明：取票人凭身份信息及手机，前往景区指定窗口兑换纸质门票！</p>
        </div>
        <ul class="list-group form-inline">
          <li class="list-group-item">
            <div class="form-group">
                <input type="radio" name="contact_option" id="contact1" checked onclick="$('.contact_select').css('display','block'),$('.contact_input').css('display','none');">常用联系人
                <input type="radio" name="contact_option" id="contact2" onclick="$('.contact_select').css('display','none'),$('.contact_input').css('display','block');">设置联系人
            </div>
            <div class="form-group cityList">
              <select class="form-control province" name="tourists" id="tourists">
              </select>
              <select class="form-control city" name="city" id="citys">
                <option value="">客源地</option> 
              </select>
            </div>
          </li>
          <li class="list-group-item contact_select">            
            <select class="form-control" name="contact" id="contact">
              <option value="">常用联系人</option>
              <volist name="list" id="vo">
                <option value="{$vo.id}" data-phone="{$vo.phone}" data-name="{$vo.name}" data-idcard="{$vo.id_card}">{$vo.name}</option>
              </volist>  
            </select>
          </li>
          <li class="list-group-item contact_input" style="display:none"> 
          <div class="form-group"><input type="text" name="contacts" class="form-control" id="contacts" placeholder="联系人"></div>
          <div class="form-group"><input type="text" name="phone" class="form-control" id="phone" placeholder="手机号"></div>          
          </li>
          <li class="list-group-item contact_input" style="display:none">
          <div class="input-group">
            <div class="input-group-addon">身份证取票</div>
            <input type="text" name="id_card" id="id_card" class="form-control" placeholder="身份证号码">
            <div class="input-group-addon"><i class="glyphicon glyphicon-info-sign"></i> 自助取票机取票</div>
            </div>
          </li>
          <!--导游手机号 s-->
          <if condition="$proconf.black eq '1'">
          <li class="list-group-item">
          <div class="input-group">
            <div class="input-group-addon">导游手机号</div>
            <input type="text" name="guide_black" id="guide_black" class="form-control" placeholder="导游手机号码">
            <div class="input-group-addon"><i class="glyphicon glyphicon-info-sign"></i> 许可验证</div>
            </div>
          </li>
          </if>
          <!--导游手机号 e-->
          <if condition="$uinfo['group']['type'] eq '1'">
          <li class="list-group-item">
            <div class="input-group">
            <span class="input-group-addon">业务员</span>
            <input type="hidden" id="guideid" name="guide" value="{$uinfo['id']}" >
            <input type="text" class="form-control" id="guidename" value="{$uinfo['nickname']}" placeholder="业务员姓名" disabled="">
            <span class="input-group-addon"><a id="findguide" href="#" data-toggle="modal"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></a></span></div>
          </li>
          <else />
          <input type="hidden" id="guideid" value="{$uinfo['id']}"/>
          </if>
        </ul>
      </div>
      <div class="panel panel-default table-responsive" id="selectTickt">
        <table class="table table-bordered table-hover table-condensed" id="kselect">
          <thead>
            <tr>
              <td>票型</td>
              <td>单价</td>
              <td style="width:120px">数量</td>
              <td>小计</td>
              <td>操作</td>
            </tr>
          </thead>
          <tbody id="cart">
          </tbody>
        </table>
        <div class="panel-footer" >合计：<span id="subtoal">0.00</span></div>
      </div>
      <input type="hidden" value="" id="planID">
      <div class="btn-group">
      <if condition="$uinfo['group']['type'] neq '3'">
        <button type="button" id="print" class="btn btn-default" data-toggle="modal"><span class="glyphicon glyphicon-qrcode"></span>立即下单</button>
        <button type="button" class="btn btn-default" id="pre" data-toggle="modal">超量申请</button>
       <else />
       <button type="button" class="btn btn-default" id="gov" data-toggle="modal">立即预定</button>
       </if>
      </div>
  </div>
  </div>
  <script type="text/javascript">
  var selectdate;
  $(function() {
    empty_cart_ticket();
    scenic_drifting_plan($("#plantime").val(),{$info['type']},{$info['pid']});
    $('#plantime').datetimepicker().on('changeDate', function(ev) {
        selectdate = $('#plantime').val();
        empty_cart_ticket();
        scenic_drifting_plan(selectdate,{$info['type']},{$info['pid']});
    });
  });
  </script>

</div>
<div> 
  <!--内容主体区域 end--> 
  <!--弹出窗口 strat--> 
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:560px">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">支付方式</h4>
        </div>
        <div class="modal-body">
          <div class="panel panel-default">
            <div class="panel-body"> 当前订单总计：<strong>￥</strong><strong id="totalcash">0.00</strong> </div>
          </div>
          
          <!-- Nav tabs -->
          <ul class="nav nav-tabs" role="tablist">
          <if condition="$uinfo['groupid'] neq '3'">
            <li class="active"><a href="#balance" role="tab" data-toggle="tab">余额支付</a></li>
            <if condition="in_array('2',explode(',',$uinfo['param']['ispay']))">
            <li><a href="#window" role="tab" data-toggle="tab">窗口现付</a></li>
            </if>
            <if condition="in_array('3',explode(',',$uinfo['param']['ispay']))">
            <li><a href="#silver" role="tab" data-toggle="tab">个人网银</a></li>
            </if>
            <!-- 
            <li><a href="#firm" role="tab" data-toggle="tab">企业网银</a></li>
            -->
            <else />
            <li class="active"><a href="#ious" role="tab" data-toggle="tab">支付/排座方式</a></li>
            </if>
          </ul>
          
          <!-- Tab panes -->
          <div class="tab-content">
          <if condition="$uinfo['group']['type'] neq '3'">
            <div class="tab-pane active" id="balance">
            <p></p>
              <div class="form-group">
                <p>当前账户可用余额：<strong>￥</strong><strong id="money"></strong></p>
                
              </div>
              <div class="form-group">
                <input class="form-control qk" type="text" id="tomoney" value="" readonly>
              </div>
              
              <button type="button" class="btn btn-success" id="balancePay" data-loading-text="正在提交..." data-toggle="modal">立即支付</button>
            </div>
            <if condition="in_array('2',explode(',',$uinfo['param']['ispay']))">
            <div class="tab-pane" id="window">
              <div class="form-group">
                <label class="radio-inline">
                    <input type="radio" name="pay_type" id="pay_type" value="1" checked> 现金支付
                 </label>
                 <div class="form-group">
                 </div>
                 <input type="radio" name="seat_type" id="seat_type" value="1" checked>
                 <button type="button" class="btn btn-success" id="govPay" data-loading-text="正在提交..." data-toggle="modal">立即预订</button>
              </div>
            </div>
            </if>
            <if condition="in_array('3',explode(',',$uinfo['param']['ispay']))">
            <div class="tab-pane" id="silver">
              <form action="{:U('Home/Pay/index')}" method="post" target="_blank" id="form-pay">
                <input type="hidden" id="pay_money" name="money" value="" readonly>
                <ul class="pay">
                  <li>
                    <input id="bank-alipay" type="radio" name="bank" value="alipay" checked="">
                    <label for="bank-alipay" class="bank-alipay"></label>
                  </li>
                </ul>
                <input id="order_id" name="order_id" value="" type="hidden">
                <button type="button" class="btn btn-success" id="webpay">立即支付</button>
              </form>
            </div>
            </if>
            <else />
            <div class="tab-pane active" id="ious">
            <p></p>
            <div class="form-group">
                <input class="form-control qk" type="text" id="tomoney" value="" readonly>
              </div>
              <p>
                 <label class="radio-inline">
                    <input type="radio" name="pay_type" id="pay_type" value="1" checked> 现金支付
                 </label>
                <label class="radio-inline">
                    <input type="radio" name="pay_type" id="pay_type" value="3"> 结算单支付
                 </label>
              </p><p>
              <label class="radio-inline">
                <input type="radio" name="seat_type" id="seat_type" value="1" checked> 自动排座
              </label>
              <label class="radio-inline">
                <input type="radio" name="seat_type" id="seat_type" value="2"> 手动选座
              </label>
              </p>
              <button type="button" class="btn btn-success" id="govPay" data-loading-text="正在提交..." data-toggle="modal">立即预定</button>
            </div>
            </if>
            <input id="sn" value="" type="hidden">
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--错误提示-->
  <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:560px">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">错误提示</h4>
        </div>
        <div class="modal-body">
          <!-- Tab panes -->
          <div class="tab-content">
            <div class="tab-pane active" id="balance">
              <p></p>
              <div class="form-group">
                <p class="text-danger"><span class="glyphicon glyphicon-remove "></span><strong id="error">您选择的票型已经售完,请选择其他种类。</strong></p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
        <a href="javascript:void(0)" onClick="newPage();" class="btn btn-danger" data-dismiss="modal">关闭</a>
      	</div>
      </div>
    </div>
  </div>
  <!--成功提示-->
  <div class="modal fade" id="success" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:560px">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">成功提示</h4>
        </div>
        <div class="modal-body">
          <!-- Tab panes -->
          <div class="tab-content">
            <div class="tab-pane active" id="balance">
              <p></p>
              <div class="form-group">
                <h3><span class="glyphicon glyphicon-ok"></span><strong id="succ_info"></strong></h3>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
        <a href="javascript:void(0)" onClick="newPage();" class="btn btn-danger" data-dismiss="modal">关闭</a>
      	</div>
      </div>
    </div>
  </div>
  <!--查找导游-->
  <div class="modal fade" id="myModal4" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:560px">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">查找导游</h4>
        </div>
        <div class="modal-body"> 
              <div class="form-inline" style="margin-top:10px;">
                <div class="form-group">
                  <input type="text" class="form-control" name="name" id="guidesname" placeholder="导游姓名"/>
                </div>
                <div class="form-group">
                  <div class="input-group">
                    <div class="input-group-addon">或</div>
                    <input type="text" name="phone" class="form-control" id="guidesphone" placeholder="手机号码"/>
                  </div>
                </div>
                <button class="btn btn-default" id="guidesearch"> 查找 </button>
              <!--导游信息显示-->
              <div><table class="table table-bordered table-condensed">
                <thead>
                  <tr>
                    <th>姓名</th>
                    <th>电话</th>
                    <th>选择</th>
                  </tr>
                </thead>
                <tbody  id="chooseguide">
                </tbody>
              </table> </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--在线支付弹出窗口S by liran 2015/3/31添加-->
  <div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:300px">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">付款确认</h4>
        </div>
        <div class="modal-body">
          <!-- Tab panes -->
          <div class="tab-content">
            <div class="tab-pane active" id="balance">
              <button type="button" class="btn btn-success check-status">支付完成</button>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <button type="button" class="btn btn-danger check-status">支付遇到问题</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div> 
  <!--身份证号-->
  <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mycard">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      ...
    </div>
  </div>
</div>
  <!--弹出窗口 end-->
  <Managetemplate file="Home/Public/footer"/>
  <!--页脚--> 
</div>
</body>
</html>
<script src="{$config_siteurl}static/home/js/cart.js?=<?php echo  rand(100,999);?>"></script>
<script type="text/javascript">
var type = {$info['type']},
    product = {$info['pid']},
    real = 0;
/*网银支付*/
$('#webpay').click(function(){
    $("#pay_money").val($("#tomoney").val()); //订单金额
    $("#order_id").val($("#sn").val());       //订单号    
    $("#form-pay").submit();                  //表单提交
    $("#myModal").modal('hide');
    $("#payModal").modal('show');  //显示支付状态框   
  });
$(".check-status").click(function(){
  window.location.reload();
}) 
</script>