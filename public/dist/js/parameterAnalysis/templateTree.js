$(document).ready(function () {
	toogle("baselineCheck");
	//设置树
	setTree();
	getData();
	getAllCity();
	// getInit();
	//根据树，得到相应的值
});

//清空模板树
function clearLteQuery() {
	$("#paramQueryMoErbs").val("");
	setTree();
}

function setTree() {
	var tree = "#templateTree";
	$(tree).treeview({
		data: getTree(),
		onNodeSelected: function (event, data) {
			paramQuerySearch();
		}
	}); //树
}
//--------start of tableSearch-----
function paramQuerySearch() {
	var params = getParam("paramQuery");
	initDistribution(params);
	//parameterSearch(params);
}
var table = null;
function parameterSearch(params) {
	if (params == false) {
		return false;
	}
	var fieldArr = [];
	$.get("baselineCheck/getParamTableField", params, function (data) {
		var paraName = data.split(",");
		for (var i in paraName) {
			if (paraName[i] != "id") {
				if (paraName[i] == "mo") {
					fieldArr[fieldArr.length] = {field: paraName[i], title: paraName[i], width: 300};
				} else {
					fieldArr[fieldArr.length] = {field: paraName[i], title: paraName[i], width: 150};
				}
			}
		}
		$("#paramQueryTable").grid("destroy", true, true);
		$("#paramQueryTable").grid({
			// dataSource:"paramQuery/getParamItems/"+JSON.stringify(params),
			columns: fieldArr,
			dataSource: {url: "baselineCheck/getParamItems", type: "post", data: params},
			primaryKey: "id",
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			//shrinkToFit: false,
			uiLibrary: "bootstrap",
		});
	});
}

function getTree() {
	var url = "baselineCheck/getBaseTree";
	var treeData;
	$.ajax({
		type: "post",
		url: url,
		dataType: "json",
		async: false,
		success: function (data) {
			treeData = data;
		}
	});
	return treeData;
}

function getAllCity() {
	$("#allCity").multiselect({
		dropRight: true,
		buttonWidth: 200,
		//enableFiltering: true,
		nonSelectedText: "请选择城市",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有平台类型",
		maxHeight: 200,
		maxWidth: "100%"
	});
	var url = "baselineCheck/getParamCitys";
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
			$("#allCity").multiselect("dataprovider", newOptions);
		}
	});
}
//--end of paramQueryCity init--
function getData() {
	//获取时间
	var url = "baselineCheck/getParamTasks";
	$.ajax({
		type: "post",
		url: url,
		//data:{ids:platform_type_ids},
		dataType: "json",
		success: function (data) {
			var paramQueryDateSelect = $("#paramQueryDate").select2({
				placeholder: "请选择日期",
				//allowClear: true,
				data: data
			});
			$("#paramQueryDate").val(getCurrentDate()).trigger("change");
			var task = getCurrentDate("kget");
			$("#paramQueryDate").val(getCurrentDate("kget")).trigger("change");
			if ($("#paramQueryDate").val() == null) {
				$("#paramQueryDate").val(getYesterdayDate("kget")).trigger("change");
			}
		}
	});
}
function getParam(action) {
	if (action == "paramQuery") {
		var task = $("#paramQueryDate").val();
		var moSelected = $("#templateTree").treeview("getSelected");
		if (moSelected == "") {
			//alert("Please choose parameter tree first!");
			layer.open({
				title: "提示",
				content: "请选择参数"
			});
			return false;
		}
		var mo = moSelected[0].text;
		var templateId = moSelected[0].id;
		if (task != null) {
			var params = {
				db: task,
				table: mo,
				templateId: templateId,
			};
			return params;
		} else {
			//alert("Please choose database first!");
			layer.open({
				title: "提示",
				content: "请选择数据库"
			});
			return false;
		}
	}
}
//--------end of tableSearch-------
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

