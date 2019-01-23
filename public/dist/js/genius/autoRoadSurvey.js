var mapv;
var layerin = null;
var allCells;
var cells;
var count = "1";
$(function () {
	toogle("autoRoadSurvey");
			initCitys();
	// setTime();

	mapv = initMap("map1");
	chooseCells();
});

function initCitys() {
	$("#citys").multiselect({
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
	$.get("autoRoadSurvey/getCitys", null, function (data) {
		data = JSON.parse(data);
		$("#citys").multiselect("dataprovider", data);
		setTime();
	});
}
function setTime() {
	$("#date").datepicker({format: "yyyy-mm-dd"});  //返回日期
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;

	var params = {
		city: $("#citys").val()
	};
	$.get("autoRoadSurvey/getDate", params, function (data) {
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
	$("#citys").change(function () {
		var city = $("#city").val();
		var params = {
			city: city
		};
		$.get("autoRoadSurvey/getDate", params, function (data) {
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

function getData() {
	var city = $("#citys").val();
	var date = $("#date").val();
	var hour = $("#hour").val();
	if (!city) {
		layer.open({
			title: "提示",
			content: "请选择要查询的城市"
		});
		return;
	}
	if (!date) {
		layer.open({
			title: "提示",
			content: "请选择要查询的日期"
		});
		return;
	}
	if (!hour) {
		layer.open({
			title: "提示",
			content: "请选择要查询的小时"
		});
		return;
	}
	initMapLeftControl();
	var params = {
		city: city,
		date: date,
		hour: hour
	};
	// $("#last_city").val(city);
	// $("#last_date").val(date);
	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	queryBtn.start();
	$(".zhaozi").show();
	$(".loadingImg").show();
	$.post("autoRoadSurvey/getData", params, function (data) {

		data = JSON.parse(data);
		if (data.length > 0) {
			drawMap1(data);
			allCells = data;
			$(".chooseCell").prop("checked", "checked");
			// cells = data;
		} else {
			layer.open({
				title: "提示",
				content: "没有数据！"
			});
			$("#map1_zhaozi").hide();
			$("#map1_loadingImg").hide();
		}
		queryBtn.stop();
	});
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
function drawMap1(data) {
	mapv.bmap.clearOverlays();
	//console.log(data);
	var returnData = []; // 取城市的点来做示例展示的点数据
	mapv.bmap.centerAndZoom(new BMap.Point(data[0].longtitude, data[0].latitude), 12);
	for (var i = 0; i < data.length; i++) {
		returnData.push({
			lng: data[i].longtitude,
			lat: data[i].latitude,
			count: 1,
			dir: "120",
			band: "D",
			cell: data[i].ecgi,
			master: false
		});
	}
	//console.log(data);
	var layer = new Mapv.Layer({
		mapv: mapv.mapv, // 对应的mapv实例
		zIndex: 1, // 图层层级
		dataType: "point", // 数据类型，点类型
		data: returnData, // 数据
		drawType: "choropleth", // 展示形式
		dataRangeControl: false,
		drawOptions: {// 绘制参数
			size: 20, // 点大小
			unit: "px", // 单位
			strokeStyle: "gray", // 描边颜色

			type: "siteband",
			// splitList数值表示按数值区间来展示不同颜色的点
			// fillStyle: 'rgba(255, 50, 50, 1)',
			// radius:5,
			splitList: [
				{
					end: -120,
					color: "red"
				}, {
					start: -120,
					end: -115,
					color: "orange"
				}, {
					start: -115,
					end: -110,
					color: "magenta"
				}, {
					start: -110,
					end: -105,
					color: "yellow"
				}, {
					start: -105,
					end: -95,
					color: "lime"
				}, {
					start: -95,
					end: -85,
					color: "green"
				}, {
					start: -85,
					color: "blue"
				}
			],
			events: {
				mousemove: function (e, data) {
					//console.log(data);
					$("#leftControl").children().remove();
					var li = "";
					for (var i = 0; i < data.length; i++) {
						li += ("<li " + 'class="list-group-item"' + ">" + data[i].cell + "</li>");
					}
					$("#leftControl").append(li);
				},
				click: function (e, data) {
					// setDetailTable(data);
				}
			}
		}
	});
	$("#map1_zhaozi").hide();
	$("#map1_loadingImg").hide();
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

			// var count = allCells[i].avgRsrp;
			if (count < -120 && checkbox[0]) {
				returnData.push({
					lng: allCells[i].longtitude,
					lat: allCells[i].latitude,
					count: count,
					// dir  : allCells[i].dir,
					// cell : allCells[i].ecgi,
					// band : allCells[i].band,
					master: false
				});
				// }else if(count >= 5 && count <10 && checkbox[1]){
			} else if (count >= -120 && count < -115 && checkbox[1]) {
				returnData.push({
					lng: allCells[i].longtitude,
					lat: allCells[i].latitude,
					count: count,
					// dir  : allCells[i].dir,
					// cell : allCells[i].ecgi,
					// band : allCells[i].band,
					master: false
				});
				// }else if(count >= 10 && count <15 && checkbox[2]){
			} else if (count >= -115 && count < -110 && checkbox[2]) {
				returnData.push({
					lng: allCells[i].longtitude,
					lat: allCells[i].latitude,
					count: count,
					// dir  : allCells[i].dir,
					// cell : allCells[i].ecgi,
					// band : allCells[i].band,
					master: false
				});
				// }else if(count >= 15 && count <20 && checkbox[3]){
			} else if (count >= -110 && count < -105 && checkbox[3]) {
				returnData.push({
					lng: allCells[i].longtitude,
					lat: allCells[i].latitude,
					count: count,
					// dir  : allCells[i].dir,
					// cell : allCells[i].ecgi,
					// band : allCells[i].band,
					master: false
				});
				// }else if(count >= 20 && checkbox[4]){
			} else if (count >= -105 && count < -95 && checkbox[4]) {
				returnData.push({
					lng: allCells[i].longtitude,
					lat: allCells[i].latitude,
					count: count,
					// dir  : allCells[i].dir,
					// cell : allCells[i].ecgi,
					// band : allCells[i].band,
					master: false
				});
			} else if (count >= -95 && count < -85 && checkbox[5]) {
				returnData.push({
					lng: allCells[i].longtitude,
					lat: allCells[i].latitude,
					count: count,
					// dir  : allCells[i].dir,
					cell: allCells[i].ecgi,
					// band : allCells[i].band,
					master: false
				});
			} else if (count >= -85 && checkbox[6]) {
				// console.log(allCells)
				returnData.push({
					lng: allCells[i].longtitude,
					lat: allCells[i].latitude,
					count: count,
					dir: 120,
					cell: allCells[i].ecgi,
					band: "D",
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
				strokeStyle: "gray", // 描边颜色
				type: "siteband",
				// fillStyle: 'rgba(255, 50, 50, 1)',
				// radius:5,
				splitList: [
					{
						end: -120,
						color: "red"
					}, {
						start: -120,
						end: -115,
						color: "orange"
					}, {
						start: -115,
						end: -110,
						color: "magenta"
					}, {
						start: -110,
						end: -105,
						color: "yellow"
					}, {
						start: -105,
						end: -95,
						color: "lime"
					}, {
						start: -95,
						end: -85,
						color: "green"
					}, {
						start: -85,
						color: "blue"
					}
				]
			}
		});
	});
}
function setPointByCell() {
	var cell = $("#cell").val();
	var city = $("#citys").val();

	var date = $("#date").val();
	var hour = $("#hour").val();

	// console.log(city)
	// console.log(date)
	// console.log(hour)

	// var channel = $("#last_channel").val();
	if (city == "" || date == "" || hour == "") {
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
		hour: hour
	};
	$.post("autoRoadSurvey/getOneCell", params, function (data) {
		data = JSON.parse(data);
		//console.log(data);
		if (data == false) {
			layer.open({
				title: "提示",
				content: "所查询的数据中并没有该小区"
			});
			return;
		}
		var point = new BMap.Point(data.longtitude, data.latitude);
		mapv.bmap.centerAndZoom(point, 18);
	});
}
