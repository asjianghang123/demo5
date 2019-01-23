var map;
$(function(){
	toogle("locationAndRanging");
	map = setMap();
});

function setMap(){
	var bmap = new BMap.Map("map1");
	bmap.enableScrollWheelZoom(); // 启用滚轮放大缩小
	// 初始化地图,设置中心点坐标和地图级别
	var arr = setMapPoint();
	bmap.centerAndZoom(new BMap.Point(arr[0], arr[1]), 10);
	var mapv = new Mapv({
		drawTypeControl: false,
		map: bmap // 百度地图的map实例
	});
	return {"bmap":bmap,"mapv":mapv};
}
function ranging(){
	var myDis = new BMapLib.DistanceTool(map.bmap);
	myDis.open();
}
function setMapPoint1(){
	var longitude = $("#addr").val().split(",")[0];
	var latitude = $("#addr").val().split(",")[1];

	if(!longitude&&!latitude){
		//alert("请使用英文逗号来间隔经纬度");
		layer.open({
			title: "提示",
			content: "请使用英文逗号来间隔经纬度"
		});
		return;
	}
	if(isNaN(longitude)||longitude<-180||longitude>180){
		//alert("经度输入不正确");
		layer.open({
			title: "提示",
			content: "经度输入不正确"
		});
		return;
	}
	if(isNaN(latitude)||latitude<-90||latitude>90){
		//alert("纬度输入不正确");
		layer.open({
			title: "提示",
			content: "纬度输入不正确"
		});
		return;
	}
	var point = new BMap.Point(longitude, latitude);
	map.bmap.centerAndZoom(point, 11);
	var marker = new BMap.Marker(point);
	map.bmap.addOverlay(marker);
}
function setPointByCell(){
	var cell = $("#cell").val();
	var url = "locationAndRanging/getCoordinateByCell";
	$.post(url,{cell:cell},function(data){
		data = JSON.parse(data);
		if(data == false){
			layer.open({
				title: "提示",
				content: "没有查到该小区的坐标，请检查是否正确输入"
			});
			return;
		}
		console.log(data);
		drawMap([data]);
	});
}
function drawMap(returnData){
	returnData[0].dir = returnData[0].dir -30;
	var point = new BMap.Point(returnData[0].lng, returnData[0].lat);
	map.bmap.centerAndZoom(point, 10);
	var layer = new Mapv.Layer({
		mapv: map.mapv, // 对应的mapv实例
		zIndex: 1, // 图层层级
		dataType: "point", // 数据类型，点类型
		data: returnData, // 数据
		drawType: "choropleth", // 展示形式
		dataRangeControl: false ,
		drawOptions: { // 绘制参数
			size: 20, // 点大小
			unit: "px", // 单位
			type:"siteband",
			// splitList数值表示按数值区间来展示不同颜色的点
			splitList: [
				{
					end: 100,
					color: "blue"
				}
			],
			label: { // 是否显示count值
				show: false // 是否显示，默认不显示 
			},
			events: {
				click: function(e, data) {
					console.log(data);
				},
				// mousemove: function(e, data) {
				//     console.log("move",e, data)
				// }
			}
		}
	});
}