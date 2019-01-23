@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>直连管理</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>系统管理
		</li>
		<li>权限管理</li>
		<li class="active">直连管理</li>
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
						<h3 class="box-title">ENIQ</h3>
					</div>
					<div class="box-body">
						<div class="form-group"  style="height:600px; overflow:auto;overflow-x:hidden">
							<div id="ENIQTypeTree"></div>
						</div>
						<input type="hidden" value="" id="ENIQTypeValue">
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
							<a id="addUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="selectENIQ()"><span class="ladda-label">新增</span></a>
							<a id="deleteUser" class="btn btn-default ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="deleteItem()"><span class="ladda-label">删除</span></a>
							<a id="deleteUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="editItem()"><span class="ladda-label">修改</span></a> 
						</div>
						
					</div>
					<div class="box-body">
						<table id="ENIQTable"></table>
					</div>
				</div>
			</div>
		</div>

		<!-- <div class="col-sm-12">
			<ul class="nav nav-tabs" role="tablist">
				<li class="active"><a href="#table_tab_0" data-toggle="tab" id="table_tab_0_nav"
					aria-expanded="false">4GENIQ</a></li>
				<li class=""><a href="#table_tab_1" data-toggle="tab" id="table_tab_1_nav"
					aria-expanded="false">2GENIQ</a></li>
				<li class=""><a href="#table_tab_2" data-toggle="tab" id="table_tab_2_nav"
					aria-expanded="false">Alarm</a></li>
			</ul>	
		</div>    
		<div class="col-sm-12">      
			<div class="tabs tab-content ">
				<div class="box tab-pane active" id="table_tab_0">
					<div class="box-header">
						<a id="addUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="add4G()"><span class="ladda-label">新增</span></a>
						<a id="deleteUser" class="btn btn-default ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="delete4G()"><span class="ladda-label">删除</span></a> 
						<a id="deleteUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="edit4G()"><span class="ladda-label">修改</span></a> 
					</div>
					<div class="box-body">
			            <table id="4GTable">
			            
			            </table>
		            </div>
				</div>
				<div class="box tab-pane" id="table_tab_1">
					<div class="box-header">
						<a id="addUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="add2G()"><span class="ladda-label">新增</span></a>
						<a id="deleteUser" class="btn btn-default ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="delete2G()"><span class="ladda-label">删除</span></a> 
						<a id="deleteUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="edit2G()"><span class="ladda-label">修改</span></a> 
					</div>
					<div class="box-body">
			            <table id="2GTable">
			            
			            </table>
		            </div>
				</div>
				<div class="box tab-pane" id="table_tab_2">
					<div class="box-header">
						<a id="addUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="addAlarm()"><span class="ladda-label">新增</span></a>
						<a id="deleteUser" class="btn btn-default ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="deleteAlarm()"><span class="ladda-label">删除</span></a> 
						<a id="deleteUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="editAlarm()"><span class="ladda-label">修改</span></a> 
					</div>
					<div class="box-body">
			            <table id="alarmTable">
			            
			            </table>
		            </div>
				</div>
			</div>

		</div> -->
	</div>
</section>
<!-- 选择新增ENIQ类型弹出框 -->
<div class="modal fade" id="select_edit_ENIQ">
	<div class="modal-dialog" style="width:450px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">选择新增的ENIQ类型</h8>
            </div>
			<form class="form-horizontal" role="form" id="sENIQForm">
			<div class="modal-body text-center">
				<div class="form-group">
					<label for="type" class="col-sm-3 col-sm-offset-2 control-label">类型：</label>
					<div class="col-sm-5">
						<select class="form-control" name="type" id="type">
							<option value='4GENIQ'>4GENIQ</option>
							<option value='2GENIQ'>2GENIQ</option>
							<option value='4GAlarm'>4GAlarm</option>
							<option value='2GAlarm'>2GAlarm</option>
						</select>
					</div>
				</div>				
			</div>
			<div class="modal-footer">
				<button type="button" name="submit" class="col-sm-2 col-sm-offset-5 btn btn-primary" id="saveBtn" onclick="upSelectENIQ()">保存</button>
				<button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
			</div>
			</form>
		</div>
	</div>
