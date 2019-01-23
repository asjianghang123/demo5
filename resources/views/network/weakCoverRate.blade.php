@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>弱覆盖小区</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>专项研究
		</li>
		<li>
			<i class="fa fa-dashboard"></i>弱覆盖分析
		</li>
		<li class="active">弱覆盖小区</li>
	</ol>
</section>
@endsection
@section('content')
 

<section class="content">
	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">查询条件</h3>
                </div>
                
				<div class="box-body">
					<form class="form-horizontal" role="form" id="queryForm">
						<div class="form-group">
							<label for="citys" class="col-sm-1 control-label">城市</label>
							<div class="col-sm-3">
								<select class="form-control" name="citys" id="citys">
								</select>
							</div>
							<label for="date" class="col-sm-1 control-label">日期</label>
							<div class="col-sm-3">
								<input id="dateTime" class="form-control" type="text" value=""/>
							</div>
					    </div>
						<!-- <div class="form-group">
                			&nbsp;&nbsp;忙时:
                			<select id="busyTime" class="form-control input-sm" >
                				<option value="earlyTime">早忙时</option>
                				<option value="laterTime">晚忙时</option>
							</select>
                		</div> -->
					</form>
					<!-- <form class="form-inline" role='form'  id="queryForm">
						<div class="form-group">
                			&nbsp;&nbsp;城市:
                			<select id="citys" class="form-control input-sm" >
							</select>
                		</div>
                		<div class="form-group">
                			&nbsp;&nbsp;日期:
							<input id="date" class="form-control" type="text" value=""/>
                		</div>
					</form> -->

				</div>
				
				<div class="box-footer">
					<div class="pull-right">
						<div class="btn-group">
		                    <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="query()" id="queryBtn">
		                        <i class="fa fa-search"></i>
		                        <span class="ladda-label">查询</span>
		                    </button>
		                </div>
		                <div class="btn-group">
		                    <button type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="exportDataFile()" id="exportBtn">
		                        <i class="fa fa-sign-out"></i>导出
		                        <!-- <i class="fa fa-sign-out"></i>
		                        <span class="ladda-label">导出</span> -->
		                    </button>
		                </div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class="box-body" style="overflow:auto;">
					<table id="resultTable"></table>
				</div>
			</div>
		</div>
	</div>
</section>

@endsection


@section('scripts')



<link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>
<!--input select-->
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

<!--treeview-->
<!-- <script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script> -->

<!--select2-->
<!-- <script type="text/javascript" src="plugins/select2/select2.js"></script> -->

<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

<!-- treegrid -->
<!-- <link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/easyui.css">
<link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/datagrid.css">
<link rel="stylesheet" href="dist/css/LTETemplateManage.css">
<script src="plugins/EasyUI/jquery.easyui.min.js"></script> -->

<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!--bootstrapvalidator-->
<!-- <link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script> -->


<!-- <script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script> -->

<!--bootatrap-toggle-->
<link href="plugins/bootstrap-toggle/css/bootstrap-toggle.min.css" rel="stylesheet">
<script type="text/javascript" src="plugins/bootstrap-toggle/js/bootstrap-toggle.min.js"></script>
<style type='text/css'>
	.datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
    background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
    border-color: #ffb733;
	}	
</style>

@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<!-- <script type="text/javascript" src="dist/js/NetworkOptimization/relationBadHandover.js"></script>  这个函数要自己重新复制一个 -->



<script type="text/javascript" src="dist/js/genius/weakCoverRate.js"></script>

 <script>

// 	//   $("#date").datepicker({format: 'yyyy-mm-dd'});
// 	//   var nowTemp = new Date();
// 	//   $("#date").datepicker('setValue', nowTemp);
// 	//  //  var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
// 	//   var checkin = $('#date').datepicker({
// 	// 	onRender: function(date) {
// 	// 	  return date.valueOf() < now.valueOf() ? '' : '';
// 	// 	}
// 	//   }).on('changeDate', function(ev) {
// 	// 	checkin.hide();
// 	// }).data('datepicker');



//   $("#date").datepicker({format: 'yyyy-mm-dd'});  //返回日期
//   var nowTemp = new Date();
//   $("#date").datepicker('setValue', nowTemp);

//   var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
//   var checkin = $('#date').datepicker({
// 	onRender: function(date) {
// 	  return date.valueOf() < now.valueOf() ? '' : '';
// 	}
//   }).on('changeDate', function(ev) {
// 	checkin.hide();
// 	}).data('datepicker');


// </script>
