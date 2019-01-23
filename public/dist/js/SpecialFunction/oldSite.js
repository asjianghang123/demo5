$(document).ready(function () {
    toogle("oldSite");
    $("#city").multiselect({
        dropRight: true,
        buttonWidth: "100%",
        //enableFiltering: true,
        nonSelectedText: "请选择城市",
        //filterPlaceholder:"搜索",
        nSelectedText: "项被选中",
        includeSelectAllOption: true,
        selectAllText: "全选/取消全选",
        allSelectedText: "已选中所有平台类型",
        maxHeight: 200,
        width: 200
    });
    url = "oldSite/getCitys";
    $.ajax({
        type: "post",
        url: url,
        dataType: "json",
        success: function (data) {
            var newOptions = [];
            var obj = {};
            $(data).each(function (k, v) {
                v = eval("(" + v + ")");
                obj = {
                    label: v.text,
                    value: v.value
                };
                newOptions.push(obj);
            });
            $("#city").multiselect("dataprovider", newOptions);
        }
    });
    //--end of city init--
    setTime();
});
function setTime() {
    $("#startTime").datepicker({format: "yyyy-mm-dd"});  //返回日期
    $("#endTime").datepicker({format: "yyyy-mm-dd"});

    var nowTemp = new Date();
    $("#startTime").datepicker("setValue", nowTemp);
    $("#endTime").datepicker("setValue", nowTemp);
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var checkin = $("#startTime").datepicker({
        onRender: function (date) {
            return date.valueOf() < now.valueOf() ? "" : "";
        }
    }).on("changeDate", function (ev) {
        if (ev.date.valueOf() > checkout.date.valueOf()) {
            var newDate = new Date(ev.date);
            newDate.setDate(newDate.getDate() + 1);
            checkout.setValue(newDate);
        }
        checkin.hide();
        $("#endTime")[0].focus();
    }).data("datepicker");
    var checkout = $("#endTime").datepicker({
        onRender: function (date) {
            //return date.valueOf() <= checkin.date.valueOf() ? "disabled" : "";
            return date.valueOf() <= checkin.date.valueOf() ? "" : "";
        }
    }).on("changeDate", function (ev) {
        checkout.hide();
    }).data("datepicker");

}
function textWidth(text) {
    var length = text.length;
    if (length > 15) {
        return length * 15;
    }
    return 200;
}

function oldSiteSearch(){
    var citys = $("#city").val();
    var eNodeBId = $("#eNodeBId").val();
    var startTime = $("#startTime").val();
    var endTime = $("#endTime").val();
    var l = Ladda.create(document.getElementById("search"));
    l.start();
    var params = {
        citys:citys,
        eNodeBId:eNodeBId,
        startTime:startTime,
        endTime:endTime
    };
    var fieldArr = [];
        $.post("oldSite/getTableField", params, function (data) {
            if (data.result == "error") {
                l.stop();
                $("#oldSiteTable").bootstrapTable("destroy");
                layer.open({
                    title: "提示",
                    content: "没有记录"
                });
                return;
            } else {
                fieldArr[fieldArr.length] = {checkbox:true};
                for (var k in data) {
                   if (k != '推送时间') {
                        fieldArr[fieldArr.length] = {field: k, title: k,align: "left"};
                    }
                }
                $("#oldSiteTable").bootstrapTable("destroy");
                $("#oldSiteTable").bootstrapTable({
                    url:"oldSite/getOldSite",
                    method: "post",
                    striped: true,
                    dataType: "json",
                    pagination: true,
                    pageList: [10, 20, 50, 100],
                    pageSize: 10,
                    pageNum: 1,
                    sidePagination: "server",//设置为服务器端分页
                    queryParams: function queryParams(p) { //设置查询参数 
                        params.page = p.pageNumber;
                        params.limit = p.pageSize;
                        params.citys = citys;
                        return params;
                    },
                    queryParamsType: null,
                    cache: false,
                    showColumns: false,
                    showToggle: false,                    //是否显示详细视图和列表视图的切换按钮
                    cardView: false,
                    detailView: true,
                    detailFormatter: function detailFormatter(index, row) {
                        var html = [];
                        $.each(row, function (key, value) {
                            html.push("<p><b>" + key + ":</b> " + value + "</p>");
                        });
                        return html.join("");
                    },
                    columns: fieldArr,
                    onLoadSuccess: function () { //加载成功时执行 
                        l.stop();
                    },
                    onLoadError: function () { //加载失败时执行 
                        layer.open({
                            title: "提示",
                            content: "查询失败，请重试"
                        });
                        l.stop();
                    }
                });
            }
        });

}
function exportAllSearch(){
    var citys = $("#city").val();
    var eNodeBId = $("#eNodeBId").val();
    var startTime = $("#startTime").val();
    var endTime = $("#endTime").val();
    var params = {
        citys:citys,
        eNodeBId:eNodeBId,
        startTime:startTime,
        endTime:endTime
    };
    var E = Ladda.create(document.getElementById("export"));
    //E.start();
    $.post("oldSite/exportAllSearch", params, function (data) {
        E.stop();
        if (data.result) {
            fileDownload(data.fileName);
        } else {
            layer.open({
                title: "提示",
                content: "下载失败"
            });
        }
    });
}
function oldSiteExport() {
    var citys = $("#city").val();
    var eNodeBId = $("#eNodeBId").val();
    var startTime = $("#startTime").val();
    var endTime = $("#endTime").val();
    var params = {
        citys:citys,
        eNodeBId:eNodeBId,
        startTime:startTime,
        endTime:endTime
    };
    var E = Ladda.create(document.getElementById("oldSiteExport"));
    E.start();
    $.post("oldSite/exportOldSite", params, function (data) {
        E.stop();
        if (data.result) {
            fileDownload(data.fileName);
        } else {
            layer.open({
                title: "提示",
                content: "下载失败"
            });
        }
    });
}

function oldSiteImport() {
    $("#importBtn").attr("onclick","importFile()");
    $("#fileImport").attr("accept",".csv");
    $("#import_modal").modal();
    $("#fileImportName").val("");
    $("#fileImport").val("");
}
function importFile() {
    var citys = $("#city").val();
    var params = {citys:citys}
    var E = Ladda.create(document.getElementById("importBtn"));
    E.start();
    $.ajaxFileUpload({
        url: "oldSite/uploadFile",
        data: params,
        fileElementId: "fileImport",
        secureuri: true,
        dataType: "json",
        type: "post",
        success: function (data, status) {
            params.fileName = data;
            $.post("oldSite/getFileContent", params, function (data) {
                E.stop();
                $("#import_modal").modal("hide");
                oldSiteSearch();
                if (data == 'lt1') {
                    layer.open({
                        title: "提示",
                        content: "文件中没有数据",
                    });
                    return;
                }else if (data == 'gt50') {
                    layer.open({
                        title: "提示",
                        content: "每次上传记录不能超过50条",
                    });
                    return;
                };
                layer.open({
                    title: "提示",
                    content: "上传成功",
                });
                $.post("oldSite/runTask", data, function (data) {
                    oldSiteSearch();
                    layer.open({
                        title: "提示",
                        content: "kget解析入库成功",
                    });
                    
                });
            });

        },
        error: function (data, status, e) {
            layer.open({
                title: "提示",
                content: "上传失败"
            });
        }
    });
}
function toName(self) {
    $("#fileImportName").val(self.value);
}