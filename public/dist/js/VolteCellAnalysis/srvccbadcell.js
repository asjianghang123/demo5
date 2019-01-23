var alarmpolar;
var rsrqpolar;
var rsrq;
var overlapcover;
var overlapcoverpolar;
var neight2Gcellnum;
var neight2Gcellnumpolar;
var canshu;
var canshupolar;
var esrvccpolar;
var ctrCity;
var selectCell;
var startTime;
var layerout = null;
var layerin = null;
var switchFlag = "out";
$(function(){
	toogle("srvccbadcell");
	getAllCity();
	startTime = getYestdayFormatDate();
	switchRadio();
	initTable(); 

	$("#ctrJump").on("shown.bs.modal", function () {
		initTable();//wtf
		var saveBtn = Ladda.create(document.getElementById("runBtn"));
		$("#log").html("");
		$("#taskName").val("");
		var data = $("#ctrData").html();
		var returnData = JSON.parse(data);
		$("#fileTable").treegrid("loadData", returnData);
		saveBtn.stop();
	});
	
});
$(document).ready(function() {
	// setTime();
	getAllCity();   
	//设置表格
	//setTable();
	var inputType = $("#inputCategory").val();

	$("#worstCellChartAuxiliaryAxisType").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText:"请选择两项及以内",
		//filterPlaceholder:"搜索",
		nSelectedText:"项被选中",
		// includeSelectAllOption:true,
		// selectAllText:"全选/取消全选",
		// allSelectedText:"已选中所有平台类型",
		maxHeight:200,
		maxWidth:"100%"
	});
	$("#worstCellChartPrimaryAxisType").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		// nonSelectedText:"请选择两项及以内",
		//filterPlaceholder:"搜索",
		nSelectedText:"项被选中",
		// includeSelectAllOption:true,
		// selectAllText:"全选/取消全选",
		allSelectedText:"已选中所有平台类型",
		maxHeight:200,
		maxWidth:"100%"
	});
	

	// initTable();   //这是什么原理？显示bug？
	// $("#ctrJump").on("shown.bs.modal", function () {
	// 	initTable();//wtf
	// 	var saveBtn = Ladda.create(document.getElementById("runBtn"));
	// 	saveBtn.stop();
	// 	$("#log").html("");
	// 	$("#taskName").val("");
	// 	var data = $("#ctrData").html();
	// 	var returnData = JSON.parse(data);
	// 	$("#fileTable").treegrid("loadData", returnData);
	// });
});
function getParams(){
	var type  = "badHandoverCell_ex";
	var citys = $("#allCity").val();
	if(citys == null){
		layer.open({
			title: "提示",
			content: "请选择城市"
		});
		return false;
	}
	var params = {
		city:citys,
		table:type
	};
	return params;
}
function doSearchbadCell(table) {
	var l = Ladda.create( document.getElementById( "search" ) );
	var E = Ladda.create( document.getElementById( "export" ) );
	l.start();
	E.start();
	var params = getParams();
	// console.log(params);
	if(params == false){
		l.stop();
		E.stop();
		return false;
	}
	$.get("srvccbadcell/templateQuery", params, function(data){
		var fieldArr=[];
		var text=(JSON.parse(data).content).split(",");
		var filename = JSON.parse(data).filename;
		$("#badCellFile").val(filename);

		for(var i in text){  
			fieldArr[i]={field:text[i],title:text[i],width:150,sortable:true};
		}
		var newData = JSON.parse(data).rows;
		$("#badCellTable").grid("destroy", true, true);
		var badCellTable = $("#badCellTable").grid({
			columns:fieldArr,
			dataSource:newData,
			pager: { limit: 10, sizes: [10, 20, 50, 100] },
			autoScroll:true,
			uiLibrary: "bootstrap",
		});
		l.stop();
		E.stop();
		if(table == "file"){
			filename = $("#badCellFile").val();
			fileZipSave(filename);
		}
		badCellTable.on("rowSelect", function (e, $row, id, record) {
			// console.log(record);
			ctrCity = record.city;
			getCTRJump(record);//ctr入库
			selectCell = record.cell;
			alarm(record.cell, record.city);//告警
			getRsrq(record.cell);//下行覆盖
			getoverlapcover(record.cell, record.city);//重叠覆盖
			neight(record.cell, record.city, "mreServerNeighIrat_day", "2G");//2G邻区
			parameter(record.cell, record.city);//参数
			light();//亮灯
			report(record);//极地图显示报告

		});
	});
	
}
function openConfigInfo(){
	$("#config_information").modal();
}
function alarm(cell, city) {
	// var cell = record.cell;
	// var city = record.city;
	var params = {
		cell: cell,
		city: city
	}
	$.ajax({
		type:"get",
		url:"srvccbadcell/getcurrentAlarmNum",
		data: params,
		dataType:"json",
		async : false,
		success:function(data){
			alarmpolar = data["Polar-当前告警"];
			// console.log(data);
			$("#currentAlarm").val(data["当前告警数量"]+"条"); 
			if(data["Polar-当前告警"] == 0) {   //告警  
				$("#currentAlarmNum").removeClass();
				$("#currentAlarmNum").addClass("glyphicon glyphicon-ok-circle");
				$("#currentAlarmNum").css("color","green");
			}else if(data["Polar-当前告警"] >0 && data["Polar-当前告警"] < 50) {
				$("#currentAlarmNum").removeClass();
				$("#currentAlarmNum").addClass("glyphicon glyphicon-exclamation-sign");
				$("#currentAlarmNum").css("color","orange");
			}else if(data["Polar-当前告警"] >= 50) {
				$("#currentAlarmNum").removeClass();
				$("#currentAlarmNum").addClass("glyphicon glyphicon-remove-sign");
				$("#currentAlarmNum").css("color","red");
			}
		}
	});
}

