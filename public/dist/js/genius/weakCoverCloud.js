$(function() {
    toogle("weakCoverCloud");
    getAllCity();
    getChannels();

});
var bmap = new BMap.Map("map");
bmap.enableScrollWheelZoom(); // 启用滚轮放大缩小
// 初始化地图,设置中心点坐标和地图级别
var arr = setMapPoint();
bmap.centerAndZoom(new BMap.Point(arr[0], arr[1]), 10);
var mapv = new Mapv({
    drawTypeControl: false,
    map: bmap // 百度地图的map实例
});

function getChannels() {
    $("#channel").multiselect({
        dropRight: true,
        buttonWidth: 200,
        //enableFiltering: true,
        nonSelectedText: "请选择频段",
        //filterPlaceholder:'搜索',
        nSelectedText: "项被选中",
        includeSelectAllOption: true,
        selectAllText: "全选/取消全选",
        allSelectedText: "已全选",
        maxHeight: 200,
        maxWidth: "100%"

    });

    var url = "weakCoverCloud/interCloudChannel";
    $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
        success: function(data) {
            var newOptions = new Array();
            var obj = new Object();
            $(data).each(function(k, v) {
                var v = eval("(" + v + ")");
                obj = {
                    label: v["text"],
                    value: v["value"]
                };
                newOptions.push(obj);
            });
            $("#channel").multiselect("dataprovider", newOptions);
        }
    });
}

function drawMap() {
    var S = Ladda.create(document.getElementById("search"));
    S.start();
    var returnData = []; // 取城市的点来做示例展示的点数据

    var date = $("#date").val();
    // var hour = $("#hour").val();
    // var minute	  = $('#minute').val();
    var channel = $("#channel").val();
    //var citys     = $('#allCity').val();
    var city = $("#allCity").val();
    bmap.clearOverlays();

    if (date == "") {
        S.stop();
        layer.open({
            title: "提示",
            content: "请选择日期"
        });
        return;
    }
    if (!channel) {
        S.stop();
        layer.open({
            title: "提示",
            content: "请选择频段"
        });
        return;
    }

    // var params = {
    //     date: date,
    //     hour: hour,
    //     channel: channel
    // };

    var url = "weakCoverCloud/weakCoverCloudCells";
    $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
        data: {
            date: date,
            //hour: hour,
            // minute: minute,
            channel: channel.join(","),
            city: city
        },
        success: function(data) {
            for (var i = 0; i < data.length; i++) {
                returnData.push({
                    lng: data[i].longitude,
                    lat: data[i].latitude,
                    // count:data[i].ratio110
                    count: data[i].avgRsrp
                });
            }
            var layer = new Mapv.Layer({
                mapv: mapv, // 对应的mapv实例
                //zIndex: 1, // 图层层级
                //dataType: 'point', // 数据类型mapv，点类型
                data: returnData, // 数据
                drawType: "density", // 展示形式
                //dataRangeControl: true ,
                drawOptions: { // 绘制参数
                    type: "rect", // 网格类型，方形网格或蜂窝形
                    size: 4, // 网格大小
                    unit: "px", // 单位
                    opacity: "0.5",
                    label: { // 是否显示文字标签
                        show: true
                    },
                    splitList: [
                        // {
                        // 	end: 5,
                        // 	color: 'blue'
                        // },{
                        // 	start: 5,
                        // 	end: 10,
                        // 	color: 'green'
                        // },{
                        // 	start: 10,
                        // 	end: 15,
                        // 	color: 'lime'
                        // },{
                        // 	start: 15,
                        // 	end: 20,
                        // 	color: 'yellow'
                        // },{
                        // 	start: 20,
                        // 	end: 25,
                        // 	color: 'magenta'
                        // },{
                        // 	start: 25,
                        // 	color: 'red'
                        // }
                        {
                            end: -120,
                            color: "red"
                        }, {
                            start: -120,
                            end: -115,
                            color: "orange"
                        }, {
                            start: -115,
                            end: -110,
                            color: "magenta"
                        }, {
                            start: -110,
                            end: -105,
                            color: "yellow"
                        }, {
                            start: -105,
                            end: -95,
                            color: "lime"
                        }, {
                            start: -95,
                            end: -85,
                            color: "green"
                        }, {
                            start: -85,
                            color: "blue"
                        }
                    ],
                    events: {
                        click: function(e, data) {
                            // console.log("click", e, data);
                        }
                    }
                }
            });
            S.stop();
        }
    });
}


function getAllCity() {
    $("#allCity").multiselect({
        dropRight: true,
        buttonWidth: 200,
        //enableFiltering: true,
        nonSelectedText: "请选择城市",
        //filterPlaceholder:'搜索',
        nSelectedText: "项被选中",
        includeSelectAllOption: true,
        selectAllText: "全选/取消全选",
        allSelectedText: "已选中所有平台类型",
        maxHeight: 200,
        maxWidth: 220
    });
    var url = "weakCoverCloud/getAllCity";
    $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
        success: function(data) {
            var newOptions = new Array();
            var obj = new Object();
            $(data).each(function(k, v) {
                var v = eval("(" + v + ")");
                obj = {
                    label: v["text"],
                    value: v["value"]
                };
                newOptions.push(obj);
            });
            $("#allCity").multiselect("dataprovider", newOptions);
            setTime();
        }
    });
    $("#allCity").change(function() {
        setTime();
    });
}

function setTime() {
    $("#date").datepicker({ format: "yyyy-mm-dd" }); //返回日期
    var nowTemp = new Date();
    var year = nowTemp.getFullYear();
    var month = nowTemp.getMonth() + 1;
    var day = nowTemp.getDate();
    var today = year + '-' + month + '-' + day;
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var checkin = $("#date").datepicker({
        onRender: function(date) {
            return date.valueOf() < now.valueOf() ? '' : '';
        }
    }).on("changeDate", function(ev) {
        checkin.hide();
    }).data("datepicker");
    var city = $("#allCity").val();
    var params = {
        city: city
    };
    $.post("weakCoverCloud/getDateWithData", params, function(data) {
        var sdata = [];
        for (var i = 0; i < data.length; i++) {
            if (data[i] === today) {
                continue;
            }
            sdata.push(data[i]);
        }
        sdata.push(today);
        $("#date").datepicker("setValues", sdata);
    });

}