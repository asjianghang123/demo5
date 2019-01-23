@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>
        重叠覆盖受主分析
    </h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i>专项研究</li>
        <li>重叠覆盖分析</li>
        <li class='active'>重叠覆盖受主分析</li>
    </ol>
</section>
@endsection 
@section('content')
<section class="content">
    <div class="row">
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
                                <select class="form-control" name="citys" id="citys">
                                </select>
                            </div>
                            <label for="date" class="col-sm-1 control-label">日期</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="date" id="date">
                                </input>
                            </div>
                            <label for="cellName" class="col-sm-1 control-label">小区</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="cellName" id="cellName" placeholder="输入小区名或者ecgi进行查询">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box-footer">
                    <div class="pull-right">
                        <div class="btn-group">
                            <a  class="btn  btn-primary ladda-button" data-style="expand-right" onclick="queryTrail()" id="queryBtn">
                                <span class="ladda-label">查询</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-8">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">地理化显示</h3>
                </div>
                <div class="box-body">
                    <div id="map1" style="position: relative;height: 500px;width:100%" ></div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">指标详情</h3>
                    <div class="pull-right">
                        <div class="btn-group">
                            <a  class="btn btn-primary ladda-button" data-style="expand-right" onclick="exportData()" id="exportBtn">
                                <span class="ladda-label">导出</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <table id="detailTable" ></table>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">详细数据</h4>
            </div>
            <div class="modal-body">
                <table id='bMapTable' class="display" cellspacing="0" border="1">
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
    <script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>
    <!--datatables-->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="plugins/datatables/grid.js"></script>
    <link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

    <!--loading-->
    <link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
    <script src="plugins/loading/js/spin.js"></script>
    <script src="plugins/loading/js/ladda.js"></script>


    <!-- <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=XtxLWdHvIBw0FKDLBh835SwO"></script> -->
    <script type="text/javascript" src="plugins/baidumapv2/baidumap_offline_v2_load.js"></script>
    <link rel="stylesheet" type="text/css" href="plugins/baidumapv2/css/baidu_map_v2.css"/>
    <script src="plugins/mapv/Mapv.js"></script>
    <style type='text/css'>
    .datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
    background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
    border-color: #ffb733;
    }
    
</style>
@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/genius/overlappingAcceptorAnalysis.js"></script>