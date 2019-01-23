@extends('layouts.nav')

@section('content')
<section class="content-header">
	<h1>异系统加邻区分析</h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-dashboard"></i>专项研究
		</li>
		<li>邻区分析
		</li>
		<li class="active">异系统加邻区分析</li>
	</ol>
</section>
<section class="content">
	<div class="row">
		<div class="col-sm-12">
			
					<div class="box">
						<div class="box-header with-border">
							<h3 class="box-title">查询条件</h3>
							<div class="box-tools pull-right">
				                <div class="btn-group">
				                    <a class="btn btn-primary" onClick="openConfigInfo()"><span class="fa">筛选条件</span></a>
				                </div>
				            </div>
						</div>
						<div class="box-body">
							<form class="form-horizontal" role="form" id="queryForm">
								<div class="form-group">
								    <label class="col-sm-1 control-label">异系统类型</label>
                            		<div class="col-sm-3">
                                		<select id="type" class="form-control input-sm">
                                		    <option value='2G'>2G邻区</option>
											<option value='3G'>3G邻区</option>
										</select>
                            		</div>
									<label class="col-sm-1 control-label">城市</label>
                            		<div class="col-sm-3">
                                		<select id="city" class="form-control input-sm">
										</select>
                            		</div>
                            		<label class="col-sm-1 control-label">日期</label>
                            		<div class="col-sm-3">
                                		<input id="dateTime" class="form-control" type="text" value=""/> 
                            		</div>
								</div>
								<input type="hidden" id="input1" value="">		  	
								<input type="hidden" id="input2" value="">		  	
								<input type="hidden" id="input3" value="">		  	
								<input type="hidden" id="input4" value="">		  	
								<input type="hidden" id="input5" value="">	
								<input type="hidden" id="input6" value="">		  	
								<input type="hidden" id="input7" value="">
								<input type="hidden" id="input8" value="">		  	
								<input type="hidden" id="input9" value="">	 	  	
							</form>
						</div>
						<div class='box-footer' style="text-align:right;">
							<input id="filename" hidden='true' />
							<input id="filenameGSM" hidden='true' />
							<input id="filenameLte" hidden='true' />
							<!-- <a id="importWhiteList" type="submit"  class="btn btn-primary ladda-button" onclick="importWhiteList();return false;">导入白名单</a>
                             <a id="exportWhiteList" type="submit"  class="btn btn-primary ladda-button" onclick="exportWhiteList();return false;">导出白名单</a> -->
							<a id="search" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="doSearchGSM()"><span class="ladda-label">查询</span></a>
							<!-- <div hidden="true">
								<a id="save" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="fileSave()"><span class="ladda-label">保存</span></a> 
							</div> -->
							
						</div>	
					</div>
					<div class="box">
			            <div class="box-header with-border">
							<h3 class="box-title">查询数据</h3>
							<div class="box-tools pull-right">
								<!-- <a id="exportBtn" class="btn btn-primary ladda-button" data-color='red' onclick="fileSave()"  disabled="disabled">
								<span class="ladda-label">导出</span> -->
								</a>		
								<a id="export" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="exportAll()"><span class="ladda-label">导出</span></a>
				            </div>
						</div>
						<div class="box-body">
							<table id="GSMNeighTable"> 
							</table>
						</div>
		            </div>
				</div>
				
			</div>

</section>

<!-- 配置信息弹出框 -->
<div class="modal fade" id="config_information">
	<div class="modal-dialog" style="width:900px;">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">配置信息</h8>
            </div>
			<form class="form-horizontal" role="form" id="configForm">
			<div class="modal-body text-left row" style="margin-left:150px;">
			<!-- <div class="form-group col-md-12">
			(
			</div> -->
				<div class="form-group col-md-12">
		    		MRE : (呼叫比例（目标）>=
				    <input type="text" name="input1Temp" id="input1Temp" placeholder="" style="width:50px">
				    )   AND   (平均RSSI（目标）>=
				    <input type="text" name="input6Temp" id="input6Temp" placeholder="" style="width:40px">  AND   
				    平均RSRQ（主）>=
			    	<input type="text" name="input7Temp" id="input7Temp" placeholder="" style="width:40px">)
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" name="saveBtn" class="col-sm-1 col-sm-offset-11 btn btn-primary" id="saveBtn" onclick="updateConfigInfo()">确定</button>
			</div>
		</div>
		</form>
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
      	<input id="dataType" type="hidden" value="">
        <button type="button" class="col-sm-2 col-sm-offset-4 btn btn-primary" id="importBtn" onclick="importFile()">确定</button>
        <button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<style type="text/css">
	#loadSaveData {
    	cursor:pointer;
    	color:#97a0b3;
    }
    #loadSaveData:hover {
    	color: #606c84;
    }
</style>
<link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>
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
<!--input select-->
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>
<script type="text/javascript" src="dist/js/NetworkOptimization/NeighAnalysis.js"></script>
<!-- <script type="text/javascript" src="dist/js/NetworkOptimization/CDRNeighAnalysis.js"></script> -->
<link rel="stylesheet" href="dist/css/button.css">
<style type='text/css'>
	.datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
    background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
    border-color: #ffb733;
	}	
</style>
@endsection
