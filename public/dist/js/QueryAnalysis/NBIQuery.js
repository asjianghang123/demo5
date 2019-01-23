$(document).ready(function () {
	//设置日期
	// setTime();
	initSelects();
	$("#startTime").datepicker({format: "yyyy-mm-dd"});  //返回日期
	$("#endTime").datepicker({format: "yyyy-mm-dd"});

	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;
	var format = $("#NBIFormat").val();

	// console.log(today);
	var params = {
		format: format,
		city: getFirstCity()
	};
	$.get("NBIQuery/NBIsTime", params, function (data) {
		var sdata = [];
		for (var i = 0; i < data.length; i++) {
			if (data[i] === today) {
				continue;
			}
			sdata.push(data[i]);
		}
		sdata.push(today);
		$("#startTime").datepicker("setValues", sdata);
		$("#endTime").datepicker("setValues", sdata);
	});
	// $.get("NBIQuery/NBIeTime", params, function(data){
	//     var sdata = [];
	//     for(var i=0; i<data.length; i++){
	//       if(data[i] === today){
	//         continue;
	//       }
	//       sdata.push(data[i]);
	//     }
	//     sdata.push(today);
	//     $("#endTime").datepicker("setValues", sdata);
	// })
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#startTime").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");
	var checkout = $("#endTime").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkout.hide();
	}).data("datepicker");

	//设置树
	setNbiTree("TDD");
	//设置树
	$("#NBIQueryMoTree").treeview("collapseAll", {silent: true});
	customDblClickFun();
	initElementTree();

	//设置输入框状态
	setInputStatus();

	//数据库获取所有城市
	getAllCity();

	$('#NBIFormat').change(function() {
		if(format == "TDD") {
			setNbiTree("TDD");
			getAllCity();
			customDblClickFun();
		}else if ("FDD"){
			setNbiTree("FDD");
			getAllCity();
			customDblClickFun();
		} else if ("NBIOT") {
			setNbiTree("NBIOT");
			getAllCity();
			customDblClickFun();
		}
	});
	//初始化下拉框
	/*$("#subNetworks").multiselect({
		buttonWidth: "100%",
		enableFiltering: true,
		nonSelectedText: "请选择子网",
		filterPlaceholder: "搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有子网",
		maxHeight: 200,
		maxWidth: "100%"
	});*/

	//设置小时/15分钟选择
	setHQSelect();

	toogle("NBIQuery");
});

//导入小区
function toName(self) {
	$.ajaxFileUpload({
		url: "NBIQuery/uploadFile",
		//data : data,
		fileElementId: "fileImport",
		secureuri: false,
		dataType: "json",
		type: "post",
		success: function (data, status) {
			$("#cellInput").val(data);
		},
		error: function (data, status, e) {
			// alert("上传失败");
			layer.open({
				title: "提示",
				content: "上传失败"
			});
		}
	});
}

//获取所有被选择的城市
function getChooseCitys() {
	var citys = $("#allCity").val();
	return citys;
}

