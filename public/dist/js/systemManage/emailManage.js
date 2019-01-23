$(document).ready(function () {
	toogle("emailManage");

	setTree();
	$("#EmailQueryTree").treeview("collapseAll", {silent: true});

	// initCitys();

});
var selectedRow;
var selectScope;

function setTree() {
	var tree = "#EmailQueryTree";
	$(tree).treeview({
		data: getTree(),
		onNodeSelected: function (event, data) {

			$("#scopeN").val(data.scope);
			$("#roleN").val(data.text);
			doQuery(data.text, data.scope);

		}
	}); //树
}

function getTree() {
	var url = "emailManage/treeQuery";
	var treeData;
	$.ajax({
		type: "post",
		url: url,
		dataType: "json",
		async: false,
		success: function (data) {
			treeData = data;
		}
	});
	return treeData;
}

function initCitys(selectedCity) {
	$("#city").multiselect({
		//dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "选择城市",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		//selectAllText:"全选/取消全选",
		//allSelectedText:"已选中所有城市",
		maxHeight: 200,
		maxWidth: "100%"
	});
	var url = "emailManage/getAllCity";
	var obj = {};
	var newOptions = [];
	$.post(url, null, function (data) {
		var sdata = [];
		for (var key in data) {
			obj = {
				label: key,
				value: data[key]
			};
			newOptions.push(obj);
		}
		$("#city").multiselect("dataprovider", newOptions);
		if (selectedCity) {
			$("#city").multiselect("select", selectedCity);
		}
	});

}
function doQuery(scope, role) {
	var params = {
		scope: scope,
		role: role
	};
	var fieldArr = [];
	var text = "mailAddress,name,role,scope,city";
	var textArr = text.split(",");
	for (var i in textArr) {
		fieldArr[fieldArr.length] = {field: textArr[fieldArr.length], title: textArr[fieldArr.length], width: 150};
	}
	$("#emailTable").grid("destroy", true, true);
	var grid = $("#emailTable").grid({
		columns: fieldArr,
		dataSource: {
			url: "emailManage/getTableData",
			success: function (data) {
				data = JSON.parse(data);
				grid.render(data);
			},
			type:"post"
		},
		params: params,
		pager: {limit: 10, sizes: [10, 20, 50, 100]},
		autoScroll: true,
		uiLibrary: "bootstrap",
		primaryKey : "id"
		// autoLoad: true
	});
	grid.on("rowSelect", function (e, $row, id, record) {
		selectedRow = record;
	});
}


function addItem() {
	$("#downloadForm input").val("");
	initCitys();
	// initRole();
	initScope();
	initValidata();
	$("#add_edit_modal").modal();
}


function initRole(value, selectedRole) {
	$("#role").multiselect({
		//dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "选择角色",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		//selectAllText:"全选/取消全选",
		//allSelectedText:"已选中所有城市",
		maxHeight: 200,
		maxWidth: "100%"
	});
	var param = {
		scope: value
	};
	var url = "emailManage/getRole";
	var obj = {};
	var newOptions = [];
	$.post(url, param, function(data) {
		var sdata = [];
		for (var key in data) {
			obj = {
				label: key,
				value: data[key]
			};
			newOptions.push(obj);
		}
		$("#role").multiselect("dataprovider", newOptions);
		if (selectedRole != "aaa") {
			$("#role").multiselect("select", selectedRole);
		}
	});
}

function initScope(selectedScope) {
	$("#scope").multiselect({
		//dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "选择邮箱角色",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		//selectAllText:"全选/取消全选",
		//allSelectedText:"已选中所有城市",
		maxHeight: 200,
		maxWidth: "100%"
	});
	var url = "emailManage/getScope";
	var obj = {};
	var newOptions = [];
	var roles = "aaa";
	$.post(url, null, function (data) {
		var sdata = [];
		for (var key in data) {
			obj = {
				label: key,
				value: data[key]
			};
			newOptions.push(obj);
		}
		$("#scope").multiselect("dataprovider", newOptions);
		if (selectedScope) {
			$("#scope").multiselect("select", selectedScope);
		}
		$("#scope").change(function () {
			var scope = $("#scope").val();
			initRole(scope, roles);
		});

	});
}

function insertDownload() {
	$("#downloadForm").data("bootstrapValidator").validate();
	var flag = $("#downloadForm").data("bootstrapValidator").isValid();
	if (!flag) {
		return;
	}

	var params = $("#downloadForm").serialize().split("&");
	var data = {};
	for (var i = 0; i < params.length; i++) {
		data[params[i].split("=")[0]] = decodeURIComponent(params[i].split("=")[1], true);
	}
	$.post("emailManage/insertDownload", data, function (res) {
		if (res == 1) {
			$("form input").val("");
			// $("form textarea").val("");
			//alert("保存成功");
			layer.open({
				title: "提示",
				content: "保存成功"
			});
			setTree();
			doQuery(data.role, data.scope);
			// $("#table_tab_"+data.type+"_nav").click();
		} else {
			//alert("保存失败，请重试");
			layer.open({
				title: "提示",
				content: "保存失败，请重试"
			});
		}
		$("#add_edit_modal").modal("hide");
	});
}

