@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>
        邻区定义分析
    </h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i>地理呈现</li>
        <li>邻区分析</li>
        <li class='active'>邻区定义分析</li>
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
                            <label class="col-sm-1 control-label">小区</label>
                            <div class="col-sm-3">
                                <input id="cell" class="form-control" type="text" value=""/>
                            </div>
                        </div> 
                    </form>
                    <!-- <div class="row">
                        <form>
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
                            <a class="btn  btn-primary ladda-button" data-style="expand-right" onclick="drawMap()" id="search">
                                <span class="ladda-label">查询</span>
                            </a>
                        </div>
                        <!-- <div class="btn-group">
                            <a class="btn  btn-primary ladda-button" data-style="expand-right" onclick="paramQueryExport()" id="export">
                                导出
                            </a>
                        </div> -->
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-8">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">查询结果</h3>
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
                        <!-- /.box-header -->
                        <div class="box-body">
                            <div id="map" style="position: relative;height: 600px;width:100%" ></div>
                            <!-- ./box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">指标详情</h3>
                        </div>
                        <div class="box-body">
                            <table id="detailTable" style="position: relative;height: 580px;width:100%"></table>
                        </div>
                    </div>
                </div>    
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
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
           
            <!-- <div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                        </div>
                        <div class="modal-body">
                            <table id='bMapTable' class="display" cellspacing="0" border="1">
                                <thead>
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
                                </thead>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div> -->
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
    <link rel="stylesheet" href="dist/css/button.css">
    <!--loading-->
    <link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
    <script src="plugins/loading/js/spin.js"></script>
    <script src="plugins/loading/js/ladda.js"></script>

    <!-- <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=XtxLWdHvIBw0FKDLBh835SwO"></script> -->
    <script type="text/javascript" src="plugins/baidumapv2/baidumap_offline_v2_load.js"></script>
    <link rel="stylesheet" type="text/css" href="plugins/baidumapv2/css/baidu_map_v2.css"/>
    <script src="plugins/mapv/Mapv.js"></script>
    <script>
        $(document).ready(function(){
            toogle('switchdefine');
            drawMapIn();
        });

        function drawMap() {
            drawMapOut();
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
            //创建控件实例
            var staticsCtrl = new staticControl();
            //添加到地图当中
            bmap.addControl(staticsCtrl);

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
                url: "switchdefine/switchSite",
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
                                    $("#leftControl").children().remove();
                                    var li = '';
                                    for (var i = 0; i < data.length; i++) {
                                        li  += ("<li " + 'class="list-group-item"' + ">" + data[i].cell + "</li>");
                                    }
                                    $("#leftControl").append(li);
                                }
                            }
                        }
                    });
                }
            });
            return {"bmap":bmap,"mapv":mapv};
        }

        var mapv = initMap("map");
        var layerout = null;
        var layerin = null;

        function getDetailTable(data){
            var fieldArr=[];
            data = eval("("+data+")");
            var text = "cell,scell,mlongitude,mlatitude,mdir,mband,slongitude,slatitude,sdir,sband,sc_channel,nc_channel";
            var textArr = text.split(",");
            for(var i in textArr){
                fieldArr[fieldArr.length]={field:textArr[fieldArr.length],title:textArr[fieldArr.length],width:150};
            }
            $('#detailTable').grid('destroy', true, true);
            var grid = $("#detailTable").grid({
                columns:fieldArr,
                dataSource:data,
                pager: { limit: 10, sizes: [10, 20, 50, 100] },
                autoScroll:true,
                uiLibrary: 'bootstrap',
                primaryKey : 'id',
                autoLoad: true   
            });
            grid.on('rowSelect', function (e, $row, id, record) {
                var browerInfo = getBrowerInfo();
                var scell = '';
                if (browerInfo=="firefox"){
                    scell = $row.context.innerText.split('\t')[1];
                } else if (browerInfo == "chrome") {
                    scell = $row.context.innerText.split('\t')[1];
                }
                drawMapOut2(data,scell);
            });
        }

        var drawMapOut2 = function (data,t) {
            var returnData = data;
            var vdata = [];
            for(var i=0;i<returnData.length;i++) {
                var count;
                if(returnData[i].sc_channel == '' || returnData[i].nc_channel == ''){
                    count = 70;
                }else if(returnData[i].sc_channel == returnData[i].nc_channel){
                    count = 30;
                }else if(returnData[i].sc_channel != returnData[i].nc_channel){
                    count = 100;
                }
                if(returnData[i].scell == t){
                    count = 130;
                }
                vdata.push({
                    lng: returnData[i].slongitude,
                    lat: returnData[i].slatitude,
                    count: count,
                    dir: returnData[i].sdir-30,  
                    band: returnData[i].sband,  //代表宽度
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
                    type: 'switch',
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
                            color:'purple'
                        },{
                            start:120,
                            end:140,
                            color:'yellow'
                        }
                    ],
                    events: {}
                }
            });
		}
        var drawMapOut = function (t) {
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
                url: "switchdefine/switchDataDefine",
                data: {cell: document.getElementById("cell").value},
                dataType: "text",
                beforeSend: function () {
                    $("map").html('<img class="col-md-offset-5" src="dist/img/ajax-loader.gif">')
                },
                success: function (data) {
                    S.stop();
                    // E.stop();
                    var returnData = JSON.parse(data);
                    getDetailTable(data);
                    var vdata = [];
                    for(var i=0;i<returnData.length;i++) {
                        var count;
                        if(returnData[i].sc_channel == '' || returnData[i].nc_channel == ''){
                            count = 70;
                        }else if(returnData[i].sc_channel == returnData[i].nc_channel){
                            count = 30;
                        }else if(returnData[i].sc_channel != returnData[i].nc_channel){
                            count = 100;
                        }
                        if(returnData[i].scell == t){
                            count = 130;
                        }
                        //alert(returnData[i].scell);
                        vdata.push({
                            lng: returnData[i].slongitude,
                            lat: returnData[i].slatitude,
                            count: count,
                            dir: returnData[i].sdir-30,  
                            band: returnData[i].sband,  //代表宽度
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
                            type: 'switch',
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
                                    color:'purple'
                                },{
                                    start:120,
                                    end:140,
                                    color:'yellow'
                                }
                            ],
                            events: {
                                click: function(e, data) {
                                    //console.log('click',e,data);
                                    var scells = [];
                                    for(var i=0;i<data.length;i++) {
                                        if(scells.indexOf(data[i].scell) == -1){
                                            scells.push(data[i].scell);
                                        }
                                    }
                                    var params = {
                                        cell: document.getElementById("cell").value,
                                        scells: scells
                                    }
                                    $.get('switchdefine/switchDefineDetail', params, function(data){
                                        var newData = JSON.parse(data).data;
                                        $('#bMapTable').grid('destroy', true, true);
                                        $('#bMapTable').grid({
                                            columns:[
                                                {'text':"ecgi",'field':'ecgi','height':50,'width':150},
                                                {'text':"cellName",'field':'cellName','height':50,'width':150},
                                                {'text':"siteName",'field':'siteName','height':50,'width':150},
                                                {'text':"cellNameChinese",'field':'cellNameChinese','height':50,'width':150},
                                                {'text':"longitude",'field':'longitude','height':50,'width':150},
                                                {'text':"latitude",'field':'latitude','height':50,'width':150},
                                                {'text':"dir",'field':'dir','height':50,'width':150},
                                                {'text':"pci",'field':'pci','height':50,'width':150},
                                                {'text':"earfcn",'field':'earfcn','height':50,'width':150},
                                                {'text':"cellType",'field':'cellType','height':50,'width':150},
                                                {'text':"tiltM",'field':'tiltM','height':50,'width':150},
                                                {'text':"tiltE",'field':'tiltE','height':50,'width':150},
                                                {'text':"antHeight",'field':'antHeight','height':50,'width':150},
                                                {'text':"city",'field':'city','height':50,'width':150},
                                                {'text':"importDate",'field':'importDate','height':50,'width':150},
                                                {'text':"band",'field':'band','height':50,'width':150},
                                                {'text':"highTraffic",'field':'highTraffic','height':50,'width':150},
                                                {'text':"highInterference",'field':'highInterference','height':50,'width':150},
                                                {'text':"HST",'field':'HST','height':50,'width':150}
                                            ],
                                            dataSource:newData,
                                            pager: { limit: 10, sizes: [10, 20, 50, 100] },
                                            autoScroll:true,
                                            uiLibrary: 'bootstrap'
                                        });
                                    });
                                    $('#myModal').modal({
                                        keyboard: false
                                    })
                                }
                            }
                        }
                    });
                }
            })
        }

        var drawMapIn = function () {
            returnData = [];
            var layer = new Mapv.Layer({
                mapv: mapv.mapv, // 对应的mapv实例
                // zIndex: 1, // 图层层级
                // dataType: 'point', // 数据类型mapv，点类型
                // data: returnData, // 数据
                drawType: 'choropleth', // 展示形式
                // dataRangeControl: true ,
                drawOptions: { // 绘制参数
                    // type: "rect", // 网格类型，方形网格或蜂窝形
                    // size: 4, // 网格大小
                    // unit: 'px', // 单位
                    // opacity: '0.5',
                    // label: { // 是否显示文字标签
                    //     show: true,
                    // },
                    splitList: [
                        {
                            start:'chinese',
                            color:'red'
                        },{
                            end: '同频',
                            color: 'blue'
                        },{
                            end: "异频",
                            /*end: -110,*/
                            color: 'purple'
                        }
                    ]
                }
            });
        }
        function getBrowerInfo(){
             var uerAgent = navigator.userAgent.toLowerCase();
             var format =/(msie|firefox|chrome|opera|version).*?([\d.]+)/;
             var matches = uerAgent.match(format);
             return matches[1].replace(/version/, "'safari"); 
        }
    </script>
@endsection
