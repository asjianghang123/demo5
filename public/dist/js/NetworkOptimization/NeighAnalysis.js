$(document).ready(function () {
	$("#input1").val(3);
	$("#input2").val(1);
	$("#input3").val(50);
	$("#input4").val(0.8);
	$("#input5").val(50);
	$("#input6").val(-90);
	$("#input7").val(-15);
	$("#input8").val(3);
	$("#input9").val(50);
	$("#input1Temp").val(3);
	$("#input2Temp").val(1);
	$("#input3Temp").val(50);
	$("#input4Temp").val(0.8);
	$("#input5Temp").val(50);
	$("#input6Temp").val(-90);
	$("#input7Temp").val(-15);
	$("#input8Temp").val(3);
	$("#input9Temp").val(50);
	getAllCity();
	setTime_MRE();
	typeSelect();
	toogle("GSMNeighborAnalysis");
});

function typeSelect(){
	$("#type").multiselect({
	buttonWidth: "100%",
	//enableFiltering: true,
	nonSelectedText:"请选择类型",
	//filterPlaceholder:"搜索",
	nSelectedText:"项被选中",
	includeSelectAllOption:true,
	// selectAllText:"全选/取消全选",
	// allSelectedText:"已选中所有",
	maxHeight:200,
	maxWidth:"100%"
});
}
function getAllCity() {
	$("#city").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "请选择城市",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有平台类型",
		maxHeight: 200,
		width: 220
	});
	var url = "GSMNeighborAnalysis/getAllCity";
	$.ajax({
		type: "GET",
		url: url,
		dataType: "json",
		success: function (data) {
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
			$("#city").multiselect("dataprovider", newOptions);
		}
	});
}
function setTime_MRE() {
	$("#dateTime").datepicker({format: "yyyy-mm-dd"});  //返回日期
	var nowTemp = new Date();
	//$("#dateTime").datepicker("setValue", nowTemp);
	//$("#dateTime").datepicker("setValues", ["2016-10-09","2016-10-10","2016-10-18"]);
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;

	// console.log(today);
	var params = {
		city: getFirstCity(),
		type: $("#type").val()
	};
	$.get("GSMNeighborAnalysis/getDate", params, function (data) {
		var sdata = [];
		for (var i = 0; i < data.length; i++) {
			if (data[i] === today) {
				continue;
			}
			sdata.push(data[i]);
		}
		sdata.push(today);
		$("#dateTime").datepicker("setValues", sdata);
	});
	$("#city").change(function () {
		var city = $("#city").val();
		var params = {
			city: city,
			type: $("#type").val()
		};
		$.get("GSMNeighborAnalysis/getDate", params, function (data) {
			var sdata = [];
			for (var i = 0; i < data.length; i++) {
				if (data[i] === today) {
					continue;
				}
				sdata.push(data[i]);
			}
			sdata.push(today);
			$("#dateTime").datepicker("setValues", sdata);
		});
	});
	$("#type").change(function () {
		var city = $("#city").val();
		var params = {
			city: city,
			type: $("#type").val()
		};
		$.get("GSMNeighborAnalysis/getDate", params, function (data) {
			var sdata = [];
			for (var i = 0; i < data.length; i++) {
				if (data[i] === today) {
					continue;
				}
				sdata.push(data[i]);
			}
			sdata.push(today);
			$("#dateTime").datepicker("setValues", sdata);
		});
	});

	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#dateTime").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? '': '';
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");
}

function openConfigInfo() {
	var type = $("#type").val();
	if (type == "2G") {
		$("#config_information").modal();
	} else {
		return;
	}	
}
function updateConfigInfo() {
	$("#input1").val($("#input1Temp").val());
	$("#input2").val($("#input2Temp").val());
	$("#input3").val($("#input3Temp").val());
	$("#input4").val($("#input4Temp").val());
	$("#input5").val($("#input5Temp").val());
	$("#input6").val($("#input6Temp").val());
	$("#input7").val($("#input7Temp").val());
	$("#input8").val($("#input8Temp").val());
	$("#input9").val($("#input9Temp").val());
	$("#config_information").modal("hide");
}

