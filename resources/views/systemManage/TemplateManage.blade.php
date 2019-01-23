@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>模板管理</h1>
	<ol class="breadcrumb"> 
		<li>
			<i class="fa fa-dashboard">系统管理</i>
			<li class="active">模板管理</li>		
		</li>
	</ol>
</section>
@endsection
@section('content')

<section class="content">
	<div class="row">
		<div class="col-sm-12">	          
			 <div class="box">
                 <div class="box-header with-border">

                        <div style="display:inline" class="col-md-2 col-sm-4">
                            <h3 class="box-title">模板类型</h3>
                        </div>
                        <div class="col-md-2 col-sm-2">
                        	 <select id="allTypes" class="form-control input-sm" multiple="multiple">
							</select>
                        </div>
                        <div class="input-group col-md-4 col-sm-4" style="display:inline;float:right">
                        {{-- <a id="searchManage" class="btn btn-primary btn-sm ladda-button" data-color='red' data-style="expand-right" onClick="searchManage()"><span class="ladda-label">查询</span></a> --}}
                        <a id="addManage" class="btn btn-primary btn-sm ladda-button" data-color='red' data-style="expand-right" onClick="addManage()"><span class="ladda-label">新建</span></a>
                        <a id="updManage" class="btn btn-primary btn-sm ladda-button" data-color='red' data-style="expand-right" onClick="updManage()"><span class="ladda-label">编辑</span></a>
                        <a id="delete" class="btn btn-primary btn-sm ladda-button" data-color='red' data-style="expand-right" onClick="deleteData()"><span class="ladda-label">删除</span></a>
                         <a id="import" class="btn btn-primary btn-sm ladda-button" data-color='red' data-style="expand-right" onClick="importQuery()"><span class="ladda-label">导入</span></a>
                          <a id="export" class="btn btn-primary btn-sm ladda-button" data-color='red' data-style="expand-right" onClick="exportManage()"><span class="ladda-label">导出</span></a>                     
                        </div>
                </div>
                	<input type="hidden" id="deleteData" name="id">
                <div class="box-body">
		            <table id="ManageTable">
		            
		            </table>
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
					
				<input type="hidden" name="siteSign" id="siteSign" value="">
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
				<button type="button" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="importBtn" onclick="importFile()">确定</button>
				<button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
			</div>
		</div>
	</div>
</div>
<!-- 新增和修改公式弹出框 -->
<div class="modal fade" id="add_edit_formula">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">新建/修改</h8>
            </div>
			<form class="form-horizontal" role="form" id="formulaForm">
			<div class="modal-body text-center">

					<input type="hidden" name="formulaId" id="formulaId" value="">
					<div class="form-group">
						<label for="type" class="col-sm-2 col-sm-offset-2 control-label">Type：</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="type" id="type" placeholder="Type" maxlength="50">
						</div>
					</div>
					<div class="form-group">
						<label for="dataSource" class="col-sm-2 col-sm-offset-2 control-label">DataSource：</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="dataSource" id="dataSource" placeholder="dataSource" maxlength="50">
						</div>
					</div>
					<div class="form-group">
						<label for="templateName" class="col-sm-2 col-sm-offset-2 control-label">templateName：</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="templateName" id="templateName" placeholder="templateName" maxlength="50">
						</div>
					</div>
					<div class="form-group">
						<label for="name" class="col-sm-2 col-sm-offset-2 control-label">Name：</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="name" id="name" placeholder="name" maxlength="50">
						</div>
					</div>
					<div class="form-group">
						<label for="precision" class="col-sm-2 col-sm-offset-2 control-label">Precision：</label>
						<div class="col-sm-6">
							<input type="number" class="form-control" name="precision" id="precision" placeholder="Precision" maxlength="11">
						</div>
					</div>
					<div class="form-group">
						<label for="formula" class="col-sm-2 col-sm-offset-2 control-label">Formula:</label>
						<div class="col-sm-6">
							<textarea class="form-control" name="formula" id="formula" style="height : 150px;resize: none;" maxlength="3000"></textarea>
						</div>
					</div>
					
				
			</div>
			<div class="modal-footer">
				<button type="submit" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" onclick="updateManage()">保存</button>
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
<!-- <script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script> -->
<script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>
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
<script type="text/javascript" src="dist/js/systemManage/TemplateManage.js"></script>
