var baseDate = "#baseDate";
var compareDate = "#compareDate";
var paramQueryMoTreeId = "#paramQueryMoTree";
var paramQueryMoTreeData = "";

var currentTab = "基础";
$(document).ready(function () {
	$(".table_tab").on("shown.bs.tab", function (e) {
		currentTab = e.target.innerText;
		var params = getParam();
		if (params) {
			parameterSearch(params);
		} else return false;
	});
	//--start of date init--
	$(baseDate).select2();
	$(compareDate).select2();
	getAllCity();
	var url = "paramCompare/getParamTasks";
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
			var task = getCurrentDate("kget");
			//基础日期-基站纬度
			var baseDateSelect = $(baseDate).select2({
				height: 50,
				placeholder: "请选择日期",
				//allowClear: true,
				data: newOptions
			});
			$(baseDate).val(getCurrentDate("kget")).trigger("change");
			if ($(baseDate).val() == null) {
				$(baseDate).val(getYesterdayDate("kget")).trigger("change");
			}
			//对比日期-基站纬度
			var compareDateSelect = $(compareDate).select2({
				height: 50,
				placeholder: "请选择日期",
				//allowClear: true,
				data: newOptions
			});
			$(compareDate).val(getCurrentDate("kget")).trigger("change");
			if ($(compareDate).val() == null) {
				$(compareDate).val(getYesterdayDate("kget")).trigger("change");
			}
		}
	});
	//--end of date init--
	//--start of city init--
	function getAllCity() {
		$("#allCity").multiselect({
			dropRight: true,
			buttonWidth: 220,
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
		var url = "paramCompare/getAllCity";
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

//--end of city init---
	//---------start of paramTree---------

	$.get("common/json/parameterTreeData.json", null, function (data) {
		paramQueryMoTreeData = eval("(" + data + ")");
		var options = {
			bootstrap2: false,
			showTags: true,
			showCheckbox: true,
			levels: 2,
			multiSelect: true, //多选
			data: paramQueryMoTreeData,
			/*onNodeSelected: function(event, data) {
			 clickNode(event, data);
			 //checkAllChildren(data);
			 //checkParent(data);
			 },
			 onNodeUnselected: function(event, data) {
			 clickNode(event, data);
			 //unCheckAllChildren(data);
			 //unCheckParent(data);
			 },*/
			onNodeChecked: function (event, data) {
				checkAllChildren(data);
			},
			onNodeUnchecked: function (event, data) {
				unCheckAllChildren(data);
			}
		};

		$("#paramQueryMoTree").treeview(options);
		customDblClickFun("paramQueryMoTree");
	});
	//---------end of paramTree---------
});
//最后一次触发节点Id
var lastSelectedNodeId = null;
//最后一次触发时间
var lastSelectTime = null;

//自定义业务方法
function dblClick(data) {
	if (data.state.checked) {
		checkAllChildren(data);
		checkParent(data);
	} else {
		unCheckAllChildren(data);
		unCheckParent(data);
	}
}
function onceClick(data) {
	if (data.state.checked) {
		unCheckParent(data);
	} else {
		checkParent(data);
	}
}
function clickNode(event, data) {
	if ((lastSelectedNodeId == 0 || lastSelectedNodeId) && lastSelectTime) {
		var time = new Date().getTime();
		var t = time - lastSelectTime;
		if (lastSelectedNodeId == data.nodeId && t < 300) {
			dblClick(data);
		} else {
			onceClick(data);
		}
	} else {
		onceClick(data);
	}
	lastSelectedNodeId = data.nodeId;
	lastSelectTime = new Date().getTime();
}

//自定义双击事件
function customDblClickFun(treeId) {
	//节点选中时触发
	$("#" + treeId).on("nodeSelected", function (event, data) {
		clickNode(event, data);
	});
	//节点取消选中时触发
	$("#" + treeId).on("nodeUnselected", function (event, data) {
		clickNode(event, data);
	});
}
//递归获取所有的结点id
function getNodeIdArr(node) {
	var ts = [];
	if (node.nodes) {
		for (x in node.nodes) {
			ts.push(node.nodes[x].nodeId);
			if (node.nodes[x].nodes) {
				var getNodeDieDai = getNodeIdArr(node.nodes[x]);
				for (j in getNodeDieDai) {
					ts.push(getNodeDieDai[j]);
				}
			}
		}
	} else {
		ts.push(node.nodeId);
	}
	return ts;
}
function getNodeTextArr(node) {
	var ts = [];
	if (node.nodes) {
		for (x in node.nodes) {
			ts.push(node.nodes[x].text);
			if (node.nodes[x].nodes) {
				var getNodeDieDai = getNodeTextArr(node.nodes[x]);
				for (j in getNodeDieDai) {
					ts.push(getNodeDieDai[j]);
				}
			}
		}
	} else {
		ts.push(node.text);
	}
	return ts;
}
function checkAllChildren(node) {
	var children = getNodeIdArr(node);
	if (children) {
		$("#paramQueryMoTree").treeview("checkNode", [children, {silent: true}]);
	}
}
function unCheckAllChildren(node) {
	var children = getNodeIdArr(node);
	if (children) {
		$("#paramQueryMoTree").treeview("uncheckNode", [children, {silent: true}]);
	}
}
function unCheckParent(node) {
	var parentId = node.nodeId;
	if (parentId != undefined) {
		$("#paramQueryMoTree").treeview("uncheckNode", [parentId, {silent: true}]);
		$("#paramQueryMoTree").treeview("unselectNode", [parentId, {silent: true}]);
	}
}

function checkParent(node) {
	var parentId = node.nodeId;
	if (parentId != undefined) {
		$("#paramQueryMoTree").treeview("checkNode", [parentId, {silent: true}]);
		$("#paramQueryMoTree").treeview("selectNode", [parentId, {silent: true}]);
	}
}
//-----start of moTreeView--------
//根据关键字搜索树
var moData = [];
function search(treeId, erbId) {
	moData = [];
	searchParamMoTree(treeId, erbId);
}
function clearSearch(treeId, erbId) {
	clearParamMoTree(treeId, erbId);
}

function searchParamMoTree(treeId, erbId) {

	var pattern = $("#" + erbId).val();

	$("#" + treeId).on("searchComplete", function (event, data) {
		//alert(data);
		for (var i in data) {
			var obj = {
				id: data[i].id,
				text: data[i].text
			};
			moData.push(obj);
		}

	});
	$("#" + treeId).treeview("search", [pattern, {
		ignoreCase: true,   // case insensitive
		exactMatch: false,  // like or equals
		revealResults: true,  // reveal matching nodes
	}]);
	searchParamData(treeId, erbId);

}

function searchParamData(treeId, erbId) {

	var pattern = $("#paramQueryMoErbs").val();
	var task = $(baseDate).val();
	var data = {
		task: task,
		pattern: pattern,
		moData: moData
	};
	var moParamData = [];
	var url = "paramCompare/getParamData";
	$.post(url, data, function (data) {
		data = JSON.parse(data);
		for (var i in data) {
			var obj = {
				text: data[i].TABLE_NAME
			};

			moParamData.push(obj);
		}
		var options = {
			bootstrap2: false,
			showTags: true,
			showCheckbox: true,
			levels: 2,
			multiSelect: true, //多选
			data: moParamData,
			/*onNodeSelected: function(event, data) {
			 checkAllChildren(data);
			 checkParent(data);
			 },
			 onNodeUnselected: function(event, data) {
			 unCheckAllChildren(data);
			 unCheckParent(data);
			 },*/
			onNodeChecked: function (event, data) {
				checkAllChildren(data);
			},
			onNodeUnchecked: function (event, data) {
				unCheckAllChildren(data);
			}
		};
		$("#" + treeId).treeview(options);
		customDblClickFun(treeId);
	});
}
//清空搜索历史
function clearParamMoTree(treeId, erbId) {
	$("#" + treeId).treeview("clearSearch");
	var options = {
		bootstrap2: false,
		showTags: true,
		showCheckbox: true,
		levels: 2,
		multiSelect: true, //多选
		data: paramQueryMoTreeData,
		/*onNodeSelected: function(event, data) {
		 checkAllChildren(data);
		 checkParent(data);
		 },
		 onNodeUnselected: function(event, data) {
		 unCheckAllChildren(data);
		 unCheckParent(data);
		 },*/
		onNodeChecked: function (event, data) {
			checkAllChildren(data);
		},
		onNodeUnchecked: function (event, data) {
			unCheckAllChildren(data);
		}
	};

	$("#" + treeId).treeview(options);
	customDblClickFun(treeId);
	$("#" + erbId).val("");
}
//-----end of moTreeView--------
//--------start of tableSearch-----
function paramCompareSearch() {
	var params = getParam();
	if (params) {
		var l = Ladda.create(document.getElementById("search"));
		var E = Ladda.create(document.getElementById("export"));
		l.start();
		E.start();
		var ajaxTimeoutTest = $.ajax({
			url:"paramCompare/getCompareResult",  //请求的URL
			timeout : 600000, //超时时间设置，单位毫秒
			type : "post",  //请求方式，get或post
			data :params,  //请求所传参数，json格式
			dataType:"json",//返回的数据格式
			success:function(data){ //请求成功的回调函数
				parameterSearch(params);
			l.stop();
			E.stop();
			},
			complete : function(XMLHttpRequest,status){ //请求完成后最终执行参数
			if(status=="timeout"){//超时,status还有success,error等值的情况
			layer.confirm("请求超时,出现未知情况，请联系开发人员！", {title: "提示"}, function (index) {
			layer.close(index);
		}, function (index) {
			l.stop();
			E.stop();
			ajaxTimeoutTest.abort();
			layer.close(index);
		});
			}
			}
		});
	}
	else return false;
	

}
function paramCompareExport() {
	var params = getParam();
	if (params) parameterExport(params);
	else return false;
}
function getParam() {
	var city = $("#allCity").val();
	var baseDate = $("#baseDate").val();
	var compareDate = $("#compareDate").val();
	var base = $("#base").val();
	var compare = $("#compare").val();
	var baseSpecial = $("#baseSpecial").val();
	var compareSpecial = $("#compareSpecial").val();
	if (baseDate == "" || compareDate == "") {
		//alert("日期项未选，请检查");
		layer.open({
			title: "提示",
			content: "日期项未选，请检查"
		});
		return false;
	}
	if (base == "" || compare == "") {
		//alert("查询条件输入不全，请检查");
		layer.open({
			title: "提示",
			content: "查询条件输入不全，请检查"
		});
		return false;
	}
	;
	var moChecked = $(paramQueryMoTreeId).treeview("getChecked");
	var mos = [];
	if (moChecked == "") {
		var r = confirm("是否确认进行？");
		if (r) {
			mos = getNodeTextArr(paramQueryMoTreeData[0]);
		} else {
			return false;
		}

	} else {
		for (var k in moChecked) {
			mos[mos.length] = moChecked[k].text;
		}
	}

	var params = {
		city: city,
		basedb: baseDate,
		comparedb: compareDate,
		base: base,
		compare: compare,
		baseSpecial: baseSpecial,
		compareSpecial: compareSpecial,
		mos: mos
	};
	return params;
}
function parameterSearch(params) {
	//基础
	var table0 = "#paramCompareTable";
	url = "paramCompare/getItems?basedb=" + params["basedb"];
	$(table0).treegrid({
		url: url,
		idField: "id",
		treeField: "MO",
		align: "center",
		/*width:  100,
		 minWidth: 50,*/
		enableMove: false,
		enableResize: true,
		rownumbers: true,
		pagination: true,
		fitColumns: true,
		columns: [[
			{title: "MO", field: "MO", width: 230},
			{field: "baseId", title: "基础ID", width: 280},
			{field: "compareId", title: "对比ID", width: 200},
			{field: "参数名", title: "参数名", width: 280},
			{field: "基础值", title: "基础值", width: 200},
			{field: "对比值", title: "对比值", width: 200}
		]],
		autoRowHeight: false,
		onLoadSuccess: function () {
			delete $(this).treegrid("options").queryParams["id"];
		}
	});
	//新增
	var table1 = "#paramCompareTableADD";
	var url = "paramCompare/getItemsAdd?basedb=" + params["basedb"];
	$(table1).treegrid({
		url: url,
		idField: "id",
		treeField: "MO",
		align: "center",
		/*width:  100,
		 minWidth: 50,*/
		enableMove: false,
		enableResize: true,
		rownumbers: true,
		pagination: true,
		fitColumns: true,
		columns: [[
			{title: "MO", field: "MO", width: 230},
			{field: "baseId", title: "基础ID", width: 280},
			{field: "compareId", title: "对比ID", width: 200},
			{field: "参数名", title: "参数名", width: 280},
			{field: "基础值", title: "基础值", width: 200},
			{field: "对比值", title: "对比值", width: 200}
		]],
		autoRowHeight: false,
		onLoadSuccess: function () {
			delete $(this).treegrid("options").queryParams["id"];
		}
	});
	//缺失
	var table2 = "#paramCompareTableLESS";
	var url = "paramCompare/getItemsLess?basedb=" + params["basedb"];
	$(table2).treegrid({
		url: url,
		idField: "id",
		treeField: "MO",
		align: "center",
		/*width:  100,
		 minWidth: 50,*/
		enableMove: false,
		enableResize: true,
		rownumbers: true,
		pagination: true,
		fitColumns: true,
		columns: [[
			{title: "MO", field: "MO", width: 230},
			{field: "baseId", title: "基础ID", width: 280},
			{field: "compareId", title: "对比ID", width: 200},
			{field: "参数名", title: "参数名", width: 280},
			{field: "基础值", title: "基础值", width: 200},
			{field: "对比值", title: "对比值", width: 200}
		]],
		autoRowHeight: false,
		onLoadSuccess: function () {
			delete $(this).treegrid("options").queryParams["id"];
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
function parameterExport(params) {
	var E = Ladda.create(document.getElementById("export"));
	E.start();
	params.tabType = currentTab;
	$.post("paramCompare/exportFile", params, function (data) {
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
//--------end of tableSearch-------
//-------------------------------common-----------------------------------
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