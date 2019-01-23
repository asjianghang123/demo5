@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>LTE模板查询(本地)
		<small>查询方式：</small>
	</h1>
	<ol class="breadcrumb">
		<li><i class="fa fa-dashboard"></i>日常优化
		</li>
		<li>指标分析
		</li>
		<li class="active">LTE指标查询(本地)</li>
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
	                        <!-- <a class="btn btn-box-tool fa fa-wrench" href="LTETemplateManage"></a> -->
	                        <a class="btn btn-primary" href="LTETemplateManage"><span class="fa">管理</span></a>
                        </div>
                    </div>
				</div>
				<div class="box-body">

				<div class="input-group">
		           	<input type="text" class="form-control" id="paramQueryMoErbs" aria-describedby="basic-addon1" placeholder="请输入模板名查询" />
		               <span class="input-group-btn">
			                <button class="btn btn-default" type="button" onClick="searchLTEQuery()">
			                     &nbsp;<span class="glyphicon glyphicon-search btn-group-lg" aria-hidden="true"></span>
			                </button>
			                <button class="btn btn-default" type="button" onClick="clearLteQuery()">
								&nbsp;<span class="glyphicon glyphicon-remove btn-group-lg" aria-hidden="true"></span>
							</button>
		               	</span>
		            </div>
					<br />
					<div class="form-group"  style="height:600px; overflow:auto;overflow-x:hidden">
						<div id="LTEQueryMoTree"></div>
						<input type="hidden" id="customName" value="">
					</div>
				</div>
			</div>	
		</div>
		<div class="col-sm-9">	
			<div class="box"> 
				<div class="box-header with-border" style="height:35px">
					<h3 class="box-title" style="display:inline;float:left;margin-right:5px">查询条件</h3>
					<div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
				</div>
				<div class="box-body">     
					<form class="form-horizontal" role="form" id="queryForm">
						<div class="form-group">
							<label class="col-sm-2 control-label">区域维度</label>
                            <div class="col-sm-4">
                                <select id="locationDim" class="form-control input-sm">
									<option value='city'>城市</option>
									<option value='subNetwork'>子网</option>
									<option value='subNetworkGroup'>子网组</option>
									<option value='erbs'>基站</option>
									<option value='erbsGroup'>基站组</option>
									<option value='cell'>小区</option>
									<option value='cellGroup'>小区组</option>
								</select>
                            </div>
							<label class="col-sm-2 control-label">时间维度</label>
							<div class="col-sm-4">
								<select id="timeDim" class="form-control input-sm">
									<option value='day'>天</option>
									<option value='daygroup'>天组</option>
									<option value='hour'>小时</option>
									<option value='hourgroup'>小时组</option>
									<option value='quarter'>15分钟</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">起始日期</label>
                            <div class="col-sm-4">
                                <input id="startTime" class="form-control" type="text" value=""/>
                            </div>
                            <label class="col-sm-2 control-label">结束日期</label>
                            <div class="col-sm-4">
                                <input id="endTime" class="form-control" type="text" value=""/>
                            </div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">城市</label>
                            <div class="col-sm-4">
                                <select id="allCity" class="form-control input-sm" multiple="multiple">
								</select>
                            </div>
							<label class="col-sm-2 control-label">子网</label>
							<div class="col-sm-4">
								<select id="subNetworks" class="form-control" multiple="multiple">
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">基站</label>
                            <div class="col-sm-4">
                                <input id="erbsInput" class="form-control" type="text" value=""/>
                            </div>
                            <label class="col-sm-2 control-label">小区</label>
                            <div class="col-sm-4">
                                <div class="input-group">									 
					               	<input type="text" class="form-control" id="cellInput">
					               	<input type="file" class="hidden" name="fileImport" id="fileImport" onchange="toName(this)">
					               	<span class="input-group-btn">
					                  	<button class="btn btn-default" type="button" onclick="fileImport.click()">选择文件</button>
					               	</span>
					            </div>
                            </div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">小时</label>
                            <div class="col-sm-4">
                                <select id="hourSelect" class="form-control" multiple="multiple">
									<option value='0'>0</option>
									<option value='1'>1</option>
									<option value='2'>2</option>
									<option value='3'>3</option>
									<option value='4'>4</option>
									<option value='5'>5</option>
									<option value='6'>6</option>
									<option value='7'>7</option>
									<option value='8'>8</option>
									<option value='9'>9</option>
									<option value='10'>10</option>
									<option value='11'>11</option>
									<option value='12'>12</option>
									<option value='13'>13</option>
									<option value='14'>14</option>
									<option value='15'>15</option>
									<option value='16'>16</option>
									<option value='17'>17</option>
									<option value='18'>18</option>
									<option value='19'>19</option>
									<option value='20'>20</option>
									<option value='21'>21</option>
									<option value='22'>22</option>
									<option value='23'>23</option>
								</select>
                            </div>
							<label class="col-sm-2 control-label">15分钟</label>
							<div class="col-sm-4">
								<select id="quarterSelect" class="form-control" multiple="multiple">
									<option value='0'>0</option>
									<option value='15'>15</option>
									<option value='30'>30</option>
									<option value='45'>45</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">制式</label>
                            <div class="col-sm-4">
                                <select id="LTEFormat" class="form-control">
									<option>TDD</option>
									<option>FDD</option>
									<option>NBIOT</option>
								</select>
                            </div>
                           {{--  <label class="col-sm-2 control-label">模式</label>
                            <div class="col-sm-4">
                                <input id='checkedType' checked data-toggle="toggle" data-on="本地查询" data-off="在线查询" data-onstyle="info" data-offstyle="success" data-width="100" data-size="mini" type="checkbox">
                            </div> --}}
						</div>
					</form> 
					<!-- <table class="table">
							<tr>
								<td style="width:15%">区域维度</td>
								<th style="width:35%">
									<select id="locationDim" class="form-control input-sm">
										<option value='city'>城市</option>
										<option value='subNetwork'>子网</option>
										<option value='subNetworkGroup'>子网组</option>
										<option value='erbs'>基站</option>
										<option value='cell'>小区</option>
										<option value='cellGroup'>小区组</option>
									</select>
								</th>
								<td style="width:15%">时间维度</td>
								<th style="width:35%">				
									<select id="timeDim" class="form-control input-sm">
										<option value='day'>天</option>
										<option value='daygroup'>天组</option>
										<option value='hour'>小时</option>
										<option value='hourgroup'>小时组</option>
										<option value='quarter'>15分钟</option>
									</select>
								</th>
							</tr>
							<tr>
								<td>起始日期</td>
								<th>
									<div class="input-group input-group-md" style="width:100%">
										<input id="startTime" class="form-control" type="text" value=""/>
									</div>
								</th>
								<td>结束日期</td>
								<th>
									<div class="input-group input-group-md"  style="width:100%">
										<input id="endTime" class="form-control" type="text" value=""/>										
									</div>
								</th>
							</tr>
							<tr>
								<td>城市</td>
								<th>
									<select id="allCity" class="form-control input-sm" multiple="multiple">
									</select>   
								</th>
								<td>子网</td>
								<th>
									<select id="subNetworks" class="form-control" multiple="multiple">
									</select>
								</th>
							</tr>
							<tr>	
								<td>基站</td>
								<th>
									<div class="input-group input-group-md" style="width:100%">
										<input id="erbsInput" class="form-control" type="text" value=""/>
									</div>
								</th>
								<td>小区</td>
								<th>
									<div class="input-group input-group-md" style="width:100%">
										<input id="cellInput" class="form-control" type="text" value=""/>
									</div>
						
									<div class="input-group">									 
						               <input type="text" class="form-control" id="cellInput">
						               <input type="file" class="hidden" name="fileImport" id="fileImport" onchange="toName(this)">
						               <span class="input-group-btn">
						                  <button class="btn btn-default" type="button" onclick="fileImport.click()">选择文件</button>
						               </span>
						            </div>
								</th>
							</tr>
							<tr>
								<td>小时</td>
								<th>
									<select id="hourSelect" class="form-control" multiple="multiple">
										<option value='0'>0</option>
										<option value='1'>1</option>
										<option value='2'>2</option>
										<option value='3'>3</option>
										<option value='4'>4</option>
										<option value='5'>5</option>
										<option value='6'>6</option>
										<option value='7'>7</option>
										<option value='8'>8</option>
										<option value='9'>9</option>
										<option value='10'>10</option>
										<option value='11'>11</option>
										<option value='12'>12</option>
										<option value='13'>13</option>
										<option value='14'>14</option>
										<option value='15'>15</option>
										<option value='16'>16</option>
										<option value='17'>17</option>
										<option value='18'>18</option>
										<option value='19'>19</option>
										<option value='20'>20</option>
										<option value='21'>21</option>
										<option value='22'>22</option>
										<option value='23'>23</option>
									</select>
								</th>
								<td>15分钟</td>
								<th>
									<select id="quarterSelect" class="form-control" multiple="multiple">
										<option value='0'>0</option>
										<option value='15'>15</option>
										<option value='30'>30</option>
										<option value='45'>45</option>
									</select>
								</th>
							</tr>
							<tr>
								<td>模式&nbsp;&nbsp;&nbsp;</td>
								<th>
									<select id="LTEFormat" class="form-control">
										<option>TDD</option>
										<option>FDD</option>
									</select>
								</th>
							</tr>
						</table>		 -->					
					<input id="LTEQueryFile" value='' hidden="true" />		
				</div>
				<div class="box-footer" style="text-align:right;">
					<a id="search" class="btn btn-primary ladda-button" data-style="expand-right"><span class="ladda-label">查询</span></a>
					<!-- <a id="save" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="fileSave('file')"><span class="ladda-label">保存</span></a>  -->
					<a id="export" type="button" class="btn btn-primary ladda-button hidden" data-color='red' data-style="expand-right" onClick="doSearchLTE('file')"><span class="ladda-label">导出</span></a> 
				</div>
			</div>
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">查询数据</h3>
					<div class="box-tools pull-right">
						<a id="save" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right"><span>导出</span></a>
						<!-- <span id="loadSaveData" class="glyphicon glyphicon-save" aria-hidden="true" onClick="fileSave('file')"></span>
						                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->        
                    </div>
				</div>
				<div class="box-body">
		            <table id="LTEQueryTable"> 
		            </table>
	            </div>
			</div>
		</div>
	</div>
