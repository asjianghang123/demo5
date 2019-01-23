@extends('layouts.nav')
@section('content-header')
<section class="content-header">
	<h1>SRVCC差小区</h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i>日常优化</a>
		</li>
		<li><a href="#"><i class="fa fa-dashboard"></i>差小区分析</a>
		</li>
		<li class="active">SRVCC差小区
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
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool dropdown-toggle" data-toggle="dropdown" onClick="openConfigInfo()">
                    		<i class="fa fa-wrench"></i>
                    	</button>
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
				</div>
				<div class='box-body'>
					<div class="row">
						<div class="col-md-1">城市</div>
						<div class="col-md-3">
							<select id="allCity" class="form-control input-sm" multiple="multiple">
							</select>
						</div>
					</div>
				</div>
				<div class='box-footer' style="text-align:right">
					<a id="search" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#" onClick="doSearchbadCell('table','VOLTE上行丢包差小区')"><span class="ladda-label ">查询</span></a>
					<input id="badCellFile" value='' hidden="true" />	
					<input id='inputCategory' value='volteupbadcell' hidden="true" />
					<input id ="tableChoose" value='volteupbadcell' hidden="true" />
					<input id="chooseTable" value='volteupbadcell' hidden="true" />
					<input id="badCellFileIndex" value='volteupbadcell' hidden="true" />
				</div>
			</div>
			<div class='box'>
				<div class="box-header with-border">
					<h3 class="box-title">小区列表</h3>
					<div class="box-tools pull-right">
                        <a id="export" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="doSearchbadCell('file','VOLTE上行丢包差小区')"><span class="ladda-label">导出</span></a>
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
				<div class="box-header">
					<h3 class="box-title">诊断报告</h3>
					<div class="box-tools pull-right">
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
						<div class="box" style="height: 400px;">
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
																<label for="currentAlarm" class="col-sm-4 control-label" style="text-align: center;">告警数量:</label>
															      	<input type="text" id="currentAlarm" readonly="readonly">
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
																<label for="2GneedAddNeigh" class="col-sm-7 control-label" style="text-align: center;">2G邻区数量:</label>
																<div class="col-sm-5">
															      	<input type="text" id="2GneedAddNeigh" readonly="readonly" style="border: none;width: 60px;height:20px">
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
															<span id='less155ProportionNum' style="left: 24px;top: 11px; position: absolute;"></span>下行质差
														</a>
													</h4>
												</div>
												<div id="collapseFive" class="panel-collapse collapse in">
													<div class="panel-body">
														<form class="form-horizontal">
															<div class="form-group" style="margin: 0">
																<div class="col-sm-12" style="word-break: keep-all;white-space: nowrap;">
																	<label for="less155Proportion" style="text-align: center;padding: 0;font-weight: normal;">RSRQ<-15.5的比例:</label>
															      	<input type="text" id="less155Proportion" readonly="readonly" style="border: none;width: 60px;height:20px">
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
														   href="#collapseSeven" style="padding-left: 10px;">
															<span id='parameterNum' style="left: 24px;top: 11px; position: absolute;"></span>ESRVCC
														</a>
													</h4>
												</div>
												<div id="collapseSeven" class="panel-collapse collapse in">
													<div class="panel-body" id="ESRVCC_body">														
														<form class="form-horizontal">
															<div class="form-group" style="margin: 0">
																<label for="ESRVCC" class="col-sm-7 control-label" style="text-align: center;">失败原因占比:</label>
																<div class="col-sm-5" style="word-break: keep-all;white-space: nowrap;">
																	<input type="text" id="ESRVCC" readonly="readonly" style="border: none;width: 60px;height:20px">
																</div>
															</div>
														</form>
													</div>
												</div>
											</div>
										</div>
										<div class="loadingImg text-center" id="ESRVCC_loadingImg" style="top:40%">
											<span><i class="fa fa-spinner fa-pulse fa-fw"></i></span>
										</div>
									</div>
								</div>
							</div>
						</div>	
					</div>
					{{-- <div class="col-md-12">
						<div class="box">
							<div class="box-header">
								<h3 class="box-title">无线掉线原因值分布</h3>
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
									<div class=" tab-pane" id="table_tab_0">
										<table id="rrcResultTable">
							            </table>
							            <input type="hidden" id="selectedResult">
							        </div>
									<div class=" tab-pane active" id="table_tab_1" style="maxheight:400px;overflow:auto;">
										<div id="rrcResultContainer"></div>
									</div>
							    </div>
							</div>
						</div>	
					</div> --}}
				</div>				
			</div>

			{{-- <div class="box"><!--0524-->
				<div class="box-header">
					<h3 class="box-title">Counter失败原因值分布</h3>
				</div>
				<div class="box-body">
					<div id="counterLoseResultDistribution"></div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="box">
						<div class="box-header">
							<h3 class="box-title">维度相关性</h3>
							<span id="wirelessCallRate_date_"></span>
						</div>
						<div class="box-body">
							<div id="container_Relevance_"></div>
							<span id='relevance_backups_' hidden="hidden"></span>
						</div>
					</div>
				</div>
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
				
				<div class="nav-tabs-custom">
                    <ul id="getfirstTab" class="nav nav-tabs pull-left">
                        <li class="active"><a href="#indexTrend" data-toggle="tab">指标趋势</a></li>
                        <li><a href="#indexDetails" data-toggle="tab">指标详情</a></li>
                    </ul>
                    <div class="tabs tab-content">
                        <div class="tab-pane active" id="indexTrend">  
                            <div class="box-body">
                            	<form class="form-horizontal" role="form" id="lowAccessForm" hidden="hidden">
                            		<div class="form-group">
                            			<br /><br />
                            			<label for="worstCellChartPrimaryAxisType" class="col-sm-1 control-label">主轴</label>
										<div class="col-sm-5">
											<select class="form-control" id="worstCellChartPrimaryAxisType" multiple="multiple" hidden="hidden">
												<option value="无线掉线率" selected="selected" disabled="disabled">无线接通率</option>
											</select>
										</div>
										<label for="worstCellChartAuxiliaryAxisType" class="col-sm-1 control-label">辅轴</label>
										<div class="col-sm-5">
											<select id="worstCellChartAuxiliaryAxisType" class="form-control" multiple="multiple" hidden="hidden">
												<option value="干扰" selected="selected">干扰</option>
												<option value="质差" selected="selected">质差</option>
											</select>
										</div>
                            		</div>                  
									<input type="text" id='mapCell' hidden="hidden" />
								</form>
								<div id="worstCellContainer"></div>
								<div class="zhaozi" id="chart_zhaozi"></div>
								<div class="loadingImg text-center" id="chart_loadingImg">
									<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
								</div>
							</div>	
                        </div>
                        <div class="chart tab-pane" id="indexDetails">
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
            </div> --}}
			<!-- <div class="box" >
				<div class="box-header">
					<h3 class="box-title">指标</h3>
					<div class="box-tools pull-right">
						<span id="loadSaveData" class="glyphicon glyphicon-save" aria-hidden="true" onClick="fileSave()"></span>
				                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				                </div>
				</div>
				<div class="box-body">
					<div class="nav-tabs-custom">
			                        <ul class="nav nav-tabs pull-left">
			                            <li class="active"><a href="#indexTrend" data-toggle="tab">指标趋势</a></li>
			                            <li><a href="#indexDetails" data-toggle="tab">指标详情</a></li>
			                        </ul>
			                        <div class="tab-content">
			                            <div class="chart tab-pane active" id="indexTrend">
			                            	<table class="table">
									<tr>
										<td >主轴</td>
											<th >
												<select id="worstCellChartPrimaryAxisType" class="form-control">
													<option value="无线掉线率">无线掉线率</option>
												</select>
											</th>
			
											<td>辅轴</td>
											<th >
												<select id="worstCellChartAuxiliaryAxisType" class="form-control">
													<option value="无线掉线次数">无线掉线次数</option>
													<option value="上下文建立成功数">上下文建立成功数</option>
													<option value="遗留上下文数">遗留上下文数</option>
													<option value="小区闭锁导致的掉线">小区闭锁导致的掉线</option>
													<option value="切换导致的掉线">切换导致的掉线</option>
													<option value="S1接口故障导致的掉线">S1接口故障导致的掉线</option>
													<option value="UE丢失导致的掉线">UE丢失导致的掉线</option>
													<option value="预清空导致的掉线">预清空导致的掉线</option>
												</select>
											</th>
											<input type="text" id='mapCell' hidden='true'/>
									</tr>				
								</table>
								<div id="worstCellContainer" ></div>
								<div id="worstCellContainer" style="width:100%;height:100%" ></div>
								<div class="zhaozi" id="chart_zhaozi"></div>
								<div class="loadingImg text-center" id="chart_loadingImg">
									<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
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
			</div> -->
			
			<!-- <div class="box" id="alarmBox">
				<div class="box-header">
					<h3 class="box-title">告警分析</h3>
						<div class="box-tools pull-right">
				                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				                    </div>
				</div>
				<div class="box-body">  style="position:relative; height:650px;"
					<div class="nav-tabs-custom">
						<ul class="nav nav-tabs pull-left">
			                            <li class="active"><a href="#cellAlarmClassify" data-toggle="tab">小区级告警分类</a></li>
			                            <li><a href="#erbsAlarmClassify" data-toggle="tab">基站级告警分类</a></li>
			                            <li><a href="#alarmDetails" data-toggle="tab">告警详情</a></li>
			                        </ul>
			                        <div class="tab-content">
			                        	<div class="chart tab-pane active" id="cellAlarmClassify">
			                        	</div>
			                        	<div class="chart tab-pane" id="erbsAlarmClassify">	
			                        	</div>
			                        	<div class="chart tab-pane" id="alarmDetails">
			                        		<table class="table" id="alarmWorstCellTable">
								</table>
			                        	</div>
			                        </div>
			                        <div class="zhaozi" id="alarm_zhaozi"></div>
							<div class="loadingImg text-center" id="alarm_loadingImg">
								<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
							</div>
					</div>
				</div>
			</div> -->

			<!-- <div class="box" id="weakCoverBox">
				<div class="box-header">
					<h3 class="box-title">弱覆盖分析</h3>
						<div class="box-tools pull-right">
				                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
				                    </div>
				</div>
				<div class="box-body" style="position:relative;overflow:auto">    style="position:relative; height:400px; overflow:auto"
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
				<div class="box-body" style="position:relative;overflow:auto">   style="position:relative; height:550px; overflow:auto"
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
							<div class="zhaozi" id="LTE_zhaozi"></div>
							<div class="loadingImg text-center" id="LTE_loadingImg">
								<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
							</div>
						</div>
						<div class=" tab-pane" id="table_tab_1">
							<table id="GSMTable"></table>
							<div class="zhaozi" id="GSM_zhaozi"></div>
							<div class="loadingImg text-center" id="GSM_loadingImg">
								<span><i class="fa fa-spinner fa-pulse fa-fw"></i>加载中，请稍等</span>
							</div>
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
					<div class="form-group col-md-4">
			    		VOLTE上行丢包率>5%
			    	</div>
			    	<div class="form-group col-md-3">	
			    		AND
			    	</div>
			    	<div class="form-group col-md-5">	
					    VOLTE上行总包数>1000
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" name="saveBtn" class="col-sm-1 col-sm-offset-11 btn btn-primary" id="saveBtn" data-dismiss="modal">确定</button>
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
					<button type="button" name="saveBtn" class="col-sm-1 col-sm-offset-11 btn btn-primary" data-dismiss="modal">确定</button>
				</div>	
			</div>	
		</div>
	</div>
	<div class="modal fade" id="config_information_parameter">
		<div class="modal-dialog" style="width:900px;">
			<div class="modal-content">
				<div class="modal-header">
					<!-- <div class="box-tools pull-right">
						<span id="baselineFileSave" class="glyphicon glyphicon-save" aria-hidden="true" onClick="fileSave_baseline()"></span>
						                    <button type="button" class="btn btn-box-tool" data-dismiss="modal"><i class="fa fa-times"></i></button>
						                </div> -->
	                <h8 class="modal-title" id="mtitle">参数维度</h8>
	            </div>
	            <div class="modal-body">
					<div class="nav-tabs-custom">
						<ul class="nav nav-tabs pull-left">
                             <li class="active" id="chanshu0" ><a href="#baselineTableDiv_0" data-toggle="tab">2G邻区过少</a></li>
                            <li id="chanshu1" ><a href="#baselineTableDiv_1" data-toggle="tab">外部2G小区定义错误</a></li>
                            <li id="chanshu2" ><a href="#baselineTableDiv_2" data-toggle="tab">A2门限</a></li>
                            <li id="chanshu3" ><a href="#baselineTableDiv_3" data-toggle="tab">B2门限Rsrp </a></li>
                            <li id="chanshu4" ><a href="#baselineTableDiv_4" data-toggle="tab">B2门限Geran</a></li>
                            <li id="chanshu5" ><a href="#baselineTableDiv_5" data-toggle="tab">PDCCH符号数设置低于2</a></li>
                            <li id="chanshu6" ><a href="#baselineTableDiv_6" data-toggle="tab">增强相关功能未开DU基站</a></li>
                            <li id="chanshu7" ><a href="#baselineTableDiv_7" data-toggle="tab">增强相关功能未开Baseband-based基站</a></li>
                            <li id="chanshu8" ><a href="#baselineTableDiv_8" data-toggle="tab">Timer设置和baseline不一致</a></li>
                        </ul>
                        <div class="tab-content">
                        	<div class="chart tab-pane active" id="baselineTableDiv_0">
                        		<table class="table baselineTableIndex" id="baselineTableIndex_0">
								</table>
                        	</div>
                        	<div class="chart tab-pane" id="baselineTableDiv_1">
                        		<table class="table baselineTableIndex" id="baselineTableIndex_1">
								</table>
                        	</div>
                        	<div class="chart tab-pane" id="baselineTableDiv_2">
                        		<table class="table baselineTableIndex" id="baselineTableIndex_2">
								</table>
                        	</div>
                        	<div class="chart tab-pane" id="baselineTableDiv_3">
                        		<table class="table baselineTableIndex" id="baselineTableIndex_3">
								</table>
                        	</div>
                        	<div class="chart tab-pane" id="baselineTableDiv_4">
                        		<table class="table baselineTableIndex" id="baselineTableIndex_4">
								</table>
                        	</div>
                        	<div class="chart tab-pane" id="baselineTableDiv_5">
                        		<table class="table baselineTableIndex" id="baselineTableIndex_5">
								</table>
                        	</div>
                        	<div class="chart tab-pane" id="baselineTableDiv_6">
                        		<table class="table baselineTableIndex" id="baselineTableIndex_6">
								</table>
                        	</div>
                        	<div class="chart tab-pane" id="baselineTableDiv_7">
                        		<table class="table baselineTableIndex" id="baselineTableIndex_7">
								</table>
                        	</div>
                        	<div class="chart tab-pane" id="baselineTableDiv_8">
                        		<table class="table baselineTableIndex" id="baselineTableIndex_8">
								</table>
                        	</div>
                        </div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" name="saveBtn" class="col-sm-1 col-sm-offset-11 btn btn-primary" data-dismiss="modal">确定</button>
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
					<button type="button" name="saveBtn" class="col-sm-1 col-sm-offset-11 btn btn-primary" data-dismiss="modal">确定</button>
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
					<button type="button" name="saveBtn" class="col-sm-1 col-sm-offset-11 btn btn-primary" data-dismiss="modal">确定</button>
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
				</div>
				<div class="modal-footer">
					<button type="button" name="saveBtn" class="col-sm-1 col-sm-offset-11 btn btn-primary" data-dismiss="modal">确定</button>
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
					<button type="button" name="saveBtn" class="col-sm-1 col-sm-offset-11 btn btn-primary" data-dismiss="modal">确定</button>
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
				</div>
				<div class="modal-footer">
					<button type="button" name="saveBtn" class="col-sm-1 col-sm-offset-11 btn btn-primary" data-dismiss="modal">确定</button>
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
					<a id="exportRrcDetail" class="btn btn-primary ladda-button" data-color='red' data-style="expand-right" href="#"  onClick="exportRrcDetail()"><span class="ladda-label">导出</span></a>&nbsp;&nbsp;
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                	<span aria-hidden="true">&times;</span>
	                </button>
                </div>
                <h8 class="modal-title" id="mtitle">无线掉线原因值分布详情<span id="loading"></span></h8>
            </div>
			<form class="form-horizontal" role="form" id="scopeForm">
				<div class="modal-body text-center">
					<table id="rrcResultDetailTable">
					</table>
				</div>
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
	                                    <td align='center' width="240px">无线掉线率</td>
	                                    <td bgColor='red' align='center' width="80px"> >5%</td>
	                                    <td bgColor='yellow' align='center' width="80px"> 1%~5%</td>
	                                    <td bgColor='blue' align='center' width="80px"> <1%</td>
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
    #alarmNum,#LteNum,#GsmNum,#weakCoverNum,#highInterfereNum,#erbsAlarmNum,#overlapCeakCoverNum,#firstOrderConflictNum,#secondOrderConflictNum,#erbsAlarmNumHour,#alarmNumHour,#highInterfereNumHour,#prbHighInterfereNumHour,#prbHighInterfereNum{
	   border:0;
	}
	#currentAlarm,#needAddNeigh,#less116Proportion,#less155Proportion,#cqi,#featureState,#licenseState,#avgTA,#overlapCover,#AvgPRB,#highTraffic,#highTraffic2,#parameter,#wirelessCallRate_interfere,#wirelessCallRate_zhicha,#wirelessCallRate_RRCEstSucc,#wirelessCallRate_ERABEstSucc{
	   border:0;
	   background-color: #fff;
	}
