@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>RRU板卡分析</h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-dashboard"></i>日常优化
		</li>
		<li>告警分析</li>
		<li class="active">RRU板卡分析
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
					<div class="box-tools pull-right">
						<!-- <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown" onClick="openConfigInfo()">
                    		<i class="fa fa-wrench"></i>
                    	</button> -->
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
				</div>
				<div class='box-body'>
					<div class="row">
						<div class="col-md-1">城市</div>
						<div class="col-md-3">
							<select id="allCity" class="form-control input-sm" multiple="multiple">
							</select>
						</div>
					</div>
				</div>
				<div class='box-footer' style="text-align:right">
					<a id="search" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" onClick="searchSlot()"><span class="ladda-label ">查询</span></a>
				</div>
			</div>
			<div class='box'>
				<div class="box-header with-border">
					<h3 class="box-title">板卡串号记录表(当前板卡数量：<span id="currSlotNum"></span> 当前信息显示日期：<span id="currTime"></span>)</h3>
					<div class="box-tools pull-right">
                        <a id="export" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" onClick="exportAllSlot()"><span class="ladda-label">导出</span></a>
                    </div>
				</div>
				<div class="box-body">
					<div class="table-responsive">
						<table id="slotTable">
						</table>
					</div>
				</div>	
			</div>
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">消失板卡串号报告</h3>
				</div>
				<div class="box-body">
					<div class="box">
						<div class="box-header with-border">
							<h3 class="box-title">消失板卡串号列表</h3>
							<div class="box-tools pull-right">
		                        <a id="exportDisappearSlot" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" onClick="exportDisappearSlot()"><span class="ladda-label">导出</span></a>
		                    </div>
						</div>
						<div class="box-body">
							<table id="disappearSlotTable"></table>
						</div>	
					</div>
					<div class="box">
						<div class="box-header with-border">
							<h3 class="box-title">串号趋势图</h3>
						</div>
						<div class="box-body">
							<div id="slotTrendChart" style="max-height: 400px"></div>
						</div>	
					</div>
				</div>	
			</div>

			<div class="box">
				<div class="box-header">
					<h3 class="box-title">板卡串号查询</h3>

				</div>
				<div class="box-body">
					<div class="box">
						<!-- <div class='box-header with-border'>
							<h3 class="box-title">查询条件</h3>
							<div class="box-tools pull-right">
		                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
		                    </div>
						</div> -->
						<div class='box-body'>
							<div class="row">
								<div class="col-md-1">板卡串号</div>
								<div class="col-md-3">
									<input id="serialNumber" type="text" class="form-control input-sm" >
									</input>
								</div>
							</div>
						</div>
						<div class='box-footer' style="text-align:right">
							<a id="searchOneSolt" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" onClick="searchOneSolt()"><span class="ladda-label ">查询</span></a>
							<a id="exportOneSolt" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" onClick="exportOneSolt()"><span class="ladda-label">导出</span></a>
						</div>
					</div>
					<div class="box">
						<div class="box-header with-border">
							<h3 class="box-title">板卡信息</h3>
							<div class="btn-div pull-right">
								<div class="btn-group">
								<a type="button" class='btn' href="#table_tab_0" data-toggle="tab" id="table_tab_0_nav"
								aria-expanded="false"><i class="fa fa-picture-o"></i></a>
		                        <a type="button" class='btn' href="#table_tab_1" data-toggle="tab" id="table_tab_1_nav"
								aria-expanded="false"><i class="fa fa-bars"></i></a></li>
				                </div>
							</div>
						</div>
						<div class="tabs tab-content">
							<div class=" tab-pane active" id="table_tab_0">	          
								<div class="box-body">
						            <div id="resultView" style="max-height:400px;"></div>
					            </div>
							</div>
							<div class=" tab-pane" id="table_tab_1">
								<div class="box-body">
									<table id="resultTable">
									</table>
								</div>
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
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >  

<script src="plugins/highcharts/js/highcharts.js"></script>
<script type="text/javascript" src="plugins/highcharts/js/highcharts-more.js"></script>

<!--loading-->
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>
 <!-- treegrid -->
<script src="plugins/EasyUI/jquery.easyui.min.js"></script>

@endsection
<script src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/alarmAnalysis/rruSlotAnalysis.js"></script>
<link rel="stylesheet" href="dist/css/button.css">
<style>
	#slotTable td div{
		width:100%;
		white-space:nowrap;
		overflow:hidden;
		text-overflow:ellipsis;
	}
	#disappearSlotTable td div{
		width:100%;
		white-space:nowrap;
		overflow:hidden;
		text-overflow:ellipsis;
	}
	#resultTable td div{
		width:100%;
		white-space:nowrap;
		overflow:hidden;
		text-overflow:ellipsis;
	}
</style>
