@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>日活用户</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>系统管理
		</li>
		<li>功能管理</li>
		<li class="active">日活用户</li>
	</ol>
</section>
@endsection
@section('content')
<section class="content">
	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">查询条件</h3>
				</div>
				<div class="box-body">
					<form class="form-horizontal" role="form" id="queryForm">
						<div class="form-group">
							<label for="startDate" class="col-sm-1 control-label">起始日期</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="startDate" id="startDate">
								</input>
							</div>
							<label for="endDate" class="col-sm-1 control-label">结束日期</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="endDate" id="endDate">
								</input>
							</div>
						</div>
					</form>
				</div>
				<div class="box-footer">
					<div class="pull-right">
						<div class="btn-group">
		                    <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="query()" id="queryBtn">
		                        <i class="fa fa-search"></i>
		                        <span class="ladda-label">查询</span>
		                    </button>
		                </div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class="box-body">
					<div id="userChart"></div>
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
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<script src="plugins/highcharts/js/highcharts.js"></script>
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/activeUser.js"></script>