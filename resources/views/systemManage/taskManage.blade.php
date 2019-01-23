@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>任务管理</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-dashboard"></i>系统管理
		</li>
		<li class="active">任务管理</li>
	</ol>
</section>
@endsection
@section('content')


<section class="content">
	<div class="row">
		<div class="col-sm-12">	          
			<div class="box">
				<div class="box-header with-border">
						<h3 class="box-title">定时任务</h3>
						<div class="box-tools pull-right">
                        	<a id="editTaskFile" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="editTaskFile()"><span class="ladda-label">编辑</span></a>
                        	<a id="saveTaskFile" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="saveTaskFile()"><span class="ladda-label">保存</span></a>
							<a id="cancelBtn" class="btn btn-default ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="cancelEdit()"><span class="ladda-label">取消</span></a> 
		                </div>
		                
				</div>
				<div class="box-body">
		            <textarea class="form-control" name="taskFileContent" id="taskFileContent" disabled="disabled" style="height:400px;resize:none;"></textarea>
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
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/systemManage/taskManage.js"></script>
<style>
a.btn {
  display: inline-block;
  padding: 4px 8px;
  margin-bottom: 0;
  font-size: 13px;
  font-weight: normal;
  line-height: 1.42857143;
  text-align: center;
  white-space: nowrap;
  vertical-align: middle;
  -ms-touch-action: manipulation;
      touch-action: manipulation;
  cursor: pointer;
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
          user-select: none;
  background-image: none;
  border: 1px solid transparent;
  border-radius: 4px;
}
</style>
