@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>参数管理</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-cogs"></i>系统管理
		</li>
		<li class="active">参数管理</li>
	</ol>
</section>
@endsection
@section('content')


<section class="content">
	<div class="row">
		<div class="col-sm-12">
			<ul class="nav nav-tabs" role="tablist">
				<li class="active"><a href="#table_tab_0" data-toggle="tab" id="table_tab_0_nav"
					aria-expanded="false">Baseline模板管理</a></li>
				<li class=""><a href="#table_tab_1" data-toggle="tab" id="table_tab_1_nav"
					aria-expanded="false">Baseline任务管理</a></li>
			</ul>	
		</div>
		<div class="tabs tab-content ">
			<div class=" tab-pane active" id="table_tab_0">
				<div class="col-sm-3">
					<div class='box'>
						<div class="box-header with-border">
							<h3 class="box-title">模板</h3>
							<div class="box-tools pull-right">
		                        <a id="addMode" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="addMode()"><span class="ladda-label">新建</span></a>
								<a id="editMode" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="editMode()"><span class="ladda-label">修改</span></a> 
								<a id="deleteMode" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onclick="deleteMode()">删除</a>
		                	</div>
						</div>
						<div class="box-body">
							<div class="input-group">
					           <input type="text" class="form-control" id="baselineManageQuery" aria-describedby="basic-addon1" placeholder="查询" />
				               <span class="input-group-btn">
					                <button class="btn btn-default" type="button" onClick="searchBaselineManageQuery()">
					                     &nbsp;<span class="glyphicon glyphicon-search btn-group-lg" aria-hidden="true"></span>
					                </button>
					                <button class="btn btn-default" type="button" onClick="clearBaselineManageQuery()">
										&nbsp;<span class="glyphicon glyphicon-remove btn-group-lg" aria-hidden="true"></span>
									</button>
				               	</span>
				            </div>
							
							<br />
							<div class="form-group"  style="height:500px; overflow:auto;overflow-x:hidden">
								<div id="baselineManageTree"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-9">
					<div class="box">
						<div class="box-header">
							<a id="addUser" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="importBaselineManage()"><span class="ladda-label">导入</span></a>
							<a id="deleteUser" class="btn btn-default ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="exportBaselineManage()"><span class="ladda-label">导出</span></a> 
							<a id="queryWhiteList" type="submit"  class="btn btn-primary ladda-button" onclick="queryWhiteList();return false;">白名单</a>
							<a id="importWhiteList" type="submit"  class="btn btn-primary ladda-button" onclick="importWhiteList();return false;">导入白名单</a>
                            <a id="exportWhiteList" type="submit"  class="btn btn-default ladda-button" onclick="exportWhiteList();return false;">导出白名单</a>
						</div>
						<div class="box-body">
							<input type="hidden" id="templateName" value="">
                          	<input type="hidden" id="templateId" value="">
                          	<input type="hidden" id="user" value="">
				            <table id="baselineManageTable">
				            </table>
			            </div>
					</div>
		        </div>
			</div>
			
			<div class=" tab-pane" id="table_tab_1">
				<div class="col-sm-3">
					<div class='box'>
						<div class="box-header with-border"> 
							<h3 class="box-title">日期</h3>
						</div>
						<div class="box-body">
							<select class="form-control" name="date" id="date">
							</select>
						</div>
					</div>
					<div class='box'>
						<div class="box-header with-border">
							<h3 class="box-title">模板</h3>
						</div>
						<div class="box-body">
							<div class="input-group">
					           <input type="text" class="form-control" id="baselineTaskQuery" aria-describedby="basic-addon1" placeholder="查询" />
				               <span class="input-group-btn">
					                <button class="btn btn-default" type="button" onClick="searchBaselineTaskQuery()">
					                     &nbsp;<span class="glyphicon glyphicon-search btn-group-lg" aria-hidden="true"></span>
					                </button>
					                <button class="btn btn-default" type="button" onClick="clearBaselineTaskQuery()">
										&nbsp;<span class="glyphicon glyphicon-remove btn-group-lg" aria-hidden="true"></span>
									</button>
				               	</span>
				            </div>
							<br />
							<div class="form-group"  style="height:400px; overflow:auto;overflow-x:hidden">
								<div id="baselineTaskTree"></div>
							</div>
						</div>
					</div>	
				</div>
				<div class="col-sm-9">
					<div class="box">
						<div class="box-header">
							<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
								<div class="box-tools">
				                   <div class="btn-group">
				                        <button type="button" class="btn  btn-primary" onclick="addBaselineTask()">
					                        <i class="fa fa-plus"></i>新建
					                    </button>
				                    </div>
				                    <div class="btn-group">
				                        <button type="button" class="btn btn-default" onclick="deleteBaselineTask()">
					                        <i class="fa fa-remove"></i>删除
					                    </button>
				                    </div>
				                    <div class="btn-group">
				                        <button type="button" class="btn btn-primary" onclick="runBaselineTask()">
					                        <i class="fa fa-play"></i>启动
					                    </button>
				                    </div>
				                    <div class="btn-group">
				                        <button type="button" class="btn  btn-danger" onclick="stopBaselineTask()">
					                        <i class="fa fa-stop"></i>停止
					                    </button>
				                    </div>
				                </div>		
							</div>
						</div>
						<div class="box-body">
				            <table id="baselineTaskTable">
				            </table>
			            </div>
					</div>
					<div class="box">
						<div class="box-header with-border">
							<h3 class="box-title">日志</h3>
						</div>
						<div class="box-body">
				            <div id="log" ></div>
			            </div>
					</div>
		        </div>
			</div>
		</div>
	</div>
