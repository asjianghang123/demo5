@extends('layouts.nav')
@section('content-header')
<section class="content-header">
    <h1>基础数据检查</h1>
    <ol class="breadcrumb">
       <li><i class="fa fa-dashboard"></i>日常优化 
    </li>
    <li>参数分析
    </li>
    <li class="active">基础数据检查</li>
    </ol>
</section>
@endsection
@section('content')
<section class="content">
<div class="row">
	<div class="col-sm-3" >
		<div class="box">
			<div class="box-body">
        <form>
  				<div class="form-group">
  				    <label for="parameterAnalysisDate">日期</label>
  						<select id="parameterAnalysisDate" class="js-example-basic-single js-states form-group col-xs-10">
  						</select>
  				</div>
          <div class="form-group" style="height:570px;overflow:auto;">
              <div id="consistencyTree"></div>
          </div>
        </form>
			</div>
		</div>
		<div class="form-group">
			  <div id="templateTree"></div>
               <input type="hidden" id="templateId" value="">
               <input type="hidden" id="templateName" value="">
		</div>
	</div>
	<div class="col-sm-9">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">检查概要</h3>

                        <div class="box-tools pull-right">
                           {{--  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                            </button> --}}
                            <button type="button" class="btn btn-primary btn-sm" id="exportAllcontent" onclick="exportFiles()">导出</button>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="nav-tabs-custom">
                            <div class="tab-content">
                                <div class="chart tab-pane active" id="chart-consistency" style="position: relative;height: 400px;"></div>
                            </div>
                        </div>
                    <!-- ./box-body -->
                    </div>
                <!-- /.box -->
                </div>

            </div>
        <!-- /.col -->
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">检查详情</h3>
                        <div class="box-tools pull-right">
                            <div style="text-align:right; margin-right:10px;">
                                <a id="export" type="submit"  class="btn btn-primary" onclick="consistencyCheckExportTofile();return false;">导出详情</a>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="nav-tabs-custom">
                            <div id="checkType" class="row">
                                
                            </div>
                            <div id="TempEUtranCellFreqRelation" class="row" style="display:none;">
                                <div style="text-align:right; margin-right:25px;">
                                     <a id="import" type="submit"  class="btn btn-primary ladda-button" onclick="importUnidirectionalNeighborCell();return false;">导入白名单</a>
                                     <a id="exportTemplate" type="submit"  class="btn btn-primary ladda-button" onclick="consistencyCheckExportTemplate();return false;">导出白名单</a>
                                    <!-- <button id="export" type="submit"  class="btn btn-primary ladda-button" onclick="consistencyCheckExportTofile();return false;">导出详情</button> -->
                                    <input id="tableName" type="hidden" value="">
                                </div>
                            </div>
                            <div id="UnidirectionalNeighborCell" class="row" style="display:none;">
                                <div style="text-align:right; margin-right:25px;">
                                     <a id="import" type="submit"  class="btn btn-primary ladda-button" onclick="importUnidirectionalNeighborCell();return false;">导入白名单</a>
                                     <a id="exportTemplate" type="submit"  class="btn btn-primary ladda-button" onclick="consistencyCheckExportTemplate();return false;">导出白名单</a>
                                    <!-- <button id="export" type="submit"  class="btn btn-primary ladda-button" onclick="consistencyCheckExportTofile();return false;">导出详情</button> -->
                                   	<input id="tableName" type="hidden" value="">
                                </div>
                            </div>
                            <div id="LatlonCheckWhiteList" class="row" style="display:none;">
                                <div style="text-align:right; margin-right:25px;">
                                     <a id="import_latlon" type="submit"  class="btn btn-primary ladda-button" onclick="importLatlonCheckWhiteList();return false;">导入白名单</a>
                                     <a id="exportTemplate" type="submit"  class="btn btn-primary ladda-button" onclick="consistencyCheckExportTemplate();return false;">导出白名单</a>
                                    <!-- <button id="export" type="submit"  class="btn btn-primary ladda-button" onclick="consistencyCheckExportTofile();return false;">导出详情</button> -->
                                </div>
                            </div>
                            <div id="ExternalGeranCell" class="row" style="display:none;">
                                <div style="text-align:right; margin-right:25px;">
                                     <a id="DT_drop" type="submit"  class="btn btn-primary ladda-button" onclick="exportDT('DT_drop');return false;">导出DT脚本-删除</a>
                                     <a id="DT_add" type="submit"  class="btn btn-primary ladda-button" onclick="exportDT('DT_add');return false;">导出DT脚本-添加</a>
                                    <!-- <button id="export" type="submit"  class="btn btn-primary ladda-button" onclick="consistencyCheckExportTofile();return false;">导出详情</button> -->
                                    
                                </div>
                            </div>
                            <div id="searchType" class="row" style="display:none;">
                              <form class="form-inline">
                                <div class="form-group col-sm-12">
                                    <label>基站</label>
                                  <input id="erbs" class="form-control input-sm" type="text" placeholder="请输入站点名称" name="erbs" style="height:33px;">
                                  <label>eNBId</label>
                                  <input id="eNBId" class="form-control input-sm" type="text" placeholder="请输入eNBId" name="eNBId" style="height:33px;">
                                  <label>ecgi</label>
                                  <input id="ecgi" class="form-control input-sm" type="text" placeholder="请输入ecgi" name="ecgi" style="height:33px;">
                                  <label>IP</label>
                                  <input id="ip" class="form-control input-sm" type="text" placeholder="请输入IP" name="ip" style="height:33px;">
                                </div>
                                
                              </form>
                              <br/>
                              <form class="form-inline">
                                <div class="form-group col-sm-10">
                                  <label>小区</label>
                                  <input id="cell" class="form-control input-sm" type="text" placeholder="请输入小区名" name="cell" style="height:33px;">
                                </div>
                                <div class="form-group col-sm-2" style="text-align:right;">
                                    <button id="search_1" type="submit"  class="btn btn-primary" onclick="consistencyCheckDetailsSearch_1();return false;">查询</button>
                                    <button id="export_1" type="submit"  class="btn btn-primary" onclick="consistencyCheckExportTofile_1();return false;">导出</button>
                                </div>
                              </form>
                            </div>
                            <div class="tab-content">
                                <table id="consistencyCheckDetailTable" class="gj-grid-table table table-bordered table-hover">
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
<!-- 导入弹出框 -->
<div class="modal fade" id="import_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">导入</h8>
            </div>
      <div class="modal-body col-sm-12">
          
        <div class="col-sm-8 col-sm-offset-2">
          <div class="input-group">
                   <input type="text" class="form-control" id="fileImportName">
                   <input type="file" accept=".csv" class="hidden" name="fileImport" id="fileImport" onchange="toName(this)">
                   <span class="input-group-btn">
                      <button class="btn btn-default" type="button" onclick="fileImport.click()">选择文件</button>
                   </span>
                </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="importBtn" onclick="importFile()">确定</button>
        <button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
      </div>
    </div>
  </div>
