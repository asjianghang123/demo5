@extends('layouts.nav')

@section('content')
<section class="content-header">
	<h1>强干扰小区处理 <!-- <small>advanced tables</small> --></h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-thumbs-up"></i>特色功能
		</li>
		<li>参数
		</li>
		<li class="active">强干扰小区处理 </li>
	</ol>
</section>

<section class="content">
	<div class="row">
	    <div class="col-sm-12">
			<div class='box'>
				<div class="box-header with-border">
					<h3 class="box-title">查询条件</h3>
					<div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
				</div>
				<div class="box-body">
	                <div class="row">
	                  <form class="form-inline">
	                  <div class="form-group col-sm-4">
	                        <label style="width: 30%; text-align: center;" for="date">日期</label>
                        	<div class="input-group">
                            	<select id="date" class="js-example-basic-single js-states form-control input-md" style="width:200px;">
								</select>
                        	</div>
							
	                    </div>
	                    <div class="form-group col-sm-4">
	                        <label style="width: 30%; text-align: center;"  for="cellInput">小区</label>
	                        <label class="sr-only"></label>
	                        <div class="input-group">
	                           <input type="text" class="form-control" id="cellInput">
	                           <input type="file" class="hidden" name="fileImport" id="fileImport" onchange="toName(this)">
	                           <span class="input-group-btn">
	                              <button class="btn btn-default" type="button" onclick="fileImport.click()">选择文件</button>
	                           </span>
	                        </div>
	                    </div>
	                  </form>
	                </div>
				</div>
				<div class="box-footer">
					<div style="text-align:right;">
						<a id="search" class="btn btn-primary ladda-button"  href="#" role="button" onClick="searchInfo();return false;" data-color='red' data-style="expand-right" ><span class="ladda-label">查询</span></a>
					</div>
				</div>
			</div>
			<div class="box">
				<div class="box-header  with-border">
                    <h3 class="box-title">查询数据</h3>
                    <div class="box-tools pull-right">
                    	 <a id="export" class="btn btn-primary ladda-button"  href="#" role="button" onClick="exportInfo();return false;" data-color='red' data-style="expand-right" ><span class="ladda-label">导出</span></a>
                    </div>
                </div>
				<div class="box-body">
				    <input type="hidden" id="tableName"></div>
		            <table id="strongInterferenceCellTable"></table>

	            </div>
			</div>
		</div>
	</div>
</section>

@endsection

@section('scripts')
<style>
	#strongInterferenceCellTable td div{
		width:100%;
		white-space:nowrap;
		overflow:hidden;
		text-overflow:ellipsis;
	}
</style>
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
<script type="text/javascript" src="dist/js/SpecialFunction/strongInterferenceCell.js"></script>
@endsection

