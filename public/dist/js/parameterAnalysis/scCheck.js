$(document).ready(function () {
	toogle("scCheck");
	//setTree();
	getTasks();
	initCityList();
});
function setTree() {
	var data_4G = {
		text: "cityChinese",
		value: "connName"
	};
	$.get("scCheck/TreeQuery", data_4G, function (data) {
		var options = {
			bootstrap2: false,
			showTags: true,
			levels: 2,
			data: data,
			onNodeSelected: function (event, data) {
				$("#cityValue").val(data.value);
			}
		};
		$("#cityTree").treeview(options);
	});
}
var dateId = "#date";
function getTasks() {
	$(dateId).select2();
	var url = "scCheck/getTasks";
	$.ajax({
		type: "GET",
		url: url,
		dataType: "json",
		success: function (data) {
			var parameterAnalysisDateSelect = $(dateId).select2({
				height: 50,
				placeholder: "请选择日期",
				//allowClear: true,
				data: data
			});
			var task = getCurrentDate("kget");
			$(dateId).val(getCurrentDate("kget")).trigger("change");
			if ($(dateId).val() == null) {
				$(dateId).val(getYesterdayDate("kget")).trigger("change");
			}
			//doQuery();
		}
	});
}
function initCityList(){
	$("#cityList").multiselect({
		dropRight: true,
		buttonWidth: "230",
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
	var url = "scCheck/getCityList";
	$.ajax({
		type: "post",
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
			/*obj = {
				label: "未知",
				value: "unknow"
			};
			newOptions.push(obj);*/
			$("#cityList").multiselect("dataprovider", newOptions);
		}
	});
}
function getYesterdayDate(taskType) {
	var mydate = new Date();
	var yesterday_miliseconds = mydate.getTime() - 1000 * 60 * 60 * 24;
	var Yesterday = new Date();
	Yesterday.setTime(yesterday_miliseconds);

	var yesterday_year = Yesterday.getYear().toString().substring(1.3);
	var month_temp = Yesterday.getMonth() + 1;
	var yesterday_month = month_temp > 9 ? month_temp.toString() : "0" + month_temp.toString();
	var d = Yesterday.getDate();
	var Day = d > 9 ? d.toString() : "0" + d.toString();
	var kgetDate = taskType + yesterday_year + yesterday_month + Day;
	return kgetDate;
}

function getCurrentDate(taskType) {
	var mydate = new Date();
	var myyear = mydate.getYear();
	var myyearStr = (myyear + "").substring(1);
	var mymonth = mydate.getMonth() + 1; //值范围0-11
	mydate = mydate.getDate();  //值范围1-31
	var mymonthStr = "";
	var mydateStr = "";
	mymonthStr = mymonth >= 10 ? mymonth : "0" + mymonth;
	mydateStr = mydate >= 10 ? mydate : "0" + mydate;
	var kgetDate = taskType + myyearStr + mymonthStr + mydateStr;
	return kgetDate;
}
function importTemplate() {
	var city = $("#cityValue").val();
	if (city == "") {
		layer.open({
			title: "提示",
			content: "请选择城市"
		});
		return false;
	}
	;
	$("#import_modal").modal();
	$("#fileImportName").val("");
	$("#fileImport").val("");
}
function toName(self) {
	$("#fileImportName").val(self.value);
}
function importFile() {
	var params = getParam();
	if (params == false) {
		return false;
	}
	var E = Ladda.create(document.getElementById("importBtn"));
	E.start();
	$.ajaxFileUpload({
		url: "scCheck/uploadFile",
		data: params,
		fileElementId: "fileImport",
		secureuri: false,
		dataType: "json",
		type: "post",
		success: function (data, status) {
			params.fileName = data;
			$.post("scCheck/getFileContent", params, function (data) {
				E.stop();
				$("#import_modal").modal("hide");
				layer.open({
					title: "提示",
					content: "上传成功"
				});
			});

		},
		error: function (data, status, e) {
			layer.open({
				title: "提示",
				content: "上传失败"
			});
		}
	});
}
function getParam() {
	var importDate = getNowFormatDate();
	var city = $("#cityValue").val();
	if (city == "" || city == "city") {
		//alert("请选择城市！");
		layer.open({
			title: "提示",
			content: "请选择城市"
		});
		return false;
	}
	var data = {city: city, importDate: importDate};

	if ($("#fileImport").val() == "") {
		//alert("请选择上传的文件！");
		layer.open({
			title: "提示",
			content: "请选择上传的文件"
		});
		return false;
	}
	return data;
}
function exportTemplate() {
	var E = Ladda.create(document.getElementById("exportTemplate"));
	E.start();
	$.post("scCheck/downloadTemplateFile", null, function (data) {
		E.stop();
		if (data.result) {
			fileDownload(data.fileName);
		} else {
			layer.open({
				title: "提示",
				content: "下载失败"
			});
		}
	});
}
function run() {
	layer.confirm("核查时间大概需要二十分钟左右。确认运行吗？", {title: "提示"}, function (index) {
		layer.close(index);
		begintime = 1500;
		endFlag = "false";
		runProcedure();
	});
}
var sh = "";
//计时器
//var CallTimeLen = "0";
var begintime = 1500;
var endFlag = "false";
//var timer = null;

function DoConverseCallTimer()
{
	
	var minute="";
	var second="0";
	begintime = parseInt(begintime)-1;
	minute = parseInt(begintime/60);
	second = begintime%60;
	if (endFlag == "true") {
		layer.open({
				title: "提示",
				content: "已检查完，请查看"
			});
		clearInterval(sh);
		return false;
	}
	var content = "";
	if(minute=="0")
	{
		if (second == 0) {
		layer.open({
				title: "提示",
				content: "可能出现未知错误，请联系开发人员"
			});
			clearInterval(sh);
			return false;
		} else {
			content="剩余的时间为:"+second+"秒";
			$("#thzt").html("剩余的时间为:"+second+"秒");
			//timer1 = window.setTimeout("DoConverseCallTimer("true")",1000);
		}
	}
	else
	{
		content ="剩余的时间为:"+minute+"分"+second+"秒";
		$("#thzt").html("剩余的时间为:"+minute+"分"+second+"秒");
		//timer1 = window.setTimeout("DoConverseCallTimer("true")",1000);
	}
}
//计时器
function runProcedure() {
	var l = Ladda.create(document.getElementById("run"));
	l.start();
	var citys = $("#cityList").val();
	if (!citys) {
		layer.open({
			title: "提示",
			content: "请选择城市"
		});
		l.stop();
		return false;
	}
	var task = $("#date").val();
	var params = {
		citys:citys,
		task: task
	};
	layer.open({
		title: "提示",
		content: '<div id="thzt"></div>'
	});
	DoConverseCallTimer();
	sh = setInterval(DoConverseCallTimer,1000);
	$.post("scCheck/runProcedure", params, function (data) {
		if (data == "true") {
			begintime = 1;
			endFlag = "true";
			l.stop();
			doQuery();
			return false;
		}
	});
	
}


function doQuery() {
	var E = Ladda.create(document.getElementById("exportSystemConstantsCheck"));
	E.stop();
	var l = Ladda.create(document.getElementById("query"));
	l.start();
	$("#exportSystemConstantsCheck").attr("disabled", false);
	var citys = $("#cityList").val();
	var task = $("#date").val();
	var params = {
		citys:citys,
		task: task
	};

	var tableId = "#systemConstantsCheckTable";
	var fieldArr = [];
	$.post("scCheck/getTableField", params, function (data) {
		l.stop();
		$(tableId).grid("destroy", true, true);
		if (data.result == "error") {
			$("#exportSystemConstantsCheck").attr("disabled", true);
			layer.open({
				title: "提示",
				content: "没有记录"
			});
			return;
		} else {
			for (var k in data) {
				if (k == "systConstants" || k == "systConstantsR") {
					fieldArr[fieldArr.length] = {field: k, title: k, width: 600};
				} else {
					fieldArr[fieldArr.length] = {field: k, title: k, width: textWidth(k)};
				}
			}
			$(tableId).grid("destroy", true, true);
			$(tableId).grid({
				columns: fieldArr,
				dataSource: {url: "scCheck/getItems", type: "post", data: params},
				//primaryKey: "id",
				pager: {limit: 10, sizes: [10, 20, 50, 100]},
				autoScroll: true,
				uiLibrary: "bootstrap",
			});
		}
	});
}
function textWidth(text) {
	var length = text.length;
	if (length > 15) {
		return length * 10;
	}
	return 150;
}

function exportSystemConstantsCheck() {
	var E = Ladda.create(document.getElementById("exportSystemConstantsCheck"));
	E.start();
	downloadFile("TempSystemConstantsCheck", "SC分场景核查", E);
}
/*function exportOriginalConfig(){
 var E = Ladda.create( document.getElementById( "exportOriginalConfig" ) );
 E.start();
 downloadFile("TempscCheckOriginal","倒回配置",E);
 }*/

function downloadFile(table, type, E) {
	var citys = $("#cityList").val();
	var task = $("#date").val();
	var params = {
		citys: citys,
		task: task,
		table: table,
		type: type
	};
	$.post("scCheck/downloadFile", params, function (data) {
		E.stop();
		if (data.result) {
			fileDownload(data.fileName);
		} else {
			layer.open({
				title: "提示",
				content: "没有数据"
			});
		}
	});
}
//获取当前时间
function getNowFormatDate() {
	var date = new Date();
	var seperator1 = "-";
	var seperator2 = ":";
	var month = date.getMonth() + 1;
	var strDate = date.getDate();
	if (month >= 1 && month <= 9) {
		month = "0" + month;
	}
	if (strDate >= 0 && strDate <= 9) {
		strDate = "0" + strDate;
	}
	var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate + " " + date.getHours() + seperator2 + date.getMinutes() + seperator2 + date.getSeconds();
	return currentdate;
}