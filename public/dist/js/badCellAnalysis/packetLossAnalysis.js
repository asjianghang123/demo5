$(function() {
    toogle("packetLossAnalysis");
    initDataSource();
    initSurvey();
    checkType();
    initCitys();
    //setDate();
    setHour();
});

function initDataSource() {
    $("#dataSource").multiselect({
        //dropRight: true,
        buttonWidth: "100%",
        //enableFiltering: true,
        //nonSelectedText: "选择城市",
        //filterPlaceholder:"搜索",
        //nSelectedText: "项被选中",
        includeSelectAllOption: true,
        //selectAllText: "全选/取消全选",
        //allSelectedText:"已选中所有城市",
        maxHeight: 200,
        maxWidth: "100%"
    });
    // var data = ["MRS", "MRO"];
    var data = ["MRS"];
    var newData = [];
    for (var i in data) {
        newData.push({ "label": data[i], "value": data[i] });
    }
    $("#dataSource").multiselect("dataprovider", newData);
    $("#dataSource").on("change", function() {
        initSurvey();
    });
}

function initSurvey() {
    $("#survey").multiselect({
        //dropRight: true,
        buttonWidth: "100%",
        //enableFiltering: true,
        //nonSelectedText: "选择城市",
        //filterPlaceholder:"搜索",
        //nSelectedText: "项被选中",
        includeSelectAllOption: true,
        //selectAllText: "全选/取消全选",
        //allSelectedText:"已选中所有城市",
        maxHeight: 200,
        maxWidth: "100%"
    });
    var dataSource = $("#dataSource").val();
    var newData = [];
    if (dataSource == "MRS") {
        var data = ["PacketLossRate", "RSRP", "PowerHeadRoom", "RSRQ", "TADV", "AOA", "TadvRsrp", "SinrUL", "RipRsrp"];
        for (var i in data) {
            newData.push({ "label": data[i], "value": data[i] });
        }
    } else if (dataSource == "MRO") {
        var data = ["RSRP", "RSRP频点级"];
        for (var i in data) {
            newData.push({ "label": data[i], "value": data[i] + "_MRO" });
        }
    }

    $("#survey").multiselect("dataprovider", newData);
    setDate();
}

function checkType() {
    $("#regionType").on("change", function() {
        var regionType = $(this).val();
        if (regionType == "city") {
            $("#baseStation").attr("disabled", "disabled");
            $("#groupEcgi").attr("disabled", "disabled");
        } else if (regionType == "baseStation" || regionType == "baseStationGroup") {
            $("#baseStation").removeAttr("disabled");
            $("#groupEcgi").attr("disabled", "disabled");
        } else if (regionType == "groupEcgi" || regionType == "cellGroup") {
            $("#groupEcgi").removeAttr("disabled");
            $("#baseStation").attr("disabled", "disabled");
        }
    });
    $("#timeType").on("change", function() {
        var timeType = $(this).val();
        if (timeType == "day") {
            $("#hour").multiselect("disable");
        } else {
            $("#hour").multiselect("enable");
        }
    });

}

function initCitys() {
    $("#city").multiselect({
        //dropRight: true,
        buttonWidth: "100%",
        //enableFiltering: true,
        nonSelectedText: "选择城市",
        //filterPlaceholder:"搜索",
        nSelectedText: "项被选中",
        includeSelectAllOption: true,
        selectAllText: "全选/取消全选",
        //allSelectedText:"已选中所有城市",
        maxHeight: 200,
        maxWidth: "100%"
    });
    // $.get("packetLossAnalysis/getCitys", null, function(data) {
    //     data = JSON.parse(data);
    //     var newData = [];
    //     for (var i in data) {
    //         var CHCity = data[i].split("-")[0];
    //         var dataBase = data[i].split("-")[1];
    //         newData.push({ "label": CHCity, "value": dataBase });
    //     }
    //     $("#city").multiselect("dataprovider", newData);
    //     setDate();
    // });
    // $("#survey").on("change", function() {
    //     setDate();
    // });
    var newData = [{
        "label": "常州",
        "value": "pgsql_cz"
    }, {
        "label": "南通",
        "value": "pgsql_nt"
    }, {
        "label": "无锡",
        "value": "pgsql_wx"
    }];
    $("#city").multiselect("dataprovider", newData);
    setDate();
}

