$(function () {
	toogle("ctrSignalingAnalysis");
	$("#ztreeNode").val("");
	//setTime();
	getDataBase();
	$("#modalDialog").draggable();//为模态对话框添加拖拽
	$("#detailMessage").css("overflow", "hidden");

	// showxialakuang();
});

/*function showxialakuang() {
	$.post("ctrSignalingAnalysis/getxialakuang", null, function(data){
		alert(data);
	});
}*/


function getDataBase() {
	var url = "ctrSignalingAnalysis/getDataBase";
	$.get(url, null, function (data) {
		data = eval(data);
		var dataArr = [];
		for (var i = data.length - 1; i >= 0; i--) {
			if (data[i]["id"].indexOf("my_result") >= 0) {
				continue;
			}
			dataArr.push(data[i]);
		}
		$("#dataBase").select2({
			placeholder: "请选择数据库",
			data: dataArr
		});
	});
}

function exports() {
	var s = Ladda.create(document.getElementById("querySelectBtn"));
	s.start();
	fileSave($("#fileNameBak").html());
	s.stop();
}

function select(q) {
	var dataBase = $("#dataBase").val();
	var node = $("#ztreeNode").val();
	var eventName = $("#eventName").val();
	if(eventName != ""){
		node = "einternal.EVENT_NAME:" + eventName;
	}
	// var s = Ladda.create(document.getElementById("queryBtn"));
	// s.start();
	// if (node != "") {
	$("#signalingChart").grid("destroy", true, true);
	var params = {
		dataBase: dataBase,
		node: node
	};
	$.get("ctrSignalingAnalysis/getNodeTable", params, function (data) {
		// fileSave(data["fileName"]);
		$("#fileNameBak").html(data["fileName"]);
		q.stop();
		$("#dataBase").val(dataBase);
		query_filter(dataBase);
		// console.log(data);
	});
	// 	return;
	// } else {
	// 	s.stop();
	// 	// alert("");
	// 	layer.open({
	// 		title: "提示",
	// 		content: "请输入过滤条件"
	// 	});
	// 	return;
	// }
}

