$(function(){
	toogle("LteTopCell");
	checkType();
	setHour();
	initCitys();
	// setDate();
});

function checkType(){
	$("#timeType").on("change", function () {
		var timeType = $(this).val();
		if (timeType == "day"||timeType == "daygroup") {
			$("#hour").multiselect("disable");
			$("#quarterSelect").multiselect("disable");
		} else if(timeType == "hour" || timeType == "hourgroup") {
			$("#hour").multiselect("enable");
			$("#quarterSelect").multiselect("disable");
		}else{
			$("#hour").multiselect("enable");
			$("#quarterSelect").multiselect("enable");
		}
	});
}
function setHour() {
	$('#hour').multiselect({
		//dropRight: true,
		buttonWidth: '100%',
		//enableFiltering: true,
		nonSelectedText: '选择小时',
		//filterPlaceholder:'搜索',
		nSelectedText: '项被选中',
		includeSelectAllOption: true,
		selectAllText: '全选/取消全选',
		allSelectedText: '已选全天',
		maxHeight: 200,
		maxWidth: '100%'
	});
	$('#quarterSelect').multiselect({
		dropRight: true,
		buttonWidth: '100%',
		//enableFiltering: true,
		nonSelectedText: '请选择15分钟',
		//filterPlaceholder:'搜索',
		nSelectedText: '项被选中',
		includeSelectAllOption: true,
		selectAllText: '全选/取消全选',
		allSelectedText: '已选中所有',
		maxHeight: 200,
		maxWidth: '100%'
	});
	var hours = [];
	for (var i = 0; i < 24; i++) {
		// if (i < 10) {
		// 	hours.push({"label": "0" + i, "value": "0" + i});
		// } else {
		// 	hours.push({"label": i, "value": i});
		// }
		hours.push({"label": i, "value": i});
	}
	$('#hour').multiselect('dataprovider', hours);
	$("#hour").multiselect("disable");
	$("#quarterSelect").multiselect("disable");
}
function initCitys() {
	$('#city').multiselect({
		//dropRight: true,
		buttonWidth: '100%',
		//enableFiltering: true,
		nonSelectedText: '选择城市',
		//filterPlaceholder:'搜索',
		nSelectedText: '项被选中',
		includeSelectAllOption: true,
		selectAllText: '全选/取消全选',
		//allSelectedText:'已选中所有城市',
		maxHeight: 200,
		maxWidth: '100%'
	});
	$.get("LteTopCell/getCities", null, function (data) {
		var newData = [];
		for (var i in data) {
			data = JSON.parse(data[i]);
			newData.push({"label": data.text, "value": data.value});
		}
		$('#city').multiselect('dataprovider', newData);
		// console.log($("#city").val());
		setDate();
	});
}
function setDate() {
	$("#startTime").datepicker({format: 'yyyy-mm-dd'});  //返回日期
	$("#endTime").datepicker({format: 'yyyy-mm-dd'});  //返回日期
	var nowTemp = new Date();
	var year = nowTemp.getFullYear();
	var month = nowTemp.getMonth() + 1;
	var day = nowTemp.getDate();
	var today = year + '-' + month + '-' + day;
	$("#startTime").datepicker('setValue', today);
	$("#endTime").datepicker('setValue', today);
	var params = {
		dataBase: $('#city').val()
	};
	$.post('LteTopCell/getCityDate', params, function (data) {
		data = JSON.parse(data);
		var sdata = [];
		for (var i = 0; i < data.length; i++) {
			if (data[i] === today) {
				continue;
			}
			sdata.push(data[i]);
		}
		sdata.push(today);
		$("#startTime").datepicker('setValues', sdata);
		$("#endTime").datepicker('setValues', sdata);

	});
	$("#city").change(function () {
		var city = $("#city").val();
		var params = {
			dataBase: city
		};
		$.post('LteTopCell/getCityDate', params, function (data) {
			data = JSON.parse(data);
			var sdata = [];
			for (var i = 0; i < data.length; i++) {
				if (data[i] === today) {
					continue;
				}
				sdata.push(data[i]);
			}
			sdata.push(today);
			$("#startTime").datepicker('setValues', sdata);
			$("#endTime").datepicker('setValues', sdata);
		});
	});
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $('#startTime').datepicker({
		onRender: function (date) {
			return date.valueOf() < now.valueOf() ? '' : '';
		}
	}).on('changeDate', function (ev) {
		checkin.hide();
	}).data('datepicker');

	var checkout = $('#endTime').datepicker({
		onRender: function (date) {
			//return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
			return date.valueOf() <= checkin.date.valueOf() ? '' : '';
		}
	}).on('changeDate', function (ev) {
		checkout.hide();
	}).data('datepicker');



}

function getParam()
{

	if(!$("#city").val()){
		layer.open({
			title :"提示",
			content :"请选择城市"
		})
		return null;
	}
	if(!$("#startTime").val()||!$("#endTime").val()){
		layer.open({
			title :"提示",
			content :"请确认起始时间"
		})
		return null;
	}
	if($("#startTime").val()>$("#endTime").val()){
		layer.open({
			title :"提示",
			content :"开始时间不能大于结束时间"
		})
		return null;
	}
	var params = {
		survey 	 : $("#survey").val(),
		city   	 : $("#city").val(),
		timeType : $("#timeType").val(),
		startTime: $("#startTime").val(),
		endTime  : $("#endTime").val(),
		hour    : $("#hour").val(),
		quarter : $("#quarterSelect").val()
	}

	return params;
}
var filename='';
function query(){

	var data = getParam();
	if(!data){
		return;
	}
	var E = Ladda.create(document.getElementById('queryBtn'));
	var L = Ladda.create(document.getElementById('exportBtn'));
	E.start();
	L.start();
	$.post("LteTopCell/getAllData",data,function(data){


		var data = JSON.parse(data);
		var fieldArr = [];

		var text = data.text.split(",");
		for (var i in data.rows[0]) {
			if (text[fieldArr.length] =="小区名") {
				fieldArr[fieldArr.length] = {field: i, title: text[fieldArr.length], width: 250};
			}else if(text[fieldArr.length].length>10){
				fieldArr[fieldArr.length] = {field: i, title: text[fieldArr.length], width: 160};
			}else{
				fieldArr[fieldArr.length] = {field: i, title: text[fieldArr.length], width: 120};

			}

		}
		var newData = data.rows;
		$("#AllDataTable").grid("destroy", true, true);
		$("#AllDataTable").grid({
			columns: fieldArr,
			dataSource: newData,
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap",
			primaryKey: "id"
		});

		E.stop();
	})

	$.post("LteTopCell/downloadFile",data,function(data){

		filename = JSON.parse(data);
		console.log(filename);
		L.stop();
	})


}
function exportFile(){
		console.log(filename);
	
	if(filename){
		fileDownload(filename);
	}else{
		layer.open({
			title:"提示",
			content:"无数据"
		})
	}
}