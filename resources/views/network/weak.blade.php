@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>短板概览</h1>
	<ol class="breadcrumb">
        <li><i class="fa fa-dashboard"></i> 网络概览</li>
        <li>短板概览</li>
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
                        <h4>当前日期：{{date('Y-m-d')}}</h4>
                    </div>
                    <button id="weakExport" class="btn btn-primary pull-right ladda-button" data-style="expand-right" href="#" onClick="weakExport()"><span class="ladda-label">导出报告</span></button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">差小区概览</h3>
                    <div class="box-tools pull-right">
                        <a class="btn  btn-primary ladda-button" data-style="expand-right" onClick="ruleOpenConfigInfo()" id="rule">
                            <span class="ladda-label">筛选规则</span>
                        </a>
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="nav-tabs-custom">
                      
                        <div class="tab-content">
                            <div class="chart tab-pane active" id="chart-badCell" style="position: relative;height: 400px;"></div>
                            <button id="cellBackBtn" class="btn btn-default" style="position:absolute;top:65px;right:60px;display:none;">◁ Back to previous</button>
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
                <div class="box-header">
                    <h3 class="box-title">告警概览</h3>
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
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">当前告警</h3>
                                </div>
                                <div class="box-body" style="position:relative">
                                    <div class="chart tab-pane active" id="bar-chart-current" style="position: relative;height: 400px;"></div>
                                   <button id="backBtn" class="btn btn-default" style="position:absolute;top:20px;right:50px;display:none;">◁ Back to previous</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3 class="box-title">历史告警</h3>
                                </div>
                                <div class="box-body" style="position:relative">
                                    <div class="chart tab-pane active" id="bar-chart-history" style="position: relative;height: 400px;">
                                    </div>
                                   <button id="backBtnhistory" class="btn btn-default" style="position:absolute;top:20px;right:50px;
                                   display:none;">◁ Back to previous</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col -->
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">干扰概览</h3>
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
                    <div class="nav-tabs-custom">
                      
                        <div class="tab-content">
                            <div class="chart tab-pane active" id="chart-interfere" style="position: relative;height: 400px;"></div>
                        </div>
                    </div>
                <!-- ./box-body -->
                </div>
            </div>
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">弱覆盖概览</h3>
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
                <div class="box-body">
                    <div class="nav-tabs-custom">
                      
                        <div class="tab-content">
                            <div class="chart tab-pane active" id="chart-cover" style="position: relative;height: 400px;"></div>
                        </div>
                    </div>
                <!-- ./box-body -->
                </div>
            </div>
            <div class="box">    
                <div class="box-header with-border">
                    <h3 class="box-title">重叠覆盖概览</h3>
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
                    <div class="nav-tabs-custom">
                      
                        <div class="tab-content">
                            <div class="chart tab-pane active" id="chart-overlapCover" style="position: relative;height: 400px;"></div>
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
    			<div class="box-header">
                    <h3 class="box-title">参数概览</h3>
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
    			<div class="box-body">
    				<div class="row">
    					<div class="col-md-12">
	    					<div class="box">
	    						<div class="box-header">
	    							<h3 class="box-title">Baseline检查</h3>
	    						</div>
	    						<div class="box-body">
	    							<div class="row">
	    								<div class="col-md-6">
	    									<div class="box">
	    										<div class="box-header">
	    											<h3 class="box-title">参数数量分布</h3>
	    										</div>
	    										<div class="box-body">
				                                    <div id="baselineParamNum" style="height: 400px"></div>
				                                </div>
	    									</div>
	    								</div>
	    								<div class="col-md-6">
	    									<div class="box">
	    										<div class="box-header">
	    											<h3 class="box-title">基站数量分布</h3>
	    										</div>
	    										<div class="box-body">
				                                    <div id="baselineBSNum" style="height: 400px"></div>
				                                </div>
	    									</div>
	    								</div>
	    							</div>
	    						</div>
	    					</div>
	    				</div>
    				</div>
    				<!-- <div class="row">
    					<div class="col-md-12">
	    					<div class="box">
	    						<div class="box-header">
	    							<h3 class="box-title">一致性检查</h3>
	    						</div>
	    						<div class="box-body">
	    							<div class="row">
	    								<div class="col-md-6">
	    									<div class="box">
	    										<div class="box-header">
	    											<h3 class="box-title">参数数量分布</h3>
	    										</div>
	    										<div class="box-body">
				                                    <div id="consistencyParamNum" style="height: 400px"></div>
				                                </div>
	    									</div>
	    								</div>
	    								<div class="col-md-6">
	    									<div class="box">
	    										<div class="box-header">
	    											<h3 class="box-title">小区数量分布</h3>
	    										</div>
	    										<div class="box-body">
				                                    <div id="consistencyBSNum" style="height: 400px"></div>
				                                </div>
	    									</div>
	    								</div>
	    							</div>
	    						</div>
	    					</div>
	    				</div>
    				</div> -->
    			</div>
    		</div>
    	</div>
    </div>
</section>
@endsection
@section('scripts')
<script src="https://code.highcharts.com/stock/highstock.js"></script>
<script src="plugins/highcharts/js/highcharts.js"></script>
<script src="https://code.highcharts.com/stock/modules/exporting.js"></script>
<link rel="stylesheet" href="dist/css/button.css">
@endsection
<!-- jQuery 2.2.0 -->
<script type="text/javascript" src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/genius/weak-badCell.js"></script>
<script type="text/javascript" src="dist/js/genius/weak-alarm.js"></script>
<script type="text/javascript" src="dist/js/genius/weak-interfere.js"></script>
<script type="text/javascript" src="dist/js/genius/network-weak.js"></script>


