var schoolArr = [];
var dormArr = [];
var tmpArr = [];

function updateSchool( areaId,selectObj )
{
	$.get("index.php?controller=user&action=getschool&area_id=" + areaId,null,function (data){				
		//var ctlObj = selectObj + " select";
		//alert(ctlObj);
		var ctlObj = $('#reg_school_id');
		ctlObj.empty();		
		ctlObj.append('<option value="" selected>-请选择学校-</option>');
		//生成学校数据
		for( var idx in data.schools )
		{
			ctlObj.append('<option value="' + data.schools[idx].id + '">'
							+  data.schools[idx].name + '</option>');
		}
	},"json");
}

function updateDorm( schoolId,selectObj )
{
	$.get("index.php?controller=user&action=getdorm&school_id=" + schoolId,null,function (data){				
		//var ctlObj = selectObj + " select";
		//alert(ctlObj);
		var ctlObj = $('#reg_dorm_id');
		ctlObj.empty();		
		ctlObj.append('<option value="" selected>-请选择宿舍-</option>');
		//生成宿舍数据
		for( var idx in data.dorms )
		{
			ctlObj.append('<option value="' + data.dorms[idx].id + '">'
							+  data.dorms[idx].name + '</option>');
		}
	},"json");
}


$(document).ready(function(){
	//绑定增加宿舍的学校选择
	$('#reg_area_id').bind('change',function(event){
		//alert("change");
		var areaId = $(this).attr("value");	
		//alert("" + areaId );
		updateSchool( areaId,this );	
		$('#area_id').attr("value",areaId);
		$('#school_id').attr("value","");	
		$('#dorm_id').attr("value","");
		
		//alert($('#school_id').attr("value"));
		//alert($('#dorm_id').attr("value"));
	}
	);
	//绑定增加宿舍的学校选择
	$('#reg_school_id').bind('change',function(event){
		//alert("change");
		var schoolId = $(this).attr("value");	
		//alert("" + areaId );
		updateDorm( schoolId,this );
		$('#school_id').attr("value",schoolId);	
		$('#dorm_id').attr("value","");
		
		//alert($('#school_id').attr("value"));
		//alert($('#dorm_id').attr("value"));
	}
	);	
	//宿舍
	$('#reg_dorm_id').bind('change',function(event){
		//alert("change");
		var dormId = $(this).attr("value");	
		//alert("" + areaId );
		//updateDorm( schoolId,this );
		$('#dorm_id').attr("value",dormId);	
	}
	);
});

//开始处理jquery事件
$(document).ready(function(){
	//$.formValidator.initConfig({formid:"regist",onerror:function(msg){alert(msg)}});
	$.formValidator.initConfig({formid:"regist",onerror:function(msg){alert(msg);}});
	$("#username").formValidator
	({onshow:"登录手机不能修改",
	onfocus:"手机号为11位数字，并且以13或者15开头",
	oncorrect:"OK"}).inputValidator
	({min:11,max:11,onerror:"你输入的手机长度不对,请确认"}).regexValidator
	({regexp:"mobile",
	datatype:"enum",onerror:"你输入的手机号码格式不正确"});
	
	$("#name").formValidator
	({onshow:"请输入您的名字，方便我们联系您",
	onfocus:"名字不能为空",
	oncorrect:"OK"}).inputValidator({min:1,onerror:"名字不能为空哦,请确认"});

	$("#password").formValidator
	({onshow:"如要修改密码，请输入新密码，否则留空",
	onfocus:"密码不能为空",
	oncorrect:"如要修改密码，请输入新密码，否则留空"}).inputValidator({min:0,onerror:"密码不能为空,请确认"});

	$("#password2").formValidator
	({onshow:"如要修改密码，请重复输入新密码，否则留空",
	onfocus:"两次密码必须一致哦",
	oncorrect:"如要修改密码，请重复输入新密码，否则留空"}).inputValidator
	({min:0,onerror:"重复密码不能为空,请确认"}).compareValidator
	({desid:"password",
	operateor:"=",onerror:"2次密码不一致,请确认"});	
	
	$("input:radio[name='sexy']").formValidator().inputValidator({min:1,max:1,onerror:"请选择您的性别"});
	
	$("#area_id").formValidator
	({oncorrect:"OK"}).inputValidator({min:1,onerror:"还没有选择地区哦，请确认"});
	
	$("#school_id").formValidator
	({oncorrect:"OK"}).inputValidator({min:1,onerror:"还没有选择学校哦，请确认"});
	
	$("#dorm_id").formValidator
	({oncorrect:"OK"}).inputValidator({min:1,onerror:"还没有选择宿舍哦，请确认"});	
	
	$("#room").formValidator
	({onshow:"请输入你所在的房号，例如:501",
	onfocus:"房号不能为空",
	oncorrect:"OK"}).inputValidator({min:1,onerror:"房号不能为空,请确认"});	
	
	/* Ajax过渡效果 */
	$("#loading").ajaxStart(function(){
		var offset= $("#username").offset();
	
	
		$("#loading").css({
		"left": offset.left+160,
		"top": offset.top+80
	
		});
		$("#loading").show();
	
	});
	
	$("#loading").ajaxStop(function(){
		$("#loading").fadeOut(1000);
	
	});

});