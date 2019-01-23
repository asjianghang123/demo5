@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>下载管理</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>系统管理
		</li>
		<li>权限管理</li>
		<li class="active">下载管理</li>
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
						<h3 class="box-title">log类型</h3>
						<div style="float:right;display:inline">
							<input id='checkedType' checked data-toggle="toggle" data-on="检测开" data-off="检测关" data-onstyle="info" data-offstyle="success" data-width="100" data-size="mini" type="checkbox">
						</div>
					</div>
					<div class="box-body">
						<div class="form-group"  style="height:600px; overflow:auto;overflow-x:hidden">
							<div id="downloadTypeTree"></div>
						</div>
						<input type="hidden" value="" id="downloadTypeValue">
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
						<table id="downloadTable"></table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- 新增和修改弹出框 -->
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
					<input type="hidden" name="downloadId" id="downloadId" value="">
					<div class="form-group">
						<label for="serverName" class="col-sm-2 control-label">ServerName：</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="serverName" id="serverName" placeholder="服务器名称" maxlength="50">
						</div>
					</div>
					<div class="form-group">
						<label for="citys" class="col-sm-2 control-label">City：</label>
						<div class="col-sm-4">
							<select class="form-control" name="citys" id="citys"></select>
						</div>
						<label for="type" class="col-sm-2 control-label">Type：</label>
						<div class="col-sm-4">
							<select class="form-control" name="type" id="type"></select>
						</div>
					</div>
					<div class="form-group">
						<label for="externalAddress" class="col-sm-2 control-label">ExternalAddress:</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="externalAddress" id="externalAddress" placeholder="外网地址" maxlength="30"/>
						</div>
						<label for="internalAddress" class="col-sm-2 control-label">InternalAddress:</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="internalAddress" id="internalAddress" placeholder="内网地址" maxlength="30"/>
						</div>
					</div>
					<div class="form-group">
						<label for="userName" class="col-sm-2 control-label">UserName:</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="userName" id="userName" placeholder="用户名" maxlength="30"/>
						</div>
						<label for="password" class="col-sm-2 control-label">Passowrd:</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="password" id="password" placeholder="密码" maxlength="40"/>
						</div>
					</div>
					<div class="form-group">
						<label for="subNetwork" class="col-sm-2 control-label">SubNetwork:</label>
						<div class="col-sm-4">
							<textarea class="form-control" name="subNetwork" id="subNetwork" style="height : 100px;resize: none;" maxlength="500"></textarea>
						</div>
						<label for="fileDir" class="col-sm-2 control-label">FileDir:</label>
						<div class="col-sm-4">
							<textarea class="form-control" name="fileDir" id="fileDir" style="height : 100px;resize: none;" maxlength="500"></textarea>
						</div>
					</div>
				
			</div>
			<div class="modal-footer">
				<button type="button" class="col-sm-1 col-sm-offset-5 btn btn-primary" id="updateDownloadBtn" onclick="updateDownload()">保存</button>
				<button type="button" class="col-sm-1 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
			</div>
			</form>
		</div>
	</div>
</div>
@endsection


@section('scripts')

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
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!--bootstrapvalidator-->
<link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script>
<!--bootatrap-toggle-->
<link href="plugins/bootstrap-toggle/css/bootstrap-toggle.min.css" rel="stylesheet">
<script type="text/javascript" src="plugins/bootstrap-toggle/js/bootstrap-toggle.min.js"></script>
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/downloadManage.js"></script>
<style>
	.connectedSuccess{
		color:green;
		text-align:center!important;
	}
	.connectedFailed{
		color:red;
		text-align:center!important;
	}
</style>