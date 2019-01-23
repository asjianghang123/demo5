@extends('layouts.nav')

@section('content')
<section class="content-header">
	<h1>测量指标查询-RSRP_MRO</h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-dashboard"></i>日常优化
		</li>
		<li>指标分析
		</li>
		<li class="active">测量指标查询-RSRP_MRO</li>
	</ol>
</section>
<section class="content">
	<div class="row">
		<div class="col-sm-12">
			
					<div class="box">
						<div class="box-header with-border">
							<h3 class="box-title">查询条件</h3>
						</div>
						<div class="box-body">
							<form class="form-horizontal" role="form" id="queryForm">
								<div class="form-group">
								   
									<label class="col-sm-1 control-label">城市</label>
                            		<div class="col-sm-3">
                                		<select id="city" class="form-control input-sm">
										</select>
                            		</div>
                            		<label class="col-sm-1 control-label">日期</label>
                            		<div class="col-sm-3">
                                		<input id="dateTime" class="form-control" type="text" value=""/> 
                            		</div>
								</div>
							</form>
						</div>
						<div class='box-footer' style="text-align:right;">
							<a id="search" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="doSearchMRORSRP()"><span class="ladda-label">查询</span></a>
							
						</div>	
					</div>
					<div class="box">
			            <div class="box-header with-border">
							<h3 class="box-title">查询数据</h3>
							<div class="box-tools pull-right">
								</a>		
								<a id="export" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="exportAll()"><span class="ladda-label">导出</span></a>
				            </div>
						</div>
						<div class="box-body">
							<table id="MRORSRPTable"> 
							</table>
						</div>
		            </div>
				</div>
				
			</div>

</section>
@endsection

@section('scripts')
<style type="text/css">
	#MRORSRPTable td div{
	    width:100%;
	    white-space:nowrap;
	    overflow:hidden;
	    text-overflow:ellipsis;
	  }
	#loadSaveData {
    	cursor:pointer;
    	color:#97a0b3;
    }
    #loadSaveData:hover {
    	color: #606c84;
    }
</style>
<link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>
<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

<!--loading-->
<link rel="stylesheet" href="plugins/loading/css/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>
<!--bootstrapvalidator-->
<link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script>
<!--input select-->
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>
<script type="text/javascript" src="dist/js/QueryAnalysis/MRORSRPQuery.js"></script>
<link rel="stylesheet" href="dist/css/button.css">
<style type='text/css'>
	.datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
    background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
    border-color: #ffb733;
	}	
</style>
@endsection
