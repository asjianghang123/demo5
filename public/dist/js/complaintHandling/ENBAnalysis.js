$(function () {
	toogle("ENBAnalysis");
	initCitys();
	//setHQSelect();
	//绑定信令图的tab页面，保证页面出来才开始画图，避免画图错位的问题
	$("#table_tab_0_nav").on("shown.bs.tab", function () {
		if (chartDatas) {
			setChart();
		} else {
			$("#resultView").empty();
		}
	});
});
/*function setHQSelect(){
 $.get("common/json/ctrProcess.json",null,function(data){
 var processTreeData =eval("("+data+")");
 var process = $("#eventName").select2({
 placeholder: "请选择流程",
 //allowClear: true,
 data:processTreeData
 });
 setTime();
 });
 }*/
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
	$.get("ENBAnalysis/getCitys", null, function (data) {
		data = JSON.parse(data);
		$("#citys").multiselect("dataprovider", data);
		//setHQSelect();
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
	$.post("ENBAnalysis/getENBAnalysisDate", params, function (data) {
		data = JSON.parse(data);
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
		//var table = $("#eventName").val();
		var params = {
			dataBase: city
		};
		$.post("ENBAnalysis/getENBAnalysisDate", params, function (data) {
			data = JSON.parse(data);
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
	/*$("#eventName").change(function(){
	 var city = $("#citys").val();
	 var table = $("#eventName").val();
	 var params = {
	 dataBase:city,
	 table : table
	 };
	 $.post("ENBAnalysis/getENBAnalysisDate", params, function(data){
	 data = JSON.parse(data);
	 var sdata = [];
	 for(var i=0; i<data.length; i++){
	 if(data[i] === today){
	 continue;
	 }
	 sdata.push(data[i]);
	 }
	 sdata.push(today);
	 $("#date").datepicker("setValues", sdata);
	 })
	 });*/
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
	var task = $("#citys").val();
	var date = $("#date").val();
	if (!date || date == "") {
		// alert("");
		layer.open({
			title: "提示",
			content: "查询条件不能为空"
		});
		return false;
	}
	
	var params = {
		date: date,
		db: task
	};
	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	queryBtn.start();
	$.post("ENBAnalysis/getSuccessChartData", params, function (data) {
		queryBtn.stop();
		if (data == "error") {
			// alert("");
			layer.open({
				title: "提示",
				content: "没有数据"
			});
			return;
		}
		data = JSON.parse(data);
		var gaugeOptions = {
			chart: {
				type: "solidgauge"
			},
			title: null,
			pane: {
				center: ["50%", "85%"],
				size: "100%",
				startAngle: -90,
				endAngle: 90,
				background: {
					backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || "#EEE",
					innerRadius: "60%",
					outerRadius: "100%",
					shape: "arc"
				}
			},
			tooltip: {
				enabled: false
			},
			// the value axis
			yAxis: {
				stops: [
					[0.1, "#DF5353"], // green
					[0.5, "#DDDF0D"], // yellow
					[0.9, "#55BF3B"] // red
				],
				lineWidth: 0,
				minorTickInterval: null,
				tickPixelInterval: 400,
				tickWidth: 0,
				title: {
					y: -70
				},
				labels: {
					y: 16
				}
			},
			plotOptions: {
				solidgauge: {
					dataLabels: {
						y: 5,
						borderWidth: 0,
						useHTML: true
					}
				}
			}
		};
		$("#successChart").empty();
		for (var i in data) {
			$("#successChart").append("<div id='" + i + "' class='col-sm-2' style='height:200px'></div>");
			$("#" + i).highcharts(Highcharts.merge(gaugeOptions, {
				plotOptions: {
					solidgauge: {
						events: {
							click: function (event) {
								switchTab(table_tab_1, table_tab_0, "chart");
								$("#L3Chart").empty();
								$("#L3Table").grid("destroy", true, true);
								$("#detailTable").grid("destroy", true, true);
								query1(this.name);
							}
						}
					}
				},
				yAxis: {
					min: 0,
					max: 100,
					title: {
						text: i
					}
				},
				credits: {
					enabled: false
				},
				series: [{
					name: i,
					data: [data[i]],
					dataLabels: {
						format: '<div style="text-align:center"><span style="font-size:25px;color:' +
						((Highcharts.theme && Highcharts.theme.contrastTextColor) || "black") + '">{y}</span>' +
						'<span style="font-size:12px;color:silver">%</span></div>'
					},
					tooltip: {
						valueSuffix: "%"
					}
				}]
			}));
		}
	});
}
function query1(table) {
	switchTab(table_tab_1, table_tab_0, "chart");
	var task = $("#citys").val();
	var date = $("#date").val();
	$("#eventName").val(table);
	var params = {
		date: date,
		db: task,
		table: table
	};
	//var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	//queryBtn.start();
	showMobilityResultDistribution(params, queryBtn);
}

function showMobilityResultDistribution(params, queryBtn) {
	$("#chart_loadingImg").show();
	$.post("ENBAnalysis/getChartData", params, function (data) {
		$("#chart_loadingImg").hide();
		chartDatas = data;
		//var categories = data["categories"];
		//var series = data["series"];
		if (data.flag) {
			// alert("");
			layer.open({
				title: "提示",
				content: "没有数据"
			});
			//queryBtn.stop();
			return;
		}
		setChart("#L3Chart", data);
		doSearchEvent_table(data.table);
		$("#detailTable").grid("destroy", true, true);
		//queryBtn.stop();
	});
}
function setChart(block, data) {
	$(block).highcharts({
		chart: {
			type: "bar"
		},
		title: {
			text: $("#eventName").val()
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
		/*legend: {
		 layout: "vertical",
		 align: "right",
		 verticalAlign: "top",
		 x: -40,
		 y: 80,
		 floating: true,
		 borderWidth: 1,
		 backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || "#FFFFFF"),
		 shadow: true
		 },*/
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
							result = event.point.category;
							$("#selectedResult").val(result);
							//$(".loadingImg").show();
							detailTable(result);

						}
					}
				}
			}

		},
		series: [data["series"]]
	});
}
function DrillDownColMobilityResult() {
	var fieldArr = [];
	fieldArr[fieldArr.length] = {field: "result", title: "result", width: 300};
	fieldArr[fieldArr.length] = {field: "num", title: "num", width: 300};
	var fieldCol = new Array(fieldArr);
	return fieldArr;
}
function showTable(data, fieldCol) {
	$("#L3Table").grid("destroy", true, true);
	var grid = $("#L3Table").grid({
		columns: fieldCol,
		dataSource: data,
		pager: {limit: 10, sizes: [10, 20, 50, 100]},
		autoScroll: true,
		uiLibrary: "bootstrap",
		primaryKey: "id",
		autoLoad: true
	});
	grid.on("rowSelect", function (e, $row, id, record) {
		var result;
		result = $row.children().eq(0).children().html();
		$("#selectedResult").val(result);
		//$(".loadingImg").show();
		detailTable(result);
	});

}
function doSearchEvent_table(data) {
	var fieldCol = DrillDownColMobilityResult();
	if (fieldCol == false) {
		return false;
	}
	showTable(data, fieldCol);
}

function detailTable(result) {
	var task = $("#citys").val();
	var date = $("#date").val();
	var table = $("#eventName").val();
	var params = {
		date: date,
		db: task,
		result: result,
		table: table
	};

	$.post("ENBAnalysis/getdetailDataHeader", params, function (data) {
		if (data.error == "error") {
			// alert("");
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
			return;
		}
		var fieldArr = [];
		for (var k in data) {
			if (k == "establCause" || k == "result" || k == "3gppCauseGroup" || k == "3gppCause") {
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
				url: "ENBAnalysis/getdetailData",
				success: function (data) {
					data = JSON.parse(data);
					if (data.error == "error") {
						$("#detailTable").grid("destroy", true, true);
						// alert("");
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
	var table = $("#eventName").val();
	var result = $("#selectedResult").val();
	if (!result) {
		// alert("");
		layer.open({
			title: "提示",
			content: "请先查询详情在进行导出！"
		});
		return;
	}
	var params = {
		date: date,
		db: task,
		result: result,
		table: table
	};
	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	exportBtn.start();
	$.post("ENBAnalysis/exportFile", params, function (data) {
		exportBtn.stop();
		data = eval("(" + data + ")");
		if (data.result == "true") {
			var filepath = data.filename.replace("\\", "");
			download(filepath, "", "data:text/csv;charset=utf-8");
		} else {
			// alert("");
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
