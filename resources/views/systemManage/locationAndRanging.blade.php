@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>
        定位测距
    </h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i>地理呈现</li>
        <li>定位测距</li>
        <li class='active'>定位测距</li>
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
                </div>
                <div class="box-body">
                    <form class="form-horizontal" role="form" id="queryForm">
                        <div class="form-group">
                            <label for="addr" class="col-sm-1 control-label">经纬度</label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="addr" id="addr" placeholder="经度,纬度">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary" type="button"  onclick="setMapPoint1()" id="locationBtn">打点</button>
                                    </span>
                                </div>
                            </div>

                            <label for="cell" class="col-sm-1 control-label">小区</label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="cell" id="cell" placeholder="输入小区">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary" type="button"  onclick="setPointByCell()" id="locationCellBtn">打点</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <!-- <div class="btn-group">
                            <button type="button" class="btn btn-primary ladda-button" data-style="expand-right" onclick="setMapPoint1()" id="locationBtn">
                                <span class="ladda-label">打点</span>
                            </button>
                        </div> -->
                        <div class="btn-group">
                            <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="ranging()" id="rangingBtn">
                                <span class="ladda-label">测距</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">查询结果</h3>
                </div>
                <div class="box-body">
                    <div id="map1" style="position: relative;height: 600px;width:100%" ></div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
    <!--loading-->
    <link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
    <script src="plugins/loading/js/spin.js"></script>
    <script src="plugins/loading/js/ladda.js"></script>


    <!-- <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=XtxLWdHvIBw0FKDLBh835SwO"></script>
    <script type="text/javascript" src="http://api.map.baidu.com/library/DistanceTool/1.2/src/DistanceTool_min.js"></script> -->
    <script type="text/javascript" src="plugins/baidumapv2/baidumap_offline_v2_load.js"></script>
    <script type="text/javascript" src="plugins/baidumapv2/tools/DistanceTool_min.js"></script>
    <link rel="stylesheet" type="text/css" href="plugins/baidumapv2/css/baidu_map_v2.css"/>
    <script src="plugins/mapv/Mapv.js"></script>
    <style>
        .BMapLabel{
            max-width:none;
        }
    </style>
@endsection
<script src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/locationAndRanging.js"></script>