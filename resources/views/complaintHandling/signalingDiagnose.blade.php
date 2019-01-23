@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>信令分析</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-rocket"></i>专项研究
		</li>
		<li>信令分析</li>
		<li class="active">信令诊断</li>
	</ol>
</section>
@endsection
@section('content')
 

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
					<form class="form-horizontal" role="form" id="queryForm">
						<label for="city" class="col-sm-1 control-label">城市</label>
						<div class="col-sm-2">
							<select class="form-control" name="city" id="city">
							</select>
						</div>
	                    <label for="dateTime" class="col-sm-1 control-label">日期</label>
	                    <div class="col-sm-2">
	                        <input id="dateTime" class="form-control" type="text" value=""/>
	                    </div>
	                    <label for="hourSelect" class="col-sm-1 control-label">小时</label>
	                    <div class="col-sm-2">
	                        <select class="form-control" name="hourSelect" id="hourSelect" multiple="multiple">
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
	                    <label for="user" class="col-sm-1 control-label">用户</label>
	                    <div class="col-sm-2">
	                        <input id="userInfo" class="form-control" type="text" value=""/>
	                    </div>
	                </form>
				</div>
				<div class="box-footer" style="text-align:right">
					
						<a id="search" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="doSearch()"><span class="ladda-label ">查询</span></a>
						<!-- <a id="export" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="doSearchbadCell('file','低接入小区')"><span class="ladda-label">导出</span></a> -->
					
				</div>
			</div>
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">诊断报告</h3>
					<div class="box-tools pull-right">
	                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                </div>
				</div>
				<div class="box-body">
					<div class="col-md-6">
						<div class="panel-group">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion" 
										   href="#collapseOne">
											核心网侧
										</a>
									</h4>
								</div>
								<div class="panel-body">
									<div class="chart tab-pane active" id="chart-coreNetwork" style="position: relative;height: 400px;"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="panel-group">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion" 
										   href="#collapseOne">
											无线网侧
										</a>
									</h4>
								</div>
								<div class="panel-body">
									<div class="chart tab-pane active" id="chart-wlanNetwork" style="position: relative;height: 400px;"></div>
								
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--信令详情-->
		    <div class="box">
				<div class="box-header">
					<h3 class="box-title">信令详情</h3>
					<div class="box-tools pull-right">
	                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                </div>
				</div>
				<div class="box-body">
						<div class="box">
							<div class="box-header">
								<h3 class="box-title">核心网侧</h3>
								<div class="box-tools pull-right">
				                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				                </div>
							</div>
							<div class="box-body">
							 	<table id="signalingDetailTable_core" class="gj-grid-table table table-bordered table-hover">
	                                </table>
							</div>
						</div>
						<div class="box">
							<div class="box-header">
								<h3 class="box-title">无线网侧</h3>
								<div class="box-tools pull-right">
				                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				                </div>
							</div>
							<div class="box-body">
							 	<table id="signalingDetailTable_wlan" class="gj-grid-table table table-bordered table-hover">
	                                </table>
							</div>
						</div>
				</div>
			</div>
			<!--信令详情结束-->
			<!--信令时序图-->
		    <div class="box">
				<div class="box-header">
					<h3 class="box-title">信令时序图</h3>
					<div class="box-tools pull-right">
	                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                </div>
				</div>
				<div class="box-body">
				 	<div id="timingDiagramChart" style="position: relative;height: 400px;"></div>
				</div>
			</div>
			<!--信令时序图-->
		</div>
	</div>

	
</section>
@endsection


@section('scripts')


<link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>

<!--select2-->
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!-- raphael -->
<script src="plugins/raphael/raphael-min.js"></script>

<!--zTree-->
<link rel="stylesheet" href="plugins/zTree/css/zTreeStyle/zTreeStyle.css">
<script src="plugins/zTree/js/jquery.ztree.core.js"></script>
<style>
	.datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
    background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
    border-color: #ffb733;
	}	
	.select2-container .select2-selection--single{
		height:34px;
		border-radius:0;
	   	border: 1px solid #d2d6de;
	}
	.select2-container--default .select2-selection--single .select2-selection__arrow{
		top:3px;
	}
</style>
<script src="plugins/highcharts/js/highstock.js"></script>
<script src="plugins/highcharts/js/highcharts-more.js"></script>
<script src="plugins/highcharts/js/modules/exporting.js"></script>

@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/complaintHandling/signalingDiagnose.js"></script>


