@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>告警管理</h1>
	<ol class="breadcrumb"> 
		<li>
			<i class="fa fa-dashboard">系统管理</i>
			<li class="active">告警管理</li>		
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
                        <div style="display:inline">
                            <h3 class="box-title">详细信息</h3>
                        </div>
                        <div class="input-group" style="float:right;display:inline">
                         <a id="import" class="btn btn-primary btn-sm ladda-button" data-color='red' data-style="expand-right" href="#" onClick="importAlarmManage()"><span class="ladda-label">导入</span></a> 
                           <a id="export" class="btn btn-primary btn-sm ladda-button" data-color='red' data-style="expand-right" href="#" onClick="exportAlarmManage()"><span class="ladda-label">导出</span></a>                    
                        </div>
                </div>
				<div class="box-body">
		            <table id="alarmTable">
		            
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
<script type="text/javascript" src="dist/js/systemManage/alarmManage.js"></script>
