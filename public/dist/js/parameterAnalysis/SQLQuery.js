var parameterAnalysisDateId = "#allCity";
$(document).ready(function () {
	//设置树
	setTree();
	$("#CustomQueryMoTree").treeview("collapseAll", {silent: true});
	//getAllCity();
	getDate();
	customDblClickFun();
	$("#inputName").css("display", "none");
	toogle("SQLQuery");
});

function getText(text) {
	var params = {
		treeData: text
	};
	$("#customName").val(text);
	$.get("SQLQuery/getKpiFormula", params, function (data) {
		$("#customContext").val(data);
		$("#edit_LTE").modal();
	});
}

function saveModeChange() {
	var templateId = $("#customName").val();
	var flag = $("#LTEFlag").val();
	var path = "";
	var content = $("#customContext").val();
	var data = {
		content: content,
		templateId: templateId,
		path: path
	};
	$.post("SQLQuery/saveModeChange", data, function (data) {
		$("#edit_LTE").modal("hide");
		layer.open({
			title: "提示",
			content: "保存成功"
		});
	});
}

function saveMode() {
	var templateName = $("#customName").val();
	var customContext = $("#customContext").val();
	var params = {
		templateName: templateName,
		customContext: customContext
	};
	$.get("SQLQuery/saveMode", params, function (data) {
		if (data == "success") {
			//alert("保存成功！");
			layer.open({
				title: "提示",
				content: "保存成功"
			});
		} else if (data == "login") {
			window.location.href = "login";
		} else {
			//alert("保存失败！");
			layer.open({
				title: "提示",
				content: "保存失败"
			});
		}
	});
}

function deleteMode(type) {
	var selectedNode = $("#CustomQueryMoTree").treeview("getSelected")[0];
	if (!selectedNode) {
		layer.open({
			title: "提示",
			content: "请选择要删除的模板"
		});
		return;
	}
	layer.confirm("确认删除吗？", {title: "提示"}, function (index) {
		var params = {
			id: selectedNode.value
		};
		$.get("SQLQuery/deleteMode", params, function (data) {
			setTree();
			customDblClickFun();
		});
		layer.close(index);
	});
}
function newBuild(type) {
	$("#inputName").modal();
	$("#insertName").val("");
}

function insertTable() {
	var insertName = $("#insertName").val();
	var params = {
		insertName: insertName
	};
	$.get("SQLQuery/insertMode", params, function (data) {
		if (data == "success") {
			setTree();
			customDblClickFun();
			//alert("插入成功！");
			layer.open({
				title: "提示",
				content: "插入成功"
			});
		} else if (data == "login") {
			window.location.href = "login";
		} else if (data == "wrong") {
			//alert("模板重名，请重新输入！");
			layer.open({
				title: "提示",
				content: "模板重名，请重新输入"
			});
		}
		$("#inputName").modal("hide");
	});
}
function getTable() {
	var l = Ladda.create(document.getElementById("run"));
	var S = Ladda.create(document.getElementById("export"));
	l.start();
	S.start();
	var dataBase = $("#allCity").val();
	var sql = $("#customContext").val();
	var templateId = $("#customName").val();
	if (!templateId) {
		l.stop();
		S.stop();
		layer.open({
			title: "提示",
			content: "请选择模板"
		});
		return false;
	}
	if (!dataBase) {
		l.stop();
		S.stop();
		layer.open({
			title: "提示",
			content: "请选择日期"
		});
		return false;
	}
	var params = {
		templateId: templateId,
		dataBase: dataBase,
		sql: sql
	};
	$.post("SQLQuery/getTableHeader", params, function (data) {
		var fieldArr = [];
		var text = (JSON.parse(data).text).split(",");
		for (var i in text) {
			fieldArr[fieldArr.length] = {field: text[i], title: text[i], width: 150};
		}
		$("#CustomQueryTable").grid("destroy", true, true);
		$("#CustomQueryTable").grid({
			columns: fieldArr,
			dataSource: {
				url: "SQLQuery/getTableData",
				type: "post",
				data: params
			},
			//primaryKey: "id",
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap",
		});
		l.stop();
		S.stop();
	});
}
function exportTable() {
	var l = Ladda.create(document.getElementById("run"));
	var S = Ladda.create(document.getElementById("export"));
	l.start();
	S.start();
	var dataBase = $("#allCity").val();
	var sql = $("#customContext").val();
	var templateId = $("#customName").val();
	if (!templateId) {
		l.stop();
		S.stop();
		layer.open({
			title: "提示",
			content: "请选择模板"
		});
		return false;
	}
	if (!dataBase) {
		l.stop();
		S.stop();
		layer.open({
			title: "提示",
			content: "请选择日期"
		});
		return false;
	}
	var params = {
		templateId: templateId,
		dataBase: dataBase,
		sql: sql
	};
	$.post("SQLQuery/getAllTableData", params, function (data) {
		fileDownload(data);
		l.stop();
		S.stop();
	});
}

