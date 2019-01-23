$(document).ready(function () {
	toogle("ENIQManage");
	// 加载用户表
	// doQuery4G();
	// doQuery2G();
	//    doQueryAlarm();
	setTree();
	initValidata();
	initValidata_alarm();

});
var selectedRow;
var ENIQRow;
function setTree() {
	$.get("common/json/ENIQTreeData.json", null, function (data) {
		storageQueryTreeData = eval("(" + data + ")");
		var options = {
			bootstrap2: false,
			showTags: true,
			levels: 2,
			data: storageQueryTreeData,
			onNodeSelected: function (event, data) {
				ENIQRow = data.id;
				if (data.id == 1) {
					$("#ENIQTypeValue").val(data.id);
					doQuery4G();
				} else if (data.id == 2) {
					$("#ENIQTypeValue").val(data.id);
					doQuery2G();
				} else if (data.id == 3) {
					$("#ENIQTypeValue").val(data.id);
					doQuery4GAlarm();
				} else {
					$("#ENIQTypeValue").val(data.id);
					doQuery2GAlarm();
				}
			}
		};
		$("#ENIQTypeTree").treeview(options);
	});
}

function doQuery4G() {
	layer.msg("加载中", {
		icon: 16,
		shade: 0.01,
		time: 0
	});
	//加载4GENIQ表
	$.get("ENIQManage/Query4G", "", function (data) {
		var fieldArr = [];
		var text = (JSON.parse(data).text).split(",");
		for (var i in text) {
			if (text[i] == "subNetwork" || text[i] == "subNetworkFdd" || text[i] == "subNetworkNbiot") {
				fieldArr[i] = {field: text[i], title: text[i], width: 250, cssClass: "nowrap"};
			} else if (text[i] == "status") {
				fieldArr[i] = {field: text[i], title: text[i], width: 50};
			} else {
				fieldArr[i] = {field: text[i], title: text[i], width: 150};
			}
		}
		var newData = JSON.parse(data).rows;
		$("#ENIQTable").grid("destroy", true, true);
		var grid = $("#ENIQTable").grid({
			columns: fieldArr,
			dataSource: newData,
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap",
			primaryKey: "id"
		});
		grid.on("rowSelect", function (e, $row, id, record) {
			selectedRow = record;
		});
		$('#ENIQTable div[title="true"]').html("").addClass("glyphicon glyphicon-ok-circle").removeClass("glyphicon-exclamation-sign").parent("td").addClass("connectedSuccess").removeClass("connectedFailed");
		$('#ENIQTable div[title="false"]').html("").addClass("glyphicon glyphicon-exclamation-sign").removeClass("glyphicon-ok-circle").parent("td").addClass("connectedFailed").removeClass("connectedSuccess");
		layer.closeAll("dialog");
	});
}
function doQuery2G() {
	//加载2GENIQ表
	layer.msg("加载中", {
		icon: 16,
		shade: 0.01,
		time: 0
	});
	$.get("ENIQManage/Query2G", "", function (data) {
		var fieldArr = [];
		var text = (JSON.parse(data).text).split(",");
		for (var i in text) {
			if (text[i] == "status") {
				fieldArr[i] = {field: text[i], title: text[i], width: 50};
			} else {
				fieldArr[i] = {field: text[i], title: text[i], width: 140};
			}
		}
		var newData = JSON.parse(data).rows;
		$("#ENIQTable").grid("destroy", true, true);
		var grid = $("#ENIQTable").grid({
			columns: fieldArr,
			dataSource: newData,
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap",
			primaryKey: "id"
		});
		grid.on("rowSelect", function (e, $row, id, record) {
			selectedRow = record;
		});
		$('#ENIQTable div[title="true"]').html("").addClass("glyphicon glyphicon-ok-circle").removeClass("glyphicon-exclamation-sign").parent("td").addClass("connectedSuccess").removeClass("connectedFailed");
		$('#ENIQTable div[title="false"]').html("").addClass("glyphicon glyphicon-exclamation-sign").removeClass("glyphicon-ok-circle").parent("td").addClass("connectedFailed").removeClass("connectedSuccess");
		layer.closeAll("dialog");
	});
}