function doSearchGSM(){
	var l = Ladda.create(document.getElementById("search"));
	var E = Ladda.create(document.getElementById("export"));
	l.start();
	E.start();
	var type = $("#type").val();
	// if (type == "MRE") {
	// 	doSearchGSM_MRE(l,E);
	// } else {
	// 	query_CDR(l,E);
	// 	$("#exportBtn").hide();
	// }
	if (type == "2G") {
		doSearchGSM_MRE_2G(l,E);
	} else if (type == "3G") {
		doSearchGSM_MRE_3G(l,E);
		$("#exportBtn").hide();
	}
}
function doSearchGSM_MRE_2G(l,E) {
	var select = $("#city").val();
	var dateTime = $("#dateTime").val();
	var input1 = $("#input1").val();
	var input2 = $("#input2").val();
	var input3 = $("#input3").val();
	var input4 = $("#input4").val();
	var input5 = $("#input5").val();
	var input6 = $("#input6").val();
	var input7 = $("#input7").val();
	var input8 = $("#input8").val();
	var input9 = $("#input9").val();
	var type = $("#type").val();

	if (input1 == "") {
		input1 = 3;
	}
	if (input2 == "") {
		input2 = 1;
	}
	if (input3 == "") {
		input3 = 50;
	}
	if (input4 == "") {
		input4 = 0.8;
	}
	if (input5 == "") {
		input5 = 50;
	}
	if (input6 == "") {
		input6 = -90;
	}
	if (input7 == "") {
		input7 = -15;
	}
	if (input6 == "") {
		input8 = 3;
	}
	if (input7 == "") {
		input9 = 50;
	}

	var params = {
		input1: input1,
		input2: input2,
		input3: input3,
		input4: input4,
		input5: input5,
		input6: input6,
		input7: input7,
		input8: input8,
		input9: input9,
		select: select,
		dateTime: dateTime,
		dataType: type + "数据",
		OptimizationType: "补2G邻区分析"
	};
	$.get("GSMNeighborAnalysis/GSMNeighAnalysis", params, function (data) {
		if (data.error == "error") {
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
			l.stop();
			E.stop();
			return;
		}
		var text=data.text.split(",");
		var field=data.field.split(",");
		var fieldArr = [];
		for (var k in text) {
			if (text[k] == "呼叫数量（目标）呼叫比例（目标）") {
				fieldArr[fieldArr.length] = {field: field[k], title: text[k], width: 250};
			} else {
				fieldArr[fieldArr.length] = {field: field[k], title: text[k], width: textWidth(text[k])};
			}			
		}
		$("#GSMNeighTable").grid("destroy", true, true);
		var grid = $("#GSMNeighTable").grid({
			columns: fieldArr,
			params: {
				input1: input1,
				input2: input2,
				input3: input3,
				input4: input4,
				input5: input5,
				input6: input6,
				input7: input7,
				input8: input8,
				input9: input9,
				select: select,
				dateTime: dateTime,
				dataType: type + "数据",
				OptimizationType: "补2G邻区分析"
			},
			dataSource: {
				url: "GSMNeighborAnalysis/GSMNeighAnalysisSplit",
				success: function (data) {
					data = eval("(" + data + ")");
					if (data.error == "error") {
						$("#GSMNeighTable").grid("destroy", true, true);
						layer.open({
							title: "提示",
							content: "数据不存在，请重新选择！"
						});
						l.stop();
						E.stop();
						return;
					}
					$("#filename").val(data.filename);
					
					grid.render(data);
					l.stop();
					E.stop();
					$("#exportBtn").removeAttr("disabled");
				}
			},
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap"
		});
	});
}
function doSearchGSM_MRE_3G(l,E) {
	var select = $("#city").val();
	var dateTime = $("#dateTime").val();
	var type = $("#type").val();

	var params = {
		select: select,
		dateTime: dateTime,
		dataType: type + "数据",
		OptimizationType: "补3G邻区分析"
	};
	$.get("GSMNeighborAnalysis/GSMNeighAnalysis_3G", params, function (data) {
		if (data.error == "error") {
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
			l.stop();
			E.stop();
			return;
		}
		var text=data.text.split(",");
		var field=data.field.split(",");
		var fieldArr = [];
		for (var k in text) {
			if (text[k] == "呼叫数量（目标）呼叫比例（目标）") {
				fieldArr[fieldArr.length] = {field: field[k], title: text[k], width: 250};
			} else {
				fieldArr[fieldArr.length] = {field: field[k], title: text[k], width: textWidth(text[k])};
			}			
		}
		$("#GSMNeighTable").grid("destroy", true, true);
		var grid = $("#GSMNeighTable").grid({
			columns: fieldArr,
			params: {
				select: select,
				dateTime: dateTime,
				dataType: type + "数据",
				OptimizationType: "补3G邻区分析"
			},
			dataSource: {
				url: "GSMNeighborAnalysis/GSMNeighAnalysisSplit_3G",
				success: function (data) {
					data = eval("(" + data + ")");
					if (data.error == "error") {
						$("#GSMNeighTable").grid("destroy", true, true);
						layer.open({
							title: "提示",
							content: "数据不存在，请重新选择！"
						});
						l.stop();
						E.stop();
						return;
					}
					$("#filename").val(data.filename);
					
					grid.render(data);
					l.stop();
					E.stop();
					$("#exportBtn").removeAttr("disabled");
				}
			},
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap"
		});
	});
}
function exportAll() {
	var l = Ladda.create(document.getElementById("search"));
	var E = Ladda.create(document.getElementById("export"));
	l.start();
	E.start();
	var type = $("#type").val();
	// if (type == "MRE") {
	// 	exportAll_MRE(l,E);
	// } else {
	// 	exportFile_CDR(l,E);
	// }
	if (type == "2G") {
		exportAll_MRE_2G(l,E);
	} else if (type == "3G") {
		exportAll_MRE_3G(l,E);
	}
}
function exportAll_MRE_2G(l,E) {

	var select = $("#city").val();
	var dateTime = $("#dateTime").val();
	var input1 = $("#input1").val();
	var input2 = $("#input2").val();
	var input3 = $("#input3").val();
	var input4 = $("#input4").val();
	var input5 = $("#input5").val();
	var input6 = $("#input6").val();
	var input7 = $("#input7").val();
	var input8 = $("#input8").val();
	var input9 = $("#input9").val();
	var type = $("#type").val();

	var params = {
		input1: input1,
		input6: input6,
		input7: input7,
		select: select,
		dateTime: dateTime,
	};
	$.get("GSMNeighborAnalysis/GSMNeighAnalysisAll", params, function (data) {
		if (data.error == "error") {
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
			l.stop();
			E.stop();
			return;
		}
		$("#filenameGSM").val(data.filename);
		layer.open({
			title: "提示",
			content: data.filename
		});
		fileDownload(data.filename);
		l.stop();
		E.stop();
	});
}
function exportAll_MRE_3G(l,E) {

	var select = $("#city").val();
	var dateTime = $("#dateTime").val();
	var type = $("#type").val();

	var params = {
		select: select,
		dateTime: dateTime,
	};
	$.get("GSMNeighborAnalysis/GSMNeighAnalysisAll_3G", params, function (data) {
		if (data.error == "error") {
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
			l.stop();
			E.stop();
			return;
		}
		$("#filenameGSM").val(data.filename);
		layer.open({
			title: "提示",
			content: data.filename
		});
		fileDownload(data.filename);
		l.stop();
		E.stop();
	});
}

