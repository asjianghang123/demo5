@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>小区PRB分析</h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-dashboard"></i>专项研究
		</li>
		<li><i class="fa fa-dashboard"></i>高干扰分析
		</li>
		<li class="active">小区PRB分析
		</li>
	</ol>
</section>
@endsection
@section('content')
<section class="content">
	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class='box-header with-border'>
					
						<h3 class="box-title">查询条件</h3>
						<div class="box-tools pull-right">
	                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                    </div>
					
				</div>
				<div class='box-body'>
					<form class="form-horizontal" role="form" id="queryForm">
						<div class="form-group">
							<label for="dateTime" class="col-sm-1 control-label">时间</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="dateTime" id="dateTime">
							</div>
							<label for="cell" class="col-sm-1 control-label">小区</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="cell" id="cell">
							</div>
						</div>
					</form>
				</div>
				<div class="box-footer">
					<div class="pull-right">
						<div class="btn-group">
		                    <!-- <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="query()" id="queryBtn">
		                        <i class="fa fa-search"></i>
		                        <span class="ladda-label">查询</span>
		                    </button> -->
		                    <a id="queryBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onclick="query()"><span class="ladda-label">查询</span></a>
		                </div>
					</div>
				</div>
			</div>
			<div class='box'>
				<div class="box-header with-border">
					<h3 class="box-title">小区PRB分析</h3>
					<div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
				</div>
				<div class="box-body" style="position:relative;">
					<!-- <div class="form-group">
						<label for="timeList" class="col-sm-1 control-label">时间</label>
						<div class="col-sm-3">
							<select class="form-control" type="text" name="timeList" id="timeList">
							</select>
						</div>
					</div> -->
					<div id="cellPRBAnalysis" style="position: relative;height: 400px;"></div>
					<!-- <div class="zhaozi" id="chart2_zhaozi"></div>
					<div class="loadingImg text-center" id="chart2_loadingImg">
						<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
					</div> -->
				</div>
			</div>
		</div>	
	</div>
</section>



@endsection
@section('scripts')
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<!-- jQuery 2.2.0 -->
<!-- datepicker -->
 <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
 <script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>

<!-- Bootstrap WYSIHTML5 -->

<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<script src="plugins/highcharts/js/highcharts.js"></script>

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
<script src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/genius/cellPRBAnalysis.js"></script>
<link rel="stylesheet" href="dist/css/button.css">
<style>
	.zhaozi{
		width:100%;
		height:100%;
		position:absolute;
		top:0;
		left:0;
		display:none;
		background-color:#000;
		opacity:.3;
		z-index:10;
	}
	.loadingImg{
		position:absolute;
		top:80px;
		width:100%;
		z-index:11;
		display:none;
	}
	.loadingImg > span{
		display: inline-block;
		padding: 10px 15px;
		background-color:#fff;
	}
</style>
