@extends('layouts.nav')
@section('content-header')
<section class="content-header">
		<h1>SQL语句查询</h1>
		<ol class="breadcrumb">
			<li><i class="fa fa-dashboard"></i>日常优化
			</li>
			<li>指标分析
			</li>
			<li class="active">SQL语句查询</li>
		</ol>
	</section>
@endsection
@section('content')
	
	<section class="content">
		<div class='row'>
			<div class='col-sm-3'>
				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title">模板</h3>
						<div class="box-tools pull-right">
							<input id='checkedType'  checked data-toggle="toggle" data-on="LTE" data-off="GSM" data-onstyle="info" data-offstyle="success" data-width="80" data-size="mini" type="checkbox">
							<div class="btn-group">
                        		<a id="newBuild" class="btn btn-primary" data-widget="add" data-color='red' data-style="expand-right" href="#"  onClick="newBuild('file')"><i class="fa">新建</i></a>
                    		</div>
	                        <div class="btn-group">
	                        	<a id="delete" class="btn btn-primary"  data-color='red' data-style="expand-right" href="#"  onClick="deleteMode('file')"><i class="fa">删除</i></a>
	                        </div>
                    	</div>
					</div>
					<div class="box-body">
					  	<div class="input-group">
					    	<input type="text" class="form-control" id="paramQueryMoErbs" placeholder="请输入参数查询" />
				    	 	<span class="input-group-btn">
								<button class="btn btn-default" type="button" onClick="searchCustomQuery()">
								&nbsp;<span class="glyphicon glyphicon-search btn-group-lg" aria-hidden="true"></span>
				                </button>
								<button class="btn btn-default" type="button" onClick="clearCustomQuery()">
								&nbsp;<span class="glyphicon glyphicon-remove btn-group-lg" aria-hidden="true"></span>
								</button>
							</span>
						</div>
					
						<br/>
				
					<!-- <div class="input-group" id="inputName">
						<input type="text" class="form-control" id="insertName" placeholder="请输入模板名" style="width:70%"/>
						<span class="input-group-btn" style="display:inline-block;">
							<button class="btn btn-primary" type="button" onClick="insertTable()">插入</button>
						</span>
					</div>
					 -->
						<div class="form-group" style="height:500px; overflow:auto;overflow-x:hidden">
					  		<div id="CustomQueryMoTree"></div>
						</div>
						<input type="hidden" value="" id="LTEFlag">
					</div>
				</div>
			</div>

			<div class="col-sm-9">
				<div class="box">
				    <div class="box-header with-border">
					    <h3 class="box-title">查询条件</h3>
				    </div>
					<div class="box-body">
						<!-- <div class="row"> -->
							<form class="form-inline" role="form">
							    <div class="form-group">
						            城市：
						        </div>
						        <div class="form-group">
							        <label class="sr-only"></label>
		    				        <p class="form-control-static" style="width: 160px;">
		    					        <select id="allCity" multiple="multiple" >
									    </select>
		    				        </p>					
					  	        </div>
							    <!-- <div class="form-group">
						            城市：
						        </div>
								<div class="form-group col-sm-3">
									
								</div> -->
								<!-- <a id="run" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="run('table')"><span class="ladda-label">运行</span></a> -->
								<!-- <a id="export" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="run('file')"><span class="ladda-label">导出</span></a>  -->
								<div hidden="true">
								<a id="save" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="fileSave('file')"><span class="ladda-label">保存</span></a> 
								</div>
							</form>
							<input id="CustomQueryfile" value='' hidden="true">
						<!-- </div> -->
					</div>	
					<div class="box-footer">
					<div class="pull-right">
						<div class="btn-group">
		                    <a id="search" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" onClick="doSearch('table')"><span class="ladda-label">查询</span></a>


		                    <a id="export" class="btn btn-primary ladda-button hidden" data-color='red' data-style="expand-right" onClick="doSearch('file')"><span class="ladda-label">导出</span></a>  
		                </div>
					</div>
				</div>				
				</div>
				<div class="box" hidden='true'>					
					<input id='customName' value='' />
					<input id='customTableName' value='' />
				</div>

				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title">查询数据</h3>
						<div class="box-tools pull-right">
							<!-- <span id="loadSaveData" class="glyphicon glyphicon-save" aria-hidden="true" onClick="fileSave('file')"></span>
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        	<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>       	 -->
                        	<a id="save" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" onClick="fileSave('file')"><span>导出</span></a>    
	                    </div>
					</div>
					<div class="box-body">
						<table id="CustomQueryTable">
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>	

