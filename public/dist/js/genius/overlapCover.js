$(function () {
	toogle("overlapCover");
	getAllCity("#city");
	setTime();
});
function setTime() {
	$("#dateTime").datepicker({format: "yyyy-mm-dd"});  //返回日期
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;

	//console.log(today);
	var params = {
		city: getFirstCity()
	};
	$.get("overlapCover/overlapCoverDate", params, function (data) {
		var sdata = [];
		for (var i = 0; i < data.length; i++) {
			if (data[i] === today) {
				continue;
			}
			sdata.push(data[i]);
		}
		sdata.push(today);
		$("#dateTime").datepicker("setValues", sdata);
	});
	$("#city").change(function () {
		var city = $("#city").val();
		var params = {
			city: city
		};
		$.get("overlapCover/overlapCoverDate", params, function (data) {
			var sdata = [];
			for (var i = 0; i < data.length; i++) {
				if (data[i] === today) {
					continue;
				}
				sdata.push(data[i]);
			}
			sdata.push(today);
			$("#dateTime").datepicker("setValues", sdata);
		});
	});

	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#dateTime").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");
}
function setTime_Genius() {
	$("#dateTime_Genius").datepicker({format: "yyyy-mm-dd"});  //返回日期
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;

	//console.log(today);
	var params = {
		city: getFirstCity()
	};
	$.get("overlapCover/overlapCoverGeniusDate", params, function (data) {
		var sdata = [];
		for (var i = 0; i < data.length; i++) {
			if (data[i] === today) {
				continue;
			}
			sdata.push(data[i]);
		}
		sdata.push(today);
		$("#dateTime_Genius").datepicker("setValues", sdata);
	});
	$("#city_Genius").change(function () {
		var city = $("#city_Genius").val();
		var params = {
			city: city
		};
		$.get("overlapCover/overlapCoverGeniusDate", params, function (data) {
			var sdata = [];
			for (var i = 0; i < data.length; i++) {
				if (data[i] === today) {
					continue;
				}
				sdata.push(data[i]);
			}
			sdata.push(today);
			$("#dateTime_Genius").datepicker("setValues", sdata);
		});
	});

	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#dateTime_Genius").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");
}
function getAllCity(cityId) {
	$(cityId).multiselect({
		dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "请选择城市",
		//filterPlaceholder:'搜索',
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有平台类型",
		maxHeight: 200,
		width: 220
	});
	var url = "overlapCover/overlapCoverCity";
	$.ajax({
		type: "GET",
		url: url,
		dataType: "json",
		success: function (data) {
			var newOptions = [];
			var obj = {};
			$(data).each(function (k, v) {
				v = eval("(" + v + ")");
				obj = {
					label: v.text,
					value: v.value
				};
				newOptions.push(obj);
			});
			$(cityId).multiselect("dataprovider", newOptions);
		}
	});
}
function query() {
	var city = $("#city").val();
	var dateTime = $("#dateTime").val();
	var params = {
		dataBase: city,
		dateTime: dateTime
	};

	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	queryBtn.start();
	exportBtn.start();

	$.get("overlapCover/overlapCoverDataHeader", params, function (data) {
		if (data.error == "error") {
			// alert("数据不存在，请重新选择！");
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
			queryBtn.stop();
			exportBtn.stop();
			return;
		}
		var fieldArr = [];
		jQuery.i18n.properties({
			name: "overlapCover", //资源文件名称
			path: "common/i18n", //资源文件路径
			mode: "both", //用Map的方式使用资源文件中的值
			language: "zh",
			callback: function () {//加载成功后设置显示内容
				for (var k in data) {
					if (k == "datetime_id") {
						fieldArr[fieldArr.length] = {field: k, title: $.i18n.prop(k), width: 180};
					} else if (k == "rate" || k == "intensity") {
						fieldArr[fieldArr.length] = {field: k, title: $.i18n.prop(k), width: 180, sortable: true};
					} else {
						fieldArr[fieldArr.length] = {field: k, title: $.i18n.prop(k), width: textWidth($.i18n.prop(k))};
					}
				}
			}
		});
		$("#overlapCoverTable").grid("destroy", true, true);
		var grid = $("#overlapCoverTable").grid({
			columns: fieldArr,
			params: params,
			dataSource: {
				url: "overlapCover/overlapCoverData",
				success: function (data) {
					data = eval("(" + data + ")");
					if (data.error == "error") {
						$("#overlapCoverTable").grid("destroy", true, true);
						// alert("数据不存在，请重新选择！");
						layer.open({
							title: "提示",
							content: "数据不存在，请重新选择！"
						});
						queryBtn.stop();
						exportBtn.stop();
						return;
					}
					grid.render(data);

					queryBtn.stop();
					exportBtn.stop();
				}
			},
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap"
		});

	});
}
function query_Genius() {
	var city = $("#city_Genius").val();
	var dateTime = $("#dateTime_Genius").val();
	var params = {
		dataBase: city,
		dateTime: dateTime
	};

	var queryBtn_Genius = Ladda.create(document.getElementById("queryBtn_Genius"));
	var exportBtn_Genius = Ladda.create(document.getElementById("exportBtn_Genius"));
	queryBtn_Genius.start();
	exportBtn_Genius.start();

	$.get("overlapCover/overlapCoverGeniusDataHeader", params, function (data) {
		if (data.error == "error") {
			// alert("数据不存在，请重新选择！");
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
			queryBtn_Genius.stop();
			exportBtn_Genius.stop();
			return;
		}
		var fieldArr = [];
		jQuery.i18n.properties({
			name: "overlapCover", //资源文件名称
			path: "common/i18n", //资源文件路径
			mode: "both", //用Map的方式使用资源文件中的值
			language: "zh",
			callback: function () {//加载成功后设置显示内容
				for (var k in data) {
					if (k == "id") {
						fieldArr[fieldArr.length] = {field: k, title: $.i18n.prop(k), width: 45};
					} else if (k == "intensity") {
						fieldArr[fieldArr.length] = {field: k, title: $.i18n.prop(k), width: 180, sortable: true};
					} else {
						fieldArr[fieldArr.length] = {field: k, title: $.i18n.prop(k), width: textWidth($.i18n.prop(k))};
					}
				}
			}
		});
		$("#overlapCoverTable_Genius").grid("destroy", true, true);
		var grid = $("#overlapCoverTable_Genius").grid({
			columns: fieldArr,
			params: params,
			dataSource: {
				url: "overlapCover/overlapCoverGeniusData",
				success: function (data) {
					data = eval("(" + data + ")");
					if (data.error == "error") {
						$("#overlapCoverTable_Genius").grid("destroy", true, true);
						// alert("数据不存在，请重新选择！");
						layer.open({
							title: "提示",
							content: "数据不存在，请重新选择！"
						});
						queryBtn_Genius.stop();
						exportBtn_Genius.stop();
						return;
					}
					grid.render(data);

					queryBtn_Genius.stop();
					exportBtn_Genius.stop();
				}
			},
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap"
		});

	});
}
function textWidth(text) {
	var length = text.length;
	if (length > 15) {
		return length * 10;
	}
	return 150;
}

