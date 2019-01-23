@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>入库管理</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>系统管理
		</li>
		<li class="active">入库管理</li>
	</ol>
</section>
@endsection
@section('content')


<section class="content">
	<div class="row">
		<div class="col-sm-3">
			<div class='box'>
				<div class="box-header with-border">
					<h3 class="box-title">入库类型</h3>
				</div>
				<div class="box-body">
					<div class="form-group"  style="height:600px; overflow:auto;overflow-x:hidden">
						<div id="storageQueryTree"></div>
					</div>
					<input type="hidden" value="" id="storageFlag">
				</div>
			</div>	
		</div>
		<div class="col-sm-9">	          
			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
				<div class="box">
					<div class="box-header with-border">
						<div style="display:inline">
							<h3 class="box-title">详细信息</h3>
						</div>
						<div class="box-tools" style="float:right;display:inline">
		                   <div class="btn-group">
			                    <a class="btn btn-primary" href="#" onClick="addTask()"><span class="fa fa-plus">新建</span></a>
		                    </div>
		                    <div class="btn-group">
			                    <a class="btn btn-default" href="#" onClick="deleteTask()"><span class="fa fa-remove">删除</span></a>
		                    </div>
		                    <div class="btn-group" style="display:none">
			                    <a class="btn btn-primary" href="#" onClick="runTask()"><span class="fa fa-play">启动</span></a>
		                    </div>
		                    <div class="btn-group">
			                    <a class="btn btn-danger" href="#" onClick="stopTask()"><span class="fa fa-stop">停止</span></a>
		                    </div>
							<div class="btn-group" style="display:none">
								<button id="fileExport" type="button" class="btn  btn-danger" onclick="exportFile()">
									<i class="fa fa-stop"></i>导出
								</button>
							</div>
		            	</div>
					</div>
	            	<div class="box-body">
		            	<table id="storageTable">
		            	</table>
	            	</div>
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
</section>
<!-- 新增弹出框 -->
<div class="modal fade" id="add_task">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">新建任务</h8>
            </div>
			<div class="modal-body row">
				<div class="col-sm-12">
					<div class="box">
						<div class="box-body">
							<form class="form-horizontal" role="form" id="taskName_form" style="margin-bottom:0">
								<div class="form-group" style="margin-bottom:0">
									<label for="taskName" class="control-label" style="text-align: left;display:inline-block;float:left;padding-left:15px">任务名称：</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" name="taskName" id="taskName" placeholder="只能包含数字，字母，$和_" maxlength="18">
									</div>
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
						<div class="box-body">
							<div class="input-group">
								<input type="hidden" id="paramsQueryDataTrace_change">
				           		<input type="text" class="form-control" id="paramsQueryDataTrace" aria-describedby="basic-addon1" placeholder="请输入目录查询" />
		              		 	<span class="input-group-btn">
					                <button class="btn btn-default" id="dataTraceQueryBtn" type="button" onClick="searchDataTraceQuery()">
					                     &nbsp;<span class="glyphicon glyphicon-search btn-group-lg" aria-hidden="true"></span>
					                </button>
					                <button class="btn btn-default" type="button" onClick="clearDataTraceQuery()">
										&nbsp;<span class="glyphicon glyphicon-remove btn-group-lg" aria-hidden="true"></span>
									</button>
				               	</span>
			            	</div>
							<br />
							<div class="form-group" id="DataTraceQueryTreeDiv"  style="height:250px; overflow:auto;overflow-x:hidden;">
								<div id="DataTraceQueryTree"></div>
							</div>
							<input type="hidden" value="" id="dataTraceFlag">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="saveBtn" onclick="saveTask()">执行</button>
				<button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<!--treeview-->
<script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script>

<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!--bootstrapvalidator-->
<link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script>

<script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>

<style>
.treeview .list-group-item {
    cursor: pointer;
    word-break: break-all;
}
#storageTable td div{
	width:100%;
	overflow:hidden;
	white-space:nowrap;
	text-overflow:ellipsis;
}
</style>
@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/storageManage.js"></script>

