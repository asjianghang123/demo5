@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>
        重叠覆盖点图
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> 专项研究</a></li>
        <li><a href="#">重叠覆盖分析</a></li>
        <li class='active'>重叠覆盖点图</li>
    </ol>
</section>
@endsection
@section('content')
<section class="content">
	<div class="row">
        <div class="col-sm-4">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">导航</h3>
                </div>
                <div class="box-body">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">查询条件</h3>
                        </div>
                        <div class="box-body">
                            <form class="form-horizontal" role="form" id="queryForm">
                                <div class="form-group">
                                    <label for="city" class="col-sm-2 control-label">城市</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="city" id="city">
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="date" class="col-sm-2 control-label">日期</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="date" id="date">
                                        </input>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="channel" class="col-sm-2 control-label">频段</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="channel" id="channel" multiple="multiple">
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="box-footer">
                            <div class="pull-right">
                                <div class="btn-group">
                                    <a class="btn  btn-primary ladda-button" data-style="expand-right" onclick="getData()" id="search">
                                        <span class="ladda-label">查询</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">扇区颜色</h3>
                        </div>
                        <div class="box-body">
                            <table border="1px" style="font-size:14px;border:none;float:left;margin-left:15px;">
                                <tr>
                                    <td align='center'><input class="chooseCell" name='chooseCell' type='checkbox' value='0' style="margin:2px" /></td>
                                    <td bgColor='blue' align='center' width="80px"> 0~1 </td>
                                </tr>
                                <tr>
                                    <td align='center'><input class="chooseCell" name='chooseCell' type='checkbox' value='1'style="margin:2px"/></td>
                                    <td bgColor='green' align='center' width="80px"> 1~2 </td>
                                </tr>
                            </table>
                            <table border="1px" style="font-size:14px;border:none;float:left;margin-left:15px;">
                                <tr>
                                    <td align='center'><input class="chooseCell" name='chooseCell' type='checkbox' value='2'style="margin:2px"/></td>
                                    <td bgColor='lime' align='center' width="80px"> 2~3 </td>
                                </tr>
                                <tr>
                                    <td align='center'><input class="chooseCell" name='chooseCell' type='checkbox' value='3'style="margin:2px"/></td>
                                    <td bgColor='yellow' align='center' width="80px"> 3~4 </td>
                                </tr>
                            </table>
                            <table border="1px" style="font-size:14px;border:none;float:left;margin-left:15px;">
                                <tr>
                                    <td align='center'><input class="chooseCell" name='chooseCell' type='checkbox' value='4'style="margin:2px"/></td>
                                    <td bgColor='magenta' align='center' width="80px"> 4~5 </td>
                                </tr>
                                <tr>
                                    <td align='center'><input class="chooseCell" name='chooseCell' type='checkbox' value='5'style="margin:2px"/></td>
                                    <td bgColor='red' align='center' width="80px"> >=5 </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">定位</h3>
                        </div>
                        <div class="box-body">
                            <form class="form-horizontal" role="form" id="queryForm">
                                <div class="form-group">
                                    <label for="cell" class="col-sm-2 control-label">小区</label>
                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <input type="hidden" id="last_city">
                                            <input type="hidden" id="last_date">
                                            <input type="hidden" id="last_channel">
                                            <input type="text" class="form-control" name="cell" id="cell" placeholder="输入ecgi">
                                            <span class="input-group-btn">
                                                <button class="btn btn-primary" type="button"  onclick="setPointByCell()" id="locationCellBtn">定位</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="col-sm-8">
            <div class="box">
                <div class="box-body">
                    <div id="map" style="position: relative;height: 600px;width:100%" ></div>
                </div>
            </div>
            <div class="zhaozi" id="map1_zhaozi"></div>
            <div class="loadingImg text-center" id="map1_loadingImg">
                <span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
            </div>
        </div>
    </div>

    <!-- <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">重叠覆盖点图</h3>
                </div>
                <div class="box-body">
                	<form class="form-inline" role='form'>
                		<div class="form-group">
                			&nbsp;&nbsp;城市:
                			<select id="city" class="form-control input-sm" >
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
                		<div class="form-group">
                			<a id="search" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="getData()"><span class="ladda-label">查询</span></a>
                	    </div>
                	</form>
                </div>
                <div class="box-body">
                    <div id="map" style="position: relative;height: 600px;"></div>
                </div>
        	</div>
    	</div>
    </div> -->
</section>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                  &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    柱状图显示
                </h4>
          	</div>
        	<div id="weakCoverChartsContainer" class="modal-body"></div>
    	</div>
	</div>
</div>
@endsection
@section('scripts')
    <!-- <script src="dist/js/genius/alarm-chart.js"></script> -->
    <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
    <script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>
    <script src="plugins/highcharts/js/highcharts.js"></script>
    <!--datatables-->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="plugins/datatables/grid.js"></script>
    <link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

    <!-- <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=XtxLWdHvIBw0FKDLBh835SwO"></script> -->
    <script type="text/javascript" src="plugins/baidumapv2/baidumap_offline_v2_load.js"></script>
	<link rel="stylesheet" type="text/css" href="plugins/baidumapv2/css/baidu_map_v2.css"/>
    <script src="plugins/mapv/Mapv.js"></script>
    <style type='text/css'>
		.datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
	    	background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
	    	border-color: #ffb733;
		}	
		.modal-dialog{
		    position: relative;
		    display: table; 
		    overflow-y: auto;    
		    overflow-x: auto;
		    width: auto;
		    min-width: 300px;   
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
@endsection
<script src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/genius/overlapCoverPoint.js"></script>