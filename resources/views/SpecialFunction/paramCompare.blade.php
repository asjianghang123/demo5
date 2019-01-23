@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>参数对比<!-- <small>advanced tables</small> --></h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-dashboard"></i>日常优化
		</li>
		<li>参数分析
		</li>
		<li class="active">参数对比</li>
	</ol>
</section>
@endsection
@section('content')

<section class="content">

	<div class="row">
		<div class="col-sm-3">
			<div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">参数结构</h3>
                </div>
                <div class="box-body"> 
                   <form>
					  <div class="form-group">
						<div class="input-group">
				           	<input type="text" class="form-control" id="paramQueryMoErbs" aria-describedby="basic-addon1" placeholder="请输入参数查询" />
				               <span class="input-group-btn">
					                <button class="btn btn-default" type="button" onClick="search('paramQueryMoTree','paramQueryMoErbs')">
					                     &nbsp;<span class="glyphicon glyphicon-search btn-group-lg" aria-hidden="true"></span>
					                </button>
					                <button class="btn btn-default" type="button" onClick="clearSearch('paramQueryMoTree','paramQueryMoErbs')">
										&nbsp;<span class="glyphicon glyphicon-remove btn-group-lg" aria-hidden="true"></span>
									</button>
				               	</span>
				            </div>
					  </div>
					  <div class="form-group" style="height:600px;overflow:auto;">
					  	<div id="paramQueryMoTree"></div>
					  </div>
				  	</form>
                </div>
            </div>
				
		</div>
		<div class="col-sm-9">
			<div class="box" >
				<div class="box-header with-border">
                    <h3 class="box-title">查询条件</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
				<div class="box-body">
		            <div class="row">
						<form class="form-horizontal" role="form">
							<div class="form-group">
							    <label class="col-sm-2 control-label">城市：</label>
							    <div class="col-sm-1 ">
									
								</div>
							    <div class="col-sm-3 ">
									<select id="allCity" class="js-example-basic-single js-states form-control input-md" multiple="multiple" style="height:31px;">
								</select>
								</div>
							</div>
							<div class="form-group">
							    <label class="col-sm-2 control-label">日期：</label>
                                <label for="baseDate" class="col-sm-1 control-label">基础</label>
							    <div class="col-sm-3 ">
									<select id="baseDate" class="js-example-basic-single js-states form-control input-md">
									</select>
								</div>
								<label for="compareDate" class="col-sm-1 control-label">对比</label>
							    <div class="col-sm-3 ">
									<select id="compareDate" class="js-example-basic-single js-states form-control input-md">
									</select>
								</div>
							</div>
							<div class="form-group">
							    <label for="paramQueryErbs" class="col-sm-2 control-label">基站名称：</label>
                                <label for="base" class="col-sm-1 control-label">基础</label>
							    <div class="col-sm-3 ">
									<input id="base" class="form-control input-sm" type="text" placeholder="请输入条件" name="paramQueryErbs" style="height:33px;">
								</div>
								<label for="compare" class="col-sm-1 control-label">对比</label>
							    <div class="col-sm-3 ">
									<input id="compare" class="form-control input-sm" type="text" placeholder="请输入条件" name="paramQueryErbs" style="height:33px;">
								</div>
							</div>
							<div class="form-group" id="special" style="display:block">
							    <label for="paramQueryErbs" class="col-sm-2 control-label">详细ID：</label>
                                <label for="baseSpecial" class="col-sm-1 control-label">基础</label>
							    <div class="col-sm-3 ">
									<input id="baseSpecial" class="form-control input-sm" type="text" placeholder="请输入指定条件" name="paramQueryErbs" style="height:33px;">
								</div>
								<label for="compareSpecial" class="col-sm-1 control-label">对比</label>
							    <div class="col-sm-3 ">
									<input id="compareSpecial" class="form-control input-sm" type="text" placeholder="请输入指定条件" name="paramQueryErbs" style="height:33px;">
								</div>
							</div>
						</form>
					</div>
	            </div>
	            <div class="box-footer">
					<div style="text-align:right;">
						<a id="search" class="btn btn-primary ladda-button"  href="#" role="button" onClick="paramCompareSearch();return false;" data-color='red' data-style="expand-right" ><span class="ladda-label">对比</span></a>
					</div>
				</div>
			</div>
			<div class="box">
				<div class="box-header with-border">
                    <h3 class="box-title">查询数据</h3>
                    <div class="box-tools pull-right">
                        <a id="export" class="btn btn-primary ladda-button"  href="#" role="button" onClick="paramCompareExport();return false;" data-color='red' data-style="expand-right" ><span class="ladda-label">导出</span></a>
                    </div>
                </div>
                <ul class="nav nav-tabs" role="tablist">
					<li class="active"><a class="table_tab" href="#table_tab_0" data-toggle="tab" id="table_tab_0_nav" 
						aria-expanded="false">基础</a></li>
					<li class=""><a class="table_tab" href="#table_tab_1" data-toggle="tab" id="table_tab_1_nav" 
						aria-expanded="false">新增</a></li>
					<li class=""><a class="table_tab" href="#table_tab_2" data-toggle="tab" id="table_tab_2_nav" 
						aria-expanded="false">缺失</a></li>
				</ul>
				<!-- <div class="box-body">
					<div class="form-group"  style="height:600px;width:100%; overflow:auto;margin-top:34px" id="paramCompareTableDiv">
						<table id="paramCompareTable">
		              </table>
					</div>
		              
	            </div> -->
	            <div class="tabs tab-content" style="height:600px;width:100%; overflow:auto;margin-top:34px">
					<div class=" tab-pane active" id="table_tab_0" >	          
						<div class="box-body">
								<table id="paramCompareTable">
				              </table>
			            </div>
					</div>
					<div class=" tab-pane" id="table_tab_1">
						<div class="box-body">
								<table id="paramCompareTableADD">
				              </table>
						</div>
					</div>
					<div class=" tab-pane" id="table_tab_2">
						<div class="box-body">
								<table id="paramCompareTableLESS">
				              </table>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		
	</div>
</section>
@endsection
@section('scripts')

<!--select2-->
<script type="text/javascript" src="plugins/select2/select2.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<!-- treeview -->
<script src="plugins/treeview/bootstrap-treeview.min.js"></script>
<!-- grid -->
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >
<!-- treegrid -->
<link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/easyui.css">
<link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/datagrid.css">
<script src="plugins/EasyUI/jquery.easyui.min.js"></script>
<link rel="stylesheet" href="dist/css/button.css">
<!--loading-->
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>
<style>
.select2-container .select2-selection--single {
    height: 33px;
}
.dropdown-menu {
   min-width:230px;
}
.datagrid-cell, .datagrid-cell-group, .datagrid-header-rownumber, .datagrid-cell-rownumber{
	height: 40px !important;
    padding: 10px 4px;
    font-size : 14px;
    line-height : 20px;
}
.tree-title{
	font-size : 14px;
}
.datagrid-header .datagrid-cell span {
	font-weight: bold;
	font-size : 14px;
}
.datagrid-header td, .datagrid-body td, .datagrid-footer td{
	border : 1px solid #f4f4f4;
}
.datagrid-header-inner{
	background-color: #f5f5f5;
}
</style>
@endsection
<!-- jQuery 2.2.0 -->
<script type="text/javascript" src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/SpecialFunction/paramCompare.js"></script>
