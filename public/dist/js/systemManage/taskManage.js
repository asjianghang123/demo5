/*$(document).ready(function() {
	toogle("taskManage");
	doQueryTask();
});*/

//下载任务-定时任务查询
function doQueryTask(){
	var e = Ladda.create(document.getElementById("edit"));
	e.start();
	if (selectedRow) {
		$("#edit_email").modal();

		$("#taskFileContent").attr("disabled",true);
		$("#taskFileContent").val("");
		var data ={
			//path:"common/txt/root.txt",
			path:"/data/cronTemp/root.txt",
			remoteIp:selectedRow.ipAddress
		};
		$.post("storeManage/openTaskFile", data, function(data){
			var content = $("#taskFileContent").val(data);
		},"html");
	} else {
		//alert("请选择要修改的数据");
		layer.open({
			title: "提示",
			content: "请选择需要编辑的记录"
		});
	}
	e.stop();
}
//编辑文件
function editTaskFile(){
	$("#taskFileContent").attr("disabled",false);
}
function cancelEdit(){
	var c = Ladda.create(document.getElementById("cancelBtn"));
	c.start();
	doQueryTask();
	c.stop();
}
function saveTaskFile(){
	if (selectedRow) {
		var s = Ladda.create(document.getElementById("saveTaskFile"));
		s.start();
		var path = "/data/cronTemp/root.txt";
		var content = $("#taskFileContent").val();
		var params = {
			path:path,
			content:content,
			remoteIp:selectedRow.ipAddress
		};
		$.post("storeManage/saveTaskFile",params,function(data){
			s.stop();
			doQueryTask();
			layer.open({
				title: "提示",
				content: "保存成功"
			});
		});
	} else {
		//alert("请选择要修改的数据");
		layer.open({
			title: "提示",
			content: "请选择需要编辑的记录"
		});
	}
}
