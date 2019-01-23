var parameterAnalysisCityId = "#parameterAnalysisCity";
var tableId = "#parameterDistributeTable";

$(function () {
	toogle("paramDistribution");
	var l = Ladda.create(document.getElementById("queryBtn"));
	var E = Ladda.create(document.getElementById("exportBtn"));
	l.stop();
	E.stop();
	initDate();
	setMOTree();
	//initCitys();
	getCities();
});
$("#subNetworks").multiselect({
		buttonWidth: "80%",
		enableFiltering: true,
		nonSelectedText: "请选择子网",
		filterPlaceholder: "搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有子网",
		maxHeight: 200,
		maxWidth: "100%"
	});
	//数据库获取对应subNwork
	$("#parameterAnalysisCity").change(function () {
		getAllSubNetwork();
	});
	//获取所有被选择的城市
function getChooseCitys() {
	var citys = $("#parameterAnalysisCity").val();
	return citys;
}
	//获取所有被选择的子网
	function getChoosesubNet() {
	var subNet = $("#subNetworks").val();
	return subNet;
}

function getAllSubNetwork() {
	var citys = getChooseCitys();	
	var params = {
		citys: citys
	};
	$.post("paramDistribution/getAllSubNetwork", params, function (data) {
		var newOptions = [];
		var obj = {};
		$(data).each(function (k, v) {
			v = eval("(" + v + ")");
			obj = {
				label: v.text,
				value: v.value,
				selected: true
			};
			newOptions.push(obj);
		});
		$("#subNetworks").multiselect("dataprovider", newOptions);
	});
}
function initDate() {
	var url = "paramDistribution/getDate";
	$.post(url, null, function (data) {
		data = eval(data);
		var date = $("#date").select2({
			placeholder: "请选择日期",
			//allowClear: true,
			data: data
		});
		var task = getCurrentDate("kget");
		$("#date").val(getCurrentDate("kget")).trigger("change");
		if ($("#date").val() == null) {
			$("#date").val(getYesterdayDate("kget")).trigger("change");
		}
	});
}

function getCurrentDate(taskType) {
	var mydate = new Date();
	var myyear = mydate.getYear();
	var myyearStr = (myyear + "").substring(1);
	var mymonth = mydate.getMonth() + 1; //值范围0-11
	mydate = mydate.getDate();  //值范围1-31
	var mymonthStr = "";
	var mydateStr = "";
	mymonthStr = mymonth >= 10 ? mymonth : "0" + mymonth;
	mydateStr = mydate >= 10 ? mydate : "0" + mydate;
	var kgetDate = taskType + myyearStr + mymonthStr + mydateStr;
	return kgetDate;
}
function getYesterdayDate(taskType) {
	var mydate = new Date();
	var yesterday_miliseconds = mydate.getTime() - 1000 * 60 * 60 * 24;
	var Yesterday = new Date();
	Yesterday.setTime(yesterday_miliseconds);

	var yesterday_year = Yesterday.getYear().toString().substring(1.3);
	var month_temp = Yesterday.getMonth() + 1;
	var yesterday_month = month_temp > 9 ? month_temp.toString() : "0" + month_temp.toString();
	var d = Yesterday.getDate();
	var Day = d > 9 ? d.toString() : "0" + d.toString();
	var kgetDate = taskType + yesterday_year + yesterday_month + Day;
	return kgetDate;
}

function setMOTree() {
	$.get("common/json/parameterTreeData.json", null, function (data) {
		// date = eval("(" + data + ")");
		if(typeof(data)=="object"){
				data = data;
			}else{
				data = eval("(" + data + ")");
			}
		var options = {
			bootstrap2: false,
			showTags: true,
			levels: 2,
			data: data,
			onNodeSelected: function (event, data) {
				$("#paramFlag").val("");
				$("#MOFlag").val(data.text);
				var queryMOValue = $("#queryMO").val();
				setParamTree(queryMOValue);
			}
		};

		$("#MOQueryTree").treeview(options);
	});
}
//清空模板树
function clearMO() {
	$("#queryMO").val("");
	setMOTree();
}

//筛选模板树
function searchMO() {
	var pattern = $("#queryMO").val();
	if (pattern == "update") {   //添加表的时候需要手动update，以更新paramDistribution.txt文件
		var params = {
			task: $("#date").val(),
			data: "update"
		};
		$.get("paramDistribution/updateSearchContext", params, function () {
			//alert("update sucess!");
			layer.open({
				title: "提示",
				content: "上传成功"
			});
		});
	}
	if (!pattern) {
		return;
	}
	var params = {
		data: pattern
	};
	$.get("paramDistribution/updateSearch", params, function (data) {
		data = JSON.parse(data);
		var mdata = data.mo;
		//console.log(data);
		//$("#idNum").val(data.count);
		var options = {
			bootstrap2: false,
			showTags: true,
			levels: 2,
			data: eval("(" + mdata.content + ")"),
			onNodeSelected: function (event, data) {
				$("#MOFlag").val(data.text);
				var queryMOValue = $("#queryMO").val();
				setParamTree(queryMOValue);
				
			}
		};
		$("#MOQueryTree").treeview(options);

		var pdata = data.params;
		//$("#idNum").val(data.count);
		var options = {
			bootstrap2: false,
			showTags: true,
			levels: 2,
			data: eval("(" + pdata.content + ")"),
			onNodeSelected: function (event, data) {
				var queryMOValue = $("#queryMO").val();
				setParamTree(queryMOValue);
			}
		};
		$("#paramQueryTree").treeview(options);
	});
}

