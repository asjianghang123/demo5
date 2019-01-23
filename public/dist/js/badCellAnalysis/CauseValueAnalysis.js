$(function () {
	toogle("CauseValueAnalysis");

	initCitys();
	// setTime();

	//绑定信令图的tab页面，保证页面出来才开始画图，避免画图错位的问题
	$("#table_tab_0_nav").on("shown.bs.tab", function () {
		if (chartDatas) {
			setChart();
		} else {
			$("#resultView").empty();
		}
	});
});
var chartDatas = "";
function initCitys() {
	$("#citys").multiselect({
		//dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "选择城市",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有城市",
		maxHeight: 200,
		maxWidth: "100%"
	});
	$.get("CauseValueAnalysis/getCitys", null, function (data) {
		data = JSON.parse(data);
		var newData = [];
		for (var i in data) {
			var CHCity = data[i].split("-")[0];
			var dataBase = data[i].split("-")[1];
			newData.push({"label": CHCity, "value": dataBase});
		}
		$("#citys").multiselect("dataprovider", newData);
		setTime();
	});
}

function setTime() {
	$("#date").datepicker({format: "yyyy-mm-dd"});  //返回日期
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;
	//$("#date").datepicker("setValue", nowTemp);

	var params = {
		dataBase: $("#citys").val()
	};
	$.post("CauseValueAnalysis/getCauseValueAnalysisData", params, function (data) {
		//data = JSON.parse(data);
		var sdata = [];
		for (var i = 0; i < data.length; i++) {
			if (data[i] === today) {
				continue;
			}
			sdata.push(data[i]);
		}
		sdata.push(today);
		$("#date").datepicker("setValues", sdata);
	});
	$("#citys").change(function () {
		var city = $("#citys").val();
		var params = {
			dataBase: city
		};
		$.post("CauseValueAnalysis/getCauseValueAnalysisData", params, function (data) {
			//data = JSON.parse(data);
			var sdata = [];
			for (var i = 0; i < data.length; i++) {
				if (data[i] === today) {
					continue;
				}
				sdata.push(data[i]);
			}
			sdata.push(today);
			$("#date").datepicker("setValues", sdata);
		});
	});
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#date").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");
}

function query() {
	switchTab(table_tab_1, table_tab_0, "chart");
	var task = $("#citys").val();
	var date = $("#date").val();
	var params = {
		date: date,
		db: task
	};
	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	queryBtn.start();
	showMobilityResultDistribution(params, queryBtn);
	doSearchEvent_table();
}


