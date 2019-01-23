$(document).ready(function () {
	toogle("strongInterferenceCell");
	//setTree();
	getTasks();

});
var dateId = "#date";
function getTasks() {
	$(dateId).select2();
	var url = "strongInterferenceCell/getTasks";
	$.ajax({
		type: "post",
		url: url,
		dataType: "json",
		success: function (data) {
			var dateSelect = $(dateId).select2({
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
function toName(self) {
	$("#cellInput").val(self.value);
}
function searchInfo(){
	var S = Ladda.create(document.getElementById("search"));
	S.start();
	var task = $("#date").val();
	var fileName = $("#cellInput").val();
	if (fileName.indexOf(".") > 0) {
	$.ajaxFileUpload({
				url: "strongInterferenceCell/uploadFile",
				data: null,
				fileElementId: "fileImport",
				secureuri: false,
				dataType: "json",
				type: "post",
				success: function (data, status) {
					$.post("strongInterferenceCell/getFileContent", {fileName:data,task:task}, function (data) {
						if (data.result == "true") {
							$("#tableName").val(data.table);
							doQuery(S, data.table);
						}else{
							S.stop();
							layer.open({
								title: "提示",
								content: "上传失败"
							});
						}
					});

				},
				error: function (data, status, e) {
					S.stop();
					layer.open({
						title: "提示",
						content: "上传失败"
					});
				}
			});
	}else if (fileName != "") {
		$.post("strongInterferenceCell/insertCellList",{task:task, cellList:fileName}, function(data){
			if (data.result == "true") {
				$("#tableName").val(data.table);
				doQuery(S, data.table);
			}else{
				S.stop();
				layer.open({
					title: "提示",
					content: "查询失败"
				});
			}
		});
	} else {
		S.stop();
		layer.open({
			title: "提示",
			content: "请输入强干扰小区列表"
		});
		return;
	}
}
function doQuery(S, table){
	var task = $("#date").val();
	var params = {
		task : task,
		table : table
	};
	var tableId = "#strongInterferenceCellTable";
	var fieldArr = [];
	$.post("strongInterferenceCell/getTableField", params, function (data) {
		S.stop();
		$(tableId).grid("destroy", true, true);
		if (data.result == "error") {
			layer.open({
				title: "提示",
				content: "没有记录"
			});
			return;
		} else {
			for (var k in data) {
				fieldArr[fieldArr.length] = {field: k, title: k, width: textWidth(k)};
			}
			$(tableId).grid("destroy", true, true);
			$(tableId).grid({
				columns: fieldArr,
				dataSource: {url: "strongInterferenceCell/getItems", type: "post", data: params},
				//primaryKey: "id",
				pager: {limit: 10, sizes: [10, 20, 50, 100]},
				autoScroll: true,
				uiLibrary: "bootstrap",
			});
		}
	});
}
function textWidth(text){
	var length = text.length;
	if(length > 15){
		return length*10;
	}
	return 150;
}
function exportInfo(){
	var table = $("#tableName").val();
	var task = $("#date").val();
	var params = {
		task: task,
		table: table
	};
	var E = Ladda.create(document.getElementById("export"));
	E.start();
	$.post("strongInterferenceCell/downloadFile", params, function (data) {
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