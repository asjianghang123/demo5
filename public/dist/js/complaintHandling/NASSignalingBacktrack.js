var queryBtn = Ladda.create(document.getElementById("queryBtn"));
//var filterBtn = Ladda.create(document.getElementById("filterBtn"));
var exportBtn = Ladda.create(document.getElementById("exportBtn"));
$(function () {
	toogle("NASSignalingBacktrack");
	initCitys();

	initHours();
	//绑定信令图的tab页面，保证页面出来才开始画图，避免画图错位的问题
	$("#table_tab_1_nav").on("shown.bs.tab", function () {
		if ($("#eventChoosedChange").val() == "true") {
			//if($("#sectionchoose").val()=="true"){
			doSearchEvent_chart();
			//}
		}
	});
	$("#modalDialog").draggable({cursor: "default"});//为模态对话框添加拖拽
	$("#message_modal").css("overflow", "hidden");
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

	$.get("NASSignalingBacktrack/getCityDate", function (data) {
		data = JSON.parse(data);
		$("#citys").multiselect("dataprovider", data);
		setTime();
	});
}
function setTime() {
	$("#date").datepicker({format: "yyyy-mm-dd"});  //返回日期
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;
	//$("#date").datepicker("setValue", nowTemp);

	var params = {
		dataBase: $("#citys").val()
	};
	$.post("NASSignalingBacktrack/getDataGroupByDate", params, function (data) {
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
	$("#citys").change(function () {
		var city = $("#citys").val();
		var params = {
			dataBase: city
		};
		$.post("NASSignalingBacktrack/getDataGroupByDate", params, function (data) {
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
function initHours() {
	$("#hours").multiselect({
		//dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "选择时段",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选择全天",
		maxHeight: 200,
		maxWidth: "100%"
	});
	var data = [];
	for (var i = 0; i < 24; i++) {
		var obj = {"value": i, "label": i};
		data.push(obj);
	}
	$("#hours").multiselect("dataprovider", data);
}
function queryProcess() {
	$("#sectionchoose").val("false");
	//$("#filterBtn").attr("disabled","disabled");
	//$("#exportBtn").attr("disabled","disabled");
	$("#eventChoosedChange").val("true");
	if (!$("#imsi").val()) {
		// alert("");
		layer.open({
			title: "提示",
			content: "请输入用户进行查询"
		});
		return;
	}
	queryBtn.start();
	//filterBtn.start();
	exportBtn.start();
	doSearchEvent();
}
$.extend($.fn.datagrid.methods, {
	fixRownumber: function (jq) {
		return jq.each(function () {
			var panel = $(this).datagrid("getPanel");
			var clone = $(".datagrid-cell-rownumber", panel).last().clone();
			clone.css({"position": "absolute", left: -1000}).appendTo("body");
			var width = clone.width("auto").width();
			if (width > 25) {
				$(".datagrid-header-rownumber,.datagrid-cell-rownumber", panel).width(width + 5);
				$(this).datagrid("resize");
				clone.remove();
				clone = null;
			} else {
				$(".datagrid-header-rownumber,.datagrid-cell-rownumber", panel).removeAttr("style");
				$(this).datagrid("resize");
			}
		});
	}
});

function doSearchEvent() {
	var params = {
		dataBase: $("#citys").val(),
		date: $("#date").val(),
		hours: $("#hours").val(),
		imsi: $("#imsi").val(),

		filterSection: $("#sectionchoose").val(),
		ueRefChoosed: $("#ueRefChoosed").val(),
		type: "event"
	};

	params.viewType = "table";

	$("#signalingTable").datagrid({
		url: "NASSignalingBacktrack/getEventData",
		view: scrollview,
		rownumbers: true,
		singleSelect: true,
		autoRowHeight: false,
		pageSize: 50,
		loadMsg: "",
		onClickRow: function (rowIndex, rowData) {
			uechoosed(rowData.imsi);
		},
		onDblClickRow: function (rowIndex, rowData) {
			eventMessageDetail(rowData.id, rowData.eventTime);
		},
		columns: [[
			{field: "eventName", title: "Event Name", width: 250},
			{field: "eventTime", title: "Event Time", width: 180},
			{field: "result", title: "Result", width: 100},
			{field: "msisdn", title: "Msisdn", width: 100},
			{field: "imsi", title: "Imsi", width: 100},
			{field: "imeiTac", title: "ImeiTac", width: 100},
			{field: "imeisv", title: "Imeisv", width: 100},
			{field: "lCauseProtType", title: "LCauseProtType", width: 100},
			{field: "causeCode", title: "CauseCode", width: 100},
			{field: "subCauseCode", title: "SubCauseCode", width: 120},
			{field: "tai", title: "Tai", width: 120},
			{field: "ecgi", title: "Ecgi", width: 120},
			{field: "mmei", title: "Mmei", width: 120},
		]],
		queryParams: params,
		onLoadSuccess: function (data) {
			$(this).datagrid("fixRownumber");
			if ($("#sectionchoose").val() == "true") {
				$("#exportBtn").removeAttr("disabled");
			}
			queryBtn.stop();
			//filterBtn.stop();
			exportBtn.stop();
		}
	});

}


function doSearchEvent_chart() {
	var params = {
		dataBase: $("#citys").val(),
		date: $("#date").val(),
		hours: $("#hours").val(),
		imsi: $("#imsi").val(),

		filterSection: $("#sectionchoose").val(),
		ueRefChoosed: $("#ueRefChoosed").val(),
		type: "event"
	};
	params.viewType = "flow";
	$.ajax({
		type: "post",
		url: "NASSignalingBacktrack/getEventData",
		data: params,
		async: false,
		success: function (returnData) {
			if (returnData == "false") {
				$("#signalingChart").html("数据库中无相应记录!");
			} else {
				$("#signalingChart").html("");
				//draw(returnData);
				drawHighchart(returnData);
				if ($("#sectionchoose").val() == "true") {
					$("#exportBtn").removeAttr("disabled");
					$("#eventChoosedChange").val("false");
				}
			}
		}
	});

}

function uechoosed(ueRef) {
	$("#ueRefChoosed").val(ueRef);
	//$("#filterBtn").removeAttr("disabled");
}

function eventMessageDetail(id, eventTime) {
	//var task="CTR";
	var task = $("#citys").val();
	var date_id = eventTime.split(" ")[0];
	var hour_id = eventTime.split(" ")[1].split(":")[0];
	var data = {
		"id": encodeURI(id),
		"db": encodeURI(task),
		//"eventTime":eventTime
		"date_id": date_id,
		"hour_id": hour_id
	};
	$.ajax({
		type: "post",
		url: "NASSignalingBacktrack/showMessage",
		data: data,
		async: false,
		success: function (returnData) {
			$("#message_modal").modal();
			$("#message").attr("src", returnData);
		}
	});
}

function filterProcess() {
	$("#sectionchoose").val("true");
	queryBtn.start();
	//filterBtn.start();
	exportBtn.start();
	doSearchEvent();
}

function exportProcess() {
	var params = {
		dataBase: $("#citys").val(),
		date: $("#date").val(),
		hours: $("#hours").val(),
		imsi: $("#imsi").val(),

		filterSection: $("#sectionchoose").val(),
		ueRefChoosed: $("#ueRefChoosed").val(),
		type: "event"
	};
	if (!$("#imsi").val()) {
		// alert("");
		layer.open({
			title: "提示",
			content: "请输入用户进行导出"
		});
		return;
	}
	queryBtn.start();
	//filterBtn.start();
	exportBtn.start();
	var url = "NASSignalingBacktrack/getAllEventData";
	$.post(url, params, function (data) {
		data = JSON.parse(data);
		if (data) {
			download(data, "", "data:text/csv;charset=utf-8");
		} else {
			// alert("");
			layer.open({
				title: "提示",
				content: "出现异常，请重试"
			});
			return;
		}
		queryBtn.stop();
		//filterBtn.stop();
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

function getMessageMap() {
	return {
		"RRC": {"source": "eNB", "target": "UE", "color": "green"},
		"S1": {"source": "eNB", "target": "MME", "color": "#15A9C3"},
		"X2": {"source": "eNB", "target": "targeteNB", "color": "blue"},
		"INTERNAL": {"source": "eNB", "target": "eNB", "color": "gray"},
		"UE": {"source": "eNB", "target": "eNB", "color": "gray"}
	};
}
function drawHighchart(returnData) {
	var location = {"UE": "180", "eNB": "400", "MME": "620", "targeteNB": "840"};
	var map = getMessageMap();
	var yinterval = 60;
	var yelement = 50;
	//var returnData = window.localStorage.getItem("drawData");
	var data = JSON.parse(returnData);
	data.records = data.rows;
	var rowlen = data.records.length;
	var height = 100 + yinterval * rowlen;

	var message = [];
	var box = [];
	$("#signalingChart").highcharts({
		title: {
			text: null
		},
		chart: {
			width: 900,
			height: height,
			backgroundColor: "white",
			events: {
				load: function () {
					// Draw the flow chart
					var ren = this.renderer,
						colors = Highcharts.getOptions().colors;

					ren.path(["M", Number.parseInt(location.UE), 70, "V", height - 10])
						.attr({
							"stroke-width": 2,
							stroke: colors[0]
						}).add();
					ren.path(["M", Number.parseInt(location.eNB), 70, "V", height - 10])
						.attr({
							"stroke-width": 2,
							stroke: colors[0]
						}).add();
					ren.path(["M", Number.parseInt(location.MME), 70, "V", height - 10])
						.attr({
							"stroke-width": 2,
							stroke: colors[0]
						}).add();
					ren.path(["M", Number.parseInt(location.targeteNB), 70, "V", height - 10])
						.attr({
							"stroke-width": 2,
							stroke: colors[0]
						}).add();
					ren.label("UE", location.UE - 20, 30)
						.attr({
							fill: colors[0],
							stroke: "white",
							"stroke-width": 2,
							padding: 10,
							r: 5
						})
						.css({
							color: "white",
							fontSize: "16px"
						})
						.add()
						.shadow(true);
					ren.label("eNB", location.eNB - 25, 30)
						.attr({
							fill: colors[0],
							stroke: "white",
							"stroke-width": 2,
							padding: 10,
							r: 5
						})
						.css({
							color: "white",
							fontSize: "16px"
						})
						.add()
						.shadow(true);
					ren.label("MME", location.MME - 30, 30)
						.attr({
							fill: colors[0],
							stroke: "white",
							"stroke-width": 2,
							padding: 10,
							r: 5
						})
						.css({
							color: "white",
							fontSize: "16px"
						})
						.add()
						.shadow(true);
					ren.label("Target eNB", location.targeteNB - 55, 30)
						.attr({
							fill: colors[0],
							stroke: "white",
							"stroke-width": 2,
							padding: 10,
							r: 5
						})
						.css({
							color: "white",
							fontSize: "16px"
						})
						.add()
						.shadow(true);

					for (var i = 0; i < rowlen; i++) {
						var typearr = data.records[i].eventName.split("_");
						var type = typearr[0];

						var id = data.records[i].id;
						var eventName = data.records[i].eventName;
						var direction = data.records[i].direction;
						var eventTime = data.records[i].eventTime;
						var ueRef = data.records[i].ueRef;
						var ecgi = data.records[i].ecgi;
						var y = yelement + (i + 1) * yinterval;
						var source, target;
						if (direction == "EVENT_VALUE_SENT") {
							source = parseInt(location[map[type].source]);
							target = parseInt(location[map[type].target]);
						} else {
							source = parseInt(location[map[type].target]);
							target = parseInt(location[map[type].source]);
						}
						var linetri;
						if (target > source) {
							linetri = ["M", source, y, "L", target, y, "L", (target - 5), (y - 5), "M", target, y, "L", (target - 5), (y + 5)];
						} else if (target < source) {
							linetri = ["M", source, y, "L", target, y, "L", (target + 5), (y - 5), "M", target, y, "L", (target + 5), (y + 5)];
						} else {
							linetri = ["M", (target + 150), y, "L", (target - 150), y];
						}
						box[i] = ren.path(linetri)
							.attr({
								"stroke-width": 2,
								stroke: map[type].color,
								"target": id,
								"title": ueRef
							}).add();
						box[i].on("click", function () {
							uechoosed($(this).attr("title"));
						});
						box[i].on("dblclick", function () {
							eventMessageDetail($(this).attr("target"), eventTime);
						});
						var mid = (source + target) / 2;
						var ymessage = y;

						message[i] = ren.label(eventName, mid - 100, ymessage - 20)
							.attr({
								"target": id,
								"title": ueRef
							})
							.css({
								color: "black",
								fontSize: "10px"
							})
							.add();
						message[i].on("click", function () {
							uechoosed($(this).attr("title"));
						});
						message[i].on("dblclick", function () {
							eventMessageDetail($(this).attr("target"), eventTime);
						});
						var time = ren.label(eventTime, 5, y - 10)
							.css({
								color: "black",
								fontSize: "8px"
							})
							.add();
						if (type == "RRC") {
							var ueid = ren.label("(UE:" + ueRef + ")", mid - 100, ymessage)
								.attr({
									"target": id,
									"title": ueRef
								})
								.css({
									color: "gray",
									fontSize: "8px"
								})
								.add();
							ueid.on("click", function () {
								uechoosed($(this).attr("title"));
							});
							ueid.on("dblclick", function () {
								eventMessageDetail($(this).attr("target"), eventTime);
							});
						}
						if (type == "S1") {
							var ecgi = ren.label("(plmnId:" + ecgi + ")", mid - 100, ymessage)
								.attr({
									"target": id,
									"title": ueRef
								})
								.css({
									color: "gray",
									fontSize: "8px"
								})
								.add();
							ecgi.on("click", function () {
								uechoosed($(this).attr("title"));
							});
							ecgi.on("dblclick", function () {
								eventMessageDetail($(this).attr("target"), eventTime);
							});
						}
					}
				}
			}
		}
	});
}
