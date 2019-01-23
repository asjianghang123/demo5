// var plygons = [];
// var selected = "";
// var bmap = new BMap.Map("scale-map");
//
// // 初始化地图,设置中心点坐标和地图级别
// bmap.centerAndZoom(new BMap.Point(120.602701, 31.807101), 8);
//
// bmap.disableDoubleClickZoom();
//
// bmap.addControl(new BMap.NavigationControl());
//
// bmap.addControl(new BMap.MapTypeControl());
//
// //去除路网
// bmap.setMapStyle({
//     styleJson:[
//         {
//             "featureType": "poi",
//             "elementType": "all",
//             "stylers": {
//                 "color": "#ffffff",
//                 "visibility": "off"
//             }
//         },
//         {
//             "featureType": "road",
//             "elementType": "all",
//             "stylers": {
//                 "color": "#ffffff",
//                 "visibility": "off"
//             }
//         },
//         {
//             "featureType": "background",
//             "elementType": "all",
//             "stylers": {
//                 "color": "#ffffff"
//             }
//         }
//     ]
// });
//
// // 创建地理编码实例
// var myGeo = new BMap.Geocoder();
//
// bmap.addEventListener("click", function(e){
//     myGeo.getLocation(new BMap.Point(e.point.lng, e.point.lat), function(result){
//         if (result){
//             var addComp = result.addressComponents;
//                 for (var city in cityNames) {
//                 if (city == addComp.city) {
//                     var params = {};
//                     if (selected == addComp.city) {
//                         plygons[addComp.city].setFillColor("#fff");
//                         params = {'city': 'province'};
//                         selected = "";
//                         smallBox('scale/meContextNum','#meContextNum');
//                         smallBox('scale/cellNum','#cellNum');
//                         smallBox('scale/slaveNum','#slaveNum');
//                         smallBoxOnAutoKPI('scale/numOnAutoKPI');
//                         smallBox('scale/volteCalls','#volteCalls');
//                     } else {
//                         if (selected != "") {
//                             plygons[selected].setFillColor("#fff");
//                         }
//                         setCityFillColor(addComp.city, cityNames[addComp.city]);
//                         selected = addComp.city;
//                         params = {'city': addComp.city};
//                         smallBox('scale/meContextNumByCity','#meContextNum',params);
//                         smallBox('scale/cellNumByCity','#cellNum',params);
//                         smallBox('scale/slaveNumByCity','#slaveNum',params);
//                         smallBoxOnAutoKPI('scale/numOnAutoKPIByCity',params);
//                         smallBox('scale/volteCallsByCity','#volteCalls',params);
//                     }
//                 }
//             }
//         }
//     });
// });
//
// /*var cityNames = {
//     "常州市": '#ff0000',
//     "无锡市": '#00ff00',
//     "苏州市": '#0000ff',
//     "镇江市": '#f00000',
//     "南通市": '#0f0000',
// };*/
// function getColorCity(){
//     var cityNames = {};
//     $.ajax({
//         url:'scale/citysColor',
//         async:false,
//         type:'get',
//         success:function(data){
//             var cityName = [];
//             for(var i=0; i<data.length; i++){
//                 cityNames[data[i][0]] = data[i][1];
//             }
//         }
//     });
//     return cityNames;
// }
// var cityNames = getColorCity();
// console.log(cityNames);
//
//
// function getBoundary(cityname){
//     var bdary = new BMap.Boundary();
//     bdary.get(cityname, function(rs){ // 异步加载
//         var count = rs.boundaries.length; //行政区域的点有多少个
//         var ply = {};
//         for(var i = 0; i < count; i++){
//             ply = new BMap.Polygon(rs.boundaries[i], {strokeWeight: 2, strokeColor: "#4169e1"}); //建立多边形覆盖物
//         }
//         bmap.addOverlay(ply);  //添加覆盖物
//         plygons[cityname]=ply;
//     });
// }
//
// function setCityFillColor(city,color) {
//     plygons[city].setFillColor(color);
// }
//
// for (var key in cityNames) {
//     getBoundary(key);
// }

