<Managetemplate file="Wechat/Public/header"/>
<style type="text/css" media="screen">
  .content p{font-size:0.8rem;line-height: 0.5rem;}
  .content h4{margin:0.1rem;}
  .content-block{margin:0rem;}
  .mt2{margin-top: 0.2rem}
</style>
<script type="text/javascript">
    var globals = {$goods_info};
</script>
<div class="page">
  <header class="bar bar-nav">
    <h1 class="title"><i class="iconfont">&#xe603</i>{$info.title}</h1>
    <if condition="empty($uinfo['promote'])">
    <button class="button button-link button-nav pull-right"  ontouchend="window.location.href='{:U('Wechat/Index/uinfo');}'">
    </if>
      <span class="icon icon-me"></span>
    </button>
  </header>
  <div class="content">
    <div class="content-padded">
      <div valign="bottom" class="card-header color-white no-border no-padding">
        <img class='card-cover' src="https://img.alizhiyou.com/310.jpg">
      </div>
    </div>
    <div class="content-block" style="margin-top: 1.5rem">
      <p><a href="#" class="button button-big button-fill button-warning open-goods-cart">立即购票</a></p>
    </div>
</div>
  <!--内容-->
</div>
<!-- About Popup -->
<div class="popup goods-cart">
  <header class="bar bar-nav">
    <a href="#" class="icon pull-right close-popup"><i class="iconfont">&#xe609</i></a>
  </header>
  <div class="sku-layout">
    <div class="adv-opts layout-content">
    <div class="goods-models js-sku-views block block-list block-border-top-none">
      <dl class="clearfix block-item">
        <dt class="model-title sku-sel-title">
          <label>演出时间：</label>
        </dt>
        <dd><?php //dump($uinfo);?>
          <ul class="model-list sku-sel-list" id="plan">
          </ul>
        </dd>
      </dl>
      <dl class="clearfix block-item">
        <dt class="model-title sku-sel-title">
          <label>演出票价：</label>
        </dt>
        <dd>
          <ul class="model-list sku-sel-list" id="price">
            <li class="tag sku-tag pull-left ellipsis unavailable">请选择售票日期</li>
          </ul>
        </dd>
      </dl>
      <dl class="clearfix block-item">
        <dt class="model-title sku-num pull-left">
          <label>数量</label>
        </dt>
        <dd>
          <dl class="clearfix">
            <div class="quantity">
              <button class="minus disabled" type="button" disabled="true"></button>
              <input type="text" class="txt" value="1" id="num">
              <button class="plus" type="button"></button>
              <div class="response-area response-area-minus"></div>
              <div class="response-area response-area-plus"></div>
              <div class="txtCover"></div>
            </div>
            <!--
            <div class="stock pull-right font-size-12">
              <dt class="model-title stock-label pull-left">
                <label>剩余: </label>
              </dt>
              <dd class="stock-num"> 0 </dd>
            </div>
            -->
          </dl>
        </dd>
      </dl>
      <div class="block-item block-item-messages">
        <div class="sku-message">
          <dl class="clearfix">
            <dt class="pull-left">
              <label for="ipt-0"><sup class="required">*</sup>姓名</label>
            </dt>
            <dd class="comment-wrapper clearfix">
              <input data-valid-type="text" required="" tabindex="1" id="name" name="name" type="text" class="txt js-message font-size-14">
            </dd>
          </dl>
          <dl class="clearfix">
            <dt class="pull-left">
              <label for="ipt-1"><sup class="required">*</sup>电话</label>
            </dt>
            <dd class="comment-wrapper clearfix">
              <input data-valid-type="text" required="" tabindex="2" id="phone" name="phone" type="text" class="txt js-message font-size-14">
            </dd>
          </dl>
          <!--
          <dl class="clearfix">
            <dt class="pull-left">
              <label for="ipt-2">身份证</label>
            </dt>
            <dd class="comment-wrapper clearfix">
              <input data-valid-type="text" tabindex="3" id="card" name="card" type="text" class="txt js-message font-size-14">
            </dd>
          </dl>
          -->
          <dl class="clearfix">
            <dt class="pull-left">
              <label for="ipt-2">留言</label>
            </dt>
            <dd class="comment-wrapper clearfix item-input">
             <input data-valid-type="text" tabindex="3" id="remark" name="remark" type="text" class="txt js-message font-size-14" value="">
            </dd>
          </dl>
        </div>
      </div>
    </div>
    <div class="btn">
      <a href="#" class="button button-fill button-warning button-big buy">下一步</a>
    </div>
  </div>
  </div>
