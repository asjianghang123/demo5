@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>GSM模板管理</h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-dashboard"></i>日常优化
		</li>
		<li>指标分析
		</li>
		<li><a href="GSMQuery">GSM指标查询</a>
		</li>
		<li class="active">GSM模板管理</li>
	</ol>
</section>
@endsection
@section('content')


<section class="content">
	<div class="row">
		<div class="col-sm-3">
			<div class='box'>
				<div class="box-header with-border">
					<h3 class="box-title">模板</h3>
					<div class="box-tools pull-right">
                        <div class="btn-group">
                        	<a class="btn btn-primary" onClick="addMode()" title="新增模板"><span class="fa">新建</span></a>
                    	</div>
                    	<div class="btn-group">
                    		<a class="btn btn-primary" onClick="deleteMode()" title="删除模板"><span class="fa">删除</span></a>
                    	</div>
                    	<div class="btn-group">    
                        	<a class="btn btn-primary" onClick="copyMode()" title="复制模板"><span class="fa">复制</span></a>
                		</div>
                	</div>
				</div>
				<div class="box-body">

					<div class="input-group">
			           	<input type="text" class="form-control" id="paramQueryMoErbs" aria-describedby="basic-addon1" placeholder="请输入模板名查询" />
		               	<span class="input-group-btn">
			                <button class="btn btn-default" type="button" onClick="searchGSMQuery()">
			                     &nbsp;<span class="glyphicon glyphicon-search btn-group-lg" aria-hidden="true"></span>
			                </button>
			                <button class="btn btn-default" type="button" onClick="clearGSMQuery()">
								&nbsp;<span class="glyphicon glyphicon-remove btn-group-lg" aria-hidden="true"></span>
							</button>
		               	</span>
		            </div>

					<br />
					<div class="form-group"  style="height:500px; overflow:auto;overflow-x:hidden">
						<div id="GSMQueryMoTree"></div>
					</div>
				</div>
			</div>	
		</div>
		<div class="col-sm-3">	
			<div class="box"> 
				<div class="box-header with-border">
					<h3 class="box-title">指标</h3>
					<div class="box-tools pull-right">
                        <div class="btn-group">
                        	<a class="btn btn-primary" onClick="elementUp()" title="上移指标"><span class="fa">上移</span></a>
                    	</div>
                    	<div class="btn-group">
                    		<a class="btn btn-primary" onClick="elementDown()" title="下移指标"><span class="fa">下移</span></a>
                    	</div>
                    	<div class="btn-group">    
                        	<a class="btn btn-primary" onClick="elementDelete()" title="删除指标"><span class="fa">删除</span></a>
                		</div>
                	</div>
				</div>
				<div class="box-body">
					<div class="form-group"  style="height:554px; overflow:auto;overflow-x:hidden">
						<input type="hidden" id="elementIds" value="">
						<div id="GSMElementTree"></div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class='box'>
				<div class="box-header with-border">
					<h3 class="box-title">公式</h3>
					<div class="box-tools pull-right">
                        <div class="btn-group">
                        	<a class="btn btn-primary" onClick="addFormula()" title="新建公式"><span class="fa">新建</span></a>
                    	</div>
                    	<div class="btn-group">
                    		<a class="btn btn-primary" onClick="deleteFormula()" title="删除公式"><span class="fa">删除</span></a>
                    	</div>
                	</div>
				</div>
				<div class="box-body">
					<div class="box-tools" style="float:left;">
                        <div class="btn-group">
                            <button type="button" class="btn  btn-default" onclick="elementAdd()">
		                        <i class="fa  fa-arrow-left"></i>添加到模板
		                    </button>
                        </div>
                    </div>
                    <div class="col-sm-8 pull-right" style="padding-right:0">
						<div class="input-group">
			           		<input type="text" class="form-control" id="formulaQuery" aria-describedby="basic-addon1" placeholder="请输入Name或者Formula查询" />
			               	<span class="input-group-btn">
				                <button class="btn btn-default" type="button" onClick="searchFormula()">
				                     &nbsp;<span class="glyphicon glyphicon-search btn-group-lg" aria-hidden="true"></span>
				                </button>
				                <button class="btn btn-default" type="button" onClick="clearFormula()">
									&nbsp;<span class="glyphicon glyphicon-remove btn-group-lg" aria-hidden="true"></span>
								</button>
			               	</span>
			            </div>
                    </div>
					

					<br />
					<div class="form-group"  style="height:500px;width:100%; overflow:auto;margin-top:34px" id="GSMFormulaTableDiv">
						<table id="GSMFormulaTable"></table>
					</div>
				</div>
			</div>	
		</div>
	</div>
