var search_result_num = 0;//弹出框中搜索定位计数
var taskMonitor; //任务监视器

$(document).ready(function () {
	toogle("storageManage");

	setTree();
	doQueryStorage("0");
	//setChooseEventTree();
	initValidata();
});
var storageQueryTreeData = "";
//设置左侧树
function setTree() {
	$.get("common/json/taskTreeData.json", null, function (data) {
		storageQueryTreeData = eval("(" + data + ")");
		var options = {
			bootstrap2: false,
			showTags: true,
			levels: 2,
			data: storageQueryTreeData,
			onNodeSelected: function (event, data) {
				$("#storageFlag").val(data.id);
				doQueryStorage(data.id);
			}
		};
		$("#storageQueryTree").treeview(options);
	});
}

function doQueryStorage(id) {
	var data = {id: id};
	$.get("storageManage/taskQuery", data, function (data) {
		var fieldArr = [];
		var text = (JSON.parse(data).text).split(",");
		for (var i in JSON.parse(data).rows[0]) {
			if (text[fieldArr.length] == "startTime" || text[fieldArr.length] == "endTime" || text[fieldArr.length] == "createTime") {
				fieldArr[fieldArr.length] = {field: i, title: text[fieldArr.length], width: 250};
			} else if (text[fieldArr.length] == "tracePath") {
				fieldArr[fieldArr.length] = {field: i, title: text[fieldArr.length], width: 300};
			} else {
				fieldArr[fieldArr.length] = {field: i, title: text[fieldArr.length], width: 150};
			}
		}
		var newData = JSON.parse(data).rows;
		$("#storageTable").grid("destroy", true, true);
		var grid = $("#storageTable").grid({
			columns: fieldArr,
			dataSource: newData,
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap"
		});

		grid.on("rowSelect", function (e, $row, id, record) {
			clearInterval(taskMonitor);
			$("#log").html("");
			if (record.status == "ongoing") {
				taskMonitor = setInterval(function () {
					updateMonitor(record.taskName);
				}, 2000);
				updateMonitor(record.taskName);
			} else if (record.status == "complete") {
				updateMonitor(record.taskName);
			}
		});

	});
}

function addTask() {
	if (!$("#storageQueryTree").treeview("getSelected")[0]) {
		//alert("Please select directory of trace first!");
		layer.open({
			title: "提示",
			content: "请先选择入库类型"
		});
		return;
	}
	var text = $("#storageQueryTree").treeview("getSelected")[0].text;
	/*if(text != "KGET"){
	 $("#chooseEventBox").attr("disabled","false");
	 }else{
	 $("#chooseEventBox").attr("disabled","true");
	 }*/
	var type;
	if (text == "KGET") {
		type = "kget";
	} else if (text == "CTR") {
		type = "ctr";
	} else if (text == "CDR") {
		type = "cdr";
	} else if (text == "EBM") {
		type = "ebm";
	} else if (text == "PCAP") {
		type = "pacp";
	// } else if (text == "CTRFULL") {
	// 	type = "ctrfullsystem";
	}
	$.get("storageManage/getTaskTraceDir", {"type": type}, function (data) {
		data = eval("(" + data + ")");
		var tree = "#DataTraceQueryTree";
		$(tree).treeview({
			data: data,
			onSearchComplete: function (event, data) {
				$("#DataTraceQueryTree li.search-result").css("background-color", "#428bca");
				search_result_num = 0;
				var searchNode = $("#DataTraceQueryTree li.search-result").eq(search_result_num++);
				if (searchNode) {
					var scroll = searchNode.offset().top - $("#DataTraceQueryTreeDiv").offset().top + $("#DataTraceQueryTreeDiv").scrollTop();
					$("#DataTraceQueryTreeDiv").scrollTop(scroll);
				}
			}
		}); //树
		// if (text == "CTR" || text == "CDR" || text == "EBM" || text == "CTRFULL") {
		// 	$("#chooseEventBox").css("visibility", "visible");
		// } else {
		// 	$("#chooseEventBox").css("visibility", "hidden");
		// }
		// if (text == "CTR" || text == "CTRFULL") {
		// 	$(".ctrOnly").removeClass("hidden");
		// } else {
		// 	$(".ctrOnly").addClass("hidden");
		// }
		if (type == "ctr") {
			$("#ctrTypeDiv").show();
		} else {
			$("#ctrTypeDiv").hide();
		}
		$("#add_task").modal();
		$("#taskName_form").data("bootstrapValidator").destroy();
		initValidata();
	});
}

