$(document).ready(function () {
	toogle("operationQuery");
	initCitys();
	setTime();
	initActionType();
	initActionSource();
});

function initCitys() {
	$("#city").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "请选择城市",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有平台类型",
		maxHeight: 200,
		width: 220
	});
	var url = "operationQuery/getCitys";
	$.ajax({
		type: "GET",
		url: url,
		dataType: "json",
		success: function (data) {
			var newOptions = [];
			var obj = {};
			$(data).each(function (k, v) {
				v = eval("(" + v + ")");
				obj = {
					label: v.text,
					value: v.value
				};
				newOptions.push(obj);
			});
			$("#city").multiselect("dataprovider", newOptions);
		}
	});
}
// function getFormatDate(){
//	 var today=new Date();
//	 var year = today.getFullYear();
//	 var month = today.getMonth()+1;
//	 month = month > 9 ? month : "0"+month;
//	 var day = today.getDate();
//	 day = day > 9 ? day : "0"+day;
//	 var hour = today.getHours();
//	 hour = hour > 9 ? hour : "0"+hour;
//	 var minute = today.getMinutes();
//	 minute = minute > 9 ? minute : "0"+minute;
//	 var time = year+"-"+month+"-"+day+" "+hour+":"+minute;
// }
function setTime() {
	var today = new Date();
	var year = today.getFullYear();
	var month = today.getMonth() + 1;
	month = month > 9 ? month : "0" + month;
	var day = today.getDate();
	day = day > 9 ? day : "0" + day;
	var dayS = today.getDate() - 1;
	dayS = dayS > 9 ? dayS : "0" + dayS;
	var hour = today.getHours();
	hour = hour > 9 ? hour : "0" + hour;
	var minute = today.getMinutes();
	minute = minute > 9 ? minute : "0" + minute;
	var stime = year + "-" + month + "-" + dayS + " " + hour + ":" + minute;
	var etime = year + "-" + month + "-" + day + " " + hour + ":" + minute;
	// alert(time);
	$("#startTime").val(stime);
	$("#endTime").val(etime);
	$("#startTime").datetimepicker({
		format: "yyyy-mm-dd hh:ii",
		autoclose: true,
		todayBtn: true,
		startDate: "2013-02-14 10:00",
		minuteStep: 1,
	});
	$("#endTime").datetimepicker({
		format: "yyyy-mm-dd hh:ii",
		autoclose: true,
		todayBtn: true,
		startDate: "2013-02-14 10:00",
		minuteStep: 1
	});
}
function query(type){
	var startTime = $("#startTime").val();
	var endTime = $("#endTime").val();
	var city = $("#city").val();
	var action_type = $("#action_type").val();
	var site = $("#site").val();
	var param = $("#params").val();
	var action_source = $("#action_source").val();
	var params = {
		startTime: startTime,
		endTime: endTime,
		city: city,
		action_type: action_type,
		site: site,
		param: param,
		action_source : action_source
	};

	var queryBtn = Ladda.create( document.getElementById( "queryBtn" ) );
	var exportBtn = Ladda.create( document.getElementById( "exportBtn" ) );
	queryBtn.start();
	exportBtn.start();

	
	$.get("operationQuery/operationData", params, function(data) {
		
		var fieldArr=[];
		var text=data.content.split(",");
		// console.log(text);

		// var filename = JSON.parse(data).filename;
		// $("#operationFile").val(filename);
		/*if(worstCellType == "file"){
		  csvZipDownload(filename);
		}*/
		for (var i in data.rows[0]) {  
		  //console.log(JSON.parse(data).rows[0]);  
			if (i == "action_source" || i == "action_type" || i == "site") {
				fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:100};
			} else if (i == "mo") {
				fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:350};
			} else {
				fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:160};
			}
	  
		} 
		var filename = data.filename;
		if (type == "file") {
			fileZipSave(filename);
			queryBtn.stop();
			exportBtn.stop();
			return;
		}
		var newData = data.rows;
		// alert(newData);
		if (newData == "") {
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
		}

		$("#operationQueryTable").grid("destroy", true, true);
		var badCellTable = $("#operationQueryTable").grid({
			columns:fieldArr,
			dataSource:newData,
			pager: { limit: 10, sizes: [10, 20, 50, 100] },
			autoScroll:true,
			uiLibrary: "bootstrap",
			primaryKey:"id"
		});
	
		queryBtn.stop();
		exportBtn.stop();
	});
}

