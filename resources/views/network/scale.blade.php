@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>
        规模概览
    </h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i> 网络概览</li>
        <li>规模概览</li>
    </ol>
</section>
@endsection 
@section('content')
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <div class="box-title">
                        <h4>
                            当前日期：{{date('Y-m-d')}}
                        </h4>
                    </div>
                    <button id="scaleExport" class="btn btn-primary pull-right ladda-button" data-style="expand-right" href="#" onClick="scaleExport()"><span class="ladda-label">导出报告</span></button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">关键规模</h3>
                    <!-- <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div> -->
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="box border-right">
                                <div class="box-body">
                                    <div id="scale-map" style="height: 505px" onmousewheel="return false">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="box border-left">
                            <div class="box-body" style="height: 525px">
                            <div class="col-md-4">
                                <div class="small-box bg-green">
                                    <div class="inner">
                                        <p>基站数量</p>

                                        <h4 id="meContextNum">15000</h4>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-rss"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="small-box bg-green">
                                    <div class="inner">
                                        <p>小区数量</p>

                                        <h4 id="cellNum">45000</h4>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-wifi"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="small-box bg-green">
                                    <div class="inner">
                                        <p>载频数量</p>

                                        <h4 id="slaveNum">45000</h4>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-adn"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <p>最大用户数</p>

                                        <h4 id="maxUser">0</h4>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-user"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <p>上行业务量(GB)</p>

                                        <h4 id="upTraffic">0</h4>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-angle-double-up"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <p>下行业务量(GB)</p>

                                        <h4 id="downTraffic">0</h4>

                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-angle-double-down"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="small-box bg-red">
                                    <div class="inner">
                                        <p>CSFB次数</p>

                                        <h4 id="csfbCount">0</h4>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-phone"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="small-box bg-red">
                                    <div class="inner">
                                        <p>volte呼叫次数</p>

                                        <h4 id="volteCalls">0</h4>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-volume-control-phone"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="small-box bg-red">
                                    <div class="inner">
                                        <p>Attach用户数</p>

                                        <h4 id="attachUser">0</h4>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-user-md"></i>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">基站类型分布</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">基于覆盖维度</h3>
                                </div>
                                <div class="box-body">
                                    <div id="bscSiteType" style="height: 400px"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">基于载波维度</h3>
                                </div>
                                <div class="box-body">
                                    <div id="bscSlave" style="height: 400px"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">基于CA维度</h3>
                                </div>
                                <div class="box-body">
                                    <div id="bscCA" style="height: 400px"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">基于城市维度</h3>
                                </div>
                                <div class="box-body">
                                    <div id="bscSiteTypeCity" style="height: 400px"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">基于城市维度</h3>
                                </div>
                                <div class="box-body">
                                    <div id="bscSlaveCity" style="height: 400px"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">基于城市维度</h3>
                                </div>
                                <div class="box-body">
                                    <div id="bscCACity" style="height: 400px"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">基站版本分布</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">基于城市分布</h3>
                                </div>
                                <div class="box-body">
                                    <div id="bscversion_city" style="height: 300px">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">基于类型分布</h3>
                                </div>
                                <div class="box-body">
                                    <div id="bscversion_type" style="height: 300px">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">载频频点分布</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">基于城市分布</h3>
                                </div>
                                <div class="box-body">
                                    <div id="rruandSlave_city" style="height: 300px">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">基于频点分布</h3>
                                </div>
                                <div class="box-body">
                                    <div id="rruandSlave_slave" style="height: 300px">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">RRU_DU数量分布</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">RRU数量分布</h3>
                                </div>
                                <div class="box-body">
                                    <div id="RRU_num_city" style="height: 300px">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">DU数量分布</h3>
                                </div>
                                <div class="box-body">
                                    <div id="DU_num_city" style="height: 300px">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">MMEGI_TAC数量分布</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">MMEGI_TAC数量分布</h3>
                                </div>
                                <div class="box-body">
                                    <div id="MMEGI_TAC_num" style="height: 300px">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-md-6">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">DU数量分布</h3>
                                </div>
                                <div class="box-body">
                                    <div id="DU_num_city" style="height: 300px">

                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- <div class="row">
        <div class="col-md-6">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">用户分布</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div id="users">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">业务分布</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div id="bussiness">

                    </div>
                </div>
            </div>
        </div>
    </div> -->
</section>
@endsection
@section('scripts')
<link rel="stylesheet" href="dist/css/genius/network_scale.css">
<script src="plugins/highcharts/js/highstock.js"></script>
<script src="plugins/highcharts/js/highcharts-more.js"></script>
<script src="plugins/highcharts/js/modules/map.js"></script>
<script src="plugins/highcharts/js/modules/exporting.js"></script>
{{--<script src="http://api.map.baidu.com/api?v=2.0&ak=XtxLWdHvIBw0FKDLBh835SwO"></script>--}}
{{--<script src="plugins/mapv/Mapv.js"></script>--}}
<!-- <script src="plugins/echarts/echarts.min.js"></script>
<script src="plugins/echarts/jiangsu.js"></script> -->
<script src="dist/js/genius/common/download.js"></script>
<script src="dist/js/genius/network-scale.js"></script>
@endsection