function deleteItem() {
	if (selectedRow) {
		layer.confirm("确认删除吗？", {title: "提示"}, function (index) {
			$.get("ENIQManage/deleteENIQ", {"id": selectedRow.id, "sign": ENIQRow}, function (res) {
				if (res) {
					layer.open({
						title: "提示",
						content: "删除成功。"
					});
					if (ENIQRow == 1) {
						doQuery4G();
					} else if (ENIQRow == 2) {
						doQuery2G();
					} else if (ENIQRow == 3) {
						doQuery4GAlarm();
					} else {
						doQuery2GAlarm();
					}
				} else {
					layer.open({
						title: "提示",
						content: "删除失败，请重试"
					});
				}
			});
			layer.close(index);
		});
		/*var flag = confirm("确认删除吗？");
		 if(flag){
		 $.get("ENIQManage/deleteENIQ",{"id":selectedRow.id,"sign":ENIQRow},function(res){
		 if(res){
		 alert("删除成功。");
		 if (ENIQRow == 1) {
		 // $("#ENIQTypeValue").val(data.id);
		 doQuery4G();
		 } else if (ENIQRow == 2) {
		 // $("#ENIQTypeValue").val(data.id);
		 doQuery2G();
		 } else {
		 // $("#ENIQTypeValue").val(data.id);
		 doQueryAlarm();
		 }
		 }else{
		 alert("删除失败，请重试");
		 }
		 });
		 }*/
	} else {
		//alert("请选择要删除的数据");
		layer.open({
			title: "提示",
			content: "请选择要删除的数据"
		});
	}
}

function selectENIQ() {
	$("#select_edit_ENIQ").modal();
	$("form select").val("");
	$("#saveBtn").html("新增");
}
function upSelectENIQ() {
	var type = $("#type").val();
	if (type == "4GENIQ") {
		add4G();
		// $("#select_edit_ENIQ").modal("hide");
	} else if (type == "2GENIQ") {
		add2G();
		// $("#select_edit_ENIQ").modal("hide");
	} else if (type == "4GAlarm") {
		add4GAlarm();
		// $("#select_edit_ENIQ").modal("hide");
	} else {
		add2GAlarm();
		// $("#select_edit_ENIQ").modal("hide");
	}
}
function add4G() {
	$("#add_edit_ENIQ").modal();
	$("form input").val("");
	$("form textarea").val("");
	$("form textarea").parents(".form-group").show();
	$("#saveBtn").html("新增");

	$("#ENIQSign").val("4G");
	$("#ENIQForm").data("bootstrapValidator").destroy();
	initValidata();
}
function add2G() {

	$("#add_edit_ENIQ").modal();
	$("form input").val("");
	$("form textarea").val("");
	$("form textarea").parents(".form-group").hide();
	$("#saveBtn").html("新增");

	$("#ENIQSign").val("2G");
	$("#ENIQForm").data("bootstrapValidator").destroy();
	initValidata();
}
function add4GAlarm() {
	$("#add_edit_alarm").modal();
	$("form input").val("");
	$("#alarmSign").val("4GAlarm");
	$("#alarmForm").data("bootstrapValidator").destroy();
	initValidata_alarm();
}
function add2GAlarm() {
	$("#add_edit_alarm").modal();
	$("form input").val("");
	$("#alarmSign").val("2GAlarm");

	$("#alarmForm").data("bootstrapValidator").destroy();
	initValidata_alarm();
}


