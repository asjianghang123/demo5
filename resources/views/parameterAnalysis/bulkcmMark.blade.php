@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>Bulkcm留痕</h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-thumbs-up"></i>特色功能
		</li>
		<li>留痕
		</li>
		<li class="active">Bulkcm留痕</li>
	</ol>
</section>
@endsection
@section('content')

<section class="content">

	<div class="row">
		
		<div class="col-sm-12">
			<div class="box" >
				<div class="box-header with-border">
                    <h3 class="box-title">查询条件</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
				<div class="box-body">
					<form class="form-inline" role="form" id="queryForm">
						<div class="form-group">
						日期：
						</div>
						<div class="form-group">
							<label class="sr-only"></label>
		    				<p class="form-control-static">
		    					
		    					<select id="date" class="form-control input-sm" style="width:180px;">
								</select> 
								
		    				</p>					
					  	</div>
					  	<div class="form-group">
						城市：
						</div>
						<div class="form-group">
							<label class="sr-only"></label>
		    				<p class="form-control-static">
		    					<!-- <select id="allCity" class="form-control input-sm">
								</select>   -->
								<select id="allCity" class="js-example-basic-single js-states form-control input-md" multiple="multiple" style="height:31px;">
								</select>
		    				</p>					
					  	</div>
					</form>
					
				</div>
				<div class="box-footer">
					<div style="text-align:right;">
						<a id="queryBtn" class="btn btn-primary ladda-button"  href="#" role="button" onClick="query()" data-color='red' data-style="expand-right" ><span class="ladda-label">查询</span></a>
						

					</div>
				</div>
			</div>
			<div class="box">
				<div class="box-header with-border">
                    <h3 class="box-title">查询数据</h3>
                    <div class="box-tools pull-right">
                        <a id="exportBtn" class="btn btn-primary ladda-button"  href="#" role="button" onClick="exportFile()" data-color='red' data-style="expand-right" ><span class="ladda-label">导出</span></a>
                    </div>
                </div>
				<div class="box-body">
		              <table id="bulkcmMarkTable">
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
<!--select2-->
<script type="text/javascript" src="plugins/select2/select2.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<!-- treeview -->
<script src="plugins/treeview/bootstrap-treeview.min.js"></script>
<!--loading-->
<link rel="stylesheet" href="plugins/loading/css/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>
<style>
.select2-container .select2-selection--single {
    height: 33px;
}
.dropdown-menu {
   min-width:230px;
}
</style>
@endsection
<link rel="stylesheet" href="dist/css/button.css">
<!-- jQuery 2.2.0 -->
<script type="text/javascript" src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/parameterAnalysis/bulkcmMark.js"></script>