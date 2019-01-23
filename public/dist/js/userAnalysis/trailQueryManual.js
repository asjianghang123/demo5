var mapv;
$(function() {
    toogle("trailQueryManual");
    initCitys();
    mapv = initMap("map1");

});

function initCitys() {
    $("#citys").multiselect({
        //dropRight: true,
        buttonWidth: "100%",
        //enableFiltering: true,
        nonSelectedText: "选择城市",
        //filterPlaceholder:"搜索",
        nSelectedText: "项被选中",
        includeSelectAllOption: true,
        selectAllText: "全选/取消全选",
        allSelectedText: "已选中所有城市",
        maxHeight: 200,
        maxWidth: "100%"
    });
    $.get("trailQueryManual/getCitys", null, function(data) {
        data = JSON.parse(data);
        $("#citys").multiselect("dataprovider", data);
        setTime();
    });
}

function setTime() {
    $("#startDate").datepicker({ format: "yyyy-mm-dd" }); //返回日期
    $("#endDate").datepicker({ format: "yyyy-mm-dd" }); //返回日期
    var nowTemp = new Date();
    var year = nowTemp.getFullYear();
    var month = nowTemp.getMonth() + 1;
    var day = nowTemp.getDate();
    var today = year + "-" + month + "-" + day;
    //$("#date").datepicker("setValue", nowTemp);

    var params = {
        dataBase: $("#citys").val()
    };
    $.post("trailQueryManual/getDataGroupByDate", params, function(data) {
        data = JSON.parse(data);
        var sdata = [];
        for (var i = 0; i < data.length; i++) {
            if (data[i] === today) {
                continue;
            }
            sdata.push(data[i]);
        }
        sdata.push(today);
        $("#startDate").datepicker("setValues", sdata);
        $("#endDate").datepicker("setValues", sdata);
    });
    $("#citys").change(function() {
        var city = $("#citys").val();
        var params = {
            dataBase: city
        };
        $.post("trailQueryManual/getDataGroupByDate", params, function(data) {
            data = JSON.parse(data);
            var sdata = [];
            for (var i = 0; i < data.length; i++) {
                if (data[i] === today) {
                    continue;
                }
                sdata.push(data[i]);
            }
            sdata.push(today);
            $("#startDate").datepicker("setValues", sdata);
            $("#endDate").datepicker("setValues", sdata);
        });
    });
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var checkin = $("#startDate").datepicker({
        onRender: function(date) {
            return date.valueOf() < now.valueOf() ? '' : '';
        }
    }).on("changeDate", function(ev) {
        checkin.hide();
    }).data("datepicker");
    var checkout = $("#endDate").datepicker({
        onRender: function(date) {
            return date.valueOf() < now.valueOf() ? '' : '';
        }
    }).on("changeDate", function(ev) {
        checkout.hide();
    }).data("datepicker");
}

function queryTrail() {
    var city = $("#citys").val();
    var startDate = $("#startDate").val();
    var endDate = $("#endDate").val();
    var user = $("#user_query").val();
    if (startDate > endDate) {
        layer.open({
            title: "提示",
            content: "结束日期不能早于起始日期！"
        });
        return;
    }
    if (!user) {
        layer.open({
            title: "提示",
            content: "请输入需要查询的用户信息！"
        });
        return;
    }

    var params = {
        city: city,
        startDate: startDate,
        endDate: endDate,
        user: user
    };
    var queryBtn = Ladda.create(document.getElementById("queryBtn"));
    queryBtn.start();
    $.post("trailQueryManual/getTrailData", params, function(data) {
        data = JSON.parse(data);
        if (data.length > 0) {
            setMap(data);
        } else {
            layer.open({
                title: "提示",
                content: "没有轨迹数据！"
            });
        }
        queryBtn.stop();

    });
}

function setMap(data) {

    //console.log(data);
    // 百度地图API功能
    var map = mapv.bmap;
    map.centerAndZoom(new BMap.Point(data[0].longitudeBD, data[0].latitudeBD), 16);
    map.enableScrollWheelZoom();

    //起点
    var startPoint = new BMap.Point(data[0].longitudeBD, data[0].latitudeBD);
    //创建圆对象
    var circle = new BMap.Circle(startPoint, 50, {
        strokeColor: "blue",
        strokeWeight: 1,
        fillColor: "blue",
        fillOpacity: 0.8
    });
    //画到地图上面
    map.addOverlay(circle);

    //终点
    var count = data.length;
    var endPoint = new BMap.Point(data[count - 1].longitudeBD, data[count - 1].latitudeBD);
    //创建圆对象
    var circle1 = new BMap.Circle(endPoint, 50, {
        strokeColor: "red",
        strokeWeight: 1,
        fillColor: "red",
        fillOpacity: 0.8
    });
    //画到地图上面
    map.addOverlay(circle1);


    var points = [];
    for (var i in data) {
        points.push(new BMap.Point(data[i].longitudeBD, data[i].latitudeBD));
    }
    var polyline = new BMap.Polyline(points, { strokeColor: "#110E91", strokeWeight: 2 }); //创建折线
    map.addOverlay(polyline); //增加折线
    addArrow(polyline, 8, 20);
    // map.addEventListener("zoomend", function() {
    //     map.clearOverlays();
    //     map.addOverlay(polyline);
    //     addArrow(polyline, 8, 20);
    // });

}

