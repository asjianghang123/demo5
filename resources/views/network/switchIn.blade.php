@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>
        邻区切入分析
    </h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i>地理呈现</li>
        <li>邻区分析</li>
        <li class='active'>邻区切入分析</li>
    </ol>
</section>
@endsection 
@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">查询条件</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-wrench"></i></button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#">Print Chart</a></li>
                                <li class="divider"></li>
                                <li><a href="#">Download PNG img</a></li>
                                <li><a href="#">Download JPEG img</a></li>
                                <li><a href="#">Download PDF document</a></li>
                                <li><a href="#">Download SVG vector img</a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <form class="form-horizontal" role="form" id="queryForm">
                        <div class="form-group">
                            <label class="col-sm-1 control-label">日期</label>
                            <div class="col-sm-3">
                                <input id="date" class="form-control" type="text" value=""/>
                            </div>
                            <label class="col-sm-1 control-label">小区</label>
                            <div class="col-sm-3">
                                <input id="cell" class="form-control" type="text" value=""/>
                            </div>
                                
                        </div>
                    </form>
                    <!-- <div class="row">
                        <form>
                            <div class="form-group col-sm-6">
                                <label>日期</label>
                                <div class="input-group input-group-md" style="width:100%">
                                    <input id="date" class="form-control" type="text" value=""/>
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                <label>小区</label>
                                <div class="input-group input-group-md" style="width:100%">
                                    <input id="cell" class="form-control" type="text" value=""/>
                                </div>
                            </div>
                        </form>
                    </div> -->
                </div>
                <div class="box-footer">
                    <!-- <div style="text-align:right;">
                        <a class="btn btn-primary"  href="#" role="button" onClick="drawMapOut();return false;">查询</a>
                        <a class="btn btn-primary"  href="#" role="button" onClick="paramQueryExport();return false;">导出</a>
                        <a class="btn btn-primary"  href="#" role="button" onClick="drawMapOut();return false;">切出</a>
                        <a class="btn btn-primary"  href="#" role="button" onClick="drawMapIn();return false;">切入</a>
                    </div> -->
                    <div class="pull-right">
                        <div class="btn-group">
                            <a type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="drawMap()" id="search">
                                <span class="ladda-label">查询</span>
                            </a>
                        </div>
                        <div class="btn-group">
                            <a type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="exportSwitchData()" id="exportBtn">
                                <span class="ladda-label">导出</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">切出</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-wrench"></i></button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#">Print Chart</a></li>
                                <li class="divider"></li>
                                <li><a href="#">Download PNG img</a></li>
                                <li><a href="#">Download JPEG img</a></li>
                                <li><a href="#">Download PDF document</a></li>
                                <li><a href="#">Download SVG vector img</a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div id="map" style="position: relative;height: 400px;width:100%" ></div>
                </div>
            </div> -->
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">查询结果</h3>
                    <!-- <div class="box-tools pull-right">
                        <div class="btn-group">
                            <a id="export" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onclick="paramQueryExport()"><span class="ladda-label">导出</span></a>
                        </div>  
                    </div> -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div id="map1" style="position: relative;height: 600px;width:100%" ></div>
                    <!-- ./box-body -->
                </div>
                <!-- /.box -->
            </div>
            <div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">详细数据</h4>
                        </div>
                        <div class="modal-body">
                            <table id='bMapTable' class="display" cellspacing="0" border="1">
                                <!-- <thead>
                                <tr>
                                    <th>id</th>
                                    <th>day_id</th>
                                    <th>city</th>
                                    <th>subNetwork</th>
                                    <th>cell</th>
                                    <th>EutranCellRelation</th>
                                    <th>切换成功率</th>
                                    <th>同频切换成功率</th>
                                    <th>异频切换成功率</th>
                                    <th>同频准备切换尝试数</th>
                                    <th>同频准备切换成功数</th>
                                    <th>同频执行切换尝试数</th>
                                    <th>同频执行切换成功数</th>
                                    <th>异频准备切换尝试数</th>
                                    <th>异频准备切换成功数</th>
                                    <th>异频执行切换尝试数</th>
                                    <th>准备切换成功率</th>
                                    <th>执行切换成功率</th>
                                    <th>准备切换尝试数</th>
                                    <th>准备切换成功数</th>
                                    <th>准备切换失败数</th>
                                    <th>执行切换尝试数</th>
                                    <th>执行切换成功数</th>
                                    <th>执行切换失败数</th>
                                </tr>
                                </thead> -->
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
        <!-- /.col -->
