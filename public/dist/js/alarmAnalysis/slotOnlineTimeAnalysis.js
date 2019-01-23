$(function(){
	toogle("slotOnlineTimeAnalysis");
	getAllCity();
	//绑定信令图的tab页面，保证页面出来才开始画图，避免画图错位的问题
	/*$("#table_tab_0_nav").on("shown.bs.tab", function () {
		if (chartDatas) {
			setChart();
		} else {
			$("#resultView").empty();
		}
	});*/
});
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
	var url = "slotOnlineTimeAnalysis/getAllCity";
	$.ajax({
		type:"post",
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

function getParams(){
	var citys = $("#allCity").val();
	if(citys == null){
		layer.open({
			title: "提示",
			content: "请选择城市"
		});
		return false;
	}
	var params = {
		city:citys
	};
	return params;
}
var num = 0;
function searchSlot(){
	$("#currSlotNum").html("");
	$("#currTime").html("");
	doSearchAllSlot("slotOnlineTimeAnalysis/getAllSlot","#slotTable");//板卡串号列表
	doSearchAllSlot("slotOnlineTimeAnalysis/getDisappearSlot","#disappearSlotTable");//消失板卡串号
}
function doSearchAllSlot(url, tableId){
	var S = Ladda.create( document.getElementById( "search" ) );
	S.start();
	var params = getParams();
	if(params == false){
		S.stop();
		return false;
	}
	$.post(url, params, function(data){
		num ++;
		if (num == 2) {
			S.stop();
		}
		if (data["data"]["total"] == 0) {
			layer.open({
				title: "提示",
				content: "没有记录"
			});
			return false;
		}
		if (tableId == "#slotTable") {
			$("#currSlotNum").html(data["currSlotNum"]);
			$("#currTime").html(data["currTime"]);
		};
		var fieldArr=[];
		var text = data["titles"];
		var keys = data["keys"];
		for(var i in text){  
			fieldArr[i]={field:keys[i],title:text[i],width:200,sortable:true};
		} 
		$(tableId).grid("destroy", true, true);
		var grid = $(tableId).grid({
			columns:fieldArr,
			dataSource:{
				url:url,
				success:function(data){
					grid.render(data["data"]);
				},
				type:"post"
			},
			params: params,
			pager: { limit: 10, sizes: [10, 20, 50, 100] },
			autoScroll:true,
			uiLibrary: "bootstrap",
			primaryKey:"",
			autoLoad: true,
			rowSelect:function(e, $row, id, record){
				if (tableId == "#disappearSlotTable") {
					slotTrendChart(record["productData_serialNumber"], "#slotTrendChart");
				}
			}
		});
	});
}
function exportAllSlot(){
	var E = Ladda.create( document.getElementById( "export" ) );
	E.start();
	var params = getParams();
	if(params == false){
		E.stop();
		return false;
	}
	$.post("slotOnlineTimeAnalysis/exportAllSlot", params, function (data) {
		E.stop();
		if (data.result) {
			fileDownload(data.fileName);
		} else {
			layer.open({
				title: "提示",
				content: "下载失败"
			});
		}
	});
}
function exportDisappearSlot(){
	var E = Ladda.create( document.getElementById( "exportDisappearSlot" ) );
	E.start();
	var params = getParams();
	if(params == false){
		E.stop();
		return false;
	}
	$.post("slotOnlineTimeAnalysis/exportDisappearSlot", params, function (data) {
		E.stop();
		if (data.result) {
			fileDownload(data.fileName);
		} else {
			layer.open({
				title: "提示",
				content: "下载失败"
			});
		}
	});
}

function slotTrendChart(serialNumber,block){
	$.ajax({
		type: "post",
		url: "slotOnlineTimeAnalysis/getSlotTrendChart",
		data : {"serialNumber":serialNumber},
		dataType: "json",
		beforeSend: function () {
			$(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
		},
		success: function (data) {
			$(block).html("");
			if (data.categories.length != 0) {
				$(block).highcharts({
					chart: {
						type: "line"
					},
					title: {
						text: null
					},
					xAxis: {
						categories: data.categories
					},
					yAxis: {
						min: 0,
						title: {
							text: null
						}
					},plotOptions: {
						series: {
							dataLabels: {
								enabled: true,
								//format: '{point.x} <br> {point.name}',
								formatter:function(){
									var point = this.point;
									if (point.name == "") {
										return '<span style="font-size:13px">' + 'productionDate: ' + point.category + '</span>';
									} else{
										return '<span style="font-size:13px">' + 'kgetTime: ' + point.category + '</span><br> ' +'<span style="font-size:13px">meContext: ' + point.name + '</span>';
									}
								}
							}
						}
					},
					tooltip: {
						headerFormat: '<span style="font-size:13px">{point.x}</span><br>',
						pointFormat: '<span style="font-size:13px">{point.name}</span>'
					},
					credits: {
						enabled: false,
					},
					series: [data.series]
				});
			}
		}
	});
}

function searchOneSolt(){
	var serialNumber = $("#serialNumber").val();
	if (serialNumber == "") {
		layer.open({
			title: "提示",
			content: "请输入板卡串号"
		});
		return false;
	}
	slotTrendChart(serialNumber,"#resultView");
	getOneSlotInfo(serialNumber);
}

function getOneSlotInfo(serialNumber){
	var S = Ladda.create( document.getElementById( "searchOneSolt" ) );
	S.start();
	var params = {"serialNumber":serialNumber};
	$.post("slotOnlineTimeAnalysis/getOneSlotInfo", params, function(data){
		S.stop();
		if (data["data"]["total"] == 0) {
			layer.open({
				title: "提示",
				content: "没有记录"
			});
			return false;
		}
		var fieldArr=[];
		var text = data["titles"];
		var keys = data["keys"];
		for(var i in text){  
			fieldArr[i]={field:keys[i],title:text[i],width:200,sortable:true};
		} 
		$("#resultTable").grid("destroy", true, true);
		var grid = $("#resultTable").grid({
			columns:fieldArr,
			dataSource:{
				url:"slotOnlineTimeAnalysis/getOneSlotInfo",
				success:function(data){
					grid.render(data["data"]);
				},
				type:"post"
			},
			params: params,
			pager: { limit: 10, sizes: [10, 20, 50, 100] },
			autoScroll:true,
			uiLibrary: "bootstrap",
			primaryKey:"",
			autoLoad: true
		});
	});
}
function exportOneSolt(){
	var E = Ladda.create( document.getElementById( "exportOneSolt" ) );
	E.start();
	var serialNumber = $("#serialNumber").val();
	if (serialNumber == "") {
		E.stop();
		layer.open({
			title: "提示",
			content: "请输入板卡串号"
		});
		return false;
	}
	$.post("slotOnlineTimeAnalysis/exportOneSolt",{"serialNumber":serialNumber},function(data){
		E.stop();
		if (data.result) {
			fileDownload(data.fileName);
		} else {
			layer.open({
				title: "提示",
				content: "下载失败"
			});
		}
	});
}

