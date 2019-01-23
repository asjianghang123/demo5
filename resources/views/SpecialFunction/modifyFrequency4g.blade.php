@extends('layouts.nav')

@section('content')
<section class="content-header">
	<h1>4G翻频<!-- <small>advanced tables</small> --></h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-thumbs-up"></i>特色功能
		</li>
		<li>翻频
		</li>
		<li class="active">4G翻频</li>
	</ol>
</section>

<section class="content">
	<div class="row">
		<div class="col-sm-3">
			<div class='box'>
				<div class="box-header with-border">
					<div style="display:inline">
						<h3 class="box-title">城市</h3>
					</div>
					<div class="input-group" style="float:right;display:inline">
		               	<a id="import" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="importTemplate()"><span class="ladda-label">导入</span></a>
						<a id="exportTemplate" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="exportTemplate()"><span class="ladda-label">导出模板</span></a> 
		            </div>	
				</div>
				<div class="box-body">
					<ul class="nav nav-tabs" role="tablist">
						<li class="active"><a href="#table_tab_0" data-toggle="tab" aria-expanded="false">4G翻频</a></li>
						<!-- <li class=""><a href="#table_tab_1" data-toggle="tab" onClick="switchTab('2GSiteManage')"
							aria-expanded="false">2G站点</a></li> -->
					</ul>
					<div class="box-body">
						<div class="form-group"  style="height:600px; overflow:auto;overflow-x:hidden">
							<div id="cityTree"></div>
						</div>
						<input type="hidden" value="" id="cityValue">
						<input type="hidden" value="siteManage" id="siteType">
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-9">
			<div class="box">
				<div class="box-header with-border">
					<div style="display:inline">
						<h3 class="box-title">
						<form class="form-inline">
							<div class="form-group">
								日期：
							</div>
							<div class="form-group">
								<label class="sr-only"></label>
			    				<p class="form-control-static">
			    					
			    					<select id="date" class="form-control input-sm" style="width:180px;">
									</select> 
									
			    				</p>					
						  	</div>
						</form>

						</h3>
								  
					</div>

					<div class="input-group" style="padding-top:10px;float:right;display:inline">
						<a id="run" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="run()"><span class="ladda-label">运行</span></a>
						<a id="exportModifyFrequency" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="exportModifyFrequency()"><span class="ladda-label">导出4G翻频</span></a>
		               	<a id="exportOriginalConfig" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="exportOriginalConfig()"><span class="ladda-label">导出倒回配置</span></a>
		            </div>
				</div>
				<div class="box-body">
		            <table id="modifyFrequency4gTable">
		            
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

<!--treeview-->
<script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script>

<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >
<!--select2-->
<script type="text/javascript" src="plugins/select2/select2.js"></script>
<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!--bootstrapvalidator-->
<link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script>


<script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>
<link type="text/css" rel="stylesheet" href="dist/css/button.css" >
<script type="text/javascript" src="dist/js/SpecialFunction/modifyFrequency4g.js"></script>
@endsection

