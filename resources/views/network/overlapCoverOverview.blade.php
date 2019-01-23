@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>重叠覆盖概览</h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i> 专项研究</li>
        <li>重叠覆盖分析</li>
        <li class="active">重叠覆盖概览</li>
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
                    <label for="startTime" class="col-sm-1 control-label">日期</label>
                    <div class="col-sm-3">
                        <input id="startTime" class="form-control" type="text" value=""/>
                    </div>
                    <!-- <label for="busyTime" class="col-sm-1 control-label">忙时</label>
                    <div class="col-sm-3">
                        <select class="form-control" name="busyTime" id="busyTime">
                            <option value="earlyTime">早忙时</option>
                            <option value="laterTime">晚忙时</option>
                        </select>
                    </div> -->
                </form>
            </div>
            <div class="box-footer">
                <button id="search" class="btn btn-primary pull-right ladda-button" data-style="expand-right" href="#" onClick="search()"><span class="ladda-label">查询</span></button>
            </div>
        </div>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">重叠覆盖小区占比</h3>
            </div>
            <div class="box-body">
                <div id="overlapCoverOverview" style="height: 400px"></div>
            </div>    
        </div>
    </div>
</div>
</section>
@endsection
@section('scripts')
<link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>

<!--loading-->
<link rel="stylesheet" href="plugins/loading/css/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<script src="https://code.highcharts.com/stock/highstock.js"></script>
<script src="plugins/highcharts/js/highcharts.js"></script>
<script src="https://code.highcharts.com/stock/modules/exporting.js"></script>
<style type='text/css'>
    .datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
    background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
    border-color: #ffb733;
    }   
</style>
@endsection
<!-- jQuery 2.2.0 -->
<script type="text/javascript" src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/genius/overlapCoverOverview.js"></script>
