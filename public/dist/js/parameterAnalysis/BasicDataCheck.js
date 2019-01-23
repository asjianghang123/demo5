var parameterAnalysisDateId = "#parameterAnalysisDate";
var consistencyTreeId = "#consistencyTree";
var parameterAnalysisCityId = "#parameterAnalysisCity";
var tableId = "#consistencyCheckDetailTable";
var data_value;
var data_table;
var data_city;
$(function () {
	toogle("BasicDataCheck");
	getTasks();
	getCities();
	initConsistencyTree();
	//-------start of date init----------------
	/*$(parameterAnalysisDateId).on("change",function(node){
	 var treeSelected = $(consistencyTreeId).treeview("getSelected");
	 if(treeSelected != ""){
	 consistencyCheckSearch();
	 }
	 });*/
	//-------end of date init------------------

});
function initConsistencyTree() {

	//-------start of init consistencyTree-----
	$.post("consistencyCheck/getCityList", null, function (data) {
		YCategories = data;
		data_city   = data;
		var url = "common/json/BasicDataCheckTreeData.json";
		$.get(url, null, function (data) {
			var consistencyTreeData;
			if(typeof(data)=="object"){
				consistencyTreeData = data;
			}else{
				consistencyTreeData = eval("(" + data + ")");
			}
			var options = {
				bootstrap2: false,
				showTags: true,
				levels: 1,
				data: consistencyTreeData,
				onNodeSelected: function (event, data) {
					$("#searchType").css("display", "none");
					$("#checkType").css("display", "block");
					$("#UnidirectionalNeighborCell").css("display", "none");
					$("#ExternalGeranCell").css("display", "none");
					//YCategories = ["nantong","changzhou","wuxi","suzhou","zhenjiang"];
					XCategories = [];
					tables = [];
					data_value=data.value;
					if (data.value == "S1Related") {
						XCategories = ["S1-activePlmnList核查", "S1-MMEGI组合不一致", "S1-UsedIP核查"];
						tables = ["TempExternalEUtranCellTDDActivePlmnListCheck", "TempTermPointToMme_S1_MMEGI_dif", "TempTermPointToMme_S1_UsedIP"];
					} else if (data.value == "X2Related") {
						XCategories = ["X2-邻区IP", "X2-UsedIP", "X2-邻区eNBId", "X2-对端配置缺失核查"];
						tables = ["TempTermPointToENB_ENBID_ipAddress", "TempTermPointToENB_ENBID_usedIpAddress", "TempTermPointToENB_IP", "TempTermPointToENB_X2Status"];
					} else if (data.value == "PCIRelated") {
						XCategories = ["PCI一阶冲突", "PCI二阶冲突"];
						tables = ["TempEUtranCellRelationNeighOfPci", "TempEUtranCellRelationNeighOfNeighPci"];
					} else if (data.value == "NeighRelated") {
						/*XCategories = ["冗余频点","单向邻区","有邻区无X2","邻区过多","邻区过少","2G邻区过少","测量频点过多核查","冗余GeranFrequency频点核查"];
						 tables = ["TempEUtranCellFreqRelation","TempEUtranCellRelationUnidirectionalNeighborCell","TempEUtranCellRelationExistNeighborCellWithoutX2","TempEUtranCellRelationManyNeighborCell","TempEUtranCellRelationFewNeighborCell","TempGeranCellRelation2GNeighbor","TempMeasuringFrequencyTooMuch","TempGeranFrequency_1_check"];*/
						XCategories = ["单向邻区", "有邻区无X2", "邻区过多", "邻区过少","超远距离邻区核查","同频RSI冲突核查"];
						tables = ["TempEUtranCellRelationUnidirectionalNeighborCell", "TempEUtranCellRelationExistNeighborCellWithoutX2", "TempEUtranCellRelationManyNeighborCell", "TempEUtranCellRelationFewNeighborCell","EUtranCellRelation_overDistance","TempEUtranCellRelationNeighOfRSI"];
					} else if (data.value == "ParamRelated") {
						XCategories = ["A1-A2门限", "B2-A2critical门限", "A5频率偏移核查1", "A5频率偏移核查2", "B2频率偏移核查1", "B2频率偏移核查2"];
						tables = ["TempParameterQCI_A1A2", "TempParameterQCI_B2A2critical", "TempA5Threshold1Rsrp", "TempA5Threshold2Rsrp", "TempB2Threshold1RsrpGeranOffset", "TempB2Threshold2GeranOffset"];
					} else if (data.value == "ExternalRelated") {
						/*XCategories = ["2G外部定义核查","4G外部定义核查","冗余External_4G定义核查","ExternalOSS_网元OSS归属查询","ExternalOSS_eNBId核查","ExternalOSS_mfbiSupport核查","ExternalOSS_eutranFrequencyRef核查","ExternalOSS_PCI核查","ExternalOSS_tac核查"];
						 tables = ["TempParameter2GKgetCompare","TempExternalNeigh4G","redundancy_External4G","ossInfo","eNBId_check","mfbiSupport_check","eutranFrequencyRef_check","pci_check","tac_check"];*/
						XCategories = ["2G外部定义核查", "4G外部定义核查", "OSS 4G外部定义检查", "冗余External_4G定义核查", "OSS_2G外部定义检查", "OSS_2G外部未定义检查"];
						tables = ["TempParameter2GKgetCompare", "TempExternalNeigh4G", "TempParameterExternalOSSCheck", "redundancy_External4G", "TempParameterKgetExternalCompare", "TempParameterKgetExternalCompare_1"];
					} else if (data.value == "TACRelated") {
						/*XCategories = ["MMEGI-Tac分布","Tac-MMEGI核查","同站不同TAC检查","最近邻区TAC检查","基于MRO的TAC检查","基于MRE的TAC检查"];
						 tables = ["TempTermPointToMme_S1_MMEGI_Tac","TempTermPointToMme_S1_Tac_MMEGI","TempParameterTAC_EUtranCellTDD","TempParameter_TAC_AZ_DIS_INf","TempParameter_TAC_MRO","TempParameter_TAC_MRE"];*/
						XCategories = ["Tac-MMEGI核查", "同站不同TAC检查", "最近邻区TAC检查"];
						tables = ["TempTermPointToMme_S1_Tac_MMEGI", "TempParameterTAC_EUtranCellTDD", "TempParameter_TAC_AZ_DIS_INf"];
					} else if (data.value == "OtherRelated") {
						XCategories = ["经纬度检查", "方向角检查"];
						tables = ["latloncheck", "Angle_Check_table"];
					} else if (data.value == "FreqRelated") {
						XCategories = ["冗余频点", "MR频点定义核查", "测量频点过多核查", "同频测量频点缺失核查", "冗余GeranFrequency频点核查", "2G异常测量频点核查"];
						tables = ["TempEUtranCellFreqRelation", "TempMissingMRFrequency", "TempMeasuringFrequencyTooMuch", "TempMissEqualFrequency", "TempGeranFrequency_1_check", "TempGeranFrequencyException"];
					} else if (data.value == "SiteSoft") {
						XCategories = ["基站CV数量过多"];
						tables = ["TempCVTooMany"];
					}
					data_table=tables;
					consistencyCheckSearch(XCategories, YCategories, tables);
				}
			};

			$(consistencyTreeId).treeview(options);
		});
	});
	//-------end of init consistencyTree-----
}
function getTasks() {
	$(parameterAnalysisDateId).select2();
	var url = "consistencyCheck/getTasks";
	$.ajax({
		type: "post",
		url: url,
		dataType: "json",
		success: function (data) {
			var parameterAnalysisDateSelect = $(parameterAnalysisDateId).select2({
				height: 50,
				placeholder: "请选择日期",
				//allowClear: true,
				data: data
			});
			var task = getCurrentDate("kget");
			$(parameterAnalysisDateId).val(getCurrentDate("kget")).trigger("change");
			if ($(parameterAnalysisDateId).val() == null) {
				$(parameterAnalysisDateId).val(getYesterdayDate("kget")).trigger("change");
			}
		}
	});
}
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
	url = "consistencyCheck/getCities";
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
var tableRow;
function consistencyCheckSearch(XCategories, YCategories, tables) {
	$(tableId).grid("destroy", true, true);
	var params = getParams();
	if (params == false) {
		return false;
	}
	params.tables = tables;
	params.XCategories = XCategories;
	params.YCategories = YCategories;
	var url = "consistencyCheck/consistencyCheckDistribute";
	chart_heatmap(XCategories, YCategories, tables, url, params, "#chart-consistency");
}
var table = "";
var city = "";
var chart_heatmap = function (XCategories, YCategories, tables, route, params, block) {
	$.ajax({
		type: "post",
		url: route,
		data: params,
		dataType: "json",
		beforeSend: function () {
			$(block).html("<img class='col-md-offset-5' src='dist/img/ajax-loader.gif'>");
		},
		success: function (data) {
			$(block).html("");
			$(block).highcharts({
				chart: {
					type: "heatmap"
				},
				title: {
					text: "检查类型数量分布"
				},
				subtitle: {
					text: null
				},
				xAxis: {
					categories: XCategories,
					labels: {
						formatter: function () {
							var length = XCategories.length;
							//获取到刻度值
							var labelVal = this.value;
							//实际返回的刻度值
							var reallyVal = labelVal;
							var standard = 7;
							//判断刻度值的长度
							if (length >= 6 && labelVal.length > standard) {
								var counts = parseInt(labelVal.length / standard) + 1;
								//alert("Oceania".substring(6,7));
								reallyVal = "";
								for (var i = 0; i < counts; i = i + 1) {
									if (i == 0) {
										//截取刻度值
										reallyVal = labelVal.substr(0, standard) + "<br/>";
									} else if (i == counts - 1) {
										reallyVal = reallyVal + labelVal.substring(standard * i, labelVal.length);
									} else {
										reallyVal = reallyVal + labelVal.substr(standard * i, standard) + "<br/>";
									}

								}

							}
							return reallyVal;
						}
					}
				},
				yAxis: {
					categories: YCategories,
					title: null
				},
				colorAxis: {
					min: 0,
					minColor: "#FFFFFF",
					//maxColor: Highcharts.getOptions().colors[0]
					maxColor: "#FF0000"
				},
				plotOptions: {
					heatmap: {
						events: {
							click: function (e) {
								table = tables[e.point.x];
								city = YCategories[e.point.y];
								value = e.point.value;
								params.city = city;
								params.table = table;
								params.currValue = value;
								if (table == "ossInfo") {
									$("#searchType").css("display", "block");
									$("#checkType").css("display", "none");
									$("#UnidirectionalNeighborCell").css("display", "none");
									$("#ExternalGeranCell").css("display", "none");
									$("#LatlonCheckWhiteList").css("display", "none");

									consistencyCheckDetailsSearch_1(params);
								} else if (table == "TempEUtranCellRelationUnidirectionalNeighborCell") {
									$("#searchType").css("display", "block");
									$("#checkType").css("display", "none");
									$("#UnidirectionalNeighborCell").css("display", "none");
									$("#ExternalGeranCell").css("display", "none");
									$("#LatlonCheckWhiteList").css("display", "none");
									tableRow = "TempEUtranCellRelationUnidirectionalNeighborCell";
									// $("#tableName").val("TempEUtranCellRelationUnidirectionalNeighborCell");

									consistencyCheckDetailsSearch(params);
								} else if (table == "TempParameter2GKgetCompare") {
									$("#searchType").css("display", "none");
									$("#checkType").css("display", "none");
									$("#UnidirectionalNeighborCell").css("display", "none");
									$("#ExternalGeranCell").css("display", "block");
									$("#LatlonCheckWhiteList").css("display", "none");

									consistencyCheckDetailsSearch(params);
								} else if (table == "latloncheck") {
									$("#searchType").css("display", "none");
									$("#checkType").css("display", "none");
									$("#UnidirectionalNeighborCell").css("display", "block");
									$("#ExternalGeranCell").css("display", "none");
									$("#LatlonCheckWhiteList").css("display", "none");
									tableRow = "latloncheck";
									// $("#tableName").val("latloncheck");
									consistencyCheckDetailsSearch(params);
								} else if (table == "TempEUtranCellFreqRelation"){
									$("#searchType").css("display", "none");
									$("#checkType").css("display", "none");
									$("#UnidirectionalNeighborCell").css("display", "none");
									$("#ExternalGeranCell").css("display", "none");
									$("#LatlonCheckWhiteList").css("display", "none");
									$("#TempEUtranCellFreqRelation").css("display", "block");
									tableRow = "TempEUtranCellFreqRelation";
									consistencyCheckDetailsSearch(params);
								} else {
									$("#searchType").css("display", "none");
									$("#checkType").css("display", "block");
									$("#UnidirectionalNeighborCell").css("display", "none");
									$("#ExternalGeranCell").css("display", "none");
									$("#LatlonCheckWhiteList").css("display", "none");

									consistencyCheckDetailsSearch(params);
								}
								;

							}
						}
					}
				},
				legend: {
					align: "right",
					layout: "vertical",
					margin: 0,
					verticalAlign: "top",
					y: 25,
					symbolHeight: 280
				},
				tooltip: {
					formatter: function () {
						return "<b>" + this.series.xAxis.categories[this.point.x] + "<br></b> 数量 <b>" +
							this.point.value + "<br>" + this.series.yAxis.categories[this.point.y] + "</b>";
					}
				},
				credits: {
					enabled: false,
				},
				series: [{
					name: "",
					borderWidth: 1,
					data: data,
					dataLabels: {
						enabled: true,
						color: "#000000"
					}
				}]
			});
		}
	});
};
function hex(x) {
	return ("0" + parseInt(x).toString(16)).slice(-2);
}
function RGB2HEX(rgb) {
	rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
	return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}
