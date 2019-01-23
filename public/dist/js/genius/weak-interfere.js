/**
 * Created by wangyang on 2016/6/23.
 */
jQuery(document).ready(function () {
	stock_column("weak/interfereOverview", "#chart-interfere");

	sstock_column("weak/WeakCoverOverview", "#chart-cover");

	stock_columnn("weak/overlapCoverview", "#chart-overlapCover");
});
var stock_column = function (route, block) {
	$.ajax({
		type: "GET",
		url: route,
		//data : {range : "day"},
		dataType: "json",
		beforeSend: function () {
			$(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
		},
		success: function (data) {
			//var data = data;
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

				legend: {enabled: true},
				credits: {enabled: false},

				yAxis: {
					tickPositions: data.yAxis
				},

				chart: {
					type: "column",
					renderTo: "chart-interfere",
					alignTicks: false
				},

				title: {
					text: null
				},
				// chart: {
				//     type:'column',
				//     renderTo: 'chart-interfere',
				//     alignTicks: false
				// },
				// yAxis: {
				//     labels: {
				//         formatter: function () {
				//             return this.value + '%';
				//         }
				//     },
				//     plotLines: [{
				//         value: 0,
				//         width: 2,
				//         color: 'silver'
				//     }]
				// },
				//     rangeSelector: {
				//         selected: 1
				//     },

				//     title: {
				//         text: null
				//     },
				//     tooltip: {
				//         pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}%</b><br/>',
				//         valueDecimals: 2
				//     },
				//     legend:{enabled:true},
				//     credits: {
				//         enabled: false,
				//     },
				series: data.series
			});
		}
	});
};


var sstock_column = function (route, block) {
	$.ajax({
		type: "GET",
		url: route,
		//data : {range : "day"},
		dataType: "json",
		beforeSend: function () {
			$(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
		},
		success: function (data) {
			//var data = data;
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
					inputEditDateFormat: "%Y-%m-%d",

				},

				legend: {enabled: true},
				credits: {enabled: false},

				yAxis: {
					tickPositions: data.yAxis,
				},

				chart: {
					type: "column",
					renderTo: "chart-cover",
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

var stock_columnn = function (route, block) {
	$.ajax({
		type: "GET",
		url: route,
		//data : {range : "day"},
		dataType: "json",
		beforeSend: function () {
			$(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
		},
		success: function (data) {
			//var data = data;
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
					inputEditDateFormat: "%Y-%m-%d",

				},

				legend: {enabled: true},
				credits: {enabled: false},

				yAxis: {
					tickPositions: data.yAxis,
				},

				chart: {
					type: "column",
					renderTo: "chart-overlapCover",
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

// jQuery(document).ready( function(){
//     sstock_column('WeakCoverOverview','#chart-cover');
// });
// var sstock_column = function(route,block) {
//     $.ajax({
//         type : "GET",
//         url : route,
//         //data : {range : "day"},
//         dataType : "json",
//         beforeSend : function () {
//             $(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
//         },
//         success: function(data) {
//             //var data = data;
//             $(block).html('');
//             var chartTest = new Highcharts.StockChart({
//             rangeSelector : {
//                         buttons : [{
//                             type : 'month',
//                             count : 1,
//                             text : '1m'
//                         }, {
//                             type : 'day',
//                             count : 6,
//                             text : '7D'
//                         }, {
//                             type : 'all',
//                             count : 1,
//                             text : 'All'
//                         }],
//                         selected : 1,
//                         //inputEnabled : false
//                         inputDateFormat: '%Y-%m-%d',
//                         inputEditDateFormat: '%Y-%m-%d',

//                     },

//                     legend:{enabled:true},
//                     credits:{enabled:false},

//                     yAxis: {
//                         tickPositions: data.yAxis,
//                     },

//                     chart: {
//                         type:'column',
//                         renderTo: 'chart-cover',
//                         alignTicks: false
//                     },

//                     title: {
//                         text: null
//                     },

//                 series: data.series
//             });
//         }
//     });  
// };
//};
