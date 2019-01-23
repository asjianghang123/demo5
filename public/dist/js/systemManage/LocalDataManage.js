$(document).ready(function () {
	toogle("LocalDataManage");
	setTree();
});
// var taskMonitor;
function setTree() {
	$.get("LocalDataManage/getDir",null,function(data){
		data = JSON.parse(data);
		var options = {
			bootstrap2: false,
			showTags: true,
			levels: 2,
			data: data
		};
		$("#logTypeTree").treeview(options);
		$("#logTypeTree").on("nodeSelected", function(event, data) {
			getFileByDir(data.path);
			if (data.type == "ctr") {
				getLogByDir(data.path);
			}
		});
	});
}
function addDir(){
	var selectedNode = $("#logTypeTree").treeview("getSelected")[0];
	if(!selectedNode){
		layer.open({
			title: "提示",
			content: "请选择目录"
		});
		return;
	}
	layer.prompt({title: "输入目录名称", formType: 2}, function(dirName, index){
		var regex = /^[a-zA-Z0-9_$]+$/;  
		if(!regex.test(dirName)){
			layer.open({
				title: "提示",
				content: "请输入正确的目录"
			});
			return;
		}
		layer.close(index);
		var path = selectedNode.path+"/"+dirName;
		$.post("LocalDataManage/addDir",{"path":path},function(data){
			if(data){
				layer.open({
					title: "提示",
					content: "新增目录成功"
				});
				setTree();
			}else{
				layer.open({
					title: "提示",
					content: "已存在该目录"
				});
			}
		});
	});
}
function deleteDir(){
	var selectedNode = $("#logTypeTree").treeview("getSelected")[0];
	if(!selectedNode){
		layer.open({
			title: "提示",
			content: "请选择要删除的目录"
		});
	}
	var path = selectedNode.path;
	layer.confirm("确认删除" + path + "目录吗？", {title: "提示"}, function (index) {
		var params = {"path": path};
		$.post("LocalDataManage/deleteDir",params,function(data){
			if(data){
				layer.open({
					title: "提示",
					content: "删除目录成功"
				});
				setTree();
			}else{
				layer.open({
					title: "提示",
					content: "该目录已删除"
				});
			}
		});
		layer.close(index);
	});
}
function updateLog() {
	var selectedNode = $("#logTypeTree").treeview("getSelected")[0];
	if(!selectedNode){
		layer.open({
			title: "提示",
			content: "请选择目录"
		});
		return;
	}
	$("#updateLog_modal").modal();
}
function toName(self) {
	var fileList = self.files;
	var name = fileList[0].name;
	$(self).prev().val(name);
}
function addFileInput() {
	var n = $(".newFileInput").length;
	if (n != 0) {
		if (!$("#fileImportName_" + n).val()) {
			return;
		}
	}
	var html = '<div class="input-group newFileInput">' +
		'<input type="text" class="form-control" id="fileImportName_' + (n + 1) + '">' +
		'<input type="file" accept=".gz" class="hidden fileImport" name="fileImport_' + (n + 1) + '" id="fileImport_' + (n + 1) + '" onchange="toName(this)">' +
		'<span class="input-group-btn">' +
		'<button class="btn btn-default" type="button" onclick="fileImport_' + (n + 1) + '.click()">选择文件</button>' +
		'</span>' +
		'</div>';
	$("#filesListDiv").append(html);
}
function importFile() {
	var selectedNode = $("#logTypeTree").treeview("getSelected")[0];
	var dirName = selectedNode.path;
	var type = selectedNode.type;
	var params = {
		type: type,
		dirName: dirName
	};
	var length = $(".fileImport").length;
	var sum = 0;
	var num = 0;
	var flag = true;
	$(".fileImport").each(function () {
		if ($(this).val()) {
			sum++;
		}
	});
	var logs = [];
	var importBtn = Ladda.create(document.getElementById("importBtn"));
	var cancelBtn = Ladda.create(document.getElementById("cancelBtn"));
	importBtn.start();
	cancelBtn.start();
	for (var i = 0; i < length; i++) {
		if ($("#fileImport_" + i).val()) {
			params.id = "fileImport_" + i;
			$("#fileImportName_" + i).val("");
			$.ajaxFileUpload({
				url: "LocalDataManage/uploadFile",
				data: params,
				secureuri: false,
				fileElementId: "fileImport_" + i,
				secureuri: false,
				dataType: "json",
				type: "post",
				success: function (data, status) {
					num++;
					if (data == "error") {
						flag = false;
					} else {
						logs.push({logName: data});
					}
					if (num == sum) {
						importBtn.stop();
						cancelBtn.stop();
						if (flag) {
							//alert("上传成功");
							layer.open({
								title: "提示",
								content: "上传成功"
							});
							$("#updateLog_modal").modal("hide");
							$(".newFileInput").remove();
							getFileByDir(dirName);
						} else {
							layer.open({
								title: "提示",
								content: "上传失败，请重试"
							});
						}
					}
				},
				error: function (data, status, e) {
					num++;
					flag = false;
					if (num == sum) {
						importBtn.stop();
						cancelBtn.stop();
						layer.open({
							title: "提示",
							content: "上传失败，请重试"
						});
					}

				}
			});
		} else {
			layer.open({
				title: "提示",
				content: "请选择上传文件"
			});
			importBtn.stop();
			cancelBtn.stop();
		}
	}
}
function showLog(dirName,data) {
	var fieldArr = [];
	fieldArr[0] = {field: "text", title: "当前目录：" + dirName, width: 250};
	$("#dirTable").grid("destroy", true, true);
	var grid = $("#dirTable").grid({
		columns: fieldArr,
		dataSource: data,
		params: [],
		pager: {limit: 10, sizes: [10, 20, 50, 100]},
		autoScroll: true,
		uiLibrary: "bootstrap",
		// primaryKey: "logName",
		autoLoad: true
	});
}
function showLog2(dirName,data) {
	var fieldArr = [];
	fieldArr[0] = {field: "text", title: "当前目录：" + dirName, width: 250};
	$("#outputTable").grid("destroy", true, true);
	var grid = $("#outputTable").grid({
		columns: fieldArr,
		dataSource: data,
		params: [],
		pager: {limit: 10, sizes: [10, 20, 50, 100]},
		autoScroll: true,
		uiLibrary: "bootstrap",
		// primaryKey: "logName",
		autoLoad: true
	});
}