function setDate() {
    $("#startTime").datepicker({ format: "yyyy-mm-dd" }); //返回日期
    $("#endTime").datepicker({ format: "yyyy-mm-dd" }); //返回日期
    var nowTemp = new Date();
    var year = nowTemp.getFullYear();
    var month = nowTemp.getMonth() + 1;
    var day = nowTemp.getDate();
    var today = year + "-" + month + "-" + day;
    $("#startTime").datepicker("setValue", today);
    $("#endTime").datepicker("setValue", today);
    var survey = $("#survey").val();
    var url = "packetLossAnalysis/getCityDate";
    if (survey == "RSRP_MRO" || survey == "RSRP频点级_MRO") {
        url = "packetLossAnalysis/MRORSRPQuery/getCityDate";
    };
    var params = {
        dataBase: $("#city").val()
    };
    $.post(url, params, function(data) {
        data = JSON.parse(data);
        var sdata = [];
        for (var i = 0; i < data.length; i++) {
            if (data[i] === today) {
                continue;
            }
            sdata.push(data[i]);
        }
        sdata.push(today);
        $("#startTime").datepicker("setValues", sdata);
        $("#endTime").datepicker("setValues", sdata);

    });
    $("#city").change(function() {
        var city = $("#city").val();
        var params = {
            dataBase: city
        };
        $.post(url, params, function(data) {
            data = JSON.parse(data);
            var sdata = [];
            for (var i = 0; i < data.length; i++) {
                if (data[i] === today) {
                    continue;
                }
                sdata.push(data[i]);
            }
            sdata.push(today);
            $("#startTime").datepicker("setValues", sdata);
            $("#endTime").datepicker("setValues", sdata);
        });
    });
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var checkin = $("#startTime").datepicker({
        onRender: function(date) {
            return date.valueOf() < now.valueOf() ? "" : "";
        }
    }).on("changeDate", function(ev) {
        checkin.hide();
    }).data("datepicker");

    var checkout = $("#endTime").datepicker({
        onRender: function(date) {
            //return date.valueOf() <= checkin.date.valueOf() ? "disabled" : "";
            return date.valueOf() <= checkin.date.valueOf() ? '' : '';
        }
    }).on("changeDate", function(ev) {
        checkout.hide();
    }).data("datepicker");



}

function setHour() {
    $("#hour").multiselect({
        //dropRight: true,
        buttonWidth: "100%",
        //enableFiltering: true,
        nonSelectedText: "选择小时",
        //filterPlaceholder:"搜索",
        nSelectedText: "项被选中",
        includeSelectAllOption: true,
        selectAllText: "全选/取消全选",
        allSelectedText: "已选全天",
        maxHeight: 200,
        maxWidth: "100%"
    });
    var hours = [];
    for (var i = 0; i < 24; i++) {
        if (i < 10) {
            hours.push({ "label": "0" + i, "value": "0" + i });
        } else {
            hours.push({ "label": i, "value": i });
        }

    }
    $("#hour").multiselect("dataprovider", hours);
    $("#hour").multiselect("disable");
}