function editItem() {
	if (selectedRow) {
		if (ENIQRow == 1) {
			$("#add_edit_ENIQ").modal();
			$("form input").val("");
			$("form textarea").val("");
			$("form textarea").parents(".form-group").show();
			$("#saveBtn").html("更新");
			$("#ENIQSign").val("4G");
			$("#ENIQId").val(selectedRow.id);
			$("#connName").val(selectedRow.connName);
			$("#cityChinese").val(selectedRow.cityChinese);
			$("#host").val(selectedRow.host);
			$("#port").val(selectedRow.port);
			$("#dbName").val(selectedRow.dbName);
			$("#userName").val(selectedRow.userName);
			$("#password").val(selectedRow.password);
			$("#subNetwork").val(selectedRow.subNetwork);
			$("#subNetworkFdd").val(selectedRow.subNetworkFdd);
			$("#subNetworkNbiot").val(selectedRow.subNetworkNbiot);
			$("#ENIQForm").data("bootstrapValidator").destroy();
			initValidata();
		} else if (ENIQRow == 2) {
			$("#add_edit_ENIQ").modal();
			$("form input").val("");
			$("form textarea").val("");
			$("form textarea").parents(".form-group").hide();
			$("#saveBtn").html("更新");
			$("#ENIQSign").val("2G");
			$("#ENIQId").val(selectedRow.id);
			$("#connName").val(selectedRow.connName);
			$("#cityChinese").val(selectedRow.cityChinese);
			$("#host").val(selectedRow.host);
			$("#port").val(selectedRow.port);
			$("#dbName").val(selectedRow.dbName);
			$("#userName").val(selectedRow.userName);
			$("#password").val(selectedRow.password);
			$("#ENIQForm").data("bootstrapValidator").destroy();
			initValidata();
		} else if (ENIQRow == 3) {
			$("#add_edit_alarm").modal();
			$("form input").val("");
			$("#saveBtn").html("更新");
			$("#alarmSign").val("4GAlarm");
			$("#alarmId").val(selectedRow.id);
			$("#alarmServerName").val(selectedRow.serverName);
			$("#alarmCity").val(selectedRow.city);
			$("#alarmHost").val(selectedRow.host);
			$("#alarmPort").val(selectedRow.port);
			$("#alarmDbName").val(selectedRow.dbName);
			$("#alarmUserName").val(selectedRow.userName);
			$("#alarmPassword").val(selectedRow.password);
			$("#alarmForm").data("bootstrapValidator").destroy();
			initValidata_alarm();

		} else if (ENIQRow == 4) {
			$("#add_edit_alarm").modal();
			$("form input").val("");
			$("#saveBtn").html("更新");
			$("#alarmSign").val("2GAlarm");
			$("#alarmId").val(selectedRow.id);
			$("#alarmServerName").val(selectedRow.serverName);
			$("#alarmCity").val(selectedRow.city);
			$("#alarmHost").val(selectedRow.host);
			$("#alarmPort").val(selectedRow.port);
			$("#alarmDbName").val(selectedRow.dbName);
			$("#alarmUserName").val(selectedRow.userName);
			$("#alarmPassword").val(selectedRow.password);
			$("#alarmForm").data("bootstrapValidator").destroy();
			initValidata_alarm();
		} else {
			//alert("请选择要修改的数据");
			layer.open({
				title: "提示",
				content: "请选择要修改的数据"
			});
		}
	}
}

function initValidata() {
	$("#ENIQForm").bootstrapValidator({
		message: "This value is not valid",
		feedbackIcons: {
			valid: "glyphicon glyphicon-ok",
			invalid: "glyphicon glyphicon-remove",
			validating: "glyphicon glyphicon-refresh"
		},
		fields: {
			connName: {
				validators: {
					notEmpty: {
						message: "Conn Name不能为空"
					}
				}
			},
			cityChinese: {
				validators: {
					notEmpty: {
						message: "City Chinese不能为空"
					}
				}
			},
			host: {
				validators: {
					notEmpty: {
						message: "Host不能为空"
					}
				}
			},
			port: {
				validators: {
					notEmpty: {
						message: "Port不能为空"
					}
				}
			},
			dbName: {
				validators: {
					notEmpty: {
						message: "DB Name不能为空"
					}
				}
			},
			userName: {
				validators: {
					notEmpty: {
						message: "User Name不能为空"
					}
				}
			},
			password: {
				validators: {
					notEmpty: {
						message: "Password不能为空"
					}
				}
			},
		}
	});
}

function getSelected(table) {
	var id = $("#" + table).find("tr.active").children("td").eq(0).children("div").html();
	var data = $("#" + table).grid("getById", id);
	return data;

}

