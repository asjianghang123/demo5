$(document).ready(function() {
    toogle("trailQueryDataManage");
    // 加载用户表
    queryData();
});

function queryData() {
    var fieldArr = [];
    var text = "eventTime,date_id,hour_id,city,imsi,cell,longitudeBD,latitudeBD,longitude,latitude,dir";
    var textArr = text.split(",");
    for (var i in textArr) {
        fieldArr[i] = { field: textArr[i], title: textArr[i], width: 150 };
    }
    $("#trailQueryDataTable").grid("destroy", true, true);
    var grid = $("#trailQueryDataTable").grid({
        columns: fieldArr,
        dataSource: {
            url: "trailQueryDataManage/getData",
            success: function(data) {
                data = JSON.parse(data);
                grid.render(data);
            },
            type: "post"
        },
        params: {},
        pager: { limit: 10, sizes: [10, 20, 50, 100] },
        autoScroll: true,
        uiLibrary: "bootstrap",
        primaryKey: "id"
            // autoLoad: true
    });
}

//文件导出
function exportAlarmManage() {
    $.get("trailQueryDataManage/downloadFile", function(data) {

        data = eval("(" + data + ")");
        if (data.result == "true") {
            var filepath = data.filename.replace("\\", "");
            download(filepath, "", "data:text/csv;charset=utf-8");
        } else {
            //alert("There is error occured!");
            layer.open({
                title: "提示",
                content: "下载失败"
            });
        }
    });
}

function importAlarmManage() {
    $("#import_modal").modal();
    $("#fileImportName").val("");
    $("#fileImport").val("");
}

function toName(self) {
    $("#fileImportName").val(self.value);
}

function importFile() {
    var data = {
        "filename": $("#fileImportName").val()
    };
    // console.log(data);

    $.ajaxFileUpload({
        url: "trailQueryDataManage/uploadFile",
        type: "POST",
        fileElementId: "fileImport",
        secureuri: false,
        dataType: "json",
        success: function(data) {
            if (data == "lenError") {
                layer.open({
                    title: "提示",
                    content: "没有告警数据或没有表头"
                });
            } else {
                queryData();
                $("#import_modal").modal("hide");

                layer.open({
                    title: "提示",
                    content: "上传成功"
                });
            }

        },
        error: function(data, status, e) {
            //alert("上传失败");
            layer.open({
                title: "提示",
                content: "上传失败"
            });
        }
    });
}