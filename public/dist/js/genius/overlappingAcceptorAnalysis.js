var mapv1;
var layerout = null;
$(function() {
    toogle("overlappingAcceptorAnalysis");
    initCitys();
    //setTime();

    mapv1 = initMap("map1");
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
    $.get("overlappingAcceptorAnalysis/getCitys", null, function(data) {
        data = JSON.parse(data);
        var newData = [];
        for (var i in data) {
            var CHCity = data[i].split("-")[0];
            var dataBase = data[i].split("-")[1];
            newData.push({ "label": CHCity, "value": dataBase });
        }
        $("#citys").multiselect("dataprovider", newData);
        setTime();
    });
}

function setTime() {
    $("#date").datepicker({ format: "yyyy-mm-dd" }); //返回日期
    var nowTemp = new Date();
    var year = nowTemp.getFullYear();
    var month = nowTemp.getMonth() + 1;
    var day = nowTemp.getDate();
    var today = year + "-" + month + "-" + day;
    //$("#date").datepicker('setValue', nowTemp);

    var params = {
        dataBase: $("#citys").val()
    };
    $.post("overlappingAcceptorAnalysis/getDataGroupByDate", params, function(data) {
        data = JSON.parse(data);
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
    $("#citys").change(function() {
        var city = $("#citys").val();
        var params = {
            dataBase: city
        };
        $.post("overlappingAcceptorAnalysis/getDataGroupByDate", params, function(data) {
            data = JSON.parse(data);
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

function queryTrail() {
    var date = $("#date").val();
    var cellName = $("#cellName").val();
    var dataBase = $("#citys").val();
    if (!cellName) {
        // alert("");
        layer.open({
            title: "提示",
            content: "请输入需要查询小区！"
        });
        return;
    }

    var params = {
        dataBase: dataBase,
        date: date,
        cellName: cellName
    };
    var queryBtn = Ladda.create(document.getElementById("queryBtn"));
    queryBtn.start();
    $.post("overlappingAcceptorAnalysis/getData", params, function(data) {
        data = JSON.parse(data);
        if (data.targetCell && data.otherCells.length > 0) {
            drawMap(data, null);
            setTableData(data);
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

function initMap(mapId) {
    var bmap = new BMap.Map(mapId);
    bmap.enableScrollWheelZoom(); // 启用滚轮放大缩小
    // 初始化地图,设置中心点坐标和地图级别
    var arr = setMapPoint();
    bmap.centerAndZoom(new BMap.Point(arr[0], arr[1]), 10);
    // bmap.centerAndZoom(new BMap.Point(120.602701, 32.227101), 10);
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
    bmap.addControl(leftCtrl);
    var mapv = new Mapv({
        drawTypeControl: false,
        map: bmap // 百度地图的map实例
    });

    $.ajax({
        type: "GET",
        url: "overlappingAcceptorAnalysis/switchSite",
        dataType: "text",
        beforeSend: function() {
            $("map").html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
        },
        success: function(data) {
            var returnData = JSON.parse(data);
            var vdata = [];
            for (var i = 0; i < returnData.length; i++) {
                vdata.push({
                    cell: returnData[i].cellName,
                    lng: returnData[i].longitude,
                    lat: returnData[i].latitude,
                    count: 5,
                    dir: returnData[i].dir - 30,
                    band: returnData[i].band
                });
            }

            var layer = new Mapv.Layer({
                mapv: mapv, // 对应的mapv实例
                zIndex: 1, // 图层层级
                dataType: "point", // 数据类型，点类型
                data: vdata, // 数据
                drawType: "choropleth", // 展示形式
                dataRangeControl: false,
                drawOptions: { // 绘制参数
                    size: 20, // 点大小
                    unit: "px", // 单位
                    strokeStyle: "gray", // 描边颜色
                    type: "site",
                    // splitList数值表示按数值区间来展示不同颜色的点
                    splitList: [{
                        start: 0,
                        end: 10,
                        color: 'gray'
                    }],
                    events: {
                        mousemove: function(e, data) {
                            //console.log('click', e, data);
                            $("#leftControl").children().remove();
                            var li = "";
                            for (var i = 0; i < data.length; i++) {
                                li += ("<li " + 'class="list-group-item"' + ">" + data[i].cell + "</li>");
                            }
                            //console.log(li);
                            $("#leftControl").append(li);
                        }
                    }
                }
            });
        }
    });
    return { "bmap": bmap, "mapv": mapv };
}


function drawMap(data, ecgi) {
    var targetCell = data.targetCell;
    var otherCells = data.otherCells;
    var vdata = [];
    var count = 30;
    for (var i in otherCells) {
        // count = otherCells[i].ecgi == ecgi ? 100 : 30;
        if ((otherCells[i].ecgi && otherCells[i].ecgi.indexOf(ecgi) >= 0) || (ecgi && ecgi.indexOf(otherCells[i].ecgi) >= 0)) {
            count = 100;
        } else {
            count = 30;
        }
        var lineCount = i < 10 ? "black" : "null";
        vdata.push({
            lng: otherCells[i].longitudeBD,
            lat: otherCells[i].latitudeBD,
            count: count,
            dir: otherCells[i].dir - 30,
            band: otherCells[i].band,
            master: false,
            scell: otherCells[i].cellName,
            lineCount: lineCount
        });
    }
    vdata.push({
        lng: targetCell.longitudeBD,
        lat: targetCell.latitudeBD,
        count: -1,
        dir: targetCell.dir - 30,
        band: targetCell.band,
        master: true,
        scell: targetCell.cellName
    });
    //console.log(vdata)
    var points = [];

    for (var i = 0; i < vdata.length; i++) {
        points.push(new BMap.Point(vdata[i].lng, vdata[i].lat));
    }

    mapv1.bmap.setViewport(points);
    layerout = new Mapv.Layer({
        mapv: mapv1.mapv, // 对应的mapv实例
        zIndex: 1, // 图层层级
        dataType: "point", // 数据类型，点类型
        data: vdata, // 数据
        drawType: "choropleth", // 展示形式
        dataRangeControl: false,
        drawOptions: { // 绘制参数
            size: 20, // 点大小
            unit: "px", // 单位
            type: "switchout",
            // splitList数值表示按数值区间来展示不同颜色的点
            splitList: [{
                end: 0,
                color: "green"
            }, {
                start: 0,
                end: 50,
                color: "blue"
            }, {
                start: 50,
                end: 60,
                color: "red"
            }, {
                start: 60,
                end: 90,
                color: "gray"
            }, {
                start: 90,
                end: 120,
                color: "yellow"
            }],
            events: {
                click: function(e, data1) {
                    var cells = [];
                    var detailData = [];
                    for (var i = 0; i < data1.length; i++) {
                        for (var j = 0; j < data.otherCells.length; j++) {
                            if (data.otherCells[j].cellName == data1[i].scell) {
                                cells.push(data.otherCells[j].ecgi);
                                continue;
                            }
                        }
                    }
                    for (var i = 0; i < cells.length; i++) {
                        for (var j = 0; j < data.tableData.length; j++) {
                            if (cells[i].indexOf(data.tableData[j].ecgi) >= 0 || (data.tableData[j].ecgi && data.tableData[j].ecgi.indexOf(cells[i]) >= 0)) {
                                detailData.push(data.tableData[j]);
                                continue;
                            }
                        }
                    }
                    $("#bMapTable").grid("destroy", true, true);
                    $("#bMapTable").grid({
                        columns: [
                            { "text": "dateId", "field": "dateId", "height": 50, "width": 120 },
                            { "text": "ecgi", "field": "ecgi", "height": 50, "width": 150 },
                            { "text": "ecgiNc", "field": "ecgiNc", "height": 50, "width": 150 },
                            { "text": "mr_LteNcEarfcn", "field": "mr_LteNcEarfcn", "height": 50, "width": 150 },
                            { "text": "mr_LteNcPci", "field": "mr_LteNcPci", "height": 50, "width": 150 },
                            { "text": "intensity", "field": "intensity", "height": 50, "width": 120 },
                            { "text": "mr_LteScRSRP", "field": "mr_LteScRSRP", "height": 50, "width": 120 },
                            { "text": "mr_LteNcRSRP", "field": "mr_LteNcRSRP", "height": 50, "width": 120 }
                        ],
                        dataSource: detailData,
                        pager: { limit: 10, sizes: [10, 20, 50, 100] },
                        autoScroll: true,
                        uiLibrary: "bootstrap"
                    });
                    $("#myModal").modal({
                        keyboard: false
                    });
                }
            }
        }
    });
    var layer = new Mapv.Layer({
        mapv: mapv1.mapv, // 对应的mapv实例
        zIndex: 1, // 图层层级
        dataType: "point", // 数据类型，点类型
        data: vdata, // 数据
        drawType: "choropleth", // 展示形式
        dataRangeControl: false,
        drawOptions: { // 绘制参数
            size: 20, // 点大小
            unit: "px", // 单位
            strokeStyle: "gray", // 描边颜色
            type: "site",
            // splitList数值表示按数值区间来展示不同颜色的点
            splitList: [{
                start: 0,
                end: 10,
                color: "gray"
            }],
            events: {
                mousemove: function(e, data) {
                    //console.log('click', e, data);
                    $("#leftControl").children().remove();
                    var li = "";
                    for (var i = 0; i < data.length; i++) {
                        li += ("<li " + 'class="list-group-item"' + ">" + data[i].scell + "</li>");
                    }
                    //console.log(li);
                    $("#leftControl").append(li);
                }
            }
        }
    });
}

function drawMap1(data, ecgi) {
    var targetCell = data.targetCell;
    var otherCells = data.otherCells;
    var vdata = [];
    var count = 30;
    for (var i in otherCells) {
        // count = otherCells[i].ecgi == ecgi ? 100 : 30;
        if ((otherCells[i].ecgi && otherCells[i].ecgi.indexOf(ecgi) >= 0) || (ecgi && ecgi.indexOf(otherCells[i].ecgi) >= 0)) {
            count = 100;
        } else {
            count = 30;
        }
        vdata.push({
            lng: otherCells[i].longitudeBD,
            lat: otherCells[i].latitudeBD,
            count: count,
            dir: otherCells[i].dir - 30,
            band: otherCells[i].band,
            master: false,
            scell: otherCells[i].cellName,
            lineCount: "null"
        });
    }
    vdata.push({
        lng: targetCell.longitudeBD,
        lat: targetCell.latitudeBD,
        count: -1,
        dir: targetCell.dir - 30,
        band: targetCell.band,
        master: true,
        scell: targetCell.cellName
    });
    layerout = new Mapv.Layer({
        mapv: mapv1.mapv, // 对应的mapv实例
        zIndex: 1, // 图层层级
        dataType: "point", // 数据类型，点类型
        data: vdata, // 数据
        drawType: "choropleth", // 展示形式
        dataRangeControl: false,
        drawOptions: { // 绘制参数
            size: 20, // 点大小
            unit: "px", // 单位
            type: "switchout",
            // splitList数值表示按数值区间来展示不同颜色的点
            splitList: [{
                end: 0,
                color: "green"
            }, {
                start: 0,
                end: 50,
                color: "blue"
            }, {
                start: 50,
                end: 60,
                color: "red"
            }, {
                start: 60,
                end: 90,
                color: "gray"
            }, {
                start: 90,
                end: 120,
                color: "yellow"
            }]
        }
    });
}

function setTableData(data) {
    var fieldArr = [];
    var text = "ecgi,ecgiNc,mr_LteNcEarfcn,mr_LteNcPci,intensity,mr_LteScRSRP,mr_LteNcRSRP";
    var textArr = text.split(",");
    for (var i in textArr) {
        fieldArr[fieldArr.length] = { field: textArr[fieldArr.length], title: textArr[fieldArr.length], width: 150 };
    }
    $("#detailTable").grid("destroy", true, true);
    var grid = $("#detailTable").grid({
        columns: fieldArr,
        dataSource: data.tableData,
        pager: { limit: 10, sizes: [10, 20, 50, 100] },
        autoScroll: true,
        uiLibrary: "bootstrap",
        primaryKey: "ecgi",
        autoLoad: true
    });
    grid.on("rowSelect", function(e, $row, id, record) {
        drawMap1(data, record.ecgi);
    });
}

function exportData() {
    var date = $("#date").val();
    var cellName = $("#cellName").val();
    var dataBase = $("#citys").val();
    if (!cellName) {
        // alert("");
        layer.open({
            title: "提示",
            content: "请输入需要查询小区！"
        });
        return;
    }

    var params = {
        dataBase: dataBase,
        date: date,
        cellName: cellName
    };
    var exportBtn = Ladda.create(document.getElementById("exportBtn"));
    exportBtn.start();
    $.post("overlappingAcceptorAnalysis/exportData", params, function(data) {
        exportBtn.stop();
        data = JSON.parse(data);
        download(data.filename);
    });
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
    /*var evt = document.createEvent("HTMLEvents");
     evt.initEvent("click", false, false);
     aLink.dispatchEvent(evt);*/
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