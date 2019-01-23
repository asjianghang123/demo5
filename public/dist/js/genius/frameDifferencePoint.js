var mapv;
var allCells;
var sections = [-3, 3];
$(function() {
    getAllCity();
    getDate();
    getChannels();
    toogle("frameDifferencePoint");
    mapv = initMap("mapPoint");
    initMapLeftControl();
    chooseCells();
});

function getDate() {
    $("#date").datepicker({
        format: "yyyy-mm-dd"
    }); //返回日期
    var nowTemp = new Date();
    var year = nowTemp.getFullYear();
    var month = nowTemp.getMonth() + 1;
    var day = nowTemp.getDate();
    var today = year + "-" + month + "-" + day;

    // console.log(today);
    var params = {
        city: getFirstCity()
    };
    $.get("frameDifferencePoint/getDate", params, function(data) {
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
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var checkin = $("#date").datepicker({
        onRender: function(date) {
            return date.valueOf() < now.valueOf() ? "" : "";
        }
    }).on("changeDate", function(ev) {
        checkin.hide();
    }).data("datepicker");
}

function getChannels() {
    $("#channel").multiselect({
        dropRight: true,
        buttonWidth: "100%",
        //enableFiltering: true,
        nonSelectedText: "请选择频段",
        //filterPlaceholder:"搜索",
        nSelectedText: "项被选中",
        includeSelectAllOption: true,
        selectAllText: "全选/取消全选",
        allSelectedText: "已全选",
        maxHeight: 200,
        maxWidth: "100%"
    });
    var url = "frameDifferencePoint/getChannel";
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

function getAllCity() {
    $("#allCity").multiselect({
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
        maxWidth: "100%"
    });
    var url = "frameDifferencePoint/getAllCity";
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
        }
    });
}

function initMap(mapId) {
    var bmap = new BMap.Map(mapId);
    bmap.enableScrollWheelZoom(); // 启用滚轮放大缩小
    bmap.disableDoubleClickZoom(); //禁止双击放大
    // 初始化地图,设置中心点坐标和地图级别
    var arr = setMapPoint();
    bmap.centerAndZoom(new BMap.Point(arr[0], arr[1]), 10);
    var mapv = new Mapv({
        drawTypeControl: false,
        map: bmap // 百度地图的map实例
    });
    return {
        "bmap": bmap,
        "mapv": mapv
    };
}

function initMapLeftControl() {
    //添加小区
    function staticControl() {
        this.defaultAnchor = BMAP_ANCHOR_TOP_RIGHT;
        this.defaultOffset = new BMap.Size(10, 10);
    }
    //创建控件实例
    var staticsCtrl = new staticControl();
    //添加到地图当中
    mapv.bmap.addControl(staticsCtrl);
    //自定义控件
    function LeftControl() {
        this.defaultAnchor = BMAP_ANCHOR_TOP_LEFT;
        this.defaultOffset = new BMap.Size(10, 10);
    }
    //继承Control的API
    LeftControl.prototype = new BMap.Control();
    //初始化控件
    LeftControl.prototype.initialize = function(map) {
        var ul = document.createElement("ul");
        ul.setAttribute("class", "list-group");
        ul.setAttribute("id", "leftControl");
        var li = document.createElement("li");
        li.setAttribute("class", "list-group-item");
        li.textContent = "请滑动鼠标查看小区名";
        ul.appendChild(li);
        //添加DOM元素到地图中
        map.getContainer().appendChild(ul);
        //返回DOM
        return ul;
    };
    //创建控件实例
    var leftCtrl = new LeftControl();
    //添加到地图当中
    mapv.bmap.addControl(leftCtrl);
    //end
}

function doSearch() {
    var l = Ladda.create(document.getElementById("searchBtn"));
    l.start();
    var date = $("#date").val();
    var hour = $("#hour").val();
    var minute = $("#quarter").val();
    var channel = $("#channel").val();
    var citys = $("#allCity").val();

    if (channel == null) {
        layer.open({
            title: "提示",
            content: "请选择频段"
        });
        l.stop();
        return;
    }
    if (citys) {
        $("#last_city").val(citys.join(","));
    } else {
        $("#last_city").val("");
    }
    $("#last_date").val(date);
    $("#last_hour").val(hour);
    $("#last_minute").val(minute);
    $("#last_channel").val(channel.join(","));
    $(".zhaozi").show();
    $(".loadingImg").show();
    var url = "frameDifferencePoint/getCells";
    $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
        data: {
            date: date,
            hour: hour,
            minute: minute,
            channel: channel.join(","),
            citys: citys
        },
        success: function(data) {
            if (data == "") {
                layer.open({
                    title: "提示",
                    content: "暂无数据"
                });
                l.stop();
                $("#map1_zhaozi").hide();
                $("#map1_loadingImg").hide();
                return;
            }
            allCells = data;
            drawMap(data);
            $(".chooseCell").prop("checked", "checked");
            l.stop();
        }
    });
}