function getNowFormatDate(date) {
	date = new Date(date);
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
function getAllCity() {
	$("#allCity").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "请选择日期",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有平台类型",
		maxHeight: 200,
		maxWidth: "100%"
	});
	var url = "SQLQuery/getAllCity";
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

function clearCustomQuery() {
	$("#paramQueryMoErbs").val("");
	setTree();
	customDblClickFun();
	$("#CustomQueryMoTree").treeview("collapseAll", {silent: true});
}

function searchCustomQuery() {
	var inputData = $("#paramQueryMoErbs").val();
	inputData = $.trim(inputData);
	if (inputData == "") {
		setTree();
		customDblClickFun();
		return;
	}
	var params = {
		inputData: inputData
	};
	$.get("SQLQuery/searchCustomTreeData", params, function (data) {
		// data = "[" + data + "]";
		var tree = "#CustomQueryMoTree";
		$(tree).treeview({data: data});
		customDblClickFun();
	});
}

function setTree() {
	var tree = "#CustomQueryMoTree";

	$(tree).treeview({
		data: getTree()
	}); //树
}
//最后一次触发节点Id
var lastSelectedNodeId = null;
//最后一次触发时间
var lastSelectTime = null;
//自定义业务方法
function customBusiness(data) {
//    alert("双击获得节点名字： "+data.text);
	var text = data.value;
	getText(text);
}
function clickNode(event, data) {
	if (lastSelectedNodeId && lastSelectTime) {
		var time = new Date().getTime();
		var t = time - lastSelectTime;
		if (lastSelectedNodeId == data.nodeId && t < 300) {
			customBusiness(data);
		}
	}
	lastSelectedNodeId = data.nodeId;
	lastSelectTime = new Date().getTime();
	if (!data.nodes) {
		$("#customName").val(data.value);
	}

}
//自定义双击事件
function customDblClickFun() {
	//节点选中时触发
	$("#CustomQueryMoTree").on("nodeSelected", function (event, data) {
		clickNode(event, data);
	});
	//节点取消选中时触发
	$("#CustomQueryMoTree").on("nodeUnselected", function (event, data) {
		clickNode(event, data);
		//searchCustomQuery();
	});
}

function getTree() {
	var url = "SQLQuery/getCustomTreeData";
	var treeData;
	$.ajax({
		type: "GET",
		url: url,
		dataType: "json",
		async: false,
		success: function (data) {
			treeData = data;
		}
	});
	return treeData;
}

function getDate() {
	$(parameterAnalysisDateId).select2();
	var url = "SQLQuery/getParamTasks";
	$.ajax({
		type: "post",
		url: url,
		dataType: "json",
		success: function (data) {
			if (data.length == 1 && data[0] == "login") {
				window.location.href = "login";
			}
			var newOptions = [];
			var obj = {};
			$(data).each(function (k, v) {
				//v = eval("(" + v + ")");
				var i = 0;
				obj = {
					id: v.text,
					text: v.text
				};
				newOptions.push(obj);
			});
			var parameterAnalysisDateSelect = $(parameterAnalysisDateId).select2({
				height: 50,
				placeholder: "请选择日期",
				//allowClear: true,
				data: newOptions
			});
			//var value = $(parameterAnalysisDateId).val();
			var task = getCurrentDate("kget");
			$(parameterAnalysisDateId).val(getCurrentDate("kget")).trigger("change");
			if ($(parameterAnalysisDateId).val() == null) {
				$(parameterAnalysisDateId).val(getYesterdayDate("kget")).trigger("change");
			}
		}
	});
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