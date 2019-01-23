@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>干扰概览</h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i> 专项研究</li>
        <li>高干扰分析</li>
        <li class="active">干扰概览</li>
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
                <form class="form-inline" role="form" id="queryForm">
                    <div class="form-group">
                    日期：
                    </div>
                    <div class="form-group">
                        <label class="sr-only"></label>
                        <p class="form-control-static">
                            <input id="startTime" class="form-control" type="text" value=""/> 
                        </p>                    
                    </div>
                </form>
            </div>
            <div class="box-footer">
                <button id="search" class="btn btn-primary pull-right ladda-button" data-style="expand-right" href="#" onClick="search()"><span class="ladda-label">查询</span></button>
            </div>
        </div>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">高干扰小区占比</h3>
            </div>
            <div class="box-body">
                <div id="interCoverRatio" style="height: 400px"></div>
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
<script type="text/javascript" src="dist/js/genius/interCoverRatio.js"></script>
