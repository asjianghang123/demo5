@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>
        指标概览
    </h1>
    <ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i> 网络概览</li>
        <li>指标概览</li>
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
                    <button id="kpiExport" class="btn btn-primary pull-right ladda-button" data-style="expand-right" href="" onclick="kpiExport()">导出报告</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">实时指标</h3>
                    <!-- <div class="box-tools pull-right">
                        <button  type="button" class="btn btn-box-tool" title="选取数据小时" data-toggle="modal" data-target="#chooseHourModal"><i class="glyphicon glyphicon-pencil" aria-hidden="true"></i></button>
                    </div>
                    <div class="modal fade" id="chooseHourModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">
                                        当前获取数据小时
                                    </h4>
                                </div>
                                <div class="modal-body">
                                    关键三项指标：<input type="text" id="dataFrom4" text-align='right' value='0' />时;
                                    volte指标：<input type="text" id="dataFrom2" text-align='right' value='0' />时;<br />
                                    vodeo指标：<input type="text" id="dataFrom3" text-align='right' value='0' />时。<br />
                                </div>
                            </div>
                        </div>
                    </div> -->

                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <!-- Todo: the height shound not be specified -->
                            <div class="box border-right">
                                <div class=box-body>
                                    <div id="map" style="height: 505px"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                                <div class="box border-left">
                                    <div class="box-header text-center">
                                        <span class="box-title"><small>关键三项指标 /
                                            <input id='threeKeysInput' style="width:17px;" readonly="readonly"/>时
                                        <?php 
                                            // $dsn = "mysql:host=localhost;dbname=AutoKPI"; 
                                            // $db = new PDO($dsn, 'root', 'mongs');
                                            // $sql = "SELECT * FROM SysCoreTemp_city_hour ORDER BY id DESC LIMIT 1";
                                            // $res = $db->query($sql);
                                            // $row = $res->fetch();
                                            // print_r($row['hour_id'].'时');
                                        ?></small></span>
                                    </div>
                                    <div class="box-body">
                                        <div id="key3_kpigroup"></div>
                                    </div>
                                </div>

                                <div class="box">
                                    <div class="box-header text-center">
                                        <span class="box-title"><small>volte指标 /
                                            <input id='volteInput' style="width:17px;" readonly="readonly"/>时
                                        <?php 
                                            // $dsn = "mysql:host=localhost;dbname=nbi"; 
                                            // $db = new PDO($dsn, 'root', 'mongs');
                                            // $sql =  "SELECT HourId from EutranCellTddQuarter GROUP BY DateId,HourId ORDER by DateId DESC, HourId DESC LIMIT 1";
                                            // $res = $db->query($sql);
                                            // $row = $res->fetch();
                                            // print_r($row['HourId'].'时');
                                        ?>
                                        </small></span>
                                    </div>
                                    <div class="box-body">
                                        <div id="vlote_kpigroup"></div>
                                    </div>
                                </div>

                                <div class="box">
                                    <div class="box-header text-center">
                                        <span class="box-title"><small>video指标 /
                                            <input id='videoInput' style="width:17px;" readonly="readonly"/>时
                                        <?php 
                                            // $dsn = "mysql:host=localhost;dbname=nbi"; 
                                            // $db = new PDO($dsn, 'root', 'mongs');
                                            // $sql =  "SELECT HourId from EutranCellTddQuarter GROUP BY DateId,HourId ORDER by DateId DESC, HourId DESC LIMIT 1";
                                            // $res = $db->query($sql);
                                            // $row = $res->fetch();
                                            // print_r($row['HourId'].'时');
                                        ?>
                                        </small></span>
                                    </div>
                                    <div class="box-body">
                                        <div id="video_kpigroup"></div>
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
                <div class="box-header with-border">
                    <h3 class="box-title">关键三项指标</h3>

                    <div class="box-tools pull-right">
                        <button  type="button" class="btn btn-box-tool" title="Y轴设置" data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-pencil" aria-hidden="true"></i></button>
                        <button id="rank_threeKeys" type="button" class="btn btn-box-tool" data-toggle="tooltip" title="排名"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
                        <button id="trend_threeKeys" type="button" class="btn btn-box-tool" data-toggle="tooltip" title="趋势"><i class="fa fa-line-chart" aria-hidden="true"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                    <!-- 模态框 -->
                    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog" style="width:450px">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                        &times;
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">
                                        设置Y轴最大/最小值
                                    </h4>
                                </div>
                                <div class="modal-body">
                                    minY:<input type="number" id="dataFrom" text-align='right' value='0' onchange='dataFrom()'/>
                                    <input type="number" id="dataFrom1" text-align='right' value='0' onchange='dataFrom1()' style="display:none"/>&nbsp;&nbsp; 
                                    maxY:<input type="number" id="dataTo" text-align='right' value='100' onchange="dataTo()" />
                                    <input type="number" id="dataTo1" text-align='right' value='100' onchange="dataTo1()" style='display: none' />
                                </div>
                                <div class="modal-footer">
                                    <!-- <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                                    </button> -->
                                    <button id='buttonClick' type="button" class="btn btn-primary" data-dismiss="modal" onclick="buttonClick()">
                                        提交更改
                                    </button>
                                </div>
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal -->
                    </div>
                    <!-- 模态框 -->
                </div>
                <!-- /.box-header -->
                <!-- <div class="box-body"> -->
                    <!-- <div class="row"> -->
                        <!-- <div class="col-md-12"> -->
                            <!-- <div class="box"> -->
                                <!-- <div class="box-body" style="position:relative"> -->
                        <!-- <ul class="nav nav-tabs pull-left"> -->
                                <!-- <li class="active"><a href="#chart-access" onclick="lowAccess()">无线接通率</a></li> -->
                                <!-- <div class="tab-content"> -->
                                <!-- <div class="chart tab-pane active" id="chart-access" style="position: relative;height: 400px;"></div> -->
                            <!-- </div> -->
                    <!-- </div> -->

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">无线接通率</h3>
                                </div>
                                <!-- <div class="nav-tabs-custom">
                                <div class="tab-content"> -->
                                <div class="chart tab-pane active" id="chart-access" style="position: relative;height: 400px;"></div>
                            </div>
                         </div>
                    </div>
                </div>    

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">无线掉线率</h3>
                                </div>
                                <!-- <div class="nav-tabs-custom">
                                <div class="tab-content"> -->
                                <div class="chart tab-pane active" id="chart-lost" style="position: relative;height: 400px;"></div>
                            </div>
                         </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">切换成功率</h3>
                                </div>
                                <!-- <div class="nav-tabs-custom">
                                <div class="tab-content"> -->
                                <div class="chart tab-pane active" id="chart-handover" style="position: relative;height: 400px;"></div>
                            </div>   
                        
                        </div>
                    </div>
                <!-- ./box-body -->
                </div>
            <!-- /.box -->
            </div>

        </div>
        <!-- /.col -->
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">VoLTE指标</h3>
                    <div class="box-tools pull-right">
                        <button id="rank_volte" type="button" class="btn btn-box-tool" data-toggle="tooltip" title="排名"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
                        <button id="trend_volte" type="button" class="btn btn-box-tool" data-toggle="tooltip" title="趋势"><i class="fa fa-line-chart" aria-hidden="true"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- <div class="box-body">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs pull-left">
                            <li class='active'><a href="#chart_erab_success" data-toggle="tab">无线接通率(QCI=1)</a></li>
                            <li><a href="#chart_wireless_success" data-toggle="tab">VoLTE用户切换成功率</a></li>
                            <li><a href="#chart_erab_lost" data-toggle="tab">E-RAB掉线率(QCI=1)</a> </li>
                            <li><a href="#chart_volte_handover" data-toggle="tab">eSRVCC切换成功率</a> </li>
                        </ul>
                        <div class="tab-content">
                            <div class="chart tab-pane active" id="chart_erab_success" style="position: relative;height: 400px"></div>
                            <div class="chart tab-pane" id="chart_erab_lost" style="position: relative;height: 400px"></div>
                            <div class="chart tab-pane" id="chart_wireless_success" style="position: relative;height: 400px"></div>
                            <div class="chart tab-pane" id="chart_volte_handover" style="position: relative;height: 400px"></div>
                        </div>
                    </div>
                </div> -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">无线接通率(QCI=1)</h3>
                                </div>
                                <!-- <div class="nav-tabs-custom">
                                <div class="tab-content"> -->
                                <div class="chart tab-pane active" id="chart_erab_success" style="position: relative;height: 400px;"></div>
                            </div>
                         </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">VoLTE用户切换成功率</h3>
                                </div>
                                <!-- <div class="nav-tabs-custom">
                                <div class="tab-content"> -->
                                <div class="chart tab-pane active" id="chart_wireless_success" style="position: relative;height: 400px;"></div>
                            </div>
                         </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">E-RAB掉线率(QCI=1)</h3>
                                </div>
                                <!-- <div class="nav-tabs-custom">
                                <div class="tab-content"> -->
                                <div class="chart tab-pane active" id="chart_erab_lost" style="position: relative;height: 400px;"></div>
                            </div>
                         </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">eSRVCC切换成功率</h3>
                                </div>
                                <!-- <div class="nav-tabs-custom">
                                <div class="tab-content"> -->
                                <div class="chart tab-pane active" id="chart_volte_handover" style="position: relative;height: 400px;"></div>
                            </div>
                         </div>
                    </div>
                </div>
            </div>

            <div class="box">    
                <div class="box-header with-border">
                    <h3 class="box-title">Video指标</h3>
                    <div class="box-tools pull-right">
                        <button id="rank_video" type="button" class="btn btn-box-tool" data-toggle="tooltip" title="排名"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
                        <button id="trend_video" type="button" class="btn btn-box-tool" data-toggle="tooltip" title="趋势"><i class="fa fa-line-chart" aria-hidden="true"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- <div class="box-body">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs pull-left">
                            <li class='active'><a href="#chart1_wireless_success" data-toggle="tab">无线接通率(QCI=2)</a></li>
                            <li><a href="#chart1_erab_lost" data-toggle="tab">E-RAB掉线率(QCI=2)</a></li>
                            <li><a href="#chart1_VideoCall_success" data-toggle="tab">VideoCall用户切换成功率</a> </li>
                            <li><a href="#chart1_eSRVCC_handover" data-toggle="tab">eSRVCC切换成功率</a> </li>
                        </ul>
                        <div class="tab-content">
                            <div class="chart tab-pane active" id="chart1_wireless_success" style="position: relative;height: 400px"></div>
                            <div class="chart tab-pane" id="chart1_erab_lost" style="position: relative;height: 400px"></div>
                            <div class="chart tab-pane" id="chart1_VideoCall_success" style="position: relative;height: 400px"></div>
                            <div class="chart tab-pane" id="chart1_eSRVCC_handover" style="position: relative;height: 400px"></div>
                        </div>
                    </div>
                </div> -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">无线接通率(QCI=2)</h3>
                                </div>
                                <!-- <div class="nav-tabs-custom">
                                <div class="tab-content"> -->
                                <div class="chart tab-pane active" id="chart1_wireless_success" style="position: relative;height: 400px;"></div>
                            </div>
                         </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">E-RAB掉线率(QCI=2)</h3>
                                </div>
                                <!-- <div class="nav-tabs-custom">
                                <div class="tab-content"> -->
                                <div class="chart tab-pane active" id="chart1_erab_lost" style="position: relative;height: 400px;"></div>
                            </div>
                         </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">VideoCall用户切换成功率</h3>
                                </div>
                                <!-- <div class="nav-tabs-custom">
                                <div class="tab-content"> -->
                                <div class="chart tab-pane active" id="chart1_VideoCall_success" style="position: relative;height: 400px;"></div>
                            </div>
                         </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">eSRVCC切换成功率</h3>
                                </div>
                                <!-- <div class="nav-tabs-custom">
                                <div class="tab-content"> -->
                                <div class="chart tab-pane active" id="chart1_eSRVCC_handover" style="position: relative;height: 400px;"></div>
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
<style type="text/css"> 
    #threeKeysInput,#volteInput,#videoInput{
       border:0;
    }
</style> 
<script src="plugins/highcharts/js/highstock.js"></script>
<script src="plugins/highcharts/js/highcharts-more.js"></script>
<script src="plugins/highcharts/js/modules/solid-gauge.js"></script>
<!-- <script src="plugins/highcharts/js/highcharts-more.js"></script>
<script src="plugins/highcharts/js/highstock.js"></script>
<script src="plugins/highcharts/js/modules/solid-gauge.js"></script> -->
<!-- <script src="dist/js/genius/network-chart.js"></script> -->
<script src="dist/js/genius/network-chartTable.js"></script>
{{--<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=XtxLWdHvIBw0FKDLBh835SwO"></script>--}}
{{--<script src="plugins/mapv/Mapv.js"></script>--}}
<!-- <script src="plugins/echarts/echarts.min.js"></script>
<script src="plugins/echarts/jiangsu.js"></script> -->
<script src="dist/js/genius/common/download.js"></script>
<script src="dist/js/genius/network-chart.js"></script>
<script src="dist/js/genius/getNowTime.js"></script>
@endsection
