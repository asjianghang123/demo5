$(document).ready(function() {
    toogle("userManage");
    // 加载用户表
    setTree();
    // doQueryUser();
    initValidata();
})
var selectedRow;

function setTree() {
    var options = [];

    $.get("userManage/treeQuery", null, function(data) {
        for (var i in type) {
            options = {
                bootstrap2: false,
                showTags: true,
                levels: 2,
                data: data,
                onNodeSelected: function(event, data) {
                    $("#userTypeValue").val(data.value);
                    doQueryUser(data.value);
                    if (data.value == "unaudited") {
                        $("#editUser span").text("审核");
                    } else {
                        $("#editUser span").text("修改");
                    }
                }
            };
            $("#userTypeTree").treeview(options);
        }
    });
}

function doQueryUser(type) {
    var params = {
        type: type
    };
    var fieldArr = [];
    var text = "user,name,pwd,type,email,province,operator";
    var textArr = text.split(",");
    for (var i in textArr) {
        fieldArr[fieldArr.length] = { field: textArr[fieldArr.length], title: textArr[fieldArr.length], width: 150 };
    }
    $("#userTable").grid("destroy", true, true);
    var grid = $("#userTable").grid({
        columns: fieldArr,
        dataSource: {
            url: "userManage/templateQuery",
            success: function(data) {
                data = JSON.parse(data);
                grid.render(data);
            }
        },
        params: params,
        pager: { limit: 10, sizes: [10, 20, 50, 100] },
        autoScroll: true,
        uiLibrary: "bootstrap",
        // primaryKey : "id",
        // autoLoad: true
    });
    grid.on("rowSelect", function(e, $row, id, record) {
        selectedRow = record;
    });
    // params = {
    // 	type : type
    // }
    //  $.get("userManage/templateQuery", params, function(data){
    // var fieldArr=new Array();
    // var text=(JSON.parse(data).text).split(",");
    // for(var i in JSON.parse(data).rows[0]){
    // 	if(fieldArr.length == 0){
    // 		fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:150};
    // 	}else{
    // 		fieldArr[fieldArr.length]={field:i,title:text[fieldArr.length],width:150};
    // 	}

    // }
    // fieldArr[fieldArr.length-1].hidden = true;
    // 	var newData = JSON.parse(data).rows;
    // 	$("#userTable").grid("destroy", true, true);
    // 	var grid = $("#userTable").grid({
    // 	  	columns:fieldArr,
    // 	  	dataSource:newData,
    // 	  	pager: { limit: 10, sizes: [10, 20, 50, 100] },
    // 	  	autoScroll:true,
    // 	  	uiLibrary: "bootstrap",
    // 	  	primaryKey : "id"
    // 	});
    // 	grid.on("rowSelect", function (e, $row, id, record) {
    //        	selectedRow = record;
    // 	});
    //  });
}

function deleteUser() {
    if (selectedRow) {
        layer.confirm("确认删除吗？", { title: "提示" }, function(index) {
            $.get("userManage/deleteUser", { "id": selectedRow.id }, function(res) {
                if (res) {
                    layer.open({
                        title: "提示",
                        content: "删除成功"
                    });
                    doQueryUser(selectedRow.type);
                } else {
                    layer.open({
                        title: "提示",
                        content: "禁止删除admin类型的用户"
                    });
                }
            });
            layer.close(index);
        });

    } else {
        //alert("请选择要删除的数据");
        layer.open({
            title: "提示",
            content: "请选择要删除的数据"
        });
    }
}

function addUser() {
    $("#add_edit_user").modal();
    $("form input").val("");
    initTypes();
    $("#saveBtn").html("新增");

    $("#userForm").data("bootstrapValidator").destroy();
    initValidata();
}

function initTypes(selectType) {
    $("#type").multiselect({
        buttonWidth: "100%",
        nonSelectedText: "选择类型",
        nSelectedText: "项被选中",
        includeSelectAllOption: true,
        maxHeight: 200,
        maxWidth: "100%"
    });
    var url = "userManage/getType";
    var obj = {};
    var newOptions = [];
    $.get(url, null, function(data) {
        var sdata = [];
        for (var key in data) {
            obj = {
                label: key,
                value: data[key]
            };
            if (selectType == data[key]) {
                obj.selected = true;
            }
            newOptions.push(obj);
        }
        $("#type").multiselect("dataprovider", newOptions);
    });
}

