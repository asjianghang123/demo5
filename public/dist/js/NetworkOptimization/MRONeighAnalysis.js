$(function () {
	getAllCity("#city");
	typeSelect();
	setTime();
	$("#input1").val(3);
	$("#input2").val(3);
	$("#input3").val(50);
	$("#input4").val(10);
	$("#input5").val(50);
	$("#input6").val(-115);
	$("#input7").val(-10);
	$("#input8").val(-110);
	$("#input9").val(3);
	$("#input10").val(50);
	$("#input11").val(50);
	$("#input12").val(-115);
	$("#input13").val(-10);
	$("#input14").val(-110);
	$("#input1Temp").val(3);
	$("#input2Temp").val(3);
	$("#input3Temp").val(50);
	$("#input4Temp").val(10);
	$("#input5Temp").val(50);
	$("#input6Temp").val(-115);
	$("#input7Temp").val(-10);
	$("#input8Temp").val(-110);
	$("#input9Temp").val(3);
	$("#input10Temp").val(50);
	$("#input11Temp").val(50);
	$("#input12Temp").val(-115);
	$("#input13Temp").val(-10);
	$("#input14Temp").val(-110);

	$("#dateTime").datepicker({format: "yyyy-mm-dd"});  //返回日期
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;

	console.log(today);
	var params = {
		city: getFirstCity(),
		type: $("#type").val()
	};
	$.get("MROServeNeighAnalysis/getDate", params, function (data) {
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
		$.get("MROServeNeighAnalysis/getDate", params, function (data) {
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
		$.get("MROServeNeighAnalysis/getDate", params, function (data) {
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
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");

	toogle("MROServeNeighAnalysis");
});
function setTime(){

}

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
function getAllCity(cityId) {
	$(cityId).multiselect({
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
	var url = "MROServeNeighAnalysis/getAllCity";
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
			$(cityId).multiselect("dataprovider", newOptions);
		}
	});
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
	$("#input10").val($("#input10Temp").val());
	$("#input11").val($("#input11Temp").val());
	$("#input12").val($("#input12Temp").val());
	$("#input13").val($("#input13Temp").val());
	$("#input14").val($("#input14Temp").val());
	$("#config_information").modal("hide");
}
//mro
function mroOpenConfigInfo() {
	$("#config_information").modal();
}
function mroUpdateConfigInfo() {
	$("#input9").val($("#input9Temp").val());
	$("#input10").val($("#input10Temp").val());
	$("#input11").val($("#input11Temp").val());
	$("#input12").val($("#input12Temp").val());
	$("#input13").val($("#input13Temp").val());
	$("#input14").val($("#input14Temp").val());
	$("#config_information_mro").modal("hide");
}
function query(){
	var type = $("#type").val();
	var l = Ladda.create(document.getElementById("search"));
	var E = Ladda.create(document.getElementById("export"));

	l.start();
	E.start();
	if (type == "MRO") {
		query_MRO(l,E);
	} else {
		query_mre(l,E);
	}
}
function query_MRO(l,E) {
	var dataBase = $("#city").val();
	var dateTime = $("#dateTime").val();
	var input9 = $("#input9").val();
	var input10 = $("#input10").val();
	var input11 = $("#input11").val();
	var input12 = $("#input12").val();
	var input13 = $("#input13").val();
	var input14 = $("#input14").val();
	var type = $("#type").val();

	if (input9 == "") {
		input9 = 10;
	}
	if (input10 == "") {
		input10 = 2;
	}
	if (input11 == "") {
		input11 = 50;
	}
	if (input12 == "") {
		input12 = -115;
	}
	if (input13 == "") {
		input13 = -10;
	}
	if (input14 == "") {
		input14 = -110;
	}
	var params = {
		input9: input9,
		input10: input10,
		input11: input11,
		input12: input12,
		input13: input13,
		input14: input14,
		dataBase: dataBase,
		dateTime: dateTime,
		dataType: type + "数据",
		OptimizationType: "补4G同频邻区分析"
	};

	$.get("MROServeNeighAnalysis/getMroServeNeighDataHeader", params, function (data) {
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
		$("#mroServeNeighTable").grid("destroy", true, true);
		var grid = $("#mroServeNeighTable").grid({
			columns: fieldArr,
			params: params,
			dataSource: {
				url: "MROServeNeighAnalysis/getMroServeNeighData",
				success: function (data) {
					data = eval("(" + data + ")");
					if (data.error == "error") {
						$("#mroServeNeighTable").grid("destroy", true, true);
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
//mre数据查询
function query_mre(l,E) {
	var dataBase = $("#city").val();
	var dateTime = $("#dateTime").val();
	var input1 = $("#input1").val();
	var input2 = $("#input2").val();
	var input3 = $("#input3").val();
	var input4 = $("#input4").val();
	var input5 = $("#input5").val();
	var input6 = $("#input6").val();
	var input7 = $("#input7").val();
	var input8 = $("#input8").val();
	var type = $("#type").val();

	if (input1 == "") {
		input1 = 3;
	}
	if (input2 == "") {
		input2 = 3;
	}
	if (input3 == "") {
		input3 = 50;
	}
	if (input4 == "") {
		input4 = 10;
	}
	if (input5 == "") {
		input5 = 50;
	}
	if (input6 == "") {
		input6 = -115;
	}
	if (input7 == "") {
		input7 = -10;
	}
	if (input8 == "") {
		input8 = -110;
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
		dataBase: dataBase,
		dateTime: dateTime,
		dataType: type + "数据",
		OptimizationType: "补4G同频邻区分析"
	};

	$.get("MROServeNeighAnalysis/getMreServeNeighDataHeader", params, function (data) {
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
		$("#mroServeNeighTable").grid("destroy", true, true);
		var grid = $("#mroServeNeighTable").grid({
			columns: fieldArr,
			params: params,
			dataSource: {
				url: "MROServeNeighAnalysis/getMreServeNeighData",
				success: function (data) {
					data = eval("(" + data + ")");
					if (data.error == "error") {
						$("#mroServeNeighTable").grid("destroy", true, true);
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
function textWidth(text) {
	var length = text.length;
	if (length > 15) {
		return length * 10;
	}
	return 150;
}

function exportAll(){
	var type = $("#type").val();
	var l = Ladda.create(document.getElementById("search"));
	var E = Ladda.create(document.getElementById("export"));

	l.start();
	E.start();
	if (type == "MRO") {
		exportAll_MRO(l,E);
	} else {
		exportAll_mre(l,E);
	}
}
function exportAll_MRO(l,E) {

	var dataBase = $("#city").val();
	var dateTime = $("#dateTime").val();
	var input9 = $("#input9").val();
	var input10 = $("#input10").val();
	var input11 = $("#input11").val();
	var input12 = $("#input12").val();
	var input13 = $("#input13").val();
	var input14 = $("#input14").val();

	if (input9 == "") {
		input9 = 10;
	}
	if (input10 == "") {
		input10 = 2;
	}
	if (input11 == "") {
		input11 = 50;
	}
	if (input12 == "") {
		input12 = -115;
	}
	if (input13 == "") {
		input13 = -10;
	}
	if (input14 == "") {
		input14 = -110;
	}
	var params = {
		input9: input9,
		input10: input10,
		input11: input11,
		input12: input12,
		input13: input13,
		input14: input14,
		dataBase: dataBase,
		dateTime: dateTime,
	};

	$.get("MROServeNeighAnalysis/getAllMroServeNeighData", params, function (data) {
		if (data.error == "error") {
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
			l.stop();
			E.stop();
			return;
		}
		$("#filenameLte").val(data.filename);

		layer.open({
			title: "提示",
			content: data.filename
		});
		download(data.filename);

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
		download(fileName);
	}
	else {
		layer.open({
			title: "提示",
			content: "No file generated so far!"
		});
	}
}
function exportAll_mre(l,E) {
	var dataBase = $("#city").val();
	var dateTime = $("#dateTime").val();
	var input1 = $("#input1").val();
	var input2 = $("#input2").val();
	var input3 = $("#input3").val();
	var input4 = $("#input4").val();
	var input5 = $("#input5").val();
	var input6 = $("#input6").val();
	var input7 = $("#input7").val();
	var input8 = $("#input8").val();

	if (input1 == "") {
		input1 = 3;
	}
	if (input2 == "") {
		input2 = 3;
	}
	if (input3 == "") {
		input3 = 50;
	}
	if (input4 == "") {
		input4 = 10;
	}
	if (input5 == "") {
		input5 = 50;
	}
	if (input6 == "") {
		input6 = -115;
	}
	if (input7 == "") {
		input7 = -10;
	}
	if (input8 == "") {
		input8 = -110;
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
		dataBase: dataBase,
		dateTime: dateTime,
	};

	$.get("MROServeNeighAnalysis/getAllMreServeNeighData", params, function (data) {
		if (data.error == "error") {
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
			l.stop();
			E.stop();
			return;
		}
		$("#filenameLte").val(data.filename);

		layer.open({
			title: "提示",
			content: data.filename
		});
		download(data.filename);

		l.stop();
		E.stop();
	});
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
function getBrowerInfo() {
	var uerAgent = navigator.userAgent.toLowerCase();
	var format = /(msie|firefox|chrome|opera|version).*?([\d.]+)/;
	var matches = uerAgent.match(format);
	return matches[1].replace(/version/, "'safari");
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
		OptimizationType: "补4G同频邻区分析"
	};
	var l = Ladda.create(document.getElementById("importBtn"));
	l.start();
	$.ajaxFileUpload({
		url: "MROServeNeighAnalysis/uploadFile",
		data: params,
		fileElementId: "fileImport",
		secureuri: false,
		dataType: "json",
		type: "post",
		success: function (data, status) {
			params.fileName = data;
			$.post("MROServeNeighAnalysis/getMREFileContent", params, function (data) {
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
		table: "NeighOptimizationWhiteList",
		city: city,
		dataType: type + "数据",
		OptimizationType: "补4G同频邻区分析"
	};
	var E = Ladda.create(document.getElementById("exportWhiteList"));
	E.start();
	$.post("MROServeNeighAnalysis/exportWhiteList", params, function (data) {
		E.stop();
		if (data.result) {
			fileDownload(data.fileName);
		} else {
			layer.open({
				title: "提示",
				content: "没有记录"
			});
		}
	});
}