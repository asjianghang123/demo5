var num;
var queryBtn;
$(function () {
	toogle("marketAnalysis");
	initCitys();
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
	$.get("marketAnalysis/getCitys", null, function (data) {
		data = JSON.parse(data);
		var newData = [];
		for (var i in data) {
			var CHCity = data[i].split("-")[0];
			var dataBase = data[i].split("-")[1];
			newData.push({"label": CHCity, "value": dataBase});
		}
		$("#citys").multiselect("dataprovider", newData);
	});
}
function query() {
	var city = $("#citys").val();
	queryBtn = Ladda.create(document.getElementById("queryBtn"));
	queryBtn.start();
	num = 0;
	getBrandTable(city);
	getModeTable(city);
	getBrandChartData(city);
	getModeChartData(city);
}
function getBrandTable(city) {
	var fieldArr = [];
	var text = "rank,brandName,users";
	var textArr = text.split(",");
	for (var i in textArr) {
		if (textArr[i] == "rank") {
			fieldArr[fieldArr.length] = {field: textArr[i], title: textArr[i], width: 50};
		} else {
			fieldArr[fieldArr.length] = {field: textArr[i], title: textArr[i], width: 200};
		}
	}
	$("#brandTable").grid("destroy", true, true);
	var grid = $("#brandTable").grid({
		columns: fieldArr,
		dataSource: {
			url: "marketAnalysis/getBrandData",
			success: function (data) {
				data = JSON.parse(data);
				grid.render(data);
				if (++num == 4) {
					queryBtn.stop();
				}
			}
		},
		params: {"city": city},
		pager: {limit: 20, sizes: [10, 20, 50, 100]},
		autoScroll: true,
		uiLibrary: "bootstrap",
		autoLoad: true
	});
}
function getModeTable(city) {
	var fieldArr = [];
	var text = "rank,modelName,users";
	var textArr = text.split(",");
	for (var i in textArr) {
		if (textArr[i] == "rank") {
			fieldArr[fieldArr.length] = {field: textArr[i], title: textArr[i], width: 50};
		} else {
			fieldArr[fieldArr.length] = {field: textArr[i], title: textArr[i], width: 200};
		}
	}
	$("#modeTable").grid("destroy", true, true);
	var grid = $("#modeTable").grid({
		columns: fieldArr,
		dataSource: {
			url: "marketAnalysis/getModeData",
			success: function (data) {
				data = JSON.parse(data);
				grid.render(data);
				if (++num == 4) {
					queryBtn.stop();
				}
			}
		},
		params: {"city": city},
		pager: {limit: 20, sizes: [10, 20, 50, 100]},
		autoScroll: true,
		uiLibrary: "bootstrap",
		autoLoad: true
	});
}
function brandExport() {
	var city = $("#citys").val();
	var brandExportBtn = Ladda.create(document.getElementById("brandExportBtn"));
	brandExportBtn.start();

	var url = "marketAnalysis/getAllBrandData";
	$.get(url, {"city": city}, function (data) {
		data = JSON.parse(data);
		if (data.result == "true") {
			var filepath = data.filename.replace("\\", "");
			download(filepath, "", "data:text/csv;charset=utf-8");
		} else {
			//alert("There is error occured!");
			layer.open({
				title: "提示",
				content: "下载失败！"
			});
		}
		brandExportBtn.stop();
	});
}
function modeExport() {
	var city = $("#citys").val();
	var modeExportBtn = Ladda.create(document.getElementById("modeExportBtn"));
	modeExportBtn.start();

	var url = "marketAnalysis/getAllModeData";
	$.get(url, {"city": city}, function (data) {
		data = JSON.parse(data);
		if (data.result == "true") {
			var filepath = data.filename.replace("\\", "");
			download(filepath, "", "data:text/csv;charset=utf-8");
		} else {
			//alert("There is error occured!");
			layer.open({
				title: "提示",
				content: "下载失败！"
			});
		}
		modeExportBtn.stop();
	});
}


function download(url) {
	var browerInfo = getBrowerInfo();
	if (browerInfo == "chrome") {
		download_chrome(url);
	} else if (browerInfo == "firefox") {
		download_firefox(url);
	}
}

function download_chrome(url) {
	var aLink = document.createElement("a");
	aLink.href = url;
	aLink.download = url;
	/*var evt = document.createEvent("HTMLEvents");
	 evt.initEvent("click", false, false);
	 aLink.dispatchEvent(evt);*/
	document.body.appendChild(aLink);
	aLink.click();
}

function download_firefox(url) {
	window.open(url);
}
function getBrowerInfo() {
	var uerAgent = navigator.userAgent.toLowerCase();
	var format = /(msie|firefox|chrome|opera|version).*?([\d.]+)/;
	var matches = uerAgent.match(format);
	return matches[1].replace(/version/, "'safari");
}

function getBrandChartData(city) {
	$.get("marketAnalysis/getBrandChartData", {"city": city}, function (data) {

		data = JSON.parse(data);
		$("#brandChart").highcharts({
			exporting: {
				enabled: true,
			},
			chart: {
				type: "column"
			},
			title: {
				text: "品牌排名"
			},
			xAxis: {
				categories: data.categories,
				crosshair: true
			},
			yAxis: {
				title: {
					text: "用户数"
				}
			},
			tooltip: {
				headerFormat: '<span style="font-size:10px">{point.key}</span><table style="font-size:10px;white-space:nowrap">',
				pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
				'<td style="padding:0"><b>{point.y}</b></td></tr>',
				footerFormat: "</table>",
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
			series: [{
				name: "用户数",
				data: data.series
			}]
		});
		if (++num == 4) {
			queryBtn.stop();
		}
	});
}
function getModeChartData(city) {
	$.get("marketAnalysis/getModeChartData", {"city": city}, function (data) {

		data = JSON.parse(data);
		$("#modeChart").highcharts({
			exporting: {
				enabled: true,
			},
			chart: {
				type: "column"
			},
			title: {
				text: "型号排名"
			},
			xAxis: {
				categories: data.categories,
				crosshair: true
			},
			yAxis: {
				title: {
					text: "用户数"
				}
			},
			tooltip: {
				headerFormat: '<span style="font-size:10px">{point.key}</span><table style="font-size:10px;white-space:nowrap">',
				pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
				'<td style="padding:0"><b>{point.y}</b></td></tr>',
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
			series: [{
				name: "用户数",
				data: data.series
			}]
		});
		if (++num == 4) {
			queryBtn.stop();
		}
	});
}

function switchTab(div1, div2) {
	$(div2).removeClass("active");
	$(div1).addClass("active");
}