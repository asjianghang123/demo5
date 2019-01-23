var layerout = null;
var layerin = null;
var selectCell;
var startTime;
var switchFlag = "out";
$(function(){
	toogle("badHandoverCell");
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
  

function openConfigInfo(){
	$("#config_information").modal();
}

function closeModal() {
	$("#cancelBtn2").modal("hide");
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
	var url = "badHandoverCell/getAllCity";
	$.ajax({
		type:"GET",
		url:url,
		dataType:"json",
		success:function(data){
		// alert(data);
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
var ctrCity;
function doSearchbadCell(table){
	var l = Ladda.create( document.getElementById( "search" ) );
	var E = Ladda.create( document.getElementById( "export" ) );
	var X = Ladda.create( document.getElementById( "exportNBI" ) );
	l.start();
	E.start();
	X.start();
	var params = getParams();
	if(params == false){
		l.stop();
		E.stop();
		X.stop();
		return false;
	}
	$.get("badHandoverCell/templateQuery", params, function(data){
		var fieldArr=[];
		var fieldArr1=[];
		var text=(JSON.parse(data).content).split(",");
		var text1=(JSON.parse(data).content1).split(",");
		text.splice(0,1);
		// text1.splice(0,1);
		var filename = JSON.parse(data).filename;
		var filename1 = JSON.parse(data).filename1;
		$("#badCellFile").val(filename);
		$("#badCellFileNBM").val(filename1);

		for(var i in text){  
			fieldArr[i]={field:text[i],title:text[i],width:150,sortable:true};
		}
		for(var j in text1){  
			fieldArr1[j]={field:text1[j],title:text1[j],width:150,sortable:true};
		}
		var newData = JSON.parse(data).rows;
		var newData1 = JSON.parse(data).rows1;
		$("#badCellTable").grid("destroy", true, true);
		var badCellTable = $("#badCellTable").grid({
			columns:fieldArr,
			dataSource:newData,
			pager: { limit: 10, sizes: [10, 20, 50, 100] },
			autoScroll:true,
			uiLibrary: "bootstrap",
			primaryKey:"id"
		});
		$("#badCellTableNBI").grid("destroy", true, true);
		var badCellTableNBI = $("#badCellTableNBI").grid({
			columns:fieldArr1,
			dataSource:newData1,
			pager: { limit: 10, sizes: [10, 20, 50, 100] },
			autoScroll:true,
			uiLibrary: "bootstrap",
			primaryKey:"date_id"
		});
		l.stop();
		E.stop();
		X.stop();

		//点击导出
		if(table == "file"){
			filename = $("#badCellFile").val();
			fileZipSave(filename);
		}
		if(table == "filenbm"){
			filename1 = $("#badCellFileNBM").val();
			fileZipSave(filename1);
		}
		badCellTable.on("rowSelect", function (e, $row, id, record) {
			$("#currentAlarmNum").removeClass();
            $("#less116ProportionNum").removeClass();
            $("#overlapCoverNum").removeClass();
            $("#less155ProportionNum").removeClass();
            $("#needAddNeighNum").removeClass();
            $("#AvgPRBNum").removeClass();
            $("#highTrafficNum").removeClass();
            $("#highTrafficNum2").removeClass();
            $("#parameterNum").removeClass();

				//CTR跳转
			getCTRJump(record);
			ctrCity = record["city"];
			report(record);
			doSearchEvent_table(record.cell,record.city);
			rrcResult(record.cell,record.city);
			doSearchEventExec_table(record.cell,record.city);
			execResult(record.cell,record.city);
			getRelevanceChart(record);
			getNeighBadHandoverCellTable(record.cell,record.hour_id);
			selectCell = record.cell;
			getIndexChartData(record);
			getIndexTableData(record);
		});
		badCellTableNBI.on("rowSelect", function (e, $row, id, record) {
			$("#currentAlarmNum").removeClass();
            $("#less116ProportionNum").removeClass();
            $("#overlapCoverNum").removeClass();
            $("#less155ProportionNum").removeClass();
            $("#needAddNeighNum").removeClass();
            $("#AvgPRBNum").removeClass();
            $("#highTrafficNum").removeClass();
            $("#highTrafficNum2").removeClass();
            $("#parameterNum").removeClass();
			console.log(record);
			getCTRJumpvolte(record);
			ctrCity = record["City"];
			reportvolte(record);
		});
	});
}

function reportvolte(record){
	var alarmpolar;
	var rrcnumpolar;
	var ganraopolar;
	var weakcoverpolar;
	var zhichapolar;
	var canshupolar;
	var overlapcoverpolar;
	var neighnum;
	var weakcover;
	var cell = record.EutranCellTdd;
	var date = record.date_id;
	var hour = record.hour;
	var city = record.City;
	var table = "temp_badhandovercell";
	var params = {
		cell: cell,
		hour: hour,
		table: table,
		date: date,
		city: city
	};
	$.ajax({
		type:"get",
		url:"badHandoverCell/getVolteAlarmNum",
		data: params,
		dataType:"json",
		async : false,
		success:function(data){
			alarmpolar = data["Polar-告警"];
			// console.log(data);
			$("#currentAlarm").val(data["告警数量"]+"条"); 
			if(data["Polar-告警"] == 0) {   //告警  
				$("#currentAlarmNum").removeClass();
				$("#currentAlarmNum").addClass("glyphicon glyphicon-ok-circle");
				$("#currentAlarmNum").css("color","green");
			}else if(data["Polar-告警"] >0 && data["Polar-告警"] < 50) {
				$("#currentAlarmNum").removeClass();
				$("#currentAlarmNum").addClass("glyphicon glyphicon-exclamation-sign");
				$("#currentAlarmNum").css("color","orange");
			}else if(data["Polar-告警"] >= 50) {
				$("#currentAlarmNum").removeClass();
				$("#currentAlarmNum").addClass("glyphicon glyphicon-remove-sign");
				$("#currentAlarmNum").css("color","red");
			}
		}
	});
	$.ajax({
		type:"get",
		url:"badHandoverCell/getVolteAvgPrb",
		data: params,
		dataType:"json",
		async : false,
		success:function(data){
		// data = String(data);
			ganraopolar = data["Polar-干扰"];
			if(data["平均PRB"] == null || data["平均PRB"] == 0.00 || data["平均PRB"] == 0 ){
				$("#AvgPRB").val("无数据"); 
			}else{
				// console.log(typeof data["平均PRB"]);
				var datastring = data["平均PRB"]+"";
				// console.log(datastring);
				var prb=datastring.split("--");
				$("#AvgPRB").val(prb[0]+"dBm");
				if(prb[1])
					{
						$("#AvgPRB_head").show();
						if(prb[2]){
							if(prb[3])
							{
								$("#AvgPRB_lab").html(prb[1]+" "+prb[2]+prb[3]);
							}else{
								$("#AvgPRB_lab").html(prb[1]+" "+prb[2]);
							}
						}else{
							$("#AvgPRB_lab").html(prb[1]);
						}
					}else{
						$("#AvgPRB_head").hide();
						$("#AvgPRB_lab").html("");
					}               
			}
			if(data["Polar-干扰"] == 0) {  //干扰
				$("#AvgPRBNum").removeClass();
				$("#AvgPRBNum").addClass("glyphicon glyphicon-ok-circle");
				$("#AvgPRBNum").css("color","green");
			}else if(data["Polar-干扰"] >0 && data["Polar-干扰"] < 50) {
				$("#AvgPRBNum").removeClass();
				$("#AvgPRBNum").addClass("glyphicon glyphicon-exclamation-sign");
				$("#AvgPRBNum").css("color","orange");
			}else if(data["Polar-干扰"] >= 50) {
				$("#AvgPRBNum").removeClass();
				$("#AvgPRBNum").addClass("glyphicon glyphicon-remove-sign");
				$("#AvgPRBNum").css("color","red");
			}
			if (data["平均PRB"] == null || data["平均PRB"] == 0.00 || data["平均PRB"] == 0 ) {
				$("#AvgPRBNum").removeClass();
				$("#AvgPRBNum").addClass("glyphicon glyphicon-ok-circle");
				$("#AvgPRBNum").css("color","green");
				ganraopolar = 0;
			}
		}
	});
	$.ajax({
		type:"get",
		url:"badHandoverCell/highrrcnum",
		data: params,
		dataType:"json",
		async : false,
		success:function(data){
		// console.log(typeof data["告警数量"]);
			rrcnumpolar = data["最高RRC用户数"];
			if(data["最高RRC用户数"] == null || data["最高RRC用户数"] == 0 || data["最高RRC用户数"] == 0.00){
				$("#highTraffic").val("无数据"); 
			}else{
				$("#highTraffic").val(data["最高RRC用户数"]+"个");                 
			}
			if(data["Polar-最高RRC用户数"] == 0) {   //最高RRC用户数
				$("#highTrafficNum").removeClass();
				$("#highTrafficNum").addClass("glyphicon glyphicon-ok-circle");
				$("#highTrafficNum").css("color","green");
			}else if(data["Polar-最高RRC用户数"] >0 && data["Polar-最高RRC用户数"] < 50) {
				$("#highTrafficNum").removeClass();
				$("#highTrafficNum").addClass("glyphicon glyphicon-exclamation-sign");
				$("#highTrafficNum").css("color","orange");
			}else if(data["Polar-最高RRC用户数"] >= 50) {
				$("#highTrafficNum").removeClass();
				$("#highTrafficNum").addClass("glyphicon glyphicon-remove-sign");
				$("#highTrafficNum").css("color","red");
			} 
			// if(data["Polar-最高RRC用户数"] >300&&data["MAC层时延"]>100) {   //高话务
			// 	$("#highTrafficNum2").removeClass();
			// // $("#highTrafficNum2").addClass("glyphicon glyphicon-ok-circle");
			// // $("#highTrafficNum2").css("color","green");
			// 	$("#highTraffic2").val(data["MAC层时延"]+"ms"); 
			// 	$("#highTrafficNum2").addClass("glyphicon glyphicon-remove-sign");
			// 	$("#highTrafficNum2").css("color","red");
			// }else {
			// 	$("#highTrafficNum2").removeClass();
			// 	$("#highTrafficNum2").addClass("glyphicon glyphicon-ok-circle");
			// 	$("#highTrafficNum2").css("color","green");
			// 	$("#highTraffic2").val("非高话务"); 
			// }
		}
	});
	$.ajax({
		type:"get",
		url:"badHandoverCell/weakcover",
		data: params,
		dataType:"json",
		async : false,
		success:function(data){
			weakcoverpolar = data["Polar-弱覆盖"];
			// console.log(data);
			if(data["RSRP<-116的比例"]){
				$("#less116Proportion").val(data["RSRP<-116的比例"]+"%");
			}else{
				$("#less116Proportion").val("");
			} 
			if(data["Polar-弱覆盖"] == 0) {  //弱覆盖
				$("#less116ProportionNum").removeClass();
				$("#less116ProportionNum").addClass("glyphicon glyphicon-ok-circle");
				$("#less116ProportionNum").css("color","green");
			}else if(data["Polar-弱覆盖"] >0 && data["Polar-弱覆盖"] < 50) {
				$("#less116ProportionNum").removeClass();
				$("#less116ProportionNum").addClass("glyphicon glyphicon-exclamation-sign");
				$("#less116ProportionNum").css("color","orange");
			}else if(data["Polar-弱覆盖"] >= 50) {
				$("#less116ProportionNum").removeClass();
				$("#less116ProportionNum").addClass("glyphicon glyphicon-remove-sign");
				$("#less116ProportionNum").css("color","red");
			}
		}
	});
	$.ajax({
		type:"get",
		url:"badHandoverCell/zhicha",
		data: params,
		dataType:"json",
		async : false,
		success:function(data){
			zhichapolar = data["Polar-质差"];
			// console.log(data);
			if(data["RSRQ<-15.5的比例"]){
				$("#less155Proportion").val(data["RSRQ<-15.5的比例"]+"%"); 

			}else{
				$("#less155Proportion").val(""); 
			}
			if (data["下行CQI<3的比例"]) {
		    	$("#cqi").val(data["下行CQI<3的比例"]+"%");
		    } else {
		    	$("#cqi").val("");
		    } 
			if(data["Polar-质差"] == 0) {  //质差
				$("#less155ProportionNum").removeClass();
				$("#less155ProportionNum").addClass("glyphicon glyphicon-ok-circle");
				$("#less155ProportionNum").css("color","green");
			}else if(data["Polar-质差"] >0 && data["Polar-质差"] < 50) {
				$("#less155ProportionNum").removeClass();
				$("#less155ProportionNum").addClass("glyphicon glyphicon-exclamation-sign");
				$("#less155ProportionNum").css("color","orange");
			}else if(data["Polar-质差"] >= 50) {
				$("#less155ProportionNum").removeClass();
				$("#less155ProportionNum").addClass("glyphicon glyphicon-remove-sign");
				$("#less155ProportionNum").css("color","red");
			}
		}
	});
	$.ajax({
		type:"get",
		url:"lowAccessCell/overlapcover",
		data: params,
		dataType:"json",
		async : false,
		success:function(data){
		// console.log(typeof data["告警数量"]);
			overlapcoverpolar = data["Polar-重叠覆盖"];
			if(data["重叠覆盖度"] == null||data["重叠覆盖度"]=="MRConnectFailed"){
				$("#overlapCover").val("");
			}else{
				$("#overlapCover").val(data["重叠覆盖度"]+"%");
			}
			if(data["Polar-重叠覆盖"] == 0) {  //重叠覆盖
				$("#overlapCoverNum").removeClass();
				$("#overlapCoverNum").addClass("glyphicon glyphicon-ok-circle");
				$("#overlapCoverNum").css("color","green");
			}else if(data["Polar-重叠覆盖"] >0 && data["Polar-重叠覆盖"] < 50) {
				$("#overlapCoverNum").removeClass();
				$("#overlapCoverNum").addClass("glyphicon glyphicon-exclamation-sign");
				$("#overlapCoverNum").css("color","orange");
			}else if(data["Polar-重叠覆盖"] >= 50) {
				$("#overlapCoverNum").removeClass();
				$("#overlapCoverNum").addClass("glyphicon glyphicon-remove-sign");
				$("#overlapCoverNum").css("color","red");
			}
		}
	});
	var params2 = {
		flag: weakcover,
		cell: cell,
		city: city
	}
	$.ajax({
		type:"get",
		url:"lowAccessCell/neighcell",
		data: params2,
		dataType:"json",
		async : false,
		success:function(data){
		// console.log(typeof data["告警数量"]);
			neighnum = data["Polar-邻区"];
			if(data["需要加邻区数量"] == null || data["需要加邻区数量"] == "MRConnectFailed"){
				$("#needAddNeigh").val("");
			}else{
				$("#needAddNeigh").val(parseInt(data["需要加邻区数量"])+"条");
			}
			// console.log(data["Polar-参数"]);
			// console.log(data["参数"]);
			if(data["Polar-邻区"] == 0) {  //邻区
				$("#needAddNeighNum").removeClass();
				$("#needAddNeighNum").addClass("glyphicon glyphicon-ok-circle");
				$("#needAddNeighNum").css("color","green");
			}else if(data["Polar-邻区"] >0 && data["Polar-邻区"] < 50) {
				$("#needAddNeighNum").removeClass();
				$("#needAddNeighNum").addClass("glyphicon glyphicon-exclamation-sign");
				$("#needAddNeighNum").css("color","orange");
			}else if(data["Polar-邻区"] >= 50) {
				$("#needAddNeighNum").removeClass();
				$("#needAddNeighNum").addClass("glyphicon glyphicon-remove-sign");
				$("#needAddNeighNum").css("color","red");
			}
		}
	});
	$.ajax({
		type:"get",
		url:"badHandoverCell/badHandovercellcanshu",
		data: params2,
		dataType:"json",
		async : false,
		success:function(data){
		// console.log(typeof data["告警数量"]);
			canshupolar = data["Polar-参数"];
			$("#parameter").val(parseInt(data["参数"])+"个");
			// console.log(data["Polar-参数"]);
			// console.log(data["参数"]);
			if(data["Polar-参数"] == 0) {   //参数
				$("#parameterNum").removeClass();
				$("#parameterNum").addClass("glyphicon glyphicon-ok-circle");
				$("#parameterNum").css("color","green");
			}else if(data["Polar-参数"] >0 && data["Polar-参数"] <= 50) {
				$("#parameterNum").removeClass();
				$("#parameterNum").addClass("glyphicon glyphicon-exclamation-sign");
				$("#parameterNum").css("color","orange");
			}else if(data["Polar-参数"] > 50) {
				$("#parameterNum").removeClass();
				$("#parameterNum").addClass("glyphicon glyphicon-remove-sign");
				$("#parameterNum").css("color","red");
			} 
		}
	});
	$.ajax({
        type:"get",
        url:"badHandoverCell/avgtabadhandover",
        data: params,
        dataType:"json",
        async : false,
        success:function(data){
        // console.log(typeof data["告警数量"]);
            if(data["avgTA"]){
                $("#avgTA").val(data["avgTA"]);
            }else{
                $("#avgTA").val("");
            }
        }
    });
	$("#container").highcharts({
		chart: {
			polar: true
		},
		title: {
			text: "极地图"
		},
		xAxis: {
			categories: ["告警","弱覆盖","重叠覆盖","质差","邻区","干扰","参数"],
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
				parseInt(weakcoverpolar),
				parseInt(overlapcoverpolar),
				parseInt(zhichapolar),
				parseInt(neighnum),
				parseInt(ganraopolar),
				parseInt(canshupolar)
			],
			pointPlacement: "on",
			events: {
				click: function(e) {
					if(e.point.category === "告警") {
						openAlarmModel(record.EutranCellTdd);   
					}else if(e.point.category === "邻区") {
						openNeighborCellModel(record.EutranCellTdd, record.city); 
					}else if(e.point.category === "弱覆盖") {
						openWeakCoverCellModelvolte(record.EutranCellTdd, record.City);
					}else if(e.point.category === "质差") {
						openzhichaCellModel(record.EutranCellTdd, record.City);
					}else if(e.point.category === "重叠覆盖") {
						openOverlapCoverModel(record.EutranCellTdd, record.city);
					}else if(e.point.category === "干扰") {
						openInterfereCellModelvolte(record.EutranCellTdd, record);
					}else if(e.point.category === "参数") {
						openParameterModel(record.EutranCellTdd, record.city);
					}         
				}
			}
		}]
	});

}

function openInterfereCellModelvolte(cell, record) {
	$("#config_information_interferenceCell").modal();
	$("#getfirstInterferenceCellTab a:first").tab("show");
	$("#InterferenceCellWorstCellContainer").empty();
	$("#interferenceCell_model").empty();
	
	var paramsLTE_1 = {
		cell : cell,
		hour : record.hour,
		city:record.City,
   
		table : "badHandoverCell_ex"
	};
		//console.log(paramsLTE_1);
	$.ajax({
		url:"badHandoverCell/getGanraoCell_chart",
		data:paramsLTE_1,
		type:"get",
		success:function(data){
			var data=JSON.parse(data);
			if(data.result=="error"){
				$("#InterferenceCellWorstCellContainer").html("数据为空！");
			}else{
				$("#InterferenceCellWorstCellContainer").css("width","850px").css("hight","400px");
				var categories=eval("["+data.key+"]");
				var series=eval("["+data.data+"]");
				var chart = new Highcharts.Chart("InterferenceCellWorstCellContainer", {
					title: {
								text: cell+"/干扰",
								x: -20
							},
					xAxis: {
								categories: categories
							},
					yAxis: {
								title: {
									text: "干扰 (dBm)"
								},
								plotLines: [{
									value: 0,
									width: 1,
									color: "#808080"
								}]
							},
					tooltip: {
								valueSuffix: "dBm"
							},
					legend: {
								layout: "vertical",
								align: "right",
								verticalAlign: "middle",
								borderWidth: 0
							},
					series: [{
								name: "干扰",
								data: series
							}]
				});         
			}
		}
	});
	$.get("badHandoverCell/getInterfereCell_model", paramsLTE_1, function(data){
		var fieldArr=[];
		var text=data.content.split(",");
		var filename = data.filename;
		for(var i in data.rows[0]){       
			fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:textWidth(i)};
		} 
		var newData = data.rows;
		$("#interferenceCell_model").grid("destroy", true, true);
		var alarmWorstCellTable = $("#interferenceCell_model").grid({
			columns:fieldArr,
			dataSource:newData,
			pager: { limit: 5, sizes: [5, 10, 15, 20] },
			autoScroll:true,
			uiLibrary: "bootstrap",
			primaryKey:"dateTime_id"
		});
	});
}
function report(record){
	// getNumOfDiagnosisDataFilter_alarm(record.cell,record.city);//告警
	// getNumOfDiagnosisDataFilter_weakCover(record.cell,record.city);//弱覆盖
	// getNumOfDiagnosisDataFilter_zhicha(record.cell,record.city);//质差
	// getNumOfDiagnosisDataFilter_overlapCover(record.cell,record.city);//重叠覆盖
	// getNumOfDiagnosisDataFilter_AvgPRB(record.cell,record.city);//干扰
	// getNumOfDiagnosisDataFilter_highTraffic(record.cell,record.city);//高话务
	// getNumOfDiagnosisDataFilter_parameter(record.cell,record.city);//参数
	// getNumOfDiagnosisData_MR(record.cell,record.city);  //将MR查询分离出来-邻区

	$("#currentAlarm").val(record["告警数量"]+"条"); //告警数量 
	if(record["RSRP<-116的比例"]){
		$("#less116Proportion").val(record["RSRP<-116的比例"]+"%");
	}else{
		$("#less116Proportion").val("");
	} 
	if(record["avgTA"]){
		$("#avgTA").val(record["avgTA"]);
	}else{
		$("#avgTA").val("");
	} 
	if(record["RSRQ<-15.5的比例"]){
		$("#less155Proportion").val(record["RSRQ<-15.5的比例"]+"%"); 

	}else{
		$("#less155Proportion").val(""); 
	}
   $("#cqi").val(record["下行CQI<3的比例"]+"%"); 

	if(record["重叠覆盖度"] == null){
		$("#overlapCover").val("");
	}else if (record["重叠覆盖度"]=="MRConnectFailed"){
		$("#overlapCover").val("");

	}else{
		$("#overlapCover").val(record["重叠覆盖度"]+"%");
	}
	if(record["平均PRB"]==null || record["平均PRB"]==0.00 || record["平均PRB"]==0 ){

		$("#AvgPRB").val("无数据"); 
	}else{

		var prb=record["平均PRB"].split("--");
		$("#AvgPRB").val(prb[0]+"dBm");

		if(prb[1])
		{
			$("#AvgPRB_head").show();
			$("#AvgPRB_lab").html(prb[1]);
		}else{
			$("#AvgPRB_head").hide();
			$("#AvgPRB_lab").html("");
		}
	
	}
	if(record["关联度"] == null || record["关联度"] == 0.00 || record["关联度"] == 0){
		$("#highTraffic").val("无数据"); 
	}else{
		$("#highTraffic").val(record["关联度"]*20+"个");                 
	}
	//$("#highTraffic").val(record["关联度"]*20+"个"); 
  
	$("#parameter").val(parseInt(record["参数"])+"个");  
	var height_body=80;
	if(record["featureState"]&&record["featureState"]!="none"){
		$("#featureState_label").show();
		height_body+=40;
		var str=record["featureState"].split(",");
		$("#featureState").val(str[0]);
		$("#featureState_lab").remove();
		$("#featureState").after("<label class='control-label col-sm-12' id='featureState_lab'>"+str[1]+"</label>");
	}else{
		$("#featureState_label").hide();
		$("#featureState").val("");
		$("#featureState_lab").remove();
	}
	if(record["licenseState"]&&record["licenseState"]!="none"){
		$("#licenseState_label").show();
		height_body+=20;
		$("#licenseState").val(record["licenseState"]);     
	}else{
		$("#licenseState_label").hide();
		$("#licenseState").val("");   
	}
	$("#collapseFore").css("height",height_body+"px");
	$("#collapseSeven").css("height",height_body+"px");
	$("#collapseSix").css("height",height_body+"px");
	if(record["需要加邻区数量"] == null){
		$("#needAddNeigh").val(""); 
	}else if(record["需要加邻区数量"]=="MRConnectFailed") {
		$("#needAddNeigh").val("");  
	}else{
		$("#needAddNeigh").val(parseInt(record["需要加邻区数量"])+"条");  
	}

	if(record["Polar-告警"] == 0) {   //告警
		$("#currentAlarmNum").removeClass();
		$("#currentAlarmNum").addClass("glyphicon glyphicon-ok-circle");
		$("#currentAlarmNum").css("color","green");
	}else if(record["Polar-告警"] >0 && record["Polar-告警"] < 50) {
		$("#currentAlarmNum").removeClass();
		$("#currentAlarmNum").addClass("glyphicon glyphicon-exclamation-sign");
		$("#currentAlarmNum").css("color","orange");
	}else if(record["Polar-告警"] >= 50) {
		$("#currentAlarmNum").removeClass();
		$("#currentAlarmNum").addClass("glyphicon glyphicon-remove-sign");
		$("#currentAlarmNum").css("color","red");
	} 
	if(record["Polar-弱覆盖"] == 0) {  //弱覆盖
		$("#less116ProportionNum").removeClass();
		$("#less116ProportionNum").addClass("glyphicon glyphicon-ok-circle");
		$("#less116ProportionNum").css("color","green");
	}else if(record["Polar-弱覆盖"] >0 && record["Polar-弱覆盖"] < 50) {
		$("#less116ProportionNum").removeClass();
		$("#less116ProportionNum").addClass("glyphicon glyphicon-exclamation-sign");
		$("#less116ProportionNum").css("color","orange");
	}else if(record["Polar-弱覆盖"] >= 50) {
		$("#less116ProportionNum").removeClass();
		$("#less116ProportionNum").addClass("glyphicon glyphicon-remove-sign");
		$("#less116ProportionNum").css("color","red");
	}
	if(record["Polar-重叠覆盖"] == 0) {  //重叠覆盖
		$("#overlapCoverNum").removeClass();
		$("#overlapCoverNum").addClass("glyphicon glyphicon-ok-circle");
		$("#overlapCoverNum").css("color","green");
	}else if(record["Polar-重叠覆盖"] >0 && record["Polar-重叠覆盖"] <= 50) {
		$("#overlapCoverNum").removeClass();
		$("#overlapCoverNum").addClass("glyphicon glyphicon-exclamation-sign");
		$("#overlapCoverNum").css("color","orange");
	}else if(record["Polar-重叠覆盖"] > 50) {
		$("#overlapCoverNum").removeClass();
		$("#overlapCoverNum").addClass("glyphicon glyphicon-remove-sign");
		$("#overlapCoverNum").css("color","red");
	}
	if(record["Polar-质差"] == 0) {  //质差
		$("#less155ProportionNum").removeClass();
		$("#less155ProportionNum").addClass("glyphicon glyphicon-ok-circle");
		$("#less155ProportionNum").css("color","green");
	}else if(record["Polar-质差"] >0 && record["Polar-质差"] < 50) {
		$("#less155ProportionNum").removeClass();
		$("#less155ProportionNum").addClass("glyphicon glyphicon-exclamation-sign");
		$("#less155ProportionNum").css("color","orange");
	}else if(record["Polar-质差"] >= 50) {
		$("#less155ProportionNum").removeClass();
		$("#less155ProportionNum").addClass("glyphicon glyphicon-remove-sign");
		$("#less155ProportionNum").css("color","red");
	}
	if(record["Polar-邻区"] == 0) {  //邻区
		$("#needAddNeighNum").removeClass();
		$("#needAddNeighNum").addClass("glyphicon glyphicon-ok-circle");
		$("#needAddNeighNum").css("color","green");
	}else if(record["Polar-邻区"] >0 && record["Polar-邻区"] < 50) {
		$("#needAddNeighNum").removeClass();
		$("#needAddNeighNum").addClass("glyphicon glyphicon-exclamation-sign");
		$("#needAddNeighNum").css("color","orange");
	}else if(record["Polar-邻区"] >= 50) {
		$("#needAddNeighNum").removeClass();
		$("#needAddNeighNum").addClass("glyphicon glyphicon-remove-sign");
		$("#needAddNeighNum").css("color","red");
	}
	if(record["Polar-干扰"] == 0) {  //干扰
		$("#AvgPRBNum").removeClass();
		$("#AvgPRBNum").addClass("glyphicon glyphicon-ok-circle");
		$("#AvgPRBNum").css("color","green");
	}else if(record["Polar-干扰"] >0 && record["Polar-干扰"] <= 50) {
		$("#AvgPRBNum").removeClass();
		$("#AvgPRBNum").addClass("glyphicon glyphicon-exclamation-sign");
		$("#AvgPRBNum").css("color","orange");
	}else if(record["Polar-干扰"] > 50) {
		$("#AvgPRBNum").removeClass();
		$("#AvgPRBNum").addClass("glyphicon glyphicon-remove-sign");
		$("#AvgPRBNum").css("color","red");
	}
	if(record["Polar-高话务"] == 0) {   //高话务
		$("#highTrafficNum").removeClass();
		$("#highTrafficNum").addClass("glyphicon glyphicon-ok-circle");
		$("#highTrafficNum").css("color","green");
	}else if(record["Polar-高话务"] >0 && record["Polar-高话务"] < 50) {
		$("#highTrafficNum").removeClass();
		$("#highTrafficNum").addClass("glyphicon glyphicon-exclamation-sign");
		$("#highTrafficNum").css("color","orange");
	}else if(record["Polar-高话务"] >= 50) {
		$("#highTrafficNum").removeClass();
		$("#highTrafficNum").addClass("glyphicon glyphicon-remove-sign");
		$("#highTrafficNum").css("color","red");
	} 
	if(record["Polar-参数"] == 0) {   //参数
		$("#parameterNum").removeClass();
		$("#parameterNum").addClass("glyphicon glyphicon-ok-circle");
		$("#parameterNum").css("color","green");
	}else if(record["Polar-参数"] >0 && record["Polar-参数"] <= 50) {
		$("#parameterNum").removeClass();
		$("#parameterNum").addClass("glyphicon glyphicon-exclamation-sign");
		$("#parameterNum").css("color","orange");
	}else if(record["Polar-参数"] > 50) {
		$("#parameterNum").removeClass();
		$("#parameterNum").addClass("glyphicon glyphicon-remove-sign");
		$("#parameterNum").css("color","red");
	} 
	var alarmNum = record.cell;
	$("#container").highcharts({
		chart: {
			polar: true
		},
		title: {
			text: "极地图"
		},
		xAxis: {
			categories: ["告警","弱覆盖","重叠覆盖","质差","邻区","干扰","参数"],
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
				parseInt(record["Polar-告警"]),
				parseInt(record["Polar-弱覆盖"]),
				parseInt(record["Polar-重叠覆盖"]),
				parseInt(record["Polar-质差"]),
				parseInt(record["Polar-邻区"]),
				parseInt(record["Polar-干扰"]),
				parseInt(record["Polar-参数"])
			],
			pointPlacement: "on",
			events: {
				click: function(e) {
					if(e.point.category === "告警") {
						openAlarmModel(alarmNum);   
					}else if(e.point.category === "邻区") {
						openNeighborCellModel(alarmNum, record.city); 
					}else if(e.point.category === "弱覆盖") {
						openWeakCoverCellModel(alarmNum, record.city);
					}else if(e.point.category === "质差") {
						openzhichaCellModel(alarmNum, record.city);
					}else if(e.point.category === "重叠覆盖") {
						openOverlapCoverModel(alarmNum, record.city);
					}else if(e.point.category === "干扰") {
						openInterfereCellModel(alarmNum, record);
					}else if(e.point.category === "参数") {
						openParameterModel(alarmNum, record.city);
					}         
				}
			}
		}]
	});
}

//告警
// function getNumOfDiagnosisDataFilter_alarm(cell,city){
//     var params = {
//         city: city,
//         cell: cell
//     };
//     $.get("highLostCell/getNumOfDiagnosisDataFilter_alarm", params, function(data) {
//         $("#alarm_loadingImg").hide();   //告警
//         $("#currentAlarm").val(data[0]);          //告警数量
//     });
// }
// //弱覆盖
// function getNumOfDiagnosisDataFilter_weakCover(cell,city){
//     var params = {
//         city: city,
//         cell: cell
//     };
//     $.get("highLostCell/getNumOfDiagnosisDataFilter_weakCover", params, function(data) {      
//         $("#weakCocer_loadingImg").hide();    //弱覆盖
//         $("#less116Proportion").val(data[0]);     //弱覆盖-RSRP<-116的比例
//     });
// }
// //质差
// function getNumOfDiagnosisDataFilter_zhicha(cell,city){
//     var params = {
//         city: city,
//         cell: cell
//     };
//     $.get("highLostCell/getNumOfDiagnosisDataFilter_zhicha", params, function(data) {
//         $("#zhicha_loadingImg").hide();    //质差
//         $("#less155Proportion").val(data[0]);     //质差-RSRQ<-15.5的比例
//     });
// }
// //重叠覆盖
// function getNumOfDiagnosisDataFilter_overlapCover(cell,city){
//     var params = {
//         city: city,
//         cell: cell
//     };
//     $.get("highLostCell/getNumOfDiagnosisDataFilter_overlapCover", params, function(data) {
//         $("#overlapCover_loadingImg").hide();    //重叠覆盖
//         $("#overlapCover").val(data[0]);          //重叠覆盖-重叠覆盖度
//     });
// }
// //干扰
// function getNumOfDiagnosisDataFilter_AvgPRB(cell,city){
//     var params = {
//         city: city,
//         cell: cell
//     };
//     $.get("highLostCell/getNumOfDiagnosisDataFilter_AvgPRB", params, function(data) {
//         $("#avgPRB_loadingImg").hide();    //干扰
//         $("#AvgPRB").val(data[0]);                //干扰-平均PRB
//     });
// }
// //高话务
// function getNumOfDiagnosisDataFilter_highTraffic(cell,city){
//     var params = {
//         city: city,
//         cell: cell
//     };
//     $.get("highLostCell/getNumOfDiagnosisDataFilter_highTraffic", params, function(data) {
//         $("#highTraffic_loadingImg").hide();    //高话务
//         $("#highTraffic").val(data[0]);           //高话务
//     });
// }
// //参数
// function getNumOfDiagnosisDataFilter_parameter(cell,city){
//     var params = {
//         city: city,
//         cell: cell
//     };
//     $.get("highLostCell/getNumOfDiagnosisDataFilter_parameter", params, function(data) {
//         $("#parameter_loadingImg").hide();    //参数
//         $("#parameter").val(data[0]);             //参数
//     });
// }
// //邻区
// function getNumOfDiagnosisData_MR(cell, city){
//     var params = {
//         city: city,
//         cell: cell
//     };
//     $.get("highLostCell/getNumOfDiagnosisData_mr", params, function(data) {
//         $("#needAddNeigh_loadingImg").hide();
//         $("#needAddNeigh").val(data[0]);          //邻区-需要加邻区数量
//     });
// }
function openAlarmModel(cell) {
	bsc_table_model("badHandoverCell/getCellAlarmClassifyTable", "#cellAlarmTable_model", {cell:cell,hours:null});  //当前告警
	bsc_table_model("badHandoverCell/getErbsAlarmClassifyTable", "#erbsAlarmTable_model", {cell:cell,hours:null});  //历史告警
	$("#config_information_alarm").modal();
}
function openNeighborCellModel(cell, city) {
	var paramsLTE_1 = {
		cell : cell,
		city : city
	};
	$.get("badHandoverCell/getLTENeighborData_model", paramsLTE_1, function(data){
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
		getNeighborCellMapData(paramsLTE_1);
	},300);
}
function openWeakCoverCellModel(cell, city) {
	$("#config_information_weakCoverCell").modal();
	$("#getfirstWeakCoverCellTab a:first").tab("show");
	$("#weakCoverCellWorstCellContainer").empty();
	var cat_obj,ser_obj,yAxisData;
	var yAxis_name_left = "切换成功率";
	var yAxis_name_right = "RSRP<-116的比例";
	var paramsLTE_1 = {
		cell : cell,
		city : city,
		yAxis_name_left : "切换成功率",
		yAxis_name_right : "RSRP<-116的比例",
		table : "badHandoverCell_ex"
	};

	$.ajax({
		url:"badHandoverCell/getZhichaCell_chart",
		data:paramsLTE_1,
		type:"get",
		success:function(data){
			var cat_str = JSON.stringify(JSON.parse(data).categories);
			var ser_str = JSON.stringify(JSON.parse(data).series);
			yAxisData = (JSON.parse(data).yAxis);
			var cell_str = JSON.parse(data).cell;
			ser_str=ser_str.replace(/"/g,"");
			ser_str=ser_str.replace(yAxis_name_left,"'"+yAxis_name_left+"'");
			ser_str=ser_str.replace("spline","'spline'");
			ser_str=ser_str.replace("#89A54E","'#89A54E'");
			ser_str=ser_str.replace(yAxis_name_right,"'"+yAxis_name_right+"'");
			ser_str=ser_str.replace("column","'column'");
			ser_str=ser_str.replace("#4572A7","'#4572A7'");
			cat_obj = eval("("+cat_str+")");     
			ser_obj = eval("("+ser_str+")");
			$("#config_information_weakCoverCell").on("shown.bs.modal",function(){
				var chart = Highcharts.chart("weakCoverCellWorstCellContainer", {
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
								color: "#89A54E"
							}
						},
						title: {
							text: yAxis_name_left,
							style: {
								color: "#89A54E"
							}
						},
						tickPositions: yAxisData
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
		}
	});
	$.get("badHandoverCell/getWeakCoverCell_model", paramsLTE_1, function(data){
		var fieldArr=[];
		var text=data.content.split(",");
		var filename = data.filename;
		for(var i in data.rows[0]){       
			fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:textWidth(i)};
		} 
		var newData = data.rows;
		$("#weakCoverCell_model").grid("destroy", true, true);
		var alarmWorstCellTable = $("#weakCoverCell_model").grid({
			columns:fieldArr,
			dataSource:newData,
			pager: { limit: 5, sizes: [5, 10, 15, 20] },
			autoScroll:true,
			uiLibrary: "bootstrap",
			primaryKey:"id"
		});
	});
}
function openWeakCoverCellModelvolte(cell, city) {
	$("#config_information_weakCoverCell").modal();
	$("#getfirstWeakCoverCellTab a:first").tab("show");
	$("#weakCoverCellWorstCellContainer").empty();
	var cat_obj,ser_obj,yAxisData;
	var yAxis_name_left = "切换成功率";
	var yAxis_name_right = "RSRP<-116的比例";
	var paramsLTE_1 = {
		cell : cell,
		city : city,
		yAxis_name_left : "切换成功率",
		yAxis_name_right : "RSRP<-116的比例",
		table : "badHandoverCell"
	};

	$.ajax({
		url:"lowAccessCell/getvolteZhichaCellChart",
		data:paramsLTE_1,
		type:"get",
		success:function(data){
			var cat_str = JSON.stringify(JSON.parse(data).categories);
			var ser_str = JSON.stringify(JSON.parse(data).series);
			yAxisData = (JSON.parse(data).yAxis);
			var cell_str = JSON.parse(data).cell;
			ser_str=ser_str.replace(/"/g,"");
			ser_str=ser_str.replace(yAxis_name_left,"'"+yAxis_name_left+"'");
			ser_str=ser_str.replace("spline","'spline'");
			ser_str=ser_str.replace("#89A54E","'#89A54E'");
			ser_str=ser_str.replace(yAxis_name_right,"'"+yAxis_name_right+"'");
			ser_str=ser_str.replace("column","'column'");
			ser_str=ser_str.replace("#4572A7","'#4572A7'");
			cat_obj = eval("("+cat_str+")");     
			ser_obj = eval("("+ser_str+")");
			$("#config_information_weakCoverCell").on("shown.bs.modal",function(){
				var chart = Highcharts.chart("weakCoverCellWorstCellContainer", {
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
								color: "#89A54E"
							}
						},
						title: {
							text: yAxis_name_left,
							style: {
								color: "#89A54E"
							}
						},
						tickPositions: yAxisData
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
		}
	});
	$.get("lowAccessCell/getvolteWeakCoverCellModel", paramsLTE_1, function(data){
		var fieldArr=[];
		var text=data.content.split(",");
		var filename = data.filename;
		for(var i in data.rows[0]){       
			fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:textWidth(i)};
		} 
		var newData = data.rows;
		$("#weakCoverCell_model").grid("destroy", true, true);
		var alarmWorstCellTable = $("#weakCoverCell_model").grid({
			columns:fieldArr,
			dataSource:newData,
			pager: { limit: 5, sizes: [5, 10, 15, 20] },
			autoScroll:true,
			uiLibrary: "bootstrap",
			primaryKey:"id"
		});
	});
}
function openzhichaCellModel(cell, city) {
	$("#config_information_zhichaCell").modal();
	$("#getfirstZhichaCellTab a:first").tab("show");
	$("#zhichaCellWorstCellContainer").empty();
	var cat_obj,ser_obj,yAxisData;
	var yAxis_name_left = "切换成功率";
	var yAxis_name_right = "RSRQ<-15.5的比例";
	var paramsLTE_1 = {
		cell : cell,
		city : city,
		yAxis_name_left : "切换成功率",
		yAxis_name_right : "RSRQ<-15.5的比例",
		table : "badHandoverCell_ex"
	};
	$.ajax({
		url:"badHandoverCell/getZhichaCell_chart",
		data:paramsLTE_1,
		type:"get",
		success:function(data){
			var cat_str = JSON.stringify(JSON.parse(data).categories);
			var ser_str = JSON.stringify(JSON.parse(data).series);
			yAxisData = (JSON.parse(data).yAxis);
			var cell_str = JSON.parse(data).cell;
			ser_str=ser_str.replace(/"/g,"");
			ser_str=ser_str.replace(yAxis_name_left,"'"+yAxis_name_left+"'");
			ser_str=ser_str.replace("spline","'spline'");
			ser_str=ser_str.replace("#89A54E","'#89A54E'");
			ser_str=ser_str.replace(yAxis_name_right,"'"+yAxis_name_right+"'");
			ser_str=ser_str.replace("column","'column'");
			ser_str=ser_str.replace("#4572A7","'#4572A7'");
			cat_obj = eval("("+cat_str+")");     
			ser_obj = eval("("+ser_str+")");
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
								color: "#89A54E"
							}
						},
						title: {
							text: yAxis_name_left,
							style: {
								color: "#89A54E"
							}
						},
						tickPositions: yAxisData
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
		}
	});

	$.get("badHandoverCell/getzhichaCell_model", paramsLTE_1, function(data){
		var fieldArr=[];
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
function openOverlapCoverModel(cell, city) {
	var paramsLTE_1 = {
		cell : cell,
		city : city
	};
	$.get("badHandoverCell/getOverlapCover_model", paramsLTE_1, function(data){
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
function openInterfereCellModel(cell, record) {
	$("#config_information_interferenceCell").modal();
	$("#getfirstInterferenceCellTab a:first").tab("show");
	$("#InterferenceCellWorstCellContainer").empty();
	$("#interferenceCell_model").empty();

	var paramsLTE_1 = {
		cell : cell,
		hour : record.hour_id,
		city:record.city,
   
		table : "badHandoverCell_ex"
	};
		//console.log(paramsLTE_1);
	$.ajax({
		url:"badHandoverCell/getGanraoCell_chart",
		data:paramsLTE_1,
		type:"get",
		success:function(data){
			var data=JSON.parse(data);
			if(data.result=="error"){
				$("#InterferenceCellWorstCellContainer").html("数据为空！");
			}else{
				$("#InterferenceCellWorstCellContainer").css("width","850px").css("hight","400px");
				var categories=eval("["+data.key+"]");
				var series=eval("["+data.data+"]");
				var chart = new Highcharts.Chart("InterferenceCellWorstCellContainer", {
					title: {
								text: cell+"/干扰",
								x: -20
							},
					xAxis: {
								categories: categories
							},
					yAxis: {
								title: {
									text: "干扰 (dBm)"
								},
								plotLines: [{
									value: 0,
									width: 1,
									color: "#808080"
								}]
							},
					tooltip: {
								valueSuffix: "dBm"
							},
					legend: {
								layout: "vertical",
								align: "right",
								verticalAlign: "middle",
								borderWidth: 0
							},
					series: [{
								name: "干扰",
								data: series
							}]
				});         
			}
		}
	});
	$.get("badHandoverCell/getInterfereCell_model", paramsLTE_1, function(data){
		var fieldArr=[];
		var text=data.content.split(",");
		var filename = data.filename;
		for(var i in data.rows[0]){       
			fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:textWidth(i)};
		} 
		var newData = data.rows;
		$("#interferenceCell_model").grid("destroy", true, true);
		var alarmWorstCellTable = $("#interferenceCell_model").grid({
			columns:fieldArr,
			dataSource:newData,
			pager: { limit: 5, sizes: [5, 10, 15, 20] },
			autoScroll:true,
			uiLibrary: "bootstrap",
			primaryKey:"dateTime_id"
		});
	});
}
function openParameterModel(cell, city) {

	getBaselineCheckData(cell, city);
}
function bsc_table_model(url, id, params){
	$.get(url, params, function(data){
		var fieldArr=[];
		var text=data.content.split(",");
		var filename = data.filename;
		for(var i in data.rows[0]){       
			fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:textWidth(i)};
		} 
		var newData = data.rows;

		$(id).grid("destroy", true, true);
		var alarmWorstCellTable = $(id).grid({
			columns:fieldArr,
			dataSource:newData,
			pager: { limit: 5, sizes: [5, 10, 15, 20] },
			autoScroll:true,
			uiLibrary: "bootstrap",
		});
	});
}
function getBaselineCheckData(cell,city){
	$(".baselineTableIndex").val("");
	var params = {
		cell:cell,
		table:"ParaCheckBaseline",
		city:city
	};
	$.get("badHandoverCell/getBaselineCheckData", params,function(data){
		data = JSON.parse(data);
		for (var i in data) {
			if(data[i].record == 0){
				$("#baselineTableIndex_"+i).empty();
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
//准备阶段
function doSearchEvent_table(cell,city){
	var params_rrc = {
			city:city,
			cell:cell
	};
	var fieldArr=[];
	fieldArr[fieldArr.length]={field:"date_id",title:"date_id",width:100};
	fieldArr[fieldArr.length]={field:"eventName",title:"eventName",width:200};
	fieldArr[fieldArr.length]={field:"ecgi",title:"ecgi",width:150};
	fieldArr[fieldArr.length]={field:"result",title:"result",width:300};
	fieldArr[fieldArr.length]={field:"times",title:"times",width:100};
	fieldArr[fieldArr.length]={field:"timesTotal",title:"timesTotal",width:100};
	fieldArr[fieldArr.length]={field:"ratio",title:"ratio(%)",width:80};
	var fieldCol=fieldArr; 
	if(fieldCol==false){return false;}
	$("#rrcResultTable").grid("destroy", true, true);
	var grid = $("#rrcResultTable").grid({
		columns:fieldCol,
		dataSource:{ 
			url: "badHandoverCell/getRrcResultTableData", 
			success: function(data){
				if(data.error){
					$("#rrcResultTable").grid("destroy", true, true);
					return;
				}
				grid.render(data);
			} 
		},
		params : params_rrc,
		pager: { limit: 10, sizes: [10, 20, 50, 100] },
		autoScroll:true,
		uiLibrary: "bootstrap",
		primaryKey : "id",
		autoLoad: true   
	});
}
function rrcResult(cell,city){
	var params_rrc = {
		city:city,
		cell:cell
	};
	var clickDetected = false;
	$.get("badHandoverCell/rrcResult", params_rrc,function(data){
		if(data.error == "数据库不存在！") {
			$("#rrcResultContainer").html("数据库不存在！");
		}else if(data.error == "数据表不存在！"){
			$("#rrcResultContainer").html("数据表不存在！");
		}else if(data.error == "数据为空！"){
			$("#rrcResultContainer").html("数据为空！");
		}else if(data.error == "ecgi数据为空！"){
			$("#rrcResultContainer").html("ecgi数据为空！");
		}else{
			$("#rrcResultContainer").highcharts({
				chart: {
					type: "bar"
				},
				title: {
					text: "准备失败原因值分布"+" ("+data.date+")"
				},
				subtitle: {
					text: "数量  "+data.tooltip
				},
				xAxis: {
					categories: data.categories,
					title: {
						text: null
					}
				},
				yAxis: {
					min: 0,
						title: {
						text: "ratio(%)",
						align: "high"
					},
					labels: {
						overflow: "justify"
					}
				},
				tooltip: {
					valueSuffix: " %"
				},
				plotOptions: {
					bar: {
						dataLabels: {
							enabled: true,
							allowOverlap: true
						}
					},
					series: {
						cursor: "pointer",
						point: {
							events: {
								click: function(e) {
									if (clickDetected) {
										params_rrc.ecgi = data.ecgi;
										params_rrc.result = this.category;
										params_rrc.table = this.name;
										getrrcResultDetail(params_rrc);
										$("#rrcResultDetailTable").grid("destroy", true, true);
										$("#config_information_rrcResultDetail").modal();
									} else {
										clickDetected = true;
										setTimeout(function() {
											clickDetected = false;
										}, 500);
									}
								}
							}
						}
					}
				},
				credits: {
					enabled: false
				},
				series: [{
					name: "ratio",
					data: data.yAxis//[107, 31, 635]
				}]
			});
		}  
	});
}

function  getrrcResultDetail(params_rrc){
	var E = Ladda.create( document.getElementById( "exportRrcDetail" ) );
	E.start();
	$("#loading").html("<img class='col-md-offset-5' src='dist/img/ajax-loader.gif'>加载中");
	params_temp = params_rrc;
	var tableId = "#rrcResultDetailTable";
	var fieldArr=[]; 
	$.post("badHandoverCell/getRrcResultDetailTableField",params_rrc,function(data){
		$("#loading").html("");    
		E.stop();
		$(tableId).grid("destroy", true, true);
		if (data.result == "error") {
			layer.open({
				title: "提示",
				content: "没有记录"
			});
			return;
		}else{
			for(var k in data){
				if (k == "establCause" || k == "eventTime" || k == "hoSrcOrTarget") {
					fieldArr[fieldArr.length]={field:k,title:k,width:300};
				}else if (k == "result" || k == "3gppCauseGroup" || k == "3gppCause"){
					fieldArr[fieldArr.length]={field:k,title:k,width:500};
				}else {
					fieldArr[fieldArr.length]={field:k,title:k,width:textWidth(k)};
				}
			}
			$(tableId).grid("destroy", true, true);
			$(tableId).grid({
				columns:fieldArr,
				dataSource: { url: "badHandoverCell/getRrcResultDetailData",type:"post", data: params_rrc},
				params : params_rrc,
				pager: { limit: 10, sizes: [10, 20, 50, 100] },
				autoScroll:true,
				uiLibrary: "bootstrap",
			});
		}
	});
}
function exportRrcDetail(){
	var E = Ladda.create( document.getElementById( "exportRrcDetail" ) );
	E.start();
	$.post("badHandoverCell/exportRrcResultDetail",params_temp,function(data){
		E.stop();
		if(data.result) {
			fileZipSave(data.fileName);
		} else {
			layer.open({
				title: "提示",
				content: "没有记录"
			});
		}
	});
}

//执行阶段
function doSearchEventExec_table(cell,city){
	var params_rrc = {
			city:city,
			cell:cell
	};
	var fieldArr=[];
	fieldArr[fieldArr.length]={field:"date_id",title:"date_id",width:100};
	fieldArr[fieldArr.length]={field:"eventName",title:"eventName",width:200};
	fieldArr[fieldArr.length]={field:"ecgi",title:"ecgi",width:150};
	fieldArr[fieldArr.length]={field:"result",title:"result",width:300};
	fieldArr[fieldArr.length]={field:"times",title:"times",width:100};
	fieldArr[fieldArr.length]={field:"timesTotal",title:"timesTotal",width:100};
	fieldArr[fieldArr.length]={field:"ratio",title:"ratio(%)",width:80};
	var fieldCol=fieldArr; 
	if(fieldCol==false){return false;}
	$("#rrcResultTable_RRCC").grid("destroy", true, true);
	var grid = $("#rrcResultTable_RRCC").grid({
		columns:fieldCol,
		dataSource:{ 
			url: "badHandoverCell/getExecResultTableData", 
			success: function(data){
				if(data.error){
					$("#rrcResultTable_RRCC").grid("destroy", true, true);
					return;
				}
				grid.render(data);
			} 
		},
		params : params_rrc,
		pager: { limit: 10, sizes: [10, 20, 50, 100] },
		autoScroll:true,
		uiLibrary: "bootstrap",
		primaryKey : "id",
		autoLoad: true   
	});
}
function execResult(cell,city){
	var params_rrc = {
		city:city,
		cell:cell
	};
	var clickDetected = false;
	$.get("badHandoverCell/execResult", params_rrc,function(data){
		if(data.error == "数据库不存在！") {
			$("#rrcResultContainer_RRCC").html("数据库不存在！");
		}else if(data.error == "数据表不存在！"){
			$("#rrcResultContainer_RRCC").html("数据表不存在！");
		}else if(data.error == "数据为空！"){
			$("#rrcResultContainer_RRCC").html("数据为空！");
		}else if(data.error == "ecgi数据为空！"){
			$("#rrcResultContainer_RRCC").html("ecgi数据为空！");
		}else{
			$("#rrcResultContainer_RRCC").highcharts({
				chart: {
					type: "bar"
				},
				title: {
					text: "执行失败原因值分布"+" ("+data.date+")"
				},
				subtitle: {
					text: "数量  "+data.tooltip
				},
				xAxis: {
					categories: data.categories,
					title: {
						text: null
					}
				},
				yAxis: {
					min: 0,
						title: {
						text: "ratio(%)",
						align: "high"
					},
					labels: {
						overflow: "justify"
					}
				},
				tooltip: {
					valueSuffix: " %"
				},
				plotOptions: {
					bar: {
						dataLabels: {
							enabled: true,
							allowOverlap: true
						}
					},
					series: {
						cursor: "pointer",
						point: {
							events: {
								click: function(e) {
									if (clickDetected) {
										params_rrc.ecgi = data.ecgi;
										params_rrc.result = this.category;
										params_rrc.table = this.name;
										getexecResultDetail(params_rrc);
										$("#execResultDetailTable").grid("destroy", true, true);
										$("#config_information_execResultDetail").modal();
									} else {
										clickDetected = true;
										setTimeout(function() {
											clickDetected = false;
										}, 500);
									}
								}
							}
						}
					}
				},
				credits: {
					enabled: false
				},
				series: [{
					name: "ratio",
					data: data.yAxis//[107, 31, 635]
				}]
			});
		}  
	});
}

function  getexecResultDetail(params_rrc){
	var E = Ladda.create( document.getElementById( "exportExecDetail" ) );
	E.start();
	$("#loading").html("<img class='col-md-offset-5' src='dist/img/ajax-loader.gif'>加载中");
	params_temp = params_rrc;
	var tableId = "#execResultDetailTable";
	var fieldArr=[]; 
	$.post("badHandoverCell/getExecResultDetailTableField",params_rrc,function(data){
		$("#loading").html("");    
		E.stop();
		$(tableId).grid("destroy", true, true);
		if (data.result == "error") {
			layer.open({
				title: "提示",
				content: "没有记录"
			});
			return;
		}else{
			for(var k in data){
				if (k == "establCause" || k == "eventTime" || k == "hoSrcOrTarget") {
					fieldArr[fieldArr.length]={field:k,title:k,width:300};
				}else if (k == "result" || k == "3gppCauseGroup" || k == "3gppCause"){
					fieldArr[fieldArr.length]={field:k,title:k,width:500};
				}else {
					fieldArr[fieldArr.length]={field:k,title:k,width:textWidth(k)};
				}
			}
			$(tableId).grid("destroy", true, true);
			$(tableId).grid({
				columns:fieldArr,
				dataSource: { url: "badHandoverCell/getExecResultDetailData",type:"post", data: params_rrc},
				params : params_rrc,
				pager: { limit: 10, sizes: [10, 20, 50, 100] },
				autoScroll:true,
				uiLibrary: "bootstrap",
			});
		}
	});
}
function exportExecDetail(){
	var E = Ladda.create( document.getElementById( "exportExecDetail" ) );
	E.start();
	$.post("badHandoverCell/exportExecResultDetail",params_temp,function(data){
		E.stop();
		if(data.result) {
			fileZipSave(data.fileName);
		} else {
			layer.open({
				title: "提示",
				content: "没有记录"
			});
		}
	});
}
function getCounterLoseResultDistribution(cell) {
	var params = {
		cell: cell
	};
	$.get("highLostCell/getCounterLoseResultDistribution", params, function (data) {
		if (data["categories"].length == 0) {
			$("#counterLoseResultDistribution").html(cell + "暂无数据！");
			return;
		}
		$("#counterLoseResultDistribution").highcharts({
			chart: {
				type: "column"
			},
			title: {
				text: "Counter失败原因值分布"
			},
			subtitle: {
				text: "日期:" + data["date"] + "-至今"
			},
			xAxis: {
				categories: data["categories"],
				crosshair: true
			},
			yAxis: {
				min: 0,
				title: {
					text: "原因值"
				}
			},
			plotOptions: {
				column: {
					pointPadding: 0.2,
					borderWidth: 0,
					dataLabels:{
						enabled:true// dataLabels设为true
					}
				}
			},
			series: [{
				name: "Counter失败原因值分布",
				data: data["yAxis"]
			}]
		});
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
	// var city = $("#node").val();
	var node = $("#node option:selected");
	// var remoteIp = node.val();

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
	// $.ajax({
	//  type: "post",
	//  url: "lowAccessCell/storage",
	//  data : params,
	//  success: function (data) {
 //            $("#list").html(data)
 //        }
	// });
	$.post("badHandoverCell/storage", params, function (data) {
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
	// $("#taskName_form").data("bootstrapValidator").validate();
	// var flag = $("#taskName_form").data("bootstrapValidator").isValid();
	// if (!flag) {
	//  return;
	// }
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
	// } else if (taskType == "ctrfull") {
	//  taskType = "ctrfullsystem";
	}
	prepareTask(taskName, tracePath, taskType, saveBtn);
}
function updateMonitor(taskName) {
	var data = {"taskName": encodeURI(taskName)};
	$.ajax({
		type: "get",
		url: "badHandoverCell/monitor",
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
function initValidata() {
	$("#taskName_form").bootstrapValidator({
		message: "This value is not valid",
		feedbackIcons: {
			valid: "glyphicon glyphicon-ok",
			invalid: "glyphicon glyphicon-remove",
			validating: "glyphicon glyphicon-refresh"
		},
		fields: {
			taskName: {
				validators: {
					notEmpty: {
						message: "任务名不能为空"
					},
					regexp: {
						/* 只需加此键值对，包含正则表达式，和提示 */
						regexp: /^[a-zA-Z0-9_$]+$/,
						message: "只能包含数字，字母，$和_"
					}

				}
			}
		}
	});
}
function prepareTask(name, tracePath, type, saveBtn) {
	var myDate = new Date().Format("yyyy-MM-dd hh:mm:ss");
	var data = {
		"tracePath": encodeURI(tracePath),
		"taskName": encodeURI(name),
		"createTime": myDate,
		"type": type

	};
	/*if(type == "ctrsystem" | type == "cdrsystem" | type == "ebmsystem" | type == "ctrfullsystem"){
	 console.log(type);
	 var treeNodes=$("#eventQueryTree").treeview("getNode",0);
	 data.taskConfig = JSON.stringify(treeNodes);
	 }*/
	// var saveBtn = Ladda.create(document.getElementById("saveBtn"));
	// var cancelBtn2 = Ladda.create(document.getElementById("cancelBtn2"));
	// saveBtn.start();
	// cancelBtn2.start();
	$.ajax({
		type: "POST",
		url: "badHandoverCell/addTask",
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
		url: "badHandoverCell/runTask",
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
				$.post("badHandoverCell/deleteAutoDir",{"tracePath":tracePath});
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
			{field: "准备切换失败数", title: "准备切换失败数", width: 100},
			{field: "执行切换失败数", title: "执行切换失败数", width: 100},
		]]
	});
}

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

function getCTRJump(record) {
	var S = Ladda.create( document.getElementById( "openCtrJumpBtn" ) );
	S.start();
	var date = new Date();
	var month = (date.getMonth()+1) > 10 ? (date.getMonth()+1) : "0"+(date.getMonth()+1);
	var day = (date.getDate()) >10 ? (date.getDate()) : "0"+(date.getDate());
	var hour;
	if(record.hour_id<10){
		hour="0"+record.hour_id;
	}else{
		hour=record.hour_id;
	}

	var ctrDldPoint = ""+date.getFullYear()+month+day+hour; 
	var rrcFailureNum = record["准备切换失败数(今日)"];
	var erabEstNum = record["执行切换失败数(今日)"];

	var city = record.city;
	var cell = record.cell;
	var type="ctr";
	// var ctrDldPoint = ""+date.getFullYear()+month+day+"06";    //代码测试
	// var cell = "LF31G43C";                                     //代码测试
	var params = {
		type  : type,
		point : ctrDldPoint,
		city  : city,
		cell  : cell,
		rrc   : rrcFailureNum,
		erab  : erabEstNum
	};
	$.post("badHandoverCell/ctrTreeItems", params, function (data) {
		S.stop();
		// var returnData = JSON.parse(data);
		$("#ctrData").html("");  

		$("#ctrData").append(data);
		// $("#fileTable").treegrid("loadData", returnData);
	});
}


function getCTRJumpvolte(record) {
	var S = Ladda.create( document.getElementById( "openCtrJumpBtn" ) );
	S.start();
	var date = new Date();
	var month = (date.getMonth()+1) > 10 ? (date.getMonth()+1) : "0"+(date.getMonth()+1);
	var day = (date.getDate()) >10 ? (date.getDate()) : "0"+(date.getDate());
	var hour;
	if(record.hour_id<10){
		hour="0"+record.hour_id;
	}else{
		hour=record.hour_id;
	}

	var ctrDldPoint = ""+date.getFullYear()+month+day+hour; 
	var rrcFailureNum = 0;
	var erabEstNum = 0;
	// console.log(typeof record.City);
	var a = record.City;
	// console.log(typeof a);
	var city;
	if(a.indexOf("CZ")!=-1){
		city = "changzhou";
	}
	if(a.indexOf('NT')!=-1) {
		city = "nantong";
	}
	if (a.indexOf('SZ')!=-1) {
		city = "suzhou";
	}
	if (a.indexOf('ZJ')!=-1) {
		city = "zhenjiang";
	}
	if (a.indexOf('WX')!=-1) {
		city = "wuxi";
	}
	console.log(city);
	var cell = record.EutranCellTdd;
	var type = "ctr";
	// var ctrDldPoint = ""+date.getFullYear()+month+day+"06";    //代码测试
	// var cell = "LF31G43C";                                     //代码测试
	var params = {
		type  : type,
		point : ctrDldPoint,
		city  : city,
		cell  : cell,
		rrc   : rrcFailureNum,
		erab  : erabEstNum
	};
	$.post("badHandoverCell/ctrTreeItems", params, function (data) {
		S.stop();
		// var returnData = JSON.parse(data);
		$("#ctrData").html("");  

		$("#ctrData").append(data);
		// $("#fileTable").treegrid("loadData", returnData);
	});
}
  


function getRelevanceChart(record){
	var categories = [];
	var myData = [];
	var categories_ = [];
	var myData_ = [];
	if (record["切换成功率_准备切换成功率"] >= record["切换成功率_执行切换成功率"]) {
		categories = ["切换成功率_准备切换成功率","切换成功率_执行切换成功率"];
		myData = [parseFloat(record["切换成功率_准备切换成功率"]),parseFloat(record["切换成功率_执行切换成功率"])];
	} else {
		categories = ["切换成功率_执行切换成功率","切换成功率_准备切换成功率"];
		myData = [parseFloat(record["切换成功率_执行切换成功率"]),parseFloat(record["切换成功率_准备切换成功率"])];
	}
	if(record["切换成功率_干扰"]>=record["切换成功率_质差"]){
	if(record["切换成功率_质差"]>=record["切换成功率_弱覆盖"]){
			categories_ = ["切换成功率_干扰","切换成功率_质差","切换成功率_弱覆盖"];
			myData_ = [parseFloat(record["切换成功率_干扰"]),parseFloat(record["切换成功率_质差"]),parseFloat(record["切换成功率_弱覆盖"])];
		}else if(record["切换成功率_干扰"]>=record["切换成功率_弱覆盖"]){
			categories_ = ["切换成功率_干扰","切换成功率_弱覆盖","切换成功率_质差"];
			myData_ = [parseFloat(record["切换成功率_干扰"]),parseFloat(record["切换成功率_弱覆盖"]),parseFloat(record["切换成功率_质差"])];
		}else{
			categories_ = ["切换成功率_弱覆盖","切换成功率_干扰","切换成功率_质差"];
			myData_ = [parseFloat(record["切换成功率_弱覆盖"]),parseFloat(record["切换成功率_干扰"]),parseFloat(record["切换成功率_质差"])];
		}
		}else{
			if(record["切换成功率_干扰"]>=record["切换成功率_弱覆盖"]){
			categories_ = ["切换成功率_质差","切换成功率_干扰","切换成功率_弱覆盖"];
			myData_ = [parseFloat(record["切换成功率_质差"]),parseFloat(record["切换成功率_干扰"]),parseFloat(record["切换成功率_弱覆盖"])];
		}else if(record["切换成功率_质差"]<=record["切换成功率_弱覆盖"]){
			categories_ = ["切换成功率_弱覆盖","切换成功率_质差","切换成功率_干扰"];
			myData_ = [parseFloat(record["切换成功率_弱覆盖"]),parseFloat(record["切换成功率_质差"]),parseFloat(record["切换成功率_干扰"])];
		}else{
			categories_ = ["切换成功率_质差","切换成功率_弱覆盖","切换成功率_干扰"];
			myData_ = [parseFloat(record["切换成功率_质差"]),parseFloat(record["切换成功率_弱覆盖"]),parseFloat(record["切换成功率_干扰"])];
		}
		}


	var myCharts = Highcharts.chart("container_Relevance", {
		chart: {
			type: "bar"
		},
		title: {
			text: null
		},
		xAxis: {
			categories: categories,
			title: {
				text: null
			}
		},
		yAxis: {
			min: 0,
			title: {
				text: "相关系数",
				align: "high"
			},
			labels: {
				overflow: "justify"
			}
		},
		plotOptions: {
			bar: {
				dataLabels: {
					enabled: true,
					allowOverlap: true
				}
			}
		},
		credits: {
			enabled: false
		},
		series: [{
			name: "相关系数",
			data: myData
		}]
	});
	var myCharts_ = Highcharts.chart("container_Relevance_", {
		chart: {
			type: "bar"
		},
		title: {
			text: null
		},
		xAxis: {
			categories: categories_,
			title: {
				text: null
			}
		},
		yAxis: {
			min: 0,
			title: {
				text: "相关系数",
				align: "high"
			},
			labels: {
				overflow: "justify"
			}
		},
		plotOptions: {
			bar: {
				dataLabels: {
					enabled: true,
					allowOverlap: true
				}
			}
		},
		credits: {
			enabled: false
		},
		series: [{
			name: "相关系数",
			data: myData_
		}]
	});
}
function getNeighBadHandoverCellTable(cell,hour){
	$("#neighBadHandoverCellTable_zhaozi").show(); 
	$("#neighBadHandoverCellTable_loadingImg").show();
	$.post("badHandoverCell/getNeighBadHandoverCellTable",{cell:cell,hour:hour},function(data){
		$("#neighBadHandoverCellTable_zhaozi").hide(); 
		$("#neighBadHandoverCellTable_loadingImg").hide();
		$("#neighBadHandoverCellTable").grid("destroy", true, true);
		if (data.result == "error") {
			$("#neighBadHandoverCellTable").html("没有记录");
			// layer.open({
			//     title: "提示",
			//     content: "没有记录"
			// });

			return;
		}else{

			var text = data.text.split(",");
			var fieldArr = [];
			for(var i in text){
				fieldArr[i]={field:text[i],title:text[i],width:textWidth(text[i]),sortable:true};
			}
			$("#neighBadHandoverCellTable").grid("destroy", true, true);
			$("#neighBadHandoverCellTable").grid({
				columns:fieldArr,
				dataSource: data.records,         
				sortname:"切换成功率",
				sortorder:"desc",
				// sortcolumn:" distance ",
				// sortdirection:"asc",
				pager: { limit: 10, sizes: [10, 20, 50, 100] },
				autoScroll:true,
				uiLibrary: "bootstrap",
			});
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
function textWidth(text){
	var length = text.length;
	if(length > 15){
		return length*10;
	}
	return 150;
}
function switchTab(div1,div2,type){
	$(div2).removeClass("active");
	$(div1).addClass("active");
}
function switchTab_RRCC(div1,div2,type){
	$(div2).removeClass("active");
	$(div1).addClass("active");
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
function checkSwitchOut(){
	$(".switchRadio").eq(0).prop("checked","checked");
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

function getYestdayFormatDate() {
	var date = new Date();
	var seperator1 = "-";
	var month = date.getMonth() + 1;
	var strDate = date.getDate()-1;
	if (month >= 1 && month <= 9) {
		month = "0" + month;
	}
	if (strDate >= 0 && strDate <= 9) {
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

//指标-图
function getIndexChartData(record){
	$("#chart_zhaozi").show(); 
	$("#chart_loadingImg").show();
	var yAxis_name_left = ["切换成功率", "准备切换成功率", "执行切换成功率"];
	var yAxis_name_right = $("#worstCellChartAuxiliaryAxisType").val(); 
	var tableChart1 = "badHandoverCell";
	var table = "FMA_alarm_log";
	var params1={
		db:"AutoKPI",
		table:"badHandoverCell",
		rowCell:record.cell,
		hour:record.hour_id,
		yAxis_name_left:yAxis_name_left,
		yAxis_name_right:yAxis_name_right
	}; 
	$.get("badHandoverCell/getIndexChartData",params1,function(data){ 
		$("#chart_zhaozi").hide(); 
		$("#chart_loadingImg").hide();
		$("#worstCellContainer").empty();
		var cat_str =JSON.stringify(JSON.parse(data).categories);
		var ser_str = JSON.stringify(JSON.parse(data).series);
		var yAxisSetData = JSON.stringify(JSON.parse(data).yAxis_set);
		var cell_str = JSON.parse(data).cell;
	
		var yAxisSetData_str = yAxisSetData.replace(/"/g,"");
		ser_str=ser_str.replace(/"/g,"");
		ser_str=ser_str.replace("#89A54E","'#89A54E'");
		if(yAxis_name_right.length == 2) {
			var arr1 = yAxis_name_right[0];
			var arr2 = yAxis_name_right[1];
			ser_str=ser_str.replace(arr1,"'"+arr1+"'");
			ser_str=ser_str.replace(arr2,"'"+arr2+"'");
		}else {
			ser_str=ser_str.replace(yAxis_name_right,"'"+yAxis_name_right+"'");
		}
		if(yAxis_name_left.length == 3) {
			var arr1 = yAxis_name_left[0];
			var arr2 = yAxis_name_left[1];
			var arr3 = yAxis_name_left[2];
			ser_str=ser_str.replace(arr1,"'"+arr1+"'");
			ser_str=ser_str.replace(arr2,"'"+arr2+"'");
			ser_str=ser_str.replace(arr3,"'"+arr3+"'");
		}else {
			var arr0 = yAxis_name_left[0];
			ser_str=ser_str.replace(arr0,"'"+arr0+"'");
		}
		ser_str=ser_str.replace("spline3","'spline'");
		ser_str=ser_str.replace("spline4","'spline'");
		ser_str=ser_str.replace("spline5","'spline'");
		ser_str=ser_str.replace("spline1","'spline'");
		ser_str=ser_str.replace("spline2","'spline'");
		
		ser_str=ser_str.replace("#4572A7","'#4572A7'");
		ser_str=ser_str.replace("#87CEFF","'#87CEFF'");
		ser_str=ser_str.replace("#F4A460","'#F4A460'");
		ser_str=ser_str.replace("#D1EEEE","'#D1EEEE'");
		ser_str=ser_str.replace("#AAAAAA","'#AAAAAA'");
		var  cat_obj = eval("("+cat_str+")");     
		var  ser_obj = eval("("+ser_str+")");
		var yAxisSetData_obj = eval("("+yAxisSetData_str+")");
		myChart = Highcharts.chart("worstCellContainer", {
			chart: {
				zoomType: "xy"
			},
			title: {
				text: selectCell+" / 切换差小区"
			},
			xAxis: [{
				categories: cat_obj,
				crosshair: true
			}],
			yAxis: yAxisSetData_obj,
			tooltip: {
				shared: true
			},
			series: ser_obj
		});
	}); 
}
function fileSave(){
	filename = $("#badCellFileIndex").val();
	// download(filename);
	fileZipSave(filename);
}
//指标-表
function getIndexTableData(record){
	var params2={
		db:"AutoKPI",
		rowCell:record.cell,
		city:$("#allCity").val(),
		table:"highLostCell"
	}; 
	$.get("badHandoverCell/getIndexTableData", params2,function(data){
		var fieldArr=[];
		var text=(JSON.parse(data).content).split(",");
		var filename = JSON.parse(data).filename;
		$("#badCellFileIndex").val(filename);
		for(var i in JSON.parse(data).rows[0]){        
			fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:150,sortable:true};     
		} 
		var newData = JSON.parse(data).rows;
		$("#badCellTableIndex").grid("destroy", true, true);
		var badCellTable = $("#badCellTableIndex").grid({
			columns:fieldArr,
			dataSource:newData,
			pager: { limit: 10, sizes: [10, 20, 50, 100] },
			autoScroll:true,
			uiLibrary: "bootstrap",
		});
	});
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
						"<div id='switch' class='box-body' style='position: relative;'>"+
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

function getNeighborCellMapData(params){
	$.post("badHandoverCell/getNeighborCellMapData", params, function(data){
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