var GSMQueryTreeData = "";
var GSMQueryMoTreeId = "#GSMQueryMoTree";
$(document).ready(function () {
//     var nowTemp = new Date();
// $("#startTime").datepicker("setValue", nowTemp);
//设置日期
	setTime();
	initSelects();
//数据库获取所有城市
	getAllCity();
	//设置输入框状态
	setInputStatus();
	//设置小时/15分钟选择
	setHQSelect();
	//设置树
	setTree();
	$("#GSMQueryMoTree").treeview("collapseAll", {silent: true});
	customDblClickFun();
	initElementTree();
	//设置表格
	setTable();

	toogle("GSMQuery");
});

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
		} else if ($("#locationDim").val() == "erbs" || $("#locationDim").val() == "erbsGroup") {
			$("#erbsInput").removeAttr("disabled");
			$("#cellInput").attr("disabled", "true");
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

//---------start of paramTree---------

//---------end of paramTree---------
function setTree() {
	var tree = "#GSMQueryMoTree";
	$(tree).treeview({data: getTree()}); //树
}

function getTree() {
	var url = "GSMQuery/getGSMTreeData";
	var treeData;
	$.ajax({
		type: "GET",
		url: url,
		dataType: "json",
		async: false,
		success: function (data) {
			treeData = data;
		}
	});
	return treeData;
}

//-------设置日期------//
function setTime() {
	$("#startTime").datepicker({format: "yyyy-mm-dd"});  //返回日期
	$("#endTime").datepicker({format: "yyyy-mm-dd"});

	var nowTemp = new Date();
	$("#startTime").datepicker("setValue", nowTemp);
	$("#endTime").datepicker("setValue", nowTemp);
	//alert(nowTemp);
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#startTime").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		if (ev.date.valueOf() > checkout.date.valueOf()) {
			var newDate = new Date(ev.date);
			newDate.setDate(newDate.getDate() + 1);
			checkout.setValue(newDate);
		}
		checkin.hide();
		$("#endTime")[0].focus();
	}).data("datepicker");
	var checkout = $("#endTime").datepicker({
		onRender: function (date) {
			//return date.valueOf() <= checkin.date.valueOf() ? "disabled" : "";
			return date.valueOf() <= checkin.date.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkout.hide();
	}).data("datepicker");
}

//----------获得城市----------//

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
	var url = "GSMQuery/getAllCity";
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
//设置小时和15分钟的状态
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

function getChooseCitys() {
	var citys = $("#allCity").val();
	return citys;
}

