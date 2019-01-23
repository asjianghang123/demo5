//For all the post method
(function() {
    var op = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function() {
        var resp = op.apply(this, arguments);
        this.setRequestHeader('X-CSRF-Token', $('meta[name=csrf-token]').attr('content'));
        return resp;
    };
}());

$(function () {
	//For all the post method
	/*$.ajaxSetup({
		headers: {"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")}
	});*/

	// $.get("nav/getUser", null, function (data) {
	// 	if (data == "login") {
	// 		//window.location.href = "login";
	// 		//alert("尚未登录");
	// 		layer.open({
	// 			title: "提示",
	// 			content: "尚未登录",
	// 			yes: function (index, layero) {
	// 				window.location.href = "login";
	// 				layer.close(index);
	// 			},
	// 			cancel: function (index, layero) {
	// 				window.location.href = "login";
	// 				layer.close(index);
	// 			}
	// 		});
	// 	} else {
	// 		// data = eval("(" + data + ")");
	// 		// $("#user_user").html(data.user);
	// 		// $("#user_type").html(data.type);
	// 		// $("#user_email").html(data.email);
	// 		//如果不是管理员，系统管理中的权限管理不显示
	// 		// if (data.type !== "admin") {
	// 			// $("#sys_container").css("width", "200px");
	// 			// $("#sys_container .equal-height-in").removeClass("col-md-4").addClass("col-md-12");
	// 			// $("#sys_container").parents("ul.dropdown-menu").removeAttr("style");

	// 			// $("#user_container").css("width", "200px");
	// 			// $("#user_container .equal-height-in").removeClass("col-md-6").addClass("col-md-12");
	// 			// $(".adminOnly").hide();
	// 			// getMenuList();
	// 		// }
	// 		// $(".features").hide();
	// 		// if (data.type == "admin") {
	// 		// 	$(".features").show();
	// 		// } else if (data.province == "江苏" && data.operator == "移动") {
	// 		// 	$(".features").show();
	// 		// }
	// 		initNotice();
	// 	}
	// });
	initNotice();
	//统计每小时登录情况
	updateOlineNumber();
	layui.use(["layer", "form"], function () {
		var layer = layui.layer,
			form = layui.form();
	});
});

//设置中英文切换
function setLocaleLang(type) {
	$.ajax({
		type: "get",
		url: "nav/localeLang",
		data: {"lang":type},
		// async: true,
		success: function (returnData) {
			location.reload();
		}
	});
}

function signout() {
	$.get("nav/signout", null, function (data) {
		if (data == "success") {
			window.location.href = "login";
		}
	});
}
function updateOlineNumber() {
	var date = new Date();
	var year = date.getFullYear();
	var mon = date.getMonth() + 1;
	var day = date.getDate();
	var hour = date.getHours();
	var min = date.getMinutes();
	var sec = date.getSeconds();
	var hrefArr = window.location.href.split("/");
	var isGenius = hrefArr[3];
	var href = hrefArr[5].split("#")[0].split("?")[0];
	//if (href == "home") {
	//    href = "network";
	//}
	var params = {
		year: year,
		mon: mon,
		day: day,
		hour: hour,
		isGenius: isGenius,
		href: href
	};
	$.ajax({
		type: "get",
		url: "nav/getSessions",
		data: params,
		async: true,
		success: function (returnData) {
		}
	});
}
function addNotice() {
	initUserGroup();
	$("#add_notice").modal();
}
function initUserGroup() {
	$("#userGroup_notice").multiselect({
		//dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "选择用户组",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选全部用户组",
		maxHeight: 200,
		maxWidth: "100%"
	});
	$.get("nav/getUserGroup", null, function (data) {
		data = JSON.parse(data);
		$("#userGroup_notice").multiselect("dataprovider", data);
	});
}
function updateNotice() {
	var id = $("#noticeId").val();
	var title = $("#noticeTitle").val().trim();
	var content = $("#noticeContent").val().trim();
	var userGroup = $("#userGroup_notice").val();
	if (!title) {
		//alert("标题不能为空！");
		layer.open({
			title: "提示",
			content: "标题不能为空！"
		});
		return;
	}
	if (!content) {
		//alert("内容不能为空！");
		layer.open({
			title: "提示",
			content: "内容不能为空！"
		});
		return;
	}
	if (!userGroup) {
		//alert("内容不能为空！");
		layer.open({
			title: "提示",
			content: "请选择用户组！"
		});
		return;
	}
	var params = {
		id: id,
		title: title,
		content: content,
		userGroup: userGroup.join(",")
	};
	var url = "nav/addNotice";
	$.post(url, params, function (data) {
		if (data == "1") {
			//alert("添加通知成功");
			layer.open({
				title: "提示",
				content: "添加通知成功"
			});
			$("#add_notice").modal("hide");
			$("#noticeTitle").val("");
			$("#noticeContent").val("");
			$("#noticeId").val("");
			initNotice();
		}
	});
}
function initNotice() {
	var url = "nav/getNotice";
	$.get(url, null, function (data) {
		data = eval("(" + data + ")");
		var html = "";
		var ids = [];
		for (var i in data) {
			html += "<li><a id='" + data[i].id + "' data-time='" + data[i].publishTime + "' data-publisher='" + data[i].publisher + "' data-content='" + data[i].content + "' onclick='readNotice(this)'>" + data[i].title + "</a></li>";
			ids.push(data[i].id);
		}

		$("#noticeUl").empty().append(html);
		if (data.length) {
			$("#noticeNumber").html(data.length);
		} else {
			$("#noticeNumber").html("");
		}

		$("#noticeIds").val(ids.join(","));
	});
	if ($("#noticeTable").length) {
		doQueryNotice();
	}
}

