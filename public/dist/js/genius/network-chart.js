/**
 * Created by wangyang on 2016/6/23.
 */

// var plygons = [];
//
// var selected = "";
//
// var bmap = new BMap.Map("map");
//
// // 初始化地图,设置中心点坐标和地图级别
// bmap.centerAndZoom(new BMap.Point(120.602701, 31.807101), 8);
//
// bmap.disableDoubleClickZoom();
//
// //bmap.addControl(new BMap.NavigationControl());
//
// bmap.addControl(new BMap.MapTypeControl());
//
// //去除路网
// bmap.setMapStyle({
// 	styleJson:[
// 		{
// 			"featureType": "poi",
// 			"elementType": "all",
// 			"stylers": {
// 				"color": "#ffffff",
// 				"visibility": "off"
// 			}
// 		},
// 		{
// 			"featureType": "road",
// 			"elementType": "all",
// 			"stylers": {
// 				"color": "#ffffff",
// 				"visibility": "off"
// 			}
// 		},
// 		{
// 			"featureType": "background",
// 			"elementType": "all",
// 			"stylers": {
// 				"color": "#ffffff"
// 			}
// 		}
// 	]
// });
//
// // 创建地理编码实例
// var myGeo = new BMap.Geocoder();
//
// bmap.addEventListener("click", function(e) {
// 	myGeo.getLocation(new BMap.Point(e.point.lng, e.point.lat), function (result) {
// 		if (result) {
// 			var addComp = result.addressComponents;
// 			for (var city in cityNames) {
// 				if (city == addComp.city) {
// 					var params = {};
// 					if (selected == addComp.city) {
// 						plygons[addComp.city].setFillColor("#fff");
// 						params = {'city': 'province'};
// 						selected = "";
// 					} else {
// 						if (selected != "") {
// 							plygons[selected].setFillColor("#fff");
// 						}
// 						setCityFillColor(addComp.city, cityNames[addComp.city]);
// 						selected = addComp.city;
// 						params = {'city': addComp.city};
// 					}
//
// 					$.get('threeKeysGauge', params, function(data) {
// 						threeKeys(data);
// 					});
// 					$.get('volteGauge', params, function(data) {
// 						volteGauge(data);
// 					});
// 					$.get('videosGauge', params, function(data) {
// 						videosGauge(data);
// 					});
// 				}
// 			}
// 		}
// 	});
// });
//
// /*var cityNames = {
// 	"常州市": '#ff0000',
// 	"无锡市": '#00ff00',
// 	"苏州市": '#0000ff',
// 	"镇江市": '#f00000',
// 	"南通市": '#0f0000'
// };*/
//
// function getColorCity(){
//     var cityNames = {};
//     $.ajax({
//         url:'scale/citysColor',
//         async:false,
//         type:'get',
//         success:function(data){
//             var cityName = [];
//             for(var i=0; i<data.length; i++){
//                 cityNames[data[i][0]] = data[i][1];
//             }
//         }
//     });
//     return cityNames;
// }
// var cityNames = getColorCity();
// console.log(cityNames);
//
// function getBoundary(cityname){
// 	var bdary = new BMap.Boundary();
// 	bdary.get(cityname, function(rs){ // 异步加载
// 		var count = rs.boundaries.length; //行政区域的点有多少个
// 		var ply = {};
// 		for(var i = 0; i < count; i++){
// 			ply = new BMap.Polygon(rs.boundaries[i], {strokeWeight: 2, strokeColor: "#4169e1"}); //建立多边形覆盖物
// 		}
// 		bmap.addOverlay(ply);  //添加覆盖物
// 		plygons[cityname]=ply;
// 	});
// }
//
// function setCityFillColor(city,color) {
// 	plygons[city].setFillColor(color);
// }
//
// for (var key in cityNames) {
// 	getBoundary(key);
// }

