@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>
        下行业务量点图
    </h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i>地理呈现</li>
        <li>负荷分析</li>
        <li class='active'>下行业务量点图</li>
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
                                    <label for="citys" class="col-sm-2 control-label">城市</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="citys" id="citys" multiple="multiple">
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
                            </form>
                        </div>
                        <div class="box-footer">
                            <div class="pull-right">
                                <div class="btn-group">
                                    <a class="btn  btn-primary ladda-button" data-style="expand-right" onclick="getData()" id="queryBtn">
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
                                    <td bgColor='green' align='center' width="80px"> 1~10 </td>
                                </tr>
                            </table>
                            <table border="1px" style="font-size:14px;border:none;float:left;margin-left:15px;">
                                <tr>
                                    <td align='center'><input class="chooseCell" name='chooseCell' type='checkbox' value='2'style="margin:2px"/></td>
                                    <td bgColor='lime' align='center' width="80px"> 10~20 </td>
                                </tr>
                                <tr>
                                    <td align='center'><input class="chooseCell" name='chooseCell' type='checkbox' value='3'style="margin:2px"/></td>
                                    <td bgColor='yellow' align='center' width="80px"> 20~50 </td>
                                </tr>
                            </table>
                            <table border="1px" style="font-size:14px;border:none;float:left;margin-left:15px;">
                                <tr>
                                    <td align='center'><input class="chooseCell" name='chooseCell' type='checkbox' value='4'style="margin:2px"/></td>
                                    <td bgColor='magenta' align='center' width="80px"> 50~100 </td>
                                </tr>
                                <tr>
                                    <td align='center'><input class="chooseCell" name='chooseCell' type='checkbox' value='5'style="margin:2px"/></td>
                                    <td bgColor='red' align='center' width="80px"> >=100 </td>
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
                    <div id="map2" style="position: relative;height: 600px;width:100%" ></div>
                </div>
            </div>
            <div class="zhaozi" id="map2_zhaozi"></div>
            <div class="loadingImg text-center" id="map2_loadingImg">
                <span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
            </div>
        </div>
    </div>
    <!-- <div class="row">
        <div class="col-md-12">
        	<div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">查询条件</h3>
                </div>
                <div class="box-body">
                    <form class="form-horizontal" role="form" id="queryForm">
                        <div class="form-group">
                            <label for="citys" class="col-sm-1 control-label">城市</label>
                            <div class="col-sm-3">
                                <select class="form-control" name="citys" id="citys" multiple="multiple">
                                </select>
                            </div>
                            <label for="date" class="col-sm-1 control-label">日期</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="date" id="date">
                                </input>
                            </div>
                            <label for="cell" class="col-sm-1 control-label">小区</label>
                            <div class="col-sm-3">
                                <div class="input-group">
                                    <input type="hidden" id="last_city">
                                    <input type="hidden" id="last_date">
                                    <input type="text" class="form-control" name="cell" id="cell" placeholder="输入小区">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary" type="button"  onclick="setPointByCell()" id="locationCellBtn">定位</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <div class="btn-group">
                            <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="getData()" id="queryBtn">
                                <span class="ladda-label">查询</span>
                            </button>
                            <a id="queryBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onclick="getData()"><span class="ladda-label">查询</span></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">下行业务量（GB）</h3>
                </div>
                <div class="box-body">
                    <div id="map2" style="position: relative;height: 600px;width:100%" ></div>
                    <div class="zhaozi" id="map2_zhaozi"></div>
                    <div class="loadingImg text-center" id="map2_loadingImg">
                        <span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
</section>
<!-- 详细信息 -->
<div class="modal fade" id="detail_modal">
    <div class="modal-dialog" style="width:900px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title">详细信息</h8>
            </div>
            <div class="modal-body">
                <div class="form-group"">
                    <table id="cellDetailTable"></table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="col-sm-2 col-sm-offset-5 btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
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

   <!--  <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=XtxLWdHvIBw0FKDLBh835SwO"></script> -->
   <script type="text/javascript" src="plugins/baidumapv2/baidumap_offline_v2_load.js"></script>
    <link rel="stylesheet" type="text/css" href="plugins/baidumapv2/css/baidu_map_v2.css"/>
    <script src="plugins/mapv/Mapv.js"></script>
@endsection
<script src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/genius/downlinkTrafficCloud.js"></script>
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