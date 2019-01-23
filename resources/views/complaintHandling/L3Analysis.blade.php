@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>NAS原因值分析</h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-dashboard"></i>日常优化
		</li>
		<li>
			原因值分析
		</li>
		<li class="active">NAS原因值分析</li>
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
							<label for="city" class="col-sm-1 control-label">城市</label>
							<div class="col-sm-3">
								<select class="form-control" name="city" id="citys">
								</select>
							</div>
							<label for="date" class="col-sm-1 control-label">日期</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="date" id="date">
                                </input>
                            </div>
                            <input type="hidden" id="eventName">
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
		                    <a id="queryBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onclick="query()"><span class="ladda-label">查询</span></a>
		                </div>
					</div>
				</div>
			</div>	
		</div>
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title" style="margin-top:8px;">成功率</h3>
					<div class="btn-div pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body" style="max-height:500px;">
					<div id="successChart" ></div>
				</div>
			</div>
		</div>
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title" style="margin-top:8px;">原因值</h3>
					<div class="btn-div pull-right">
						<div class="btn-group">
                         	<a type="button" class="btn" title="图" onclick="switchTab(table_tab_1,table_tab_0,'chart')">
								<i class="fa fa-picture-o"></i>
							</a>  
							<a type="button" class="btn" title="表" onclick="switchTab(table_tab_0,table_tab_1,'table')">
								<i class="fa fa-bars"></i>
							</a> 
		                </div>
					</div>
				</div>
				<div class="box-body">
					<div class="tabs tab-content" id="table_chart" >

						<div class=" tab-pane active" id="table_tab_0">
							<table id="L3Table">
				            </table>
				            <input type="hidden" id="selectedResult">
				        </div>
						<div class=" tab-pane" id="table_tab_1" style="height:400px;overflow:auto;">
						    <div class="loadingImg text-center" id="chart_loadingImg">
								<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
							</div>
							<div id="L3Chart" >
		            		</div>
		            		<button id="backBtn" class="btn btn-default" style="position:absolute;top:65px;right:60px;display:none;">◁ Back to previous</button>
						</div>
				    </div>
				</div>
			</div>
		</div>
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">详情</h3>
					<div class="pull-right">
	                    <!-- <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="exportFile()" id="exportBtn">
	                        <i class="fa fa-sign-out"></i>导出
	                    </button> -->
	                    <a id="exportBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onclick="exportFile()">
                        <span class="ladda-label">导出</span></a>
	                </div>
				</div>
				<div class="box-body">
					<div class="tabs tab-content ">
					<table id="detailTable"></table>
					</div>
				</div>
				
			</div>
		</div>
	</div>

	
</section>

@endsection


@section('scripts')

<script type="text/javascript" src="plugins/select2/select2.js"></script>
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
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
<script src="plugins/highcharts/js/highcharts-more.js"></script>
<script src="plugins/highcharts/js/modules/solid-gauge.js"></script>

<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=XtxLWdHvIBw0FKDLBh835SwO"></script>
<style type='text/css'>
	.datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
    background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
    border-color: #ffb733;
	}	
</style>

@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/complaintHandling/L3Analysis.js"></script>
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

