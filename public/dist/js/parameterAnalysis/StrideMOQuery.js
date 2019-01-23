$(document).ready(function () {
	toogle("StrideMOQuery");
	getData();
});

function getData() {
	//获取时间
	var url = "StrideMOQuery/getData";
	$.ajax({
		type: "GET",
		url: url,
		dataType: "json",
		success: function (data) {
			var paramQueryDateSelect = $("#date").select2({
				placeholder: "请选择日期",
				//allowClear: true,
				data: data
			});
			$("#date").val(getCurrentDate()).trigger("change");
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
	mydate = mydate - 1;
	mymonthStr = mymonth >= 10 ? mymonth : "0" + mymonth;
	mydateStr = mydate >= 10 ? mydate : "0" + mydate;
	var kgetDate;
	if (type == "bulkcm" || type == "kgetpart") {
		kgetDate = type + myyearStr + mymonthStr + mydateStr;
	} else {
		kgetDate = "kget" + myyearStr + mymonthStr + mydateStr;
	}
	return kgetDate;
}

// function importBox(){
// 	$("#import_modal").modal();
// 	$("#siteSign").val("");
// 	$("#fileImportName").val("");
// 	$("#fileImport").val("");
// }

function toName(self) {
	$("#cellInput").val(self.value);
}
function query() {
	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	queryBtn.start();
	exportBtn.start();
	var date = $("#date").val();
	var cellInput = $("#cellInput").val();
	data = {date: date};
	if (cellInput.indexOf(".") > 0) {
		$.ajaxFileUpload({
			//url : "StrideMOQuery/uploadFile",
			url: "StrideMOQuery/uploadFile",
			data: data,
			fileElementId: "fileImport",
			secureuri: false,
			dataType: "json",
			type: "post",
			success: function (data, status) {
				$.post("StrideMOQuery/getFileContent", {"fileName": data, "date": date}, function (data) {
					if (data) querydata();
					//else alert("文件解析失败");
					else {
						layer.open({
							title: "提示",
							content: "文件解析失败"
						});
					}
					queryBtn.stop();
					exportBtn.stop();
				});
			},
			error: function (data, status, e) {
				//alert("文件上传失败");
				layer.open({
					title: "提示",
					content: "文件上传失败"
				});
			}
		});
	} else {
		params = {
			date: date,
			cellInput: cellInput
		};
		$.get("StrideMOQuery/insertFile", params, function (data) {
			querydata();
			queryBtn.stop();
			exportBtn.stop();
		});
	}
}

function querydata() {
	var dataBase = $("#date").val();
	params = {
		dataBase: dataBase
	};

	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	queryBtn.start();
	exportBtn.start();

	$.get("StrideMOQuery/StrideMOQueryDataHeader", params, function (data) {
		if (data.error == "error") {
			//alert("数据不存在，请重新选择！");
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择"
			});
			queryBtn.stop();
			exportBtn.stop();
			return;
		}
		var fieldArr = [];
		for (var k in data) {
			if (k == "mo") {
				fieldArr[fieldArr.length] = {field: k, title: k, width: 800};
			} else {
				fieldArr[fieldArr.length] = {field: k, title: k, width: textWidth(k)};
			}
		}
		$("#StrideMOQueryTable").grid("destroy", true, true);
		var grid = $("#StrideMOQueryTable").grid({
			columns: fieldArr,
			params: params,
			dataSource: {
				url: "StrideMOQuery/StrideMOQueryData",
				success: function (data) {
					data = eval("(" + data + ")");
					if (data.error == "error") {
						$("#StrideMOQueryTable").grid("destroy", true, true);
						//alert("数据不存在，请重新选择！");
						layer.open({
							title: "提示",
							content: "数据不存在，请重新选择"
						});
						queryBtn.stop();
						exportBtn.stop();
						return;
					}
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
function filetoExport(table) {
	var dataBase = $("#date").val();
	params = {
		dataBase: dataBase
	};
	$.get("StrideMOQuery/downloadFile", params, function (data) {
		data = eval("(" + data + ")");
		if (data.result == "true") {
			fileDownload(data.fileName);
		} else {
			//alert("There is error occured!");
			layer.open({
				title: "提示",
				content: "下载失败"
			});
		}
	});
}

function openConfigInfo() {
	$("#config_information").modal();

	getparam();
}

function updateConfigInfo() {
	$("#config_information").modal("hide");
}

function getparam() {
	params = {};

	// var queryParam = Ladda.create( document.getElementById( "queryParam" ) );
	var queryBtn = Ladda.create(document.getElementById("queryBtn_param"));
	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	// queryParam.start();
	queryBtn.start();
	exportBtn.start();
	// alert("数据不存在，请重新选择！");
	$.get("StrideMOQuery/paramDataHeader", params, function (data) {
		if (data.error == "error") {
			//alert("数据不存在，请重新选择！");
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择"
			});
			// queryParam.stop();
			queryBtn.stop();
			exportBtn.stop();
			return;
		}
		var fieldArr = [];
		for (var k in data) {
			if (k == "mo") {
				fieldArr[fieldArr.length] = {field: k, title: k, width: 150};
			}
			else {
				fieldArr[fieldArr.length] = {field: k, title: k, width: 100};
			}
		}
		$("#paramTable").grid("destroy", true, true);
		var grid = $("#paramTable").grid({
			columns: fieldArr,
			params: params,
			dataSource: {
				url: "StrideMOQuery/paramData",
				success: function (data) {
					data = eval("(" + data + ")");
					if (data.error == "error") {
						$("#paramTable").grid("destroy", true, true);
						//alert("数据不存在，请重新选择！");
						layer.open({
							title: "提示",
							content: "数据不存在，请重新选择"
						});
						// queryParam.stop();
						queryBtn.stop();
						exportBtn.stop();
						return;
					}
					grid.render(data);

					// queryParam.stop();
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
