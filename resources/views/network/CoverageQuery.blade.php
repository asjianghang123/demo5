@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>覆盖率查询</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>日常优化
		</li>
		<li>
			指标分析
		</li>
		<li class="active">覆盖率查询</li>
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
							<label for="regionType" class="col-sm-1 control-label">区域维度</label>
							<div class="col-sm-2">
								<select class="form-control" name="regionType" id="regionType">
									<option value="city">城市</option>
									<option value="baseStation">基站</option>
									<option value="groupEcgi">小区</option>
								</select>
							</div>
							<label for="city" class="col-sm-1 control-label">城市</label>
							<div class="col-sm-2">
								<select class="form-control" name="city" id="city">
								</select>
							</div>
							<label for="baseStation" class="col-sm-1 control-label">基站</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" name="baseStation" id="baseStation" disabled></input>
							</div>
							<label for="groupEcgi" class="col-sm-1 control-label">小区</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" name="groupEcgi" id="groupEcgi" disabled></input>
							</div>
						</div>
						<div class="form-group">
							<label for="timeType" class="col-sm-1 control-label">时间维度</label>
							<div class="col-sm-2">
								<select class="form-control" name="timeType" id="timeType">
									<option value="day">天</option>
									<option value="hour">小时</option>
								</select>
							</div>
							<label for="date" class="col-sm-1 control-label">日期</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="date" id="date"></input>
                            </div>
                            <label for="hour" class="col-sm-1 control-label">小时</label>
							<div class="col-sm-2">
								<select class="form-control" multiple name="hour" id="hour"></select>
							</div>
						</div>
					</form>
				</div>
				<div class="box-footer">
					<div class="pull-right">
						<div class="btn-group">
		                    <!-- <button type="button" class="btn btn-primary ladda-button" data-style="expand-right" onclick="query()" id="queryBtn">
		                        <i class="fa fa-search"></i>
		                        <span class="ladda-label">查询</span>
		                    </button> -->
		                    <a id="queryBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" onclick="query()"><span class="ladda-label">查询</span></a>
		                </div>
		                <!-- <div class="btn-group">
		                    <button type="button" class="btn btn-primary ladda-button" data-style="expand-right" onclick="exportFile()" id="exportBtn">
		                        <i class="fa fa-sign-out"></i>
		                        <span class="ladda-label">导出</span>
		                    </button>
		                </div> -->
					</div>
				</div>
			</div>	
		</div>
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">弱覆盖</h3>
					<div class="box-tools pull-right">
						<a id="exportBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" onclick="exportFile()"><span>导出</span></a> 
                    </div>
				</div>
				<div class="box-body">
					<table id="coverageTable"></table>
				</div>
			</div>
		</div>
		<!-- <div class="col-sm-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">详情</h3>
				</div>
				<div class="box-body">
					<table id="coverageDetailTable"></table>
				</div>
			</div>
		</div> -->
	</div>
</section>
@endsection
@section('scripts')


<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

<!--select2-->
<script type="text/javascript" src="plugins/select2/select2.js"></script>

<!--treeview-->
<script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script>

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
<link rel="stylesheet" href="dist/css/button.css">
<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/genius/CoverageQuery.js"></script>