function getFormatAllSubNetwork() {
	var citys = getChooseCitys();
	var format = $("#NBIFormat").val();
	var params = {
		format: format,
		citys: citys
	};
	console.log(format);
	$.get("NBIQuery/getFormatAllSubNetwork", params, function (data) {
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

function getParams(action) {
	var locationDim = $("#locationDim").val();
	var timeDim = $("#timeDim").val();
	var startTime = $("#startTime").val();
	var endTime = $("#endTime").val();
	var citys = $("#allCity").val();
	var format = $("#NBIFormat").val();
	if (citys == null) {
		// alert("请选择城市!");
		layer.open({
			title: "提示",
			content: "请选择城市!"
		});
		return false;
	}

	var NBITree = $("#NBIQueryMoTree").treeview("getSelected");
	if (NBITree == "") {
		// alert("请选择模板名称！");
		layer.open({
			title: "提示",
			content: "请选择模板名称!"
		});
		return false;
	}
	var moTree = NBITree[0].text;
	var moTreeId = NBITree[0].id;
	var hour = $("#hourSelect").val();
	var min = $("#quarterSelect").val();
	var cell = $("#cellInput").val();
	var erbs = $("#erbsInput").val();

	var params = {
		template: moTree,
		templateId:moTreeId,
		locationDim: locationDim,
		timeDim: timeDim,
		startTime: startTime,
		endTime: endTime,
		hour: JSON.stringify(hour),//hour,
		//hour:hour,
		minute: JSON.stringify(min),//min,
		city: JSON.stringify(citys),//citys,
		erbs: erbs,
		cell: cell,
		format: format,
		action: action
	};
	return params;
}
//清空模板树
function clearNBIQuery() {
	var format = $("#NBIFormat").val();
	$("#paramQueryMoErbs").val("");
	setNbiTree(format);
	$("#NBIQueryMoTree").treeview("collapseAll", {silent: true});
	customDblClickFun();
}

//筛选模板树
function searchNBIQuery() {
	var format = $("#NBIFormat").val();
	var inputData = $("#paramQueryMoErbs").val();
	inputData = $.trim(inputData);
	if (inputData == "") {
		setNbiTree(format);
		customDblClickFun();
		return;
	}
	var params = {
		inputData: inputData,
		format: format
	};
	var url = "NBIQuery/searchNBITreeData";
	//var treeData;

	$.get("NBIQuery/searchNBITreeData", params, function (data) {
		//data = "["+data+"]";
		var tree = "#NBIQueryMoTree";
		$(tree).treeview({data: data});
		customDblClickFun();
	});
}

function doSearch(action) {
	var l = Ladda.create(document.getElementById("search"));
	var S = Ladda.create(document.getElementById("save"));
	var E = Ladda.create(document.getElementById("export"));
	l.start();
	S.start();
	E.start();
	var params = getParams(action);
	if (params == false) {
		l.stop();
		S.stop();
		E.stop();
		return false;
	}

	var timeout = setTimeout(function () {   // post设置超时时间
		var r = confirm("请求超时,出现未知情况，是否愿意继续等待？");
		if (r != true) {
			l.stop();
			S.stop();
			E.stop();
			xhr.abort();
		}
	}, setPDOSearchTime());

	var xhr = $.ajax({
		type: "POST",
		url: "NBIQuery/templateQuery",
		data: params,
		success: function (data) {
			if (timeout) { //清除定时器
				clearTimeout(timeout);
				timeout = null;
			}
			if(JSON.parse(data).check!=0) {
				l.stop();
				S.stop();
				E.stop();
				layer.open({
					title: "检查公式",
					content: JSON.parse(data).check
				});
			}
			$("#NBIQueryFile").val(JSON.parse(data).filename);
			// alert(JSON.parse(data).filename)
			// console.log()
			var fieldArr = [];
			var text = (JSON.parse(data).text).split(",");
			for (var i in text) {
				fieldArr[fieldArr.length] = {
					field: i,
					title: text[fieldArr.length],
					width: textWidth(text[fieldArr.length]),
					sortable:true
				};
			}
			var newData = JSON.parse(data).rows;

			$("#NBIQueryTable").grid("destroy", true, true);
			$("#NBIQueryTable").grid({
				columns: fieldArr,
				dataSource: newData,
				pager: {limit: 10, sizes: [10, 20, 50, 100]},
				autoScroll: true,
				uiLibrary: "bootstrap"
			});
			if (action == "file") {
				// alert(JSON.parse(data).filename);
				layer.open({
					title: "提示",
					content: JSON.parse(data).filename,
					yes:function(index, layero){
						download(JSON.parse(data).filename);
						layer.close(index);
					}
				});
				
			}
			l.stop();
			S.stop();
			E.stop();
		}
	});

	/*var xhr = $.ajax({
	 type:"POST",
	 url:"NBIQuery/templateQueryHeader",
	 data:params,
	 success:function(data){
	 if(timeout){ //清除定时器
	 clearTimeout(timeout);
	 timeout=null;
	 }
	 var fieldArr=[];
	 var text=(JSON.parse(data).text).split(",");
	 var ids=(JSON.parse(data).ids).split(",");
	 for(var i in text){
	 fieldArr[fieldArr.length]={field:ids[i],title:text[fieldArr.length],width:textWidth(text[fieldArr.length])};
	 }
	 console.log(fieldArr);
	 $("#NBIQueryTable").grid("destroy", true, true);
	 var grid = $("#NBIQueryTable").grid({
	 columns:fieldArr,
	 params : params,
	 dataSource:{
	 url: "NBIQuery/templateQuery",
	 type:"post",
	 success: function(data){
	 $("#NBIQueryFile").val(JSON.parse(data).filename);
	 grid.render(JSON.parse(data));
	 l.stop();
	 S.stop();
	 E.stop();
	 }
	 },
	 pager: { limit: 10, sizes: [10, 20, 50, 100] },
	 autoScroll:true,
	 uiLibrary: "bootstrap",
	 //primaryKey : "id",
	 autoLoad: true
	 });
	 if(action == "file") {
	 alert(JSON.parse(data).filename);
	 download(JSON.parse(data).filename);
	 }
	 }
	 });*/

	/*$.post("NBIQuery/templateQueryHeader", params, function(data){
	 var fieldArr=[];
	 var text=(JSON.parse(data).text).split(",");
	 var ids=(JSON.parse(data).ids).split(",");
	 for(var i in text){
	 fieldArr[fieldArr.length]={field:ids[i],title:text[fieldArr.length],width:textWidth(text[fieldArr.length])};
	 }
	 console.log(fieldArr);
	 $("#NBIQueryTable").grid("destroy", true, true);
	 var grid = $("#NBIQueryTable").grid({
	 columns:fieldArr,
	 params : params,
	 dataSource:{
	 url: "NBIQuery/templateQuery",
	 type:"post",
	 success: function(data){
	 $("#NBIQueryFile").val(JSON.parse(data).filename);
	 grid.render(JSON.parse(data));
	 l.stop();
	 S.stop();
	 E.stop();
	 }
	 },
	 pager: { limit: 10, sizes: [10, 20, 50, 100] },
	 autoScroll:true,
	 uiLibrary: "bootstrap",
	 //primaryKey : "id",
	 autoLoad: true
	 });
	 if(action == "file") {
	 alert(JSON.parse(data).filename);
	 download(JSON.parse(data).filename);
	 }
	 });*/
}

function textWidth(text) {
	var length = text.length;
	if (length > 15) {
		return length * 13;
	}
	return 150;
}

function setHQSelect() {
	$("#hourSelect").multiselect({
		buttonWidth: "100%",
		enableFiltering: true,
		nonSelectedText: "请选择小时",
		filterPlaceholder: "搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有小时",
		maxHeight: 200,
		maxWidth: "100%"
	});

	$("#quarterSelect").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "请选择15分钟",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有",
		maxHeight: 200,
		maxWidth: "100%"
	});
}

function getAllCity() {
	$("#allCity").multiselect({
		dropRight: true,
		buttonWidth: "100%",
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
	var url = "NBIQuery/getAllCity";
	$.ajax({
		type: "GET",
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

function setInputStatus() {
	//区域维度初始值设置
	$("#locationDim").val("city");
	$("#cellInput").attr("disabled", "true");
	$("#erbsInput").attr("disabled", "true");
	$("#cellInput").val("");
	$("#erbsInput").val("");
	$("#locationDim").change(function () {
		if ($("#locationDim").val() == "cell" || $("#locationDim").val() == "cellGroup") {
			$("#cellInput").removeAttr("disabled");
			$("#erbsInput").attr("disabled", "true");
			$("#erbsInput").val("");
		} else if ($("#locationDim").val() == "erbs") {
			$("#cellInput").attr("disabled", "true");
			$("#erbsInput").removeAttr("disabled");
			$("#cellInput").val("");
		} else {
			$("#cellInput").attr("disabled", "true");
			$("#erbsInput").attr("disabled", "true");
			$("#cellInput").val("");
			$("#erbsInput").val("");
		}
	});

	//时间维度初始值设置
	$("#timeDim").val("day");
	$("#hourSelect").attr("disabled", "disabled");
	$("#quarterSelect").attr("disabled", "disabled");
	$("#timeDim").change(function () {
		if ($("#timeDim").val() == "hour" || $("#timeDim").val() == "hourgroup") {
			$("#quarterSelect").multiselect("disable");
			$("#hourSelect").multiselect("enable");
		} else if ($("#timeDim").val() == "quarter") {
			$("#quarterSelect").multiselect("enable");
			$("#hourSelect").multiselect("enable");
		} else {
			$("#hourSelect").multiselect("disable");
			$("#quarterSelect").multiselect("disable");
		}
	});
}

// function setTime(){
//   $("#startTime").datepicker({format: "yyyy-mm-dd"});  //返回日期
//   $("#endTime").datepicker({format: "yyyy-mm-dd"});

//   var nowTemp = new Date();
//   $("#startTime").datepicker("setValue", nowTemp);
//   $("#endTime").datepicker("setValue", nowTemp);
//   //alert(nowTemp);
//   var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
//   var checkin = $("#startTime").datepicker({
//     onRender: function(date) {
//       return date.valueOf() < now.valueOf() ? "" : "";
//     }
//   }).on("changeDate", function(ev) {
//     if (ev.date.valueOf() > checkout.date.valueOf()) {
//       var newDate = new Date(ev.date);
//       newDate.setDate(newDate.getDate() + 1);
//       checkout.setValue(newDate);
//     }
//     checkin.hide();
//     $("#endTime")[0].focus();
//     }).data("datepicker");
//       var checkout = $("#endTime").datepicker({
//       onRender: function(date) {
//         //return date.valueOf() <= checkin.date.valueOf() ? "disabled" : "";
//         return date.valueOf() <= checkin.date.valueOf() ? "" : "";
//       }
//     }).on("changeDate", function(ev) {
//       checkout.hide();
//     }).data("datepicker");
// }

function getNbiTree(format) {
	var format = $("#NBIFormat").val();
	var url = "NBIQuery/getNbiTreeData";
	var treeData;
	$.ajax({
		type: "GET",
		url: url,
		data : {"format":format},
		dataType: "json",
		async: false,
		success: function (data) {
			treeData = data;
		}
	});

	return treeData;
}

function setNbiTree(format) {
	$("#NBIQueryMoTree").treeview({data: getNbiTree(format)});
}

function fileSave(table) {
	var fileName = $("#NBIQueryFile").val();
	// alert(fileName);
	if (fileName != "") {
		layer.open({
			title: "提示",
			content: fileName,
			yes:function(index, layero){
				var fileNames = csvZipDownload(fileName);
				download(fileNames);
				layer.close(index);
			}
		});
	}
	else {
		layer.open({
			title: "提示",
			content: "下载失败"
		});
		// alert("No file generated so far!");
	}
}

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
//最后一次触发节点Id
var lastSelectedNodeText = null;
//最后一次触发时间
var lastSelectTime = null;
//自定义业务方法
function customBusiness(data) {
	if (!data.nodes) {
		var text = data.text;
		$("#mtitle").html("指标-" + text);
		$("#checkTemplate").modal();
		$("#formula").html("");
		setElementTree(data.id);
	}

}
function clickNode(event, data) {
	if (lastSelectedNodeText && lastSelectTime) {
		var time = new Date().getTime();
		var t = time - lastSelectTime;
		if (lastSelectedNodeText == data.id && t < 300) {
			customBusiness(data);
		}
	}
	lastSelectedNodeText = data.id;
	lastSelectTime = new Date().getTime();
	$("#customName").val(data.id);
}
//自定义双击事件
function customDblClickFun() {
	//节点选中时触发
	$("#NBIQueryMoTree").on("nodeSelected", function (event, data) {
		clickNode(event, data);
	});
	//节点取消选中时触发
	$("#NBIQueryMoTree").on("nodeUnselected", function (event, data) {
		clickNode(event, data);
	});
}

function initElementTree() {
	var tree = "#NBIElementTree";
	$(tree).treeview({
		data: null
	}); //树
}

function setElementTree(text) {
	var tree = "#NBIElementTree";
	$(tree).treeview({
		data: getElementTree(text),
		onNodeSelected: function (event, data) {
			$("#formula").html(data.formula);
		}
	}); //树
}

function getElementTree(text) {
	var url = "NBIQuery/getElementTree";
	var treeData;
	$.ajax({
		type: "GET",
		url: url,
		data: {
			templateName: text
		},
		dataType: "json",
		async: false,
		success: function (data) {
			$("#elementIds").val(data.elementId);
			treeData = getKpiNamebyId(data.elementId);
		}
	});
	return treeData;
}
function getKpiNamebyId(id) {
	var url = "NBIQuery/getKpiNamebyId";
	var treeData;
	$.ajax({
		type: "GET",
		url: url,
		data: {
			id: id
		},
		dataType: "json",
		async: false,
		success: function (data) {
			treeData = data;
		}
	});
	return treeData;
}
function initSelects() {
	$("#locationDim").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "请选择区域维度",
		//filterPlaceholder:"搜索",
		//nSelectedText:"项被选中",
		//includeSelectAllOption:true,
		//selectAllText:"全选/取消全选",
		//allSelectedText:"已选中所有平台类型",
		maxHeight: 200,
		maxWidth: "100%"
	});
	$("#timeDim").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "请选择时间维度",
		//filterPlaceholder:"搜索",
		//nSelectedText:"项被选中",
		//includeSelectAllOption:true,
		//selectAllText:"全选/取消全选",
		//allSelectedText:"已选中所有平台类型",
		maxHeight: 200,
		maxWidth: "100%"
	});
	$("#NBIFormat").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "请选择制式",
		//filterPlaceholder:"搜索",
		//nSelectedText:"项被选中",
		//includeSelectAllOption:true,
		//selectAllText:"全选/取消全选",
		//allSelectedText:"已选中所有平台类型",
		maxHeight: 200,
		maxWidth: "100%"
	});
}