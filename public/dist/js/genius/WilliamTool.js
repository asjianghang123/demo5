var kgetDate = "#kgetDate";
$(document).ready(function () {
	toogle("WilliamTool");
	//setTree();
	getTasks();
});

function getTasks() {
	$(kgetDate).select2();
	var url = "WilliamTool/getTasks";
	$.ajax({
		type: "post",
		url: url,
		dataType: "json",
		success: function (data) {
			console.log(data)
			var parameterAnalysisDateSelect = $(kgetDate).select2({
				height: 50,
				placeholder: "请选择日期",
				//allowClear: true,
				data: data
			});
			var task = getCurrentDate("kget");
			$(kgetDate).val(getCurrentDate("kget")).trigger("change");
			if ($(kgetDate).val() == null) {
				$(kgetDate).val(getYesterdayDate("kget")).trigger("change");
			}
		}
	});
}
function getParams(){
	var params = {
		kget:$("#kgetDate").val()
	};
	if(!params.kget){
		layer.open({
			title: "提示",
			content: "没有kget信息"
		});
		return false;
	}
	return params;
}
function queryCarrierData()
{
	var params = getParams();
	var E = Ladda.create(document.getElementById("queryCarrier"));
	E.start();

	$.post("WilliamTool/getCarrierData",params,function(data){

		loadCarrier(JSON.parse(data));
		E.stop();
	})
}
//导出Carrier数据
function importCarrierData(){

	var params = getParams();
	var E = Ladda.create(document.getElementById("importCarrier"));
	E.start();
	$.post("WilliamTool/importCarrierData",params,function(data){
		fileDownload(JSON.parse(data).filename);
		E.stop();
	})
}
function queryNeighborData()
{
	var params = getParams();
	var E = Ladda.create(document.getElementById("queryNeighbor"));
	E.start();
	$.post("WilliamTool/getNeighborData",params,function(data){

		loadNeighbor(JSON.parse(data));
		E.stop();
	})
}
var importCarrier='';
var importNeighbor='';


function loadCarrier(data){

		var fieldArr = [];
		var text = data.text.split(",");
		for (var i in data.rows[0]) {
			if (text[fieldArr.length].length < 10) {
				fieldArr[fieldArr.length] = {field: i, title: text[fieldArr.length], width: 150};
			}else {
				fieldArr[fieldArr.length] = {field: i, title: text[fieldArr.length], width: 200};
			}

		}
		var newData = data.rows;
		$("#CarrierTable").grid("destroy", true, true);
		$("#CarrierTable").grid({
			columns: fieldArr,
			dataSource: newData,
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap",
			primaryKey: "id"
		});


}
function loadNeighbor(data){

		var fieldArr = [];
		var text = data.text.split(",");
		for (var i in data.rows[0]) {

		fieldArr[fieldArr.length] = {field: i, title: text[fieldArr.length], width: 150};

		}
		var newData = data.rows;
		$("#NeighborTable").grid("destroy", true, true);
		$("#NeighborTable").grid({
			columns: fieldArr,
			dataSource: newData,
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap",
			primaryKey: "id"
		});


}

//导出Neighbor数据
function importNeighborData(){

	layer.confirm("大概需要二十分钟左右。确认运行吗？", {title: "提示"}, function (index) {
		var params = getParams();
		var E = Ladda.create(document.getElementById("importNeighbor"));
		E.start();
		$.post("WilliamTool/importNeighborData",params,function(data){

			fileDownload(JSON.parse(data).filename);
			E.stop();
		})
		layer.close(index);

	});

}

//获得kget日期
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