</div>
<!-- 导入latlon弹出框 -->
<div class="modal fade" id="import_modal_latlon">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle_latlon">导入</h8>
            </div>
      <div class="modal-body col-sm-12">
          
        <div class="col-sm-8 col-sm-offset-2">
          <div class="input-group">
                   <input type="text" class="form-control" id="fileImportName_latlon">
                   <input type="file" accept=".csv" class="hidden" name="fileImport_latlon" id="fileImport_latlon" onchange="toName_latlon(this)">
                   <span class="input-group-btn">
                      <button class="btn btn-default" type="button" onclick="fileImport_latlon.click()">选择文件</button>
                   </span>
                </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="importBtn_latlon" onclick="importFile_latlon()">确定</button>
        <button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script src="plugins/highcharts/js/highstock.js"></script>
<script src="plugins/highcharts/js/highcharts-more.js"></script>
<script src="plugins/highcharts/js/modules/heatmap.js"></script>
<script src="plugins/highcharts/js/modules/exporting.js"></script>
<!-- grid -->
<script type="text/javascript" src="plugins/bootstrap-grid/js/grid.js"></script>
<!--select2-->
<script type="text/javascript" src="plugins/select2/select2.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<script src="plugins/loading/js/ladda.js"></script>
<script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>

<!-- treeview -->
<script src="plugins/treeview/bootstrap-treeview.min.js"></script>
<style>
.dropdown-menu {
   min-width:230px;
}
</style>
@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/parameterAnalysis/BasicDataCheck.js"></script>

