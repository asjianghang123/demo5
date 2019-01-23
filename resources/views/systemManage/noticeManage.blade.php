@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>通知管理</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>系统管理
		</li>
		<li>功能管理</li>
		<li class="active">通知管理</li>
	</ol>
</section>
@endsection
@section('content')


<section class="content">
	<div class="row">
		<div class="col-sm-12">	          
			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
				<div>
					<a class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="addNotice_man()"><span class="ladda-label">新增</span></a>
					<a class="btn btn-default ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="deleteNotice_man()"><span class="ladda-label">删除</span></a> 
					<a class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="editNotice_man()"><span class="ladda-label">修改</span></a> 
				</div>			
			</div>
			<div class="box">
				<div class="box-body">
		            <table id="noticeTable">
		            
		            </table>
	            </div>
			</div>
		</div>
	</div>
</section>

@endsection


@section('scripts')


<!-- <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script> -->

<!--input select-->
<!-- <script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" /> -->

<!--treeview-->
<!-- <script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script> -->

<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!--bootstrapvalidator-->
<link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script>
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/noticeManage.js"></script>

