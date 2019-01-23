@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>能力分析</h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-dashboard"></i>用户分析
		</li>
		<li>终端分析</li>>
		<li class="active">能力分析
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
					<h3 class="box-title">查询条件</h3>
				</div>
				<div class='box-body'>
					<form class="form-horizontal" role="form" id="queryForm">
						<div class="form-group">
							<label for="citys" class="col-sm-1 control-label">城市</label>
                            <div class="col-sm-3">
                                <select class="form-control" name="citys" id="citys">
                                </select>
                            </div>
                            <!-- <label for="startTime" class="col-sm-1 control-label">日期</label>
                            <div class="col-sm-3">
                                <input id="startTime" class="form-control" type="text" value=""/>
                            </div> -->

                            <label for="startTime" class="col-sm-1 control-label">日期</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="startTime" id="startTime">
                                </input>
                            </div>
                            
						</div>
					</form>
				</div>
				<div class="box-footer">
					<div class="pull-right">
						<div class="btn-group">
		                    <a type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="query()" id="queryBtn">
		                        <span class="ladda-label">查询</span>
		                    </a>
		                </div>
					</div>
				</div>
			</div>
			<div class='box'>
				<div class="box-header">
					<h3 class="box-title" style="padding-top:10px">FDD频段能力渗透率</h3>
					<div class="btn-group pull-right">
	                    <button type="button" class="btn btn-primary" onclick="switchTab(table_tab_0,table_tab_1)">
	                        <i class="fa fa-bar-chart"></i>
	                    </button>
	                    <button type="button" class="btn btn-primary" onclick="switchTab(table_tab_1,table_tab_0)">
	                        <i class="fa fa-table"></i>
	                    </button>
	                </div>
				</div>
				<div class="box-body">
					<div class="tabs tab-content">
						<div class="box tab-pane active" id="table_tab_0">
							<div class="box-header">

							</div>
							<div class="box-body">
					            <div class="chart tab-pane active" id="chartData" style="height:400px">
					            </div>
				            </div>
						</div>
						<div class="box tab-pane" id="table_tab_1">
							<!-- <div class="box-header">
								<div class="btn-group pull-right">
											                    <button type="button" class="btn btn-primary ladda-button" data-style="expand-right" id="brandExportBtn" onclick="tableDataExport()">
											                        <i class="fa fa-search"></i>导出
											                    </button>
											                </div>
							</div> -->
							<div class="box-body">
					            <table id="tableData">
								</table>
				            </div>
						</div>
					</div>	
				</div>	
			</div>
            <div class='box'>
				<div class="box-header">
					<h3 class="box-title" style="padding-top:10px">TDD频段能力渗透率</h3>
					<div class="btn-group pull-right">
	                    <button type="button" class="btn btn-primary" onclick="switchTab(table_tab_2,table_tab_3)">
	                        <i class="fa fa-bar-chart"></i>
	                    </button>
	                    <button type="button" class="btn btn-primary" onclick="switchTab(table_tab_3,table_tab_2)">
	                        <i class="fa fa-table"></i>
	                    </button>
	                </div>
				</div>
				<div class="box-body">
					<div class="tabs tab-content">
						<div class="box tab-pane active" id='table_tab_2'>
							<div class="box-header">

							</div>
							<div class="box-body">
					            <div class="chart tab-pane active" id="chart-bandEutra" style="height: 400px;"></div>
				            </div>
						</div>
						<div class="box tab-pane" id='table_tab_3'>
							<div class="box-header">

							</div>
							<div class="box-body">
					            <table id="table-bandEutra">
								</table>
				            </div>
						</div>
					</div>	
				</div>	
			</div>
			<div class='box'>
				<div class="box-header">
					<h3 class="box-title" style="padding-top:10px">FGI能力渗透率</h3>
					<div class="btn-group pull-right">
	                    <button type="button" class="btn btn-primary" onclick="switchTab(table_tab_4,table_tab_5)">
	                        <i class="fa fa-bar-chart"></i>
	                    </button>
	                    <button type="button" class="btn btn-primary" onclick="switchTab(table_tab_5,table_tab_4)">
	                        <i class="fa fa-table"></i>
	                    </button>
	                </div>
				</div>
				<div class="box-body">
					<div class="tabs tab-content">
						<div class="box tab-pane active" id='table_tab_4'>
							<div class="box-header">

							</div>
							<div class="box-body" style="height:400px;overflow:auto;">
					            <div class="chart tab-pane active" id="chart-FGI"></div>
				            </div>
						</div>
						<div class="box tab-pane" id='table_tab_5'>
							<div class="box-header">

							</div>
							<div class="box-body">
					            <table id="table-FGI">
								</table>
				            </div>
						</div>
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

<style type='text/css'>
	.datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
	    background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
	    border-color: #ffb733;
	}	
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
<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css">
<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<script src="plugins/highcharts/js/highstock.js"></script>
<script src="plugins/highcharts/js/highcharts-more.js"></script>
<script src="plugins/highcharts/js/modules/exporting.js"></script>
@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/userAnalysis/abilityAnalysis.js"></script>

