$(document).ready(function () {
	toogle("dataSourceManage");
	initLogType();
	//initNode();
	initFileName();
	initTable();
	if (window.location.search) {
		var onlineStorage = window.location.search.split("=")[1];
		if (onlineStorage) {
			$("#storageBtn_div").removeClass("hidden");
		}
	} else {
		$("#storage_div").removeClass("hidden");
	}
	//setChooseEventTree();
	initValidata();
});
function initLogType() {
	var url = "dataSourceManage/getLogType";
	$.get(url, null, function (data) {
		data = eval("(" + data + ")");
		var html = "";
		for (var i in data) {
			html += "<option value='" + data[i].value + "'>" + data[i].text + "</option>";
		}
		$("#logType").append(html);

		//获取第一个节点的文件名列表
		var logType = $("#logType option:selected").val();
		initNode(logType);
		//绑定change事件
		$("#logType").on("change", function () {
			var logType = $("#logType option:selected").val();
			initNode(logType);
		});
	});
}
function initNode(logType) {
	var url = "dataSourceManage/getNode";
	$.post(url, {"logType": logType}, function (data) {
		data = eval("(" + data + ")");
		var html = "";
		for (var i in data) {
			html += "<option value='" + data[i].value + "' data-sshUserName='" + data[i].sshUserName + "' data-sshPassword='" + data[i].sshPassword + "' data-fileDir='" + data[i].fileDir + "'>" + data[i].text + "</option>";
		}
		$("#node").append(html);

		//获取第一个节点的文件名列表
		var node = $("#node option:selected");
		getFileName(node);
		//绑定change事件
		$("#node").on("change", function () {
			var node = $("#node option:selected");
			getFileName(node);
		});
	});
}
function initFileName() {
	$("#fileName").multiselect({
		//dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "选择文件",
		//filterPlaceholder:'搜索',
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有平台类型",
		maxHeight: 200,
		maxWidth: "100%"
	});
}
function getFileName(node) {

	var remoteIp = node.val();
	var userName = node.attr("data-sshUserName");
	var userPassword = node.attr("data-sshPassword");
	var fileDir = node.attr("data-fileDir");
	var params = {
		remoteIp: remoteIp,
		userName: userName,
		userPassword: userPassword,
		fileDir: fileDir,
		type:$("#logType").val()
	};

	$("#exportBtn").removeAttr("disabled");
	if (remoteIp == "localhost") {
		$("#exportBtn").attr("disabled", "disabled");
	}

	$.get("dataSourceManage/getFileName", params, function (data) {
		var returnData = JSON.parse(data);
		$("#fileName").multiselect("dataprovider", returnData);
	});
}
function initTable() {
	var table = "#fileTable";
	$(table).treegrid({
		idField: "id",
		treeField: "kpiName",
		columns: [[
				{
					title: "Name", field: "kpiName", width: 900,
					formatter: function (val, row) {
						return "<input type='checkbox' onclick=show('" + row.id + "')  id='check_" + row.id + "' " + (row.checked ? 'checked' : '') + "/>" + row.kpiName;
					}
				},
				{field: "size", title: "Size", width: 100}
			]]
	}); //树
}
function query() {
	//$("#queryBtn").attr("disabled","disabled");
	$("#exportBtn").attr("disabled", "disabled");
	$("#storageBtn").attr("disabled", "disabled");
	var query = Ladda.create(document.getElementById("queryBtn"));
	query.start();

	var node = $("#node option:selected");
	var remoteIp = node.val();
	var userName = node.attr("data-sshUserName");
	var userPassword = node.attr("data-sshPassword");
	var fileDir = node.attr("data-fileDir");

	var ctrDldPoint = $("#fileName").val(); //获取文件名
	var erbs = $("#cellInput").val();
	var ctrDldPoints = node.html(); //获取节点名
	if(!erbs){
			layer.open({
				title: "提示",
				content: "请输入基站名称"
			});
			query.stop();
			return;
	}
	var params = {
		point: ctrDldPoint,
		erbs: erbs,
		points: ctrDldPoints,
		remoteIp: remoteIp,
		userName: userName,
		userPassword: userPassword,
		fileDir: fileDir,
		type:$("#logType").val()
	};
	$.post("dataSourceManage/ctrTreeItems", params, function (data) {

		var returnData = JSON.parse(data);
		$("#fileTable").treegrid("loadData", returnData);
		query.stop();
		$("#exportBtn").removeAttr("disabled");
		$("#storageBtn").removeAttr("disabled");

	});
}

