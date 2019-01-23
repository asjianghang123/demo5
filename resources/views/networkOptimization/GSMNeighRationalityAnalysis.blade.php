@extends('layouts.nav')

@section('content')
<section class="content-header">
	<h1>2G邻区合理性分析</h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-dashboard"></i>网络规划
		</li>
		<li>邻区合理性分析
		</li>
		<li class="active">2G邻区合理性分析</li>
	</ol>
</section>

<section class="content">
<div class="row"> 
<div class="col-sm-12">
	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title">查询条件</h3>
			<div class="box-tools pull-right">
                <div class="btn-group">
	                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> 
                </div>
            </div>
		</div>
		<div class="box-body">
			<form class="form-inline">
			  	<div class="form-group">
				日期：
				</div>
				<div class="form-group">
					<label class="sr-only"></label>
    				<p class="form-control-static">
    					<input id="dateTime" class="form-control" type="text" value=""/> 
    				</p>					
			  	</div>
			</form>
		</div>
		<div class='box-footer' style="text-align:right;">
			<input id="filename" hidden='true' />
			<input id="filenameGSM" hidden='true' />
			<input id="filenameLte" hidden='true' />
			<a id="search" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="searchGSM()"><span class="ladda-label">查询</span></a>
		</div>	
	</div>
	<div class="box">
		<div class="box-body">
			 <div class="chart tab-pane active" id="chart-gsmNeighRation" style="position: relative;height: 400px;"></div>
		</div>
	</div>
</div>
</div>
</section>
@endsection

@section('scripts')
<script src="plugins/highcharts/js/highstock.js"></script>
<script src="plugins/highcharts/js/highcharts-more.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>

<script type="text/javascript" src="dist/js/NetworkOptimization/GSMNeighRationalityAnalysis.js"></script>

<style type='text/css'>
	.datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
    background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
    border-color: #ffb733;
	}	
</style>
@endsection
