$(document).ready(function () {
	//查询条件 and 城市
	getLowAccessCitysOption();          //低接入小区城市
	getHighLostCitysOption();           //高掉线小区城市
	getBadHandoverCitysOption();        //切换差小区城市
	getHighInterferenceCitysOption();   //高干扰小区城市
	//查询日期
	setLowAccessTime();                 //低接入小区时间
	setHighLostTime();                  //高掉线小区时间
	setBadHandoverTime();               //切换差小区时间
	setHighInterferenceTime();          //高干扰小区时间
	//设置小时格式
	setHours();
	//设置平均PRB&任一PRBSELECT
	setCellType();
	//设置主轴与辅轴
	setPrincipalAndSecondaryOpticalAxis();

	toogle("historyCellSearch");
});

function setPrincipalAndSecondaryOpticalAxis() {
	$("#worstCellChartPrimaryAxisType").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		nonSelectedText: "请选择类别",
		nSelectedText: "项被选中",
		maxHeight: 200,
		maxWidth: "100%"
	});
	$("#worstCellChartAuxiliaryAxisType").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		nonSelectedText: "请选择类别",
		nSelectedText: "项被选中",
		maxHeight: 200,
		maxWidth: "100%"
	});
	var data_worstCellChartPrimaryAxisType = [
		{label: "无线接通率", value: "无线接通率"},
		{label: "RRC建立成功率", value: "RRC建立成功率"},
		{label: "ERAB建立成功率", value: "ERAB建立成功率"}
	];
	var data_worstCellChartAuxiliaryAxisType = [
		{label: "RRC建立请求次数", value: "RRC建立请求次数"},
		{label: "RRC建立成功次数", value: "RRC建立成功次数"},
		{label: "RRC建立失败次数", value: "RRC建立失败次数"},
		{label: "ERAB建立请求次数", value: "ERAB建立请求次数"},
		{label: "ERAB建立成功次数", value: "ERAB建立成功次数"},
		{label: "ERAB建立失败次数", value: "ERAB建立失败次数"}
	];
	$("#worstCellChartPrimaryAxisType").multiselect("dataprovider", data_worstCellChartPrimaryAxisType);
	$("#worstCellChartAuxiliaryAxisType").multiselect("dataprovider", data_worstCellChartAuxiliaryAxisType);
	$("#lowAccess").bind("click", function () {
		var data_worstCellChartPrimaryAxisType = [
			{label: "无线接通率", value: "无线接通率"},
			{label: "RRC建立成功率", value: "RRC建立成功率"},
			{label: "ERAB建立成功率", value: "ERAB建立成功率"}
		];
		var data_worstCellChartAuxiliaryAxisType = [
			{label: "RRC建立请求次数", value: "RRC建立请求次数"},
			{label: "RRC建立成功次数", value: "RRC建立成功次数"},
			{label: "RRC建立失败次数", value: "RRC建立失败次数"},
			{label: "ERAB建立请求次数", value: "ERAB建立请求次数"},
			{label: "ERAB建立成功次数", value: "ERAB建立成功次数"},
			{label: "ERAB建立失败次数", value: "ERAB建立失败次数"}
		];
		$("#worstCellChartPrimaryAxisType").multiselect("dataprovider", data_worstCellChartPrimaryAxisType);
		$("#worstCellChartAuxiliaryAxisType").multiselect("dataprovider", data_worstCellChartAuxiliaryAxisType);
		$("#index").css("display", "block");
	});
	$("#highLost").bind("click", function () {
		var data_worstCellChartPrimaryAxisType = [
			{label: "无线掉线率", value: "无线掉线率"}
		];
		var data_worstCellChartAuxiliaryAxisType = [
			{label: "无线掉线次数", value: "无线掉线次数"},
			{label: "上下文建立成功数", value: "上下文建立成功数"},
			{label: "遗留上下文数", value: "遗留上下文数"},
			{label: "小区闭锁导致的掉线", value: "小区闭锁导致的掉线"},
			{label: "切换导致的掉线", value: "切换导致的掉线"},
			{label: "S1接口故障导致的掉线", value: "S1接口故障导致的掉线"},
			{label: "UE丢失导致的掉线", value: "UE丢失导致的掉线"},
			{label: "预清空导致的掉线", value: "预清空导致的掉线"}
		];
		$("#worstCellChartPrimaryAxisType").multiselect("dataprovider", data_worstCellChartPrimaryAxisType);
		$("#worstCellChartAuxiliaryAxisType").multiselect("dataprovider", data_worstCellChartAuxiliaryAxisType);
		$("#index").css("display", "block");
	});
	$("#badHandover").bind("click", function () {
		var data_worstCellChartPrimaryAxisType = [
			{label: "切换成功率", value: "切换成功率"},
			{label: "准备切换成功率", value: "准备切换成功率"},
			{label: "执行切换成功率", value: "执行切换成功率"}
		];
		var data_worstCellChartAuxiliaryAxisType = [
			{label: "准备切换成功数", value: "准备切换成功数"},
			{label: "准备切换失败数", value: "准备切换失败数"},
			{label: "准备切换尝试数", value: "准备切换尝试数"},
			{label: "执行切换成功数", value: "执行切换成功数"},
			{label: "执行切换失败数", value: "执行切换失败数"},
			{label: "执行切换尝试数", value: "执行切换尝试数"}
		];
		$("#worstCellChartPrimaryAxisType").multiselect("dataprovider", data_worstCellChartPrimaryAxisType);
		$("#worstCellChartAuxiliaryAxisType").multiselect("dataprovider", data_worstCellChartAuxiliaryAxisType);
		$("#index").css("display", "block");
	});
	$("#Interference").bind("click", function () {
		$("#index").css("display", "none");
	});
}

