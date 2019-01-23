$(function () {
	toogle("signalingAnalysis");
	//setTime();
	getDataBase();
	$("#modalDialog").draggable();//为模态对话框添加拖拽
	$("#detailMessage").css("overflow", "hidden");
});

function setTime() {
	$("#date").datepicker({format: "yyyy-mm-dd"});  //返回日期
	var nowTemp = new Date();
	$("#date").datepicker("setValue", nowTemp);
	//alert(nowTemp);
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#date").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? '' : '';
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");
}
function getDataBase() {
	var url = "signalingAnalysis/getDataBase";
	$.get(url, null, function (data) {
		data = eval(data);
		$("#dataBase").select2({
			placeholder: "请选择数据库",
			//allowClear: true,
			data: data
		});
	});
}
function query() {
	//var date = $("#date").val();
	var dataBase = $("#dataBase").val();
	var url = "signalingAnalysis/getChartData";
	var params = {
		//"date":date,
		"dataBase": dataBase
	};
	$.get(url, params, function (returnData) {
		if (returnData == "false") {
			$("#signalingChart").html("数据库中无相应记录!");
		} else {
			//$("#signalingChart").html("");
			//draw(returnData);
			drawHighchart(returnData);
		}
	});
}
function draw(returnData) {
	var obj = JSON.parse(returnData);
	var nodes = obj.nodes;
	var nodes_length = obj.nodes_length;
	var location = {};
	for (var i in nodes) {
		location[nodes[i]] = 110 * (parseInt(i) + 1) + 50;
	}
	var width = 110 * nodes_length + 100;
	var yinterval = 60;
	obj.records = obj.rows;
	;
	var rowlen = obj.records.length;
	var height = 100 + yinterval * rowlen;
	var paper = Raphael("signalingChart", width, height);
	var headPaper = Raphael("chartHead", width, 100);
	var yelement = 50;

	var element;
	for (var i in location) {
		headPaper.rect(location[i] - 50, yelement - 20, 100, 40, 3).attr("stroke", "#ccc").attr("fill", "#f8f8f8");
		element = headPaper.text(location[i], yelement, i);
		element.attr("font-size", "12px");
	}

	var line;
	for (var i in location) {
		var vline = "M" + location[i] + "," + (yelement + 20) + "V" + (height - 10);
		line = headPaper.path(vline);
		line.attr({"stroke": "#ccc"});

		var vline = "M" + location[i] + "," + (yelement - 50) + "V" + (height - 110);
		line = paper.path(vline);
		line.attr({"stroke": "#ccc"});
	}

	var message = [];
	var box = [];
	for (var i = 0; i < rowlen; i++) {
		var date_id = obj.records[i].date_id;
		var msg_name = obj.records[i].msg_name;
		var id = obj.records[i]._id.$id;
		var y = yelement + (i + 1) * yinterval - 80;
		var source, target;
		source = parseInt(location[obj.records[i].ip_src]);
		target = parseInt(location[obj.records[i].ip_dst]);
		var linetri;
		if (target > source) {
			linetri = "M" + source + "," + (y - 20) + "L" + (target - 20) + "," + (y - 20) + "L" + (target - 20) + "," + (y - 20) + "L" + (target) + "," + y + "L" + (target - 20) + "," + (y + 20) + "L" + (target - 20) + "," + (y + 20) + "L" + source + "," + (y + 20) + "Z";
		} else if (target < source) {
			linetri = "M" + (target + 20) + "," + (y - 20) + "L" + source + "," + (y - 20) + "L" + source + "," + (y + 20) + "L" + (target + 20) + "," + (y + 20) + "L" + (target + 20) + "," + (y + 20) + "L" + target + "," + y + "L" + (target + 20) + "," + (y - 20) + "Z";
		} else {
			linetri = "M" + (target + 150) + "," + (y - 20) + "L" + (target + 150) + "," + (y + 20) + "L" + (target - 150) + "," + (y + 20) + "L" + (target - 150) + "," + (y - 20) + "L" + (target + 150) + "," + (y - 20) + "Z";
		}
		box[i] = paper.path(linetri);
		box[i].attr({"fill": "grey", "stroke": "grey", "opacity": "0.8", "title": msg_name});
		box[i].dblclick(function () {
			eventMessageDetail(id);
		});
		var mid = (source + target) / 2;
		var ymessage = y;
		var start = Math.min(source, target) + msg_name.length * 6 / 2;
		message[i] = paper.text(start, ymessage, msg_name);
		message[i].dblclick(function () {
			eventMessageDetail(id);
		});
		var time = paper.text(80, y, date_id);
		time.attr({"font-size": "8"});
	}
	$("#signalingChart").scroll(function () {
		var left = $("#signalingChart").scrollLeft();
		$("#chartHead svg").css("left", -left);
	});
}
function eventMessageDetail(id) {
	var data = {
		"id": id,
		"dataBase": $("#dataBase").val()
	};
	$.ajax({
		type: "get",
		url: "signalingAnalysis/showMessage",
		data: data,
		async: false,
		success: function (returnData) {
			returnData = JSON.parse(returnData);
			var setting = {
				view: {
					showIcon: false
				},
				data: {
					simpleData: {
						enable: true
					}
				}
			};
			$.fn.zTree.init($("#detailMessageTree"), setting, returnData.tree);
			$("#detailMessage").modal();
		}
	});
}
function drawHighchart(returnData) {
	var obj = JSON.parse(returnData);
	var nodes = obj.nodes;
	var nodes_length = obj.nodes_length;
	var location = {};
	for (var i in nodes) {
		location[nodes[i]] = 150 * (parseInt(i) + 1) + 50;
	}
	var width = 150 * nodes_length + 200;
	var yinterval = 60;
	obj.records = obj.rows;
	;
	var rowlen = obj.records.length;
	var height = 100 + yinterval * rowlen;
	var paper = Raphael("signalingChart", width, height);
	var headPaper = Raphael("chartHead", width, 100);
	var yelement = 50;

	$("#chartHead").highcharts({
		title: {
			text: null
		},
		credits: {
			enabled: false
		},
		chart: {
			width: width,
			height: 100,
			backgroundColor: "white",
			events: {
				load: function () {
					var ren = this.renderer,
						colors = Highcharts.getOptions().colors;
					var element;
					for (var i in location) {
						ren.path(["M", location[i], yelement, "V", height - 10])
							.attr({
								"stroke-width": 2,
								stroke: colors[0]
							}).add();

						ren.label(i, location[i] - (4 * (i.length - 3) + 16), yelement)
							.attr({
								fill: colors[0],
								stroke: "white",
								"stroke-width": 2,
								padding: 10,
								r: 5
							})
							.css({
								color: "white",
								fontSize: "12px"
							})
							.add()
							.shadow(true);
					}

				}
			}
		}
	});
	$("#signalingChart").highcharts({
		title: {
			text: null
		},
		credits: {
			enabled: false
		},
		chart: {
			width: width,
			height: height,
			backgroundColor: "white",
			events: {
				load: function () {
					var ren = this.renderer,
						colors = Highcharts.getOptions().colors;
					var element;
					for (var i in location) {
						ren.path(["M", location[i], yelement - 50, "V", height - 10])
							.attr({
								"stroke-width": 2,
								stroke: colors[0]
							}).add();
					}
					var message = [];
					var box = [];
					for (var i = 0; i < rowlen; i++) {
						var date_id = obj.records[i].date_id;
						var msg_name = obj.records[i].msg_name;
						var id = obj.records[i]._id.$id;
						var y = yelement + (i + 1) * yinterval - 80;
						var source, target;
						source = parseInt(location[obj.records[i].ip_src]);
						target = parseInt(location[obj.records[i].ip_dst]);
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
								stroke: "grey",
								"title": msg_name
							}).add();
						box[i].on("dblclick", function () {
							eventMessageDetail(id);
						});

						var start = Math.min(source, target);
						var ymessage = y;
						message[i] = ren.label(msg_name, start + 20, ymessage - 20)
							.css({
								color: "black",
								fontSize: "10px"
							})
							.add();
						message[i].on("dblclick", function () {
							eventMessageDetail(id);
						});

						var time = ren.label(date_id, 5, y - 10)
							.css({
								color: "black",
								fontSize: "8px"
							})
							.add();
					}

				}
			}
		}
	});
	$("#signalingChart").scroll(function () {
		var left = $("#signalingChart").scrollLeft();
		$("#chartHead .highcharts-container").css("left", -left);
	});
}