</section>
<!-- 导入弹出框 -->
<div class="modal fade" id="import_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">导入</h8>
            </div>
			<div class="modal-body col-sm-12">
				<div class="col-sm-8 col-sm-offset-2">
					<div class="input-group">
		               <input type="text" class="form-control" id="fileImportName">
		               <input type="file" accept=".csv" class="hidden" name="fileImport" id="fileImport" onchange="toName(this)">
		               <span class="input-group-btn">
		                  	<button class="btn btn-default" type="button" onclick="fileImport.click()">选择文件</button>
		               </span>
		            </div>
				</div>
			</div>
			<div class="modal-footer">
				<input id="importType" type="hidden" value=""/>
				<button type="button" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="importBtn" onclick="importFile()">确定</button>
				<button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
			</div>
		</div>
	</div>
</div>
<!-- 新增模板弹出框 -->
<div class="modal fade" id="add_mode">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">添加/修改模板</h8>
            </div>
			<form class="form-horizontal" role="form" id="modeForm">
				<div class="modal-body text-center">
					<div class="form-group">
						<label for="modeName" class="col-sm-2 col-sm-offset-2 control-label">模板名称：</label>
						<div class="col-sm-6">
						    <input type="hidden" id="updateTemplateId" value="">
							<input type="text" class="form-control" name="modeName" id="modeName" placeholder="模板名称" maxlength="50">
						</div>
					</div>
					<div class="form-group">
						<label for="isAutoExecute" class="col-sm-2 col-sm-offset-2 control-label">城市：</label>
						<div class="col-sm-6">
							<select id="city" class="form-control " multiple="multiple"></select>
						</div>
					</div>
					<div class="form-group">
						<label for="isAutoExecute" class="col-sm-2 col-sm-offset-2 control-label">网络制式：</label>
						<div class="col-sm-6">
							<input id="TDD" type="radio" name="networkStandard" value="TDD" checked="checked"/>TDD
							<input id="FDD" type="radio" name="networkStandard" value="FDD" />FDD
							<input id="NB" type="radio" name="networkStandard" value="NB" />NB
						</div>
					</div>
					<div class="form-group">
						<label for="isAutoExecute" class="col-sm-2 col-sm-offset-2 control-label">自动执行：</label>
						<div class="col-sm-6">
							<input id="isAutoExecuteYes" type="radio" name="isAutoExecute" value="yes"/>是
							<input id="isAutoExecuteNo" type="radio" name="isAutoExecute" value="no" checked="checked"/>否
						</div>
					</div>
					<div class="form-group">
						<label for="isAutoExecute" class="col-sm-2 col-sm-offset-2 control-label">是否新站：</label>
						<div class="col-sm-6">
							<input id="isNewSiteYes" type="radio" name="isNewSite" value="yes"/>是
							<input id="isNewSiteNo" type="radio" name="isNewSite" value="no" checked="checked"/>否
						</div>
					</div>
					<div class="form-group">
						<label for="modeDescription" class="col-sm-2 col-sm-offset-2 control-label">描述：</label>
						<div class="col-sm-6">
							<textarea class="form-control" name="modeDescription" id="modeDescription" style="height : 100px;resize: none;" maxlength="500"></textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" onclick="addOrUpdateTemplate()">保存</button>
					<button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- 新增任务弹出框 -->
<div class="modal fade" id="add_task">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">添加任务</h8>
            </div>
			<form class="form-horizontal" role="form" id="taskForm">
				<div class="modal-body text-center">
					<div class="form-group">
						<label for="modeName_task" class="col-sm-2 col-sm-offset-2 control-label">模板名称：</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="modeName_task" id="modeName_task" readonly>
							<input type="hidden" id="templateId_task">
						</div>
					</div>
					<div class="form-group">
						<label for="taskName" class="col-sm-2 col-sm-offset-2 control-label">任务名称：</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="taskName" id="taskName" maxlength="18" placeholder="输入任务名称">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" onclick="addTask()">保存</button>
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

<!--select2-->
<script type="text/javascript" src="plugins/select2/select2.js"></script>

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


<script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>

<style>
	#baselineManageTable td div{
		width:100%;
		white-space:nowrap;
		overflow:hidden;
		text-overflow:ellipsis;
	}
	.select2-container .select2-selection--single{
		height:34px;
		border-radius:0;
	   	border: 1px solid #d2d6de;
	}
	.select2-container--default .select2-selection--single .select2-selection__arrow{
		top:3px;
	}
	.node-MOQueryTree,
	.node-paramQueryTree{
		word-break: break-all;
	}
</style>
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/paramsManage.js"></script>