function doSearchbadCell(type, table) {
	$("#badCellTable").grid("destroy", true, true);
	if (table == "lowAccess") {
		var sl = Ladda.create(document.getElementById("searchLowAccess"));
		var el = Ladda.create(document.getElementById("exportLowAccess"));
		sl.start();
		el.start();
		if ($("#citysLowAccess").val() == null) {
			// alert("请选择城市");
			layer.open({
				title: "提示",
				content: "请选择城市"
			});
			sl.stop();
			el.stop();
			return;
		}
		if ($("#startDateLowAccess").val() == "" || $("#endDateLowAccess").val() == "") {
			// alert("请选择日期");
			layer.open({
				title: "提示",
				content: "请选择日期"
			});
			sl.stop();
			el.stop();
			return;
		}
		var params = {
			table: "lowAccessCell_ex",
			city: $("#citysLowAccess").val(),
			startTime: $("#startDateLowAccess").val(),
			endTime: $("#endDateLowAccess").val(),
			cell: $("#cellLowAccess").val(),
			hour: $("#hourLowAccess").val()
		};
	} else if (table == "highLost") {
		var sh = Ladda.create(document.getElementById("searchHighLost"));
		var eh = Ladda.create(document.getElementById("exportHighLost"));
		sh.start();
		eh.start();
		if ($("#citysHighLost").val() == null) {
			// alert("请选择城市");
			layer.open({
				title: "提示",
				content: "请选择城市"
			});
			sh.stop();
			eh.stop();
			return;
		}
		if ($("#startDateHighLost").val() == "" || $("#endDateHighLost").val() == "") {
			// alert("请选择日期");
			layer.open({
				title: "提示",
				content: "请选择日期"
			});
			sh.stop();
			eh.stop();
			return;
		}
		var params = {
			table: "highLostCell_ex",
			city: $("#citysHighLost").val(),
			startTime: $("#startDateHighLost").val(),
			endTime: $("#endDateHighLost").val(),
			cell: $("#cellHighLost").val(),
			hour: $("#hourHighLost").val()
		};
	} else if (table == "badHandover") {
		var sb = Ladda.create(document.getElementById("searchBadHandover"));
		var eb = Ladda.create(document.getElementById("exportBadHandover"));
		sb.start();
		eb.start();
		if ($("#citysBadHandover").val() == null) {
			// alert("请选择城市");
			layer.open({
				title: "提示",
				content: "请选择城市"
			});
			sb.stop();
			eb.stop();
			return;
		}
		if ($("#startDateBadHandover").val() == "" || $("#endDateBadHandover").val() == "") {
			// alert("请选择日期");
			layer.open({
				title: "提示",
				content: "请选择日期"
			});
			sb.stop();
			eb.stop();
			return;
		}
		var params = {
			table: "badHandoverCell_ex",
			city: $("#citysBadHandover").val(),
			startTime: $("#startDateBadHandover").val(),
			endTime: $("#endDateBadHandover").val(),
			cell: $("#cellBadHandover").val(),
			hour: $("#hourBadHandover").val()
		};
	} else if (table == "highInterference") {
		var shi = Ladda.create(document.getElementById("searchHighInterference"));
		var ehi = Ladda.create(document.getElementById("exportHighInterference"));
		shi.start();
		ehi.start();
		if ($("#citysHighInterference").val() == null) {
			// alert("请选择城市");
			layer.open({
				title: "提示",
				content: "请选择城市"
			});
			shi.stop();
			ehi.stop();
			return;
		}
		if ($("#startDateHighInterference").val() == "" || $("#endDateHighInterference").val() == "") {
			// alert("请选择日期");
			layer.open({
				title: "提示",
				content: "请选择日期"
			});
			shi.stop();
			ehi.stop();
			return;
		}
		var cellType = $("#cellType").val();
		if (cellType == "avgPRB") {
			tables = "interfereCell_avg";
		} else if (cellType == "onePRB") {
			tables = "interfereCell_one";
		}
		var params = {
			table: tables,
			city: $("#citysHighInterference").val(),
			startTime: $("#startDateHighInterference").val(),
			endTime: $("#endDateHighInterference").val(),
			cell: $("#cellHighInterference").val(),
			hour: $("#hourHighInterference").val()
		};
	}

	$.post("historyCellSearch/historyCell", params, function (data) {
		if (table == "lowAccess") {
			sl.stop();
			el.stop();
		} else if (table == "highLost") {
			sh.stop();
			eh.stop();
		} else if (table == "badHandover") {
			sb.stop();
			eb.stop();
		} else if (table == "highInterference") {
			shi.stop();
			ehi.stop();
		}
		var filename = data.filename;
		if (type == "file") {
			fileZipSave(filename);
			return;
		}
		var fieldArr = [];
		var textArr = data.content.split(",");
		var text = [];
		for (var i = 1; i < textArr.length; i++) {
			text.push(textArr[i]);
		}
		for (var i in data.rows[0]) {
			if (i == "id") {
				continue;
			} else {
				fieldArr[fieldArr.length] = {
					field: i,
					title: text[fieldArr.length],
					width: textWidth(i),
					sortable: true
				};
			}
		}
		var newData = data.rows;
		$("#badCellTable").grid("destroy", true, true);
		var badCellTable = $("#badCellTable").grid({
			columns: fieldArr,
			dataSource: newData,
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap",
			primaryKey: "id"
		});
		badCellTable.on("rowSelect", function (e, $row, id, record) {
			$("#worstCellContainer1").empty();
			$("#badCellTableIndex").empty();
			//指标详情
			var type = $("#type").children(".active").children().children().val();
			// alert($("#type").children(".active").children().html());
			var params = {
				table: type,
				cell: record.cell,
				startTime: $("#startDate" + type).val(),
				endTime: $("#endDate" + type).val(),
				city: $("#citys" + type).val()
			};
			$.get("historyCellSearch/getIndexCell", params, function (data) {
				var filename = data.filename;
				$("#badCellTableIndexFilename").val(filename);
				var fieldArr = [];
				var textArr = data.content.split(",");
				var text = [];
				for (var i = 1; i < textArr.length; i++) {
					text.push(textArr[i]);
				}
				for (var i in data.rows[0]) {
					if (i == "id") {
						continue;
					} else {
						fieldArr[fieldArr.length] = {
							field: i,
							title: text[fieldArr.length],
							width: textWidth(i),
							sortable: true
						};
					}
				}
				var newData = data.rows;
				$("#badCellTableIndex").grid("destroy", true, true);
				var badCellTableIndex = $("#badCellTableIndex").grid({
					columns: fieldArr,
					dataSource: newData,
					pager: {limit: 10, sizes: [10, 20, 50, 100]},
					autoScroll: true,
					uiLibrary: "bootstrap",
					primaryKey: "id"
				});
			});
			//指标趋势
			var type = $("#type").children(".active").children().children().val();
			var yAxis_name_left = $("#worstCellChartPrimaryAxisType").val();
			var yAxis_name_right = $("#worstCellChartAuxiliaryAxisType").val();
			var params_trends = {
				table: type,
				cell: record.cell,
				startTime: $("#startDate" + type).val(),
				endTime: $("#endDate" + type).val(),
				yAxis_name_left: yAxis_name_left,
				yAxis_name_right: yAxis_name_right
			};
			$.get("historyCellSearch/getChartDataHistory", params_trends, function (data) {
				var cat_str = JSON.stringify(JSON.parse(data).categories);
				var ser_str = JSON.stringify(JSON.parse(data).series);
				ser_str = ser_str.replace(/"/g, "");
				ser_str = ser_str.replace(yAxis_name_left, "'" + yAxis_name_left + "'");
				ser_str = ser_str.replace("spline", "'column'");
				ser_str = ser_str.replace("#89A54E", "'#89A54E'");
				ser_str = ser_str.replace(yAxis_name_right, "'" + yAxis_name_right + "'");
				ser_str = ser_str.replace("column", "'spline'");
				ser_str = ser_str.replace("#4572A7", "'#4572A7'");
				var cat_obj = eval('(" + cat_str + ")');
				var ser_obj = eval('(" + ser_str + ")');
				$("#worstCellContainer1").highcharts({
					exporting: {
						enabled: true,
					},
					credits: {
						enabled: false
					},
					chart: {
						zoomType: "xy"
					},
					title: {
						text: record.cell + " / " + type
					},
					xAxis: [{
						categories: cat_obj
					}],
					yAxis: [{
						labels: {
							format: "{value} %",
							style: {
								color: "#89A54E"
							}
						},
						title: {
							text: yAxis_name_left,
							style: {
								color: "#89A54E"
							}
						},
						tickPositions: [0, 25, 50, 75, 100]
					}, {
						labels: {
							format: "{value}",
							style: {
								color: "#4572A7"
							}
						},
						title: {
							text: yAxis_name_right,
							style: {
								color: "#4572A7"
							}
						},
						opposite: true
					}],
					tooltip: {
						shared: true
					},
					legend: {
						layout: "vertical",
						align: "right",
						x: 0,
						verticalAlign: "bottom",
						y: 0,
						floating: true,
						backgroundColor: "#FFFFFF"
					},
					series: ser_obj
				});
			});


			$("#worstCellChartPrimaryAxisType").change(function () {
				var type = $("#type").children(".active").children().children().val();
				var yAxis_name_left = $("#worstCellChartPrimaryAxisType").val();
				var yAxis_name_right = $("#worstCellChartAuxiliaryAxisType").val();
				var params_trends = {
					table: type,
					cell: record.cell,
					startTime: $("#startDate" + type).val(),
					endTime: $("#endDate" + type).val(),
					yAxis_name_left: yAxis_name_left,
					yAxis_name_right: yAxis_name_right
				};

				$.get("historyCellSearch/getChartDataHistory", params_trends, function (data) {
					var cat_str = JSON.stringify(JSON.parse(data).categories);
					var ser_str = JSON.stringify(JSON.parse(data).series);
					ser_str = ser_str.replace(/"/g, "");
					ser_str = ser_str.replace(yAxis_name_left, "'" + yAxis_name_left + "'");
					ser_str = ser_str.replace("spline", "'column'");
					ser_str = ser_str.replace("#89A54E", "'#89A54E'");
					ser_str = ser_str.replace(yAxis_name_right, "'" + yAxis_name_right + "'");
					ser_str = ser_str.replace("column", "'spline'");
					ser_str = ser_str.replace("#4572A7", "'#4572A7'");
					var cat_obj = eval("(" + cat_str + ")");
					var ser_obj = eval("(" + ser_str + ")");
					$("#worstCellContainer1").highcharts({
						exporting: {
							enabled: true,
						},
						credits: {
							enabled: false
						},
						chart: {
							zoomType: "xy"
						},
						title: {
							text: record.cell + " / " + type
						},
						xAxis: [{
							categories: cat_obj
						}],
						yAxis: [{
							labels: {
								format: "{value} %",
								style: {
									color: "#89A54E"
								}
							},
							title: {
								text: yAxis_name_left,
								style: {
									color: "#89A54E"
								}
							},
							tickPositions: [0, 25, 50, 75, 100]
						}, {
							labels: {
								format: "{value}",
								style: {
									color: "#4572A7"
								}
							},
							title: {
								text: yAxis_name_right,
								style: {
									color: "#4572A7"
								}
							},
							opposite: true
						}],
						tooltip: {
							shared: true
						},
						legend: {
							layout: "vertical",
							align: "right",
							x: 0,
							verticalAlign: "bottom",
							y: 0,
							floating: true,
							backgroundColor: "#FFFFFF"
						},
						series: ser_obj
					});

				});
			});
			$("#worstCellChartAuxiliaryAxisType").change(function () {
				var type = $("#type").children(".active").children().children().val();
				var yAxis_name_left = $("#worstCellChartPrimaryAxisType").val();
				var yAxis_name_right = $("#worstCellChartAuxiliaryAxisType").val();
				var params_trends = {
					table: type,
					cell: record.cell,
					startTime: $("#startDate" + type).val(),
					endTime: $("#endDate" + type).val(),
					yAxis_name_left: yAxis_name_left,
					yAxis_name_right: yAxis_name_right
				};

				$.get("historyCellSearch/getChartDataHistory", params_trends, function (data) {
					var cat_str = JSON.stringify(JSON.parse(data).categories);
					var ser_str = JSON.stringify(JSON.parse(data).series);
					ser_str = ser_str.replace(/"/g, "");
					ser_str = ser_str.replace(yAxis_name_left, "'" + yAxis_name_left + "'");
					ser_str = ser_str.replace("spline", "'column'");
					ser_str = ser_str.replace("#89A54E", "'#89A54E'");
					ser_str = ser_str.replace(yAxis_name_right, "'" + yAxis_name_right + "'");
					ser_str = ser_str.replace("column", "'spline'");
					ser_str = ser_str.replace("#4572A7", "'#4572A7'");
					var cat_obj = eval("(" + cat_str + ")");
					var ser_obj = eval("(" + ser_str + ")");
					$("#worstCellContainer1").highcharts({
						exporting: {
							enabled: true,
						},
						credits: {
							enabled: false
						},
						chart: {
							zoomType: "xy"
						},
						title: {
							text: record.cell + " / " + type
						},
						xAxis: [{
							categories: cat_obj
						}],
						yAxis: [{
							labels: {
								format: "{value} %",
								style: {
									color: "#89A54E"
								}
							},
							title: {
								text: yAxis_name_left,
								style: {
									color: "#89A54E"
								}
							},
							tickPositions: [0, 25, 50, 75, 100]
						}, {
							labels: {
								format: "{value}",
								style: {
									color: "#4572A7"
								}
							},
							title: {
								text: yAxis_name_right,
								style: {
									color: "#4572A7"
								}
							},
							opposite: true
						}],
						tooltip: {
							shared: true
						},
						legend: {
							layout: "vertical",
							align: "right",
							x: 0,
							verticalAlign: "bottom",
							y: 0,
							floating: true,
							backgroundColor: "#FFFFFF"
						},
						series: ser_obj
					});

				});
			});

		});
	});
}

function fileSave() {
	var filename = $("#badCellTableIndexFilename").val();
	fileZipSave(filename);
}

function fileZipSave(fileName) {
	if (fileName != "") {
		var fileNames = csvZipDownload(fileName);
		download(fileNames);
	}
	else {
		// alert("No file generated so far!");
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

function textWidth(text) {
	var length = text.length;
	if (length > 15) {
		return length * 10;
	}
	return 150;
}

function setHours() {
	$("#hourLowAccess").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "请选择小时",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有",
		maxHeight: 200,
		maxWidth: "100%"
	});
	$("#hourHighLost").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "请选择小时",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有",
		maxHeight: 200,
		maxWidth: "100%"
	});
	$("#hourBadHandover").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "请选择小时",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有",
		maxHeight: 200,
		maxWidth: "100%"
	});
	$("#hourHighInterference").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "请选择小时",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有",
		maxHeight: 200,
		maxWidth: "100%"
	});
}

