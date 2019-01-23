@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>CTR信令回溯</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>投诉处理
		</li>
		<li>实时监控</li>
		<li class="active">CTR信令回溯</li>
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
							<label for="citys" class="col-sm-1 control-label">城市</label>
                            <div class="col-sm-3">
                                <select class="form-control" name="citys" id="citys">
                                </select>
                            </div>
							<label for="date" class="col-sm-1 control-label">日期</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="date" id="date">
								</input>
							</div>
							<label for="hours" class="col-sm-1 control-label">小时</label>
							<div class="col-sm-3">
								<select class="form-control" name="hours" id="hours" multiple="true">
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="imsi" class="col-sm-1 control-label">用户</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="imsi" id="imsi" placeholder="输入imsi或者msisdn">
							</div>
						</div>
					</form>
				</div>
				<div class="box-footer">
					<div class="btn-group pull-right">
	                    <a type="button" class="btn btn-primary ladda-button" data-style="expand-right" id="queryBtn" onclick="queryProcess()">
	                        <span class="ladda-label">查询</span>
	                    </a>
	                </div>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title" style="margin-top:8px;">信令流程</h3>
					<div class="btn-div pull-right">
						<div class="btn-group">
		                    <a  class="btn btn-warning ladda-button" data-style="expand-right" onclick="filterProcess()" id="filterBtn" disabled="disabled">
		                        <span class="ladda-label">筛选流程</span>
		                    </a>
		                </div>
		                <div class="btn-group">
		                    <a  class="btn btn-default ladda-button" data-style="expand-right" onclick="exportProcess()" id="exportBtn" disabled="disabled">
		                        <span class="ladda-label">导出</span>
		                    </a>
		                </div>
					</div>
					
				</div>
				<ul class="nav nav-tabs" role="tablist">
					<li class="active"><a href="#table_tab_0" data-toggle="tab" id="table_tab_0_nav"
						aria-expanded="false">信令表</a></li>
					<li class=""><a href="#table_tab_1" data-toggle="tab" id="table_tab_1_nav"
						aria-expanded="false">信令图</a></li>
				</ul>	
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
	</div>
</section>
<!-- 详细解码弹出框 -->
<div class="modal fade" id="message_modal">
	<div class="modal-dialog" id="modalDialog" style="width:900px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">详细解码</h8>
            </div>
			<div class="modal-body col-sm-12" style="height:400px;overflow:auto;background-color:#fff;">
					
				<iframe scrolling="auto" id="message" frameborder="0" style="width:100%;height:100%;white-space: nowrap;"></iframe>
			</div>
			
		</div>
	</div>
</div>

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

<!--highcharts-->
<script src="plugins/highcharts/js/highcharts.js"></script>

<script type="text/javascript" src="dist/js/complaintHandling/xinlinghuisu.js"></script>

<!--bootstrapvalidator-->
<!-- <link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script> -->

<style type='text/css'>
    .datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
    background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
    border-color: #ffb733;
    }   
</style>
@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>


