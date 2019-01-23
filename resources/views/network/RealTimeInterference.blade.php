@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>
        实时干扰
    </h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i> 专项研究</li>
        <li>高干扰分析</li>
        <li class='active'>实时干扰</li>
    </ol>
</section>
@endsection 
@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">实时干扰</h3>
                    <div class="box-tools pull-right">
                        <button id="collapseBtn" type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
					<form class="form-horizontal" role="form" id="queryForm">
						<div class="form-group">
                            <label class="col-sm-1 control-label">城市</label>
                            <div class="col-sm-3">
                                <select id="citys" class="form-control input-sm" multiple="multiple">
                                </select>
                            </div>
							<label class="col-sm-1 control-label">时间</label>
                            <div class="col-sm-3">
                                <select id="dateSelect" class="form-control input-sm">
								</select>
                            </div>
                            <div class="col-sm-1 col-sm-offset-3">
                                <button id="search" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" onClick="query()">
                                    <span class="ladda-label">查询
                                </button>
                            </div>
						</div>
					</form>
                </div>
            </div>
            <div class="box">
            	<div class="box-body">
                    <div id="mapPoint" style="position: relative;height: 600px;"></div>
                </div>
            </div>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">异常小区</h3>
                </div>
                <div class="box-body">
                    <table id="interfere_connection_cell"></table>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade" id="detailData_modal">
    <div class="modal-dialog" style="width:900px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">详细信息</h8>
            </div>
            <div class="modal-body">
                <table class="table" id="detailDataTable">
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="col-sm-2 col-sm-offset-5 btn btn-default" id="cancelBtn" data-dismiss="modal">关闭</button>
            </div>  
        </div>  
    </div>
</div>
@endsection
@section('scripts')
    <script src="plugins/highcharts/js/highstock.js"></script>
    <script src="dist/js/genius/alarm-chart.js"></script>
    <!-- <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
    <script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script> -->
    <!--datatables-->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="plugins/datatables/grid.js"></script>
    <link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

    <link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
	<script src="plugins/loading/js/spin.js"></script>
	<script src="plugins/loading/js/ladda.js"></script>
	<!--input select-->
	<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
	<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

    <!-- <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=XtxLWdHvIBw0FKDLBh835SwO"></script> -->
    <script type="text/javascript" src="plugins/baidumapv2/baidumap_offline_v2_load.js"></script>
	<link rel="stylesheet" type="text/css" href="plugins/baidumapv2/css/baidu_map_v2.css"/>
    <script src="plugins/mapv/Mapv.js"></script>

    <!-- 复制到剪切板 -->
    <script src="plugins/clipboard/clipboard.min.js"></script>
    <style type='text/css'>
    	table div{
            word-break: break-all;
        }
    </style>
@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/genius/RealTimeInterference.js"></script>