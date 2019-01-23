$(document).ready(function () {
	toogle("NBAlarmQuery");
	initCitys();
	setTime();

	$("#exportBtn").on("click", function () {
		fileSave("file");
	});

	$("select#NBAlarm").change(function(){
		var type =$("#NBAlarm").val();
		if(type =="NBCurrent"){
			$("#NBTime").hide();
			$("#NBAlarmManage").val("NBCurrent");
		}else if(type =="NBHistory"){
			$("#NBAlarmManage").val("NBHistory");
			$("#NBTime").show();
		}
	});


});

function initCitys() {
	$("#citys").multiselect({
		//dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "选择城市",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有城市",
		maxHeight: 200,
		maxWidth: "100%"
	});

	var url = "NBAlarmQuery/getCitys";
	$.get(url, null, function (data) {
		data = eval("(" + data + ")");
		$("#citys").multiselect("dataprovider", data);
	});
}
function query() {
	var citys  = $("#citys").val();
	var subnet = $("#SubNet").val();
	if (!citys) {
		// alert("");
		layer.open({
			title: "提示",
			content: "未选择城市无法查询！"
		});
		return;
	}


	var flag = $("#regionalDimension").prop("checked");
	var placeDim = flag ? "meContext" : "eutranCell";
	var areaName = $("#areaName").val();

	var NBType  = $("#NBAlarmManage").val();
	if(NBType=="NBCurrent"){

		var params = {
			placeDim: placeDim,
			placeDimName: areaName,
			city: citys.join(","),
			type:NBType,
			subnet:subnet
		};

		var text = "Event_time,city,subNetwork,cluster,siteType,siteNameChinese,alarmNameC,levelC,meContext,eutranCell,SP_text,Problem_text";

	}else if(NBType=="NBHistory"){
		var dateFrom = $("#startTime").val();
		var dateTo = $("#endTime").val();
		if (!dateFrom || !dateTo) {
			// alert("起始日期或结束日期不能为空！");
			layer.open({
				title: "提示",
				content: "起始日期或结束日期不能为空！"
			});
			return;
		}
		if (dateFrom > dateTo) {
			// alert("结束日期不能早于起始日期！");
			layer.open({
				title: "提示",
				content: "结束日期不能早于起始日期！"
			});
			return;
		}

		var text = "Event_time,city,subNetwork,cluster,siteType,siteNameChinese,alarmNameC,levelC,meContext,eutranCell,Cease_time,SP_text,Problem_text";
		var params = {
			placeDim: placeDim,
			placeDimName: areaName,
			city: citys.join(","),
			dateFrom: dateFrom,
			dateTo: dateTo,
			type:NBType,
			subnet:subnet

		};
	}



	$("#exportBtn").val("");	

	var fieldArr = [];
	
	var textArr = text.split(",");
	for (var i in textArr) {
		if (textArr[fieldArr.length] == "Event_time" || textArr[fieldArr.length] == "Cease_time") {
			fieldArr[fieldArr.length] = {field: textArr[fieldArr.length], title: textArr[fieldArr.length], width: 250};
		} else if (textArr[fieldArr.length] == "Problem_text") {
			fieldArr[fieldArr.length] = {field: textArr[fieldArr.length], title: textArr[fieldArr.length], width: 350};
		} else {
			fieldArr[fieldArr.length] = {field: textArr[fieldArr.length], title: textArr[fieldArr.length], width: 150};
		}

	}
	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	queryBtn.start();
	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	exportBtn.start();
	$("#NBAlarmTable").grid("destroy", true, true);
	var grid = $("#NBAlarmTable").grid({
		columns: fieldArr,
		dataSource: {
			url: "NBAlarmQuery/getTableData",
			success: function (data) {
				data = eval("(" + data + ")");
				grid.render(data);
				// if(data.filename){
				// 	$("#exportBtn").val(data.filename);				
				// }
				// console.log($("#exportBtn").val());
				queryBtn.stop();
				
			}
		},
		params: params,
		pager: {limit: 10, sizes: [10, 20, 50, 100]},
		autoScroll: true,
		uiLibrary: "bootstrap",
		primaryKey: "id",
		autoLoad: true
	});

	var url = "NBAlarmQuery/getAllTableData";
	$.post(url, params, function (data) {
		data = eval("(" + data + ")");
		if (data.result == "true") {
			// fileSave(data.filename);
			$("#exportBtn").val(data.filename);	
			exportBtn.stop();
		}
	});
}





function fileSave(table) {
	var fileName = $("#exportBtn").val();
	if (fileName != "") {
		//alert(fileName);
		layer.open({
			title: "提示",
			content: fileName,
			yes:function(index, layero){
				var fileNames = csvZipDownload(fileName);
				download(fileNames);
				layer.close(index);
			}
		});
		
	}
	else {
		//alert("No file generated so far!");
		layer.open({
			title: "提示",
			content: "下载失败"
		});
		return;
	}
	// $("#exportBtn").on("click", function () {
	// 	fileSave("file")
	// });
}

function download(url) {
	var browerInfo = getBrowerInfo();
	if (browerInfo == "chrome") {
		download_chrome(url);
	} else if (browerInfo == "firefox") {
		download_firefox(url);
	}
}

function download_chrome(url) {
	var aLink = document.createElement("a");
	aLink.href = url;
	aLink.download = url;
	/*var evt = document.createEvent("HTMLEvents");
	 evt.initEvent("click", false, false);
	 aLink.dispatchEvent(evt);*/
	document.body.appendChild(aLink);
	aLink.click();
}

function download_firefox(url) {
	window.open(url);
}
function getBrowerInfo() {
	var uerAgent = navigator.userAgent.toLowerCase();
	var format = /(msie|firefox|chrome|opera|version).*?([\d.]+)/;
	var matches = uerAgent.match(format);
	return matches[1].replace(/version/, "'safari");
}
function setTime() {
	$("#startTime").datepicker({format: "yyyy-mm-dd"});  //返回日期
	$("#endTime").datepicker({format: "yyyy-mm-dd"});
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;

	console.log(today);
	var params = {
		city: getFirstCity()
	};
	$.get("NBAlarmQuery/getNBAlarmTime", params, function (data) {
		var sdata = [];
		for (var i = 0; i < data.length; i++) {
			if (data[i] === today) {
				continue;
			}
			sdata.push(data[i]);
		}
		sdata.push(today);
		$("#startTime").datepicker("setValues", sdata);
		$("#endTime").datepicker("setValues", sdata);
	});
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#startTime").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");
	var checkout = $("#endTime").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkout.hide();
	}).data("datepicker");
}
//导入小区
function toName(self) {
	$.ajaxFileUpload({
		url: "NBAlarmQuery/uploadFile",
		//data : data,
		fileElementId: "fileImport",
		secureuri: false,
		dataType: "json",
		type: "post",
		success: function (data, status) {
			$("#areaName").val(data);
		},
		error: function (data, status, e) {
			// alert("上传失败");
			layer.open({
				title: "提示",
				content: "上传失败"
			});
		}
	});
}


