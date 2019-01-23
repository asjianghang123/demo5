$(function () {
	toogle("CoverageQuery");
	checkType();
	initCitys();
	//setDate();
	setHour();
});
function checkType() {
	$("#regionType").on("change", function () {
		var regionType = $(this).val();
		if (regionType == "city") {
			$("#baseStation").attr("disabled", "disabled");
			$("#groupEcgi").attr("disabled", "disabled");
		} else if (regionType == "baseStation") {
			$("#baseStation").removeAttr("disabled");
			$("#groupEcgi").attr("disabled", "disabled");
		} else if (regionType == "groupEcgi") {
			$("#groupEcgi").removeAttr("disabled");
			$("#baseStation").attr("disabled", "disabled");
		}
	});
	$("#timeType").on("change", function () {
		var timeType = $(this).val();
		if (timeType == "day") {
			$("#hour").multiselect("disable");
		} else {
			$("#hour").multiselect("enable");
		}
	});
}
function initCitys() {
	$("#city").multiselect({
		//dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "选择城市",
		//filterPlaceholder:'搜索',
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		//allSelectedText:'已选中所有城市',
		maxHeight: 200,
		maxWidth: "100%"
	});
	$.get("CoverageQuery/getCitys", null, function (data) {
		data = JSON.parse(data);
		var newData = [];
		for (var i in data) {
			var CHCity = data[i].split("-")[0];
			var dataBase = data[i].split("-")[1];
			newData.push({"label": CHCity, "value": dataBase});
		}
		$("#city").multiselect("dataprovider", newData);
		setDate();
	});
}
function setDate() {
	$("#date").datepicker({format: "yyyy-mm-dd"});  //返回日期
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;
	$("#date").datepicker("setValue", today);

	var params = {
		dataBase: $("#city").val()
	};
	$.post("CoverageQuery/getCityDate", params, function (data) {
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
	$("#city").change(function () {
		var city = $("#city").val();
		var params = {
			dataBase: city
		};
		$.post("CoverageQuery/getCityDate", params, function (data) {
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
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");
}
function setHour() {
	$("#hour").multiselect({
		//dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "选择小时",
		//filterPlaceholder:'搜索',
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选全天",
		maxHeight: 200,
		maxWidth: "100%"
	});
	var hours = [];
	for (var i = 0; i < 24; i++) {
		if (i < 10) {
			hours.push({"label": "0" + i, "value": "0" + i});
		} else {
			hours.push({"label": i, "value": i});
		}

	}
	$("#hour").multiselect("dataprovider", hours);
	$("#hour").multiselect("disable");
}
function query() {
	var params = getParams();
	if (!params) {
		return;
	}
	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	queryBtn.start();
	exportBtn.start();

	var fieldArr = [];
	fieldArr[fieldArr.length] = {field: "date", title: "date", width: 200};
	if (params.timeType == "hour") {
		fieldArr[fieldArr.length] = {field: "hourId", title: "hourId", width: 100};
	}
	fieldArr[fieldArr.length] = {field: "city", title: "city", width: 100};
	if (params.regionType == "baseStation") {
		fieldArr[fieldArr.length] = {field: "userLabel", title: "userLabel", width: 200};
	} else if (params.regionType == "groupEcgi") {
		fieldArr[fieldArr.length] = {field: "userLabel", title: "userLabel", width: 200};
		fieldArr[fieldArr.length] = {field: "ecgi", title: "ecgi", width: 200};
	}
	for (var i = 1; i <= 48; i++) {
		fieldArr[fieldArr.length] = {field: "RSRP" + i, title: "RSRP" + i, width: 150};
	}
	var fieldCol = new Array(fieldArr);

	$.get("CoverageQuery/getTableData", params, function (data) {
		queryBtn.stop();
		exportBtn.stop();
		data = JSON.parse(data);
		if (!data.records) {
			//alert("没有数据");
			layer.open({
				title: "提示",
				content: "没有数据"
			});
			return;
		}
		$("#coverageTable").grid("destroy", true, true);
		var grid = $("#coverageTable").grid({
			columns: fieldArr,
			dataSource: data.records,
			params: params,
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap",
			primaryKey: "id",
			autoLoad: true
		});
	});
}
function getParams() {
	var regionType, city, baseStation, groupEcgi, timeType, date, hour;
	regionType = $("#regionType").val();
	if (!$("#city").val()) {
		//alert("请选择城市");
		layer.open({
			title: "提示",
			content: "请选择城市"
		});
		return "";
	}
	city = $("#city").val();
	if (regionType == "city") {
		baseStation = "";
		groupEcgi = "";
	} else if (regionType == "baseStation") {
		if ($("#baseStation").val()) {
			baseStation = $("#baseStation").val();
		}
	} else if (regionType == "groupEcgi") {
		if ($("#groupEcgi").val()) {
			groupEcgi = $("#groupEcgi").val();
		}
	}
	timeType = $("#timeType").val();
	date = $("#date").val();
	if (timeType == "day") {
		hour = "";
	} else {
		if ($("#hour").val()) {
			hour = $("#hour").val().join(",");
		}
	}
	var params = {
		regionType: regionType,
		citys: city,
		baseStation: baseStation,
		groupEcgi: groupEcgi,
		timeType: timeType,
		date: date,
		hour: hour
	};
	return params;
}
function exportFile() {
	var params = getParams();
	if (!params) {
		return;
	}
	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	queryBtn.start();
	exportBtn.start();

	$.post("CoverageQuery/exportFile", params, function (data) {
		queryBtn.stop();
		exportBtn.stop();
		data = JSON.parse(data);
		if (data.result == "true") {
			var filepath = data.filename.replace("\\", "");
			download(filepath, "", "data:text/csv;charset=utf-8");
		} else {
			//alert("There is error occured!");
			layer.open({
				title: "提示",
				content: "下载失败"
			});
		}
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