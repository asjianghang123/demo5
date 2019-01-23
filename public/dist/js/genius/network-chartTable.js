/**
 * Created by wangyang on 2016/6/23.
 */

/**
 * Draw gauge.
 */

$(document).ready(function () {
	var dataFrom = $("#dataFrom").val("0");
	var dataTo = $("#dataTo").val("100");
	// if(dataFrom < dataTo){
	// 	$('#dataFrom').onchange(
	// 		alert('123');
	// 	);
	// }
});

var allyAxis = [];

function lowAccess() {
	// 	$('#dataFrom').val(allyAxis[0]);
	// $('#dataTo').val(allyAxis[1]);
}

function highLost() {
	// $('#dataFrom').val(allyAxis[2]);
	// $('#dataTo').val(allyAxis[3]);
}

function badHandover() {
	// $('#dataFrom').val(allyAxis[4]);
	// $('#dataTo').val(allyAxis[5]);
}

/*function dataFrom(){
 // var dataFrom = $('#dataFrom').val();
 // var dataTo = $('#dataTo').val();
 // chartRankDefine('lowAccessDefine','chart-access',dataFrom,dataTo);
 // chartRankDefine('highLostDefine','chart-lost',dataFrom,dataTo);
 // chartRankDefine('badHandoverDefine','chart-handover',dataFrom,dataTo);
 }
 
 function dataTo(){
 // var dataFrom = $('#dataFrom').val();
 // var dataTo = $('#dataTo').val();
 // chartRankDefine('lowAccessDefine','chart-access',dataFrom,dataTo);
 // chartRankDefine('highLostDefine','chart-lost',dataFrom,dataTo);
 // chartRankDefine('badHandoverDefine','chart-handover',dataFrom,dataTo);
 }
 
 function dataFrom1(){
 // var dataFrom = $('#dataFrom1').val();
 // var dataTo = $('#dataTo1').val();
 // chartTrendMore('lowAccessTrendMore','chart-access',dataFrom,dataTo);
 // chartTrendMore('highLostTrendMore','chart-lost',dataFrom,dataTo);
 // chartTrendMore('badHandoverTrendMore','chart-handover',dataFrom,dataTo);
 }
 
 function dataTo1(){
 // var dataFrom = $('#dataFrom1').val();
 // var dataTo = $('#dataTo1').val();
 // chartTrendMore('lowAccessTrendMore','chart-access',dataFrom,dataTo);
 // chartTrendMore('highLostTrendMore','chart-lost',dataFrom,dataTo);
 // chartTrendMore('badHandoverTrendMore','chart-handover',dataFrom,dataTo);
 }*/

function buttonClick() {
	var display = $("#dataFrom").css("display");
	if (display != "none") {
		var dataFrom = $("#dataFrom").val();
		var dataTo = $("#dataTo").val();
		chartRankDefine("network/lowAccessDefine", "chart-access", dataFrom, dataTo);
		chartRankDefine("network/highLostDefine", "chart-lost", dataFrom, dataTo);
		chartRankDefine("network/badHandoverDefine", "chart-handover", dataFrom, dataTo);
	} else {
		var dataFrom = $("#dataFrom1").val();
		var dataTo = $("#dataTo1").val();
		chartTrendMore("network/lowAccessTrendMore", "chart-access", dataFrom, dataTo);
		chartTrendMore("network/highLostTrendMore", "chart-lost", dataFrom, dataTo);
		chartTrendMore("network/badHandoverTrendMore", "chart-handover", dataFrom, dataTo);
	}
}