function fileSave(fileName) {
	if (fileName != "") {
		layer.open({
			title: "提示",
			content: fileName,
			yes:function(index, layero){
				var fileNames = csvZipDownload(fileName);
				//alert("success");
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



function query() {
	var q = Ladda.create(document.getElementById("queryBtn"));
	q.start();

	dataBase = $("#dataBase").val();
	ztreeNode = $("#ztreeNode").val();
	eventName = $("#eventName").val();

	// bakZtreeNode = $("#bakZtreeNode").html(ztreeNode);
	if(ztreeNode != "" || eventName != "") { 
		select(q);
		// q.stop();
		return;
	}

	// if (dataBase.indexOf("my_result") >= 0) {
	// 	query_filter(dataBase);
	// 	q.stop();
	// 	return;
	// }
	
	var url = "ctrSignalingAnalysis/getChartData";
	// if(eventName != "") {
	// 	url = "ctrSignalingAnalysis/getChartData_eventName";
	// }
	var params = {
		"dataBase": dataBase,
		eventName : eventName
	};

	$("#signalingChart").grid("destroy", true, true);
	/*$.get(url,params,function(data){
	 data = eval("("+data+")");
	 var len = 0;
	 for (var i = data["records"].length - 1; i >= 0; i--) {
	 var length = data["records"][i]["Event_NAME"].length;
	 len = (len>length)?len:length;
	 }
	 maxLen = len*13;*/
	var fieldArr = [];
	var textArr = ["Event_Time", "Event_NAME", "EVENT_PARAM_RAC_UE_REF", "EVENT_PARAM_ENBS1APID", "EVENT_PARAM_MMES1APID", "EVENT_PARAM_GLOBAL_CELL_ID", "EVENT_PARAM_GUMMEI"];
	for (var i in textArr) {
		if (textArr[i] == "Event_NAME") {
			fieldArr[fieldArr.length] = {field: textArr[fieldArr.length], title: textArr[fieldArr.length], width: 450};
		} else {
			fieldArr[fieldArr.length] = {
				field: textArr[fieldArr.length],
				title: textArr[fieldArr.length],
				width: textWidth(textArr[fieldArr.length])
			};  //textWidth(text[fieldArr.length])
		}
	}
	var grid = $("#signalingChart").grid({
		columns: fieldArr,
		dataSource: {
			url: url,
			data: params,
			success: function (data) {
				q.stop();
				data = eval("(" + data + ")");

				if (data == false) {
					$("#signalingChart").grid("destroy", true, true);
					// alert("");
					layer.open({
						title: "提示",
						content: "数据不存在，请重新选择！"
					});
					return;
				}
				grid.render(data);
				$("#queryBtn").removeAttr("disabled");
			}
		},
		pager: {limit: 10, sizes: [10, 20, 50, 100]},
		autoScroll: true,
		uiLibrary: "bootstrap"
	});

	grid.on("rowSelect", function (e, $row, id, record) {
		//var id = _id.$id;
		var data = {
			"id": record._id.$id,
			"dataBase": $("#dataBase").val()
		};
		$.ajax({
			type: "get",
			url: "ctrSignalingAnalysis/showMessage",
			data: data,
			async: false,
			success: function (returnData) {
				returnData = JSON.parse(returnData);
				var setting = {
					view: {
						showIcon: false
					},
					data: {
						simpleData: {
							enable: true
						}
					},
					callback: {
						onClick: zTreeOnClick
					}
				};
				$.fn.zTree.init($("#detailMessageTree"), setting, returnData.tree);
				var treeObj = $.fn.zTree.getZTreeObj("detailMessageTree");
				treeObj.expandAll(true);
				$("#detailMessage").modal();
			}
		});
	});
	/*});*/
}

function textWidth(text) {
	var length = text.length;//alert(length);
	if (length > 15) {
		return length * 13;
	}
	return 150;
}

function zTreeOnClick(event, treeId, treeNode) {
	//alert(treeNode.tId + ", " + treeNode.name);
	$("#ztreeNode").val(treeNode.name);
	if (treeNode.name.indexOf(":") < 0) {
		//q.stop();
		$("#ztreeNode").val("");
		// alert("");
		layer.open({
			title: "提示",
			content: "此项不可点击，请重新选择！"
		});
		return;
	}
	/*var dataBase = $("#dataBase").val();
	 var node = $("#ztreeNode").val();

	 $("#signalingChart").grid("destroy", true, true);
	 var params = {
	 dataBase : dataBase,
	 node : node//,
	 //table : $("#inputTable").val()
	 };
	 $.get("ctrSignalingAnalysis/showNodeTable", params, function(data){
	 q.stop();
	 //getDataBase(); //刷新数据库
	 //dataBase = $("#inputTable").val();
	 //$("#inputTable").val("");  //清空表input
	 $("#dataBase").val(dataBase);
	 query_filter(dataBase);
	 console.log(data);
	 })*/
	//}
};

function query_filter(dataBase) {

	var url = "ctrSignalingAnalysis/getChartData_filter";
	var params = {
		"dataBase": dataBase
	};
	var fieldArr = [];
	var textArr = ["Event_Time", "Event_NAME", "EVENT_PARAM_RAC_UE_REF", "EVENT_PARAM_ENBS1APID", "EVENT_PARAM_MMES1APID", "EVENT_PARAM_GLOBAL_CELL_ID", "EVENT_PARAM_GUMMEI"];
	for (var i in textArr) {
		if (textArr[i] == "Event_NAME") {
			fieldArr[fieldArr.length] = {field: textArr[fieldArr.length], title: textArr[fieldArr.length], width: 450};
		} else {
			fieldArr[fieldArr.length] = {
				field: textArr[fieldArr.length],
				title: textArr[fieldArr.length],
				width: textWidth(textArr[fieldArr.length])
			};  //textWidth(text[fieldArr.length])
		}
	}
	$("#signalingChart").grid("destroy", true, true);
	var grid = $("#signalingChart").grid({
		columns: fieldArr,
		dataSource: {
			url: url,
			data: params,
			success: function (data) {
				data = eval("(" + data + ")");
				//console.log(data);
				if (data == false) {
					$("#signalingChart").grid("destroy", true, true);
					// alert("");
					layer.open({
						title: "提示",
						content: "数据不存在，请重新选择！"
					});
					return;
				}
				grid.render(data);
				$("#queryBtn").removeAttr("disabled");
			}
		},
		pager: {limit: 10, sizes: [10, 20, 50, 100]},
		autoScroll: true,
		uiLibrary: "bootstrap"
	});

	grid.on("rowSelect", function (e, $row, id, record) {
		//var id = _id.$id;
		var data = {
			"id": record._id.$id//,
			//"dataBase":$("#inputTable").val()
		};
		$.ajax({
			type: "get",
			url: "ctrSignalingAnalysis/showMessage_filter",
			data: data,
			async: false,
			success: function (returnData) {
				returnData = JSON.parse(returnData);
				var setting = {
					view: {
						showIcon: false
					},
					data: {
						simpleData: {
							enable: true
						}
					}/*,
					 callback: {
					 onClick: zTreeOnClick
					 }*/
				};
				$.fn.zTree.init($("#detailMessageTree"), setting, returnData.tree);
				var treeObj = $.fn.zTree.getZTreeObj("detailMessageTree");
				treeObj.expandAll(true);
				$("#detailMessage").modal();
			}
		});
	});
}


function switchTab(div1, div2, div3) {
	$(div2).removeClass("active");
	$(div1).addClass("active");
	if (div3 == "chart") {
		$("#title").html("信令流程");
	} else if (div3 == "table") {
		$("#title").html("信令图");
	}
}