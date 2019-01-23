$(document).ready(function () {
	toogle("relationBadHandover");
	initCitys();

	$("#date").datepicker({format: "yyyy-mm-dd"});  //返回日期
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;

	// console.log(today);
	var params = {
		city: getFirstCity()
	};
	$.post("relationBadHandover/allDate", params, function (data) {
		var sdata = [];
		for (var i = 0; i < data.length; i++) {
			if (data[i] === today) {
				continue;
			}
			sdata.push(data[i]);
		}
		sdata.push(today);
		$("#date").datepicker("setValues", sdata);
	})
	$("#city").change(function () {
		var city = $("#city").val();
		var params = {
			city: city
		};
		$.post("relationBadHandover/allDate", params, function (data) {
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
	var checkin = $("#dateTime").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? '' : '';
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");


	// $("#date").datepicker({format: 'yyyy-mm-dd'});
	// var nowTemp = new Date();
	// var year = nowTemp.getFullYear();
	// var month = nowTemp.getMonth()+1;
	// var day = nowTemp.getDate();
	// var today = year +'-'+month+'-'+day;
	// $("#date").datepicker('setValue', today);
	// var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	//   var checkin = $('#date').datepicker({
	//   onRender: function(date) {
	//     return date.valueOf() < now.valueOf() ? '' : '';
	//   }
	//   }).on('changeDate', function(ev) {
	//   checkin.hide();
	// }).data('datepicker');
});

function initCitys() {
	$("#citys").multiselect({
		//dropRight: true,
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

	var url = "relationBadHandover/getCitys";
	$.post(url, null, function (data) {
		data = eval("(" + data + ")");
		$("#citys").multiselect("dataprovider", data);
	});
}
function query() {
	var citys = $("#citys").val();
	var date = $("#date").val();
	var params = {
		city: citys,
		date: date
	};
	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	queryBtn.start();
	var url = "relationBadHandover/getDataHeader";
	$.post(url, null, function (data) {
		var text = eval("(" + data + ")").text;
		var textArr = text.split(",");
		var fieldArr = [];
		for (var i in textArr) {
			fieldArr[fieldArr.length] = {field: textArr[fieldArr.length], title: textArr[fieldArr.length], width: 200};
		}

		$("#resultTable").grid("destroy", true, true);
		var grid = $("#resultTable").grid({
			columns: fieldArr,
			dataSource: {
				url: "relationBadHandover/getTableData",
				success: function (data) {
					data = eval("(" + data + ")");
					grid.render(data);
					queryBtn.stop();
				},
				type:"post"
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

function exportFile() {
	var citys = $("#citys").val();
	var date = $("#date").val();
	var params = {
		city: citys,
		date: date
	};
	var exportBtn = Ladda.create(document.getElementById("exportBtn"));
	exportBtn.start();

	var url = "relationBadHandover/getAllTableData";
	$.post(url, params, function (data) {
		data = eval("(" + data + ")");
		if (data.result == "true") {
			fileSave(data.filename);
		} else {
			layer.open({
				title: "提示",
				content: "There is error occured!"
			});
		}
		exportBtn.stop();
	});
}
function fileSave(fileName) {
	if (fileName != "") {
		// alert(fileName);
		var fileNames = csvZipDownload(fileName);
		download(fileNames);
	}
	else {
		// alert('');
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

