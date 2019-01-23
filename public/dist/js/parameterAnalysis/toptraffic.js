$(function(){
	toogle('TopTraffic');
	initCity();
    initDate();
});
var fileName;
function initCity() {
    $("#allCity").multiselect({
        dropRight: true,
        buttonWidth: "100%",
        //enableFiltering: true,
        // nonSelectedText: "请选择城市",
        //filterPlaceholder:"搜索",
        nSelectedText: "个城市被选中",
        includeSelectAllOption: true,
        // selectAllText: "全选/取消全选",
        allSelectedText: "已选中所有城市",
        maxHeight: 200,
        maxWidth: "100%"
    });
    var url = "TopTraffic/getAllCity";
    $.ajax({
        type: "GET",
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
            $("#allCity").multiselect("dataprovider", newOptions);
            // $("#allCity").multiselect('disable');
        }
    });
}

function initDate(){
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

function query(){
    var params = getParams();
    if(!params){
        return;
    }
    var queryBtn = Ladda.create(document.getElementById("queryBtn"));
    var exportBtn = Ladda.create(document.getElementById("exportBtn"));
    queryBtn.start()
    exportBtn.start()
    $.get("TopTraffic/getTableData",params,function(data){
    
        data = JSON.parse(data);

        // if(!data.error){
        //     layer.open({
        //             title: "提示",
        //             content: "没有数据"
        //         });
        //     return;
        // }
        var fieldArr=[];
        
        var text = (data.text).split(",");
        if(params.dimensions=="day"){
            fieldArr[fieldArr.length] = {field: 'day_id', title: "日期", width: 150};
            fieldArr[fieldArr.length] = {field: 'hour_id', title: "小时", width: 100};
        }else if(params.dimensions=="dayGroup"){
            fieldArr[fieldArr.length] = {field: 'DayGroup', title: "天组", width: 100};

        }

        fieldArr[fieldArr.length] = {field: 'city', title: "城市", width: 100};
        fieldArr[fieldArr.length] = {field: 'subNetwork', title: "子网", width: 180};
        fieldArr[fieldArr.length] = {field: 'cell', title: "小区", width: 150};
        for (var i in text) {
            fieldArr[fieldArr.length] = {field: text[i], title: text[i], width: 150};
        }
        // console.log(fieldArr);
        $("#queryDataTable").grid("destroy", true, true);
        var grid = $("#queryDataTable").grid({
            columns: fieldArr,
            dataSource: data.rows,
            params: params,
            pager: {
                limit: 10,
                sizes: [10, 20, 50, 100]
            },
            autoScroll: true,
            uiLibrary: "bootstrap",
            // primaryKey: "id",
            autoLoad: true
        });
        queryBtn.stop();
        // console.log(data.rows.length);
        // console.log(fileName);

        if(data.rows.length){
            fileName = data.fileName;
        }else{
            fileName ='';
        }
        exportBtn.stop();

    })

  



}

function exportFile(){
    if(fileName){
        download(fileName); 
    }else{
         layer.open({
            title:"提示",
            content:"数据为空"
        });
    }
}
function getParams(){
    var city = $("#allCity").val();
    var dimensions = $("#dimensions").val();
    var startTime = $("#startTime").val();
    var endTime = $("#endTime").val();

    if(!city){
        layer.open({
            title:"提示",
            content:"请选择城市"
        });
        return false;
    }
    if(!startTime||!startTime){
         layer.open({
            title:"提示",
            content:"请选择日期"
        });
        return false;
    }
     if(startTime>endTime){
        layer.open({
            title:"提示",
            content:"开始日期不应大于结束日期"
        });
        return false;
    }

    return {
        city:city,
        dimensions:dimensions,
        startTime:startTime,
        endTime:endTime,
    }
}