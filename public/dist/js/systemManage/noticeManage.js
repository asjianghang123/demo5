$(document).ready(function () {
	toogle("noticeManage");
	// 加载用户表
	doQueryNotice();
});

function doQueryNotice() {

	$.get("noticeManage/getNotice", "", function (data) {
		var fieldArr = [];
		var text = (JSON.parse(data).text).split(",");
		for (var i in JSON.parse(data).rows[0]) {
			if (fieldArr.length == 0) {
				fieldArr[fieldArr.length] = {field: text[fieldArr.length], title: text[fieldArr.length], width: 50};
			} else if (text[fieldArr.length] == "content") {
				fieldArr[fieldArr.length] = {field: text[fieldArr.length], title: text[fieldArr.length], width: 450};
			} else {
				fieldArr[fieldArr.length] = {field: text[fieldArr.length], title: text[fieldArr.length], width: 150};
			}

		}
		var newData = JSON.parse(data).rows;
		$("#noticeTable").grid("destroy", true, true);
		$("#noticeTable").grid({
			columns: fieldArr,
			dataSource: newData,
			pager: {limit: 10, sizes: [10, 20, 50, 100]},
			autoScroll: true,
			uiLibrary: "bootstrap",
			primaryKey: "id"
		});
	});
}
function deleteNotice_man() {
	var data = getSelected();
	if (!data) {
		//alert("请选择需要删除的通知。");
		layer.open({
			title: "提示",
			content: "请选择需要删除的通知"
		});
		return;
	}
	/*var flag = confirm("确认删除id为"+data.id+"的通知吗？");
	 if(flag){
	 $.get("noticeManage/deleteNotice",{"id":data.id},function(res){
	 if(res){
	 alert("删除成功。");
	 doQueryNotice();
	 initNotice();
	 }
	 });
	 }*/
	layer.confirm("确认删除id为" + data.id + "的通知吗？", {title: "提示"}, function (index) {
		$.get("noticeManage/deleteNotice", {"id": data.id}, function (res) {
			if (res) {
				layer.open({
					title: "提示",
					content: "删除成功"
				});
				doQueryNotice();
				initNotice();
			}
		});
		layer.close(index);
	});
}
function addNotice_man() {
	initUserGroup();
	$("#add_notice").modal();
}
function editNotice_man() {
	var data = getSelected();
	if (!data) {
		//alert("请选择需要修改的通知。");
		layer.open({
			title: "提示",
			content: "请选择需要修改的通知"
		});
		return;
	}
	$("#add_notice").modal();

	$("#noticeId").val(data.id);
	$("#noticeTitle").val(data.title);
	$("#noticeContent").val(data.content);
	initUserGroupById(data.id);
}

function getSelected() {
	var id = $("#noticeTable").grid("getSelected");
	var data = $("#noticeTable").grid("getById", id);
	return data;

}
function initUserGroupById(id) {
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
	$.post("noticeManage/getUserGroupById", {id: id}, function (data) {
		data = JSON.parse(data);
		$("#userGroup_notice").multiselect("dataprovider", data);
	});
}