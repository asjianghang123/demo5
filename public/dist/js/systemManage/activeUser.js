$(function(){
	toogle("activeUser");
	setTime();
});
function setTime(){
	$("#startDate").datepicker({format: "yyyy-mm-dd"}); 
	$("#endDate").datepicker({format: "yyyy-mm-dd"}); //返回日期
	var nowTemp = new Date();
	$("#startDate").datepicker("setValue", nowTemp);
	$("#endDate").datepicker("setValue", nowTemp);
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
	var checkin = $("#startDate").datepicker().on("changeDate", function(ev) {
		checkin.hide();
	}).data("datepicker");
	var checkout = $("#endDate").datepicker().on("changeDate", function(ev) {
		checkout.hide();
	}).data("datepicker");
}
function query(){
	var startDate = $("#startDate").val();
	var endDate = $("#endDate").val();
	if (startDate > endDate) {
		//alert("结束日期不能早于起始日期");
		layer.open({
			title: "提示",
			content: "结束日期不能早于起始日期"
		});
		return;
	}
	var params = {
		startDate : startDate,
		endDate : endDate
	};
	var queryBtn = Ladda.create( document.getElementById( "queryBtn" ) );
	queryBtn.start();	
	$.post("activeUser/getAccessData",params,function(data){
		data = JSON.parse(data);
		setChart(data);
		queryBtn.stop();	
	});
}
function getCategories(){
	var startDate = $("#startDate").val();
	var endDate = $("#endDate").val();
	var categories = [];
	var temp = startDate;
	while(temp<=endDate){
		categories.push(temp);
		var time = new Date(temp).getTime();
		time = new Date(time + 24*60*60*1000);
		temp = formatTime(time);
	}
	return categories;
}
function formatTime(time){
	var year,month,day;
	year = time.getFullYear();
	month = time.getMonth() + 1;
	month = month < 10 ? "0" + month : month;
	day = time.getDate();
	day = day < 10 ? "0" + day : day;
	return year + "-" + month + "-" + day;
}
function setChart(data){
	var dates = data.dates;
	var nums = data.nums;
	var categories = getCategories();
	var series = [];
	for(var i in categories){
		var num = 0;
		var index = dates.indexOf(categories[i]);
		if(index > -1){
			num = nums[index];
		}
		series.push(num);
	}
	$("#userChart").highcharts({
		chart: {
			type: "column"
		},
		title: {
			text: "每天的活跃用户数"
		},
		xAxis: {
			categories: categories,
			crosshair: true
		},
		yAxis: {
			min: 0,
			title: {
				text: "人数"
			}
		},
		tooltip: {
			shared: true,
			useHTML: true
		},
		plotOptions: {
			column: {
				pointPadding: 0.2,
				borderWidth: 0,
				dataLabels:{
					enabled:true // dataLabels设为true
				}
			}
		},
		series: [{
			name:"用户",
			data:series
		}]
	});
}