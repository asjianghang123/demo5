$(document).ready(function () {
	toogle("kgetpartMark");
	getData();
	getAllCity();

});

function getAllCity() {
	$("#allCity").multiselect({
		dropRight: true,
		buttonWidth: 228,
		//enableFiltering: true,
		nonSelectedText: "请选择城市",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有平台类型",
		maxHeight: 200,
		maxWidth: "100%"
	});
	var url = "kgetpartMark/getAllCity";
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
			$("#allCity").multiselect("dataprovider", newOptions);
		}
	});
}

function getData() {
	//获取时间
	var url = "kgetpartMark/getParamTasks";
	$.ajax({
		type: "GET",
		data: "type=kgetpart",
		url: url,
		dataType: "json",
		success: function (data) {
			var newOptions = [];
			var obj = {};
			if (data.length == 1 && data[0] == "login") {
				window.location.href = "login";
			}
			$(data).each(function (k, v) {
				//var v = eval("("+v+")");
				var i = 0;
				//var currentDate = getCurrentDate("bulkcm");
				obj = {
					id: v.text,
					text: v.text
				};
				newOptions.push(obj);
			});
			var paramQueryDateSelect = $("#date").select2({
				placeholder: "请选择日期",
				//allowClear: true,
				data: newOptions
			});
			//var value = $(paramQueryDateId).val();
			$("#date").val(getCurrentDate("kgetpart")).trigger("change");
		}
	});
}
function getCurrentDate(type) {
	var mydate = new Date();
	var myyear = mydate.getYear();
	var myyearStr = (myyear + "").substring(1);
	var mymonth = mydate.getMonth() + 1; //值范围0-11
	mydate = mydate.getDate();  //值范围1-31
	var mymonthStr = "";
	var mydateStr = "";
	mymonthStr = getMonOrDateStr(mymonth);
	mydateStr = getMonOrDateStr(mydate);
	var kgetDate;
	if (type == "bulkcm" || type == "kgetpart") {
		mydate = mydate - 1;
		mydateStr = getMonOrDateStr(mydate);
		kgetDate = type + myyearStr + mymonthStr + mydateStr;
	} else {
		kgetDate = "kget" + myyearStr + mymonthStr + mydateStr;
	}
	return kgetDate;
}
function getMonOrDateStr(monOrDate) {
	var monOrDateStr = "";
	if (monOrDate < 10) {
		monOrDateStr = monOrDateStr + "0" + monOrDate;
	} else {
		monOrDateStr = monOrDateStr + monOrDate;
	}
	return monOrDateStr;
}

function query() {
	var citys = $("#allCity").val();
	var date = $("#date").val();
	var params = {
		dataBase: date,
		citys: citys
	};

	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	queryBtn.start();
	exportBtn.start();

	$.get("kgetpartMark/getKgetpartMarkDataHeader", params, function (data) {
		if (data.flag == "error") {
			//alert("数据不存在，请重新选择！");
			layer.open({
				title: "提示",
				content: "数据不存在，留痕还未完成或者联系开发人员查看！"
			});
			queryBtn.stop();
			exportBtn.stop();
			return;
		}
		var fieldArr = [];
		for (var k in data) {
			if (fieldArr.length == 0) {
				fieldArr[fieldArr.length] = {field: k, title: k, hidden: true};
			} else {
				if (k == "parameterName") {
					fieldArr[fieldArr.length] = {field: k, title: k, width: 320};
				} else if (k == "DN") {
					fieldArr[fieldArr.length] = {field: k, title: k, width: 300};
				} else if (k == "parameter") {
					fieldArr[fieldArr.length] = {field: k, title: k, width: 300};
				}
				else {
					fieldArr[fieldArr.length] = {field: k, title: k, width: textWidth(k)};
				}
			}
		}
		$("#kgetpartMarkTable").grid("destroy", true, true);
		var grid = $("#kgetpartMarkTable").grid({
			columns: fieldArr,
			params: params,
			dataSource: {
				url: "kgetpartMark/getKgetpartMarkData",
				success: function (data) {
					data = eval("(" + data + ")");
					grid.render(data);

					queryBtn.stop();
					exportBtn.stop();
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

	var citys = $("#allCity").val();
	var date = $("#date").val();
	var params = {
		dataBase: date,
		citys: citys
	};

	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	queryBtn.start();
	exportBtn.start();
	var url = "kgetpartMark/getAllKgetpartMarkData";
	$.get(url, params, function (data) {
		data = eval("(" + data + ")");
		if (data.result) {
			fileDownload(data.fileName);
		} else {
			//alert("数据不存在，请重新选择！");
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
		}
		queryBtn.stop();
		exportBtn.stop();
	});
}