var chartRankDefine = function (route, block, dataFrom, dataTo) {
	$.ajax({
		type: "GET",
		url: route,
		data: {dataFrom: dataFrom, dataTo: dataTo},
		dataType: "json",
		beforeSend: function () {
			$("#" + block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
		},
		success: function (data) {
			if (route == "lowAccess") { //无线接通率yAxis
				$("#dataFrom").val(data.yAxis[0]);
				$("#dataTo").val(data.yAxis[4]);
			}
			allyAxis.push(data.yAxis[0], data.yAxis[4]);
			//var data = data;
			$("#" + block).html("");
			var chartTest1 = new Highcharts.StockChart({

				//$(block).highcharts('StockChart', {
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

				legend: {enabled: true},
				credits: {enabled: false},

				// yAxis: {
				// 	tickPositions: data.yAxis,
				// },

				chart: {
					type: "column",
					renderTo: block,
					alignTicks: false
				},

				title: {
					text: null
				},
				series: data.series
						//series: data['series']
			});
		}
	});
};

var chartRank = function (route, block) {
	$.ajax({
		type: "GET",
		url: route,
		//data : {range : "day"},
		dataType: "json",
		beforeSend: function () {
			$("#" + block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
		},
		success: function (data) {
			if (route == "lowAccess") { //无线接通率yAxis
				$("#dataFrom").val(data.yAxis[0]);
				$("#dataTo").val(data.yAxis[4]);
			}
			allyAxis.push(data.yAxis[0], data.yAxis[4]);
			//var data = data;
			$("#" + block).html("");
			var chartTest1 = new Highcharts.StockChart({

				//$(block).highcharts('StockChart', {
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

				legend: {enabled: true},
				credits: {enabled: false},

				// yAxis: {
				// 	tickPositions: data.yAxis,
				// },

				chart: {
					type: "column",
					renderTo: block,
					alignTicks: false
				},

				title: {
					text: null
				},
				series: data.series
						//series: data['series']
			});
		}
	});
};

var chartTrendMore = function (route, block, dataFrom, dataTo) {
	$.ajax({
		type: "GET",
		url: route,
		data: {dataFrom: dataFrom, dataTo: dataTo},
		dataType: "json",
		beforeSend: function () {
			$("#" + block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
		},
		success: function (data) {
			//var data = data;
			$(block).html("");
			var chartTest2 = new Highcharts.StockChart({

				//$(block).highcharts('StockChart', {
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
					selected: 1,
					//inputEnabled : false
					inputDateFormat: "%Y-%m-%d",
					inputEditDateFormat: "%Y-%m-%d"
				},

				legend: {enabled: true},
				credits: {enabled: false},

				// yAxis: {
				// 	tickPositions: data.yAxis,
				// },

				chart: {
					type: "line",
					renderTo: block,
					alignTicks: false
				},

				title: {
					text: null
				},

				series: data.series
			});
		}
	});
};

var chartTrend = function (route, block) {
	$.ajax({
		type: "GET",
		url: route,
		//data : {range : "day"},
		dataType: "json",
		beforeSend: function () {
			$("#" + block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
		},
		success: function (data) {
			//var data = data;
			$(block).html("");
			var chartTest2 = new Highcharts.StockChart({

				//$(block).highcharts('StockChart', {
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
					selected: 1,
					//inputEnabled : false
					inputDateFormat: "%Y-%m-%d",
					inputEditDateFormat: "%Y-%m-%d"
				},

				legend: {enabled: true},
				credits: {enabled: false},

				// yAxis: {
				// 	tickPositions: data.yAxis,
				// },

				chart: {
					type: "line",
					renderTo: block,
					alignTicks: false
				},

				title: {
					text: null
				},

				series: data.series
			});
		}
	});
};


//When document was loaded.
jQuery(document).ready(function () {
	// var width = $("#chart-access").width();
	// $("#chart-lost").width(width);
	// $("#chart-handover").width(width);
	// $("#chart_erab_success").width(width);
	// $("#chart_erab_lost").width(width);
	// $("#chart_wireless_success").width(width);
	// $("#chart_volte_handover").width(width);
	// $("#chart1_wireless_success").width(width);
	// $("#chart1_erab_lost").width(width);
	// $("#chart1_VideoCall_success").width(width);
	// $("#chart1_eSRVCC_handover").width(width);

	chartTrend("network/lowAccessTrend", "chart-access");
	chartTrend("network/highLostTrend", "chart-lost");
	chartTrend("network/badHandoverTrend", "chart-handover");

	chartTrend("network/erabSuccessTrend", "chart_erab_success");
	chartTrend("network/erabLostTrend", "chart_erab_lost");
	chartTrend("network/wirelessSuccessTrend", "chart_wireless_success");
	chartTrend("network/volteHandoverTrend", "chart_volte_handover");

	chartTrend("network/chart1WireSuccTrend", "chart1_wireless_success");
	chartTrend("network/chart1ErbLostTrend", "chart1_erab_lost");
	chartTrend("network/chart1VideoSuccTrend", "chart1_VideoCall_success");
	chartTrend("network/chart1EsrvccHanderTrend", "chart1_eSRVCC_handover");

	$("#rank_threeKeys").click(function () {
		$("#dataFrom1").css("display", "none");
		$("#dataFrom").css("display", "inline");
		$("#dataTo1").css("display", "none");
		$("#dataTo").css("display", "inline");
		chartRank("network/lowAccessDefine", "chart-access");
		chartRank("network/highLostDefine", "chart-lost");
		chartRank("network/badHandoverDefine", "chart-handover");
	});

	$("#trend_threeKeys").click(function () {
		var dataFrom = $("#dataFrom1").val();
		var dataTo = $("#dataTo1").val();
		$("#dataFrom1").css("display", "inline");
		$("#dataFrom").css("display", "none");
		$("#dataTo1").css("display", "inline");
		$("#dataTo").css("display", "none");
		chartTrend("network/lowAccessTrend", "chart-access");
		chartTrend("network/highLostTrend", "chart-lost");
		chartTrend("network/badHandoverTrend", "chart-handover");
		// chartTrendMore('lowAccessTrend','chart-access',dataFrom,dataTo);
		// chartTrendMore('highLostTrend','chart-lost',dataFrom,dataTo);
		// chartTrendMore('badHandoverTrend','chart-handover',dataFrom,dataTo);
	});

	// $("#chooseY").click(function(){
	// 	alert('123');
	// });

	$("#rank_volte").click(function () {
		chartRank("network/erabSuccess", "chart_erab_success");
		chartRank("network/erabLost", "chart_erab_lost");
		chartRank("network/wirelessSuccess", "chart_wireless_success");
		chartRank("network/volteHandover", "chart_volte_handover");
	});

	$("#trend_volte").click(function () {
		chartTrend("network/erabSuccessTrend", "chart_erab_success");
		chartTrend("network/erabLostTrend", "chart_erab_lost");
		chartTrend("network/wirelessSuccessTrend", "chart_wireless_success");
		chartTrend("network/volteHandoverTrend", "chart_volte_handover");
	});

	$("#rank_video").click(function () {
		chartRank("network/chart1WireSucc", "chart1_wireless_success");
		chartRank("network/chart1ErbLost", "chart1_erab_lost");
		chartRank("network/chart1VideoSucc", "chart1_VideoCall_success");
		chartRank("network/chart1EsrvccHander", "chart1_eSRVCC_handover");
	});

	$("#trend_video").click(function () {
		chartTrend("network/chart1WireSuccTrend", "chart1_wireless_success");
		chartTrend("network/chart1ErbLostTrend", "chart1_erab_lost");
		chartTrend("network/chart1VideoSuccTrend", "chart1_VideoCall_success");
		chartTrend("network/chart1EsrvccHanderTrend", "chart1_eSRVCC_handover");
	});


	// //VideoCall指标
	// chart('genius/public/chart1WireSucc','#chart1_wireless_success');

	// chart('genius/public/chart1ErbLost','#chart1_erab_lost');

	// chart('genius/public/chart1VideoSucc','#chart1_VideoCall_success');

	// chart('genius/public/chart1EsrvccHander','#chart1_eSRVCC_handover');


});

//Resize
$('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
	$(".tab-content .chart.tab-pane.active").highcharts().reflow();
});
