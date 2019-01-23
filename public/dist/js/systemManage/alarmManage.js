$(document).ready(function () {
	toogle("alarmManage");
	// 加载用户表
	doQueryNotice();
});

function doQueryNotice() {

	$.get("alarmManage/getAlarm", "", function (data) {
		var fieldArr = [];
		var text = (JSON.parse(data).text).split(",");
		for (var i in JSON.parse(data).rows[0]) {
			if (fieldArr.length == 0) {
				fieldArr[fieldArr.length] = {field: text[fieldArr.length], title: text[fieldArr.length], width: 50};
			} else if (text[fieldArr.length] == "content") {
				fieldArr[fieldArr.length] = {field: text[fieldArr.length], title: text[fieldArr.length], width: 450};
			} else {
				fieldArr[fieldArr.length] = {field: text[fieldArr.length], title: text[fieldArr.length], width: 150};
			}

		}
		var newData = JSON.parse(data).rows;
		$("#alarmTable").grid("destroy", true, true);
		$("#alarmTable").grid({
			columns: fieldArr,
			dataSource: newData,
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap",
			primaryKey: "id"
		});
	});
}

//文件导出
function exportAlarmManage() {
	$.get("alarmManage/downloadFile", function (data) {

		data = eval("(" + data + ")");
		if (data.result == "true") {
			var filepath = data.filename.replace("\\", "");
			download(filepath, "", "data:text/csv;charset=utf-8");
		} else {
			//alert("There is error occured!");
			layer.open({
				title: "提示",
				content: "下载失败"
			});
		}
	});
}

function importAlarmManage() {
	$("#import_modal").modal();
	$("#fileImportName").val("");
	$("#fileImport").val("");
}
function toName(self) {
	$("#fileImportName").val(self.value);
}
function importFile() {
	var data = {
		"filename": $("#fileImportName").val()
	};
	// console.log(data);

	$.ajaxFileUpload({
		url: "alarmManage/uploadFile",
		type: "POST",
		fileElementId: "fileImport",
		secureuri: false,
		dataType: "json",
		success: function (data) {
			if (data == "lenError") {
				layer.open({
					title: "提示",
					content: "没有告警数据或没有表头"
				});
			} else {
				doQueryNotice();
				$("#import_modal").modal("hide");

				layer.open({
					title: "提示",
					content: "上传成功"
				});
			}

		},
		error: function (data, status, e) {
			//alert("上传失败");
			layer.open({
				title: "提示",
				content: "上传失败"
			});
		}
	});
}

