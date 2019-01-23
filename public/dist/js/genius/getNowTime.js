$(document).ready(function () {
	getTime();
});

function getTime() {
	$.get("network/getNowTime", function (data) {
		data = eval("(" + data + ")");
		$("#threeKeysInput").val(data[0]);
		$("#volteInput").val(data[1]);
		$("#videoInput").val(data[2]);
	});
}