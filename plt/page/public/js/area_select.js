
var schoolArr = [];
var dormArr = [];
var tmpArr = [];

function removeOptions(selectObj)
{
     if (typeof selectObj != 'object')
     {
          selectObj = document.getElementById(selectObj);
     }
     var len = selectObj.options.length;
     for (var i=0; i < len; i++)
     {
     		selectObj.options[0] = null;
     }
}
function setSelectOption(selectObj, optionList, firstOption, selected)
{
	if (typeof selectObj != 'object')
	{
		selectObj = document.getElementById(selectObj);
	}
	removeOptions(selectObj);
	 var start = 0;
	 if (firstOption)
	 {
	 	selectObj.options[0] = new Option(firstOption, '');
	 	start ++;
	 }
	 var len = optionList.length;
	 for (var i=0; i < len; i++)
	 {
		 selectObj.options[start] = new Option(optionList[i].txt, optionList[i].val);
		 if(selected == optionList[i].val)
		 {
		 	selectObj.options[start].selected = true;
		 }
		 start ++;
	 }
}				
function setSchool( school )
{
	if( "" == school )
	{		
		var tmpArr = [];
		setSelectOption('school_id', tmpArr, '-请选择学校-');
		setSelectOption('dorm_id', tmpArr, '-请选择宿舍楼-');
		//alert("null school");
	}else
	{
		setSelectOption('school_id', schoolArr[school], '-请选择学校-');
	}
}
function setDorm( dorm )
{
	if( "" == dorm )
	{
		var tmpArr = [];
		setSelectOption('dorm_id', tmpArr, '-请选择宿舍楼-');
	}else
	{
		setSelectOption('dorm_id', dormArr[dorm], '-请选择宿舍楼-');
	}
	
}
