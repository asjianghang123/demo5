jQuery(document).ready(function () {
	$("#startTime").datepicker({format: "yyyy-mm-dd"});  //返回日期
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;

	//console.log(today);
	var params = {
		city: getFirstCity()
	};
	$.get("overlapCoverOverview/busyTime", params, function (data) {
		var sdata = [];
		for (var i = 0; i < data.length; i++) {
			if (data[i] === today) {
				continue;
			}
			sdata.push(data[i]);
		}
		sdata.push(today);
		$("#startTime").datepicker("setValues", sdata);
	});
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#startTime").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");

	toogle("overlapCoverOverview");
});

function search() {
	var l = Ladda.create(document.getElementById("search"));
	l.start();
	var time = $("#startTime").val();
	var params = {
		date: time
	};
	$.get("overlapCoverOverview/SearchOverlapCoverOverview", params, function (data) {
		l.stop();
		if (data == "databaseNotExists") {
			layer.open({
				title: "提示",
				content: "数据库不存在"
			});
			return;
		}
		data = eval("(" + data + ")");
		$("#overlapCoverOverview").html("");
		$("#overlapCoverOverview").highcharts({
			chart: {
				type: "column"
			},
			title: {
				text: null
			},
			subtitle: {
				text: data.date
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
				pointFormat: "重叠覆盖占比：{point.y:.1f} %",
				shared: true,
				useHTML: true
			},
			legend: {
				enabled: false
			},
			credits: {
				enabled: false
			},
			series: data.series
		});

	});
}