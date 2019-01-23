$(function () {
	toogle("overlayRaster");
	getMap();
	// getMapTest();
});

function getMap() {
	// 自定义分辨率和瓦片坐标系
	var resolutions = [];
	var maxZoom = 18;
	// 计算百度使用的分辨率
	for (var i = 0; i <= maxZoom; i++) {
		resolutions[i] = Math.pow(2, maxZoom - i);
	}
	var tilegrid = new ol.tilegrid.TileGrid({
		origin: [0, 0], // 设置原点坐标
		resolutions: resolutions    // 设置分辨率
	});

	// 创建百度地图的数据源
	var baiduSource = new ol.source.TileImage({
		projection: "EPSG:3857",
		tileGrid: tilegrid,
		tileUrlFunction: function (tileCoord, pixelRatio, proj) {
			var z = tileCoord[0];
			var x = tileCoord[1];
			var y = tileCoord[2];

			// 百度瓦片服务url将负数使用M前缀来标识
			if (x < 0) {
				x = "M" + (-x);
			}
			if (y < 0) {
				y = "M" + (-y);
			}
			return "plugins/baidumapv2/tiles/" + z + "/" + x + "/" + y + ".png";
		}
	});
	// 百度地图层
	var baiduMapLayer = new ol.layer.Tile({
		source: baiduSource
	});
	var map = new ol.Map({
		// 设置地图图层
		layers: [
			// 创建一个使用Open Street Map地图源的瓦片图层
			new ol.layer.Tile({source: new ol.source.OSM()}),
			// baiduMapLayer,
			// 再加载geoserver
			new ol.layer.Tile({
				source: new ol.source.TileWMS({
					params: {
						"LAYERS": "cite:20171201224131",
						"VERSION": "1.1.0",
						"BBOX": [119.08, 31.09, 120.11950000000168, 32.03950000000154],
						"CRS": "EPSG:4326",
						"WIDTH": 768,
						"HEIGHT": 701
					},
					projection: "EPSG:4326",
					url: 'http://7.140.28.88:822/geoserver/cite/wms'
				})
			})
		],
		// 设置显示地图的视图
		view: new ol.View({
			center: [119.10, 31.095], // 定义地图显示中心于经度0度，纬度0度处
			zoom: 9, // 并且定义地图显示层级为2
			projection: "EPSG:4326"
		}),
		//    view: new ol.View({
		//   	center: ol.proj.transform(
		//       	[119.10, 31.095], 'EPSG:4326', 'EPSG:3857'),
		//   	zoom: 9
		// }),
		// 让id为map的div作为地图的容器
		target: "map"
	});
	var viewport = map.getViewport();
	var html = '<div id="legend" class="legend" style = "position: absolute; top: 10px; right: 10px">'+
					'<img src="http://7.140.28.88:822/geoserver/wms?REQUEST=GetLegendGraphic&VERSION=1.1.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=cite:20171201224131">'+
				'</div>';
	$(viewport).append(html);
}
function getMap1() {
	// 自定义分辨率和瓦片坐标系
	var resolutions = [];
	var maxZoom = 18;
	// 计算百度使用的分辨率
	for (var i = 0; i <= maxZoom; i++) {
		resolutions[i] = Math.pow(2, maxZoom - i);
	}
	var tilegrid = new ol.tilegrid.TileGrid({
		origin: [0, 0], // 设置原点坐标
		resolutions: resolutions    // 设置分辨率
	});

	// 创建百度地图的数据源
	var baiduSource = new ol.source.TileImage({
		projection: "EPSG:3857",
		tileGrid: tilegrid,
		tileUrlFunction: function (tileCoord, pixelRatio, proj) {
			var z = tileCoord[0];
			var x = tileCoord[1];
			var y = tileCoord[2];

			// 百度瓦片服务url将负数使用M前缀来标识
			if (x < 0) {
				x = "M" + (-x);
			}
			if (y < 0) {
				y = "M" + (-y);
			}
			return "plugins/baidumapv2/tiles/" + z + "/" + x + "/" + y + ".png";
		}
	});
	// 百度地图层
	var baiduMapLayer = new ol.layer.Tile({
		source: baiduSource
	});
	new ol.Map({
		// 设置地图图层
		layers: [
			// 创建一个使用Open Street Map地图源的瓦片图层
			// new ol.layer.Tile({source: new ol.source.OSM()}),
			baiduMapLayer,
			// 再加载geoserver
			new ol.layer.Tile({
				source: new ol.source.TileWMS({
					params: {
						"LAYERS": "cite:testData",
						"VERSION": "1.1.0",
						"BBOX": [-180.0, -90.0, 180.0, 90.0],
						"CRS": "EPSG:4326",
						"WIDTH": 768,
						"HEIGHT": 384
					},
					projection: "EPSG:4326",
					url: 'http://7.140.28.88:822/geoserver/cite/wms'
				})
			})
		],
		// 设置显示地图的视图
		// view: new ol.View({
		//   center: [0,0],    // 定义地图显示中心于经度0度，纬度0度处
		//   zoom: 9,            // 并且定义地图显示层级为2
		//   projection: 'EPSG:3857'
		// }),
		view: new ol.View({
			center: ol.proj.transform(
					[119.10, 31.095], "EPSG:4326", "EPSG:3857"),
			zoom: 10
		}),
		// 让id为map的div作为地图的容器
		target: "map"
	});
}
function getMapTest() {
	var osmSource = new ol.source.OSM();
	var map = new ol.Map({
		layers: [
			// 加载Open Street Map地图
			new ol.layer.Tile({
				source: osmSource
			}),
			// 添加一个显示Open Street Map地图瓦片网格的图层
			new ol.layer.Tile({
				source: new ol.source.TileDebug({
					projection: "EPSG:3857",
					tileGrid: osmSource.getTileGrid()
				})
			})
		],
		target: "map",
		view: new ol.View({
			center: ol.proj.transform(
					[119.10, 31.095], "EPSG:4326", "EPSG:3857"),
			zoom: 10
		})
	});
}
