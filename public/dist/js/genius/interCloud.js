var mapv1;
var allData;
$(function() {
    toogle('interCloud');
    initDate();
    getAllCity();
    getChannels();
    mapv1 = initMap("map");

});

function initDate() {
    $("#date").datepicker({
        format: 'yyyy-mm-dd'
    }); //返回日期
    var nowTemp = new Date();
    var year = nowTemp.getFullYear();
    var month = nowTemp.getMonth() + 1;
    var day = nowTemp.getDate();
    var today = year + '-' + month + '-' + day;

    console.log(today);
    var params = {
        city: getFirstCity()
    };
    $.get('interCloud/interfepointDate', params, function(data) {
        var sdata = [];
        for (var i = 0; i < data.length; i++) {
            if (data[i] === today) {
                continue;
            }
            sdata.push(data[i]);
        }
        sdata.push(today);
        $("#date").datepicker('setValues', sdata);
    });
    $('#allCity').change(function() {
        var city = $('#allCity').val();
        var params = {
            city: city
        };
        $.get('interCloud/interfepointDate', params, function(data) {
            var sdata = [];
            for (var i = 0; i < data.length; i++) {
                if (data[i] === today) {
                    continue;
                }
                sdata.push(data[i]);
            }
            sdata.push(today);
            $("#date").datepicker('setValues', sdata);
        });
    });

    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
    var checkin = $('#date').datepicker({
        onRender: function(date) {
            return date.valueOf() < now.valueOf() ? '' : '';
        }
    }).on('changeDate', function(ev) {
        checkin.hide();
    }).data('datepicker');
}

function initMap(mapId) {
    var bmap = new BMap.Map(mapId);
    bmap.enableScrollWheelZoom(); // 启用滚轮放大缩小
    // 初始化地图,设置中心点坐标和地图级别
    var arr = setMapPoint();
    bmap.centerAndZoom(new BMap.Point(arr[0], arr[1]), 10);
    bmap.setMinZoom(8); //设置缩放级别
    bmap.setMaxZoom(18); //设置缩放级别
    var mapv = new Mapv({
        drawTypeControl: false,
        map: bmap // 百度地图的map实例
    });
    return { "bmap": bmap, "mapv": mapv };
}

function getChannels() {
    $('#channel').multiselect({
        dropRight: true,
        buttonWidth: 200,
        //enableFiltering: true,
        nonSelectedText: '请选择频段',
        //filterPlaceholder:'搜索',
        nSelectedText: '项被选中',
        includeSelectAllOption: true,
        selectAllText: '全选/取消全选',
        allSelectedText: '已全选',
        maxHeight: 200,
        maxWidth: '100%'
    });

    var url = "interCloud/interCloudChannel";
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
            $('#channel').multiselect('dataprovider', newOptions);
        }
    });
};



function drawMap() {
    var S = Ladda.create(document.getElementById('search'));
    S.start();
    // var returnData = []; // 取城市的点来做示例展示的点数据

    var date = $('#date').val();
    var hour = $('#hour').val();
    var minute = $('#minute').val();
    var channel = $('#channel').val();
    var citys = $('#allCity').val();


    if (date == '' || hour == '' || minute == '' || channel == '') {
        S.stop();
        //alert('请用户输入选择信息');
        layer.open({
            title: '提示',
            content: '请用户输入选择信息'
        });
        return;
    }

    var params = {
        date: date,
        hour: hour,
        minute: minute,
        channel: channel
    };
    var url = "interCloud/interCloudCells";
    $.ajax({
        type: "GET",
        url: url,
        dataType: "json",
        data: {
            date: date,
            hour: hour,
            minute: minute,
            channel: channel ? channel.join(",") : "",
            citys: citys
        },
        success: function(data) {
            S.stop();
            if (data) {
                allData = data;
                drawMapPoint();
            } else {
                allData = null;
                layer.open({
                    title: "提示",
                    content: "没有数据！"
                });
            }
        }
    });
};

function drawMapPoint() {
    if (!allData) {
        layer.open({
            title: "提示",
            content: "没有数据！"
        });
    } else {
        var reBtn = Ladda.create(document.getElementById('refreshButton'));
        reBtn.start();
        mapv1.bmap.clearOverlays();
        var returnData = [];
        var length = allData.length;
        var dimension = $("#dimension").val();
        for (var i = 0; i < length; i++) {
            returnData.push({
                lng: allData[i].longitude,
                lat: allData[i].latitude,
                count: allData[i][dimension]
            });
        }
        var mapLayer = new Mapv.Layer({
            mapv: mapv1.mapv, // 对应的mapv实例
            zIndex: 1, // 图层层级
            dataType: 'point', // 数据类型mapv，点类型
            data: returnData, // 数据
            drawType: 'density', // 展示形式
            dataRangeControl: true,
            drawOptions: { // 绘制参数
                type: "rect", // 网格类型，方形网格或蜂窝形
                size: 4, // 网格大小
                unit: 'px', // 单位
                opacity: '0.5',
                label: { // 是否显示文字标签
                    show: true,
                },
                splitList: [{
                    end: -120,
                    color: 'blue'
                }, {
                    start: -120,
                    end: -110,
                    color: 'green'
                }, {
                    start: -110,
                    end: -105,
                    color: 'lime'
                }, {
                    start: -105,
                    end: -100,
                    color: 'yellow'
                }, {
                    start: -100,
                    end: -90,
                    color: 'magenta'
                }, {
                    start: -90,
                    color: 'red'
                }],
                events: {
                    click: function(e, data) {
                        console.log('click', e, data)
                    }
                }
            }
        });
        reBtn.stop();
    }
}


function getAllCity() {
    $('#allCity').multiselect({
        dropRight: true,
        buttonWidth: 200,
        //enableFiltering: true,
        nonSelectedText: '请选择城市',
        //filterPlaceholder:'搜索',
        nSelectedText: '项被选中',
        includeSelectAllOption: true,
        selectAllText: '全选/取消全选',
        allSelectedText: '已选中所有平台类型',
        maxHeight: 200,
        maxWidth: '100%'
    });
    var url = "interCloud/getAllCity";
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
            $('#allCity').multiselect('dataprovider', newOptions);
        }
    });
}