function readNotice(notice) {
	$("#read_notice").modal();
	$("#noticeId_read").val($(notice).attr("id"));
	$("#noticeTitle_read").html($(notice).html());
	$("#noticePublisher").html($(notice).attr("data-publisher"));
	$("#noticePublishTime").html($(notice).attr("data-time"));
	$("#noticeContent_read").html($(notice).attr("data-content"));

}
function setNoticeReaded() {
	var id = $("#noticeId_read").val();
	var url = "nav/readNotice";
	$.get(url, {id: id}, function (data) {
		$("#read_notice").modal("hide");
		initNotice();
	});
}
function readAll() {
	var ids = $("#noticeIds").val();
	if (ids) {
		var url = "nav/readAllNotice";
		$.post(url, {ids: ids}, function (data) {
			initNotice();
			$("#noticeIds").val("");
		});
	}
}
function setPDOSearchTime(type=null) {
	if (type == "lteQuery" || type == "gsmQuery" || type == "LTEQueryHW") {
		return 60000;    //设置LTE查询时间60S
	} else if (type == null) {
		return 20000;    //设置默认查询时间20S
	}
}

function csvZipDownload(fileName) {
	var zipFile = "";
	var params = {
		fileName: fileName
	};
	$.ajax({
		url: "csvZipDownload",
		async: false,
		type: "GET",
		data: params,
		success: function (data) {
			zipFile = data;
		}
	});
	return zipFile;
}


function getOption() {  //离线地图option
	var dataArr = mapDataInitialization();
	var option = {
		tooltip: {
			//show: false //不显示提示标签
			formatter: "{b}", //提示标签格式
			backgroundColor: "#ff7f50",//提示标签背景颜色
			textStyle: {color: "#fff"} //提示标签字体颜色
		},
		series: [{
			type: "map",
			mapType: dataArr[0],
			label: {
				normal: {
					show: true,//显示地市标签
					textStyle: {color: "#389BB7", fontSize: 18}//省份标签字体颜色
				},
				emphasis: {//对应的鼠标悬浮效果
					show: true,
					textStyle: {color: "#3c8dbc"}
				}
			},
			itemStyle: {
				normal: {
					borderWidth: .5,//区域边框宽度
					borderColor: "#3c8dbc",//区域边框颜色
					areaColor: "#ffffff",//区域颜色
				},
				emphasis: {
					borderWidth: .5,
					borderColor: "#4b0082",
					areaColor: "#3c8dbc",
				}
			},
			data: []
		}],
	};
	return option;
}


function setMapPoint() {   //地图经纬度适配
	var dataArr = mapDataInitialization();
	var longitude = dataArr[2];
	var latitude = dataArr[3];
	var minZoom = dataArr[4];
	var maxZoom = dataArr[5];
	var arr = [];
	arr.push(longitude, latitude);
	return arr;
}

function mapDataInitialization() {
	//地图初始化
	var dataArr;
	$.ajax({
		url: "nav/getOption",
		async: false,
		success: function (data) {
			dataArr = data;
		}
	});
	return dataArr;
}
//统一解决城市是多选框时，加载日期选择城市的问题
function getFirstCity() {
	return "南通";
}

function fileDownload(fileName) {
	if (fileName != "") {
		var fileNames = csvZipDownload(fileName);
		download(fileNames);
	}
	else {
		//alert("No file generated so far!");
		layer.open({
			title: "提示",
			content: "下载失败!"
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
function getBrowerInfo() {
	var uerAgent = navigator.userAgent.toLowerCase();
	var format = /(msie|firefox|chrome|opera|version).*?([\d.]+)/;
	var matches = uerAgent.match(format);
	return matches[1].replace(/version/, "'safari");
}
function getMenuList() {
	$.get("nav/getMenuList", null, function (data) {
		data = JSON.parse(data);
		for (var i in data) {
			$("a[href='" + data[i].menu + "']").parent("li").hide();
		}
	});
}