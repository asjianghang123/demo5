@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>极端高话务小区</h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-dashboard"></i>日常优化
		</li>
		<li><i class="fa fa-dashboard"></i>差小区分析
		</li>
		<li class="active">极端高话务小区
		</li>
	</ol>
</section>
@endsection
@section('content')
<section class="content">
	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class='box-header with-border'>
					<div class="box-header">
						<h3 class="box-title">查询条件</h3>
						<div class="box-tools pull-right">
	                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                    </div>
					</div>
				</div>
				<div class='box-body'>
					<form class="form-horizontal" role="form" id="queryForm">
						<div class="form-group">
							<label for="date" class="col-sm-1 control-label">日期</label>
							<div class="col-sm-3">
								<select id="date" class="form-control input-sm" style="width:270px;">
                                </select> 
							</div>
							<label for="citys" class="col-sm-1 control-label">城市</label>
							<div class="col-sm-3">
								<select class="form-control" name="citys" id="citys" multiple="true">
								</select>
							</div>
							<label for="cell" class="col-sm-1 control-label">小区</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="cell" id="cell">
							</div>
						</div>
					</form>
				</div>
				<div class="box-footer">
					<div class="pull-right">
						<div class="btn-group">
							<a id="queryBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onclick="query()"><span class="ladda-label ">查询</span></a>
		                    <!-- <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="query()" id="queryBtn">
		                        <i class="fa fa-search"></i>
		                        <span class="ladda-label">查询</span>
		                    </button> -->
		                </div>
		                <!-- <div class="btn-group">
		                    <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="exportFile()" id="exportBtn">
		                        <i class="fa fa-sign-out"></i>导出
		                    </button>
		                </div> -->
					</div>
				</div>
			</div>
			<div class='box'>
				<div class="box-header with-border">
					<h3 class="box-title">小区列表</h3>
					<div class="box-tools pull-right">
                        <a id="exportCellBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onclick="exportCellFile('极端高话务小区','EUtranCellTDD_ETH','exportCellBtn')">
                        <span class="ladda-label">导出</span></a>
                    </div>
				</div>
				<div class="box-body">
					<div class="table-responsive">
						<table id="cellTable">
						</table>
					</div>
				</div>	
			</div>
			<div class='box'>
				<div class="box-header with-border">
					<h3 class="box-title">BaseLine核查列表</h3>
					<div class="box-tools pull-right">
                        <a id="exportBaselineBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onclick="exportCellFile('BaseLine核查列表','TempParaCheckBaselineCheck_ETH','exportBaselineBtn')">
                        <span class="ladda-label">导出</span></a>
                    </div>
				</div>
				<div class="box-body">
					<div class="table-responsive">
						<table id="BaselineTable">
						</table>
					</div>
				</div>	
			</div>
			<div class='box'>
				<div class="box-header with-border">
					<h3 class="box-title">SC分场景核查列表</h3>
					<div class="box-tools pull-right">
                        <a id="exportSCBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onclick="exportCellFile('SC分场景核查列表','TempSystemConstantsCheck_ETH','exportSCBtn')">
                        <span class="ladda-label">导出</span></a>
                    </div>
				</div>
				<div class="box-body">
					<div class="table-responsive">
						<table id="SCTable">
						</table>
					</div>
				</div>	
			</div>
		</div>
	</div>
</section>



@endsection
@section('scripts')
<style type="text/css"> 
    .treeview span.indent{
    	margin:0;
    }
    #loadSaveData {
    	cursor:pointer;
    	color:#97a0b3;
    }
    #loadSaveData:hover {
    	color: #606c84;
    }
    /*#alarmNum,#LteNum,#GsmNum,#weakCoverNum,#highInterfereNum,#erbsAlarmNum,#overlapCeakCoverNum{
	   border:0;
	}*/
	#alarmNum,#LteNum,#GsmNum,#weakCoverNum,#highInterfereNum,#erbsAlarmNum,#overlapCeakCoverNum,#firstOrderConflictNum,#secondOrderConflictNum,#erbsAlarmNumHour,#alarmNumHour,#highInterfereNumHour,#prbHighInterfereNumHour,#prbHighInterfereNum{
	   border:0;
	}
</style> 
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<!-- jQuery 2.2.0 -->
<!-- datepicker -->
 <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
 <script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>

 <!--select2-->
<script type="text/javascript" src="plugins/select2/select2.js"></script>
 <!--select-->
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

<!-- Bootstrap WYSIHTML5 -->
<script src="plugins/highcharts/js/highcharts.js"></script>
<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>
<style type='text/css'>
	.datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
    background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
    border-color: #ffb733;
	}	
</style>
@endsection
<script src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/badCellAnalysis/extremeHighTrafficCell.js"></script>
<link rel="stylesheet" href="dist/css/button.css">