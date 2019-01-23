/**
 * Created by wangyang on 2016/6/23.
 */

		var chart = function (route, block) {
			$.ajax({
				type: "GET",
				url: route,
				data: {range: "day"},
				dataType: "json",
				beforeSend: function () {
					$(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
				},
				success: function (data) {
					$(block).html("");
					$(block).highcharts({
						chart: {
							type: "column"
						},
						title: {
							text: "当前告警"
						},
						xAxis: {
							type: "category",
							labels: {
								rotation: -45,
								style: {
									fontSize: "13px",
									fontFamily: "Verdana, sans-serif"
								}
							}
						},
						yAxis: {
							min: 0,
							title: {
								text: "告警数"
							}
						},
						legend: {
							enabled: false
						},
						tooltip: {
							pointFormat: "告警数: <b>{point.y:.1f} </b>"
						},
						series: [{
								name: "告警数",
								data: data,
								dataLabels: {
									enabled: true,
									rotation: -90,
									color: "#FFFFFF",
									align: "right",
									format: "{point.y:.1f}", // one decimal
									y: 10, // 10 pixels down from the top
									style: {
										fontSize: "13px",
										fontFamily: "Verdana, sans-serif"
									}
								}
							}]
					});
				}
			});
		};

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
			// console.log(series);
			$(block).html("");
			$(block).highcharts("StockChart", {

				rangeSelector: {
					selected: 1
				},

				title: {
					text: "AAPL Stock Price"
				},

				series: series
			});
		}
	});
};

//When document was loaded.
jQuery(document).ready(function () {

	chart("currentAlarm", "#bar-chart-current");
	chart2("historyAlarm", "#bar-chart-history");

});

//Resize
$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
	$(".tab-content .chart.tab-pane.active").highcharts().reflow();
});
