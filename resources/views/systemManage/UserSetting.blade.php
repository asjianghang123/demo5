@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>用户设置</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>系统管理
		</li>
		<li class="active">用户设置</li>
	</ol>
</section>
@endsection
@section('content')
<section class="content">
	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header">
					<div style="display:inline">
						<h3 class="box-title">用户信息</h3>
					</div>
					<div style="float:right;display:inline">
						<a id="refreshBtn" class="btn btn-default ladda-button" data-color='red' data-style="expand-right" onClick="refreshUser()"><span class="ladda-label">重置</span></a> 
						<a id="saveBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" onClick="saveUser()"><span class="ladda-label">保存</span></a> 
					</div>
				</div>
				<div class="box-body">
					<form class="form-horizontal col-sm-6 col-sm-offset-3" role="form" id="userForm"  method="post" action="{{ url('UserSetting/updateUser') }}">
						<button id="hiddenSaveBtn" type="submit" class="hidden">
							<span class="ladda-label">保存</span>
						</button>
						<input type="hidden" name="id" id="userId" value="{{Auth::user()->id}}">
						<div class="form-group">
							<label for="userName" class="col-sm-2 control-label">用户名</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="userName" id="userName" placeholder="用户名" maxlength="255" readonly="true" value="{{Auth::user()->user}}">
							</div>
						</div>
						<div class="form-group{{ $errors->has('nickname') ? ' has-error' : '' }}">
							<label for="nickname" class="col-sm-2 control-label">昵称</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="nickname" id="nickname" placeholder="昵称" maxlength="18" value="{{Auth::user()->name}}">
								@if ($errors->has('nickname'))
									<span class="help-block">
										<strong>{{ $errors->first('nickname') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
							<label for="email" class="col-sm-2 control-label">邮箱</label>
							<div class="col-sm-8">
								<input type="email" class="form-control" name="email" id="email" placeholder="邮箱" maxlength="255" value="{{Auth::user()->email}}" />
								@if ($errors->has('email'))
									<span class="help-block">
										<strong>{{ $errors->first('email') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="form-group">
							<label for="type" class="col-sm-2 control-label">用户组</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="type" id="type" placeholder="用户组" readonly="true" value="{{Auth::user()->type}}">
							</div>
						</div>
						<div class="form-group">
							<label for="province" class="col-sm-2 control-label">省份</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="province" id="province" placeholder="省份" readonly="true" value="{{Auth::user()->province}}">
							</div>
						</div>
						<div class="form-group">
							<label for="operator" class="col-sm-2 control-label">运营商</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="operator" id="operator" placeholder="运营商" readonly="true" value="{{Auth::user()->operator}}">
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header">
					<div style="display:inline">
						<h3 class="box-title">修改密码</h3>
					</div>
				</div>
				<div class="box-body">
					<form class="form-horizontal col-sm-6 col-sm-offset-3" role="form" id="modifyPasswordForm" method="post" action="{{ url('UserSetting/updatePassword') }}">
						<input type="hidden" name="id" id="userId_pwd" value="{{Auth::user()->id}}">
						<div class="form-group{{ $errors->has('oldPwd') ? ' has-error' : '' }}">
							<label class="col-sm-2 control-label">当前密码</label>
							<div class="col-sm-8">
								<input type="password" class="form-control" name="oldPwd" id="oldPwd" placeholder="当前密码" autocomplete="off">
								@if ($errors->has('oldPwd'))
									<span class="help-block">
										<strong>{{ $errors->first('oldPwd') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="form-group{{ $errors->has('newPwd') ? ' has-error' : '' }}">
							<label for="newPwd" class="col-sm-2 control-label">新密码</label>
							<div class="col-sm-8">
								<input type="password" class="form-control" name="newPwd" id="newPwd" placeholder="新密码" autocomplete="off">
								@if ($errors->has('newPwd'))
									<span class="help-block">
										<strong>{{ $errors->first('newPwd') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<div class="form-group{{ $errors->has('confirmPwd') ? ' has-error' : '' }}">
							<label for="confirmPwd" class="col-sm-2 control-label">确认密码</label>
							<div class="col-sm-8">
								<input type="password" class="form-control" name="confirmPwd" id="confirmPwd" placeholder="确认密码" autocomplete="off">
								@if ($errors->has('confirmPwd'))
									<span class="help-block">
										<strong>{{ $errors->first('confirmPwd') }}</strong>
									</span>
								@endif
							</div>
						</div>
						<button type="submit" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="savePwdBtn">确定</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/UserSetting.js"></script>

