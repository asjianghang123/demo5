@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>常用参数查询</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>日常优化
		</li>
		<li>
			<i class="fa fa-dashboard"></i>参数分析
		</li>
		<li class="active">常用参数查询</li>
	</ol>
</section>
@endsection
@section('content')

<section class="content"> 
	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">查询条件</h3>
					<div class="box-tools pull-right">
                		<div class="btn-group">
                			<button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown" onclick="openConfigInfo()" id="queryBtn_param">
                    		<i class="fa fa-wrench"></i>
                    		<span class="ladda-label"></span></button>
	                		<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    		<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> 
                		</div>
            		</div>
				</div>
				<div class="box-body">
					<form class="form-inline" role="form" id="queryForm">
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
					  	<div class="form-group">
						基站：
						</div>
						<div class="form-group">
							<label class="sr-only"></label>
		    				<p class="form-control-static">
		    					<div class="input-group">
								   <input type="text" class="form-control" id="cellInput">
								   <input type="file" class="hidden" name="fileImport" id="fileImport" onchange="toName(this)">
								   <span class="input-group-btn">
								      <button class="btn btn-default" type="button" onclick="fileImport.click()">选择文件</button>
								   </span>
								</div>
		    				</p>					
					  	</div>
					</form>
				</div>
				<div class="box-footer">
					<div class="pull-right">
						<div class="btn-group">
							<a id="queryBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onclick="query()"><span class="ladda-label">查询</span></a>
		                    <!-- <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="query()" id="queryBtn">
		                        <i class="fa fa-search"></i>
		                        <span class="ladda-label">查询</span>
		                    </button> -->
		                </div>
		                <!-- <div class="btn-group">
		                    <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="filetoExport()" id="exportBtn">
		                        <i class="fa fa-sign-out"></i>导出
		                    </button>
		                </div> -->
					</div>
				</div>
			</div>
		</div>
		
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">查询数据</h3>
					<div class="box-tools pull-right">
                        <div class="btn-group">
                            <a id="exportBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onclick="filetoExport()">
                            <span class="ladda-label">导出</span></a>
				        </div>  
                    </div>
				</div>
				<div class="box-body">
					<table id="StrideMOQueryTable"></table>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- 配置信息弹出框 -->
<div class="modal fade" id="config_information">
	<div class="modal-dialog" style="width:900px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">参数列表</h8>
            </div>
            <div class="box-body">
					<table id="paramTable"></table>
			</div>
			
			<div class="modal-footer">
				<button type="button" name="saveBtn" class="col-sm-1 col-sm-offset-11 btn btn-primary" id="saveBtn" onclick="updateConfigInfo()">确定</button>
			</div>
		</div>
	</div>
</div>		

@endsection


@section('scripts')

<script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script>

<!--datatables-->
<script type="text/javascript" src="plugins/select2/select2.js"></script>
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

<!--loading-->
<link rel="stylesheet" href="plugins/loading/css/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!--bootstrapvalidator-->
<link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script>


<script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>
<style>
.select2-container .select2-selection--single {
    height: 33px;
}
.dropdown-menu {
   min-width:230px;
}
</style>
@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>


<script  src="dist/js/parameterAnalysis/StrideMOQuery.js"></script>

