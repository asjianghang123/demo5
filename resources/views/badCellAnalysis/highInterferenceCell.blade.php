@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>高干扰小区</h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-dashboard"></i>日常优化
		</li>
		<li><i class="fa fa-dashboard"></i>差小区分析
		</li>
		<li class="active">高干扰小区
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
							<label for="cellType" class="col-sm-1 control-label">小区类别</label>
							<div class="col-sm-3">
								<select class="form-control" type="text" name="cellType" id="cellType">
									<option value="avg">平均PRB</option>
                					<option value="one">任一PRB</option>
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
						<div class="form-group">
							<label for="startDate" class="col-sm-1 control-label">起始日期</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="startDate" id="startDate">
								</input>
							</div>
							<label for="endDate" class="col-sm-1 control-label">结束日期</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="endDate" id="endDate">
								</input>
							</div>
							<label for="startDate" class="col-sm-1 control-label">小时</label>
							<div class="col-sm-3">
								<select id="allHour" class="form-control" multiple="multiple">
									<option value='0'>0</option>
									<option value='1'>1</option>
									<option value='2'>2</option>
									<option value='3'>3</option>
									<option value='4'>4</option>
									<option value='5'>5</option>
									<option value='6'>6</option>
									<option value='7'>7</option>
									<option value='8'>8</option>
									<option value='9'>9</option>
									<option value='10'>10</option>
									<option value='11'>11</option>
									<option value='12'>12</option>
									<option value='13'>13</option>
									<option value='14'>14</option>
									<option value='15'>15</option>
									<option value='16'>16</option>
									<option value='17'>17</option>
									<option value='18'>18</option>
									<option value='19'>19</option>
									<option value='20'>20</option>
									<option value='21'>21</option>
									<option value='22'>22</option>
									<option value='23'>23</option>
								</select>
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
                        <a id="exportBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onclick="exportFile()">
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
			<!-- <div class='box'>
				<div class='box-header'>
					<h3 class="box-title">Alarm</h3>
					<div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
				</div>
				<div class="box-body" style="position:relative; height:400px; overflow:auto">
					<table class="table" id="alarmTable">
					</table>
					<div class="zhaozi" id="alarm_zhaozi"></div>
					<div class="loadingImg text-center" id="alarm_loadingImg">
						<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
					</div>
				</div>
			</div> -->
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">诊断报告</h3>
					<div class="box-tools pull-right">
	                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                </div>
				</div>
				<div class="box-body">
					<div class="col-md-4">
						<div class="panel-group">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion" 
										   href="#collapseOne">
											告警类
										</a>
									</h4>
								</div>
								<div id="collapseOne" class="panel-collapse collapse in">
									<div class="panel-body">
										<!-- <a href="#alarmBox"><div class="col-md-8"><span id='erbsAlarmNumSpan'></span>&nbsp;&nbsp;基站级告警数量：</div><div class="col-md-4"><input type="text" id='erbsAlarmNum' style="width: 50px" value="" /></div></a>
										<a href="#alarmBox"><div class="col-md-8"><span id='alarmNumSpan'></span>&nbsp;&nbsp;小区级告警数量：</div><div class="col-md-4"><input type="text" id='alarmNum' style="width: 50px" value="" /></div></a> -->
										<a href="#alarmBox"><div class="col-md-6"><span id='erbsAlarmNumSpan'></span>&nbsp;&nbsp;基站级告警数量：</div><div class="col-md-6"><input alt="全天" title="全天" type="text" id='erbsAlarmNum' style="width: 60px" value="全天" /><input alt="差时" title="差时" type="text" id='erbsAlarmNumHour' style="width: 60px" value="差时" /></div></a>
										<a href="#alarmBox"><div class="col-md-6"><span id='alarmNumSpan'></span>&nbsp;&nbsp;小区级告警数量：</div><div class="col-md-6"><input alt="全天" title="全天" type="text" id='alarmNum' style="width: 60px" value="全天" /><input alt="差时" title="差时" type="text" id='alarmNumHour' style="width: 60px" value="差时" /></div></a>
									</div>
								</div>
							</div>
						</div>
					</div>
						
					<div class="col-md-4">
						<div class="panel-group">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion" 
										   href="#collapseTwo">
											邻区类
										</a>
									</h4>
								</div>
								<div id="collapseTwo" class="panel-collapse collapse in">
									<div class="panel-body">
										<a href="#table_tab_0"><div class="col-md-8"><span id='LteNumSpan'></span>&nbsp;&nbsp;建议补4G邻区数量：</div><div class="col-md-4"><input type="text" id='LteNum' style="width: 50px" value="" /></div></a>	
										<a href="#table_tab_0"><div class="col-md-8"><span id='GsmNumSpan'></span>&nbsp;&nbsp;建议补2G邻区数量：</div><div class="col-md-4"><input type="text" id='GsmNum' style="width: 50px" value="" /></div></a>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-4">
						<div class="panel-group">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion" 
										   href="#collapseThree">
											覆盖类
										</a>
									</h4>
								</div>
								<div id="collapseThree" class="panel-collapse collapse in">
									<div class="panel-body">
										<a href="#weakCoverBox"><div class="col-md-8"><span id='weakCoverNumSpan'></span>&nbsp;&nbsp;弱覆盖小区频次：</div><div class="col-md-4"><input type="text" id='weakCoverNum' style="width: 50px" value="" /></div></a>
										<a href="#weakCoverBox"><div class="col-md-8"><span id='overlapCeakCoverNumSpan'></span>&nbsp;&nbsp;重叠覆盖小区频次：</div><div class="col-md-4"><input type="text" id='overlapCeakCoverNum' style="width: 50px" value="" /></div></a>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-4">
						<div class="panel-group">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion" 
										   href="#collapseFour">
											干扰类
										</a>
									</h4>
								</div>
								<div id="collapseFour" class="panel-collapse collapse in">
									<div class="panel-body">
										<!-- <a href="#interfereBox"><div class="col-md-8"><span id='highInterfereNumSpan'></span>&nbsp;&nbsp;高干扰小区频次：</div><div class="col-md-4"><input type="text" id='highInterfereNum' style="width: 50px" value="" /></div></a> -->
										<a href="#interfereBox"><div class="col-md-6"><span id='highInterfereNumSpan'></span>&nbsp;&nbsp;高干扰小区频次：</div><div class="col-md-6"><input alt="全天" title="全天" type="text" id='highInterfereNum' style="width: 60px" value="全天" /><input alt="差时" title="差时" type="text" id='highInterfereNumHour' style="width: 60px" value="差时" /></div></a>
										<a href="#prbInterfereBox"><div class="col-md-6"><span id='prbHighInterfereNumSpan'></span>&nbsp;&nbsp;PRB干扰：</div><div class="col-md-6"><input alt="全天" title="全天" type="text" id='prbHighInterfereNum' style="width: 60px" value="全天" /><input alt="差时" title="差时" type="text" id='prbHighInterfereNumHour' style="width: 60px" value="差时" /></div></a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class='box'>
				<div class="box-header">
					<h3 class="box-title">趋势图:时域</h3>
					<div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
				</div>
				<div class="box-body" style="position:relative;">
					<div id="timeDomainContainer" style="position: relative;height: 400px;"></div>
					<div class="zhaozi" id="chart1_zhaozi"></div>
					<div class="loadingImg text-center" id="chart1_loadingImg">
						<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
					</div>
				</div>
			</div>
			<div class='box'>
				<div class="box-header">
					<h3 class="box-title">趋势图:频域</h3>
					<div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
				</div>
				<div class="box-body" style="position:relative;">
					<div class="form-group">
						<label for="timeList" class="col-sm-1 control-label">时间</label>
						<div class="col-sm-3">
							<select class="form-control" type="text" name="timeList" id="timeList">
							</select>
						</div>
					</div>
					<div id="frequencyDomainContainer" style="position: relative;height: 400px;"></div>
					<div class="zhaozi" id="chart2_zhaozi"></div>
					<div class="loadingImg text-center" id="chart2_loadingImg">
						<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
					</div>
				</div>
			</div>
			<div class="box" id="alarmBox">
				<div class="box-header">
					<h3 class="box-title">告警分析</h3>
						<div class="box-tools pull-right">
	                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                    </div>
				</div>
				<div class="box-body">  <!-- style="position:relative; height:650px;" -->
					<div class="nav-tabs-custom">
						<ul class="nav nav-tabs pull-left">
                            <li class="active"><a href="#cellAlarmClassify" data-toggle="tab">小区级告警分类</a></li>
                            <li><a href="#erbsAlarmClassify" data-toggle="tab">基站级告警分类</a></li>
                            <li><a href="#alarmDetails" data-toggle="tab">告警详情</a></li>
                        </ul>
                        <div class="tab-content">
                        	<div class="chart tab-pane active" id="cellAlarmClassify">
                        	</div>
                        	<div class="chart tab-pane" id="erbsAlarmClassify">	
                        	</div>
                        	<div class="chart tab-pane" id="alarmDetails">
                        		<table class="table" id="alarmWorstCellTable">
								</table>
                        	</div>
                        </div>
                        <div class="zhaozi" id="alarm_zhaozi"></div>
								<div class="loadingImg text-center" id="alarm_loadingImg">
									<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
								</div>
					</div>
				</div>
			</div>

			<div class="box" id="weakCoverBox">
				<div class="box-header">
					<h3 class="box-title">弱覆盖分析</h3>
						<div class="box-tools pull-right">
	                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                    </div>
				</div>
				<div class="box-body" style="position:relative;overflow:auto">    <!-- style="position:relative; height:400px; overflow:auto" -->
					<div class="chart tab-pane active" id="weakCoverageCell">
					</div>
					<div class="zhaozi" id="weak_zhaozi"></div>
					<div class="loadingImg text-center" id="weak_loadingImg">
						<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
					</div>
				</div>
			</div>

			<div class="box" id="interfereBox">
				<div class="box-header">
					<h3 class="box-title">干扰分析</h3>
						<div class="box-tools pull-right">
	                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                    </div>
				</div>
				<div class="box-body" style="position:relative;overflow:auto">   <!-- style="position:relative; height:550px; overflow:auto" -->
					<table id='interfereAnalysis' class="display" cellspacing="0" border="1">             
                    </table>
					<div class="zhaozi" id="interfere_zhaozi"></div>
					<div class="loadingImg text-center" id="interfere_loadingImg">
						<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
					</div>
				</div>
			</div>
			<div class="box" id="neighBox">
				<div class="box-header">
					<h3 class="box-title">邻区分析</h3>
					<div class="box-tools pull-right">
	                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                </div>
				</div>
				<div class="box-body">
					<ul class="nav nav-tabs" role="tablist">
						<li class="active"><a href="#table_tab_2" data-toggle="tab" id="table_tab_2_nav"
							aria-expanded="false">缺失同频邻区</a></li>
						<li class=""><a href="#table_tab_0" data-toggle="tab" id="table_tab_0_nav"
							aria-expanded="false">缺失异频邻区</a></li>
						<li class=""><a href="#table_tab_1" data-toggle="tab" id="table_tab_1_nav"
							aria-expanded="false">缺失2G邻区</a></li>
					</ul>
					<div class="tabs tab-content ">
						<div class=" tab-pane" id="table_tab_0">  <!-- style="position:relative; height:500px; overflow:auto" -->
							<table id="LTETable"></table>
							<!-- <div class="zhaozi" id="LTE_zhaozi"></div>
							<div class="loadingImg text-center" id="LTE_loadingImg">
								<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
							</div> -->
						</div>
						<div class=" tab-pane" id="table_tab_1">  <!-- style="position:relative; height:500px; overflow:auto" -->
							<table id="GSMTable"></table>
							<!-- <div class="zhaozi" id="GSM_zhaozi"></div>
							<div class="loadingImg text-center" id="GSM_loadingImg">
								<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
							</div> -->
						</div>
						<div class=" tab-pane active" id="table_tab_2">
							<table id="LTETable_1"></table>

						</div>
					</div>
				</div>
			</div>
			<div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">详细数据</h4>
                        </div>
                        <div class="modal-body">
                            <table id='bMapTable' class="display" cellspacing="0" border="1">
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
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

  <!--select-->
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

<!-- Bootstrap WYSIHTML5 -->

<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
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
<script type="text/javascript" src="dist/js/badCellAnalysis/highInterferenceCell.js"></script>
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
</style>
