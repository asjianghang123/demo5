$(function () {
	toogle("paramsManage");
	setTree();
	$("#baselineManageTree").treeview("collapseAll", {silent: true});
	initValidata_mode();

	initDate();
	setTaskTree();
	$("#baselineTaskTree").treeview("collapseAll", {silent: true});
	initValidata_task();
});

function setTree() {
	var tree = "#baselineManageTree";
	$(tree).treeview({
		data: getTree(),
		onNodeSelected: function (event, data) {
			if (data.id) {
				$("#templateId").val(data.id);
				$("#templateName").val(data.text);
				$("#user").val(data.user);
				getBaselineManageTable(data.id);
			}
		}
	}); //树
}

function getTree() {
	var url = "paramsManage/getBaselineTreeData";
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

//清空模板树
function clearBaselineManageQuery() {
	$("#baselineManageQuery").val("");
	setTree();
	$("#baselineManageTree").treeview("collapseAll", {silent: true});
}

//筛选模板树
function searchBaselineManageQuery() {
	var inputData = $("#baselineManageQuery").val();
	inputData = $.trim(inputData);
	if (inputData === "") {
		setTree();
		return;
	}
	var params = {
		inputData: inputData
	};
	var url = "paramsManage/searchBaselineTreeData";
	$.get(url, params, function (data) {
		var tree = "#baselineManageTree";
		$(tree).treeview({
			data: data,
			onNodeSelected: function (event, data) {
				if (data.id) {
					$("#templateId").val(data.id);
					$("#templateName").val(data.text);
					getBaselineManageTable(data.id);
				}
			}
		});
		$("#baselineManageTree").treeview("collapseAll", {silent: true});
	});
}

function getBaselineManageTable(templateId) {
	var url = "paramsManage/getBaselineTableData";
	var data = {"templateId": templateId};
	$.get(url, data, function (data) {
		var fieldArr = [];
		var text = (JSON.parse(data).text).split(",");
		for (var i in JSON.parse(data).rows[0]) {
			if (text[fieldArr.length] == "id") {
				fieldArr[fieldArr.length] = {field: i, title: text[fieldArr.length], hidden: true};
			} else {
				fieldArr[fieldArr.length] = {field: i, title: text[fieldArr.length], width: 250};
			}
		}
		var newData = JSON.parse(data).rows;
		$("#baselineManageTable").grid("destroy", true, true);
		$("#baselineManageTable").grid({
			columns: fieldArr,
			dataSource: newData,
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap",
			primaryKey: "id"
		});
	});
}
function queryWhiteList(){
	var Q = Ladda.create(document.getElementById("queryWhiteList"));
	Q.start();
	var url = "paramsManage/getWhiteList";
	var templateId = $("#templateId").val();
	if (templateId == "") {
		layer.open({
			title: "提示",
			content: "请选择模板"
		});
		Q.stop();
		return;
	}
	var data = {"templateId": templateId};
	$.post(url, data, function (data) {
		Q.stop();
		if (JSON.parse(data).total != 0) {
			var fieldArr = [];
			var text = (JSON.parse(data).text).split(",");
			for (var i in JSON.parse(data).rows[0]) {
				if (text[fieldArr.length] == "id") {
					fieldArr[fieldArr.length] = {field: i, title: text[fieldArr.length], hidden: true};
				} else {
					fieldArr[fieldArr.length] = {field: i, title: text[fieldArr.length], width: 250};
				}
			}
			var newData = JSON.parse(data).rows;
			$("#baselineManageTable").grid("destroy", true, true);
			$("#baselineManageTable").grid({
				columns: fieldArr,
				dataSource: newData,
				pager: {limit: 10, sizes: [10, 20, 50, 100]},
				autoScroll: true,
				uiLibrary: "bootstrap",
				primaryKey: "id"
			});
		} else {
			layer.open({
				title: "提示",
				content: "模板还没有白名单，请导入白名单"
			});
			return;
		}
	});
}
function exportBaselineManage() {
	var params = {
		templateName: $("#templateName").val(),
		templateId: $("#templateId").val()
	};
	$.get("paramsManage/downloadFile", params, function (data) {
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
function exportWhiteList() {
	var E = Ladda.create(document.getElementById("exportWhiteList"));
	E.start();
	var templateId = $("#templateId").val();
	if (templateId == "") {
		layer.open({
				title: "提示",
				content: "请选择模板"
			});
		return;
	}
	var params = {
		templateId: templateId
	};
	$.post("paramsManage/exportWhiteList", params, function (data) {
		if (data.result) {
			fileDownload(data.fileName);
		} else {
			layer.open({
				title: "提示",
				content: "下载失败"
			});
		}
		E.stop();
	});
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

function importBaselineManage() {
	$("#import_modal").modal();
	$("#fileImportName").val("");
	$("#fileImport").val("");
	$("#importType").val("template");
}
function importWhiteList(){
	var loginUser = $("#user_user").html();
	var templateUser = $("#user").val();
	if (loginUser == templateUser) {
		
		$("#import_modal").modal();
		$("#fileImportName").val("");
		$("#fileImport").val("");
		$("#importType").val("whiteList");
	} else {
		layer.open({
			title: "提示",
			content: "请操作本用户下模板白名单"
		});
		return;
	}
}
function toName(self) {
	$("#fileImportName").val(self.value);
}
function importFile() {
	var importType = $("#importType").val();
	if (importType == "template") importBaselineTemplate();
	else importBaselineWhiteList();
}
function importBaselineTemplate(){
	var data = getParam();
	if (data === "false") {
		return false;
	}
	$.ajaxFileUpload({
		url: "paramsManage/uploadFile",
		data: data,
		fileElementId: "fileImport",
		secureuri: false,
		dataType: "json",
		type: "post",
		success: function (data, status) {
			$("#import_modal").modal("hide");
			getBaselineManageTable($("#templateId").val());
			//alert("上传成功");
			layer.open({
				title: "提示",
				content: "上传成功"
			});
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


function importBaselineWhiteList(){
	var I = Ladda.create(document.getElementById("importBtn"));
	I.start();
	var params = getParam();
	if (params === "false") {
		return false;
	}
	$.ajaxFileUpload({
		url: "paramsManage/uploadWhiteListFile",
		data: params,
		fileElementId: "fileImport",
		secureuri: true,
		dataType: "json",
		type: "post",
		success: function (data, status) {
			params.fileName = data;
			$.post("paramsManage/getFileContent", params, function (data) {
				$("#import_modal").modal("hide");
				queryWhiteList();
				layer.open({
					title: "提示",
					content: "上传成功"
				});
			});
			I.stop();
		},
		error: function (data, status, e) {
			layer.open({
				title: "提示",
				content: "上传失败"
			});
			I.stop();
		}
	});
}
function getParam() {
	var templateId = $("#templateId").val();
	if (!templateId) {
		//alert("请选择模板!");
		layer.open({
			title: "提示",
			content: "请选择模板"
		});
		return false;
	}
	var data = {templateId: templateId};
	if ($("#fileImport").val() === "") {
		//alert("请选择上传的文件！");
		layer.open({
			title: "提示",
			content: "请选择上传的文件"
		});
		return false;
	}
	return data;
}

function addMode() {
	$("#add_mode").modal();
	$("#add_mode input").val("");
	$("#add_mode textarea").val("");
	$("#modeForm").data("bootstrapValidator").destroy();
	$("#TDD").prop("checked","checked");
	$("#isAutoExecuteNo").prop("checked","checked");
	$("#isNewSiteNo").prop("checked","checked");
	getCities('');
	initValidata_mode();
}
function getCities(templateId) {
	$("#city").multiselect({
		dropRight: true,
		buttonWidth: 200,
		//enableFiltering: true,
		nonSelectedText: "请选择城市",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有平台类型",
		maxHeight: 200,
		width: 150
	});
	url = "paramsManage/getCitySelect";
	$.ajax({
		type: "post",
		url: url,
		dataType: "json",
		data : {templateId:templateId},
		success: function (data) {
			var newOptions = [];
			var obj = {};
			$(data).each(function (k, v) {
				v = eval("(" + v + ")");
				obj = {
					label: v.text,
					value: v.value,
					selected: v.selected
				};
				newOptions.push(obj);
			});
			$("#city").multiselect("dataprovider", newOptions);
		}
	});
}
function editMode() {
	var loginUser = $("#user_user").html();
	var templateUser = $("#user").val();
	if (loginUser == templateUser) {
		var templateId = $("#templateId").val();
		if (templateId == "") {
			layer.open({
				title: "提示",
				content: "未选择模板"
			});
			return;
		}
		$("#add_mode").modal();
		$("#modeForm").data("bootstrapValidator").destroy();
		$.post("paramsManage/getTemplate", {templateId:templateId}, function (data) {
			$("#modeName").val(data.templateName);
			$("#"+data.networkStandard).prop("checked","checked");
			if (data.isAutoExecute == "yes") {
				$("#isAutoExecuteYes").prop("checked", "checked");
			}else {
				$("#isAutoExecuteNo").prop("checked", "checked");
			}

			if (data.isNewSite == "yes") {
				$("#isNewSiteYes").prop("checked", "checked");
			}else {
				$("#isNewSiteNo").prop("checked", "checked");
			}

			$("#modeDescription").val(data.description);
			$("#updateTemplateId").val(data.id);
			getCities(data.id);
		});
		initValidata_mode();
	} else {
		layer.open({
				title: "提示",
				content: "请修改本用户下模板"
			});
			return;
	}
	
} 
function addOrUpdateTemplate() {
	$("#modeForm").data("bootstrapValidator").validate();
	var flag = $("#modeForm").data("bootstrapValidator").isValid();
	if (!flag) {
		return;
	}
	var data = {};
	data.modeName = $("#modeName").val();
	data.modeDescription = $("#modeDescription").val();
	if ($("input[name='isAutoExecute']:checked")[0].id == "isAutoExecuteYes") {
		data.isAutoExecute = "yes";
	}else{
		data.isAutoExecute = "no";
	};
	if ($("input[name='isNewSite']:checked")[0].id == "isNewSiteYes") {
		data.isNewSite = "yes";
	}else{
		data.isNewSite = "no";
	};
	data.networkStandard = $("input[name='networkStandard']:checked")[0].id;
	data.templateId = $("#updateTemplateId").val();
	data.citys = $("#city").val();
	$.post("paramsManage/addOrUpdateTemplate", data, function (res) {
		if (res == "login") {
			//alert("尚未登录，不能添加模板");
			layer.open({
				title: "提示",
				content: "尚未登录，不能添加模板"
			});
			window.location.href = "login";
			return;
		}
		if (res) {
			//alert("添加成功！");
			layer.open({
				title: "提示",
				content: "添加/修改成功"
			});
			setTree();
		} else {
			//alert("添加失败!");
			layer.open({
				title: "提示",
				content: "添加/修改失败"
			});
		}
		$("#add_mode").modal("hide");
	});
}


function initValidata_mode() {
	$("#modeForm").bootstrapValidator({
		message: "This value is not valid",
		feedbackIcons: {
			valid: "glyphicon glyphicon-ok",
			invalid: "glyphicon glyphicon-remove",
			validating: "glyphicon glyphicon-refresh"
		},
		fields: {
			modeName: {
				//message: "用户名验证失败",
				validators: {
					notEmpty: {
						message: "模板名称不能为空"
					}
				}
			}
		}
	});
}

function deleteMode() {
	if (!$("#baselineManageTree").treeview("getSelected")[0] || !$("#baselineManageTree").treeview("getSelected")[0].id) {
		//alert("尚未选择模板");
		layer.open({
			title: "提示",
			content: "尚未选择模板"
		});
		return;
	}
	layer.confirm("确认删除该模板吗？", {title: "提示"}, function (index) {
		var id = $("#baselineManageTree").treeview("getSelected")[0].id;
		$.get("paramsManage/deleteMode", {"id": id}, function (res) {
			if (res == "login") {
				layer.open({
					title: "提示",
					content: "尚未登录，不能删除模板"
				});
				window.location.href = "login";
				return;
			}
			if (res == "1") {
				layer.open({
					title: "提示",
					content: "删除成功"
				});
				setTree();
			} else if (res == "2") {
				layer.open({
					title: "提示",
					content: "删除失败"
				});
			} else if (res == "3") {
				layer.open({
					title: "提示",
					content: "没有权限删除该模板"
				});
			}
		});
		layer.close(index);
	});
	/*var flag = confirm("确认删除该模板吗？");
	 if(!flag){
	 return;
	 }
	 var id = $("#baselineManageTree").treeview("getSelected")[0].id;
	 $.get("paramsManage/deleteMode",{"id":id}, function(res){
	 if(res == "login"){
	 //alert("尚未登录，不能删除模板");
	 layer.open({
	 title: "提示",
	 content: "尚未登录，不能删除模板"
	 });
	 window.location.href = "login";
	 return;
	 }
	 if (res == "1") {
	 //alert("删除成功！");
	 layer.open({
	 title: "提示",
	 content: "删除成功"
	 });
	 setTree();
	 } else if (res == "2") {
	 //alert("删除失败！");
	 layer.open({
	 title: "提示",
	 content: "删除失败"
	 });
	 } else if (res == "3") {
	 //alert("没有权限删除该模板！");
	 layer.open({
	 title: "提示",
	 content: "没有权限删除该模板"
	 });
	 }
	 });*/
}

function initDate() {
	var url = "paramsManage/getDate";
	$.get(url, null, function (data) {
		data = JSON.parse(data);
		var date = $("#date").select2({
			placeholder: "请选择日期",
			//allowClear: true,
			data: data
		});
		$(".select2-container").css("width", "100%");
	});
}
function setTaskTree() {
	var tree = "#baselineTaskTree";
	$(tree).treeview({
		data: getTree(),
		onNodeSelected: function (event, data) {
			if (data.id) {
				$("#log").html("");
				getBaselineTaskTable(data.id);
				$("#modeName_task").val(data.text);
				$("#templateId_task").val(data.id);
			}
		}
	}); //树
}

//清空模板树
function clearBaselineTaskQuery() {
	$("#baselineTaskQuery").val("");
	setTaskTree();
	$("#baselineTaskTree").treeview("collapseAll", {silent: true});
}

//筛选模板树
function searchBaselineTaskQuery() {
	var inputData = $("#baselineTaskQuery").val();
	inputData = $.trim(inputData);
	if (inputData === "") {
		setTaskTree();
		return;
	}
	var params = {
		inputData: inputData
	};
	var url = "paramsManage/searchBaselineTreeData";
	$.get(url, params, function (data) {
		var tree = "#baselineTaskTree";
		$(tree).treeview({
			data: data,
			onNodeSelected: function (event, data) {
				if (data.id) {
					getBaselineTaskTable(data.id);
					$("#modeName_task").val(data.text);
					$("#templateId_task").val(data.id);
				}
			}
		});
		$("#baselineTaskTree").treeview("collapseAll", {silent: true});
	});
}

function getBaselineTaskTable(id) {
	var fieldArr = [];
	var fieldStr = "taskName,status,startTime,endTime,owner,createTime,databaseDate,templateName";
	var text = fieldStr.split(",");
	for (var i in text) {
		fieldArr[fieldArr.length] = {field: text[fieldArr.length], title: text[fieldArr.length], width: 200};
	}
	$("#baselineTaskTable").grid("destroy", true, true);
	var grid = $("#baselineTaskTable").grid({
		columns: fieldArr,
		dataSource: {
			url: "paramsManage/getBaselineTaskTable",
			success: function (data) {
				data = JSON.parse(data);
				grid.render(data);
			}
		},
		params: {"id": id},
		pager: {limit: 10, sizes: [10, 20, 50, 100]},
		autoScroll: true,
		uiLibrary: "bootstrap",
		primaryKey: "id",
		autoLoad: true,
		rowSelect:function(e, $row, id, record){
			$("#log").html(record.taskLog);
		}
	});
}
function addBaselineTask() {
	if (!$("#templateId_task").val()) {
		//alert("请选择模板");
		layer.open({
			title: "提示",
			content: "请选择模板"
		});
		return;
	}
	$("#add_task").modal();
	$("#taskName").val("");
	$("#taskForm").data("bootstrapValidator").destroy();
	initValidata_task();
}
function initValidata_task() {
	$("#taskForm").bootstrapValidator({
		message: "This value is not valid",
		feedbackIcons: {
			valid: "glyphicon glyphicon-ok",
			invalid: "glyphicon glyphicon-remove",
			validating: "glyphicon glyphicon-refresh"
		},
		fields: {
			taskName: {
				//message: "用户名验证失败",
				validators: {
					notEmpty: {
						message: "任务名称不能为空"
					}
				}
			}
		}
	});
}
function addTask() {
	$("#taskForm").data("bootstrapValidator").validate();
	var flag = $("#taskForm").data("bootstrapValidator").isValid();
	if (!flag) {
		return;
	}
	var params = {
		//"myDate" : decodeURIComponent(new Date().Format("yyyy-MM-dd hh:mm:ss")),
		"taskName": decodeURIComponent($("#taskName").val()),
		"templateId": decodeURIComponent($("#templateId_task").val()),
		"templateName": decodeURIComponent($("#modeName_task").val()),
		"databaseDate": decodeURIComponent($("#date").val())
	};
	$.get("paramsManage/addTask", params, function (res) {
		if (res == "true") {
			//alert("添加成功！");
			layer.open({
				title: "提示",
				content: "添加成功！"
			});
			$("#log").html("");
			getBaselineTaskTable($("#templateId_task").val());
		} else {
			//alert("添加失败!已存在同名任务");
			layer.open({
				title: "提示",
				content: "添加失败!已存在同名任务或者文件中含有不合法字符"
			});
		}
		$("#add_task").modal("hide");
	});
}
function deleteBaselineTask() {
	var taskId = $("#baselineTaskTable").grid("getSelected");
	if (!taskId) {
		//alert("请选择要删除的任务！");
		layer.open({
			title: "提示",
			content: "请选择要删除的任务！"
		});
		return;
	}
	layer.confirm("确认删除该任务吗？", {title: "提示"}, function (index) {
		$.get("paramsManage/deleteTask", {"taskId": taskId}, function (res) {
			if (res == "true") {
				layer.open({
					title: "提示",
					content: "删除成功"
				});
				$("#log").html("");
				getBaselineTaskTable($("#templateId_task").val());
			} else {
				layer.open({
					title: "提示",
					content: "删除失败"
				});
			}
		});
		layer.close(index);
	});
	/*var flag = confirm("确认删除该任务吗？");
	 if(!flag){
	 return;
	 }
	 $.get("paramsManage/deleteTask",{"taskId":taskId},function(res){
	 if (res == "true") {
	 //alert("删除成功！");
	 layer.open({
	 title: "提示",
	 content: "删除成功"
	 });
	 getBaselineTaskTable($("#templateId_task").val());
	 } else {
	 //alert("删除失败！");
	 layer.open({
	 title: "提示",
	 content: "删除失败"
	 });
	 }
	 });*/
}
function runBaselineTask() {
	var user = $("#user_user").html();
	var selectedData = $("#baselineTaskTable").grid("getById", $("#baselineTaskTable").grid("getSelected"));
	if (!selectedData) {
		//alert("请选择需要启动的任务");
		layer.open({
			title: "提示",
			content: "请选择需要启动的任务"
		});
		return;
	}
	if (user == "admin" || user == selectedData.owner) {
		if (selectedData.status == "complete" || selectedData.status == "ongoing") {
			//alert("不能启动一个正在执行或者执行完毕的任务!");
			layer.open({
				title: "提示",
				content: "不能启动一个正在执行或者执行完毕的任务"
			});
			return;
		}
		var taskName     = decodeURIComponent(selectedData.taskName);
		var templateId   = decodeURIComponent(selectedData.templateId);
		var databaseDate = decodeURIComponent(selectedData.databaseDate);
		var params = {
			"taskName": taskName,
			"templateId": templateId,
			"databaseDate": databaseDate
		};
		var myDate = getNewDate();
		
		$("#log").html("");
		$.post("paramsManage/runTaskCheck", params, function(data){
			var taskLog = "";
			if(data!="[]"){
				data = eval("("+data+")");
				taskLog = "有问题模板如下：<br/>";
				for (var i = 0; i <data.length; i++) {
					taskLog = taskLog+"moName : "+data[i]["moName"]+", parameter : "+data[i]["parameter"];
					if (data[i]["qualification"]) {
						taskLog = taskLog+",qualification : "+data[i]["qualification"];
					}
					taskLog = taskLog+"<br/>";
				}
				updateTaskLog(taskName,taskLog);
				$("#log").html(taskLog);
				layer.confirm("模板中有错出现,确认执行该任务吗？", {title: "提示"}, function (index) {
					layer.close(layer.index);
					//$("#baselineTaskTable tr.active td").eq(1).children("div").html("ongoing");
					$("#baselineTaskTable tr.active td").eq(2).children("div").html(myDate);
					$.get("paramsManage/runTask", params, function (data) {
						
						taskLog = "算法执行完成！<br/>"+taskLog;
						updateTaskLog(taskName,taskLog);
						$("#log").html(taskLog);
						var data = eval("("+data+")");
						getBaselineTaskTable($("#templateId_task").val());
					});
				});
			}else{
				$("#baselineTaskTable tr.active td").eq(1).children("div").html("ongoing");
				$("#baselineTaskTable tr.active td").eq(2).children("div").html(myDate);
				$.get("paramsManage/runTask", params, function (data) {
					taskLog = "算法执行完成！<br/>"+taskLog;
					updateTaskLog(taskName,taskLog);
					$("#log").html(taskLog);
					var data = eval("("+data+")");
					getBaselineTaskTable($("#templateId_task").val());
				});
			}
		});
	} else {
		//alert("没有权限启动该任务！");
		layer.open({
			title: "提示",
			content: "没有权限启动该任务"
		});
		return;
	}
}
function updateTaskLog(taskName,taskLog){
	var params = {
		taskName:taskName,
		taskLog:taskLog
	};
	$.post("paramsManage/updateTaskLog",params,function(data){
	});
}
function getNewDate() {
	var date = new Date();
	var year = date.getFullYear();
	var month = date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1;
	var day = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
	var hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
	var minute = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
	var second = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
	var mydate = year + "-" + month + "-" + day + " " + hour + ":" + minute + ":" + second;
	return mydate;
}
function stopBaselineTask() {
	var user = $("#user_user").html();
	var selectedData = $("#baselineTaskTable").grid("getById", $("#baselineTaskTable").grid("getSelected"));
	if (!selectedData) {
		//alert("请选择需要停止的任务");
		layer.open({
			title: "提示",
			content: "请选择需要停止的任务"
		});
		return;
	}
	if (user == "admin" || user == selectedData.owner) {
		if (selectedData.status != "ongoing") {
			//alert("只能停止正在运行的任务!");
			layer.open({
				title: "提示",
				content: "只能停止正在运行的任务"
			});
			return;
		}
		var params = {
			"taskName": decodeURIComponent(selectedData.taskName),
			"templateId": decodeURIComponent(selectedData.templateId)
		};

		$.get("paramsManage/stopTask", params, function (data) {
			getBaselineTaskTable($("#templateId_task").val());
		});
	} else {
		//alert("没有权限停止该任务！");
		layer.open({
			title: "提示",
			content: "没有权限停止该任务"
		});
		return;
	}
}