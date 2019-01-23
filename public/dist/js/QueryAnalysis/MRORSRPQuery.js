$(document).ready(function () {
	getAllCity();
	setTime_MRO();
	toogle("MRORSRPQuery");
});
function getAllCity() {
	$("#city").multiselect({
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
	var url = "MRORSRPQuery/getAllCity";
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
function setTime_MRO() {
	$("#dateTime").datepicker({format: "yyyy-mm-dd"});  //返回日期
	var nowTemp = new Date();
	//$("#dateTime").datepicker("setValue", nowTemp);
	//$("#dateTime").datepicker("setValues", ["2016-10-09","2016-10-10","2016-10-18"]);
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;

	// console.log(today);
	var params = {
		city: getFirstCity()
	};
	$.get("MRORSRPQuery/getDate", params, function (data) {
		var sdata = [];
		for (var i = 0; i < data.length; i++) {
			if (data[i] === today) {
				continue;
			}
			sdata.push(data[i]);
		}
		sdata.push(today);
		$("#dateTime").datepicker("setValues", sdata);
	})
	$("#city").change(function () {
		var city = $("#city").val();
		var params = {
			city: city,
		};
		$.get("MRORSRPQuery/getDate", params, function (data) {
			var sdata = [];
			for (var i = 0; i < data.length; i++) {
				if (data[i] === today) {
					continue;
				}
				sdata.push(data[i]);
			}
			sdata.push(today);
			$("#dateTime").datepicker("setValues", sdata);
		})
	})
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#dateTime").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? " : ";
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");
}
function doSearchMRORSRP(){
	var l = Ladda.create(document.getElementById("search"));
	var E = Ladda.create(document.getElementById("export"));
	l.start();
	//E.start();
	var select = $("#city").val();
	var dateTime = $("#dateTime").val();
	var params = {
		dateTime: dateTime,
		select:select
	};
	$.post("MRORSRPQuery/getMRORSRPDataField", params, function (data) {
		if (data.error == "error") {
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
			l.stop();
			E.stop();
			return;
		}
		var field = data["field"];
		var fieldArr = [];
		for (var k in field) {
			
			fieldArr[fieldArr.length] = {field: field[k], title: field[k], width: textWidth(field[k])};
		}
		$("#MRORSRPTable").grid("destroy", true, true);
		var grid = $("#MRORSRPTable").grid({
			columns: fieldArr,
			params:params,
			dataSource: {
				url: "MRORSRPQuery/getMRORSRPDataSplit",
				success: function (data) {
					data = eval("(" + data + ")");
					if (data.error == "error") {
						$("#MRORSRPTable").grid("destroy", true, true);
						layer.open({
							title: "提示",
							content: "数据不存在，请重新选择！"
						});
						l.stop();
						E.stop();
						return;
					}
					grid.render(data);
					l.stop();
					E.stop();
					$("#exportBtn").removeAttr("disabled");
				},
				type:"post"
			},
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap"
		});
	});
	
}
function exportAll() {
	var l = Ladda.create(document.getElementById("search"));
	var E = Ladda.create(document.getElementById("export"));
	//l.start();
	E.start();
	var select = $("#city").val();
	var dateTime = $("#dateTime").val();
	var params = {
		select: select,
		dateTime: dateTime,
	};
	$.post("MRORSRPQuery/getAllData", params, function (data) {
		if (data.error == "error") {
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
			l.stop();
			E.stop();
			return;
		}
		layer.open({
			title: "提示",
			content: data.fileName
		});
		fileDownload(data.fileName);
		l.stop();
		E.stop();
	});
}

function textWidth(text) {
	var length = text.length;
	if (length > 15) {
		return length * 10;
	}
	return 150;
}