function query() {
    var params = getParams();
    if (!params) {
        return;
    }
    var queryBtn = Ladda.create(document.getElementById("queryBtn"));
    var exportBtn = Ladda.create(document.getElementById("exportBtn"));
    queryBtn.start();
    //exportBtn.start();

    var fieldArr = [];
    var survey = $("#survey").val();



    fieldArr[fieldArr.length] = { field: "date", title: "date", width: 120 };
    if (params.timeType == "hour") {
        fieldArr[fieldArr.length] = { field: "hourId", title: "hourId", width: 100 };
    }

    if (survey == "PacketLossRate") {
        if (params.regionType != "groupEcgi") {
            fieldArr[fieldArr.length] = { field: "cellTotal", title: "cellTotal", width: 100 };
            fieldArr[fieldArr.length] = { field: "city", title: "city", width: 100 };
        }
        if (params.regionType == "city") {
            fieldArr[fieldArr.length] = { field: "上行丢包率", title: "上行丢包率", width: 120 };
            fieldArr[fieldArr.length] = { field: "下行丢包率", title: "下行丢包率", width: 120 };
            fieldArr[fieldArr.length] = { field: "上行丢包率占比", title: "上行丢包率>20%的采样点占比", width: 220 };
            fieldArr[fieldArr.length] = { field: "下行丢包率占比", title: "下行丢包率>20%的采样点占比", width: 220 };
        }
        if (params.regionType == "baseStation") {
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
            fieldArr[fieldArr.length] = { field: "上行丢包率", title: "上行丢包率", width: 120 };
            fieldArr[fieldArr.length] = { field: "下行丢包率", title: "下行丢包率", width: 120 };
        } else if (params.regionType == "cellGroup") {
            fieldArr[fieldArr.length] = { field: "cellGroup", title: "cellGroup", width: 150 };
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
            fieldArr[fieldArr.length] = { field: "ecgi", title: "ecgi", width: 150 };
            fieldArr[fieldArr.length] = { field: "上行丢包率", title: "上行丢包率", width: 120 };
            fieldArr[fieldArr.length] = { field: "下行丢包率", title: "下行丢包率", width: 120 };
        } else if (params.regionType == "groupEcgi") {
            fieldArr[fieldArr.length] = { field: "city", title: "city", width: 100 };
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
            fieldArr[fieldArr.length] = { field: "ecgi", title: "ecgi", width: 150 };
            fieldArr[fieldArr.length] = { field: "上行丢包率", title: "上行丢包率", width: 120 };
            fieldArr[fieldArr.length] = { field: "下行丢包率", title: "下行丢包率", width: 120 };
            fieldArr[fieldArr.length] = { field: "上行丢包率占比", title: "上行丢包率>20%的采样点占比", width: 220 };
            fieldArr[fieldArr.length] = { field: "下行丢包率占比", title: "下行丢包率>20%的采样点占比", width: 220 };
        } else if (params.regionType == "baseStationGroup") {
            fieldArr[fieldArr.length] = { field: "baseStationGroup", title: "baseStationGroup", width: 150 };
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
            fieldArr[fieldArr.length] = { field: "上行丢包率", title: "上行丢包率", width: 120 };
            fieldArr[fieldArr.length] = { field: "下行丢包率", title: "下行丢包率", width: 120 };
        }

        for (var i = 1; i <= 28; i++) {
            fieldArr[fieldArr.length] = { field: "up" + i, title: "上行丢包数" + i, width: 120 };
        }
        for (var i = 1; i <= 28; i++) {
            fieldArr[fieldArr.length] = { field: "down" + i, title: "下行丢包数" + i, width: 120 };
        }
    } else if (survey == "RSRP") {
        if (params.regionType == "city") { //城市
            fieldArr[fieldArr.length] = { field: "cellTotal", title: "cellTotal", width: 100 };
            fieldArr[fieldArr.length] = { field: "city", title: "city", width: 100 };
            fieldArr[fieldArr.length] = { field: "总RSRP采样点总数", title: "总RSRP采样点总数", width: 150 };
            fieldArr[fieldArr.length] = { field: "RSRP平均覆盖率", title: "RSRP平均覆盖率", width: 150 };
            fieldArr[fieldArr.length] = { field: "RSRP的比例", title: "RSRP>=-110的比例", width: 150 };

        } else if (params.regionType == "baseStation") { //基站
            fieldArr[fieldArr.length] = { field: "cellTotal", title: "cellTotal", width: 100 };
            fieldArr[fieldArr.length] = { field: "city", title: "city", width: 100 };
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
            fieldArr[fieldArr.length] = { field: "总RSRP采样点总数", title: "总RSRP采样点总数", width: 150 };
            fieldArr[fieldArr.length] = { field: "RSRP平均覆盖率", title: "RSRP平均覆盖率", width: 150 };
            fieldArr[fieldArr.length] = { field: "RSRP的比例", title: "RSRP>=-110的比例", width: 150 };
        } else if (params.regionType == "groupEcgi") { //小区
            fieldArr[fieldArr.length] = { field: "city", title: "city", width: 100 };
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
            // fieldArr[fieldArr.length] = {field: "cellName", title: "cellName", width: 150};
            fieldArr[fieldArr.length] = { field: "ecgi", title: "ecgi", width: 150 };
            fieldArr[fieldArr.length] = { field: "总RSRP采样点总数", title: "总RSRP采样点总数", width: 150 };
            fieldArr[fieldArr.length] = { field: "RSRP平均覆盖率", title: "RSRP平均覆盖率", width: 150 };
            fieldArr[fieldArr.length] = { field: "RSRP的比例", title: "RSRP>=-110的比例", width: 150 };
        } else if (params.regionType == "baseStationGroup") { //基站组
            fieldArr[fieldArr.length] = { field: "cellTotal", title: "cellTotal", width: 100 };
            fieldArr[fieldArr.length] = { field: "city", title: "city", width: 100 };
            fieldArr[fieldArr.length] = { field: "baseStationGroup", title: "baseStationGroup", width: 150 };
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
            fieldArr[fieldArr.length] = { field: "总RSRP采样点总数", title: "总RSRP采样点总数", width: 150 };
            fieldArr[fieldArr.length] = { field: "RSRP平均覆盖率", title: "RSRP平均覆盖率", width: 150 };
            fieldArr[fieldArr.length] = { field: "RSRP的比例", title: "RSRP>=-110的比例", width: 150 };
        } else if (params.regionType == "cellGroup") { //小区组
            fieldArr[fieldArr.length] = { field: "cellTotal", title: "cellTotal", width: 100 };
            fieldArr[fieldArr.length] = { field: "city", title: "city", width: 100 };
            fieldArr[fieldArr.length] = { field: "cellGroup", title: "cellGroup", width: 150 };
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
            // fieldArr[fieldArr.length] = {field: "cellName", title: "cellName", width: 150};
            fieldArr[fieldArr.length] = { field: "ecgi", title: "ecgi", width: 150 };
            fieldArr[fieldArr.length] = { field: "总RSRP采样点总数", title: "总RSRP采样点总数", width: 150 };
            fieldArr[fieldArr.length] = { field: "RSRP平均覆盖率", title: "RSRP平均覆盖率", width: 150 };
            fieldArr[fieldArr.length] = { field: "RSRP的比例", title: "RSRP>=-110的比例", width: 150 };
        }
    } else if (survey == "PowerHeadRoom") {
        if (params.regionType != "groupEcgi") {
            fieldArr[fieldArr.length] = { field: "cellTotal", title: "cellTotal", width: 100 };
            fieldArr[fieldArr.length] = { field: "city", title: "city", width: 100 };
        }
        if (params.regionType == "baseStation") {
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
        } else if (params.regionType == "groupEcgi") {
            fieldArr[fieldArr.length] = { field: "city", title: "city", width: 100 };
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
            fieldArr[fieldArr.length] = { field: "ecgi", title: "ecgi", width: 150 };
        } else if (params.regionType == "baseStationGroup") {
            fieldArr[fieldArr.length] = { field: "baseStationGroup", title: "baseStationGroup", width: 150 };
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
        } else if (params.regionType == "cellGroup") {
            fieldArr[fieldArr.length] = { field: "cellGroup", title: "cellGroup", width: 150 };
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
            fieldArr[fieldArr.length] = { field: "ecgi", title: "ecgi", width: 150 };
        }

    } else if (survey == "SinrUL") {
        if (params.regionType == "city") {
            fieldArr[fieldArr.length] = { field: "cellTotal", title: "cellTotal", width: 100 };
            fieldArr[fieldArr.length] = { field: "city", title: "city", width: 100 };
        }
        if (params.regionType == "baseStation") {
            fieldArr[fieldArr.length] = { field: "city", title: "city", width: 100 };
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
        } else if (params.regionType == "groupEcgi") {
            fieldArr[fieldArr.length] = { field: "city", title: "city", width: 100 };
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
            fieldArr[fieldArr.length] = { field: "ecgi", title: "ecgi", width: 150 };
        } else if (params.regionType == "baseStationGroup") {
            fieldArr[fieldArr.length] = { field: "city", title: "city", width: 100 };
            fieldArr[fieldArr.length] = { field: "baseStationGroup", title: "baseStationGroup", width: 150 };
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
        } else if (params.regionType == "cellGroup") {
            fieldArr[fieldArr.length] = { field: "city", title: "city", width: 100 };
            fieldArr[fieldArr.length] = { field: "cellGroup", title: "cellGroup", width: 150 };
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
            fieldArr[fieldArr.length] = { field: "ecgi", title: "ecgi", width: 150 };
        }
        fieldArr[fieldArr.length] = { field: "total", title: "采样点总数", width: 100 };
        fieldArr[fieldArr.length] = { field: "sinr", title: "SINR<-3采样点数", width: 150 };
        fieldArr[fieldArr.length] = { field: "sinr_avg", title: "SINR<-3的比例", width: 130 };
        fieldArr[fieldArr.length] = { field: "total_avg", title: "平均上行SINR", width: 120 };

        for (var i = 0; i < 37; i++) {
            if (i < 10) {
                var n = "0" + i;
            } else {
                var n = i;
            }
            fieldArr[fieldArr.length] = { field: "SinrUL" + n, title: "SinrUL" + n, width: 120 };
        }

    } else if (survey == "TADV") {
        if (params.regionType != "groupEcgi") {
            fieldArr[fieldArr.length] = { field: "cellTotal", title: "cellTotal", width: 100 };
            fieldArr[fieldArr.length] = { field: "city", title: "city", width: 100 };
            fieldArr[fieldArr.length] = { field: "终端平均距离", title: "终端平均距离", width: 150 };
        }

        if (params.regionType == "baseStation") {
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
        } else if (params.regionType == "groupEcgi") {
            fieldArr[fieldArr.length] = { field: "city", title: "city", width: 100 };
            fieldArr[fieldArr.length] = { field: "终端平均距离", title: "终端平均距离", width: 150 };
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
            fieldArr[fieldArr.length] = { field: "ecgi", title: "ecgi", width: 150 };
        } else if (params.regionType == "baseStationGroup") {
            fieldArr[fieldArr.length] = { field: "baseStationGroup", title: "baseStationGroup", width: 150 };
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
        } else if (params.regionType == "cellGroup") {
            fieldArr[fieldArr.length] = { field: "cellGroup", title: "cellGroup", width: 150 };
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
            fieldArr[fieldArr.length] = { field: "ecgi", title: "ecgi", width: 150 };
        }
        for (var i = 0; i < 45; i++) {
            if (i < 10) {
                var n = "0" + i;
            } else {
                var n = i;
            }
            fieldArr[fieldArr.length] = { field: "TADV" + n, title: "TADV" + n, width: 120 };
        }
    } else {
        if (params.regionType != "groupEcgi") {
            fieldArr[fieldArr.length] = { field: "cellTotal", title: "cellTotal", width: 100 };
            fieldArr[fieldArr.length] = { field: "city", title: "city", width: 100 };
        }
        if (params.regionType == "baseStation") {
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
        } else if (params.regionType == "groupEcgi") {
            fieldArr[fieldArr.length] = { field: "city", title: "city", width: 100 };
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
            fieldArr[fieldArr.length] = { field: "ecgi", title: "ecgi", width: 150 };
        } else if (params.regionType == "baseStationGroup") {
            fieldArr[fieldArr.length] = { field: "baseStationGroup", title: "baseStationGroup", width: 150 };
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
        } else if (params.regionType == "cellGroup") {
            fieldArr[fieldArr.length] = { field: "cellGroup", title: "cellGroup", width: 150 };
            fieldArr[fieldArr.length] = { field: "userLabel", title: "userLabel", width: 150 };
            fieldArr[fieldArr.length] = { field: "ecgi", title: "ecgi", width: 150 };
        }
        if (survey == "RSRQ") {
            for (var i = 0; i < 18; i++) {
                if (i < 10) {
                    var n = "0" + i;
                } else {
                    var n = i;
                }
                fieldArr[fieldArr.length] = { field: "RSRQ" + n, title: "RSRQ" + n, width: 120 };
            }
        } else if (survey == "AOA") {
            for (var i = 0; i < 72; i++) {
                if (i < 10) {
                    var n = "0" + i;
                } else {
                    var n = i;
                }
                fieldArr[fieldArr.length] = { field: "AOA" + n, title: "AOA" + n, width: 120 };
            }
        } else if (survey == "TadvRsrp") {
            for (var i = 0; i < 11; i++) {
                if (i < 10) {
                    var n = "0" + i;
                } else {
                    var n = i;
                }
                for (var j = 0; j < 12; j++) {
                    if (j < 10) {
                        var s = "0" + j;
                    } else {
                        var s = j;
                    }
                    fieldArr[fieldArr.length] = { field: "Tadv" + n + "Rsrp" + s, title: "Tadv" + n + "Rsrp" + s, width: 120 };
                }
            }
        } else if (survey == "RipRsrp") {
            for (var i = 0; i < 9; i++) {
                for (var j = 0; j < 12; j++) {
                    if (j < 10) {
                        var s = "0" + j;
                    } else {
                        var s = j;
                    }
                    fieldArr[fieldArr.length] = { field: "mr_Rip0" + i + "Rsrp" + s, title: "Rip0" + i + "Rsrp" + s, width: 150 };
                }
            }
        }
    }
    // var fieldCol = new Array(fieldArr);
    if (survey == "PacketLossRate") {
        $("#packetLossTable").grid("destroy", true, true);
        var grid = $("#packetLossTable").grid({
            columns: fieldArr,
            dataSource: {
                url: "packetLossAnalysis/getTableData",
                success: function(data) {
                    queryBtn.stop();
                    exportBtn.stop();
                    data = JSON.parse(data);
                    grid.render(data);
                }
            },
            params: params,
            pager: {
                limit: 10,
                sizes: [10, 20, 50, 100]
            },
            autoScroll: true,
            uiLibrary: "bootstrap",
            primaryKey: "id",
            autoLoad: true
        });
    } else if (survey == "RSRP") {
        $.post("packetLossAnalysis/CoverageQuery/getRSRPKey", null, function(data) {
            data = JSON.parse(data);
            for (var i in data) {
                fieldArr[fieldArr.length] = { field: data[i], title: data[i], width: 140 };
            }
            $("#packetLossTable").grid("destroy", true, true);
            var grid = $("#packetLossTable").grid({
                columns: fieldArr,
                dataSource: {
                    url: "packetLossAnalysis/CoverageQuery/getTableData",
                    success: function(data) {
                        queryBtn.stop();
                        exportBtn.stop();
                        data = JSON.parse(data);
                        grid.render(data);
                    }
                },
                params: params,
                pager: {
                    limit: 10,
                    sizes: [10, 20, 50, 100]
                },
                autoScroll: true,
                uiLibrary: "bootstrap",
                primaryKey: "id",
                autoLoad: true
            });
        });
    } else if (survey == "RSRP_MRO" || survey == "RSRP频点级_MRO") {
        $.get("packetLossAnalysis/MRORSRPQuery/getTableData", params, function(data) {
            queryBtn.stop();
            exportBtn.stop();
            $("#packetLossTable").grid("destroy", true, true);
            data = JSON.parse(data);
            if (data.result == "error") {
                layer.open({
                    title: "提示",
                    content: "没有数据"
                });
                return;
            };
            var fieldArr = [];
            for (var i = 0; i < data.key.length; i++) {
                if (data.key[i] == "Sample110") {
                    fieldArr[fieldArr.length] = { field: data.key[i], title: "Sample>=-110", width: 140 };
                } else if (data.key[i] == "Rate110") {
                    fieldArr[fieldArr.length] = { field: data.key[i], title: "Rate>-110", width: 140 };
                } else {
                    fieldArr[fieldArr.length] = { field: data.key[i], title: data.key[i], width: 140 };
                }

            }
            /*$("#packetLossTable").grid("destroy", true, true);
            $("#packetLossTable").grid({
            	columns: fieldArr,
            	dataSource: {url: "packetLossAnalysis/MRORSRPQuery/getTableData", type: "get", data: params},
            	//primaryKey: "id",
            	pager: {limit: 10, sizes: [10, 20, 50, 100]},
            	autoScroll: true,
            	uiLibrary: "bootstrap",
            });*/

            var grid = $("#packetLossTable").grid({
                columns: fieldArr,
                dataSource: data.records,
                params: params,
                pager: { limit: 10, sizes: [10, 20, 50, 100] },
                autoScroll: true,
                uiLibrary: "bootstrap",
                primaryKey: "id",
                autoLoad: true
            });
        });
    } else if (survey == "PowerHeadRoom") {
        $.post("packetLossAnalysis/PowerHeadRoom/getPowerHeadRoomKey", null, function(data) {
            data = JSON.parse(data);
            fieldArr[fieldArr.length] = { field: "PHR满功率发射比例", title: "PHR满功率发射比例", width: 150 };
            for (var i in data) {
                fieldArr[fieldArr.length] = { field: data[i], title: data[i], width: 120 };
            }
            $("#packetLossTable").grid("destroy", true, true);
            var grid = $("#packetLossTable").grid({
                columns: fieldArr,
                dataSource: {
                    url: "packetLossAnalysis/PowerHeadRoom/getTableData",
                    success: function(data) {
                        queryBtn.stop();
                        exportBtn.stop();
                        data = JSON.parse(data);
                        grid.render(data);
                    }
                },
                params: params,
                pager: {
                    limit: 10,
                    sizes: [10, 20, 50, 100]
                },
                autoScroll: true,
                uiLibrary: "bootstrap",
                primaryKey: "id",
                autoLoad: true
            });
        });
    } else if (survey == "TADV") {
        $("#packetLossTable").grid("destroy", true, true);
        var grid = $("#packetLossTable").grid({
            columns: fieldArr,
            dataSource: {
                url: "packetLossAnalysis/SurveyTADV/getTableData",
                success: function(data) {
                    queryBtn.stop();
                    exportBtn.stop();
                    data = JSON.parse(data);
                    grid.render(data);
                }
            },
            params: params,
            pager: {
                limit: 10,
                sizes: [10, 20, 50, 100]
            },
            autoScroll: true,
            uiLibrary: "bootstrap",
            primaryKey: "id",
            autoLoad: true
        });
    } else if (survey == "SinrUL") {
        $("#packetLossTable").grid("destroy", true, true);
        var grid = $("#packetLossTable").grid({
            columns: fieldArr,
            dataSource: {
                url: "packetLossAnalysis/SurveySinr/getTableData",
                success: function(data) {
                    queryBtn.stop();
                    exportBtn.stop();
                    data = JSON.parse(data);
                    grid.render(data);
                }
            },
            params: params,
            pager: {
                limit: 10,
                sizes: [10, 20, 50, 100]
            },
            autoScroll: true,
            uiLibrary: "bootstrap",
            primaryKey: "id",
            autoLoad: true
        });
    } else {
        $("#packetLossTable").grid("destroy", true, true);
        var grid = $("#packetLossTable").grid({
            columns: fieldArr,
            dataSource: {
                url: "packetLossAnalysis/Survey/getTableData",
                success: function(data) {
                    queryBtn.stop();
                    exportBtn.stop();
                    data = JSON.parse(data);
                    grid.render(data);
                }
            },
            params: params,
            pager: {
                limit: 10,
                sizes: [10, 20, 50, 100]
            },
            autoScroll: true,
            uiLibrary: "bootstrap",
            primaryKey: "id",
            autoLoad: true
        });
    }

}

