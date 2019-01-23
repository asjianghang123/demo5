@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>站点管理</h1>
	<ol class="breadcrumb">
		<li>
			<i class="fa fa-cogs"></i>系统管理
		</li>
		<li class="active">站点管理</li>
	</ol>
</section>
@endsection
@section('content')


<section class="content">
	<div class="row">
		<div class="col-sm-3">
			<div class='box'>
				<div class="box-header with-border">
					<div style="display:inline">
						<h3 class="box-title">城市</h3>
					</div>
					<div class="input-group" style="float:right;display:inline">
		               	<a id="import" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="importTemplate()"><span class="ladda-label">导入</span></a>
						<a id="exportTemplate" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="exportTemplate()"><span class="ladda-label">模板</span></a> 
		            </div>	
				</div>
				<div class="box-body">
					<ul class="nav nav-tabs" role="tablist">
						<li class="active"><a href="#table_tab_0" data-toggle="tab" onClick="switchTab('siteManage')"
							aria-expanded="false">4G</a></li>
						<!-- <li class=""><a href="#table_tab_1" data-toggle="tab" onClick="switchTab('3GSiteManage')"
							aria-expanded="false">3G</a></li> -->
						<li class=""><a href="#table_tab_2" data-toggle="tab" onClick="switchTab('2GSiteManage')"
							aria-expanded="false">2G</a></li>
						<li class=""><a href="#table_tab_3" data-toggle="tab" onClick="switchTab('otherSiteManage')"
						aria-expanded="false">友商</a></li>
					</ul>
					<div class="box-body">
						<div class="form-group"  style="height:600px; overflow:auto;overflow-x:hidden">
							<div id="cityTree"></div>
						</div>
						<input type="hidden" value="" id="cityValue">
						<input type="hidden" value="siteManage" id="siteType">
						<input type="hidden" value="" id="IP_Address">
						<input type="hidden" value="" id="majorActivities_Address">
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-9">
			<div class="box" id="abnormal_station_box">
				<div class="box-header with-border">
					<div style="display:inline">
						<h3 class="box-title">异常基站站点数据</h3>
					</div>
				</div>
				<div class="box-body">
					<div class="col-sm-6">
						<div class="col-sm-6">新站补全：</div>
						<div class="col-sm-6">
							<a href="#" class="abnormal_station_count" id="new_station_count" onClick="exportAbnormalStation('new_station')"></a>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-6">数据缺失：</div>
						<div class="col-sm-6">
							<a href="#" class="abnormal_station_count" id="data_loss_count" onClick="exportAbnormalStation('data_loss')"></a>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-6">重复站点：</div>
						<div class="col-sm-6">
							<a href="#" class="abnormal_station_count" id="repaeting_site_count" onClick="exportAbnormalStation('repaeting_site')"></a>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-6">非法格式：</div>
						<div class="col-sm-6">
							<a href="#" class="abnormal_station_count" id="illegal_format_count" onClick="exportAbnormalStation('illegal_format')"></a>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-6">跨站100米内同频检查：</div>
						<div class="col-sm-6">
							<a href="#" class="abnormal_station_count" id="cross_station_count" onClick="exportAbnormalStation('cross_station')"></a>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-6">站内同频小区方位角检查：</div>
						<div class="col-sm-6">
							<a href="#" class="abnormal_station_count" id="same_station_count" onClick="exportAbnormalStation('same_station')"></a>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-6">方位角检查：</div>
						<div class="col-sm-6">
							<a href="#" class="abnormal_station_count" id="azimuth_check_count" onClick="exportAbnormalStation('azimuth_check')"></a>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-6">经纬度检查：</div>
						<div class="col-sm-6">
							<a href="#" class="abnormal_station_count" id="lon_lat_check_count" onClick="exportAbnormalStation('lon_lat_check')"></a>
						</div>
					</div>
	            </div>
			</div>
			<div class="box">
				<div class="box-header with-border">
					<div style="display:inline">
						<h3 class="box-title">现网信息</h3>
					</div>
					<div class="input-group" style="float:right;display:inline">
		               	<a id="export" class="btn btn-primary ladda-button pull-right" data-color='red' data-style="expand-right" href="#"  onClick="exportSite()"><span class="ladda-label">导出</span></a>
		            </div>
					<input type="hidden" value="" id="searchText_site">
				</div>
				<div class="box-body">
		            <table id="siteTable">
		            </table>
	            </div>
			</div>
			<div class="box" id="newSiteLte_box">
				<div class="box-header with-border">
					<div style="display:inline">
						<h3 class="box-title">缺少基本信息的站点</h3>
					</div>
					<div class="input-group" style="float:right;display:inline">
						<a id="deleteNewSite" class="btn btn-primary ladda-button pull-right" data-color='red' data-style="expand-right" href="#"  onClick="deleteNewSite()"><span class="ladda-label">删除</span></a>
		               	<a id="exportNewSite" class="btn btn-primary ladda-button pull-right" data-color='red' data-style="expand-right" href="#"  onClick="exportNewSite()"><span class="ladda-label">导出</span></a>
		            </div>
		            <input type="hidden" value="" id="searchText">
				</div>
				<div class="box-body">
		            <table id="newSiteLteTable">
		            </table>
	            </div>
			</div>
			<div class="box" id="IP_box">
				<div class="box-header with-border">
					<div style="display:inline">
						<h3 class="box-title">IP信息(IP数量：<span id="IPNum"></span>)</h3>
					</div>
					<div class="input-group" style="float:right;display:inline">
						<a id="importIpList" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="importIpList()"><span class="ladda-label">导入</span></a>
                    	<a id="exportIpListFile" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="exportIpListFile()"><span class="ladda-label">导出</span></a>
		               	<a id="editIpListFile" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="editIpListFile()"><span class="ladda-label">编辑</span></a>
                    	<a id="saveIpListFile" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="saveIpListFile()"><span class="ladda-label">保存</span></a>
						<a id="cancelEdit" class="btn btn-default ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="cancelEdit()"><span class="ladda-label">取消</span></a> 
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
		            </div>
				</div>
				<div class="box-body" id="ipListBox">
					<!-- <div id="loading"></div> -->
		            <!-- <textarea class="form-control" name="ipListContent" id="ipListContent" disabled="disabled" style="height:400px;resize:none;"></textarea> -->
	            </div>
			</div>
			<!-- <div class="box" id="majorActivities_box">
				<div class="box-header with-border">
					<div style="display:inline">
						<h3 class="box-title">重大活动保障信息</h3>
					</div>
					<div class="input-group" style="float:right;display:inline">
		               	<a id="export" class="btn btn-primary ladda-button pull-right" data-color='red' data-style="expand-right" onClick="majorActivities()"><span class="ladda-label">导入</span></a>
		            </div>
		            <div class="input-group" style="float:right;display:inline">
		               	<a id="export" class="btn btn-primary ladda-button pull-right" data-color='red' data-style="expand-right" onClick="majorActivities_export()"><span class="ladda-label">导出</span></a>
		            </div>
				</div>
				<div class="box-body">
		            <table id="majorActivitiesTable">
		            </table>
	            </div>
			</div> -->
			<!-- <div class="box" id="majorActivities_2G_box">
				<div class="box-header with-border">
					<div style="display:inline">
						<h3 class="box-title">重大活动保障信息</h3>
					</div>
					<div class="input-group" style="float:right;display:inline">
		               	<a id="export" class="btn btn-primary ladda-button pull-right" data-color='red' data-style="expand-right" onClick="majorActivities()"><span class="ladda-label">导入</span></a>
		            </div>
		            <div class="input-group" style="float:right;display:inline">
		               	<a id="export" class="btn btn-primary ladda-button pull-right" data-color='red' data-style="expand-right" onClick="majorActivities_2G_export()"><span class="ladda-label">导出</span></a>
		            </div>
				</div>
				<div class="box-body">
		            <table id="majorActivities_2GTable">
		            </table>
	            </div>
			</div> -->
		</div>
	</div>
