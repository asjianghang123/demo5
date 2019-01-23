@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>
        高干扰分析
    </h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i> 专项研究</li>
        <li>高干扰分析</li>
        <li class='active'>干扰云图</li>
    </ol>
</section>
@endsection
@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">干扰云图</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-wrench"></i></button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#">Print Chart</a></li>
                                <li class="divider"></li>
                                <li><a href="#">Download PNG img</a></li>
                                <li><a href="#">Download JPEG img</a></li>
                                <li><a href="#">Download PDF document</a></li>
                                <li><a href="#">Download SVG vector img</a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
					<form class="form-horizontal" role="form" id="queryForm">
						<div class="form-group">
							<label class="col-sm-1 control-label" for="allCity">城市</label>
							<div class="col-sm-2">
								<select id="allCity" class="form-control input-sm" multiple="multiple">
								</select>
							</div>
							<label class="col-sm-1 control-label" for="date">日期</label>
							<div class="col-sm-2">
								<input id="date" class="form-control" type="text" value=""/>
							</div>
							<label class="col-sm-1 control-label" for="hour">小时</label>
							<div class="col-sm-2">
								<select id="hour" class="form-control input-sm">
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
							<label class="col-sm-1 control-label" for="minute">分钟</label>
							<div class="col-sm-2">
								<select id="minute" class="form-control input-sm">
									<option value='0'>0</option>
									<option value='15'>15</option>
									<option value='30'>30</option>
									<option value='45'>45</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-1 control-label" for="channel">频段</label>
							<div class="col-sm-2">
								<select id="channel" class="form-control input-sm" multiple="multiple">
								</select>
							</div>
							<label class="col-sm-1 control-label" for="dimension">维度</label>
							<div class="col-sm-2">
								<select id="dimension" class="form-control input-sm">
									<option value='PUSCH上行干扰电平'>pusch</option>
									<option value='PUCCH上行干扰电平'>pucch</option>
									<option value='prb100_avg'>100prb</option>
								</select>
							</div>
							<div class="col-sm-2 col-sm-offset-4">
								<a id="search" class="btn btn-primary ladda-button" data-color='red' data-style="expand-left" onClick="drawMap()">
									<span class="ladda-label">查询</span>
								</a>
								<a id="refreshButton" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" onClick="drawMapPoint()">
									<span class="ladda-label">刷新维度</span>
								</a>
							</div>
						</div>
					</form>
                </div>
                <div class="box-body">
                    <div id="map" style="position: relative;height: 600px;"></div>
                </div>
                
            </div>
        </div>
    </div>
</section>
        <!-- /.col -->
@endsection
@section('scripts')
    <script src="plugins/highcharts/js/highstock.js"></script>
    <script src="dist/js/genius/alarm-chart.js"></script>
    <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
    <script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>
    <!--datatables-->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="plugins/datatables/grid.js"></script>
    <link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >
    <!--input select-->
	<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
	<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

    <!-- <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=XtxLWdHvIBw0FKDLBh835SwO"></script> -->
    <script type="text/javascript" src="plugins/baidumapv2/baidumap_offline_v2_load.js"></script>
	<link rel="stylesheet" type="text/css" href="plugins/baidumapv2/css/baidu_map_v2.css"/>
    <script src="plugins/mapv/Mapv.js"></script>
    <style type='text/css'>
		.datepicker table tr td.today,
		.datepicker table tr td.today:hover,
		.datepicker table tr td.today.disabled,
		.datepicker table tr td.today.disabled:hover {
			background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
			border-color: #ffb733;
		}	
    </style>
@endsection
<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/genius/interCloud.js"></script>