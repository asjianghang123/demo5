$(document).ready(function () {
	toogle("storeManage");

	setTree();
});
var selectedRow;
function setTree() {
	$.post("storeManage/treeQuery", null, function (data) {
		var options = {
			bootstrap2: false,
			showTags: true,
			levels: 2,
			data: data,
			onNodeSelected: function (event, data) {
				doQuery(data.value);
			}
		};
		$("#cityTree").treeview(options);
	});
}
function doQuery(city) {
	var params = {
		city: city
	};
	var fieldArr = [];
	var text = "serverName,city,type,ipAddress,fileDir,sshUserName,sshPassword,ftpUserName,ftpPassword";
	var textArr = text.split(",");
	for (var i in textArr) {
		if (textArr[fieldArr.length] == "fileDir") {
			fieldArr[fieldArr.length] = {field: textArr[fieldArr.length], title: textArr[fieldArr.length], width: 300};
		} else {
			fieldArr[fieldArr.length] = {field: textArr[fieldArr.length], title: textArr[fieldArr.length], width: 150};
		}
	}
	$("#storeTable").grid("destroy", true, true);
	var grid = $("#storeTable").grid({
		columns: fieldArr,
		dataSource: {
			url: "storeManage/getTableData",
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
	$.get("storeManage/getCitys", null, function (data) {
		data = JSON.parse(data);
		$("#citys").multiselect("dataprovider", data);
		if (selectedCity) {
			$("#citys").multiselect("select", selectedCity);
		}
	});

}
function initType(selectedType) {
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
	$.get("storeManage/getTypes", null, function (data) {
		data = JSON.parse(data);
		$("#type").multiselect("dataprovider", data);
		if (selectedType) {
			$("#type").multiselect("select", selectedType);
		}
	});
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
	$.post("storeManage/updateDownload", data, function (res) {
		if (res == 1) {
			$("form input").val("");
			$("form textarea").val("");
			//alert("保存成功");
			layer.open({
				title: "提示",
				content: "保存成功"
			});
			doQuery(data.citys);
		} else {
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
		layer.confirm("确认删除吗？", {title: "提示"}, function (index) {
			$.get("storeManage/deleteDownload", {"id": selectedRow.id}, function (res) {
				if (res == 1) {
					layer.open({
						title: "提示",
						content: "删除成功"
					});
					doQuery(selectedRow.city);
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
function editItem() {
	if (selectedRow) {
		$("form input").val("");
		$("form textarea").val("");
		initCitys(selectedRow.city);
		initType(selectedRow.type);
		initValidata();
		$("#add_edit_modal").modal();

		$("#downloadId").val(selectedRow.id);
		$("#serverName").val(selectedRow.serverName);
		$("#type").multiselect("select", selectedRow.type);
		$("#ipAddress").val(selectedRow.ipAddress);
		$("#sshUserName").val(selectedRow.sshUserName);
		$("#sshPassword").val(selectedRow.sshPassword);
		$("#ftpUserName").val(selectedRow.ftpUserName);
		$("#ftpPassword").val(selectedRow.ftpPassword);
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
			ipAddress: {
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
			/*userName: {
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
			},*/
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