@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>CTR信令分析</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-rocket"></i>专项研究
		</li>
		<li>信令分析</li>
		<li class="active">CTR信令分析</li>
	</ol>
</section>
@endsection
@section('content')
 

<section class="content">
	<div class="row">
		<div class="col-sm-12">	          
			<div class="">
                <input type="hidden" id="eventChoosedChange" value="" /> 
                <input type="hidden" id="sectionchoose" value="false"  />
                <input type="hidden" id="ueRefChoosed" value=""/>
			</div>
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">查询条件</h3>
				</div>
				<div class="box-body">
					<form class="form-horizontal" role="form" id="queryForm">
						<div class="form-group">
							<label for="database" class="col-sm-1 control-label">DataBase</label>
							<div class="col-sm-3">
								<select class="form-control" name="database" id="database">
								</select>
							</div>
							<label for="eventName" class="col-sm-1 control-label">EventName</label>
							<div class="col-sm-3">
								<select class="form-control" name="eventName" id="eventName" multiple="multiple">
								</select>
							</div>
							<label for="imsi" class="col-sm-1 control-label">Imsi</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="imsi" id="imsi">
							</div>
							

						</div>
						<div class="form-group">
							
							<label for="ueref" class="col-sm-1 control-label">UeRef</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="ueref" id="ueref">
							</div>
							<label for="enbs1apid" class="col-sm-1 control-label">ENBS1APId</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="enbs1apid" id="enbs1apid">
							</div>
							<label for="mmes1apid" class="col-sm-1 control-label">MMES1APId</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="mmes1apid" id="mmes1apid">
							</div>
						</div>
						<!-- <div class="form-group">
							
							<label for="ecgi" class="col-sm-1 control-label">ECGI</label>
							<div class="col-sm-3">
								<select class="form-control" name="ecgi" id="ecgi" multiple="multiple">
								</select>
							</div>
						</div> -->
					</form>
				</div>
				<div class="box-footer">
					<div class="btn-group pull-right">
	                    <!-- <button type="button" class="btn  btn-primary" onclick="queryProcess()">
	                        <i class="fa fa-search"></i>查询
	                    </button> -->
	                    <a id="queryBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onclick="queryProcess()"><span class="ladda-label">查询</span></a>
	                </div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-8">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title" style="margin-top:8px;">信令流程</h3>
					<div class="btn-div pull-right">
						<!-- <div class="btn-group">
								                    <button type="button" class="btn btn-warning disabled" onclick="filterProcess()" id="filterBtn">
								                        <i class="fa fa-filter"></i>筛选流程
								                    </button>
								                </div> -->
						<div class="btn-group">
							<a type="button" class="btn" title="信令表" onclick="switchTab(table_tab_0,table_tab_1,'table')">
								<i class="fa fa-bars"></i>
							</a>
                         	<a type="button" class="btn" title="信令图" onclick="switchTab(table_tab_1,table_tab_0,'chart')">
								<i class="fa fa-picture-o"></i>
							</a>   
		                </div>
		                <div class="btn-group">
		                    <!-- <button type="button" class="btn btn-default disabled" onclick="exportProcess()" id="exportBtn">
		                        <i class="fa fa-sign-out"></i>导出
		                    </button> -->
		                    <a id="exportBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onclick="exportProcess()"><span class="ladda-label">导出</span></a>
		                    <a id="exportBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onclick="exportProcessPicture()"><span class="ladda-label">导出图片</span></a>
		                </div>
					</div>
					
				</div>
				<!-- <ul class="nav nav-tabs" role="tablist">
						<li class="active"><a href="#table_tab_0" data-toggle="tab" id="table_tab_0_nav"
							aria-expanded="false">信令表</a></li>
						<li class=""><a href="#table_tab_1" data-toggle="tab" id="table_tab_1_nav"
							aria-expanded="false">信令图</a></li>
					</ul> -->	
				<div class="box-body" style="height:560px;overflow:auto;padding:10px 0;">
					<div class="tabs tab-content" id="table_chart">
						<div class=" tab-pane active" id="table_tab_0">
							<table id="signalingTable" class="easyui-datagrid" style="width:100%;height:100%">
				            </table>
				        </div>
						<div class=" tab-pane" id="table_tab_1">
							<div id="signalingChart">
		            		</div>
						</div>
				    </div>
				</div>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="box">
				<div  class="box-header with-border">
					<h3 class="box-title">详细解码</h3>
				</div>
				<div class="box-body">
					<div class="modal-body col-sm-12" style="height:560px;overflow:auto;background-color:#fff;">
						<iframe scrolling="auto" id="message" frameborder="0" style="width:100%;height:100%;white-space: nowrap;"></iframe>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection


@section('scripts')


<link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>

<!--input select-->
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

<!--select2-->
<script type="text/javascript" src="plugins/select2/select2.js"></script>

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

<!-- raphael -->
<script src="plugins/raphael/raphael-min.js"></script>

<!-- treegrid -->
<link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/easyui.css">
<link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/datagrid.css">
<link rel="stylesheet" href="dist/css/signalingBacktracking.css">
<script src="plugins/EasyUI/jquery.easyui.min.js"></script>
<script src="plugins/EasyUI/locale/datagrid-scrollview.js"></script>

<script type="text/javascript" src="dist/js/complaintHandling/signalingBacking.js"></script>

<!--bootstrapvalidator-->
<!-- <link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script> -->

<!--highcharts-->
<script src="plugins/highcharts/js/highcharts.js"></script>
<script src="plugins/highcharts/js/modules/exporting.js"></script>

<link rel="stylesheet" href="dist/css/button.css">
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>


