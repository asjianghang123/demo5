@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>数据下载</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>系统管理
		</li>
		<li class="active">数据下载</li>
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
							<label for="logType" class="col-sm-1 control-label">数据源</label>
							<div class="col-sm-3">
								<select class="form-control" name="logType" id="logType">
								</select>
							</div>
							<label for="baseStation" class="col-sm-2 control-label"></label>
							<label for="node" class="col-sm-1 control-label">城市</label>
							<div class="col-sm-3">
								<select class="form-control" name="node" id="node">
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="fileName" class="col-sm-1 control-label">时间段</label>
							<div class="col-sm-3">
								<select class="form-control" name="fileName" id="fileName" multiple="true">
								</select>
							</div>
							<label for="baseStation" class="col-sm-2 control-label"></label>
							<label for="baseStation" class="col-sm-1 control-label">基站</label>
							<div class="col-sm-3">
								<div class="input-group">
									<input type="text" class="form-control" id="cellInput">
								<input type="file" class="hidden" name="fileImport" id="fileImport" onchange="toName(this)">
								<span class="input-group-btn">
								    <button class="btn btn-default" type="button" onclick="fileImport.click()">选择文件</button>
								</span>
								</div>

								<!-- <input type="text" class="form-control" name="baseStation" id="baseStation"> -->
								
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
		                <div class="btn-group">
		                    <button type="button" class="btn  btn-primary" onclick="exportFile()" id="exportBtn">
		                        <i class="fa fa-sign-out"></i>导出
		                    </button>
		                </div>
		                <div class="btn-group hidden" id="storageBtn_div">
		                    <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="onlineStorage()" id="storageBtn">
		                        <i class="fa fa-level-up"></i>入库
		                    </button>
		                </div>
		                <div class="btn-group hidden" id="storage_div">
		                    <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="storage()" id="storage">
		                        <i class="fa fa-level-up"></i>入库
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
				<div class="box-body" style="height:400px;overflow:auto;">
					<table id="fileTable"></table>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- 新增弹出框 -->
<div class="modal fade" id="add_task">
	<div class="modal-dialog" style="width:600px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">新增任务</h8>
            </div>
			<div class="modal-body row">
				<!-- <div class="col-sm-6">
					<div class='box' id="chooseEventBox">
						<div class="box-header with-border">
							<h3 class="box-title">事件选择</h3>
						</div>
						<div class="box-body">
							<div class="form-group" style="height:334px; overflow:auto;;overflow-x:hidden;">
								<div id="eventQueryTree"></div>
							</div>
						</div>
					</div>
				</div> -->
				<div class="col-sm-12">
					<div class="box">
						<div class="box-header with-border">
							<h3 class="box-title">任务名称</h3>
						</div>
						<div class="box-body">
							<form id="taskName_form">
								<div class="form-group">
									<!-- <label for="taskName" class="control-label">任务名称：</label> -->
									<input type="text" class="form-control" name="taskName" id="taskName" placeholder="只能包含数字，字母，$和_" maxlength="18">
								</div>
								<div class="form-group" style="margin-bottom:0;padding-left:15px;padding-top:10px;display:none;" id="ctrTypeDiv">
									<label class="radio-inline">
								        <input type="radio" name="ctrType" class="ctrType" value="ctr" checked> 统计分析
								    </label>
								    <label class="radio-inline">
								        <input type="radio" name="ctrType" class="ctrType" value="ctrFull"> 协议分析
								    </label>
								</div>
							</form>
						</div>
					</div>
					<div class="box">
						<div class="box-header with-border">
							<h3 class="box-title">日志</h3>
						</div>
						<div class="box-body" style="height:200px; overflow:auto;overflow-x:hidden">
				            <p id="log" style="word-wrap: break-word;"></p>
			            </div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="col-sm-2 col-sm-offset-4 btn btn-primary ladda-button" data-color='red' data-style="expand-right" id="saveBtn" onclick="saveTask()">运行</button>
				<button type="button" class="col-sm-2 btn btn-default ladda-button" id="cancelBtn2" data-color='red' data-style="expand-right" data-dismiss="modal">取消</button>
			</div>
		</div>
	</div>
</div>
@endsection


@section('scripts')


<!-- <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script> -->

<!--input select-->
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

<!--treeview-->
<script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script>

<!--select2-->
<!-- <script type="text/javascript" src="plugins/select2/select2.js"></script> -->

<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

<!-- treegrid -->
<link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/easyui.css">
<link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/datagrid.css">
<link rel="stylesheet" href="dist/css/LTETemplateManage.css">
<script src="plugins/EasyUI/jquery.easyui.min.js"></script>

<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!--bootstrapvalidator-->
<link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script>


<script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>

@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/dataSourceManage.js"></script>



