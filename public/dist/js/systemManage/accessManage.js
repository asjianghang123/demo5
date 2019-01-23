$(function () {
	toogle("accessManage");
	setTime();

	$("#users").multiselect({
		buttonWidth: "100%",
		enableFiltering: true,
		nonSelectedText: "请选择用户",
		filterPlaceholder: "搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有用户",
		maxHeight: 200,
		maxWidth: "100%"
	});

	getAllUsers();
});

function getAllUsers() {
	$.get("accessManage/getAllUsers", {}, function (data) {
		var newOptions = [];
		var obj = {};
		$(data).each(function (k, v) {
			// v = eval("(" + v + ")");
			obj = {
				label: v.user,
				value: v.user,
				selected: true
			};
			newOptions.push(obj);
		});
		$("#users").multiselect("dataprovider", newOptions);
	});
}
function setTime() {
	$("#startDate").datepicker({format: "yyyy-mm-dd"});
	$("#endDate").datepicker({format: "yyyy-mm-dd"}); //返回日期
	var nowTemp = new Date();
	$("#startDate").datepicker("setValue", nowTemp);
	$("#endDate").datepicker("setValue", nowTemp);
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#startDate").datepicker().on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");
	var checkout = $("#endDate").datepicker().on("changeDate", function (ev) {
		checkout.hide();
	}).data("datepicker");
}
function query() {
	var startDate = $("#startDate").val();
	var endDate = $("#endDate").val();
	var users = $("#users").val();
	if (startDate > endDate) {
		//alert("结束日期不能早于起始日期");
		layer.open({
			title: "提示",
			content: "结束日期不能早于起始日期"
		});
		return;
	}
	var params = {
		startDate: startDate,
		endDate: endDate,
		users: users
	};
	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	queryBtn.start();
	exportBtn.start();
	var fieldArr = [];
	var field = ["url", "urlChinese", "sum"];
	for (var k in field) {
		fieldArr[fieldArr.length] = {field: field[k], title: field[k], width: 250};
	}


	$.post("accessManage/getAccessData", params, function (data) {
		data = JSON.parse(data);
		$("#accessTable").grid("destroy", true, true);
		var grid = $("#accessTable").grid({
			columns: fieldArr,
			dataSource: data.records,
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			uiLibrary: "bootstrap"
		});
		queryBtn.stop();
		exportBtn.stop();
	});
}

function exportFile() {

	var startDate = $("#startDate").val();
	var endDate = $("#endDate").val();
	var users = $("#users").val();
	if (startDate > endDate) {
		//alert("结束日期不能早于起始日期");
		layer.open({
			title: "提示",
			content: "结束日期不能早于起始日期"
		});
		return;
	}
	var params = {
		startDate: startDate,
		endDate: endDate,
		users: users
	};

	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	queryBtn.start();
	exportBtn.start();
	var url = "accessManage/downloadAccessData";
	$.post(url, params, function (data) {
		data = JSON.parse(data);
		if (data.result == "true") {
			var filepath = data.filename.replace("\\", "");
			download(filepath, "", "data:text/csv;charset=utf-8");
		} else {
			//alert("数据不存在，请重新选择！");
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
		}
		queryBtn.stop();
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