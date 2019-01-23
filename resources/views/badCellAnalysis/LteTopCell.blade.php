@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>LTE模板查询
		<small>查询方式：</small>
	</h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-dashboard"></i>日常优化
		</li>
		<li>差小区分析
		</li>
		<li class="active">LTE差小区</li>
	</ol>
</section>
@endsection
@section('content')
<section class="content">
	<div class="row">
		<div class="col-sm-12">
			<div class='box'>
				<div class="box-header with-border">
					<h3 class="box-title">查询条件</h3>
				</div>
				<div class="box-body">
					<form class="form-horizontal" role="form" id="queryForm">
						<div class="form-group">
							<label for="survey" class="col-sm-1 control-label">指标类型</label>
							<div class="col-sm-2">
								<select class="form-control" name="survey" id="survey">
									<option value="LowCell">低接通</option>
									<option value="HighCell">高掉线</option>
									<option value="BadCell">低切换</option>
									<option value="InterfereCell">高干扰</option>
									<option value="RrcCell">用户数</option>									
								</select>
							</div>
							<label for="city" class="col-sm-1 control-label">城市</label>
							<div class="col-sm-2">
								<select class="form-control" name="city" id="city">
								</select>
							</div>
							<label for="timeType" class="col-sm-1 control-label">时间维度</label>
							<div class="col-sm-2">
								<select class="form-control" name="timeType" id="timeType">
									<option value="day">天</option>
									<option value="daygroup">天组</option>
									<option value="hour">小时</option>
									<option value="hourgroup">小时组</option>
									<option value="quarter">15分钟</option>
								</select>
							</div>
						</div>
						<div class="form-group">
						<!-- 	<label for="regionType" class="col-sm-1 control-label">区域维度</label>
							<div class="col-sm-2">
								<select class="form-control" name="regionType" id="regionType">
									<option value="city">城市</option>
									<option value="baseStation">基站</option>
									<option value="groupEcgi">小区</option>
									<option value="baseStationGroup">基站组</option>
									<option value="cellGroup">小区组</option>
								</select>
							</div> -->
							
						</div>
						<div class="form-group">
							
							<label for="startTime" class="col-sm-1 control-label">起始日期</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="startTime" id="startTime"></input>
                            </div>
                            <label for="endTime" class="col-sm-1 control-label">结束日期</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="endTime" id="endTime"></input>
                            </div>
                            	
                            <label for="hour" class="col-sm-1 control-label">小时</label>
							<div class="col-sm-2">
								<select class="form-control" multiple name="hour" id="hour"></select>
							</div>
							 <label for="quarter" class="col-sm-1 control-label">15分钟</label>
							<div class="col-sm-2">
								<select id="quarterSelect" class="form-control" multiple="multiple">
									<option value='0'>0</option>
									<option value='15'>15</option>
									<option value='30'>30</option>
									<option value='45'>45</option>
								</select>
							</div>

						</div>
					</form>
				</div>
				<div class="box-footer">
					<div class="pull-right">
						<a id="queryBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" onclick="query()"><span class="ladda-label">查询</span></a>
					</div>
				</div>
			</div>	
		</div>
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">查询数据</h3>
					<div class="box-tools pull-right">
						<a id="exportBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" onclick="exportFile()"><span>导出</span></a> 
                    </div>
				</div>
				<div class="box-body">
					<table id="AllDataTable"></table>
				</div>
			</div>
		</div>
		<!-- <div class="col-sm-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">丢包详情</h3>
				</div>
				<div class="box-body">
					<table id="packetLossDetailTable"></table>
				</div>
			</div>
		</div> -->
	</div>
</section>
@endsection
@section('scripts')
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<script type="text/javascript" src="dist/js/badCellAnalysis/lteTopcell.js"></script>
<!--select2-->
<script type="text/javascript" src="plugins/select2/select2.js"></script>

<!--treeview-->
<script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script>

<!-- highcharts -->
<script src="plugins/highcharts/js/highcharts.js"></script>
<!-- <script src="plugins/highcharts/js/modules/exporting.js"></script> -->
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
@endsection
