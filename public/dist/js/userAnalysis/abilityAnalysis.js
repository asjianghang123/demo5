var num;
var queryBtn;
$(function () {
	toogle("abilityAnalysis");
	setTime();
	initCitys();
	
});

function setTime() {
	$("#startTime").datepicker({format: "yyyy-mm-dd"});  //返回日期
    var nowTemp = new Date();
    var year = nowTemp.getFullYear();
    var month = nowTemp.getMonth() + 1;
    var day = nowTemp.getDate();
    var today = year + "-" + month + "-" + day;

    console.log(today);
    var params = {
        city: getFirstCity()
    };
    $.get("abilityAnalysis/getTime", params, function (data) {
        var sdata = [];
        for (var i = 0; i < data.length; i++) {
            if (data[i] === today) {
                continue;
            }
            sdata.push(data[i]);
        }
        sdata.push(today);
        console.log(sdata);
        $("#startTime").datepicker("setValues", sdata);
    });

    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var checkin = $("#startTime").datepicker({
        onRender: function (date) {
            return date.valueOf() < now.valueOf() ? "" : "";
        }
    }).on("changeDate", function (ev) {
        checkin.hide();
    }).data("datepicker");
}
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
	$.get("abilityAnalysis/getCitys", null, function (data) {
		data = JSON.parse(data);
		var newData = [];
		for (var i in data) {
			var CHCity = data[i].split("-")[0];
			var dataBase = data[i].split("-")[1];
			newData.push({"label": CHCity, "value": dataBase});
		}
		$("#citys").multiselect("dataprovider", newData);
	});
}
function query() {
	var city = $("#citys").val();
	queryBtn = Ladda.create(document.getElementById("queryBtn"));
	queryBtn.start();
	num = 0;
	var text = "FDD,Value,Total,Share";
	var date = $("#startTime").val();
	if(city.substring(0,3) == "CDR") {
		getTableData(text, "abilityAnalysis/getTableData", "#tableData", city);
		getChartData("abilityAnalysis/getChartData", "#chartData", city);

		text = "EUTRA,Value,Total,Share";
		getTableData(text, "abilityAnalysis/bandEutraData", "#table-bandEutra", city);
		getChartData("abilityAnalysis/bandEutraChart", "#chart-bandEutra", city);
		//////bandEutraChart("abilityAnalysis/bandEutraChart","#chart-bandEutra","BandEUTRA能力",city);//能力分析
		text = "FGI,Value,Total,Share";
		getTableData(text, "abilityAnalysis/FGIData", "#table-FGI", city);
		barChart("abilityAnalysis/FGIChart", "#chart-FGI", "渗透率", city);
	}else {
		// getTableData(text, "abilityAnalysis/getTableData", "#tableData", city, date);
		getChartData("abilityAnalysis/getChartData", "#chartData", city, date);
		getChartData("abilityAnalysis/TDDChart", "#chart-bandEutra", city, date);
	}

	
}
function getTableData(text, route, block, city, date) {
	date = date || null;
	var fieldArr = [];

	var textArr = text.split(",");
	for (var i in textArr) {
		fieldArr[fieldArr.length] = {field: textArr[i], title: textArr[i], width: 200};
	}
	$(block).grid("destroy", true, true);
	$.get(route, {"city": city,"date":date}, function (returnData) {
		/*if (++num == 4) {
			queryBtn.stop();
		}
		returnData = JSON.parse(returnData);
		var grid = $(block).grid({
			columns: fieldArr,
			dataSource: returnData.records,
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap",
			autoLoad: true
		});*/
	});
}
function getChartData(route, block, city, date) {
	date = date || null;
	$(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
	$.get(route, {"city": city,"date":date}, function (data) {

		$(block).highcharts({
			chart: {
				type: "bar"
			},
			title: {
				text: "渗透率"
			},
			tooltip: {
				pointFormat: "{point.y}%"
			},
			xAxis: {
				categories: data["categories"],
				title: {
					text: null
				}
			},
			legend: {
				enabled: false,
				layout: "vertical",
				align: "right",
				verticalAlign: "top",
				x: -40,
				y: 80,
				floating: true,
				borderWidth: 1,
				backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || "#FFFFFF"),
				shadow: true
			},
			credits: {enabled: false},
			plotOptions: {
				bar: {
					allowPointSelect: true,
					cursor: "pointer",
					dataLabels: {
						enabled: true,
						color: "#000000",
						connectorColor: "#000000",
						format: "<b>{point.name}</b> {point.y}%"
					}
				}

			},
			series: [data["series"]]
		});
		// if (++num == 4) {
			queryBtn.stop();
		// }
	});
}