function doQuery4GAlarm() {
	//加载Alarm表
	layer.msg("加载中", {
		icon: 16,
		shade: 0.01,
		time: 0
	});
	$.get("ENIQManage/Query4GAlarm", null, function (data) {
		var fieldArr = [];
		var text = (JSON.parse(data).text).split(",");
		for (var i in text) {
			if (text[i] == "status") {
				fieldArr[i] = {field: text[i], title: text[i], width: 50};
			} else {
				fieldArr[i] = {field: text[i], title: text[i], width: 150};
			}
		}
		var newData = JSON.parse(data).rows;
		$("#ENIQTable").grid("destroy", true, true);
		var grid = $("#ENIQTable").grid({
			columns: fieldArr,
			dataSource: newData,
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap",
			primaryKey: "id"
		});
		grid.on("rowSelect", function (e, $row, id, record) {
			selectedRow = record;
		});
		$('#ENIQTable div[title="true"]').html("").addClass("glyphicon glyphicon-ok-circle").removeClass("glyphicon-exclamation-sign").parent("td").addClass("connectedSuccess").removeClass("connectedFailed");
		$('#ENIQTable div[title="false"]').html("").addClass("glyphicon glyphicon-exclamation-sign").removeClass("glyphicon-ok-circle").parent("td").addClass("connectedFailed").removeClass("connectedSuccess");
		layer.closeAll("dialog");
	});
}
function doQuery2GAlarm() {
	//加载Alarm表
	layer.msg("加载中", {
		icon: 16,
		shade: 0.01,
		time: 0
	});
	$.get("ENIQManage/Query2GAlarm", null, function (data) {
		var fieldArr = [];
		var text = (JSON.parse(data).text).split(",");
		for (var i in text) {
			if (text[i] == "status") {
				fieldArr[i] = {field: text[i], title: text[i], width: 50};
			} else {
				fieldArr[i] = {field: text[i], title: text[i], width: 150};
			}
		}
		var newData = JSON.parse(data).rows;
		$("#ENIQTable").grid("destroy", true, true);
		var grid = $("#ENIQTable").grid({
			columns: fieldArr,
			dataSource: newData,
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap",
			primaryKey: "id"
		});
		grid.on("rowSelect", function (e, $row, id, record) {
			selectedRow = record;
		});
		$('#ENIQTable div[title="true"]').html("").addClass("glyphicon glyphicon-ok-circle").removeClass("glyphicon-exclamation-sign").parent("td").addClass("connectedSuccess").removeClass("connectedFailed");
		$('#ENIQTable div[title="false"]').html("").addClass("glyphicon glyphicon-exclamation-sign").removeClass("glyphicon-ok-circle").parent("td").addClass("connectedFailed").removeClass("connectedSuccess");
		layer.closeAll("dialog");
	});
}

function updateENIQ() {

	$("#ENIQForm").data("bootstrapValidator").validate();
	var flag = $("#ENIQForm").data("bootstrapValidator").isValid();
	if (!flag) {
		return;
	}

	var params = $("#ENIQForm").serialize().split("&");
	var data = {};
	for (var i = 0; i < params.length; i++) {
		data[params[i].split("=")[0]] = decodeURIComponent(params[i].split("=")[1], true);
	}
	$.get("ENIQManage/updateENIQ", data, function (res) {
		$("#add_edit_ENIQ").modal("hide");
		$("#select_edit_ENIQ").modal("hide");
		if (res == "4G") {
			doQuery4G();
		} else if (res == "2G") {
			doQuery2G();
		}

	});

}
function updateAlarm() {

	$("#alarmForm").data("bootstrapValidator").validate();
	var flag = $("#alarmForm").data("bootstrapValidator").isValid();
	if (!flag) {
		return;
	}
	var params = $("#alarmForm").serialize().split("&");
	var data = {};
	for (var i = 0; i < params.length; i++) {
		data[params[i].split("=")[0]] = decodeURIComponent(params[i].split("=")[1], true);
	}
	$.post("ENIQManage/updateAlarm", data, function (res) {
		$("#select_edit_ENIQ").modal("hide");
		if (res == 0) {
			//alert("新增失败，请重试");
			layer.open({
				title: "提示",
				content: "新增失败，请重试"
			});
			return;
		}
		$("#add_edit_alarm").modal("hide");
		doQueryAlarm();
	});
}
function initValidata_alarm() {
	$("#alarmForm").bootstrapValidator({
		message: "This value is not valid",
		feedbackIcons: {
			valid: "glyphicon glyphicon-ok",
			invalid: "glyphicon glyphicon-remove",
			validating: "glyphicon glyphicon-refresh"
		},
		fields: {
			alarmServerName: {
				validators: {
					notEmpty: {
						message: "serverName不能为空"
					}
				}
			},
			alarmCity: {
				validators: {
					notEmpty: {
						message: "city不能为空"
					}
				}
			},
			alarmHost: {
				validators: {
					notEmpty: {
						message: "Host不能为空"
					}
				}
			},
			alarmPort: {
				validators: {
					notEmpty: {
						message: "Port不能为空"
					}
				}
			},
			alarmDbName: {
				validators: {
					notEmpty: {
						message: "DB Name不能为空"
					}
				}
			},
			alarmUserName: {
				validators: {
					notEmpty: {
						message: "User Name不能为空"
					}
				}
			},
			alarmPassword: {
				validators: {
					notEmpty: {
						message: "Password不能为空"
					}
				}
			}
		}
	});
}