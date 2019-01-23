<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Genius</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- For all the post method -->
    <meta name="csrf-token" content="{!! csrf_token() !!}"/> 
    <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}" /> -->
    <link rel="stylesheet" href="plugins/layui/css/layui.css">
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/font-awesome/css/font-awesome.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="plugins/iCheck/flat/blue.css">
    <!-- Morris chart -->
    <link rel="stylesheet" href="plugins/morris/morris.css">
    <!-- Date Picker -->
    <link rel="stylesheet" href="plugins/datepicker/datepicker3.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker-bs3.css">
    <!-- bootstrap wysihtml5 - text editor -->
    <link type="text/css" rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
    <!-- grid -->
    <link type="text/css" rel="stylesheet" href="plugins/bootstrap-grid/css/grid.css" >
    <!-- datatables -->
     <link rel="stylesheet" href="plugins/datatables/grid.css">
    <!--select2-->
    <link type="text/css" href="plugins/select2/select2.css" rel="stylesheet" />
    <!-- treeview -->
    <link type="text/css" href="plugins/treeview/bootstrap-treeview.min.css" rel="stylesheet"/>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="plugins/html5shiv/html5shiv/3.7.3/"></script>
    <script src="plugins/respond/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="dist/css/nav.css">
</head>
<body class="hold-transition skin-blue sidebar-mini" style="font-family: Arial,Microsoft YaHei,微软雅黑,sans-serif">
<div class="wrapper">
    <header class="main-header header-v6">
        <!-- Logo -->
        <a href="" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>E</b>GS</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>Ericsson</b> Genius</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top mega-menu">
            <!-- Sidebar toggle button-->
            <!-- <a href="" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a> -->
            <div class="collapse navbar-collapse navbar-responsive-collapse">
                <div class="menu-container">
                    <ul class="nav navbar-nav">
                        <!-- 网络概览 -->
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-camera"></i>{{trans('nav.OVERVIEW')}}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <div class="mega-menu-content disable-icons">
                                        <div class="container" style="width:600px;">
                                            <div class="row equal-height">
                                                <div class="col-md-4 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>{{trans('nav.NETWORK_SCALE')}}</h3></li>
                                                        <!-- 规模概览 -->
                                                        @can('scale',[Auth::user()->type,'scale'])
                                                        <li><a href="scale"><i class="fa fa-circle-o"></i> <span>{{trans('nav.NETWORK_SCALE')}}</span></a></li>
                                                        @endcan
                                                        <!-- End 规模概览 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-4 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>{{trans('nav.NETWORK_KPI')}}</h3></li>
                                                        <!-- 指标概览 -->
                                                        @can('network',[Auth::user()->type,'network'])
                                                        <li><a href="network"><i class="fa fa-circle-o"></i> <span>{{trans('nav.NETWORK_KPI')}}</span></a></li>
                                                        @endcan
                                                        <!-- End 指标概览 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-4 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>{{trans('nav.NETWORK_GAP')}}</h3></li>
                                                        <!-- 短板概览 -->
                                                        @can('weak',[Auth::user()->type,'weak'])
                                                        <li><a href="weak"><i class="fa fa-circle-o"></i> <span>{{trans('nav.NETWORK_GAP')}}</span></a></li>
                                                        @endcan
                                                        <!-- End 短板概览 -->
                                                    </ul>
                                                </div>
                                                <!-- <div class="col-md-3 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>{{trans('nav.NETWORK_SIGNALLING')}}</h3></li> -->
                                                        <!-- 信令概览 -->
                                                        <!-- @can('singal',[Auth::user()->type,'singal'])
                                                        <li><a href="singal"><i class="fa fa-circle-o"></i> <span>{{trans('nav.NETWORK_SIGNALLING')}}</span></a></li>
                                                        @endcan -->
                                                        <!-- End 信令概览 -->
                                                    <!-- </ul>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- end 网络概览 -->
                        <!-- 日常优化 -->
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-dashboard"></i>{{trans('nav.DAILY')}}
                            </a>
                            <ul class="dropdown-menu" style="left:-200px;">
                                <li>
                                    <div class="mega-menu-content disable-icons">
                                        <div class="container" style="width:1000px;">
                                            <div class="row equal-height">
                                                <div class="col-md-3 equal-height-in" style="width:20%">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>指标查询</h3></li>
                                                        <!-- 指标分析 -->
                                                        @can('LTEQuery',[Auth::user()->type,'LTEQuery'])
                                                        <li><a href="LTEQuery"><i class="fa fa-circle-o"></i> LTE指标查询</a></li>
                                                        @endcan
                                                        @can('LTEQueryHW',[Auth::user()->type,'LTEQueryHW'])
                                                        <li><a href="LTEQueryHW"><i class="fa fa-circle-o"></i> LTE指标查询(华为)</a></li>
                                                        @endcan
                                                        @can('LTEQueryLocal',[Auth::user()->type,'LTEQueryLocal'])
                                                        <li><a href="LTEQueryLocal"><i class="fa fa-circle-o"></i> LTE指标查询(本地)</a></li>
                                                        @endcan
                                                        @can('NBIQuery',[Auth::user()->type,'NBIQuery'])
                                                        <li><a href="NBIQuery"><i class="fa fa-circle-o"></i> NBI指标查询</a></li>
                                                        @endcan
                                                        @can('GSMQuery',[Auth::user()->type,'GSMQuery'])
                                                        <li><a href="GSMQuery"><i class="fa fa-circle-o"></i> GSM指标查询</a></li>
                                                        @endcan
                                                        @can('CustomQuery',[Auth::user()->type,'CustomQuery'])
                                                        <li><a href="CustomQuery"><i class="fa fa-circle-o"></i> SQL语句查询</a></li>
                                                        @endcan
                                                        @can('packetLossAnalysis',[Auth::user()->type,'packetLossAnalysis'])
                                                        <li><a href="packetLossAnalysis"><i class="fa fa-circle-o"></i> <span>测量指标查询</span></a></li>
                                                        @endcan
                                                        @can('FlowQuery',[Auth::user()->type,'FlowQuery'])
                                                        <li><a href="FlowQuery"><i class="fa fa-circle-o"></i> <span>专项指标查询</span></a></li>
                                                        @endcan
                                       
                                                        <!-- End 指标分析 -->
                                                    </ul>
                                                </div>
                                                  <div class="col-md-3 equal-height-in" style="width:20%">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>重要指标分析</h3></li>
                                                        <!--重要指标分析-->
                                                        @can('LteTopCell',[Auth::user()->type,'LteTopCell'])
                                                        <li><a href="LteTopCell"><i class="fa fa-circle-o"></i> Lte差小区</a><li>
                                                        @endcan
                                                        @can('AppCoverage',[Auth::user()->type,'AppCoverage'])
                                                        <li><a href="AppCoverage"><i class="fa fa-circle-o"></i> 自忙时小区</a><li>
                                                        @endcan
                                                        @can('lowAccessCell',[Auth::user()->type,'lowAccessCell'])
                                                        <li><a href="lowAccessCell"><i class="fa fa-circle-o"></i> 低接入小区</a><li>
                                                        @endcan
                                                        @can('highLostCell',[Auth::user()->type,'highLostCell'])
                                                        <li><a href="highLostCell"><i class="fa fa-circle-o"></i> 高掉线小区</a><li>
                                                        @endcan
                                                        @can('badHandoverCell',[Auth::user()->type,'badHandoverCell'])
                                                        <li><a href="badHandoverCell"><i class="fa fa-circle-o"></i> 切换差小区</a><li>
                                                        @endcan
                                                        @can('highInterferenceCell',[Auth::user()->type,'highInterferenceCell'])
                                                        <li><a href="highInterferenceCell"><i class="fa fa-circle-o"></i> 高干扰小区</a><li>
                                                        @endcan
                                                        @can('historyCellSearch',[Auth::user()->type,'historyCellSearch'])
                                                        <li><a href="historyCellSearch"><i class="fa fa-circle-o"></i> 历史小区查询</a><li>
                                                        @endcan
                                                        @can('extremeHighTrafficCell',[Auth::user()->type,'extremeHighTrafficCell'])
                                                        <li><a href="extremeHighTrafficCell"><i class="fa fa-circle-o"></i> 极端高话务小区</a><li>
                                                        @endcan
                                                        @can('volteupbadcell',[Auth::user()->type,'volteupbadcell'])
                                                        <li><a href="volteupbadcell"><i class="fa fa-circle-o"></i> VOLTE上行丢包差小区</a></li>
                                                        @endcan
                                                        @can('voltedownbadcell',[Auth::user()->type,'voltedownbadcell'])
                                                        <li><a href="voltedownbadcell"><i class="fa fa-circle-o"></i> VOLTE下行丢包差小区</a></li>
                                                        @endcan
                                                        @can('srvccbadcell',[Auth::user()->type,'srvccbadcell'])
                                                        <li><a href="srvccbadcell"><i class="fa fa-circle-o"></i> SRVCC差小区</a></li>
                                                        @endcan
                                                      
                                                        <!-- End 差小区分析 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-3 equal-height-in" style="width:20%">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>参数分析</h3></li>
                                                        <!-- 参数分析 -->
                                                        @can('paramQuery',[Auth::user()->type,'paramQuery'])
                                                        <li><a href="paramQuery"><i class="fa fa-circle-o"></i> 参数查询</a><li>
                                                        @endcan
                                                        @can('KgetG2',[Auth::user()->type,'KgetG2'])
                                                        <li><a href="KgetG2"><i class="fa fa-circle-o"></i> 参数查询(Detail)</a><li>
                                                        @endcan
                                                        @can('paramDistribution',[Auth::user()->type,'paramDistribution'])
                                                        <li><a href="paramDistribution"><i class="fa fa-circle-o"></i> 参数分布</a><li>
                                                        @endcan
                                                        @can('consistencyCheck',[Auth::user()->type,'consistencyCheck'])
                                                        <li><a href="consistencyCheck"><i class="fa fa-circle-o"></i> 一致性检查</a><li>
                                                        @endcan
                                                        @can('baselineCheck',[Auth::user()->type,'baselineCheck'])
                                                        <li><a href="baselineCheck"><i class="fa fa-circle-o"></i> Baseline检查</a><li>
                                                        @endcan
                                                        @can('PCIMOD3Analysis',[Auth::user()->type,'PCIMOD3Analysis'])
                                                        <li><a href="PCIMOD3Analysis"><i class="fa fa-circle-o"></i> PCI同模检查</a></li>
                                                        @endcan
                                                        @can('scCheck',[Auth::user()->type,'scCheck'])
                                                        <li><a href="scCheck"><i class="fa fa-circle-o"></i> 系统常量检查</a><li>
                                                        @endcan
                                                        @can('BasicDataCheck',[Auth::user()->type,'BasicDataCheck'])
                                                        <li><a href="BasicDataCheck"><i class="fa fa-circle-o"></i> 基础数据检查</a><li>
                                                        @endcan
                                                        @can('SQLQuery',[Auth::user()->type,'SQLQuery'])
                                                        <li><a href="SQLQuery"><i class="fa fa-circle-o"></i> SQL语句查询</a><li>
                                                        @endcan
                                                        <!-- 参数对比 -->
                                                        @can('paramCompare',[Auth::user()->type,'paramCompare'])
                                                        <li><a href="paramCompare"><i class="fa fa-circle-o"></i> 参数对比</a><li>
                                                        @endcan
                                                        @can('ReduantData',[Auth::user()->type,'ReduantData'])
                                                        <li><a href="ReduantData"><i class="fa fa-circle-o"></i> 冗余数据清除</a><li>
                                                        @endcan
                                                        <!-- End 参数对比 -->
                                                        <!-- License分析 -->
                                                        @can('licenseAnalysis',[Auth::user()->type,'licenseAnalysis'])
                                                        <li><a href="licenseAnalysis"><i class="fa fa-circle-o"></i> License分析</a><li>
                                                        @endcan
                                                        <!-- License分析 -->
                                                        <!-- End 参数分析 -->
                                                    </ul>
                                                </div>
                                              
                                                <div class="col-md-3 equal-height-in" style="width:20%">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>告警分析</h3></li>
                                                        <!-- 告警分析 -->
                                                        @can('LteAlarmQuery',[Auth::user()->type,'LteAlarmQuery'])
                                                        <li><a href="LteAlarmQuery"><i class="fa fa-circle-o"></i> LTE告警分析</a></li>
                                                        @endcan
                                                        @can('NBAlarmQuery',[Auth::user()->type,'NBAlarmQuery'])
                                                        <li><a href="NBAlarmQuery"><i class="fa fa-circle-o"></i> NB告警分析</a></li>
                                                        @endcan
                                                        @can('GSMAlarmQuery',[Auth::user()->type,'GSMAlarmQuery'])
                                                        <li><a href="GSMAlarmQuery"><i class="fa fa-circle-o"></i> GSM告警分析</a></li>
                                                        @endcan
                                                     
                                                        <!-- End 告警分析 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-3 equal-height-in" style="width:20%">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>原因值分析</h3></li>
                                                        <!-- L3原因值分析 -->
                                                        @can('L3Analysis',[Auth::user()->type,'L3Analysis'])
                                                        <li><a href="L3Analysis"><i class="fa fa-circle-o"></i> <span>NAS原因值分析</span></a></li>
                                                        @endcan
                                                        @can('ENBAnalysis',[Auth::user()->type,'ENBAnalysis'])
                                                        <li><a href="ENBAnalysis"><i class="fa fa-circle-o"></i> <span>ENB原因值分析</span></a></li>
                                                        @endcan
                                                        @can('CauseValueAnalysis',[Auth::user()->type,'CauseValueAnalysis'])
                                                        <li><a href="CauseValueAnalysis"><i class="fa fa-circle-o"></i> <span>ESRVCC原因值分析</span></a></li>
                                                        @endcan
                                                        <!-- End L3原因值分析 -->
                                                    </ul>
                                                </div>
                                               
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- end 日常优化 -->
                        <!-- 专项研究 -->
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-rocket"></i>{{trans('nav.SPECIAL')}}
                            </a>
                            <ul class="dropdown-menu" style="left:-200px;">
                                <li>
                                    <div class="mega-menu-content disable-icons">
                                        <div class="container" style="width:1200px;">
                                            <div class="row equal-height">
                                                <div class="col-md-2 equal-height-in" style="width:20%">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>{{trans('nav.SIGNALLING_ANALYSIS')}}</h3></li>
                                                        <!-- CTR信令分析 -->
                                                        @can('signalingBacktracking',[Auth::user()->type,'signalingBacktracking'])
                                                        <li><a href="signalingBacktracking"><i class="fa fa-circle-o"></i> <span>{{trans('nav.CTR_SIGNALLING_ANALYSIS')}}</span></a></li>
                                                        <!-- End CTR信令分析 -->
                                                        <!-- Volte信令分析 -->
                                                        @endcan
                                                        @can('signalingAnalysis',[Auth::user()->type,'signalingAnalysis'])
                                                        <li><a href="signalingAnalysis"><i class="fa fa-circle-o"></i> <span>{{trans('nav.VOLTE_SIGNALLING_ALALYSIS')}}</span></a></li>
                                                        <!-- End Volte信令分析 -->
                                                        <!-- ENB原因值分析 -->
                                                        @endcan
                                                        @can('failureAnalysis',[Auth::user()->type,'failureAnalysis'])
                                                        <li><a href="failureAnalysis"><i class="fa fa-circle-o"></i> <span>{{trans('nav.ENB_ROOT_CAUSE_ANALYSIS')}}</span></a></li>
                                                        <!-- End ENB原因值分析 -->
                                                        <!-- CTR信令搜索 -->
                                                        @endcan
                                                        @can('ctrSignalingAnalysis',[Auth::user()->type,'ctrSignalingAnalysis'])
                                                        <li><a href="ctrSignalingAnalysis"><i class="fa fa-circle-o"></i> <span>{{trans('nav.CTR_SIGNALLING_SEARCH')}}</span></a></li>
                                                        @endcan
                                                        <!-- End CTR信令搜索 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-2 equal-height-in" style="width:20%">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>{{trans('nav.RELATION_ANALYSIS')}}</h3></li>
                                                        @can('relationNonHandover',[Auth::user()->type,'relationNonHandover'])
                                                        <li><a href="relationNonHandover"><i class="fa fa-circle-o"></i> <span>{{trans('nav.NON_HANDOVER_RELATION')}}</span></a></li>
                                                        @endcan
                                                        @can('relationBadHandover',[Auth::user()->type,'relationBadHandover'])
                                                        <li><a href="relationBadHandover"><i class="fa fa-circle-o"></i> <span>{{trans('nav.BAD_HANDOVER_RELATION')}}</span></a></li>
                                                        @endcan
                                                        @can('GSMNeighborAnalysis',[Auth::user()->type,'GSMNeighborAnalysis'])
                                                        <li><a href="GSMNeighborAnalysis"><i class="fa fa-circle-o"></i> <span>{{trans('nav.INTER_SYSTEM_RELATION')}}</span></a></li>
                                                        @endcan
                                                        @can('LTENeighborAnalysis',[Auth::user()->type,'LTENeighborAnalysis'])
                                                        <li><a href="LTENeighborAnalysis"><i class="fa fa-circle-o"></i> <span>补4G异频邻区分析</span></a></li>
                                                        @endcan
                                                        @can('MROServeNeighAnalysis',[Auth::user()->type,'MROServeNeighAnalysis'])
                                                        <li><a href="MROServeNeighAnalysis"><i class="fa fa-circle-o"></i> <span>补4G同频邻区分析</span></a></li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                                <div class="col-md-2 equal-height-in" style="width:20%">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>{{trans('nav.WEEK_COVERAGE_ANALYSIS')}}</h3></li>
                                                        <!-- 弱覆盖分析 -->
                                                        @can('weakCoverRatio',[Auth::user()->type,'weakCoverRatio'])
                                                        <li><a href="weakCoverRatio"><i class="fa fa-circle-o"></i> <span>{{trans('nav.WEEK_COVERAGE_OVERVIEW')}}</span></a></li>
                                                        @endcan
                                                        @can('weakCover',[Auth::user()->type,'weakCover'])
                                                        <li><a href="weakCover"><i class="fa fa-circle-o"></i> <span>{{trans('nav.WEEK_COVERAGE_MAP')}}</span></a></li>
                                                        @endcan
                                                        @can('weakCoverRate',[Auth::user()->type,'weakCoverRate'])
                                                        <li><a href="weakCoverRate"><i class="fa fa-circle-o"></i> <span>{{trans('nav.WEEK_COVERAGE_CELL')}}</span></a></li>
                                                        @endcan
                                                        @can('weakCoverCloud',[Auth::user()->type,'weakCoverCloud'])
                                                        <li><a href="weakCoverCloud"><i class="fa fa-circle-o"></i> <span>{{trans('nav.WEEK_COVERAGE_CLOUD')}}</span></a></li>
                                                        @endcan
                                                        @can('RSRPAnalysis',[Auth::user()->type,'RSRPAnalysis'])
                                                        <li><a href="RSRPAnalysis"><i class="fa fa-circle-o"></i> <span>{{trans('nav.CELL_RSRP_ANALYSIS')}}</span></a></li>
                                                        @endcan
                                                        <!-- End 弱覆盖分析 -->
                                                    </ul>
                                                </div>
                                                 <div class="col-md-2 equal-height-in" style="width:20%">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>{{trans('nav.OVERLAP_COVERAGE_ANALYSIS')}}</h3></li>
                                                        <!-- 重叠覆盖分析 -->
                                                        @can('overlapCoverOverview',[Auth::user()->type,'overlapCoverOverview'])
                                                        <li><a href="overlapCoverOverview"><i class="fa fa-circle-o"></i> <span>{{trans('nav.OVERLAP_COVERAGE_OVERVIEW')}}</span></a></li>
                                                        @endcan
                                                        @can('overlapCover',[Auth::user()->type,'overlapCover'])
                                                        <li><a href="overlapCover"><i class="fa fa-circle-o"></i> <span>{{trans('nav.OVERLAP_ACCEPTOR_CELL')}}</span></a></li>
                                                        @endcan
                                                        @can('areaCoverage',[Auth::user()->type,'areaCoverage'])
                                                        <li><a href="areaCoverage"><i class="fa fa-circle-o"></i> <span>{{trans('nav.OVERLAP_DONOR_CELL')}}</span></a></li>
                                                        @endcan
                                                        @can('overlapCoverPoint',[Auth::user()->type,'overlapCoverPoint'])
                                                        <li><a href="overlapCoverPoint"><i class="fa fa-circle-o"></i> <span>{{trans('nav.OVERLAP_COVERAGE_MAP')}}</span></a></li>
                                                        @endcan
                                                    {{--     @can('overlappingAcceptorAnalysis',[Auth::user()->type,'overlappingAcceptorAnalysis'])
                                                        <li><a href="overlappingAcceptorAnalysis"><i class="fa fa-circle-o"></i> <span>{{trans('nav.OVERLAP_COVERAGE_RELATION')}}</span></a></li>
                                                        @endcan
                                                        @can('overlappingDonorAnalysis',[Auth::user()->type,'overlappingDonorAnalysis'])
                                                        <li><a href="overlappingDonorAnalysis"><i class="fa fa-circle-o"></i> <span>{{trans('nav.OVERLAP_COVERAGE_RELATION')}}</span></a></li>
                                                        @endcan --}}
                                                          @can('overlappingAcceptorAnalysis',[Auth::user()->type,'overlappingAcceptorAnalysis'])
                                                        <li><a href="overlappingAcceptorAnalysis"><i class="fa fa-circle-o"></i> <span>重叠覆盖受主分析</span></a></li>
                                                        @endcan
                                                        @can('overlappingDonorAnalysis',[Auth::user()->type,'overlappingDonorAnalysis'])
                                                        <li><a href="overlappingDonorAnalysis"><i class="fa fa-circle-o"></i> <span>重叠覆盖施主分析</span></a></li>
                                                        @endcan
                                                        <!-- End 重叠覆盖分析 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-2 equal-height-in" style="width:20%">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>{{trans('nav.INTERFERENCE_ANALYSIS')}}</h3></li>
                                                        <!-- 高干扰分析 -->
                                                        @can('interCoverRatio',[Auth::user()->type,'interCoverRatio'])
                                                        <li><a href="interCoverRatio"><i class="fa fa-circle-o"></i> <span>{{trans('nav.INTERFERENCE_OVERVIEW')}}</span></a></li>
                                                        @endcan
                                                        @can('interCloud',[Auth::user()->type,'interCloud'])
                                                        <li><a href="interCloud"><i class="fa fa-circle-o"></i> <span>{{trans('nav.INTERFERENCE_CLOUD')}}</span></a></li>
                                                        @endcan
                                                        @can('interPointCloud',[Auth::user()->type,'interPointCloud'])
                                                        <li><a href="interPointCloud"><i class="fa fa-circle-o"></i> <span>{{trans('nav.INTERFERENCE_MAP')}}</span></a></li>
                                                        @endcan
                                                        @can('cellPRBAnalysis',[Auth::user()->type,'cellPRBAnalysis'])
                                                        <li><a href="cellPRBAnalysis"><i class="fa fa-circle-o"></i> <span>{{trans('nav.PRB_ANALYSIS')}}</span></a></li>
                                                        @endcan
                                                        @can('RealTimeInterference',[Auth::user()->type,'RealTimeInterference'])
                                                        <li><a href="RealTimeInterference"><i class="fa fa-circle-o"></i> <span>{{trans('nav.REAL_TIME_INTERFERENCE')}}</span></a></li>
                                                        @endcan
                                                        @can('frameDifferencePoint',[Auth::user()->type,'frameDifferencePoint'])
                                                        <li><a href="frameDifferencePoint"><i class="fa fa-circle-o"></i> <span>子帧差异点图</span></a></li>
                                                        @endcan
                                                        <!-- End 高干扰分析 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-3 equal-height-in" style="width:20%">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>硬件分析</h3></li>
                                                        <!-- 硬件分析 -->
                                                       @can('BoardAnalysis',[Auth::user()->type,'BoardAnalysis'])
                                                        <li><a href="BoardAnalysis"><i class="fa fa-circle-o"></i> 板卡分析</a></li>
                                                        @endcan
                                                        @can('RRU',[Auth::user()->type,'RRU'])
                                                        <li><a href="RRU"><i class="fa fa-circle-o"></i> 扩容分析</a><li>
                                                        @endcan
                                                        @can('DOT',[Auth::user()->type,'DOT'])
                                                        <li><a href="DOT"><i class="fa fa-circle-o"></i> 新型室分(DOT)站点分析</a><li>
                                                        @endcan
                                                        @can('GSMSlot',[Auth::user()->type,'GSMSlot'])
                                                        <li><a href="GSMSlot"><i class="fa fa-circle-o"></i> GSM板卡串号统计</a><li>
                                                        @endcan
                                                        @can('workingParameterDataAnalysis',[Auth::user()->type,'workingParameterDataAnalysis'])
                                                        <li><a href="workingParameterDataAnalysis"><i class="fa fa-circle-o"></i> 工参数据分析</a><li>
                                                        @endcan
                                                        @can('TopTraffic',[Auth::user()->type,'TopTraffic '])
                                                        <li><a href="TopTraffic"><i class="fa fa-circle-o"></i> 小区自忙时分析</a><li>
                                                        @endcan
                                                        @can('RRUHardwear',[Auth::user()->type,'RRUHardwear'])
                                                        <li><a href="RRUHardwear"><i class="fa fa-circle-o"></i> RRU硬件能力查询</a></li>
                                                        @endcan
                                                        <!-- End 硬件分析 -->
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- end 专项研究 -->
                             <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bars"></i>网络规划
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <div class="mega-menu-content disable-icons">
                                        <div class="container" style="width:200px;">
                                            <div class="row equal-height">
                                                <div class="col-md-12 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3></h3></li>
                                                        <!-- 网络规划 -->
                                                   
                                                        @can('WilliamTool',[Auth::user()->type,'WilliamTool'])
                                                        <li><a href="WilliamTool"><i class="fa fa-circle-o"></i> WilliamTool对接</a><li>
                                                        @endcan
                                                        <!-- End 网络规划 -->
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- end 网络规划 -->
                        <!-- 投诉处理 -->
                        <li class="dropdown hidden">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-phone-square"></i>投诉处理
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <div class="mega-menu-content disable-icons">
                                        <div class="container" style="width:400px;">
                                            <div class="row equal-height">
                                                <div class="col-md-6 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>信令诊断</h3></li>
                                                         <!-- 信令诊断 -->
                                                        @can('signalingDiagnose',[Auth::user()->type,'signalingDiagnose'])
                                                        <li><a href="signalingDiagnose"><i class="fa fa-circle-o"></i> <span>信令诊断</span></a></li>
                                                        @endcan
                                                        <!-- End 信令诊断 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-6 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>实时监控</h3></li>
                                                        <!-- 实时监控 -->
                                                        <!-- @can('xinlinghuisu',[Auth::user()->type,'xinlinghuisu'])
                                                        <li><a href="xinlinghuisu"><i class="fa fa-circle-o"></i> <span>CTR信令回溯</span></a></li>
                                                        @endcan -->
                                                        @can('NASSignalingBacktrack',[Auth::user()->type,'NASSignalingBacktrack'])
                                                        <li><a href="NASSignalingBacktrack"><i class="fa fa-circle-o"></i> <span>NAS信令回溯</span></a></li>
                                                        @endcan
                                                        <!-- End 实时监控 -->
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- end 投诉处理 -->
                        <!-- 用户分析 -->
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-user"></i>{{trans('nav.TERMINAL')}}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <div class="mega-menu-content disable-icons">
                                        @can('adminOnly',Auth::user()->type)
                                        <div class="container" style="width:600px;">
                                        @else
                                        <div class="container" style="width:400px;">
                                        @endcan
                                            <div class="row equal-height">
                                                @can('adminOnly',Auth::user()->type)
                                                <div class="col-md-4 equal-height-in">
                                                @else
                                                <div class="col-md-6 equal-height-in">
                                                @endcan
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>{{trans('nav.TERMINAL')}}</h3></li>
                                                        <!-- 终端分析 -->
                                                        @can('terminalQuery',[Auth::user()->type,'terminalQuery'])
                                                        <li><a href="terminalQuery"><i class="fa fa-circle-o"></i> <span>{{trans('nav.TERMINAL_QUERY')}}</span></a></li>
                                                        @endcan
                                                        @can('marketAnalysis',[Auth::user()->type,'marketAnalysis'])
                                                        <li><a href="marketAnalysis"><i class="fa fa-circle-o"></i> <span>{{trans('nav.MARKET_ANALYSIS')}}</span></a></li>
                                                        @endcan
                                                        @can('abilityAnalysis',[Auth::user()->type,'abilityAnalysis'])
                                                        <li><a href="abilityAnalysis"><i class="fa fa-circle-o"></i> <span>{{trans('nav.CAPABILITY_ANALYSIS')}}</span></a></li>
                                                        @endcan
                                                        <!-- End 终端分析 -->
                                                    </ul>
                                                </div>
                                                @can('adminOnly',Auth::user()->type)
                                                <div class="col-md-4 equal-height-in">
                                                @else
                                                <div class="col-md-6 equal-height-in">
                                                @endcan
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>{{trans('nav.SIGNALLING_RECALL')}}</h3></li>
                                                        <!-- 信令回溯 -->
                                                        @can('NASSignalingBacktrack',[Auth::user()->type,'NASSignalingBacktrack'])
                                                        <li><a href="NASSignalingBacktrack"><i class="fa fa-circle-o"></i> <span>{{trans('nav.NAS_SIGNALLING')}}</span></a></li>
                                                        @endcan
                                                        <!-- End 信令回溯 -->
                                                    </ul>
                                                </div>
                                                @can('adminOnly',Auth::user()->type)
                                                <div class="col-md-6 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>{{trans('nav.LOCATION_ANALYSIS')}}</h3></li>
                                                        <!-- 位置分析 -->
                                                        @can('trailQuery',[Auth::user()->type,'trailQuery'])
                                                        <li><a href="trailQuery"><i class="fa fa-circle-o"></i> <span>{{trans('nav.TRACE_QUERY')}}</span></a></li>
                                                        @endcan
                                                        @can('trailQueryManual',[Auth::user()->type,'trailQueryManual'])
                                                        <li><a href="trailQueryManual"><i class="fa fa-circle-o"></i> <span>轨迹查询(手动入库)</span></a></li>
                                                        @endcan
                                                        <!-- End 位置分析 -->
                                                    </ul>
                                                </div>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- end 用户分析 -->
                        <!-- 地理呈现 -->
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-map"></i>{{trans('nav.MAP')}}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <div class="mega-menu-content disable-icons">
                                        <div class="container" style="width:800px;">
                                            <div class="row equal-height">
                                                <div class="col-md-3 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>路测分析</h3></li>
                                                        <!-- 路测分析 -->
                                                        @can('locationAndRanging',[Auth::user()->type,'locationAndRanging'])
                                                        <li><a href="locationAndRanging"><i class="fa fa-circle-o"></i> 定位测距</a></li>
                                                        @endcan
                                                        @can('autoRoadSurvey',[Auth::user()->type,'autoRoadSurvey'])
                                                        <li><a href="autoRoadSurvey"><i class="fa fa-circle-o"></i> 自动路测</a></li>
                                                        @endcan
                                                        <!-- End 路测分析 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-3 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>邻区分析</h3></li>
                                                        <!-- 邻区分析 -->
                                                        @can('switchdefine',[Auth::user()->type,'switchdefine'])
                                                        <li><a href="switchdefine"><i class="fa fa-circle-o"></i> <span>邻区定义分析</span></a></li>
                                                        @endcan
                                                        @can('switch',[Auth::user()->type,'switch'])
                                                        <li><a href="switch"><i class="fa fa-circle-o"></i> <span>邻区切出分析</span></a></li>
                                                        @endcan
                                                        @can('switchIn',[Auth::user()->type,'switchIn'])
                                                        <li><a href="switchIn"><i class="fa fa-circle-o"></i> <span>邻区切入分析</span></a></li>
                                                        @endcan
                                                        <!-- End 邻区分析 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-3 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>小区分析</h3></li>
                                                        <!-- 高负荷分析 -->
                                                        @can('RRCUserCloud',[Auth::user()->type,'RRCUserCloud'])
                                                        <li><a href="RRCUserCloud"><i class="fa fa-circle-o"></i> <span>RRC用户数点图</span></a></li>
                                                        @endcan
                                                        @can('downlinkTrafficCloud',[Auth::user()->type,'downlinkTrafficCloud'])
                                                        <li><a href="downlinkTrafficCloud"><i class="fa fa-circle-o"></i> <span>下行业务量点图</span></a></li>
                                                        @endcan
                                                        <!-- End 高负荷分析 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-3 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>覆盖分析</h3></li>
                                                        <!-- 覆盖分析 -->
                                                        @can('overlayRaster',[Auth::user()->type,'overlayRaster'])
                                                        <li><a href="overlayRaster"><i class="fa fa-circle-o"></i> <span>覆盖栅格图</span></a></li>
                                                        @endcan
                                                        <!-- End 覆盖分析 -->
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- end 地理呈现 -->
                        <!-- 系统管理 -->
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-cogs"></i>{{trans('nav.SYSTEM')}}
                            </a>
                            @can('adminOnly',Auth::user()->type)
                            <ul class="dropdown-menu">
                            @else
                            <ul class="dropdown-menu">
                            @endcan
                                <li>
                                    <div class="mega-menu-content disable-icons">
                                        @can('adminOnly',Auth::user()->type)
                                        <div class="container" style="width:600px;">
                                        @else
                                        <div class="container" style="width:200px;">
                                        @endcan
                                            <div class="row equal-height">
                                                @can('adminOnly',Auth::user()->type)
                                                <div class="col-md-4 equal-height-in">
                                                @else
                                                <div class="col-md-12 equal-height-in">
                                                @endcan
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>数据管理</h3></li>
                                                        <!-- 数据管理 -->
                                                        @can('siteManage',[Auth::user()->type,'siteManage'])
                                                        <li><a href="siteManage"><i class="fa fa-circle-o"></i>站点管理</a></li>
                                                        @endcan
                                                        @can('alarmManage',[Auth::user()->type,'alarmManage'])
                                                           <li><a href="alarmManage"><i class="fa fa-circle-o"></i>告警管理</a></li>
                                                        @endcan
                                                        @can('storageManage',[Auth::user()->type,'storageManage'])
                                                        <li><a href="storageManage"><i class="fa fa-circle-o"></i>入库管理</a></li>
                                                        @endcan
                                                        @can('paramsManage',[Auth::user()->type,'paramsManage'])
                                                        <li><a href="paramsManage"><i class="fa fa-circle-o"></i>参数管理</a></li>
                                                        @endcan
                                                        @can('dataSourceManage',[Auth::user()->type,'dataSourceManage'])
                                                        <li><a href="dataSourceManage"><i class="fa fa-circle-o"></i>数据下载</a></li>
                                                        @endcan
                                                        @can('LocalDataManage',[Auth::user()->type,'LocalDataManage'])
                                                        <li><a href="LocalDataManage"><i class="fa fa-circle-o"></i>数据上传</a></li>
                                                        @endcan
                                                        @can('TemplateManage',[Auth::user()->type,'TemplateManage'])
                                                        <li><a href="TemplateManage"><i class="fa fa-circle-o"></i>模板管理</a></li>
                                                        @endcan
                                                        @can('trailQueryDataManage',[Auth::user()->type,'trailQueryDataManage'])
                                                        <li><a href="trailQueryDataManage"><i class="fa fa-circle-o"></i>轨迹数据管理</a></li>
                                                        @endcan
                                                        <!-- End 数据管理 -->
                                                    </ul>
                                                </div>
                                                @can('adminOnly',Auth::user()->type)
                                                <div class="col-md-4 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>权限管理</h3></li>
                                                        <!-- 权限管理 -->
                                                        @can('userManage',[Auth::user()->type,'userManage'])
                                                        <li><a href="userManage"><i class="fa fa-circle-o"></i>账户管理</a></li>
                                                        @endcan
                                                        @can('emailManage',[Auth::user()->type,'emailManage'])
                                                        <li><a href="emailManage"><i class="fa fa-circle-o"></i>邮箱管理</a></li>
                                                        @endcan
                                                        @can('ENIQManage',[Auth::user()->type,'ENIQManage'])
                                                        <li><a href="ENIQManage"><i class="fa fa-circle-o"></i>直连管理</a></li>
                                                        @endcan
                                                        @can('downloadManage',[Auth::user()->type,'downloadManage'])
                                                        <li><a href="downloadManage"><i class="fa fa-circle-o"></i>下载管理</a></li>
                                                        @endcan
                                                        @can('storeManage',[Auth::user()->type,'storeManage'])
                                                        <li><a href="storeManage"><i class="fa fa-circle-o"></i>存储管理</a></li>
                                                        @endcan
                                                        <!-- <li><a href="taskManage"><i class="fa fa-circle-o"></i>任务管理</a></li> -->
                                                        <!-- End 权限管理 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-4 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>功能管理</h3></li>
                                                        <!-- 功能管理 -->
                                                        @can('noticeManage',[Auth::user()->type,'noticeManage'])
                                                        <li><a href="noticeManage"><i class="fa fa-circle-o"></i>通知管理</a></li>
                                                        @endcan
                                                        @can('accessManage',[Auth::user()->type,'accessManage'])
                                                        <li><a href="accessManage"><i class="fa fa-circle-o"></i>点击管理</a></li>
                                                        @endcan
                                                        @can('activeUser',[Auth::user()->type,'activeUser'])
                                                        <li><a href="activeUser"><i class="fa fa-circle-o"></i>日活用户</a></li>
                                                        @endcan
                                                        <!-- End 功能管理 -->
                                                    </ul>
                                                </div>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- end 系统管理 -->
                        <!-- 专项工具 -->
                        <li class="dropdown hidden">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-wrench"></i>专项工具
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <div class="mega-menu-content disable-icons">
                                        <div class="container" style="width:200px;">
                                            <div class="row equal-height">
                                                <div class="col-md-12 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <!-- <li><h3>专项工具</h3></li> -->
                                                        <!-- 专项工具 -->
                                                        <li><a href="http://192.168.3.220:9120/" target="_blank"><i class="fa fa-circle-o"></i>XNOW</a></li>
                                                        <li><a href="http://59.110.71.40:8080/" target="_blank"><i class="fa fa-circle-o"></i>LROSE</a></li>
                                                        <!-- End 专项工具 -->
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!-- end 专项工具 -->
                        <!-- 特色功能 -->
                        @can('features',[Auth::user()->type,Auth::user()->province,Auth::user()->operator])
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-thumbs-up feature"></i>{{trans('nav.CUSTOMIZATION')}}
                            </a>
                            <ul class="dropdown-menu" style="left:500px;">
                                <li>
                                    <div class="mega-menu-content disable-icons">
                                        <div class="container" style="width:600px;">
                                            <div class="row equal-height">
                                                <div class="col-md-4 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>留痕</h3></li>
                                                        <!-- 功能管理 -->
                                                        @can('bulkcmMark',[Auth::user()->type,'bulkcmMark'])
                                                        <li><a href="bulkcmMark"><i class="fa fa-circle-o"></i> Bulkcm留痕</a><li>
                                                        @endcan
                                                        @can('kgetpartMark',[Auth::user()->type,'kgetpartMark'])
                                                        <li><a href="kgetpartMark"><i class="fa fa-circle-o"></i> Kgetpart留痕</a><li>
                                                        @endcan
                                                        <!-- End 功能管理 -->
                                                    </ul>
                                                </div>
                                                <div class="col-md-4 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>参数</h3></li>
                                                        <!-- 常用参数查询 -->
                                                        @can('StrideMOQuery',[Auth::user()->type,'StrideMOQuery'])
                                                        <li><a href="StrideMOQuery"><i class="fa fa-circle-o"></i> 常用参数查询</a><li>
                                                        @endcan
                                                        <!-- End 常用参数查询 -->
                                                        <!-- 操作查询 -->
                                                        @can('operationQuery',[Auth::user()->type,'operationQuery'])
                                                        <li><a href="operationQuery"><i class="fa fa-circle-o"></i> 操作查询</a><li>
                                                        @endcan
                                                        <!-- End 操作查询 -->
                                                        <!-- 强干扰小区处理 -->
                                                        @can('strongInterferenceCell',[Auth::user()->type,'strongInterferenceCell'])
                                                        <li><a href="strongInterferenceCell"><i class="fa fa-circle-o"></i> 强干扰小区处理</a><li>
                                                        @endcan
                                                        <!-- End 强干扰小区处理 -->
                                                        @can('newSite',[Auth::user()->type,'newSite'])
                                                        <li><a href="newSite"><i class="fa fa-circle-o"></i> 新站跟踪</a><li>
                                                        @endcan
                                                        @can('oldSite',[Auth::user()->type,'oldSite'])
                                                        <li><a href="oldSite"><i class="fa fa-circle-o"></i> 老站跟踪</a><li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                                <div class="col-md-4 equal-height-in">
                                                    <ul class="list-unstyled equal-height-list">
                                                        <li><h3>翻频</h3></li>
                                                        <!-- 4G翻频 -->
                                                        @can('modifyFrequency4g',[Auth::user()->type,'modifyFrequency4g'])
                                                        <li><a href="modifyFrequency4g"><i class="fa fa-circle-o"></i> 4G翻频</a><li>
                                                        @endcan
                                                        <!-- End 4G翻频-->
                                                        <!-- 2G设备统计-->
                                                        @can('equipmentStatistics2G', [Auth::user()->type,'equipmentStatistics2G'])
                                                        <li><a href="equipmentStatistics2G"><i class="fa fa-circle-o"></i> 2G设备统计</a></li>
                                                        @endcan
                                                        <!-- End 2G设备统计-->
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        @endcan
                        <!-- end 特色功能 -->
                    </ul>
                </div>
                <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- Messages: style can be found in dropdown.less-->
                    <!-- <li class="dropdown messages-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-envelope-o"></i>
                        </a>
                    </li> -->

                    <li class="dropdown notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="glyphicon glyphicon-refresh"></i>
                            <!-- <span class="label label-warning" id="noticeNumber"></span> -->
                        </a>
                        <ul class="dropdown-menu">
                            <li >
                                <a class="btn btn-default" onclick="setLocaleLang('zh_cn')">
                                    中文
                                </a>
                            </li>
                            <li >
                                <a class="btn btn-default" onclick="setLocaleLang('en')">
                                    English
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Notifications: style can be found in dropdown.less -->
                    <li class="dropdown notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bell-o"></i>
                            <span class="label label-warning" id="noticeNumber"></span>
                        </a>
                        <ul class="dropdown-menu">
                            @can('adminOnly',Auth::user()->type)
                            <li class="header">
                                <a class="btn btn-default" onclick="addNotice()">
                                    <i class="fa fa-plus"></i>{{trans('nav.CREATE_NOTIFICATION')}}
                                </a>
                            </li>
                            @endcan
                            <li>
                            <!-- inner menu: contains the actual data -->
                                <ul class="menu" id="noticeUl">
                                    <!-- <li><a href="#">5 new members joined today</a></li> -->
                                </ul>
                            </li>
                            <li class="header">
                                <input type="hidden" value="" id="noticeIds">
                                <!-- <a class="btn btn-default" onclick="readAll()">全部已读</a> -->
                                <a class="text-center" href="readAllNotice" onclick="readAll()">{{trans('nav.VIEW_ALL')}}</a>
                            </li>
                        </ul>
                    </li>
                    <!-- Tasks: style can be found in dropdown.less -->
                    <!-- <li class="dropdown tasks-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-flag-o"></i>
                        </a>
                    </li> -->
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-user"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- Menu Body -->
                            <li class="user-body">
                                <div class="row">
                                    <label class="col-sm-4 text-right">user：</label>
                                    <span class="col-sm-8" id="user_user">{{Auth::user()->user}}</span>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 text-right">type：</label>
                                    <span class="col-sm-8" id="user_type">{{Auth::user()->type}}</span>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 text-right">email：</label>
                                    <span class="col-sm-8" id="user_email">{{Auth::user()->email}}</span>
                                </div>
                                
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="text-center">
                                    <a class="btn btn-default btn-flat" href="UserSetting">{{trans('nav.SETTING')}}</a>
                                    <a class="btn btn-default btn-flat" onclick="signout()">{{trans('nav.LOG_OFF')}}</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <!-- Control Sidebar Toggle Button -->
                    <li class="dropdown options-menu">
                        <a href="#"  class="dropdown-toggle" data-toggle="dropdown">
                        	<i class="fa fa-gears"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <div class="mega-menu-content disable-icons">
                                    <div class="container" style="width:200px;">
                                        <div class="row equal-height">
                                            <div class="col-md-12 equal-height-in">
                                                <ul class="list-unstyled equal-height-list">
                                                    <li><h3>{{trans('nav.SERVICE')}}</h3></li>
                                                    <!-- 服务 -->
                                                    <li><a href="downloadCourse"><i class="fa fa-circle-o"></i> {{trans('nav.USING_HELP')}}</a><li>
                                                    <li><a href="feedBack"><i class="fa fa-circle-o"></i> {{trans('nav.USING_FEEDBACK')}}</a><li>
                                                    <!-- End 服务 -->
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            </div>
            <!--/navbar-collapse-->
            
        </nav>
    </header>
   
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content-header">
            @yield('content-header')
        </section>
        <section class="content">
            @yield('content')
        </section>
    </div>
    <!-- /.content-wrapper -->
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 2.3.3
        </div>
        <strong>Copyright &copy; 2015-2016 Ericsson.</strong> All rights
        reserved.
    </footer>
    <!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- 新增通知 -->
