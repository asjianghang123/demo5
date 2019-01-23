var mtype="LTE";
var type_model="LTE";
$(document).ready(function () {
	
	//设置树
	setTree("LTE");
	getAllCity("LTE");
	customDblClickFun();
	//树切换
	$("#checkedType").change(function() {
		if($(this).prop("checked")) {
			setTree("LTE");
			getAllCity("LTE");
			customDblClickFun();
			mtype="LTE";
		}else {
			setTree("GSM");
			getAllCity("GSM");
			customDblClickFun();
			mtype="GSM";
		}
	});

	$("#checkedType_Model").change(function() {
		if($(this).prop("checked")) {
			type_model="LTE";
		}else {
			type_model="GSM";
		}
	});


	$("#CustomQueryMoTree").treeview("collapseAll", {silent: true});
	$("#inputName").css("display", "none");
	toogle("CustomQuery");
});

function getText(value) {
	var params = {
		treeData: value
	};
	$("#customName").val(value);
	$.get("CustomQuery/getKpiFormula", params, function (data) {
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
	$.post("CustomQuery/saveModeChange", data, function (data) {
		$("#edit_LTE").modal("hide");
		layer.open({
			title: "提示",
			content: "保存成功"
		});
	});

}

// function saveMode() {
// 	var templateName = $("#customName").val();
// 	var customContext = $("#customContext").val();
// 	var params = {
// 		templateName: templateName,
// 		customContext: customContext
// 	};
// 	$.get("CustomQuery/saveMode", params, function (data) {
// 		if (data == "success") {
// 			//alert("保存成功！");
// 			layer.open({
// 				title: "提示",
// 				content: "保存成功"
// 			});
// 		} else if (data == "login") {
// 			window.location.href = "login";
// 		} else {
// 			//alert("保存失败！");
// 			layer.open({
// 				title: "提示",
// 				content: "保存失败"
// 			});
// 		}
// 	});
// }

function deleteMode(type) {
	var selectedNode = $("#CustomQueryMoTree").treeview("getSelected")[0];
	if (!selectedNode) {
		layer.open({
			title: "提示",
			content: "请选择要删除的模板"
		});
		return;
	}
	layer.confirm("确定删除该模板吗？", {title: "提示"}, function (index) {
		var params = {
			templateId: selectedNode.value
		};
		$.get("CustomQuery/deleteMode", params, function (data) {
			setTree(mtype);
			customDblClickFun(type);
		});
		layer.close(index);
	});
}
function newBuild(type) {
	//$("#inputName").css("display","inline-block");
	$("#inputName").modal();
	$("#insertName").val("");

}

function insertTable() {
	//$("#inputName").css("display","none");
	var insertName = $("#insertName").val();
	var params = {
		insertName: insertName,
		type:type_model
	};
	$.get("CustomQuery/insertMode", params, function (data) {
		if (data == "success") {
			setTree(mtype);
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


function doSearch(type) {
	var l = Ladda.create(document.getElementById("search"));
	var S = Ladda.create(document.getElementById("save"));
	var E = Ladda.create(document.getElementById("export"));
	l.start();
	S.start();
	E.start();
	var city = $("#allCity").val();
	var sql = $("#customContext").val();
	var templateId = $("#customName").val();
	var templatetree = $("#CustomQueryMoTree").treeview("getSelected");

	if (templatetree == "") {
		l.stop();
		S.stop();
		E.stop();

		layer.open({
			title: "提示",
			content: "请选择模板"
		});
		return false;
	}

	if (city == null) {
		l.stop();
		S.stop();
		E.stop();
		layer.open({
			title: "提示",
			content: "请选择城市筛选项"
		});
		return false;
	}
	var params = {
		templateId: templateId,
		city: city,
		sql: sql,
		type:mtype
	};

	var timeout = setTimeout(function () {   // post设置超时时间
		layer.confirm("请求超时,出现未知情况，是否愿意继续等待？", {title: "提示"}, function (index) {
			layer.close(index);
		}, function (index) {
			l.stop();
			S.stop();
			E.stop();
			xhr.abort();
			layer.close(index);
		});
	}, setPDOSearchTime());

	var xhr = $.ajax({
		type: "POST",
		url: "CustomQuery/getTable",
		data: params,
		success: function (data) {
			if (timeout) { //清除定时器
				clearTimeout(timeout);
				timeout = null;
			}
			if (JSON.parse(data).failed) {
				layer.open({
					title: "提示",
					content: "查询无数据"
				});
				l.stop();
				S.stop();
				E.stop();
				return;
			}
			$("#customTableName").val(JSON.parse(data).filename);
			var fieldArr = [];
			var text = (JSON.parse(data).text).split(",");
			for (var i in JSON.parse(data).rows[0]) {
				fieldArr[fieldArr.length] = {field: i, title: text[fieldArr.length], width: 150};
			}
			var newData = JSON.parse(data).rows;
			$("#CustomQueryTable").grid("destroy", true, true);
			$("#CustomQueryTable").grid({
				columns: fieldArr,
				dataSource: newData,
				pager: {limit: 10, sizes: [10, 20, 50, 100]},
				autoScroll: true,
				uiLibrary: "bootstrap"
			});
			if (type == "file") {
				layer.open({
					title: "提示",
					content: JSON.parse(data).filename,
					yes:function(index, layero){
						download(JSON.parse(data).filename);
						layer.close(index);
					}
				});
			}
			l.stop();
			S.stop();
			E.stop();
		}
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

function fileSave(table) {
	var fileName = $("#customTableName").val();
	//alert(fileName);
	if (fileName != "") {
		layer.open({
			title: "提示",
			content: fileName,
			yes:function(index, layero){
				var fileNames = csvZipDownload(fileName);
				download(fileNames);
				layer.close(index);
			}
		});
	}
	else {
		layer.open({
			title: "提示",
			content: "下载失败"
		});
		// alert("No file generated so far!");
	}
}

function getAllCity(type) {
	$("#allCity").multiselect({
		dropRight: true,
		buttonWidth: "100%",
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
	var url = "CustomQuery/getAllCity";
	$.ajax({
		type: "GET",
		url: url,
		data:{"type":type},
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
	setTree(mtype);
	customDblClickFun();
	$("#CustomQueryMoTree").treeview("collapseAll", {silent: true});
}

function searchCustomQuery() {
	var inputData = $("#paramQueryMoErbs").val();
	// var inputData = text;
	inputData = $.trim(inputData);
	if (inputData == "") {
		setTree(mtype);
		customDblClickFun();
		return;
	}
	var params = {
		inputData: inputData,
		type:mtype
	};
	//var url = "CustomQuery/searchCustomTreeData";
	//var treeData;
	
	$.get("CustomQuery/searchCustomTreeData", params, function (data) {
		// data = "[" + data + "]";
		var tree = "#CustomQueryMoTree";
		$(tree).treeview({data: data});
		customDblClickFun();

	});
}

function setTree(type) {
	var tree = "#CustomQueryMoTree";

	$(tree).treeview({
		data: getTree(type)
	}); //树
}

function setCheckedTree(type) {
	var tree = "#CustomQueryMoTree";
	$(tree).treeview({
		data: getCheckedTree(type)
	});
}


//最后一次触发节点Id
var lastSelectedNodeId = null;
//最后一次触发时间
var lastSelectTime = null;
//自定义业务方法
function customBusiness(data) {
	var text = data.value;
	getText(text);
}
function clickNode(event, data) {
	// alert(lastSelectedNodeId + lastSelectTime)
	if (lastSelectedNodeId && lastSelectTime) {
		var time = new Date().getTime();
		var t = time - lastSelectTime;
		if (lastSelectedNodeId == data.nodeId && t < 300) {
			customBusiness(data);
		}
	}
	lastSelectedNodeId = data.nodeId;
	lastSelectTime = new Date().getTime();
	$("#customName").val(data.value);
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
	});
}

function getTree(type) {
	var url = "CustomQuery/getCustomTreeData";
	var treeData;
	$.ajax({
		type: "GET",
		url: url,
		dataType: "json",
		data : {"type":type},
		async: false,
		success: function (data) {
			treeData = data;
		}
	});
	//alert(data);
	return treeData;
}

function getCheckedTree(type) {
	var url = "CustomQuery/getCheckedCustomTreeData";
	var treeData;
	$.ajax({
		type: "GET",
		url: url,
		dataType: "json",
		data : {"type":type},
		async: false,
		success: function (data) {
			treeData = data;
		}
	});
	//alert(data);
	return treeData;
}

function download(url) {
	var browerInfo = getBrowerInfo();
	if (browerInfo == "chrome") {
		download_chrome(url);
	} else if (browerInfo == "firefox") {
		download_firefox(url);
	}
}

function download_chrome(url) {
	var aLink = document.createElement("a");
	aLink.href = url;
	aLink.download = url;
	/*var evt = document.createEvent("HTMLEvents");
	 evt.initEvent("click", false, false);
	 aLink.dispatchEvent(evt);*/
	document.body.appendChild(aLink);
	aLink.click();
}

function download_firefox(url) {
	window.open(url);
}

function getBrowerInfo() {
	var uerAgent = navigator.userAgent.toLowerCase();
	var format = /(msie|firefox|chrome|opera|version).*?([\d.]+)/;
	var matches = uerAgent.match(format);
	return matches[1].replace(/version/, "'safari");
}