function getParams() {
    var survey, regionType, city, baseStation, groupEcgi, timeType, date, hour;
    regionType = $("#regionType").val();
    if (!$("#city").val()) {
        // alert("请选择城市");
        layer.open({
            title: "提示",
            content: "请选择城市"
        });
        return "";
    }
    survey = $("#survey").val();
    city = $("#city").val();
    if (regionType == "city") {
        baseStation = "";
        groupEcgi = "";
    } else if (regionType == "baseStation" || regionType == "baseStationGroup") {
        if ($("#baseStation").val()) {
            baseStation = $("#baseStation").val();
        }
    } else if (regionType == "groupEcgi" || regionType == "cellGroup") {
        if ($("#groupEcgi").val()) {
            groupEcgi = $("#groupEcgi").val();
        }
    }
    timeType = $("#timeType").val();
    startTime = $("#startTime").val();
    endTime = $("#endTime").val();
    // date = $("#date").val();

    if (timeType == "day") {
        hour = "";
    } else {
        if ($("#hour").val()) {
            hour = $("#hour").val().join(",");
        }
    }
    var params = {
        survey: survey,
        regionType: regionType,
        citys: city,
        baseStation: baseStation,
        groupEcgi: groupEcgi,
        timeType: timeType,
        startTime: startTime,
        endTime: endTime,
        hour: hour
    };
    return params;
}

