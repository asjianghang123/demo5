@extends('layouts.app')

@section('content')
<?php
ini_set('display_errors','Off');
error_reporting(NULL);
?>
<div class="content">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading text-center">账号注册</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" id="registerForm" method="post">
                        <div class="form-group">
                            <!-- <label for="name" class="col-md-2 control-label">昵称</label> -->
                            <div class="col-md-12">
                                <input id="name" type="text" class="form-control" name="name" placeholder="昵称" maxlength="18" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <!-- <label for="email" class="col-md-2 control-label">邮箱</label> -->
                            <div class="col-md-12">
                                <input id="email" type="email" class="form-control" name="email" value="" placeholder="邮箱即为账号" maxlength="255">
                            </div>
                        </div>
                        <div class="form-group">
                            <!-- <label for="password" class="col-md-2 control-label">密码</label> -->
                            <div class="col-md-12">
                                <input id="password" type="password" class="form-control" name="password" placeholder="密码">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <button id="registerBtn" type="button" class="btn btn-primary btn-block ladda-button" data-color='red' data-style="expand-right" onClick="userRegister()">
                                    <span class="ladda-label">注册账号</span>
                                </button>
                                <!-- <a href="login" class="btn btn-default">返回登录</a> -->
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label>
                                    <a href="login">返回登录</a>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

@endsection
<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/userRegister.js"></script>