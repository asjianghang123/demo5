var parameterAnalysisDateId = "#parameterAnalysisDate";
var parameterAnalysisCityId = "#parameterAnalysisCity";
var paramQueryMoTreeId = "#paramQueryMoTree";
var featureListId = "#featureList";
var paramQueryMoTreeData = "";

$(document).ready(function() {
    toogle("paramQuery");
    //--start of parameterAnalysisDate init--
    $(parameterAnalysisDateId).select2();
    var url = "paramQuery/getParamTasks";
    $.ajax({
        type: "post",
        url: url,
        dataType: "json",
        success: function(data) {
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
    $("#parameterAnalysisCity").change(function() {
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

        $.post("paramQuery/getAllSubNetwork", params, function(data) {
            var newOptions = [];
            var obj = {};
            $(data).each(function(k, v) {
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
    url = "paramQuery/getParamCitys";
    $.ajax({
        type: "post",
        url: url,
        dataType: "json",
        success: function(data) {
            var newOptions = [];
            var obj = {};
            $(data).each(function(k, v) {
                v = eval("(" + v + ")");
                obj = {
                    label: v.text,
                    value: v.value
                };
                newOptions.push(obj);
            });
            /*obj = {
                label: "未知",
                value: "unknow"
            };
            newOptions.push(obj);*/
            $(parameterAnalysisCityId).multiselect("dataprovider", newOptions);
        }
    });

    //--end of parameterAnalysisCity init--

    //---------start of paramTree---------

    $.get("common/json/parameterTreeData.json", null, function(data) {
        if (typeof(data) == "object") {
            paramQueryMoTreeData = data;
        } else {
            paramQueryMoTreeData = eval("(" + data + ")");
        }
        var options = {
            bootstrap2: false,
            showTags: true,
            levels: 2,
            data: paramQueryMoTreeData,
            onNodeSelected: function(event, data) {
                if (data.text == "OptionalFeatureLicense") {
                    // $("#OptionalFeatureLicenseId").css("display", "block");
                    // $(featureListId).multiselect('enable');
                    $("#Feature_label").show();
                    $("#Feature_div").show();
                } else {
                    // $("#OptionalFeatureLicenseId").css("display", "none");
                    // $(featureListId).multiselect('disable');
                    $("#Feature_label").hide();
                    $("#Feature_div").hide();
                }
                // paramQuerySearch();
                getMoTableFiedls();
            }
        };

        $("#paramQueryMoTree").treeview(options);
    });
    //---------end of paramTree---------
    initMoTableFields();

    $("#Feature_label").hide();
    $("#Feature_div").hide();
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
        var url = "paramQuery/getFeatureList";
        $.ajax({
            type: "post",
            url: url,
            data: { db: task },
            dataType: "json",
            success: function(data) {
                var newOptions = [];
                var obj = {};
                $(data).each(function(k, v) {
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
                // $(featureListId).multiselect("disable");
            }
        });
    }
}
//-----start of moTreeView--------
//根据关键字搜索树
document.onkeydown = function() {
    if (event.keyCode == 13) {
        search("paramQueryMoTree", "paramQueryMoErbs");
    }
}
var moData = [];

function search(treeId, erbId) {
    moData = [];
    searchParamMoTree(treeId, erbId);
}

function clearSearch(treeId, erbId) {
    clearParamMoTree(treeId, erbId);
}

function searchParamMoTree(treeId, erbId) {
    var options = {
        bootstrap2: false,
        showTags: true,
        levels: 2,
        data: paramQueryMoTreeData,
        onNodeSelected: function(event, data) {
            if (data.text == "OptionalFeatureLicense") {
                // $("#OptionalFeatureLicenseId").css("display", "block");
                // $(featureListId).multiselect('enable');
                $("#Feature_label").show();
                $("#Feature_div").show();
            } else {
                // $("#OptionalFeatureLicenseId").css("display", "none");
                // $(featureListId).multiselect('disable');
                $("#Feature_label").hide();
                $("#Feature_div").hide();
            }
            // paramQuerySearch();
            getMoTableFiedls();
        }
    };

    $("#" + treeId).treeview(options);

    var pattern = $("#" + erbId).val();

    $("#" + treeId).on("searchComplete", function(event, data) {
        //alert(data);
        for (var i in data) {
            var obj = {
                id: data[i].id,
                text: data[i].text
            };
            moData.push(obj);
        }

    });
    $("#" + treeId).treeview("search", [pattern, {
        ignoreCase: true, // case insensitive
        exactMatch: false, // like or equals
        revealResults: true, // reveal matching nodes
    }]);
    searchParamData(treeId, erbId);

}

function searchParamData(treeId, erbId) {

    var pattern = $("#paramQueryMoErbs").val();
    var task = $(parameterAnalysisDateId).val();
    var data = {
        task: task,
        pattern: pattern,
        moData: moData
    };
    var moParamData = [];
    var url = "paramQuery/getParamData";
    $.post(url, data, function(data) {
        data = JSON.parse(data);
        for (var i in data) {
            var obj = {
                text: data[i].TABLE_NAME
            };

            moParamData.push(obj);
        }
        var options = {
            bootstrap2: false,
            showTags: true,
            levels: 2,
            data: moParamData,
            onNodeSelected: function(event, data) {
                if (data.text == "OptionalFeatureLicense") {
                    // $("#OptionalFeatureLicenseId").css("display", "block");
                    // $(featureListId).multiselect('enable');
                    $("#Feature_label").show();
                    $("#Feature_div").show();
                } else {
                    // $("#OptionalFeatureLicenseId").css("display", "none");
                    // $(featureListId).multiselect('disable');
                    $("#Feature_label").hide();
                    $("#Feature_div").hide();
                }
                // paramQuerySearch();
                getMoTableFiedls();
            }
        };
        $("#" + treeId).treeview(options);
    });
}
//清空搜索历史
function clearParamMoTree(treeId, erbId) {
    $("#" + treeId).treeview("clearSearch");
    var options = {
        bootstrap2: false,
        showTags: true,
        levels: 2,
        data: paramQueryMoTreeData,
        onNodeSelected: function(event, data) {
            if (data.text == "OptionalFeatureLicense") {
                // $("#OptionalFeatureLicenseId").css("display", "block");
                // $(featureListId).multiselect('enable');
                $("#Feature_label").show();
                $("#Feature_div").show();
            } else {
                // $("#OptionalFeatureLicenseId").css("display", "none");
                // $(featureListId).multiselect('disable');
                $("#Feature_label").hide();
                $("#Feature_div").hide();
            }
            // paramQuerySearch();
            getMoTableFiedls();
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
        var mo = moSelected[0].text;
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
var table = null;

function parameterSearch(params) {
    // var l = Ladda.create(document.getElementById("search"));
    // var E = Ladda.create(document.getElementById("export"));
    // l.start();
    // E.start();
    if (params == false) {
        return false;
    }
    var fieldArr = [];
    // $.post("paramQuery/getParamTableField", params, function(data) {
    //     if (data) {
    //         for (var k in data) {
    //             if (k == "mo") {

    //                 fieldArr[fieldArr.length] = { field: k, title: k, width: 600 };

    //             } else if (k != "id") {

    //                 fieldArr[fieldArr.length] = { field: k, title: k, width: textWidth(k) };
    //             }
    //         }
    //         $("#paramQueryTable").grid("destroy", true, true);
    //         $("#paramQueryTable").grid({
    //             columns: fieldArr,
    //             dataSource: { url: "paramQuery/getParamItems", type: "post", data: params },
    //             primaryKey: "id",
    //             pager: { limit: 10, sizes: [10, 20, 50, 100] },
    //             autoScroll: true,
    //             uiLibrary: "bootstrap",
    //         });
    //     } else {
    //         layer.open({
    //             title: "提示",
    //             content: "没有数据，请联系开发人员查看kget是否正常入库"
    //         });
    //     }
    //     l.stop();
    //     E.stop();
    // });
    var selectedFields = $("#moTableFields").val();
    if (selectedFields) {
        for (var i in selectedFields) {
            if (selectedFields[i] == "mo") {
                fieldArr[i] = {
                    field: selectedFields[i],
                    title: selectedFields[i],
                    width: 600
                };
            } else {
                fieldArr[i] = {
                    field: selectedFields[i],
                    title: selectedFields[i],
                    width: textWidth(selectedFields[i])
                };
            }
        }
    } else {
        $("#moTableFields").children().each(function() {
            var value = $(this).text();
            if (value == "mo") {
                fieldArr.push({
                    field: value,
                    title: value,
                    width: 600
                });
            } else {
                fieldArr.push({
                    field: value,
                    title: value,
                    width: textWidth(value)
                });
            }
        })
    }
    $("#paramQueryTable").grid("destroy", true, true);
    $("#paramQueryTable").grid({
        columns: fieldArr,
        dataSource: {
            url: "paramQuery/getParamItems",
            type: "post",
            data: params
        },
        primaryKey: "id",
        pager: {
            limit: 10,
            sizes: [10, 20, 50, 100]
        },
        autoScroll: true,
        uiLibrary: "bootstrap",
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
    params.fields = $("#moTableFields").val();
    var l = Ladda.create(document.getElementById("search"));
    var E = Ladda.create(document.getElementById("export"));
    l.start();
    E.start();
    var ajaxTimeoutTest = $.ajax({
        url: "paramQuery/exportParamFile", //请求的URL
        timeout: 600000, //超时时间设置，单位毫秒
        type: "post", //请求方式，get或post
        data: params, //请求所传参数，json格式
        dataType: "json", //返回的数据格式
        success: function(data) { //请求成功的回调函数
            l.stop();
            E.stop();
            if (data.result) {
                fileDownload(data.fileName);
            } else {
                // alert("");
                layer.open({
                    title: "提示",
                    content: "There is error occured!"
                });
            }
        },
        complete: function(XMLHttpRequest, status) { //请求完成后最终执行参数
            if (status == "timeout") { //超时,status还有success,error等值的情况
                layer.confirm("请求超时,出现未知情况，请联系开发人员！", { title: "提示" }, function(index) {
                    layer.close(index);
                }, function(index) {
                    l.stop();
                    E.stop();
                    ajaxTimeoutTest.abort();
                    layer.close(index);
                });
            }
        }
    });
    /*$.post("paramQuery/exportParamFile",params,function(data){
      	l.stop();
      	E.stop();
      	if(data.result){
            fileDownload(data.fileName);
      	}else{
        	// alert("");
            layer.open({
                title: "提示",
                content: "There is error occured!"
            });
      	}
    }); */
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
    mydate = mydate.getDate(); //值范围1-31
    var mymonthStr = "";
    var mydateStr = "";
    mymonthStr = mymonth >= 10 ? mymonth : "0" + mymonth;
    mydateStr = mydate >= 10 ? mydate : "0" + mydate;
    var kgetDate = taskType + myyearStr + mymonthStr + mydateStr;
    return kgetDate;
}

function initMoTableFields() {
    $("#moTableFields").multiselect({
        dropRight: true,
        buttonWidth: "100%",
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        nonSelectedText: "请选择属性",
        filterPlaceholder: "搜索",
        nSelectedText: "项被选中",
        includeSelectAllOption: true,
        selectAllText: "全选/取消全选",
        allSelectedText: "已选中所有属性",
        maxHeight: 200,
        width: 220
    });
    $("#moTableFields").multiselect('disable');
}

function getMoTableFiedls() {
    var params = getParam("paramQuery");
    $("#moTableFields").multiselect('disable');
    $.post("paramQuery/getParamTableField", params, function(data) {
        var newOptions = [];
        for (var k in data) {
            var obj = {
                label: k,
                value: k,
                selected: true
            };
            newOptions.push(obj);
        }
        $("#moTableFields").multiselect("dataprovider", newOptions);
        $("#moTableFields").multiselect('enable');
        $("#moTableFields").next().children(".btn").click();
    });
}