function drawMap(data) {
    if (!data) {
        layer.open({
            title: "提示",
            content: "无数据！"
        });
        return;
    }
    mapv.bmap.clearOverlays();
    var returnData = []; // 取城市的点来做示例展示的点数据
    for (var i = 0; i < data.length; i++) {
        var count = (parseInt(data[i].sf1) + parseInt(data[i].sf6)) - (parseInt(data[i].sf2) + parseInt(data[i].sf7));
        var band = data[i].band;
        if (band != 'D' && band != 'E' && band != 'F') {
            band = 'F';
        }
        returnData.push({
            lng: data[i].longitude,
            lat: data[i].latitude,
            count: count,
            dir: data[i].dir - 30,
            band: band,
            cell: data[i].cell
        });
    }
    var mapLayer = new Mapv.Layer({
        mapv: mapv.mapv, // 对应的mapv实例
        zIndex: 1, // 图层层级
        dataType: "point", // 数据类型，点类型
        data: returnData, // 数据
        drawType: "choropleth", // 展示形式
        dataRangeControl: false,
        drawOptions: { // 绘制参数
            size: 20, // 点大小
            unit: "px", // 单位
            type: "siteband",
            // splitList数值表示按数值区间来展示不同颜色的点
            splitList: [{
                end: sections[0],
                color: "blue"
            }, {
                start: sections[0],
                end: sections[1],
                color: "grey"
            }, {
                start: sections[1],
                color: "red"
            }],
            events: {
                //添加小区				
                mousemove: function(e, data) {
                    $("#leftControl").children().remove();
                    var li = "";
                    for (var i = 0; i < data.length; i++) {
                        li += ("<li " + 'class="list-group-item"' + ">" + data[i].cell + "</li>");
                    }
                    $("#leftControl").append(li);
                }
            }
        }
    });
    $("#map1_zhaozi").hide();
    $("#map1_loadingImg").hide();
}

function chooseCells() {
    $(".chooseCell").on("change", function() {
        var checkbox = [];
        $(".chooseCell").each(function() {
            checkbox.push($(this).prop("checked"));
        });
        var returnData = []; // 取城市的点来做示例展示的点数据
        mapv.bmap.clearOverlays();
        var length = allCells.length;
        for (var i = 0; i < length; i++) {
            var count = (parseInt(allCells[i].sf1) + parseInt(allCells[i].sf6)) - (parseInt(allCells[i].sf2) + parseInt(allCells[i].sf7));
            var band = allCells[i].band;
            if (band != 'D' && band != 'E' && band != 'F') {
                band = 'D';
            }
            if (count < sections[0] && checkbox[0]) {
                returnData.push({
                    lng: allCells[i].longitude,
                    lat: allCells[i].latitude,
                    count: count,
                    dir: allCells[i].dir - 30,
                    band: band,
                    cell: allCells[i].cell
                });
            } else if (count >= sections[0] && count < sections[1] && checkbox[1]) {
                returnData.push({
                    lng: allCells[i].longitude,
                    lat: allCells[i].latitude,
                    count: count,
                    dir: allCells[i].dir - 30,
                    band: band,
                    cell: allCells[i].cell
                });
            } else if (count >= sections[1] && checkbox[2]) {
                returnData.push({
                    lng: allCells[i].longitude,
                    lat: allCells[i].latitude,
                    count: count,
                    dir: allCells[i].dir - 30,
                    band: band,
                    cell: allCells[i].cell
                });
            }
        }
        var mapLayer = new Mapv.Layer({
            mapv: mapv.mapv, // 对应的mapv实例
            zIndex: 1, // 图层层级
            dataType: "point", // 数据类型，点类型
            data: returnData, // 数据
            drawType: "choropleth", // 展示形式
            dataRangeControl: false,
            drawOptions: { // 绘制参数
                size: 20, // 点大小
                unit: "px", // 单位
                type: "siteband",
                // splitList数值表示按数值区间来展示不同颜色的点
                splitList: [{
                    end: sections[0],
                    color: "blue"
                }, {
                    start: sections[0],
                    end: sections[1],
                    color: "grey"
                }, {
                    start: sections[1],
                    color: "red"
                }],
            }
        });
    });
}

function setPointByCell() {
    var cell = $("#cell").val();
    var city = $("#last_city").val();
    var hour = $("#last_hour").val();
    var minute = $("#last_minute").val();
    var date = $("#last_date").val();
    var channel = $("#last_channel").val();
    if (date == "" || channel == "") {
        layer.open({
            title: "提示",
            content: "请先进行查询"
        });
        return;
    }
    if (cell == "") {
        layer.open({
            title: "提示",
            content: "请输入小区"
        });
        return;
    }
    var cityArr = [];
    if (city) {
        cityArr = city.split(",");
    }
    var params = {
        citys: cityArr,
        date: date,
        hour: hour,
        minute: minute,
        cell: cell,
        channel: channel
    };
    $.post("frameDifferencePoint/getCell", params, function(data) {
        data = JSON.parse(data);
        //console.log(data);
        if (data == false) {
            layer.open({
                title: "提示",
                content: "所查询的数据中并没有该小区"
            });
            return;
        }
        var point = new BMap.Point(data.longitude, data.latitude);
        mapv.bmap.centerAndZoom(point, 18);
    });
}