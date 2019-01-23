$(function () {
	setTree();
	$("#LTEQueryMoTree").treeview("collapseAll", {silent: true});
	initElementTree();

	setFormulaTreeTable();

	initValidata_mode();
	initValidata_mode_copy();
	initValidata_formula();
});

function setTree() {
	var tree = "#LTEQueryMoTree";
	$(tree).treeview({
		data: getTree(),
		onNodeSelected: function (event, data) {
			if (data.parentId || data.parentId == 0) {
				var user = $(tree).treeview("getNode", data.parentId).text;
				setElementTree(data.id);
			} else {
				$("#LTEElementTree").empty();
			}

		}
	}); //树
}

function getTree() {
	var url = "LTETemplateManage/getLTETreeData";
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
function clearLteQuery() {
	$("#paramQueryMoErbs").val("");
	setTree();
	$("#LTEQueryMoTree").treeview("collapseAll", {silent: true});
}

//筛选模板树
function searchLTEQuery() {
	var inputData = $("#paramQueryMoErbs").val();
	inputData = $.trim(inputData);
	if (inputData == "") {
		setTree();
		return;
	}
	var params = {
		inputData: inputData
	};
	var url = "LTETemplateManage/searchLTETreeData";
	//var treeData;

	$.get(url, params, function (data) {
		//data = "["+data+"]";
		var tree = "#LTEQueryMoTree";
		$(tree).treeview({
			data: data,
			onNodeSelected: function (event, data) {
				if (data.parentId || data.parentId == 0) {
					var user = $(tree).treeview("getNode", data.parentId).text;
					setElementTree(data.id);
				} else {
					$("#LTEElementTree").empty();
				}
				//var user = data.user;
				//setElementTree(data.text,user);

			}
		});
		$("#LTEQueryMoTree").treeview("collapseAll", {silent: true});
	});
}
function initElementTree() {
	var tree = "#LTEElementTree";
	$(tree).treeview({
		data: null,
		multiSelect:true
	}); //树
}
function setElementTree(id) {
	var tree = "#LTEElementTree";
	$(tree).treeview({
		data: getElementTree(id),
		multiSelect:true,
		onNodeSelected: function (event, data) {
			findTemplateById(data.id);
		}
	}); //树
}
function getElementTree(id) {
	var url = "LTETemplateManage/getElementTree";
	var treeData;
	$.ajax({
		type: "GET",
		url: url,
		data: {
			templateId: id
		},
		dataType: "json",
		async: false,
		success: function (data) {
			$("#elementIds").val(data.elementId);
			treeData = getKpiNamebyId(data.elementId);
		}
	});
	return treeData;
}
function getKpiNamebyId(id) {
	var url = "LTETemplateManage/getKpiNamebyId";
	var treeData;
	$.ajax({
		type: "GET",
		url: url,
		data: {
			id: id
		},
		dataType: "json",
		async: false,
		success: function (data) {
			treeData = data;
			var ids = [];
			for(var i in data){
				ids.push(data[i].id);
			}
			$("#elementIds").val(ids.join(","));
		}
	});
	return treeData;
}

function setFormulaTreeTable() {


	var table = "#LTEFormulaTable";
	$(table).treegrid({
		url: "LTETemplateManage/getTreeTemplate",
		idField: "id",
		treeField: "kpiName",
		singleSelect : false,
		columns: [[
			{title: "Name", field: "kpiName", width: 150},
			{field: "kpiFormula", title: "Formula", width: 350},
			{field: "kpiPrecision", title: "Precision", width: 100}
		]],
		onDblClickRow: function (data) {
			if (!data.children) {
				editFormula(data);
			}
		}
	}); //树
}

//公式导入
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
	}
	$.ajaxFileUpload({
	
		url: "LTETemplateManage/uplodeFile",
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
				parent.location.reload(); 
				searchManage();
				getAllTypes();
				}

		}
	});
}

