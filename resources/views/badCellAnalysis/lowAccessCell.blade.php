@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>低接入小区</h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i>日常优化</a>
		</li>
		<li><a href="#"><i class="fa fa-dashboard"></i>差小区分析</a>
		</li>
		<li class="active"><a href="#">低接入小区</a>
		</li>
	</ol>
</section>
@endsection
@section('content')



<section class="content">
	<div class="row">
		<div class="col-sm-12">
			<div class='box'>
				<div class="box-header with-border">
					<h3 class="box-title">查询条件</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown" onClick="openConfigInfo()">
	                    		<i class="fa fa-wrench"></i>
	                    	</button>
	                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                    </div>
				</div>
				<div class="box-body">

					<div class="row">
						<div class="col-md-1">城市</div>
						<div class="col-md-3">
							<select id="allCity" class="form-control input-sm" multiple="multiple">
							</select>
						</div>
					</div>	

					<table hidden class="table">
									<tr>
										
										<td style="width: 66px;">城市</td>
										<th>
											<select id="allCity" class="form-control input-sm" multiple="multiple">
											</select>   
										</th>	

										<td>小区</td>
										<th>
											<div class="input-group input-group-md" style="width:100%">
												<input id="cellInput" class="form-control" type="text" value=""/>
											</div>
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
										<td>小时</td>
										<th>
											<select id="allHour" class="form-control" multiple="multiple">
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
									</tr>
								</table>
				</div>
				<div class="box-footer" style="text-align:right">
					
						<a id="search" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="doSearchbadCell('table','低接入小区')"><span class="ladda-label ">查询</span></a>
						
						<input id="badCellFile" value='' hidden="true" />	
						<input id="badCellFilevolte" value='' hidden="true" />
						<input id='inputCategory' value='lowAccessCell' hidden="true" />
						<input id ="tableChoose" value='lowAccessCell' hidden="true" />
						<input id="chooseTable" value='lowAccessCell' hidden="true" />
						<input id="badCellFileIndex" value='lowAccessCell' hidden="true" />
						<input id="baselineFileIndex" value='' hidden="true" />
					
				</div>
			</div>
			<div class="box">
				<div class='box-header with-border'>
					<h3 class="box-title">小区列表</h3>
					<div class="box-tools pull-right">
	                    <a id="export" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="doSearchbadCell('file','低接入小区')"><span class="ladda-label">导出</span></a>
	                </div>
				</div>
				<div class="box-body">
					<div class="table-responsive">
						
						<table id="badCellTable">
						</table>
					</div>
				</div>	
			</div>
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">volte小区列表</h3>
					<div class="box-tools pull-right">
						<a id="volteexport" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="doSearchbadCell('filevolte','低接入小区')"><span class="ladda-label">导出</span></a>
					</div>
				</div>
				<div class="box-body">
					<div class="table-responsive">
						<table id="badCellTablevolte">
						</table>
					</div>
				</div>	
			</div>
			
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">诊断报告</h3>
					<div class="box-tools pull-right">
	                    <!-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button> -->
	                    <a id='openCtrJumpBtn' class="btn btn-primary ladda-button"  data-color='red' data-style="expand-right" onclick="openCtrJump()"><span class="ladda-label">CTR入库</span></a>
	                    <a class="btn btn-primary" onclick="openMapModal()">地理化分析</a>
	                </div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="box">
							<div class="box-header">
								<h3 class="box-title">极地图</h3>
							</div>
							<div class="box-body">
								<div id="container"></div>
								<div class="zhaozi" id="jiditu_zhaozi"></div>
								<div class="loadingImg text-center" id="jiditu_loadingImg">
									<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
								</div>
							</div>
						</div>	
					</div>
					<div class="col-md-8">
						<div class="box">
							<div class="box-header">
								<h3 class="box-title">诊断数据</h3>
							</div>
							<div class="box-body">
								<div class="row">
									<div class="col-md-4">
										<div class="panel-group">
											<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a data-toggle="collapse" data-parent="#accordion" 
														   href="#collapseOne" style="padding-left: 10px;">
															<span id='currentAlarmNum' style="left: 24px;top: 11px; position: absolute;"></span>当前告警
														</a>
													</h4>
												</div>
												<div id="collapseOne" class="panel-collapse collapse in">
													<div class="panel-body">
														<form class="form-horizontal">
															<div class="form-group">
																<label for="currentAlarm" class="col-sm-7 control-label" style="text-align: center;">告警数量:</label>
																<div class="col-sm-5">
															      	<input type="text" class="form-control" id="currentAlarm" readonly="readonly">
															    </div>
															</div>
														</form>											
													</div>
												</div>
											</div>
										</div>
										<div class="loadingImg text-center" id="alarm_loadingImg" style="top:40%">
											<span><i class="fa fa-spinner fa-pulse fa-fw"></i></span>
										</div>							
									</div>
									<div class="col-md-4">
										<div class="panel-group">
											<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a data-toggle="collapse" data-parent="#accordion" 
														   href="#collapseTwo" style="padding-left: 10px;">
															<span id='AvgPRBNum' style="left: 24px;top: 11px; position: absolute;"></span>干扰
														</a>
													</h4>
												</div>
												<div id="collapseTwo" class="panel-collapse collapse in">
													<div class="panel-body">
														<form class="form-horizontal">
															<div class="form-group" style="margin: 0">
																<label for="AvgPRB" class="col-sm-5 control-label" style="text-align: center;">平均PRB:</label>
																<div class="col-sm-7">
															      	<input type="text" class="form-control" id="AvgPRB" readonly="readonly">
															    </div>
															     <div id="AvgPRB_head" style="display: none">
														    	<label id="AvgPRB_lab" class="col-sm-12 control-label" style="text-align: center;"></label>
														     </div>
															</div>
														</form>
													</div>
												</div>
											</div>
										</div>
										<div class="loadingImg text-center" id="avgPRB_loadingImg" style="top:40%">
											<span><i class="fa fa-spinner fa-pulse fa-fw"></i></span>
										</div>	
									</div>
									<div class="col-md-4">
										<div class="panel-group">
											<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a data-toggle="collapse" data-parent="#accordion" 
														   href="#collapseThree" style="padding-left: 10px;">
															<span id='less116ProportionNum' style="left: 24px;top: 11px; position: absolute;"></span>弱覆盖
														</a>
													</h4>
												</div>
												<div id="collapseThree" class="panel-collapse collapse in">
												<div class="panel-body">														
													<form class="form-horizontal">
														<div class="form-group" style="margin: 0;">
															<label for="less116Proportion" class="col-sm-7 control-label" style="text-align: center;padding:8px 0 0 0">RSRP<-116的比例:</label>
															<div class="col-sm-5">
														      	<input type="text" class="form-control" id="less116Proportion" readonly="readonly" style="padding: 0">
														    </div>
														    <label for="avgTA" class="col-sm-7 control-label" style="text-align: center;">avgTA:</label>
															<div class="col-sm-5">
														      	<input type="text" class="form-control" id="avgTA" readonly="readonly">
														    </div>
														</div>
													</form>
												</div>
											</div>
											</div>
										</div>
										<div class="loadingImg text-center" id="weakCocer_loadingImg" style="top:40%">
											<span><i class="fa fa-spinner fa-pulse fa-fw"></i></span>
										</div>
									</div>
									<div class="col-md-4">
										<div class="panel-group">
											<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a data-toggle="collapse" data-parent="#accordion" 
														   href="#collapseThree_1" style="padding-left: 10px;">
															<span id='parameterNum' style="left: 24px;top: 11px; position: absolute;"></span>参数
														</a>
													</h4>
												</div>
												<div id="collapseThree_1" class="panel-collapse collapse in">
													<div class="panel-body" id="parameter_body">														
														<form class="form-horizontal">
															<div class="form-group">
																<label for="parameter" class="col-sm-5 control-label" style="text-align: center;">参数:	</label>
																<div class="col-sm-5">
																	<input type="text" id="parameter" readonly="readonly" class="form-control">
																</div>
															    <label for="featureState" class="col-sm-12 control-label" style="text-align: center;display: none" id="featureState_label">featureState:<input type="text"  id="featureState" readonly="readonly" style="width:130px" value='1 (ACTIVATED)'></label>
														    	<label for="licenseState" class="col-sm-12 control-label" style="text-align: center;display: none" id="licenseState_label">licenseState:<input type="text"  id="licenseState" readonly="readonly" style="width: 130px"></label>
														    	<label for="srUser" class="col-sm-12 control-label" style="text-align: center;display: none" id="srUser_label">SR USER参数:<input type="text"  id="srUser" readonly="readonly" style="width: 130px;padding-left:25px"></label>
																
															      	
															    
															</div>
														</form>
													</div>
												</div>
											</div>
										</div>
										<div class="loadingImg text-center" id="parameter_loadingImg" style="top:40%">
											<span><i class="fa fa-spinner fa-pulse fa-fw"></i></span>
										</div>
									</div>
								
									<div class="col-md-4">
										<div class="panel-group">
											<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a data-toggle="collapse" data-parent="#accordion" 
														   href="#collapseFour" style="padding-left: 10px;">
															<span id='overlapCoverNum' style="left: 24px;top: 11px; position: absolute;"></span>重叠覆盖
														</a>
													</h4>
												</div>
												<div id="collapseFour" class="panel-collapse collapse in">
													<div class="panel-body">
														<form class="form-horizontal">
															<div class="form-group">
																<label for="overlapCover" class="col-sm-7 control-label" style="text-align: center;">重叠覆盖度:</label>
																<div class="col-sm-5"> 
															      	<input type="text" class="form-control" id="overlapCover" readonly="readonly">
															    </div>
															</div>
														</form>
													</div>
												</div>
											</div>
										</div>
										<div class="loadingImg text-center" id="overlapCover_loadingImg" style="top:40%">
											<span><i class="fa fa-spinner fa-pulse fa-fw"></i></span>
										</div>
									</div>
									<div class="col-md-4">
										<div class="panel-group">
											<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a data-toggle="collapse" data-parent="#accordion" 
														   href="#collapseSix" style="padding-left: 10px;">
															<span id='needAddNeighNum' style="left: 24px;top: 11px; position: absolute;"></span>邻区
														</a>
													</h4>
												</div>
												<div id="collapseSix" class="panel-collapse collapse in">
													<div class="panel-body">
														<form class="form-horizontal">
															<div class="form-group">
																<label for="needAddNeigh" class="col-sm-7 control-label" style="text-align: center;">需要加邻区数量:</label>
																<div class="col-sm-5">
															      	<input type="text" class="form-control" id="needAddNeigh" readonly="readonly">
															    </div>
															</div>
														</form>
													</div>
												</div>
											</div>
										</div>
										<div class="loadingImg text-center" id="needAddNeigh_loadingImg" style="top:40%">
											<span><i class="fa fa-spinner fa-pulse fa-fw"></i></span>
										</div>
									</div>
									<div class="col-md-4">
										<div class="panel-group">
											<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a data-toggle="collapse" data-parent="#accordion" 
														   href="#collapseFive" style="padding-left: 10px;">
															<span id='less155ProportionNum' style="left: 24px;top: 11px; position: absolute;"></span>质差
														</a>
													</h4>
												</div>
												<div id="collapseFive" class="panel-collapse collapse in">
													<div class="panel-body">
														<form class="form-horizontal">
															<div class="form-group" style="margin: 0">
																<label for="less155Proportion" class="col-sm-8 control-label" style="text-align: center;padding: 0">RSRQ<-15.5的比例:</label>
																<div class="col-sm-4">
															      	<input type="text" class="form-control" id="less155Proportion" readonly="readonly" style="padding: 0">
															    </div>
															    	<label for="cqi" class="col-sm-8 control-label" style="text-align: center;">下行CQI<3的比例:</label>
																	<div class="col-sm-4">
																      	<input type="text" class="form-control" id="cqi" readonly="readonly" style="padding: 0">
																    </div>
															</div>
														</form>
													</div>
												</div>
											</div>
										</div>
										<div class="loadingImg text-center" id="zhicha_loadingImg" style="top:40%">
											<span><i class="fa fa-spinner fa-pulse fa-fw"></i></span>
										</div>
									</div>
									<div class="col-md-4">
											<div class="panel-group">
												<div class="panel panel-default">
													<div class="panel-heading">
														<h4 class="panel-title">
															<a data-toggle="collapse" data-parent="#accordion" 
															   href="#collapseThree_2" style="padding-left: 10px;">
																<span id='highTrafficNum' style="left: 24px;top: 11px; position: absolute;"></span>最高RRC用户数
															</a>
														</h4>
													</div>
													<div id="collapseThree_2" class="panel-collapse collapse in">
														<div class="panel-body" style="height:99px">		<form class="form-horizontal">
																<div class="form-group">
																	<label for="highTraffic" class="col-sm-7 control-label" style="text-align: center;">RRC数量:</label>
																	<div class="col-sm-5">
																      	<input type="text" class="form-control" id="highTraffic" readonly="readonly">
																    </div>
																</div>
															</form>
														</div>
													</div>
												</div>
										
										<div class="loadingImg text-center" id="highTraffic_loadingImg" style="top:40%">
											<span><i class="fa fa-spinner fa-pulse fa-fw"></i></span>
										</div>
									</div>
									</div>
									<div class="col-md-4">
											<div class="panel-group">
												<div class="panel panel-default">
													<div class="panel-heading">
														<h4 class="panel-title">
															<a data-toggle="collapse" data-parent="#accordion" 
															   href="#collapseThree_3" style="padding-left: 10px;">
																<span id='highTrafficNum2' style="left: 24px;top: 11px; position: absolute;"></span>高话务
															</a>
														</h4>
													</div>
													<div id="collapseThree_3" class="panel-collapse collapse in">
														<div class="panel-body" style="height:99px">		<form class="form-horizontal">
																<div class="form-group">
																	<label for="highTraffic2" class="col-sm-0 control-label" style="text-align: center;"></label>
																	<div class="col-sm-9">
																      	<input type="text" class="form-control" id="highTraffic2" readonly="readonly"
																      	>
																    </div>
																</div>
															</form>
														</div>
													</div>
												</div>
										
										<div class="loadingImg text-center" id="highTraffic_loadingImg" style="top:40%">
											<span><i class="fa fa-spinner fa-pulse fa-fw"></i></span>
										</div>
									</div>
									</div>
								</div>
							</div>
						</div>	
					</div>
					
					<!-- 0524 col-md-12变成6-->
					<div class="col-md-6">
						<div class="box">
							<div class="box-header">
								<h3 class="box-title">RRC失败原因值分布</h3>
								<div class="btn-div pull-right">
									<div class="btn-group">
			                         	<a type="button" class="btn" title="图" onclick="switchTab(table_tab_1,table_tab_0,'chart')">
											<i class="fa fa-picture-o"></i>
										</a>  
										<a type="button" class="btn" title="表" onclick="switchTab(table_tab_0,table_tab_1,'table')">
											<i class="fa fa-bars"></i>
										</a> 
					                </div>
								</div>
							</div>
							<div class="box-body">
								<div class="tabs tab-content" id="table_chart" >

									<div class=" tab-pane active" id="table_tab_0">
										<table id="rrcResultTable">
							            </table>
							            <input type="hidden" id="selectedResult">
							        </div>
									<div class=" tab-pane" id="table_tab_1" style="maxheight:400px;overflow:auto;">
										<div id="rrcResultContainer"></div>
					            		<!-- <button id="backBtn" class="btn btn-default" style="position:absolute;top:65px;right:60px;display:none;">◁ Back to previous</button> -->
									</div>
							    </div>
							</div>
						</div>	
					</div>

					<!-- 0524 去掉display-->
					<div class="col-md-6">
						<div class="box">
							<div class="box-header">
								<h3 class="box-title">ERAB失败原因值分布</h3>
								<div class="btn-div pull-right">
									<div class="btn-group">
			                         	<a type="button" class="btn" title="图" onclick="switchTab_RRCC(table_tab_3,table_tab_2,'chart')">
											<i class="fa fa-picture-o"></i>
										</a>  
										<a type="button" class="btn" title="表" onclick="switchTab_RRCC(table_tab_2,table_tab_3,'table')">
											<i class="fa fa-bars"></i>
										</a> 
					                </div>
								</div>
							</div>
							<div class="box-body">
								<div class="tabs tab-content" id="table_chart" >

									<div class=" tab-pane" id="table_tab_2">
										<table id="rrcResultTable_RRCC">
							            </table>
							            <input type="hidden" id="selectedResult_RRCC">
							        </div>
									<div class=" tab-pane active" id="table_tab_3" style="maxheight:400px;overflow:auto;">
										<div id="rrcResultContainer_RRCC"></div>
									</div>
							    </div>
							</div>
						</div>	
					</div>
			
					<!-- <div class="zhaozi" id="jiditu_zhaozi"></div>
					<div class="loadingImg text-center" id="jiditu_loadingImg">
						<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
					</div> -->
				</div>				
			</div>

			<div class="box"><!--0524-->
				<div class="box-header">
					<h3 class="box-title">Counter失败原因值分布</h3>
					<div class="box-tools pull-right">
	                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                </div>
				</div>
				<div class="box-body">
					<div id="counterLoseResultDistribution"></div>
				</div>
			</div>
			<!-- <div class="box">
				<div class="box-header">
					<h3 class="box-title">诊断报告</h3>
					<div class="box-tools pull-right">
	                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                </div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-4">
							<div class="panel-group">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#accordion" 
											   href="#collapseOne">
												告警类
											</a>
										</h4>
									</div>
									<div id="collapseOne" class="panel-collapse collapse in">
										<div class="panel-body">
											<a href="#alarmBox"><div class="col-md-6"><span id='erbsAlarmNumSpan'></span>&nbsp;&nbsp;基站级告警数量：</div><div class="col-md-6"><input alt="全天" title="全天" type="text" id='erbsAlarmNum' style="width: 60px" value="全天" /><input alt="差时" title="差时" type="text" id='erbsAlarmNumHour' style="width: 60px" value="差时" /></div></a>
											<a href="#alarmBox"><div class="col-md-6"><span id='alarmNumSpan'></span>&nbsp;&nbsp;小区级告警数量：</div><div class="col-md-6"><input alt="全天" title="全天" type="text" id='alarmNum' style="width: 60px" value="全天" /><input alt="差时" title="差时" type="text" id='alarmNumHour' style="width: 60px" value="差时" /></div></a>
										</div>
									</div>
								</div>
							</div>
						</div>
							
						<div class="col-md-4">
							<div class="panel-group">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#accordion" 
											   href="#collapseTwo">
												邻区类
											</a>
										</h4>
									</div>
									<div id="collapseTwo" class="panel-collapse collapse in">
										<div class="panel-body">
											<a href="#table_tab_0"><div class="col-md-8"><span id='LteNumSpan'></span>&nbsp;&nbsp;建议补4G邻区数量：</div><div class="col-md-4"><input type="text" id='LteNum' style="width: 50px" value="" /></div></a>	
											<a href="#table_tab_0"><div class="col-md-8"><span id='GsmNumSpan'></span>&nbsp;&nbsp;建议补2G邻区数量：</div><div class="col-md-4"><input type="text" id='GsmNum' style="width: 50px" value="" /></div></a>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="panel-group">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#accordion" 
											   href="#collapseThree">
												覆盖类
											</a>
										</h4>
									</div>
									<div id="collapseThree" class="panel-collapse collapse in">
										<div class="panel-body">
											<a href="#weakCoverBox"><div class="col-md-8"><span id='weakCoverNumSpan'></span>&nbsp;&nbsp;弱覆盖小区频次：</div><div class="col-md-4"><input type="text" id='weakCoverNum' style="width: 50px" value="" /></div></a>
											<a href="#weakCoverBox"><div class="col-md-8"><span id='overlapCeakCoverNumSpan'></span>&nbsp;&nbsp;重叠覆盖小区频次：</div><div class="col-md-4"><input type="text" id='overlapCeakCoverNum' style="width: 50px" value="" /></div></a>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="panel-group">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#accordion" 
											   href="#collapseFour">
												干扰类
											</a>
										</h4>
									</div>
									<div id="collapseFour" class="panel-collapse collapse in">
										<div class="panel-body">
											<a href="#interfereBox"><div class="col-md-6"><span id='highInterfereNumSpan'></span>&nbsp;&nbsp;高干扰小区频次：</div><div class="col-md-6"><input alt="全天" title="全天" type="text" id='highInterfereNum' style="width: 60px" value="全天" /><input alt="差时" title="差时" type="text" id='highInterfereNumHour' style="width: 60px" value="差时" /></div></a>
											<a href="#prbInterfereBox"><div class="col-md-6"><span id='prbHighInterfereNumSpan'></span>&nbsp;&nbsp;PRB干扰：</div><div class="col-md-6"><input alt="全天" title="全天" type="text" id='prbHighInterfereNum' style="width: 60px" value="全天" /><input alt="差时" title="差时" type="text" id='prbHighInterfereNumHour' style="width: 60px" value="差时" /></div></a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="panel-group">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#accordion" 
											   href="#collapseFour">
												PCI类
											</a>
										</h4>
									</div>
									<div id="collapseFour" class="panel-collapse collapse in">
										<div class="panel-body">
											<a href="#"><div class="col-md-8"><span id='firstOrderConflictNumSpan'></span>&nbsp;&nbsp;一阶冲突：</div><div class="col-md-4"><input type="text" id='firstOrderConflictNum' style="width: 50px" value="" /></div></a>
											<a href="#"><div class="col-md-8"><span id='secondOrderConflictNumSpan'></span>&nbsp;&nbsp;二阶冲突：</div><div class="col-md-4"><input type="text" id='secondOrderConflictNum' style="width: 50px" value="" /></div></a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
				</div>
			</div> -->

			<div class="row">
				<div class="col-md-6">
					<div class="box">
						<div class="box-header">
							<h3 class="box-title">维度相关性</h3>
							<span id="wirelessCallRate_date_"></span>
						</div>
						<div class="box-body">
							<div id="container_Relevance"></div>
							<span id='relevance_backups' hidden="hidden"></span>
							<div class="zhaozi" id="relevance_zhaozi"></div>
							<div class="loadingImg text-center" id="relevance_loadingImg" style="top: 10%">
								<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="box">
						<div class="box-header">
							<h3 class="box-title">指标相关性</h3>
							<span id="wirelessCallRate_date"></span>
							<!-- <input type="text" class="form-control" id="wirelessCallRate_date" readonly="readonly"> -->
						</div>
						<div class="box-body">
							<div id="container_Relevance_"></div>
							<!-- <span id='relevance_backups' hidden="hidden"></span> -->
							<div class="zhaozi" id="relevance_zhaozi_"></div>
							<div class="loadingImg text-center" id="relevance_loadingImg_" style="top: 10%">
								<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
							</div>
							<!-- <form class="form-horizontal">
								<div class="form-group">
									<label for="wirelessCallRate_interfere" class="col-sm-6 control-label" style="text-align: center;">无线接通率&干扰:</label>
									<div class="col-sm-6">
								      	<input type="text" class="form-control" id="wirelessCallRate_interfere" readonly="readonly">
								      	<div class="loadingImg text-center" id="wirelessCallRate_interfere_loadingImg" style="top:10%">
											<span><i class="fa fa-spinner fa-pulse fa-fw"></i></span>
										</div>
								    </div>
								    <label for="wirelessCallRate_zhicha" class="col-sm-6 control-label" style="text-align: center;">无线接通率&质差:</label>
									<div class="col-sm-6">
								      	<input type="text" class="form-control" id="wirelessCallRate_zhicha" readonly="readonly">
								      	<div class="loadingImg text-center" id="wirelessCallRate_zhicha_loadingImg" style="top:10%">
											<span><i class="fa fa-spinner fa-pulse fa-fw"></i></span>
										</div>
								    </div>
								    <label for="wirelessCallRate_RRCEstSucc" class="col-sm-6 control-label" style="text-align: center;">无线接通率&RRC建立成功率:</label>
									<div class="col-sm-6">
								      	<input type="text" class="form-control" id="wirelessCallRate_RRCEstSucc" readonly="readonly">
								      	<div class="loadingImg text-center" id="wirelessCallRate_RRCEstSucc_loadingImg" style="top:10%">
											<span><i class="fa fa-spinner fa-pulse fa-fw"></i></span>
										</div>
								    </div>
								    <label for="wirelessCallRate_ERABEstSucc" class="col-sm-6 control-label" style="text-align: center;">无线接通率&ERAB建立成功率:</label>
									<div class="col-sm-6">
								      	<input type="text" class="form-control" id="wirelessCallRate_ERABEstSucc" readonly="readonly">
								      	<div class="loadingImg text-center" id="wirelessCallRate_ERABEstSucc_loadingImg" style="top:10%">
											<span><i class="fa fa-spinner fa-pulse fa-fw"></i></span>
										</div>
								    </div>
								</div>
							</form>	 -->
						</div>
					</div>
				</div>
				
				<!-- <div class="col-md-8">
					<div class="box">
						<div class="box-header">
							<h3 class="box-title">相关趋势</h3>
						</div>
						<div class="box-body">
							<div id="trendContainer"></div>
							<div class="zhaozi" id="trend_zhaozi"></div>
							<div class="loadingImg text-center" id="trend_loadingImg" style="top: 10%">
								<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
							</div>
						</div>
					</div>
				</div> -->
			</div>

			<div class="box" >
				<div class="box-header">
					<h3 class="box-title">指标</h3>
					<div class="box-tools pull-right">
						<span id="loadSaveData" class="glyphicon glyphicon-save" aria-hidden="true" onClick="fileSave()"></span>
	                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                </div>
				</div>
				<!-- <div class="box-body">  -->
				<div class="nav-tabs-custom">
                    <ul id="getfirstTab" class="nav nav-tabs pull-left">
                        <li class="active"><a href="#indexTrend" data-toggle="tab">指标趋势</a></li>
                        <li><a href="#indexDetails" data-toggle="tab">指标详情</a></li>
                    </ul>
                    <div class="tabs tab-content">
                        <div class="tab-pane active" id="indexTrend">  <!-- style="position: relative;height: 500px" -->
                            <div class="box-body" >
                            	<form class="form-horizontal" role="form" id="lowAccessForm" hidden="hidden">
                            		<div class="form-group">
                            			<br /><br />
                            			<label for="worstCellChartPrimaryAxisType" class="col-sm-1 control-label">主轴</label>
										<div class="col-sm-5" >
											<select class="form-control" id="worstCellChartPrimaryAxisType" multiple="multiple" hidden="hidden">
												<option value="无线接通率" selected="selected" disabled="disabled">无线接通率</option>
												<option value="RRC建立成功率" selected="selected" disabled="disabled">RRC建立成功率</option>
												<option value="ERAB建立成功率" selected="selected" disabled="disabled">ERAB建立成功率</option>
											</select>
										</div>
										<label for="worstCellChartAuxiliaryAxisType" class="col-sm-1 control-label">辅轴</label>
										<div class="col-sm-5">
											<select id="worstCellChartAuxiliaryAxisType" class="form-control" multiple="multiple" hidden="hidden">
												<option value="干扰" selected="selected">干扰</option>
												<option value="质差" selected="selected">质差</option>
												<!-- <option value="RRC建立失败次数" selected="selected">RRC建立失败次数</option>
												<option value="ERAB建立失败次数" selected="selected">ERAB建立失败次数</option> -->
												<option value="RRC建立请求次数">RRC建立请求次数</option>
												<option value="RRC建立成功次数">RRC建立成功次数</option>												
												<option value="ERAB建立请求次数">ERAB建立请求次数</option>
												<option value="ERAB建立成功次数">ERAB建立成功次数</option>
												<!-- <option value="ERAB建立失败次数">ERAB建立失败次数</option> -->
											</select>
										</div>
                            		</div>                  
									<input type="text" id='mapCell' hidden="hidden" />
								</form>
								<div id="worstCellContainer"></div>
								<!-- <div id="worstCellContainer" style="width:100%;height:100%" ></div> -->
								<div class="zhaozi" id="chart_zhaozi"></div>
								<div class="loadingImg text-center" id="chart_loadingImg">
									<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
								</div>
							</div>	
                        </div>
                        <div class="chart tab-pane" id="indexDetails" >
                        	<div class="table-responsive">
								<table id="badCellTableIndex">
								</table>
							</div>
                        </div>
                    </div>
                </div>
			</div>

			<div class="box" id="flagHighInterfere">
                <div class="box-header">
                    <h3 class="box-title" id="getHighInterfereDate">高干扰的应对处理原则</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body">
                	<div class="form-control" style="border: 0">
                		 <label for="isModifyPowerControlParameter" class="col-md-3">1、修改功控参数: </label>
	                    <span id="isModifyPowerControlParameter"></span><br />	
                	</div>
	               <div class="form-control" style="border: 0">
	               	   <label for="isModifyCellBarredInterfereCell" class="col-md-3">2、cellBarred干扰小区: </label>
                
                    <span id="isModifyCellBarredInterfereCell"></span><br />
	               </div>
	               <div class="form-control" style="border: 0">
	               <label for="isModifyLimitInterfereCellqRxlevmin" class="col-md-3">3、限制干扰小区qRxlevmin建议修改值:</label>
	               	  <span id="isModifyLimitInterfereCellqRxlevmin"></span> <br />
	               </div>
	               <div class="form-control" style="border: 0">
	               	 <label for="isModifyReduceInterfereCellTransmittedPower" class="col-md-3">4、降低干扰小区的发射功率: </label> <span id="isModifyReduceInterfereCellTransmittedPower"></span> <br />
	               </div>
	               <div class="form-control" style="border: 0">
	               <label for="isModifyTurnOffNeighCellToInterfereCells" class="col-md-5"> 5、关闭周边小区到干扰小区的isHoAllowed=false 开关: <a id="isModifyTurnOffNeighCellToInterfereCells" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" onClick="exportTurnOffNeighCellToInterfereCells()"><span class="ladda-label ">导出</span></a><span id="isModifyTurnOffNeighCellToInterfereCell" style="display: none"></span></label>
	               </div>
                </div>
            </div>
			<!-- <div class="box" >
				<div class="box-header">
					<h3 class="box-title">参数</h3>
					<div class="box-tools pull-right">
						<span id="baselineFileSave" class="glyphicon glyphicon-save" aria-hidden="true" onClick="fileSave_baseline()"></span>
	                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                </div>
				</div>
				<div class="box-body">
                    <div class="table-responsive">
						<table id="baselineTableIndex">
						</table>
					</div>
					
				</div>	
			</div> -->
			<div class="box" id="alarmBox" style="display: none">
				<div class="box-header">
					<h3 class="box-title">告警分析</h3>
						<div class="box-tools pull-right">
	                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                    </div>
				</div>
				<div class="box-body"> 
					<div class="nav-tabs-custom">
						<ul class="nav nav-tabs pull-left">
                            <li class="active"><a href="#cellAlarmClassify" data-toggle="tab">当前告警</a></li>
                            <li><a href="#erbsAlarmClassify" data-toggle="tab">历史告警</a></li>
                        </ul>
                        <div class="tab-content">
                        	<div class="chart tab-pane active" id="cellAlarmClassify">
                        		<table class="table" id="cellAlarmTable">
								</table>
                        	</div>
                        	<div class="chart tab-pane" id="erbsAlarmClassify">	
                        		<table class="table" id="erbsAlarmTable">
								</table>
                        	</div>
                        </div>
                        <div class="zhaozi" id="alarm_zhaozi"></div>
								<div class="loadingImg text-center" id="alarm_loadingImg">
									<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
								</div>
					</div>
				</div>
			</div>

			<!-- <div class="box" id="weakCoverBox">
				<div class="box-header">
					<h3 class="box-title">弱覆盖分析</h3>
						<div class="box-tools pull-right">
	                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                    </div>
				</div>
				<div class="box-body" style="position:relative;overflow:auto">   
					<div class="chart tab-pane active" id="weakCoverageCell">
					</div>
					<div class="zhaozi" id="weak_zhaozi"></div>
					<div class="loadingImg text-center" id="weak_loadingImg">
						<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
					</div>
				</div>
			</div> -->

			<!-- <div class="box" id="interfereBox">
				<div class="box-header">
					<h3 class="box-title">干扰分析</h3>
						<div class="box-tools pull-right">
	                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                    </div>
				</div>
				<div class="box-body" style="position:relative;overflow:auto"> 
					<table id='interfereAnalysis' class="display" cellspacing="0" border="1">             
                    </table>
					<div class="zhaozi" id="interfere_zhaozi"></div>
					<div class="loadingImg text-center" id="interfere_loadingImg">
						<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
					</div>
				</div>
			</div> -->
			
			<!-- <div class="box">
				<div class="box-header">
					<h3 class="box-title">地理化分析</h3>
					<div class="box-tools pull-right">
				                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				                </div>
				</div>
				<div class="row">
					<div class="col-sm-4">
						<div class="box">
							<div class="box-header">
								<h3 class="box-title">维度选择</h3>
							</div>
							<div class="box-body">
								<form class="form-horizontal" role="form">
									<div class="form-group">
									    <div class="col-sm-4">
									      	<div class="radio">
									        	<label>
									          		<input type="radio" class="switchRadio" name="switchRadio" value="out" checked="checked">切出维度
									        	</label>
									      	</div>
									    </div>
									    <div class="col-sm-4">
									      	<div class="radio">
									        	<label>
									          		<input type="radio" class="switchRadio" name="switchRadio" value="in">切入维度
									        	</label>
									      	</div>
									    </div>
								  	</div>
								</form>
							</div>
						</div>
						<div class="box">
							<div class="box-header">
								<h3 class="box-title">扇区颜色维度</h3>
							</div>
							<div class="box-body">
				                            <table border="1px" style="font-size:12px;border:0.2px solid gray;">
				                                <tr>
				                                    <td colspan=4 align='center' width="240px">最大RRC连接用户数</td>
				                                </tr>
				                                <tr>
				                                    <td rowspan=2 align='center'><input name='t1' type='radio' value='1' onClick='res(1)'/></td>
				                                    <td bgColor='red' align='center' width="80px"> >200</td>
				                                    <td bgColor='yellow' align='center' width="80px"> 100~200</td>
				                                    <td bgColor='blue' align='center' width="80px"> <100</td>
				                                </tr>
				                                <tr>
				                                   <td bgColor='red' align='center' width="80px" id="nummore200">null</td>
				                                   <td bgColor='yellow' align='center' width="80px" id="num100to200">null</td>
				                                   <td bgColor='blue' align='center' width="80px" id="numless100">null</td>
				                                </tr>
				                                <tr>
				                                    <td colspan=4 align='center' width="240px">无线掉线率</td>
				                                </tr>
				                                <tr>
				                                    <td rowspan=2 align='center'><input name='t1' type='radio' value='2' onClick='res(2)'/></td>
				                                    <td bgColor='red' align='center' width="80px"> >5%</td>
				                                    <td bgColor='yellow' align='center' width="80px"> 1%~5%</td>
				                                    <td bgColor='blue' align='center' width="80px"> <1%</td>
				                                </tr>
				                                <tr>
				                                   <td bgColor='red' align='center' width="80px" id="wireLostmore5">null</td>
				                                   <td bgColor='yellow' align='center' width="80px" id="wireLost1to5">null</td>
				                                   <td bgColor='blue' align='center' width="80px" id="wireLostLess1">null</td>
				                                </tr>
				                                <tr>
				                                    <td colspan=4 align='center' width="240px">PUSCH上行干扰电平</td>
				                                </tr>
				                                <tr>
				                                    <td rowspan=2 align='center'><input name='t1' type='radio' value='3' onClick='res(3)'/></td>
				                                    <td bgColor='red' align='center' width="80px"> >-95</td>
				                                    <td bgColor='yellow' align='center' width="80px"> -110~-95</td>
				                                    <td bgColor='blue' align='center' width="80px"> <-110</td>
				                                </tr>
				                                <tr>
				                                   <td bgColor='red' align='center' width="80px" id="phschmore95">null</td>
				                                   <td bgColor='yellow' align='center' width="80px" id="phsch110to95">null</td>
				                                   <td bgColor='blue' align='center' width="80px" id="phschless110">null</td>
				                                </tr>
				                           	</table>
							</div>
						</div>
						<div class="box">
							<div class="box-header">
								<h3 class="box-title">指标详情</h3>
							</div>
							<div class="box-body">
								<table id="detailTable" style="position: relative;height: 580px;width:100%"></table>
							</div>
						</div>
					</div>
					<div class="col-sm-8">
						<div class="box-body" style="position:relative;">
							<div id="map" style="position: relative;height: 1000px;width:100%"></div>   
						</div>
					</div>
					<div class="zhaozi" id="map_zhaozi"></div>
					<div class="loadingImg text-center" id="map_loadingImg">
						<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
					</div>
				</div>
			</div> -->
            <!-- <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">指标详情</h3>
                </div>
                <div class="box-body">
                    <table id="detailTable" style="position: relative;height: 580px;width:100%"></table>
                </div>
            </div> -->
			
			<!-- <div class="box" id="neighBox">
				<div class="box-header">
					<h3 class="box-title">邻区分析</h3>
					<div class="box-tools pull-right">
	                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
	                </div>
				</div>
				<div class="box-body">
					<ul class="nav nav-tabs" role="tablist">
						<li class="active"><a href="#table_tab_2" data-toggle="tab" id="table_tab_2_nav"
							aria-expanded="false">缺失同频邻区</a></li>
						<li class=""><a href="#table_tab_0" data-toggle="tab" id="table_tab_0_nav"
							aria-expanded="false">缺失异频邻区</a></li>
						<li class=""><a href="#table_tab_1" data-toggle="tab" id="table_tab_1_nav"
							aria-expanded="false">缺失2G邻区</a></li>
					</ul>
					<div class="tabs tab-content ">
						<div class=" tab-pane" id="table_tab_0">
							<table id="LTETable"></table>
						</div>
						<div class=" tab-pane" id="table_tab_1">
							<table id="GSMTable"></table>
						</div>
						<div class=" tab-pane active" id="table_tab_2">
							<table id="LTETable_1"></table>

						</div>
					</div>
				</div>
			</div> -->
		</div>	
	</div>

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
					<div class="form-group col-md-3">
			    		无线接通率<=97%
			    	</div>
			    	<div class="form-group col-md-2">	
			    		AND
			    	</div>
			    	<div class="form-group col-md-3">	
					    (RRC建立请求次数>50
					</div>
					<div class="form-group col-md-1">	
					    OR
					</div>
					<div class="form-group col-md-3">	
					    ERAB建立请求次数>50)
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" name="saveBtn" class="col-sm-1 col-sm-offset-11 btn btn-primary" id="saveBtn" onclick="updateConfigInfo()">确定</button>
				</div>
			</div>
			</form>
		</div>
	</div>

	<div class="modal fade" id="config_information_alarm">
		<div class="modal-dialog" style="width:900px;">
			<div class="modal-content">
				<div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
	                <h8 class="modal-title" id="mtitle">告警维度</h8>
	            </div>
	            <div class="modal-body">
					<div class="nav-tabs-custom">
						<ul class="nav nav-tabs pull-left">
                            <li class="active"><a href="#cellAlarmClassify_1" data-toggle="tab">当前告警</a></li>
                            <li><a href="#erbsAlarmClassify_1" data-toggle="tab">历史告警</a></li>
                        </ul>
                        <div class="tab-content">
                        	<div class="chart tab-pane active" id="cellAlarmClassify_1">
                        		<table class="table" id="cellAlarmTable_model">
								</table>
                        	</div>
                        	<div class="chart tab-pane" id="erbsAlarmClassify_1">	
                        		<table class="table" id="erbsAlarmTable_model">
								</table>
                        	</div>
                        </div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" name="saveBtn" class="col-sm-1 col-sm-offset-11 btn btn-primary" id="saveBtn" onclick="updateConfigInfo()">确定</button>
				</div>	
			</div>	
		</div>
	</div>

	<div class="modal fade" id="config_information_parameter">
		<div class="modal-dialog" style="width:900px;">
			<div class="modal-content">
				<div class="modal-header">
					<div class="box-tools pull-right">
						<span id="baselineFileSave" class="glyphicon glyphicon-save" aria-hidden="true" onClick="fileSave_baseline()"></span>
	                    <!-- <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button> -->
	                    <button type="button" class="btn btn-box-tool" data-dismiss="modal"><i class="fa fa-times"></i></button>
	                </div>
	                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>   
	                <span id="baselineFileSave" class="glyphicon glyphicon-save" aria-hidden="true" style="float: right; position: absolute;" onClick="fileSave_baseline()"></span> -->
	                <h8 class="modal-title" id="mtitle">参数维度</h8>
	            </div>
	            <div class="modal-body">
					<div class="nav-tabs-custom">
						<!-- <div class="box" >
							<div class="box-header">
								<h3 class="box-title">参数</h3>
								<div class="box-tools pull-right">
									<span id="baselineFileSave" class="glyphicon glyphicon-save" aria-hidden="true" onClick="fileSave_baseline()"></span>
				                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				                </div>
							</div>
							<div class="box-body">
			                    <div class="table-responsive"> -->
			            <div class='progress' id="loadingparam" style="text-align: center;">  
                    		<a>加载中</a>
                    	</div>
						<table id="baselineTableIndex">
						</table>
								<!-- </div>							
							</div>	
						</div> -->
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" name="saveBtn" class="col-sm-1 col-sm-offset-11 btn btn-primary" id="saveBtn" onclick="updateConfigInfo()">确定</button>
				</div>	
			</div>	
		</div>
	</div>

	<div class="modal fade" id="config_information_neighborCell">
		<div class="modal-dialog" style="width:900px;">
			<div class="modal-content">
				<div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
	                <h8 class="modal-title" id="mtitle">邻区维度</h8>
	            </div>
	            <div class="modal-body">
	            <div class="nav-tabs-custom">
                        <ul id="getNeighborCellTab" class="nav nav-tabs pull-left">
                            <li class="active"><a href="#neighborCellMapDiv" data-toggle="tab">邻区显示</a></li>
                            <li><a href="#neighborCellTableDiv" data-toggle="tab">邻区详情</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="chart tab-pane active" id="neighborCellMapDiv" style="position: relative;"> 
								<div id="neighborCellMap" style="position: relative;height: 450px;width:100%"></div>
                            </div>
                            <div class="chart tab-pane" id="neighborCellTableDiv">
                            	<div class="table-responsive">
									<table class="table" id="neighborCell_model">
									</table>
								</div>
                            </div>
                        </div>
                    </div>
				</div>
				<div class="modal-footer">
					<button type="button" name="saveBtn" class="col-sm-1 col-sm-offset-11 btn btn-primary" id="saveBtn" onclick="updateConfigInfo()">确定</button>
				</div>	
			</div>	
		</div>
	</div>

	<div class="modal fade" id="config_information_weakCoverCell">
		<div class="modal-dialog" style="width:900px;">
			<div class="modal-content">
				<div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
	                <h8 class="modal-title" id="mtitle">弱覆盖</h8>
	            </div>
	            <div class="modal-body">
	            	<div class="nav-tabs-custom">
                        <ul id="getfirstWeakCoverCellTab" class="nav nav-tabs pull-left">
                            <li class="active"><a href="#indexTrendWeakCoverCell" data-toggle="tab">指标趋势</a></li>
                            <li><a href="#indexDetailsWeakCoverCell" data-toggle="tab">指标详情</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="chart tab-pane active" id="indexTrendWeakCoverCell"> 
								<div id="weakCoverCellWorstCellContainer"></div>
                            </div>
                            <div class="chart tab-pane" id="indexDetailsWeakCoverCell">
                            	<div class="table-responsive">
									<table class="table" id="weakCoverCell_model">
									</table>
								</div>
                            </div>
                        </div>
                    </div>
	            	<!-- <table class="table" id="weakCoverCell_model">
					</table> -->
				</div>
				<div class="modal-footer">
					<button type="button" name="saveBtn" class="col-sm-1 col-sm-offset-11 btn btn-primary" id="saveBtn" onclick="updateConfigInfo()">确定</button>
				</div>	
			</div>	
		</div>
	</div>

	<div class="modal fade" id="config_information_zhichaCell">
		<div class="modal-dialog" style="width:900px;">
			<div class="modal-content">
				<div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
	                <h8 class="modal-title" id="mtitle">质差维度</h8>
	            </div>
	            <div class="modal-body">
	            	<div class="nav-tabs-custom">
                        <ul id="getfirstZhichaCellTab" class="nav nav-tabs pull-left">
                            <li class="active"><a href="#indexTrendZhichaCell" data-toggle="tab">指标趋势</a></li>
                            <li><a href="#indexDetailsZhichaCell" data-toggle="tab">指标详情</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="chart tab-pane active" id="indexTrendZhichaCell"> 
								<div id="zhichaCellWorstCellContainer"></div>
                            </div>
                            <div class="chart tab-pane" id="indexDetailsZhichaCell">
                            	<div class="table-responsive">
									<table class="table" id="zhichaCell_model">
									</table>
								</div>
                            </div>
                        </div>
                    </div>
	            	<!-- <table class="table" id="zhichaCell_model">
					</table> -->
				</div>
				<div class="modal-footer">
					<button type="button" name="saveBtn" class="col-sm-1 col-sm-offset-11 btn btn-primary" id="saveBtn" onclick="updateConfigInfo()">确定</button>
				</div>	
			</div>	
		</div>
	</div>

	<div class="modal fade" id="config_information_overlapCoverCell">
		<div class="modal-dialog" style="width:900px;">
			<div class="modal-content">
				<div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
	                <h8 class="modal-title" id="mtitle">重叠覆盖</h8>
	            </div>
	            <div class="modal-body">
	            	<table class="table" id="overlapCoverCell_model">
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" name="saveBtn" class="col-sm-1 col-sm-offset-11 btn btn-primary" id="saveBtn" onclick="updateConfigInfo()">确定</button>
				</div>	
			</div>	
		</div>
	</div>

	<div class="modal fade" id="config_information_interferenceCell">
		<div class="modal-dialog" style="width:900px;">
			<div class="modal-content">
				<div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
	                <h8 class="modal-title" id="mtitle">干扰维度</h8>
	            </div>
	            <div class="modal-body">
	            	<div class="nav-tabs-custom">
	            		<div class='progress' id="loadingganrao" style="text-align: center;">  
                    		<a>加载中</a>
                    	</div>
                        <ul id="getfirstInterferenceCellTab" class="nav nav-tabs pull-left">
                            <li class="active"><a href="#indexTrendInterferenceCell" data-toggle="tab">指标趋势</a></li>
                            <li><a href="#indexDetailsInterferenceCell" data-toggle="tab">指标详情</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="chart tab-pane active" id="indexTrendInterferenceCell"> 
								<div id="InterferenceCellWorstCellContainer"></div>
                            </div>
                            <div class="chart tab-pane" id="indexDetailsInterferenceCell">
                            	<div class="table-responsive">
									<table class="table" id="interferenceCell_model">
									</table>
								</div>
                            </div>
                        </div>
                    </div>

	            	<!-- <table class="table" id="interferenceCell_model">
					</table> -->
				</div>
				<div class="modal-footer">
					<button type="button" name="saveBtn" class="col-sm-1 col-sm-offset-11 btn btn-primary" id="saveBtn" onclick="updateConfigInfo()">确定</button>
				</div>	
			</div>	
		</div>
	</div>



	<!-- rrc原因值详情弹出框-->