var myChart = echarts.init(document.getElementById("scale-map"));
var dataArr;
$.ajax({
    url: "scale/getOption",
    async: false,
    success: function (data) {
        dataArr = data;
    }
});
var option = {
    tooltip: {
        //show: false //不显示提示标签
        formatter: "{b}", //提示标签格式
        backgroundColor: "#ff7f50", //提示标签背景颜色
        textStyle: {color: "#fff"} //提示标签字体颜色
    },
    series: [{
            type: "map",
            mapType: dataArr[0],
            label: {
                normal: {
                    show: true, //显示地市标签
                    textStyle: {color: "#389BB7", fontSize: 18}//省份标签字体颜色
                },
                emphasis: {//对应的鼠标悬浮效果
                    show: true,
                    textStyle: {color: "#3c8dbc"}
                }
            },
            itemStyle: {
                normal: {
                    borderWidth: .5, //区域边框宽度
                    borderColor: "#3c8dbc", //区域边框颜色
                    areaColor: "#ffffff"//区域颜色
                },
                emphasis: {
                    borderWidth: .5,
                    borderColor: "#4b0082",
                    areaColor: "#3c8dbc"
                }
            },
            data: []
        }]
};
// var option = getOption();
myChart.setOption(option);
myChart.on("click", function (params) {
    var name = params.name;
    var data = option.series[0].data;

    if (data.length != 0) {
        if (data[0].name == name) {
            data.splice(0, data.length);
            params = {"city": "province"};
        } else {
            data.splice(0, data.length);
            data.push({name: name, selected: true});
            params = {"city": name};
        }
    } else {
        data.push({name: name, selected: true});
        params = {"city": name};
    }

    myChart.setOption(option);

    if (params.city == "province") {
        smallBox("scale/meContextNum", "#meContextNum");
        smallBox("scale/cellNum", "#cellNum");
        smallBox("scale/slaveNum", "#slaveNum");
        smallBoxOnAutoKPI("scale/numOnAutoKPI");
        smallBox("scale/volteCalls", "#volteCalls");
    } else {
        smallBox("scale/meContextNumByCity", "#meContextNum", params);
        smallBox("scale/cellNumByCity", "#cellNum", params);
        smallBox("scale/slaveNumByCity", "#slaveNum", params);
        smallBoxOnAutoKPI("scale/numOnAutoKPIByCity", params);
        smallBox("scale/volteCallsByCity", "#volteCalls", params);
    }

});

var bsc_version = function (route, block) {
    createChart(route, block);
};

var scaleExport = function () {
    var lscale = Ladda.create(document.getElementById("scaleExport"));
    $.ajax({
        type: "GET",
        url: "scale/scaleExport",
        beforeSend: function () {
            lscale.start();
        },
        success: function (data) {
            lscale.stop();
            download(data);
        }
    });

};
var bsc_type = function (route, block) {
    $.ajax({
        type: "GET",
        url: route,
        //data : {range : "day"},
        dataType: "json",
        beforeSend: function () {
            $(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
        },
        success: function (data) {
            if(data == "nodata") {
                $(block).html("<center>无数据</center>");
                return;
            }
            $(block).highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: "pie"
                },
                title: {
                    text: null
                },
                subtitle: {
                    text: null
                },
                tooltip: {
                    pointFormat: ": <b>{point.y}({point.percentage:.2f} %)</b>"
                },
                plotOptions: {
                    pie: {
                        size: "130px",
                        allowPointSelect: true,
                        cursor: "pointer",
                        dataLabels: {
                            enabled: true,
                            format: "<b>{point.name}</b>: {point.percentage:.2f} %",
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || "black"
                            }
                        },
                        showInLegend: true
                    }
                },
                credits: {
                    enabled: false
                },
                series: [{
                        name: "Brands",
                        colorByPoint: true,
                        data: data.series
                    }]
            });
        }
    });

};

var carrier = function () {
    // Create the chart
    $("#carrier").highcharts({
        chart: {
            type: "pie"
        },
        title: {
            text: null
        },
        subtitle: {
            text: null
        },
        plotOptions: {
            series: {
                dataLabels: {
                    enabled: true,
                    format: "{point.name}: {point.y:.1f}%"
                }
            }
        },

        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
        },
        credits: {
            enabled: false
        },
        series: [{
                name: "Brands",
                colorByPoint: true,
                data: [{
                        name: "D",
                        y: 56,
                        drilldown: "D"
                    }, {
                        name: "E",
                        y: 24,
                        drilldown: "E"
                    }, {
                        name: "F",
                        y: 20,
                        drilldown: "F"
                    }]
            }],
        drilldown: {
            series: [{
                    name: "D",
                    id: "D",
                    data: [
                        ["D1", 24],
                        ["D2", 17],
                        ["D3", 59]
                    ]
                }, {
                    name: "E",
                    id: "E",
                    data: [
                        ["E1", 24],
                        ["E2", 17],
                        ["E3", 59]
                    ]
                }, {
                    name: "F",
                    id: "F",
                    data: [
                        ["F1", 24],
                        ["F2", 17],
                        ["F3", 59]
                    ]
                }]
        }
    });
};
function smallBox(route, block, params) {
    $.get(route, params, function (data) {
        // if(params){
        //     setCityFillColor(params.city, cityNames[params.city]);
        // }
        $(block).html(data);
    });
}
function smallBoxOnAutoKPI(route, params) {
    $.get(route, params, function (data) {
        // if(params != null){
        //     setCityFillColor(params.city, cityNames[params.city])
        // }
        data = JSON.parse(data);
        for (var key in data) {
            if (data[key]) {
                $("#" + key).html(data[key]);
            } else {
                $("#" + key).html(0);
            }

        }
    });
}
function rru_slave(route, block) {
    $.ajax({
        type: "GET",
        url: route,
        //data : {range : "day"},
        dataType: "json",
        beforeSend: function () {
            $(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
        },
        success: function (data) {
            $(block).html("");
            if(data == "nodata") {
                $(block).html("<center>无数据</center>");
                return;
            }
            $(block).highcharts({
                chart: {
                    type: "column"
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: data.category
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: null
                    }
                },
                legend: {
                    enabled: false
                },
                credits: {
                    enabled: false
                },
                series: data.series
            });
        }
    });
}
function createChart(route, block) {
    $.ajax({
        type: "GET",
        url: route,
        //data : {range : "day"},
        dataType: "json",
        beforeSend: function () {
            $(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
        },
        success: function (data) {
            $(block).html("");
            if(data == "nodata") {
                $(block).html("<center>无数据</center>");
                return;
            }
            $(block).highcharts({
                chart: {
                    type: "column"
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: data.category
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: null
                    }
                },
                credits: {
                    enabled: false
                },
                series: data.series
            });
        }
    });
}