</section>

<!--重大活动保障参数配置弹出框-->
<div class="modal fade" id="p_majorActivities_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">参数-<span id='p_majorActivities'></span></h8>
            </div>
			<div class="modal-body col-sm-12" id='p_majorActivities_id'>		
				
			</div>
			<div class="modal-footer">
				<button type="button" class="col-sm-2 btn btn-default" id="next">下一步</button>
				<button type="button" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="p_majorActivities_importBtn" >确定</button>
				<button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
			</div>
		</div>
	</div>
</div>

<!--重大活动保障导入弹出框-->
<div class="modal fade" id="majorActivities_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">导入</h8>
            </div>
			<div class="modal-body col-sm-12">
					
				<input type="hidden" name="siteSign" id="siteSign" value="">
				<div class="col-sm-8 col-sm-offset-2">
					<div class="input-group">
		               <input type="text" class="form-control" id="majorActivities_fileImportName">
		               <input type="file" accept=".csv" class="hidden" name="majorActivities_fileImport" id="majorActivities_fileImport" onchange="majorActivities_toName(this)">
		               <span class="input-group-btn">
		                  <button class="btn btn-default" type="button" onclick="majorActivities_fileImport.click()">选择文件</button>
		               </span>
		            </div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="majorActivities_importBtn" >确定</button>
				<button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
			</div>
		</div>
	</div>
