function refreshUser() {
    $.get("nav/getUser", null, function(data) {
        data = JSON.parse(data);
        $("#userId").val(data.id);
        $("#userName").val(data.user);
        $("#nickname").val(data.name);
        $("#email").val(data.email);
        $("#type").val(data.type);
        $("#province").val(data.province);
        $("#operator").val(data.operator);
    });
}

function saveUser() {
    $("#hiddenSaveBtn").click();
}

// function saveUser1() {
//     $("#userForm").data("bootstrapValidator").validate();
//     var flag = $("#userForm").data("bootstrapValidator").isValid();
//     if (!flag) {
//         return;
//     }
//     var params = {
//         id: $("#userId").val(),
//         name: $("#nickname").val(),
//         email: $("#email").val()
//     };
//     $.post("UserSetting/updateUser", params, function(res) {
//         if (res == 1 || res == 0) {
//             //alert("保存成功");
//             layer.open({
//                 title: "提示",
//                 content: "保存成功"
//             });
//         } else {
//             //alert("保存失败，请重试");
//             layer.open({
//                 title: "提示",
//                 content: "保存失败，请重试"
//             });
//         }
//     });
// }

// function initValidata() {
//     $("#userForm").bootstrapValidator({
//         message: "This value is not valid",
//         feedbackIcons: {
//             valid: "glyphicon glyphicon-ok",
//             invalid: "glyphicon glyphicon-remove",
//             validating: "glyphicon glyphicon-refresh"
//         },
//         fields: {
//             nickname: {
//                 //message: "密码验证失败",
//                 validators: {
//                     notEmpty: {
//                         message: "昵称不能为空"
//                     }
//                 }
//             },
//             email: {
//                 validators: {
//                     notEmpty: {
//                         message: "邮箱地址不能为空"
//                     },
//                     emailAddress: {
//                         message: "邮箱地址格式有误"
//                     }

//                 }
//             }
//         }
//     });
// }

// function initValidata_password() {
//     $("#modifyPasswordForm").bootstrapValidator({
//         message: "This value is not valid",
//         feedbackIcons: {
//             valid: "glyphicon glyphicon-ok",
//             invalid: "glyphicon glyphicon-remove",
//             validating: "glyphicon glyphicon-refresh"
//         },
//         fields: {
//             currentPwd: {
//                 //message: "密码验证失败",
//                 validators: {
//                     notEmpty: {
//                         message: "当前密码不能为空"
//                             /* },
//                              identical: {
//                              field: "password",
//                              message: "密码错误"*/
//                     }
//                 }
//             },
//             newPwd: {
//                 //message: "密码验证失败",
//                 validators: {
//                     notEmpty: {
//                         message: "新密码不能为空"
//                     }
//                 }
//             },
//             newPwd2: {
//                 //message: "密码验证失败",
//                 validators: {
//                     notEmpty: {
//                         message: "确认密码不能为空"
//                     },
//                     identical: {
//                         field: "newPwd",
//                         message: "两次输入密码不一致"
//                     }
//                 }
//             }
//         }
//     });
// }

function modifyPassword() {
    $("#modifyPassword_modal").modal();
}

// function updatePassword() {
//     $("#modifyPasswordForm").data("bootstrapValidator").validate();
//     var flag = $("#modifyPasswordForm").data("bootstrapValidator").isValid();
//     if (!flag) {
//         return;
//     }
//     if ($("#currentPwd").val() != $("#password").val()) {
//         layer.open({
//             title: "提示",
//             content: "当前密码输入错误"
//         });
//         return;
//     }
//     var params = {
//         id: $("#userId").val(),
//         password: $("#newPwd").val()
//     };
//     $.post("UserSetting/updatePassword", params, function(res) {
//         if (res == 1 || res == 0) {
//             //alert("保存成功");
//             layer.open({
//                 title: "提示",
//                 content: "修改成功",
//                 yes: function() {
//                     $("#modifyPassword_modal").modal("hide");
//                     $("input[type='password']").val("");
//                     $("#modifyPasswordForm").data("bootstrapValidator").destroy();
//                     signout();
//                 }
//             });
//         } else {
//             //alert("保存失败，请重试");
//             layer.open({
//                 title: "提示",
//                 content: "修改失败，请重试"
//             });
//         }
//     });
// }