var myChart = echarts.init(document.getElementById("map"));
var dataArr;
$.ajax({
	url: "network/getOption",
	async: false,
	success: function (data) {
		dataArr = data;
	}
});
var option = {
	tooltip: {
			//show: false //不显示提示标签
		formatter: "{b}", //提示标签格式
		backgroundColor: "#ff7f50", //提示标签背景颜色
		textStyle: {color: "#fff"} //提示标签字体颜色
	},
	series: [{
		type: "map",
		mapType: dataArr[0],
		label: {
			normal: {
				show: true, //显示地市标签
				textStyle: {color: "#389BB7", fontSize: 18}//省份标签字体颜色
			},
			emphasis: {//对应的鼠标悬浮效果
				show: true,
				textStyle: {color: "#3c8dbc"}
			}
		},
		itemStyle: {
			normal: {
				borderWidth: .5, //区域边框宽度
				borderColor: "#3c8dbc", //区域边框颜色
				areaColor: "#ffffff", //区域颜色
			},
			emphasis: {
				borderWidth: .5,
						
				borderColor: "#4b0082",
				areaColor: "#3c8dbc",
			}
		},
		data: []
	}],
};
// var option = getOption();
myChart.setOption(option);
myChart.on("click", function (params) {
	var name = params.name;
	var data = option.series[0].data;
	if (data.length != 0) {
		if (data[0].name == name) {
			data.splice(0, data.length);
			params = {"city": "province"};
		} else {
			data.splice(0, data.length);
			data.push({name: name, selected: true});
			params = {"city": name};
		}
	} else {
		data.push({name: name, selected: true});
		params = {"city": name};
	}

	myChart.setOption(option);
	$.get("network/threeKeysGauge", params, function (data) {
		threeKeys(data);
	});
	$.get("network/volteGauge", params, function (data) {
		volteGauge(data);
	});
	$.get("network/videosGauge", params, function (data) {
		videosGauge(data);
	});
});
		/**
		 * Draw gauge.
		 */
