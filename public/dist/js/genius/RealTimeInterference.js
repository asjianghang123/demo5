var mapv1;
$(function () {
	toogle("RealTimeInterference");
	getAllCity();
	getDateTime();
	mapv1 = initMap("mapPoint");
	setIntervalDate();
	collapse();
});
function getAllCity() {
	$("#citys").multiselect({
		//dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "请选择城市",
		//filterPlaceholder:'搜索',
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有城市",
		maxHeight: 200,
		maxWidth: "100%"
	});
	var url = "RealTimeInterference/getAllCity";
	$.ajax({
		type: "post",
		url: url,
		dataType: "json",
		success: function (data) {
			$("#citys").multiselect("dataprovider", data);
		}
	});
}
function getDateTime() {
	$("#dateSelect").multiselect({
		//dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "请选择时间",
		//filterPlaceholder:'搜索',
		//nSelectedText:'项被选中',
		//includeSelectAllOption:true,
		//selectAllText:'全选/取消全选',
		//allSelectedText:'已选中所有平台类型',
		maxHeight: 200,
		maxWidth: "100%"
	});
	var url = "RealTimeInterference/getDateTime";
	$.ajax({
		type: "post",
		url: url,
		dataType: "json",
		success: function (data) {
			$("#dateSelect").multiselect("dataprovider", data);
			var dateTime = $("#dateSelect").val();
			var params = {
				dateTime: dateTime,
				city: null
			};
			$("#search").click();
			//getRealTimeData(params);
		}
	});
}
function initMap(mapId) {
	var bmap = new BMap.Map(mapId);
	bmap.enableScrollWheelZoom(); // 启用滚轮放大缩小
	// 初始化地图,设置中心点坐标和地图级别
	var arr = setMapPoint();
	bmap.centerAndZoom(new BMap.Point(arr[0], arr[1]), 10);
	var mapv = new Mapv({
		drawTypeControl: false,
		map: bmap // 百度地图的map实例
	});

	//显示当前时间
	function TimeControl() {
		this.defaultAnchor = BMAP_ANCHOR_TOP_RIGHT;
		this.defaultOffset = new BMap.Size(10, 10);
	}
	TimeControl.prototype = new BMap.Control();
	//初始化控件
	TimeControl.prototype.initialize = function (map) {
		var ul = document.createElement("ul");
		ul.setAttribute("class", "list-group");
		var li = document.createElement("li");
		li.setAttribute("class", "list-group-item");
		li.setAttribute("id", "mapTime");
		ul.appendChild(li);
		//添加DOM元素到地图中
		map.getContainer().appendChild(ul);
		//返回DOM
		return ul;
	};
	//创建控件实例
	var timeCtrl = new TimeControl();
	//添加到地图当中
	bmap.addControl(timeCtrl);
	//自定义控件
	function TacControl() {
		this.defaultAnchor = BMAP_ANCHOR_TOP_LEFT;
		this.defaultOffset = new BMap.Size(10, 10);
	}
	//异常tac列表
	TacControl.prototype = new BMap.Control();
	//初始化控件
	TacControl.prototype.initialize = function (map) {
		var ul = document.createElement("ul");
		ul.setAttribute("class", "list-group");
		ul.setAttribute("id", "tacList");
		var li = document.createElement("li");
		li.setAttribute("class", "list-group-item");
		li.textContent = "受干扰tac列表";
		ul.appendChild(li);
		//添加DOM元素到地图中
		map.getContainer().appendChild(ul);
		//返回DOM
		return ul;
	};
	//创建控件实例
	var tacCtrl = new TacControl();
	//添加到地图当中
	bmap.addControl(tacCtrl);

	//图例
	function legendControl() {
		this.defaultAnchor = BMAP_ANCHOR_BOTTOM_RIGHT;
		this.defaultOffset = new BMap.Size(10, 10);
	}
	//继承Control的API
	legendControl.prototype = new BMap.Control();
	//初始化控件
	legendControl.prototype.initialize = function (map) {
		var _box = document.createElement("div");
		/*$("#search").click(function(){
		 _box.innerHTML = 
		 "<div class='box'>"+
		 "<div class='box-body' style='position: relative;'>"+
		 "<canvas id='legendCanvas' width='100' height='100' style='width: 100px; height: 100px; background: rgb(255, 255, 255);'></canvas>"+
		 "</div>"+
		 "</div>";
		 legendCanvas();
		 });*/
		_box.innerHTML =
				"<div class='box'>" +
				"<div class='box-body' style='position: relative;'>" +
				"<canvas id='legendCanvas' width='100' height='100' style='width: 100px; height: 100px; background: rgb(255, 255, 255);'></canvas>" +
				"</div>" +
				"</div>";

		map.getContainer().appendChild(_box);
		return _box;
	};
	//创建控件实例
	var legendCtr = new legendControl();
	//添加到地图当中
	bmap.addControl(legendCtr);
	return {"bmap": bmap, "mapv": mapv};
}
function query() {
	var dateTime = $("#dateSelect").val();
	var city = $("#citys").val();
	if (city) {
		city = city.join(",");
	}
	var params = {
		dateTime: dateTime,
		city: city
	};
	getRealTimeData(params);
}
function getRealTimeData(params) {
	var searchBtn = Ladda.create(document.getElementById("search"));
	searchBtn.start();
	var url = "RealTimeInterference/getRealTimeData";
	$.post(url, params, function (data) {
		searchBtn.stop();
		data = JSON.parse(data);
		mapv1 = initMap("mapPoint");
		legendCanvas();
		setMapTime();
		drawMap(data.mapData);
		setTable(data.tableData);
		setTacList(data.tac);
	});
}
function drawMap(returnData) {
	for (var i in returnData) {
		returnData[i].dir = returnData[i].dir-30;
	}
	var layer = new Mapv.Layer({
		mapv: mapv1.mapv, // 对应的mapv实例
		zIndex: 1, // 图层层级
		dataType: "point", // 数据类型mapv，点类型
		data: returnData, // 数据
		drawType: "density", // 展示形式
		dataRangeControl: false,
		drawOptions: {// 绘制参数
			type: "rect", // 网格类型，方形网格或蜂窝形
			size: 4, // 网格大小
			unit: "px", // 单位
			opacity: "0.5",
			label: {// 是否显示文字标签
				show: true
			},
			splitList: [
				/*{
				 end: -110,
				 color: '#008000'
				 },{
				 start: -100,
				 color: '#ff0000'
				 }*/
				{
					end: 10,
					color: "#008000"
				}, {
					start: 10,
					end: 15,
					color: "#0000FF"
				}, {
					start: 15,
					end: 20,
					color: "#FFCC00"
				}, {
					start: 20,
					color: "#ff0000"
				}
			],
			events: {
				click: function (e, data) {
					// console.log(data);
				}
				// mousemove: function(e, data) {
				//     console.log('move',e, data)
				// }
			}
		}
	});
}
function openDetailModal(data) {
	//console.log(data)
	$("#detailData_modal").modal();
	var fieldArr = [];
	var text = "cell,count,lng,lat,dir,band,sf1,sf2,sf6,sf7";
	var textArr = text.split(",");
	for (var i in textArr) {
		fieldArr[i] = {field: textArr[i], title: textArr[i], width: 120};
	}
	$("#detailDataTable").grid("destroy", true, true);
	var grid = $("#detailDataTable").grid({
		columns: fieldArr,
		dataSource: data,
		pager: {limit: 10, sizes: [10, 20, 50, 100]},
		autoScroll: true,
		uiLibrary: "bootstrap",
		primaryKey: "cell",
		autoLoad: true
	});
}
function setIntervalDate() {
	window.setInterval(function () {
		getAllCity();
		getDateTime();
		// console.log(new Date());
	}, 300000);
}
function legendCanvas() {
	var legendCanvas = document.getElementById("legendCanvas");
	var context = legendCanvas.getContext("2d");
	context.textAlign = "left";
	//设置对象起始点和终点
	context.beginPath();
	context.arc(15.5, 15.5, 5, 0, 2 * Math.PI);
	context.closePath();
	context.fillStyle = "rgb(0, 128, 0)";
	context.fill();
	context.fillText("<10", 40.5, 18.5);

	//设置对象起始点和终点
	context.beginPath();
	context.arc(15.5, 35.5, 5, 0, 2 * Math.PI);
	context.closePath();
	context.fillStyle = "rgb(0, 0, 255)";
	context.fill();
	context.fillText("10-15", 40.5, 38.5);

	//设置对象起始点和终点
	context.beginPath();
	context.arc(15.5, 55.5, 5, 0, 2 * Math.PI);
	context.closePath();
	context.fillStyle = "rgb(255, 204, 0)";
	context.fill();
	context.fillText("15-20", 40.5, 58.5);

	//设置对象起始点和终点
	context.beginPath();
	context.arc(15.5, 75.5, 5, 0, 2 * Math.PI);
	context.closePath();
	context.fillStyle = "rgb(255, 0, 0)";
	context.fill();
	context.fillText(">20", 40.5, 78.5);
}
function collapse() {
	$("#collapseBtn").on("click", function () {
		var childrenClass = $(this).children().attr("class");
		// console.log(childrenClass);
		if (childrenClass == "fa fa-minus") {
			$("#mapPoint").css("height", "700px");
		} else {
			$("#mapPoint").css("height", "600px");
		}
	});
}
function setTable(data) {
	var fieldArr = [];
	var text = "date_id,city,site,cell,cell_state";
	var textArr = text.split(",");
	for (var i in textArr) {
		fieldArr[i] = {field: textArr[i], title: textArr[i], width: 120};
	}
	$("#interfere_connection_cell").grid("destroy", true, true);
	var grid = $("#interfere_connection_cell").grid({
		columns: fieldArr,
		dataSource: data,
		pager: {limit: 10, sizes: [10, 20, 50, 100]},
		autoScroll: true,
		uiLibrary: "bootstrap",
		primaryKey: "cell",
		autoLoad: true
	});
}
function setMapTime() {
	$("#mapTime").text($("#dateSelect").val());
}
function setTacList(data) {
	$("#tacList").empty();
	if (data.length > 0) {
		$("#tacList").css("overflow", "auto").css("height", "80%");
		$("#tacList").append("<li class='list-group-item' id='copyBtn' style='cursor:pointer'>" +
				"点击复制受干扰tac列表" +
				"</li>");
		var tacList = [];
		for (var i in data) {
			$("#tacList").append("<li class='list-group-item'>" + data[i].tac + " [" + data[i].tac_status + "]</li>");
			tacList.push(data[i].tac);
		}
		var clipboard = new Clipboard("#copyBtn", {
			text: function () {
				return tacList.join("\n");
			}
		});
		clipboard.on("success", function (e) {
			// console.log(e);
			layer.open({
				title: "提示",
				content: "已复制到剪切板"
			});
		});
		clipboard.on("error", function (e) {
			// console.log(e);
			layer.open({
				title: "提示",
				content: "复制失败，请重试"
			});
		});
	}
}
function copyTacList() {
	var errorTacList = document.getElementById("errorTacList");
	errorTacList.select(); // 选择对象
	var js = errorTacList.createTextRange();
	js.execCommand("Copy");
	alert("复制成功!");
}