function editItem() {
	if (selectedRow) {
		$("form input").val("");
		initCitys(selectedRow.city);
		initRole(selectedRow.scope, selectedRow.role);
		initScope(selectedRow.scope);
		initValidata();
		$("#id").val(selectedRow.id);
		$("#add_edit_modal").modal();
		$("#mailAddress").val(selectedRow.mailAddress);
		$("#name").val(selectedRow.name);
		//$("#city").multiselect("select",selectedRow.city);
		// $("#scope").multiselect("select",selectedRow.scope);
		// $("#role").multiselect("select",selectedRow.role);
	} else {
		//alert("请选择要修改的数据");
		layer.open({
			title: "提示",
			content: "请选择要修改的数据"
		});
	}
}

function deleteItem() {
	if (selectedRow) {
		layer.confirm("确认删除吗？", {title: "提示"}, function (index) {
			params = {
				"id": selectedRow.id
			};
			$.post("emailManage/deleteDownload", params, function (res) {
				if (res == 1) {
					layer.open({
						title: "提示",
						content: "删除成功"
					});
					doQuery(selectedRow.role, selectedRow.scope);
				} else {
					layer.open({
						title: "提示",
						content: "删除失败，请重试"
					});
				}
			});
			layer.close(index);
		});
	} else {
		layer.open({
			title: "提示",
			content: "请选择要删除的数据"
		});
	}
}

function initValidata() {
	$("#downloadForm").bootstrapValidator({
		message: "This value is not valid",
		feedbackIcons: {
			valid: "glyphicon glyphicon-ok",
			invalid: "glyphicon glyphicon-remove",
			validating: "glyphicon glyphicon-refresh"
		},
		fields: {
			mailAddress: {
				validators: {
					notEmpty: {
						message: "邮箱不能为空"
					}
				}
			},
			name: {
				validators: {
					notEmpty: {
						message: "姓名不能为空"
					}
				}
			},
			role: {
				validators: {
					notEmpty: {
						message: "用户角色不能为空"
					}
				}
			},
			city: {
				validators: {
					notEmpty: {
						message: "城市不能为空"
					}
				}
			}
		}
	});
}

function addScope() {
	$("#add_scope").modal();

	$("#add_scope input").val("");
}

function updateScope() {

	var scope = $("#scopeName").val();
	var role = $("#roleName").val();
	params = {
		scope: scope,
		role: role
	};

	$.post("emailManage/updateScope", params, function (res) {
		if (res == 1) {
			$("#scopeName").val("");
			$("#roleName").val("");
			//alert("保存成功");
			layer.open({
				title: "提示",
				content: "保存成功"
			});
			setTree();
			$("#EmailQueryTree").treeview("collapseAll", {silent: true});
		} else {
			//alert("保存失败，请重试");
			layer.open({
				title: "提示",
				content: "保存失败，请重试"
			});
		}
		$("#add_scope").modal("hide");
	});
}

function deleteScope() {
	var id = $("#EmailQueryTree").treeview("getSelected")[0].id;
	var scope = $("#EmailQueryTree").treeview("getSelected")[0].scope;
	var text = $("#EmailQueryTree .node-selected").text();

	if (!text) {
		//alert("尚未选择要删除的数据");
		layer.open({
			title: "提示",
			content: "尚未选择要删除的数据"
		});
		return;
	}
	layer.confirm("确认删除吗？", {title: "提示"}, function (index) {
		if (id) {
			params = {
				id: id,
				scope: scope,
				role: text
			};
		} else {
			params = {
				id: 0,
				scope: text,
				role: 0
			};
		}
		$.post("emailManage/deleteScope", params, function (res) {

			if (res == "1") {
				layer.open({
					title: "提示",
					content: "删除成功！"
				});
				setTree();
				$("#EmailQueryTree").treeview("collapseAll", {silent: true});
				doQuery(text, scope);
			} else {
				layer.open({
					title: "提示",
					content: "删除失败！"
				});
			}
		});
		layer.close(index);
	});
	/*var flag = confirm("确认删除吗？");
	 if(!flag){
	 return;
	 }
	 if (id) {
	 params = {
	 id : id,
	 scope : scope,
	 role : text
	 }
	 } else {
	 params = {
	 id : 0,
	 scope : text,
	 role : 0
	 }
	 }
	 $.get("emailManage/deleteScope",params, function(res){

	 if (res == "1") {
	 alert("删除成功！");
	 setTree();
	 $("#EmailQueryTree").treeview("collapseAll", { silent: true });
	 doQuery(text,scope);
	 } else {
	 alert("删除失败！");
	 }
	 });*/
}

