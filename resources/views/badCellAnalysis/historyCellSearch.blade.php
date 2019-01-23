@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>历史小区查询</h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i>日常优化</a>
		</li>
		<li><a href="#"><i class="fa fa-dashboard"></i>差小区分析</a>
		</li>
		<li class="active"><a href="#">历史小区查询</a>
		</li>
	</ol>
</section>
@endsection
@section('content')

<section class="content">
	<div class="row">
		<div class="col-sm-12">
			<div class='box'>
				<div class="box-header">
					<h3 class="box-title">查询条件</h3>
					<div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
				</div>
                <ul class="nav nav-tabs" role="tablist" id="type">
                    <li id="lowAccess" class="active"><a href="#lowAccessTab" data-toggle="tab"><input type="text" value="LowAccess" hidden="true" />低接入小区</a></li>
                    <li id="highLost"><a href="#highLostTab" data-toggle="tab"><input type="text" value="HighLost" hidden="true" />高掉线小区</a></li>
                    <li id="badHandover"><a href="#badHandoverTab" data-toggle="tab"><input type="text" value="BadHandover" hidden="true" />切换差小区</a></li>
                    <li id="Interference"><a href="#highInterferenceTab" data-toggle="tab"><input type="text" value="Interference" hidden="true" />高干扰小区</a></li>
                </ul>
                <div class="tabs tab-content"> 
                    <div class="tab-pane active" id="lowAccessTab">  <!-- style="position: relative;height: 500px" -->
                        <div class="box-body">
                        	<form class="form-horizontal" role="form" id="lowAccessForm">
                        		<br />
                        		<div class="form-group">
                        			<label for="citysLowAccess" class="col-sm-1 control-label">城市</label>
									<div class="col-sm-4">
										<select class="form-control" name="citysLowAccess" id="citysLowAccess" multiple="true">
										</select>
									</div>
									<label for="cellLowAccess" class="col-sm-1 control-label">小区</label>
									<div class="col-sm-4">
										<input type="text" class="form-control" name="cellLowAccess" id="cellLowAccess">
									</div>
                        		</div>
                        		<div class="form-group">
                        			<label for="startDateLowAccess" class="col-sm-1 control-label">起始日期</label>
									<div class="col-sm-4">
										<input type="text" class="form-control" name="startDateLowAccess" id="startDateLowAccess">
									</div>
									<label for="endDateLowAccess" class="col-sm-1 control-label">结束日期</label>
									<div class="col-sm-4">
										<input type="text" class="form-control" name="endDateLowAccess" id="endDateLowAccess">
									</div>
                        		</div>
                        		<div class="form-group">
                        			<label for="hourLowAccess" class="col-sm-1 control-label">小时</label>
									<div class="col-sm-4">
										<select class="form-control" name="hourLowAccess" id="hourLowAccess" multiple="true">
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
                        <div class="box-footer" style="text-align:right">
							<a id="searchLowAccess" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="doSearchbadCell('table', 'lowAccess')"><span class="ladda-label">查询</span></a>
							<a id="exportLowAccess" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="doSearchbadCell('file', 'lowAccess')"><span class="ladda-label">导出</span></a>
						</div>
                    </div>

                    <div class="tab-pane" id="highLostTab">  <!-- style="position: relative;height: 500px" -->
                        <div class="box-body">
                        	<form class="form-horizontal" role="form" id="highLostForm">
                        		<br />
                        		<div class="form-group">
                        			<label for="citysHighLost" class="col-sm-1 control-label">城市</label>
									<div class="col-sm-4">
										<select class="form-control" name="citysHighLost" id="citysHighLost" multiple="true">
										</select>
									</div>
									<label for="cellHighLost" class="col-sm-1 control-label">小区</label>
									<div class="col-sm-4">
										<input type="text" class="form-control" name="cellHighLost" id="cellHighLost">
									</div>
                        		</div>
                        		<div class="form-group">
                        			<label for="startDateHighLost" class="col-sm-1 control-label">起始日期</label>
									<div class="col-sm-4">
										<input type="text" class="form-control" name="startDateHighLost" id="startDateHighLost">
									</div>
									<label for="endDateHighLost" class="col-sm-1 control-label">结束日期</label>
									<div class="col-sm-4">
										<input type="text" class="form-control" name="endDateHighLost" id="endDateHighLost">
									</div>
                        		</div>
                        		<div class="form-group">
                        			<label for="hourHighLost" class="col-sm-1 control-label">小时</label>
									<div class="col-sm-4">
										<select class="form-control" name="hourHighLost" id="hourHighLost" multiple="true">
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
                        <div class="box-footer" style="text-align:right">
							<a id="searchHighLost" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="doSearchbadCell('table', 'highLost')"><span class="ladda-label">查询</span></a>
							<a id="exportHighLost" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="doSearchbadCell('file', 'highLost')"><span class="ladda-label">导出</span></a>
						</div>
                    </div>

                    <div class="tab-pane" id="badHandoverTab">  <!-- style="position: relative;height: 500px" -->
                        <div class="box-body">
                        	<form class="form-horizontal" role="form" id="badHandoverForm">
                        		<br />
                        		<div class="form-group">
                        			<label for="citysBadHandover" class="col-sm-1 control-label">城市</label>
									<div class="col-sm-4">
										<select class="form-control" name="citysBadHandover" id="citysBadHandover" multiple="true">
										</select>
									</div>
									<label for="cellBadHandover" class="col-sm-1 control-label">小区</label>
									<div class="col-sm-4">
										<input type="text" class="form-control" name="cellBadHandover" id="cellBadHandover">
									</div>
                        		</div>
                        		<div class="form-group">
                        			<label for="startDateBadHandover" class="col-sm-1 control-label">起始日期</label>
									<div class="col-sm-4">
										<input type="text" class="form-control" name="startDateBadHandover" id="startDateBadHandover">
									</div>
									<label for="endDateBadHandover" class="col-sm-1 control-label">结束日期</label>
									<div class="col-sm-4">
										<input type="text" class="form-control" name="endDateBadHandover" id="endDateBadHandover">
									</div>
                        		</div>
                        		<div class="form-group">
                        			<label for="hourBadHandover" class="col-sm-1 control-label">小时</label>
									<div class="col-sm-4">
										<select class="form-control" name="hourBadHandover" id="hourBadHandover" multiple="true">
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
                        <div class="box-footer" style="text-align:right">
							<a id="searchBadHandover" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="doSearchbadCell('table', 'badHandover')"><span class="ladda-label">查询</span></a>
							<a id="exportBadHandover" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="doSearchbadCell('file', 'badHandover')"><span class="ladda-label">导出</span></a>
						</div>
                    </div>

                    <div class="tab-pane" id="highInterferenceTab">  <!-- style="position: relative;height: 500px" -->
                        <div class="box-body">
                        	<form class="form-horizontal" role="form" id="highLostForm">
                        		<br />
                        		<div class="form-group">
                        			<label for="cellType" class="col-sm-1 control-label">小区类别</label>
                        			<div class="col-sm-3">
                        				<select class="form-control" name="cellType" id="cellType">
                        					<option value="avgPRB">平均PRB</option>
                        					<option value="onePRB">任一PRB</option>
										</select>
                        			</div>
                        			<label for="citysHighInterference" class="col-sm-1 control-label">城市</label>
									<div class="col-sm-3">
										<select class="form-control" name="citysHighInterference" id="citysHighInterference" multiple="true">
										</select>
									</div>
									<label for="cellHighInterference" class="col-sm-1 control-label">小区</label>
									<div class="col-sm-3">
										<input type="text" class="form-control" name="cellHighInterference" id="cellHighInterference">
									</div>
                        		</div>
                        		<div class="form-group">
                        			<label for="startDateHighInterference" class="col-sm-1 control-label">起始日期</label>
									<div class="col-sm-3">
										<input type="text" class="form-control" name="startDateHighInterference" id="startDateHighInterference">
									</div>
									<label for="endDateHighInterference" class="col-sm-1 control-label">结束日期</label>
									<div class="col-sm-3">
										<input type="text" class="form-control" name="endDateHighInterference" id="endDateHighInterference">
									</div>
									<label for="hourHighInterference" class="col-sm-1 control-label">小时</label>
									<div class="col-sm-3">
										<select class="form-control" name="hourHighInterference" id="hourHighInterference" multiple="true">
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
                        <div class="box-footer" style="text-align:right">
							<a id="searchHighInterference" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="doSearchbadCell('table', 'highInterference')"><span class="ladda-label">查询</span></a>
							<a id="exportHighInterference" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="doSearchbadCell('file', 'highInterference')"><span class="ladda-label">导出</span></a>
						</div>
                    </div>
				</div>
			</div>

			<div class="box" id="lowAccessTable">
				<div class='box-header'>
					<h3 class="box-title">小区列表</h3>
					<div class="box-tools pull-right">
	                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                </div>
				</div>
				<div class="box-body">
					<div class="table-responsive">
						<table id="badCellTable">
						</table>
					</div>
				</div>	
			</div>
			
			<div class="box" id="index">
				<div class="box-header">
					<h3 class="box-title">指标</h3>
					<div class="box-tools pull-right">
						<span id="loadSaveData" class="glyphicon glyphicon-save" aria-hidden="true" onClick="fileSave()"></span>
	                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                </div>
				</div>
				<div class="nav-tabs-custom">
                    <ul class="nav nav-tabs pull-left">
                        <li class="active"><a href="#indexTrend" data-toggle="tab">指标趋势</a></li>
                        <li><a href="#indexDetails" data-toggle="tab">指标详情</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class=" tab-pane active" id="indexTrend"> 
                            <div class="box-body"> 
                            	<form class="form-horizontal" role="form" id="indexForm">
                            		<br /><br />
                            		<div class="form-group">
	                        			<label for="worstCellChartPrimaryAxisType" class="col-sm-1 control-label">主轴</label>
										<div class="col-sm-4">
											<select id="worstCellChartPrimaryAxisType" name="worstCellChartPrimaryAxisType" class="form-control">
											</select>
										</div>
										<label for="worstCellChartAuxiliaryAxisType" class="col-sm-1 control-label">辅轴</label>
										<div class="col-sm-4">
											<select id="worstCellChartAuxiliaryAxisType" name="worstCellChartAuxiliaryAxisType" class="form-control">
											</select>
										</div>
	                        		</div>
	                        		<div class="form-group">
	                        			<div id="worstCellContainer1"></div>	 
	                        		</div>
                            	</form>
                            </div>
                        </div>
                       	<div class="chart tab-pane" id="indexDetails">
                       		<div class="box-body">
                            	<div class="table-responsive">
									<table id="badCellTableIndex">
									</table>
									<input type="text" id="badCellTableIndexFilename" value="" style="display: none;" />
								</div>
                            </div>
                        </div>
                	</div>		
				</div>	
			</div>
</section>

@endsection
@section('scripts')
<style type="text/css"> 
    #loadSaveData {
    	cursor:pointer;
    	color:#97a0b3;
    }
    #loadSaveData:hover {
    	color: #606c84;
    }
</style> 

<!-- jQuery 2.2.0 -->
<!-- datepicker -->
 <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
 <script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>

 <!--select-->
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

 <!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

<!--bootstrapvalidator-->
<link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script>     

<!-- Bootstrap WYSIHTML5 -->
<script src="plugins/highcharts/js/highcharts.js"></script>

<!--loading-->
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>
<style type='text/css'>
	.datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
    background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
    border-color: #ffb733;
	}	
</style>
<link rel="stylesheet" href="dist/css/button.css">
@endsection
<script src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/badCellAnalysis/historyCellSearch.js"></script>