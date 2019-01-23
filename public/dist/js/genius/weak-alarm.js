/**
 * Created by wangyang on 2016/6/23.
 */
		var chartData;
var currentAlarmData;
var historyAlarmData;
var historyAlarmDateData;
function currentAlarmChart(route, block, btnId, pieRoute) {
	$.ajax({
		type: "GET",
		url: route,
		data: {range: "day"},
		dataType: "json",
		beforeSend: function () {
			$(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
		},
		success: function (data) {
			currentAlarmData = data;
			createCurrentAlarmChart(data, block, btnId, pieRoute);
		}
	});
}


function historyAlarmChart(route, block, btnId, pieRoute) {
	$.ajax({
		type: "GET",
		url: route,
		data: {range: "day"},
		dataType: "json",
		beforeSend: function () {
			$(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
		},
		success: function (data) {
			historyAlarmData = data;
			createHistoryAlarmChart(data, block, btnId, pieRoute);
		}
	});
}
function createHistoryAlarmChart(data, block, btnId, pieRoute) {
	$(block).html("");
	var chartTest = new Highcharts.StockChart({

		rangeSelector: {
			buttons: [{
					type: "month",
					count: 1,
					text: "1m"
				}, {
					type: "day",
					count: 6,
					text: "7D"
				}, {
					type: "all",
					count: 1,
					text: "All"
				}],
			selected: 1,
			//inputEnabled : false
			inputDateFormat: "%Y-%m-%d",
			inputEditDateFormat: "%Y-%m-%d"

		},
		subtitle: {
			text: "Click the columns to view detail"
		},
		legend: {enabled: true},
		credits: {enabled: false},

		yAxis: {
			tickPositions: data.yAxis
		},
		chart: {
			type: "column",
			renderTo: "bar-chart-history",
			alignTicks: false
		},

		plotOptions: {
			column: {
				cursor: "pointer",
				point: {
					events: {
						click: function (event) {
							var date = event.point.category;
							historyAlarmDateChart("weak/historyAlarmDateData", date, block, btnId, pieRoute);
						}
					}
				}
			}
		},
		credits: {
			enabled: false
		},
		series: data.series
	});
}
function historyAlarmDateChart(route, date, block, btnId, pieRoute) {
	$.ajax({
		type: "GET",
		url: route,
		data: {time: date},
		dataType: "json",
		beforeSend: function () {
			$(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
		},
		success: function (data) {
			historyAlarmDateData = data;
			createHistoryDateChart(data, block, btnId, pieRoute, date);
		}
	});
}
function createHistoryDateChart(data, block, btnId, pieRoute, date) {
	$(block).html('');
	$(block).highcharts({
		chart: {
			type: "column"
		},
		title: {
			text: null
		},
		subtitle: {
			text: "Click the columns to view detail."
		},
		xAxis: {
			categories: data.category
		},
		yAxis: {
			min: 0,
			title: {
				text: "the number of historyAlarm"
			},
			stackLabels: {
				enabled: true,
				style: {
					fontWeight: "bold",
					color: (Highcharts.theme && Highcharts.theme.textColor) || "gray"
				}
			}
		},
		/*legend: {
		 align: 'right',
		 x: -30,
		 verticalAlign: 'top',
		 y: 25,
		 floating: true,
		 backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
		 borderColor: '#CCC',
		 borderWidth: 1,
		 shadow: false
		 },*/
		credits: {enabled: false},
		tooltip: {
			headerFormat: "<b>{point.x}</b><br/>",
			pointFormat: "{series.name}: {point.y}<br/>Total: {point.stackTotal}<br/>Click to the detail"

		},
		plotOptions: {
			column: {
				stacking: "normal",
				cursor: "pointer",
				point: {
					events: {
						click: function (event) {
							var city = event.point.category;
							createHistoryDonutPieChart(pieRoute, city, block, btnId, date);
						}
					}
				},
				dataLabels: {
					enabled: true,
					color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || "white",
					style: {
						textShadow: "0 0 3px black"
					}
				}
			}

		},
		series: data.series

	});
	$(btnId).css("display", "block");
	$(btnId).click(function () {
		createHistoryAlarmChart(historyAlarmData, block, btnId, pieRoute);
		$(btnId).css("display", "none");
	});
}
function createHistoryDonutPieChart(pieRoute, city, block, btnId, date) {
	$.ajax({
		type: "GET",
		//url: 'currentAlarm/drillDownDonutPie',
		url: pieRoute,
		data: {city: city, time: date},
		dataType: "text",
		beforeSend: function () {
			$(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
		},
		success: function (data) {
			var colors = Highcharts.getOptions().colors;
			data = JSON.parse(data);
			var perceived_severity_data = data.perceived_severity;
			var sp_text_data_return = data.sp_text;
			var sp_text_data = [];
			for (var i = 0; i <= perceived_severity_data.length - 1; i++) {
				perceived_severity_data[i].color = colors[i];
				for (var j = 0; j <= sp_text_data_return[perceived_severity_data[i].name].length - 1; j++) {
					sp_text_data_return[perceived_severity_data[i].name][j].color = colors[i];
					sp_text_data[sp_text_data.length] = sp_text_data_return[perceived_severity_data[i].name][j];
				}
			}
			$(block).highcharts({
				chart: {
					type: "pie"
				},
				title: {
					text: "the detail of " + city
				},
				subtitle: {
					text: null
				},
				yAxis: {
					title: {
						text: null
					}
				},
				plotOptions: {
					pie: {
						shadow: false,
						center: ["50%", "50%"],
						cursor: "pointer",
						point: {
							events: {
								click: function (event) {
									createHistoryDateChart(historyAlarmDateData, block, btnId, pieRoute, date);
									//$(btnId).css('display','none');
								}
							}
						}
					}
				},
				tooltip: {
					pointFormat: ": <b>{point.y}({point.percentage:.2f} %)</b><br/>Click to back"
				},
				credits: {
					enabled: false
				},
				series: [{
						name: "Perceived_severity",
						data: perceived_severity_data,
						size: "60%",
						dataLabels: {
							formatter: function () {
								return this.y > 5 ? this.point.name : null;
							},
							color: "#ffffff",
							distance: -30
						}
					}, {
						name: "SP_text",
						data: sp_text_data,
						size: "80%",
						innerSize: "60%",
						dataLabels: {
							format: "<b>{point.name}</b>:{point.y}({point.percentage:.2f} %)"

						}
					}]
			});
			$(btnId).css("display", "block");
			$(btnId).click(function () {
				createHistoryDateChart(historyAlarmDateData, block, btnId, pieRoute, date);
				//$(btnId).css('display','none');
			});
		}
	});
}

function createDonutPieChart(pieRoute, city, block, btnId) {
	$.ajax({
		type: "GET",
		//url: 'currentAlarm/drillDownDonutPie',
		url: pieRoute,
		data: {city: city},
		dataType: "text",
		beforeSend: function () {
			$(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
		},
		success: function (data) {
			var colors = Highcharts.getOptions().colors;
			data = JSON.parse(data);
			var perceived_severity_data = data.perceived_severity;
			var sp_text_data_return = data.sp_text;
			var sp_text_data = [];
			for (var i = 0; i <= perceived_severity_data.length - 1; i++) {
				perceived_severity_data[i].color = colors[i];
				for (var j = 0; j <= sp_text_data_return[perceived_severity_data[i].name].length - 1; j++) {
					sp_text_data_return[perceived_severity_data[i].name][j].color = colors[i];
					sp_text_data[sp_text_data.length] = sp_text_data_return[perceived_severity_data[i].name][j];
				}
			}
			$(block).highcharts({
				chart: {
					type: "pie"
				},
				title: {
					text: "the detail of " + city
				},
				subtitle: {
					text: null
				},
				yAxis: {
					title: {
						text: null
					}
				},
				plotOptions: {
					pie: {
						shadow: false,
						center: ["50%", "50%"],
						cursor: "pointer",
						point: {
							events: {
								click: function (event) {
									createCurrentAlarmChart(currentAlarmData, block, btnId, pieRoute);
									$(btnId).css("display", "none");
								}
							}
						}
					}
				},
				tooltip: {
					pointFormat: ': <b>{point.y}({point.percentage:.2f} %)</b><br/>Click to back'
				},
				credits: {
					enabled: false
				},
				series: [{
						name: "Perceived_severity",
						data: perceived_severity_data,
						size: "60%",
						dataLabels: {
							formatter: function () {
								return this.y > 5 ? this.point.name : null;
							},
							color: "#ffffff",
							distance: -30
						}
					}, {
						name: "SP_text",
						data: sp_text_data,
						size: "80%",
						innerSize: "60%",
						dataLabels: {
							format: "<b>{point.name}</b>:{point.y}({point.percentage:.2f} %)"

						}
					}]
			});
			$(btnId).css("display", "block");
			$(btnId).click(function () {
				createCurrentAlarmChart(currentAlarmData, block, btnId, pieRoute);
				$(btnId).css("display", "none");
			});
		}
	});
}

var chart2 = function (route, block) {
	$.ajax({
		type: "GET",
		url: route,
		data: {range: "day"},
		dataType: "text",
		beforeSend: function () {
			$(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
		},
		success: function (data) {
			var obj = eval(data);
			var series = [];
			for (var i = 0; i < obj.length; i++) {
				series.push(obj[i]);
			}
			//console.log(series);
			$(block).html("");
			var chartTest = new Highcharts.StockChart({
				chart: {
					renderTo: "bar-chart-history",
					alignTicks: false
				},
				rangeSelector: {
					selected: 1
				},

				title: {
					text: null
				},
				credits: {
					enabled: false
				},
				series: series
			});
		}
	});
};

function createCurrentAlarmChart(data, block, btnId, pieRoute) {
	$(block).html('');
	$(block).highcharts({
		chart: {
			type: "column"
		},
		title: {
			text: null
		},
		subtitle: {
			text: "Click the columns to view detail."
		},
		xAxis: {
			categories: data.category
		},
		yAxis: {
			min: 0,
			title: {
				text: "the number of currentAlarm"
			},
			stackLabels: {
				enabled: true,
				style: {
					fontWeight: "bold",
					color: (Highcharts.theme && Highcharts.theme.textColor) || "gray"
				}
			}
		},
		/*legend: {
		 align: 'right',
		 x: -30,
		 verticalAlign: 'top',
		 y: 25,
		 floating: true,
		 backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
		 borderColor: '#CCC',
		 borderWidth: 1,
		 shadow: false
		 },*/
		credits: {enabled: false},
		tooltip: {
			headerFormat: "<b>{point.x}</b><br/>",
			pointFormat: "{series.name}: {point.y}<br/>Total: {point.stackTotal}<br/>Click to the detail"

		},
		plotOptions: {
			column: {
				stacking: "normal",
				cursor: "pointer",
				point: {
					events: {
						click: function (event) {
							var city = event.point.category;
							createDonutPieChart(pieRoute, city, block, btnId);
						}
					}
				},
				dataLabels: {
					enabled: true,
					color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || "white",
					style: {
						textShadow: "0 0 3px black"
					}
				}
			}

		},
		series: data.series

	});
}

//When document was loaded.
jQuery(document).ready(function () {
	var width = $("#bar-chart-current").width();
	$("#bar-chart-history").width(width);
	currentAlarmChart("weak/currentAlarm", "#bar-chart-current", "#backBtn", "weak/currentAlarmdrillDownDonutPie");
	historyAlarmChart("weak/historyAlarm", "#bar-chart-history", "#backBtnhistory", "weak/historyAlarmDrillDownDonutPie");
	// chart2('historyAlarm','#bar-chart-history')

});

//Resize
$('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
	$(".tab-content .chart.tab-pane.active").highcharts().reflow();
});
