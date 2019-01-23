@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>
        轨迹查询(手动入库)
    </h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i>终端分析</li>
        <li>位置分析</li>
        <li class='active'>轨迹查询(手动入库)</li>
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
                            <label for="citys" class="col-sm-1 control-label">城市</label>
                            <div class="col-sm-2">
                                <select class="form-control" name="citys" id="citys">
                                </select>
                            </div>
                            <label for="startDate" class="col-sm-1 control-label">起始日期</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="startDate" id="startDate">
                                </input>
                            </div>
                            <label for="endDate" class="col-sm-1 control-label">结束日期</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="endDate" id="endDate">
                                </input>
                            </div>
                            <label for="user_query" class="col-sm-1 control-label">用户</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="user_query" id="user_query" placeholder="输入imsi">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <div class="btn-group">
                            <a type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="queryTrail()" id="queryBtn">
                                <span class="ladda-label">查询</span>
                            </a>
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


    <!-- <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=XtxLWdHvIBw0FKDLBh835SwO"></script> -->
    <script type="text/javascript" src="plugins/baidumapv2/baidumap_offline_v2_load.js"></script>
    <link rel="stylesheet" type="text/css" href="plugins/baidumapv2/css/baidu_map_v2.css"/>
    <script src="plugins/mapv/Mapv.js"></script>

<style type='text/css'>
    .datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
    background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
    border-color: #ffb733;
    }   
</style>
@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/userAnalysis/trailQueryManual.js"></script>