var threeKeys = function (data) {
	var data0;
	if (data.data[0] < 95 || data.data[0] > 100) {
		data0 = 95;
	} else {
		data0 = data["data"][0];
	}
	var data1;
	if (data.data[1] < 0 || data.data[1] > 5) {
		data1 = 0;
	} else {
		data1 = data.data[1];
	}
	var data2;
	if (data.data[2] < 95 || data.data[2] > 100) {
		data2 = 95;
	} else {
		data2 = data["data"][2];
	}
	$("#key3_kpigroup").highcharts({
		chart: {
			type: "gauge",
			plotBorderWidth: 0,
			plotBackgroundImage: null,
			margin: [0, 0, 0, 0],
			spacingTop: 0,
			height: 100
		},
		title: {
			text: null
		},
		pane: [{
				startAngle: - 45,
				endAngle: 45,
				background: null,
				center: ["18%", "120%"],
				size: 150
			}, {
			startAngle: - 45,
			endAngle: 45,
			background: null,
			center: ["50%", "120%"],
			size: 150
		}, {
					startAngle: - 45,
					endAngle: 45,
					background: null,
					center: ["82%", "120%"],
					size: 150
				}],
		tooltip: {
			enabled: false
		},
		yAxis: [{
			min: 95,
			max: 100,
			minorTickPosition: "outside",
			tickPosition: "outside",
			labels: {
				rotation: "auto",
				distance: 20
			},
			plotBands: [{
				from: 95,
				to: 98,
				color: "#DF5353",
				innerRadius: "100%",
				outerRadius: "105%"
			}, {
					from: 98,
					to: 99,
					color: "#DDDF0D",
					innerRadius: "100%",
					outerRadius: "105%"
				}, {
						from: 99,
						to: 100,
						color: "#55BF3B",
						innerRadius: "100%",
						outerRadius: "105%"
					}],
			pane: 0,
			title: {
				text: "无线接通率<br />" + [data.data[0]],
				y: - 20
			}
		}, {
			reversed: true,
			min: 0,
			max: 5,
			minorTickPosition: "outside",
			tickPosition: "outside",
			labels: {
						rotation: "auto",
						distance: 20
					},
			plotBands: [{
						from: 0,
						to: 1,
						color: "#55BF3B",
						innerRadius: "100%",
							outerRadius: "105%"
					}, {
					from: 1,
					to: 2,
					color: "#DDDF0D",
					innerRadius: "100%",
					outerRadius: "105%"
				}, {
						from: 2,
						to: 5,
						color: "#DF5353",
						innerRadius: "100%",
						outerRadius: "105%"
					}],
			pane: 1,
			title: {
						text: "无线掉线率<br />" + [data.data[1]],
						y: - 20
					}
		}, {
					min: 95,
					max: 100,
					minorTickPosition: "outside",
					tickPosition: "outside",
					labels: {
					rotation: "auto",
					distance: 10
				},
					plotBands: [{
					from: 95,
					to: 98,
					color: "#DF5353",
					innerRadius: "100%",
					outerRadius: "105%"
				}, {
						from: 98,
						to: 99,
						color: "#DDDF0D",
						innerRadius: "100%",
						outerRadius: "105%"
					}, {
						from: 99,
						to: 100,
						color: "#55BF3B",
						innerRadius: "100%",
						outerRadius: "105%"
					}],
					pane: 2,
					title: {
					text: "切换成功率<br />" + [data.data[2]],
					y: - 20
						}
				}],
		plotOptions: {
			gauge: {
						dataLabels: {
						enabled: false
					},
						dial: {
						radius: "100%"
					}
					}
		},
		credits: {
			enabled: false
		},
		series: [{
			name: "无线接通率",
			data: [data0],
			yAxis: 0
		}, {
					name: "无线掉线率",
					data: [data1],
					yAxis: 1
				}, {
				name: "切换成功率",
				data: [data2],
				yAxis: 2
			}]
	});
};
var volteGauge = function (data) {
	var data0, data1, data2;
	if (data[0] < 95 || data[0] > 100) {
		data0 = 95;
	} else {
		data0 = data[0];
	}
	if (data[1] < 0 || data[1] > 5) {
		data1 = 0;
	} else {
		data1 = data[1];
	}
	if (data[2] < 95 || data[2] > 100) {
		data2 = 95;
	} else {
		data2 = data[2];
	}
	if (data[3] < 95 || data[3] > 100){
		data3 = 95;
	} else{
		data3 = data[3];
	}
	$("#vlote_kpigroup").highcharts({

				chart: {
					type: "gauge",
					plotBorderWidth: 0,
					plotBackgroundImage: null,
					margin: [0, 0, 0, 0],
					spacingTop: 0,
					height: 100
				},
		title: {
					text: null
				},
		pane: [{
					startAngle: - 45,
					endAngle: 45,
					background: null,
					center: ["13%", "120%"],
					size: 150
				}, {
				startAngle: - 45,
				endAngle: 45,
				background: null,
				center: ["38%", "120%"],
				size: 150
			}, {
					startAngle: - 45,
					endAngle: 45,
					background: null,
					center: ["63%", "120%"],
					size: 150
				}, {
					startAngle: - 45,
					endAngle: 45,
					background: null,
					center: ["87%", "120%"],
					size: 150
				}],
		tooltip: {
				enabled: false
				},
		yAxis: [{
					min: 95,
					max: 100,
					minorTickPosition: "outside",
					tickPosition: "outside",
					labels: {
					rotation: "auto",
					distance: 20
				},
					plotBands: [{
					from: 95,
					to: 98,
					color: "#DF5353",
					innerRadius: "100%",
					outerRadius: "105%"
				}, {
						from: 98,
						to: 99,
						color: "#DDDF0D",
						innerRadius: "100%",
						outerRadius: "105%"
					}, {
							from: 99,
							to: 100,
							color: "#55BF3B",
							innerRadius: "100%",
							outerRadius: "105%"
						}],
				pane: 0,
				title: {
						text: "volte接入成功率<br />" + [data[0]],
						y: - 5
					}
			}, {
				reversed: true,
				min: 0,
				max: 5,
				minorTickPosition: "outside",
				tickPosition: "outside",
				labels: {
						rotation: "auto",
						distance: 20
					},
				plotBands: [{
						from: 0,
						to: 1,
						color: "#55BF3B",
						innerRadius: "100%",
						outerRadius: "105%"
					}, {
						from: 1,
						to: 2,
						color: "#DDDF0D",
						innerRadius: "100%",
						outerRadius: "105%"
					}, {
							from: 2,
							to: 5,
							color: "#DF5353",
							innerRadius: "100%",
							outerRadius: "105%"
						}],
				pane: 1,
				title: {
						text: "volte无线掉线率<br />" + [data[1]],
						y: - 5
					}
			}, {
					min: 95,
					max: 100,
					minorTickPosition: "outside",
					tickPosition: "outside",
					labels: {
						rotation: "auto",
						distance: 20
					},
					plotBands: [{
						from: 95,
						to: 98,
						color: "#DF5353",
						innerRadius: "100%",
						outerRadius: "105%"
					}, {
						from: 98,
						to: 99,
						color: "#DDDF0D",
						innerRadius: "100%",
						outerRadius: "105%"
					}, {
							from: 99,
							to: 100,
							color: "#55BF3B",
							innerRadius: "100%",
							outerRadius: "105%"
						}],
					pane: 2,
					title: {
						text: "VoLTE用户切换成功率<br />" + [data[2]],
						y: - 5
					}
				}, {
					min: 95,
					max: 100,
					minorTickPosition: "outside",
					tickPosition: "outside",
					labels: {
						rotation: "auto",
						distance: 20
					},
					plotBands: [{
						from: 95,
						to: 98,
						color: "#DF5353",
						innerRadius: "100%",
						outerRadius: "105%"
					}, {
						from: 98,
						to: 99,
						color: "#DDDF0D",
						innerRadius: "100%",
						outerRadius: "105%"
					}, {
							from: 99,
							to: 100,
							color: "#55BF3B",
							innerRadius: "100%",
							outerRadius: "105%"
						}],
					pane: 3,
					title: {
						text: "eSRVCC切换成功率<br />" + [data[3]],
						y: - 5
					}
				}],
		plotOptions: {
					gauge: {
					dataLabels: {
							enabled: false
						},
					dial: {
							radius: "100%"
						}
				}
				},
		credits: {
					enabled: false
				},
		series: [{
					name: "无线接通率",
					data: [data0],
					yAxis: 0
				}, {
				name: "无线掉线率",
				data: [data1],
				yAxis: 1
			}, {
					name: "切换成功率",
					data: [data2],
					yAxis: 2
				}, {
					name: "eSRVCC切换成功率",
					data: [data3],
					yAxis: 3
				}]

	});
};
var videosGauge = function (data) {
	var data0, data1, data2;
	if (data[0] < 95 || data[0] > 100) {
			data0 = 95;
		} else {
			data0 = data[0];
		}
	if (data[1] < 0 || data[1] > 5) {
			data1 = 0;
		} else {
			data1 = data[1];
		}
	if (data[2] < 95 || data[2] > 100) {
			data2 = 95;
		} else {
			data2 = data[2];
		}
	$("#video_kpigroup").highcharts({

			chart: {
					type: "gauge",
					plotBorderWidth: 0,
					plotBackgroundImage: null,
					margin: [0, 0, 0, 0],
					spacingTop: 0,
					height: 100
				},
			title: {
					text: null
				},
			pane: [{
					startAngle: - 45,
					endAngle: 45,
					background: null,
					center: ["18%", "120%"],
					size: 150
				}, {
				startAngle: - 45,
				endAngle: 45,
				background: null,
				center: ["50%", "120%"],
				size: 150
			}, {
					startAngle: - 45,
					endAngle: 45,
					background: null,
					center: ["82%", "120%"],
					size: 150
				}],
			tooltip: {
						enabled: false
				},
			yAxis: [{
					min: 95,
					max: 100,
					minorTickPosition: "outside",
					tickPosition: "outside",
					labels: {
					rotation: "auto",
					distance: 20
				},
					plotBands: [{
					from: 95,
					to: 98,
					color: "#DF5353",
					innerRadius: "100%",
					outerRadius: "105%"
				}, {
						from: 98,
						to: 99,
						color: "#DDDF0D",
						innerRadius: "100%",
						outerRadius: "105%"
					}, {
							from: 99,
							to: 100,
							color: "#55BF3B",
							innerRadius: "100%",
							outerRadius: "105%"
						}],
					pane: 0,
					title: {
					text: "video接入成功率<br />" + [data[0]],
					y: - 10
				}
				}, {
				reversed: true,
				min: 0,
				max: 5,
				minorTickPosition: "outside",
				tickPosition: "outside",
				labels: {
						rotation: "auto",
						distance: 20
					},
				plotBands: [{
						from: 0,
						to: 1,
						color: "#55BF3B",
						innerRadius: "100%",
						outerRadius: "105%"
					}, {
						from: 1,
						to: 2,
						color: "#DDDF0D",
						innerRadius: "100%",
						outerRadius: "105%"
					}, {
							from: 2,
							to: 5,
							color: "#DF5353",
							innerRadius: "100%",
							outerRadius: "105%"
						}],
				pane: 1,
				title: {
						text: "video无线掉线率<br />" + [data[1]],
						y: - 10
					}
			}, {
					min: 95,
					max: 100,
					minorTickPosition: "outside",
					tickPosition: "outside",
					labels: {
						rotation: "auto",
						distance: 20
					},
					plotBands: [{
						from: 95,
						to: 98,
						color: "#DF5353",
						innerRadius: "100%",
						outerRadius: "105%"
					}, {
						from: 98,
						to: 99,
						color: "#DDDF0D",
						innerRadius: "100%",
						outerRadius: "105%"
					}, {
							from: 99,
							to: 100,
							color: "#55BF3B",
							innerRadius: "100%",
							outerRadius: "105%"
						}],
					pane: 2,
					title: {
						text: "VideoCall用户切换成功率<br />" + [data[2]],
						y: - 10
					}
				}],
			plotOptions: {
					gauge: {
					dataLabels: {
							enabled: false
						},
					dial: {
							radius: "100%"
						}
				}
				},
			credits: {
					enabled: false
				},
			series: [{
					name: "无线接通率",
					data: [data0],
					yAxis: 0
				}, {
				name: "无线掉线率",
				data: [data1],
				yAxis: 1
			}, {
					name: "切换成功率",
					data: [data2],
					yAxis: 2
				}]
		});
};
var kpiExport = function () {
	var lKpi = Ladda.create(document.getElementById("kpiExport"));
	$.ajax({
		type: "GET",
		url: "network/kpiExport",
		beforeSend: function () {
					lKpi.start();
				},
		success: function (data) {
					lKpi.stop();
					download(data);
				}
	});
};
//When document was loaded.
jQuery(document).ready(function () {

/*var params = {
 city:'常州市'
 };
 
 $.get('threeKeysGauge', params, function(data){
 threeKeys(data);
 setCityFillColor('常州市',cityNames['常州市']);
 });
 $.get('volteGauge', params, function(data){
 volteGauge(data);
 setCityFillColor('常州市',cityNames['常州市']);
 });
 $.get('videosGauge', params, function(data){
 videosGauge(data);
 setCityFillColor('常州市',cityNames['常州市']);
 });*/

	var params = {
		city: "province"
	};
	$.get("network/threeKeysGauge", params, function (data) {
		threeKeys(data);
				//setCityFillColor('常州市',cityNames['常州市']);
	});
	$.get("network/volteGauge", params, function (data) {
		volteGauge(data);
				//setCityFillColor('常州市',cityNames['常州市']);
	});
	$.get("network/videosGauge", params, function (data) {
		videosGauge(data);
				//setCityFillColor('常州市',cityNames['常州市']);
	});
	toogle("network");
});
//Resize
$('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
	$(".tab-content .chart.tab-pane.active").highcharts().reflow();
});
