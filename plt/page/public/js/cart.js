/*
封装购物车处理、页面js
@author allenhuang
@date 2010-3-28
*/
/////////////////////////////////////////////////////////////////////////////////////
//购物车封装
//添加到购物车
var isLoading = false;
var showHelp = false;

function addCart( id )
{
	if( isLoading )
	{
		return;
	}
	//alert("add");
	$.post("index.php?controller=cart&action=add",{"menu_id":id},function (data, textStatus){				
		//alert(data);
		alert(data.msg);		
		if( 0 != data.code )
		{
			location.reload(true);
			return;
		}
		genCartPage(data);		
	},"json");	
	
	return;
}
//更新购物车
function updateCart( id,newNum,oldNum )
{	
	if( isLoading )
	{
		return;
	}
	
	if( newNum == oldNum )
	{
		return;
	}	
	$.post("index.php?controller=cart&action=update",{"menu_id":id,"num":newNum},function (data, textStatus){				
		alert(data.msg);
		if( 0 != data.code )
		{
			location.reload(true);
			return;
		}
		genCartPage(data);		
	},"json");	
	
	return;
}

function balanceCart()
{
	if( isLoading )
	{
		return;
	}
	//获取积分
	$point = $("#usepoint").attr("value")
	//alert("balance");
	$.post("index.php?controller=cart&action=balance",{"menu_id":0,"usepoint":$point},function (data, textStatus){				
		alert(data.msg);	
		location.reload(true);	
	},"json");	
}

function removeCart()
{
	//清空购物车
	$("tr[id^='menu_']").each(function(){		
		$(this).remove();
	}	
	);
	//隐藏结算按钮
	$("#userpoint").hide();
	$("#balance").hide();
	$("#total_price").hide();		
	$("#notchoose").show();		
}
//生成、更新购物车爷们
function genCartPage( data )
{
	if( 0 == data.totalcnt )
	{
		location.reload(true);
	}
	//更新购物车所有菜单
	for( var order in data.cart )
	{
		if( $("#menu_" + data.cart[order].id).html() != null )
		{
			var oldNum = $("#menu_" + data.cart[order].id + " input").next().attr("value");
			if( oldNum != data.cart[order].num )
			{
				$("#menu_" + data.cart[order].id + " input").next().attr("value",data.cart[order].num);
				$("#menu_" + data.cart[order].id + " input").attr('value',data.cart[order].num);
				$("#menu_" + data.cart[order].id + " :nth-child(5)").html("<b>"+data.cart[order].tprice+"</b>");
			}
						
		}else
		{
			var showOrder = "";
			showOrder = "<TR id='menu_"+data.cart[order].id+"'>";
			showOrder += "<TD><B>"+data.cart[order].name+"</B> </TD>";
			showOrder += "<TD><B>"+data.cart[order].price+"</B> </TD>";
			showOrder += "<TD><input size='2' class='updatecart' value='"+data.cart[order].num+"' alt='"+data.cart[order].id+"'><input type='hidden' value='"+data.cart[order].num+"'> </TD>\n";
			//showOrder += "<TD><input size='2' class='updatcart' value='"+data.cart[order].num+"'> </TD>";
			showOrder += "<TD><a href='#' class='clearcart' alt='"+data.cart[order].id+"'>删除</a> </TD>";
			showOrder += "<TD><B>"+data.cart[order].tprice+"</B> </TD>";
			showOrder += "</TR>";
			//alert(showOrder);
			$("#buy_cart").append(showOrder);
		}	

	}
	//构建总价
	//var totalPrice = "一共 <b>"+data.totalprice+"</b>  元";		
	var totalPrice = "您点了 <b>"+data.totalcnt+"</b> 份餐，共 <b>"+data.totalprice+"</b>  元";			
	$("#total_price").html(totalPrice);
	$("#total_price").show();
	$("#balance").show();	
	$("#userpoint").show();
	$("#notchoose").hide();		
	if( false == showHelp )
	{
		for( var order in data.cart )
		{			
			var modifyNotice = "完成点餐后，要按‘确认下单’才会下单哦。你还可以直接编辑“数量”更新订单，修改数字后按回车键确认即可。";
			var modifyTitle = "您知道吗？";
			showHelp = true;
			showTips("#menu_" + data.cart[order].id,modifyTitle,modifyNotice);
			break;
		}
		/*
		if( 0 != data.cart.length )
		{
			var modifyNotice = "可以直接编辑“数量”更新订单，修改数字后按回车键确认即可。";
			var modifyTitle = "您知道吗？";
			showHelp = true;
			showTips("#menu_" + data.cart[0].id,modifyTitle,modifyNotice);
		}
		*/			
	}
	//删除空的菜单
	$("tr[id^='menu_']").each(function(){		
		var find = false;
		for( var order in data.cart )
		{
			if( data.cart[order].id == $(this).find("input").attr("alt") )
			{
				find = true;
				break;
			}
		}
		if( false == find )
		{
			$(this).remove();
		}
	}	
	);
	//生成元素之后再绑定响应事件
	$('.updatecart').bind('blur',function(event){
		//alert("blur");
		var id = $(this).attr("alt");
		var newNum = $(this).attr("value");
		var oldNum = $(this).next().attr("value");
		updateCart(id,newNum,oldNum);
	}	
	);
	$('.updatecart').bind('keypress',function(event){
		//alert(event.which);
		if( event.which != 13 )
		{
			return;
		}
		//alert("null");
		var id = $(this).attr("alt");
		var newNum = $(this).attr("value");
		var oldNum = $(this).next().attr("value");
		updateCart(id,newNum,oldNum);
		//alert("id:"+id+" newNum:"+newNum+" oldNum:"+oldNum);
	}	
	);
	//绑定删除事件
	$('.clearcart').bind('click',function(event){
		//alert("clear");
		var id = $(this).attr("alt");
		var newNum = 0;
		var oldNum = $(this).next().attr("value");
		updateCart(id,newNum,oldNum);
	}	
	);
}

