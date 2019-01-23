$(document).ready(function () {
	// setTime();
	getCitys();

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
	$.get("weakCoverRate/weakCoverRateDate", params, function (data) {
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
	$("#citys").change(function () {
		var city = $("#citys").val();
		var params = {
			city: city
		};
		$.get("weakCoverRate/weakCoverRateDate", params, function (data) {
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

	toogle("weakCoverRate");
});

function getCitys() {
	$("#citys").multiselect({
		dropRight: true,
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

	var url = "weakCoverRate/getCitys";
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
			$("#citys").multiselect("dataprovider", newOptions);
		}
	});
}

function query() {
	var citys = $("#citys").val();
	var datetime = $("#dateTime").val();
	var params = {
		dataBase: citys,
		date: datetime
	};

	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	queryBtn.start();
	var url = "weakCoverRate/getMroWeakCoverageDataHeader";
	$.get(url, params, function (data) {
		var fieldArr = [];
		for (var k in data) {
			if (k == "datetime_id") {
				fieldArr[fieldArr.length] = {field: k, title: k, width: 180};
			} else if (k == "ratio110") {
				fieldArr[fieldArr.length] = {field: k, title: k, width: 180, sortable: true};
			} else {
				fieldArr[fieldArr.length] = {field: k, title: k, width: 200};
			}

		}

		$("#resultTable").grid("destroy", true, true);
		var grid = $("#resultTable").grid({
			columns: fieldArr,
			dataSource: {
				url: "weakCoverRate/getMroWeakCoverageData",
				success: function (data) {
					data = eval("(" + data + ")");
					grid.render(data);
					queryBtn.stop();
				}
			},
			params: params,
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap",
			primaryKey: "id",
			autoLoad: true
		});
	});
}

function exportDataFile() {

	var citys = $("#citys").val();
	var datetime = $("#dateTime").val();

	var params = {
		dataBase: citys,
		date: datetime
	};

	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	exportBtn.start();

	var url = "weakCoverRate/getAllMroWeakCoverageData";
	$.get(url, params, function (data) {
		if (data["filename"] != "") {
			var filepath = data["filename"];
			download(filepath, "", "data:text/csv;charset=utf-8");
		} else {
			layer.open({
				title: "提示",
				content: "There is error occured!"
			});
		}
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
