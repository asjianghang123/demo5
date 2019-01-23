var parameterAnalysisDateId = "#parameterAnalysisDate";
var parameterAnalysisCityId = "#parameterAnalysisCity";
var paramQueryMoTreeId = "#paramQueryMoTree";
var featureListId = "#featureList";
var paramQueryMoTreeData = "";

$(document).ready(function() {
    toogle("licenseAnalysis");
    //--start of parameterAnalysisDate init--
    $(parameterAnalysisDateId).select2();
    var url = "licenseAnalysis/getParamTasks";
    $.ajax({
        type: "post",
        url: url,
        dataType: "json",
        success: function(data) {
            var parameterAnalysisDateSelect = $(parameterAnalysisDateId).select2({
                height: 50,
                placeholder: "请选择日期",
                //allowClear: true,
                data: data,
            });
            //var value = $(parameterAnalysisDateId).val();
            var task = getCurrentDate("kget");
            $(parameterAnalysisDateId).val(getCurrentDate("kget")).trigger("change");
            if ($(parameterAnalysisDateId).val() == null) {
                $(parameterAnalysisDateId).val(getYesterdayDate("kget")).trigger("change");
            }
        }

    });
    $(parameterAnalysisDateId).on("change",function(e){
        　　// e 的话就是一个对象 然后需要什么就 “e.参数” 形式 进行获取 
        console.log(e.currentTarget.value);
        initLicenseNameList(e.currentTarget.value);
        initLicenseIdList(e.currentTarget.value);
        initStateList(e.currentTarget.value);
    })
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

        $.post("licenseAnalysis/getAllSubNetwork", params, function(data) {
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
    url = "licenseAnalysis/getParamCitys";
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
            $(parameterAnalysisCityId).multiselect("dataprovider", newOptions);
        }
    });

    //--end of parameterAnalysisCity init--

});
//--start of licenseName init--
function initLicenseNameList(task){
    if (task) {
        $("#licenseName").multiselect({
            buttonWidth: "100%",
            enableFiltering: true,
            nonSelectedText: "请选择license名称",
            filterPlaceholder: "搜索",
            nSelectedText: "项被选中",
            includeSelectAllOption: true,
            selectAllText: "全选/取消全选",
            allSelectedText: "已选中所有license名称",
            maxHeight: 200,
            maxWidth: "100%"
        });
        var url = "licenseAnalysis/getLicenseNameList";
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
                $("#licenseName").multiselect("dataprovider", newOptions);
            }
        });
    }
}
//--start of licenseName init--
//--start of licenseId init--
function initLicenseIdList(task){
    if (task) {
        $("#licenseId").multiselect({
            buttonWidth: "100%",
            enableFiltering: true,
            nonSelectedText: "请选择licenseId",
            filterPlaceholder: "搜索",
            nSelectedText: "项被选中",
            includeSelectAllOption: true,
            selectAllText: "全选/取消全选",
            allSelectedText: "已选中所有licenseId",
            maxHeight: 200,
            maxWidth: "100%"
        });
        var url = "licenseAnalysis/getLicenseIdList";
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
                $("#licenseId").multiselect("dataprovider", newOptions);
            }
        });
    }
}
//--start of licenseId init--
//--start of state init--
function initStateList(task){
    if (task) {
        $("#state").multiselect({
            buttonWidth: "100%",
            enableFiltering: true,
            nonSelectedText: "请选择状态",
            filterPlaceholder: "搜索",
            nSelectedText: "项被选中",
            includeSelectAllOption: true,
            selectAllText: "全选/取消全选",
            allSelectedText: "已选中所有状态",
            maxHeight: 200,
            maxWidth: "100%"
        });
        var url = "licenseAnalysis/getStateList";
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
                $("#state").multiselect("dataprovider", newOptions);
            }
        });
    }
}
//--start of state init--

//--------start of tableSearch-----
function licenseAnalysisSearch() {
    var params = getParam();
    if (params == false) {
        return false;
    }
    var l = Ladda.create(document.getElementById("search"));
    l.start();
    var fieldArr = [];
    $.post("licenseAnalysis/getTableField", params, function(data) {
        if (data) {
            for (var k in data) {
                if (k == "mo") {

                    fieldArr[fieldArr.length] = { field: k, title: k, width: 200 };

                } else if (k != "id") {

                    fieldArr[fieldArr.length] = { field: k, title: k, width: textWidth(k) };
                }
            }
            $("#licenseAnalysisTable").grid("destroy", true, true);
            $("#licenseAnalysisTable").grid({
                columns: fieldArr,
                dataSource: { url: "licenseAnalysis/getItems", type: "post", data: params },
                primaryKey: "id",
                pager: { limit: 10, sizes: [10, 20, 50, 100] },
                autoScroll: true,
                uiLibrary: "bootstrap",
            });
        } else {
            layer.open({
                title: "提示",
                content: "没有数据，请联系开发人员查看原因"
            });
        }
        l.stop();
    });
}

function licenseAnalysisExport() {
    var params = getParam();
    if (params == false) {
        return false;
    }
    var E = Ladda.create(document.getElementById("export"));
    E.start();
    var ajaxTimeoutTest = $.ajax({
        url: "licenseAnalysis/exportFile", //请求的URL
        timeout: 600000, //超时时间设置，单位毫秒
        type: "post", //请求方式，get或post
        data: params, //请求所传参数，json格式
        dataType: "json", //返回的数据格式
        success: function(data) { //请求成功的回调函数
            E.stop();
            if (data.result) {
                fileDownload(data.fileName);
            } else {
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
}

function getParam(action) {
    
    var task = $(parameterAnalysisDateId).val();
    var citys = $(parameterAnalysisCityId).val();
    var subNet = $(subNetworks).val();
    var erbs = $("#paramQueryErbs").val();
    var licenseNameList = $("#licenseName").val();
    var licenseIdList = $("#licenseId").val();
    var stateList = $("#state").val();
    if (task != null) {
        var params = {
            db: task,
            erbs: erbs,
            citys: citys,
            subNet: subNet,
            licenseNameList: licenseNameList,
            licenseIdList: licenseIdList,
            stateList: stateList
        };
        return params;
    } else {
        layer.open({
            title: "提示",
            content: "Please choose database first!"
        });
        return false;
    }
}
function textWidth(text) {
    var length = text.length;
    if (length > 15) {
        return length * 15;
    }
    return 200;
}
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