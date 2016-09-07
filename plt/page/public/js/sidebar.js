

function updateSchool( areaId,selectObj )
{
	$.get("index.php?controller=user&action=getschool&area_id=" + areaId,null,function (data){				
		//var ctlObj = selectObj + " select";
		//alert(ctlObj);
		var ctlObj = $('#side_school_id');
		ctlObj.empty();		
		ctlObj.append('<option value="" selected>选择学校</option>');
		//生成学校数据
		for( var idx in data.schools )
		{
			ctlObj.append('<option value="' + data.schools[idx].id + '">'
							+  data.schools[idx].name + '</option>');
		}
	},"json");
}

function updateSideDorm( schoolId,selectObj )
{
	$.get("index.php?controller=user&action=getdorm&school_id=" + schoolId,null,function (data){				
		//var ctlObj = selectObj + " select";
		//alert(ctlObj);
		var ctlObj = $('#side_dorm_id_se');
		ctlObj.empty();		
		ctlObj.append('<option value="" selected>选择宿舍</option>');
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
	$('#side_school_id_se').bind('change',function(event){
		//alert("change");
		var schoolId = $(this).attr("value");	
		//alert("" + areaId );
		updateSideDorm( schoolId,this );
		$('#side_school_id').attr("value",schoolId);	
		$('#side_dorm_id').attr("value","");
		
		//alert($('#school_id').attr("value"));
		//alert($('#dorm_id').attr("value"));
	}
	);	
	//宿舍
	$('#side_dorm_id_se').bind('change',function(event){
		//alert("change");
		var dormId = $(this).attr("value");	
		//alert("" + areaId );
		//updateDorm( schoolId,this );
		$('#side_dorm_id').attr("value",dormId);	
	}
	);
	//校验值
	$('#side_form').submit(function(){
		//alert("change");
		var dormId = $("#side_dorm_id").attr("value");	
		var room = $("#side_room").attr("value");	
		//判断是否有空
		if( "" == dormId )
		{
			alert("还没有选择宿舍楼哦。");
			return false;
		}
		if( "" == room )
		{
			alert("还没有填你的宿舍号哦。");
			return false;
		}
		if( !window.confirm("修改地址购物车将会被清空哦，确认要修改吗？") )
		{
			return false;
		}
		//alert("" + areaId );
		//updateDorm( schoolId,this );
		return true;
	}
	);
});

