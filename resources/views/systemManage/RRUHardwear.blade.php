@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>RRU硬件能力查询</h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-dashboard"></i>专项研究
		</li>
		<li>硬件分析</li>
		<li class="active">RRU硬件能力查询
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
				</div>
				<div class='box-body'>
					<form class="form-horizontal" role="form" id="queryForm">
						<div class="form-group">
							<label for="date" class="col-sm-1 control-label">日期</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="date" id="date">
							</div>
							<label for="rulogicalid" class="col-sm-1 control-label">2G RU型号</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="rulogicalid" id="rulogicalid" placeholder="2G RU型号">
							</div>
                            <label for="ruserialno" class="col-sm-1 control-label">2G串号</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="ruserialno" id="ruserialno" placeholder="2G串号">
							</div>
						</div>
                        <div class="form-group">
							<label for="productName" class="col-sm-1 control-label">4G RU型号</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="productName" id="productName" placeholder="4G RU型号">
							</div>
							<label for="serialNumber" class="col-sm-1 control-label">4G串号</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="serialNumber" id="serialNumber" placeholder="4G串号">
							</div>
                            <label for="ecgi" class="col-sm-1 control-label">4G ECGI</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="ecgi" id="ecgi" placeholder="4G ECGI">
							</div>
						</div>
                        <div class="form-group">
							<label for="cell_2g" class="col-sm-1 control-label">2G cell ID</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="cell_2g" id="cell_2g" placeholder="2G cell ID">
							</div>
							<label for="bsc" class="col-sm-1 control-label">bsc</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="bsc" id="bsc" placeholder="bsc">
							</div>
							<label for="subNetwork" class="col-sm-1 control-label">subNetwork</label>
							<div class="col-sm-3">
								<!-- <input type="text" class="form-control" name="subNetwork" id="subNetwork" placeholder="subNetwork"> -->
								<select class="form-control" name="subNetwork" id="subNetwork" multiple="multiple"></select>
							</div>
						</div>
					</form>
				</div>
				<div class="box-footer">
					<div class="pull-right">
						<div class="btn-group">
		                    <a type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="query()" id="queryBtn">
		                        <span class="ladda-label">查询</span>
		                    </a>
		                </div>
                        <div class="btn-group">
		                    <a type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="exportData()" id="exportBtn">
		                        <span class="ladda-label">导出</span>
		                    </a>
		                </div>
					</div>
				</div>
			</div>
			<div class='box'>
				<div class="box-header with-border">
					<h3 class="box-title">查询结果</h3>
				</div>
				<div class="box-body">
					<div class="table-responsive">
						<table id="RRUHardwearTable">
						</table>
					</div>
				</div>	
			</div>
		</div>	
	</div>
</section>



@endsection
@section('scripts')
<link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>
<!-- bootstrap-table-->
<link href="plugins/bootstrap-table/bootstrap-table.css" rel="stylesheet" />
<script type="text/javascript" src="plugins/bootstrap-table/bootstrap-table.js"></script>
<script type="text/javascript" src="plugins/bootstrap-table/locale/bootstrap-table-zh-CN.js"></script>
<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>
<style type='text/css'>
    .datepicker table tr td.today, 
    .datepicker table tr td.today:hover, 
    .datepicker table tr td.today.disabled, 
    .datepicker table tr td.today.disabled:hover {
        background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
        border-color: #ffb733;
    }
	.fixed-table-body{
		height:auto !important;
	}
    
</style>
@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/RRUHardwear.js"></script>