</section>
<!-- 新增和修改公式弹出框 -->
<div class="modal fade" id="add_edit_formula">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">新建/修改</h8>
            </div>
			<form class="form-horizontal" role="form" id="formulaForm">
			<div class="modal-body text-center">

					<input type="hidden" name="formulaId" id="formulaId" value="">
					<div class="form-group">
						<label for="name" class="col-sm-2 col-sm-offset-2 control-label">Name：</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="name" id="name" placeholder="Name" maxlength="50">
						</div>
					</div>
					<div class="form-group">
						<label for="precision" class="col-sm-2 col-sm-offset-2 control-label">Precision：</label>
						<div class="col-sm-6">
							<input type="number" class="form-control" name="precision" id="precision" placeholder="Precision" maxlength="11">
						</div>
					</div>
					<div class="form-group">
						<label for="formula" class="col-sm-2 col-sm-offset-2 control-label">Formula:</label>
						<div class="col-sm-6">
							<textarea class="form-control" name="formula" id="formula" style="height : 150px;resize: none;" maxlength="3000"></textarea>
						</div>
					</div>
					
				
			</div>
			<div class="modal-footer">
				<button type="submit" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" onclick="updateFormula()">保存</button>
				<button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
			</div>
			</form>
		</div>
	</div>
</div>
<!-- 新增模板弹出框 -->
<div class="modal fade" id="add_mode">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">添加模板</h8>
            </div>
			<form class="form-horizontal" role="form" id="modeForm">
			<div class="modal-body text-center">

					<div class="form-group">
						<label for="modeName" class="col-sm-2 col-sm-offset-2 control-label">模板名称：</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="modeName" id="modeName" placeholder="模板名称" maxlength="50">
						</div>
					</div>
					<div class="form-group">
						<label for="modeDescription" class="col-sm-2 col-sm-offset-2 control-label">描述:</label>
						<div class="col-sm-6">
							<textarea class="form-control" name="modeDescription" id="modeDescription" style="height : 100px;resize: none;" maxlength="500"></textarea>
						</div>
					</div>
					
				
			</div>
			<div class="modal-footer">
				<button type="button" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" onclick="updateMode()">保存</button>
				<button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
			</div>
			</form>
		</div>
	</div>
</div>
<!-- 复制模板弹出框 -->
<div class="modal fade" id="copy_mode">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">复制模板</h8>
            </div>
			<form class="form-horizontal" role="form" id="modeForm_copy">
			<div class="modal-body text-center">
					<input type="hidden" value="" id="copyId" name="copyId">
					<div class="form-group">
						<label for="modeName_copy" class="col-sm-2 col-sm-offset-2 control-label">模板名称：</label>
						<div class="col-sm-6">
							<input type="text" class="form-control" name="modeName_copy" id="modeName_copy" placeholder="模板名称" maxlength="50">
						</div>
					</div>
					<div class="form-group">
						<label for="modeDescription_copy" class="col-sm-2 col-sm-offset-2 control-label">描述:</label>
						<div class="col-sm-6">
							<textarea class="form-control" name="modeDescription_copy" id="modeDescription_copy" style="height : 100px;resize: none;" maxlength="500"></textarea>
						</div>
					</div>
					
				
			</div>
			<div class="modal-footer">
				<button type="button" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" onclick="updateModeCopy()">复制</button>
				<button type="button" class="col-sm-2 btn btn-default" id="cancelBtn" data-dismiss="modal">取消</button>
			</div>
			</form>
		</div>
	</div>
</div>
@endsection


@section('scripts')
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
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!-- treegrid -->
<link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/easyui.css">
<link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/datagrid.css">
<link rel="stylesheet" href="dist/css/LTETemplateManage.css">
<script src="plugins/EasyUI/jquery.easyui.min.js"></script>

<!--bootstrapvalidator-->
<link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script>
@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/QueryAnalysis/GSMTemplateManage.js"></script>

