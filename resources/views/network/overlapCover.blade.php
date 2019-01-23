@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>重叠覆盖小区</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>专项研究
		</li>
		<li>
			<i class="fa fa-dashboard"></i>重叠覆盖分析
		</li>
		<li class="active">重叠覆盖小区</li>
	</ol>
</section>
@endsection
@section('content')


<section class="content"> 
	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">移动标准</h3>
					<div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
				</div>
				<div class="box-body">
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
						<div class="box-footer">
							<div class="pull-right">
								<div class="btn-group">
									<a id="queryBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onclick="query()"><span class="ladda-label">查询</span></a>
				                    <!-- <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="query()" id="queryBtn">
				                        <i class="fa fa-search"></i>
				                        <span class="ladda-label">查询</span>
				                    </button> -->
				                </div>
				                <!-- <div class="btn-group">
				                    <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="exportFile()" id="exportBtn">
				                        <i class="fa fa-sign-out"></i>导出
				                    </button>
				                </div> -->
							</div>
						</div>
					</div>
					<div class="box">
			            <div class="box-header with-border">
				            <h3 class="box-title">查询数据</h3>
				            <div class="box-tools pull-right"> 
				            	<div class="btn-group">
                            		<a id="exportBtn" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onclick="exportFile()"><span class="ladda-label">导出</span></a>
				            	</div>  
	                            <!-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>      -->   
	                        </div>
		            	</div>
		            	<div class="box-body">
		            		<table id="overlapCoverTable"> 
		            		</table>
		            	</div>
		            </div>
				</div>
			</div>
		</div>
	</div>

	<!-- <div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">Genius标准</h3>
					<div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
				</div>
				<div class="box-body">
					<div class="box">
						<div class="box-header with-border">
							<h3 class="box-title">查询条件</h3>
						</div>
						<div class="box-body">
							<form class="form-horizontal" role="form" id="queryForm">
								<div class="form-group">
									<label class="col-sm-1 control-label">城市</label>
                            		<div class="col-sm-3">
                                		<select id="city_Genius" class="form-control input-sm">
										</select>
                            		</div>
                            		<label class="col-sm-1 control-label">日期</label>
                            		<div class="col-sm-3">
                                		<input id="dateTime_Genius" class="form-control" type="text" value=""/> 
                            		</div>
								</div>
							</form>
						</div>
						<div class="box-footer">
							<div class="pull-right">
								<div class="btn-group">
				                    <a id="queryBtn_Genius" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onclick="query_Genius()"><span class="ladda-label">查询</span></a>
				                </div>
							</div>
						</div>
					</div>
					<div class="box">
			            <div class="box-header with-border">
				            <h3 class="box-title">查询数据</h3>
				            <div class="box-tools pull-right"> 
	                            <div class="btn-group">
	                            	<a id="exportBtn_Genius" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onclick="exportFile_Genius()"><span class="ladda-label">导出</span></a>
					            </div>     
	                        </div>
		            	</div>
		            	<div class="box-body">
		            		<table id="overlapCoverTable_Genius"> 
		            		</table>
		            	</div>
		            </div>
				</div>				
			</div>
		</div>
	</div> -->
</section>

@endsection


@section('scripts')
<link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!-- jquery.i18n.properties -->
<script src="plugins/jquery.i18n.properties/jquery.i18n.properties.min.js"></script>
<style type='text/css'>
	.datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
    background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
    border-color: #ffb733;
	}	
</style>

@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/genius/overlapCover.js"></script>



