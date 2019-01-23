@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>老站跟踪<!-- <small>advanced tables</small> --></h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-thumbs-up feature"></i>定制
		</li>
		<li>参数
		</li>
		<li class="active">老站跟踪</li>
	</ol>
</section>
@endsection
@section('content')

<section class="content">
	<div class="row">
		<div class="col-sm-12">
			<div class="box" >
                    <div class="box-header with-border" style="height:35px">
						<h3 class="box-title" style="display:inline;float:left;margin-right:5px">查询条件</h3>
						<div class="box-tools pull-right">
                        	<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        	<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    	</div>
					</div>
                <!-- </div> -->
				<div class="box-body">
					<!-- <div class="row"> -->
						<form class="form-horizontal" role="form" id="queryForm">
							<div class="form-group">
								
                            	<label class="col-sm-2 control-label">城市</label>
                            	<div class="col-sm-4">
                                	<select id="city" class="form-control " multiple="multiple">
								</select>
                            	</div>
                            	<label class="col-sm-2 control-label">eNodeBId</label>
                        		<div class="col-sm-4">
                            		<input id="eNodeBId" class="form-control" type="text" value=""/> 
                        		</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">起始日期</label>
	                            <div class="col-sm-4">
	                                <input id="startTime" class="form-control" type="text" value=""/>
	                            </div>
	                            <label class="col-sm-2 control-label">结束日期</label>
	                            <div class="col-sm-4">
	                                <input id="endTime" class="form-control" type="text" value=""/>
	                            </div>
							</div>
						</form>
					<!-- </div> -->
				</div>
				<div class="box-footer">
					<div style="text-align:right;">
						<a id="search" class="btn btn-primary ladda-button"  href="#" role="button" onClick="oldSiteSearch()" data-color='red' data-style="expand-right" ><span class="ladda-label">查询</span></a>
						<a id="export" class="btn btn-primary ladda-button"  href="#" role="button" onClick="exportAllSearch()" data-color='red' data-style="expand-right" ><span class="ladda-label">导出</span></a>
					</div>
				</div>
			</div>
			<div class="box">
				<div class="box-header  with-border">
                    <h3 class="box-title">查询数据</h3>
                    <div class="box-tools pull-right">
                   	 	<!-- <a id="export" class="btn btn-primary ladda-button"  href="#" role="button" onClick="paramQueryExport();return false;" data-color='red' data-style="expand-right" ><span class="ladda-label">获取kget</span></a>
						<a id="export" class="btn btn-primary ladda-button"  href="#" role="button" onClick="paramQueryExport();return false;" data-color='red' data-style="expand-right" ><span class="ladda-label">获取STS</span></a> -->
                    	<a id="oldSiteImport" class="btn btn-primary ladda-button"  href="#" role="button" onClick="oldSiteImport()" data-color='red' data-style="expand-right" ><span class="ladda-label">导入</span></a>
                   		<a id="oldSiteExport" class="btn btn-primary ladda-button"  href="#" role="button" onClick="oldSiteExport()" data-color='red' data-style="expand-right" ><span class="ladda-label">导出模板</span></a>
                        <!-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button> -->
                    </div>
                </div>
				<div class="box-body">
		              <table id="oldSiteTable">
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
				<button type="button" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="importBtn" >确定</button>
				<button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>
<!-- grid -->
<script type="text/javascript" src="plugins/bootstrap-grid/js/grid.js"></script>
<!-- bootstrap-table-->
<link href="plugins/bootstrap-table/bootstrap-table.css" rel="stylesheet" />
<script type="text/javascript" src="plugins/bootstrap-table/bootstrap-table.js"></script>
<script type="text/javascript" src="plugins/bootstrap-table/locale/bootstrap-table-zh-CN.js"></script>
<!--select2-->
<script type="text/javascript" src="plugins/select2/select2.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<!-- treeview -->
<script src="plugins/treeview/bootstrap-treeview.min.js"></script>
<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>
<script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>
<style>
#oldSiteTable td div{
		width:100%;
		white-space:nowrap;
		overflow:hidden;
		text-overflow:ellipsis;
	}
.select2-container .select2-selection--single {
    height: 33px;
}
.dropdown-menu {
   min-width:230px;
}
label.control-label{
		font-weight:500;
		text-align:left!important;
		text-indent:5px;
	}
	.toggle.btn-xs{
		margin-top:5px;
	}
.datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
    background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
    border-color: #ffb733;
	}
</style>
@endsection
<!-- jQuery 2.2.0 -->
<link rel="stylesheet" href="dist/css/button.css">
<script type="text/javascript" src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/SpecialFunction/oldSite.js"></script>