function updateUser() {

    $("#userForm").data("bootstrapValidator").validate();
    var flag = $("#userForm").data("bootstrapValidator").isValid();
    if (!flag) {
        return;
    }

    var params = $("#userForm").serialize().split("&");
    var data = {};
    for (var i = 0; i < params.length; i++) {
        data[params[i].split("=")[0]] = decodeURIComponent(params[i].split("=")[1], true);
    }
    if ($("#editUser span").text() == "审核") {
        data.unaudited = true;
    }
    $.get("userManage/updateUser", data, function(res) {
        if (res == 1) {
            $("form input").val("");
            $("form select").val("");
            //alert("保存成功");
            layer.open({
                title: "提示",
                content: "保存成功"
            });
            doQueryUser(data.type);
        } else {
            //alert("保存失败，请重试");
            layer.open({
                title: "提示",
                content: "保存失败，请重试"
            });
        }
        $("#add_edit_user").modal("hide");
        // $("#add_edit_user").modal("hide");
        // doQueryUser(data.type);
    });

}

function editUser() {
    if (selectedRow) {
        $("#add_edit_user").modal();
        $("form input").val("");
        initTypes(selectedRow.type);
        $("#saveBtn").html("更新");

        $("#userId").val(selectedRow.id);
        $("#userName").val(selectedRow.user);
        $("#name").val(selectedRow.name);
        $("#password").val(selectedRow.pwd);
        // $("#type").val(selectedRow.type);
        $("#email").val(selectedRow.email);
        $("#province").val(selectedRow.province);
        $("#operator").val(selectedRow.operator);

        $("#userName").removeAttr("readonly");
        $("#name").removeAttr("readonly");
        $("#password").removeAttr("readonly");
        $("#email").removeAttr("readonly");
        if (!selectedRow.user) {
            $("#userName").val(selectedRow.email);
            $("#userName").attr("readonly", "readonly");
            $("#name").attr("readonly", "readonly");
            $("#password").attr("readonly", "readonly");
            $("#email").attr("readonly", "readonly");
            $("#saveBtn").html("确认");
        }

        $("#userForm").data("bootstrapValidator").destroy();
        initValidata();
    } else {
        //alert("请选择要修改的数据");
        layer.open({
            title: "提示",
            content: "请选择要修改的数据"
        });
    }
}

function initValidata() {
    $("#userForm").bootstrapValidator({
        message: "This value is not valid",
        feedbackIcons: {
            valid: "glyphicon glyphicon-ok",
            invalid: "glyphicon glyphicon-remove",
            validating: "glyphicon glyphicon-refresh"
        },
        fields: {
            userName: {
                //message: "用户名验证失败",
                validators: {
                    notEmpty: {
                        message: "用户名不能为空"
                    }
                }
            },
            password: {
                //message: "密码验证失败",
                validators: {
                    notEmpty: {
                        message: "密码不能为空"
                    },
                    regexp: {
                        regexp: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,16}$|^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)[a-zA-Z\d]{6,16}$|^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[a-zA-Z\d]{6,16}$|^(?=.*[a-z])(?=.*\d)(?=.*[A-Z])[a-zA-Z\d]{6,16}$|^(?=.*[A-Z])(?=.*\d)(?=.*[a-z])[a-zA-Z\d]{6,16}$|^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])[a-zA-Z\d]{6,16}$/,
                        message: '密码必须是字母和数字的组合，至少包含1个大写字母，长度6-16'
                    }
                }
            },
            name: {
                validators: {
                    notEmpty: {
                        message: "昵称不能为空"
                    }
                }
            },
            type: {
                //message: "用户类型验证失败",
                validators: {
                    notEmpty: {
                        message: "用户类型不能为空"
                    }
                }
            },
            email: {
                validators: {
                    notEmpty: {
                        message: "邮箱地址不能为空"
                    },
                    emailAddress: {
                        message: "邮箱地址格式有误"
                    }

                }
            }
        }
    });
}

function getSelected() {
    var id = $("#userTable").find("tr.active").children("td").eq(0).children("div").html();
    var data = $("#userTable").grid("getById", id);
    return data;

}

function addUserType() {
    $("#add_user_type").modal();

    $("#add_user_type input").val("");
}

function updateUserType() {

    var userType = $("#userType").val();
    params = {
        userType: userType
    }

    $.post("userManage/updateUserType", params, function(res) {
        if (res == 1) {
            $("#userType").val("");
            //alert("保存成功");
            layer.open({
                title: "提示",
                content: "保存成功"
            });
            setTree();
            // $("#EmailQueryTree").treeview("collapseAll", { silent: true });
        } else {
            //alert("保存失败，请重试");
            layer.open({
                title: "提示",
                content: "保存失败，请重试"
            });
        }
        $("#add_user_type").modal("hide");
    });
}

