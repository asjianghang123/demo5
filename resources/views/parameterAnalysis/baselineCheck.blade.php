@extends('layouts.nav')
@section('content-header')
<section class="content-header">
  <h1>Baseline检查<!-- <small>advanced tables</small> --></h1>
  <ol class="breadcrumb">
    <li><i class="fa fa-dashboard"></i>日常优化
    </li>
    <li>参数分析
    </li>
    <li class="active">Baseline检查</li>
  </ol>
</section>
@endsection
@section('content')

<section class="content">
<div class="row">
	<div class="col-sm-3" >
		<div class="box">
			<div class="box-body">
				<div class="form-group">
				    <label for="paradistributionDate">日期</label>
						<select id="paramQueryDate" onchange="changeDate(this.value)" class="js-example-basic-single js-states form-group col-xs-10">
						</select>
				</div>
			</div>
   

      <div class="form-group">
        <div id="templateTree"></div>
        <input type="hidden" id="templateId" value="">
        <input type="hidden" id="templateName" value="">
      </div>
    </div>

	</div>

	<div class="col-sm-9">
    <div class="row">
      <div class="col-md-12">
		    <div class="box">
          <div class="box-header with-border">
              <h3 class="box-title">检查概要</h3>

               <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                  </button>
              </div>
          </div>

    			<div class="box-body">
            <div class="nav-tabs-custom">
              <div class="tab-content">
                <div class="chart tab-pane active" id="categoryDistribution" style="position: relative;height: 400px;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

      <div class="row">
        <div class="col-md-12">
          <div class="box">
             <div class="box-header with-border">
                    <h3 class="box-title">检查详情</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                         <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                        </button>
                    </div>
              </div>

            <div class="box-body">
              <div class="nav-tabs-custom">
                <div class="row">
                  <form class="form-inline">
                    <div class="form-group col-sm-8">
                        <label style="width: 10%; text-align: center;" >城市:</label>
                        <select name="paramdistributionCity" id="allCity" class="form-group" multiple="multiple">
                        </select>
                        <label style="width: 10%; text-align: center;" >基站:</label>
                        <label class="sr-only"></label>
                        <p class="form-control-static">
                          <div class="input-group">
                           <input type="text" class="form-control" id="cellInput">
                           <input type="file" class="hidden" name="fileImport" id="fileImport" onchange="toName(this)">
                           <span class="input-group-btn">
                              <button class="btn btn-default" type="button" onclick="fileImport.click()">选择文件</button>
                           </span>
                        </div>
                        </p>
                    </div>
                    <div class="form-group col-sm-4" style="text-align:right;">
                        <button id="search" type="submit"  class="btn btn-primary" onclick="parameterViewSearch();return false;">查询</button>
                        <button id="export" type="submit"  class="btn btn-primary" onclick="exporttofile();return false;">导出</button>
                    </div>
                  </form>
                </div>

              <div class="tab-content">
                <table id="tempParameterCellPrintTable" class="gj-grid-table table table-bordered table-hover">
                </table>
              </div>
            </div>
          </div>
				</div>
			</div>
		</div>
  </div>
</div>
</section>
@endsection

@section('scripts')
<style>
#tempParameterCellPrintTable td div{
    width:100%;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }
</style>
<link type="text/css" href="plugins/treeview/bootstrap-treeview.min.css" rel="stylesheet"/>
<script src="plugins/treeview/bootstrap-treeview.min.js"></script>

<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>

<link type="text/css" href="plugins/select2/select2.css" rel="stylesheet" />
<script type="text/javascript" src="plugins/select2/select2.js"></script>

 <script src="plugins/highcharts/js/highcharts.js"></script>
 <script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>

@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/parameterAnalysis/templateTree.js"></script>

