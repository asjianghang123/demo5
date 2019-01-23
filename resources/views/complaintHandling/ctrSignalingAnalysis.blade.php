@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>CTR信令搜索</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>专项研究
		</li>
		<li>信令分析</li>
		<li class="active">CTR信令搜索</li>
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
				</div>
				<div class="box-body">
					<form class="form-horizontal" role="form" id="queryForm">
						<div class="form-group">
							<!-- <div id="bakZtreeNode"></div> -->
							<div id="fileNameBak" style="display: none;"></div>
							<label for="dataBase" class="col-sm-1 control-label">数据库</label>
							<div class="col-sm-3">
								<select type="text" class="form-control" name="dataBase" id="dataBase">
								</select>
							</div>
							<label for="eventName" class="col-sm-1 control-label">Event Name</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="eventName" id="eventName"  placeholder="输入类似：'RRC_MEASUREMENT_REPORT'">
								</input>
							</div>
							<label for="ztreeNode" class="col-sm-1 control-label">过滤</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="ztreeNode" id="ztreeNode">
								</input>
							</div>
							<!-- <label for="inputTable" class="col-sm-1 control-label">表</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="inputTable" id="inputTable">
								</input>
							</div> -->
						</div>
					</form>
				</div>
				<div class="box-footer" style="text-align:right;">
					<a id="queryBtn" class="btn btn-primary ladda-button" data-style="expand-right" href="#" onClick="query()"><span class="ladda-label">查询&筛选</span></a>
					<a id="querySelectBtn" class="btn btn-primary ladda-button" data-style="expand-right" href="#" onClick="exports()"><span class="ladda-label">导出</span></a> <!-- select() -->
					<!-- <div class="btn-group pull-right">
	                    <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="query()" id="queryBtn">
	                        <i class="fa fa-search"></i>
	                        <span class="ladda-label">查询</span>
	                    </button>
	                </div>
	                &nbsp;&nbsp;
	                <div class="btn-group pull-right">
	                    <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="select()" id="querySelectBtn">
	                        <i class="fa fa-search"></i>
	                        <span class="ladda-label">筛选</span>
	                    </button>
	                </div> -->
				</div>
			</div>
		</div>
	</div>

	<!-- <div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">信令图</h3>
				</div>
				<div id="chartHead" style="width:100%;overflow:hidden;"></div>
				<div id="signalingChart" style="width:100%;height:500px;overflow:auto;"></div>
			</div>
		</div>
	</div> -->
	<div class="row">
		<div class="col-sm-8">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title" id='title'>信令流程</h3>
					<div class="btn-group pull-right">
						<a type="button" class="btn" onclick="switchTab(table_tab_1,table_tab_0,'table')">
	                        <i class="fa fa-picture-o"></i>     
	                    </a>
	                    <a type="button" class="btn"  onclick="switchTab(table_tab_0,table_tab_1,'chart')">
	                        <i class="fa fa-bars"></i>
	                    </a>
	                </div>
				</div>
				<div class="box-body"> <!-- style="height:570px;overflow:auto;" -->
					<div class="tabs tab-content">
						<div class="tab-pane" id="table_tab_1">
							
						</div>
						<div class="tab-pane active" id="table_tab_0">
							<table id="signalingChart" style="word-wrap:break-word;word-break:break-all;"> 
							</table>
							<!-- <table id="signalingChart_filter" style="word-wrap:break-word;word-break:break-all;"> 
							</table> -->
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
					<div class="col-sm-12" style="height:563px;overflow:auto;">
						<ul id="detailMessageTree" class="ztree"></ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!--查看详细解码弹出框 -->
<!-- <div class="modal fade" id="detailMessage">
	<div class="modal-dialog" id="modalDialog" style="width:900px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">详细解码</h8>
            </div>
			<div class="modal-body row">
				<div class="col-sm-12" style="height:400px;overflow:auto;">
					<ul id="detailMessageTree" class="ztree"></ul>
				</div>
			</div>
			<div class="modal-footer">
				<div class="pull-right">
					<button type="button" class="btn btn-primary"  data-dismiss="modal">关闭</button>
				 </div>
			</div>

		</div>
	</div>
</div> -->
@endsection


@section('scripts')
<style type="text/css"> 
    a {
    	cursor:pointer;
    	color:#97a0b3;
    }
    a:hover {
    	color: #606c84;
    }
</style> 

<link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>

<!--input select-->
<!-- <script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" /> -->

<!--select2-->
<script type="text/javascript" src="plugins/select2/select2.js"></script>

<!--treeview-->
<!-- <script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script> -->

<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>

<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!-- raphael -->
<script src="plugins/raphael/raphael-min.js"></script>

<!-- treegrid -->
<!-- <link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/easyui.css">
<link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/datagrid.css">
<link rel="stylesheet" href="dist/css/signalingBacktracking.css">
<script src="plugins/EasyUI/jquery.easyui.min.js"></script>
<script src="plugins/EasyUI/locale/datagrid-scrollview.js"></script> -->

<!--bootstrapvalidator-->
<!-- <link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script> -->

<!--zTree-->
<link rel="stylesheet" href="plugins/zTree/css/zTreeStyle/zTreeStyle.css">
<script src="plugins/zTree/js/jquery.ztree.core.js"></script>

<!--highcharts-->
<script src="plugins/highcharts/js/highcharts.js"></script>
<style>
	.select2-container .select2-selection--single{
		height:34px;
		border-radius:0;
	   	border: 1px solid #d2d6de;
	}
	.select2-container--default .select2-selection--single .select2-selection__arrow{
		top:3px;
	}
</style>
@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/complaintHandling/ctrSignalingAnalysis.js"></script>


