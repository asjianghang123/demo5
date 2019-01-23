var alarmpolar;
var rrcnumpolar;
var ganraopolar;
$(document).ready(function() {
	
	//设置日期 
	$("#allHour").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText:"请选择小时",
		//filterPlaceholder:"搜索",
		nSelectedText:"项被选中",
		includeSelectAllOption:true,
		selectAllText:"全选/取消全选",
		allSelectedText:"已选中所有平台类型",
		maxHeight:200,
		maxWidth:"100%"
	});
	// setTime();
	getAllCity();   
	//设置表格
	//setTable();
	var inputType = $("#inputCategory").val();
	if(inputType == "lowAccessCell") {
		setLowTime();
		toogle("lowAccessCell");
	}else if(inputType == "highLostCell") {
		// setHighlostTime();
		// toogle("highLostCell");
	}else if(inputType == "badHandoverCell") {
		// setBadHandoverTime();
		// toogle("badHandoverCell");
	}
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
	switchRadio();

	initTable();   //这是什么原理？显示bug？
	$("#ctrJump").on("shown.bs.modal", function () {
		initTable();//wtf
		var saveBtn = Ladda.create(document.getElementById("runBtn"));
		saveBtn.stop();
		$("#log").html("");
		$("#taskName").val("");
		var data = $("#ctrData").html();
		var returnData = JSON.parse(data);
		$("#fileTable").treegrid("loadData", returnData);
	});
});

function openConfigInfo() {
	$("#config_information").modal();
}

function closeModal() {
	$("#cancelBtn2").modal("hide");
}

function updateConfigInfo(){
	$("#config_information").modal("hide");
	$("#config_information_alarm").modal("hide");
	$("#config_information_neighborCell").modal("hide");
	$("#config_information_weakCoverCell").modal("hide");
	$("#config_information_zhichaCell").modal("hide");
	$("#config_information_overlapCoverCell").modal("hide");
	$("#config_information_interferenceCell").modal("hide");
	$("#config_information_parameter").modal("hide");
}


//-------设置日期------//
function setLowTime(){
	$("#startTime").datepicker({format: "yyyy-mm-dd"});  //返回日期
	$("#endTime").datepicker({format: "yyyy-mm-dd"});

	var d = new Date();
	d.setTime(d.getTime()-24*60*60*1000);
	var s = d.getFullYear()+"-" + ((d.getMonth()+1)>=10?(d.getMonth()+1):"0"+(d.getMonth()+1)) + "-" + (d.getDate()>=10?d.getDate():"0"+d.getDate());
	$("#startTime").val(s);

	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth()+1;
	var day = nowTemp.getDate();
	var today = year +"-"+(month>=10?month:"0"+month)+"-"+(day>=10?day:"0"+day);
	$("#endTime").val(today);

	// console.log(today);
	var params = {
		city:getFirstCity()
	};
	/*$.get("lowAccessCell/lowStartTime", params, function(data){
		var sdata = [];
		for(var i=0; i<data.length; i++){
			if(data[i] === today){
				continue;
			}
			sdata.push(data[i]);
		}
		sdata.push(today);
		$("#startTime").datepicker("setValues", sdata);
		$("#endTime").datepicker("setValues", sdata);
	});
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#startTime").datepicker({
		onRender: function(date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function(ev) {
		checkin.hide();
	}).data("datepicker");
	var checkout = $("#endTime").datepicker({
		onRender: function(date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function(ev) {
		checkout.hide();
	}).data("datepicker");*/
}
/*function setHighlostTime(){
	$("#startTime").datepicker({format: "yyyy-mm-dd"});  //返回日期
	$("#endTime").datepicker({format: "yyyy-mm-dd"});

	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth()+1;
	var day = nowTemp.getDate();
	var today = year +"-"+month+"-"+day;

	console.log(today);
	var params = {
		city:getFirstCity()
	};
	$.get("lowAccessCell/setHighlostTime", params, function(data){
		var sdata = [];
		for(var i=0; i<data.length; i++){
			if(data[i] === today){
				continue;
			}
			sdata.push(data[i]);
		}
		sdata.push(today);
		$("#startTime").datepicker("setValues", sdata);
		$("#endTime").datepicker("setValues", sdata);
	});
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#startTime").datepicker({
		onRender: function(date) {
		return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function(ev) {
		checkin.hide();
	}).data("datepicker");
	var checkout = $("#endTime").datepicker({
		onRender: function(date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function(ev) {
		checkout.hide();
	}).data("datepicker");
}*/
/*function setBadHandoverTime(){
	$("#startTime").datepicker({format: "yyyy-mm-dd"});  //返回日期
	$("#endTime").datepicker({format: "yyyy-mm-dd"});

	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth()+1;
	var day = nowTemp.getDate();
	var today = year +"-"+month+"-"+day;

	console.log(today);
	var params = {
		city:getFirstCity()
	};
	$.get("lowAccessCell/setBadHandoverTime", params, function(data){
		var sdata = [];
		for(var i=0; i<data.length; i++){
			if(data[i] === today){
				continue;
			}
			sdata.push(data[i]);
		}
		sdata.push(today);
		$("#startTime").datepicker("setValues", sdata);
		$("#endTime").datepicker("setValues", sdata);
	});
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#startTime").datepicker({
		onRender: function(date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function(ev) {
		checkin.hide();
	}).data("datepicker");
	var checkout = $("#endTime").datepicker({
		onRender: function(date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function(ev) {
		checkout.hide();
	}).data("datepicker");
}*/

//----------获得城市----------//

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
	var url = "lowAccessCell/getAllCity";
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

function getChooseCitys(){
	var citys = $("#allCity").val();
	return citys;
}


function getParams(table,inputType){
	//var inputType = $("#worstCellType").val();
	var type;
	if(inputType == "低接入小区") {
		type = "lowAccessCell_ex";
	}else if(inputType == "高掉线小区") {
		type = "highLostCell_ex";
	}else if(inputType == "切换差小区") {
		type = "badHandoverCell_ex";
	}
	var startTime   = $("#startTime").val();
	var endTime     = $("#endTime").val();
	var citys       = $("#allCity").val();
	var hours       = $("#allHour").val();

	if(citys == null){
		// alert("Please choose city first!");
		layer.open({
			title: "提示",
			content: "Please choose city first!"
		});
		return false;
	}
	var cell= $("#cellInput").val();
	var params = {
		startTime:startTime,
		endTime:endTime,
		city:citys,
		//city:JSON.stringify(citys),//citys,
		//subNet:JSON.stringify(subNetworks),//subNetworks,
		table:type,
		cell:cell,
		hour:hours
		//action:action 
	};
	return params;
}

