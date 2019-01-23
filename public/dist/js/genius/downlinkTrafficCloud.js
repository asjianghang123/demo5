var mapv2;
var layerin = null;
var cells;
$(function() {
    toogle("downlinkTrafficCloud");
    initCitys();
    setTime();

    mapv2 = initMap("map2");
    //initYestoadyData();
    chooseCells();
});

function initCitys() {
    $("#citys").multiselect({
        //dropRight: true,
        buttonWidth: "100%",
        //enableFiltering: true,
        nonSelectedText: "选择城市",
        //filterPlaceholder:'搜索',
        nSelectedText: "项被选中",
        includeSelectAllOption: true,
        selectAllText: "全选/取消全选",
        allSelectedText: "已选中所有城市",
        maxHeight: 200,
        maxWidth: "100%"
    });
    $.get("downlinkTrafficCloud/getCitys", null, function(data) {
        data = JSON.parse(data);
        $("#citys").multiselect("dataprovider", data);
    });
}

function setTime() {
    $("#date").datepicker({ format: "yyyy-mm-dd" }); //返回日期
    var nowTemp = new Date();
    var yestoday = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate() - 1, 0, 0, 0, 0);
    $("#date").datepicker("setValue", yestoday);
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var checkin = $("#date").datepicker({
        onRender: function(date) {
            return date.valueOf() < now.valueOf() ? "" : "";
        }
    }).on("changeDate", function(ev) {
        checkin.hide();
    }).data("datepicker");
}

function initMap(mapId) {
    var bmap = new BMap.Map(mapId);
    bmap.enableScrollWheelZoom(); // 启用滚轮放大缩小
    // 初始化地图,设置中心点坐标和地图级别
    var arr = setMapPoint();
    bmap.centerAndZoom(new BMap.Point(arr[0], arr[1]), 10);
    // bmap.centerAndZoom(new BMap.Point(120.602701, 32.227101), 10);
    //bmap.addControl(new BMap.NavigationControl());
    //bmap.addControl(new BMap.MapTypeControl());
    var mapv = new Mapv({
        drawTypeControl: false,
        map: bmap // 百度地图的map实例
    });
    return { "bmap": bmap, "mapv": mapv };
}

function initYestoadyData() {
    //var city = "changzhou,nantong,suzhou,wuxi,zhenjiang";
    var city = "";
    var date = $("#date").val();

    var params = {
        city: city,
        date: date
    };
    var queryBtn = Ladda.create(document.getElementById("queryBtn"));
    queryBtn.start();
    $(".zhaozi").show();
    $(".loadingImg").show();
    $.post("downlinkTrafficCloud/getData", params, function(data) {
        data = JSON.parse(data);
        if (data.length > 0) {
            drawMap2(data);
        } else {
            // alert("");
            layer.open({
                title: "提示",
                content: "昨天没有数据！"
            });
        }
        queryBtn.stop();
    });
}

function getData() {
    var city = $("#citys").val();
    var date = $("#date").val();
    if (!city) {
        // alert("");
        layer.open({
            title: "提示",
            content: "请选择要查询的城市"
        });
        return;
    }
    if (!date) {
        // alert("");
        layer.open({
            title: "提示",
            content: "请选择要查询的日期"
        });
        return;
    }
    var params = {
        city: city.join(","),
        date: date
    };
    $("#last_city").val(city.join(","));
    $("#last_date").val(date);
    var queryBtn = Ladda.create(document.getElementById("queryBtn"));
    queryBtn.start();
    $(".zhaozi").show();
    $(".loadingImg").show();
    $.post("downlinkTrafficCloud/getData", params, function(data) {
        data = JSON.parse(data);
        if (data.length > 0) {
            drawMap2(data);
            $(".chooseCell").prop("checked", "checked");
            cells = data;
        } else {
            // alert("");
            layer.open({
                title: "提示",
                content: "没有数据！"
            });
        }
        queryBtn.stop();
    });
}

function drawMap2(data) {
    var returnData = []; // 取城市的点来做示例展示的点数据
    mapv2.bmap.clearOverlays();
    for (var i = 0; i < data.length; i++) {
        returnData.push({
            lng: data[i].longitude,
            lat: data[i].latitude,
            count: data[i].空口下行业务量GB,
            dir: data[i].dir - 30,
            band: data[i].band,
            cell: data[i].cell
        });
    }
    var layer = new Mapv.Layer({
        mapv: mapv2.mapv, // 对应的mapv实例
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
                start: 0,
                end: 1,
                color: "blue"
            }, {
                start: 1,
                end: 10,
                color: "green"
            }, {
                start: 10,
                end: 20,
                color: "lime"
            }, {
                start: 20,
                end: 50,
                color: "yellow"
            }, {
                start: 50,
                end: 100,
                color: "magenta"
            }, {
                start: 100,
                color: "red"
            }],
            events: {
                click: function(e, data) {
                    setDetailTable(data);
                }
            }
        }
    });
    $("#map2_zhaozi").hide();
    $("#map2_loadingImg").hide();
}

