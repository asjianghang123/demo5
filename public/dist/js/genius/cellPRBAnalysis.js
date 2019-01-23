$(document).ready(function () {
	setTime();
	// initTimeList();
	toogle("cellPRBAnalysis");
});

function setTime() {
	$("#dateTime").datepicker({format: "yyyy-mm-dd"});  //返回日期
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;

	//console.log(today);
	var params = {
		city: getFirstCity()
	};
	$.get("cellPRBAnalysis/getPRBTime", params, function (data) {
		var sdata = [];
		for (var i = 0; i < data.length; i++) {
			if (data[i] === today) {
				continue;
			}
			sdata.push(data[i]);
		}
		sdata.push(today);
		$("#dateTime").datepicker("setValues", sdata);
	});
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

	var checkin = $("#dateTime").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");
}
function query() {
	setFrequencyChart();
}
// function initTimeList(){
//     $('#timeList').multiselect({
//         //dropRight: true,
//         buttonWidth: '100%',
//         //enableFiltering: true,
//         nonSelectedText:'选择时间',
//         //filterPlaceholder:'搜索',
//         //nSelectedText:'项被选中',
//         includeSelectAllOption:true,
//         //selectAllText:'全选/取消全选',
//         //allSelectedText:'已选中所有城市',
//         maxHeight:200,
//         maxWidth:'100%'
//     });
// }
// function getTimeList(){
//     var timestamp = new Date().getTime();
//     var urlStr = 'common/json/highInterfereCell_ChartTime_'+timestamp.toString()+'.json';
//     // var cell_str = JSON.stringify(rowData.cell);
//     // var cell = cell_str.replace(/"/g,"");
//     var cell = $("#cell").val();
//     var dateTime = $("#dateTime").val();
//     var params={
//         dateTime:dateTime,
//         cell:cell,
//         url:urlStr
//     };
//     var url = "cellPRBAnalysis/getTimeList";
//     $.post(url,params,function(data){
//         data = JSON.parse(data);
//         $('#timeList').multiselect('dataprovider', data);
//         setFrequencyChart();
//         $('#timeList').on("change",function(){
//             $("#chart2_zhaozi").show();
//             $("#chart2_loadingImg").show();
//             setFrequencyChart();
//         })

//     });
// }
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
function setFrequencyChart() {
	// var dateStr = $('#timeList').val();
	var day_id = $("#dateTime").val();
	// var hour_id = (((dateStr.split(' '))[1]).split(':'))[0];

	var table = "interfereCell";
	// var rowData_str = JSON.stringify(rowData.cell);
	// var click_cell_name = rowData_str.replace(/"/g,"");
	// var subNetwork_str = JSON.stringify(rowData.subNetwork);
	// subNetwork_str = subNetwork_str.replace(/"/g,"");
	var cell = $("#cell").val();
	var params = {
		table: table,
		cell: cell,
		day_id: day_id,
		// hour_id:hour_id
	};
	$.post("cellPRBAnalysis/getPRBAnalysisData", params, function (data) {
		var cat_str = JSON.stringify(JSON.parse(data).categories);
		var ser_str = JSON.stringify(JSON.parse(data).series);
		var subNetwork = JSON.parse(data).subNetwork;

		//ser_str=ser_str.replace(/"/g,"");

		ser_str = ser_str.replace("PRB", "'" + "PRB");
		ser_str = ser_str.replace("上行干扰电平", "上行干扰电平" + "'");
		ser_str = ser_str.replace("电平值", "'" + "电平值" + "'");

		var cat_obj = eval("(" + cat_str + ")");
		var ser_obj = eval("(" + ser_str + ")");

		$("#cellPRBAnalysis").highcharts({
			exporting: {
				enabled: true
			},
			chart: {
				type: "spline"
			},
			title: {
				text: "小区PRB分析"
			},
			subtitle: {
				text: day_id + " / " + subNetwork + " / " + cell
			},
			xAxis: {
				categories: cat_obj,
				crosshair: true
			},
			yAxis: {
				tickPositions: [-130, -120, -110, -100, -90, -80],
				title: {
					text: "电平值 (dBm)"
				}
			},
			tooltip: {
				headerFormat: '<span style="font-size:10px">{point.key}</span><table style="font-size:10px;white-space:nowrap">',
				pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
						'<td style="padding:0"><b>{point.y:.1f} dBm</b></td></tr>',
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
			plotOptions: {
				column: {
					pointPadding: 0.2,
					borderWidth: 0
				},
				spline: {
					lineWidth: 1.5,
					fillOpacity: 0.1,
					marker: {enabled: false, states: {hover: {enabled: true, radius: 2}}}, shadow: false
				}
			},
			credits: {
				enabled: false
			},
			series: ser_obj
		});
		// $("#chart2_zhaozi").hide();
		// $("#chart2_loadingImg").hide();
	});
}