function deleteUserType() {
    // var id = $("#EmailQueryTree").treeview("getSelected")[0].id;
    // var scope = $("#EmailQueryTree").treeview("getSelected")[0].scope;
    var text = $("#userTypeTree .node-selected").text();

    if (!text) {
        //alert("尚未选择要删除的数据");
        layer.open({
            title: "提示",
            content: "尚未选择要删除的数据"
        });
        return;
    }
    layer.confirm("确认删除吗？", { title: "提示" }, function(index) {
        params = {
            type: text
        }
        $.get("userManage/deleteUserType", params, function(res) {

            if (res == "1") {
                layer.open({
                    title: "提示",
                    content: "删除成功！"
                });
                setTree();
                doQueryUser(text);
            } else {
                layer.open({
                    title: "提示",
                    content: "删除失败！"
                });
            }
        });
        layer.close(index);
    });
}

function modifyPermission() {
    var type = $("#userTypeTree .node-selected").text();
    if (!type || type == "全部类型") {
        layer.open({
            title: "提示",
            content: "请选择需要修改菜单权限的用户类型"
        });
        return;
    }
    if (type == "admin") {
        layer.open({
            title: "提示",
            content: "admin用户不用配置权限"
        });
        return;
    }
    setMenuTree(type);
}

function setMenuTree(type) {
    $.get("userManage/getMenuList", { type: type }, function(data) {
        data = JSON.parse(data);
        var options = {
            bootstrap2: false,
            showTags: true,
            levels: 3,
            showCheckbox: true,
            data: [data],
            onNodeChecked: function(event, data) {
                checkAllChildren(data);
                checkParent(data);
            },
            onNodeUnchecked: function(event, data) {
                unCheckAllChildren(data);
                unCheckParent(data);
            }
        };
        $("#menuTree").treeview(options);
        $("#modifyPermission_modal").modal();
    });
}

function checkAllChildren(node) {
    var children = node.nodes;
    if (children) {
        var len = children.length;
        for (var i = 0; i < len; i++) {
            $("#menuTree").treeview("checkNode", [children[i].nodeId, { silent: false }]);
        }
    }
}

function unCheckAllChildren(node) {
    var children = node.nodes;
    if (children) {
        var len = children.length;
        for (var i = 0; i < len; i++) {
            $("#menuTree").treeview("uncheckNode", [children[i].nodeId, { silent: false }]);
        }
    }
}

function unCheckParent(node) {
    var parentId = node.parentId;
    if (parentId != undefined) {
        $("#menuTree").treeview("uncheckNode", [parentId, { silent: true }]);
        var parentNode = $("#menuTree").treeview("getNode", parentId);
        unCheckParent(parentNode);
    }
}

function checkParent(node) {
    var parentId = node.parentId;
    if (parentId != undefined) {
        var parentNode = $("#menuTree").treeview("getNode", parentId);
        var children = parentNode.nodes;
        var len = children.length;
        for (var i = 0; i < len; i++) {
            if (children[i].state.checked == false) {
                return;
            }
        }
        $("#menuTree").treeview("checkNode", [parentId, { silent: true }]);
        checkParent(parentNode);
    }
}

function updatePermission() {
    var treeNodes = $("#menuTree").treeview("getNode", 0);
    var checkedMenus = getCheckedMenus(treeNodes);
    var type = $("#userTypeTree .node-selected").text();
    var params = {
        menus: checkedMenus.join(","),
        type: type
    };
    $.post("userManage/updatePermission", params, function(data) {
        if (data) {
            layer.open({
                title: "提示",
                content: "修改成功"
            });
            $("#modifyPermission_modal").modal("hide");
        } else {
            layer.open({
                title: "提示",
                content: "修改失败，请重试"
            });
        }
    });

}

function getCheckedMenus(treeNodes) {
    var checkedMenus = [];
    var nodes = treeNodes.nodes;
    if (nodes) {
        for (var i in nodes) {
            $.merge(checkedMenus, getCheckedMenus(nodes[i]));
        }
    } else {
        var checked = treeNodes.state.checked;
        if (checked) {
            checkedMenus.push(treeNodes.id);
        }
    }
    return checkedMenus;
}