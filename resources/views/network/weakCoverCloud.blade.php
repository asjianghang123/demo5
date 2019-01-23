@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>
        弱覆盖分析
    </h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i> 专项研究</li>
        <li>弱覆盖分析</li>
        <li class='active'>弱覆盖云图</li>
    </ol>
</section>
@endsection
@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">弱覆盖云图</h3>
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
                	<form class="form-inline" role='form'>
                		<div class="form-group">
                			城市:
                		</div>
                		<div class="form-group">
                			<select id="allCity" class="form-control input-sm">
								</select>
                		</div>
                		<div class="form-group">
                			&nbsp;&nbsp;日期:
							<input id="date" class="form-control" type="text" value=""/>
                		</div>
                		<td>频段</td>
								<th>
									<select id="channel" class="form-control input-sm" multiple="multiple">
									</select>
								</th>
                        <td>
                		<div class="form-group">
                			<a id="search" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="drawMap()"><span class="ladda-label">查询</span></a>
                	    </div>
                	</form>
                   <!--  <table class="table">
                        <tr>
                        	<td>城市</td>
                        	<th>
                        		<select id="allCity" class="form-control input-sm"  multiple="multiple">
								</select>
                        	</th>
                            <td>日期</td>
                            <th>
                            	<input id="date" class="form-control input-sm" type="text" value="" style="width:250px"/>
                            </th>
                            <td>频段</td>
								<th>
									<select id="channel" class="form-control input-sm" multiple="multiple">
									</select>
								</th>
                            <td>

                                <div style="text-align:right;">
                                    <a id="search" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="drawMap()"><span class="ladda-label">查询</span></a>
                                </div>
                            </td>
                        </tr>
                    </table> -->
                </div>
                <div class="box-body">
                    <div id="map" style="position: relative;height: 600px;"></div>
                    <!-- ./box-body -->
                </div>
                
            </div>
        </div>
    </div>
</section>
        <!-- /.col -->
@endsection
@section('scripts')
    <script src="plugins/highcharts/js/highstock.js"></script>
    <!-- <script src="dist/js/genius/alarm-chart.js"></script> -->
    <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
    <script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>
    <style type='text/css'>
        .datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
        background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
        border-color: #ffb733;
        }   
    </style>
    <!--datatables-->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="plugins/datatables/grid.js"></script>
    <link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >
    <!--input select-->
	<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
	<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

	<script type="text/javascript" src="plugins/baidumapv2/baidumap_offline_v2_load.js"></script>
	<link rel="stylesheet" type="text/css" href="plugins/baidumapv2/css/baidu_map_v2.css"/>

    <script src="plugins/mapv/Mapv.js"></script>
 	<script type="text/javascript" src="dist/js/genius/weakCoverCloud.js"></script>
@endsection