function fileSave() {
	var fileName = $("#filename").val();
	layer.open({
		title: "提示",
		content: fileName
	});
	if (fileName != "") {
		fileDownload(fileName);
	}
	else {
		layer.open({
			title: "提示",
			content: "No file generated so far!"
		});
	}
}

function textWidth(text) {
	var length = text.length;
	if (length > 15) {
		return length * 10;
	}
	return 150;
}

function query_CDR(l,E) {
	var dataBase = $("#city").val();
	var dateTime = $("#dateTime").val();
	var type = $("#type").val();
	var params = {
		dataBase: dataBase,
		dateTime: dateTime,
		dataType: type + "数据",
		OptimizationType: "补2G邻区分析"
	};

	$.get("GSMNeighborAnalysis/getCdrServeNeighDataHeader", params, function (data) {
		if (data.error == "error") {
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
			l.stop();
			E.stop();
			return;
		}

		var text=data.text.split(",");
		var field=data.field.split(",");
		var fieldArr = [];
		for (var k in text) {
			fieldArr[fieldArr.length] = {field: field[k], title: text[k], width: textWidth(text[k])};
		}
		$("#GSMNeighTable").grid("destroy", true, true);
		var grid = $("#GSMNeighTable").grid({
			columns: fieldArr,
			params: params,
			dataSource: {
				url: "GSMNeighborAnalysis/getCdrServeNeighData",
				success: function (data) {
					data = eval("(" + data + ")");
					if (data.error == "error") {
						$("#GSMNeighTable").grid("destroy", true, true);
						layer.open({
							title: "提示",
							content: "数据不存在，请重新选择！"
						});
						l.stop();
						E.stop();
						return;
					}
					grid.render(data);

					l.stop();
					E.stop();
				}
			},
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap"
		});

	});
}