function showMobilityResultDistribution(params, queryBtn) {
	$("#signalingChart").html("<img class='col-md-offset-5' src='dist/img/ajax-loader.gif'>");
	$.post("CauseValueAnalysis/getChartData", params, function (data) {
		$("#chart_loadingImg").hide();
		$("#signalingChart").html("");
		chartDatas = data;
		var categories = data["categories"];
		var series = data["series"];
		setChart("#signalingChart", data, params);
		queryBtn.stop();
	});

}
function setChart(block, data, params) {
	var height = 400;
	if (data["categories"].length > 8) {
		height = data["categories"].length * 30;
	}
	
	$(block).css("height", height + "px");
	$(block).highcharts({
		chart: {
			type: "bar"
		},
		title: {
			text: "Share"
		},
		tooltip: {
			pointFormat: "{point.y}% Click to the detail"
		},
		xAxis: {
			categories: data["categories"],
			title: {
				text: null
			}
		},
		credits: {enabled: false},
		plotOptions: {
			bar: {
				allowPointSelect: true,
				cursor: "pointer",
				dataLabels: {
					enabled: true,
					color: "#000000",
					connectorColor: "#000000",
					format: "<b>{point.name}</b> {point.y}%"
				},
				point: {
					events: {
						click: function (event) {
							var result = [];
							result = event.point.category.split("/");
							$("#selectedResult").val(result);
							detailTable(result);
						}
					}
				}
			}

		},
		series: [data["series"]]
	});
}
function showDrillDownChartData(params) {
	$.post("CauseValueAnalysis/getDrillDownChartData", params, function (data) {
		$("#chart_loadingImg").hide();
		setChart("#signalingChart", data, params);
		$("#backBtn").css("display", "block");
		$("#backBtn").click(function () {
			setChart("#signalingChart", chartDatas, params);
			$("#backBtn").css("display", "none");
		});
	});

}
function DrillDownColMobilityResult() {
	var fieldArr = [];
	fieldArr[fieldArr.length] = {field: "causeCode", title: "causeCode", width: 300};
	fieldArr[fieldArr.length] = {field: "subCauseCode", title: "subCauseCode", width: 300};
	fieldArr[fieldArr.length] = {field: "times", title: "times", width: 100};
	fieldArr[fieldArr.length] = {field: "timesFailure", title: "timesFailure", width: 100};
	fieldArr[fieldArr.length] = {field: "timesTotal", title: "timesTotal", width: 100};
	fieldArr[fieldArr.length] = {field: "ratioFailure", title: "ratioFailure", width: 100};
	fieldArr[fieldArr.length] = {field: "ratioTotal", title: "ratioTotal", width: 100};
	var fieldCol = new Array(fieldArr);
	return fieldArr;
}
function showTable(params, fieldCol) {
	$("#signalingTable").grid("destroy", true, true);
	var grid = $("#signalingTable").grid({
		columns: fieldCol,
		dataSource: {
			url: "CauseValueAnalysis/getTableData",
			success: function (data) {
				//data = eval("("+data+")");
				if (data.message) {
					// alert(data.message);
					layer.open({
						title: "提示",
						content: data.message
					});
					return;
				}
				grid.render(data);
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
		var result = [];
		result[0] = $row.children().eq(0).children().html();
		result[1] = $row.children().eq(1).children().html();
		$("#selectedResult").val(result);
		$(".loadingImg").show();
		detailTable(result);
	});

}
function doSearchEvent_table() {
	var task = $("#citys").val();
	var date = $("#date").val();
	var params = {
		date: date,
		db: task
	};
	var fieldCol = DrillDownColMobilityResult();
	if (fieldCol == false) {
		return false;
	}
	showTable(params, fieldCol);
}

function detailTable(result) {
	var task = $("#citys").val();
	var date = $("#date").val();
	var params = {
		date: date,
		db: task,
		result: result
	};

	$.get("CauseValueAnalysis/getdetailDataHeader", params, function (data) {
		$("#chart_loadingImg").hide();
		if (data.error == "error") {
			// alert("数据不存在，请重新选择！");
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
			return;
		}
		var fieldArr = [];
		for (var k in data) {
			if (k == "causeCode" || k == "subCauseCode") {
				fieldArr[fieldArr.length] = {field: k, title: k, width: 300};
			} else {
				fieldArr[fieldArr.length] = {field: k, title: k, width: textWidth(k)};
			}
		}
		$("#detailTable").grid("destroy", true, true);
		var grid = $("#detailTable").grid({
			columns: fieldArr,
			params: params,
			dataSource: {
				url: "CauseValueAnalysis/getdetailData",
				success: function (data) {
					if (data.error == "error") {
						$("#detailTable").grid("destroy", true, true);
						// alert("数据不存在，请重新选择！");
						layer.open({
							title: "提示",
							content: "数据不存在，请重新选择！"
						});
						return;
					}
					grid.render(data);
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
function exportFile() {
	var task = $("#citys").val();
	var date = $("#date").val();
	var result = $("#selectedResult").val();
	if (!result) {
		// alert("请先查询详情在进行导出！");
		layer.open({
			title: "提示",
			content: "请先查询详情在进行导出！"
		});
		return;
	}
	var params = {
		date: date,
		db: task,
		result: result
	};
	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	exportBtn.start();
	$.post("CauseValueAnalysis/exportFile", params, function (data) {
		exportBtn.stop();
		data = eval("(" + data + ")");
		if (data.result == "true") {
			var filepath = data.filename.replace("\\", "");
			download(filepath, "", "data:text/csv;charset=utf-8");
		} else {
			// alert("There is error occured!");
			layer.open({
				title: "提示",
				content: "There is error occured!"
			});
		}
	});
}

function switchTab(div1, div2, type) {
	$(div2).removeClass("active");
	$(div1).addClass("active");
	/* if(type=="table"){
	 doSearchEvent_table();
	 }*/
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