function getRsrq(cell, city) {
	// var cell = record.cell;
	// var city = record.city;
	var params = {
		cell: cell
	}
	$.ajax({
		type:"get",
		url:"srvccbadcell/getRsrq",
		data: params,
		dataType:"json",
		async : false,
		success:function(data){
			rsrq = data["RSRQ<-15.5的比例"];
			$("#less155Proportion").val(rsrq + "%");
		}
	});
}
function parameter(cell, city) {
	var params = {
		cell: cell,
		city: city
	};
	$.ajax({
		type:"get",
		url:"volteupbadcell/parameter",
		data: params,
		dataType:"json",
		async : false,
		success:function(data){
			canshu = data["参数"];
			canshupolar = data["Polar-参数"];
			if (canshu) {
				$("#parameter").val(canshu+"个");
			} else {
				$("#parameter").val(0+"个");
			}
		}
	});
}
function getoverlapcover(cell, city) {
	// var cell = record.cell;
	// var city = record.city;
	var params = {
		cell: cell,
		city: city
	}
	$.ajax({
		type:"get",
		url:"srvccbadcell/overlapcover",
		data: params,
		dataType:"json",
		async : false,
		success:function(data){
			overlapcover = data["重叠覆盖度"];
			overlapcoverpolar = data["Polar-重叠覆盖"];
			if (overlapcover) {
				$("#overlapCover").val(overlapcover+"%");
			} else {
				$("#overlapCover").val("");
			}
		}
	});
}
function neight(cell, city, table, flag) {
	var params = {
		cell: cell,
		city: city,
		table: table,
		flag: flag
	}
	$.ajax({
		type:"get",
		url:"srvccbadcell/neightcell",
		data: params,
		dataType:"json",
		async : false,
		timeout : 100,
		success:function(data){
			neight2Gcellnum = data["邻区数量"];
		}
	});
}
function light(){
	if (neight2Gcellnum > 6) {
		$("#needAddNeighNum").removeClass();
		$("#needAddNeighNum").addClass("glyphicon glyphicon-remove-sign");
		$("#needAddNeighNum").css("color","red");
		neight2Gcellnumpolar = 100;
	} else if (neight2Gcellnum > 6 && neight2Gcellnum <= 12) {
		$("#needAddNeighNum").removeClass();
		$("#needAddNeighNum").addClass("glyphicon glyphicon-exclamation-sign");
		$("#needAddNeighNum").css("color","orange");
		neight2Gcellnumpolar = 50+(neight2Gcellnum-6)*7;
	} else {
		$("#needAddNeighNum").removeClass();
		$("#needAddNeighNum").addClass("glyphicon glyphicon-ok-circle");
		$("#needAddNeighNum").css("color","green");
		neight2Gcellnumpolar = 0;
	}//2G邻区
	if (overlapcoverpolar == 0) {
		$("#overlapCoverNum").removeClass();
		$("#overlapCoverNum").addClass("glyphicon glyphicon-ok-circle");
		$("#overlapCoverNum").css("color","green");
	} else if (overlapcoverpolar == 50) {
		$("#overlapCoverNum").removeClass();
		$("#overlapCoverNum").addClass("glyphicon glyphicon-exclamation-sign");
		$("#overlapCoverNum").css("color","orange");
	} else if (overlapcoverpolar == 100) {
		$("#overlapCoverNum").removeClass();
		$("#overlapCoverNum").addClass("glyphicon glyphicon-remove-sign");
		$("#overlapCoverNum").css("color","red");
	}//重叠覆盖
	if (overlapcoverpolar > 0) {
		$("#less155ProportionNum").removeClass();
		$("#less155ProportionNum").addClass("glyphicon glyphicon-exclamation-sign");
		$("#less155ProportionNum").css("color","orange");
		rsrqpolar = 50;
	} else {
		if (rsrq>10 && rsrq<=20) {
			$("#less155ProportionNum").removeClass();
			$("#less155ProportionNum").addClass("glyphicon glyphicon-exclamation-sign");
			$("#less155ProportionNum").css("color","orange");
			rsrqpolar = 50;
		} else if(rsrq>20) {
			$("#less155ProportionNum").removeClass();
			$("#less155ProportionNum").addClass("glyphicon glyphicon-remove-sign");
			$("#less155ProportionNum").css("color","red");
			rsrqpolar = 100;
		} else {
			$("#less155ProportionNum").removeClass();
			$("#less155ProportionNum").addClass("glyphicon glyphicon-ok-circle");
			$("#less155ProportionNum").css("color","green");
			rsrqpolar = 0;
		}
	}//rsrq
	if (canshupolar == 50) {
		$("#parameterNum").removeClass();
		$("#parameterNum").addClass("glyphicon glyphicon-exclamation-sign");
		$("#parameterNum").css("color","orange");
	} else if (canshupolar == 100) {
		$("#parameterNum").removeClass();
		$("#parameterNum").addClass("glyphicon glyphicon-remove-sign");
		$("#parameterNum").css("color","red");
	} else {
		$("#parameterNum").removeClass();
		$("#parameterNum").addClass("glyphicon glyphicon-ok-circle");
		$("#parameterNum").css("color","green");
	}//参数
	esrvccpolar = 0;
}
function report(record){
	$("#container").highcharts({
        chart: {
            polar: true
        },
        title: {
            text: "极地图"
        },
        xAxis: {
            categories: ["告警","参数","重叠覆盖","下行质差","ESRVCC","邻区"],
            tickmarkPlacement: "on"
        },
        yAxis: {
            min: 0,
            max:100
        },
        series: [{
            name: "关联度",
            type:"area",
            data: [
                parseInt(alarmpolar),
                parseInt(canshupolar),
                parseInt(overlapcoverpolar),
                parseInt(rsrqpolar),
                parseInt(esrvccpolar),
                parseInt(neight2Gcellnumpolar)
            ],
            pointPlacement: "on",
            events: {
                click: function(e) {
                    if(e.point.category === "告警") {
                      openAlarmModel(record);   
                    }else if(e.point.category === "邻区") {
                      open2GNeighborCellModel(record);
                    }else if(e.point.category === "下行质差") {
                      openzhichaCellModel(record);
                    }else if(e.point.category === "重叠覆盖") {
                      openOverlapCoverModel(record);
                    }else if(e.point.category === "参数") {
                      openParameterModel(record);
                    }
                    // }else if(e.point.category === "ESRVCC") {
                    //   openEsrvccCellModel(alarmNum, record);         
                }
            }
        }]
    });
}

