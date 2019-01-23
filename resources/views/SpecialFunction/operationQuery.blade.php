@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>操作查询</h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-thumbs-up"></i>特色功能
		</li>
		<li>参数
		</li>
		<li class="active">操作查询</li>
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
								<input size="16" class="form-control" type="text" id="startTime">
    							<span class="add-on"><i class="icon-remove"></i></span>
    							<span class="add-on"><i class="icon-calendar"></i></span>
							</div>
							<label for="endTime" class="col-sm-1 control-label">结束日期</label>
							<div class="col-sm-3">
								<input size="16" class="form-control" type="text" id="endTime">
    							<span class="add-on"><i class="icon-remove"></i></span>
    							<span class="add-on"><i class="icon-calendar"></i></span>
							</div>
							<label for="endTime" class="col-sm-1 control-label"></label>
							<div class="col-sm-3">
								<div class="btn-group pull-right">
									<div class="btn-group">
                        				<a id="queryBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onclick="query('table')"><span class="ladda-label">查询</span></a>
				        			</div>  
	                			</div>
							</div>
							
						</div>
						<div class="form-group">
							<label for="city" class="col-sm-1 control-label">城市</label>
							<div class="col-sm-3">
								<select id="city" class="form-control input-sm">
								</select>
							</div>
							<label for="zhanNo" class="col-sm-1 control-label">站号</label>
							<div class="col-sm-3">
								<!-- <input type="text" class="form-control" name="site" id="site"> -->
								<div class="input-group">
									<input type="text" class="form-control" id="site">
								<input type="file" class="hidden" name="fileImport" id="fileImport" onchange="toName(this)">
								<span class="input-group-btn">
								    <button class="btn btn-default" type="button" onclick="fileImport.click()">选择文件</button>
								</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="action_type" class="col-sm-1 control-label">操作类型</label>
							<div class="col-sm-3">
								<select class="form-control input-sm" name="action_type" id="action_type" multiple="multiple"></select>
							</div>
							<label for="action_source" class="col-sm-1 control-label">操作来源</label>
							<div class="col-sm-3">
								<select class="form-control" name="action_source" id="action_source" multiple="multiple"></select>
							</div>
							<label for="params" class="col-sm-1 control-label">参数</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="params" id="params">
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="box">
				<div class="box-header with-border">
			        <h3 class="box-title">查询数据</h3>
			        <div class="box-tools pull-right">
                        <div class="btn-group">
                        	<a class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onclick="getparam('table')" id="queryBtn_param">
                            <span class="ladda-label">统计</span></a>
				        </div>  
				        <div class="btn-group">
                            <a id="exportBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onclick="query('file')">
                            <span class="ladda-label">导出</span></a>
				        </div>  
                    </div>
	            </div>
	            <div class="box-body">
	            	<table id="operationQueryTable"> 
	            	</table>
	            </div>
			</div>
		</div>
	</div>
</section>
<!-- 统计信息弹出框 -->
<div class="modal fade" id="config_information">
	<div class="modal-dialog" style="width:900px;">
		<div class="modal-content">
			<div class="modal-header">
				<h8 class="modal-title" id="mtitle">参数列表</h8>
				<div class="box-tools pull-right">
	                <div class="btn-group">
	                    <a class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onclick="getparam('file')" id="exportBtn_param">
	                    <span class="ladda-label">导出</span></a>
					</div>
				</div>
            </div>
            <div class="box-body">
					<table id="paramTable"></table>
			</div>
			
			<div class="modal-footer">
				<div class="btn-group">
	                <a class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onclick="updateConfigInfo()" id="saveBtn">
	                <span class="ladda-label">确定</span></a>
	            </div>
				<!-- <button type="button" name="saveBtn" class="col-sm-1 col-sm-offset-11 btn btn-primary" id="saveBtn" onclick="updateConfigInfo()">确定</button> -->
			</div>
		</div>
	</div>
</div>		
@endsection

@section('scripts')


<link type="text/css" rel="stylesheet" href="plugins/bootstrap-datetimepicker-master/css/bootstrap-datetimepicker.css">
<script type="text/javascript" src="plugins/bootstrap-datetimepicker-master/js/bootstrap-datetimepicker.js"></script>

<!--input select-->
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

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

<script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>

<!--bootatrap-toggle-->
<link href="plugins/bootstrap-toggle/css/bootstrap-toggle.min.css" rel="stylesheet">
<script type="text/javascript" src="plugins/bootstrap-toggle/js/bootstrap-toggle.min.js"></script>

@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/SpecialFunction/operationQuery.js"></script>