function exportFile_CDR(l,E) {
	var dataBase = $("#city").val();
	var dateTime = $("#dateTime").val();
	var type = $("#type").val();

	var params = {
		dataBase: dataBase,
		dateTime: dateTime,
		dataType: type + "数据",
		OptimizationType: "补2G邻区分析"
	};

	var url = "GSMNeighborAnalysis/getAllCdrServeNeighData";
	$.get(url, params, function (data) {
		data = eval("(" + data + ")");
		if (data.error == "error") {
			$("#GSMNeighTable").grid("destroy", true, true);
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
			l.stop();
			E.stop();
			return;
		}
		if (data.result == "true") {
			var filepath = data.filename.replace("\\", "");
			download(filepath, "", "data:text/csv;charset=utf-8");
		} else {
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
		}
		l.stop();
		E.stop();
	});
}
//导入白名单
function importWhiteList() {
	$("#import_modal").modal();
	$("#fileImportName").val("");
	$("#fileImport").val("");
	// $("#dataType").val(type);
}
function toName(self) {
	$("#fileImportName").val(self.value);
}
function importFile() {
	var type = $("#type").val();
	var city = $("#city").val();
	var params = {
		table: "NeighOptimizationWhiteList",
		city: city,
		dataType: type + "数据",
		OptimizationType: "补2G邻区分析"
	};
	var l = Ladda.create(document.getElementById("importBtn"));
	l.start();
	$.ajaxFileUpload({
		url: "GSMNeighborAnalysis/uploadFile",
		data: params,
		fileElementId: "fileImport",
		secureuri: false,
		dataType: "json",
		type: "post",
		success: function (data, status) {
			params.fileName = data;
			$.post("GSMNeighborAnalysis/getMREFileContent", params, function (data) {
				if (data) {
					$("#import_modal").modal("hide");
					layer.open({
						title: "提示",
						content: "上传成功"
					});
				}
				l.stop();
			});
		},
		error: function (data, status, e) {
			layer.open({
				title: "提示",
				content: "上传失败"
			});
			l.stop();
		}
	});
}
//导出白名单
function exportWhiteList() {
	var city = $("#city").val();
	var type = $("#type").val();
	var params = {
		city: city,
		dataType: type + "数据",
		OptimizationType: "补2G邻区分析"
	};
	var E = Ladda.create(document.getElementById("exportWhiteList"));
	E.start();
	$.post("GSMNeighborAnalysis/exportWhiteList", params, function (data) {
		E.stop();
		if (data.result) {
			fileDownload(data.fileName);
		} else {
			// alert("");
			layer.open({
				title: "提示",
				content: "没有记录"
			});
		}
	});
}