function exportFile() {

	var dataBase = $("#city").val();
	var dateTime = $("#dateTime").val();

	var params = {
		dataBase: dataBase,
		dateTime: dateTime
	};

	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	queryBtn.start();
	exportBtn.start();
	var url = "overlapCover/allOverlapCoverData";
	$.get(url, params, function (data) {
		data = eval("(" + data + ")");
		if (data.error == "error") {
			$("#overlapCoverTable").grid("destroy", true, true);
			// alert("数据不存在，请重新选择！");
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
			queryBtn.stop();
			exportBtn.stop();
			return;
		}
		if (data.result == "true") {
			var filepath = data.filename.replace("\\", "");
			download(filepath, "", "data:text/csv;charset=utf-8");
		} else {
			// alert("数据不存在，请重新选择！");
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
		}
		queryBtn.stop();
		exportBtn.stop();
	});
}
function exportFile_Genius() {

	var dataBase = $("#city_Genius").val();
	var dateTime = $("#dateTime_Genius").val();

	var params = {
		dataBase: dataBase,
		dateTime: dateTime
	};

	var queryBtn_Genius = Ladda.create(document.getElementById("queryBtn_Genius"));
	var exportBtn_Genius = Ladda.create(document.getElementById("exportBtn_Genius"));
	queryBtn_Genius.start();
	exportBtn_Genius.start();
	var url = "overlapCover/allOverlapCoverGeniusData";
	$.get(url, params, function (data) {
		data = eval("(" + data + ")");
		if (data.error == "error") {
			$("#overlapCoverTable_Genius").grid("destroy", true, true);
			// alert("数据不存在，请重新选择！");
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
			queryBtn_Genius.stop();
			exportBtn_Genius.stop();
			return;
		}
		if (data.result == "true") {
			var filepath = data.filename.replace("\\", "");
			download(filepath, "", "data:text/csv;charset=utf-8");
		} else {
			// alert("数据不存在，请重新选择！");
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
		}
		queryBtn_Genius.stop();
		exportBtn_Genius.stop();
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