function show(checkid) {
	var s = "#check_" + checkid;
	/*选子节点*/
	var nodes = $("#fileTable").treegrid("getChildren", checkid);
	if (nodes != null) {
		for (i = 0; i < nodes.length; i++) {
			$(("#check_" + nodes[i].id))[0].checked = $(s)[0].checked;
		}
	}
	var parent;
	//选上级节点
	if ($(s)[0].checked == false) {
		parent = $("#fileTable").treegrid("getParent", checkid);
		if (parent != null) {
			$(("#check_" + parent.id))[0].checked = false;
		}

	} else {
		parent = $("#fileTable").treegrid("getParent", checkid);
		var flag = true;
		if (parent != null) {
			var sons = parent.children;
			for (i = 0; i < sons.length; i++) {
				if ($(("#check_" + $(s).attr("id"))).checked == false) {
					flag = false;
					break;
				}
			}
			if (flag) {
				$(("#check_" + parent.id))[0].checked = true;
			}
		}

	}
}

function exportFile() {

	var j = 0;
	var id = 1, checkid;
	var childrenId = [];
	for (id = 1; ; id++) {
		var nodes = $("#fileTable").treegrid("getChildren", id);
		if (nodes != "") {
			//parentId = id;
			for (i = 1; i <= nodes.length; i++) {
				childrenId[j] = id + "" + i;
				j++;
			}
		} else if (nodes.length == 0) {
			break;
		}
	}
	var gzFile = [];
	j = 0;
	for (i = 0; i < childrenId.length; i++) {
		var checkId = "check_" + childrenId[i];
		var status = $("#" + checkId).is(":checked");
		if (status) {
			var text = $("#" + checkId).parent().text();
			gzFile[j] = text + ";";
			j++;
		}
	}
	/*params = {
		file: gzFile
	};*/
	var node = $("#node option:selected");
	var remoteIp = node.val();
	params = {
		remoteIp:remoteIp,
		file:gzFile,
		type:$("#logType").val()
	};
	$.post('dataSourceManage/scpfiles', params, function(data) {
		download(data);
		// fileDownload(data);
		$("#CTRLoading").css("display", "none");
	})
	//var userName = node.attr("data-sshUserName");
	//var userPassword = node.attr("data-sshPassword");

	/*if(remoteIp == "10.40.57.189") { //机房ip
	 remoteIp = "7.140.28.88:805";
	 }else if(remoteIp == "localhost"){
	 remoteIp = "7.140.28.88:803";
	 }else if(remoteIp == "10.40.48.244"){ //南通224
	 remoteIp = "7.140.28.88:808";
	 }else if(remoteIp == "10.40.48.245"){ //南通224
	 remoteIp = "7.140.28.88:807";
	 }  */
	// var routeIp = "http://" + remoteIp + "/mongs_web/SystemManager/copyFiles.php?file=" + gzFile;

	//chrome和firefox都适用。改成download() chrome浏览器会无法下载文件。
	//window.open("http://10.40.57.189/mongs_web/SystemManager/copyFiles.php?file="+gzFile);
	//都可以了
	// window.open(routeIp);
	//window.open("http://7.140.28.88:805/mongs_web/SystemManager/copyFiles.php?file="+gzFile);

	$("#CTRLoading").css("display", "none");
	return;
}
function onlineStorage() {
	var type = window.location.search.split("=")[1];
	var j = 0;
	var id = 1, checkid;
	var childrenId = [];
	for (id = 1; ; id++) {
		var nodes = $("#fileTable").treegrid("getChildren", id);
		if (nodes != "") {
			//parentId = id;
			for (i = 1; i <= nodes.length; i++) {
				childrenId[j] = id + "" + i;
				j++;
			}
		} else if (nodes.length == 0) {
			break;
		}
	}
	var gzFile = [];
	j = 0;
	for (i = 0; i < childrenId.length; i++) {
		var checkId = "check_" + childrenId[i];
		var status = $("#" + checkId).is(":checked");
		if (status) {
			var text = $("#" + checkId).parent().text();
			gzFile[j] = text;
			j++;
		}
	}
	var node = $("#node option:selected");
	var remoteIp = node.val();
	if (gzFile.length == 0) {
		//alert("请选择要在线入库的数据源");
		layer.open({
			title: "提示",
			content: "请选择要在线入库的数据源"
		});
		return;
	}
	var params = {
		type: type,
		gzFiles: gzFile.join(";;"),
		remoteIp: remoteIp,
		baseStation: $("#cellInput").val(),
	};
	var storageBtn = Ladda.create(document.getElementById("storageBtn"));
	storageBtn.start();
	$.post("dataSourceManage/onlineStorage", params, function (data) {
		if (data) {
			storageBtn.stop();
			//alert("入库完成，文件夹名为"+data);
			//window.localStorage.setItem("storageName", data);
			//window.close();
			layer.open({
				title: "提示",
				content: "请选择要在线入库的数据源",
				yes: function (index, layero) {
					window.localStorage.setItem("storageName", data);
					window.close();
					layer.close(index);
				},
				cancel: function (index, layero) {
					window.localStorage.setItem("storageName", data);
					window.close();
					layer.close(index);
				}
			});
		}

	});

}
var Path;
function storage() {
	var type = $("#logType").val();
	var j = 0;
	var id = 1, checkid;
	var childrenId = [];
	for (id = 1; ; id++) {
		var nodes = $("#fileTable").treegrid("getChildren", id);
		if (nodes != "") {
			//parentId = id;
			for (i = 1; i <= nodes.length; i++) {
				childrenId[j] = id + "" + i;
				j++;
			}
		} else if (nodes.length == 0) {
			break;
		}
	}
	var gzFile = [];
	j = 0;
	for (i = 0; i < childrenId.length; i++) {
		var checkId = "check_" + childrenId[i];
		var status = $("#" + checkId).is(":checked");
		if (status) {
			var text = $("#" + checkId).parent().text();
			gzFile[j] = text;
			j++;
		}
	}
	// var city = $("#node").val();
	var node = $("#node option:selected");
	var remoteIp = node.val();
	var fileName = $("#fileName").val();
	var fileDir = node.attr("data-fileDir");
	if (gzFile.length == 0) {
		//alert("请选择要在线入库的数据源");
		layer.open({
			title: "提示",
			content: "请选择要在线入库的数据源"
		});
		return;
	}
	var params = {
		type: type,
		gzFiles: gzFile.join(";;"),
		remoteIp: remoteIp,
		baseStation: $("#cellInput").val(),
		fileDir: fileDir,
		fileName: fileName
				// city : city
	};
	var storage = Ladda.create(document.getElementById("storage"));
	storage.start();
	$.post("dataSourceManage/storage", params, function (data) {
		if (data) {
			storage.stop();
			// alert("入库完成，文件夹名为"+data);
			// window.localStorage.setItem("storageName", data);
			// window.close();
			// Path = "/data/trace/" + type + "/" + data;
			Path = data;
			if (type == "ctr") {
				$("#ctrTypeDiv").show();
			} else {
				$("#ctrTypeDiv").hide();
			}
			$("#add_task").modal();
			$("#taskName_form").data("bootstrapValidator").destroy();
			initValidata();
		}

	});

}
function saveTask() {
	var taskName = $("#taskName").val().trim();
	$("#taskName_form").data("bootstrapValidator").validate();
	var flag = $("#taskName_form").data("bootstrapValidator").isValid();
	if (!flag) {
		return;
	}
	var tracePath = Path;
	var taskType = $("#logType").val();
	if (taskType == "kget") {
		taskType = "parameter";
	} else if (taskType == "ctr") {
		taskType = "ctrsystem";
	} else if (taskType == "cdr") {
		taskType = "cdrsystem";
	} else if (taskType == "ebm") {
		taskType = "ebmsystem";
	} else if (taskType == "pcap") {
		taskType = "pcapsystem";
		// } else if (taskType == "ctrfull") {
		// 	taskType = "ctrfullsystem";
	}
	prepareTask(taskName, tracePath, taskType);
}
function prepareTask(name, tracePath, type) {
	var myDate = new Date().Format("yyyy-MM-dd hh:mm:ss");
	var data = {
		"tracePath": encodeURI(tracePath),
		"taskName": encodeURI(name),
		"createTime": myDate,
		"type": type

	};
	/*if(type == "ctrsystem" | type == "cdrsystem" | type == "ebmsystem" | type == "ctrfullsystem"){
	 console.log(type);
	 var treeNodes=$("#eventQueryTree").treeview("getNode",0);
	 data.taskConfig = JSON.stringify(treeNodes);
	 }*/
	var saveBtn = Ladda.create(document.getElementById("saveBtn"));
	var cancelBtn2 = Ladda.create(document.getElementById("cancelBtn2"));
	saveBtn.start();
	cancelBtn2.start();
	$.ajax({
		type: "POST",
		url: "dataSourceManage/addTask",
		data: data,
		async: true,
		success: function (returnData) {
			console.log(returnData);
			returnData = JSON.parse(returnData);
			if (returnData.state == 0) {
				runTask(name, tracePath, type, saveBtn, cancelBtn2);
			} else if (returnData.state == 1) {
				//alert("任务名重复，请重新输入");
				layer.open({
					title: "提示",
					content: "任务名重复，请重新输入"
				});
				saveBtn.stop();
				cancelBtn2.stop();
				return;
			} else {
				//alert("新增失败，请重试");
				layer.open({
					title: "提示",
					content: "新增失败，请重试"
				});
				saveBtn.stop();
				cancelBtn2.stop();
				return;
			}
		}
	});
}
function runTask(taskName, tracePath, type, saveBtn, cancelBtn2) {
	var myDate = new Date().Format("yyyy-MM-dd hh:mm:ss");
	var ctrFlag = $(".ctrType:checked").val();
	var data = {
		"taskName": taskName,
		"tracePath": tracePath,
		"startTime": myDate,
		"type": type,
		"ctrFlag": ctrFlag
	};

	taskMonitor = setInterval(function () {
		updateMonitor(taskName);
	}, 2000);
	$.ajax({
		type: "get",
		url: "dataSourceManage/runTask",
		data: data,
		async: true,
		success: function (data) {
			saveBtn.stop();
			cancelBtn2.stop();
			clearInterval(taskMonitor);
			var returnData = eval("(" + data + ")");
			if (returnData.status == "true") {
				//alert("入库成功");
				layer.open({
					title: "提示",
					content: "入库成功"
				});
				$.post("dataSourceManage/deleteAutoDir", {"tracePath": tracePath});
			} else if (returnData.status == "abort") {
				//alert("入库失败，请查看日志");
				layer.open({
					title: "提示",
					content: "入库失败，请查看日志"
				});
			}
		}
	});
}
function updateMonitor(taskName) {
	var data = {"taskName": encodeURI(taskName)};
	$.ajax({
		type: "get",
		url: "dataSourceManage/monitor",
		data: data,
		async: true,
		success: function (returnData) {
			$("#log").html(returnData);
			$("#log").parent().scrollTop($("#log").height());
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
	if (/(y+)/.test(fmt))
		fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
	for (var k in o)
		if (new RegExp("(" + k + ")").test(fmt))
			fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
	return fmt;
};
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
/*function setChooseEventTree(){
 $.get("common/json/taskConfigData.json",null,function(data){
 var options = {
 bootstrap2: false,
 showTags: true,
 levels: 2,
 showCheckbox: true,
 data:eval("("+data+")"),
 onNodeChecked :function(event,data){
 checkAllChildren(data);
 checkParent(data);
 },
 onNodeUnchecked : function(event,data){
 unCheckAllChildren(data);
 unCheckParent(data);
 }
 };
 $("#eventQueryTree").treeview(options);
 });
 }
 
 function checkAllChildren(node) {
 var children = node.nodes;
 if(children){
 var len = children.length;
 for(var i = 0;i < len;i++){
 $("#eventQueryTree").treeview("checkNode", [ children[i].nodeId, { silent: false } ]);
 }
 }
 }
 function unCheckAllChildren(node) {
 var children = node.nodes;
 if(children){
 var len = children.length;
 for(var i = 0;i < len;i++){
 $("#eventQueryTree").treeview("uncheckNode", [ children[i].nodeId, { silent: false } ]);
 }
 }
 }
 function unCheckParent(node) {
 var parentId = node.parentId;
 if(parentId != undefined){
 $("#eventQueryTree").treeview("uncheckNode", [parentId, { silent: true } ]);
 var parentNode = $("#eventQueryTree").treeview("getNode", parentId);
 unCheckParent(parentNode);
 }
 }
 
 function checkParent(node){
 var parentId = node.parentId;
 if(parentId != undefined){
 var parentNode = $("#eventQueryTree").treeview("getNode", parentId);
 var children = parentNode.nodes;
 var len = children.length;
 for(var i = 0;i < len;i++){
 if(children[i].state.checked == false){
 return;
 }
 }
 $("#eventQueryTree").treeview("checkNode", [ parentId, { silent: true } ]);
 checkParent(parentNode);
 }
 }	*/

function toName(self) {
	$.ajaxFileUpload({
		url: "dataSourceManage/uploadFile",
		//data : data,
		fileElementId: "fileImport",
		secureuri: false,
		dataType: "json",
		type: "post",
		success: function (data, status) {
			$("#cellInput").val(data);
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
