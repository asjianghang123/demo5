@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>邮箱管理</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>系统管理
		</li>
		<li class="active">邮箱管理</li>
	</ol>
</section>
@endsection
@section('content')


<section class="content">
	<div class="row">
		<div class=" tab-pane active">
			<div class="col-sm-3">
				<div class='box'>
					<div class="box-header with-border">
						<h3 class="box-title">邮箱角色</h3>
						<div class="box-tools pull-right">
		                    <button type="button"  class="btn btn-box-tool" onClick="addScope()" title="新增"><i class="fa fa-file-o"></i></button>
                        	<button type="button"  class="btn btn-box-tool"  onClick="deleteScope()" title="删除"><i class="fa fa-scissors"></i></button>
		                </div>
					</div>
					<div class="box-body">
						<div class="form-group"  style="height:600px; overflow:auto;overflow-x:hidden">
							<div id="EmailQueryTree"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-9">
				<div class="box">
					<div class="box-header">
						<div style="display:inline">
							<h3 class="box-title">详细信息</h3>
						</div>
						<div style="float:right;display:inline">
							<a id="addUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="addItem()"><span class="ladda-label">新增</span></a>
							<a id="deleteUser" class="btn btn-default ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="deleteItem()"><span class="ladda-label">删除</span></a> 
							<a id="deleteUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="editItem()"><span class="ladda-label">修改</span></a> 
						</div>						
					</div>
					<div class="box-body">
						<input type="hidden" id="scopeN" value="">
                		<input type="hidden" id="roleN" value="">
						<table id="emailTable"></table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- 新增修改用户弹出框 -->
<div class="modal fade" id="add_edit_modal">
	<div class="modal-dialog" style="width:900px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">详细信息</h8>
            </div>
			<form class="form-horizontal" role="form" id="downloadForm"> 
				<div class="modal-body text-center">
					<input type="hidden" name="id" id="id" value="">
					<div class="form-group">
						<label for="serverName" class="col-sm-2 control-label">邮箱：</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="mailAddress" id="mailAddress" placeholder="邮箱" maxlength="50">
						</div>
					</div>
					<div class="form-group">
						<label for="citys" class="col-sm-2 control-label">Name：</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="name" id="name" placeholder="姓名" maxlength="50">
						</div>
						<label for="role" class="col-sm-2 control-label">Scope：</label>
						<div class="col-sm-4">
							<!-- <input type="text" class="form-control" name="role" id="role" placeholder="用户角色" maxlength="50"> -->
							<select class="form-control" name="scope" id="scope"></select>
						</div>
					</div>
					<div class="form-group">
						<label for="citys" class="col-sm-2 control-label">City：</label>
						<div class="col-sm-4">
							<!-- <input type="text" class="form-control" name="city" id="city" placeholder="城市" maxlength="50"> -->
							<select class="form-control" name="city" id="city"></select>
						</div>
						<label for="citys" class="col-sm-2 control-label">Role：</label>
						<div class="col-sm-4">
							<!-- <input type="text" class="form-control" name="scope" id="scope" placeholder="邮箱角色" maxlength="50"> -->
							<select class="form-control" name="role" id="role"></select>
						</div>
					</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="col-sm-1 col-sm-offset-5 btn btn-primary" id="updateDownloadBtn" onclick="insertDownload()">保存</button>
				<button type="button" class="col-sm-1 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
			</div>
			</form>
		</div>
	</div>
</div>

<!-- 新增scope弹出框 -->
<div class="modal fade" id="add_scope">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">添加角色</h8>
            </div>
			<form class="form-horizontal" role="form" id="scopeForm">
				<div class="modal-body text-center">
					<div class="form-group">
						<label for="modeName" class="col-sm-2 col-sm-offset-2 control-label">scope：</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="scopeName" id="scopeName" placeholder="scope" maxlength="50">
						</div>
						<br>
						<label for="modeName" class="col-sm-2 col-sm-offset-2 control-label">role：</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="roleName" id="roleName" placeholder="role" maxlength="50">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" onclick="updateScope()">保存</button>
					<button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection


@section('scripts')


<!-- <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script> -->

<!--input select-->
<!-- <script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" /> -->

<!--treeview-->
<script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script>

<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

<!--loading-->
<link rel="stylesheet" href="plugins/loading/css/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!--bootstrapvalidator-->
<link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script>
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/emailManage.js"></script>

