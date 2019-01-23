$(function () {
	layui.use(["layer", "form"], function () {
		var layer = layui.layer,
			form = layui.form();
	});
});
function userRegister() {
	var name = $("#name").val();
	var email = $("#email").val();
	var password = $("#password").val();
	if (!name) {
		layer.open({
			title: "提示",
			content: "昵称不能为空"
		});
		return;
	}
	if (!email) {
		layer.open({
			title: "提示",
			content: "邮箱不能为空"
		});
		return;
	}
	if (!password) {
		layer.open({
			title: "提示",
			content: "密码不能为空"
		});
		return;
	}
	var params = {
		name: name,
		email: email,
		password: password
	};
	var registerBtn = Ladda.create(document.getElementById("registerBtn"));
	registerBtn.start();
	$.post("userRegister/userRegister", params, function (data) {
		registerBtn.stop();
		data = JSON.parse(data);
		if (data.error == "email") {
			layer.open({
				title: "提示",
				content: "已有该邮箱申请的账号"
			});
		} else if (data.result == "success") {
			layer.open({
				title: "提示",
				content: "注册完成，请等待管理员审核"
			});
		}
	});

}