</section>
<!--查看指标弹出框 -->
<div class="modal fade" id="checkTemplate">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
                </button>
                <h8 class="modal-title" id="mtitle">指标</h8>
            </div>
			<div class="modal-body row">
				<div class="col-sm-6" style="height:400px;overflow:auto;">
					<input type="hidden" id="elementIds" value="">
					<div id="LTEElementTree"></div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<!-- <label class="control-label">公式:</label> -->
						<span class="form-control" id="formula" style="height:350px;word-break:break-all;overflow:auto;"></span>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<div class="pull-right">
					<button type="button" class="btn btn-primary"  data-dismiss="modal">关闭</button>
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
<link rel="stylesheet" href="plugins/loading/dist/ladda-themeless.min.css">
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!--fileStyle-->
<script type="text/javascript" src="plugins/ajaxFileUpload/ajaxfileupload.js"></script>

<!--bootatrap-toggle-->
<link href="plugins/bootstrap-toggle/css/bootstrap-toggle.min.css" rel="stylesheet">
<script type="text/javascript" src="plugins/bootstrap-toggle/js/bootstrap-toggle.min.js"></script>
<style type='text/css'>
	.datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
    background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
    border-color: #ffb733;
	}
	table tr td,
	table tr th{
		border:none!important;
	}
	label.control-label{
		font-weight:500;
		text-align:left!important;
		text-indent:5px;
	}
	.toggle.btn-xs{
		margin-top:5px;
	}
</style>
@endsection
<link rel="stylesheet" href="dist/css/button.css">
<script type="text/javascript" src="plugins/jQuery/jquery.min.js"></script>
<script type="text/javascript" src="dist/js/QueryAnalysis/LTEQueryLocal.js"></script>

