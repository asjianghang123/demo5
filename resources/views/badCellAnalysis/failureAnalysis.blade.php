@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>ENB原因值分析</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-rocket"></i>专项研究
		</li>
		<li>
			信令分析
		</li>
		<li class="active">ENB原因值分析</li>
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
							<label for="dataBase" class="col-sm-1 control-label">数据库</label>
							<div class="col-sm-3">
								<select class="form-control" name="dataBase" id="dataBase">
								</select>
							</div>
							<label for="process" class="col-sm-1 control-label">流程</label>
							<div class="col-sm-4">
								<select class="form-control" name="process" id="process">
								</select>
							</div>
						</div>
					</form>
				</div>
				<div class="box-footer">
					<div class="pull-right">
						<div class="btn-group">
		                    <!-- <button type="button" class="btn  btn-primary" onclick="query()" id="queryBtn">
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
					<h3 class="box-title">概要</h3>
			<!-- 		<ul class="nav nav-tabs" role="tablist" style="float: right">
					<li class="active"><a href="#table_tab_0" data-toggle="tab" id="table_tab_0_nav"
						aria-expanded="false">图</a></li>
					<li class=""><a href="#table_tab_1" data-toggle="tab" id="table_tab_1_nav"
						aria-expanded="false">表</a></li>
				</ul> -->
					<div class="btn-div pull-right">
						<div class="btn-group">
						<a type="button" class='btn' href="#table_tab_0" data-toggle="tab" id="table_tab_0_nav"
						aria-expanded="false"><i class="fa fa-picture-o"></i></a>
                        <a type="button" class='btn' href="#table_tab_1" data-toggle="tab" id="table_tab_1_nav"
						aria-expanded="false"><i class="fa fa-bars"></i></a></li>
		                </div>
					</div>





				</div>
				
				<div class="tabs tab-content">
					<div class=" tab-pane active" id="table_tab_0">	          
						<div class="box-body">
				            <div id="resultView" style="height:400px;"></div>
			            </div>
					</div>
					<div class=" tab-pane" id="table_tab_1">
						<div class="box-body">
							<input type="hidden" id="selectedResult">
							<table id="resultTable">
							</table>
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
						<!-- <button type="button" class="btn btn-primary" onclick="openProcedure()">
	                        <i class="fa fa-server"></i>筛选流程
	                    </button> -->
	                    <a class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onclick="openProcedure()"><span class="ladda-label">筛选流程</span></a>
	                    <!-- <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="exportFile()" id="exportBtn">
	                        <i class="fa fa-sign-out"></i>导出
	                    </button> -->
	                    <a id="exportBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onclick="exportFile()"><span class="ladda-label">导出</span></a>
	                </div>
				</div>
				<div class="box-body">
					<input type="hidden" id="ueRef">
					<table id="detailTable">
					</table>
				</div>
			</div>
		</div>
	</div>

	
</section>

@endsection


@section('scripts')


<!-- <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script> -->

<!--input select-->
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

<!--select2-->
<script type="text/javascript" src="plugins/select2/select2.js"></script>

<!--treeview-->
<script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script>

<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>

<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!-- highcharts -->
<script src="plugins/highcharts/js/highcharts.js"></script>
<script src="plugins/highcharts/js/modules/exporting.js"></script>

<!-- raphael -->
<!-- <script src="plugins/raphael/raphael-min.js"></script> -->

<!-- treegrid -->
<!-- <link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/easyui.css">
<link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/datagrid.css">
<link rel="stylesheet" href="dist/css/signalingBacktracking.css">
<script src="plugins/EasyUI/jquery.easyui.min.js"></script>
<script src="plugins/EasyUI/locale/datagrid-scrollview.js"></script> -->



<!--bootstrapvalidator-->
<!-- <link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script> -->

<style>
	.select2-container .select2-selection--single{
		height:34px;
		border-radius:0;
	   	border: 1px solid #d2d6de;
	}
	.select2-container--default .select2-selection--single .select2-selection__arrow{
		top:3px;
	}
	.node-processQueryTree{
		word-break: break-all;
	}
</style>


@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/badCellAnalysis/failureAnalysis.js"></script>



