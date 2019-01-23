$(document).ready(function () {
	toogle("GSMSlot");
	getData();
	initCityList();
});

function getData() {
	//获取时间
	var url = "GSMSlot/getData";
	$.ajax({
		type: "GET",
		url: url,
		//data:{ids:platform_type_ids},
		dataType: "json",
		success: function (data) {
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
	url = "GSMSlot/getCityList";
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
	$.get("GSMSlot/getGSMSlotDataHeader", params, function (data) {
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
		$("#GSMSlotTable").grid("destroy", true, true);
		var grid = $("#GSMSlotTable").grid({
			columns: fieldArr,
			params: params,
			dataSource: {
				url: "GSMSlot/getGSMSlotData",
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
	var url = "GSMSlot/getAllGSMSlotData";
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