function getAllCity(){
	$("#allCity").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText:"请选择城市",
		//filterPlaceholder:"搜索",
		nSelectedText:"项被选中",
		includeSelectAllOption:true,
		selectAllText:"全选/取消全选",
		allSelectedText:"已选中所有平台类型",
		maxHeight:200,
		maxWidth:"100%"
	});
	var url = "srvccbadcell/getAllCity";
	$.ajax({
		type:"GET",
		url:url,
		dataType:"json",
		success:function(data){
			var newOptions = [];
			var obj = {};
			$(data).each(function(k,v){
				v = eval("("+v+")");
				obj = {
					label : v.text,
					value : v.value
				};
				newOptions.push(obj);
			});
			$("#allCity").multiselect("dataprovider", newOptions);
		}
	});
}
function fileZipSave(fileName) {
	if(fileName!=""){
		var fileNames = csvZipDownload(fileName);
		download(fileNames);
	}else{
		layer.open({
			title: "提示",
			content: "No file generated so far!"
		});
	}
}
function download(url) {
	var browerInfo = getBrowerInfo();
	if (browerInfo=="chrome"){
		download_chrome(url);
	}else if(browerInfo == "firefox") {
		download_firefox(url);
	}
}
function download_chrome(url){
	var aLink = document.createElement("a");
	aLink.href=url;
	aLink.download = url;
	document.body.appendChild(aLink);
	aLink.click();
}
function download_firefox(url){
	window.open(url);
}
function getBrowerInfo(){
	var uerAgent = navigator.userAgent.toLowerCase();
	var format =/(msie|firefox|chrome|opera|version).*?([\d.]+)/;
	var matches = uerAgent.match(format);
	return matches[1].replace(/version/, "'safari"); 
}
function openAlarmModel(record) {
	$("#config_information_alarm").modal();
	var cell = record.cell;
	var date = record.day_id;
	var city = record.city;
	var params = {
		cell:cell,
		date:date,
		city:city
	};
	$.get("voltereportcell/getAlarmCellModel", params, function(data){
		var fieldArr=[];
		var text=data.content.split(",");
		var filename = data.filename;
		for(var i in data.rows[0]){       
			fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:textWidth(i)};
		} 
		var newData = data.rows;
		$("#cellAlarmTable_model").grid("destroy", true, true);
		var alarmWorstCellTable = $("#cellAlarmTable_model").grid({
			columns:fieldArr,
			dataSource:newData,
			pager: { limit: 5, sizes: [5, 10, 15, 20] },
			autoScroll:true,
			uiLibrary: "bootstrap",
			primaryKey:"id"
		});
	});
	$.get("voltereportcell/getAlarmErbsModel", params, function(data){
		var fieldArr=[];
		var text=data.content.split(",");
		var filename = data.filename;
		for(var i in data.rows[0]){       
			fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:textWidth(i)};
		} 
		var newData = data.rows;
		$("#erbsAlarmTable_model").grid("destroy", true, true);
		var alarmWorstCellTable = $("#erbsAlarmTable_model").grid({
			columns:fieldArr,
			dataSource:newData,
			pager: { limit: 5, sizes: [5, 10, 15, 20] },
			autoScroll:true,
			uiLibrary: "bootstrap",
			primaryKey:"id"
		});
	});
}
function openzhichaCellModel(record) {
	$("#config_information_zhichaCell").modal();
	$("#getfirstZhichaCellTab a:first").tab("show");
	$("#zhichaCellWorstCellContainer").empty();
	var cat_obj,ser_obj,yAxisData;
	var cell = record.cell;
	var city = record.city;
	var date = record.day_id;
	var yAxis_name = "RSRQ<-15.5的比例";
	var paramsLTE_1 = {
		cell : cell,
		date: date,
		city : city,
		yAxis_name : "RSRQ<-15.5的比例",
		table : "lowAccessCell"
	};
	$.ajax({
		url:"voltereportcell/getZhichaCellChart",
		data:paramsLTE_1,
		type:"get",
		success:function(data){
			// console.log(data);
			var cat_str = JSON.stringify(JSON.parse(data).categories);
			var ser_str = JSON.stringify(JSON.parse(data).series);
			yAxisData = (JSON.parse(data).yAxis);
			var cell_str = JSON.parse(data).cell;
			ser_str=ser_str.replace(/"/g,"");
			ser_str=ser_str.replace("#89A54E","'#89A54E'");
			ser_str=ser_str.replace(yAxis_name,"'"+yAxis_name+"'");
			ser_str=ser_str.replace("column","'column'");
			ser_str=ser_str.replace("#4572A7","'#4572A7'");
			// console.log(cell);
			cat_obj = eval("("+cat_str+")");  
			// console.log(cat_obj);   
			ser_obj = eval("("+ser_str+")");
			console.log(yAxisData);
			$("#config_information_zhichaCell").on("shown.bs.modal",function(){
				var chart = Highcharts.chart("zhichaCellWorstCellContainer", {
					exporting: {   
						enabled:true,     
					},
					credits: {  
						enabled: false  
					},
					chart: {
						zoomType: "xy"
					},
					title: {
						text: cell
					},
					xAxis: [{
						categories: cat_obj
					}],
					yAxis: [{
						labels: {
							format: "{value} %",
							style: {
								color: "#4572A7"
							}
						},
						title: {
							text: yAxis_name,
							style: {
								color: "#4572A7"
							}
						},
						tickPositions: yAxisData
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
		}
	});
	$.get("voltereportcell/getzhichaCellModel", paramsLTE_1, function(data){
		var fieldArr=[];
		// console.log(data);
		var text=data.content.split(",");
		var filename = data.filename;
		for(var i in data.rows[0]){       
			fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:textWidth(i)};
		} 
		var newData = data.rows;
		$("#zhichaCell_model").grid("destroy", true, true);
		var alarmWorstCellTable = $("#zhichaCell_model").grid({
				columns:fieldArr,
				dataSource:newData,
				pager: { limit: 5, sizes: [5, 10, 15, 20] },
				autoScroll:true,
				uiLibrary: "bootstrap",
				primaryKey:"id"
			});
	}); 
}
function open2GNeighborCellModel(record) {
	var cell = record.cell;
	var date = record.day_id;
	var city = record.city;
	var paramsLTE_1 = {
		cell : cell,
		date : date,
		city : city
	};
	$.get("voltereportcell/getGSMNeighborDataModel", paramsLTE_1, function(data){
		var fieldArr=[];
		var text=data.content.split(",");
		var filename = data.filename;
		for(var i in data.rows[0]){       
			fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:textWidth(i)};
		} 
		var newData = data.rows;
		$("#neighborCell_model").grid("destroy", true, true);
		var alarmWorstCellTable = $("#neighborCell_model").grid({
			columns:fieldArr,
			dataSource:newData,
			pager: { limit: 5, sizes: [5, 10, 15, 20] },
			autoScroll:true,
			uiLibrary: "bootstrap",
			primaryKey:"id"
		});
	});
	$("#config_information_neighborCell").modal();
	$("#getNeighborCellTab a:first").tab("show");

	$("#neighborCellMap").empty();
	setTimeout(function(){
		window.mapv1 = initMap1("neighborCellMap");
		get2GNeighborCellMapData(paramsLTE_1);
	},300);
}
function openOverlapCoverModel(record) {
	var cell = record.cell;
	var date = record.day_id;
	var city = record.city;
	var paramsLTE_1 = {
		cell : cell,
		date : date,
		city : city
	};
	$.get("voltereportcell/getOverlapCoverModel", paramsLTE_1, function(data){
		var fieldArr=[];
		var text=data.content.split(",");
		var filename = data.filename;
		for(var i in data.rows[0]){       
			fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:textWidth(i)};
		} 
		var newData = data.rows;
		$("#overlapCoverCell_model").grid("destroy", true, true);
		var alarmWorstCellTable = $("#overlapCoverCell_model").grid({
			columns:fieldArr,
			dataSource:newData,
			pager: { limit: 5, sizes: [5, 10, 15, 20] },
			autoScroll:true,
			uiLibrary: "bootstrap",
			primaryKey:"dateTime_id"
		});
	});
	$("#config_information_overlapCoverCell").modal();
}
function openParameterModel(record){
	$(".baselineTableIndex").val("");
	var cell = record.cell;
	var date = record.date_id;
	var city = record.city;
	var params = {
		cell:cell,
		date:date,
		city:city
	};
	$.get("srvccbadcell/getBaselineCheckData", params,function(data){
		data = JSON.parse(data);
		for (var i in data) {
			if(data[i].record == 0){
				$("#baselineTableIndex_"+i).empty();
				$("#chanshu"+i).hide();
			}else{

				var fieldArr=[];
				var text=data[i].content.split(",");
				for(var j in text){
					fieldArr[fieldArr.length]={field:text[j],title:text[j],width:150,sortable:true}; 
				}
				$("#baselineTableIndex_"+i).grid("destroy", true, true);
				$("#baselineTableIndex_"+i).grid({
					columns:fieldArr,
					dataSource:data[i].rows,
					pager: { limit: 10, sizes: [10, 20, 50, 100]},
					autoScroll:true,
					uiLibrary: "bootstrap",
				});
			}
		}
	});
	$("#config_information_parameter").modal();
}
function initMap1(mapId) {
	var bmap = new BMap.Map(mapId);
	bmap.enableScrollWheelZoom(); // 启用滚轮放大缩小
	// 初始化地图,设置中心点坐标和地图级别
	bmap.centerAndZoom(new BMap.Point(120.602701, 32.227101), 10);

	//自定义控件
	function LeftControl(){
		this.defaultAnchor = BMAP_ANCHOR_TOP_LEFT;
		this.defaultOffset = new BMap.Size(10,10);
	}
	//继承Control的API
	LeftControl.prototype = new BMap.Control();
	//初始化控件
	LeftControl.prototype.initialize=function(map){
		var ul = document.createElement("ul");
		ul.setAttribute("class","list-group");
		ul.setAttribute("id","leftControl_neigh");
		var li = document.createElement("li");
		li.setAttribute("class","list-group-item");
		li.textContent = "请滑动鼠标查看小区名";
		ul.appendChild(li);
		//添加DOM元素到地图中
		map.getContainer().appendChild(ul);
		//返回DOM
		return ul;
	};
	//创建控件实例
	var leftCtrl = new LeftControl();
	//添加到地图当中
	bmap.addControl(leftCtrl);
	var mapv = new Mapv({
		drawTypeControl: false,
		map: bmap // 百度地图的map实例
	});

	//自定义控件
	function legendControl(){
		this.defaultAnchor = BMAP_ANCHOR_BOTTOM_RIGHT;
		this.defaultOffset = new BMap.Size(10,10);
	}
	//继承Control的API
	legendControl.prototype = new BMap.Control();
	//初始化控件
	legendControl.prototype.initialize=function(map){
		var _box = document.createElement("div");
		_box.innerHTML = 
			"<div class='box'>"+
				"<div class='box-header'>"+
					"<h3 class='box-title'>扇形图例&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h3>"+
					"<div class='box-tools pull-right'>"+
						"<button type='button' class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i>"+
						"</button>"+
					"</div>"+
				"</div>"+
				"<div class='box-body' style='position: relative;'>"+
					// "<div class="box">"+
						"<div id='switch' class='box-body' style='position: relative;''>"+
						"<canvas id='neighborMapLegend'width='100' height='100' style='width: 100px; height: 100px; background: rgb(255, 255, 255);'></canvas>"
						"</div>"+
					// "</div>"+
				"</div>"+
			"</div>";
		//});
		map.getContainer().appendChild(_box);
		return _box;
	};
	//创建控件实例
	var legendCtr = new legendControl();
	//添加到地图当中
	bmap.addControl(legendCtr);
	neighborMapLegend("neighborMapLegend");

	return {"bmap":bmap,"mapv":mapv};
}
function get2GNeighborCellMapData(params){
	$.post("voltereportcell/get2GNeighborCellMapData", params, function(data){
		drawMap(data);
	});
}
function drawMap(data){
	var cell = data.cell;
	var neigh = data.Neigh;
	var lostNeigh = data.lostNeigh;
	var vdata = [];
	for (var i in neigh) {
		vdata.push({
			lng: neigh[i].slongitude,
			lat: neigh[i].slatitude,
			count: 10,
			dir: parseInt(neigh[i].sdir),
			band: neigh[i].sband,
			master: false,
			scell: neigh[i].scell,
			lineCount: "blue",
			lineWidth : 1
		});
	}
	for (var i in lostNeigh) {
		vdata.push({
			lng: lostNeigh[i].longitudeBD,
			lat: lostNeigh[i].latitudeBD,
			count: 5,
			dir: parseInt(lostNeigh[i].dir)-30,
			band: lostNeigh[i].band,
			master: false,
			scell: lostNeigh[i].cellName,
			lineCount: "red",
			lineWidth : 1
		});
	}
	vdata.push({
		lng: cell.longitudeBD,
		lat: cell.latitudeBD,
		count: -1,
		dir: parseInt(cell.dir)-30,
		band: cell.band,
		master: true,
		scell: cell.cellName
	});
	var points = [];
	for (var i = 0; i < vdata.length; i++) {
		points.push(new BMap.Point(vdata[i].lng, vdata[i].lat));
	}
	mapv1.bmap.setViewport(points);
	var layerNeigh = new Mapv.Layer({
		mapv: mapv1.mapv, // 对应的mapv实例
		zIndex: 10, // 图层层级
		dataType: "point", // 数据类型，点类型
		data: vdata, // 数据
		drawType: "choropleth", // 展示形式
		dataRangeControl: false,
		drawOptions: { // 绘制参数
			size: 20, // 点大小
			unit: "px", // 单位
			type: "switchout",
			// splitList数值表示按数值区间来展示不同颜色的点
			splitList: [
				{
					end: 0,
					color: "blue"
				}, {
					start: 0,
					end: 6,
					color: "red"
				}, {
					start: 6,
					end: 11,
					color: "green"
				}
			],
			events: {
			}
		}
	});
	var layer = new Mapv.Layer({
		mapv: mapv1.mapv, // 对应的mapv实例
		zIndex: 10, // 图层层级
		dataType: "point", // 数据类型，点类型
		data: vdata, // 数据
		drawType: "choropleth", // 展示形式
		dataRangeControl: false,
		drawOptions: { // 绘制参数
			size: 20, // 点大小
			unit: "px", // 单位
			strokeStyle: "gray", // 描边颜色
			type: "site",
			// splitList数值表示按数值区间来展示不同颜色的点
			splitList: [
				{
					start: 0,
					end: 10,
					color: "gray"
				}
			],
			events: {
				mousemove: function (e, data) {
					$("#leftControl_neigh").children().remove();
					var li = "";
					for (var i = 0; i < data.length; i++) {
						li += ("<li " + "class='list-group-item'" + ">" + data[i].scell + "</li>");
					}
					$("#leftControl_neigh").append(li);
				}
			}
		}
	});
}
function neighborMapLegend(canvas){
	var switchNumber = document.getElementById(canvas);
	var context = switchNumber.getContext("2d");
	//设置对象起始点和终点
	context.beginPath();
	context.fillStyle = "red";
	context.strokeStyle = "black";
	context.fillRect(10.5,20.5,35.5,15.5);

	context.fillText("待补充", 50.5,30.5);
	context.strokeStyle = "gray";
	context.moveTo(0,40.5);
	context.lineTo(100,40.5);
	context.stroke();
	context.closePath();

	//设置对象起始点和终点
	context.beginPath();

	context.fillStyle = "green";
	context.strokeStyle = "black";
	context.fillRect(10.5,50.5,35.5,15.5);

	context.fillText("已存在", 50.5,60.5);
	context.strokeStyle = "gray";
	context.moveTo(0,70.5);
	context.lineTo(100,70.5);
	context.stroke();
	context.closePath();

	//设置对象起始点和终点
	context.beginPath();
	context.fillStyle = "blue";
	context.strokeStyle = "black";
	context.fillRect(10.5,80.5,35.5,15.5);

	context.fillText("服务小区", 50.5,90.5);
	context.closePath();
}
function getCTRJump(record) {
	var S = Ladda.create( document.getElementById( "openCtrJumpBtn" ) );
	S.start();
    var date = new Date();
    var month = (date.getMonth()+1) > 10 ? (date.getMonth()+1) : "0"+(date.getMonth()+1);
    var day = (date.getDate()) >10 ? (date.getDate()) : "0"+(date.getDate());
    var hour = (date.getHours()) >10 ? (date.getHours()-2) : "0"+(date.getHours()-2);   
    var ctrDldPoint = ""+date.getFullYear()+month+day+hour; 
    console.log(ctrDldPoint);
    var kpi0 = record["eSRVCC切换成功率"];
    var kpi1 = record["ESRVCC失败总次数"];
    var city = record.city;
    var cell = record.cell;
    var type = "ctr";
    // var ctrDldPoint = ""+date.getFullYear()+month+day+"06";    //代码测试
    // var cell = "LD31S42B";									  //代码测试
    var params = {
        type  : type,
        point : ctrDldPoint,
        city  : city,
        cell  : cell,
        kpi0  : kpi0,
        kpi1  : kpi1
    }
    $.post("srvccbadcell/ctrTreeItems", params, function (data) {
    	console.log(data);
    	S.stop();
        // var returnData = JSON.parse(data);
        $("#ctrData").html("");
        $("#ctrData").append(data);
        // $("#fileTable").treegrid("loadData", returnData);
    });
}
function initTable() {
	var table = "#fileTable";
	$(table).treegrid({
		idField: "id",
		treeField: "kpiName",
		fitColumn:false,
		columns: [[
			{
				title: "Name", field: "kpiName", width: 950,
				formatter: function (val, row) {
					return "<input type='checkbox' onclick=show('" + row.id + "')  id='check_" + row.id + "' " + (row.checked ? 'checked' : '') + "/>" + row.kpiName;
				}
			},
			{field: "size", title: "Size", width: 100},
			{field: "eSRVCC切换成功率", title: "eSRVCC切换成功率", width: 100},
			{field: "ESRVCC失败总次数", title: "ESRVCC失败总次数", width: 100},
		]]
	});
}
function openCtrJump() {
	$("#ctrJump").modal();
}
var path;
function storage() {
	var saveBtn = Ladda.create(document.getElementById("runBtn"));
	saveBtn.start();
	var type = "ctr";
	var j = 0;
	var id = 1, checkid;
	var childrenId = [];
	for (id = 1; ; id++) {
		var nodes = $("#fileTable").treegrid("getChildren", id);
		if (nodes != "") {
			//parentId = id;
			for (i = 1; i <= nodes.length; i++) {
				childrenId[j] = id + "" + i;
				j++;
			}
		} else if (nodes.length == 0) {
			break;
		}
	}
	var gzFile = [];
	j = 0;
	for (i = 0; i < childrenId.length; i++) {
		var checkId = "check_" + childrenId[i];
		var status = $("#" + checkId).is(":checked");
		if (status) {
			var text = $("#" + checkId).parent().text();
			gzFile[j] = text;
			j++;
		}
	}
	var node = $("#node option:selected");

	var fileDir = node.attr("data-fileDir");
	if (gzFile.length == 0) {
		//alert("请选择要在线入库的数据源");
		saveBtn.stop();
		layer.open({
			title: "提示",
			content: "请选择要在线入库的数据源"
		});
		return;
	}
	
	var params = {
		type: type,
		gzFiles: gzFile.join(";;"),
		// remoteIp: remoteIp,
		baseStation: $("#cellInput").val(),
		fileDir: fileDir,
		city : ctrCity
	};
	$.post("srvccbadcell/storage", params, function (data) {
		if (data) {
			path = data;
			if (type == "ctr") {
				$("#ctrTypeDiv").show();
			} else {
				$("#ctrTypeDiv").hide();
			}
			saveCtrTask(data, saveBtn);
		}

	});
	return path;
}
function saveCtrTask(path, saveBtn) {
	var taskName = $("#taskName").val().trim();
	var tracePath = path;
	var taskType = "ctr";
	if (taskType == "kget") {
		taskType = "parameter";
	} else if (taskType == "ctr") {
		taskType = "ctrsystem";
	} else if (taskType == "cdr") {
		taskType = "cdrsystem";
	} else if (taskType == "ebm") {
		taskType = "ebmsystem";
	} else if (taskType == "pcap") {
		taskType = "pcapsystem";
	}
	prepareTask(taskName, tracePath, taskType, saveBtn);
}
function prepareTask(name, tracePath, type, saveBtn) {
	var mDate = new Date();
	var myDate = mDate.Format("yyyy-MM-dd hh:mm:ss");
	var data = {
		"tracePath": encodeURI(tracePath),
		"taskName": encodeURI(name),
		"createTime": myDate,
		"type": type

	};
	$.ajax({
		type: "POST",
		url: "srvccbadcell/addTask",
		data: data,
		async: true,
		success: function (returnData) {
			//console.log(returnData);
			returnData = JSON.parse(returnData);
			if (returnData.state == 0) {
				runTask(name, tracePath, type, saveBtn, cancelBtn2);
			} else if (returnData.state == 1) {
				//alert("任务名重复，请重新输入");
				layer.open({
					title: "提示",
					content: "任务名重复，请重新输入"
				});
				saveBtn.stop();
				// cancelBtn2.stop();
				return;
			} else {
				//alert("新增失败，请重试");
				layer.open({
					title: "提示",
					content: "新增失败，请重试"
				});
				saveBtn.stop();
				// cancelBtn2.stop();
				return;
			}
		}
	});
}
function runTask(taskName, tracePath, type, saveBtn, cancelBtn2) {
	var myDate = new Date().Format("yyyy-MM-dd hh:mm:ss");
	var ctrFlag = $(".ctrType:checked").val();
	var data = {
		"taskName": taskName,
		"tracePath": tracePath,
		"startTime": myDate,
		"type": type,
		"ctrFlag":ctrFlag
	};
	taskMonitor = setInterval(function () {
		updateMonitor(taskName);
	}, 2000);
	$.ajax({
		type: "get",
		url: "srvccbadcell/runTask",
		data: data,
		async: true,
		success: function (data) {
			saveBtn.stop();
			// cancelBtn2.stop();
			clearInterval(taskMonitor);
			var returnData = eval("(" + data + ")");
			if (returnData.status == "true") {
				//alert("入库成功");
				layer.open({
					title: "提示",
					content: "入库成功"
				});
				$.post("volteupbadcell/deleteAutoDir",{"tracePath":tracePath});
			} else if (returnData.status == "abort") {
				//alert("入库失败，请查看日志");
				layer.open({
					title: "提示",
					content: "入库失败，请查看日志"
				});
			}
		}
	});
}
function updateMonitor(taskName) {
	var data = {"taskName": encodeURI(taskName)};
	$.ajax({
		type: "get",
		url: "srvccbadcell/monitor",
		data: data,
		async: true,
		success: function (returnData) {
			$("#log").html(returnData);
			$("#log").parent().scrollTop($("#log").height());
		}
	});
}
Date.prototype.Format = function (fmt) {
	var o = {
		"M+": this.getMonth() + 1,
		"d+": this.getDate(),
		"h+": this.getHours(),
		"m+": this.getMinutes(),
		"s+": this.getSeconds(),
		"q+": Math.floor((this.getMonth() + 3) / 3),
		"S": this.getMilliseconds()
	};
	if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
	for (var k in o)
		if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
	return fmt;
};
function show(checkid) {
    var s = "#check_" + checkid;
    /*选子节点*/
    var nodes = $("#fileTable").treegrid("getChildren", checkid);
    if (nodes != null) {
        for (i = 0; i < nodes.length; i++) {
            $(("#check_" + nodes[i].id))[0].checked = $(s)[0].checked;
        }
    }
    var parent;
    //选上级节点
    if ($(s)[0].checked == false) {
        parent = $("#fileTable").treegrid("getParent", checkid);
        if (parent != null) {
            $(("#check_" + parent.id))[0].checked = false;
        }
    } else {
        parent = $("#fileTable").treegrid("getParent", checkid);
        var flag = true;
        if (parent != null) {
            var sons = parent.children;
            for (i = 0; i < sons.length; i++) {
                if ($(("#check_" + $(s).attr("id"))).checked == false) {
                    flag = false;
                    break;
                }
            }
            if (flag) {
                $(("#check_" + parent.id))[0].checked = true;
            }
        }

    }
}
function checkSwitchOut(){
	$(".switchRadio").eq(0).prop("checked","checked");
}
function openMapModal(){
	$("#mapModal").modal();
	$("#map").empty();
	setTimeout(function(){
		window.mapv = initMap("map");
		drawMapOut("origin");
	},300);
}
function initMap(mapId) {
	var bmap = new BMap.Map(mapId);
	bmap.enableScrollWheelZoom(); // 启用滚轮放大缩小
	// 初始化地图,设置中心点坐标和地图级别
	bmap.centerAndZoom(new BMap.Point(120.602701, 32.227101), 10);
	//自定义控件
	function switchControl(){
		this.defaultAnchor = BMAP_ANCHOR_TOP_RIGHT;
		this.defaultOffset = new BMap.Size(10,10);
	}
	//继承Control的API
	switchControl.prototype = new BMap.Control();
	//创建控件实例
	var switchCtrl = new switchControl();
	//添加到地图当中
	bmap.addControl(switchCtrl);

	//自定义控件
	function legendControl(){
		this.defaultAnchor = BMAP_ANCHOR_BOTTOM_RIGHT;
		this.defaultOffset = new BMap.Size(10,10);
	}
	//继承Control的API
	legendControl.prototype = new BMap.Control();
	//初始化控件
	legendControl.prototype.initialize=function(map){
		var _box = document.createElement("div");
		//$("#search").click(function(){
		_box.innerHTML = 
					"<div class='box'>"+
						"<div class='box-header'>"+
							"<h3 class='box-title'>线条图例&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h3>"+
							"<div class='box-tools pull-right'>"+
								"<button type='button' class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i>"+
								"</button>"+
							"</div>"+
						"</div>"+
						"<div id='switch' class='box-body' style='position: relative;'>"+
							"<div class='box'>"+
								"<div id='switch' class='box-body' style='position: relative;''>"+
									"<canvas id='switchSuccessRatio'width='100' height='100' style='width: 100px; height: 100px; background: rgb(255, 255, 255);''></canvas>"
									+
								"</div>"+
								"<div class='box'>"+
								"<div id='switch' class='box-body' style='position: relative;'>"+
									"<canvas id='switchNumber' width='100' height='140' style='width: 100px; height: 140px; background: rgb(255, 255, 255);'></canvas>"+
								"</div>"+
						"</div>"+
					"</div>";
		map.getContainer().appendChild(_box);
		return _box;
	};
	//创建控件实例
	var legendCtr = new legendControl();
	//添加到地图当中
	bmap.addControl(legendCtr);
	switchNumberLegend("switchNumber");
	switchSuccessRatioLegend("switchSuccessRatio");
	//自定义控件
	function LeftControl(){
		this.defaultAnchor = BMAP_ANCHOR_TOP_LEFT;
		this.defaultOffset = new BMap.Size(10,10);
	}
	//继承Control的API
	LeftControl.prototype = new BMap.Control();
	//初始化控件
	LeftControl.prototype.initialize=function(map){
		var ul = document.createElement("ul");
		ul.setAttribute("class","list-group");
		ul.setAttribute("id","leftControl");
		var li = document.createElement("li");
		li.setAttribute("class","list-group-item");
		li.textContent = "请滑动鼠标查看小区名";
		ul.appendChild(li);
		//添加DOM元素到地图中
		map.getContainer().appendChild(ul);
		//返回DOM
		return ul;
	};
	//创建控件实例
	var leftCtrl = new LeftControl();
	//添加到地图当中
	bmap.addControl(leftCtrl);
	var mapv = new Mapv({
		drawTypeControl: false,
		map: bmap // 百度地图的map实例
	});
	return {"bmap":bmap,"mapv":mapv};
}
var drawMapOut = function (t, scell) {
	checkSwitchOut();
	switchOutTable();
	if(t == "最大RRC连接用户数"){
		url = "badHandoverCell/RRCusers";
	}else if(t=="无线掉线率"){
		url = "badHandoverCell/wireLessLost";
	}else if(t=="PUSCH上行干扰电平"){
		url = "badHandoverCell/PUSCHInterfere";
	}else if(t=="origin"){
		url = "badHandoverCell/switchData";
	}
	var S = Ladda.create( document.getElementById( "search" ) );
	var E = Ladda.create( document.getElementById( "export" ) );
	S.start();E.start();

	if(layerout != null) {
		layerout.hide();
	}
	if(layerin != null){
		layerin.hide();
	}
	$.ajax({
		type: "GET",
		url:url,
		data: {date: startTime,cell: selectCell},
		dataType: "text",
		beforeSend: function () {
			$("map").html("<img class='col-md-offset-5' src='dist/img/ajax-loader.gif'>");
		},
		success: function (data) {
			setTableData(data);
			$("#map_zhaozi").hide();
			$("#map_loadingImg").hide();
			S.stop();E.stop();
			var returnData = JSON.parse(data);
			console.log(returnData);
			var vdata = [];
			for(var i=0;i<returnData.length;i++) {
				var count;
				var lineCounts;
				if(t=="origin"){
					if(returnData[i].handoverAttemptCount == 0) {
						count = 80;  //gray
					}else if(returnData[i].handoverSuccessRatio <= 90 && returnData[i].handoverAttemptCount >= 50) {
						count = 55;  //red
					}else {
						count = 30; //blue
					}
					if(returnData[i].scell == scell){
						count = 100;
					}
				}else{
					if(returnData[i].handoverAttemptCount == 55) {
						count = 55;  //red
					}else if(returnData[i].handoverAttemptCount == 100) {
						count = 100;  //yellow
					}else if(returnData[i].handoverAttemptCount == 30){
						count = 30; //blue
					}
				}
				
				if(returnData[i].handoverSuccessRatio == null){
					lineCounts = "null"; 
				}else if(returnData[i].handoverSuccessRatio < 85) {
					lineCounts = "red"; 
				}else if(returnData[i].handoverSuccessRatio <= 95 && returnData[i].handoverSuccessRatio >= 85) {
					lineCounts = "yellow";
				}else if(returnData[i].handoverSuccessRatio > 95){
					lineCounts = "blue";
				}
				var lineWidth;
				if (returnData[i].handoverAttemptCount1 >= 0 && returnData[i].handoverAttemptCount1 <20) {
					lineWidth = 1;
				} else if (returnData[i].handoverAttemptCount1  >= 20 && returnData[i].handoverAttemptCount1 < 80) {
					lineWidth = 2;
				} else if (returnData[i].handoverAttemptCount1  >= 80 && returnData[i].handoverAttemptCount1 < 160) {
					lineWidth = 3;
				} else if (returnData[i].handoverAttemptCount1  >= 160) {
					lineWidth = 4;
				} 
				vdata.push({
					lng: returnData[i].slongitude,
					lat: returnData[i].slatitude,
					count: count,
					lineCount:lineCounts,
					dir: returnData[i].sdir-30,
					band: returnData[i].sband,
					master: false,
					scell: returnData[i].scell,
					lineWidth : lineWidth
				});
			}
			vdata.push({
				lng:returnData[0].mlongitude,
				lat: returnData[0].mlatitude,
				count: -1,
				dir:returnData[0].mdir-30,
				band: returnData[0].mband,
				master: true,
				lineWidth : -1
			});

			var points = [];

			for(var i=0;i<vdata.length;i++) {
				points.push(new BMap.Point(vdata[i].lng,vdata[i].lat));
			}

			mapv.bmap.setViewport(points);

			layerout = new Mapv.Layer({
				mapv: mapv.mapv, // 对应的mapv实例
				zIndex: 1, // 图层层级
				dataType: "point", // 数据类型，点类型
				data: vdata, // 数据
				drawType: "choropleth", // 展示形式
				dataRangeControl: false ,
				drawOptions: { // 绘制参数
					size: 20, // 点大小
					unit: "px", // 单位
					type: "switchout",
					// splitList数值表示按数值区间来展示不同颜色的点
					splitList: [
						{
							end: 0,
							color: "green"
						},{
							start: 0,
							end: 50,
							color: "blue"
						},{
							start: 50,
							end: 60,
							color: "red"
						},{
							start: 60,
							end: 90,
							color: "gray"
						},{
							start:90,
							end:120,
							color:"yellow"
						}
					],
					events: {
						click: function(e, data) {
							var scells = [];
							for(var i=0;i<data.length;i++) {
								scells.push(data[i].scell);
							}
							var params = {
								date: startTime,
								cell: selectCell,
								scells: scells
							};

							$.get("badHandoverCell/switchDetail", params, function(data){
								var newData = JSON.parse(data).data;
								$("#bMapTable").grid("destroy", true, true);
								$("#bMapTable").grid({
									columns:[
									{"text":"id","field":"id","height":50,"width":150},
									{"text":"day_id","field":"day_id","height":50,"width":150},
									{"text":"city","field":"city","height":50,"width":150},
									{"text":"subNetwork","field":"subNetwork","height":50,"width":150},
									{"text":"cell","field":"cell","height":50,"width":150},
									{"text":"EutranCellRelation","field":"EutranCellRelation","height":50,"width":150},
									{"text":"切换成功率","field":"切换成功率","height":50,"width":150},
									{"text":"同频切换成功率","field":"同频切换成功率","height":50,"width":150},
									{"text":"异频切换成功率","field":"异频切换成功率","height":50,"width":150},
									{"text":"同频准备切换尝试数","field":"同频准备切换尝试数","height":50,"width":150},
									{"text":"同频准备切换成功数","field":"同频准备切换成功数","height":50,"width":150},
									{"text":"同频执行切换尝试数","field":"同频执行切换尝试数","height":50,"width":150},
									{"text":"异频准备切换尝试数","field":"异频准备切换尝试数","height":50,"width":150},
									{"text":"异频准备切换成功数","field":"异频准备切换成功数","height":50,"width":150},
									{"text":"异频执行切换尝试数","field":"异频执行切换尝试数","height":50,"width":150},
									{"text":"准备切换成功率","field":"准备切换成功率","height":50,"width":150},
									{"text":"执行切换成功率","field":"执行切换成功率","height":50,"width":150},
									{"text":"准备切换尝试数","field":"准备切换尝试数","height":50,"width":150},
									{"text":"准备切换成功数","field":"准备切换成功数","height":50,"width":150},
									{"text":"准备切换失败数","field":"准备切换失败数","height":50,"width":150},
									{"text":"执行切换尝试数","field":"执行切换尝试数","height":50,"width":150},
									{"text":"执行切换成功数","field":"执行切换成功数","height":50,"width":150},
									{"text":"执行切换失败数","field":"执行切换失败数","height":50,"width":150}
									],
									dataSource:newData,
									pager: { limit: 10, sizes: [10, 20, 50, 100] },
									autoScroll:true,
									uiLibrary: "bootstrap"
								});
							});
							$("#myModal").modal({
								keyboard: false
							});
						},
					}
				}
			});
		}
	});
}
function switchOutTable(){
	//$("input[name="t1"]").eq(0).prop("checked","checked");
	var masterCell = selectCell;
	var date = startTime;
	var params={
		cell:masterCell,
		date:date,
		type:'BadHandoverCell'
	};
	$.get("badHandoverCell/switchOutTable", params, function(data){
		var myData = eval(data);
		//最大RRC连接用户数
		$("#nummore200").html(myData[0]);
		$("#num100to200").html(myData[1]);
		$("#numless100").html(myData[2]);
		$("#wireLostmore5").html(myData[3]);
		$("#wireLost1to5").html(myData[4]);
		$("#wireLostLess1").html(myData[5]);
		$("#phschless110").html(myData[6]);
		$("#phsch110to95").html(myData[7]);
		$("#phschmore95").html(myData[8]);
	});
}
function setTableData(data){
	var fieldArr=[];//console.log(data);
	data = eval("("+data+")");
	var text = "ServeCell,NeighCell,ServeLongitude,ServeLatitude,ServeDir,ServeBand,NeighLongitude,NeighLatitude,NeighDir,NeighBand,执行切换失败数,准备切换尝试数,切换成功率";
	var textArr = text.split(",");
	for(var i in textArr){
		fieldArr[fieldArr.length]={field:textArr[fieldArr.length],title:textArr[fieldArr.length],width:150};
	}
	$("#detailTable").grid("destroy", true, true);
	var grid = $("#detailTable").grid({
		columns:fieldArr,
		dataSource:data,
		pager: { limit: 10, sizes: [10, 20, 50, 100] },
		autoScroll:true,
		uiLibrary: "bootstrap",
		primaryKey : "id",
		autoLoad: true   
	});
	grid.on("rowSelect", function (e, $row, id, record) {
		drawMapOut2(data, record.NeighCell);
	});
}
var drawMapOut2 = function (data, scell) {
	//console.log(scell);
	/*if(t == "最大RRC连接用户数"){
		url = "RRCusers";
	}else if(t=="无线掉线率"){
		url = "wireLessLost";
	}else if(t=="PUSCH上行干扰电平"){
		url = "PUSCHInterfere";
	}else if(t=="origin"){
		url = "switchData";
	}*/
	var t = "origin";

	var returnData = data;
	var vdata = [];
	for(var i=0;i<returnData.length;i++) {
		var count;
		var lineCounts;
		if(t=="origin"){
			if(returnData[i].handoverAttemptCount == 0) {
				count = 80;  //gray
			}else if(returnData[i].handoverSuccessRatio <= 90 && returnData[i].handoverAttemptCount >= 50) {
				count = 55;  //red
			}else {
				count = 30; //blue
			} 
			if(returnData[i].scell == scell){
				count = 100;
			}
		}else{
			if(returnData[i].handoverAttemptCount == 55) {
				count = 55;  //red
			}else if(returnData[i].handoverAttemptCount == 100) {
				count = 100;  //yellow
			}else if(returnData[i].handoverAttemptCount == 30){
				count = 30; //blue
			}
		}
		vdata.push({
			lng: returnData[i].slongitude,
			lat: returnData[i].slatitude,
			count: count,
			lineCount:"null",
			dir: returnData[i].sdir-30,
			band: returnData[i].sband,
			master: false,
			scell: returnData[i].scell
			//lineWidth : lineWidth
		});
	}
	vdata.push({
		lng:returnData[0].mlongitude,
		lat: returnData[0].mlatitude,
		count: -1,
		dir:returnData[0].mdir-30,
		band: returnData[0].mband,
		master: true,
		lineWidth : -1
	});
	var points = [];

	for(var i=0;i<vdata.length;i++) {
		points.push(new BMap.Point(vdata[i].lng,vdata[i].lat));
	}

	//mapv.bmap.setViewport(points);

	layerout = new Mapv.Layer({
		mapv: mapv.mapv, // 对应的mapv实例
		zIndex: 1, // 图层层级
		dataType: "point", // 数据类型，点类型
		data: vdata, // 数据
		drawType: "choropleth", // 展示形式
		dataRangeControl: false ,
		drawOptions: { // 绘制参数
			size: 20, // 点大小
			unit: "px", // 单位
			type: "switchout",
			// splitList数值表示按数值区间来展示不同颜色的点
			splitList: [
				{
					end: 0,
					color: "green"
				},{
					start: 0,
					end: 50,
					color: "blue"
				},{
					start: 50,
					end: 60,
					color: "red"
				},{
					start: 60,
					end: 90,
					color: "gray"
				},{
					start:90,
					end:120,
					color:"yellow"
				}
			],
			events: {
				/*$("#myModal").modal({
					keyboard: false
				})*/
			},
		}
	});
}
function switchRadio(){
	$(".switchRadio").on("change",function(){
		var value = $(this).val();
		if(value == "out"){
			switchFlag = "out";
			drawMapOut("origin");
			//switchOutTable();
		}else{
			switchFlag = "in";
			drawMapIn("origin");
			//switchOutTableIn();
		}
	});
}
var drawMapIn = function (t, scell) {
	// console.log(scell)
	switchOutTableIn();
	if(t == "最大RRC连接用户数"){
		url = "badHandoverCell/RRCusersin";
	}else if(t=="无线掉线率"){
		url = "badHandoverCell/wireLessLostin";
	}else if(t=="PUSCH上行干扰电平"){
		url = "badHandoverCell/PUSCHInterferein";
	}else if(t=="origin"){
		url = "badHandoverCell/handoverin";
	}
	var S = Ladda.create( document.getElementById( "search" ) );
	var E = Ladda.create( document.getElementById( "export" ) );
	S.start();E.start();

	if(layerin != null) {
		layerin.hide();
	}

	if(layerout != null) {
		layerout.hide();
	}
	$.ajax({
		type: "GET",
		url: url,
		data: {date: startTime,cell: selectCell},
		dataType: "text",
		beforeSend: function () {
			$("map").html("<img class='col-md-offset-5' src='dist/img/ajax-loader.gif'>");
		},
		success: function (data) {
			setTableDataIn(data);
			$("#map_zhaozi").hide();
			$("#map_loadingImg").hide();
			S.stop();E.stop();
			var returnData = JSON.parse(data);
			var vdata = [];
			for(var i=0;i<returnData.length;i++) {
				var count;
				if(t=="origin"){
					if(returnData[i].handoverAttemptCount == 0) {
						count = 80;
					}else if(returnData[i].handoverSuccessRatio <= 90 && returnData[i].handoverAttemptCount >= 50) {
						count = 55;
					}else {
						count = 30;
					}
					if(returnData[i].cell == scell){
						count = 100;
					}
				}else{
					if(returnData[i].handoverAttemptCount == 55) {
			
						count = 55;  //red
					}else if(returnData[i].handoverAttemptCount == 100) {
						count = 100;  //yellow
					}else if(returnData[i].handoverAttemptCount == 30){
						count = 30; //blue
					}

				}
				var lineCounts;
				if(returnData[i].handoverSuccessRatio == null){
					lineCounts = "null"; 
				}else if(returnData[i].handoverSuccessRatio < 85) {
					lineCounts = "red"; 
				}else if(returnData[i].handoverSuccessRatio <= 95 && returnData[i].handoverSuccessRatio >= 85) {
					lineCounts = "yellow";
				}else if(returnData[i].handoverSuccessRatio > 95){
					lineCounts = "blue";

				}
				var lineWidth;
				if (returnData[i].handoverAttemptCount1 >= 0 && returnData[i].handoverAttemptCount1 <20) {
					lineWidth = 1;
				} else if (returnData[i].handoverAttemptCount1  >= 20 && returnData[i].handoverAttemptCount1 < 80) {
					lineWidth = 2;
				} else if (returnData[i].handoverAttemptCount1  >= 80 && returnData[i].handoverAttemptCount1 < 160) {
					lineWidth = 3;
				} else if (returnData[i].handoverAttemptCount1  >= 160) {
					lineWidth = 4;
				} 
				vdata.push({
					lng: returnData[i].mlongitude,
					lat: returnData[i].mlatitude,
					count: count,
					dir: returnData[i].mdir-30,
					band: returnData[i].mband,
					master: false,
					cell: returnData[i].cell,
					lineWidth : lineWidth,
					lineCount:lineCounts
				});
			}
			vdata.push({
				lng:returnData[0].slongitude,
				lat: returnData[0].slatitude,
				count: -1,
				dir:returnData[0].sdir-30,
				band: returnData[0].sband,
				master: true,
				lineWidth : -1
			});

			var points = [];

			for(var i=0;i<vdata.length;i++) {
				points.push(new BMap.Point(vdata[i].lng,vdata[i].lat));
			}

			mapv.bmap.setViewport(points);

			layerin = new Mapv.Layer({
				mapv: mapv.mapv, // 对应的mapv实例
				zIndex: 1, // 图层层级
				dataType: "point", // 数据类型，点类型
				data: vdata, // 数据
				drawType: "choropleth", // 展示形式
				dataRangeControl: false ,
				drawOptions: { // 绘制参数
					size: 20, // 点大小
					unit: "px", // 单位
					type: "switchin",
					// splitList数值表示按数值区间来展示不同颜色的点
					splitList: [
						{
							end: 0,
							color: "green"
						},{
							start: 0,
							end: 50,
							color: "blue"
						},{
							start: 50,
							end: 60,
							color: "red"
						},{
							start: 60,
							end: 90,
							color: "gray"
						},{
							start:90,
							end:120,
							color:"yellow"
						}
					],
					events: {
						click: function(e, data) {
							//console.log("click",e,data);
							var cells = [];
							for(var i=0;i<data.length;i++) {
								cells.push(data[i].cell);
							}
							var params = {
								date: startTime,
								cell: selectCell,
								cells: cells
							};


							$.get("badHandoverCell/handOverInDetail", params, function(data){
								var newData = JSON.parse(data).data;
								//console.log(newData);
								$("#bMapTable").grid("destroy", true, true);
								$("#bMapTable").grid({
									columns:[
									{"text":"id","field":"id","height":50,"width":150},
									{"text":"day_id","field":"day_id","height":50,"width":150},
									{"text":"city","field":"city","height":50,"width":150},
									{"text":"subNetwork","field":"subNetwork","height":50,"width":150},
									{"text":"cell","field":"cell","height":50,"width":150},
									{"text":"EutranCellRelation","field":"EutranCellRelation","height":50,"width":150},
									{"text":"切换成功率","field":"切换成功率","height":50,"width":150},
									{"text":"同频切换成功率","field":"同频切换成功率","height":50,"width":150},
									{"text":"异频切换成功率","field":"异频切换成功率","height":50,"width":150},
									{"text":"同频准备切换尝试数","field":"同频准备切换尝试数","height":50,"width":150},
									{"text":"同频准备切换成功数","field":"同频准备切换成功数","height":50,"width":150},
									{"text":"同频执行切换尝试数","field":"同频执行切换尝试数","height":50,"width":150},
									{"text":"异频准备切换尝试数","field":"异频准备切换尝试数","height":50,"width":150},
									{"text":"异频准备切换成功数","field":"异频准备切换成功数","height":50,"width":150},
									{"text":"异频执行切换尝试数","field":"异频执行切换尝试数","height":50,"width":150},
									{"text":"准备切换成功率","field":"准备切换成功率","height":50,"width":150},
									{"text":"执行切换成功率","field":"执行切换成功率","height":50,"width":150},
									{"text":"准备切换尝试数","field":"准备切换尝试数","height":50,"width":150},
									{"text":"准备切换成功数","field":"准备切换成功数","height":50,"width":150},
									{"text":"准备切换失败数","field":"准备切换失败数","height":50,"width":150},
									{"text":"执行切换尝试数","field":"执行切换尝试数","height":50,"width":150},
									{"text":"执行切换成功数","field":"执行切换成功数","height":50,"width":150},
									{"text":"执行切换失败数","field":"执行切换失败数","height":50,"width":150} 
									],
									dataSource:newData,
									pager: { limit: 10, sizes: [10, 20, 50, 100] },
									autoScroll:true,
									uiLibrary: "bootstrap"
								});
							});
							$("#myModal").modal({
								keyboard: false
							});
						},
					}
				}
			});
		}
	});
}
function switchOutTableIn(){
	//$("input[name="t1"]").eq(0).prop("checked","checked");
	var masterCell = selectCell;
	var date = startTime;
	var params={
		cell:masterCell,
		date:date
	};
	$.get("badHandoverCell/switchOutTableIn", params, function(data){
		var myData = eval(data);
		//最大RRC连接用户数
		$("#nummore200").html(myData[0]);
		$("#num100to200").html(myData[1]);
		$("#numless100").html(myData[2]);
		$("#wireLostmore5").html(myData[3]);
		$("#wireLost1to5").html(myData[4]);
		$("#wireLostLess1").html(myData[5]);
		$("#phschless110").html(myData[6]);
		$("#phsch110to95").html(myData[7]);
		$("#phschmore95").html(myData[8]);
	});
}
function setTableDataIn(data){
  var fieldArr=[];//console.log(data);
	data = eval("("+data+")");
	var text = "ServeCell,NeighCell,ServeLongitude,ServeLatitude,ServeDir,ServeBand,NeighLongitude,NeighLatitude,NeighDir,NeighLBand,执行切换失败数,准备切换尝试数,切换成功率";
	var textArr = text.split(",");
	for(var i in textArr){
		fieldArr[fieldArr.length]={field:textArr[fieldArr.length],title:textArr[fieldArr.length],width:150};
	}
	$("#detailTable").grid("destroy", true, true);
	var grid = $("#detailTable").grid({
		columns:fieldArr,
		dataSource:data,
		pager: { limit: 10, sizes: [10, 20, 50, 100] },
		autoScroll:true,
		uiLibrary: "bootstrap",
		primaryKey : "id",
		autoLoad: true   
	});
	grid.on("rowSelect", function (e, $row, id, record) {
		drawMapIn2(data, record.ServeCell);
	});
}
function switchNumberLegend(switchNumber){
	var switchNumber = document.getElementById(switchNumber);
	var context = switchNumber.getContext("2d");
	context.fillStyle="#000000";
	context.font="12px serif";
	context.textAlign="center";
	context.fillText("准备切换尝试数",50,10.5);
	context.textAlign="left";
	//设置对象起始点和终点
	context.beginPath();
	context.moveTo(20.5,40.5);
	context.lineTo(40.5,40.5);
	//设置样式
	context.lineWidth = 1;
	context.strokeStyle = "#000000";
	//绘制
	context.stroke();
	context.fillText("0~20",50.5,45.5);
	context.lineWidth = 0.2;
	context.strokeStyle = "gray";
	context.moveTo(0,60.5);
	context.lineTo(100,60.5);
	context.stroke();
	//设置对象起始点和终点
	context.beginPath();
	context.moveTo(20.5,70.5);
	context.lineTo(40.5,70.5);
	//设置样式
	context.lineWidth = 2;
	context.strokeStyle = "#000000";
	//绘制
	context.stroke();
	context.fillText("20~80",50.5,75.5);
	context.lineWidth = 0.2;
	context.strokeStyle = "gray";
	context.moveTo(0,90.5);
	context.lineTo(100,90.5);
	context.stroke();
	//设置对象起始点和终点
	context.beginPath();
	context.moveTo(20.5,100.5);
	context.lineTo(40.5,100.5);
	//设置样式
	context.lineWidth = 3;
	context.strokeStyle = "#000000";
	//绘制
	context.stroke();
	context.fillText("80~160",50.5,105.5);
	context.lineWidth = 0.2;
	context.strokeStyle = "gray";
	context.moveTo(0,120.5);
	context.lineTo(100,120.5);
	context.stroke();
	//设置对象起始点和终点
	context.beginPath();
	context.moveTo(20.5,130.5);
	context.lineTo(40.5,130.5);
	//设置样式
	context.lineWidth = 4;
	context.strokeStyle = "#000000";
	//绘制
	context.stroke();
	context.fillText(">160",50.5,135.5);
}
function switchSuccessRatioLegend(switchSuccessRatio){
	var switchNumber = document.getElementById(switchSuccessRatio);
	var context = switchNumber.getContext("2d");
	context.fillStyle="#000000";
	context.font="12px serif";
	context.textAlign="center";
	context.fillText("切换成功率",40,10.5);
	context.lineWidth = 0.2;
	context.textAlign="left";
	//设置对象起始点和终点
	context.beginPath();
	context.fillStyle = "red";
	context.strokeStyle = "black";
	context.fillRect(10.5,20.5,35.5,15.5);

	context.fillText("<85%", 50.5,30.5);
	context.strokeStyle = "gray";
	context.moveTo(0,40.5);
	context.lineTo(100,40.5);
	context.stroke();
	context.closePath();

	//设置对象起始点和终点
	context.beginPath();

	context.fillStyle = "yellow";
	context.strokeStyle = "black";
	context.fillRect(10.5,50.5,35.5,15.5);

	context.fillText("85%~95%", 50.5,60.5);
	context.strokeStyle = "gray";
	context.moveTo(0,70.5);
	context.lineTo(100,70.5);
	context.stroke();
	context.closePath();

	//设置对象起始点和终点
	context.beginPath();
	context.fillStyle = "blue";
	context.strokeStyle = "black";
	context.fillRect(10.5,80.5,35.5,15.5);

	context.fillText(">95%", 50.5,90.5);
	context.closePath();

}
function getYestdayFormatDate() {
	var date = new Date();
	var seperator1 = "-";
	var month = date.getMonth() + 1;
	var strDate = date.getDate()-1;
	if (strDate == "0") {
		month = month-1;
		if (month == 1 || month == 3 || month == 5 || month == 7 || month == 8 || month == 10 || month == 12) {
			strDate = 31;
		} else if (month == 4 || month == 6 || month == 9 || month == 11) {
			strDate = 30;
		} else {
			strDate = 28;
		}
	}
	if (month >= 1 && month <= 9) {
		month = "0" + month;
	}
	if (strDate > 0 && strDate <= 9) {
		strDate = "0" + strDate;
	}
	var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate;
	return currentdate;
}
var res = function(t){
	if(switchFlag=="out"){
		if(t == 1){
			drawMapOut("最大RRC连接用户数");
		}else if(t==2){
			drawMapOut("无线掉线率");
		}else if(t==3){
			drawMapOut("PUSCH上行干扰电平");
		}
	} else if(switchFlag=="in"){
		if(t == 1){
			drawMapIn("最大RRC连接用户数");
		}else if(t==2){
			drawMapIn("无线掉线率");
		}else if(t==3){
			drawMapIn("PUSCH上行干扰电平");
		}
	}
}