function clearDataTraceQuery() {
	$("#paramsQueryDataTrace").val("");
	$("#paramsQueryDataTrace_change").val("");
	$("#DataTraceQueryTree").treeview("clearSearch");
}

function searchDataTraceQuery() {
	var paramers = $("#paramsQueryDataTrace").val();
	if ($("#paramsQueryDataTrace_change").val() != paramers) {
		$("#paramsQueryDataTrace_change").val(paramers);
		$("#DataTraceQueryTree").treeview("search", [paramers, {
			ignoreCase: true,     // case insensitive
			exactMatch: false,    // like or equals
			revealResults: true  // reveal matching nodes
		}]);
	} else {
		var searchNode = $("#DataTraceQueryTree li.search-result").eq(search_result_num++);
		if (searchNode) {
			var scroll = searchNode.offset().top - $("#DataTraceQueryTreeDiv").offset().top + $("#DataTraceQueryTreeDiv").scrollTop();
			$("#DataTraceQueryTreeDiv").scrollTop(scroll);
		}
		if (search_result_num == $("#DataTraceQueryTree li.search-result").length) {
			search_result_num = 0;
		}
	}
}
function saveTask() {
	var taskName = $("#taskName").val().trim();
	$("#taskName_form").data("bootstrapValidator").validate();
	var flag = $("#taskName_form").data("bootstrapValidator").isValid();
	if (!flag) {
		return;
	}
	/*if(!taskName){
	 alert("任务名称不能为空！");
	 return;
	 }
	 if(taskName.indexOf("-")>=0||taskName.indexOf("%")>=0){
	 alert("任务名称不能含有“-”和“%”！");
	 return;
	 }*/
	var selectedNode = $("#DataTraceQueryTree").treeview("getSelected")[0];
	if (!selectedNode) {
		//alert("请选择数据目录！");
		layer.open({
			title: "提示",
			content: "请选择数据目录"
		});
		return;
	}
	var taskType = $("#storageFlag").val();
	if (taskType == 1) {
		taskType = "parameter";
	} else if (taskType == 2) {
		taskType = "ctrsystem";
	} else if (taskType == 3) {
		taskType = "cdrsystem";
	} else if (taskType == 4) {
		taskType = "ebmsystem";
	} else if (taskType == 5) {
		taskType = "pcapsystem";
	// } else if (taskType == 6) {
	// 	taskType = "ctrfullsystem";
	}
	prepareTask(taskName, selectedNode, taskType);
}
function prepareTask(name, node, type) {
	var myDate = new Date().Format("yyyy-MM-dd hh:mm:ss");
	var data = {
		"tracePath": encodeURI(node.path),
		"taskName": encodeURI(name),
		"createTime": myDate,
		"type": type

	};
	/*if(type == "ctrsystem" | type == "cdrsystem" | type == "ebmsystem" | type == "ctrfullsystem"){
	 console.log(type);
	 var treeNodes=$("#eventQueryTree").treeview("getNode",0);
	 data.taskConfig = JSON.stringify(treeNodes);
	 }*/
	$.ajax({
		type: "POST",
		url: "storageManage/addTask",
		data: data,
		async: true,
		success: function (returnData) {
			if (returnData == "true") {
				runTask(data.taskName, data.tracePath, data.type);
				//alert("新增成功！");
				layer.open({
					title: "提示",
					content: "新增成功"
				});
				doQueryStorage($("#storageFlag").val());

				$("#add_task").modal("hide");
				$("#taskName").val("");

			} else if (returnData == "false") {
				//alert("任务名称已经存在，请重新输入！");
				layer.open({
					title: "提示",
					content: "任务名称已经存在，请重新输入"
				});
			} else if (returnData == "login") {
				//alert("尚未登录，不能新增任务");
				layer.open({
					title: "提示",
					content: "尚未登录，不能新增任务"
				});
				window.location.href = "login";
				return;
			}
		}
	});
	//return true;
}
function runTask(taskName, tracePath, type) {
	var myDate = new Date().Format("yyyy-MM-dd hh:mm:ss");
	var ctrFlag = $(".ctrType:checked").val();
	var data = {
		"taskName": taskName,
		"tracePath": tracePath,
		"startTime": myDate,
		"type": type,
		"ctrFlag":ctrFlag
	};
	taskMonitor = setInterval(function () {
		updateMonitor(taskName);
	}, 2000);
	$.ajax({
		type: "get",
		url: "storageManage/runTask",
		data: data,
		async: true,
		success: function (data) {
			var returnData = JSON.parse(data);
			if (returnData.status == "true" || returnData.status == "abort") {
				doQueryStorage($("#storageFlag").val());
				clearInterval(taskMonitor);
				updateMonitor(taskName);
			}
		}
	});
}
Date.prototype.Format = function (fmt) {
	var o = {
		"M+": this.getMonth() + 1,
		"d+": this.getDate(),
		"h+": this.getHours(),
		"m+": this.getMinutes(),
		"s+": this.getSeconds(),
		"q+": Math.floor((this.getMonth() + 3) / 3),
		"S": this.getMilliseconds()
	};
	if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
	for (var k in o)
		if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
	return fmt;
};
function deleteTask() {
	var taskName = $("#storageTable").find("tr.active").children("td").eq(0).children("div").html();
	var status = $("#storageTable").find("tr.active").children("td").eq(1).children("div").html();
	var type = $("#storageTable").find("tr.active").children("td").eq(7).children("div").html();
	if (!taskName) {
		//alert("选择需要删除的任务!");
		layer.open({
			title: "提示",
			content: "选择需要删除的任务"
		});
		return;
	}
	if (status == "ongoing") {
		//alert("不能删除一个正在运行的任务!");
		layer.open({
			title: "提示",
			content: "不能删除一个正在运行的任务"
		});
		return;
	}
	layer.confirm("确认删除" + taskName + "任务吗？", {title: "提示"}, function (index) {
		var data = {"taskName": encodeURI(taskName), "type": type};
		$.ajax({
			type: "get",
			url: "storageManage/deleteTask",
			data: data,
			async: true,
			success: function (returnData) {
				//alert("删除成功！");
				layer.open({
					title: "提示",
					content: "删除成功"
				});
				doQueryStorage($("#storageFlag").val());
				clearInterval(taskMonitor);
				$("#log").html("");
			}
		});
		layer.close(index);
	});
	/*var flag = confirm("确认删除"+taskName+"任务吗？");
	 if(!flag){
	 return;
	 }
	 var data={"taskName":encodeURI(taskName),"type":type};
	 $.ajax({
	 type:"get",
	 url: "storageManage/deleteTask",
	 data:data,
	 async: true,
	 success: function(returnData){
	 alert("删除成功！");
	 doQueryStorage($("#storageFlag").val());
	 clearInterval(taskMonitor);
	 $("#log").html("");
	 }
	 });*/
}
function updateMonitor(taskName) {
	var data = {"taskName": encodeURI(taskName)};
	$.ajax({
		type: "get",
		url: "storageManage/monitor",
		data: data,
		async: true,
		success: function (returnData) {
			$("#log").html(returnData);
			$("#log").parent().scrollTop($("#log").height());
			//var div = document.getElementById("log");
			// div.scrollTop = div.scrollHeight;
		}
	});
}

