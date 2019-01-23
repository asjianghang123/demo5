var parameterAnalysisDateId = "#parameterAnalysisDate";
var parameterAnalysisCityId = "#parameterAnalysisCity";
var paramQueryMoTreeId = "#paramQueryMoTree";
var featureListId = "#featureList";
var paramQueryMoTreeData = "";
var allTableFields;
var selectedTableFields;
$(document).ready(function () {
    toogle("KgetG2");
    //--start of parameterAnalysisDate init--
    $(parameterAnalysisDateId).select2();
    var url = "KgetG2/getParamTasks";
    $.ajax({
        type: "post",
        url: url,
        dataType: "json",
        success: function (data) {
            var parameterAnalysisDateSelect = $(parameterAnalysisDateId).select2({
                height: 50,
                placeholder: "请选择日期",
                //allowClear: true,
                data: data
            });
            //var value = $(parameterAnalysisDateId).val();
            var task = getCurrentDate("kget");
            $(parameterAnalysisDateId).val(getCurrentDate("kget")).trigger("change");
            if ($(parameterAnalysisDateId).val() == null) {
                $(parameterAnalysisDateId).val(getYesterdayDate("kget")).trigger("change");
            }
        }
    });
    //--end of parameterAnalysisDate init--
    //--end of parameterAnalysisDate init--
    //数据库获取对应subNwork
    $("#parameterAnalysisCity").change(function () {
        getAllSubNetwork();
    });
    //获取所有被选择的城市
    function getChooseCitys() {
        var citys = $("#parameterAnalysisCity").val();
        return citys;
    }
    //获取所有被选择的子网
    function getChoosesubNet() {
        var subNet = $("#subNetworks").val();
        return subNet;
    }

    function getAllSubNetwork() {
        var citys = getChooseCitys();
        //var format = $("#LTEFormat").val();
        var params = {
            //format: format,
            citys: citys
        };

        $.post("KgetG2/getAllSubNetwork", params, function (data) {
            var newOptions = [];
            var obj = {};
            $(data).each(function (k, v) {
                v = eval("(" + v + ")");
                obj = {
                    label: v.text,
                    value: v.value,
                    selected: true
                };
                newOptions.push(obj);
            });
            $("#subNetworks").multiselect("dataprovider", newOptions);
        });
    }
    $(parameterAnalysisCityId).multiselect({
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
        width: 220
    });
    $("#subNetworks").multiselect({
        buttonWidth: "100%",
        enableFiltering: true,
        nonSelectedText: "请选择子网",
        filterPlaceholder: "搜索",
        nSelectedText: "项被选中",
        includeSelectAllOption: true,
        selectAllText: "全选/取消全选",
        allSelectedText: "已选中所有子网",
        maxHeight: 200,
        maxWidth: "100%"
    });
    url = "KgetG2/getParamCitys";
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
            obj = {
                label: "未知",
                value: "unknow"
            };
            newOptions.push(obj);
            $(parameterAnalysisCityId).multiselect("dataprovider", newOptions);
        }
    });

    //--end of parameterAnalysisCity init--

    //---------start of paramTree---------

    $.get("common/json/parameterTreeData_mongodb.json", null, function (data) {
        if (typeof (data) == "object") {
            paramQueryMoTreeData = data;
        } else {
            paramQueryMoTreeData = eval("(" + data + ")");
        }
        var options = {
            bootstrap2: false,
            showTags: true,
            levels: 2,
            data: paramQueryMoTreeData,
            onNodeSelected: function (event, data) {
                if (data.text == "OptionalFeatureLicense") {
                    $("#OptionalFeatureLicenseId").css("display", "block");
                } else {
                    $("#OptionalFeatureLicenseId").css("display", "none");
                }
                getTableField(data);
                paramQuerySearch();
            }
        };

        $("#paramQueryMoTree").treeview(options);
    });
    initTableField();

    //---------end of paramTree---------
});
//--start of FeatureList init--
function initFeatureList() {
    var task = $(parameterAnalysisDateId).val();
    if (task) {
        $(featureListId).multiselect({
            dropRight: true,
            buttonWidth: "100%",
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            nonSelectedText: "请选择Feature",
            filterPlaceholder: "搜索",
            nSelectedText: "项被选中",
            includeSelectAllOption: true,
            selectAllText: "全选/取消全选",
            allSelectedText: "已选中所有平台类型",
            maxHeight: 200,
            width: 220
        });
        url = "KgetG2/getFeatureList";
        $.ajax({
            type: "post",
            url: url,
            data: { db: task },
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
                obj = {
                    label: "未知",
                    value: "unknow"
                };
                newOptions.push(obj);
                $(featureListId).multiselect("dataprovider", newOptions);
            }
        });
    }
}
//-----start of moTreeView--------
//根据关键字搜索树
// document.onkeydown=function(){
//            	if (event.keyCode == 13){
//               	search("paramQueryMoTree","paramQueryMoErbs");
//            	}
//         }
var moData = [];
function search(treeId, erbId) {
    moData = [];
    searchParamMoTree(treeId, erbId);
}
function clearSearch(treeId, erbId) {
    clearParamMoTree(treeId, erbId);
}
function searchParamMoTree(treeId, erbId) {
    var pattern = $("#" + erbId).val();
    pattern = eval("/" + pattern + "/ig");
    var searchedTables = [];
    for (var i in allTableFields) {
        if (pattern.test(allTableFields[i].name)) {
            searchedTables.push({ table: allTableFields[i].table, text: allTableFields[i].name });
            continue;
        }
        var fields = allTableFields[i].fields;
        if (pattern.test(fields.join(","))) {
            searchedTables.push({ table: allTableFields[i].table, text: allTableFields[i].name });
            continue;
        }
    }
    var options = {
        bootstrap2: false,
        showTags: true,
        levels: 2,
        data: searchedTables,
        onNodeSelected: function (event, data) {
            if (data.text == "OptionalFeatureLicense") {
                $("#OptionalFeatureLicenseId").css("display", "block");
            } else {
                $("#OptionalFeatureLicenseId").css("display", "none");
            }
            getTableField(data);
            paramQuerySearch();
        }
    };
    $("#" + treeId).treeview(options);

}
//清空搜索历史
function clearParamMoTree(treeId, erbId) {
    $("#" + treeId).treeview("clearSearch");
    var options = {
        bootstrap2: false,
        showTags: true,
        levels: 2,
        data: paramQueryMoTreeData,
        onNodeSelected: function (event, data) {
            if (data.text == "OptionalFeatureLicense") {
                $("#OptionalFeatureLicenseId").css("display", "block");
            } else {
                $("#OptionalFeatureLicenseId").css("display", "none");
            }
            getTableField(data);
            paramQuerySearch();
        }
    };

    $("#" + treeId).treeview(options);
    $("#" + erbId).val("");
}
//-----end of moTreeView--------
//--------start of tableSearch-----
function paramQuerySearch() {
    var params = getParam("paramQuery");
    parameterSearch(params);
}
function paramQueryExport() {
    var params = getParam("paramQuery");
    parameterExport(params);
}
function getParam(action) {
    if (action == "paramQuery") {
        var task = $(parameterAnalysisDateId).val();
        var moSelected = $(paramQueryMoTreeId).treeview("getSelected");
        if (moSelected == "") {
            // alert("");
            layer.open({
                title: "提示",
                content: "Please choose parameter tree first!"
            });
            return false;
        }
        // var mo = moSelected[0].text;
        var mo = getFullTableName(moSelected[0]);
        var citys = $(parameterAnalysisCityId).val();
        var subNet = $(subNetworks).val();
        var erbs = $("#paramQueryErbs").val();
        var featureList = $(featureListId).val();
        if (task != null) {
            var params = {
                db: task,
                table: mo,
                erbs: erbs,
                citys: citys,
                subNet: subNet,
                featureList: featureList
            };
            return params;
        } else {
            // alert("");
            layer.open({
                title: "提示",
                content: "Please choose database first!"
            });
            return false;
        }
    }
}
function getFullTableName(moSelected) {
    if (moSelected.table != undefined) {
        return moSelected.table;
    }
    var parentId = moSelected.parentId;
    var moText = moSelected.text;
    if (parentId != undefined) {
        return getFullTableName($(paramQueryMoTreeId).treeview("getNode", parentId)) + "_" + moText;
    } else {
        return moText;
    }
}
var table = null;
function parameterSearch(params) {
    var l = Ladda.create(document.getElementById("search"));
    var E = Ladda.create(document.getElementById("export"));
    l.start();
    E.start();
    if (params == false) {
        l.stop();
        E.stop();
        return false;
    }
    $("#paramQueryTable").bootstrapTable("destroy");
    $("#paramQueryTable").bootstrapTable({
        url: "KgetG2/getParamItems_mongodb",
        method: "post",
        striped: true,
        dataType: "json",
        pagination: true,
        pageList: [10, 20, 50, 100],
        pageSize: 10,
        pageNum: 1,
        sidePagination: "server",//设置为服务器端分页
        queryParams: function queryParams(p) { //设置查询参数 
            params.pageNumber = p.pageNumber;
            params.pageSize = p.pageSize;
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
        columns: [{
            field: "_id",
            title: "主键",
            visible: false
        }, {
            field: "MeContext",
            title: "基站",
            align: "left"
        }, {
            field: "SubNetwork",
            title: "子网",
            align: "left"
        }, {
            field: "mo",
            title: "mo",
            align: "left"
        // }, {
        //     field: "recordTime",
        //     title: "recordTime",
        //     isDate: true,
        //     dateType: 3
        }],
        onLoadSuccess: function () { //加载成功时执行 
            l.stop();
            E.stop();
        },
        onLoadError: function () { //加载失败时执行 
            layer.open({
                title: "提示",
                content: "查询失败，请重试"
            });
            l.stop();
            E.stop();
        }
    });
}
function textWidth(text) {
    var length = text.length;
    if (length > 15) {
        return length * 15;
    }
    return 200;
}
function parameterExport(params) {
    var l = Ladda.create(document.getElementById("search"));
    var E = Ladda.create(document.getElementById("export"));
    l.start();
    E.start();
    params.fields = selectedTableFields;
    var ajaxTimeoutTest = $.ajax({
        url: "KgetG2/exportParamFile_mongodb",  //请求的URL
        timeout: 600000, //超时时间设置，单位毫秒
        type: "post",  //请求方式，get或post
        data: params,  //请求所传参数，json格式
        dataType: "json",//返回的数据格式
        success: function (data) { //请求成功的回调函数
            l.stop();
            E.stop();
            fileDownload(data.fileName);
            // if(data.result){
            //     fileDownload(data.fileName);
            // }else{
            //     layer.open({
            //         title: "提示",
            //         content: "There is error occured!"
            //     });
            // }
        },
        complete: function (XMLHttpRequest, status) { //请求完成后最终执行参数
            if (status == "timeout") {//超时,status还有success,error等值的情况
                layer.confirm("请求超时,出现未知情况，请联系开发人员！", { title: "提示" }, function (index) {
                    layer.close(index);
                }, function (index) {
                    l.stop();
                    E.stop();
                    ajaxTimeoutTest.abort();
                    layer.close(index);
                });
            }
        }
    });
}
//--------end of tableSearch-------
//-------------------------------common-----------------------------------
function getYesterdayDate(taskType) {
    var mydate = new Date();
    var yesterday_miliseconds = mydate.getTime() - 1000 * 60 * 60 * 24;
    var Yesterday = new Date();
    Yesterday.setTime(yesterday_miliseconds);

    var yesterday_year = Yesterday.getYear().toString().substring(1.3);
    var month_temp = Yesterday.getMonth() + 1;
    var yesterday_month = month_temp > 9 ? month_temp.toString() : "0" + month_temp.toString();
    var d = Yesterday.getDate();
    var Day = d > 9 ? d.toString() : "0" + d.toString();
    var kgetDate = taskType + yesterday_year + yesterday_month + Day;
    return kgetDate;
}

function getCurrentDate(taskType) {
    var mydate = new Date();
    var myyear = mydate.getYear();
    var myyearStr = (myyear + "").substring(1);
    var mymonth = mydate.getMonth() + 1; //值范围0-11
    mydate = mydate.getDate();  //值范围1-31
    var mymonthStr = "";
    var mydateStr = "";
    mymonthStr = mymonth >= 10 ? mymonth : "0" + mymonth;
    mydateStr = mydate >= 10 ? mydate : "0" + mydate;
    var kgetDate = taskType + myyearStr + mymonthStr + mydateStr;
    return kgetDate;
}
/* 预先加载好table的字段json */
function initTableField() {
    $.get("common/json/parameterTreeField_mongodb.json", null, function (data) {
        data = JSON.parse(data);
        allTableFields = data;
        return;
    });
}

/* 点击table获取其字段 */
function getTableField(selectedNode) {
    var tableName;
    if (selectedNode.table == undefined) {
        tableName = getFullTableName(selectedNode);
    } else {
        tableName = selectedNode.table;
    }
    for (var i in allTableFields) {
        if (allTableFields[i].table == tableName) {
            selectedTableFields = allTableFields[i].fields;
            return;
        }
    }
}