<div class="modal fade" id="config_information_rrcResultDetail">
	<div class="modal-dialog" style="width:900px;">
		<div class="modal-content">
			<div class="modal-header">
				<div class="box-tools pull-right">
					<!-- <span id="exportRrcDetail" class="glyphicon glyphicon-save" aria-hidden="true" onClick="exportRrcDetail()"></span>&nbsp;&nbsp; -->
					<a id="exportRrcDetail" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="exportRrcDetail()"><span class="ladda-label">导出</span></a>&nbsp;&nbsp;
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
                </div>

                <h8 class="modal-title" id="mtitle">原因值详情<span id="loading"></span></h8>

            </div>
			<form class="form-horizontal" role="form" id="scopeForm">
				<div class="modal-body text-center">
					<table id="rrcResultDetailTable">
					</table>
				</div>
				<!-- <div class="modal-footer">
					<button type="button" name="submit" class="col-sm-2 col-sm-offset-4 btn btn-primary" onclick="updateScope()">保存</button>
					<button type="button" class="col-sm-2 btn btn-default pull-right" id="cancelBtn" data-dismiss="modal">返回</button>
				</div> -->
			</form>
		</div>
	</div>
</div>
<div id="ctrData" style="display: none;"></div>
<!--CTR跳转-->
<div class="modal fade" id="ctrJump">
	<div class="modal-dialog" style="width: 90%">
		<div class="modal-content">
			<div class="modal-header">
				<div class="box-tools pull-right">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
                </div>
                <h8 class="modal-title" id="mtitle">CTR跳转</h8>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<div class="box">
							<div class="box-body" style="height:250px;overflow:auto;">
								<table id="fileTable"></table>
							</div>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="box">
							<div class="box-header with-border">
								<h3 class="box-title">任务名称</h3>
							</div>
							<div class="box-body">
								<form id="taskName_form">
									<div class="form-group">
										<!-- <label for="taskName" class="control-label">任务名称：</label> -->
										<input type="text" class="form-control" name="taskName" id="taskName" placeholder="只能包含数字，字母，$和_" maxlength="18">
									</div>
									<div class="form-group" style="margin-bottom:0;padding-left:15px;padding-top:10px;" id="ctrTypeDiv">
										<label class="radio-inline">
									        <input type="radio" name="ctrType" class="ctrType" value="ctr" checked> 统计分析
									    </label>
									    <label class="radio-inline">
									        <input type="radio" name="ctrType" class="ctrType" value="ctrFull"> 协议分析
									    </label>
									</div>
								</form>
							</div>
						</div>
						<div class="box">
							<div class="box-header with-border">
								<h3 class="box-title">日志</h3>
							</div>
							<div class="box-body" style="height:150px; overflow:auto;overflow-x:hidden">
					            <p id="log" style="word-wrap: break-word;"></p>
				            </div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button id="runBtn" type="button" class="col-sm-2 col-sm-offset-4 btn btn-primary ladda-button" data-color='red' data-style="expand-right" onclick="storage()">运行</button>
					<button type="button" class="col-sm-2 btn btn-default ladda-button" id="cancelBtn2" data-color='red' data-style="expand-right" data-dismiss="modal">关闭</button>
				</div>

			</div>
		</div>
	</div>