function stopTask() {
	var taskName = $("#storageTable").find("tr.active").children("td").eq(0).children("div").html();
	var status = $("#storageTable").find("tr.active").children("td").eq(1).children("div").html();
	var type = $("#storageTable").find("tr.active").children("td").eq(7).children("div").html();
	if (taskName) {
		if (status == "ongoing") {
			//var rowIndex =$("#taskTable").datagrid("getRowIndex",row);
			//var data="taskName="+encodeURI(row.taskName)+"&action=stop";
			var data = {
				"taskName": encodeURI(taskName)
			};
			$.ajax({
				type: "get",
				url: "storageManage/stopTask",
				data: data,
				async: true,
				success: function (returnData) {
					doQueryStorage($("#storageFlag").val());
					clearInterval(taskMonitor);
				}
			});
		} else {
			//alert("只能停止正在运行的任务!");
			layer.open({
				title: "提示",
				content: "只能停止正在运行的任务"
			});
			return;
		}
	} else {
		//alert("选择需要停止的任务!");
		layer.open({
			title: "提示",
			content: "选择需要停止的任务"
		});
		return;
	}
}

function initValidata() {
	$("#taskName_form").bootstrapValidator({
		message: "This value is not valid",
		feedbackIcons: {
			valid: "glyphicon glyphicon-ok",
			invalid: "glyphicon glyphicon-remove",
			validating: "glyphicon glyphicon-refresh"
		},
		fields: {
			taskName: {
				validators: {
					notEmpty: {
						message: "任务名不能为空"
					},
					regexp: {
						/* 只需加此键值对，包含正则表达式，和提示 */
						regexp: /^[a-zA-Z].[a-zA-Z0-9_$]+$/,
						message: "只能包含数字，字母，$和_,且只能字母开头"
					}

				}
			}
		}
	});
}