var bsc_slave = function (route, block) {
    $.ajax({
        type: "GET",
        url: route,
        //data : {range : "day"},
        dataType: "json",
        beforeSend: function () {
            $(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
        },
        success: function (data) {
            if(data == "nodata") {
                $(block).html("<center>无数据</center>");
                return;
            }
            $(block).highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: "pie"
                },
                title: {
                    text: data.db
                },
                subtitle: {
                    text: null
                },
                tooltip: {
                    pointFormat: ": <b>{point.y}({point.percentage:.2f} %)</b>"
                },
                plotOptions: {
                    pie: {
                        size: "130px",
                        allowPointSelect: true,
                        cursor: "pointer",
                        dataLabels: {
                            enabled: true,
                            format: "<b>{point.name}</b>: {point.percentage:.2f} %",
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || "black"
                            }
                        },
                        showInLegend: true
                    }
                },
                credits: {
                    enabled: false
                },
                series: [{
                        name: "Brands",
                        colorByPoint: true,
                        data: eval('(' + data['data'] + ')').series
                    }]
            });
        }
    });

};

function bsc_slave_city(route, block) {
    $.ajax({
        type: "GET",
        url: route,
        //data : {range : "day"},
        dataType: "json",
        beforeSend: function () {
            $(block).html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">');
        },
        success: function (data) {
            $(block).html("");
            if(data == "nodata") {
                $(block).html("<center>无数据</center>");
                return;
            }
            $(block).highcharts({
                chart: {
                    type: "column"
                },
                title: {
                    text: data.db
                },
                xAxis: {
                    categories: data['data'].category
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: null
                    }
                },
                credits: {
                    enabled: false
                },
                series: data['data'].series
            });
        }
    });
}
function rru_slave_city(route, block) {
    createChart(route, block);
}
function bsc_type_city(route, block) {
    createChart(route, block);
}
jQuery(document).ready(function () {
    toogle("scale");
    bsc_type("scale/bscSiteType", "#bscSiteType");
    //bsc_type("scale/bscSlave", "#bscSlave");
    //bsc_type("scale/bscCA", "#bscCA");
    bsc_type_city("scale/bscSiteTypeCity", "#bscSiteTypeCity");
    //bsc_type_city("scale/bscSlaveCity", "#bscSlaveCity");
    //bsc_type_city("scale/bscCACity", "#bscCACity");
    bsc_version("scale/bscversion_type", "#bscversion_type");
    bsc_version("scale/bscversion_city", "#bscversion_city");
    carrier();
    smallBox("scale/meContextNum", "#meContextNum");
    smallBox("scale/cellNum", "#cellNum");
    smallBox("scale/slaveNum", "#slaveNum");
    smallBoxOnAutoKPI("scale/numOnAutoKPI");
    smallBox("scale/volteCalls", "#volteCalls");
    //rru_slave_city("scale/rruandSlave_city", "#rruandSlave_city");
    //rru_slave("scale/rruandSlave_slave", "#rruandSlave_slave");
    createChart("scale/RRUNum_city", "#RRU_num_city");
    createChart("scale/DUNum_city", "#DU_num_city");
    rru_slave("scale/MMEGI_TAC_num", "#MMEGI_TAC_num");

    bsc_slave("scale/bscSlave", "#bscSlave");
    bsc_slave_city("scale/bscSlaveCity", "#bscSlaveCity");
    bsc_slave_city("scale/rruandSlave_city", "#rruandSlave_city");
    bsc_slave_city("scale/rruandSlave_slave", "#rruandSlave_slave");
    bsc_slave("scale/bscCA", "#bscCA");
    bsc_slave_city("scale/bscCACity", "#bscCACity");
});