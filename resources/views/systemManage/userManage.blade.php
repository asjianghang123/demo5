@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>账户管理</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>系统管理
		</li>
		<li class="active">用户管理</li>
	</ol>
</section>
@endsection
@section('content')


<section class="content">
	<div class="row">
		
			<div class="col-sm-3">
				<div class='box'>
					<div class="box-header with-border">
						<div style="display:inline">
							<h3 class="box-title">用户类型</h3>
						</div>
						<div class="input-group" style="float:right;display:inline">
			               	<a id="addUserType" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" onClick="addUserType()"><span class="ladda-label">新建</span></a>
			               	<a id="modifyPermissionBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" onClick="modifyPermission()"><span class="ladda-label">权限</span></a>
							<a id="deleteUserType" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" onClick="deleteUserType()"><span class="ladda-label">删除</span></a> 
			            </div>
					</div>
					
					<div class="box-body">
						<div class="form-group"  style="height:600px; overflow:auto;overflow-x:hidden">
							<div id="userTypeTree"></div>
						</div>
						<input type="hidden" value="" id="userTypeValue">
					</div>
				</div>
			</div>
			<div class="col-sm-9">
				<div class="box">
					<div class="box-header with-border">
						<div style="display:inline">
							<h3 class="box-title">详细信息</h3>
						</div>
						<div class="input-group" style="float:right;display:inline">
							<a id="addUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="addUser()"><span class="ladda-label">新增</span></a>
							<a id="deleteUser" class="btn btn-default ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="deleteUser()"><span class="ladda-label">删除</span></a> 
							<a id="editUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="editUser()"><span class="ladda-label">修改</span></a> 
						</div>
						
					</div>
					<div class="box-body">
						<table id="userTable"></table>
					</div>
				</div>
			</div>
		
	</div>
</section>
<!-- 新增和修改用户弹出框 -->
<div class="modal fade" id="add_edit_user">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">账户信息</h8>
            </div>
			<form class="form-horizontal" role="form" id="userForm">
			<div class="modal-body text-center">

					<input type="hidden" name="id" id="userId" value="">
					<div class="form-group">
						<label for="userName" class="col-sm-2 col-sm-offset-2 control-label">用户名：</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="userName" id="userName" placeholder="用户名" maxlength="255">
						</div>
					</div>
					<div class="form-group">
						<label for="password" class="col-sm-2 col-sm-offset-2 control-label">密码：</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="password" id="password" placeholder="密码" maxlength="18">
						</div>
					</div>
					<div class="form-group">
						<label for="password" class="col-sm-2 col-sm-offset-2 control-label">昵称：</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="name" id="name" placeholder="昵称" maxlength="255">
						</div>
					</div>
					<div class="form-group">
						<label for="type" class="col-sm-2 col-sm-offset-2 control-label">用户类型：</label>
						<div class="col-sm-6">
							<select class="form-control" name="type" id="type"></select>
							<!-- <input type="text" class="form-control" name="type" id="type" placeholder="类型" maxlength="20"> -->
							<!-- <select class="form-control" name="type" id="type">
								<option value='engineer'>engineer</option>
								<option value='customer'>customer</option>
								<option value='admin'>admin</option>
							</select> -->
						</div>
					</div>
					<div class="form-group">
						<label for="email" class="col-sm-2 col-sm-offset-2 control-label">邮箱：</label>
						<div class="col-sm-6">
							<input type="email" class="form-control" name="email" id="email" placeholder="邮箱" maxlength="255"/>
						</div>
					</div>
					<div class="form-group">
						<label for="province" class="col-sm-2 col-sm-offset-2 control-label">省份: </label>
						<div class="col-sm-6">
							<!-- <select id="province" name="province" class="form-control">
								<option value='江苏'>江苏</option>
								<option value='广东'>广东</option>
								<option value='重庆'>重庆</option>
							</select> -->
							<input type="text" class="form-control" name="province" id="province" placeholder="省份" maxlength="255"/>
						</div>
					</div>
					<div class="form-group">
						<label for="operator" class="col-sm-2 col-sm-offset-2 control-label">运营商：</label>
						<div class="col-sm-6">
							<!-- <select id="operator" name="operator" class="form-control">
								<option value='移动'>移动</option>
								<option value='联通'>联通</option>
								<option value='电信'>电信</option>
							</select> -->
							<input type="text" class="form-control" name="operator" id="operator" placeholder="运营商" maxlength="255"/>
						</div>
					</div>
				
			</div>
			<div class="modal-footer">
				<button type="submit" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="saveBtn" onclick="updateUser()">保存</button>
				<button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
			</div>
			</form>
		</div>
	</div>
</div>
<!-- 新增用户类型弹出框 -->
<div class="modal fade" id="add_user_type">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">添加用户类型</h8>
            </div>
			<form class="form-horizontal" role="form" id="scopeForm">
				<div class="modal-body text-center">
					<div class="form-group">
						<label for="modeName" class="col-sm-2 col-sm-offset-2 control-label">用户类型：</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="userType" id="userType" placeholder="scope" maxlength="50">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" onclick="updateUserType()">保存</button>
					<button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- 修改权限弹出框 -->
<div class="modal fade" id="modifyPermission_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">修改访问权限</h8>
            </div>
			<div class="modal-body">
				<div class="form-group" style="height:400px; overflow:auto;overflow-x:hidden;">
					<div id="menuTree"></div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" onclick="updatePermission()">保存</button>
				<button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
			</div>
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
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!--bootstrapvalidator-->
<link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script>
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/userManage.js"></script>
<style>
a.btn {
  display: inline-block;
  padding: 4px 8px;
  margin-bottom: 0;
  font-size: 13px;
  font-weight: normal;
  line-height: 1.42857143;
  text-align: center;
  white-space: nowrap;
  vertical-align: middle;
  -ms-touch-action: manipulation;
      touch-action: manipulation;
  cursor: pointer;
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
          user-select: none;
  background-image: none;
  border: 1px solid transparent;
  border-radius: 4px;
}
</style>
