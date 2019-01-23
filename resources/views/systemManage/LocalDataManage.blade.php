@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>数据上传</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-cogs"></i>系统管理
		</li>
		<li>数据管理</li>
		<li class="active">数据上传</li>
	</ol>
</section>
@endsection
@section('content')

<section class="content">
	<div class="row">
		<div class="col-sm-3">
			<div class='box'>
				<div class="box-header with-border">
					<h3 class="box-title">目录结构</h3>
					<div class="input-group" style="float:right;display:inline">
		               	<a id="addDir" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" onClick="addDir()"><span class="ladda-label">新建</span></a>
						<a id="deleteDir" class="btn btn-default ladda-button" data-color='red' data-style="expand-right" onClick="deleteDir()"><span class="ladda-label">删除</span></a> 
		            </div>
				</div>
				<div class="box-body">
					<div class="form-group"  style="height:400px; overflow:auto;overflow-x:hidden">
						<div id="logTypeTree"></div>
					</div>
					<input type="hidden" value="" id="logTypeValue">
				</div>
			</div>
		</div>
		<div class="col-sm-9">
			<div class="box">
				<div class="box-header">
					<div style="display:inline">
						<h3 class="box-title">文件列表</h3>
					</div>
					<div style="float:right;display:inline">
						<a id="updateLogBtn" class="btn btn-default ladda-button" data-color='red' data-style="expand-right" onClick="updateLog()"><span class="ladda-label">上传</span></a>
						<a id="analysisLogBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" onClick="analysisLog()"><span class="ladda-label">解析</span></a> 
					</div>
				</div>
				<div class="box-body">
					<input type="hidden" id="dirName" value="">
					<table id="dirTable"></table>
				</div>
			</div>

			<div class="box">
				<div class="box-header">
					<div style="display:inline">
						<h3 class="box-title">日志</h3>
					</div>
					<div style="float:right;display:inline">
						<a id="downloadOutputBtn" class="btn btn-default ladda-button" data-color='red' data-style="expand-right" onClick="downloadOutput()"><span class="ladda-label">导出</span></a>
					</div>
				</div>
				<div class="box-body" style="height:200px;">
					<input type="hidden" id="outputDir" value="">
					<table id="outputTable"></table>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- 上传文件弹出框 -->
<div class="modal fade" id="updateLog_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">上传文件</h8>
            </div>
			<div class="modal-body col-sm-12">
				<div class="col-sm-8 col-sm-offset-2" id="filesListDiv">
					<div class="input-group">
						<span class="input-group-btn">
		                  	<button class="btn btn-default" type="button" onclick="addFileInput()">+</button>
		               	</span>
		               <input type="text" class="form-control" id="fileImportName_0">
		               <input type="file" accept=".gz" class="hidden fileImport" name="fileImport_0" id="fileImport_0" onchange="toName(this)">
		               <span class="input-group-btn">
		                  <button class="btn btn-default" type="button" onclick="fileImport_0.click()">选择文件</button>
		               </span>
		               
		            </div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="col-sm-2 col-sm-offset-4 btn btn-primary ladda-button" data-color='red' data-style="expand-right" id="importBtn" onclick="importFile()">确定</button>
				<button type="button" class="col-sm-2 btn btn-default ladda-button"  data-color='red' data-style="expand-right" id="cancelBtn" data-dismiss="modal">取消</button>
			</div>
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

<script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>
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
	  	background-image: none;
	  	border: 1px solid transparent;
	  	border-radius: 4px;
	}
	.node-logInDirTree{
		word-break:break-all;
	}
</style>
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/LocalDataManage.js"></script>