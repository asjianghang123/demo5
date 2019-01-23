@extends('layouts.nav')

@section('content')
<section class="content-header">
	<h1>2G设备统计<!-- <small>advanced tables</small> --></h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-thumbs-up"></i>特色功能
		</li>
		<li>翻频
		</li>
		<li class="active">2G设备统计</li>
	</ol>
</section>

<section class="content">
	<div class="row">
		
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header with-border">
					<div style="display:inline">
						<h3 class="box-title">
						<form class="form-inline">
							<div class="form-group">
								库名
							</div>
							<div class="form-group">
								<label class="sr-only"></label>
			    				<p class="form-control-static">
			    					
			    					<select id="database" class="form-control input-sm" style="width:180px;">
									</select> 
									
			    				</p>					
						  	</div>
						</form>

						</h3>
								  
					</div>

					<div class="input-group" style="padding-top:10px;float:right;display:inline">
						<a id="search" class="btn btn-primary ladda-button" data-color='red' role="button" data-style="expand-right" href="#"  onClick="searchData()"><span class="ladda-label">查询</span></a>
						<a id="export" class="btn btn-primary ladda-button" data-color='red' role="button" data-style="expand-right" href="#"  onClick="exportData()"><span class="ladda-label">导出</span></a>
		            </div>
				</div>
				
	            <div class="nav-tabs-custom">
					<ul class="nav nav-tabs pull-left">
                        <li class="active"><a href="#table_Situation_1" data-toggle="tab" onclick="table_show()">现网设备情况</a></li>
                        <li><a href="#table_Statistics_1" data-toggle="tab" onclick="table_show()">现网小区级设备统计</a></li>
                    </ul>
                    <div class="box-body">
                    	<!-- <div class="tab-content">	 -->
                        	<div class="chart tab-pane" id="table_Situation_1">
                      			<div>
                      				<table id="table_Situation">
									</table>
                      			</div>    
                        	</div>
                        	<div class="chart tab-pane" id="table_Statistics_1" style='display: none;'>
                        		<table id="table_Statistics">
								</table>
                        	</div>
                        <!-- </div> -->
					</div>
	            </div>
			</div>
		</div>
	</div>
</section>


@endsection

@section('scripts')

<!--treeview-->
<script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script>

<!--datatables-->
<script type="text/javascript" src="plugins/bootstrap-grid/js/grid.js"></script>
<!-- <script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" > -->
<!--select2-->
<script type="text/javascript" src="plugins/select2/select2.js"></script>
<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!--bootstrapvalidator-->
<!-- <link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script> -->


<!-- <script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script> -->
<link type="text/css" rel="stylesheet" href="dist/css/button.css" >
<script type="text/javascript" src="dist/js/SpecialFunction/equimentStatistics2G.js"></script>
@endsection

