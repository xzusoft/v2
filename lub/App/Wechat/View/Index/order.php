<Managetemplate file="Wechat/Public/header"/>
<div class="page">
<header class="bar bar-nav">
    <h1 class="title">订单确认</h1>
  </header>
  <div class="content">
    <div class="card">
        <div class="card-header">{$data.plan_id|planShow}</div>
        <div class="card-content">
          <div class="list-block">
            <ul>
            <volist name="data['info']['data']['area']" id="vo" key='k'>
              <li class="item-content">
                  <div class="item-inner">
                    <div class="item-title">{$vo.areaId|areaName}</div>
                    <div class="item-after">x {$vo.num}</div>
                  </div>
              </li>
            </volist>
            </ul>
          </div>
        </div>
        <div class="card-footer price"> <span>优惠金额</span><span>{$data['info']['data']['poor']|format_money}</span></div>
        <div class="card-footer price"> <span>实付金额</span><span> ￥ {$data['money']}</span></div>
    </div>
    
   <div class="list-block">
    <ul>
      <li class="item-content">
        <div class="item-inner">
          <div class="item-title">订单号 : {$data.order_sn}</div>
        </div>
      </li>
      <li class="item-content">
        <div class="item-inner">
          <div class="item-title">联系人 : {$data['info']['crm'][0]['contact']}</div>
        </div>
      </li>
      <li class="item-content">
        <div class="item-inner">
          <div class="item-title">电话 : {$data['info']['crm'][0]['phone']}</div>
        </div>
      </li>
      <li class="item-content">
        <div class="item-inner">
          <div class="item-title">备注 : {$data['info']['param'][0]['remark']}</div>
        </div>
      </li>
    </ul>
  </div> 

  <!--支付方式-->
  <if condition="$data.status neq '1' || $data.status neq '9' ">
  <div class="content-block">
    <if condition="$data.type eq '2'">
    <p><a href="#" class="button button-big button-fill button-warning" id="credit">授信额支付 </a></p>
    </if>
    <if condition="$data.type eq '1' || $data.type eq '8' || $data.type eq '6'">
    <p><a href="#" class="button button-big button-fill button-success" id="wxpay">微信支付</a></p>
    <p><a href="{:U('Wechat/Index/dfpay',array('sn'=>$data['order_sn'],'pid'=>$ginfo['pid']));}" class="button button-big button-fill wxpay button-warning" id="dfpay">请人代付</a></p>
    </if>
    <if condition="$data.type eq '6'">
    <p><a href="#" id="window_pay" class="button button-big button-fill">窗口现金支付 </a></p>
    </if>
    </div>
  </div>
  </if>
</div>
<Managetemplate file="Wechat/Public/footer"/>
<script type="text/javascript">
  $(function() {
    var seat_type = '1',
        money = {$data.money},
        sn = {$data.order_sn},
        pay_type = '2';
    wx.ready(function(){wx.hideOptionMenu();});
    <if condition="in_array($data['type'],array('1','6','8'))">
    /*微信支付*/
    $(document).on('click', '#wxpay',function () {
      if (typeof WeixinJSBridge == "undefined"){
         if(document.addEventListener){
             document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
         }else if(document.attachEvent){
             document.attachEvent('WeixinJSBridgeReady', onBridgeReady); 
             document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
         }
      }else{
         onBridgeReady();
      }
    });
    function onBridgeReady(){
       WeixinJSBridge.invoke(
           'getBrandWCPayRequest', 
           {$wxpay|json_encode},
           function(res){
             if(res.err_msg == "get_brand_wcpay_request:ok"){
                var link = "{:U('Wechat/Index/pay_success',array('sn'=>$data['order_sn'],'pid'=>$ginfo['pid']));}";
                window.location.href=link;
             }
           }
       ); 
    }

    /*窗口支付*/
    $(document).on('click', '#window_pay',function () {
      $.confirm("系统已成功为您预留座位，请尽快付款确认，或是演出前一小时到景区现金窗口付款，逾期座位不再保留。",
        function(){
          var postData = 'info={"tomoney":"'+money+'","sn":"'+sn+'","pay_type":"1","seat_type":'+seat_type+'}';
          $.ajax({
            type:'POST',
            url:'<?php echo U('Wechat/Index/window_pay',array('param'=>$param));?>',
            data:postData,
            dataType:'json',
            timeout: 3500,
            success:function(data){
              if(data.statusCode == "200"){
                location.href = data.url;
              }else{
                $.alert("支付失败!"+data.msg);
              }
            },
            error:function(data){
              $.alert("支付失败!"+data.msg);
            }
          });
        },
        function(){
          $.alert('您已经取消支付!');
        }
      );
    });
    </if>
    /*授信额支付*/
    <if condition="$data.type eq '2' ">
    $(document).on('click', '#credit',function () {
      $.confirm('订单金额:'+money, 
        function () {
            var postData = 'info={"tomoney":"'+money+'","sn":"'+sn+'","pay_type":"'+pay_type+'","seat_type":'+seat_type+'}';
            $.ajax({
                  type:'POST',
                  url:'<?php echo U('Wechat/Index/pay');?>',
                  data:postData,
                  dataType:'json',
                  success:function(data){
                      if(data.statusCode == "200"){
                        location.href = data.url;
                      }else{
                        $.toast("支付失败!"+data.msg);
                      }
                  }
              });
        },
        function () {
          $.alert('您已经取消支付!');
        }
      );
    });
    </if>
  });
  
</script>
</body>
</html>