//公式导出
function exportFormula() {
	$.get("LTETemplateManage/downloadFile", function (data) {

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

function addFormula() {
	$("#add_edit_formula").modal();

	$("#add_edit_formula textarea").val("");
	var defaultvalue = $('#precision').val();
	$("#precision").focus(function(){
		if ($(this).val() ==defaultvalue) {
			$(this).val("");
		}
	});
	$("#precision").blur(function(){
		console.log($(this).val());
		if ($(this).val()== "") {
			$(this).val(defaultvalue);
		}
	});
	$("#formulaForm").data("bootstrapValidator").destroy();
	initValidata_formula();
}

function changeMode() {
	if (!$("#LTEQueryMoTree").treeview("getSelected")[0] || !$("#LTEQueryMoTree").treeview("getSelected")[0].id) {
		//alert("尚未选择模板");
		layer.open({
			title: "提示",
			content: "尚未选择模板"
		});
		return;
	}
	$("#change_edit_mode").modal();
	$("#change_edit_mode input").val("");
	$("#modeChange").data("bootstrapValidator").destroy(); 
	initValidata_changemode();
}

function editFormula(data) {
	$("#add_edit_formula").modal();

	$("#formulaId").val(data.id);
	//$("#formulaUser").val(data.user);
	$("#name").val(data.kpiName);
	$("#precision").val(data.kpiPrecision);
	$("#formula").val(data.kpiFormula);

	$("#formulaForm").data("bootstrapValidator").destroy();
	initValidata_formula();
}

function updateFormula() {
	$("#formulaForm").data("bootstrapValidator").validate();
	var flag = $("#formulaForm").data("bootstrapValidator").isValid();
	if (!flag) {
		return;
	}
	var param = $("#formulaForm").serialize().replace(/\+/g," ");
	var params = param.split("&");
	//var params = $("#formulaForm").serialize().split("&");
	var data = {};
	for (var i = 0; i < params.length; i++) {
		data[params[i].split("=")[0]] = decodeURIComponent(params[i].split("=")[1], true);
	}

	$.get("LTETemplateManage/updateFormula", data, function (res) {

		var result = JSON.parse(res);
		if (result.error) {
			//alert("success！");
			layer.open({
				title: "提示",
				content: result.error
			});
		} else {
			//alert("没有权限修改该公式!");
			layer.open({
				title: "提示",
				content: "创建成功"
			});
			setFormulaTreeTable();
		}
		$("#add_edit_formula").modal("hide");

	});
}

function updateChangemode() {
	var newModeName = $("#modename").val();
	newModeName = $.trim(newModeName);
	var modeName = $("#LTEQueryMoTree").treeview("getSelected")[0].text;
	$.get("LTETemplateManage/updateNewMode", {"oldname": modeName,"newname": newModeName}, function (res) {
		if (res == "login") {
			//alert("尚未登录，不能添加模板");
			layer.open({
				title: "提示",
				content: "尚未登录，不能添加模板"
			});
			window.location.href = "login";
			return;
		}
		if (res == "名称已有") {
			//alert("已存在该模板，请重新输入");
			layer.open({
				title: "提示",
				content: "已存在该模板，请重新输入"
			});
			return;
		}
		if (res) {
			//alert("添加成功！");
			layer.open({
				title: "提示",
				content: "添加成功"
			});
			// setTree();
			var params = {
				inputData: $("#modename").val()
			};
			var url = "LTETemplateManage/searchLTETreeData";
			//var treeData;

			$.get(url, params, function (data) {
				//data = "["+data+"]";
				var tree = "#LTEQueryMoTree";
				$(tree).treeview({
					data: data,
					onNodeSelected: function (event, data) {
						console.log(data);
						if (data.parentId || data.parentId == 0) {
							var user = $(tree).treeview("getNode", data.parentId).text;
							setElementTree(data.id);
						} else {
							$("#LTEElementTree").empty();
						}
						//var user = data.user;
						//setElementTree(data.text,user);

					}
				});
				$("#LTEQueryMoTree").treeview("collapseAll", {silent: true});
			});
		} else {
			//alert("添加失败!");
			layer.open({
				title: "提示",
				content: "添加失败"
			});
		}
		$("#change_edit_mode").modal("hide");

	});
}

function deleteFormula() {
	var selectedNode = $("#LTEFormulaTable").treegrid("getChecked");
	// console.log(selectedNode[0]);
	// console.log(selectedNode.length);
	if (!selectedNode || selectedNode.children) {
		//alert("请选择要删除的Formula");
		layer.open({
			title: "提示",
			content: "请选择要删除的Formula"
		});
		return;
	}
	if (JSON.stringify(selectedNode) === '[]') {
		//alert("请选择要删除的Formula");
		layer.open({
			title: "提示",
			content: "请选择要删除的Formula"
		});
		return;
	}
	
	var kpiname = "";
	for (var i=0,j=selectedNode.length;i<j;i++) {
		if (selectedNode.length ==0||selectedNode[i].children) {
			//alert("请选择要删除的Formula");
			let moban = selectedNode[i]["kpiName"];
			layer.open({
				title: "提示",
				content: "不要选中"+moban
			});
			return;
		}
		kpiname = kpiname+selectedNode[i]["kpiName"]+",";
	}
	kpiname = kpiname.substr(0, kpiname.length - 1);
	layer.confirm(kpiname+"确认删除吗？", {title: "提示"}, function (index) {
		$.get("LTETemplateManage/deleteFormula", {"id": selectedNode}, function (res) {
			if (res) {
				layer.open({
					title: "提示",
					content: "删除成功"
				});
				setFormulaTreeTable();
				$('#LTEFormulaTable').treegrid('clearSelections');
			} else {
				layer.open({
					title: "提示",
					content: "没有权限删除该公式"
				});
			}
			$("#add_edit_formula").modal("hide");

		});
		layer.close(index);
	});
	/*var flag = confirm("确认删除吗？");
	 if(!flag){
	 return;
	 }
	 $.get("LTETemplateManage/deleteFormula",{"id":selectedNode.id},function(res){
	 if(res){
	 alert("删除成功！");
	 setFormulaTreeTable();
	 }else{
	 alert("没有权限删除该公式!");
	 }
	 $("#add_edit_formula").modal("hide");

	 });*/
}

function clearFormula() {
	$("#formulaQuery").val("");
	setFormulaTreeTable();
}
function searchFormula() {
	var inputData = $("#formulaQuery").val();
	inputData = $.trim(inputData);
	if (inputData == "") {
		setFormulaTreeTable();
		return;
	}
	var url = "LTETemplateManage/searchTreeTemplate";
	$.ajax({
		url: url,
		type: "get",
		data: {"formula": inputData},
		dataType: "json",
		success: function (data) {
			var table = "#LTEFormulaTable";
			$(table).treegrid("loadData", data);
		}
	});


}
function findTemplateById(id) {
	var data = $("#LTEFormulaTable").treegrid("find", id);
	if (!data) {
		return;
	}
	$("#LTEFormulaTable").treegrid("expandAll");
	$("#LTEFormulaTable").treegrid("select", id);

	var selectedNode = $(".datagrid-row-selected");
	var scroll = selectedNode.offset().top - $("#LTEFormulaTableDiv").offset().top + $("#LTEFormulaTableDiv").scrollTop();
	$("#LTEFormulaTableDiv").scrollTop(scroll);
}


function initValidata_formula() {
	$("#formulaForm").bootstrapValidator({
		message: "This value is not valid",
		feedbackIcons: {
			valid: "glyphicon glyphicon-ok",
			invalid: "glyphicon glyphicon-remove",
			validating: "glyphicon glyphicon-refresh"
		},
		fields: {
			name: {
				//message: "用户名验证失败",
				validators: {
					notEmpty: {
						message: "Name不能为空"
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

function initValidata_changemode() {
	$("#modeChange").bootstrapValidator({
		message: "This value is not valid",
		feedbackIcons: {
			valid: "glyphicon glyphicon-ok",
			invalid: "glyphicon glyphicon-remove",
			validating: "glyphicon glyphicon-refresh"
		},
		fields: {
			name: {
				//message: "用户名验证失败",
				validators: {
					notEmpty: {
						message: "新改的模板名不能为空"
					}
				}
			}
		}
	});
}

function elementUp() {
	if (!$("#LTEQueryMoTree").treeview("getSelected")[0]) {
		//alert("尚未选择模板");
		layer.open({
			title: "提示",
			content: "尚未选择模板"
		});
		return;
	}
	if (!$("#LTEElementTree").treeview("getSelected")[0]) {
		//alert("尚未选择需要上移的指标");
		layer.open({
			title: "提示",
			content: "尚未选择需要上移的指标"
		});
		return;
	}
	elementUpdate("up");
}
function elementDown() {
	if (!$("#LTEQueryMoTree").treeview("getSelected")[0]) {
		//alert("尚未选择模板");
		layer.open({
			title: "提示",
			content: "尚未选择模板"
		});
		return;
	}
	if (!$("#LTEElementTree").treeview("getSelected")[0]) {
		//alert("尚未选择需要下移的指标");
		layer.open({
			title: "提示",
			content: "尚未选择需要下移的指标"
		});
		return;
	}
	elementUpdate("down");
}
function elementDelete() {
	if (!$("#LTEQueryMoTree").treeview("getSelected")[0]) {
		//alert("尚未选择模板");
		layer.open({
			title: "提示",
			content: "尚未选择模板"
		});
		return;
	}
	if (!$("#LTEElementTree").treeview("getSelected")[0]) {
		//alert("尚未选择要删除的指标");
		layer.open({
			title: "提示",
			content: "尚未选择要删除的指标"
		});
		return;
	}
	elementUpdate("delete");
}
function elementAdd() {
	var selectedNode = $("#LTEFormulaTable").treegrid("getChecked");
	if (JSON.stringify(selectedNode) === '[]') {
		//alert("请选择要删除的Formula");
		layer.open({
			title: "提示",
			content: "尚未选择要添加的公式"
		});
		return;
	}
	
	for (var i=0,j=selectedNode.length;i<j;i++) {
		if (selectedNode.length ==0||selectedNode[i].children) {
			//alert("请选择要删除的Formula");
			let moban = selectedNode[i]["kpiName"];
			layer.open({
				title: "提示",
				content: "不要选中"+moban
			});
			return;
		}
	}
	if (!$("#LTEQueryMoTree").treeview("getSelected")[0] || $("#LTEQueryMoTree").treeview("getSelected")[0].parentId == undefined) {
		//alert("尚未选择模板");
		layer.open({
			title: "提示",
			content: "尚未选择模板"
		});
		return;
	}
	if (!$("#LTEFormulaTable").treegrid("getSelected") || $("#LTEFormulaTable").treegrid("getSelected")._parentId == undefined) {
		//alert("尚未选择要添加的公式");
		layer.open({
			title: "提示",
			content: "尚未选择要添加的公式"
		});
		return;
	}

	elementUpdate("add");
}
function elementUpdate(type) {
	var ids = $("#elementIds").val() == "" ? [] : $("#elementIds").val().split(",");
	var selectedNodeId = $("#LTEQueryMoTree").treeview("getSelected")[0].id;
	var selectedNodeIndex;
	var temp;
	if (type == "up") {
		selectedNodeIndex = $("#LTEElementTree").treeview("getSelected")[0].nodeId;
		temp = ids[selectedNodeIndex - 1];
		if (!temp) {
			//alert("已是第一个，无法上移");
			layer.open({
				title: "提示",
				content: "已是第一个，无法上移"
			});
			return;
		}
		ids[selectedNodeIndex - 1] = ids[selectedNodeIndex];
		ids[selectedNodeIndex] = temp;
		selectedNodeIndex = selectedNodeIndex - 1;
	} else if (type == "down") {
		selectedNodeIndex = $("#LTEElementTree").treeview("getSelected")[0].nodeId;
		var len = ids.length;
		temp = ids[selectedNodeIndex + 1];
		if (selectedNodeIndex + 1 == len) {
			//alert("已是最后一个，无法下移");
			layer.open({
				title: "提示",
				content: "已是最后一个，无法下移"
			});
			return;
		}
		ids[selectedNodeIndex + 1] = ids[selectedNodeIndex];
		ids[selectedNodeIndex] = temp;
		selectedNodeIndex = selectedNodeIndex + 1;
	} else if (type == "delete") {
		var kpiname = "";
		var deleteId = "";
		var Nodeindex;
		// var selectnodeid = $("#LTEQueryMoTree").treeview("getSelected");
		// console.log(selectnodeid);
		var deleteNodeid = $("#LTEElementTree").treeview("getSelected");
		for (var i=0,j=deleteNodeid.length;i<j;i++) {
				var temp = deleteNodeid[i].id+"";
				Nodeindex = ids.indexOf(temp);
				ids.splice(Nodeindex, 1);
				deleteId = deleteId+deleteNodeid[i]["id"]+",";
				kpiname = kpiname+deleteNodeid[i]["text"]+",";
		}
		kpiname = kpiname.substr(0, kpiname.length - 1);
		layer.confirm(kpiname+"确认删除吗？", {title: "提示"}, function (index) {
			// selectedNodeIndex = $("#LTEElementTree").treeview("getSelected")[0].nodeId;
			// ids.splice(selectedNodeIndex, 1);
			if (ids !=[]) {
				ids = ids.join(",");
			}
			$.get("LTETemplateManage/updateElement", {"ids": ids, "id": selectedNodeId}, function (res) {
				if (res) {
					var tree = "#LTEElementTree";
					$(tree).treeview({
						data: getKpiNamebyId(ids),
						onNodeSelected: function (event, data) {
							findTemplateById(data.id);
						}
					}); //树
					$("#elementIds").val(ids);
				} else {
					//alert("没有权限对该指标进行操作！");
					layer.open({
						title: "提示",
						content: "没有权限对该指标进行操作"
					});
				}
			});
			layer.close(index);
			$('#LTEFormulaTable').treegrid('clearSelections');
		});
		return;
	} else if (type == "add") {
		var selectedNode = $("#LTEFormulaTable").treegrid("getChecked");
		console.log(selectedNode);
		for (var i=0,j=selectedNode.length;i<j;i++) {
			if (ids.indexOf(selectedNode[i]["id"]) == -1) {
				ids.push(selectedNode[i]["id"]);
			} else {
				//alert("已存在该公式");
				layer.open({
					title: "提示",
					content: "已存在该公式"
				});
				return;
			}
		}
	}

	ids = ids.join(",");
	$.get("LTETemplateManage/updateElement", {"ids": ids, "id": selectedNodeId}, function (res) {
		if (res) {
			var tree = "#LTEElementTree";
			$(tree).treeview({
				data: getKpiNamebyId(ids),
				multiSelect:true,
				onNodeSelected: function (event, data) {
					findTemplateById(data.id);
				}
			}); //树
			$("#elementIds").val(ids);
			// $(tree).treeview("selectNode",selectedNodeIndex);
		} else {
			//alert("没有权限对该指标进行操作！");
			layer.open({
				title: "提示",
				content: "没有权限对该指标进行操作"
			});
		}
	});
}

function addMode() {
	$("#add_mode").modal();

	$("#add_mode input").val("");
	$("#add_mode textarea").val("");

	$("#modeForm").data("bootstrapValidator").destroy();
	initValidata_mode();
}

// $("#checkedType").bootstrapToggle("off");
function updateMode() {
	$("#modeForm").data("bootstrapValidator").validate();
	var flag = $("#modeForm").data("bootstrapValidator").isValid();
	if (!flag) {
		return;
	}
	var description;
	var flags = $("#checkedType").prop("checked");

	if(flags){
		 description="neighborCell";
	}else{
		description="";
	}
	var param = $("#modeForm").serialize().replace(/\+/g," ");
	var params = param.split("&");
	//var params = $("#modeForm").serialize().split("&");

	var data={
		modeName:$("#modeName").val(),
		description:description
	}
	$.get("LTETemplateManage/addMode", data, function (res) {
		if (res == "login") {
			//alert("尚未登录，不能添加模板");
			layer.open({
				title: "提示",
				content: "尚未登录，不能添加模板"
			});
			window.location.href = "login";
			return;
		}
		if (res == "名称已有") {
			//alert("已存在该模板，请重新输入");
			layer.open({
				title: "提示",
				content: "已存在该模板，请重新输入"
			});
			return;
		}
		if (res) {
			//alert("添加成功！");
			layer.open({
				title: "提示",
				content: "添加成功"
			});
			var params = {
				inputData: $("#modeName").val()
			};
			var url = "LTETemplateManage/searchLTETreeData";
			//var treeData;

			$.get(url, params, function (data) {
				//data = "["+data+"]";
				var tree = "#LTEQueryMoTree";
				$(tree).treeview({
					data: data,
					onNodeSelected: function (event, data) {
						if (data.parentId || data.parentId == 0) {
							var user = $(tree).treeview("getNode", data.parentId).text;
							setElementTree(data.id);
						} else {
							$("#LTEElementTree").empty();
						}
						//var user = data.user;
						//setElementTree(data.text,user);

					}
				});
				$("#LTEQueryMoTree").treeview("collapseAll", {silent: true});
			});
		} else {
			//alert("添加失败!");
			layer.open({
				title: "提示",
				content: "添加失败"
			});
		}
		$("#add_mode").modal("hide");

	});
}
function deleteMode() {
	if (!$("#LTEQueryMoTree").treeview("getSelected")[0] || !$("#LTEQueryMoTree").treeview("getSelected")[0].id) {
		//alert("尚未选择模板");
		layer.open({
			title: "提示",
			content: "尚未选择模板"
		});
		return;
	}
	layer.confirm("确认删除吗？", {title: "提示"}, function (index) {
		var id = $("#LTEQueryMoTree").treeview("getSelected")[0].id;
		$.get("LTETemplateManage/deleteMode", {"id": id}, function (res) {
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
	 var id = $("#LTEQueryMoTree").treeview("getSelected")[0].id;
	 $.get("LTETemplateManage/deleteMode",{"id":id},function(res){

	 if(res == "login"){
	 alert("尚未登录，不能删除模板");
	 window.location.href = "login";
	 return;
	 }
	 if(res == "1"){
	 alert("删除成功！");
	 setTree();
	 }else if(res == "2"){
	 alert("删除失败！");
	 }else if(res == "3"){
	 alert("没有权限删除该模板！");
	 }
	 });*/
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

function copyMode() {
	if (!$("#LTEQueryMoTree").treeview("getSelected")[0] || !$("#LTEQueryMoTree").treeview("getSelected")[0].id) {
		//alert("尚未选择模板");
		layer.open({
			title: "提示",
			content: "尚未选择模板"
		});
		return;
	}
	$("#copy_mode").modal();

	$("#copy_mode input").val("");
	$("#copy_mode textarea").val("");

	$("#modeForm_copy").data("bootstrapValidator").destroy();
	initValidata_mode_copy();

	var id = $("#LTEQueryMoTree").treeview("getSelected")[0].id;
	$("#copyId").val(id);
}
function updateModeCopy() {
	$("#modeForm_copy").data("bootstrapValidator").validate();
	var flag = $("#modeForm_copy").data("bootstrapValidator").isValid();
	if (!flag) {
		return;
	}


	var description;
	var flags = $("#checkedType1").prop("checked");
	if(flags){
		 description="neighborCell";
	}else{
		description="";
	}
	var param = $("#modeForm_copy").serialize().replace(/\+/g," ");
	var params = param.split("&");
	// var params = $("#modeForm_copy").serialize().split("&");
	var data = {};
	for (var i = 0; i < params.length; i++) {
		data[params[i].split("=")[0]] = decodeURIComponent(params[i].split("=")[1], true);
	}

	data.description=description;
	$.get("LTETemplateManage/copyMode", data, function (res) {

		if (res == "login") {
			//alert("尚未登录，不能添加模板");
			layer.open({
				title: "提示",
				content: "尚未登录，不能添加模板"
			});
			window.location.href = "login";
			return;
		}
		if (res == "名称已有") {
			//alert("已存在该模板，请重新输入");
			layer.open({
				title: "提示",
				content: "已存在该模板，请重新输入"
			});
			return;
		}
		if (res) {
			//alert("复制成功！");
			layer.open({
				title: "提示",
				content: "复制成功"
			});
			var params = {
				inputData: $("#modeName_copy").val()
			};
			var url = "LTETemplateManage/searchLTETreeData";
			//var treeData;

			$.get(url, params, function (data) {
				//data = "["+data+"]";
				var tree = "#LTEQueryMoTree";
				$(tree).treeview({
					data: data,
					onNodeSelected: function (event, data) {
						if (data.parentId || data.parentId == 0) {
							var user = $(tree).treeview("getNode", data.parentId).text;
							setElementTree(data.id);
						} else {
							$("#LTEElementTree").empty();
						}
						//var user = data.user;
						//setElementTree(data.text,user);

					}
				});
				$("#LTEQueryMoTree").treeview("collapseAll", {silent: true});
			});
		} else {
			//alert("复制失败!");
			layer.open({
				title: "提示",
				content: "复制失败"
			});
		}
		$("#copy_mode").modal("hide");

	});
}
function initValidata_mode_copy() {
	$("#modeForm_copy").bootstrapValidator({
		message: "This value is not valid",
		feedbackIcons: {
			valid: "glyphicon glyphicon-ok",
			invalid: "glyphicon glyphicon-remove",
			validating: "glyphicon glyphicon-refresh"
		},
		fields: {
			modeName_copy: {
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