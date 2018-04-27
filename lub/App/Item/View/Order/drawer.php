<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>出票方式</title>
</head>
<body>
<script src="{$config_siteurl}static/js/LodopFuncs.js?=<?php echo  rand(100,999);?>" type="text/javascript"></script>
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0> </object>
<embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0></embed>
</objec>
<div class="page unitBox">
<style type="text/css">
.print_type{height: 25px;line-height: 1.5;}
</style>
<div class="pageContent">
  <div class="pageFormContent">
  <div class="print_type">
  <input type="radio" name="type" id="print_type" value="1" <if condition="$proconf['print_type'] eq '1'">checked</if>> 一人一票  
  <input type="radio" name="type" id="print_type" value="2" <if condition="$proconf['print_type'] eq '2'">checked</if>> 一单一票
  </div>
  <button type="button" style="width:200px; height:130px" id="print_ticket" onclick="printTicket({$data.sn},{$data.plan_id})">打印门票</button>
  </div>
</div>
</div>
<script>
var LODOP; //声明为全局变量  
function printTicket(sn,planid){
	var type = $('#print_type:checked').val();
	$("#print_ticket").attr("disabled", true).val('打印中..');
	$.ajax({
		type:'get',
		dataType : 'json',
		url:'index.php?g=Item&m=Order&a=printTicket&sn='+sn+'&plan_id='+planid+'&user={$data.user}&type='+type,
		timeout: 2500,
        error: function(){
        	/*关闭当前弹窗*/
			$(this).dialog('close','print');
            layer.msg('服务器请求超时，请检查网络...');
        },
		success:function(data){
			var selSeat = eval(data.info);/*返回的座位信息*/
			if(data.status == '1'){
				$.each(selSeat,function(){
					/*打印设置部分*/
					CreateFullBill(this);
					/*设置连续打印*/
					LODOP.SET_PRINT_PAGESIZE(2,1000,1800,"USER");
					LODOP.SET_PRINT_MODE("POS_BASEON_PAPER",true);
					LODOP. PRINT();	
					/*关闭当前弹窗*/
					$(this).dialog('close','print');
				});
			}else{
				$(this).alertmsg('error',data.message);
				$(this).dialog('close','print');
			}
		}
	});

	}
/*打印页面控制*/
function CreateFullBill(data) {
	LODOP=getLodop(); 
	//LODOP.SET_PRINT_MODE("PRINT_NOCOLLATE",1);
	LODOP.ADD_PRINT_TEXT(17,469,66,30,"日期:");
	LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
	LODOP.SET_PRINT_STYLEA(0,"FontSize",13);
	LODOP.SET_PRINT_STYLEA(0,"Bold",1);
	LODOP.ADD_PRINT_TEXT(17,530,145,25,data.plantime);
	LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
	LODOP.SET_PRINT_STYLEA(0,"Bold",1);


	LODOP.ADD_PRINT_TEXT(50,469,66,30,"票价：");
	LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
	LODOP.SET_PRINT_STYLEA(0,"FontSize",13);
	LODOP.SET_PRINT_STYLEA(0,"Bold",1);
	LODOP.ADD_PRINT_TEXT(50,530,125,25,data.price+"元");
	LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
	LODOP.SET_PRINT_STYLEA(0,"Bold",1);



	LODOP.ADD_PRINT_TEXT(80,469,66,30,"人数:");
	LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
	LODOP.SET_PRINT_STYLEA(0,"FontSize",13);
	LODOP.SET_PRINT_STYLEA(0,"Bold",1);
    LODOP.ADD_PRINT_TEXT(80,530,100,25,data.number);
	LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
	LODOP.SET_PRINT_STYLEA(0,"Bold",1);

	LODOP.ADD_PRINT_TEXT(110,469,66,30,"票类:");
	LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
	LODOP.SET_PRINT_STYLEA(0,"FontSize",13);
	LODOP.SET_PRINT_STYLEA(0,"Bold",1);
	LODOP.ADD_PRINT_TEXT(110,530,112,25,data.priceName);
	LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
	LODOP.SET_PRINT_STYLEA(0,"Bold",1);


	LODOP.ADD_PRINT_BARCODE(137,497,100,100,"QRCode",data.sn);
	LODOP.SET_PRINT_STYLEA(0,"FontSize",127);
	LODOP.SET_PRINT_STYLEA(0,"ShowBarText",0);
	LODOP.SET_PRINT_STYLEA(0,"GroundColor","#FFFFFF");
	LODOP.SET_PRINT_STYLEA(0,"QRCodeErrorLevel","H");

	//LODOP.ADD_PRINT_TEXT(232,491,143,23,data.remark);
	LODOP.ADD_PRINT_TEXT(232,491,143,23,data.sns);
	var type = data.remark_type;
	switch(type){
		case '1':
			/*市民票*/
			LODOP.ADD_PRINT_RECT(60,416,180,50,0,2);
			LODOP.ADD_PRINT_TEXT(72,431,172,40,"高铁免票转赠无效");
			LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
			LODOP.SET_PRINT_STYLEA(0,"FontSize",20);
			LODOP.SET_PRINT_STYLEA(0,"Bold",1);
			break;
		case '2':
			/*市民票*/
			LODOP.ADD_PRINT_RECT(60,416,180,50,0,2);
			LODOP.ADD_PRINT_TEXT(72,431,172,40,"高铁优惠半价票");
			LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
			LODOP.SET_PRINT_STYLEA(0,"FontSize",20);
			LODOP.SET_PRINT_STYLEA(0,"Bold",1);
			break;
	}
	}
</script>
</body>
</html>