function addArrow(polyline, length, angleValue) { //绘制箭头的函数
    var map = mapv.bmap;
    var linePoint = polyline.getPath(); //线的坐标串
    var arrowCount = linePoint.length;
    for (var i = 1; i < arrowCount; i++) { //在拐点处绘制箭头
        var pixelStart = map.pointToPixel(linePoint[i - 1]);
        var pixelEnd = map.pointToPixel(linePoint[i]);
        //var angle=angleValue;//箭头和主线的夹角
        var angle = angleValue * Math.PI / 180;
        var r = length; // r/Math.sin(angle)代表箭头长度
        var delta = 0; //主线斜率，垂直时无斜率
        var param = 0; //代码简洁考虑
        var pixelTemX, pixelTemY; //临时点坐标
        var pixelX, pixelY, pixelX1, pixelY1; //箭头两个点
        if (pixelEnd.x - pixelStart.x == 0) { //斜率不存在是时
            pixelTemX = pixelEnd.x;
            if (pixelEnd.y > pixelStart.y) {
                pixelTemY = pixelEnd.y - r;
            } else {
                pixelTemY = pixelEnd.y + r;
            }
            //已知直角三角形两个点坐标及其中一个角，求另外一个点坐标算法
            pixelX = pixelTemX - r * Math.tan(angle);
            pixelX1 = pixelTemX + r * Math.tan(angle);
            pixelY = pixelY1 = pixelTemY;
        } else { //斜率存在时
            delta = (pixelEnd.y - pixelStart.y) / (pixelEnd.x - pixelStart.x);
            param = Math.sqrt(delta * delta + 1);

            if ((pixelEnd.x - pixelStart.x) < 0) { //第二、三象限
                pixelTemX = pixelEnd.x + r / param;
                pixelTemY = pixelEnd.y + delta * r / param;
            } else { //第一、四象限
                pixelTemX = pixelEnd.x - r / param;
                pixelTemY = pixelEnd.y - delta * r / param;
            }
            //已知直角三角形两个点坐标及其中一个角，求另外一个点坐标算法
            pixelX = pixelTemX + Math.tan(angle) * r * delta / param;
            pixelY = pixelTemY - Math.tan(angle) * r / param;

            pixelX1 = pixelTemX - Math.tan(angle) * r * delta / param;
            pixelY1 = pixelTemY + Math.tan(angle) * r / param;
        }

        var pointArrow = map.pixelToPoint(new BMap.Pixel(pixelX, pixelY));
        var pointArrow1 = map.pixelToPoint(new BMap.Pixel(pixelX1, pixelY1));
        var Arrow = new BMap.Polyline([
            pointArrow,
            linePoint[i],
            pointArrow1
        ], { strokeColor: "#110E91", strokeWeight: 2 });
        map.addOverlay(Arrow);
    }
}

function initMap(mapId) {
    var bmap = new BMap.Map(mapId);
    bmap.enableScrollWheelZoom(); // 启用滚轮放大缩小
    // 初始化地图,设置中心点坐标和地图级别
    var arr = setMapPoint();
    bmap.centerAndZoom(new BMap.Point(arr[0], arr[1]), 10);
    //自定义控件
    function staticControl() {
        this.defaultAnchor = BMAP_ANCHOR_TOP_RIGHT;
        this.defaultOffset = new BMap.Size(10, 10);
    }
    //继承Control的API
    staticControl.prototype = new BMap.Control();
    //创建控件实例
    var staticsCtrl = new staticControl();
    //添加到地图当中
    bmap.addControl(staticsCtrl);

    //自定义控件
    function LeftControl() {
        this.defaultAnchor = BMAP_ANCHOR_TOP_LEFT;
        this.defaultOffset = new BMap.Size(10, 10);
    }
    //继承Control的API
    LeftControl.prototype = new BMap.Control();
    //初始化控件
    LeftControl.prototype.initialize = function(map) {
            var ul = document.createElement('ul');
            ul.setAttribute('class', 'list-group');
            ul.setAttribute('id', 'leftControl');
            var li = document.createElement('li');
            li.setAttribute('class', 'list-group-item');
            li.textContent = '请滑动鼠标查看小区名'
            ul.appendChild(li);
            //添加DOM元素到地图中
            map.getContainer().appendChild(ul);
            //返回DOM
            return ul;
        }
        //创建控件实例
    var leftCtrl = new LeftControl();
    //添加到地图当中
    bmap.addControl(leftCtrl);

    var mapv = new Mapv({
        drawTypeControl: false,
        map: bmap // 百度地图的map实例
    });
    $.ajax({
        type: "POST",
        url: "trailQueryManual/getAllSite",
        dataType: "text",
        success: function(data) {
            var returnData = JSON.parse(data);
            allSiteData = [];
            for (var i = 0; i < returnData.length; i++) {
                allSiteData.push({
                    cell: returnData[i].cellName,
                    lng: returnData[i].longitude,
                    lat: returnData[i].latitude,
                    count: 5,
                    dir: returnData[i].dir - 30,
                    band: returnData[i].band,
                });
            }
            var layer = new Mapv.Layer({
                mapv: mapv, // 对应的mapv实例
                zIndex: 1, // 图层层级
                dataType: 'point', // 数据类型，点类型
                data: allSiteData, // 数据
                drawType: 'choropleth', // 展示形式
                dataRangeControl: false,
                drawOptions: { // 绘制参数
                    size: 20, // 点大小
                    unit: 'px', // 单位
                    strokeStyle: 'gray', // 描边颜色
                    type: 'site',
                    // splitList数值表示按数值区间来展示不同颜色的点
                    splitList: [{
                        start: 0,
                        end: 10,
                        color: 'gray'
                    }],
                    events: {
                        mousemove: function(e, data) {
                            $("#leftControl").children().remove();
                            var li = '';
                            for (var i = 0; i < data.length; i++) {
                                li += ("<li " + 'class="list-group-item"' + ">" + data[i].cell + "</li>");
                            }
                            $("#leftControl").append(li);
                        }
                    }
                }
            });
        }
    });

    return {
        "bmap": bmap,
        "mapv": mapv
    };
}