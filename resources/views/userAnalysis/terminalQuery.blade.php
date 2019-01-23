@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>终端查询</h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-dashboard"></i>用户分析
		</li>
		<li>终端分析</li>
		<li class="active">终端查询
		</li>
	</ol>
</section>
@endsection
@section('content')
<section class="content">
	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class='box-header with-border'>
					<h3 class="box-title">查询条件</h3>
				</div>
				<div class='box-body'>
					<form class="form-horizontal" role="form" id="queryForm">
						<div class="form-group">
							<label for="citys" class="col-sm-1 control-label">城市</label>
                            <div class="col-sm-3">
                                <select class="form-control" name="citys" id="citys">
                                </select>
                            </div>
							<label for="user_query" class="col-sm-1 control-label">用户</label>
							<div class="col-sm-3">
								<input type="text" class="form-control" name="user_query" id="user_query" placeholder="输入imsi或者msisdn">
							</div>
						</div>
					</form>
				</div>
				<div class="box-footer">
					<div class="pull-right">
						<div class="btn-group">
		                    <a type="button" class="btn  btn-primary ladda-button" data-style="expand-right" onclick="query()" id="queryBtn">
		                        <span class="ladda-label">查询</span>
		                    </a>
		                </div>
					</div>
				</div>
			</div>
			<div class='box'>
				<div class="box-header with-border">
					<h3 class="box-title">查询结果</h3>
				</div>
				<div class="box-body">
					<div class="table-responsive">
						<table id="userTable">
						</table>
					</div>
				</div>	
			</div>
		</div>	
	</div>
</section>



@endsection
@section('scripts')
<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css">
<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>
@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/userAnalysis/terminalQuery.js"></script>

