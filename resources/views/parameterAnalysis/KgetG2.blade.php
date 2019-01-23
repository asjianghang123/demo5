@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>参数查询(Detail)<!-- <small>advanced tables</small> --></h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-dashboard"></i>日常优化
		</li>
		<li>参数分析
		</li>
		<li class="active">参数查询(Detail)</li>
	</ol>
</section>
@endsection
@section('content')

<section class="content">
	<div class="row">
		<div class="col-sm-3">
			<div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">参数结构</h3>
                </div>
                <div class="box-body">
                   <form>
					  <div class="form-group">
						<div class="input-group">
				           	<input type="text" class="form-control" id="paramQueryMoErbs" aria-describedby="basic-addon1" placeholder="请输入参数查询" />
				               <span class="input-group-btn">
					                <button class="btn btn-default" type="button" onClick="search('paramQueryMoTree','paramQueryMoErbs')">
					                     &nbsp;<span class="glyphicon glyphicon-search btn-group-lg" aria-hidden="true"></span>
					                </button>
					                <button class="btn btn-default" type="button" onClick="clearSearch('paramQueryMoTree','paramQueryMoErbs')">
										&nbsp;<span class="glyphicon glyphicon-remove btn-group-lg" aria-hidden="true"></span>
									</button>
				               	</span>
				            </div>
					  </div>
					  <div class="form-group" style="height:600px;overflow:auto;">
					  	<div id="paramQueryMoTree"></div>
					  </div>
				  	</form>
                </div>
            </div>

		</div>
		<div class="col-sm-9">
			<div class="box" >
				<!-- <div class="box-header"> -->
                    <!-- <h3 class="box-title">查询条件</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div> -->
                    <div class="box-header with-border" style="height:35px">
						<h3 class="box-title" style="display:inline;float:left;margin-right:5px">查询条件</h3>
						<div class="box-tools pull-right">
                        	<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        	<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    	</div>
					</div>
                <!-- </div> -->
				<div class="box-body">
					<!-- <div class="row"> -->
						<form class="form-horizontal" role="form" id="queryForm">
							<div class="form-group">
								<label class="col-sm-2 control-label" for="parameterAnalysisDate">日期</label>
                            	<div class="col-sm-4">
                                	<select id="parameterAnalysisDate" class="js-example-basic-single js-states form-control input-md" onchange="initFeatureList()">
									</select>
                            	</div>
                            	<label class="col-sm-2 control-label">城市</label>
                            	<div class="col-sm-4">
                                	<select id="parameterAnalysisCity" class="form-control " multiple="multiple">
								</select>
                            	</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label" for="paramQueryErbs">基站</label>
                            	<div class="col-sm-4">
                                	<input id="paramQueryErbs" class="form-control" type="text" value="" placeholder="请输入基站" name="paramQueryErbs"/>
                            	</div>
                            <label class="col-sm-2 control-label">子网</label>
							<div class="col-sm-4">
								<select id="subNetworks" class="form-control" multiple="multiple">
								</select>
							</div>
                            	<div id="OptionalFeatureLicenseId" style="display:none">
	                            	<label class="col-sm-2 control-label" for="featureList">Feature</label>
	                            	<div class="col-sm-4">
	                                	<select id="featureList" class="js-example-basic-single js-states form-control input-md" multiple="multiple">
										</select>
	                            	</div>
                            	</div>
							</div>
							<!-- <div class="form-group col-sm-4">
							    <label for="paramQueryErbs">基站</label>
								<input id="paramQueryErbs" class="form-control input-sm" type="text" placeholder="请输入基站" name="paramQueryErbs" style="height:33px;">
							</div> -->
						</form>
					<!-- </div> -->
				</div>
				<div class="box-footer">
					<div style="text-align:right;">
						<a id="search" class="btn btn-primary ladda-button"  href="#" role="button" onClick="paramQuerySearch();return false;" data-color='red' data-style="expand-right" ><span class="ladda-label">查询</span></a>

					</div>
				</div>
			</div>
			<div class="box">
				<div class="box-header  with-border">
                    <h3 class="box-title">查询数据</h3>
                    <div class="box-tools pull-right">
                    	<a id="export" class="btn btn-primary ladda-button"  href="#" role="button" onClick="paramQueryExport();return false;" data-color='red' data-style="expand-right" ><span class="ladda-label">导出</span></a>
                        <!-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button> -->
                    </div>
                </div>
				<div class="box-body">
		              <table id="paramQueryTable">
		              </table>
	            </div>
			</div>
		</div>


	</div>
</section>
@endsection
@section('scripts')
<!-- grid -->
<script type="text/javascript" src="plugins/bootstrap-grid/js/grid.js"></script>
<!-- bootstrap-table-->
<link href="plugins/bootstrap-table/bootstrap-table.css" rel="stylesheet" />
<script type="text/javascript" src="plugins/bootstrap-table/bootstrap-table.js"></script>
<script type="text/javascript" src="plugins/bootstrap-table/locale/bootstrap-table-zh-CN.js"></script>
<!--select2-->
<script type="text/javascript" src="plugins/select2/select2.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<!-- treeview -->
<script src="plugins/treeview/bootstrap-treeview.min.js"></script>
<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>
<style>
#paramQueryTable td div{
		width:100%;
		white-space:nowrap;
		overflow:hidden;
		text-overflow:ellipsis;
	}
.select2-container .select2-selection--single {
    height: 33px;
}
.dropdown-menu {
   min-width:230px;
}
label.control-label{
		font-weight:500;
		text-align:left!important;
		text-indent:5px;
	}
	.toggle.btn-xs{
		margin-top:5px;
	}
</style>
@endsection
<!-- jQuery 2.2.0 -->
<link rel="stylesheet" href="dist/css/button.css">
<script type="text/javascript" src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/parameterAnalysis/kgetG2.js"></script>
