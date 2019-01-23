$(document).ready(function () {
	initCitys();
	setHQSelect();
	//setDateTime();
	var width = $("#chart-coreNetwork").width();
});
var num;
var l;
function doSearch() {
	l = Ladda.create(document.getElementById("search"));
	l.start();
	num = 0;
	//清空信令详情中的数据
	$("#signalingDetailTable_core").grid("destroy", true, true);
	$("#signalingDetailTable_wlan").grid("destroy", true, true);
	coreNetworkChart("signalingDiagnose/coreNetworkDiagnose", "#chart-coreNetwork", "core");
	// wlanNetworkChart("signalingDiagnose/wlanNetworkDiagnose", "#chart-wlanNetwork", "wlan");
	coreNetworkChart("signalingDiagnose/wlanNetworkDiagnose", "#chart-wlanNetwork", "wlan");
	timingDiagramChart();
}
function wlanNetworkChart(route, block, type) {
	var params = getParams();
	var types = ["internalProcHoExecS1In", "internalProcHoExecS1Out", "internalProcHoExecX2In", "internalProcHoExecX2Out", "internalProcHoPrepS1In", "internalProcHoPrepS1Out", "internalProcHoPrepX2In", "internalProcHoPrepX2Out", "internalProcInitialCtxtSetup", "internalProcRrcConnSetup", "internalProcS1SigConnSetup"];
	if (!params) {
		l.stop();
		// alert("");
		layer.open({
			title: "提示",
			content: "日期或者用户不能为空"
		});
		return;
	}
	var result = [];
	var categories = [];
	var series = [];
	var k = 0;
	for (var i = 0; i < 11; i++) {
		params["table"] = types[i];
		$.ajax({
			type: "GET",
			url: route,
			data: params,
			dataType: "json",
			beforeSend: function () {
				$(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
			},
			success: function (data) {
				k = k + 1;
				categories[categories.length] = data["type"];
				series[series.length] = data["num"];
				if (k == 11) {
					result["categories"] = categories;
					result["series"] = series;
					result["imsi"] = data["imsi"];
					if (++num == 3) {
						l.stop();
						$(block).html("");
					}
					createCoreNetworkChart(result, block, params, type);

				}
				
			}
		});
	}

}
function coreNetworkChart(route, block, type) {
	var params = getParams();
	if (!params) {
		l.stop();
		// alert("");
		layer.open({
			title: "提示",
			content: "日期或者用户不能为空"
		});
		return;
	}
	$.ajax({
		type: "GET",
		url: route,
		data: params,
		dataType: "json",
		beforeSend: function () {
			$(block).html("<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">");
		},
		success: function (data) {
			if (++num == 3) {
				l.stop();
			}
			$(block).html("");

			createCoreNetworkChart(data, block, params, type);

		}
	});
}
function createCoreNetworkChart(data, block, params, type) {
	$(block).highcharts({
		chart: {
			polar: true
		},
		title: {
			text: "信令失败次数"
		},

		xAxis: {
			categories: data.categories

		},
		tooltip: {
			pointFormat: ":{point.y} Click to the detail"
		},
		plotOptions: {
			line: {
				events: {
					click: function (e) {
						var value = e.point.y;
						if (value > 0) {
							if (num == 3) {
								l.start();
							}
							
							params.tableName = data.categories[e.point.x];
							params.imsi = data.imsi;
							if (type == "core") {
								searchNetworkDiagnoseDetail(params, "signalingDiagnose/coreNetworkDiagnoseDetailHeader", "signalingDiagnose/coreNetworkDiagnoseDetail", "#signalingDetailTable_core");
							} else {
								searchNetworkDiagnoseDetail(params, "signalingDiagnose/wlanNetworkDiagnoseDetailHeader", "signalingDiagnose/wlanNetworkDiagnoseDetail", "#signalingDetailTable_wlan");
							}
						}
					}
				}
			}
		},
		legend: {enabled: false},
		credits: {enabled: false},
		series: [{
			data: data.series
		}]
	});
}

//var tableId="#signalingDetailTable";
function searchNetworkDiagnoseDetail(params, headerUrl, itemUrl, tableId) {
	//清空信令详情中的数据
	$(tableId).grid("destroy", true, true);
	$.get(headerUrl, params, function (data) {
		if (data.error == "error") {
			if (num == 3) {
				l.stop();
			}
			
			// alert("");
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
			return;
		}
		if (num == 3) {
			l.stop();
		}
		
		var fieldArr = [];
		for (var k in data) {
			if (fieldArr.length == 0) {
				fieldArr[fieldArr.length] = {field: k, title: k, hidden: true};
			} else {
				if (k == "subCauseCode") {
					fieldArr[fieldArr.length] = {field: k, title: k, width: 320};
				} else if (k == "causeCode") {
					fieldArr[fieldArr.length] = {field: k, title: k, width: 450};
				} else if (k == "apn") {
					fieldArr[fieldArr.length] = {field: k, title: k, width: 320};
				} else if (k == "hoSrcOrTarget") {
					fieldArr[fieldArr.length] = {field: k, title: k, width: 320};
				} else if (k == "result") {
					fieldArr[fieldArr.length] = {field: k, title: k, width: 400};
				} else if (k == "3gppCauseGroup") {
					fieldArr[fieldArr.length] = {field: k, title: k, width: 400};
				} else if (k == "3gppCause") {
					fieldArr[fieldArr.length] = {field: k, title: k, width: 400};
				} else if (k == "srvccType") {
					fieldArr[fieldArr.length] = {field: k, title: k, width: 320};
				} else if (k == "establCause") {
					fieldArr[fieldArr.length] = {field: k, title: k, width: 320};
				} else {
					fieldArr[fieldArr.length] = {field: k, title: k, width: textWidth(k)};
				}
			}
		}
		$(tableId).grid("destroy", true, true);
		var grid = $(tableId).grid({
			columns: fieldArr,
			params: params,
			dataSource: {
				url: itemUrl,
				success: function (data) {
					data = eval("(" + data + ")");
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
function getParams() {
	var city = $("#city").val();
	var dateTime = $("#dateTime").val();
	var hourId = $("#hourSelect").val();
	var userInfo = $("#userInfo").val();
	if (dateTime == "" || userInfo == "" || hourId == null) {
		return false;
	}
	
	var params = {
		city: city,
		dateTime: dateTime,
		hourId: hourId,
		userInfo: userInfo
	};
	return params;
}
function setHQSelect() {
	$("#hourSelect").multiselect({
		buttonWidth: "100%",
		enableFiltering: false,
		nonSelectedText: "请选择小时",
		//filterPlaceholder:"搜索",
		//nSelectedText:"项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有小时",
		maxHeight: 200,
		maxWidth: "100%"
	});
}
function initCitys() {
	$("#city").multiselect({
		//dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "选择城市",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		//allSelectedText:"已选中所有城市",
		maxHeight: 200,
		maxWidth: "100%"
	});
	$.get("signalingDiagnose/getCitys", null, function (data) {
		data = JSON.parse(data);
		var newData = [];
		for (var i in data) {
			var CHCity = data[i].split("-")[0];
			var dataBase = data[i].split("-")[1];
			newData.push({"label": CHCity, "value": dataBase});
		}
		$("#city").multiselect("dataprovider", newData);
		setDateTime();
	});
}
function setDateTime() {
	$("#dateTime").datepicker({format: "yyyy-mm-dd"});  //返回日期
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;

	console.log(today);

	$.get("signalingDiagnose/coreNetworkDates", {"city": $("#city").val()}, function (data) {
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
		$.get("signalingDiagnose/coreNetworkDates", {"city": $("#city").val()}, function (data) {
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
}
function timingDiagramChart() {
	var city = $("#city").val();
	var date = $("#dateTime").val();
	var userInfo = $("#userInfo").val();
	if (!date) {
		// alert("");
		layer.open({
			title: "提示",
			content: "请选择日期"
		});
		return;
	}
	if (!userInfo) {
		// alert("");
		layer.open({
			title: "提示",
			content: "请输入用户信息"
		});
		return;
	}
	var params = {
		city: city,
		date: date,
		userInfo: userInfo
	};
	$("#timingDiagramChart").empty();
	$.get("signalingDiagnose/timingDiagramChartData", params, function (data) {
		if (++num == 3) {
			l.stop();
		}
		data = JSON.parse(data);
		setTimingDiagramChart(data);

	});

}
function setTimingDiagramChart(data) {
	var date = $("#dateTime").val();
	var timestamp = Date.parse(new Date(date));
	var defaultDate = [];
	for (var i = 0; i <= 24; i++) {
		defaultDate.push([timestamp + i * 3600 * 1000, null]);
	}

	$("#timingDiagramChart").highcharts("StockChart", {
		rangeSelector: {
			selected: 2
		},
		title: {
			text: "信令时序图"
		},
		yAxis: {
			labels: {
				formatter: function () {
					if (this.value == 1) {
						return "核心网侧";
					} else if (this.value == 2) {
						return "无线网侧";
					}
				}
			}
		},
		tooltip: {
			pointFormatter: function () {
				var type;
				var r;
				if (this.series.name == "success") {
					type = data.success[this.index].type;
					r = data.success[this.index].result;
				} else if (this.series.name == "abort") {
					type = data.abort[this.index].type;
					r = data.abort[this.index].result;
				} else if (this.series.name == "success_w") {
					type = data.success_w[this.index].type;
					r = data.success_w[this.index].result;
				} else if (this.series.name == "abort_w") {
					type = data.abort_w[this.index].type;
					r = data.abort_w[this.index].result;
				}
				return '<span style="color:' + this.series.color + '">' + type + ":" + r + "</span><br/>";
			},
			shared: true
		},
		series: [{
			name: "default",
			data: defaultDate,
			color: "black"
		}, {
			name: "success",
			data: data.successData,
			lineWidth: 0,
			marker: {
				enabled: true,
				radius: 2,
				fillColor: "blue",//点填充色
			},
			tooltip: {
				valueDecimals: 2
			},
			color: "blue"
		}, {
			name: "abort",
			data: data.abortData,
			lineWidth: 0,
			marker: {
				enabled: true,
				radius: 4,
				fillColor: "red",//点填充色
			},
			tooltip: {
				valueDecimals: 2
			},
			color: "red"
		}, {
			name: "success_w",
			data: data.successData_w,
			lineWidth: 0,
			marker: {
				enabled: true,
				radius: 2,
				fillColor: "blue",//点填充色
			},
			tooltip: {
				valueDecimals: 2
			},
			color: "blue"
		}, {
			name: "abort_w",
			data: data.abortData_w,
			lineWidth: 0,
			marker: {
				enabled: true,
				radius: 4,
				fillColor: "red",//点填充色
			},
			tooltip: {
				valueDecimals: 2
			},
			color: "red"
		}]
	});
}