@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>存储管理</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-cogs"></i>系统管理
		</li>
		<li>权限管理</li>
		<li class="active">存储管理</li>
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
						<h3 class="box-title">城市</h3>
					</div>
					<div class="box-body">
						<div class="form-group"  style="height:500px; overflow:auto;overflow-x:hidden">
							<div id="cityTree"></div>
						</div>
						<input type="hidden" value="" id="cityValue">
					</div>
				</div>
			</div>
			<div class="col-sm-9">
				<div class="box">
					<div class="box-header">
						<div style="display:inline">
							<h3 class="box-title">下载任务</h3>
						</div>
						<div style="float:right;display:inline">
							<a id="addUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="addItem()"><span class="ladda-label">新增</span></a>
							<a id="deleteUser" class="btn btn-default ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="deleteItem()"><span class="ladda-label">删除</span></a>
							<a id="deleteUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="editItem()"><span class="ladda-label">修改</span></a>
							<a id="edit" class="btn btn-default ladda-button" data-style="expand-right" href="#" onClick="doQueryTask()"><span class="ladda-label">编辑</span></a>
						</div>
						<!-- <div style="float:right;display:inline">
							<a id="addUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="addItem()"><span class="ladda-label">新增</span></a>
							<a id="deleteUser" class="btn btn-default ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="deleteItem()"><span class="ladda-label">删除</span></a>
							<a id="deleteUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="editItem()"><span class="ladda-label">修改</span></a> 
						</div> -->
						
					</div>
					<div class="box-body">
						<table id="storeTable"></table>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- 新增和修改弹出框 -->
<div class="modal fade" id="add_edit_modal">
	<div class="modal-dialog" style="width:504px;">
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
						<label for="serverName" class="col-sm-4 control-label">ServerName：</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" name="serverName" id="serverName" placeholder="服务器名称" maxlength="50">
						</div>
					</div>
					<div class="form-group">
						<label for="citys" class="col-sm-4 control-label">City：</label>
						<div class="col-sm-7">
							<select class="form-control" name="citys" id="citys"></select>
						</div>
					</div>
					<div class="form-group">
						<label for="type" class="col-sm-4 control-label">Type：</label>
						<div class="col-sm-7">
							<select class="form-control" name="type" id="type"></select>
						</div>
					</div>
					<div class="form-group">
						<label for="externalAddress" class="col-sm-4 control-label">IpAddress:</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" name="ipAddress" id="ipAddress" placeholder="Ip地址" maxlength="30"/>
						</div>
					</div>
					<div class="form-group">
						<label for="sshUserName" class="col-sm-4 control-label">sshUserName:</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" name="sshUserName" id="sshUserName" placeholder="ssh用户名" maxlength="30"/>
						</div>
					</div>
					<div class="form-group">
						<label for="sshPassword" class="col-sm-4 control-label">sshPassword:</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" name="sshPassword" id="sshPassword" placeholder="ssh密码" maxlength="40"/>
						</div>
					</div>
					<div class="form-group">
						<label for="ftpUserName" class="col-sm-4 control-label">ftpUserName:</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" name="ftpUserName" id="ftpUserName" placeholder="ftp用户名" maxlength="30"/>
						</div>
					</div>
					<div class="form-group">
						<label for="ftpPassword" class="col-sm-4 control-label">ftpPassword:</label>
						<div class="col-sm-7">
							<input type="text" class="form-control" name="ftpPassword" id="ftpPassword" placeholder="ftp密码" maxlength="40"/>
						</div>
					</div>
					<div class="form-group">
						<label for="fileDir" class="col-sm-4 control-label">FileDir:</label>
						<div class="col-sm-7">
							<textarea class="form-control" name="fileDir" id="fileDir" style="height : 70px;resize: none;" maxlength="500"></textarea>
						</div>
					</div>
				
			</div>
			<div class="modal-footer">
				<button type="button" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="updateDownloadBtn" onclick="updateDownload()">保存</button>
				<button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
			</div>
			</form>
		</div>
	</div>
</div>
<!-- 新增和修改用户弹出框 -->
<div class="modal fade" id="edit_email">
	<div class="modal-dialog" style="width:900px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">定时任务</h8>
                <div class="box-tools pull-right">
                	<a id="editTaskFile" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="editTaskFile()"><span class="ladda-label">编辑</span></a>
                	<a id="saveTaskFile" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="saveTaskFile()"><span class="ladda-label">保存</span></a>
					<a id="cancelBtn" class="btn btn-default ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="cancelEdit()"><span class="ladda-label">取消</span></a> 
					&nbsp;&nbsp;&nbsp;
		        </div>
            </div>
			<div class="modal-body text-center">
				<textarea class="form-control" name="taskFileContent" id="taskFileContent" style="height : 500px;resize: none;"></textarea>
			</div>
			<!-- <div class="modal-footer">
				<button type="submit" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="saveBtn" onclick="saveEmailFile()">保存</button>
				<button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
			</div> -->
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
<link rel="stylesheet" href="dist/css/button.css">

@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/storeManage.js"></script>
<script type="text/javascript" src="dist/js/systemManage/taskManage.js"></script>

