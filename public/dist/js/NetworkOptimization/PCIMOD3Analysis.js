$(function () {

	toogle("PCIMOD3Analysis");
	getAllCity("#city");
	getAllCity("#city_Genius");
	setTime();
	setTime_Genius();
});
function setTime() {
	$("#dateTime").datepicker({format: "yyyy-mm-dd"});  //返回日期
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;

	console.log(today);
	var params = {
		city: getFirstCity()
	};
	$.post("PCIMOD3Analysis/PCIMOD3Date", params, function (data) {
		var sdata = [];
		for (var i = 0; i < data.length; i++) {
			if (data[i] === today) {
				continue;
			}
			sdata.push(data[i]);
		}
		sdata.push(today);
		//console.log(sdata);
		//alert(sdata);
		//$("#dateTime").datepicker("update", ");
		$("#dateTime").datepicker("setValues", sdata);

	});
	$("#city").change(function () {
		var city = $("#city").val();
		var params = {
			city: city
		};
		$.post("PCIMOD3Analysis/PCIMOD3Date", params, function (data) {
			var sdata = [];
			for (var i = 0; i < data.length; i++) {
				if (data[i] === today) {
					continue;
				}
				sdata.push(data[i]);
			}
			sdata.push(today);
			//console.log(sdata);
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

	console.log(today);
	var params = {
		city: getFirstCity()
	};
	$.post("PCIMOD3Analysis/PCIMOD3GeniusDate", params, function (data) {
		var sdata = [];
		//$("#dateTime_Genius").datepicker("update",");
		for (var i = 0; i < data.length; i++) {
			if (data[i] === today) {
				continue;
			}
			sdata.push(data[i]);
		}
		sdata.push(today);
		//console.log(sdata);
		//alert(sdata);
		//$("#dateTime_Genius").datepicker("hide");
		//$("#dateTime_Genius").datepicker("update", ["2016-11-11", "2016-11-12", "2016-11-13", "2016-11-14", "2016-11-15", "2016-11-16", "2016-11-17", "2016-11-18", "2016-11-19", "2016-11-20", "2016-10-09", "2016-10-10", "2016-10-13", "2016-10-14", "2016-10-15", "2016-10-16", "2016-10-17", "2016-10-18", "2016-10-19", "2016-10-20", "2016-10-21"]);
		$("#dateTime_Genius").datepicker("setValues", sdata);
		//alert(sdata);
	});
	$("#city_Genius").change(function () {
		var city = $("#city_Genius").val();
		var params = {
			city: city
		};
		$.post("PCIMOD3Analysis/PCIMOD3GeniusDate", params, function (data) {
			var sdata = [];
			//$("#dateTime_Genius").datepicker("hide");
			for (var i = 0; i < data.length; i++) {
				if (data[i] === today) {
					continue;
				}
				sdata.push(data[i]);
			}
			sdata.push(today);
			//console.log(sdata);
			$("#dateTime_Genius").datepicker("setValues", sdata);
		});
	});

	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#dateTime_Genius").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? '' : '';
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
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有平台类型",
		maxHeight: 200,
		width: 220
	});
	var url = "PCIMOD3Analysis/getAllCity";
	$.ajax({
		type: "post",
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

	$.post("PCIMOD3Analysis/getMroPCIMOD3DataHeader", params, function (data) {
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
		for (var k in data) {
			if (fieldArr.length == 0) {
				fieldArr[fieldArr.length] = {field: k, title: k, hidden: true};
			} else {
				if (k == "datetime_id") {
					fieldArr[fieldArr.length] = {field: k, title: k, width: 180};
				} else {
					fieldArr[fieldArr.length] = {field: k, title: k, width: textWidth(k)};
				}

			}
		}
		$("#PCIMOD3Table").grid("destroy", true, true);
		var grid = $("#PCIMOD3Table").grid({
			columns: fieldArr,
			params: params,
			dataSource: {
				url: "PCIMOD3Analysis/getMroPCIMOD3Data",
				success: function (data) {
					data = eval("(" + data + ")");
					if (data.error == "error") {
						$("#PCIMOD3Table").grid("destroy", true, true);
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
				},
				type:"post"
			},
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap"
		});

	});
}
function query_Genius() {
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

	$.post("PCIMOD3Analysis/getMroPCIMOD3GeniusDataHeader", params, function (data) {
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
		for (var k in data) {
			if (fieldArr.length == 0) {
				fieldArr[fieldArr.length] = {field: k, title: k, hidden: true};
			} else {
				if (k == "datetime_id") {
					fieldArr[fieldArr.length] = {field: k, title: k, width: 180};
				} else {
					fieldArr[fieldArr.length] = {field: k, title: k, width: textWidth(k)};
				}

			}
		}
		$("#PCIMOD3Table_Genius").grid("destroy", true, true);
		var grid = $("#PCIMOD3Table_Genius").grid({
			columns: fieldArr,
			params: params,
			dataSource: {
				url: "PCIMOD3Analysis/getMroPCIMOD3GeniusData",
				success: function (data) {
					data = eval("(" + data + ")");
					if (data.error == "error") {
						$("#PCIMOD3Table_Genius").grid("destroy", true, true);
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
				},
				type:"post"
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
	var url = "PCIMOD3Analysis/getAllMroPCIMOD3Data";
	$.post(url, params, function (data) {
		data = eval("(" + data + ")");
		if (data.error == "error") {
			$("#PCIMOD3Table").grid("destroy", true, true);
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
			fileSave(data.filename);
			// var filepath = data.filename.replace("\\",");
			// download(filepath,","data:text/csv;charset=utf-8");
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
	var url = "PCIMOD3Analysis/getAllMroPCIMOD3GeniusData";
	$.post(url, params, function (data) {
		data = eval("(" + data + ")");
		if (data.error == "error") {
			$("#PCIMOD3Table_Genius").grid("destroy", true, true);
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
			fileSave(data.filename);
			// var filepath = data.filename.replace("\\",");
			// download(filepath,","data:text/csv;charset=utf-8");
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
function fileSave(fileName) {
	if (fileName != "") {
		// alert(fileName);
		var fileNames = csvZipDownload(fileName);
		download(fileNames);
	}
	else {
		// alert(");
		layer.open({
			title: "提示",
			content: "No file generated so far!"
		});
	}
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