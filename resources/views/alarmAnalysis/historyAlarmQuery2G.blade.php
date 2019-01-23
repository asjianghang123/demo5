@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>GSM历史告警</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>日常优化
		</li>
		<li>
			<i class="fa fa-dashboard"></i>告警分析
		</li>
		<li class="active">GSM历史告警</li>
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
							<label for="startTime" class="col-sm-1 control-label">起始日期</label>
							<div class="col-sm-3">
								<input class="form-control" type="text" name="startTime" id="startTime">
							</div>
							<label for="endTime" class="col-sm-1 control-label">结束日期</label>
							<div class="col-sm-3">
								<input class="form-control" type="text" name="endTime" id="endTime">
							</div>
							<label for="areaName" class="col-sm-1 control-label">区域名</label>
							<div class="col-sm-3">
								<div class="input-group">									 
					               	<input type="text" class="form-control" name="areaName" id="areaName">
					               	<input type="file" class="hidden" name="fileImport" id="fileImport" onchange="toName(this)">
					               	<span class="input-group-btn">
					                  	<button class="btn btn-default" type="button" onclick="fileImport.click()">选择文件</button>
					               	</span>
					            </div>
							</div>
						</div>
						<div class="form-group">
							<label for="citys" class="col-sm-1 control-label">城市</label>
							<div class="col-sm-3">
								<select class="form-control" name="citys" id="citys" multiple="true">
								</select>
							</div>
							<label class="col-sm-1 control-label">区域维度</label>
							<div class="col-sm-3 checkbox">
								<input id='regionalDimension' checked data-toggle="toggle" data-on="BSC" data-off="BTS" data-onstyle="info" data-offstyle="success" data-width="200" data-size="mini" type="checkbox">
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
                            <a id="exportBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#">
                            <span class="ladda-label">导出</span></a>
				        </div>  
                    </div>
	            </div>
	            <div class="box-body">
	            	<table id="historyAlarmTable"> 
	            	</table>
	            </div>
				<!-- <div class="box-body" style="height:600px;overflow:auto;">
					<table id="historyAlarmTable"></table>
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

<!--input select-->
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

<!--treeview-->
<!-- <script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script> -->

<!--select2-->
<!-- <script type="text/javascript" src="plugins/select2/select2.js"></script> -->

<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

<!-- treegrid -->
<!-- <link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/easyui.css">
<link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/datagrid.css">
<link rel="stylesheet" href="dist/css/LTETemplateManage.css">
<script src="plugins/EasyUI/jquery.easyui.min.js"></script> -->

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

<!--bootstrapvalidator-->
<!-- <link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script> -->


<script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>

<!--bootatrap-toggle-->
<link href="plugins/bootstrap-toggle/css/bootstrap-toggle.min.css" rel="stylesheet">
<script type="text/javascript" src="plugins/bootstrap-toggle/js/bootstrap-toggle.min.js"></script>

@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/alarmAnalysis/historyAlarmQuery2G.js"></script>



