$(document).ready(function () {
	// initValidata_ManageData();
	searchManage();
	getAllTypes();
	toogle("TemplateManage");
	$("#allTypes").change(function(){
		searchManage();
	});

});
var ManageTable;
function importQuery() {
	$("#import_modal").modal();
	console.log($("#queryName").val());
	$("#fileImportName").val("");
	$("#fileImports").val("");
}
function toName(self) {
	$("#fileImportName").val(self.value);
}
function importFile() {
	var data ={
		"filename":$("#fileImportName").val(),
		"type":$("#queryName").val()
	};
	$.ajaxFileUpload({
		url: "TemplateManage/uplodeFile",
		type:"POST",
		fileElementId: "fileImport",
		secureuri: false,
		data:data,
		dataType: "json",
			success: function (data) {
				if(data=="lenError"){
					layer.open({
						title: "提示",
						content: "没有数据或没有表头"
						});
				}else if(data=="emptyError"){
					layer.open({
						title: "提示",
						content: "请选择要上传的文件"
						});
				}else if(data=="dataError"){
					layer.open({
						title: "提示",
						content: "表格列数缺失"
						});
				}else{
					$("#import_modal").modal("hide");

				layer.open({
					title: "提示",
					content: "上传成功"
				});
				searchManage();
				getAllTypes();
				}

		}
	});
}
function getAllTypes()
{
	$("#allTypes").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "请选择模版类型",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有平台类型",
		maxHeight: 200,
		maxWidth: "100%"
	});
	var url = "TemplateManage/getAllTypes";

	$.ajax({
		type: "GET",
		url: url,
		dataType: "json",
		success: function (data) {
			console.log(data);
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
			$("#allTypes").multiselect("dataprovider", newOptions);
		}
	});
}
//文件导出
function exportManage() {
	var data ={
		"type":$("#queryName").val()
	};
	$.get("TemplateManage/downloadFile",data, function (data) {
	
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

//查询模版信息
function searchManage()
{	
	var type = $("#allTypes").val();
	console.log(type);
	$("#deleteData").val("");
	var data ={
		"type":type
	};
	$.get("TemplateManage/getManageDate",data, function (data) {
		var fieldArr = [];
		var text = (JSON.parse(data).text).split(",");
		for (var i in JSON.parse(data).rows[0]) {
			if (fieldArr.length == 0) {
				fieldArr[fieldArr.length] = {field: text[fieldArr.length], title: text[fieldArr.length], width: 50};
			} else if (text[fieldArr.length] == "kpiFormula") {
				fieldArr[fieldArr.length] = {field: text[fieldArr.length], title: text[fieldArr.length], width: 250};
			} else {
				fieldArr[fieldArr.length] = {field: text[fieldArr.length], title: text[fieldArr.length], width: 100};
			}

		}
		var newData = JSON.parse(data).rows;
		$("#ManageTable").grid("destroy", true, true);
		ManageTable=$("#ManageTable").grid({
			columns: fieldArr,
			dataSource: newData,
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap",
			primaryKey: "id"
		});
		ManageTable.on("rowSelect",function (e,$row,id,record){
			$("#deleteData").val(record.id);
			editManage(record);
		});

	});
}

function deleteData()
{
	var id = $("#deleteData").val();
		if(!id)
		{
		layer.open({
				title:"提示",
				content: "请选中要删除的列"
			});
		return;
		}
	var data={
		"id":id
	};

	layer.confirm("确认删除吗？",{title:"提示"},function(index){
		$.get("TemplateManage/deleteData",data,function(data){
			if(data){
				layer.open({
					title:"提示",
					content: "删除成功"
				});
				
				searchManage();
			}else{
				layer.open({
					title:"提示",
					content: "数据不存在"
				});
				searchManage();
				
			}
		});
	});
}
function addManage()
{	
	searchManage();
	$("#add_edit_formula").modal();
	$("#add_edit_formula input").val("");
	$("#add_edit_formula textarea").val("");
	$("#deleteData").val("");
	initValidata_ManageData();
}
function editManage(data)
{
	$("#formulaId").val(data.id);
	$("#type").val(data.type);
	$("#dataSource").val(data.dataSource);
	$("#templateName").val(data.templateName);
	$("#name").val(data.kpiName);
	$("#precision").val(data.kpiPrecision);
	$("#formula").val(data.kpiFormula);
	initValidata_ManageData();
}

function updManage()
{	var id = $("#deleteData").val();
		if(!id)
		{
		layer.open({
				title:"提示",
				content: "请选中要编辑的列"
			});
		return;
		}
	$("#add_edit_formula").modal();
}
function updateManage()
{
	$("#formulaForm").data("bootstrapValidator").validate();
	var flag = $("#formulaForm").data("bootstrapValidator").isValid();
	if (!flag) {
		return;
	}

	var params = $("#formulaForm").serialize().split("&");
	var data = {};
	for (var i = 0; i < params.length; i++) {
		data[params[i].split("=")[0]] = decodeURIComponent(params[i].split("=")[1], true);
	}
	$.get("TemplateManage/updateManage", data, function (res) {
	$("#formulaForm").data("bootstrapValidator").destroy();
		if (res) {
			//alert("success！");
			layer.open({
				title: "提示",
				content: "修改成功"
			});
			searchManage();
		} else {
			//alert("没有权限修改该公式!");
			layer.open({
				title: "提示",
				content: "数据已经存在"
			});
		}
		searchManage();
		$("#add_edit_formula").modal("hide");

	});
}

function initValidata_ManageData() {
	$("#formulaForm").bootstrapValidator({
		message: "This value is not valid",
		feedbackIcons: {
			valid: "glyphicon glyphicon-ok",
			invalid: "glyphicon glyphicon-remove",
			validating: "glyphicon glyphicon-refresh"
		},
		fields: {
			name: {
				//message: "name验证失败",
				validators: {
					notEmpty: {
						message: "Name不能为空"
					}
				}
			},
			type: {
				//message: "type验证失败",
				validators: {
					notEmpty: {
						message: "type不能为空"
					}
				}
			},
			dataSource: {
				//message: "dataSource验证失败",
				validators: {
					notEmpty: {
						message: "dataSource不能为空"
					}
				}
			},
			templateName: {
				//message: "用户名验证失败",
				validators: {
					notEmpty: {
						message: "templateName不能为空"
					}
				}
			},
			precision: {
				//message: "密码验证失败",
				validators: {
					notEmpty: {
						message: "Precision不能为空"
					}
				}
			},
			formula: {
				//message: "用户类型验证失败",
				validators: {
					notEmpty: {
						message: "Formula不能为空"
					}
				}
			}
		}
	});
}