function setHighInterferenceTime() {
	$("#startDateHighInterference").datepicker({format: "yyyy-mm-dd"});
	$("#endDateHighInterference").datepicker({format: "yyyy-mm-dd"});
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + (month >= 10 ? month : "0" + month) + "-" + (day >= 10 ? day : "0" + day);
	$("#startDateHighInterference").val(today);
	var params = {
		city: getFirstCity(),
		type: "高干扰小区"
	};
	$.get("historyCellSearch/historyCellDate", params, function (data) {
		var sdata = [];
		for (var i = 0; i < data.length; i++) {
			if (data[i] === today) {
				continue;
			}
			sdata.push(data[i]);
		}
		sdata.push(today);
		$("#startDateHighInterference").datepicker("setValues", sdata);
		$("#endDateHighInterference").datepicker("setValues", sdata);
	});
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#startDateHighInterference").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");
	var checkout = $("#endDateHighInterference").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkout.hide();
	}).data("datepicker");
}

function setBadHandoverTime() {
	$("#startDateBadHandover").datepicker({format: "yyyy-mm-dd"});
	$("#endDateBadHandover").datepicker({format: "yyyy-mm-dd"});
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + (month >= 10 ? month : "0" + month) + "-" + (day >= 10 ? day : "0" + day);
	$("#startDateBadHandover").val(today);
	var params = {
		city: getFirstCity(),
		type: "切换差小区"
	};
	$.get("historyCellSearch/historyCellDate", params, function (data) {
		var sdata = [];
		for (var i = 0; i < data.length; i++) {
			if (data[i] === today) {
				continue;
			}
			sdata.push(data[i]);
		}
		sdata.push(today);
		$("#startDateBadHandover").datepicker("setValues", sdata);
		$("#endDateBadHandover").datepicker("setValues", sdata);
	});
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#startDateBadHandover").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");
	var checkout = $("#endDateBadHandover").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkout.hide();
	}).data("datepicker");
}

