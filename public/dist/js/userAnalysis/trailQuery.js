var map;
$(function () {
	toogle("trailQuery");
	initCitys();
	//setTime();
});

function initCitys() {
	$("#citys").multiselect({
		//dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "选择城市",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有城市",
		maxHeight: 200,
		maxWidth: "100%"
	});
	$.get("trailQuery/getCitys", null, function (data) {
		data = JSON.parse(data);
		var newData = [];
		for (var i in data) {
			var CHCity = data[i].split("-")[0];
			var dataBase = data[i].split("-")[1];
			newData.push({"label": CHCity, "value": dataBase});
		}
		$("#citys").multiselect("dataprovider", newData);
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
	//$("#date").datepicker("setValue", nowTemp);

	var params = {
		dataBase: $("#citys").val()
	};
	$.post("trailQuery/getDataGroupByDate", params, function (data) {
		data = JSON.parse(data);
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
		var city = $("#citys").val();
		var params = {
			dataBase: city
		};
		$.post("trailQuery/getDataGroupByDate", params, function (data) {
			data = JSON.parse(data);
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
			return date.valueOf() < now.valueOf() ? '' : '';
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");
}

function queryTrail() {
	var city = $("#citys").val();
	var date = $("#date").val();
	var user = $("#user_query").val();
	if (!user) {
		//alert("请输入需要查询的用户信息！");
		layer.open({
			title: "提示",
			content: "请输入需要查询的用户信息！"
		});
		return;
	}

	var params = {
		dataBase: city,
		date: date,
		user: user
	};
	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	queryBtn.start();
	$.post("trailQuery/getTrailData", params, function (data) {
		data = JSON.parse(data);
		if (data.length > 0) {
			setMap(data);
		} else {
			//alert("没有轨迹数据！");
			layer.open({
				title: "提示",
				content: "没有轨迹数据！"
			});
		}
		queryBtn.stop();

	});
}

function setMap(data) {

	//console.log(data);
	// 百度地图API功能
	map = new BMap.Map("map1");
	map.centerAndZoom(new BMap.Point(data[0].longitude, data[0].latitude), 16);
	map.enableScrollWheelZoom();

	var points = [];
	for (var i in data) {
		points.push(new BMap.Point(data[i].longitude, data[i].latitude));
	}
	var polyline = new BMap.Polyline(points, {strokeColor: "#110E91", strokeWeight: 2});   //创建折线
	map.addOverlay(polyline);   //增加折线
	addArrow(polyline, 8, 20);
	map.addEventListener("zoomend", function () {
		map.clearOverlays();
		map.addOverlay(polyline);
		addArrow(polyline, 8, 20);
	});

}
function addArrow(polyline, length, angleValue) { //绘制箭头的函数
	var linePoint = polyline.getPath();//线的坐标串
	var arrowCount = linePoint.length;
	for (var i = 1; i < arrowCount; i++) { //在拐点处绘制箭头
		var pixelStart = map.pointToPixel(linePoint[i - 1]);
		var pixelEnd = map.pointToPixel(linePoint[i]);
		//var angle=angleValue;//箭头和主线的夹角
		var angle = angleValue * Math.PI / 180;
		var r = length; // r/Math.sin(angle)代表箭头长度
		var delta = 0; //主线斜率，垂直时无斜率
		var param = 0; //代码简洁考虑
		var pixelTemX, pixelTemY;//临时点坐标
		var pixelX, pixelY, pixelX1, pixelY1;//箭头两个点
		if (pixelEnd.x - pixelStart.x == 0) { //斜率不存在是时
			pixelTemX = pixelEnd.x;
			if (pixelEnd.y > pixelStart.y) {
				pixelTemY = pixelEnd.y - r;
			} else {
				pixelTemY = pixelEnd.y + r;
			}
			//已知直角三角形两个点坐标及其中一个角，求另外一个点坐标算法
			pixelX = pixelTemX - r * Math.tan(angle);
			pixelX1 = pixelTemX + r * Math.tan(angle);
			pixelY = pixelY1 = pixelTemY;
		} else {  //斜率存在时
			delta = (pixelEnd.y - pixelStart.y) / (pixelEnd.x - pixelStart.x);
			param = Math.sqrt(delta * delta + 1);

			if ((pixelEnd.x - pixelStart.x) < 0) { //第二、三象限
				pixelTemX = pixelEnd.x + r / param;
				pixelTemY = pixelEnd.y + delta * r / param;
			} else {//第一、四象限
				pixelTemX = pixelEnd.x - r / param;
				pixelTemY = pixelEnd.y - delta * r / param;
			}
			//已知直角三角形两个点坐标及其中一个角，求另外一个点坐标算法
			pixelX = pixelTemX + Math.tan(angle) * r * delta / param;
			pixelY = pixelTemY - Math.tan(angle) * r / param;

			pixelX1 = pixelTemX - Math.tan(angle) * r * delta / param;
			pixelY1 = pixelTemY + Math.tan(angle) * r / param;
		}

		var pointArrow = map.pixelToPoint(new BMap.Pixel(pixelX, pixelY));
		var pointArrow1 = map.pixelToPoint(new BMap.Pixel(pixelX1, pixelY1));
		var Arrow = new BMap.Polyline([
			pointArrow,
			linePoint[i],
			pointArrow1
		], {strokeColor: "#110E91", strokeWeight: 2});
		map.addOverlay(Arrow);
	}
}
