$(document).ready(function () {

	$("#allHour").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "请选择小时",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有平台类型",
		maxHeight: 200,
		maxWidth: "100%"
	});
	initCitys();
	setTime();
	initTimeList();
	toogle("highInterferenceCell");
});

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

	var url = "highInterferenceCell/getCitys";
	$.get(url, null, function (data) {
		data = eval("(" + data + ")");
		$("#citys").multiselect("dataprovider", data);
	});
}
function setTime() {
	$("#startDate").datepicker({format: "yyyy-mm-dd"});  //返回日期
	$("#endDate").datepicker({format: "yyyy-mm-dd"});

	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;

	console.log(today);
	var params = {
		city: getFirstCity()
	};
	$.get("highInterferenceCell/highStartTime", params, function (data) {
		var sdata = [];
		for (var i = 0; i < data.length; i++) {
			if (data[i] === today) {
				continue;
			}
			sdata.push(data[i]);
		}
		sdata.push(today);
		$("#startDate").datepicker("setValues", sdata);
		$("#endDate").datepicker("setValues", sdata);
	});
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

	var checkin = $("#startDate").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");
	var checkout = $("#endDate").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkout.hide();
	}).data("datepicker");
}
var bsc_type = function (route, block, params) {
	$.ajax({
		type: "GET",
		url: route,
		//data : {range : "day"},
		data: params,
		dataType: "json",
		/*beforeSend : function () {
		 $(block).html("<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">");
		 },*/
		success: function (data) {
			// var dataArr = eval("("+data+")");
			$(block).html("");
			if (data.length == 0) {
				return;
			}
			console.log(data);
			$(block).highcharts({
				chart: {
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false,
					type: "pie"
				},
				title: {
					text: null
				},
				subtitle: {
					text: null
				},
				tooltip: {
					pointFormat: ": <b>{point.y}({point.percentage:.2f} %)</b>"
				},
				plotOptions: {
					pie: {
						size: "130px",
						allowPointSelect: true,
						cursor: "pointer",
						dataLabels: {
							enabled: true,
							format: "<b>{point.name}</b>: {point.percentage:.2f} %",
							style: {
								color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || "black"
							}
						},
						showInLegend: true
					}
				},
				credits: {
					enabled: false,
				},
				series: [{
					name: "Brands",
					colorByPoint: true,
					data: data.series
				}]
			});
		}
	});
};
function query() {
	var highInterfereCellType = $("#cellType").val();
	var highInterfereCellName = $("#cell").val();
	var highInterfereCellDateFrom = $("#startDate").val();
	var highInterfereCellDateTo = $("#endDate").val();
	var citys = $("#citys").val();
	var table = "";
	if (highInterfereCellType == "avg") {
		table = "interfereCell_avg";
	} else if (highInterfereCellType == "one") {
		table = "interfereCell_one";
	}
	if (highInterfereCellDateFrom == "") {
		// alert("起始时间不能为空!");
		layer.open({
			title: "提示",
			content: "起始时间不能为空"
		});
		return false;
	}
	if (highInterfereCellDateTo == "") {
		// alert("结束时间不能为空!");
		layer.open({
			title: "提示",
			content: "结束时间不能为空"
		});
		return false;
	}
	if (highInterfereCellDateFrom > highInterfereCellDateTo) {
		// alert("结束时间不能早于起始时间!");
		layer.open({
			title: "提示",
			content: "结束时间不能早于起始时间"
		});
		return false;
	}
	
	var params = {
		table: table,
		city: citys.join(","),
		cell: highInterfereCellName,
		datefrom: highInterfereCellDateFrom,
		dateto: highInterfereCellDateTo,
		hours: $("#allHour").val()
	};

	var fieldArr = [];
	var text = "city,subNetwork,cell,times";
	var textArr = text.split(",");
	for (var i in textArr) {
		fieldArr[fieldArr.length] = {
			field: textArr[fieldArr.length],
			title: textArr[fieldArr.length],
			width: 250,
			sortable: true
		};
	}

	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	queryBtn.start();
	exportBtn.start();
	$("#cellTable").grid("destroy", true, true);
	var grid = $("#cellTable").grid({
		columns: fieldArr,
		dataSource: {
			url: "highInterferenceCell/getCellData",
			success: function (data) {
				data = JSON.parse(data);
				grid.render(data);
			}
		},
		params: params,
		pager: {limit: 10, sizes: [10, 20, 50, 100]},
		autoScroll: true,
		uiLibrary: "bootstrap",
		primaryKey: "cell",
		autoLoad: true
	});
	queryBtn.stop();
	exportBtn.stop();
	grid.on("rowSelect", function (e, $row, id, record) {

		// getAlarmData(record);
		getTimeList(record);
		setTimeChart(record);
		$("#alarmNumSpan").removeClass();//小区级告警数量：
		$("#LteNumSpan").removeClass();//建议补4G邻区数量：
		$("#GsmNumSpan").removeClass();//建议补2G邻区数量：
		$("#weakCoverNumSpan").removeClass();//弱覆盖小区频次：
		$("#erbsAlarmNumSpan").removeClass();//基站级告警数量：
		$("#highInterfereNumSpan").removeClass();//高干扰小区频次：
		$("#overlapCeakCoverNumSpan").removeClass();
		$("#timeDomainContainer").val(record.cell);
		$("#firstOrderConflictNumSpan").removeClass();
		$("#secondOrderConflictNumSpan").removeClass();
		$("#prbHighInterfereNumSpan").removeClass();

		var alarmNum = $("#timeDomainContainer").val();
		$.get("highInterferenceCell/alarmNum", {
			cell: alarmNum,
			startTime: $("#startDate").val(),
			endTime: $("#endDate").val(),
			hours: $("#allHour").val()
		}, function (data) {
			data = eval("(" + data + ")");
			$("#alarmNum").val(data[1]);
			$("#alarmNumHour").val(data[3]);
			if ($("#alarmNum").val() == 0 && ($("#alarmNumHour").val() == 0 || $("#alarmNumHour").val() == "null")) {
				$("#alarmNumSpan").removeClass();
				$("#alarmNumSpan").addClass("glyphicon glyphicon-ok-circle");
				$("#alarmNumSpan").css("color", "green");
			} else {
				$("#alarmNumSpan").removeClass();
				$("#alarmNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
				$("#alarmNumSpan").css("color", "red");
			}
			$("#erbsAlarmNum").val(data[0]);
			$("#erbsAlarmNumHour").val(data[2]);
			if ($("#erbsAlarmNum").val() == 0 && ($("#erbsAlarmNumHour").val() == 0 || $("#erbsAlarmNumHour").val() == "null")) {
				$("#erbsAlarmNumSpan").removeClass();
				$("#erbsAlarmNumSpan").addClass("glyphicon glyphicon-ok-circle");
				$("#erbsAlarmNumSpan").css("color", "green");
			} else {
				$("#erbsAlarmNumSpan").removeClass();
				$("#erbsAlarmNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
				$("#erbsAlarmNumSpan").css("color", "red");
			}
			/*$("#alarmNum").val(data[1]);
			 if($("#alarmNum").val()==0){
			 $("#alarmNumSpan").removeClass();
			 $("#alarmNumSpan").addClass("glyphicon glyphicon-ok-circle");
			 $("#alarmNumSpan").css("color","green");
			 }else{
			 $("#alarmNumSpan").removeClass();
			 $("#alarmNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
			 $("#alarmNumSpan").css("color","red");
			 }
			 $("#erbsAlarmNum").val(data[0]);
			 if($("#erbsAlarmNum").val()==0){
			 $("#erbsAlarmNumSpan").removeClass();
			 $("#erbsAlarmNumSpan").addClass("glyphicon glyphicon-ok-circle");
			 $("#erbsAlarmNumSpan").css("color","green");
			 }else{
			 $("#erbsAlarmNumSpan").removeClass();
			 $("#erbsAlarmNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
			 $("#erbsAlarmNumSpan").css("color","red");
			 }*/
		});

		$.get("highInterferenceCell/overlapCeakCoverNum", {
			cell: alarmNum,
			city: record.city,
			startTime: $("#startTime").val(),
			endTime: $("#endTime").val()
		}, function (data) {
			//console.log(data);
			$("#overlapCeakCoverNum").val(data);
			if ($("#overlapCeakCoverNum").val() == 0) {
				$("#overlapCeakCoverNumSpan").removeClass();
				$("#overlapCeakCoverNumSpan").addClass("glyphicon glyphicon-ok-circle");
				$("#overlapCeakCoverNumSpan").css("color", "green");
			} else {
				$("#overlapCeakCoverNumSpan").removeClass();
				$("#overlapCeakCoverNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
				$("#overlapCeakCoverNumSpan").css("color", "red");
			}
		});
		$(".zhaozi").show();
		$(".loadingImg").show();

		var hours = $("#allHour").val();
		var params = {
			cell: record.cell,
			day_from: $("#startDate").val(),
			day_to: $("#endDate").val(),
			hours: hours
		};
		//小区级告警分类
		bsc_type("highInterferenceCell/getCellAlarmClassify", "#cellAlarmClassify", params);
		bsc_type("highInterferenceCell/getErbsAlarmClassify", "#erbsAlarmClassify", params);
		//干扰分析
		$.get("highInterferenceCell/getInterfereAnalysis", params, function (data) {
			$("#highInterfereNum").val(data.records);
			$("#highInterfereNumHour").val(data.recordsHour);
			if ($("#highInterfereNum").val() == 0 && ($("#highInterfereNumHour").val() == 0 || $("#highInterfereNumHour").val() == "null")) {
				$("#highInterfereNumSpan").removeClass();
				$("#highInterfereNumSpan").addClass("glyphicon glyphicon-ok-circle");
				$("#highInterfereNumSpan").css("color", "green");
			} else {
				$("#highInterfereNumSpan").removeClass();
				$("#highInterfereNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
				$("#highInterfereNumSpan").css("color", "red");
			}
			/*$("#highInterfereNum").val(data.records);
			 if($("#highInterfereNum").val()==0){
			 $("#highInterfereNumSpan").removeClass();
			 $("#highInterfereNumSpan").addClass("glyphicon glyphicon-ok-circle");
			 $("#highInterfereNumSpan").css("color","green");
			 }else{
			 $("#highInterfereNumSpan").removeClass();
			 $("#highInterfereNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
			 $("#highInterfereNumSpan").css("color","red");
			 }*/

			$("#interfere_zhaozi").hide();
			$("#interfere_loadingImg").hide();
			var fieldArr = [];
			var text = data.content.split(",");
			//var filename = data.filename;
			for (var i in data.rows[0]) {
				fieldArr[fieldArr.length] = {field: i, title: text[fieldArr.length], width: 150, sortable: true};
			}
			var newData = data.rows;
			$("#interfereAnalysis").grid("destroy", true, true);
			var interfereAnalysis = $("#interfereAnalysis").grid({
				columns: fieldArr,
				dataSource: newData,
				pager: {limit: 10, sizes: [10, 20, 50, 100]},
				autoScroll: true,
				uiLibrary: "bootstrap",
			});
		});

		/*var hours = $("#allHour").val();*/
		/*var params = {
			cell: record.cell,
			day_from: $("#startTime").val(),
			day_to: $("#endTime").val(),
			hours: hours
		};*/
		$.get("highInterferenceCell/getPrbNum", params, function (data) {
			data = eval("(" + data + ")");
			$("#prbHighInterfereNum").val(data[0]);
			$("#prbHighInterfereNumHour").val(data[1]);
			if ($("#prbHighInterfereNum").val() == 0 && ($("#prbHighInterfereNumHour").val() == 0 || $("#prbHighInterfereNumHour").val() == "null")) {
				$("#prbHighInterfereNumSpan").removeClass();
				$("#prbHighInterfereNumSpan").addClass("glyphicon glyphicon-ok-circle");
				$("#prbHighInterfereNumSpan").css("color", "green");
			} else {
				$("#prbHighInterfereNumSpan").removeClass();
				$("#prbHighInterfereNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
				$("#prbHighInterfereNumSpan").css("color", "red");
			}
		});

		var tableChart = "highLostCell";
		var params_cell = {
			table: tableChart,
			//table:table,
			rowCell: record.cell
		};

		$.get("highInterferenceCell/getalarmWorstCell", params_cell, function (data) {
			$("#alarm_zhaozi").hide();
			$("#alarm_loadingImg").hide();
			//alert(data);
			var fieldArr = [];
			var text = data.content.split(",");
			var filename = data.filename;
			//$("#alarmWorstCellTable").val(filename);
			for (var i in data.rows[0]) {
				fieldArr[fieldArr.length] = {field: i, title: text[fieldArr.length], width: 150};
			} //console.log(fieldArr);
			var newData = data.rows;

			$("#alarmWorstCellTable").grid("destroy", true, true);
			var alarmWorstCellTable = $("#alarmWorstCellTable").grid({
				columns: fieldArr,
				dataSource: newData,
				pager: {limit: 10, sizes: [10, 20, 50, 100]},
				autoScroll: true,
				uiLibrary: "bootstrap",
			});
		});


		var paramsLTE_1 = {
			input1: 3,
			input2: 3,
			input3: 50,
			input4: 10,
			input5: 50,
			cell: record.cell,
			dateTime: $("#startTime").val(),
			city: record.city
		};
		$.post("highInterferenceCell/getLTENeighborHeader_1", paramsLTE_1, function (data) {
			if (data.error == "error") {
				// $("#LTE_zhaozi").hide();
				// $("#LTE_loadingImg").hide();
				return;
			}
			var fieldArr = [];
			for (var k in data) {
				if (fieldArr.length == 0) {
					fieldArr[fieldArr.length] = {field: k, title: k, hidden: true};
				} else {
					if (k == "datetime_id") {
						fieldArr[fieldArr.length] = {field: k, title: k, width: 180};
					} else {
						fieldArr[fieldArr.length] = {field: k, title: k, width: 180};
					}

				}
			}

			$("#LTETable_1").grid("destroy", true, true);
			var grid = $("#LTETable_1").grid({
				columns: fieldArr,
				params: paramsLTE,
				dataSource: {
					url: "highInterferenceCell/getLTENeighborData_1",
					success: function (data) {
						data = eval("(" + data + ")");
						grid.render(data);
						// $("#LTE_zhaozi").hide();
						// $("#LTE_loadingImg").hide();
						/*$("#LteNum").val(data.total_is0);
						 if($("#LteNum").val()==0){
						 $("#LteNumSpan").removeClass();
						 $("#LteNumSpan").addClass("glyphicon glyphicon-ok-circle");
						 $("#LteNumSpan").css("color","green");
						 }else{
						 $("#LteNumSpan").removeClass();
						 $("#LteNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
						 $("#LteNumSpan").css("color","red");
						 }*/
					}
				},
				pager: {limit: 10, sizes: [10, 20, 50, 100]},
				autoScroll: true,
				uiLibrary: "bootstrap"
			});

		});


		var paramsLTE = {
			input1: 3,
			input2: 3,
			input3: 50,
			input4: 10,
			input5: 50,
			cell: record.cell,
			dateTime: $("#startDate").val(),
			city: record.city
		};
		$.post("highInterferenceCell/getLTENeighborHeader", paramsLTE, function (data) {
			if (data.error == "error") {
				$("#LTE_zhaozi").hide();
				$("#LTE_loadingImg").hide();
				return;
			}
			var fieldArr = [];
			for (var k in data) {
				if (fieldArr.length == 0) {
					fieldArr[fieldArr.length] = {field: k, title: k, hidden: true};
				} else {
					if (k == "datetime_id") {
						fieldArr[fieldArr.length] = {field: k, title: k, width: 180};
					} else {
						fieldArr[fieldArr.length] = {field: k, title: k, width: 180};
					}

				}
			}

			$("#LTETable").grid("destroy", true, true);
			var grid = $("#LTETable").grid({
				columns: fieldArr,
				params: paramsLTE,
				dataSource: {
					url: "highInterferenceCell/getLTENeighborData",
					success: function (data) {
						data = eval("(" + data + ")");
						grid.render(data);
						$("#LTE_zhaozi").hide();
						$("#LTE_loadingImg").hide();
						$("#LteNum").val(data.total_is0);
						if ($("#LteNum").val() == 0) {
							$("#LteNumSpan").removeClass();
							$("#LteNumSpan").addClass("glyphicon glyphicon-ok-circle");
							$("#LteNumSpan").css("color", "green");
						} else {
							$("#LteNumSpan").removeClass();
							$("#LteNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
							$("#LteNumSpan").css("color", "red");
						}
					}
				},
				pager: {limit: 10, sizes: [10, 20, 50, 100]},
				autoScroll: true,
				uiLibrary: "bootstrap"
			});
		});

		var paramsGSM = {
			input1: 3,
			input2: 1,
			input3: 50,
			input4: 2,
			input5: 50,
			input6: -90,
			input7: -15,
			cell: record.cell,
			dateTime: $("#startDate").val(),
			city: record.city
		};
		$.post("highInterferenceCell/getGSMNeighborHeader", paramsLTE, function (data) {
			if (data.error == "error") {
				$("#GSM_zhaozi").hide();
				$("#GSM_loadingImg").hide();
				return;
			}
			var fieldArr = [];
			for (var k in data) {
				if (fieldArr.length == 0) {
					fieldArr[fieldArr.length] = {field: k, title: k, hidden: true};
				} else {
					if (k == "datetime_id") {
						fieldArr[fieldArr.length] = {field: k, title: k, width: 180};
					} else {
						fieldArr[fieldArr.length] = {field: k, title: k, width: 180};
					}

				}
			}

			$("#GSMTable").grid("destroy", true, true);
			var grid = $("#GSMTable").grid({
				columns: fieldArr,
				params: paramsLTE,
				dataSource: {
					url: "highInterferenceCell/getGSMNeighborData",
					success: function (data) {
						data = eval("(" + data + ")");
						grid.render(data);
						$("#GSM_zhaozi").hide();
						$("#GSM_loadingImg").hide();
						$("#GsmNum").val(data.total_is0);
						if ($("#GsmNum").val() == 0) {
							$("#GsmNumSpan").removeClass();
							$("#GsmNumSpan").addClass("glyphicon glyphicon-ok-circle");
							$("#GsmNumSpan").css("color", "green");
						} else {
							$("#GsmNumSpan").removeClass();
							$("#GsmNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
							$("#GsmNumSpan").css("color", "red");
						}
					}
				},
				pager: {limit: 10, sizes: [10, 20, 50, 100]},
				autoScroll: true,
				uiLibrary: "bootstrap"
			});
		});


		var params_coveragecell = {
			startTime: $("#startDate").val(),
			endTime: $("#endDate").val(),
			city: record.city,
			cell: record.cell
		};

		$.get("highInterferenceCell/getweakCoverageCell", params_coveragecell, function (data) {
			$("#weak_zhaozi").hide();
			$("#weak_loadingImg").hide();
			data = eval("(" + data + ")");
			$("#weakCoverNum").val(data.num);
			if ($("#weakCoverNum").val() == 0) {
				$("#weakCoverNumSpan").removeClass();
				$("#weakCoverNumSpan").addClass("glyphicon glyphicon-ok-circle");
				$("#weakCoverNumSpan").css("color", "green");
			} else {
				$("#weakCoverNumSpan").removeClass();
				$("#weakCoverNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
				$("#weakCoverNumSpan").css("color", "red");
			}
			//alert(data.num);
			// console.log(data.series)
			$("#weakCoverageCell").html("");
			if ($("#weakCoverNum").val() != 0) {
				$("#weakCoverageCell").highcharts({
					chart: {
						type: "column"
					},
					title: {
						text: null
					},

					xAxis: {
						categories: data.category
					},
					yAxis: {
						min: 0,
						title: {
							text: null
						}
					},
					tooltip: {
						pointFormat: "弱覆盖数量：{point.y:.1f} ",
						shared: true,
						useHTML: true
					},
					legend: {
						enabled: false
					},
					credits: {
						enabled: false,
					},
					series: data.series
				});
			}
		});


		// queryBtn.stop();
		// exportBtn.stop();
		// });
		if (table == "file") {
			filename = $("#badCellFile").val();
			download(filename);
		}

	});
}
function exportFile() {
	var highInterfereCellType = $("#cellType").val();
	var highInterfereCellName = $("#cell").val();
	var highInterfereCellDateFrom = $("#startDate").val();
	var highInterfereCellDateTo = $("#endDate").val();
	var citys = $("#citys").val();
	var table = "";
	if (highInterfereCellType == "avg") {
		table = "interfereCell_avg";
	} else if (highInterfereCellType == "one") {
		table = "interfereCell_one";
	}
	if (highInterfereCellDateFrom == "") {
		// alert("起始时间不能为空!");
		layer.open({
			title: "提示",
			content: "起始时间不能为空"
		});
		return false;
	}
	if (highInterfereCellDateTo == "") {
		// alert("结束时间不能为空!");
		layer.open({
			title: "提示",
			content: "结束时间不能为空"
		});
		return false;
	}
	if (highInterfereCellDateFrom > highInterfereCellDateTo) {
		// alert("结束时间不能早于起始时间!");
		layer.open({
			title: "提示",
			content: "结束时间不能早于起始时间"
		});
		return false;
	}
	
	var params = {
		table: table,
		type: highInterfereCellType,
		city: citys.join(","),
		cell: highInterfereCellName,
		datefrom: highInterfereCellDateFrom,
		dateto: highInterfereCellDateTo,
		hours: $("#allHour").val()
	};
	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	queryBtn.start();
	exportBtn.start();

	var url = "highInterferenceCell/getAllCellData";
	$.post(url, params, function (data) {
		data = JSON.parse(data);
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
		queryBtn.stop();
		exportBtn.stop();
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

function getAlarmData(rowData) {
	var params = {
		rowData: rowData,
		alarmHighInterfereCellDataFrom: $("#startDate").val(),
		alarmHighInterfereCellDataTo: $("#endDate").val()
	};
	var fieldArr = [];
	var text = "Event_time,Problem_text,Cease_time,SP_text";
	var textArr = text.split(",");
	for (var i in textArr) {
		fieldArr[fieldArr.length] = {field: textArr[fieldArr.length], title: textArr[fieldArr.length], width: 250};
	}
	$("#alarmTable").grid("destroy", true, true);
	var grid = $("#alarmTable").grid({
		columns: fieldArr,
		dataSource: {
			url: "highInterferenceCell/getAlarmData",
			success: function (data) {
				data = JSON.parse(data);
				grid.render(data);
				$("#alarm_zhaozi").hide();
				$("#alarm_loadingImg").hide();
			}
		},
		params: params,
		pager: {limit: 10, sizes: [10, 20, 50, 100]},
		autoScroll: true,
		uiLibrary: "bootstrap",
		primaryKey: "cell",
		autoLoad: true
	});
}
function initTimeList() {
	$("#timeList").multiselect({
		//dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "选择时间",
		//filterPlaceholder:"搜索",
		//nSelectedText:"项被选中",
		includeSelectAllOption: true,
		//selectAllText:"全选/取消全选",
		//allSelectedText:"已选中所有城市",
		maxHeight: 200,
		maxWidth: "100%"
	});
}
function getTimeList(rowData) {
	var timestamp = new Date().getTime();
	var urlStr = "common/json/highInterfereCell_ChartTime_" + timestamp.toString() + ".json";
	var cell_str = JSON.stringify(rowData.cell);
	var cell = cell_str.replace(/"/g, "");

	var params = {
		datefrom: $("#startDate").val(),
		dateto: $("#endDate").val(),
		cell: cell,
		url: urlStr
	};
	var url = "highInterferenceCell/getTimeList";
	$.post(url, params, function (data) {
		data = JSON.parse(data);
		$("#timeList").multiselect("dataprovider", data);
		setFrequencyChart(rowData);
		$("#timeList").on("change", function () {
			$("#chart2_zhaozi").show();
			$("#chart2_loadingImg").show();
			setFrequencyChart(rowData);
		});

	});
}
function setTimeChart(rowData) {
	var highInterfereCellDateFrom = $("#startDate").val();
	var highInterfereCellDateTo = $("#endDate").val();
	var table = "interfereCell";

	var rowData_str = JSON.stringify(rowData.cell);
	var click_cell_name = rowData_str.replace(/"/g, "");
	var subNetwork_str = JSON.stringify(rowData.subNetwork);
	subNetwork_str = subNetwork_str.replace(/"/g, "");

	var sf1 = "SF1上行干扰电平";
	var sf2 = "SF2上行干扰电平";
	var sf6 = "SF6上行干扰电平";
	var sf7 = "SF7上行干扰电平";

	var params = {
		cell: click_cell_name,
		datefrom: highInterfereCellDateFrom,
		dateto: highInterfereCellDateTo,
		sf1: sf1,
		sf2: sf2,
		sf6: sf6,
		sf7: sf7
	};

	$.post("highInterferenceCell/getTimeChartData", params, function (data) {

		var cat_str = JSON.stringify(JSON.parse(data).categories);
		var ser_str = JSON.stringify(JSON.parse(data).series);

		ser_str = ser_str.replace(/"/g, "");
		ser_str = ser_str.replace(sf1, "'" + sf1 + "'");
		ser_str = ser_str.replace(sf2, "'" + sf2 + "'");
		ser_str = ser_str.replace(sf6, "'" + sf6 + "'");
		ser_str = ser_str.replace(sf7, "'" + sf7 + "'");

		var cat_obj = eval("(" + cat_str + ")");
		var ser_obj = eval("(" + ser_str + ")");

		$("#timeDomainContainer").highcharts({
			exporting: {
				enabled: true,
			},
			chart: {
				type: "column"
			},
			title: {
				text: "高干扰小区分析--时域"
			},
			subtitle: {
				text: subNetwork_str + " / " + click_cell_name
			},
			xAxis: {
				categories: cat_obj,
				crosshair: true
			},
			yAxis: {
				title: {
					text: "电平值 (dBc)"
				}
			},
			tooltip: {
				headerFormat: '<span style="font-size:10px">{point.key}</span><table style="font-size:10px;white-space:nowrap">',
				pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
				'<td style="padding:0"><b>{point.y:.2f} dBc</b></td></tr>',
				footerFormat: "</table>",
				shared: true,
				useHTML: true
			},
			plotOptions: {
				column: {
					pointPadding: 0.2,
					borderWidth: 0
				}
			},
			credits: {
				enabled: false
			},
			series: ser_obj
		});
		$("#chart1_zhaozi").hide();
		$("#chart1_loadingImg").hide();
	});
}
function setFrequencyChart(rowData) {
	// var dateStr = $("#timeList").val();
	var day_id = $("#timeList").val();
	// var hour_id = (((dateStr.split(" "))[1]).split(":"))[0];

	var table = "interfereCell";
	var rowData_str = JSON.stringify(rowData.cell);
	var click_cell_name = rowData_str.replace(/"/g, "");
	var subNetwork_str = JSON.stringify(rowData.subNetwork);
	subNetwork_str = subNetwork_str.replace(/"/g, "");
	var params = {
		table: table,
		cell: click_cell_name,
		day_id: day_id,
		// hour_id:hour_id
	};

	$.post("highInterferenceCell/getFrequencyChartData", params, function (data) {

		var cat_str = JSON.stringify(JSON.parse(data).categories);
		var ser_str = JSON.stringify(JSON.parse(data).series);

		// ser_str=ser_str.replace(/"/g,"");

		ser_str = ser_str.replace("PRB", "'" + "PRB");
		ser_str = ser_str.replace("上行干扰电平", "上行干扰电平" + "'");
		ser_str = ser_str.replace("电平值", "'" + "电平值" + "'");

		var cat_obj = eval("(" + cat_str + ")");
		var ser_obj = eval("(" + ser_str + ")");

		$("#frequencyDomainContainer").highcharts({
			exporting: {
				enabled: true,
			},
			chart: {
				type: "spline"
			},
			title: {
				text: "高干扰小区分析--频域"
			},
			subtitle: {
				text: day_id + " / " + subNetwork_str + " / " + click_cell_name
			},
			xAxis: {
				categories: cat_obj,
				crosshair: true
			},
			yAxis: {
				tickPositions: [-130, -120, -110, -100, -90, -80],
				title: {
					text: "电平值 (dBm)"
				}
			},
			tooltip: {
				headerFormat: '<span style="font-size:10px">{point.key}</span><table style="font-size:10px;white-space:nowrap">',
				pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
				'<td style="padding:0"><b>{point.y:.1f} dBm</b></td></tr>',
				footerFormat: "</table>",
				shared: true,
				useHTML: true
			},
			plotOptions: {
				column: {
					pointPadding: 0.2,
					borderWidth: 0
				},
				spline: {
					lineWidth: 1.5,
					fillOpacity: 0.1,
					marker: {enabled: false, states: {hover: {enabled: true, radius: 2}}}, shadow: false
				}
			},
			credits: {
				enabled: false
			},
			series: ser_obj
		});
		$("#chart2_zhaozi").hide();
		$("#chart2_loadingImg").hide();
	});
}

function textWidth(text) {
	var length = text.length;
	if (length > 15) {
		return length * 10;
	}
	return 150;
}