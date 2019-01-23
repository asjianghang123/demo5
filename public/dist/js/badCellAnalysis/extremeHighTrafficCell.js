$(document).ready(function () {
	getTasks();
	initCitys();
	toogle("extremeHighTrafficCell");
});
var dateId = "#date";
function getTasks() {
	$(dateId).select2();
	var url = "extremeHighTrafficCell/getTasks";
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

	var url = "extremeHighTrafficCell/getCitys";
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
			obj = {
				label: "未知",
				value: "unknow"
			};
			newOptions.push(obj);
			$("#citys").multiselect("dataprovider", newOptions);
		}
	});
}
function query() {
	queryDetial("#cellTable", "EUtranCellTDD_ETH", "#exportCellBtn", "极端高话务小区列表");
	queryDetial("#BaselineTable", "TempParaCheckBaselineCheck_ETH", "#exportBaselineBtn", "BaseLine核查列表");
	queryDetial("#SCTable", "TempSystemConstantsCheck_ETH", "#exportSCBtn", "SC分场景核查列表");
}
function queryDetial(tableId, table, exportId, type) {
	$(exportId).attr("disabled", false);
	var task = $("#date").val();
	var citys = $("#citys").val();
	var table = table;
	var cell = $("#cell").val();
	var params = {
		task: task,
		table: table,
		citys: citys,
		cell: cell
	};

	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	queryBtn.start();
	var tableId = tableId;
	var fieldArr = [];
	$.post("extremeHighTrafficCell/getTableField", params, function (data) {
		queryBtn.stop();
		$(tableId).grid("destroy", true, true);
		if (data.result == "error") {
			$(exportId).attr("disabled", true);
			layer.open({
				title: "提示",
				content: type + " 没有记录"
			});
			return;
		} else {
			for (var k in data) {
				if (k == "mo" || k == "systConstants" || k == "systConstantsR") {
					fieldArr[fieldArr.length] = {field: k, title: k, width: 600};
				} else {
					fieldArr[fieldArr.length] = {field: k, title: k, width: textWidth(k)};
				}
			}
			$(tableId).grid("destroy", true, true);
			var grid = $(tableId).grid({
				columns: fieldArr,
				dataSource: {
					url: "extremeHighTrafficCell/getCellData",
					success: function (data) {
						//data = JSON.parse(data);
						grid.render(data);
					}
				},
				params: params,
				pager: {limit: 10, sizes: [10, 20, 50, 100]},
				autoScroll: true,
				uiLibrary: "bootstrap",
				primaryKey: "cell",
				autoLoad: true
			});

			grid.on("rowSelect", function (e, $row, id, record) {

				/*if(table == "file"){
				 filename = $("#badCellFile").val();
				 download(filename);
				 }*/
			});
		}
	});

}
function exportCellFile(type, table, exportId) {
	var task = $("#date").val();
	var citys = $("#citys").val();
	var table = table;
	var cell = $("#cell").val();
	var params = {
		task: task,
		table: table,
		citys: citys,
		cell: cell,
		type: type
	};

	var exportCellBtn = Ladda.create(document.getElementById(exportId));
	//exportCellBtn.start();

	var url = "extremeHighTrafficCell/getAllCellData";
	$.post(url, params, function (data) {
		exportCellBtn.stop();
		if (data.result) {
			fileDownload(data.fileName);
		} else {
			layer.open({
				title: "提示",
				content: "There is error occured!"
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