function getFileByDir(path){
	$.post("LocalDataManage/getFileByDir",{path:path},function(data){
		data = JSON.parse(data);
		showLog(path,data);
	});
}
function getLogByDir(path){
	$.post("LocalDataManage/getLogByDir",{path:path},function(data){
		data = JSON.parse(data);
		$("#outputDir").val(data.dir);
		showLog2(data.dir,data.content);
	});
}
function analysisLog(){
	var selectedNode = $("#logTypeTree").treeview("getSelected")[0];
	if(!selectedNode){
		layer.open({
			title: "提示",
			content: "请选择目录进行解析"
		});
		return;
	}
	if (selectedNode.type != "ctr") {
		layer.open({
			title: "提示",
			content: "只能针对ctr类型的log进行解析"
		});
		return;
	}
	var analysisLogBtn = Ladda.create(document.getElementById("analysisLogBtn"));
	var downloadOutputBtn = Ladda.create(document.getElementById("downloadOutputBtn"));
	analysisLogBtn.start();
	downloadOutputBtn.start();
	var path = selectedNode.path;
	$.post("LocalDataManage/analysisLog",{path:path},function(data){
		analysisLogBtn.stop();
		downloadOutputBtn.stop();
		layer.open({
			title: "提示",
			content: "解析完成"
		});
		getLogByDir(path);
	});
}
function downloadOutput() {
	var dirName = $("#outputDir").val();
	if (dirName) {
		fileSave(dirName);
	} else {
		layer.open({
			title: "提示",
			content: "请选择目录"
		});
		return;
	}
}

function fileSave(fileName) {
	if (fileName != "") {
		layer.open({
			title: "提示",
			content: fileName,
			yes:function(index, layero){
				var fileNames = csvZipDownload(fileName);
				//alert("success");
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
	}
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