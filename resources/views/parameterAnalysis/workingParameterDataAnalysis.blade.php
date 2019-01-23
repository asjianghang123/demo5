@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>GSM板卡串号统计</h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-rocket"></i>专项研究
		</li>
		<li>硬件分析
		</li>
		<li class="active">工参数据分析</li>
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
							<label class="col-sm-1 control-label" for="actions">功能</label>
							<div class="col-sm-3">
								<select id="actions" class="form-control input-sm">
									<option value="1">跨站100米内同频检查</option>
                                    <option value="2">站内同频小区方位角检查</option>
                                    <option value="3">方位角检查</option>
                                    <option value="4">经纬度检查</option>
								</select>
							</div>
							<label class="col-sm-1 control-label" for="dimensions">维度</label>
							<div class="col-sm-3">
								<select id="dimensions" class="form-control input-sm">
									<option value="all">全网</option>
                                    <option value="city">地市</option>
                                    <option value="station">基站</option>
                                    <option value="cell">小区</option>
                                    <option value="keyWord">关键词</option>
								</select>
							</div>
							<label for="date" class="col-sm-1 control-label">日期</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="date" id="date"></input>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-1 control-label">城市</label>
                            <div class="col-sm-3">
                                <select id="allCity" class="form-control input-sm" multiple="multiple">
								</select>
                            </div>
							<label class="col-sm-1 control-label">基站</label>
                            <div class="col-sm-3">
                                <input id="erbsInput" class="form-control" type="text" value=""/>
                            </div>
                            <label class="col-sm-1 control-label">小区</label>
                            <div class="col-sm-3">
                                <div class="input-group">									 
					               	<input type="text" class="form-control" id="cellInput">
					               	<input type="file" class="hidden" name="fileImport" id="fileImport" onchange="toName(this)">
					               	<span class="input-group-btn">
					                  	<button class="btn btn-default" type="button" onclick="fileImport.click()">选择文件</button>
					               	</span>
					            </div>
                            </div>
						</div>
						<div class="form-group">
							<label class="col-sm-1 control-label" for="keyWord">关键词</label>
							<div class="col-sm-3">
								<input type="text" value="" class="form-control" id="keyWord">
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
<!-- datepicker -->
<link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>
<!-- grid -->
<script type="text/javascript" src="plugins/bootstrap-grid/js/grid.js"></script>
<!--select2-->
<script type="text/javascript" src="plugins/select2/select2.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<!-- treeview -->
<script src="plugins/treeview/bootstrap-treeview.min.js"></script>
<!--loading-->
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>
<!--fileStyle-->
<script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>
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
<link rel="stylesheet" href="dist/css/button.css">
<!-- jQuery 2.2.0 -->
<script type="text/javascript" src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/parameterAnalysis/workingParameterDataAnalysis.js"></script>