</div>


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
					
				<input type="hidden" name="siteSign" id="siteSign" value="">
				<div class="row">
					<div class="col-sm-8 col-sm-offset-4">
							<input id="replace" type="radio" name="importType" value="replace"/>替换
							<input id="apend" type="radio" name="importType" value="apend" checked="checked"/>新增
					</div>
				</div>
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
				<button type="button" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="importBtn" >确定</button>
				<button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
			</div>
		</div>
	</div>
</div>

<!-- 导入弹出框 -->
<div class="modal fade" id="ipListFile_import_modal">
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
		               <input type="text" class="form-control" id="ipListFile_fileImportName">
		               <input type="file" accept=".txt" class="hidden" name="ipListFile_fileImport" id="ipListFile_fileImport" onchange="ipListFile_toName(this)">
		               <span class="input-group-btn">
		                  <button class="btn btn-default" type="button" onclick="ipListFile_fileImport.click()">选择文件</button>
		               </span>
		            </div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="ipListFile_importBtn" >确定</button>
				<button type="button" class="col-sm-2 btn btn-default" id="ipListFile_cancelBtn" data-dismiss="modal">取消</button>
			</div>
		</div>
	</div>
</div>

@endsection


@section('scripts')


<!-- <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script> -->

<!--input select-->
<!-- <script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" /> -->

<!--treeview-->
<script type="text/javascript" src="plugins/treeview/bootstrap-treeview.min.js"></script>

<!--datatables-->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="plugins/datatables/grid.js"></script>
<link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

<!-- bootstrap-table-->
<link href="plugins/bootstrap-table/bootstrap-table.css" rel="stylesheet" />
<script type="text/javascript" src="plugins/bootstrap-table/bootstrap-table.js"></script>
<script type="text/javascript" src="plugins/bootstrap-table/locale/bootstrap-table-zh-CN.js"></script>

<!--loading-->
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!--bootstrapvalidator-->
<link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script>


<script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>
@endsection

<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>


<script type="text/javascript" src="dist/js/systemManage/siteManage.js"></script>
<style>
.fixed-table-body{
	height:auto !important;
}
#siteTable td{
		width:100%;
		white-space:nowrap;
		overflow:hidden;
		text-overflow:ellipsis;
	}
#newSiteLteTable td{
		width:100%;
		white-space:nowrap;
		overflow:hidden;
		text-overflow:ellipsis;
	}
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

