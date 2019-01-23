var mapv;
var allCells;
$(function () {
	toogle("overlapCoverPoint");
	getChannels();
	getCity();
	getDate();
	mapv = initMap("map");
	chooseCells();
});
function getCity() {
	$("#city").multiselect({
		//dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "选择城市",
		//filterPlaceholder:'搜索',
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有城市",
		maxHeight: 200,
		maxWidth: "100%"
	});
	var url = "overlapCoverPoint/getAllCity";
	$.ajax({
		type: "GET",
		url: url,
		dataType: "json",
		success: function (data) {
			var newOptions = new Array();
			var obj = new Object();
			$(data).each(function (k, v) {
				var v = eval("("+v+")");
				obj = {
					label: v["text"],
					value: v["value"]
				};
				newOptions.push(obj);
			});
			$("#city").multiselect("dataprovider", newOptions);
		}
	});
}
function getDate() {
	$("#date").datepicker({format: "yyyy-mm-dd"});  //返回日期
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;

	//console.log(today);
	var params = {
		city: getFirstCity()
	};
	$.get("overlapCoverPoint/overlapCoverPointDate", params, function (data) {
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
	$("#city").change(function () {
		var city = $("#city").val();
		var params = {
			city: city
		};
		$.get("overlapCoverPoint/overlapCoverPointDate", params, function (data) {
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
	});

	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#date").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");
}
function getChannels() {
	$("#channel").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "请选择频段",
		//filterPlaceholder:'搜索',
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已全选",
		maxHeight: 200,
		maxWidth: "100%"
	});
	var url = "overlapCoverPoint/interCloudChannel";
	$.ajax({
		type: "GET",
		url: url,
		dataType: "json",
		success: function (data) {
			var newOptions = new Array();
			var obj = new Object();
			$(data).each(function (k, v) {
				var v = eval("("+v+")");
				obj = {
					label: v["text"],
					value: v["value"]
				};
				newOptions.push(obj);
			});
			$("#channel").multiselect("dataprovider", newOptions);
		}
	});
}
function initMap(mapId) {
	var bmap = new BMap.Map(mapId);
	bmap.enableScrollWheelZoom(); // 启用滚轮放大缩小
	bmap.disableDoubleClickZoom(); //禁止双击放大
	// 初始化地图,设置中心点坐标和地图级别
	var arr = setMapPoint();
	bmap.centerAndZoom(new BMap.Point(arr[0], arr[1]), 10);
	var mapv = new Mapv({
		drawTypeControl: false,
		map: bmap // 百度地图的map实例
	});
	return {"bmap": bmap, "mapv": mapv};
}
function initMapLeftControl() {
	//自定义控件
	function LeftControl() {
		this.defaultAnchor = BMAP_ANCHOR_TOP_LEFT;
		this.defaultOffset = new BMap.Size(10, 10);
	}
	//继承Control的API
	LeftControl.prototype = new BMap.Control();
	//初始化控件
	LeftControl.prototype.initialize = function (map) {
		var ul = document.createElement("ul");
		ul.setAttribute("class", "list-group");
		ul.setAttribute("id", "leftControl");
		var li = document.createElement("li");
		li.setAttribute("class", "list-group-item");
		li.textContent = "请滑动鼠标查看小区名";
		ul.appendChild(li);
		//添加DOM元素到地图中
		map.getContainer().appendChild(ul);
		//返回DOM
		return ul;
	};
	//创建控件实例
	var leftCtrl = new LeftControl();
	//添加到地图当中
	mapv.bmap.addControl(leftCtrl);
}
function getData() {
	var S = Ladda.create(document.getElementById("search"));
	S.start();
	var city = $("#city").val();
	var date = $("#date").val();
	var channel = $("#channel").val();
	mapv.bmap.clearOverlays();

	if (!channel) {
		S.stop();
		//alert('请用户选择正确信息');
		layer.open({
			title: "提示",
			content: "请选择频段"
		});
		return;
	}
	initMapLeftControl();

	var params = {
		city: city,
		date: date,
		channel: channel.join(",")
	};
	$("#last_city").val(city);
	$("#last_date").val(date);
	$("#last_channel").val(channel.join(","));
	$(".zhaozi").show();
	$(".loadingImg").show();
	var url = "overlapCoverPoint/overlapCoverPointCells";
	$.ajax({
		type: "GET",
		url: url,
		dataType: "json",
		data: params,
		beforeSend: function () {
			$("map").html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
		},
		success: function (data) {
			if (data == "") {
				layer.open({
					title: "提示",
					content: "暂无数据"
				});
				S.stop();
				return;
			}
			allCells = data;
			drawMap1(data);
			$(".chooseCell").prop("checked", "checked");
			S.stop();
		}
	});
}
function drawMap1(data) {
	var returnData = []; // 取城市的点来做示例展示的点数据
	for (var i = 0; i < data.length; i++) {
		returnData.push({
			lng: data[i].longitude,
			lat: data[i].latitude,
			count: data[i].rate,
			dir: data[i].dir-30,
			cell: data[i].ecgi,
			band: data[i].band,
			master: false
		});
	}
	var layer = new Mapv.Layer({
		mapv: mapv.mapv, // 对应的mapv实例
		zIndex: 1, // 图层层级
		dataType: "point", // 数据类型，点类型
		data: returnData, // 数据
		drawType: "choropleth", // 展示形式
		dataRangeControl: false,
		drawOptions: {// 绘制参数
			size: 20, // 网格大小
			unit: "px", // 单位
			//strokeStyle: 'gray', // 描边颜色
			type: "siteband",
			splitList: [
				{
					start: 0,
					end: 1,
					color: "blue"
				}, {
					start: 1,
					end: 2,
					color: "green"
				}, {
					start: 2,
					end: 3,
					color: "lime"
				}, {
					start: 3,
					end: 4,
					color: "yellow"
				}, {
					start: 4,
					end: 5,
					color: "magenta"
				}, {
					start: 5,
					color: "red"
				}
			],
			events: {
				mousemove: function (e, data) {
					$("#leftControl").children().remove();
					var li = '';
					for (var i = 0; i < data.length; i++) {
						li += ("<li " + 'class="list-group-item"' + ">" + data[i].cell + "</li>");
					}
					$("#leftControl").append(li);
				},
				click: function (e, data) {
					getChartsData(data);
				}
			}
		}
	});
	$("#map1_zhaozi").hide();
	$("#map1_loadingImg").hide();
}
function getChartsData(data) {
	var cells = [];
	for (var i = 0; i < data.length; i++) {
		cells.push(data[i].cell);
	}
	var params = {
		cells: cells.join(","),
		city: $("#city").val(),
		date: $("#date").val()
	};
	var url2 = "overlapCoverPoint/weakCoverCharts";
	$.ajax({
		type: "GET",
		url: url2,
		dataType: "json",
		data: params,
		success: function (data) {
			var ser_str = JSON.stringify(data);
			ser_str = ser_str.replace(/"/g, "");
			ser_str = ser_str.replace(/A/g, "\"");
			var ser_obj = eval("("+ser_str+")");

			$("#weakCoverChartsContainer").highcharts({
				exporting: {
					enabled: true
				},
				chart: {
					type: "column"
				},
				title: {
					text: "弱覆盖小区信号强度分布显示柱状图"
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
					min: 0,
					title: {
						text: "落在各电平区间的数量 (个)"
					}
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

			$("#myModal").modal({
				keyboard: false
			});
		}
	});
}
function chooseCells() {
	$(".chooseCell").on("change", function () {
		var checkbox = [];
		$(".chooseCell").each(function () {
			checkbox.push($(this).prop("checked"));
		});
		var returnData = []; // 取城市的点来做示例展示的点数据
		mapv.bmap.clearOverlays();
		for (var i = 0; i < allCells.length; i++) {
			var count = allCells[i].rate;
			if (count < 1 && checkbox[0]) {
				returnData.push({
					lng: allCells[i].longitude,
					lat: allCells[i].latitude,
					count: count,
					dir: allCells[i].dir-30,
					cell: allCells[i].ecgi,
					band: allCells[i].band,
					master: false
				});
			} else if (count >= 1 && count < 2 && checkbox[1]) {
				returnData.push({
					lng: allCells[i].longitude,
					lat: allCells[i].latitude,
					count: count,
					dir: allCells[i].dir-30,
					cell: allCells[i].ecgi,
					band: allCells[i].band,
					master: false
				});
			} else if (count >= 2 && count < 3 && checkbox[2]) {
				returnData.push({
					lng: allCells[i].longitude,
					lat: allCells[i].latitude,
					count: count,
					dir: allCells[i].dir-30,
					cell: allCells[i].ecgi,
					band: allCells[i].band,
					master: false
				});
			} else if (count >= 3 && count < 4 && checkbox[3]) {
				returnData.push({
					lng: allCells[i].longitude,
					lat: allCells[i].latitude,
					count: count,
					dir: allCells[i].dir-30,
					cell: allCells[i].ecgi,
					band: allCells[i].band,
					master: false
				});
			} else if (count >= 4 && count < 5 && checkbox[4]) {
				returnData.push({
					lng: allCells[i].longitude,
					lat: allCells[i].latitude,
					count: count,
					dir: allCells[i].dir-30,
					cell: allCells[i].ecgi,
					band: allCells[i].band,
					master: false
				});
			} else if (count >= 5 && checkbox[5]) {
				returnData.push({
					lng: allCells[i].longitude,
					lat: allCells[i].latitude,
					count: count,
					dir: allCells[i].dir-30,
					cell: allCells[i].ecgi,
					band: allCells[i].band,
					master: false
				});
			}
		}
		var layer = new Mapv.Layer({
			mapv: mapv.mapv, // 对应的mapv实例
			zIndex: 1, // 图层层级
			dataType: "point", // 数据类型，点类型
			data: returnData, // 数据
			drawType: "choropleth", // 展示形式
			dataRangeControl: false,
			drawOptions: {// 绘制参数
				size: 20, // 网格大小
				unit: "px", // 单位
				//strokeStyle: 'gray', // 描边颜色
				type: "siteband",
				splitList: [
					{
						start: 0,
						end: 1,
						color: "blue"
					}, {
						start: 1,
						end: 2,
						color: "green"
					}, {
						start: 2,
						end: 3,
						color: "lime"
					}, {
						start: 3,
						end: 4,
						color: "yellow"
					}, {
						start: 4,
						end: 5,
						color: "magenta"
					}, {
						start: 5,
						color: "red"
					}
				]
						/*events: {
						 mousemove: function (e, data) {
						 $("#leftControl").children().remove();
						 var li = '';
						 for (var i = 0; i < data.length; i++) {
						 li  += ("<li " + 'class="list-group-item"' + ">" + data[i].cell + "</li>");
						 }
						 $("#leftControl").append(li);
						 },
						 click: function(e, data) {
						 getChartsData(data);
						 }
						 }*/
			}
		});
	});
}
function setPointByCell() {
	var cell = $("#cell").val();
	var city = $("#last_city").val();
	var date = $("#last_date").val();
	var channel = $("#last_channel").val();
	if (city == "" || date == "" || channel == "") {
		layer.open({
			title: "提示",
			content: "请先进行查询"
		});
		return;
	}
	if (cell == "") {
		layer.open({
			title: "提示",
			content: "请输入小区"
		});
		return;
	}
	var params = {
		city: city,
		date: date,
		cell: cell,
		channel: channel
	};
	$.post("overlapCoverPoint/getCell", params, function (data) {
		data = JSON.parse(data);
		//console.log(data);
		if (data == false) {
			layer.open({
				title: "提示",
				content: "所查询的数据中并没有该小区"
			});
			return;
		}
		var point = new BMap.Point(data.longitude, data.latitude);
		mapv.bmap.centerAndZoom(point, 18);
	});
}