@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>A2门限分析</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>网络规划
		</li>
		<li>
			<i class="fa fa-dashboard"></i>门限分析
		</li>
		<li class="active">A2门限分析</li>
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
					<form class="form-inline" role="form" id="queryForm">
						<div class="form-group">
						城市：
						</div>
						<div class="form-group">
							<label class="sr-only"></label>
		    				<p class="form-control-static">
		    					<select id="city" class="form-control input-sm">
								</select>  
		    				</p>					
					  	</div>
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
				<div class="box-footer">
					<div class="pull-right">
						<div class="btn-group">
		                    <!-- <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="query()" id="queryBtn">
		                        <i class="fa fa-search"></i>
		                        <span class="ladda-label">查询</span>
		                    </button> -->
		                    <a id="queryBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onclick="query()"><span class="ladda-label">查询</span></a>
		                </div>
		                <!-- <div class="btn-group">
		                    <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="exportFile()" id="exportBtn">
		                        <i class="fa fa-sign-out"></i>导出
		                    </button>
		                </div> -->
					</div>
				</div>
			</div>
			<div class="box">
				<div class="box-header with-border">
			        <h3 class="box-title">查询数据</h3>
			        <div class="box-tools pull-right">
                        <div class="btn-group">
                         <a id="exportBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onclick="exportFile()">
                         <span class="ladda-label">导出</span></a>
				        </div>  
                    </div>
		        </div>
		        <div class="box-body">
		          	<table id="A2ThresholdTable"> 
		          	</table>
		        </div>
				<!-- <div class="box-body" style="height:600px;overflow:auto;">
					<table id="A2ThresholdTable"></table>
				</div> -->
			</div>
		</div>
		
	</div>
	<!-- <div class="row">
		<div class="col-sm-12">
			
		</div>
	</div> -->
</section>

@endsection


@section('scripts')
<link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

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
<script type="text/javascript" src="dist/js/NetworkOptimization/A2ThresholdAnalysis.js"></script>



