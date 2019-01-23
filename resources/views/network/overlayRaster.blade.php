@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>
        覆盖栅格图
    </h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i>地理呈现</li>
        <li>覆盖分析</li>
        <li class='active'>覆盖栅格图</li>
    </ol>
</section>
@endsection 
@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <!-- <div class="box-header with-border">
                    <h3 class="box-title">查询结果</h3>
                </div> -->
                <div class="box-body">
                    <div id="map" style="position: relative;height: 600px;width:100%" ></div>
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

    <!-- <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=XtxLWdHvIBw0FKDLBh835SwO"></script> -->
    <script type="text/javascript" src="plugins/baidumapv2/baidumap_offline_v2_load.js"></script>
    <link rel="stylesheet" type="text/css" href="plugins/baidumapv2/css/baidu_map_v2.css"/>
    <!-- <script src="plugins/mapv/Mapv.js"></script> -->

    <link rel="stylesheet" href="plugins/openLayers/ol.css">
    <script src="plugins/openLayers/ol-debug.js"></script>
@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/genius/overlayRaster.js"></script>