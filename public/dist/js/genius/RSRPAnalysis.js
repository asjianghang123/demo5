$(document).ready(function () {

	$("#date").datepicker({format: "yyyy-mm-dd"});  //返回日期
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;

	// console.log(today);
	var params = {
		city: getFirstCity()
	};
	$.post("RSRPAnalysis/getRSRPdate", params, function (data) {
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
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#date").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");

	toogle("RSRPAnalysis");
});

function search() {
	var l = Ladda.create(document.getElementById("search"));
	l.start();
	var params = {
		date: $("#date").val(),
		cell: $("#cell").val()
	};

	var url = "RSRPAnalysis/RSRPAnalysisdata";
	$.ajax({
		type: "post",
		url: url,
		dataType: "json",
		data: params,
		success: function (data) {
			l.stop();
			if (data == "") {
				layer.open({
					title: "提示",
					content: "该小区无数据"
				});
				return;
			}
			var ser_str = JSON.stringify(data);
			ser_str = ser_str.replace(/"/g, "");
			ser_str = ser_str.replace(/A/g, "\"");
			var ser_obj = eval("(" + ser_str + ")");
			$("#CellRSRPAnalysis").html("");
			$("#CellRSRPAnalysis").highcharts({
				exporting: {
					enabled: true,
				},
				chart: {
					type: "column"
				},
				title: {
					text: "小区RSRP分析柱状图"
				},
				subtitle: {
					text: ""
				},
				xAxis: {
					categories: [
						"signal>-80",
						"-80>=signal>-90",
						"-90>=signal>-100",
						"-100>=signal>-110",
						"signal<=-110"
					],
					crosshair: true
				},
				yAxis: {
					//max:-80,
					min: 0,
					//tickPositions: [0, 5, 10, 15, 20, 25],
					title: {
						text: "落在各电平区间的数量 (个)"
					}
					//minTickInterval: 10
				},
				tooltip: {
					headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
					pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
							'<td style="padding:0"><b>{point.y:.0f} 个</b></td></tr>',
					footerFormat: '</table>',
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
		}
	});
	l.stop();
}