<div class="modal fade" id="add_notice">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">通知</h8>
            </div>
            <form class="form-horizontal" role="form" id="noticeForm">
            <div class="modal-body text-center">
                <input type="hidden" id="noticeId" value="">
                <div class="form-group">
                    <label for="noticeTitle" class="col-sm-2 col-sm-offset-2 control-label">标题：</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="noticeTitle" id="noticeTitle" placeholder="通知标题" maxlength="50">
                    </div>
                </div>
                <div class="form-group">
                    <label for="noticeContent" class="col-sm-2 col-sm-offset-2 control-label">内容：</label>
                    <div class="col-sm-6">
                        <textarea class="form-control" name="noticeContent" id="noticeContent" style="height : 100px;resize: none;" maxlength="500"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="userGroup_notice" class="col-sm-2 col-sm-offset-2 control-label">用户组：</label>
                    <div class="col-sm-6">
                        <select name="userGroup_notice" id="userGroup_notice" multiple>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="saveBtn" onclick="updateNotice()">保存</button>
                <button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- 查看通知 -->
<div class="modal fade" id="read_notice">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title">通知</h8>
            </div>
            <div class="modal-body text-center row">
                <input type="hidden" name="noticeId_read" id="noticeId_read" value="">
                <div class="col-sm-8 col-sm-offset-2" >
                    <h4 id="noticeTitle_read" style="word-break:break-all;"></h4>
                </div>
                <div class="col-sm-6 col-sm-offset-3">
                    <small id="noticePublisher" class="hidden"></small>
                    <small id="noticePublishTime"></small>
                </div>
                <div class="col-sm-10 col-sm-offset-1" id="noticeContent_read" style="padding-top:20px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="readBtn" onclick="setNoticeReaded()">设为已读</button>
                <button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<!-- ./wrapper -->

<!-- jQuery 2.2.0 -->
<script src="plugins/jQuery/jQuery-2.2.0.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jQueryUI/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- datepicker -->
<script src="plugins/datepicker/bootstrap-datepicker.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="plugins/fastclick/fastclick.js"></script>

<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<script src="dist/js/genius/sidebar/locate.js"></script>

<!--input select-->
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>

<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!--map jiangsu 江苏-->
<script src="plugins/echarts/echarts.min.js"></script>
<script src="plugins/echarts/jiangsu.js"></script>

<!--layui -->
<script src="plugins/layui/layui.js"></script>

<script src="dist/js/nav.js"></script>
@yield('scripts')
<!--end-->
</body>
</html>