function showTips( posContainer,helpTitle,helpContent )
{
		$("#tip").hide();
		var offset= $(posContainer).offset();	
		$("#tip").css({
		"left": offset.left - 75,
		"top": offset.top	+ 15
		});
		
		$("#tipstitle").empty();
		$("#tipstitle").html(helpTitle);
		$("#tipscontent").empty();
		$("#tipscontent").html(helpContent);
		$("#tip").show( 400 );	
}
//购物车结束
/////////////////////////////////////////////////////////////////////////////////////
//开始处理“我的订单
function genMyList()
{
	//生成表单头
	$("#confirm_table").empty();
	$("#confirm_table").append('<tr><td bgcolor="#EAEAEA">单号</td><td bgcolor="#EAEAEA">份数</td> <td bgcolor="#EAEAEA">下单时间</td><td bgcolor="#EAEAEA">合计</td><td bgcolor="#EAEAEA">操作</td><td bgcolor="#EAEAEA">投诉</td></tr>');
	//开始请求后台数据
	$.post("index.php?controller=order&action=show",null,function (data, textStatus){			
		if( 0 != data.code )
		{
			alert(data.msg);
			//location.reload();
			return;
		}
		//生成订单列表
		for( var idx in data.orders )
		{
			$("#confirm_table").append('<tr><td>' +
					data.orders[idx].no +
					'</td><td>' +
					data.orders[idx].num +
					'</td> <td>' +
					data.orders[idx].time +
					'</td><td>' +
					data.orders[idx].price +
					'</td><td><a href="#" class="remind_order" alt="' +data.orders[idx].no +'">催单</a></td>' +
					'<td><a target="_blank" href="index.php?controller=order&action=complain&order_no=' + data.orders[idx].no + '" alt="' +data.orders[idx].no +'">投诉</a>'+
					'</td></tr>');
		}
		//侦听事件
		$(".remind_order").bind('click',function(event){
						var id = $(this).attr("alt");	
						remindOrder(id);	
					}
					);	
		//显示订单
		$("#thedayorders").show(400);
	},"json");	
}