</div>
<!--CTR跳转-->
<!-- 地理化分析弹出框0605 -->
<div class="modal fade" id="mapModal">
	<div class="modal-dialog" style="width:90%;">
		<div class="modal-content">
			<div class="modal-header">
				<div class="box-tools pull-right">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
                </div>
                <h8 class="modal-title" id="mtitle">地理化分析</h8>
            </div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-4">
						<div class="box">
							<div class="box-header">
								<h3 class="box-title">维度选择</h3>
							</div>
							<div class="box-body">
								<form class="form-horizontal" role="form">
									<div class="form-group">
									    <div class="col-sm-4">
									      	<div class="radio">
									        	<label>
									          		<input type="radio" class="switchRadio" name="switchRadio" value="out" checked="checked">切出维度
									        	</label>
									      	</div>
									    </div>
									    <div class="col-sm-4">
									      	<div class="radio">
									        	<label>
									          		<input type="radio" class="switchRadio" name="switchRadio" value="in">切入维度
									        	</label>
									      	</div>
									    </div>
								  	</div>
								</form>
							</div>
						</div>
						<div class="box">
							<div class="box-header">
								<h3 class="box-title">扇区颜色维度</h3>
							</div>
							<div class="box-body">
	                            <table border="1px" style="font-size:14px;border:0.2px solid gray;">
	                                <tr>
	                                	<td align='center'><input name='t1' type='radio' value='1' onClick='res(1)'/></td>
	                                    <td align='center' width="240px">最大RRC连接用户数</td>
	                                    <td bgColor='red' align='center' width="80px"> >200</td>
	                                    <td bgColor='yellow' align='center' width="80px"> 100~200</td>
	                                    <td bgColor='blue' align='center' width="80px"> <100</td>
	                                </tr>
	                                <tr>
	                                	<td></td>
	                                	<td></td>
                                    	<td align='center' width="80px" id="nummore200">null</td>
	                                   	<td align='center' width="80px" id="num100to200">null</td>
	                                   	<td align='center' width="80px" id="numless100">null</td>
	                                </tr>
	                                <tr>
	                                	<td align='center'><input name='t1' type='radio' value='2' onClick='res(2)'/></td>
	                                    <td align='center' width="240px">无线接通率</td>
	                                    <td bgColor='red' align='center' width="80px"> >95%</td>
	                                    <td bgColor='yellow' align='center' width="80px"> 90%~95%</td>
	                                    <td bgColor='blue' align='center' width="80px"> <90%</td>
	                                </tr>
	                                <tr>
	                                	<td></td>
	                                	<td></td>
	                                   <td align='center' width="80px" id="wireLostmore5">null</td>
	                                   <td align='center' width="80px" id="wireLost1to5">null</td>
	                                   <td align='center' width="80px" id="wireLostLess1">null</td>
	                                </tr>
	                                <tr>
	                                	<td align='center'><input name='t1' type='radio' value='3' onClick='res(3)'/></td>
	                                    <td align='center' width="240px">PUSCH上行干扰电平</td>
	                                    <td bgColor='red' align='center' width="80px"> >-95</td>
	                                    <td bgColor='yellow' align='center' width="80px"> -110~-95</td>
	                                    <td bgColor='blue' align='center' width="80px"> <-110</td>
	                                </tr>
	                                <tr>
	                                	<td></td>
	                                	<td></td>
	                                   <td align='center' width="80px" id="phschmore95">null</td>
	                                   <td align='center' width="80px" id="phsch110to95">null</td>
	                                   <td align='center' width="80px" id="phschless110">null</td>
	                                </tr>
	                           	</table>
							</div>
						</div>
						<div class="box">
							<div class="box-header">
								<h3 class="box-title">指标详情</h3>
							</div>
							<div class="box-body">
								<table id="detailTable" style="position: relative;height: 280px;width:100%"></table>
							</div>
						</div>
					</div>
					<div class="col-sm-8">
						<div class="box-body" style="position:relative;">
							<div id="map" style="position: relative;height: 700px;width:100%"></div>   
						</div>
					</div>
					<div class="zhaozi" id="map_zhaozi"></div>
					<div class="loadingImg text-center" id="map_loadingImg">
						<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">详细数据</h4>
            </div>
            <div class="modal-body">
                <table id='bMapTable' class="display" cellspacing="0" border="1">
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</section>



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
    #baselineFileSave {
    	cursor:pointer;
    	color:#97a0b3;
    }
    #baselineFileSave:hover {
    	color: #606c84;
    }
    #alarmNum,#LteNum,#GsmNum,#weakCoverNum,#highInterfereNum,#erbsAlarmNum,#overlapCeakCoverNum,#firstOrderConflictNum,#secondOrderConflictNum,#erbsAlarmNumHour,#alarmNumHour,#highInterfereNumHour,#prbHighInterfereNumHour,#prbHighInterfereNum{
	   border:0;
	}
	#currentAlarm,#needAddNeigh,#less116Proportion,#less155Proportion,#cqi,#featureState,#licenseState,#srUser,#avgTA,#overlapCover,#AvgPRB,#highTraffic,#highTraffic2,#parameter,#wirelessCallRate_interfere,#wirelessCallRate_zhicha,#wirelessCallRate_RRCEstSucc,#wirelessCallRate_ERABEstSucc{
	   border:0;
	   background-color: #fff;
	}
