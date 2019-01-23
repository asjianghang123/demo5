$(function () {
	getDoc();
	getVideo();
});
function getDoc() {
	$.get("downloadCourse/getDoc", null, function (data) {
		data = JSON.parse(data);
		for (var i in data) {
			var html = '<li class="list-group-item">' +
				'<label class="checkbox-inline">' +
				'<input class="docCheck" type="checkbox" value="' + data[i].value + '">' + data[i].label +
				'</label>' +
				'<div class="pull-right">' +
				'<span>' + data[i].size + '</span>' +
				'</div>' +
				'</li>';
			$("#downloadDoc").append(html);
		}
	});
}
function downloadDoc() {
	var docs = [];
	var dir = "common/Doc/guide/";
	$("#downloadDoc .docCheck:checked").each(function () {
		download(dir + $(this).val());
	});
}
function getVideo() {
	$.get("downloadCourse/getVideo", null, function (data) {
		data = JSON.parse(data);
		for (var i in data) {
			var html = '<li class="list-group-item">' +
				'<label class="checkbox-inline">' +
				'<input class="videoCheck" type="checkbox" value="' + data[i].value + '">' + data[i].label +
				'</label>' +
				'<div class="pull-right">' +
				'<span>' + data[i].size + '</span>' +
				'</div>' +
				'</li>';
			$("#downloadVideo").append(html);
		}
	});
}
function downloadVideo() {
	var videos = [];
	var dir = "common/Doc/video/";
	$("#downloadVideo .videoCheck:checked").each(function () {
		download(dir + $(this).val());
	});

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