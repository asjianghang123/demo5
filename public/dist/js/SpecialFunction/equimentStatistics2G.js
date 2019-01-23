$(document).ready(function () {
	toogle("equipmentStatistics2G");
	getTasks();
});


function exportData() {
	exportTables();
}
var database = "#database";
function getTasks() {
	$(database).select2();
	var url = "equipmentStatistics2G/getTasks";
	$.ajax({
		type: "GET",
		url: url,
		dataType: "json",
		success: function(data){
			var newOptions = [];
			var obj = {};
			$(data).each(function (k, v) {
				v = eval("(" + v + ")");
				var i = 0;
				obj = {
					id: v.text,
					text: v.text
				};
				newOptions.push(obj);
			});
			var parameterAnalysisDateSelect = $(database).select2({
				height: 50,
				placeholder: "请选择数据库",
				//allowClear: true,
				data: newOptions
			});
		}
	});
}



function exportTables() {
	var database = $("#database").val();
	var table_Situation = "#table_Situation";
	var table_Statistics = "#table_Statistics";
	var params = {
		dataBase: database
	};
	exportSituation(params, table_Situation);
	exportStatistics(params, table_Statistics);
}

function getSituation(params,id) {
	var searchBtn = Ladda.create(document.getElementById("search"));
	var exportBtn = Ladda.create(document.getElementById("export"));
	searchBtn.start();
	exportBtn.start();
	var fieldArr = [];
	$.get("equipmentStatistics2G/getSituationDataHeader", params, function (data) {
		if (data.error == "error") {
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
			searchBtn.stop();
			exportBtn.stop();
			return;
		}
		for (var j in data) {
			if (j == "GSM升级到FDD" || j == "说明" ) {
				fieldArr[fieldArr.length] = {field: j, title: j, width: 270};
			}  else {
				fieldArr[fieldArr.length] = {field: j, title: j, width: 130};
			}
		}
		$(id).grid("destroy", true, true);
		var grid = $(id).grid({
			columns: fieldArr,
			params: params,
			dataSource: {
				url: "equipmentStatistics2G/getSituationData",
				success: function (data) {
					data = eval("(" + data + ")");
					grid.render(data);
					searchBtn.stop();
					exportBtn.stop();
				}
			},
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap"
		});

	});
}

function getStatistics(params,id) {
	var searchBtn = Ladda.create(document.getElementById("search"));
	var exportBtn = Ladda.create(document.getElementById("export"));
	searchBtn.start();
	exportBtn.start();
	var fieldArr = [];
	$.get("equipmentStatistics2G/getStatisticDataHeader", params, function (data) {
		if (data.error == "error") {
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
			searchBtn.stop();
			exportBtn.stop();
			return;
		}
		for (var k in data) {
			if (k == "小区ECGI" || k == "GSM升级到FDD" ) {
				fieldArr[fieldArr.length] = {field: k, title: k, width: 220};
			} else if (k == "说明(按各厂家设备与附件2 C列一致)"  ) {
				fieldArr[fieldArr.length] = {field: k, title: k, width: 160};
			} else {
				fieldArr[fieldArr.length] = {field: k, title: k, width: 120};
			}
		}					
		$(id).grid("destroy", true, true);
		var grid = $(id).grid({
			columns: fieldArr,
			params: params,
			dataSource: {
				url: "equipmentStatistics2G/getStatisticData",
				success: function (data) {
					data = eval("(" + data + ")");
					grid.render(data);
					searchBtn.stop();
					exportBtn.stop();					
				}
			},
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap"
		});
	});	
}

function exportSituation(params, id) {
	var searchBtn = Ladda.create(document.getElementById("search"));
	var exportBtn = Ladda.create(document.getElementById("export"));
	searchBtn.start();
	exportBtn.start();
	var url = "equipmentStatistics2G/getAllSituationData";
	$.get(url, params, function (data) {
		if (data.result) {
			fileDownload(data.fileName);
		} else {
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
		}
	});
}

function exportStatistics(params, id) {
	var searchBtn = Ladda.create(document.getElementById("search"));
	var exportBtn = Ladda.create(document.getElementById("export"));
	searchBtn.start();
	exportBtn.start();
	var url = "equipmentStatistics2G/getAllStatisticsData";
	$.get(url, params, function (data) {
		if (data.result) {
			fileDownload(data.fileName);
		} else {
			layer.open({
				title: "提示",
				content: "数据不存在，请重新选择！"
			});
		}
		searchBtn.stop();
		exportBtn.stop();
	});
}

function searchData() {
	var database = $("#database").val();
	var table_Situation = "#table_Situation";
	var table_Statistics = "#table_Statistics";
	$(table_Situation).grid("destroy", true, true);
	$(table_Statistics).grid("destroy", true, true);
	var params = {
		dataBase: database
	};
	getSituation(params, table_Situation);
	getStatistics(params, table_Statistics);
}

function table_show(){
	$("#table_Situation_1").toggle();
	$("#table_Statistics_1").toggle();
}