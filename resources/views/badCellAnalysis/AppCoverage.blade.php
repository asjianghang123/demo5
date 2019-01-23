@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>自忙时小区</h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i>日常优化</a>
		</li>
		<li><a href="#"><i class="fa fa-dashboard"></i>差小区分析</a>
		</li>
		<li class="active">自忙时小区
		</li>
	</ol>
</section>
@endsection
@section('content')
<section class="content">
	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class='box-body'>
					<div class="row">
						<div class="col-md-1">城市</div>
						<div class="col-md-2">
							<select id="allCity" class="form-control input-sm" multiple="multiple">
							</select>
						</div>
						<div class="col-md-6">
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
						</div>
						<div class="col-md-1">小时</div>
						<div class="col-md-2">
							<select id="allHour" class="form-control input-sm" multiple="multiple">
							</select>
						</div>
					</div>
					<div class="row">
						<label class="col-md-1 control-label">小区</label>
	                    <div class="col-md-2">
	                        <input id="cellname" class="form-control" type="text" value=""/>
	                    </div>
					</div>
				</div>
				
				<div class='box-footer' style="text-align:right">
					<a id="search" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="templateQuery()"><span class="ladda-label ">查询</span></a>
				</div>
			</div>
			<div class='box'>
				<div class="box-header with-border">
					<h3 class="box-title">小区列表</h3>
					<div class="box-tools pull-right">
                        <a id="export" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="downfile()"><span class="ladda-label">导出</span></a>
                    </div>
				</div>
				<div class="box-body">
					<div class="table-responsive">
						<table id="badCellTable">
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
    #alarmNum,#LteNum,#GsmNum,#weakCoverNum,#highInterfereNum,#erbsAlarmNum,#overlapCeakCoverNum,#firstOrderConflictNum,#secondOrderConflictNum,#erbsAlarmNumHour,#alarmNumHour,#highInterfereNumHour,#prbHighInterfereNumHour,#prbHighInterfereNum{
	   border:0;
	}
	#currentAlarm,#needAddNeigh,#less116Proportion,#less155Proportion,#cqi,#featureState,#licenseState,#avgTA,#overlapCover,#AvgPRB,#highTraffic,#highTraffic2,#parameter,#wirelessCallRate_interfere,#wirelessCallRate_zhicha,#wirelessCallRate_RRCEstSucc,#wirelessCallRate_ERABEstSucc{
	   border:0;
	   background-color: #fff;
	}
</style> 
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<!-- jQuery 2.2.0 -->
<!-- datepicker -->
 <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
 <script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>

  <!--datatables-->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="plugins/datatables/grid.js"></script>
    <link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >


<!--bootstrapvalidator-->
<link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script>    

<!-- Bootstrap WYSIHTML5 -->

<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<script src="plugins/highcharts/js/highcharts.js"></script>
<script type="text/javascript" src="plugins/highcharts/js/highcharts-more.js"></script>

<!--loading-->
<!-- <link rel="stylesheet" href="plugins/loading/css/ladda-themeless.min.css"> -->
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!-- <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=XtxLWdHvIBw0FKDLBh835SwO"></script> -->
<script type="text/javascript" src="plugins/baidumapv2/baidumap_offline_v2_load.js"></script>
<link rel="stylesheet" type="text/css" href="plugins/baidumapv2/css/baidu_map_v2.css"/>
<script src="plugins/mapv/Mapv.js"></script>
<style type='text/css'>
	.datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
    background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
    border-color: #ffb733;
	}	
</style>
 <!-- treegrid -->
<link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/datagrid.css">
<link rel="stylesheet" href="dist/css/LTETemplateManage.css">
<script src="plugins/EasyUI/jquery.easyui.min.js"></script>

@endsection
<script src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/badCellAnalysis/AppCoverage.js"></script>
<link rel="stylesheet" href="dist/css/button.css">
<style>
	.zhaozi{
		width:100%;
		height:100%;
		position:absolute;
		top:0;
		left:0;
		display:none;
		background-color:#000;
		opacity:.3;
		z-index:10;
	}
	.loadingImg{
		position:absolute;
		top:80px;
		width:100%;
		z-index:11;
		display:none;
	}
	.loadingImg > span{
		display: inline-block;
		padding: 10px 15px;
		background-color:#fff;
	}
	.highcharts-container {
		width: 10000px;
	}

</style>
