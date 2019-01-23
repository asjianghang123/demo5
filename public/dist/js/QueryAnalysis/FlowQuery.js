$(document).ready(function () {
	//设置日期
	setTime();
	initSelects();
	//设置树
	setTree();
	$("#LTEQueryMoTree").treeview("collapseAll", {silent: true});
	// customDblClickFun();
	initElementTree();
	//设置表格
	setTable();

	//设置输入框状态
	setInputStatus();

	//数据库获取所有城市
	getAllCity();

	//初始化下拉框
	$("#subNetworks").multiselect({
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
	});

	//数据库获取对应subNwork
	$("#allCity").change(function () {
		getAllSubNetwork();
	});

	//数据库获取对应模式TDD/FDD
	$("#LTEFormat").change(function () {
		getFormatAllSubNetwork();
	});

	//设置小时/15分钟选择
	setHQSelect();

	//设置查询方式
	//setCheckedType();
	$("#checkedType").bootstrapToggle("off");


	$("#search").on("click", function () {
		doSearchLTE("table");
	});
	$("#save").on("click", function () {
		fileSave("file");
	});

	toogle("FlowQuery");
});
//导入文件

function importFlowQuery() {
	$("#import_modal").modal();
	$("#fileImportName").val("");
	$("#fileImports").val("");
}
function toNames(self) {
	$("#fileImportName").val(self.value);
}
function importFile() {
	var data ={
		"filename":$("#fileImportName").val()
	};
	// console.log(data);

	$.ajaxFileUpload({
		url: "FlowQuery/uploadFlowQueryFile",
		type:"POST",
		fileElementId: "fileImports",
		secureuri: false,
		dataType: "json",
		success: function (data) {
			if(data=="lenError"){
				layer.open({
					title: "提示",
					content: "没有数据或没有表头"
				});
			}else if(data=="emptyError"){
				layer.open({
					title: "提示",
					content: "请选择要上传的文件"
				});
			}else if(data=="dataError"){
				layer.open({
					title: "提示",
					content: "表格列数缺失"
				});
			}else{
				// doQueryNotice();
				setTree();
				$("#import_modal").modal("hide");

				layer.open({
					title: "提示",
					content: "上传成功"
				});
			}

		}
	});
}
//导入小区
function toName(self) {
	$.ajaxFileUpload({
		url: "FlowQuery/uploadFile",
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

//清空模板树
function clearLteQuery() {
	$("#paramQueryMoErbs").val("");
	setTree();
	$("#LTEQueryMoTree").treeview("collapseAll", {silent: true});
	// customDblClickFun();
}

//筛选模板树
function searchLTEQuery() {
	var inputData = $("#paramQueryMoErbs").val();
	inputData = $.trim(inputData);
	if (inputData == "") {
		setTree();
		// customDblClickFun();
		return;
	}
	var params = {
		inputData: inputData
	};
	var url = "FlowQuery/searchLTETreeData";
	//var treeData;

	$.get("FlowQuery/searchLTETreeData", params, function (data) {
		// console.log(data);
		//data = "["+data+"]";
		var tree = "#LTEQueryMoTree";
		$(tree).treeview({data: data});
		// customDblClickFun();
	});

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

//获取所有被选择的城市
function getChooseCitys() {
	var citys = $("#allCity").val();
	return citys;
}

function getFormatAllSubNetwork() {
	var citys = getChooseCitys();
	var format = $("#LTEFormat").val();
	var params = {
		format: format,
		citys: citys
	};
	$.get("FlowQuery/getFormatAllSubNetwork", params, function (data) {
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

function getAllSubNetwork() {
	var citys = getChooseCitys();
	var format = $("#LTEFormat").val();
	var params = {
		format: format,
		citys: citys
	};

	$.get("FlowQuery/getAllSubNetwork", params, function (data) {
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
	var url = "FlowQuery/getAllCity";
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
	$("#LTEFormat").removeAttr("disabled");
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
		} else if ($("#locationDim").val() == "erbs" || $("#locationDim").val() == "erbsGroup") {
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
	//$("#hourSelect").attr("disabled", "disabled");
	$("#quarterSelect").attr("disabled", "disabled");
	$("#timeDim").change(function () {
		if ($("#timeDim").val() == "hour" || $("#timeDim").val() == "hourgroup") {
			$("#quarterSelect").multiselect("disable");
			$("#hourSelect").multiselect("enable");
		} else if ($("#timeDim").val() == "quarter") {
			$("#quarterSelect").multiselect("enable");
			$("#hourSelect").multiselect("enable");
		} else {
			$("#hourSelect").multiselect("enable");
			$("#quarterSelect").multiselect("disable");
		}
	});
}

function setTable() {
	// $("#LTEQueryTable").bootgrid({	  //表格
	//	   ajax: true,
	//	   post: function ()
	//	   {
	//		   // To accumulate custom parameter with the request object
	//		   return {
	//			   id: "b0df282a-0d67-40e5-8558-c9e93b7befed"
	//		   };
	//	   },
	//	   url: "common/json/test.json"/*,
	//	   formatters: {
	//		   "link": function(column, row)
	//		   {
	//			   return "<a href=\"#\">" + column.id + ": " + row.id + "</a>";
	//		   }
	//	   }*/
	//   });
}

function setTree() {
	var tree = "#LTEQueryMoTree";
	$(tree).treeview({data: getTree()}); //树
}

function setTime() {
	$("#startTime").datepicker({format: "yyyy-mm-dd"});  //返回日期
	$("#endTime").datepicker({format: "yyyy-mm-dd"});

	var nowTemp = new Date();
	$("#startTime").datepicker("setValue", nowTemp);
	$("#endTime").datepicker("setValue", nowTemp);
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#startTime").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? '' : '';
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
			//return date.valueOf() <= checkin.date.valueOf() ? "disabled" : ";
			return date.valueOf() <= checkin.date.valueOf() ? '' : '';
		}
	}).on("changeDate", function (ev) {
		checkout.hide();
	}).data("datepicker");

}

function getTree() {
	var url = "FlowQuery/getLTETreeData";
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

function getParams(action) {
	var locationDim = $("#locationDim").val();
	var timeDim = $("#timeDim").val();
	var startTime = $("#startTime").val();
	var endTime = $("#endTime").val();
	var format = $("#LTEFormat").val();
	var citys = $("#allCity").val();
	if (citys == null) {
		//alert("请选择城市!");
		layer.open({
			title: "提示",
			content: "请选择城市"
		});
		return false;
	}
	var subNetworks = $("#subNetworks").val();
	if (subNetworks == null) {
		//alert("请选择subNetwork！");
		layer.open({
			title: "提示",
			content: "请选择subNetwork"
		});
		return false;
	}
	var LTETree = $("#LTEQueryMoTree").treeview("getSelected");
	if (LTETree == "") {
		//alert("请选择模板名称！");
		layer.open({
			title: "提示",
			content: "请选择模板名称"
		});
		return false;
	}
	// console.log(LTETree[0]);
	var moTree = LTETree[0].text;
	var moTreeId = LTETree[0].id;
	var moTreeSource=LTETree[0].dataSource;
	var hour = $("#hourSelect").val();
	var min = $("#quarterSelect").val();
	var cell = $("#cellInput").val();
	var erbs = $("#erbsInput").val();
	var parent = $("#LTEQueryMoTree").treeview("getParent", LTETree[0].nodeId).text;

	var flag = $("#checkedType").prop("checked");
	var checkStyle;
	if (flag) {
		checkStyle = "local";
	} else {
		checkStyle = "online";
	}

	var params = {
		template: moTree,
		templateId:moTreeId,
		dataSource:moTreeSource,
		locationDim: locationDim,
		timeDim: timeDim,
		startTime: startTime,
		endTime: endTime,
		hour: JSON.stringify(hour),//hour,
		//hour:hour,
		minute: JSON.stringify(min),//min,
		city: JSON.stringify(citys),//citys,
		subNet: JSON.stringify(subNetworks),//subNetworks,
		erbs: erbs,
		cell: cell,
		format: format,
		style: checkStyle,
		action: action,
		parent: parent
	};
	return params;
}


/*function show_confirm(xhr)
 {
 var r=confirm("请求超时,出现未知情况，是否愿意继续等待？");
 if (r!=true)
 {
 l.stop();
 S.stop();
 E.stop();
 xhr.abort();
 }
 }*/

//查询
function doSearchLTE(table) {
	var flag = $("#checkedType").prop("checked");
	var route;
	if (flag) {
		route = "FlowQuery/templateQueryOnline";
		if ($("#LTEFormat").val() == "FDD") {
			//alert("本地不能查询FDD");
			layer.open({
				title: "提示",
				content: "本地不能查询FDD"
			});
			$("#checkedType").bootstrapToggle("off");
			return;
		}
		if ($("#timeDim").val() == "quarter") {
			//alert("本地不能查询15分钟");
			layer.open({
				title: "提示",
				content: "本地不能查询15分钟"
			});
			$("#checkedType").bootstrapToggle("off");
			return;
		}
	} else {
		route = "FlowQuery/templateQueryLocal";
	}

	var l = Ladda.create(document.getElementById("search"));
	var S = Ladda.create(document.getElementById("save"));
	var E = Ladda.create(document.getElementById("export"));

	l.start();
	S.start();
	E.start();
	var params = getParams();

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
	}, setPDOSearchTime("lteQuery"));

	if(params.dataSource=="eniq"){
		var xhr = $.ajax({
			type: "POST",
			url: "FlowQuery/templateQuery",
			data: params,
			success: function (data) {
				// $("#search").on("click", function () {
				// 	doSearchLTE("table");
				// });
				if (timeout) { //清除定时器
					clearTimeout(timeout);
					timeout = null;
				}
				if ((JSON.parse(data).error).indexOf("Caught exception:") != -1) {
					l.stop();
					S.stop();
					E.stop();
					layer.open({
						title: "提示",
						content: JSON.parse(data).error
					});

					return false;
				}
				if (JSON.parse(data).error == "NOTFINDLINE") {
					l.stop();
					S.stop();
					E.stop();
					//alert("不存在的字段名");
					layer.open({
						title: "提示",
						content: "不存在的字段名"
					});
					return false;
				}

				if (JSON.parse(data).state == "overflow") {
					layer.open({
						title: "提示",
						content: "由于查询数据大于100万条，导出将按照100万条取！"
					});
				}

				$("#LTEQueryFile").val(JSON.parse(data).filename);
				var fieldArr = [];
				var text = (JSON.parse(data).text).split(",");
				for (var i in JSON.parse(data).rows[0]) {
					fieldArr[fieldArr.length] = {
						field: i,
						title: text[fieldArr.length],
						width: textWidth(text[fieldArr.length])
					};
				}
				var newData = JSON.parse(data).rows;

				$("#LTEQueryTable").grid("destroy", true, true);
				$("#LTEQueryTable").grid({
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
	}else if(params.dataSource=="nbi"){

		if(params.locationDim=="erbsGroup")
		{
			layer.open({
				title: "提示",
				content: "此模版不能查询基站组"
			});
			$("#checkedType").bootstrapToggle("off");
			l.stop();
			S.stop();
			E.stop();
			return;
		}
		if(params.locationDim=="subNetwork")
		{
			layer.open({
				title: "提示",
				content: "此模版不能查询子网"
			});
			$("#checkedType").bootstrapToggle("off");
			l.stop();
			S.stop();
			E.stop();
			return;
		}
		if(params.locationDim=="subNetworkGroup")
		{
			layer.open({
				title: "提示",
				content: "此模版不能查询子网组"
			});
			$("#checkedType").bootstrapToggle("off");
			l.stop();
			S.stop();
			E.stop();
			return;
		}

		var xhr = $.ajax({
			type: "POST",
			url: "FlowQuery/nbiQuery",
			data: params,
			success: function (data) {

				if (timeout) { //清除定时器
					clearTimeout(timeout);
					timeout = null;
				}
				$("#LTEQueryFile").val(JSON.parse(data).filename);
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

				$("#LTEQueryTable").grid("destroy", true, true);
				$("#LTEQueryTable").grid({
					columns: fieldArr,
					dataSource: newData,
					pager: {limit: 10, sizes: [10, 20, 50, 100]},
					autoScroll: true,
					uiLibrary: "bootstrap"
				});
				if (table == "file") {
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
	}
}

function textWidth(text) {
	var length = text.length;
	if (length > 15) {
		return length * 10;
	}
	return 150;
}


function fileSave(table) {
	var fileName = $("#LTEQueryFile").val();
	if (fileName != "") {
		//alert(fileName);
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
		//alert("No file generated so far!");
		layer.open({
			title: "提示",
			content: "下载失败"
		});
	}
	$("#save").on("click", function () {
		fileSave("file");
	});
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
	return matches[1].replace(/version/, "safari");
}

//最后一次触发节点Id
var lastSelectedNodeText = null;
//最后一次触发时间
var lastSelectTime = null;
//自定义业务方法
function customBusiness(data) {
	if (!data.nodes) {
		var text = data.text;
		var id = data.id;
		// console.log(data.nodeId);
		// console.log(data.id);
		// var parent = $("#LTEQueryMoTree").treeview("getParent", data.nodeId).text;
		$("#mtitle").html("指标-" + text);
		$("#checkTemplate").modal();
		$("#formula").html("");
		// setElementTree(id);
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


function initElementTree() {
	var tree = "#LTEElementTree";
	$(tree).treeview({
		data: null
	}); //树
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
	$("#LTEFormat").multiselect({
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