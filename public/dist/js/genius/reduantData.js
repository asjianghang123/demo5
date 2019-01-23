$(document).ready(function () {
	toogle("reduantData");
	//setTree();

});
var importKPIName='';
var import4GName='';

function exportTemplate() {
	$.get("ReduantData/downloadTemplateFile",function(data){

		if (data.result) {
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

function importTemplate(){

	$("#import_modal").modal();
	$("#fileImportName").val("");
	$("#fileImport").val("");
}
function toName(self) {
	$("#fileImportName").val(self.value);
}

function importFile(){
	if($("#fileImportName").val()==''){
		layer.open({
						title: "提示",
						content: "请选择要上传的文件"
				});
		return;
	}
	var data = {
		"filename": $("#fileImportName")
	};
	var E = Ladda.create(document.getElementById("importBtn"));
	E.start();
	$("#cancelBtn").click(function(){
			E.stop();
	})

	$.ajaxFileUpload({
		url :"ReduantData/uploadFile",
		type : "POST",
		dataType: "json",
		fileElementId :"fileImport",
		success:function(data){
			if(data=='lenError'){
				layer.open({
						title: "提示",
						content: "没有数据"
				});
			 E.stop();

			}
		 loadCell(JSON.parse(data).cellInfo)
		 load4GInfo(JSON.parse(data).Info);
		 	 E.stop();
		 	 	layer.open({
				title: "提示",
				content: "上传成功"
			});
		$("#import_modal").modal("hide");
	

		},
		error: function (data, status, e) {
			//alert("上传失败");
		 	E.stop();
			
			layer.open({
				title: "提示",
				content: "上传失败"
			});
		}

	})

}

function loadCell(data){

		var fieldArr = [];
		var text = data.text.split(",");
		console.log()
		for (var i in data.rows[0]) {
			if (fieldArr.length == 0) {
				fieldArr[fieldArr.length] = {field: i, title: text[fieldArr.length], width: 150};
			}else {
				fieldArr[fieldArr.length] = {field: i, title: text[fieldArr.length], width: 150};
			}

		}
		importKPIName = data.filename;
		var newData = data.rows;
		$("#KPITable").grid("destroy", true, true);
		$("#KPITable").grid({
			columns: fieldArr,
			dataSource: newData,
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap",
			primaryKey: "id"
		});


}
function load4GInfo(data){

		var fieldArr = [];
		var text = data.text.split(",");
		console.log()
		for (var i in data.rows[0]) {
			if (fieldArr.length == 0) {
				fieldArr[fieldArr.length] = {field: i, title: text[fieldArr.length], width: 150};
			}else {
				fieldArr[fieldArr.length] = {field: i, title: text[fieldArr.length], width: 185};
			}

		}
		import4GName = data.filename;
		var newData = data.rows;
		$("#4GTable").grid("destroy", true, true);
		$("#4GTable").grid({
			columns: fieldArr,
			dataSource: newData,
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap",
			primaryKey: "id"
		});


}

function importKPI(){
	if(importKPIName){
		fileDownload(importKPIName);
	}else{
		layer.open({
				title: "提示",
				content: "没有数据"
		});
	}
}

function import4G(){
	if(import4GName){
		fileDownload(import4GName);
	}else{
		layer.open({
				title: "提示",
				content: "没有数据"
		});
	}
}