</style> 
<script src="plugins/bootstrap-multiselect/bootstrap-multiselect.js"></script>
<link href="plugins/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" />
<!-- jQuery 2.2.0 -->
<!-- datepicker -->
<!--  <link type="text/css" rel="stylesheet" href="plugins/datepicker/css/datepicker.css">
 <script type="text/javascript" src="plugins/datepicker/js/bootstrap-datepicker.js"></script> -->

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
<script type="text/javascript" src="plugins/highcharts/js/highcharts-more.js"></script>

<!--loading-->
<!-- <link rel="stylesheet" href="plugins/loading/css/ladda-themeless.min.css"> -->
<script src="plugins/loading/js/spin.js"></script>
<script src="plugins/loading/js/ladda.js"></script>

<!-- <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=XtxLWdHvIBw0FKDLBh835SwO"></script> -->
<script type="text/javascript" src="plugins/baidumapv2/baidumap_offline_v2_load.js"></script>
<link rel="stylesheet" type="text/css" href="plugins/baidumapv2/css/baidu_map_v2.css"/>
<script src="plugins/mapv/Mapv.js"></script>
<style type='text/css'>
	.datepicker table tr td.today, .datepicker table tr td.today:hover, .datepicker table tr td.today.disabled, .datepicker table tr td.today.disabled:hover {
    background: rgba(0, 255, 0, 0.2) none repeat scroll 0 0;
    border-color: #ffb733;
	}	
</style>
 <!-- treegrid -->
<link rel="stylesheet" href="plugins/EasyUI/themes/bootstrap/datagrid.css">
<link rel="stylesheet" href="dist/css/LTETemplateManage.css">
<script src="plugins/EasyUI/jquery.easyui.min.js"></script>

@endsection
<script src="plugins/jQuery/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="dist/js/VolteCellAnalysis/srvccbadcell.js"></script>
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
