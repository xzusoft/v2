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
<div class="pageContent">
  <div class="pageFormContent">
  <button type="button" style="width:200px; height:150px" onclick="printTicket({$data.sn},{$data.plan_id})">打印门票</button>
  </div>
</div>
</div>
<script>
var LODOP; //声明为全局变量  
function printTicket(sn,planid){
	$.ajax({
		type:'get',
		dataType : 'json',
		url:'index.php?g=Item&m=Order&a=printTicket&sn='+sn+'&plan_id='+planid+'&user={$data.user}',
		success:function(data){
			var selSeat = eval(data.info);/*返回的座位信息*/
			if(data.status == '1'){
				$.each(selSeat,function(){			
					/*打印设置部分*/
					CreateFullBill(this);
					/*设置连续打印*/
					LODOP.SET_PRINT_PAGESIZE(2,800,2350,"USER");
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
		LODOP.ADD_PRINT_TEXT(114,175,140,30,"区域/AREA");
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",16);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.ADD_PRINT_TEXT(198,175,140,30,"时间/TIME");
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",16);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.ADD_PRINT_TEXT(199,304,264,30,data.plantime);
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",16);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.ADD_PRINT_TEXT(50,155,316,46,"印象普陀实景演出");


		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",20);
		LODOP.SET_PRINT_STYLEA(0,"Alignment",2);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.ADD_PRINT_TEXT(114,304,147,30,data.area);


		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",16);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.ADD_PRINT_TEXT(171,175,140,30,"票价/PRICE");
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",16);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.ADD_PRINT_BARCODE(140,554,110,110,"QRCode",data.sn);
		LODOP.SET_PRINT_STYLEA(0,"FontSize",127);
		LODOP.SET_PRINT_STYLEA(0,"ShowBarText",0);
		LODOP.SET_PRINT_STYLEA(0,"GroundColor","#FFFFFF");
		LODOP.SET_PRINT_STYLEA(0,"QRCodeErrorLevel","H");
		LODOP.ADD_PRINT_TEXT(172,304,165,29,data.price+"元");
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",16);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.ADD_PRINT_TEXT(81,1,100,20,data.plantime);
		LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
		LODOP.SET_PRINT_STYLEA(0,"Alignment",3);
		LODOP.ADD_PRINT_TEXT(117,1,100,20,data.seat);
		LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
		LODOP.SET_PRINT_STYLEA(0,"Alignment",3);
		LODOP.ADD_PRINT_TEXT(244,515,100,20,data.sns);
		LODOP.ADD_PRINT_TEXT(143,175,140,30,"座位/SEAT");
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",16);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.ADD_PRINT_TEXT(144,304,140,30,data.seat);
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",16);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);
		LODOP.ADD_PRINT_TEXT(234,166,248,20,"剧场地址：朱家尖印象普陀大剧场");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",10);
		LODOP.ADD_PRINT_TEXT(83,223,179,30,"请提前半小时入场");
		LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
		LODOP.SET_PRINT_STYLEA(0,"FontSize",13);
		LODOP.SET_PRINT_STYLEA(0,"Alignment",2);
		LODOP.SET_PRINT_STYLEA(0,"Bold",1);



		/*打印备注*/
		var type = data.remark_type;
		switch(type){
			case '1':
				/*儿童票*/
				LODOP.ADD_PRINT_RECT(72,485,200,50,0,2);
				LODOP.ADD_PRINT_TEXT(102,491,196,23,"仅限身高1.2米至1.5米儿童使用");
				LODOP.SET_PRINT_STYLEA(0,"FontSize",10);
				LODOP.ADD_PRINT_TEXT(79,551,100,30,"儿童票");
				LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
				LODOP.SET_PRINT_STYLEA(0,"FontSize",14);
				LODOP.SET_PRINT_STYLEA(0,"Bold",1);
				break;
			case '2':
				/*接待票*/
				break;
			case '3':
				/*市民票*/
				LODOP.ADD_PRINT_RECT(72,485,180,50,0,2);
				LODOP.ADD_PRINT_TEXT(80,502,172,40,"市民优惠票");
				LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
				LODOP.SET_PRINT_STYLEA(0,"FontSize",20);
				LODOP.SET_PRINT_STYLEA(0,"Bold",1);
				break;
			case '4':
				/*特殊团队票*/
				$.each(data.remark,function(item,name){
					LODOP.ADD_PRINT_RECT(72,495,200,50,0,2);
					LODOP.ADD_PRINT_TEXT(105,504,196,23,"需同时持"+name+"市民身份证入场");
					LODOP.SET_PRINT_STYLEA(0,"FontSize",10);
					LODOP.ADD_PRINT_TEXT(80,514,158,30,name+"团队专用");
					LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
					LODOP.SET_PRINT_STYLEA(0,"FontSize",14);
					LODOP.SET_PRINT_STYLEA(0,"Bold",1);
				});
				break;
			case '5':
				/*景区联合售票*/
				var width_s = 431,
					height = 454;
				$.each(data.remark,function(item,name){
					LODOP.ADD_PRINT_RECT(73,width_s,60,68,0,2);
					LODOP.ADD_PRINT_ELLIPSE(77,height,13,13,0,1);
					LODOP.ADD_PRINT_TEXT(112,width_s,59,30,"打孔后无效\n3天内有效");
					LODOP.SET_PRINT_STYLEA(0,"FontSize",7);
					LODOP.SET_PRINT_STYLEA(0,"Alignment",2);
					LODOP.ADD_PRINT_TEXT(96,width_s,60,22,name);
					LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
					LODOP.SET_PRINT_STYLEA(0,"Alignment",2);
					LODOP.SET_PRINT_STYLEA(0,"Bold",1);
					width_s = width_s+62;
					height = height+62;
				});
				break;
			case '6':
				LODOP.ADD_PRINT_RECT(72,485,180,50,0,2);
				LODOP.ADD_PRINT_TEXT(87,495,172,40,"赠不肯去观音号夜游");
				LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
				LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
				LODOP.SET_PRINT_STYLEA(0,"Bold",1);
				break;
			case '7':
				LODOP.ADD_PRINT_RECT(72,485,170,50,0,2);
				LODOP.ADD_PRINT_TEXT(87,495,172,40,"赠观光巴士夜游");
				LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
				LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
				LODOP.SET_PRINT_STYLEA(0,"Bold",1);
				break;
			case '8':
				LODOP.ADD_PRINT_RECT(72,485,170,50,0,2);
				LODOP.ADD_PRINT_TEXT(87,495,172,40,"赠东海音乐节门票");
				LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
				LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
				LODOP.SET_PRINT_STYLEA(0,"Bold",1);
				break;
			case '9':
				LODOP.ADD_PRINT_RECT(72,485,170,50,0,2);
				LODOP.ADD_PRINT_TEXT(87,495,172,40,"小手牵大手活动票");
				LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
				LODOP.SET_PRINT_STYLEA(0,"FontSize",12);
				LODOP.SET_PRINT_STYLEA(0,"Bold",1);
				break;
			case '10':
				/*景区联合售票*/
				var width_s = 431,
					height = 454;
				$.each(data.remark,function(item,name){
					LODOP.ADD_PRINT_RECT(73,width_s,60,68,0,2);
					LODOP.ADD_PRINT_ELLIPSE(77,height,13,13,0,1);
					LODOP.ADD_PRINT_TEXT(112,width_s,59,30,"打孔后无效\n28、29天内有效");
					LODOP.SET_PRINT_STYLEA(0,"FontSize",7);
					LODOP.SET_PRINT_STYLEA(0,"Alignment",2);
					LODOP.ADD_PRINT_TEXT(96,width_s,60,22,name);
					LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
					LODOP.SET_PRINT_STYLEA(0,"Alignment",2);
					LODOP.SET_PRINT_STYLEA(0,"Bold",1);
					width_s = width_s+62;
					height = height+62;
				});
				break;
			case '11':
				/*景区联合售票*/
				var width_s = 431,
					height = 454;
				$.each(data.remark,function(item,name){
					LODOP.ADD_PRINT_RECT(73,width_s,60,68,0,2);
					LODOP.ADD_PRINT_ELLIPSE(77,height,13,13,0,1);
					LODOP.ADD_PRINT_TEXT(112,width_s,59,30,"打孔后无效\n29天内有效");
					LODOP.SET_PRINT_STYLEA(0,"FontSize",7);
					LODOP.SET_PRINT_STYLEA(0,"Alignment",2);
					LODOP.ADD_PRINT_TEXT(96,width_s,60,22,name);
					LODOP.SET_PRINT_STYLEA(0,"FontName","黑体");
					LODOP.SET_PRINT_STYLEA(0,"Alignment",2);
					LODOP.SET_PRINT_STYLEA(0,"Bold",1);
					width_s = width_s+62;
					height = height+62;
				});
				break;
				
		}
		LODOP.ADD_PRINT_TEXT(234,410,100,20,data.user);
}
</script>
</body>
</html>