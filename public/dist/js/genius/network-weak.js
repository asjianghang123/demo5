var weakExport = function () {
	var wscale = Ladda.create(document.getElementById("weakExport"));
	$.ajax({
		type: "GET",
		url: "weak/weakExport",
		beforeSend: function () {
			wscale.start();
		},
		success: function (data) {
			wscale.stop();
			download(data);
		}
	});

};

function paramOverview(route, block) {
	$.ajax({
		type: "GET",
		url: route,
		//data : {range : "day"},
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
				legend: {
					enabled: false
				},
				credits: {
					enabled: false
				},
				series: [{
						name: "数量",
						data: data.series[0].data,
						dataLabels: {
							enabled: true,
							rotation: -90,
							color: "#FFFFFF",
							align: "right",
							format: "{point.y}", // one decimal
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
}
jQuery(document).ready(function () {
	paramOverview("weak/baselineParamNum", "#baselineParamNum");
	paramOverview("weak/baselineBSNum", "#baselineBSNum");
	//paramOverview('weak/consistencyParamNum',"#consistencyParamNum");
	//paramOverview('weak/consistencyBSNum',"#consistencyBSNum");
});