</style> 
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<!-- jQuery 2.2.0 -->
<!-- datepicker -->
 <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
 <script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script>


 <!--select-->
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />

 <!--datatables-->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="plugins/datatables/grid.js"></script>
    <link type="text/css" rel="stylesheet" href="plugins/datatables/grid.css" >

<!--bootstrapvalidator-->
<link rel="stylesheet" href="plugins/bootstrapvalidator-master/css/bootstrapValidator.min.css">
<script src="plugins/bootstrapvalidator-master/js/bootstrapValidator.min.js"></script>     

<!-- Bootstrap WYSIHTML5 -->

<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<script src="plugins/highcharts/js/highcharts.js"></script>

<!--loading-->
<!-- <link rel="stylesheet" href="plugins/loading/css/ladda-themeless.min.css"> -->
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>
<style type='text/css'>
	.datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
    background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
    border-color: #ffb733;
	}	
</style>

<!-- <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=XtxLWdHvIBw0FKDLBh835SwO"></script> -->
<script type="text/javascript" src="plugins/baidumapv2/baidumap_offline_v2_load.js"></script>
<link rel="stylesheet" type="text/css" href="plugins/baidumapv2/css/baidu_map_v2.css"/>
<script src="plugins/mapv/Mapv.js"></script>
<script type="text/javascript" src="plugins/highcharts/js/highcharts-more.js"></script>

<!-- <link href="plugins/iCheck/square/blue.css" rel="stylesheet">
<script src="plugins/iCheck/icheck.js"></script> -->
 <!-- treegrid -->
<link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/datagrid.css">
<link rel="stylesheet" href="dist/css/LTETemplateManage.css">
<script src="plugins/EasyUI/jquery.easyui.min.js"></script>
@endsection
<script src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/badCellAnalysis/badCell.js"></script>
<link rel="stylesheet" href="dist/css/button.css">
<style>
	.zhaozi{
		width:100%;
		height:100%;
		position:absolute;
		top:0;
		left:0;
		display:none;
		background-color:#000;
		opacity:.3;
		z-index:10;
	}
	.loadingImg{
		position:absolute;
		top:80px;
		width:100%;
		z-index:11;
		display:none;
	}
	.loadingImg > span{
		display: inline-block;
		padding: 10px 15px;
		background-color:#fff;
	}
	.highcharts-container {
		width: 10000px;
	}
</style>
