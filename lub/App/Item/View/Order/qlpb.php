<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>出票方式</title>
</head>
<body>
<script src="{$config_siteurl}static/js/LodopFuncs.js?=20160821" type="text/javascript"></script>
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
  <input type="radio" name="type" id="print_type" value="1" <if condition="$procof['print_type'] eq '1'">checked</if>> 一人一票  
  <input type="radio" name="type" id="print_type" value="2" <if condition="$procof['print_type'] eq '2'">checked</if>> 一单一票
  </div>
  <button type="button" style="width:200px; height:130px" onclick="printTicket({$data.sn},{$data.plan_id})">打印门票</button>
  </div>
</div>
</div>
<script>
var LODOP; //声明为全局变量
function printTicket(sn,planid){
	var type = $('#print_type:checked').val();
	$.ajax({
		type:'get',
		dataType : 'json',
		url:'index.php?g=Item&m=Order&a=printTicket&sn='+sn+'&plan_id='+planid+'&user={$data.user}&type='+type,
		success:function(data){
			var selSeat = eval(data.info);/*返回的座位信息*/
			if(data.status == '1'){
				$.each(selSeat,function(){			
					/*打印设置部分*/
					CreateFullBill(this,type);
					/*设置连续打印*/
					LODOP.SET_PRINT_PAGESIZE(2,800,2350,"USER");
					LODOP.SET_PRINT_MODE("POS_BASEON_PAPER",true);
					//LODOP.PREVIEW();
					LODOP.PRINT();	
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
function CreateFullBill(data,type) {
		LODOP=getLodop();
		if(type == '1'){
			//一人一票
			LODOP.ADD_PRINT_TEXT(91,220,140,30,"时间/TIME");
			LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
			LODOP.SET_PRINT_STYLEA(0,"FontSize",16);
			LODOP.SET_PRINT_STYLEA(0,"Bold",1);
			LODOP.ADD_PRINT_TEXT(165,220,140,30,"票价/PRICE");
			LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
			LODOP.SET_PRINT_STYLEA(0,"FontSize",16);
			LODOP.SET_PRINT_STYLEA(0,"Bold",1);
			LODOP.ADD_PRINT_TEXT(166,370,108,30,data.price+"元");
			LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
			LODOP.SET_PRINT_STYLEA(0,"FontSize",16);
			LODOP.SET_PRINT_STYLEA(0,"Bold",1);
			LODOP.ADD_PRINT_TEXT(42,240,451,46,data.product_name);
			LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
			LODOP.SET_PRINT_STYLEA(0,"FontSize",20);
			LODOP.SET_PRINT_STYLEA(0,"Alignment",2);
			LODOP.SET_PRINT_STYLEA(0,"Bold",1);
			LODOP.ADD_PRINT_TEXT(91,370,280,30,data.plantime);
			LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
			LODOP.SET_PRINT_STYLEA(0,"FontSize",16);
			LODOP.SET_PRINT_STYLEA(0,"Bold",1);
			LODOP.ADD_PRINT_BARCODE(98,620,110,110,"QRCode",data.sn);
			LODOP.SET_PRINT_STYLEA(0,"FontSize",127);
			LODOP.SET_PRINT_STYLEA(0,"ShowBarText",0);
			LODOP.SET_PRINT_STYLEA(0,"GroundColor","#FFFFFF");
			LODOP.SET_PRINT_STYLEA(0,"QRCodeErrorLevel","H");

			LODOP.ADD_PRINT_TEXT(129,370,163,29,data.seat);
			LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
			LODOP.SET_PRINT_STYLEA(0,"FontSize",16);
			LODOP.SET_PRINT_STYLEA(0,"Bold",1);
			LODOP.ADD_PRINT_TEXT(62,793,100,20,data.plantime);

			LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
			LODOP.SET_PRINT_STYLEA(0,"Alignment",3);
			LODOP.ADD_PRINT_TEXT(238,222,100,20,data.sns);
		}else{
			//一单一票
			LODOP.ADD_PRINT_TEXT(91,220,140,30,"时间/TIME");
			LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
			LODOP.SET_PRINT_STYLEA(0,"FontSize",16);

			LODOP.SET_PRINT_STYLEA(0,"Bold",1);
			LODOP.ADD_PRINT_TEXT(130,220,140,30,"票价/PRICE");
			LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
			LODOP.SET_PRINT_STYLEA(0,"FontSize",16);
			LODOP.SET_PRINT_STYLEA(0,"Bold",1);

			LODOP.ADD_PRINT_TEXT(165,220,140,30,"人数/NUMBER");
			LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
			LODOP.SET_PRINT_STYLEA(0,"FontSize",16);
			LODOP.SET_PRINT_STYLEA(0,"Bold",1);

			LODOP.ADD_PRINT_TEXT(42,240,451,46,data.product_name);
			LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
			LODOP.SET_PRINT_STYLEA(0,"FontSize",20);
			LODOP.SET_PRINT_STYLEA(0,"Alignment",2);
			LODOP.SET_PRINT_STYLEA(0,"Bold",1);

			LODOP.ADD_PRINT_TEXT(130,370,280,30,data.price+"元/人");
			LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
			LODOP.SET_PRINT_STYLEA(0,"FontSize",16);
			LODOP.SET_PRINT_STYLEA(0,"Bold",1);

			LODOP.ADD_PRINT_TEXT(91,370,380,30,data.plantime);
			LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
			LODOP.SET_PRINT_STYLEA(0,"FontSize",16);
			LODOP.SET_PRINT_STYLEA(0,"Bold",1);

			LODOP.ADD_PRINT_TEXT(166,370,138,30,data.number);
			LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
			LODOP.SET_PRINT_STYLEA(0,"FontSize",16);
			LODOP.SET_PRINT_STYLEA(0,"Bold",1);
			
			LODOP.ADD_PRINT_BARCODE(118,620,110,110,"QRCode",data.sn);
			LODOP.SET_PRINT_STYLEA(0,"FontSize",127);
			LODOP.SET_PRINT_STYLEA(0,"ShowBarText",0);
			LODOP.SET_PRINT_STYLEA(0,"GroundColor","#FFFFFF");
			LODOP.SET_PRINT_STYLEA(0,"QRCodeErrorLevel","H");
			
			LODOP.ADD_PRINT_TEXT(42,773,125,20,data.plantime);
			
			LODOP.ADD_PRINT_TEXT(76,773,100,20,data.number+"人");
			LODOP.ADD_PRINT_TEXT(100,773,100,20,data.sns);

			LODOP.ADD_PRINT_TEXT(238,222,100,20,data.sns);
		}
}
</script>
</body>
</html>