function getParams(table) {
	var moTree = $("#GSMQueryMoTree").val();
	var locationDim = $("#locationDim").val();
	var timeDim = $("#timeDim").val();
	var startTime = $("#startTime").val();
	var endTime = $("#endTime").val();
	var citys = $("#allCity").val();
	if (citys == null) {
		//alert("请选择城市!");
		layer.open({
			title: "提示",
			content: "请选择城市"
		});
		return false;
	}
	var GSMTree = $("#GSMQueryMoTree").treeview("getSelected");
	if (GSMTree == "") {
		//alert("请选择模板名称！");
		layer.open({
			title: "提示",
			content: "请选择模板名称"
		});
		return false;
	}
	moTree = GSMTree[0].text;
	var moTreeId = GSMTree[0].id;
	var hour = $("#hourSelect").val();
	var min = $("#quarterSelect").val();
	var cell = $("#cellInput").val();
	var erbs = $("#erbsInput").val();
	//alert(moTree);
	var params = {
		template: moTree,
		templateId: moTreeId,
		locationDim: locationDim,
		timeDim: timeDim,
		startTime: startTime,
		endTime: endTime,
		hour: JSON.stringify(hour),//hour,
		//hour:hour,
		minute: JSON.stringify(min),//min,
		city: JSON.stringify(citys),//citys,
		//subNet:JSON.stringify(subNetworks),//subNetworks,
		cell: cell,
		erbs: erbs
		//action:action
	};
	//alert(params)
	return params;
}
//查询
function doSearchGSM(table) {
	var l = Ladda.create(document.getElementById("search"));
	var S = Ladda.create(document.getElementById("save"));
	var E = Ladda.create(document.getElementById("export"));
	l.start();
	S.start();
	E.start();
	var params = getParams(table);
	if (params == false) {
		l.stop();
		S.stop();
		E.stop();
		return false;
	}

	var timeout = setTimeout(function () {   // post设置超时时间
		/*var r=confirm("请求超时,出现未知情况，是否愿意继续等待？");
		 if (r!=true)
		 {
		 l.stop();
		 S.stop();
		 E.stop();
		 xhr.abort();
		 }*/
		layer.confirm("请求超时,出现未知情况，是否愿意继续等待？", {title: "提示"}, function (index) {
			layer.close(index);
		}, function (index) {
			l.stop();
			S.stop();
			E.stop();
			xhr.abort();
			layer.close(index);
		});
	}, setPDOSearchTime("gsmQuery"));

	var xhr = $.ajax({
		type: "POST",
		url: "GSMQuery/templateQuery",
		data: params,
		success: function (data) {
			if (timeout) { //清除定时器
				clearTimeout(timeout);
				timeout = null;
			}
			$("#GSMQueryFile").val(JSON.parse(data).filename);
			$("#GSMQueryFile").val(JSON.parse(data).filename);
			var fieldArr = [];
			var text = (JSON.parse(data).text).split(",");
			for (var i in JSON.parse(data).records[0]) {
				var textLength = text[fieldArr.length].length;
				var width = textLength * 15;
				if (text[fieldArr.length] == "day") {
					width = textLength * 30;
				}

				fieldArr[fieldArr.length] = {field: i, title: text[fieldArr.length], width: width};

			}
			var newData = JSON.parse(data).records;

			$("#GSMQueryTable").grid("destroy", true, true);
			$("#GSMQueryTable").grid({
				columns: fieldArr,
				dataSource: newData,
				pager: {limit: 10, sizes: [10, 20, 50, 100]},
				autoScroll: true,
				uiLibrary: "bootstrap"
			});
			if (table == "file") {
				//alert(JSON.parse(data).filename);
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

	/*
	 $.post("GSMQuery/templateQuery", params, function(data){
	 $("#GSMQueryFile").val(JSON.parse(data).filename);
	 var fieldArr=[];
	 var text=(JSON.parse(data).text).split(",");
	 for(var i in JSON.parse(data).records[0]){
	 var textLength =text[fieldArr.length].length;
	 var width = textLength * 15;
	 if(text[fieldArr.length] == "day"){
	 width=textLength *30;
	 }

	 fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:width};

	 }
	 var newData = JSON.parse(data).records;

	 $("#GSMQueryTable").grid("destroy", true, true);
	 $("#GSMQueryTable").grid({
	 columns:fieldArr,
	 dataSource:newData,
	 pager: { limit: 10, sizes: [10, 20, 50, 100] },
	 autoScroll:true,
	 uiLibrary: "bootstrap"
	 });
	 if(table == "file") {
	 alert(JSON.parse(data).filename);
	 download(JSON.parse(data).filename);
	 }
	 l.stop();
	 S.stop();
	 E.stop();
	 });*/
}
function fileSave(table) {
	var fileName = $("#GSMQueryFile").val();
	//alert(fileName);
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
	} else {
		//alert("No file generated so far!");
		layer.open({
			title: "提示",
			content: "下载失败"
		});
	}
}
function setTable() {
	// $("#LTEQueryTable").bootgrid({   //表格
	//       ajax: true,
	//       post: function ()
	//       {
	//           // To accumulate custom parameter with the request object
	//           return {
	//               id: "b0df282a-0d67-40e5-8558-c9e93b7befed"
	//           };
	//       },
	//       url: "common/json/test.json"/*,
	//       formatters: {
	//           "link": function(column, row)
	//           {
	//               return "<a href=\"#\">" + column.id + ": " + row.id + "</a>";
	//           }
	//       }*/
	//   });
}
//搜索模板树
function searchGSMQuery() {
	var inputData = $("#paramQueryMoErbs").val();
	inputData = $.trim(inputData);
	if (inputData == "") {
		setTree();
		customDblClickFun();
		return;
	}
	var params = {
		inputData: inputData
	};
	var url = "GSMQuery/searchGSMTreeData";
	//var treeData;

	$.get("GSMQuery/searchGSMTreeData", params, function (data) {
		//data = "["+data+"]";
		var tree = "#GSMQueryMoTree";
		$(tree).treeview({data: data});
		customDblClickFun();
	});
}
//清除模板树
function clearGSMQuery() {
	$("#paramQueryMoErbs").val("");
	setTree();
	customDblClickFun();
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
	$("#GSMQueryMoTree").on("nodeSelected", function (event, data) {
		clickNode(event, data);
	});
	//节点取消选中时触发
	$("#GSMQueryMoTree").on("nodeUnselected", function (event, data) {
		clickNode(event, data);
	});
}

function initElementTree() {
	var tree = "#GSMElementTree";
	$(tree).treeview({
		data: null
	}); //树
}

function setElementTree(text) {
	var tree = "#GSMElementTree";
	$(tree).treeview({
		data: getElementTree(text),
		onNodeSelected: function (event, data) {
			$("#formula").html(data.formula);
		}
	}); //树
}

function getElementTree(text) {
	var url = "GSMQuery/getElementTree";
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
	var url = "GSMQuery/getKpiNamebyId";
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
		// dropRight: true,
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
	$.get({
		url:"GSMQuery/getTimeDim",
		success:function(data){
			var newOptions = [];
			var obj = {};
			var data = eval('('+data+')');
			// console.log(data);
			for(var key in data){
				obj={
					label:data[key],
					value:key
				}
				if(key=='quarter'){
					$("#quarterDiv").show();
					$("#quarterLabel").show();
				}
				newOptions.push(obj);

			}
			$("#timeDim").multiselect("dataprovider", newOptions);
		}
	})


}
//导入小区
function toName(self) {
	$.ajaxFileUpload({
		url: "GSMQuery/uploadFile",
		//data : data,
		fileElementId: "fileImport",
		secureuri: false,
		dataType: "json",
		type: "post",
		success: function (data, status) {
			$("#cellInput").val(data);
		},
		error: function (data, status, e) {
			//alert("上传失败");
			layer.open({
				title: "提示",
				content: "上传失败"
			});
		}
	});
}