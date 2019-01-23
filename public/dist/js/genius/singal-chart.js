var chartTrend = function (route, block) {
	$.ajax({
		type: "GET",
		url: route,
		dataType: "json",
		beforeSend: function () {
			$(".box-body").html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">加载中，请稍等');
		},
		success: function (data) {
			//data = JSON.parse(data);
			$(".box-body").empty();
			for (var i in data) {
				$(".box-body").append('<div class="chart" id="' + i + '" style="position: relative;height: 400px;"></div>');
				//var chartTest2 = new Highcharts.StockChart({
				$("#" + i).highcharts("StockChart", {
					rangeSelector: {
						buttons: [{
								type: "month",
								count: 1,
								text: "1m"
							}, {
								type: "day",
								count: 1,
								text: "1D"
							}, {
								type: "day",
								count: 3,
								text: "3D"
							}, {
								type: "day",
								count: 7,
								text: "7D"
							}, {
								type: "all",
								count: 1,
								text: "All"
							}],
						selected: 3,
						inputDateFormat: "%Y-%m-%d",
						inputEditDateFormat: "%Y-%m-%d",
					},
					legend: {enabled: true},
					credits: {enabled: false},
					chart: {
						type: "line",
						renderTo: block,
						alignTicks: false
					},

					title: {
						text: i
					},

					series: data[i]
				});
			}
		}
	});
};

jQuery(document).ready(function () {
	//chartTrend('lowAccessTrend','chart-access');
	toogle("singal");
	chartTrend("singal/singalTrend", "chart-access");
});
