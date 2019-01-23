var downfilename;
$(document).ready(function () {
	setTime();
	//数据库获取所有城市
	getAllCity();
	getAllHour();
	toogle("AppCoverage");
});
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
	var url = "AppCoverage/getAllCity";
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
function getAllHour() {
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;
	$("#allHour").multiselect({
		dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "请选择小时",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有平台类型",
		maxHeight: 200,
		maxWidth: "100%"
	});
	var params = {
		date: today,
		city: $("#allCity").val()
	}
	var url = "AppCoverage/getAllHour";
	$.ajax({
		type: "GET",
		url: url,
		data: params,
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
			$("#allHour").multiselect("dataprovider", newOptions);
		}
	});
	$("#allCity").change(function () {
		var params = {
			date: today,
			city: $("#allCity").val()
		}
		var url = "AppCoverage/getAllHour";
		$.ajax({
			type: "GET",
			url: url,
			data: $("#allCity").val(),
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
				$("#allHour").multiselect("dataprovider", newOptions);
			}
		});
	});
}
function setTime() {
	$("#startTime").datepicker({format: "yyyy-mm-dd"});  //返回日期
	$("#endTime").datepicker({format: "yyyy-mm-dd"});  //返回日期
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + "-" + month + "-" + day;
	var city = $("#allCity").val();
	var params = {
		city: city
	};
	$.get("AppCoverage/getAllDate", params, function (data) {
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
	$("#allCity").change(function () {
		var city = $("#allCity").val();
		var params = {
			city: city
		};
		$.get("AppCoverage/getAllDate", params, function (data) {
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
	});

	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#dateTime").datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? "" : "";
		}
	}).on("changeDate", function (ev) {
		checkin.hide();
	}).data("datepicker");
}
function templateQuery(table){
	var l = Ladda.create( document.getElementById( "search" ) );
	var E = Ladda.create( document.getElementById( "export" ) );
	l.start();
	E.start();
	var startTime = $("#startTime").val();
	var endTime = $("#endTime").val();
	var city = $("#allCity").val();
	var hour = $("#allHour").val();
	var cell = $("#cellname").val();
	if (city == null) {
		//alert("请选择城市!");
		layer.open({
			title: "提示",
			content: "请选择城市"
		});
		return false;
	}

	if (startTime == null || endTime == null) {
		layer.open({
			title: "提示",
			content: "请选择时间"
		});
		return false;
	}
	var params = {
		city: city,
		startTime: startTime,
		endTime: endTime,
		hour: hour,
		cell: cell
	}
	if(params == false){
		l.stop();
		E.stop();
		return false;
	}
	$.get("AppCoverage/templateQuery", params, function(data){
		console.log(data);
		if (data.rows == "") {
			l.stop();
			E.stop();
			layer.open({
				title: "提示",
				content: "没有数据",
			});
			return false;
		}
		var fieldArr=[];
		var text=(data.content).split(",");
		downfilename = data;

		for(var i in text){  
			fieldArr[i]={field:text[i],title:text[i],width:150,sortable:true};
		}
		var newData = data.rows;
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
	});
}
function downfile() {
	var l = Ladda.create( document.getElementById( "search" ) );
	var E = Ladda.create( document.getElementById( "export" ) );
	l.start();
	E.start();
	fileZipSave(downfilename.filename);
	l.stop();
	E.stop();
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
function getBrowerInfo() {
	var uerAgent = navigator.userAgent.toLowerCase();
	var format = /(msie|firefox|chrome|opera|version).*?([\d.]+)/;
	var matches = uerAgent.match(format);
	return matches[1].replace(/version/, "'safari");
}