function exportFile() {
    var params = getParams();
    if (!params) {
        return;
    }
    var queryBtn = Ladda.create(document.getElementById("queryBtn"));
    var exportBtn = Ladda.create(document.getElementById("exportBtn"));
    //queryBtn.start();
    exportBtn.start();
    if (params["survey"] == "PacketLossRate") {

        $.post("packetLossAnalysis/exportFile", params, function(data) {
            queryBtn.stop();
            exportBtn.stop();
            data = JSON.parse(data);
            if (data.result == "true") {
                var filepath = data.filename.replace("\\", "");
                download(filepath, "", "data:text/csv;charset=utf-8");
            } else {
                // alert("There is error occured!");
                layer.open({
                    title: "提示",
                    content: "下载失败"
                });
            }
        });
    } else if (params["survey"] == "RSRP") {
        $.post("packetLossAnalysis/CoverageQuery/exportFile", params, function(data) {
            queryBtn.stop();
            exportBtn.stop();
            data = JSON.parse(data);
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
    } else if (params["survey"] == "RSRP_MRO" || params["survey"] == "RSRP频点级_MRO") {
        $.post("packetLossAnalysis/MRORSRPQuery/exportFile", params, function(data) {
            queryBtn.stop();
            exportBtn.stop();
            data = JSON.parse(data);
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
    } else if (params["survey"] == "PowerHeadRoom") {
        $.post("packetLossAnalysis/PowerHeadRoom/exportFile", params, function(data) {
            queryBtn.stop();
            exportBtn.stop();
            data = JSON.parse(data);
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

    } else if (params["survey"] == "SinrUL") {
        $.post("packetLossAnalysis/SurveySinr/exportFile", params, function(data) {
            queryBtn.stop();
            exportBtn.stop();
            data = JSON.parse(data);
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

    } else if (params["survey"] == "TADV") {
        $.post("packetLossAnalysis/SurveyTADV/exportFile", params, function(data) {
            queryBtn.stop();
            exportBtn.stop();
            data = JSON.parse(data);
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
    } else {

        $.post("packetLossAnalysis/Survey/exportFile", params, function(data) {
            queryBtn.stop();
            exportBtn.stop();
            data = JSON.parse(data);
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


//导入基站
function toName1(self) {
    $.ajaxFileUpload({
        url: "packetLossAnalysis/uploadFile",
        fileElementId: "fileImport1",
        secureuri: false,
        dataType: "json",
        type: "post",
        success: function(data, status) {
            $("#baseStation").val(data);
            data = "";
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
//导入小区
function toName2(self) {
    $.ajaxFileUpload({
        url: "packetLossAnalysis/uploadFile",
        //data : data,
        fileElementId: "fileImport2",
        secureuri: false,
        dataType: "json",
        type: "post",
        success: function(data, status) {
            $("#groupEcgi").val(data);
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