function fileZipSave(fileName) {
	if(fileName!="")
	{
		var fileNames = csvZipDownload(fileName);
		download(fileNames);
	}
	else
	{
		// alert("No file generated so far!");
		layer.open({
			title: "提示",
			content: "No file generated so far!"
		});
	}
}

function download(url) {
	var browerInfo = getBrowerInfo();
	if (browerInfo=="chrome") {
		download_chrome(url);
	} else if (browerInfo == "firefox") {
		download_firefox(url);
	}
}

function download_chrome(url) {
	var aLink = document.createElement("a");
	aLink.href=url;
	aLink.download = url;
	document.body.appendChild(aLink);
	aLink.click();
}

function download_firefox(url) {
	window.open(url);
}

function initActionType() {
	$("#action_type").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		nonSelectedText: "选择操作类型",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		// allSelectedText:"已全选",
		// sceneIdList:"SET",
		maxHeight: 200,
		maxWidth: "100%"
	});
	var url = "operationQuery/getActionType";
	var obj = {};
	var newOptions = [];
	$.get(url, null, function(data) {
		var sdata = [];
		for (var key in data) {
			obj = {
				label : data[key],
				value : data[key],
				selected : true
			};
		newOptions.push(obj);  
		}
		$("#action_type").multiselect("dataprovider", newOptions);		
	});
}

function initActionSource() {
	$("#action_source").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		nonSelectedText: "选择操作来源",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		// allSelectedText:"已全选",
		maxHeight: 200,
		maxWidth: "100%"
	});
	var url = "operationQuery/getActionSource";
	var obj = {};
	var newOptions = [];
	$.get(url, null, function(data) {
		var sdata = [];
		for (var key in data) {
			obj = {
				label : data[key],
				value : data[key],
				selected : true
			};
		newOptions.push(obj);  
		}
		$("#action_source").multiselect("dataprovider", newOptions);		
	});
}

function toName(self) {
	$.ajaxFileUpload({
		url : "operationQuery/uploadFile",   　
		//data : data,
		fileElementId : "fileImport",		   
		secureuri : false,						  
		dataType: "json",
		type: "post",					 
		success:function(data, status) { 
			$("#site").val(data);
		},
		error:function(data, status, e) {
			//alert("上传失败");
			layer.open({
				title: "提示",
				content: "上传失败"
			});
		}
	});
}

function openConfigInfo() {
	getparam();
}

function updateConfigInfo() {
	$("#config_information").modal("hide");
}

function getparam(type) {
	var startTime = $("#startTime").val();
	var endTime = $("#endTime").val();
	var city = $("#city").val();
	var action_type = $("#action_type").val();
	var site = $("#site").val();
	var param = $("#params").val();
	var action_source = $("#action_source").val();
	var params = {
		startTime : startTime,
		endTime : endTime,
		city : city,
		action_type : action_type,
		site : site,
		param : param,
		action_source : action_source
	};

	var queryBtn = Ladda.create( document.getElementById( "queryBtn_param" ) );
	var exportBtn = Ladda.create( document.getElementById( "exportBtn_param" ) );
	queryBtn.start();
	exportBtn.start();
	// alert("数据不存在，请重新选择！");
	$.get("operationQuery/paramData", params, function(data){
		var fieldArr = [];
		var text = data.content.split(",");

		for(var i in data.rows[0]) { 
			if (i == "id" || i == "date_id" || i == "hour_id") {
				continue;
			} else if (i == "action_source" || i == "action_type" || i == "site") {
			fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:100};
			} else if (i == "mo") {
				fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:350};
			} else {
				fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:160};
			}	 
		}
		var filename = data.filename;
		if (type == "file") {
			fileZipSave(filename);
			queryBtn.stop();
			exportBtn.stop();
			return;
		}
		var newData = data.rows;

		$("#paramTable").grid("destroy", true, true);
		var badCellTable = $("#paramTable").grid({
			columns:fieldArr,
			dataSource:newData,
			pager: { limit: 10, sizes: [10, 20, 50, 100] },
			autoScroll:true,
			uiLibrary: "bootstrap",
			primaryKey:"id"
		});
	
		queryBtn.stop();
		exportBtn.stop();
		$("#config_information").modal();
	});

}