function setPointByCell() {
    var cell = $("#cell").val();
    var city = $("#last_city").val();
    var date = $("#last_date").val();
    if (city == "" || date == "") {
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
    var params = {
        city: city,
        date: date,
        cell: cell
    };
    $.post("downlinkTrafficCloud/getCell", params, function(data) {
        data = JSON.parse(data);
        // console.log(data);
        if (data == false) {
            layer.open({
                title: "提示",
                content: "所查询的数据中并没有该小区"
            });
            return;
        }
        var point = new BMap.Point(data.longitude, data.latitude);
        mapv2.bmap.centerAndZoom(point, 18);
    });
}

function setDetailTable(data) {
    $("#detail_modal").modal();
    var fieldArr = [];
    var text = "cell,count,lng,lat,band,dir";
    var textArr = text.split(",");
    for (var i in textArr) {
        var title = textArr[i];
        if (textArr[i] == "count") {
            title = "下行业务量（GB）";
        }
        fieldArr[i] = { field: textArr[i], title: title, width: 140 };
    }
    $("#cellDetailTable").grid("destroy", true, true);
    var grid = $("#cellDetailTable").grid({
        columns: fieldArr,
        dataSource: data,
        params: {},
        pager: { limit: 10, sizes: [10, 20, 50, 100] },
        autoScroll: true,
        uiLibrary: "bootstrap",
    });
}

function chooseCells() {
    $(".chooseCell").on("change", function() {
        var checkbox = [];
        $(".chooseCell").each(function() {
            checkbox.push($(this).prop("checked"));
        });
        var returnData = []; // 取城市的点来做示例展示的点数据
        mapv2.bmap.clearOverlays();
        for (var i = 0; i < cells.length; i++) {
            var count = cells[i].空口下行业务量GB;
            if (count < 1 && checkbox[0]) {
                returnData.push({
                    lng: cells[i].longitude,
                    lat: cells[i].latitude,
                    count: count,
                    dir: cells[i].dir - 30,
                    band: cells[i].band,
                    cell: cells[i].cell
                });
            } else if (count >= 1 && count < 10 && checkbox[1]) {
                returnData.push({
                    lng: cells[i].longitude,
                    lat: cells[i].latitude,
                    count: count,
                    dir: cells[i].dir - 30,
                    band: cells[i].band,
                    cell: cells[i].cell
                });
            } else if (count >= 10 && count < 20 && checkbox[2]) {
                returnData.push({
                    lng: cells[i].longitude,
                    lat: cells[i].latitude,
                    count: count,
                    dir: cells[i].dir - 30,
                    band: cells[i].band,
                    cell: cells[i].cell
                });
            } else if (count >= 20 && count < 50 && checkbox[3]) {
                returnData.push({
                    lng: cells[i].longitude,
                    lat: cells[i].latitude,
                    count: count,
                    dir: cells[i].dir - 30,
                    band: cells[i].band,
                    cell: cells[i].cell
                });
            } else if (count >= 50 && count < 100 && checkbox[4]) {
                returnData.push({
                    lng: cells[i].longitude,
                    lat: cells[i].latitude,
                    count: count,
                    dir: cells[i].dir - 30,
                    band: cells[i].band,
                    cell: cells[i].cell
                });
            } else if (count >= 100 && checkbox[5]) {
                returnData.push({
                    lng: cells[i].longitude,
                    lat: cells[i].latitude,
                    count: count,
                    dir: cells[i].dir - 30,
                    band: cells[i].band,
                    cell: cells[i].cell
                });
            }
        }
        var layer = new Mapv.Layer({
            mapv: mapv2.mapv, // 对应的mapv实例
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
                    start: 0,
                    end: 1,
                    color: "blue"
                }, {
                    start: 1,
                    end: 5,
                    color: "green"
                }, {
                    start: 5,
                    end: 10,
                    color: "lime"
                }, {
                    start: 10,
                    end: 15,
                    color: "yellow"
                }, {
                    start: 15,
                    end: 20,
                    color: "magenta"
                }, {
                    start: 20,
                    color: "red"
                }],
                events: {
                    click: function(e, data) {
                        setDetailTable(data);
                    }
                }
            }
        });
    });
}