<!--sql 弹出框 -->
<div class="modal fade" id="edit_LTE">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">查询语句</h8>
            </div>
		
			<div class="modal-body text-center">
				<textarea class="form-control" name="customContext" id="customContext"    value="请在此输入SQL语句(中文字符除外)。" style="height : 300px;resize: none;">
				</textarea>
			</div>

			<div class="modal-footer">
			<!-- 	<button type="submit" name="submit" class="col-sm-2 col-sm-offset-3 btn btn-primary" id="saveBtn" onclick="saveModeChange()">保存</button>
				<button type="button" class="col-sm-2 btn btn-primary" id="cancelBtn" data-dismiss="modal">取消</button>
				 <button type="button" id="delete" class="col-sm-2 btn btn-primary"  onClick="deleteMode('file')">删除模板</button> -->
				 <div class="pull-right">
					 <button type="submit" name="submit" class="btn btn-primary " id="saveBtn" onclick="saveModeChange()">保存</button>
					<button type="button" class="btn btn-primary" id="cancelBtn" data-dismiss="modal">取消</button>
					<!--  <button type="button" id="delete" class="btn btn-primary"  onClick="deleteMode('file')">删除模板</button> -->
				 </div>
			</div>

		</div>
	</div>
</div>

<!--新建模板 弹出框 -->

<div class="modal fade" id="inputName">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">请输入插入的模板名</h8>
                <input id='checkedType_Model'  checked data-toggle="toggle" data-on="LTE" data-off="GSM" data-onstyle="info" data-offstyle="success" data-width="80" data-size="mini" type="checkbox">
            </div>
		
			<div class="modal-body text-center">
				<input type="text" class="form-control" name="insertName" id="insertName" placeholder="请输入模板名" maxlength="30">
			<!-- 	<textarea class="form-control" name="insertName" id="insertName"  value="请输入模板名" style="height : 300px;resize: none;">
				</textarea> -->
			</div>

			<div class="modal-footer">
			<!-- 	<button type="submit" name="submit" class="col-sm-2 col-sm-offset-3 btn btn-primary" id="saveBtn" onclick="saveModeChange()">保存</button>
				<button type="button" class="col-sm-2 btn btn-primary" id="cancelBtn" data-dismiss="modal">取消</button>
				 <button type="button" id="delete" class="col-sm-2 btn btn-primary"  onClick="deleteMode('file')">删除模板</button> -->
				 <div class="pull-right">
					 <button type="submit" name="submit" class="btn btn-primary " onclick="insertTable()">插入</button>
					<button type="button" class="btn btn-primary"  data-dismiss="modal">取消</button>
					<!--  <button type="button" id="delete" class="btn btn-primary"  onClick="deleteMode('file')">删除模板</button> -->
				 </div>
			</div>

		</div>
	</div>
</div>


@endsection

@section('scripts')
<style type="text/css">
    .treeview span.indent{
    	margin:0;
    }
    #loadSaveData {
    	cursor:pointer;
    	color:#97a0b3;
    }
    #loadSaveData:hover {
    	color: #606c84;
    }
    /*table thead th,
    table tbody tr{
		width:auto; @important
	}
	table thead th > div,
	table tbody tr > div{
		white-space: nowrap;
	}*/
	.gj-grid-bootstrap-thead-cell div{
		width: 100%;
    	word-wrap: break-word;
	} 
</style>
<link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
<script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>

<!--input select-->
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

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

<link href="plugins/bootstrap-toggle/css/bootstrap-toggle.min.css" rel="stylesheet">
<script type="text/javascript" src="plugins/bootstrap-toggle/js/bootstrap-toggle.min.js"></script>
@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/QueryAnalysis/CustomQuery.js"></script>