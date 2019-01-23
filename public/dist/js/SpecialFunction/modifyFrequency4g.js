$(document).ready(function () {
	toogle("siteManage");
	setTree();
	getTasks();

});
function setTree() {
	var data_4G = {
		table: "databaseconn",
		text: "cityChinese",
		value: "connName"
	};
	$.get("modifyFrequency4g/TreeQuery", data_4G, function (data) {
		var options = {
			bootstrap2: false,
			showTags: true,
			levels: 2,
			data: data,
			onNodeSelected: function (event, data) {
				$("#cityValue").val(data.value);
				/*var type = $("#siteType").val();
				 if (type == "siteManage") doQuery4G(data.value);
				 else doQuery2G(data.value);*/
			}
		};
		$("#cityTree").treeview(options);
	});
}
var dateId = "#date";
function getTasks() {
	$(dateId).select2();
	var url = "modifyFrequency4g/getTasks";
	$.ajax({
		type: "GET",
		url: url,
		dataType: "json",
		success: function (data) {
			if (data.length == 1 && data[0] == "login") {
				window.location.href = "login";
			}
			var newOptions = [];
			var obj = {};
			$(data).each(function (k, v) {
				v = eval("(" + v + ")");
				var i = 0;
				obj = {
					id: v.text,
					text: v.text
				};
				newOptions.push(obj);
			});
			var parameterAnalysisDateSelect = $(dateId).select2({
				height: 50,
				placeholder: "请选择日期",
				//allowClear: true,
				data: newOptions
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
function importTemplate() {
	var city = $("#cityValue").val();
	if (city == "") {
		layer.open({
			title: "提示",
			content: "请选择城市"
		});
		return false;
	}
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
	params.table = "modifyFrequencyTemplate";
	var E = Ladda.create(document.getElementById("importBtn"));
	E.start();
	$.ajaxFileUpload({
		url: "file/uploadFile",
		data: params,
		fileElementId: "fileImport",
		secureuri: false,
		dataType: "json",
		type: "post",
		success: function (data, status) {
			params.fileName = data;
			$.post("modifyFrequency4g/getFileContent", params, function (data) {
				E.stop();
				$("#import_modal").modal("hide");
				doQuery4G(params.city);
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
	var table = "modifyFrequencyTemplate";
	var city = $("#cityValue").val();
	if (city == "") {
		layer.open({
			title: "提示",
			content: "请选择城市"
		});
		return;
	}
	var params = {
		city: city,
		table: table
	};
	var E = Ladda.create(document.getElementById("exportTemplate"));
	//E.start();
	$.post("modifyFrequency4g/downloadTemplateFile", params, function (data) {
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
	var l = Ladda.create(document.getElementById("run"));
	l.start();
	var city = $("#cityValue").val();
	if (city == "" || city == "city") {
		//alert("请选择城市！");
		layer.open({
			title: "提示",
			content: "请选择城市"
		});
		return false;
	}
	var task = $("#date").val();
	var params = {
		city: city,
		task: task
	};
	$.post("modifyFrequency4g/runProcedure", params, function (data) {
		if (data == "true") {
			doQuery(l);
		}
	});
}
function doQuery(l) {
	var city = $("#cityValue").val();
	if (city == "" || city == "city") {
		//alert("请选择城市！");
		layer.open({
			title: "提示",
			content: "请选择城市"
		});
		return false;
	}
	var task = $("#date").val();
	var params = {
		city: city,
		task: task
	};

	var tableId = "#modifyFrequency4gTable";
	var fieldArr = [];
	$.post("modifyFrequency4g/getTableField", params, function (data) {
		l.stop();
		$(tableId).grid("destroy", true, true);
		if (data.result == "error") {
			$("#exportModifyFrequency").attr("disabled", true);
			$("#exportOriginalConfig").attr("disabled", true);
			layer.open({
				title: "提示",
				content: "没有记录"
			});
			return;
		} else {
			for (var k in data) {
				if (k == "mo" || k == "geranFreqGroupRef") {
					fieldArr[fieldArr.length] = {field: k, title: k, width: 600};
				} else if (k == "remark2" || k == "remark1") {
					fieldArr[fieldArr.length] = {field: k, title: k, width: 250};
				} else {
					fieldArr[fieldArr.length] = {field: k, title: k, width: textWidth(k)};
				}
			}
			$(tableId).grid("destroy", true, true);
			$(tableId).grid({
				columns: fieldArr,
				dataSource: {url: "modifyFrequency4g/getItems", type: "post", data: params},
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

function exportModifyFrequency() {
	var E = Ladda.create(document.getElementById("exportModifyFrequency"));
	E.start();
	downloadFile("TempModifyFrequency4GCompare", "4G翻频", E);
}
function exportOriginalConfig() {
	var E = Ladda.create(document.getElementById("exportOriginalConfig"));
	E.start();
	downloadFile("TempModifyFrequencyOriginal", "倒回配置", E);
}

function downloadFile(table, type, E) {
	var city = $("#cityValue").val();
	if (city == "" || city == "city") {
		layer.open({
			title: "提示",
			content: "请选择城市"
		});
		return false;
	}
	var task = $("#date").val();
	var params = {
		city: city,
		task: task,
		table: table,
		type: type
	};
	$.post("modifyFrequency4g/downloadFile", params, function (data) {
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