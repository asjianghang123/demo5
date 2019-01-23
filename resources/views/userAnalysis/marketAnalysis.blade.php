@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>市场分析</h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-dashboard"></i>用户分析
		</li>
		<li>终端分析</li>>
		<li class="active">市场分析
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
					<h3 class="box-title" style="padding-top:10px">品牌排名</h3>
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
					            <div id="brandChart" style="height:400px">
					            </div>
				            </div>
						</div>
						<div class="box tab-pane" id="table_tab_1">
							<div class="box-header">
								<div class="btn-group pull-right">
				                    <button type="button" class="btn btn-primary ladda-button" data-style="expand-right" id="brandExportBtn" onclick="brandExport()">
				                        <i class="fa fa-search"></i>导出
				                    </button>
				                </div>
							</div>
							<div class="box-body">
					            <table id="brandTable">
								</table>
				            </div>
						</div>
					</div>	
				</div>	
			</div>
			<div class='box'>
				<div class="box-header">
					<h3 class="box-title" style="padding-top:10px">型号排名</h3>
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
						<div class="box tab-pane active" id="table_tab_2">
							<div class="box-header">

							</div>
							<div class="box-body">
					            <div id="modeChart" style="height:400px">
					            </div>
				            </div>
						</div>
						<div class="box tab-pane" id="table_tab_3">
							<div class="box-header">
								<div class="btn-group pull-right">
				                    <button type="button" class="btn btn-primary ladda-button" data-style="expand-right" id="modeExportBtn" onclick="modeExport()">
				                        <i class="fa fa-search"></i>导出
				                    </button>
				                </div>
							</div>
							<div class="box-body">
					            <table id="modeTable">
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
<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css">
<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<script src="plugins/highcharts/js/highcharts.js"></script>
@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/userAnalysis/marketAnalysis.js"></script>