function initDistribution(params) {
	var databaseDate = "";
	var templateId = "";
	//传入城市
	var databaseconnCity;
	var databaseconnCityParam = {
		db: "mongs",
		table: "databaseconn"
	};
	$.ajaxSetup({async: false});     //同步执行
	$.post("baselineCheck/getAllCity", databaseconnCityParam, function (data) {
		databaseconnCity = data;
	});
	$.ajaxSetup({async: true});    //异步执行
	var params_distribution = {
		db: params.db,  //无线掉线率
		table: params.table,
		city: databaseconnCity,
		templateId: params.templateId
	};
	$.ajax({
		type: "post",
		url: "baselineCheck/getChartDataCategory",
		data: params_distribution,
		dataType: "json",
		beforeSend: function () {
			$("#categoryDistribution").html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
		},
		success: function (data) {
			$("#categoryDistribution").highcharts({
				chart: {type: "column"},
				exporting: {
					enabled: true,
				},
				title: {
					text: "category分布",
					x: -20 //center
				},
				credits: {
					enabled: false
				},
				subtitle: {
					text: "     ",
					x: -20
				},
				xAxis: {
					categories: data.category
				},
				yAxis: {
					title: {
						text: "Number"
					},
					plotLines: [{
						value: 0,
						width: 1,
						color: "#808080"
					}]
				},
				tooltip: {
					valueSuffix: ""
				},
				legend: {
					layout: "horizontal",
					align: "center",
					x: 0,
					verticalAlign: "bottom",
					y: 0,
					backgroundColor: "#FFFFFF"
				},
				series: data.series
			});
		}

	});
}
function changeDate(val) {
	var task = val;
	var moSelected = $("#templateTree").treeview("getSelected");
	// debugger;
	var mo, templateId;
	if (moSelected == "") {
		// alert("Please choose parameter tree first!");
		mo = "";
		templateId = "";
	} else {
		mo = moSelected[0].text;
		templateId = moSelected[0].id;
		if (task != null) {
			var params = {
				db: task,
				table: mo,
				templateId: templateId,
			};
			initDistribution(params);
		} else {
			//alert("Please choose database first!");
			layer.open({
				title: "提示",
				content: "请选择数据库"
			});
			return false;
		}
	}

}
var meContexts = "";
//查询按钮
function parameterViewSearch(){
	var s = Ladda.create( document.getElementById("search"));
	//s.start();  
	var task = $("#paramQueryDate").val();
	var moSelected = $("#templateTree").treeview("getSelected");
	if(moSelected == ""){
		layer.open({
			title: "提示",
			content: "请选择参数"
		});
		return false;
	}
	var mo = moSelected[0].text;
	var templateId = moSelected[0].id;
	var params;
	if(task != null){
		var citys = $("#allCity").val();
		params = {
			db:task,
			filter:true,
			templateId:templateId,
			citys:citys
		};
	}else{
		layer.open({
			title: "提示",
			content: "请选择数据库"
		});
		return false;
	}
	var fileName = $("#cellInput").val();
	if (fileName.indexOf(".") > 0) {
		//读取导入的站点信息
		$.ajaxFileUpload({
			url : "baselineCheck/uploadFile",   　
			data : null,
			fileElementId : "fileImport",           
			secureuri : false,                          
			dataType:"json",
			type: "post",                     
			success:function(data, status){ 
				$.post("baselineCheck/getFileContent",{"fileName":data},function(data){
					meContexts = data;
					params.meContexts = data;
					params.flag = "file";
					if (data) queryDetail(params,s);
					else {
						layer.open({
							title: "提示",
							content: "文件解析失败"
						});
					}
				});
			},
			error:function(data, status, e){
				layer.open({
					title: "提示",
					content: "文件上传失败"
				});
			}
		});
	}else if (fileName != "") {
		params.meContexts = fileName;
		params.flag = "text";
		queryDetail(params,s);
	} else {
		params.flag = "";
		queryDetail(params,s);
	}
}
function queryDetail(params,s){
	// end of 读取导入的站点信息
	var fieldArr=[];
	$.post("baselineCheck/getTableField",params,function(data){
		s.stop();
		for(var k in data){
			if (k == "mo") {
				fieldArr[fieldArr.length]={field:k,title:k,width:1200};
			}else if(k == "qualification"){
				fieldArr[fieldArr.length]={field:k,title:k,width:200};
			}
			else{
				fieldArr[fieldArr.length]={field:k,title:k,width:textWidth(k)};
			}
		}
		$("#tempParameterCellPrintTable").grid("destroy", true, true);
		$("#tempParameterCellPrintTable").grid({
			// dataSource:"paramQuery/getParamItems/"+JSON.stringify(params),
			columns:fieldArr,
			dataSource: { url: "baselineCheck/getParamItems", data: params,type:"post"},
			primaryKey: "id",
			pager: { limit: 10, sizes: [10, 20, 50, 100] },
			autoScroll:true,
			//shrinkToFit: false,
			uiLibrary: "bootstrap",
			//autoGenerateColumns: true,
			//responsive: true,
		});
	});
}
function textWidth(text){
	var length = text.length;
	if(length > 15){
		return length*10;
	}
	return 150;
}
//导出
function exporttofile(){
	var e = Ladda.create( document.getElementById("export"));
	e.start();
	var task = $("#paramQueryDate").val();
	var moSelected = $("#templateTree").treeview("getSelected");
	//var rows = $("#tempParameterCellPrintTable").datagrid("getRows");
	var mo = moSelected[0].text;
	var templateId = moSelected[0].id;
	var citys = $("#allCity").val();
	var params = {
		db:task,
		table:"ParaCheckBaseline",
		filter:true,
		templateId:templateId,
		citys:citys
	};
	//if(meContexts) params.meContexts = meContexts;
	var fileName = $("#cellInput").val();
	if (fileName.indexOf(".") > 0) {
		params.meContexts = meContexts;
		params.flag = "file";
	}else if (fileName != "") {
		params.meContexts = fileName;
		params.flag = "text";
	}else{
		params.flag = "";
	}
	$.post("baselineCheck/baselineFile",params,function(data){
		e.stop();
		if(data.result){
			fileDownload(data.fileName);
		}else{
			//alert("There is error occured!");
			layer.open({
				title: "提示",
				content: "下载失败"
			});
		}
	}); 
}
function toName(self){
	$("#cellInput").val(self.value);
}