@endsection
@section('scripts')
    <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
    <script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>
    <!--datatables-->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="plugins/datatables/grid.js"></script>
    <link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

    <!--loading-->
    <link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
    <script src="plugins/loading/js/spin.js"></script>
    <script src="plugins/loading/js/ladda.js"></script>

    <style type='text/css'>
    .datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
    background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
    border-color: #ffb733;
    }   
</style>

    <!-- <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=XtxLWdHvIBw0FKDLBh835SwO"></script> -->
    <script type="text/javascript" src="plugins/baidumapv2/baidumap_offline_v2_load.js"></script>
    <link rel="stylesheet" type="text/css" href="plugins/baidumapv2/css/baidu_map_v2.css"/>
    <script src="plugins/mapv/Mapv.js"></script>
    <link rel="stylesheet" href="dist/css/button.css">
    <script>
    	toogle('switchIn');
        $("#date").datepicker({format: 'yyyy-mm-dd'});
        var nowTemp = new Date();
        var nowTemp = new Date();
        var year = nowTemp.getFullYear();
        var month = nowTemp.getMonth()+1;
        var day = nowTemp.getDate();
        var today = year +'-'+month+'-'+day;
        $.get('switchIn/getDate', function(data){
            var sdata = [];
            for(var i=0; i<data.length; i++){
                if(data[i] === today){
                    continue;
                }
                sdata.push(data[i]);
            }
            sdata.push(today);
            $("#date").datepicker('setValues', sdata);
        })

        var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
        var checkin = $("#date").datepicker({
            onRender: function(date) {
                return date.valueOf() < now.valueOf() ? '' : '';
            }
        }).on('changeDate', function(ev) {
            checkin.hide();
        }).data('datepicker');
        function drawMap() {
            drawMapIn('origin');
        }

        function initMap(mapId) {
            var bmap = new BMap.Map(mapId);
            bmap.enableScrollWheelZoom(); // 启用滚轮放大缩小
            // 初始化地图,设置中心点坐标和地图级别
            var arr = setMapPoint();
            bmap.centerAndZoom(new BMap.Point(arr[0], arr[1]), 10);
            //自定义控件
            function staticControl(){
                this.defaultAnchor = BMAP_ANCHOR_TOP_RIGHT;
                this.defaultOffset = new BMap.Size(10,10);
            }
            //继承Control的API
            staticControl.prototype = new BMap.Control();
            //初始化控件
            staticControl.prototype.initialize=function(map){
                var _box = document.createElement('div');
                $("#search").click(function(){  
                    var masterCell = $('#cell').val();
                    var date = $('#date').val();
                    var params={
                        cell:masterCell,
                        date:date
                    };
                    $.get('switchIn/switchOutTableIn', params, function(data){
                        var myData = eval(data);
                        //最大RRC连接用户数
                        var nummore200 = myData[0];
                        var num100to200 = myData[1];
                        var numless100 = myData[2];
                        //无线掉线率
                        var wireLostmore5 = myData[3];
                        var wireLost1to5 = myData[4];
                        var wireLostLess1 = myData[5];
                        //PUSCH上行干扰电平
                        var phschless110 = myData[6];
                        var phsch110to95 = myData[7];
                        var phschmore95 = myData[8];
                        _box.innerHTML = 
                            "<div class='box'>"+
                                "<div class='box-header'>"+
                                    "<h3 class='box-title'>扇形图例&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h3>"+
                                    "<div class='box-tools pull-right'>"+
                                        "<button type='button' class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i>"+
                                        "</button>"+
                                    "</div>"+
                                "</div>"+
                                "<div class='box-body'>"+
                                    "<table width=195px>"+
                                        "<tr>"+
                                            "<td></td>"+
                                            "<td colspan=3 bgColor='white' align='center' width=180px>最大RRC连接用户数</td>"+
                                        "</tr>"+
                                        "<tr>"+
                                            "<td rowspan=2 align='center'><input name='t1' type='radio' value='1' onClick='res(1)'/></td>"+
                                            "<td bgColor='red' align='center' width=60px> >200</td>"+
                                            "<td bgColor='yellow' align='center' width=60px> 100~200</td>"+
                                            "<td bgColor='blue' align='center' width=60px> <100</td>"+
                                        "</tr>"+
                                        "<tr>"+
                                           "<td bgColor='red' align='center' width=60px>"+nummore200+"</td><td bgColor='yellow' align='center' width=70px>"+num100to200+"</td><td bgColor='blue' align='center' width=60px>"+numless100+"</td>"+ 
                                        "</tr>"+
                                        "<tr>"+
                                            "<td></td><tr><td width=60px height=10px></td><td width=60px></td><td width=60px></td></tr>"+
                                        "</tr>"+
                                        "<tr>"+
                                            "<td>&nbsp;</td>"+
                                            "<td colspan=3 bgColor='white' align='center' width=180px>无线掉线率</td>"+
                                        "</tr>"+
                                        "<tr>"+
                                            "<td rowspan=2 align='center'><input name='t1' type='radio' value='2' onClick='res(2)'/></td>"+
                                            "<td bgColor='red' align='center' width=60px> >5%</td>"+
                                            "<td bgColor='yellow' align='center' width=60px> 1%~5%</td>"+
                                            "<td bgColor='blue' align='center' width=60px> <1%</td>"+
                                        "</tr>"+
                                        "<tr>"+
                                           "<td bgColor='red' align='center' width=60px>"+wireLostmore5+"</td><td bgColor='yellow' align='center' width=70px>"+wireLost1to5+"</td><td bgColor='blue' align='center' width=60px>"+wireLostLess1+"</td>"+ 
                                        "</tr>"+
                                        "<tr>"+
                                            "<tr><td></td><td width=60px height=10px></td><td width=60px></td><td width=60px></td></tr>"+
                                        "</tr>"+
                                        "<tr>"+
                                            "<td>&nbsp;</td>"+
                                            "<td colspan=3 bgColor='white' align='center' width=180px>PUSCH上行干扰电平</td>"+
                                        "</tr>"+
                                        "<tr>"+
                                            "<td rowspan=2 align='center'><input name='t1' type='radio' value='3' onClick='res(3)'/></td>"+
                                            "<td bgColor='red' align='center' width=60px> >-95</td>"+
                                            "<td bgColor='yellow' align='center' width=60px> -110~-95</td>"+
                                            "<td bgColor='blue' align='center' width=60px> <-110</td>"+
                                        "</tr>"+
                                        "<tr>"+
                                           "<td bgColor='red' align='center' width=60px>"+phschmore95+"</td><td bgColor='yellow' align='center' width=70px>"+phsch110to95+"</td><td bgColor='blue' align='center' width=60px>"+phschless110+"</td>"+ 
                                        "</tr>"+
                                    "</table>"+
                                "</div>"+
                            "</div>";
                    });
                });
                map.getContainer().appendChild(_box);
                return _box;
            }
            //创建控件实例
            var staticsCtrl = new staticControl();
            //添加到地图当中
            bmap.addControl(staticsCtrl);

            //自定义控件
            function legendControl(){
                this.defaultAnchor = BMAP_ANCHOR_BOTTOM_RIGHT;
                this.defaultOffset = new BMap.Size(10,10);
            }
            //继承Control的API
            legendControl.prototype = new BMap.Control();
            //初始化控件
            legendControl.prototype.initialize=function(map){
                var _box = document.createElement('div');
                $("#search").click(function(){
                    _box.innerHTML = 
                        "<div class='box'>"+
                            "<div class='box-header'>"+
                                "<h3 class='box-title'>线条图例&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h3>"+
                                "<div class='box-tools pull-right'>"+
                                    "<button type='button' class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i>"+
                                    "</button>"+
                                "</div>"+
                            "</div>"+
                            "<div class='box-body' style='position: relative;'>"+
                                "<canvas id='switchSuccessRatio' width='100' height='190' style='width: 100px; height: 190px; background: rgb(255, 255, 255);'></canvas>"+
                                "<canvas id='switchNumber' width='100' height='190' style='width: 100px; height: 190px; background: rgb(255, 255, 255);'></canvas>"+
                            "</div>"+
                        "</div>";
                    switchNumberLegend();
                    switchSuccessRatioLegend();
                });
                map.getContainer().appendChild(_box);
                return _box;
            }
            //创建控件实例
            var legendCtr = new legendControl();
            //添加到地图当中
            bmap.addControl(legendCtr);

            //自定义控件
            function LeftControl(){
                this.defaultAnchor = BMAP_ANCHOR_TOP_LEFT;
                this.defaultOffset = new BMap.Size(10,10);
            }
            //继承Control的API
            LeftControl.prototype = new BMap.Control();
            //初始化控件
            LeftControl.prototype.initialize=function(map){
                var ul = document.createElement('ul');
                ul.setAttribute('class','list-group');
                ul.setAttribute('id','leftControl');
                var li = document.createElement('li');
                li.setAttribute('class','list-group-item');
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
                type: "GET",
                url: "switchIn/switchSite",
                dataType: "text",
                beforeSend: function () {
                    $("map").html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">')
                },
                success: function (data) {
                    var returnData = JSON.parse(data);
                    var vdata = [];
                    for (var i = 0; i < returnData.length; i++) {
                        vdata.push({
                            cell: returnData[i].cellName,
                            lng: returnData[i].longitude,
                            lat: returnData[i].latitude,
                            count: 5,
                            dir: returnData[i].dir-30,
                            band: returnData[i].band,
                        });
                    }
                    var layer = new Mapv.Layer({
                        mapv: mapv, // 对应的mapv实例
                        zIndex: 1, // 图层层级
                        dataType: 'point', // 数据类型，点类型
                        data: vdata, // 数据
                        drawType: 'choropleth', // 展示形式
                        dataRangeControl: false ,
                        drawOptions: { // 绘制参数
                            size: 20, // 点大小
                            unit: 'px', // 单位
                            strokeStyle: 'gray', // 描边颜色
                            type: 'site',
                            // splitList数值表示按数值区间来展示不同颜色的点
                            splitList: [
                                {
                                    start:0,
                                    end: 10,
                                    color: 'gray'
                                }
                            ],
                            events: {
                                mousemove: function (e, data) {
                                    console.log('click', e, data);
                                    $("#leftControl").children().remove();
                                    var li = '';
                                    for (var i = 0; i < data.length; i++) {
                                        li  += ("<li " + 'class="list-group-item"' + ">" + data[i].cell + "</li>");
                                    }
                                    console.log(li);
                                    $("#leftControl").append(li);
                                }
                            }
                        }
                    });
                }
            });
            return {"bmap":bmap,"mapv":mapv};
        }
        var mapv1 = initMap("map1");
        var layerout = null;
        var layerin = null;

        var drawMapOut = function () {
            var S = Ladda.create( document.getElementById( 'search' ) );
            // var E = Ladda.create( document.getElementById( 'export' ) );
            S.start();
            // E.start();

            if(layerout != null) {
                layerout.hide();
            }
            if(layerin != null){
                layerin.hide();
            }
            $.ajax({
                type: "GET",
                url: "switchIn/switchData",
                data: {date: document.getElementById("date").value,cell: document.getElementById("cell").value},
                dataType: "text",
                beforeSend: function () {
                    $("map").html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">')
                },
                success: function (data) {
                    S.stop();
                    // E.stop();
                    var returnData = JSON.parse(data);
                    var vdata = [];
                    for(var i=0;i<returnData.length;i++) {
                        var count;
                        if(returnData[i].handoverAttemptCount == 0) {
                            count = 80;
                        }else if(returnData[i].handoverSuccessRatio <= 90 && returnData[i].handoverAttemptCount >= 50) {
                            count = 55;
                        }else {
                            count = 30;
                        }
                        vdata.push({
                            lng: returnData[i].slongitude,
                            lat: returnData[i].slatitude,
                            count: count,
                            dir: returnData[i].sdir-30,
                            band: returnData[i].sband,
                            master: false,
                            scell: returnData[i].scell
                        });
                    }
                    vdata.push({
                        lng:returnData[0].mlongitude,
                        lat: returnData[0].mlatitude,
                        count: -1,
                        dir:returnData[0].mdir-30,
                        band: returnData[0].mband,
                        master: true
                    });

                    var points = [];
                    for(var i=0;i<vdata.length;i++) {
                        points.push(new BMap.Point(vdata[i].lng,vdata[i].lat));
                    }
                    mapv.bmap.setViewport(points);
                    layerout = new Mapv.Layer({
                        mapv: mapv.mapv, // 对应的mapv实例
                        zIndex: 1, // 图层层级
                        dataType: 'point', // 数据类型，点类型
                        data: vdata, // 数据
                        drawType: 'choropleth', // 展示形式
                        dataRangeControl: false ,
                        drawOptions: { // 绘制参数
                            size: 20, // 点大小
                            unit: 'px', // 单位
                            type: 'switchout',
            				// splitList数值表示按数值区间来展示不同颜色的点
                            splitList: [
                                {
                                    end: 0,
                                    color: 'green'
                                },{
                                    start: 0,
                                    end: 50,
                                    color: 'blue'
                                },{
                                    start: 50,
                                    end: 60,
                                    color: 'red'
                                },{
                                    start: 60,
                                    end: 90,
                                    color: 'gray'
                                }
                            ],
                            events: {
                                click: function(e, data) {
                                    console.log('click',e,data);
                                    var scells = [];
                                    for(var i=0;i<data.length;i++) {
                                        scells.push(data[i].scell);
                                    }
                                    var params = {
                                        date: document.getElementById("date").value,
                                        cell: document.getElementById("cell").value,
                                        scells: scells
                                    }

                                    $("#bMapTable").DataTable( {
                                        "bAutoWidth": false,
                                        "destroy": true,
                                        "scrollX": true,
                                        //"processing": true,
                                        //"serverSide": true,
                                        //"aoColumnDefs":  [{ "sWidth": "500px",  "aTargets": [0] }],
                                        "ajax": {
                                            "url":"switchIn/switchDetail",
                                            "data":params
                                        },
                                        "columns": [
                                            { "data": "id" },
                                            { "data": "day_id" },
                                            { "data": "city" },
                                            { "data": "subNetwork" },
                                            { "data": "cell" },
                                            { "data": "EutranCellRelation" },
                                            { "data": "切换成功率" },
                                            { "data": "同频切换成功率" },
                                            { "data": "异频切换成功率" },
                                            { "data": "同频准备切换尝试数" },
                                            { "data": "同频准备切换成功数" },
                                            { "data": "同频执行切换尝试数" },
                                            { "data": "同频执行切换成功数" },
                                            { "data": "异频准备切换尝试数" },
                                            { "data": "异频准备切换成功数" },
                                            { "data": "异频执行切换尝试数" },
                                            { "data": "准备切换成功率" },
                                            { "data": "执行切换成功率" },
                                            { "data": "准备切换尝试数" },
                                            { "data": "准备切换成功数" },
                                            { "data": "准备切换失败数" },
                                            { "data": "执行切换尝试数" },
                                            { "data": "执行切换成功数" },
                                            { "data": "执行切换失败数" }
                                        ]
                                    });
                                    $('#myModal').modal({
                                        keyboard: false
                                    });
                                }
                            }
                        }
                    });
                }
            })
        }
        var drawMapIn = function (t) {
            if(t == '最大RRC连接用户数'){
                url = 'switchIn/RRCusersin';
            }else if(t=='无线掉线率'){
                url = 'switchIn/wireLessLostin';
            }else if(t=='PUSCH上行干扰电平'){
                url = 'switchIn/PUSCHInterferein';
            }else if(t=='origin'){
                url = "switchIn/handoverin";
            }
            var S = Ladda.create( document.getElementById( 'search' ) );
            // var E = Ladda.create( document.getElementById( 'export' ) );
            S.start();
            // E.start();

            if(layerin != null) {
                layerin.hide();
            }

            if(layerout != null) {
                layerout.hide();
            }
            
            $.ajax({
                type: "GET",
                url: url,
                data: {date: document.getElementById("date").value,cell: document.getElementById("cell").value},
                dataType: "text",
                beforeSend: function () {
                    $("map").html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">')
                },
                success: function (data) {
                    S.stop();
                    // E.stop();
                    var returnData = JSON.parse(data);
                    var vdata = [];
                    for(var i=0;i<returnData.length;i++) {
                        var count;
                        if(t=='origin'){
                            if(returnData[i].handoverAttemptCount == 0) {
                                count = 80;
                            }else if(returnData[i].handoverSuccessRatio <= 90 && returnData[i].handoverAttemptCount >= 50) {
                                count = 55;
                            }else {
                                count = 30;
                            }
                        }else{
                            if(returnData[i].handoverAttemptCount == 55) {
                                //alert('red');
                                count = 55;  //red
                            }else if(returnData[i].handoverAttemptCount == 100) {
                                count = 100;  //yellow
                            }else if(returnData[i].handoverAttemptCount == 30){
                                count = 30; //blue
                            }

                        }
                        var lineCounts;
                        if(returnData[i].handoverSuccessRatio == null){
                            lineCounts = 'null'; 
                        }else if(returnData[i].handoverSuccessRatio < 85) {
                            lineCounts = 'red'; 
                        }else if(returnData[i].handoverSuccessRatio <= 95 && returnData[i].handoverSuccessRatio >= 85) {
                            lineCounts = 'yellow';
                        }else if(returnData[i].handoverSuccessRatio > 95){
                            lineCounts = 'blue';

                        }
                        var lineWidth;
                        if (returnData[i].handoverAttemptCount1 >= 0 && returnData[i].handoverAttemptCount1 <20) {
                            lineWidth = 1;
                        } else if (returnData[i].handoverAttemptCount1  >= 20 && returnData[i].handoverAttemptCount1 < 80) {
                            lineWidth = 2;
                        } else if (returnData[i].handoverAttemptCount1  >= 80 && returnData[i].handoverAttemptCount1 < 160) {
                            lineWidth = 3;
                        } else if (returnData[i].handoverAttemptCount1  >= 160) {
                            lineWidth = 4;
                        } 
                        vdata.push({
                            lng: returnData[i].mlongitude,
                            lat: returnData[i].mlatitude,
                            count: count,
                            dir: returnData[i].mdir-30,
                            band: returnData[i].mband,
                            master: false,
                            cell: returnData[i].cell,
                            lineWidth : lineWidth,
                            lineCount:lineCounts
                        });
                    }
                    vdata.push({
                        lng:returnData[0].slongitude,
                        lat: returnData[0].slatitude,
                        count: -1,
                        dir:returnData[0].sdir-30,
                        band: returnData[0].sband,
                        master: true,
                        lineWidth : -1
                    });

                    var points = [];

                    for(var i=0;i<vdata.length;i++) {
                        points.push(new BMap.Point(vdata[i].lng,vdata[i].lat));
                    }

                    mapv1.bmap.setViewport(points);

                    layerin = new Mapv.Layer({
                        mapv: mapv1.mapv, // 对应的mapv实例
                        zIndex: 1, // 图层层级
                        dataType: 'point', // 数据类型，点类型
                        data: vdata, // 数据
                        drawType: 'choropleth', // 展示形式
                        dataRangeControl: false ,
                        drawOptions: { // 绘制参数
                            size: 20, // 点大小
                            unit: 'px', // 单位
                            type: 'switchin',
                            // splitList数值表示按数值区间来展示不同颜色的点
                            splitList: [
                                {
                                    end: 0,
                                    color: 'green'
                                },{
                                    start: 0,
                                    end: 50,
                                    color: 'blue'
                                },{
                                    start: 50,
                                    end: 60,
                                    color: 'red'
                                },{
                                    start: 60,
                                    end: 90,
                                    color: 'gray'
                                },{
                                    start:90,
                                    end:120,
                                    color:'yellow'
                                }
                            ],
                            events: {
                                click: function(e, data) {
                                    console.log('click',e,data);
                                    var cells = [];
                                    for(var i=0;i<data.length;i++) {
                                        cells.push(data[i].cell);
                                    }
                                    var params = {
                                        date: document.getElementById("date").value,
                                        cell: document.getElementById("cell").value,
                                        cells: cells
                                    }


                                    $.get('switchIn/handOverInDetail', params, function(data){
                                        var newData = JSON.parse(data).data;
                                        console.log(newData);
                                        $('#bMapTable').grid('destroy', true, true);
                                        $('#bMapTable').grid({
                                            columns:[
                                            {'text':"id",'field':'id','height':50,'width':150},
                                            {'text':"day_id",'field':'day_id','height':50,'width':150},
                                            {'text':"city",'field':'city','height':50,'width':150},
                                            {'text':"subNetwork",'field':'subNetwork','height':50,'width':150},
                                            {'text':"cell",'field':'cell','height':50,'width':150},
                                            {'text':"EutranCellRelation",'field':'EutranCellRelation','height':50,'width':150},
                                            {'text':"切换成功率",'field':'切换成功率','height':50,'width':150},
                                            {'text':"同频切换成功率",'field':'同频切换成功率','height':50,'width':150},
                                            {'text':"异频切换成功率",'field':'异频切换成功率','height':50,'width':150},
                                            {'text':"同频准备切换尝试数",'field':'同频准备切换尝试数','height':50,'width':150},
                                            {'text':"同频准备切换成功数",'field':'同频准备切换成功数','height':50,'width':150},
                                            {'text':"同频执行切换尝试数",'field':'同频执行切换尝试数','height':50,'width':150},
                                            {'text':"异频准备切换尝试数",'field':'异频准备切换尝试数','height':50,'width':150},
                                            {'text':"异频准备切换成功数",'field':'异频准备切换成功数','height':50,'width':150},
                                            {'text':"异频执行切换尝试数",'field':'异频执行切换尝试数','height':50,'width':150},
                                            {'text':"准备切换成功率",'field':'准备切换成功率','height':50,'width':150},
                                            {'text':"执行切换成功率",'field':'执行切换成功率','height':50,'width':150},
                                            {'text':"准备切换尝试数",'field':'准备切换尝试数','height':50,'width':150},
                                            {'text':"准备切换成功数",'field':'准备切换成功数','height':50,'width':150},
                                            {'text':"准备切换失败数",'field':'准备切换失败数','height':50,'width':150},
                                            {'text':"执行切换尝试数",'field':'执行切换尝试数','height':50,'width':150},
                                            {'text':"执行切换成功数",'field':'执行切换成功数','height':50,'width':150},
                                            {'text':"执行切换失败数",'field':'执行切换失败数','height':50,'width':150} 
                                            ],
                                             dataSource:newData,
                                            pager: { limit: 10, sizes: [10, 20, 50, 100] },
                                            autoScroll:true,
                                            uiLibrary: 'bootstrap'
                                        });
                                    });
                                    $('#myModal').modal({
                                        keyboard: false
                                    });

                                }
                            }
                        }
                    });
                }
            });
        }

        var res = function(t){
            if(t == 1){
                drawMapIn('最大RRC连接用户数');
            }else if(t==2){
                drawMapIn('无线掉线率');
            }else if(t==3){
                drawMapIn('PUSCH上行干扰电平');
            }
        }

        function switchNumberLegend(){
            var switchNumber = document.getElementById("switchNumber");
            var context = switchNumber.getContext("2d");
            context.fillStyle="#000000";
            context.font="12px serif";
            context.textAlign="center";
            context.fillText("准备切换尝试数",50,20.5);
            context.textAlign="left";
            //设置对象起始点和终点
            context.beginPath();
            context.moveTo(20.5,40.5);
            context.lineTo(40.5,40.5);
            //设置样式
            context.lineWidth = 1;
            context.strokeStyle = "#000000";
            //绘制
            context.stroke();
            context.fillText("0~20",50.5,45.5);

            //设置对象起始点和终点
            context.beginPath();
            context.moveTo(20.5,70.5);
            context.lineTo(40.5,70.5);
            //设置样式
            context.lineWidth = 2;
            context.strokeStyle = "#000000";
            //绘制
            context.stroke();
            context.fillText("20~80",50.5,75.5);

            //设置对象起始点和终点
            context.beginPath();
            context.moveTo(20.5,100.5);
            context.lineTo(40.5,100.5);
            //设置样式
            context.lineWidth = 3;
            context.strokeStyle = "#000000";
            //绘制
            context.stroke();
            context.fillText("80~160",50.5,105.5);

            //设置对象起始点和终点
            context.beginPath();
            context.moveTo(20.5,130.5);
            context.lineTo(40.5,130.5);
            //设置样式
            context.lineWidth = 4;
            context.strokeStyle = "#000000";
            //绘制
            context.stroke();
            context.fillText(">160",50.5,135.5);
        }
        function switchSuccessRatioLegend(){
            var switchNumber = document.getElementById("switchSuccessRatio");
            var context = switchNumber.getContext("2d");
            context.fillStyle="#000000";
            context.font="12px serif";
            context.textAlign="center";
            context.fillText("切换成功率",50,20.5);
            context.textAlign="left";
            //设置对象起始点和终点
            context.beginPath();
            context.arc(25.5,45.5,5,0,2*Math.PI);
            context.closePath();
            context.fillStyle = 'rgb(255, 0, 0)';
            context.fill();
            context.fillText("<85%",50.5,48.5);

            //设置对象起始点和终点
            context.beginPath();
            context.arc(25.5,85.5,5,0,2*Math.PI);
            context.closePath();
            context.fillStyle = 'rgb(255, 255, 0)';
            context.fill();
            context.fillText("85%~95%",50.5,88.5);

            //设置对象起始点和终点
            context.beginPath();
            context.arc(25.5,125.5,5,0,2*Math.PI);
            context.closePath();
            context.fillStyle = 'rgb(0, 0, 255)';
            context.fill();
            context.fillText(">95%",50.5,128.5);

        }

        function exportSwitchData() {
            var date = $("#date").val();
            var cell = $("#cell").val();
            if (!date) {
                layer.open({
                    title: "提示",
                    content: "请选择查询日期"
                });
                return;
            }
            if (!cell) {
                layer.open({
                    title: "提示",
                    content: "请输入查询小区"
                });
                return;
            }
            var E = Ladda.create( document.getElementById( 'exportBtn' ) );
            E.start();
            $.post("switchIn/exportSwitchInData", {date:date,cell:cell}, function(data){
                if (data.result) {
                    fileDownload(data.fileName);
                    E.stop();
                } else {
                    layer.open({
                        title: "提示",
                        content: "下载失败"
                    });
                    E.stop();
                }
            });
        }
    </script>
@endsection