function setParamTree(pattern) {
	var task = $("#date").val();
	if (task == "") {
		layer.open({
			title: "提示",
			content: "请先选择日期"
		});
		return false;
	}

	var data = {
		"task": $("#date").val(),
		"mo": $("#MOFlag").val(),
		"pattern": pattern
	};
	var url = "paramDistribution/getParameterList";
	$.post(url, data, function (data) {

		data = JSON.parse(data);
		$("#idNum").val(data.count);
		var options = {
			bootstrap2: false,
			showTags: true,
			levels: 2,
			data: eval("(" + data.content + ")"),
			onNodeSelected: function (event, data) {
				$("#paramFlag").val(data.text);
				if ($("#MOFlag").val() != "")
					parameterDistributeSearch();
				//else alert("请先选择MO项！");
				else {
					layer.open({
						title: "提示",
						content: "请先选择MO项"
					});
				}
			}
		};
		$("#paramQueryTree").treeview(options);
	});

}
//清空模板树
function clearParam() {
	$("#queryParam").val("");
	setParamTree();
}

//筛选模板树
function searchParam() {
	var pattern = $("#queryParam").val();
	if (!pattern) {
		return;
	}
	if (!$("#MOFlag").val()) {
		//alert("请先选择MO项！");
		layer.open({
			title: "提示",
			content: "请先选择MO项"
		});
		return;
	}
	setParamTree(pattern);
}
function parameterDistributeSearch() {
	var databaseDate = $("#date").val();
	var mo = $("#MOFlag").val();
	var parameterName = $("#paramFlag").val();
	var params = {
		db: databaseDate,
		table: mo,
		parameterName: parameterName,
	};
	var url = "paramDistribution/getChartData";
	chart_column(url, params, "#parameterDistributeView");

}
var chart_column = function (route, params, block) {
	$.ajax({
		type: "post",
		url: route,
		data: params,
		dataType: "json",
		beforeSend: function () {
			$(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
		},
		success: function (data) {
			$(block).html("");
			$(block).highcharts({
				chart: {
					type: "column"
				},
				title: {
					text: params["parameterName"] + "分布"
				},
				subtitle: {
					text: null
				},
				xAxis: {
					categories: data.category,
					crosshair: true
				},
				yAxis: {
					min: 0,
					title: {
						text: null
					}
				},
				tooltip: {
					shared: true,
					useHTML: true
				},
				plotOptions: {
					column: {
						pointPadding: 0.2,
						borderWidth: 0
					}
				},
				legend: {
					enabled: true
				},
				credits: {
					enabled: false,
				},
				series: data.series
			});
		}
	});
};

function getCities() {
	$(parameterAnalysisCityId).multiselect({
		dropRight: true,
		buttonWidth: 230,
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
	url = "paramDistribution/getCitySelect";
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
			$(parameterAnalysisCityId).multiselect("dataprovider", newOptions);
		}
	});
}
function queryByCity() {
	var l = Ladda.create(document.getElementById("queryBtn"));
	var E = Ladda.create(document.getElementById("exportBtn"));
	l.start();
	E.start();
	var task = $("#date").val();
	var mo = $("#MOFlag").val();
	if (!mo) {
		//alert("请先选择MO项！");
		layer.open({
			title: "提示",
			content: "请先选择MO项"
		});
		return;
	}
	var parameterName = $("#paramFlag").val();
	if (!parameterName) {
		//alert("请先选择参数项！");
		layer.open({
			title: "提示",
			content: "请先选择参数项"
		});
		return;
	}
	var citys = $(parameterAnalysisCityId).val();
	var subNet = $(subNetworks).val();
	var params = {
		db: task,
		table: mo,
		parameterName: parameterName,
		subNet:subNet,
		citys: citys
	};

	var fieldArr = [];
	$.post("paramDistribution/getTableHeader", params, function (data) {
		l.stop();
		E.stop();
		$(tableId).grid("destroy", true, true);
		if (data.result == "error") {
			//alert("没有记录");
			layer.open({
				title: "提示",
				content: "没有记录"
			});
			return;
		} else {
			for (var k in data) {
				if (k == "mo") {
					fieldArr[fieldArr.length] = {field: k, title: k, width: 500};
				} else {
					fieldArr[fieldArr.length] = {field: k, title: k, width: textWidth(k)};
				}
			}
			$(tableId).grid("destroy", true, true);
			$(tableId).grid({
				columns: fieldArr,
				dataSource: {url: "paramDistribution/getTableData", type: "post", data: params},
				//primaryKey: "id",
				pager: {limit: 10, sizes: [10, 20, 50, 100]},
				autoScroll: true,
				uiLibrary: "bootstrap",
			});
		}
	});
}
function textWidth(text) {
	var length = text.length;
	if (length > 15) {
		return length * 10;
	}
	return 150;
}
function exportByCity() {
	var l = Ladda.create(document.getElementById("queryBtn"));
	var E = Ladda.create(document.getElementById("exportBtn"));
	l.start();
	E.start();
	var task = $("#date").val();
	var mo = $("#MOFlag").val();
	if (!mo) {
		//alert("请先选择MO项！");
		layer.open({
			title: "提示",
			content: "请先选择MO项"
		});
		return;
	}
	var parameterName = $("#paramFlag").val();
	if (!parameterName) {
		//alert("请先选择参数项！");
		layer.open({
			title: "提示",
			content: "请先选择参数项"
		});
		return;
	}
	var citys = $(parameterAnalysisCityId).val();
	var subNets=$(subNetworks).val();
	var params = {
		db: task,
		table: mo,
		parameterName: parameterName,
		subNet: subNets,
		citys: citys
	};
	$.post("paramDistribution/exportCSV", params, function (data) {
		l.stop();
		E.stop();
		if (data.result) {
			fileDownload(data.fileName);
		} else {
			//alert("没有记录");
			layer.open({
				title: "提示",
				content: "没有记录"
			});
		}
	});
}