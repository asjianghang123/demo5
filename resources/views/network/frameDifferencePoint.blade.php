@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>
        高干扰分析
    </h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i> 专项研究</li>
        <li>高干扰分析</li>
        <li class='active'>子帧差异点图</li>
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
                                    <label for="allCity" class="col-sm-2 control-label">城市</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="city" id="allCity" multiple="multiple">
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
                                    <label for="hour" class="col-sm-2 control-label">小时</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="hour" id="hour">
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
                                <div class="form-group">
                                    <label for="quarter" class="col-sm-2 control-label">分钟</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="quarter" id="quarter">
                                        	<option value='0'>0</option>
											<option value='15'>15</option>
											<option value='30'>30</option>
											<option value='45'>45</option>
                                        </select>
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
                                    <span class="ladda-label" data-style="expand-left">
                                        <button class="btn btn-primary" type="button"  onclick="doSearch()" id="searchBtn">查询</button>
                                    </span>
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
                                    <td bgColor='blue' align='center' width="80px"> <-3 </td>
                                </tr>
                            </table>
                            <table border="1px" style="font-size:14px;border:none;float:left;margin-left:15px;">
                                <tr>
                                    <td align='center'><input class="chooseCell" name='chooseCell' type='checkbox' value='1'style="margin:2px"/></td>
                                    <td bgColor='grey' align='center' width="80px"> -3~3 </td>
                                </tr>
                            </table>
                            <table border="1px" style="font-size:14px;border:none;float:left;margin-left:15px;">
                                <tr>
                                    <td align='center'><input class="chooseCell" name='chooseCell' type='checkbox' value='2'style="margin:2px"/></td>
                                    <td bgColor='red' align='center' width="80px"> >3 </td>
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
                                            <input type="hidden" id="last_hour">
                                            <input type="hidden" id="last_minute">
                                            <input type="hidden" id="last_channel">
                                            <input type="text" class="form-control" name="cell" id="cell" placeholder="输入小区">
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
                    <div id="mapPoint" style="position: relative;height: 600px;width:100%" ></div>
                </div>
            </div>
            <div class="zhaozi" id="map1_zhaozi"></div>
            <div class="loadingImg text-center" id="map1_loadingImg">
                <span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
<!-- <script src="plugins/highcharts/js/highstock.js"></script>
<script src="dist/js/genius/alarm-chart.js"></script> -->
<link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>
<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

<link rel="stylesheet" href="plugins/loading/css/ladda-theme.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>
    <!--input select-->
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

<!-- <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=XtxLWdHvIBw0FKDLBh835SwO"></script> -->
<script type="text/javascript" src="plugins/baidumapv2/baidumap_offline_v2_load.js"></script>
<link rel="stylesheet" type="text/css" href="plugins/baidumapv2/css/baidu_map_v2.css"/>
<script src="plugins/mapv/Mapv.js"></script>
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
@endsection
<script src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/genius/frameDifferencePoint.js"></script>