function switchTab(div1, div2) {
	$(div2).removeClass("active");
	$(div1).addClass("active");
}
function bandEutraChart(route, block, title, city) {
	$.ajax({
		type: "GET",
		url: route,
		data: {"city": city},
		dataType: "json",
		beforeSend: function () {
			$(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
		},
		success: function (data) {
			if (++num == 4) {
				queryBtn.stop();
			}
			$(block).html("");
			createBandEutraChart(data, block, title);
		}
	});
}
function createBandEutraChart(data, block, title) {
	$(block).highcharts({
		chart: {
			polar: true
		},
		title: {
			text: title
		},

		xAxis: {
			categories: data.categories

		},
		tooltip: {
			pointFormat: ":{point.y}"
		},
		legend: {enabled: false},
		credits: {enabled: false},
		series: [{
			data: data.series
		}]
	});
}
function barChart(route, block, title, city) {
	$.ajax({
		type: "GET",
		url: route,
		data: {"city": city},
		dataType: "json",
		beforeSend: function () {
			$(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
		},
		success: function (data) {
			if (++num == 4) {
				queryBtn.stop();
			}
			$(block).html("");
			createBarChart(data, block, title);
		}
	});
}
function createBarChart(data, block, title) {
	var height = 400;
	if (data["categories"].length > 8) {
		height = data["categories"].length * 30;
	}
	;
	$(block).css("height", height + "px");
	$(block).highcharts({
		chart: {
			type: "bar"
		},
		title: {
			text: title
		},
		tooltip: {
			pointFormatter: function () {

				return getTip(this.category);

			}
		},
		xAxis: {
			categories: data["categories"],
			title: {
				text: null
			}
		},
		legend: {
			enabled: false,
			layout: "vertical",
			align: "right",
			verticalAlign: "top",
			x: -40,
			y: 80,
			floating: true,
			borderWidth: 1,
			backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || "#FFFFFF"),
			shadow: true
		},
		credits: {enabled: false},
		plotOptions: {
			bar: {
				allowPointSelect: true,
				cursor: "pointer",
				dataLabels: {
					enabled: true,
					color: "#000000",
					connectorColor: "#000000",
					format: "<b>{point.name}</b> {point.y}"
				}
			}

		},
		series: [data["series"]]
	});
}
function getTip(category) {
	var tip = "";
	switch (category) {
		case "featGroupInd1":
			tip = "Intra-subframe freq hopping for PUSCH scheduled by UL grant; DCI format 3a; PDSCH transmission mode 5; Aperiodic CQI/PMI/RI report on PUSCH: Mode 2-0 & 2-2";
			break;
		case"featGroupInd2":
			tip = "Simultaneous CQI & ACK/NACK on PUCCH (format 2a/2b); Absolute TPC command for PUSCH; Resource alloc type 1 for PDSCH; Periodic CQI/PMI/RI report on PUCCH: Mode 2-0 & 2-1";
			break;
		case"featGroupInd3":
			tip = "5bit RLC UM SN; 7bit PDCP SN";
			break;
		case"featGroupInd4":
			tip = "Short DRX cycle";
			break;
		case"featGroupInd5":
			tip = "Long DRX cycle; DRX command MAC control element";
			break;
		case"featGroupInd6":
			tip = "Prioritised bit rate";
			break;
		case"featGroupInd7":
			tip = "RLC UM";
			break;
		case"featGroupInd8":
			tip = "EUTRA RRC_CONNECTED to UTRA CELL_DCH PS handover";
			break;
		case"featGroupInd9":
			tip = "EUTRA RRC_CONNECTED to GERAN GSM_Dedicated handover";
			break;
		case"featGroupInd10":
			tip = "EUTRA RRC_CONNECTED to GERAN (Packet_) Idle by Cell Change Order; EUTRA RRC_CONNECTED to GERAN (Packet_) Idle by Cell Change Order with NACC";
			break;
		case"featGroupInd11":
			tip = "EUTRA RRC_CONNECTED to CDMA2000 1xRTT CS Active handover";
			break;
		case"featGroupInd12":
			tip = "EUTRA RRC_CONNECTED to CDMA2000 HRPD Active handover";
			break;
		case"featGroupInd13":
			tip = "Inter-frequency handover (within FDD or TDD)";
			break;
		case"featGroupInd14":
			tip = "Measurement reporting event: Event A4 - Neighbour > threshold; Measurement reporting event: Event A5 - Serving < threshold1 & Neighbour > threshold2";
			break;
		case"featGroupInd15":
			tip = "Measurement reporting event: Event B1 - Neighbour > threshold";
			break;
		case"featGroupInd16":
			tip = "non-ANR related periodical measurement reporting";
			break;
		case"featGroupInd17":
			tip = "ANR related intra-frequency measurement reporting events";
			break;
		case"featGroupInd18":
			tip = "ANR related inter-frequency measurement reporting events";
			break;
		case"featGroupInd19":
			tip = "ANR related inter-RAT measurement reporting events";
			break;
		case"featGroupInd20":
			tip = "SRB1 and SRB2 for DCCH + 8x AM DRB; SRB1 and SRB2 for DCCH + 5x AM DRB + 3x UM DRB (if indicator 7 is supported)";
			break;
		case"featGroupInd21":
			tip = "Predefined intra- and inter-subframe frequency hopping for PUSCH with N_sb > 1; Predefined inter-subframe frequency hopping for PUSCH with N_sb > 1";
			break;
		case"featGroupInd22":
			tip = "UTRAN measurements, reporting and measurement reporting event B2 in E-UTRA connected mode";
			break;
		case"featGroupInd23":
			tip = "GERAN measurements, reporting and measurement reporting event B2 in E-UTRA connected mode";
			break;
		case"featGroupInd24":
			tip = "1xRTT measurements, reporting and measurement reporting event B2 in E-UTRA connected mode";
			break;
		case"featGroupInd25":
			tip = "Inter-frequency measurements and reporting in E-UTRA connected mode";
			break;
		case"featGroupInd26":
			tip = "HRPD measurements, reporting and measurement reporting event B2 in E-UTRA connected mode";
			break;
		case"featGroupInd27":
			tip = "EUTRA RRC_CONNECTED to UTRA CELL_DCH CS handover";
			break;
		case"featGroupInd28":
			tip = "TTI bundling";
			break;
		case"featGroupInd29":
			tip = "Semi-Persistent Scheduling";
			break;
		case"featGroupInd30":
			tip = "Handover between FDD and TDD";
			break;
		case"featGroupInd31":
			tip = "Undefined";
			break;
		case"featGroupInd32":
			tip = "Undefined";
			break;
	}
	return tip;
}