$(document).ready(function () {
	toogle("RRU");
	getData();
	initCityList();
});

function getData() {
	//获取时间
	var url = "RRU/getData";
	$.ajax({
		type: "GET",
		url: url,
		//data:{ids:platform_type_ids},
		dataType: "json",
		success: function (data) {
			// var newOptions = [];
			// var obj = {};
			// if (data.length == 1 && data[0] == "login") {
			// 	window.location.href = "login";
			// }
			// $(data).each(function (k, v) {
			// 	v = eval("(" + v + ")");
			// 	var i = 0;
			// 	var currentDate = getCurrentDate();
			// 	obj = {
			// 		id: v.text,
			// 		text: v.text
			// 	};
			// 	newOptions.push(obj);
			// });
			var paramQueryDateSelect = $("#date").select2({
				placeholder: "请选择日期",
				//allowClear: true,
				data: data
			});
			$("#date").val(getCurrentDate()).trigger("change");
			var task = getCurrentDate("kget");
			$("#date").val(getCurrentDate("kget")).trigger("change");
			if ($("#date").val() == null) {
				$("#date").val(getYesterdayDate("kget")).trigger("change");
			}
			//query();
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
	url = "RRU/getCityList";
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

function query() {
	$("#exportBtn").attr("disabled",false);
	var date = $("#date").val();
	var citys = $("#cityList").val();
	var params = {
		dataBase: date,
		citys: citys
	};
	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	queryBtn.start();
	var fieldArr = [];
	$.get("RRU/getRRUDataHeader", params, function (data) {
		if (data.error == "error") {
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择"
			});
			$("#exportBtn").attr("disabled",true);
			queryBtn.stop();
			return;
		}
		var fieldArr = [];
		for (var k in data) {
			if (k == "id") {
				fieldArr[fieldArr.length] = {field: k, title: k, width: 50};
			} else {
				fieldArr[fieldArr.length] = {field: k, title: k, width: textWidth(k)};
			}
		}
		$("#RRUTable").grid("destroy", true, true);
		var grid = $("#RRUTable").grid({
			columns: fieldArr,
			params: params,
			dataSource: {
				url: "RRU/getRRUData",
				success: function (data) {
					data = eval("(" + data + ")");
					grid.render(data);

					queryBtn.stop();
				}
			},
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap"
		});

	});
}
function textWidth(text) {
	var length = text.length;
	if (length > 15) {
		return length * 10;
	}
	return 150;
}

function exportFile() {
	var date = $("#date").val();
	var citys = $("#cityList").val();
	var params = {
		dataBase: date,
		citys: citys
	};
	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	exportBtn.start();
	var url = "RRU/getAllRRUData";
	$.get(url, params, function (data) {
		if (data.result) {
			fileDownload(data.fileName);
		} else {
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择"
			});
		}
		exportBtn.stop();
	});
}
function run() {
	layer.confirm("核查时间大概需要二十分钟左右。确认运行吗？", {title: "提示"}, function (index) {
		layer.close(index);
		begintime = 2400;
		endFlag = "false";
		runProcedure();
	});
}
var sh = "";
//计时器
//var CallTimeLen = "0";
var begintime = 2400;
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
	$.post("RRU/runProcedure", params, function (data) {
		if (data == "true") {
			begintime = 1;
			endFlag = "true";
			l.stop();
			query();
			return false;
		}
	});
	
}


