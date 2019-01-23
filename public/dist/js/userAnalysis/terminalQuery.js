$(function () {
	toogle("terminalQuery");
	initCitys();
});
function initCitys() {
	$("#citys").multiselect({
		//dropRight: true,
		buttonWidth: "100%",
		//enableFiltering: true,
		nonSelectedText: "选择城市",
		//filterPlaceholder:"搜索",
		nSelectedText: "项被选中",
		includeSelectAllOption: true,
		selectAllText: "全选/取消全选",
		allSelectedText: "已选中所有城市",
		maxHeight: 200,
		maxWidth: "100%"
	});
	$.get("terminalQuery/getCitys", null, function (data) {
		data = JSON.parse(data);
		var newData = [];
		for (var i in data) {
			var CHCity = data[i].split("-")[0];
			var dataBase = data[i].split("-")[1];
			newData.push({"label": CHCity, "value": dataBase});
		}
		$("#citys").multiselect("dataprovider", newData);
	});

}
function query() {
	var city = $("#citys").val();
	var user_query = $("#user_query").val();
	if (!user_query) {
		//alert("请输入imsi或者msisdn进行查询");
		layer.open({
			title: "提示",
			content: "请输入imsi或者msisdn进行查询"
		});
		return;
	}
	var queryBtn = Ladda.create(document.getElementById("queryBtn"));
	queryBtn.start();
	var params = {
		city: city
	};
	$.get("terminalQuery/getUserInfoHead", params, function (data) {
		var fieldArr = [];
		var text = (JSON.parse(data).text).split(",");
		for (var i in text) {
			fieldArr[fieldArr.length] = {
				field: text[fieldArr.length],
				title: text[fieldArr.length],
				width: textWidth(text[fieldArr.length])
			};
		}

		$("#userTable").grid("destroy", true, true);
		var grid = $("#userTable").grid({
			columns: fieldArr,
			dataSource: {
				url: "terminalQuery/getUserInfoData",
				success: function (data) {
					data = JSON.parse(data);
					grid.render(data);
					queryBtn.stop();
				}
			},
			params: {"user": user_query, "city": city},
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap",
			autoLoad: true
		});
	});
}
function textWidth(text) {
	var length = text.length;
	if (length > 15) {
		return length * 10;
	}
	return 150;
}