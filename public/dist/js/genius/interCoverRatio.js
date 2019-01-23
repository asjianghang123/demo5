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
	$.get("interCoverRatio/interTime", params, function (data) {
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

	toogle("interCoverRatio");
});

function search() {
	var l = Ladda.create(document.getElementById("search"));
	l.start();
	var time = $("#startTime").val();
	var params = {
		date: time
	};
	$.get("interCoverRatio/SearchInterCoverRatio", params, function (data) {
		//console.log(data);
		l.stop();
		if (data == "databaseNotExists") {
			// alert("");
			layer.open({
				title: "提示",
				content: "数据库不存在"
			});
			//l.stop();
			return;
		}
		data = eval("(" + data + ")");
		$("#interCoverRatio").html("");
		$("#interCoverRatio").highcharts({
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
				pointFormat: "高干扰占比：{point.y:.2f} %",
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

