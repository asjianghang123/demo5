@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>小区自忙时分析</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-rocket">专项研究</i>
		</li>
		<li>
			硬件分析
		</li>
		<li class="active">小区自忙时分析</li>
	</ol>
</section>
@endsection
@section('content')
	<section class="content">
		<div class="row">
			<div class="col-sm-12">
			<div class="box" >
				<div class="box-header  with-border">
                    <h3 class="box-title">查询条件</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
				<div class="box-body">
					<form class="form-horizontal" role="form" id="queryForm">
						<div class="form-group">
							<label class="col-sm-1 control-label">城市</label>
                            <div class="col-sm-2">
                                <select id="allCity" class="form-control input-sm">
								</select>
                            </div>
							<label class="col-sm-1 control-label" for="dimensions">维度</label>
							<div class="col-sm-2">
								<select id="dimensions" class="form-control input-sm">
									<option value="day">天</option>
                                    <option value="dayGroup">天组</option>
								</select>
							</div>
							<label for="startTime" class="col-sm-1 control-label">开始日期</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" name="startTime" id="startTime"></input>
							</div>
							<label for="endTime" class="col-sm-1 control-label">结束日期</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" name="endTime" id="endTime"></input>
							</div>
						</div>
					</form>
					
				</div>
				<div class="box-footer">
					<div style="text-align:right;">
						<a id="queryBtn" class="btn btn-primary ladda-button" role="button" onClick="query()" data-color='red' data-style="expand-right" ><span class="ladda-label">查询</span></a>
					</div>
				</div>
			</div>
			<div class="box">
				<div class="box-header  with-border">
                    <h3 class="box-title">查询数据</h3>
                    <div class="box-tools pull-right">
                    	<a id="exportBtn" class="btn btn-primary ladda-button" role="button" onClick="exportFile()" data-color='red' data-style="expand-right" ><span class="ladda-label">导出</span></a>
                    </div>
                </div>
				<div class="box-body">
		              <table id="queryDataTable">
		              </table>
	            </div>
			</div>
		</div>
		</div>
	</section>
@endsection

@section('scripts')
<link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>
<style type='text/css'>
	.datepicker table tr td.today,
	.datepicker table tr td.today:hover,
	.datepicker table tr td.today.disabled,
	.datepicker table tr td.today.disabled:hover {
		background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
		border-color: #ffb733;
	}	
	label.control-label{
		font-weight:500;
		text-align:left!important;
		text-indent:5px;
	}
</style>
@endsection
<script type="text/javascript" src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/parameterAnalysis/toptraffic.js" ></script>