function consistencyCheckDetailsSearch(params) {
	if (params.currValue == 0) {
		$("#export").attr("disabled", true);
		//alert("没有记录");
		layer.open({
			title: "提示",
			content: "没有记录"
		});
		return;
	} else {
		var E = Ladda.create(document.getElementById("export"));
		E.start();
		var fieldArr = [];
		$.post("consistencyCheck/getTableField", params, function (data) {
			E.stop();
			$(tableId).grid("destroy", true, true);
			if (data.result == "error") {
				$("#export").attr("disabled", true);
				//alert("没有记录");
				layer.open({
					title: "提示",
					content: "没有记录"
				});
				return;
			} else {
				for (var k in data) {
					if (k == "mo" || k == "geranFreqGroupRef") {
						fieldArr[fieldArr.length] = {field: k, title: k, width: 600};
					} else if (k == "remark2" || k == "remark1") {
						fieldArr[fieldArr.length] = {field: k, title: k, width: 250};
					} else if (k == "moName") {
						fieldArr[fieldArr.length] = {field: k, title: k, width: 300};
					} else {
						fieldArr[fieldArr.length] = {field: k, title: k, width: textWidth(k)};
					}
				}
				$(tableId).grid("destroy", true, true);
				$(tableId).grid({
					columns: fieldArr,
					dataSource: {url: "consistencyCheck/getItems", type: "post", data: params},
					//primaryKey: "id",
					pager: {limit: 10, sizes: [10, 20, 50, 100]},
					autoScroll: true,
					uiLibrary: "bootstrap",
				});
			}
		});
	}
}
function consistencyCheckExportTofile() {
	var E = Ladda.create(document.getElementById("export"));
	E.start();
	var params = getParams();
	if (params == false) {
		return false;
	}
	params.city = city;
	params.table = table;

	$.post("consistencyCheck/exportFile", params, function (data) {
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
function consistencyCheckExportTemplate() {
	var E = Ladda.create(document.getElementById("exportTemplate"));
	E.start();
	var params = getParams();
	if (params == false) {
		return false;
	}
	params.city = city;
	params.table = table;

	$.post("consistencyCheck/exportTemplate", params, function (data) {
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
function exportDT(choice) {
	var E = Ladda.create(document.getElementById(choice));
	E.start();
	var params = getParams();
	if (params == false) {
		return false;
	}
	params.city = city;
	params.table = choice;
	$.post("consistencyCheck/exportDT", params, function (data) {
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
function consistencyCheckDetailsSearch_1(params) {
	if (params.currValue == 0) {
		$("#export").attr("disabled", true);
		//alert("没有记录");
		layer.open({
			title: "提示",
			content: "没有记录"
		});
		return;
	} else {
		var l = Ladda.create(document.getElementById("search_1"));
		var E = Ladda.create(document.getElementById("export_1"));
		l.start();
		E.start();
		if (!params) {
			var params = getParams();
			if (params == false) {
				return false;
			}
			params.city = city;
			params.table = table;
		}
		params.erbs = $("#erbs").val();
		params.eNBId = $("#eNBId").val();
		params.cell = $("#cell").val();
		params.ecgi = $("#ecgi").val();
		params.ip = $("#ip").val();
		var fieldArr = [];
		$.post("consistencyCheck/getTableField", params, function (data) {
			l.stop();
			E.stop();
			$(tableId).grid("destroy", true, true);
			if (data.result == "error") {
				$("#export").attr("disabled", true);
				//alert("没有记录");
				layer.open({
					title: "提示",
					content: "没有记录"
				});
				return;
			} else {
				for (var k in data) {
					if (k == "mo") {
						fieldArr[fieldArr.length] = {field: k, title: k, width: 600};
					} else if (k == "remark2" || k == "remark1") {
						fieldArr[fieldArr.length] = {field: k, title: k, width: 250};
					} else if (k == "moName") {
						fieldArr[fieldArr.length] = {field: k, title: k, width: 300};
					} else {
						fieldArr[fieldArr.length] = {field: k, title: k, width: textWidth(k)};
					}
				}
				$(tableId).grid("destroy", true, true);
				$(tableId).grid({
					columns: fieldArr,
					dataSource: {url: "consistencyCheck/getOssInfoItems", type: "post", data: params},
					//primaryKey: "id",
					pager: {limit: 10, sizes: [10, 20, 50, 100]},
					autoScroll: true,
					uiLibrary: "bootstrap",
				});
			}
		});
	}

}
function textWidth(text) {
	var length = text.length;
	if (length > 15) {
		return length * 10;
	}
	return 150;
}
function consistencyCheckExportTofile_1() {
	var l = Ladda.create(document.getElementById("search_1"));
	var E = Ladda.create(document.getElementById("export_1"));
	l.start();
	E.start();
	var params = getParams();
	if (params == false) {
		return false;
	}
	params.city = city;
	params.table = table;
	params.erbs = $("#erbs").val();
	params.eNBId = $("#eNBId").val();
	params.cell = $("#cell").val();
	params.ecgi = $("#ecgi").val();
	params.ip = $("#ip").val();
	$.post("consistencyCheck/exportOssInfoFile", params, function (data) {
		l.stop();
		E.stop();
		if (data.result) {
			fileDownload(data.filename);
		} else {
			//alert("没有记录");
			layer.open({
				title: "提示",
				content: "没有记录"
			});
		}
	});
}
function getParams() {
	var task = $(parameterAnalysisDateId).val();
	if (task == null) {
		task = getYesterdayDate("kget");
		$(parameterAnalysisDateId).val(task).trigger("change");
	}
	var treeSelected = $(consistencyTreeId).treeview("getSelected");
	if (treeSelected == "") {
		//alert("请选择相应的检查类型");
		layer.open({
			title: "提示",
			content: "请选择相应的检查类型"
		});
		return false;
	}
	var type = treeSelected[0].value;
	if (type == "") {
		//alert("请选择相应的检查类型");
		layer.open({
			title: "提示",
			content: "请选择相应的检查类型"
		});
		return false;
	}
	var params = {
		db: task,
	};
	return params;
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
function importUnidirectionalNeighborCell() {
	$("#import_modal").modal();
	$("#fileImportName").val("");
	$("#fileImport").val("");
}

function toName(self) {
	$("#fileImportName").val(self.value);
}
function importFile() {
	var task = $(parameterAnalysisDateId).val();
	if (task == null) {
		task = getYesterdayDate("kget");
		$(parameterAnalysisDateId).val(task).trigger("change");
	}
	// alert(tableRow);
	// var tableRow = $("#tableName").val();
	var whiteListTable = "";
	if (tableRow == "TempEUtranCellRelationUnidirectionalNeighborCell") {
		whiteListTable = "UnidirectionalNeighborCell_Template";
	}else if (tableRow == "TempEUtranCellFreqRelation"){
		whiteListTable = "TempEUtranCellFreqRelationWhiteList";
	} else {
		whiteListTable = "latlonCheckWhiteList";
	}

	var params = {
		table: whiteListTable,
		db: task,
		city: city
	};
	var l = Ladda.create(document.getElementById("import"));
	l.start();
	$.ajaxFileUpload({
		//url : "consistencyCheck/uploadFile",
		url: "consistencyCheck/uploadFile",
		data: params,
		fileElementId: "fileImport",
		secureuri: true,
		dataType: "json",
		type: "post",
		success: function (data, status) {
			params.fileName = data;
			$.post("consistencyCheck/getFileContent", params, function (data) {
				$("#import_modal").modal("hide");
				var params = {};
				params.city = city;
				params.table = tableRow;
				params.db = task;
				consistencyCheckSearch(XCategories, YCategories, tables);
				consistencyCheckDetailsSearch(params);
				//alert("上传成功");
				layer.open({
					title: "提示",
					content: "上传成功"
				});
				l.stop();
			});
		},
		error: function (data, status, e) {
			//alert("上传失败");
			layer.open({
				title: "提示",
				content: "上传失败"
			});
			l.stop();
		}
	});
}
function exportFiles()
{	
	var E = Ladda.create(document.getElementById("exportAllcontent"));
	E.start();
	var task = $(parameterAnalysisDateId).val();
	
	if(!data_value){
		layer.open({
			title: "提示",
			content: "请选择相应的检查类型"
		});
		E.stop();
		return;
	}
	var treeSelected = $(consistencyTreeId).treeview("getSelected")[0].text;
	var params = {
		treeName:treeSelected,
		tables: data_table,
		db: task,
		city:data_city
	};
	
	$.ajax({
		type:"post",
		url:"consistencyCheck/exportFiles",
		data:params,
		success:function(data){
			E.stop();
			// console.log(data);
			var name=JSON.parse(data);
			var filepath = name.filename.replace("\\", "");
			download(filepath, "", "data:text/csv;charset=utf-8");
		}
	})
	// console.log(task);
};

