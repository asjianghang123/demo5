$(document).ready(function () {
	setTime();
	//getAllDatabase();
	toogle("LTENeighRationalityAnalysis");
});


function setTime() {
	$("#dateTime").datepicker({format: "yyyy-mm-dd"});  //返回日期
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;
	console.log(today);

	$.get("fdfdg", function (data) {
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
	//alert(nowTemp);
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#dateTime").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? '' : '';
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");
}
function searchLte() {
	LTENeighRationalitySearch();
}
function LTENeighRationalitySearch() {
	var dateTime = $("#dateTime").val();
	var url = "LTENeighRationality/LTENeighRationalityDistribute";
	chart_column(url, {"dateTime": dateTime}, "#chart-lteNeighRation");
}
var chart_column = function (route, params, block) {
	$.ajax({
		type: "GET",
		url: route,
		data: params,
		dataType: "json",
		beforeSend: function () {
			$(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
		},
		success: function (data) {
			$(block).html("");
			if (data.message) {
				// alert();
				layer.open({
					title: "提示",
					content: data.message
				});
				return;
			}
			$(block).highcharts({
				chart: {
					type: "column"
				},
				title: {
					text: "有效率分布"
				},
				subtitle: {
					text: null
				},
				xAxis: {
					categories: data.category,
					crosshair: true
				},
				yAxis: {
					min: 0,
					title: {
						text: null
					}
				},
				tooltip: {
					shared: true,
					useHTML: true
				},
				plotOptions: {
					column: {
						pointPadding: 0.2,
						borderWidth: 0
					}
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
};