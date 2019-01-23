$(document).ready(function () {
	toogle("downloadManage");

	setTree();
	$("#checkedType").bootstrapToggle("off");
});
var selectedRow;
function setTree() {
	var options = [];

	$.get("downloadManage/treeQuery", null, function (data) {
		for (var i in type) {
			options = {
				bootstrap2: false,
				showTags: true,
				levels: 2,
				data: data,
				onNodeSelected: function (event, data) {
					$("#downloadTypeValue").val(data.value);
					doQuery(data.value);
				}
			};
			$("#downloadTypeTree").treeview(options);
		}
	});
}
function doQuery(type) {
	var params = {
		type : type,
		flag : $("#checkedType").prop("checked")
	};
	var fieldArr = [];
	var text = "status,serverName,city,type,externalAddress,internalAddress,subNetwork,fileDir,userName,password";
	var textArr = text.split(",");
	for (var i in textArr) {
		if (textArr[i] == "status") {
			fieldArr[i] = {field: textArr[i], title: textArr[i], width: 50};
		} else {
			fieldArr[i] = {field: textArr[i], title: textArr[i], width: 150};
		}
	}
	$("#downloadTable").grid("destroy", true, true);
	var grid = $("#downloadTable").grid({
		columns: fieldArr,
		dataSource: {
			url: "downloadManage/getTableData",
			success: function (data) {
				data = JSON.parse(data);
				grid.render(data);
				$('#downloadTable div[title="true"]').html("").addClass("glyphicon glyphicon-ok-circle").removeClass("glyphicon-exclamation-sign").parent("td").addClass("connectedSuccess").removeClass("connectedFailed");
				$('#downloadTable div[title="false"]').html("").addClass("glyphicon glyphicon-exclamation-sign").removeClass("glyphicon-ok-circle").parent("td").addClass("connectedFailed").removeClass("connectedSuccess");
			}
		},
		params: params,
		pager: {limit: 10, sizes: [10, 20, 50, 100]},
		autoScroll: true,
		uiLibrary: "bootstrap",
		primaryKey: "id",
		autoLoad: true
	});
	grid.on("rowSelect", function (e, $row, id, record) {
		selectedRow = record;
	});
}
function initCitys(selectedCity) {
	$("#citys").multiselect({
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
	$.get("downloadManage/getCitys", null, function (data) {
		data = JSON.parse(data);
		$("#citys").multiselect("dataprovider", data);
		if (selectedCity) {
			$("#citys").multiselect("select", selectedCity);
		}
	});
}
function initType() {
	$("#type").multiselect({
		//dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "选择类型",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		//selectAllText:"全选/取消全选",
		//allSelectedText:"已选中所有城市",
		maxHeight: 200,
		maxWidth: "100%"
	});
	var data = [
		{"label": "NBM", "value": "nbm"},
		{"label": "CTR", "value": "ctr"},
		{"label": "CDR", "value": "cdr"},
		{"label": "CDD", "value": "cdd"},
		{"label": "EBM", "value": "ebm"},
		{"label": "CTUM", "value": "ctum"},
		{ "label": "LGOS", "value": "lgos" },
		{"label": "KGET", "value": "kget"},
		{ "label": "KGETG2", "value": "kgetG2" },
		{ "label": "KGET16", "value": "kget16" },
		{ "label": "KGET_EXTERN", "value": "kget_extern" },
		{ "label": "MRO", "value": "mro" },
		{ "label": "MRE", "value": "mre" },
		{ "label": "MRS", "value": "mrs" }
	];
	$("#type").multiselect("dataprovider", data);
}
function addItem() {
	initCitys();
	initType();
	initValidata();
	$("#add_edit_modal").modal();
}
function updateDownload() {
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
	$.post("downloadManage/updateDownload", data, function (res) {
		if (res == 1) {
			$("form input").val("");
			$("form textarea").val("");
			//alert("保存成功");
			layer.open({
				title: "提示",
				content: "保存成功"
			});
			doQuery(data.type);
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
function deleteItem() {
	if (selectedRow) {
		/*var flag = confirm("确认删除吗？");
		 if(flag){
		 $.get("downloadManage/deleteDownload",{"id":selectedRow.id},function(res){
		 if(res==1){
		 alert("删除成功。");
		 doQuery(selectedRow.type);
		 }else{
		 alert("删除失败，请重试");
		 }
		 });
		 }*/
		layer.confirm("确认删除吗？", {title: "提示"}, function (index) {
			$.get("downloadManage/deleteDownload", {"id": selectedRow.id}, function (res) {
				if (res == 1) {
					layer.open({
						title: "提示",
						content: "删除成功"
					});
					doQuery(selectedRow.type);
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
		//alert("请选择要删除的数据");
		layer.open({
			title: "提示",
			content: "请选择要删除的数据"
		});
	}
}
function editItem() {
	if (selectedRow) {
		$("form input").val("");
		$("form textarea").val("");
		initCitys(selectedRow.city);
		initType();
		initValidata();
		$("#add_edit_modal").modal();

		$("#downloadId").val(selectedRow.id);
		$("#serverName").val(selectedRow.serverName);
		// $("#citys").multiselect("select", selectedRow.city);
		$("#type").multiselect("select", selectedRow.type);
		$("#externalAddress").val(selectedRow.externalAddress);
		$("#internalAddress").val(selectedRow.internalAddress);
		$("#userName").val(selectedRow.userName);
		$("#password").val(selectedRow.password);
		$("#subNetwork").val(selectedRow.subNetwork);
		$("#fileDir").val(selectedRow.fileDir);
	} else {
		//alert("请选择要修改的数据");
		layer.open({
			title: "提示",
			content: "请选择要修改的数据"
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
			externalAddress: {
				validators: {
					notEmpty: {
						message: "外网地址不能为空"
					},
					regexp: {
						regexp: /^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$/,
						message: "请输入正确的ip地址"
					}

				}
			},
			internalAddress: {
				validators: {
					regexp: {
						regexp: /^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$/,
						message: "请输入正确的ip地址"
					}

				}
			},
			userName: {
				validators: {
					notEmpty: {
						message: "用户名不能为空"
					}
				}
			},
			password: {
				validators: {
					notEmpty: {
						message: "密码不能为空"
					}
				}
			},
			fileDir: {
				validators: {
					notEmpty: {
						message: "网管路径不能为空"
					}
				}
			}
		}
	});
}