</div>
<!-- 新增和修改ENIQ弹出框 -->
<div class="modal fade" id="add_edit_ENIQ">
	<div class="modal-dialog" style="width:900px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">ENIQ信息</h8>
            </div>
			<form class="form-horizontal" role="form" id="ENIQForm">
			<div class="modal-body text-center">

					<input type="hidden" name="ENIQId" id="ENIQId" value="">
					<input type="hidden" name="ENIQSign" id="ENIQSign" value="">
					<div class="form-group">
						<label for="connName" class="col-sm-2 control-label">Conn Name：</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="connName" id="connName" placeholder="Conn Name" maxlength="50">
						</div>
						<label for="cityChinese" class="col-sm-2 control-label">City Chinese：</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="cityChinese" id="cityChinese" placeholder="City Chinese" maxlength="50">
						</div>
					</div>
					<div class="form-group">
						<label for="host" class="col-sm-2 control-label">Host：</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="host" id="host" placeholder="Host" maxlength="50">
						</div>
						<label for="port" class="col-sm-2 control-label">Port:</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="port" id="port" placeholder="Port" maxlength="30"/>
						</div>
					</div>
					<div class="form-group">
						<label for="dbName" class="col-sm-2 control-label">DB Name:</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="dbName" id="dbName" placeholder="DB Name" maxlength="30"/>
						</div>
						<label for="userName" class="col-sm-2 control-label">User Name:</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="userName" id="userName" placeholder="User Name" maxlength="30"/>
						</div>
					</div>
					<div class="form-group">
						<label for="password" class="col-sm-2 control-label">Passowrd:</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="password" id="password" placeholder="Passowrd" maxlength="40"/>
						</div>
					</div>
					<div class="form-group">
						<label for="subNetwork" class="col-sm-2 control-label">Sub Network:</label>
						<div class="col-sm-4">
							<textarea class="form-control" name="subNetwork" id="subNetwork" style="height : 100px;resize: none;" maxlength="500"></textarea>
						</div>
						<label for="subNetworkFdd" class="col-sm-2 control-label">SubNetwork Fdd:</label>
						<div class="col-sm-4">
							<textarea class="form-control" name="subNetworkFdd" id="subNetworkFdd" style="height : 100px;resize: none;" maxlength="500"></textarea>
						</div>
					</div>
					<div class="form-group">
						<label for="subNetworkNbiot" class="col-sm-2 control-label">SubNetworkNbiot</label>
						<div class="col-sm-4">
							<textarea class="form-control" name="subNetworkNbiot" id="subNetworkNbiot" style="height : 100px;resize: none;" maxlength="500"></textarea>
						</div>
						<div col-sm-8>
							
						</div>
					</div>
				
			</div>
			<div class="modal-footer">
				<button type="button" name="submit" class="col-sm-1 col-sm-offset-5 btn btn-primary" id="saveBtn" onclick="updateENIQ()">保存</button>
				<button type="button" class="col-sm-1 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
			</div>
			</form>
		</div>
	</div>
</div>
<!-- 新增和修改alarm弹出框 -->
<div class="modal fade" id="add_edit_alarm">
	<div class="modal-dialog" style="width:900px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">ENIQ信息</h8>
            </div>
			<form class="form-horizontal" role="form" id="alarmForm">
				<div class="modal-body text-center">
					<input type="hidden" name="alarmId" id="alarmId" value="">
					<input type="hidden" name="alarmSign" id="alarmSign" value="">
					<div class="form-group">
						<label for="alarmServerName" class="col-sm-2 control-label">serverName：</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="alarmServerName" id="alarmServerName" placeholder="serverName" maxlength="50">
						</div>
						<label for="alarmCity" class="col-sm-2 control-label">city：</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="alarmCity" id="alarmCity" placeholder="city" maxlength="50">
						</div>
					</div>
					<div class="form-group">
						<label for="alarmHost" class="col-sm-2 control-label">Host：</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="alarmHost" id="alarmHost" placeholder="Host" maxlength="50">
						</div>
						<label for="alarmPort" class="col-sm-2 control-label">Port:</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="alarmPort" id="alarmPort" placeholder="Port" maxlength="30"/>
						</div>
					</div>
					<div class="form-group">
						<label for="alarmDbName" class="col-sm-2 control-label">DB Name:</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="alarmDbName" id="alarmDbName" placeholder="DB Name" maxlength="30"/>
						</div>
					</div>
					<div class="form-group">
						<label for="alarmUserName" class="col-sm-2 control-label">User Name:</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="alarmUserName" id="alarmUserName" placeholder="User Name" maxlength="30"/>
						</div>
						<label for="alarmPassword" class="col-sm-2 control-label">Passowrd:</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="alarmPassword" id="alarmPassword" placeholder="Passowrd" maxlength="40"/>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" name="submit" class="col-sm-1 col-sm-offset-5 btn btn-primary" id="saveBtn" onclick="updateAlarm()">保存</button>
					<button type="button" class="col-sm-1 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
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
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!--bootstrapvalidator-->
<link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script>
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/ENIQManage.js"></script>
<style>
	.nowrap > div{
		white-space:nowrap!important; 
	}
	.connectedSuccess{
		color:green;
		text-align:center!important;
	}
	.connectedFailed{
		color:red;
		text-align:center!important;
	}
</style>

