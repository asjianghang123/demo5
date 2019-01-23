$(function () {
	toogle("areaCoverage");
	getAllCity();
	setTime();
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
	$.get("areaCoverage/areaCoverageDate", params, function (data) {
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
		$.get("areaCoverage/areaCoverageDate", params, function (data) {
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
function getAllCity() {
	$("#city").multiselect({
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
	var url = "areaCoverage/areaCoverageCity";
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
			$("#city").multiselect("dataprovider", newOptions);
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
	// var exportBtn = Ladda.create( document.getElementById( 'exportBtn' ) );
	queryBtn.start();
	// exportBtn.start();

	$.get("areaCoverage/areaCoverageDataHeader", params, function (data) {
		if (data.error == "error") {
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
			queryBtn.stop();
			// exportBtn.stop();
			return;
		}
		var fieldArr = [];
		for (var k in data) {
			if (k == "id") {
				fieldArr[fieldArr.length] = {field: k, title: k, width: 50};
			} else {
				fieldArr[fieldArr.length] = {field: k, title: k, width: textWidth(k)};
			}
		}
		$("#areaCoverageTable").grid("destroy", true, true);
		var grid = $("#areaCoverageTable").grid({
			columns: fieldArr,
			params: params,
			dataSource: {
				url: "areaCoverage/areaCoverageData",
				success: function (data) {
					data = eval("(" + data + ")");
					if (data.error == "error") {
						$("#areaCoverageTable").grid("destroy", true, true);
						// alert("数据不存在，请重新选择！");
						layer.open({
							title: "提示",
							content: "数据不存在，请重新选择！"
						});
						queryBtn.stop();
						// exportBtn.stop();
						return;
					}
					grid.render(data);

					queryBtn.stop();
					// exportBtn.stop();
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

	// var queryBtn = Ladda.create( document.getElementById( 'queryBtn' ) );
	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	// queryBtn.start();
	exportBtn.start();
	var url = "areaCoverage/allAreaCoverageData";
	$.get(url, params, function (data) {
		data = eval("(" + data + ")");
		if (data.error == "error") {
			$("#areaCoverageTable").grid("destroy", true, true);
			// alert("数据不存在，请重新选择！");
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
			// queryBtn.stop();
			exportBtn.stop();
			return;
		}
		if (data.result == "true") {
			var filepath = data.filename.replace("\\", " ''");
			download(filepath, "", "data:text/csv;charset=utf-8");
		} else {
			// alert("数据不存在，请重新选择！");
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
		}
		// queryBtn.stop();
		exportBtn.stop();
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