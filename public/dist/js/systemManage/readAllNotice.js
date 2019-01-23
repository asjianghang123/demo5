$(function () {
	getAllNotice();
});
function getAllNotice() {
	$.get("readAllNotice/getAllNotice", null, function (data) {
		data = JSON.parse(data);
		for (var i in data) {
			var html = '<li class="list-group-item">' +
				'<label class="checkbox-inline">' +
				data[i].publisher + ' 发布了通知：' + data[i].title +
				'</label>' +
				'<div class="pull-right">' +
				'<span style="color:grey">' + data[i].publishTime + '</span>' +
				'</div>' +
				'<p style="padding-left:20px;">' +
				data[i].content +
				'</p>';
			'</li>';
			$("#noticeList").append(html);
		}
	});
}