var bsc_type = function (route,block,params) {
	$.ajax({
		type : "GET",
		url : route,
		data : params,
		dataType : "json",
		success: function(data) {
			$(block).html("");
			if(data.length == 0){
				return;
			}
			//console.log(data);
			$(block).highcharts({
				chart: {
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false,
					type: "pie"
				},
				title: {
					text: null
				},
				subtitle: {
					text: null
				},
				tooltip: {
					pointFormat: ": <b>{point.y}({point.percentage:.2f} %)</b>"
				},
				plotOptions: {
					pie: {
						size:"130px",
						allowPointSelect: true,
						cursor: "pointer",
						dataLabels: {
							enabled: true,
							format: "<b>{point.name}</b>: {point.percentage:.2f} %",
							style: {
								color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || "black"
							}
						},
						showInLegend: true
					}
				},
				credits: {
					enabled: false,
				},
				series: [{
					name: "Brands",
					colorByPoint: true,
					data: data.series
				}]
			});
		}
	});
}
var ctrCity;
function doSearchbadCell(table,worstCellType){
	var l = Ladda.create( document.getElementById( "search" ) );
	var E = Ladda.create( document.getElementById( "export" ) );
	var X = Ladda.create( document.getElementById( "volteexport" ) );
	l.start();
	E.start();
	X.start();
	var params = getParams(table,worstCellType);
	if(params == false){
		l.stop();
		E.stop();
		X.stop();
		return false;
	}
	$.get("lowAccessCell/templateQuery", params, function(data){
		var fieldArr=[];
		var fieldArr1=[];
		var text=(JSON.parse(data).content).split(",");
		var text1=(JSON.parse(data).content1).split(",");
		text.splice(0,1);
		text1.splice(0,1);
		var texts = [];
		for (var i = 0; i < text.length; i++) {
			if(text[i] == "小时数low"){
				continue;
			}
			texts.push(text[i]);
		}
		var texts1 = [];
		for (var i = 0; i < text1.length; i++) {
			texts1.push(text1[i]);
		}
		var filename = JSON.parse(data).filename;
		var filename1 = JSON.parse(data).filename1;
		$("#badCellFile").val(filename);
		$("#badCellFilevolte").val(filename1);
		/*if(worstCellType == "file"){
			csvZipDownload(filename);
		}*/
		for(var i in texts){  
			//console.log(JSON.parse(data).rows[0]);  
			if (i == "id" || i== "小时数low") {
				continue;
			}else{
				fieldArr[fieldArr.length]={field:texts[i],title:texts[i],width:150,sortable:true};
			}
		}
		for(var i in texts1){  
			fieldArr1[fieldArr1.length]={field:texts1[i],title:texts1[i],width:150,sortable:true};
		}
		//console.log(fieldArr);  
		//fieldArr[fieldArr.length] = "{ width: 50, tmpl: <a href="#">edit</a>, align: "center", events: { "click": Edit } }";
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
		$("#badCellTablevolte").grid("destroy", true, true);
		var badCellTablevolte = $("#badCellTablevolte").grid({
			columns:fieldArr1,
			dataSource:newData1,
			pager: { limit: 10, sizes: [10, 20, 50, 100] },
			autoScroll:true,
			uiLibrary: "bootstrap",
			primaryKey:"id"
		});
		l.stop();
		E.stop();
		X.stop();
		var myChart;

		badCellTablevolte.on("rowSelect", function (e, $row, id, record) {
			$("#currentAlarmNum").removeClass();
			$("#less116ProportionNum").removeClass();
			$("#overlapCoverNum").removeClass();
			$("#less155ProportionNum").removeClass();
			$("#needAddNeighNum").removeClass();
			$("#AvgPRBNum").removeClass();
			$("#highTrafficNum").removeClass();
			$("#highTrafficNum2").removeClass();
			$("#parameterNum").removeClass();
			getCTRJumpvolte(record);
			var weakcover = record['RSRP<-116的比例'];
   			params1 = {
   				num: record['RSRP<-116的比例'],
   				num2: record['RSRQ<-15.5的比例']
   			};
   			$.ajax({
				type:"get",
				url:"lowAccessCell/weakcover",
				data: params1,
				dataType:"json",
				async : false,
				success:function(data){
					weakcoverpolar = data["Polar-弱覆盖"];
					if(data["RSRQ<-15.5的比例"]){
						$("#less155Proportion").val(data["RSRQ<-15.5的比例"]+"%"); 

					}else{
						$("#less155Proportion").val(""); 
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
            if(weakcover){
				$("#less116Proportion").val(weakcover+"%");
			}else{
				$("#less116Proportion").val("");
			} 
			// if(weakcoverpolar == 0) {  //弱覆盖
			// 	$("#less116ProportionNum").removeClass();
			// 	$("#less116ProportionNum").addClass("glyphicon glyphicon-ok-circle");
			// 	$("#less116ProportionNum").css("color","green");
			// }else if(weakcoverpolar >0 && weakcoverpolar < 50) {
			// 	$("#less116ProportionNum").removeClass();
			// 	$("#less116ProportionNum").addClass("glyphicon glyphicon-exclamation-sign");
			// 	$("#less116ProportionNum").css("color","orange");
			// }else if(weakcoverpolar >= 50) {
			// 	$("#less116ProportionNum").removeClass();
			// 	$("#less116ProportionNum").addClass("glyphicon glyphicon-remove-sign");
			// 	$("#less116ProportionNum").css("color","red");
			// }
			var zhicha = record["下行CQI<3的比例"];
			$("#cqi").val(zhicha+"%"); 
			// var zhichapolarValue = 50 + zhicha*2.5;
   //          if (zhichapolarValue > 100) {
   //              zhichapolarValue = 100;
   //          }
   			var zhichapolarValue = "";
            if(record["RSRQ<-15.5的比例"]){
				$("#less155Proportion").val(record["RSRQ<-15.5的比例"]+"%"); 

			}else{
				$("#less155Proportion").val(""); 
			}
            if(zhichapolarValue == 0) {  //质差
				$("#less155ProportionNum").removeClass();
				$("#less155ProportionNum").addClass("glyphicon glyphicon-ok-circle");
				$("#less155ProportionNum").css("color","green");
			}else if(zhichapolarValue >0 && zhichapolarValue < 50) {
				$("#less155ProportionNum").removeClass();
				$("#less155ProportionNum").addClass("glyphicon glyphicon-exclamation-sign");
				$("#less155ProportionNum").css("color","orange");
			}else if(zhichapolarValue >= 50) {
				$("#less155ProportionNum").removeClass();
				$("#less155ProportionNum").addClass("glyphicon glyphicon-remove-sign");
				$("#less155ProportionNum").css("color","red");
			}
			// console.log(weakcoverpolar);
			var cell = record.cell;
			var date = record.day_id;
			var hour = record.hour_id;
			var city = record.city;
			var table = "temp_lowaccesscell";
			var params = {
				cell: cell,
				city: city,
				hour: hour,
				table: table,
				date: date
			};
			$.ajax({
				type:"get",
				url:"lowAccessCell/getVolteAlarmNum",
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
			// console.log(alarmpolar);
			$.ajax({
				type:"get",
				url:"lowAccessCell/getVolteAvgPrb",
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
				url:"lowAccessCell/highrrcnum",
				data: params,
				dataType:"json",
				async : false,
				success:function(data){
				// console.log(typeof data["告警数量"]);
					rrcnumpolar = data["Polar-最高RRC用户数"];
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
					if(data["Polar-最高RRC用户数"] >300&&data["MAC层时延"]>100) {   //高话务
						$("#highTrafficNum2").removeClass();
					// $("#highTrafficNum2").addClass("glyphicon glyphicon-ok-circle");
					// $("#highTrafficNum2").css("color","green");
						$("#highTraffic2").val(data["MAC层时延"]+"ms"); 
						$("#highTrafficNum2").addClass("glyphicon glyphicon-remove-sign");
						$("#highTrafficNum2").css("color","red");
					}else {
						$("#highTrafficNum2").removeClass();
						$("#highTrafficNum2").addClass("glyphicon glyphicon-ok-circle");
						$("#highTrafficNum2").css("color","green");
						$("#highTraffic2").val("非高话务"); 
					}
				}
			});
			$.ajax({
				type:"get",
				url:"lowAccessCell/lowaccesscellcanshu",
				data: params,
				dataType:"json",
				async : false,
				success:function(data){
				// console.log(typeof data["告警数量"]);
					canshupolar = data["Polar-参数"];
					if(data["参数"]||data["参数"]=="0.00"){
						$("#parameter").val(parseInt(data["参数"])+"个");  
					}else{
						$("#parameter").val("");  
					}
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
				flag: record['RSRP<-116的比例'],
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
				url:"lowAccessCell/avgta",
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
			// console.log(alarmpolar);
			// console.log(ganraopolar);
			$("#container").highcharts({
				chart: {
					polar: true
				},
				title: {
					text: null
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
				//data: data["data"],
					data: [
					parseInt(alarmpolar),
					parseInt(weakcoverpolar),
					parseInt(overlapcoverpolar),
					parseInt(zhichapolarValue),
					parseInt(neighnum),
					parseInt(ganraopolar),
					parseInt(canshupolar)],
					pointPlacement: "on",
					events: {
					click: function(e) {
						if(e.point.category === "告警") {
							openAlarmModel(record.cell);   
						}else if(e.point.category === "邻区") {
							openNeighborCellModel(record.cell, record.city); 
						}else if(e.point.category === "弱覆盖") {
							openWeakCoverCellModelvolte(record.cell, record.city);
						}else if(e.point.category === "质差") {
							openzhichaCellModel(record.cell, record.city);
						}else if(e.point.category === "重叠覆盖") {
							openOverlapCoverModel(record.cell, record.city);
						}else if(e.point.category === "干扰") {
							openInterfereCellModel(record.cell, record);
						}else if(e.point.category === "参数") {
							openParameterModel(record.cell, record.city);
						}         
					}
				}
				}]
			});
			// $.get("lowAccessCell/getparameter", params, function(data){
			// if(data["record"]||data["record"]=="0.00"){
			// 	$("#parameter").val(parseInt(data["record"])+"个");  
			// }else{
			// 	$("#parameter").val("");  
			// }
			// });
		});

		badCellTable.on("rowSelect", function (e, $row, id, record) {
			var canshu;
			$("#currentAlarmNum").removeClass();
			$("#less116ProportionNum").removeClass();
			$("#overlapCoverNum").removeClass();
			$("#less155ProportionNum").removeClass();
			$("#needAddNeighNum").removeClass();
			$("#AvgPRBNum").removeClass();
			$("#highTrafficNum").removeClass();
			$("#highTrafficNum2").removeClass();
			$("#parameterNum").removeClass();
			if(parseInt(record["Polar-参数"])>=50)
			{   
				canshu=parseInt(record["Polar-参数"]);
			}else{
				if(parseInt(record["sruser"])>=parseInt(record["最高RRC用户数"]))
				{    
					canshu=50;
				}else{
					canshu=parseInt(record["Polar-参数"]);
				}
			}
			//高干扰的应对处理原则
			getHighInterfereHandlePrinciple(record);
			//CTR跳转
			getCTRJump(record);
			ctrCity = record["city"];
			$("#container").highcharts({
				chart: {
					polar: true
				},
				title: {
					text: null
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
				//data: data["data"],
					data: [
					parseInt(record["Polar-告警"]),
					parseInt(record["Polar-弱覆盖"]),
					parseInt(record["Polar-重叠覆盖"]),
					parseInt(record["Polar-质差"]),
					parseInt(record["Polar-邻区"]),
					parseInt(record["Polar-干扰"]),
					canshu],
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
			//相关性
			getRelevanceChart(record);
			//参数开始
			// console.log(record)
			// getBaselineCheckData(record.cell,record.city);
			//无线接通率&干扰/无线接通率&质差相关性分析
			// myCharts.destroy();
			$("#relevance_backups").html("");
			//无线接通率&质差
			getWirelessCallRate_zhicha(record.cell,record.city);    
			//无线接通率&干扰
			getWirelessCallRate_interfere(record.cell,record.city);  
			//无线接通率&RRC建立成功率
			getWirelessCallRate_RRCEstSucc(record.cell,record.city);  
			//无线接通率&ERAB建立成功率
			getWirelessCallRate_ERABEstSucc(record.cell,record.city);  
			//Counter失败原因值分布0524
			getCounterLoseResultDistribution(record.cell);  
			//参数结束
			$("#getfirstTab a:first").tab("show");

			//获取诊断数据数量
			// $("#currentAlarm").val("");          //告警数量
			// $("#needAddNeigh").val("");          //邻区-需要加邻区数量
			// $("#less116Proportion").val("");     //弱覆盖-RSRP<-116的比例
			// $("#less155Proportion").val("");     //质差-RSRQ<-15.5的比例
			// $("#overlapCover").val("");          //重叠覆盖-重叠覆盖度
			// $("#AvgPRB").val("");                //干扰-平均PRB
			// $("#parameter").val("");             //参数
			// $("#highTraffic").val("");           //最高RRC用户数
			$("#wirelessCallRate_interfere").val(""); //无线接通率&干扰
			$("#wirelessCallRate_zhicha").val(""); //无线接通率&质差
			$("#wirelessCallRate_RRCEstSucc").val(""); //无线接通率&RRC建立成功率
			$("#wirelessCallRate_ERABEstSucc").val(""); //无线接通率&ERAB建立成功率
			// getNumOfDiagnosisData(record.cell,record.city);
			// getNumOfDiagnosisDataFilter_alarm(record.cell,record.city);//告警
			// $("#alarm_loadingImg").hide();   //告警
			$("#currentAlarm").val(record["告警数量"]+"条");          //告警数量
			// getNumOfDiagnosisDataFilter_weakCover(record.cell,record.city);//弱覆盖
			// $("#weakCocer_loadingImg").hide();    //弱覆盖
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

			// getNumOfDiagnosisDataFilter_zhicha(record.cell,record.city);//质差
			// $("#zhicha_loadingImg").hide();
			// getNumOfDiagnosisDataFilter_overlapCover(record.cell,record.city);//重叠覆盖
			// $("#overlapCover_loadingImg").hide();    //重叠覆盖
			if(record["重叠覆盖度"] == null||record["重叠覆盖度"]=="MRConnectFailed"){
				$("#overlapCover").val("");
			}else{
				$("#overlapCover").val(record["重叠覆盖度"]+"%");
			}
			// getNumOfDiagnosisDataFilter_AvgPRB(record.cell,record.city);//干扰
			// $("#avgPRB_loadingImg").hide();    //干扰
			if(record["平均PRB"] == null || record["平均PRB"] == 0.00 || record["平均PRB"] == 0 ){
				$("#AvgPRB").val("无数据"); 
			}else{
				var prb=record["平均PRB"].split("--");
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
			// getNumOfDiagnosisDataFilter_highTraffic(record.cell,record.city);//最高RRC用户数
			// $("#highTraffic_loadingImg").hide();    //最高RRC用户数
			if(record["最高RRC用户数"] == null || record["最高RRC用户数"] == 0 || record["最高RRC用户数"] == 0.00){
				$("#highTraffic").val("无数据"); 
			}else{
				$("#highTraffic").val(record["最高RRC用户数"]+"个");                 
			}
			//$("#highTraffic").val(record["最高RRC用户数"]+"个"); 
			// getNumOfDiagnosisDataFilter_parameter(record.cell,record.city);//参数
			// $("#parameter_loadingImg").hide();    //参数
			if(record["参数"]||record["参数"]=="0.00"){
				$("#parameter").val(parseInt(record["参数"])+"个");  
			}else{
				$("#parameter").val("");  

			}
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
				height_body+=20;
				$("#licenseState_label").show();
				$("#licenseState").val(record["licenseState"]);     
			}else{
				$("#licenseState_label").hide();
				$("#licenseState").val("");   
			}
			if(parseInt(record["srUser"])<parseInt(record["最高RRC用户数"]))
			{   
				height_body+=20;
				$("#srUser_label").show();
				$("#srUser").val(parseInt(record["srUser"]));

			}else{
				$("#srUser_label").hide();
				$("#srUser").val("");
			}
			$("#collapseThree_1").css("height",height_body+"px");
			$("#collapseFour").css("height",height_body+"px");
			$("#collapseSix").css("height",height_body+"px");
			// getNumOfDiagnosisData_MR(record.cell,record.city);  //将MR查询分离出来-邻区
			// $("#needAddNeigh_loadingImg").hide();
			if(record["需要加邻区数量"] == null || record["需要加邻区数量"] == "MRConnectFailed"){
				$("#needAddNeigh").val("");
			}else{
				$("#needAddNeigh").val(parseInt(record["需要加邻区数量"])+"条");
			}


			$("#rrcResultContainer").html("<img class='col-md-offset-5' src='dist/img/ajax-loader.gif'>");
			switchTab(table_tab_1,table_tab_0,"chart");
			switchTab_RRCC(table_tab_3,table_tab_2,"chart");
			var params_rrc = {
				city:record.city,
				cell:record.cell,
				day_from:$("#startTime").val(),
				day_to:$("#endTime").val(),
			};
			var clickDetected = false;
			$.get("lowAccessCell/rrcResult", params_rrc,function(data){
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
						text: "RRC失败原因值分布"+" ("+data.date+")"
					},
						subtitle: {
						text: "数量  "+data.tooltip
					},
						xAxis: {
						categories: data.categories,//["EVENT_VALUE_FAILURE_IN_RADIO_PROCEDURE", "EVENT_VALUE_REJECT_DUE_TO_REATTEMPT", "EVENT_VALUE_SUCCESS"],
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
									// dbclick: function () {
									//     alert("Category: " + this.category + ", value: " + this.y);
									// },
									click: function(e) {
										if (clickDetected) {
											params_rrc.ecgi = data.ecgi;
											params_rrc.result = this.category;
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
					/*legend: {
						layout: "vertical",
						align: "right",
						verticalAlign: "top",
						x: -40,
						y: 100,
						floating: true,
						borderWidth: 1,
						backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || "#FFFFFF"),
						shadow: true
					},*/
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

		//0524
			$("#rrcResultContainer_RRCC").html("<img class='col-md-offset-5' src='dist/img/ajax-loader.gif'>");
			$.get("lowAccessCell/rrcResult_erab", params_rrc, function (data) {
				if (data.error == "数据库不存在！") {
				$("#rrcResultContainer_RRCC").html("数据库不存在！");
			} else if (data.error == "数据表不存在！") {
				$("#rrcResultContainer_RRCC").html("数据表不存在！");
			} else if (data.error == "数据为空！") {
				$("#rrcResultContainer_RRCC").html("数据为空！");
			} else if (data.error == "ecgi数据为空！") {
				$("#rrcResultContainer_RRCC").html("ecgi数据为空！");
			} else {
				$("#rrcResultContainer_RRCC").highcharts({
					chart: {
					type: "bar"
				},
					title: {
					text: "ERAB失败原因值分布" + " (" + data.date + ")"
				},
				/*subtitle: {
					text: "数量  " + data.tooltip
				},*/
					xAxis: {
					categories: data.categories,
					title: {
						text: null
					}
				},
					yAxis: {
					min: 0,
					title: {
						text: "数量",
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
								click: function (e) {
									if (clickDetected) {
										params_rrc.ecgi = data.ecgi;
										params_rrc.result = this.category;
										getrrcResultDetail(params_rrc);
										$("#rrcResultDetailTable").grid("destroy", true, true);
										$("#config_information_rrcResultDetail").modal();
									} else {
										clickDetected = true;
										setTimeout(function () {
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
					data: data.yAxis
				}]
				});
			}
			});

			doSearchEvent_table(record.cell,record.city);
	//0524
			doSearchEvent_table_rrcc(record.cell, record.city);

			$("#alarmNumSpan").removeClass();
			$("#LteNumSpan").removeClass();
			$("#GsmNumSpan").removeClass();
			$("#weakCoverNumSpan").removeClass();
			// $("#erbsAlarmNumSpan").removeClass();
			$("#highInterfereNumSpan").removeClass();
			$("#overlapCeakCoverNumSpan").removeClass();
			$("#firstOrderConflictNumSpan").removeClass();
			$("#secondOrderConflictNumSpan").removeClass();
			$("#prbHighInterfereNumSpan").removeClass();

			// $("#mapCell").val("");
			$("#mapCell").val(record.cell);
			var alarmNum = $("#mapCell").val();
	// var CELL = $("#mapCell").val();
	// $.get("alarmNum", {cell:alarmNum,startTime:$("#startTime").val(),endTime:$("#endTime").val(),hours:$("#allHour").val()}, function(data){
	//     data = eval("("+data+")");
	//     $("#alarmNum").val(data[1]);
	//     $("#alarmNumHour").val(data[3]);
	//     if($("#alarmNum").val()==0 && ($("#alarmNumHour").val()==0 || $("#alarmNumHour").val()=="null")){
	//         $("#alarmNumSpan").removeClass();
	//         $("#alarmNumSpan").addClass("glyphicon glyphicon-ok-circle");
	//         $("#alarmNumSpan").css("color","green");
	//     }else{
	//         $("#alarmNumSpan").removeClass();
	//         $("#alarmNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
	//         $("#alarmNumSpan").css("color","red");
	//     }
	//     $("#erbsAlarmNum").val(data[0]);
	//     $("#erbsAlarmNumHour").val(data[2]);
	//     if($("#erbsAlarmNum").val()==0 && ($("#erbsAlarmNumHour").val()==0 || $("#erbsAlarmNumHour").val()=="null")){
	//         $("#erbsAlarmNumSpan").removeClass();
	//         $("#erbsAlarmNumSpan").addClass("glyphicon glyphicon-ok-circle");
	//         $("#erbsAlarmNumSpan").css("color","green");
	//     }else{
	//         $("#erbsAlarmNumSpan").removeClass();
	//         $("#erbsAlarmNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
	//         $("#erbsAlarmNumSpan").css("color","red");
	//     }
	// });
	// $.get("badCell/conflictNum",{cell:alarmNum},function(data){
	//     data = JSON.parse(data);
	//     $("#firstOrderConflictNum").val(data.firstConflictNum);
	//     if($("#firstOrderConflictNum").val()==0){
	//         $("#firstOrderConflictNumSpan").removeClass();
	//         $("#firstOrderConflictNumSpan").addClass("glyphicon glyphicon-ok-circle");
	//         $("#firstOrderConflictNumSpan").css("color","green");
	//     }else{
	//         $("#firstOrderConflictNumSpan").removeClass();
	//         $("#firstOrderConflictNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
	//         $("#firstOrderConflictNumSpan").css("color","red");
	//     }
	//     $("#secondOrderConflictNum").val(data.secondConflictNum);
	//     if($("#secondOrderConflictNum").val()==0){
	//         $("#secondOrderConflictNumSpan").removeClass();
	//         $("#secondOrderConflictNumSpan").addClass("glyphicon glyphicon-ok-circle");
	//         $("#secondOrderConflictNumSpan").css("color","green");
	//     }else{
	//         $("#secondOrderConflictNumSpan").removeClass();
	//         $("#secondOrderConflictNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
	//         $("#secondOrderConflictNumSpan").css("color","red");
	//     }
	// });
	// $.get("overlapCeakCoverNum", {cell:alarmNum,city:record.city,startTime:$("#startTime").val(),endTime:$("#endTime").val()},function(data) {
	//     $("#overlapCeakCoverNum").val(data);
	//     if($("#overlapCeakCoverNum").val()==0) {
	//       $("#overlapCeakCoverNumSpan").removeClass();
	//       $("#overlapCeakCoverNumSpan").addClass("glyphicon glyphicon-ok-circle");
	//       $("#overlapCeakCoverNumSpan").css("color","green");
	//     }else{
	//       $("#overlapCeakCoverNumSpan").removeClass();
	//       $("#overlapCeakCoverNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
	//       $("#overlapCeakCoverNumSpan").css("color","red");
	//     }
	// });
			$("#jiditu_loadingImg").show();   //极地图
			$("#jiditu_zhaozi").show();       //极地图
	// $("#alarm_loadingImg").show();    //告警
	// $("#avgPRB_loadingImg").show();    //干扰
	// $("#weakCocer_loadingImg").show();    //弱覆盖
	// $("#parameter_loadingImg").show();    //参数
	// $("#highTraffic_loadingImg").show();    //最高RRC用户数
	// $("#overlapCover_loadingImg").show();    //重叠覆盖
	// $("#needAddNeigh_loadingImg").show();    //邻区
	// $("#zhicha_loadingImg").show();    //质差
			$("#wirelessCallRate_interfere_loadingImg").show();    //无线接通率&干扰
			$("#wirelessCallRate_zhicha_loadingImg").show();    //无线接通率&质差
	// $("#relevance_zhaozi").show();            //相关性
	// $("#relevance_loadingImg").show();       //相关性
	// $("#relevance_zhaozi_").show();            //相关性
	// $("#relevance_loadingImg_").show();       //相关性
			$("#wirelessCallRate_RRCEstSucc_loadingImg").show();       //无线接通率&RRC建立成功率
			$("#wirelessCallRate_ERABEstSucc_loadingImg").show();       //无线接通率&ERAB建立成功率
	// console.log(record);
   
			$("#chart_loadingImg").show();
			$("#chart_zhaozi").show(); 
  
	// $.get("badCell/getPolarMapData", {cell:alarmNum,city:record.city,startTime:$("#startTime").val(),endTime:$("#endTime").val()}, function(data) {
			$("#jiditu_loadingImg").hide(); //极地图
			$("#jiditu_zhaozi").hide();     //极地图
		// $("#alarm_loadingImg").hide();   //告警
		// $("#avgPRB_loadingImg").hide();    //干扰
		// $("#weakCocer_loadingImg").hide();    //弱覆盖
		// $("#parameter_loadingImg").hide();    //参数
		// $("#highTraffic_loadingImg").hide();    //最高RRC用户数
		// $("#overlapCover_loadingImg").hide();    //重叠覆盖
		// $("#needAddNeigh_loadingImg").hide();    //邻区
		// $("#zhicha_loadingImg").hide();    //质差

		// console.log(data);
		// data = [200, 300, 4, 500,45];
		//if(data["data"][0] == 0) {   //告警
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
			if(record["Polar-最高RRC用户数"] == 0) {   //最高RRC用户数
				$("#highTrafficNum").removeClass();
				$("#highTrafficNum").addClass("glyphicon glyphicon-ok-circle");
				$("#highTrafficNum").css("color","green");
			}else if(record["Polar-最高RRC用户数"] >0 && record["Polar-最高RRC用户数"] < 50) {
				$("#highTrafficNum").removeClass();
				$("#highTrafficNum").addClass("glyphicon glyphicon-exclamation-sign");
				$("#highTrafficNum").css("color","orange");
			}else if(record["Polar-最高RRC用户数"] >= 50) {
				$("#highTrafficNum").removeClass();
				$("#highTrafficNum").addClass("glyphicon glyphicon-remove-sign");
				$("#highTrafficNum").css("color","red");
			} 
			if(record["Polar-最高RRC用户数"] >300&&record["MAC层时延"]>100) {   //高话务
				$("#highTrafficNum2").removeClass();
			// $("#highTrafficNum2").addClass("glyphicon glyphicon-ok-circle");
			// $("#highTrafficNum2").css("color","green");
				$("#highTraffic2").val(record["MAC层时延"]+"ms"); 
				$("#highTrafficNum2").addClass("glyphicon glyphicon-remove-sign");
				$("#highTrafficNum2").css("color","red");
			}else if(record["Polar-最高RRC用户数"] >400 ) {
				if(record["RRC建立失败次数(最新)"]){
					if(record["SRcongestion数"]/record["RRC建立失败次数(最新)"] > 0.5){
						$("#highTrafficNum2").removeClass();
						$("#highTraffic2").val(record["SR拥塞比"]+"%"); 
						$("#highTrafficNum2").addClass("glyphicon glyphicon-remove-sign");
						$("#highTrafficNum2").css("color","red");
					}
				}
			}else {
				$("#highTrafficNum2").removeClass();
				$("#highTrafficNum2").addClass("glyphicon glyphicon-ok-circle");
				$("#highTrafficNum2").css("color","green");
				$("#highTraffic2").val("非高话务"); 
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
		
	//});
	// $(".zhaozi").show();
	// $(".loadingImg").show();
	//var mapv = initMap("map");
	//$("#rowSelect").val(record.cell);
	//0605
			var tableChart = $("#chooseTable").val();
	/*var tableChart;
	if($("#chooseTable").val() == "lowAccessCell") {
		//var table = "FMA_alarm_log";
		tableChart = "lowAccessCell";
		window.mapv = initMap("map");
		//getCellData(mapv,e,$row,id,record);
		drawMapOut("origin");
	}else if($("#chooseTable").val() == "highLostCell"){
		//var table = "FMA_alarm_log";
		tableChart = "highLostCell";
		window.mapv = initMap("map");
		//getCellData(mapv,e,$row,id,record);
		drawMapOut("origin");
	}else if($("#chooseTable").val() == "badHandoverCell"){
		//var table = "FMA_alarm_log";
		tableChart = "badHandoverCell";
		window.mapv = initMap("map");
		//getCellData(mapv,e,$row,id,record);
		drawMapOut("origin");
	}*/
			var hours = $("#allHour").val();
			var params = {
				cell:record.cell,
				day_from:$("#startTime").val(),
				day_to:$("#endTime").val(),
				hours:hours
			};
	// $.get("badCell/getPrbNum", params, function(data){
	//     data = eval("("+data+")");
	//     $("#prbHighInterfereNum").val(data[0]);
	//     $("#prbHighInterfereNumHour").val(data[1]);
	//     if($("#prbHighInterfereNum").val()==0 && ($("#prbHighInterfereNumHour").val()==0 || $("#prbHighInterfereNumHour").val()=="null")){
	//       $("#prbHighInterfereNumSpan").removeClass();
	//       $("#prbHighInterfereNumSpan").addClass("glyphicon glyphicon-ok-circle");
	//       $("#prbHighInterfereNumSpan").css("color","green");
	//     }else{
	//       $("#prbHighInterfereNumSpan").removeClass();
	//       $("#prbHighInterfereNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
	//       $("#prbHighInterfereNumSpan").css("color","red");
	//     }
	// });

	//小区级告警分类
			if(worstCellType == "低接入小区") {
				bsc_table("lowAccessCell/getCellAlarmClassifyTable", "#cellAlarmTable", params);  //当前告警
				bsc_table("lowAccessCell/getErbsAlarmClassifyTable", "#erbsAlarmTable", params);  //历史告警
			} else {
				bsc_type("lowAccessCell/getCellAlarmClassify","#cellAlarmClassify",params); 
				bsc_type("lowAccessCell/getErbsAlarmClassify","#erbsAlarmClassify",params);
			}
 
	//干扰分析
	// $.get("badCell/getInterfereAnalysis", params, function(data){
	//   $("#highInterfereNum").val(data.records);
	//   $("#highInterfereNumHour").val(data.recordsHour);
	//   if($("#highInterfereNum").val()==0 && ($("#highInterfereNumHour").val()==0 || $("#highInterfereNumHour").val()=="null")){
	//       $("#highInterfereNumSpan").removeClass();
	//       $("#highInterfereNumSpan").addClass("glyphicon glyphicon-ok-circle");
	//       $("#highInterfereNumSpan").css("color","green");
	//   }else{
	//       $("#highInterfereNumSpan").removeClass();
	//       $("#highInterfereNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
	//       $("#highInterfereNumSpan").css("color","red");
	//   }

	//     $("#interfere_zhaozi").hide();
	//     $("#interfere_loadingImg").hide();
	//     var fieldArr=[];
	//     var text=data.content.split(",");
	//     //var filename = data.filename;
	//     for(var i in data.rows[0]){       
	//       fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:150,sortable:true};
	//     }
	//     var newData = data.rows;
	//     $("#interfereAnalysis").grid("destroy", true, true);
	//     var interfereAnalysis = $("#interfereAnalysis").grid({
	//       columns:fieldArr,
	//       dataSource:newData,
	//       pager: { limit: 10, sizes: [10, 20, 50, 100] },
	//       autoScroll:true,
	//       uiLibrary: "bootstrap",
	//     });
	// });

			var params = {
				table:tableChart,
				//table:table,
				rowCell:record.cell
			};

	// $.get("badCell/getalarmWorstCell", params, function(data){
	//     $("#alarm_zhaozi").hide();
	//     $("#alarm_loadingImg").hide();
  
	//     var fieldArr=[];
	//     var text=data.content.split(",");
	//     var filename = data.filename;
	//     //$("#alarmWorstCellTable").val(filename);
	//     for(var i in data.rows[0]){       
	//       fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:150};
	//     } //console.log(fieldArr);
	//     var newData = data.rows;

	//     $("#alarmWorstCellTable").grid("destroy", true, true);
	//       var alarmWorstCellTable = $("#alarmWorstCellTable").grid({
	//       columns:fieldArr,
	//       dataSource:newData,
	//       pager: { limit: 10, sizes: [10, 20, 50, 100] },
	//       autoScroll:true,
	//       uiLibrary: "bootstrap",
	//     });
	// });

	//here
			if(tableChart == "lowAccessCell"){
				var params2={
				db:"AutoKPI",
				rowCell:record.cell,
				startTime:$("#startTime").val(),
				endTime:$("#endTime").val(),
				city:$("#allCity").val(),
				table:"lowAccessCell"
			}; 
			$.get("lowAccessCell/getLowAccessCellData", params2,function(data){
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
			}else if(tableChart == "highLostCell"){
				var params2={
				db:"AutoKPI",
				rowCell:record.cell,
				startTime:$("#startTime").val(),
				endTime:$("#endTime").val(),
				city:$("#allCity").val(),
				table:"highLostCell"
			}; 
		$.get("lowAccessCell/getHighLostCellData", params2,function(data){
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
	}else if(tableChart == "badHandoverCell"){
		var params2={
			db:"AutoKPI",
			rowCell:record.cell,
			startTime:$("#startTime").val(),
			endTime:$("#endTime").val(),
			city:$("#allCity").val(),
			table:"badHandoverCell"
		}; 
		$.get("lowAccessCell/getBadHandoverCellData", params2,function(data){
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
	// var yAxis_name_left  = $("#worstCellChartPrimaryAxisType").val();
			var yAxis_name_left = ["无线接通率", "RRC建立成功率", "ERAB建立成功率"];
			var yAxis_name_right = $("#worstCellChartAuxiliaryAxisType").val(); 
			var startTime = $("#startTime").val();  //返回日期
			var endTime = $("#endTime").val();
			var tableChart1;
			if($("#chooseTable").val() == "lowAccessCell") {
		var table = "FMA_alarm_log";
				tableChart1 = "lowAccessCell";
			}else if($("#chooseTable").val() == "highLostCell"){
		var table = "FMA_alarm_log";
		tableChart1 = "highLostCell";
	}else if($("#chooseTable").val() == "badHandoverCell"){
		var table = "FMA_alarm_log";
		tableChart1 = "badHandoverCell";
	}

			var params1={
		db:"AutoKPI",
		table:tableChart1,
				rowCell:record.cell,
				hour:record.hour_id,
				startTime:startTime,
				endTime:endTime,
				yAxis_name_left:yAxis_name_left,
				yAxis_name_right:yAxis_name_right
			}; 
			$.get("lowAccessCell/getChartData",params1,function(data){ 
		$("#chart_zhaozi").hide(); 
		$("#chart_loadingImg").hide();
		$("#worstCellContainer").empty();
		/*var cat_str =JSON.stringify(data.categories);
		var ser_str = JSON.stringify(data.series);*/
		var cat_str =JSON.stringify(JSON.parse(data).categories);
		var ser_str = JSON.stringify(JSON.parse(data).series);
		var yAxisSetData = JSON.stringify(JSON.parse(data).yAxis_set);
		var cell_str = JSON.parse(data).cell;
		var yAxisSetData_str = yAxisSetData.replace(/"/g,"");
		ser_str=ser_str.replace(/"/g,"");
		// ser_str=ser_str.replace(yAxis_name_left,"""+yAxis_name_left+""");
		// ser_str=ser_str.replace("spline",""spline"");
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
			// alert(yAxis_name_left[0]);
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
		// ser_str=ser_str.replace("column","'column'");
		// ser_str=ser_str.replace("column1","'column'");
		ser_str=ser_str.replace("spline3","'spline'");
		ser_str=ser_str.replace("spline4","'spline'");
		ser_str=ser_str.replace("spline5","'spline'");
		ser_str=ser_str.replace("spline1","'spline'");
		ser_str=ser_str.replace("spline2","'spline'");
		
		// ser_str=ser_str.replace("spline5","'spline'");
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
				text: $("#mapCell").val()+" / " +worstCellType
			},
			xAxis: [{
				categories: cat_obj,
				crosshair: true
			}],
			yAxis: yAxisSetData_obj,
			tooltip: {
				shared: true
			},
			// legend: {
			//     layout: "vertical",
			//     align: "left",
			//     x: 80,
			//     verticalAlign: "top",
			//     y: 55,
			//     floating: true,
			//     backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || "#FFFFFF"
		// },
			series: ser_obj
		});
	}); 

			$("#worstCellChartPrimaryAxisType").change(function(){
		myChart.destroy();
		$("#chart_zhaozi").show(); 
		$("#chart_loadingImg").show();
		$("#worstCellContainer").empty();
		var yAxis_name_left  = $("#worstCellChartPrimaryAxisType").val();
		var yAxis_name_right = $("#worstCellChartAuxiliaryAxisType").val(); 
		var startTime = $("#startTime").val();  //返回日期
		var endTime = $("#endTime").val();
		var table,tableChart;
		if($("#chooseTable").val() == "lowAccessCell") {
			table = "FMA_alarm_log";
			tableChart = "lowAccessCell_ex";
		}else if($("#chooseTable").val() == "highLostCell"){
			table = "FMA_alarm_log";
			tableChart = "highLostCell";
		}else if($("#chooseTable").val() == "badHandoverCell"){
			table = "FMA_alarm_log";
			tableChart = "badHandoverCell";
		}
 
		var params={
			db:"AutoKPI",
			table:tableChart,
			rowCell:record.cell,
			startTime:startTime,
			endTime:endTime,
			yAxis_name_left:yAxis_name_left,
			yAxis_name_right:yAxis_name_right
		}; 

		$.get("lowAccessCell/getChartData",params,function(data){ 
			$("#chart_zhaozi").hide(); 
			$("#chart_loadingImg").hide();
			$("#worstCellContainer").empty();
			/*var cat_str =JSON.stringify(data.categories);
			var ser_str = JSON.stringify(data.series);*/
			var cat_str =JSON.stringify(JSON.parse(data).categories);
			var ser_str = JSON.stringify(JSON.parse(data).series);
			var yAxisSetData = JSON.stringify(JSON.parse(data).yAxis_set);
			var cell_str = JSON.parse(data).cell;

			var yAxisSetData_str = yAxisSetData.replace(/"/g,"");
			ser_str=ser_str.replace(/"/g,"");
			ser_str=ser_str.replace(yAxis_name_left,"'"+yAxis_name_left+"'");
			ser_str=ser_str.replace("spline","'spline'");
			ser_str=ser_str.replace("#89A54E","'#89A54E'");
		
			if(yAxis_name_right.length == 2) {
				var arr1 = yAxis_name_right[0];
				var arr2 = yAxis_name_right[1];
				ser_str=ser_str.replace(arr1,"'"+arr1+"'");
				ser_str=ser_str.replace(arr2,"'"+arr2+"'");
			}else {
				ser_str=ser_str.replace(yAxis_name_right,"'"+yAxis_name_right+"'");
			}
			ser_str=ser_str.replace("column","'column'");
			ser_str=ser_str.replace("column1","'column'");
			ser_str=ser_str.replace("#4572A7","'#4572A7'");
			ser_str=ser_str.replace("#87CEFF","'#87CEFF'");
			var cat_obj = eval("("+cat_str+")");     
			var ser_obj = eval("("+ser_str+")");
			var yAxisSetData_obj = eval("("+yAxisSetData_str+")");

			myChart = Highcharts.chart("worstCellContainer", {
				chart: {
					zoomType: "xy"
				},
				title: {
					text: $("#mapCell").val()+" / " +worstCellType
				},
				xAxis: [{
					categories: cat_obj,
					crosshair: true
				}],
				yAxis: yAxisSetData_obj,
				tooltip: {
					shared: true
				},
				// legend: {
				//     layout: "vertical",
				//     align: "left",
				//     x: 80,
				//     verticalAlign: "top",
				//     y: 55,
				//     floating: true,
				//     backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || "#FFFFFF"
				// },
				series: ser_obj
			});
		}); 
	});

			$("#worstCellChartAuxiliaryAxisType").change(function(){
		var arr_worstCellChartAuxiliaryAxisType = $("#worstCellChartAuxiliaryAxisType").val();
		if(arr_worstCellChartAuxiliaryAxisType.length>2) {
			layer.open({
				title: "提示",
				content: "选择两个以内的副轴!"
			});
			return;
		}
		myChart.destroy();
		$("#chart_zhaozi").show(); 
		$("#chart_loadingImg").show();
		$("#worstCellContainer").empty();
		// var yAxis_name_left  = $("#worstCellChartPrimaryAxisType").val();
		var yAxis_name_left = ["无线接通率", "RRC建立成功率", "ERAB建立成功率"];
		var yAxis_name_right = $("#worstCellChartAuxiliaryAxisType").val(); 
		var startTime = $("#startTime").val();  //返回日期
		var endTime = $("#endTime").val();
		var table,tableChart;
		if($("#chooseTable").val() == "lowAccessCell") {
			table = "FMA_alarm_log";
			tableChart = "lowAccessCell_ex";
		}else if($("#chooseTable").val() == "highLostCell"){
			table = "FMA_alarm_log";
			tableChart = "highLostCell";
		}else if($("#chooseTable").val() == "badHandoverCell"){
			table = "FMA_alarm_log";
			tableChart = "badHandoverCell";
		}

		var params={
			db:"AutoKPI",
			table:tableChart,
			rowCell:record.cell,
			startTime:startTime,
			endTime:endTime,
			yAxis_name_left:yAxis_name_left,
			yAxis_name_right:yAxis_name_right
		}; 
		$.get("lowAccessCell/getChartData",params,function(data){ 

			$("#chart_zhaozi").hide(); 
			$("#chart_loadingImg").hide();
			$("#worstCellContainer").empty();
			/*var cat_str =JSON.stringify(data.categories);
			var ser_str = JSON.stringify(data.series);*/
			var cat_str =JSON.stringify(JSON.parse(data).categories);
			var ser_str = JSON.stringify(JSON.parse(data).series);
			var yAxisSetData = JSON.stringify(JSON.parse(data).yAxis_set);
			var cell_str = JSON.parse(data).cell;
		
			var yAxisSetData_str = yAxisSetData.replace(/"/g,"");
			ser_str=ser_str.replace(/"/g,"");
			// ser_str=ser_str.replace(yAxis_name_left,"'"+yAxis_name_left+"'");
			ser_str=ser_str.replace("spline","'spline'");
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
				// alert(yAxis_name_left[0]);
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
			ser_str=ser_str.replace("column","'column'");
			ser_str=ser_str.replace("column1","'column'");
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
					text: $("#mapCell").val()+" / " +worstCellType
				},
				xAxis: [{
					categories: cat_obj,
					crosshair: true
				}],
				yAxis: yAxisSetData_obj,
				tooltip: {
					shared: true
				},
				// legend: {
				//     layout: "vertical",
				//     align: "left",
				//     x: 80,
				//     verticalAlign: "top",
				//     y: 55,
				//     floating: true,
				//     backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || "#FFFFFF"
				// },
				series: ser_obj
			});
		});
	});
			var paramsLTE_1 = {
		input1:3,
		input2:3,
		input3:50,
		input4:10,
		input5:50,
		cell : record.cell,
		dateTime : $("#startTime").val(),
		city : record.city
	};
			$.post("lowAccessCell/getLTENeighborHeader_1",paramsLTE_1,function(data){
		if(data.error == "error"){
		// $("#LTE_zhaozi").hide();
		// $("#LTE_loadingImg").hide();
			return;
		}
		var fieldArr=[];
		for(var k in data){
			if(fieldArr.length == 0){
				fieldArr[fieldArr.length]={field:k,title:k,hidden : true};
			}else{
				if (k == "datetime_id") {
					fieldArr[fieldArr.length]={field:k,title:k,width:180};
				}else{
					fieldArr[fieldArr.length]={field:k,title:k,width:textWidth(k)};
				}
			}
		}
		$("#LTETable_1").grid("destroy", true, true);
		var grid = $("#LTETable_1").grid({
			columns:fieldArr,
			params:paramsLTE,
			dataSource:{
				url: "badCell/getLTENeighborData_1", 
				success: function(data){
					data = eval("("+data+")");
					grid.render(data);
					// $("#LTE_zhaozi").hide();
					// $("#LTE_loadingImg").hide();
					/*$("#LteNum").val(data.total_is0);
					if($("#LteNum").val()==0){
						$("#LteNumSpan").removeClass();
						$("#LteNumSpan").addClass("glyphicon glyphicon-ok-circle");
						$("#LteNumSpan").css("color","green");
					}else{
						$("#LteNumSpan").removeClass();
						$("#LteNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
						$("#LteNumSpan").css("color","red");
					}*/
				} 
			},
			pager: { limit: 10, sizes: [10, 20, 50, 100] },
			autoScroll:true,
			uiLibrary: "bootstrap"
		});
	});

			var paramsLTE = {
		input1:3,
		input2:3,
		input3:50,
		input4:10,
		input5:50,
		cell : record.cell,
		dateTime : $("#startTime").val(),
		city : record.city
	};
			$.post("lowAccessCell/getLTENeighborHeader",paramsLTE,function(data){
		if(data.error == "error"){
			$("#LTE_zhaozi").hide();
			$("#LTE_loadingImg").hide();
			return;
		}
		var fieldArr=[];
		for(var k in data){
				if(fieldArr.length == 0){
					fieldArr[fieldArr.length]={field:k,title:k,hidden : true};
				}else{
					if (k == "datetime_id") {
						fieldArr[fieldArr.length]={field:k,title:k,width:180};
					}else{
						fieldArr[fieldArr.length]={field:k,title:k,width:textWidth(k)};
					}
				}
			}
		$("#LTETable").grid("destroy", true, true);
		var grid = $("#LTETable").grid({
				columns:fieldArr,
				params:paramsLTE,
				dataSource:{
					url: "lowAccessCell/getLTENeighborData", 
					success: function(data){
						data = eval("("+data+")");
						grid.render(data);
						$("#LTE_zhaozi").hide();
						$("#LTE_loadingImg").hide();
						$("#LteNum").val(data.total_is0);
						if($("#LteNum").val()==0){
							$("#LteNumSpan").removeClass();
							$("#LteNumSpan").addClass("glyphicon glyphicon-ok-circle");
							$("#LteNumSpan").css("color","green");
						}else{
							$("#LteNumSpan").removeClass();
							$("#LteNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
							$("#LteNumSpan").css("color","red");
						}
					} 
				},
				pager: { limit: 10, sizes: [10, 20, 50, 100] },
				autoScroll:true,
				uiLibrary: "bootstrap"
			});
	});

			var paramsGSM = {
			input1:3,
			input2:1,
			input3:50,
			input4:2,
			input5:50,
			input6:-90,
			input7:-15,
			cell : record.cell,
			dateTime : $("#startTime").val(),
			city : record.city
		};
			$.post("lowAccessCell/getGSMNeighborHeader",paramsLTE,function(data){
			if(data.error == "error"){
				$("#GSM_zhaozi").hide();
				$("#GSM_loadingImg").hide();
				return;
			}
			var fieldArr=[];
			for(var k in data){
				if(fieldArr.length == 0){
					fieldArr[fieldArr.length]={field:k,title:k,hidden : true};
				}else{
					if (k == "datetime_id") {
						fieldArr[fieldArr.length]={field:k,title:k,width:180};
					}else{
						fieldArr[fieldArr.length]={field:k,title:k,width:textWidth(k)};
					}
				}
			}
			$("#GSMTable").grid("destroy", true, true);
			var grid = $("#GSMTable").grid({
				columns:fieldArr,
				params:paramsLTE,
				dataSource:{
					url: "lowAccessCell/getGSMNeighborData", 
					success: function(data){
						data = eval("("+data+")");
						grid.render(data);
						$("#GSM_zhaozi").hide();
						$("#GSM_loadingImg").hide();
						$("#GsmNum").val(data.total_is0);
						if($("#GsmNum").val()==0){
							$("#GsmNumSpan").removeClass();
							$("#GsmNumSpan").addClass("glyphicon glyphicon-ok-circle");
							$("#GsmNumSpan").css("color","green");
						}else{
							$("#GsmNumSpan").removeClass();
							$("#GsmNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
							$("#GsmNumSpan").css("color","red");
						}
					} 
				},
				pager: { limit: 10, sizes: [10, 20, 50, 100] },
				autoScroll:true,
				uiLibrary: "bootstrap"
			});
		});

			var params={
			startTime:$("#startTime").val(),
			endTime:$("#endTime").val(),
			city : record.city,
			cell : record.cell
		}; 
			$.get("lowAccessCell/getweakCoverageCell", params, function(data){
			$("#weak_zhaozi").hide();
			$("#weak_loadingImg").hide();
			data = eval("("+data+")");
			$("#weakCoverNum").val(data.num);
			if($("#weakCoverNum").val()==0){
					$("#weakCoverNumSpan").removeClass();
					$("#weakCoverNumSpan").addClass("glyphicon glyphicon-ok-circle");
					$("#weakCoverNumSpan").css("color","green");
				}else{
					$("#weakCoverNumSpan").removeClass();
					$("#weakCoverNumSpan").addClass("glyphicon glyphicon-exclamation-sign");
					$("#weakCoverNumSpan").css("color","red");
				}
			$("#weakCoverageCell").html("");
			if($("#weakCoverNum").val()!=0){  
					$("#weakCoverageCell").highcharts({
						chart: {
							type: "column"
						},
						title: {
							text: null
						},
						
						xAxis: {
							categories: data.category
						},
						yAxis: {
							min: 0,
							title: {
								text: null
							}
						},
						tooltip: {
							pointFormat: "弱覆盖数量：{point.y:.1f} ",
							shared: true,
							useHTML: true
						},
						legend:{
							enabled:false
						},
						credits: {
							enabled: false,
						},
						series: data.series
					});
				}
		});
			l.stop();
			E.stop();
		});

		if(table == "file"){
			filename = $("#badCellFile").val();
			// download(filename);
			fileZipSave(filename);
		}
		if(table == "filevolte"){
			filename1 = $("#badCellFilevolte").val();
			fileZipSave(filename1);
		}
	});
}

function fileZipSave(fileName) {
	if(fileName!=""){
		var fileNames = csvZipDownload(fileName);
		download(fileNames);
	} else {
		layer.open({
			title: "提示",
			content: "No file generated so far!"
		});
	}
}

function fileSave(){
	filename = $("#badCellFileIndex").val();
	// download(filename);
	fileZipSave(filename);
}


function download(url) {
	var browerInfo = getBrowerInfo();
	if (browerInfo=="chrome"){
		download_chrome(url);
	} else if (browerInfo == "firefox") {
		download_firefox(url);
	}
}

function download_chrome(url){
	var aLink = document.createElement("a");
	aLink.href=url;
	aLink.download = url;
	/*var evt = document.createEvent("HTMLEvents");
	evt.initEvent("click", false, false);
	aLink.dispatchEvent(evt);*/
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
	return matches[1].replace(/version/, "safari"); 
}

//点击地图功能
function getCellData(mapv,e,$row,id,record){
	$("#mapCell").val(record.cell);
	var date = $("#startTime").val();
	$.ajax({
		type: "GET",
		url: "lowAccessCell/switchData",
		data: {date: date,cell: record.cell},
		//data: {date: date,cell: "L42k47C"},
		dataType: "text",
		beforeSend: function () {
			$("map").html("<img class='col-md-offset-5' src='dist/img/ajax-loader.gif'>");
		},
		success: function (data) {
			var returnData = JSON.parse(data);
			var vdata = [];
			for(var i=0;i<returnData.length;i++) {
				var count;
				if(returnData[i].handoverAttemptCount == 0) {
					count = 80;
				}else if(returnData[i].handoverSuccessRatio <= 90 && returnData[i].handoverAttemptCount >= 50) {
					count = 55;
				}else {
					count = 30;
				}
				vdata.push({
					lng: returnData[i].slongitude,
					lat: returnData[i].slatitude,
					count: count,
					dir: returnData[i].sdir-30,
					band: returnData[i].sband,
					master: false,
					scell: returnData[i].scell
				});
			}
			vdata.push({
				lng:returnData[0].mlongitude,
				lat: returnData[0].mlatitude,
				count: -1,
				dir:returnData[0].mdir-30,
				band: returnData[0].mband,
				master: true
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
						}
					],
					events: {
						click: function(e, data) {
							//console.log("click",e,data);
							var scells = [];
							for(var i=0;i<data.length;i++) {
								scells.push(data[i].scell);
							}
							var params = {
								// date: document.getElementById("date").value,
								// cell: document.getElementById("cell").value,
								date:date,
								cell:record.cell,
								scells: scells
							};
									
							$.get("lowAccessCell/switchDetail", params, function(data){
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
							/*$("#bMapTable").DataTable( {
								"bAutoWidth": false,
								"destroy": true,
								"scrollX": true,
								//"processing": true,
								//"serverSide": true,
								//"aoColumnDefs":  [{ "sWidth": "500px",  "aTargets": [0] }],
								"ajax": {
									"url":"switchDetail",
									"data":params
								},
								"columns": [
									{ "data": "id" },
									{ "data": "day_id" },
									{ "data": "city" },
									{ "data": "subNetwork" },
									{ "data": "cell" },
									{ "data": "EutranCellRelation" },
									{ "data": "切换成功率" },
									{ "data": "同频切换成功率" },
									{ "data": "异频切换成功率" },
									{ "data": "同频准备切换尝试数" },
									{ "data": "同频准备切换成功数" },
									{ "data": "同频执行切换尝试数" },
									{ "data": "同频执行切换成功数" },
									{ "data": "异频准备切换尝试数" },
									{ "data": "异频准备切换成功数" },
									{ "data": "异频执行切换尝试数" },
									{ "data": "准备切换成功率" },
									{ "data": "执行切换成功率" },
									{ "data": "准备切换尝试数" },
									{ "data": "准备切换成功数" },
									{ "data": "准备切换失败数" },
									{ "data": "执行切换尝试数" },
									{ "data": "执行切换成功数" },
									{ "data": "执行切换失败数" }
								]
							});*/
							$("#myModal").modal({
								keyboard: false
							});
						},
						// mousemove: function(e, data) {
						// console.log("move", data)
						// }
					}
				}
			});
		}
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
var layerout = null;
var layerin = null;
function drawMapOut1() {
	//var mapv = initMap("map");
	var newCell = $("#mapCell").val();
	var newDate = $("#startTime").val();
	if(layerout != null) {
		layerout.hide();
	}
	$.ajax({
		type: "GET",
		url: "lowAccessCell/switchData",
		//data: {date: document.getElementById("date").value,cell: document.getElementById("cell").value},
		data:{date:newDate,cell:newCell},
		dataType: "text",
		beforeSend: function () {
			$("map").html("<img class='col-md-offset-5' src='dist/img/ajax-loader.gif'>");
		},
		success: function (data) {
			var returnData = JSON.parse(data);
			var vdata = [];
			for(var i=0;i<returnData.length;i++) {
				var count;
				if(returnData[i].handoverAttemptCount == 0) {
					count = 80;
				}else if(returnData[i].handoverSuccessRatio <= 90 && returnData[i].handoverAttemptCount >= 50) {
					count = 55;
				}else {
					count = 30;
				}
				vdata.push({
					lng: returnData[i].slongitude,
					lat: returnData[i].slatitude,
					count: count,
					dir: returnData[i].sdir-30,
					band: returnData[i].sband,
					master: false,
					scell: returnData[i].scell
				});
			}
			vdata.push({
				lng:returnData[0].mlongitude,
				lat: returnData[0].mlatitude,
				count: -1,
				dir:returnData[0].mdir-30,
				band: returnData[0].mband,
				master: true
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
						}
					],
					events: {
						click: function(e, data) {
							//console.log("click",e,data);
							var scells = [];
							for(var i=0;i<data.length;i++) {
								scells.push(data[i].scell);
							}
							var params = {
								// date: document.getElementById("date").value,
								// cell: document.getElementById("cell").value,
								date:newDate,
								cell:newCell,
								scells: scells
							};

							$("#bMapTable").DataTable( {
								"bAutoWidth": false,
								"destroy": true,
								"scrollX": true,
								//"processing": true,
								//"serverSide": true,
								//"aoColumnDefs":  [{ "sWidth": "500px",  "aTargets": [0] }],
								"ajax": {
									"url":"lowAccessCell/switchDetail",
									"data":params
								},
								"columns": [
									{ "data": "id" },
									{ "data": "day_id" },
									{ "data": "city" },
									{ "data": "subNetwork" },
									{ "data": "cell" },
									{ "data": "EutranCellRelation" },
									{ "data": "切换成功率" },
									{ "data": "同频切换成功率" },
									{ "data": "异频切换成功率" },
									{ "data": "同频准备切换尝试数" },
									{ "data": "同频准备切换成功数" },
									{ "data": "同频执行切换尝试数" },
									{ "data": "同频执行切换成功数" },
									{ "data": "异频准备切换尝试数" },
									{ "data": "异频准备切换成功数" },
									{ "data": "异频执行切换尝试数" },
									{ "data": "准备切换成功率" },
									{ "data": "执行切换成功率" },
									{ "data": "准备切换尝试数" },
									{ "data": "准备切换成功数" },
									{ "data": "准备切换失败数" },
									{ "data": "执行切换尝试数" },
									{ "data": "执行切换成功数" },
									{ "data": "执行切换失败数" }
								]
							});
							$("#myModal").modal({
								keyboard: false
							});
						},
						//  mousemove: function(e, data) {
						//  console.log("move", data)
						//   }
					}
				}
			});
		}
	});
}

function drawMapIn1() {
	var newCell = $("#mapCell").val();
	var newDate = $("#startTime").val();
	if(layerin != null) {
		layerin.hide();
	}

	$.ajax({
		type: "GET",
		url: "lowAccessCell/handoverin",
		//data: {date: document.getElementById("date").value,cell: document.getElementById("cell").value},
		data:{date:newDate,cell:newCell},
		dataType: "text",
		beforeSend: function () {
			$("map").html("<img class='col-md-offset-5' src='dist/img/ajax-loader.gif'>");
		},
		success: function (data) {
			var returnData = JSON.parse(data);
			var vdata = [];
			for(var i=0;i<returnData.length;i++) {
				var count;
				if(returnData[i].handoverAttemptCount == 0) {
					count = 80;
				}else if(returnData[i].handoverSuccessRatio <= 90 && returnData[i].handoverAttemptCount >= 50) {
					count = 55;
				}else {
					count = 30;
				}
				vdata.push({
					lng: returnData[i].mlongitude,
					lat: returnData[i].mlatitude,
					count: count,
					dir: returnData[i].mdir-30,
					band: returnData[i].mband,
					master: false,
					cell: returnData[i].cell
				});
			}
			vdata.push({
				lng:returnData[0].slongitude,
				lat: returnData[0].slatitude,
				count: -1,
				dir:returnData[0].sdir-30,
				band: returnData[0].sband,
				master: true
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
								// date: document.getElementById("date").value,
								// cell: document.getElementById("cell").value,
								cell:newCell,
								date:newDate,
								cells: cells
							};
							$("#bMapTable").DataTable( {
								"bAutoWidth": false,
								"destroy": true,
								"scrollX": true,
								//"processing": true,
								//"serverSide": true,
								//"aoColumnDefs":  [{ "sWidth": "500px",  "aTargets": [0] }],
								"ajax": {
									"url":"lowAccessCell/handOverInDetail",
									"data":params
								},
								"columns": [
									{ "data": "id" },
									{ "data": "day_id" },
									{ "data": "city" },
									{ "data": "subNetwork" },
									{ "data": "cell" },
									{ "data": "EutranCellRelation" },
									{ "data": "切换成功率" },
									{ "data": "同频切换成功率" },
									{ "data": "异频切换成功率" },
									{ "data": "同频准备切换尝试数" },
									{ "data": "同频准备切换成功数" },
									{ "data": "同频执行切换尝试数" },
									{ "data": "同频执行切换成功数" },
									{ "data": "异频准备切换尝试数" },
									{ "data": "异频准备切换成功数" },
									{ "data": "异频执行切换尝试数" },
									{ "data": "准备切换成功率" },
									{ "data": "执行切换成功率" },
									{ "data": "准备切换尝试数" },
									{ "data": "准备切换成功数" },
									{ "data": "准备切换失败数" },
									{ "data": "执行切换尝试数" },
									{ "data": "执行切换成功数" },
									{ "data": "执行切换失败数" }
								]
							});
							$("#myModal").modal({
								keyboard: false
							});
						},
						// mousemove: function(e, data) {
						// console.log("move", data)
						// }
					}
				}
			});
		}
	});
}

function textWidth(text){
	var length = text.length;
	if(length > 15){
		return length*10;
	}
	return 150;
}

var switchFlag = "out";
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
	//初始化控件
	/*switchControl.prototype.initialize=function(map){
		var div = document.createElement("div");
		var btn1 = document.createElement("button");
		btn1.setAttribute("class","btn btn-mini btn-primary");
		btn1.setAttribute("type","button");
		btn1.setAttribute("id","switchOutBtn");
		btn1.textContent ="切出";
		btn1.onclick = function () {
			switchFlag = "out";
			layerin.hide();
			drawMapOut("origin");
		}
		div.appendChild(btn1);
		var btn2 = document.createElement("button");
		btn2.setAttribute("class","btn btn-mini btn-primary");
		btn2.setAttribute("type","button");
		btn2.setAttribute("id","switchInBtn");
		btn2.textContent ="切入";
		btn2.onclick = function () {
			switchFlag = "in";
			layerout.hide();
			drawMapIn("origin");
		}
		div.appendChild(btn2);
		//添加DOM元素到地图中
		map.getContainer().appendChild(div);
		//返回DOM
		return div;
	}*/
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
				/*  "<canvas id="switchSuccessRatio" width="100" height="190" style="width: 100px; height: 190px; background: rgb(255, 255, 255);"></canvas>"
				  +
					"<canvas id="switchNumber" width="100" height="190" style="width: 100px; height: 190px; background: rgb(255, 255, 255);"></canvas>"+
					*/
					"<div class='box'>"+
						"<div id='switch' class='box-body' style='position: relative;'>"+
						"<canvas id='switchSuccessRatio'width='100' height='100' style='width: 100px; height: 100px; background: rgb(255, 255, 255);'></canvas>"
						+
							/* "<canvas id="switchNumber" width="100" height="190" style="width: 100px; height: 190px; background: rgb(255, 255, 255);"></canvas>"+
							*/
							
						"</div>"+
						"<div class='box'>"+
						"<div id='switch' class='box-body' style='position: relative;'>"+
						/*  "<canvas id="switchSuccessRatio" width="100" height="190" style="width: 100px; height: 190px; background: rgb(255, 255, 255);"></canvas>"
						  +*/
							"<canvas id='switchNumber' width='100' height='140' style='width: 100px; height: 140px; background: rgb(255, 255, 255);'></canvas>"+
							
							
						"</div>"+
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
	//0605
	/*$.ajax({
		type: "GET",
		url: "switchSite",
		dataType: "text",
		success: function (data) {
			$("#map_zhaozi").hide();
			$("#map_loadingImg").hide();
			var returnData = JSON.parse(data);
			var vdata = [];
			for (var i = 0; i < returnData.length; i++) {
				vdata.push({
					cell: returnData[i].cellName,
					lng: returnData[i].longitude,
					lat: returnData[i].latitude,
					count: 5,
					dir: returnData[i].dir,
					band: returnData[i].band,
				});
			}
			var layer = new Mapv.Layer({
				mapv: mapv, // 对应的mapv实例
				zIndex: 1, // 图层层级
				dataType: "point", // 数据类型，点类型
				data: vdata, // 数据
				drawType: "choropleth", // 展示形式
				dataRangeControl: false ,
				drawOptions: { // 绘制参数
					size: 20, // 点大小
					unit: "px", // 单位
					strokeStyle: "gray", // 描边颜色
					type: "site",
					// splitList数值表示按数值区间来展示不同颜色的点
					splitList: [
						{
							start:0,
							end: 10,
							color: "gray"
						}
					],
					events: {
						mousemove: function (e, data) {
							$("#leftControl").children().remove();
							var li = "";
							for (var i = 0; i < data.length; i++) {
								li  += ("<li " + "class="list-group-item"" + ">" + data[i].cell + "</li>");
							}
							 $("#leftControl").append(li);
						}
					}
				}
			});
		}
	});*/
	return {"bmap":bmap,"mapv":mapv};
}
var drawMapOut = function (t, scell) {
	checkSwitchOut();
	switchOutTable();
	//console.log(scell);
	//flagDrawMap = "drawMapOut";
	if(t == "最大RRC连接用户数"){
		url = "lowAccessCell/RRCusers";
	}else if(t=="无线掉线率"){
		url = "lowAccessCell/wireLessLost";
	}else if(t=="PUSCH上行干扰电平"){
		url = "lowAccessCell/PUSCHInterfere";
	}else if(t=="origin"){
		url = "lowAccessCell/switchData";
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

	/*$.ajax({
	  type: "GET",
	  url:"getYellowColor",
	  data: {date: document.getElementById("startTime").value,cell: document.getElementById("mapCell").value},
	  dataType: "text",
		beforeSend: function () {
			$("map").html("<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">")
		},
		success: function (data) {
		  setTableData(data);
		}
	});*/

	$.ajax({
		type: "GET",
		//url: "switchData",
		url:url,
		data: {date: document.getElementById("startTime").value,cell: document.getElementById("mapCell").value},
		dataType: "text",
		beforeSend: function () {
			$("map").html("<img class='col-md-offset-5' src='dist/img/ajax-loader.gif'>");
		},
		success: function (data) {
			setTableData(data);
			//console.log(data);
			//console.log(scell);
			$("#map_zhaozi").hide();
			$("#map_loadingImg").hide();
			S.stop();E.stop();
			var returnData = JSON.parse(data);
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
							//console.log("click",e,data);
							var scells = [];
							for(var i=0;i<data.length;i++) {
								scells.push(data[i].scell);
							}
							var params = {
								date: document.getElementById("startTime").value,
								cell: document.getElementById("mapCell").value,
								scells: scells
							};

							$.get("lowAccessCell/switchDetail", params, function(data){
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

var drawMapIn = function (t, scell) {
	//console.log(scell);
	switchOutTableIn();
	//flagDrawMap = "drawMapIn";
	if(t == "最大RRC连接用户数"){
		url = "lowAccessCell/RRCusersin";
	}else if(t=="无线掉线率"){
		url = "lowAccessCell/wireLessLostin";
	}else if(t=="PUSCH上行干扰电平"){
		url = "lowAccessCell/PUSCHInterferein";
	}else if(t=="origin"){
		url = "lowAccessCell/handoverin";
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
	/*$.ajax({
	  type: "GET",
	  url:"getYellowColorIn",
	  data: {date: document.getElementById("startTime").value,cell: document.getElementById("mapCell").value},
	  dataType: "text",
		beforeSend: function () {
			$("map").html("<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">")
		},
		success: function (data) {
		  setTableDataIn(data);
		}
	});*/
	$.ajax({
		type: "GET",
		url: url,
		data: {date: document.getElementById("startTime").value,cell: document.getElementById("mapCell").value},
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
								date: document.getElementById("startTime").value,
								cell: document.getElementById("mapCell").value,
								cells: cells
							};


							$.get("lowAccessCell/handOverInDetail", params, function(data){
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

var drawMapIn2 = function (data, scell) {
	/*if(t == "最大RRC连接用户数"){
		url = "RRCusersin";
	}else if(t=="无线掉线率"){
		url = "wireLessLostin";
	}else if(t=="PUSCH上行干扰电平"){
		url = "PUSCHInterferein";
	}else if(t=="origin"){
		url = "handoverin";
	}*/
	var t = "origin";
	var returnData = data;
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
			//lineWidth : lineWidth,
			lineCount:"null"
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

	//mapv.bmap.setViewport(points);

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
			}
		}
	});
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
function switchNumberLegend(switchNumber){
	//$("#map1 #switchNumber").remove();
	//$("#map1").append("<canvas id="switchNumber" class="BMap_noprint anchorBR" width="100" height="190" style="border-radius: 4px; position: absolute; z-index: 10; bottom: 10px; right: 10px; top: auto; left: auto; width: 100px; height: 190px; background: rgb(255, 255, 255);"></canvas>");
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
	//$("#map1 #switchSuccessRatio").remove();
	//$("#map1").append("<canvas id="switchSuccessRatio" class="BMap_noprint anchorBR" width="100" height="190" style="border-radius: 4px; position: absolute; z-index: 10; bottom: 10px; right: 105px; top: auto; left: auto; width: 100px; height: 190px; background: rgb(255, 255, 255);"></canvas>");
	var switchNumber = document.getElementById(switchSuccessRatio);
	var context = switchNumber.getContext("2d");
	context.fillStyle="#000000";
	context.font="12px serif";
	context.textAlign="center";
	context.fillText("切换成功率",40,10.5);
	context.lineWidth = 0.2;
	/*context.strokeStyle = "gray";
	context.moveTo(0,15.5);
	context.lineTo(100,15.5);
	context.stroke();*/
	context.textAlign="left";
	//设置对象起始点和终点
	context.beginPath();
	//context.arc(25.5,45.5,5,0,2*Math.PI);
	//context.fillStyle = "red";
	//context.fillRect(23.5,45.5,35.5,15.5);
	context.fillStyle = "red";
	context.strokeStyle = "black";
	//context.lineWidth = 2;
	context.fillRect(10.5,20.5,35.5,15.5);

	//context.strokeRect(0, 0, 60, 60);
	context.fillText("<85%", 50.5,30.5);
	context.strokeStyle = "gray";
	context.moveTo(0,40.5);
	context.lineTo(100,40.5);
	context.stroke();
	context.closePath();
	//context.fill();
	//context.fillText("<85%",23.5,45.5);

	//设置对象起始点和终点
	context.beginPath();

	//context.arc(25.5,85.5,5,0,2*Math.PI);
	//context.fillRect(25.5,45.5,20.5,10.5);
	context.fillStyle = "yellow";
	context.strokeStyle = "black";
	context.fillRect(10.5,50.5,35.5,15.5);

	//context.strokeRect(0, 0, 60, 60);
	context.fillText("85%~95%", 50.5,60.5);
	context.strokeStyle = "gray";
	context.moveTo(0,70.5);
	context.lineTo(100,70.5);
	context.stroke();
	context.closePath();
	//context.fillStyle = "rgb(255, 255, 0)";
	//context.fill();
	//context.fillText("85%~95%",50.5,88.5);

	//设置对象起始点和终点
	context.beginPath();
	//context.arc(25.5,125.5,5,0,2*Math.PI);
	// context.fillRect(25.5,45.5,20.5,10.5);
	context.fillStyle = "blue";
	context.strokeStyle = "black";
	context.fillRect(10.5,80.5,35.5,15.5);

	//context.strokeRect(0, 0, 60, 60);
	context.fillText(">95%", 50.5,90.5);
	context.closePath();
	//context.fillStyle = "rgb(0, 0, 255)";
	//context.fill();
	//context.fillText(">95%",50.5,128.5);

}
function switchSuccessRatioLegendBak(switchSuccessRatio){
	//$("#map1 #switchSuccessRatio").remove();
	//$("#map1").append("<canvas id="switchSuccessRatio" class="BMap_noprint anchorBR" width="100" height="190" style="border-radius: 4px; position: absolute; z-index: 10; bottom: 10px; right: 105px; top: auto; left: auto; width: 100px; height: 190px; background: rgb(255, 255, 255);"></canvas>");
	var switchNumber = document.getElementById(switchSuccessRatio);
	var context = switchNumber.getContext("2d");
	context.fillStyle="#000000";
	context.font="12px serif";
	context.textAlign="center";
	context.fillText("准备切换成功率",50,20.5);
	context.textAlign="left";
	//设置对象起始点和终点
	context.beginPath();
	context.arc(25.5,45.5,5,0,2*Math.PI);
	context.closePath();
	context.fillStyle = "rgb(255, 0, 0)";
	context.fill();
	context.fillText("<85%",50.5,48.5);

	//设置对象起始点和终点
	context.beginPath();
	context.arc(25.5,85.5,5,0,2*Math.PI);
	context.closePath();
	context.fillStyle = "rgb(255, 255, 0)";
	context.fill();
	context.fillText("85%~95%",50.5,88.5);

	//设置对象起始点和终点
	context.beginPath();
	context.arc(25.5,125.5,5,0,2*Math.PI);
	context.closePath();
	context.fillStyle = "rgb(0, 0, 255)";
	context.fill();
	context.fillText(">95%",50.5,128.5);

}

function setTableData(data){
	var fieldArr=[];//console.log(data);
	data = eval("("+data+")");
	//var text = "scell,cell,mlongitude,mlatitude,mdir,mband,slongitude,slatitude,sdir,sband,执行切换失败数,准备切换尝试数,准备切换尝试数,切换成功率";
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
		// if(flagDrawMap == "drawMapOut"){
		//console.log(record);
		drawMapOut2(data, record.NeighCell);
		// }else if(flagDrawMap == "drawMapIn"){
		//   drawMapIn("origin", record.scell);
		// }
	});
}

function setTableDataIn(data){
	var fieldArr=[];//console.log(data);
	data = eval("("+data+")");
	//var text = "cell,scell,mlongitude,mlatitude,mdir,mband,slongitude,slatitude,sdir,sband,执行切换失败数,准备切换尝试数,准备切换尝试数,切换成功率";
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
		// if(flagDrawMap == "drawMapOut"){
		drawMapIn2(data, record.ServeCell);
		// }else if(flagDrawMap == "drawMapIn"){
		//   drawMapIn("origin", record.scell);
		// }
	});
}

function bsc_table(url, id, params){
	$.get(url, params, function(data){
		var fieldArr=[];
		var text=data.content.split(",");
		var filename = data.filename;
		for(var i in data.rows[0]){       
			fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:150};
		} 
		var newData = data.rows;
		$(id).grid("destroy", true, true);
		var alarmWorstCellTable = $(id).grid({
			columns:fieldArr,
			dataSource:newData,
			pager: { limit: 10, sizes: [10, 20, 50, 100] },
			autoScroll:true,
			uiLibrary: "bootstrap",
		});
	});
}
function bsc_table_model(url, id, params){
	// console.log(params);
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

function openAlarmModel(cell) {
	bsc_table_model("lowAccessCell/getCellAlarmClassifyTable", "#cellAlarmTable_model", {cell:cell,day_from:$("#startTime").val(),day_to:$("#endTime").val(),hours:null});  //当前告警
	bsc_table_model("lowAccessCell/getErbsAlarmClassifyTable", "#erbsAlarmTable_model", {cell:cell,day_from:$("#startTime").val(),day_to:$("#endTime").val(),hours:null});  //历史告警
	$("#config_information_alarm").modal();
}

function openNeighborCellModel(cell, city) {
	var paramsLTE_1 = {
		cell : cell,
		dateTime : $("#startTime").val(),
		city : city
	};
	$.get("lowAccessCell/getLTENeighborData_model", paramsLTE_1, function(data){
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
	var yAxis_name_left = "无线接通率";
	var yAxis_name_right = "RSRP<-116的比例";
	var paramsLTE_1 = {
		cell : cell,
		dateTime : $("#startTime").val(),
		endTime : $("#endTime").val(),
		city : city,
		yAxis_name_left : "无线接通率",
		yAxis_name_right : "RSRP<-116的比例",
		table : "lowAccessCell_ex"
	};

	$.ajax({
		url:"lowAccessCell/getZhichaCell_chart",
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
	/*$.get("badCell/getZhichaCell_chart", paramsLTE_1, function(data){ 
		var cat_str = JSON.stringify(JSON.parse(data).categories);
		var ser_str = JSON.stringify(JSON.parse(data).series);
		yAxisData = (JSON.parse(data).yAxis);
		var cell_str = JSON.parse(data).cell;
		ser_str=ser_str.replace(/"/g,"");
		ser_str=ser_str.replace(yAxis_name_left,"""+yAxis_name_left+""");
		ser_str=ser_str.replace("spline",""spline"");
		ser_str=ser_str.replace("#89A54E",""#89A54E"");
		ser_str=ser_str.replace(yAxis_name_right,"""+yAxis_name_right+""");
		ser_str=ser_str.replace("column",""column"");
		ser_str=ser_str.replace("#4572A7",""#4572A7"");
		cat_obj = eval("("+cat_str+")");     
		ser_obj = eval("("+ser_str+")");
	});*/

	/*$("#config_information_weakCoverCell").on("shown.bs.modal",function(){
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
	});*/
	$.get("lowAccessCell/getWeakCoverCell_model", paramsLTE_1, function(data){
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
	var yAxis_name_left = "无线接通率";
	var yAxis_name_right = "RSRQ<-15.5的比例";
	var paramsLTE_1 = {
		cell : cell,
		dateTime : $("#startTime").val(),
		endTime : $("#endTime").val(),
		city : city,
		yAxis_name_left : "无线接通率",
		yAxis_name_right : "RSRQ<-15.5的比例",
		table : "lowAccessCell_ex"
	};
	$.ajax({
		url:"lowAccessCell/getZhichaCell_chart",
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
	/*$.get("badCell/getZhichaCell_chart", paramsLTE_1, function(data){
	  var cat_str = JSON.stringify(JSON.parse(data).categories);
	var ser_str = JSON.stringify(JSON.parse(data).series);
	  yAxisData = (JSON.parse(data).yAxis);
	  var cell_str = JSON.parse(data).cell;
	  ser_str=ser_str.replace(/"/g,"");
	  ser_str=ser_str.replace(yAxis_name_left,"""+yAxis_name_left+""");
	  ser_str=ser_str.replace("spline",""spline"");
	  ser_str=ser_str.replace("#89A54E",""#89A54E"");
	  ser_str=ser_str.replace(yAxis_name_right,"""+yAxis_name_right+""");
	  ser_str=ser_str.replace("column",""column"");
	  ser_str=ser_str.replace("#4572A7",""#4572A7"");
	  cat_obj = eval("("+cat_str+")");     
	  ser_obj = eval("("+ser_str+")");
	});*/

	$.get("lowAccessCell/getzhichaCell_model", paramsLTE_1, function(data){
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

	/*$("#config_information_zhichaCell").on("shown.bs.modal",function(){
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
  });*/
}

function openWeakCoverCellModelvolte(cell, city) {
	$("#config_information_weakCoverCell").modal();
	$("#getfirstWeakCoverCellTab a:first").tab("show");
	$("#weakCoverCellWorstCellContainer").empty();
	var cat_obj,ser_obj,yAxisData;
	var yAxis_name_left = "无线接通率";
	var yAxis_name_right = "RSRP<-116的比例";
	var paramsLTE_1 = {
		cell : cell,
		dateTime : $("#startTime").val(),
		endTime : $("#endTime").val(),
		city : city,
		yAxis_name_left : "无线接通率",
		yAxis_name_right : "RSRP<-116的比例",
		table : "lowAccessCell"
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
						enabled: true     
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
	/*$.get("badCell/getZhichaCell_chart", paramsLTE_1, function(data){ 
		var cat_str = JSON.stringify(JSON.parse(data).categories);
		var ser_str = JSON.stringify(JSON.parse(data).series);
		yAxisData = (JSON.parse(data).yAxis);
		var cell_str = JSON.parse(data).cell;
		ser_str=ser_str.replace(/"/g,"");
		ser_str=ser_str.replace(yAxis_name_left,"""+yAxis_name_left+""");
		ser_str=ser_str.replace("spline",""spline"");
		ser_str=ser_str.replace("#89A54E",""#89A54E"");
		ser_str=ser_str.replace(yAxis_name_right,"""+yAxis_name_right+""");
		ser_str=ser_str.replace("column",""column"");
		ser_str=ser_str.replace("#4572A7",""#4572A7"");
		cat_obj = eval("("+cat_str+")");     
		ser_obj = eval("("+ser_str+")");
	});*/

	/*$("#config_information_weakCoverCell").on("shown.bs.modal",function(){
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
	});*/
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

function openOverlapCoverModel(cell, city) {
	var paramsLTE_1 = {
		cell : cell,
		dateTime : $("#startTime").val(),
		endTime : $("#endTime").val(),
		city : city
	};
	$.get("lowAccessCell/getOverlapCover_model", paramsLTE_1, function(data){
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

function openParameterModel(cell, city) {
	$("#config_information_parameter").modal();
	getBaselineCheckData(cell, city);
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
   
		table : "lowAccessCell_ex"
	};
	$.ajax({
		url:"lowAccessCell/getGanraoCell_chart",
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
	$.get("lowAccessCell/getInterfereCell_model", paramsLTE_1, function(data){
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
		$("#loadingganrao").remove();
	});
}

function getNumOfDiagnosisData(cell, city) {
	var params = {
		city: city,
		dateTime : $("#startTime").val(),
		endTime : $("#endTime").val(),
		cell: cell
	};
	$.get("lowAccessCell/getNumOfDiagnosisData", params, function(data) {
		$("#currentAlarm").val(data[0]);          //告警数量
		// $("#needAddNeigh").val(data[1]);          //邻区-需要加邻区数量
		$("#less116Proportion").val(data[2]);     //弱覆盖-RSRP<-116的比例
		$("#less155Proportion").val(data[3]);     //质差-RSRQ<-15.5的比例
		$("#overlapCover").val(data[4]);          //重叠覆盖-重叠覆盖度
		$("#AvgPRB").val(data[5]);                //干扰-平均PRB
		$("#highTraffic").val(data[6]);           //最高RRC用户数
		$("#parameter").val(data[7]);             //参数
	});
}
//告警
function getNumOfDiagnosisDataFilter_alarm(cell,city){
	var params = {
		city: city,
		dateTime : $("#startTime").val(),
		endTime : $("#endTime").val(),
		cell: cell
	};
	$.get("lowAccessCell/getNumOfDiagnosisDataFilter_alarm", params, function(data) {
		$("#alarm_loadingImg").hide();   //告警
		$("#currentAlarm").val(data[0]);          //告警数量
	});
}
//弱覆盖
function getNumOfDiagnosisDataFilter_weakCover(cell,city){
	var params = {
		city: city,
		dateTime : $("#startTime").val(),
		endTime : $("#endTime").val(),
		cell: cell
	};
	$.get("lowAccessCell/getNumOfDiagnosisDataFilter_weakCover", params, function(data) {      
		$("#weakCocer_loadingImg").hide();    //弱覆盖
		$("#less116Proportion").val(data[0]);     //弱覆盖-RSRP<-116的比例
	});
}
//质差
function getNumOfDiagnosisDataFilter_zhicha(cell,city){
	var params = {
		city: city,
		dateTime : $("#startTime").val(),
		endTime : $("#endTime").val(),
		cell: cell
	};
	$.get("lowAccessCell/getNumOfDiagnosisDataFilter_zhicha", params, function(data) {
		$("#zhicha_loadingImg").hide();    //质差
		$("#less155Proportion").val(data[0]);     //质差-RSRQ<-15.5的比例
	});
}
//重叠覆盖
function getNumOfDiagnosisDataFilter_overlapCover(cell,city){
	var params = {
		city: city,
		dateTime : $("#startTime").val(),
		endTime : $("#endTime").val(),
		cell: cell
	};
	$.get("lowAccessCell/getNumOfDiagnosisDataFilter_overlapCover", params, function(data) {
		$("#overlapCover_loadingImg").hide();    //重叠覆盖
		$("#overlapCover").val(data[0]);          //重叠覆盖-重叠覆盖度
	});
}
//干扰
function getNumOfDiagnosisDataFilter_AvgPRB(cell,city){
	var params = {
		city: city,
		dateTime : $("#startTime").val(),
		endTime : $("#endTime").val(),
		cell: cell
	};
	$.get("lowAccessCell/getNumOfDiagnosisDataFilter_AvgPRB", params, function(data) {
		$("#avgPRB_loadingImg").hide();    //干扰
		$("#AvgPRB").val(data[0]);                //干扰-平均PRB
	});
}
//最高RRC用户数
function getNumOfDiagnosisDataFilter_highTraffic(cell,city){
	var params = {
		city: city,
		dateTime : $("#startTime").val(),
		endTime : $("#endTime").val(),
		cell: cell
	};
	$.get("lowAccessCell/getNumOfDiagnosisDataFilter_highTraffic", params, function(data) {
		$("#highTraffic_loadingImg").hide();    //最高RRC用户数
		$("#highTraffic").val(data[0]);           //最高RRC用户数
	});
}
//参数
function getNumOfDiagnosisDataFilter_parameter(cell,city){
	var params = {
		city: city,
		dateTime : $("#startTime").val(),
		endTime : $("#endTime").val(),
		cell: cell
	};
	$.get("lowAccessCell/getNumOfDiagnosisDataFilter_parameter", params, function(data) {
		$("#parameter_loadingImg").hide();    //参数
		$("#parameter").val(data[0]);             //参数
	});
}
function getNumOfDiagnosisData_MR(cell, city) {
	var params = {
		city: city,
		dateTime : $("#startTime").val(),
		endTime : $("#endTime").val(),
		cell: cell
	};
	$.get("lowAccessCell/getNumOfDiagnosisData_mr", params, function(data) {
		$("#needAddNeigh_loadingImg").hide();
		$("#needAddNeigh").val(data[0]);          //邻区-需要加邻区数量
	});
}

function getBaselineCheckData(cell,city){
	var params = {
		rowCell:cell,
		table:"ParaCheckBaseline"
	};
	$.get("lowAccessCell/getBaselineCheckData", params,function(data){
		$("#baselineTableIndex").grid("destroy", true, true);
		if (JSON.parse(data)["record"] == 0) {
			$("#baselineFileIndex").val("");
			$("#baselineTableIndex").html(cell+"小区对应的参数数据不存在！");
			return false;
		}
		var fieldArr=[];
		var text=(JSON.parse(data).content).split(",");
		var filename = JSON.parse(data).filename;
		$("#baselineFileIndex").val(filename);
		for(var i in JSON.parse(data).rows[0]){
			if (i == "mo") {
				fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:400,sortable:true}; 
			}else{     
				fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:150,sortable:true};  
			}   
		} 
		var newData = JSON.parse(data).rows;
		var badCellTable = $("#baselineTableIndex").grid({
			columns:fieldArr,
			dataSource:newData,
			pager: { limit: 10, sizes: [10, 20, 50, 100] },
			autoScroll:true,
			uiLibrary: "bootstrap",
		});
		$("#loadingparam").remove();
	});
}
function fileSave_baseline(){
	fileName = $("#baselineFileIndex").val();
	fileZipSave(fileName);
}
function switchTab(div1,div2,type){
	$(div2).removeClass("active");
	$(div1).addClass("active");
	/* if(type=="table"){
		doSearchEvent_table();
	}*/
}
function switchTab_RRCC(div1,div2,type){
	$(div2).removeClass("active");
	$(div1).addClass("active");
}
//0524
function doSearchEvent_table_rrcc(cell, city) {
	var params_rrc = {
		city: city,
		cell: cell,
		day_from: $("#startTime").val(),
		day_to: $("#endTime").val(),
	};
	var fieldArr = [];
	fieldArr[fieldArr.length] = {field: "eventName", title: "eventName", width: 200};
	fieldArr[fieldArr.length] = {field: "enbId", title: "enbId", width: 150};
	fieldArr[fieldArr.length] = {field: "eventTimeUt", title: "eventTimeUt", width: 200};
	fieldArr[fieldArr.length] = {field: "date_id", title: "date_id", width: 100};
	fieldArr[fieldArr.length] = {field: "hour_id", title: "hour_id", width: 100};
	fieldArr[fieldArr.length] = {field: "imsi", title: "imsi", width: 80};
	fieldArr[fieldArr.length] = {field: "mTmsi", title: "mTmsi", width: 100};
	fieldArr[fieldArr.length] = {field: "ueRef", title: "ueRef", width: 200};
	fieldArr[fieldArr.length] = {field: "enbS1apId", title: "enbS1apId", width: 150};
	fieldArr[fieldArr.length] = {field: "mmeS1apId", title: "mmeS1apId", width: 300};
	fieldArr[fieldArr.length] = {field: "ecgi", title: "ecgi", width: 100};
	fieldArr[fieldArr.length] = {field: "gummei", title: "gummei", width: 100};
	fieldArr[fieldArr.length] = {field: "recordingSessionReference", title: "recordingSessionReference", width: 300};
	fieldArr[fieldArr.length] = {field: "erabSetupResult", title: "erabSetupResult", width: 100};
	fieldArr[fieldArr.length] = {field: "erabSetupReqQci", title: "erabSetupReqQci", width: 200};
	fieldArr[fieldArr.length] = {field: "erabSetupfailure3GppCauseGroup", title: "erabSetupfailure3GppCauseGroup", width: 300};
	fieldArr[fieldArr.length] = {field: "erabSetupfailure3ppCause", title: "erabSetupfailure3ppCause", width: 300};
	var fieldCol = fieldArr;
	if (fieldCol == false) {
		return false;
	}
	$("#rrcResultTable_RRCC").grid("destroy", true, true);
	var grid = $("#rrcResultTable_RRCC").grid({
		columns: fieldCol,
		dataSource: {
			url: "lowAccessCell/getRrcResultTableData_rrcc",
			success: function (data) {
				if (data.error) {
					$("#rrcResultTable_RRCC").grid("destroy", true, true);
					return;
				}
				grid.render(data);
			}
		},
		params: params_rrc,
		pager: {limit: 10, sizes: [10, 20, 50, 100]},
		autoScroll: true,
		uiLibrary: "bootstrap",
		primaryKey: "id",
		autoLoad: true
	});
}
function doSearchEvent_table(cell,city){
	var params_rrc = {
		city:city,
		cell:cell,
		day_from:$("#startTime").val(),
		day_to:$("#endTime").val(),
	}
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
			url: "lowAccessCell/getRrcResultTableData", 
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
	/*grid.on("rowSelect", function (e, $row, id, record) {
		var result = [];
		result[0] = $row.children().eq(0).children().html();
		result[1] = $row.children().eq(1).children().html();
		$("#selectedResult").val(result);
		detailTable(result);
	});*/
}

function  getrrcResultDetail(params_rrc){
	var E = Ladda.create( document.getElementById( "exportRrcDetail" ) );
	E.start();
	$("#loading").html("<img class='col-md-offset-5' src='dist/img/ajax-loader.gif'>加载中");

	params_temp = params_rrc;
	var tableId = "#rrcResultDetailTable";
	var fieldArr=[]; 
	$.post("lowAccessCell/getRrcResultDetailTableField",params_rrc,function(data){
		$("#loading").html("");    
		E.stop();
		$(tableId).grid("destroy", true, true);
		if (data.result == "error") {
			//$("#export").attr("disabled",true);
			layer.open({
				title: "提示",
				content: "没有记录"
			});
			return;
		}else{
			for(var k in data){
				if (k == "establCause" || k == "eventTime") {
					fieldArr[fieldArr.length]={field:k,title:k,width:300};
				}else if (k == "result"){
					fieldArr[fieldArr.length]={field:k,title:k,width:500};
				}else {
					fieldArr[fieldArr.length]={field:k,title:k,width:textWidth(k)};
				}
			}
			$(tableId).grid("destroy", true, true);
			$(tableId).grid({
				columns:fieldArr,
				dataSource: { 
					url: "lowAccessCell/getRrcResultDetailData",
					type:"post", 
					data: params_rrc
				},
				params : params_rrc,
				//dataSource: { url: "badCell/getRrcResultDetailData",type:"post", data: params_rrc,},
				//primaryKey: "id",
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
	$.post("lowAccessCell/exportRrcResultDetail",params_temp,function(data){
		E.stop();
		if(data.result) {
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
function checkSwitchOut(){
	$(".switchRadio").eq(0).prop("checked","checked");
}
function switchOutTable(){
	//$("input[name="t1"]").eq(0).prop("checked","checked");
	var masterCell = $("#mapCell").val();
	var date = $("#startTime").val();
	var params={
		cell:masterCell,
		date:date,
		type:'BadCell'
	};
	$.get("lowAccessCell/switchOutTable", params, function(data){
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
	var masterCell = $("#mapCell").val();
	var date = $("#startTime").val();
	var params={
		cell:masterCell,
		date:date
	};
	$.get("lowAccessCell/switchOutTableIn", params, function(data){
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
//0524
function getCounterLoseResultDistribution(cell) {
	var params = {
		cell: cell
	};
	$.get("lowAccessCell/getCounterLoseResultDistribution", params, function (data) {
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
			/*tooltip: {
			 headerFormat: "<span style="font-size:10px">{point.key}</span><table>",
			 pointFormat: "<tr><td style="color:{series.color};padding:0">{series.name}: </td>" +
			 "<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>",
			 footerFormat: "</table>",
			 shared: true,
			 useHTML: true
			 },*/
			plotOptions: {
				column: {
					pointPadding: 0.2,
					borderWidth: 0
				}
			},
			series: [{
				name: "Counter失败原因值分布",
				data: data["yAxis"]
			}]
		});
	});

}
//0605
function openMapModal(){
	$("#mapModal").modal();
	$("#map").empty();
	setTimeout(function(){
		window.mapv = initMap("map");
		drawMapOut("origin");
	},300);
}

function openCtrJump() {
	$("#ctrJump").modal();
}
//无线接通率&质差
function getWirelessCallRate_zhicha(cell,city) {    
	var params = {
		cell:cell
	};
	// $("#container_Relevance").empty();
	$.get("lowAccessCell/getWirelessCallRate_zhicha", params, function(data) {
		$("#wirelessCallRate_zhicha_loadingImg").hide();
		$("#wirelessCallRate_date").html("("+data["date_from"] + "~NOW)");
		$("#wirelessCallRate_date_").html("("+data["date_from"] + "~NOW)");
		$("#wirelessCallRate_zhicha").val(data["data"]);
		$("#relevance_backups").append("无线接通率_质差:"+data["data"]+";");
		// $("#relevance_backups").append(data["data"]+":无线接通率_质差;");
		var relevance = $("#relevance_backups").html();
		if(relevance.split(";").length == 5) {
			// getSorteithKeyValue(relevance);
			var relevanceArr = [];
			var arr = relevance.split(";");
			for (var i = arr.length - 2; i >= 0; i--) {
				var arrs = arr[i].split(":");
				relevanceArr[arrs[0]] = arrs[1];
			}
			$("#relevance_zhaozi").hide();
			$("#relevance_loadingImg").hide();
			$("#relevance_zhaozi_").hide();
			$("#relevance_loadingImg_").hide();
			//getRelevanceChart(relevanceArr, "("+data["date_from"] + "~NOW)");
		}
	});
}
//无线接通率&干扰
function getWirelessCallRate_interfere(cell,city) {  
	var params = {
		cell:cell
	};
	// $("#container_Relevance").empty();
	$.get("lowAccessCell/getWirelessCallRate_interfere", params, function(data) {  
		$("#wirelessCallRate_interfere_loadingImg").hide();
		// $("#wirelessCallRate_date").html("("+data["date_from"] + "~NOW)");
		$("#wirelessCallRate_interfere").val(data["data"]);
		$("#relevance_backups").append("无线接通率_干扰:"+data["data"]+";");
		// $("#relevance_backups").append(data["data"]+":无线接通率_干扰;");
		var relevance = $("#relevance_backups").html();
		if(relevance.split(";").length == 5) {
			// getSorteithKeyValue(relevance);
			var relevanceArr = [];
			var arr = relevance.split(";");
			for (var i = arr.length - 2; i >= 0; i--) {
				var arrs = arr[i].split(":");
				relevanceArr[arrs[0]] = arrs[1];
			}
			$("#relevance_zhaozi").hide();
			$("#relevance_loadingImg").hide();
			$("#relevance_zhaozi_").hide();
			$("#relevance_loadingImg_").hide();
			//getRelevanceChart(relevanceArr, "("+data["date_from"] + "~NOW)");
		}
	});
}
//无线接通率&RRC建立成功率
function getWirelessCallRate_RRCEstSucc(cell,city) {    
	var params = {
		cell:cell
	};
	// $("#container_Relevance").empty();
	$.get("lowAccessCell/getWirelessCallRate_RRCEstSucc", params, function(data) {
		$("#wirelessCallRate_RRCEstSucc_loadingImg").hide();
		// $("#wirelessCallRate_date").html("("+data["date_from"] + "~NOW)");
		$("#wirelessCallRate_RRCEstSucc").val(data["data"]);
		$("#relevance_backups").append("无线接通率_RRC建立成功率:"+data["data"]+";");
		// $("#relevance_backups").append(data["data"]+":无线接通率_RRC建立成功率;");
		var relevance = $("#relevance_backups").html();
		if(relevance.split(";").length == 5) {
			// getSorteithKeyValue(relevance);
			var relevanceArr = [];
			var arr = relevance.split(";");
			for (var i = arr.length - 2; i >= 0; i--) {
				var arrs = arr[i].split(":");
				relevanceArr[arrs[0]] = arrs[1];
			}
			$("#relevance_zhaozi").hide();
			$("#relevance_loadingImg").hide();
			$("#relevance_zhaozi_").hide();
			$("#relevance_loadingImg_").hide();
			//getRelevanceChart(relevanceArr, "("+data["date_from"] + "~NOW)");
		}
	});
}
//无线接通率&ERAB建立成功率
function getWirelessCallRate_ERABEstSucc(cell,city) {  
	var params = {
		cell:cell
	};
	// $("#container_Relevance").empty();
	$.get("lowAccessCell/getWirelessCallRate_ERABEstSucc", params, function(data) {
	
		$("#wirelessCallRate_ERABEstSucc_loadingImg").hide();
		// $("#wirelessCallRate_date").html("("+data["date_from"] + "~NOW)");
		$("#wirelessCallRate_ERABEstSucc").val(data["data"]);

		$("#relevance_backups").append("无线接通率_ERAB建立成功率:"+data["data"]+";");
		// $("#relevance_backups").append(data["data"]+":无线接通率_ERAB建立成功率;");
		var relevance = $("#relevance_backups").html();
		if(relevance.split(";").length == 5) {
			// getSorteithKeyValue(relevance);
			var relevanceArr = [];
			var arr = relevance.split(";");
			for (var i = arr.length - 2; i >= 0; i--) {
				var arrs = arr[i].split(":");
				relevanceArr[arrs[0]] = arrs[1];
			}
			$("#relevance_zhaozi").hide();
			$("#relevance_loadingImg").hide();
			$("#relevance_zhaozi_").hide();
			$("#relevance_loadingImg_").hide();
			//getRelevanceChart(relevanceArr, "("+data["date_from"] + "~NOW)");
		}
	});
}

// var path;
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
	$.post("lowAccessCell/storage", params, function (data) {
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
		url: "lowAccessCell/monitor",
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
		url: "lowAccessCell/addTask",
		data: data,
		async: true,
		success: function (returnData) {
			console.log(returnData)
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
		url: "lowAccessCell/runTask",
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
				$.post("lowAccessCell/deleteAutoDir",{"tracePath":tracePath});
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
		columns: [[
			{
				title: "Name", field: "kpiName", width: 950,
				formatter: function (val, row) {
					return "<input type='checkbox' onclick=show('" + row.id + "')  id='check_" + row.id + "' " + (row.checked ? 'checked' : '') + "/>" + row.kpiName;
				}
			},
			{field: "size", title: "Size", width: 100},
			{field: "RRC失败次数", title: "RRC失败次数", width: 100},
			{field: "ERAB失败次数", title: "ERAB失败次数", width: 100},
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
	var hour = record.hour_id > 10 ? record.hour_id : "0"+record.hour_id;
	var ctrDldPoint = ""+date.getFullYear()+month+day+hour;
	var rrcFailureNum = record["RRC建立失败次数(今日)"];
	var erabEstNum = record["ERAB建立失败次数(今日)"];

	var city = record.city;
	var cell = record.cell;
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
	$.post("lowAccessCell/ctrTreeItems", params, function (data) {
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
	var hour = record.hour_id > 10 ? record.hour_id : "0"+record.hour_id;
	var ctrDldPoint = ""+date.getFullYear()+month+day+hour;
	var rrcFailureNum = 0;
	var erabEstNum = 0;

	var city = record.city;
	var cell = record.cell;
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
	$.post("lowAccessCell/ctrTreeItems", params, function (data) {
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
	if(record["无线接通率_干扰"]>=record["无线接通率_质差"]){
		if(record["无线接通率_质差"]>=record["无线接通率_弱覆盖"]){
			categories = ["无线接通率_干扰","无线接通率_质差","无线接通率_弱覆盖"];
			myData = [parseFloat(record["无线接通率_干扰"]),parseFloat(record["无线接通率_质差"]),parseFloat(record["无线接通率_弱覆盖"])];
		}else if(record["无线接通率_干扰"]>=record["无线接通率_弱覆盖"]){
			categories = ["无线接通率_干扰","无线接通率_弱覆盖","无线接通率_质差"];
			myData = [parseFloat(record["无线接通率_干扰"]),parseFloat(record["无线接通率_弱覆盖"]),parseFloat(record["无线接通率_质差"])];
		}else{
			categories = ["无线接通率_弱覆盖","无线接通率_干扰","无线接通率_质差"];
			myData = [parseFloat(record["无线接通率_弱覆盖"]),parseFloat(record["无线接通率_干扰"]),parseFloat(record["无线接通率_质差"])];
		}
	}else{
		if(record["无线接通率_干扰"]>=record["无线接通率_弱覆盖"]){
			categories = ["无线接通率_质差","无线接通率_干扰","无线接通率_弱覆盖"];
			myData = [parseFloat(record["无线接通率_质差"]),parseFloat(record["无线接通率_干扰"]),parseFloat(record["无线接通率_弱覆盖"])];
		}else if(record["无线接通率_质差"]<=record["无线接通率_弱覆盖"]){
			categories = ["无线接通率_弱覆盖","无线接通率_质差","无线接通率_干扰"];
			myData = [parseFloat(record["无线接通率_弱覆盖"]),parseFloat(record["无线接通率_质差"]),parseFloat(record["无线接通率_干扰"])];
		}else{
			categories = ["无线接通率_质差","无线接通率_弱覆盖","无线接通率_干扰"];
			myData = [parseFloat(record["无线接通率_质差"]),parseFloat(record["无线接通率_弱覆盖"]),parseFloat(record["无线接通率_干扰"])];
		}
	}




	if (record["无线接通率_RRC建立成功率"] >= record["无线接通率_ERAB建立成功率"]) {
		categories_ = ["无线接通率_RRC建立成功率","无线接通率_ERAB建立成功率"];
		myData_ = [parseFloat(record["无线接通率_RRC建立成功率"]),parseFloat(record["无线接通率_ERAB建立成功率"])];
	} else {
		categories_ = ["无线接通率_ERAB建立成功率","无线接通率_RRC建立成功率"];
		myData_ = [parseFloat(record["无线接通率_ERAB建立成功率"]),parseFloat(record["无线接通率_RRC建立成功率"])];
	}

	myCharts = Highcharts.chart("container_Relevance", {
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

	myCharts_ = Highcharts.chart("container_Relevance_", {
		chart: {
			type: "bar"
		},
		title: {
			text: null
		},
		// subtitle: {
		//     text: "日期:" + date
		// },
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
		},
		series: {
			cursor: "pointer",  
				events: {
					afterAnimate: function () {
							getRelatedTrends(this.data[0].category);   //相关趋势
							$("#trend_zhaozi").show();
							$("#trend_loadingImg").show();
							},
					click: function (event) {
							getRelatedTrends(event.point.category);
							$("#trend_zhaozi").show();
							$("#trend_loadingImg").show();
							}
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
// function getRelevanceChart(data ,date) {
//       var categories = [];
//       var myData = [];
//       var categories_ = [];
//       var myData_ = [];
//       // console.log(data);
//       for (key in data) {  
//          if(key == "无线接通率_ERAB建立成功率" || key == "无线接通率_RRC建立成功率") {
//              categories.push(key);
//          myData.push(parseFloat(data[key]));
//          }else {
//              categories_.push(key);
//          myData_.push(parseFloat(data[key]));
//          }
//       }

//       for(var i=0;i<myData.length;i++){   //排序  从大到小
//         for(var j=i;j<myData.length;j++){
//           if(myData[i]<myData[j]){
//             var tmp=myData[i];
//             var tmpC=categories[i];
//             myData[i]=myData[j];
//             categories[i]=categories[j];
//             myData[j]=tmp;
//             categories[j]=tmpC;
//           }
//         }
//       }

//       for(var i=0;i<myData_.length;i++){   //排序  从大到小
//         for(var j=i;j<myData_.length;j++){
//           if(myData_[i]<myData_[j]){
//             var tmp=myData_[i];
//             var tmpC=categories_[i];
//             myData_[i]=myData_[j];
//             categories_[i]=categories_[j];
//             myData_[j]=tmp;
//             categories_[j]=tmpC;
//           }
//         }
//       }

//       // $("#container_Relevance").highcharts({
//       myCharts = Highcharts.chart("container_Relevance", {
//         chart: {
//             type: "bar"
//         },
//         title: {
//             text: null
//         },
//         // subtitle: {
//         //     text: "日期:" + date
//         // },
//         xAxis: {
//             categories: categories,
//             title: {
//                 text: null
//             }
//         },
//         yAxis: {
//             min: 0,
//             title: {
//                 text: "相关系数",
//                 align: "high"
//             },
//             labels: {
//                 overflow: "justify"
//             }
//         },
//         plotOptions: {
//             bar: {
//                 dataLabels: {
//                     enabled: true,
//                     allowOverlap: true
//                 }
//             }
//         },
//         credits: {
//             enabled: false
//         },
//         series: [{
//             name: "相关系数",
//             data: myData
//         }]
//     });

//     myCharts_ = Highcharts.chart("container_Relevance_", {
//         chart: {
//             type: "bar"
//         },
//         title: {
//             text: null
//         },
//         // subtitle: {
//         //     text: "日期:" + date
//         // },
//         xAxis: {
//             categories: categories_,
//             title: {
//                 text: null
//             }
//         },
//         yAxis: {
//             min: 0,
//             title: {
//                 text: "相关系数",
//                 align: "high"
//             },
//             labels: {
//                 overflow: "justify"
//             }
//         },
//         plotOptions: {
//           bar: {
//               dataLabels: {
//                   enabled: true,
//                   allowOverlap: true
//               }
//           },
//           series: {
//               cursor: "pointer",  
//                   events: {
//                             afterAnimate: function () {
//                               getRelatedTrends(this.data[0].category);   //相关趋势
//                               $("#trend_zhaozi").show();
//                               $("#trend_loadingImg").show();
//                             },
//                             click: function (event) {
//                               getRelatedTrends(event.point.category);
//                               $("#trend_zhaozi").show();
//                               $("#trend_loadingImg").show();
//                             }
//                           }
//                   }   
//           },
//         credits: {
//             enabled: false
//         },
//         series: [{
//             name: "相关系数",
//             data: myData_
//         }]
//     });  
//       // $("#container_Relevance").append(myCharts);
// }

function getRelatedTrends(name) {  //相关趋势
	var params = {
		data : name,
		cell : $("#mapCell").val()
	};
	var dataArr = name.split("_");
	yAxis_name_right = dataArr[1];
	yAxis_name_left = dataArr[0];
	$.get("lowAccessCell/getRelatedTrends", params, function(data){
		$("#trend_zhaozi").hide();
		$("#trend_loadingImg").hide();
		var cat_str =JSON.stringify(JSON.parse(data).categories);
		var ser_str = JSON.stringify(JSON.parse(data).series);
		var yAxisSetData = JSON.stringify(JSON.parse(data).yAxis_set);
		var cell_str = JSON.parse(data).cell;
	
		var yAxisSetData_str = yAxisSetData.replace(/"/g,"");
		ser_str=ser_str.replace(/"/g,"");

		ser_str=ser_str.replace("#89A54E","'#89A54E'");
		
		ser_str=ser_str.replace(yAxis_name_right,"'"+yAxis_name_right+"'");
		ser_str=ser_str.replace(yAxis_name_left,"'"+yAxis_name_left+"'");

		ser_str=ser_str.replace("spline4","'spline'");
		ser_str=ser_str.replace("spline5","'spline'");

		ser_str=ser_str.replace("#87CEFF","'#87CEFF'");

		var  cat_obj = eval("("+cat_str+")");     
		var  ser_obj = eval("("+ser_str+")");
		var yAxisSetData_obj = eval("("+yAxisSetData_str+")");
		myChart_ = Highcharts.chart("trendContainer", {
			chart: {
				zoomType: "xy"
			},
			title: {
				text: null
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

function getNeighborCellMapData(params){
	$.post("lowAccessCell/getNeighborCellMapData", params, function(data){
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

function getHighInterfereHandlePrinciple(record) {
	$("#isModifyPowerControlParameter").html("");
	$("#isModifyCellBarredInterfereCell").html("");
	$("#isModifyLimitInterfereCellqRxlevmin").html("");
	$("#isModifyReduceInterfereCellTransmittedPower").html("");
	$("#isModifyTurnOffNeighCellToInterfereCell").html("");
	var params = {
		cell:record["cell"]
	};
	$.post("lowAccessCell/getTreatmentPrinciple", params, function(data){
		if(data["flag"] == false) {
			$("#flagHighInterfere").hide();
		}
		if(data["flag"] == true) {
			$("#flagHighInterfere").show();
		}
		var newDate = $("#getHighInterfereDate").html();
		$("#getHighInterfereDate").html("");
		$("#getHighInterfereDate").html(newDate+"("+data["date"]+")");
		//1.修改功控参数
		var pZeroNominalPUCCH = "";
		var alpha = "";
		if(data["pZeroNominalPUCCH"] != "-96") {
			pZeroNominalPUCCH = "pZeroNominalPUCCH建议从从"+data["pZeroNominalPUCCH"]+"修改为-96;";
		}else {
			pZeroNominalPUCCH = "pZeroNominalPUCCH当前值为-96;";
		}
		if(data["alpha"] != "1") {
			alpha = "alpha建议从从"+data["alpha"]+"修改为1;";
		}else {
			alpha = "alpha当前值为1;";
		}
		$("#isModifyPowerControlParameter").html(pZeroNominalPUCCH+alpha);
		//2.cellBarred干扰小区 
		var cellBarred = "";
		cellBarred = "cellBarred当前值为"+data["cellBarred"]+";";
		$("#isModifyCellBarredInterfereCell").html(cellBarred);

		//3.限制干扰小区qRxlevmin在干扰值以上4db
		$("#isModifyLimitInterfereCellqRxlevmin").html(data["平均PRB"]+";");

		//4.降低干扰小区的发射功率先降低一半，在根据情况降低功率
		$("#isModifyReduceInterfereCellTransmittedPower").html(data["config"]+"mW;");

		//5.关闭周边小区到干扰小区的isHoAllowed=false 开关
		$("#isModifyTurnOffNeighCellToInterfereCell").html(data["file"]);
	});
}

function exportTurnOffNeighCellToInterfereCells() {
	var file = $("#isModifyTurnOffNeighCellToInterfereCell").text();
	fileDownload(file);
}