function setHighLostTime() {
	$("#startDateHighLost").datepicker({format: "yyyy-mm-dd"});
	$("#endDateHighLost").datepicker({format: "yyyy-mm-dd"});
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + (month >= 10 ? month : "0" + month) + "-" + (day >= 10 ? day : "0" + day);
	$("#startDateHighLost").val(today);
	var params = {
		city: getFirstCity(),
		type: "高掉线小区"
	};
	$.get("historyCellSearch/historyCellDate", params, function (data) {
		var sdata = [];
		for (var i = 0; i < data.length; i++) {
			if (data[i] === today) {
				continue;
			}
			sdata.push(data[i]);
		}
		sdata.push(today);
		$("#startDateHighLost").datepicker("setValues", sdata);
		$("#endDateHighLost").datepicker("setValues", sdata);
	});
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#startDateHighLost").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");
	var checkout = $("#endDateHighLost").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkout.hide();
	}).data("datepicker");
}

function setLowAccessTime() {
	$("#startDateLowAccess").datepicker({format: "yyyy-mm-dd"});
	$("#endDateLowAccess").datepicker({format: "yyyy-mm-dd"});
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + (month >= 10 ? month : "0" + month) + "-" + (day >= 10 ? day : "0" + day);
	$("#startDateLowAccess").val(today);
	var params = {
		city: getFirstCity(),
		type: "低接入小区"
	};
	$.get("historyCellSearch/historyCellDate", params, function (data) {
		var sdata = [];
		for (var i = 0; i < data.length; i++) {
			if (data[i] === today) {
				continue;
			}
			sdata.push(data[i]);
		}
		sdata.push(today);
		$("#startDateLowAccess").datepicker("setValues", sdata);
		$("#endDateLowAccess").datepicker("setValues", sdata);
	});
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#startDateLowAccess").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");
	var checkout = $("#endDateLowAccess").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkout.hide();
	}).data("datepicker");
}

function getHighInterferenceCitysOption() {
	$("#citysHighInterference").multiselect({
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
		maxWidth: "100%"
	});
	var url = "historyCellSearch/selectCityOption";
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
			$("#citysHighInterference").multiselect("dataprovider", newOptions);
		}
	});
}

function getBadHandoverCitysOption() {
	$("#citysBadHandover").multiselect({
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
		maxWidth: "100%"
	});
	var url = "historyCellSearch/selectCityOption";
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
			$("#citysBadHandover").multiselect("dataprovider", newOptions);
		}
	});
}

function getHighLostCitysOption() {
	$("#citysHighLost").multiselect({
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
		maxWidth: "100%"
	});
	var url = "historyCellSearch/selectCityOption";
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
			$("#citysHighLost").multiselect("dataprovider", newOptions);
		}
	});
}

function getLowAccessCitysOption() {
	$("#citysLowAccess").multiselect({
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
		maxWidth: "100%"
	});
	var url = "historyCellSearch/selectCityOption";
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
			$("#citysLowAccess").multiselect("dataprovider", newOptions);
		}
	});
}

function setCellType() {
	$("#cellType").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "请选择类别",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		// includeSelectAllOption:true,
		maxHeight: 200,
		maxWidth: "100%"
	});
}