</div>
<!--产品信息区域-->
<Managetemplate file="Wechat/Public/footer"/>
<script type="text/javascript">
  $(function() {
    wx.ready(function(){
      /*
        wx.checkJsApi({
            jsApiList: [
                'onMenuShareAppMessage',
            ]
        });
        wx.showMenuItems({
            menuList: ['menuItem:share:appMessage','menuItem:share:timeline']
        });*/
        wx.onMenuShareAppMessage({
            title: '{$proconf.wx_share_title}',
            desc: '{$proconf.wx_share_desc}',
            link: '{$urls}',
            imgUrl: '{$config_siteurl}static/images/wshare_{$pid}.jpg',
            trigger: function (res) {
            },
            success: function (res) {
                alert('分享给好友成功');
            },
            cancel: function (res) {
            },
            fail: function (res) {
                alert(JSON.stringify(res));
            }
        });
        wx.onMenuShareTimeline({
            title: '{$proconf.wx_share_title}',
            desc: '{$proconf.wx_share_desc}',
            link: '{$urls}',
            imgUrl: '{$config_siteurl}static/images/wshare_{$pid}.jpg',
            trigger: function (res) {
            },
            success: function (res) {
            },
            cancel: function (res) {
            },
            fail: function (res) {
                alert(JSON.stringify(res));
            }
        });
    });
    var getPlantpl = document.getElementById('plantpl').innerHTML;
    var getPricetpl = document.getElementById('pricetpl').innerHTML;
    laytpl(getPlantpl).render(globals, function(html){
        document.getElementById('plan').innerHTML = html;
    });
    var plan = '0',
        area = '0',
        ticket = '0',
        price = '0',
        discount = '0',
        num = '0',
        name = '',
        phone = '',
        card = '',
        msg = '',
        pay = '',
        crm = '',
        param = '',
        toJSONString = '',
        postData = '',
        subtotal = '0',
        activety = {$info.id},
        remark = '';
    $("#plan li").click(function(){
      //检查当前被选择的元素是否已经有已选中的
      $(".goods-models li").each(function(){
        if($(this).hasClass("tag-orangef60 active")){ toggle($(this))};
      });
      //为当前选择加上
      active($(this));
      refreshNum();
      plan = $(this).data('plan');
      area = 0;
      ticket = 0;
      price = 0;
      $(".stock-num").html($(this).data('num'));
      laytpl.fn = plan;
      laytpl(getPricetpl).render(globals, function(html){
        document.getElementById('price').innerHTML = html;
      });
    });
    $(document).on("click","#price li",function(){
       //判断是否已经选择计划
      if(!$(this).hasClass("unavailable")){
        if(plan != 0){
          $("#price li").each(function(){
            if($(this).hasClass("tag-orangef60 active")){ toggle($(this))};
          });
          area = $(this).data('area');
          ticket = $(this).data('priceid');
          price = $(this).data('price');
          discount = $(this).data('discount');
          num = $(this).data('num');
          //更新可售数量  当为0时 禁用
          $(".stock-num").html(num);
          active($(this));
          refreshNum();
        }else{
          $.toast("请选择演出日期!");
        }
      }
    });
    //删除选中状态
    function toggle(t){t.toggleClass("tag-orangef60");t.toggleClass("active");}
    //选中
    function active(t){t.addClass("tag-orangef60");t.addClass("active");}
    //删除选中
    function deActive(t){t.removeClass("tag-orangef60");t.removeClass("active");}
    //数量增加减少
    $(".response-area-minus").click(function(){
      if(num > 1){
        num = getNum() - 1;
        updateNum();
      }else{
        updateBtnStatus();
      }
    });
    $(".response-area-plus").click(function(){
      //判断是否选择日期和价格
      if(plan != 0 && area != 0 && price != 0){
        if(num < globals['user']['maxnum']){
          //限制单笔订单最大数量
          num = getNum() + 1;
          updateNum();
        }else{
          $.toast("亲，您一次只能买这么多了!");
        }
      }else if(plan == 0 && area == 0){
        $.toast("请选择演出日期和演出票价!");
      }else if(area == 0){
        $.toast("请选择演出票价!");
      }else{
        $.toast("请选择演出日期和演出票价!");
      }
    });
    function changeNum(t){
      $("#num").val();
    }
    //更换场次时重置页面
    function refreshNum(){
      $("#num").val('1');
      getNum();
      updateBtnStatus();
    }
    //更新数量
    function updateNum(){
        $("#num").val(num);
        updateBtnStatus();
    }
    //更新数量增减状态
    function updateBtnStatus(){
      if(num > 1){
        $('.minus').removeClass("disabled");
        $('.minus').removeAttr("disabled");
      }else{
        $('.minus').addClass("disabled"),
        $('.minus').attr("disabled", "true")
      }
    }
    //获取数量
    function getNum(){
      num = parseInt($("#num").val());
      return num;
    }

    $(".buy").click(function(){
      $(this).attr("disabled", true).val('提交中..');
      //验证输入
      name = $("#name").val().replace(/ /g,''),
      phone = $("#phone").val(),
      /*card = $("#card").val().replace(/ /g,''),*/
      msg = '';
      if(plan.length == 0){
        msg = "请选择销售日期!";
      }
      if(price.length == 0 || num == 0){
        msg = "请选择票型并选择要购买的数量!";
      }
      if(name.length == 0){
        msg = "姓名不能为空";
      }
      if(!/^(0|86|17951)?(13[0-9]|15[012356789]|18[0-9]|14[57]|17[0-9])[0-9]{8}$/.test(phone)){
        msg = "手机号码格式不正确!";
      }
      if(msg != ''){$.toast(msg);return false;}
      /*
      if(card.length == 0){
        $.prompt('亲，您确定不用身份证取票这一高逼格的功能吗?',
          function (value) {
            post_server(1,value);
          },
          function (value) {
            post_server(2);
          }
        );
      }else{
        if(/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/.test(card) == false){
          msg = "身份证号码输入有误!";
        }else{
          post_server(2,card);
        }
      }*/
      post_server(2);
    });
    /*验证身份证取票 type  1验证 2 不验证  */
    function post_server(type,card){
      //计算金额
      if(globals['user']['epay'] == '1'){
        subtotal = parseFloat(price * parseInt(num));
      }else{
        subtotal = parseFloat(discount * parseInt(num));
      }
      remark = $('#remark').val();
     /*获取支付相关数据*/
      pay = '{"cash":0,"card":0,"alipay":0}';
      param = '{"remark":"'+remark+'","activity":"'+activety+'","settlement":"'+globals['user']['epay']+'"}';
      crm = '{"guide":"'+globals['user']['guide']+'","qditem":"'+globals['user']['qditem']+'","phone":"'+phone+'","contact":"'+name+'","memmber":"'+globals['user']['memmber']+'"}';
      toJSONString = '{"areaId":'+area+',"priceid":'+ticket+',"price":'+price+',"num":'+num+'}';
      postData = 'info={"subtotal":"'+subtotal+'","plan_id":'+plan+',"checkin":1,"sub_type":0,"type":1,"data":['+ toJSONString + '],"crm":['+crm+'],"pay":['+pay+'],"param":['+param+']}';
      /*提交到服务器**/
      $.ajax({
          type:'POST',
          url:'<?php echo U('Wechat/Activity/acty_order',$param);?>',
          data:postData,
          dataType:'json',
          success:function(data){
              if(data.statusCode == "200"){
                  location.href = data.url;
              }else{
                  $.toast("下单失败:"+data.msg);
              }
          }
      });
    }
  });
  var text = "<p style='text-align:left'>1、1.2米以下儿童可免费进场但无座；1.2米以上请购成人票。<br>"+
  "2、门票一旦售出，恕不退换。<br >"+
  "3、请观众提前30分钟检票入场，对号入座，如遇开演请听从工作人员安排。<br>"+
  "4、七夕活动买一赠一。<br>"+
  "5、任何通过微信购买门票的人均认为已经阅读、理解并接受了以上条款。</p>";
  $(document).on('click','.open-goods-cart', function () {
    $.alert(text, '购票须知', function () {
      $.popup('.goods-cart');
    });
  });
</script>
<script id="plantpl" type="text/html">
{{# for(var i = 0, len = d.plan.length; i < len; i++){ }}
    <li class="tag sku-tag pull-left ellipsis" data-plan="{{ d.plan[i].id }}" data-num="{{d.plan[i].num}}">{{ d.plan[i].title }}</li>
{{# $(".stock-num").html(d.plan[i].num); } }}
</script>
<script id="pricetpl" type="text/html">
{{# $(d.area[laytpl.fn]).each(function(i){ }}
  <li class="tag sku-tag pull-left ellipsis {{# if(this.num == '0'){ }}unavailable{{# } }}" data-price="{{ this.money }}" data-discount="{{ this.moneys }}" data-area="{{ this.area }}" data-priceid="{{ this.priceid }}" data-num="{{ this.num }}">{{this.moneys}}元 ( {{this.name}} )</li>
{{#    });}}
</script>
</body>
</html>