function remindOrder( id )
{
	//alert("index.php?controller=order&action=remind&order_no=" + id);
	$.get("index.php?controller=order&action=remind&order_no=" + id,null,function (data){				
		alert(data.msg);
	},"json");
	
}
//我的订单结束
/////////////////////////////////////////////////////////////////////////////////////
//显示公告栏
function showBoard()
{
	$("#tip").hide();
	$.get("index.php?controller=board&action=index",null,function (data){				
		//alert(data.msg);
		if( 0 == data.code )
		{
			var offset= $("#main_content :nth-child(2)").offset();	
			$("#tip").css({
			"left": offset.left+160,
			"top": offset.top - 80	
			});
		
			$("#tipstitle").empty();
			$("#tipstitle").html("送否公告");
			$("#tipscontent").empty();
			var cnt = 1;
			for( var idx in data.boards )
			{				
				$("#tipscontent").append(cnt + ":" + data.boards[idx].content + "<br>");
				cnt++;
			}
			$("#tip").show();
		}
	},"json");
}
///////////////////////////////////////////////////////////////////////////////////////
//显示积分帮助
function aboutPoint()
{
	var title = "送否积分有什么用？";
	var content = "送否回馈用户，现网站点餐可以积分啦！<br>点餐时每100分即可折扣1元餐费(限网站下单使用)，每份餐限用100分。<br>如果您使用的积分超过限制，送否将会为您自动折扣最大能使用的积分。<br>点餐时每1元积1分。";
	showTips("#userpoint",title,content);	
}
///////////////////////////////////////////////////////////////////////////////////////
//cookies操作
jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') {
        options = options || {};
        if (value === null) {
            value = '';
            options = $.extend({}, options);
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString();
        }
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else {
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};
/////////////////////////////////////////////////////////////////////////////////////
//开始绑定事件
$(document).ready(function(){
	//alert
	//alert($.cookie('mobile'));
	//显示公告
	showBoard();
	//关闭公告 closeTips
	$('#closeTips').bind('click',function(event){
		$("#tip").fadeOut(1000);		
	}
	);	
	//查看订单
	$('#showmyorder').bind('click',function(event){
		var offset= $("#showmyorder").offset();	
		$("#thedayorders").css({
			left: offset.left - 183,
			top: offset.top + 25
		});
		genMyList();		
	}
	);	
	//查看积分帮助
	$('#aboutpoint').bind('click',function(event){
		//alert("about");
		aboutPoint();	
	}
	);	
	//关闭查看订单
	$('#closeorders').bind('click',function(event){		
		$("#thedayorders").fadeOut(1000);
	}
	);	
	//处理添加到购物车
	$('.addtocart').bind('click',function(event){
		var id = $(this).attr("id");	
		if( (id>=2) && (id<=10))
		{
			if( 0 == confirm("该菜是现煮汤类要稍久一点才能送到哦，不提供30分钟超时免费服务的，你确定要点吗？") )
			{
				return;
			}
		}	
		addCart( id );		
	}
	);
	//处理更新菜单数量
	
	$('.updatecart').bind('blur',function(event){
		var id = $(this).attr("alt");
		var newNum = $(this).attr("value");
		var oldNum = $(this).next().attr("value");
		updateCart(id,newNum,oldNum);
	}	
	);
	
	$('.updatecart').bind('keypress',function(event){
		//alert(event.which);
		//updateCart( id,newNum,oldNum,event,type )
		if( event.which != 13 )
		{
			return;
		}
		//alert("null");
		var id = $(this).attr("alt");
		var newNum = $(this).attr("value");
		var oldNum = $(this).next().attr("value");
		updateCart(id,newNum,oldNum);
	}	
	);
	
	//绑定删除事件
	$('.clearcart').bind('click',function(event){
		//alert("clear");
		var id = $(this).attr("alt");
		var newNum = 0;
		var oldNum = $(this).next().attr("value");
		updateCart(id,newNum,oldNum);
	}	
	);	
	//绑定提交事件
	$('#balance').bind('click',function(event){
		//alert("clear");
		balanceCart();
	}	
	);	
	//ajax请求开始与结束处理
	//ajax超时设置
	$.ajaxSetup(
	{
		timeout:5 *1000
	}
	);
	
	//请求全局错误处理
	$("#loading").ajaxError(
		function(e, xhr, settings, exception) {
			alert('向后台请求：<' + settings.url + '>时出错。请重试。\n错误原因:' + exception);
			location.reload();	
		}
		);	
	/* Ajax过渡效果 */	
	$("#loading").ajaxStart(function(){
		//alert("start");
		isLoading = true;
		var offset= $("#main_content :nth-child(2)").offset();	
		$("#loading").css({
		"left": offset.left+160,
		"top": offset.top - 80	
		});
		
		$("#loading").show();	
	});
	
	$("#loading").ajaxStop(function(){
		isLoading = false;
		$("#loading").fadeOut(1000);
	});
	

});