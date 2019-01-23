<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::auth();

Route::get('/home', 'NetworkController@index');

Route::get('/network', 'NetworkController@index');

// Route::get('/scale', 'NetworkScaleController@index');
Route::get('/scale', function () {
    return view('network.scale');
})->middleware('auth');
Route::group(['prefix' => 'scale'], function () {
    Route::get('bscversion_type', 'NetworkScaleController@getBSCversionByType');
    Route::get('bscversion_city', 'NetworkScaleController@getBSCversionByCity');
    Route::get('bscSiteType', 'NetworkScaleController@getBSCSiteType');
    Route::get('bscSlave', 'NetworkScaleController@getBSCSlave');
    Route::get('bscCA', 'NetworkScaleController@getBSCCA');
    Route::get('bscSiteTypeCity', 'NetworkScaleController@getBSCSiteTypeCity');
    Route::get('bscSlaveCity', 'NetworkScaleController@getBSCSlaveCity');
    Route::get('bscCACity', 'NetworkScaleController@getBSCCACity');
    Route::get('meContextNum', 'NetworkScaleController@getMeContextNum');
    Route::get('cellNum', 'NetworkScaleController@getCellNum');
    Route::get('slaveNum', 'NetworkScaleController@getSlaveNum');
    Route::get('meContextNumByCity', 'NetworkScaleController@getMeContextNumByCity');
    Route::get('cellNumByCity', 'NetworkScaleController@getCellNumByCity');
    Route::get('slaveNumByCity', 'NetworkScaleController@getSlaveNumByCity');
    Route::get('numOnAutoKPI', 'NetworkScaleController@getNumOnAutoKPI');
    Route::get('numOnAutoKPIByCity', 'NetworkScaleController@getNumOnAutoKPIByCity');
    Route::get('rruandSlave_city', 'NetworkScaleController@getRRUAndSlaveByCity');
    Route::get('rruandSlave_slave', 'NetworkScaleController@getRRUAndSlaveBySlave');
    Route::get('volteCalls', 'NetworkScaleController@getVolteCalls');
    Route::get('volteCallsByCity', 'NetworkScaleController@getVolteCallsByCity');
    Route::get('citysColor', 'NetworkScaleController@getCitysColor');
    Route::get('RRUNum_city','NetworkScaleController@getRRUUnitTypeCity');
    Route::get('DUNum_city','NetworkScaleController@getDUProductData');
    Route::get('MMEGI_TAC_num','NetworkScaleController@getMMEGITACData');
});

Route::get('/switch', 'SwitchController@index');
Route::get('/switchSite', 'SwitchController@getSwitchSite');
Route::get('/switchData', 'SwitchController@getSwitchData');

Route::get('/getYellowColor', 'SwitchController@getYellowColor');
Route::get('/getYellowColorIn', 'SwitchController@getYellowColorIn');

Route::get('/fdfdh', 'SwitchController@getfdfdh');
Route::get('/switchDetail', 'SwitchController@getSwitchDetail');
Route::get('/handoverin', 'SwitchController@getHandOverIn');
Route::get('/handOverInDetail', 'SwitchController@getHandOverInDetail');

Route::get('/switchDefineDetail', 'SwitchController@getSwitchDefineDetail');

Route::get('/switchIn', 'SwitchController@indexIn');
Route::get('/switchdefine', 'SwitchController@indexDefine');
Route::get('/switchDataDefine', 'SwitchController@getSwitchDataDefine');

Route::get('/switchOutTable', 'SwitchController@getSwitchOutTable');
Route::get('/switchOutTableIn', 'SwitchController@getSwitchOutTableIn');

Route::get('/RRCusers', 'SwitchController@getRRCusers');
Route::get('/wireLessLost', 'SwitchController@getWireLessLost');
Route::get('/PUSCHInterfere', 'SwitchController@getPUSCHInterfere');

Route::get('/PUSCHInterferein', 'SwitchController@getPUSCHInterferein');
Route::get('/handoverSuccin', 'SwitchController@getHandoverSuccin');
Route::get('/RRCusersin', 'SwitchController@getRRCusersin');
Route::get('/wireLessLostin', 'SwitchController@getWireLessLostin');

//zjj
Route::get('/paramQuery', function () {
    return view('parameterAnalysis.paramQuery');
})->middleware('auth');
Route::group(['prefix' => 'paramQuery', 'namespace' => 'ParameterAnalysis'], function () {
    Route::post('getParamTasks', 'ParamQueryController@getParamTasks');
    Route::post('getParamItems', 'ParamQueryController@getParamItems');
    Route::post('getParamCitys', 'ParamQueryController@getParamCitys');
    Route::post('getParamTableField', 'ParamQueryController@getParamTableField');
    Route::post('exportParamFile', 'ParamQueryController@exportParamFile');
    Route::post('getParamData', 'ParamQueryController@getParamData');
    Route::post('getFeatureList', 'ParamQueryController@getFeatureList');
});
Route::post('/paramQuery/getAllSubNetwork', 'ParameterAnalysis\ParamQueryController@getAllSubNetwork');
Route::get('/consistencyCheck', function () {
    return view('parameterAnalysis.consistencyCheck');
})->middleware('auth');
Route::group(['prefix' => 'consistencyCheck', 'namespace' => 'ParameterAnalysis'], function () {
    Route::post('getTasks', 'ConsistencyCheckController@getTasks');
    Route::post('getCities', 'ConsistencyCheckController@getCities');
    Route::post('getCityList','ConsistencyCheckController@getCityList');
    Route::post('consistencyCheckDistribute', 'ConsistencyCheckController@getDistributeData');
    Route::post('getTableField', 'ConsistencyCheckController@getTableField');
    Route::post('getItems', 'ConsistencyCheckController@getItems');
    Route::post('exportFile', 'ConsistencyCheckController@exportFile');
    Route::post('getOssInfoItems', 'ConsistencyCheckController@getOssInfoItems');
    Route::post('exportOssInfoFile', 'ConsistencyCheckController@exportOssInfoFile');
    Route::post('getFileContent', 'ConsistencyCheckController@getFileContent');
    Route::post('exportTemplate', 'ConsistencyCheckController@exportTemplate');
    Route::post('exportDT', 'ConsistencyCheckController@exportDT');
});
Route::get('/weak', function () {
    return view('network.weak');
})->middleware('auth');
Route::group(['prefix' => 'weak'], function () {
    Route::get('baselineParamNum', 'WeakController@getBaselineParamNum');
    Route::get('baselineBSNum', 'WeakController@getBaselineBSNum');
    Route::get('consistencyParamNum', 'WeakController@getConsistencyParamNum');
    Route::get('consistencyBSNum', 'WeakController@getConsistencyBSNum');
});
Route::get('/badCellOverview', 'BadCellController@getBadCellData');
Route::get('/badCellOverview/drillDownDonutPie', 'BadCellController@getDrillDownDonutPie');
Route::get('/interfereOverview', 'InterfereController@getInterfereData');
Route::get('/WeakCoverOverview', 'WeakCoverOverviewController@getWeakCoverData');
Route::get('/overlapCoverview', 'WeakoverlapCoverOverviewController@getOverlapCoverData');
Route::get('/currentAlarm', 'AlarmController@getCurrentAlarm');
Route::get('/currentAlarm/drillDownDonutPie', 'AlarmController@getDrillDownDonutPie');
Route::get('/historyAlarm', 'AlarmController@getHistoryAlarm');
Route::get('/historyAlarm/historyAlarmDateData', 'AlarmController@getHistoryAlarmDateData');
Route::get('/historyAlarm/drillDownDonutPie', 'AlarmController@getHistoryDrillDownDonutPie');

//zjj

//lijian
Route::get('/LTEQuery', function () {
    return view('QueryAnalysis.LTEQuery');
})->middleware('auth');

// Route::get('/LTEQuery/getLTETreeData', 'QueryAnalysis\LTEQueryController@getLTETreeData');      //20170330
Route::get('/LTEQuery/getLTETreeData', 'QueryAnalysis\LTEQueryController@getTreeData');

Route::get('/LTEQuery/getAllCity', 'QueryAnalysis\LTEQueryController@getAllCity');

Route::get('/LTEQuery/getAllSubNetwork', 'QueryAnalysis\LTEQueryController@getAllSubNetwork');

Route::get('/LTEQuery/getFormatAllSubNetwork', 'QueryAnalysis\LTEQueryController@getFormatAllSubNetwork');

Route::get('/LTEQuery/searchLTETreeData', 'QueryAnalysis\LTEQueryController@searchLTETreeData');

Route::post('/LTEQuery/templateQuery', 'QueryAnalysis\LTEQueryController@templateQuery');

Route::post('/LTEQuery/uploadFile', 'QueryAnalysis\LTEQueryController@uploadFile');
Route::get('/LTEQuery/LTETime', 'QueryAnalysis\LTEQueryController@LTETime');
Route::get('/NBIQuery', function () {
    return view('QueryAnalysis.NBIQuery');
})->middleware('auth');

// Route::get('/NBIQuery/getNbiTreeData', 'QueryAnalysis\NBIQueryController@getNbiTreeData');    //20170330
Route::get('/NBIQuery/getNbiTreeData', 'QueryAnalysis\NBIQueryController@getTreeData');

Route::post('/NBIQuery/templateQuery', 'QueryAnalysis\NBIQueryController@templateQuery');
Route::post('NBIQuery/templateQueryHeader', 'QueryAnalysis\NBIQueryController@templateQueryHeader');
Route::get('/NBIQuery/NBIsTime', 'QueryAnalysis\NBIQueryController@NBITime');
Route::get('/network', function () {
    return view('network.survey');
})->middleware('auth');

//zhangyan
Route::get('/baselineCheck', function () {
    return view('parameterAnalysis.baselineCheck');
})->middleware('auth');
Route::group(['prefix' => 'baselineCheck', 'namespace' => 'ParameterAnalysis'],function(){
    Route::post('getBaseTree', 'BaselineCheckController@getBaseTree');
    Route::post('getParamTasks', 'BaselineCheckController@getParamTasks');
    Route::post('getParamCitys', 'BaselineCheckController@getParamCitys');
    Route::post('getAllCity', 'BaselineCheckController@getAllCity');
    Route::post('getChartDataCategory', 'BaselineCheckController@getChartDataCategory');
    Route::post('getTableField', 'BaselineCheckController@getTableField');
    Route::post('getParamItems', 'BaselineCheckController@getParamItems');
    Route::post('baselineFile', 'BaselineCheckController@baselineFile');
    Route::post('getFileContent', 'BaselineCheckController@getFileContent');
});
//ZHUJJ
Route::get('/scCheck', function () {
    return view('parameterAnalysis.scCheck');
})->middleware('auth');
Route::group(['prefix'=>'scCheck','namespace'=>'ParameterAnalysis'],function(){
    Route::get('getTasks','SCCheckController@getTasks');
    Route::post('getCityList', 'SCCheckController@getCityList');
    Route::post('getFileContent','SCCheckController@getFileContent');
    Route::post('downloadTemplateFile','SCCheckController@downloadTemplateFile');
    Route::post('runProcedure','SCCheckController@runProcedure');
    Route::post('getTableField','SCCheckController@getTableField');
    Route::post('getItems','SCCheckController@getItems');
    Route::post('downloadFile','SCCheckController@downloadFile');
});
Route::get('/extremeHighTrafficCell',function(){
    return view('badCellAnalysis.extremeHighTrafficCell');
})->middleware('auth');
Route::group(['prefix'=>'extremeHighTrafficCell','namespace'=>'BadCellAnalysis'],function(){
    Route::get('getTasks','ExtremeHighTrafficCellController@getTasks');
    Route::get('getCitys','ExtremeHighTrafficCellController@getCitys');
    Route::post('getTableField','ExtremeHighTrafficCellController@getTableField');
    Route::get('getCellData','ExtremeHighTrafficCellController@getCellData');
    Route::post('getAllCellData','ExtremeHighTrafficCellController@getAllCellData');
});
//lijian
Route::get('lowAccess', 'NetworkChartsController@getLowAccess');
Route::get('lowAccessTrend', 'NetworkChartsController@getLowAccessTrend');

Route::get('highLost', 'NetworkChartsController@getHighLost');
Route::get('highLostTrend', 'NetworkChartsController@getHighLostTrend');

Route::get('badHandover', 'NetworkChartsController@getBadHandover');
Route::get('badHandoverTrend', 'NetworkChartsController@getBadHandoverTrend');


Route::get('erabSuccess', 'NetworkChartsController@getErabSuccessHandover');
Route::get('erabSuccessTrend', 'NetworkChartsController@getErabSuccessHandoverTrend');

Route::get('erabLost', 'NetworkChartsController@getErabsLost');
Route::get('erabLostTrend', 'NetworkChartsController@getErabsLostTrend');

Route::get('wirelessSuccess', 'NetworkChartsController@getWirelessSucc');
Route::get('wirelessSuccessTrend', 'NetworkChartsController@getWirelessSuccTrend');

Route::get('volteHandover', 'NetworkChartsController@getVolteHandover');
Route::get('volteHandoverTrend', 'NetworkChartsController@getVolteHandoverTrend');


Route::get('chart1WireSucc', 'NetworkChartsController@getChart1WireSucc');
Route::get('chart1WireSuccTrend', 'NetworkChartsController@getChart1WireSuccTrend');

Route::get('chart1ErbLost', 'NetworkChartsController@getChart1ErbLost');
Route::get('chart1ErbLostTrend', 'NetworkChartsController@getChart1ErbLostTrend');

Route::get('chart1VideoSucc', 'NetworkChartsController@getChart1VideoSucc');
Route::get('chart1VideoSuccTrend', 'NetworkChartsController@getChart1VideoSuccTrend');

Route::get('chart1EsrvccHander', 'NetworkChartsController@getChart1EsrvccHander');
Route::get('chart1EsrvccHanderTrend', 'NetworkChartsController@getChart1EsrvccHanderTrend');

//自定义Y轴
Route::get('lowAccessDefine', 'NetworkChartsController@getLowAccessDefine');
Route::get('highLostDefine', 'NetworkChartsController@getHighLostDefine');
Route::get('badHandoverDefine', 'NetworkChartsController@getBadHandoverDefine');
Route::get('lowAccessTrendMore', 'NetworkChartsController@getLowAccessTrendMore');
Route::get('highLostTrendMore', 'NetworkChartsController@getHighLostTrendMore');
Route::get('badHandoverTrendMore', 'NetworkChartsController@getBadHandoverTrendMore');

//zhouyanqiu
Route::get('/GSMQuery', function () {
    return view('QueryAnalysis.GSMQuery');
})->middleware('auth');

// Route::get('/GSMQuery/getGSMTreeData', 'QueryAnalysis\GSMQueryController@getGSMTreeData');    //20170330
Route::get('/GSMQuery/getGSMTreeData', 'QueryAnalysis\GSMQueryController@getTreeData');
Route::get('/GSMQuery/GSMTime', 'QueryAnalysis\GSMQueryController@GSMTime');
Route::get('/GSMQuery/searchGSMTreeData', 'QueryAnalysis\GSMQueryController@searchGSMTreeData');

Route::get('/GSMQuery/getAllCity', 'QueryAnalysis\GSMQueryController@getAllCity');

Route::post('/GSMQuery/templateQuery', 'QueryAnalysis\GSMQueryController@templateQuery');

Route::get('/NBIQuery/searchNBITreeData', 'QueryAnalysis\NBIQueryController@searchNBITreeData');


//lijian
Route::get('/threeKeysGauge', 'NetworkChartsController@getThreeKeysGauge');
Route::get('/volteGauge', 'NetworkChartsController@getvolteGauge');
Route::get('/videosGauge', 'NetworkChartsController@getVideoGauge');


//lijian
Route::get('/CustomQuery', function () {
    return view('QueryAnalysis.CustomQuery');
})->middleware('auth');

// Route::get('/CustomQuery/getCustomTreeData', 'QueryAnalysis\CustomQueryController@getCustomTreeData');   //20170330
Route::get('/CustomQuery/getCustomTreeData', 'QueryAnalysis\CustomQueryController@getTreeData');
Route::get('/CustomQuery/searchCustomTreeData', 'QueryAnalysis\CustomQueryController@getSearchCustomTreeData');
Route::get('/CustomQuery/getAllCity', 'QueryAnalysis\CustomQueryController@getAllCity');
Route::get('/getKpiFormula', 'QueryAnalysis\CustomQueryController@getKpiFormula');
Route::post('/getTable', 'QueryAnalysis\CustomQueryController@getTable');
Route::get('/deleteMode', 'QueryAnalysis\CustomQueryController@deleteMode');
Route::get('/insertMode', 'QueryAnalysis\CustomQueryController@insertMode');
Route::get('/saveMode', 'QueryAnalysis\CustomQueryController@saveMode');


//xuyang
Route::get('/userManage', function () {
    return view('systemManage.userManage');
})->middleware('auth');
Route::get('/userManage/templateQuery', 'SystemManage\UserController@templateQuery');
Route::get('/userManage/deleteUser', 'SystemManage\UserController@deleteUser');
Route::get('/userManage/updateUser', 'SystemManage\UserController@updateUser');
Route::get('/userManage/getType', 'SystemManage\UserController@getType');
Route::get('/userManage/treeQuery', 'SystemManage\UserController@treeQuery');
Route::post('/userManage/updateUserType', 'SystemManage\UserController@updateUserType');
Route::get('/userManage/deleteUserType', 'SystemManage\UserController@deleteUserType');

//zhouyanqiu
Route::get('/lowAccessCell', function () {
    return view('badCellAnalysis.lowAccessCell');
})->middleware('auth');

Route::get('/highLostCell', function () {
    return view('badCellAnalysis.highLostCell');
})->middleware('auth');

Route::get('/badHandoverCell', function () {
    return view('badCellAnalysis.badHandoverCell');
})->middleware('auth');
Route::get('/badCell/lowStartTime', 'BadCellAnalysis\BadCellController@getlowTime');
Route::get('/badCell/setHighlostTime', 'BadCellAnalysis\BadCellController@getHighLostTime');
Route::get('/badCell/setBadHandoverTime', 'BadCellAnalysis\BadCellController@getBadHandoverTime');
Route::get('/badCell/getAllCity', 'BadCellAnalysis\BadCellController@getAllCity');
Route::get('/badCell/templateQuery', 'BadCellAnalysis\BadCellController@templateQuery');
Route::get('/badCell/getalarmWorstCell', 'BadCellAnalysis\BadCellController@getalarmWorstCell');
Route::get('/badCell/getChartData', 'BadCellAnalysis\BadCellController@getChartData');
Route::get('/badCell/getweakCoverageCell', 'BadCellAnalysis\BadCellController@getWeakCoverageCell');


Route::get('/badCell/getLowAccessCellData', 'BadCellAnalysis\BadCellController@getLowAccessCellData');

//0524
Route::get('/badCell/getCounterLoseResultDistribution', 'BadCellAnalysis\BadCellController@getCounterLoseResultDistribution');

Route::post('/CustomQuery/saveModeChange', 'QueryAnalysis\CustomQueryController@saveModeChange');

Route::get('/alarmNum', 'BadCellAnalysis\BadCellController@getAlarmNum');
Route::get('/overlapCeakCoverNum', 'BadCellAnalysis\BadCellController@getOverlapCeakCoverNum');

Route::get('/badCell/getHighLostCellData', 'BadCellAnalysis\BadCellController@getHighLostCellData');
Route::get('/badCell/getBadHandoverCellData', 'BadCellAnalysis\BadCellController@getBadHandoverCellData');

Route::get('/badCell/getInterfereAnalysis', 'BadCellAnalysis\BadCellController@getInterfereAnalysis');


//lijian 0315
Route::get('/badCell/getCellAlarmClassifyTable', 'BadCellAnalysis\BadCellController@getCellAlarmClassifyTable');
Route::get('/badCell/getErbsAlarmClassifyTable', 'BadCellAnalysis\BadCellController@getErbsAlarmClassifyTable');
Route::get('/badCell/getCellAlarmClassify', 'BadCellAnalysis\BadCellController@getCellAlarmClassify');
Route::get('/badCell/getErbsAlarmClassify', 'BadCellAnalysis\BadCellController@getErbsAlarmClassify');
//zhujj参数

Route::get('/badCell/getBaselineCheckData', 'BadCellAnalysis\BadCellController@getBaselineCheckData');

//xuyang
Route::get('/emailManage', function () {
    return view('systemManage.emailManage');
})->middleware('auth');
Route::post('/emailManage/treeQuery', 'SystemManage\EmailController@treeQuery');
Route::post('/emailManage/getTableData', 'SystemManage\EmailController@getTableData');
Route::post('/emailManage/insertDownload', 'SystemManage\EmailController@insertDownload');
Route::post('/emailManage/deleteDownload', 'SystemManage\EmailController@deleteDownload');
Route::post('/emailManage/getAllCity', 'SystemManage\EmailController@getAllCity');
Route::post('/emailManage/updateScope', 'SystemManage\EmailController@updateScope');
Route::post('/emailManage/getScope', 'SystemManage\EmailController@getScope');
Route::post('/emailManage/deleteScope', 'SystemManage\EmailController@deleteScope');
Route::post('/emailManage/getRole', 'SystemManage\EmailController@getRole');

//xuyang
Route::get('/ENIQManage', function () {
    return view('systemManage.ENIQManage');
})->middleware('auth');
Route::get('/ENIQManage/Query4G', 'SystemManage\ENIQController@query4G');
Route::get('/ENIQManage/Query2G', 'SystemManage\ENIQController@query2G');
Route::get('/ENIQManage/updateENIQ', 'SystemManage\ENIQController@updateENIQ');
Route::get('/ENIQManage/deleteENIQ', 'SystemManage\ENIQController@deleteENIQ');

//xuyang
Route::get('/siteManage', function () {
    return view('systemManage.siteManage');
})->middleware('auth');
Route::post('/siteManage/TreeQuery', 'SystemManage\SiteController@treeQuery');
Route::get('/siteManage/QuerySite4G', 'SystemManage\SiteController@querySite4G');
Route::get('/siteManage/QuerySite2G', 'SystemManage\SiteController@querySite2G');
Route::post('/siteManage/getFileContent', 'SystemManage\SiteController@getFileContent');
Route::get('/siteManage/downloadFile', 'SystemManage\SiteController@downloadFile');
Route::get('/siteManage/downloadTemplateFile', 'SystemManage\SiteController@downloadTemplateFile');


//haile
Route::get('/weakCover', function () {
    return view('network.weakCover');
})->middleware('auth');
Route::get('/weakCoverDate', 'WeakCoverController@getDate');
Route::get('/weakCoverCells', 'WeakCoverController@getCells');
Route::get('/weakCoverCharts', 'WeakCoverController@getCharts');
Route::get('/weakCoverDatee', 'WeakCoverController@weakCoverDatee');
Route::post('/weakCover/getOneCell', 'WeakCoverController@getOneCell');

Route::get('/interCloud', function () {
    return view('network.interCloud');
})->middleware('auth');
Route::get('/interCloudCells', 'InterCloudController@getCells');
Route::get('/interCloudChannel', 'InterCloudController@getChannels');
Route::get('/interfepointDate', 'InterCloudController@interfepointDate');

//lj
//重叠覆盖点图
Route::get('/overlapCoverPoint', function () {
    return view('network.overlapCoverPoint');
})->middleware('auth');
Route::get('/overlapCoverPointCells', 'WeakCoverController@getOverlapCoverPointCells');
Route::get('/overlapCoverPointDate', 'WeakCoverController@overlapCoverPointDate');
Route::post('/overlapCoverPoint/getCell', 'WeakCoverController@getCell');

//lijian
Route::get('/interPointCloud', function () {
    return view('network.interPointCloud');
})->middleware('auth');
Route::get('/interPointCloudChannel', 'InterCloudController@getPointChannels');
Route::get('/interPointCloudCells', 'InterCloudController@getCells');
Route::post('/interPointCloud/getCell', 'InterCloudController@getCell');

//xuyang
Route::get('/LTETemplateManage', function () {
    return view('QueryAnalysis.LTETemplateManage');
})->middleware('auth');
// Route::get('/LTETemplateManage/getLTETreeData', 'QueryAnalysis\LTETemplateController@getLTETreeData');  //20170330
Route::get('/LTETemplateManage/getLTETreeData', 'QueryAnalysis\LTETemplateController@getTreeData');
Route::get('/LTETemplateManage/searchLTETreeData', 'QueryAnalysis\LTETemplateController@searchLTETreeData');
Route::get('/LTETemplateManage/getElementTree', 'QueryAnalysis\LTETemplateController@getElementTree');
Route::get('/LTETemplateManage/getKpiNamebyId', 'QueryAnalysis\LTETemplateController@getKpiNamebyId');
Route::post('/LTETemplateManage/getTreeTemplate', 'QueryAnalysis\LTETemplateController@getTreeTemplate');
Route::get('/LTETemplateManage/updateFormula', 'QueryAnalysis\LTETemplateController@updateFormula');
Route::get('/LTETemplateManage/deleteFormula', 'QueryAnalysis\LTETemplateController@deleteFormula');
Route::get('/LTETemplateManage/searchTreeTemplate', 'QueryAnalysis\LTETemplateController@searchTreeTemplate');
Route::get('/LTETemplateManage/updateElement', 'QueryAnalysis\LTETemplateController@updateElement');
Route::get('/LTETemplateManage/addMode', 'QueryAnalysis\LTETemplateController@addMode');
Route::get('/LTETemplateManage/deleteMode', 'QueryAnalysis\LTETemplateController@deleteMode');

Route::get('/kpiExport', 'Exporter\KpiExporter@export');
Route::get('/scaleExport', 'Exporter\ScaleExporter@export');
//lijian
Route::get('/weakExport', 'Exporter\NetworkChartsExporter@export');


//xuyang
Route::get('/NBITemplateManage', function () {
    return view('QueryAnalysis.NBITemplateManage');
})->middleware('auth');

// Route::get('/NBITemplateManage/getNBITreeData', 'QueryAnalysis\NBITemplateController@getNBITreeData');   //20170330
Route::get('/NBITemplateManage/getNBITreeData', 'QueryAnalysis\NBITemplateController@getTreeData');
Route::get('/NBITemplateManage/searchNBITreeData', 'QueryAnalysis\NBITemplateController@searchNBITreeData');
Route::get('/NBITemplateManage/getElementTree', 'QueryAnalysis\NBITemplateController@getElementTree');
Route::get('/NBITemplateManage/getKpiNamebyId', 'QueryAnalysis\NBITemplateController@getKpiNamebyId');
Route::post('/NBITemplateManage/getTreeTemplate', 'QueryAnalysis\NBITemplateController@getTreeTemplate');
Route::get('/NBITemplateManage/updateFormula', 'QueryAnalysis\NBITemplateController@updateFormula');
Route::get('/NBITemplateManage/deleteFormula', 'QueryAnalysis\NBITemplateController@deleteFormula');
Route::get('/NBITemplateManage/searchTreeTemplate', 'QueryAnalysis\NBITemplateController@searchTreeTemplate');
Route::get('/NBITemplateManage/updateElement', 'QueryAnalysis\NBITemplateController@updateElement');
Route::get('/NBITemplateManage/addMode', 'QueryAnalysis\NBITemplateController@addMode');
Route::get('/NBITemplateManage/deleteMode', 'QueryAnalysis\NBITemplateController@deleteMode');

//xuyang
Route::get('/GSMTemplateManage', function () {
    return view('QueryAnalysis.GSMTemplateManage');
})->middleware('auth');

// Route::get('/GSMTemplateManage/getGSMTreeData', 'QueryAnalysis\GSMTemplateController@getGSMTreeData');  //20170330
Route::get('/GSMTemplateManage/getGSMTreeData', 'QueryAnalysis\GSMTemplateController@getTreeData');
Route::get('/GSMTemplateManage/searchGSMTreeData', 'QueryAnalysis\GSMTemplateController@searchGSMTreeData');
Route::get('/GSMTemplateManage/getElementTree', 'QueryAnalysis\GSMTemplateController@getElementTree');
Route::get('/GSMTemplateManage/getKpiNamebyId', 'QueryAnalysis\GSMTemplateController@getKpiNamebyId');
Route::post('/GSMTemplateManage/getTreeTemplate', 'QueryAnalysis\GSMTemplateController@getTreeTemplate');
Route::get('/GSMTemplateManage/updateFormula', 'QueryAnalysis\GSMTemplateController@updateFormula');
Route::get('/GSMTemplateManage/deleteFormula', 'QueryAnalysis\GSMTemplateController@deleteFormula');
Route::get('/GSMTemplateManage/searchTreeTemplate', 'QueryAnalysis\GSMTemplateController@searchTreeTemplate');
Route::get('/GSMTemplateManage/updateElement', 'QueryAnalysis\GSMTemplateController@updateElement');
Route::get('/GSMTemplateManage/addMode', 'QueryAnalysis\GSMTemplateController@addMode');
Route::get('/GSMTemplateManage/deleteMode', 'QueryAnalysis\GSMTemplateController@deleteMode');

//xuyang
Route::get('/storageManage', function () {
    return view('systemManage.storageManage');
})->middleware('auth');
Route::get('/storageManage/taskQuery', 'SystemManage\StorageController@taskQuery');
Route::get('/storageManage/getTaskTraceDir', 'SystemManage\StorageController@getTaskTraceDir');
Route::post('/storageManage/addTask', 'SystemManage\StorageController@addTask');
Route::get('/storageManage/deleteTask', 'SystemManage\StorageController@deleteTask');
Route::get('/storageManage/monitor', 'SystemManage\StorageController@monitor');
Route::get('/storageManage/runTask', 'SystemManage\StorageController@runTask');
Route::get('/storageManage/stopTask', 'SystemManage\StorageController@stopTask');
Route::get('/storageManage/exportFile', 'SystemManage\StorageController@exportFile');

//20161117-lijian
Route::get('/weakCoverCloud', function () {
    return view('network.weakCoverCloud');
})->middleware('auth');
Route::get('/weakCoverCloudCells', 'InterCloudController@getweakCoverCells');

//lijian
Route::get('/weakCoverRatio', function () {
    return view('network.weakCoverRatio');
})->middleware('auth');
Route::get('/SearchWeakCoverRatio', 'WeakCoverRatioController@searchWeakCoverRatio');
Route::get('/startTime', 'WeakCoverRatioController@startTime');


//shan
Route::get('/RSRPAnalysis', function () {
    return view('network.RSRPAnalysis');
})->middleware('auth');
Route::post('/getRSRPdate', 'RSRPAnalysisController@getRSRPdate');
Route::post('/RSRPAnalysisdata', 'RSRPAnalysisController@getRSRPAnalysisData');

//shan
Route::get('/weakCoverRate', function () {
    return view('network.weakCoverRate');
})->middleware('auth');
// Route::get('/SearchWeakCoverRate', 'WeakCoverRateController@SearchWeakCoverRate');
Route::get('/getCitys', 'WeakCoverRateController@getCitys');
Route::get('/getMroWeakCoverageDataHeader', 'WeakCoverRateController@getMroWeakCoverageDataHeader');
Route::get('/getMroWeakCoverageData', 'WeakCoverRateController@getMroWeakCoverageData');
Route::get('/getAllMroWeakCoverageData', 'WeakCoverRateController@getAllMroWeakCoverageData');
Route::get('/weakCoverRateDate', 'WeakCoverRateController@weakCoverRateDate');


//xuyang
Route::get('/signalingBacktracking', function () {
    return view('complaintHandling.signalingBacktracking');
})->middleware('auth');
Route::get('/signalingBacktracking/getDataBase', 'ComplaintHandling\SignalingBacktrackingController@getDataBase');
Route::get('/signalingBacktracking/getEventNameandEcgi', 'ComplaintHandling\SignalingBacktrackingController@getEventNameandEcgi');
Route::post('/signalingBacktracking/getEventData', 'ComplaintHandling\SignalingBacktrackingController@getEventData');
Route::get('/signalingBacktracking/getEventDataHeader', 'ComplaintHandling\SignalingBacktrackingController@getEventDataHeader');
Route::post('/signalingBacktracking/getAllEventData', 'ComplaintHandling\SignalingBacktrackingController@getAllEventData');
Route::get('/signalingBacktracking/showMessage', 'ComplaintHandling\SignalingBacktrackingController@showMessage');
Route::post('/signalingBacktracking/exportCSV', 'ComplaintHandling\SignalingBacktrackingController@exportCSV');

//xuyang
Route::get('/nav/getUser', 'NavController@getUser');
Route::get('/nav/signout', 'NavController@signout');

//xuyang
Route::get('/paramsManage', function () {
    return view('systemManage.paramsManage');
})->middleware('auth');
Route::get('/paramsManage/getBaselineTreeData', 'SystemManage\ParamsController@getBaselineTreeData');
Route::get('/paramsManage/searchBaselineTreeData', 'SystemManage\ParamsController@searchBaselineTreeData');
Route::get('/paramsManage/getBaselineTableData', 'SystemManage\ParamsController@getBaselineTableData');
Route::get('/paramsManage/downloadFile', 'SystemManage\ParamsController@downloadFile');
Route::post('/paramsManage/uploadFile', 'SystemManage\ParamsController@uploadFile');
Route::get('/paramsManage/addMode', 'SystemManage\ParamsController@addMode');
Route::get('/paramsManage/deleteMode', 'SystemManage\ParamsController@deleteMode');
//zjj
Route::get('/nav/getSessions', 'NavController@getSessions');

//xuyang 参数分布
Route::get('/paramDistribution', function () {
    return view('parameterAnalysis.paramDistribution');
})->middleware('auth');
Route::post('/paramDistribution/getDate', 'ParameterAnalysis\ParamDistributionController@getDate');
Route::post('/paramDistribution/getParameterList', 'ParameterAnalysis\ParamDistributionController@getParameterList');
//Route::post('/paramDistribution/getCity', 'ParameterAnalysis\ParamDistributionController@getCity');
Route::post('/paramDistribution/getChartData', 'ParameterAnalysis\ParamDistributionController@getChartData');
Route::post('/paramDistribution/getCitySelect', 'ParameterAnalysis\ParamDistributionController@getCitySelect');
Route::post('/paramDistribution/getTableHeader', 'ParameterAnalysis\ParamDistributionController@getTableHeader');
Route::post('/paramDistribution/getTableData', 'ParameterAnalysis\ParamDistributionController@getTableData');
//Route::post('/paramDistribution/getAllTableData', 'ParameterAnalysis\ParamDistributionController@getAllTableData');
Route::post('/paramDistribution/exportCSV', 'ParameterAnalysis\ParamDistributionController@exportCSV');
Route::post('/paramDistribution/getAllSubNetwork', 'ParameterAnalysis\ParamDistributionController@getAllSubNetwork');
//update lijian
Route::get('updateSearchContext', 'ParameterAnalysis\ParamDistributionController@getTreeData');
Route::get('updateSearch', 'ParameterAnalysis\ParamDistributionController@getUpdateSearch');

//xuyang 信令分析
Route::get('/signalingAnalysis', function () {
    return view('complaintHandling.signalingAnalysis');
})->middleware('auth');
Route::get('/signalingAnalysis/queryKeyword', 'ComplaintHandling\SignalingAnalysisController@queryKeyword');

//xuyang 数据源管理
Route::get('/dataSourceManage', function () {
    return view('systemManage.dataSourceManage');
})->middleware('auth');
Route::post('/dataSourceManage/getNode', 'SystemManage\DataSourceController@getNode');
Route::get('/dataSourceManage/getFileName', 'SystemManage\DataSourceController@getFileName');
Route::post('/dataSourceManage/ctrTreeItems', 'SystemManage\DataSourceController@ctrTreeItems');

//lijian 网格优化
Route::get('/GSMNeighborAnalysis', function () {
    return view('networkOptimization.NeighAnalysis');
})->middleware('auth');
Route::get('/networkOptimization/GSMNeighAnalysis', 'NetworkOptimization\GSMNeighAnalysisController@getGSMNeighData');
/*Route::get('/NetworkOptimization/getAllDatabase','NetworkOptimization\GSMNeighAnalysisController@getGSMNeighDatabases');*/
Route::get('/NetworkOptimization/getAllCity', 'NetworkOptimization\GSMNeighAnalysisController@getAllCity');
Route::get('/fdfd', 'NetworkOptimization\GSMNeighAnalysisController@getfdfd');
Route::get('/NetworkOptimization/getLTENeighborAnalysisDate', 'NetworkOptimization\GSMNeighAnalysisController@getLTENeighborAnalysisDate');

Route::get('/LTENeighborAnalysis', function () {
    return view('networkOptimization.LTENeighAnalysis');
})->middleware('auth');
Route::get('/networkOptimization/LTENeighAnalysis', 'NetworkOptimization\GSMNeighAnalysisController@getLTENeighData');
Route::get('/networkOptimization/GSMNeighAnalysisSplit', 'NetworkOptimization\GSMNeighAnalysisController@getGSMNeighDataSplit');
Route::get('/networkOptimization/LTENeighAnalysisSplit', 'NetworkOptimization\GSMNeighAnalysisController@getLTENeighDataSplit');
Route::get('/networkOptimization/GSMNeighAnalysisAll', 'NetworkOptimization\GSMNeighAnalysisController@getGSMNeighDataAll');
Route::get('/networkOptimization/GSMNeighAnalysisLteAll', 'NetworkOptimization\GSMNeighAnalysisController@getGSMNeighDataLteAll');
Route::post('/networkOptimization/getMREFileContent', 'NetworkOptimization\GSMNeighAnalysisController@getMREFileContent');
Route::post('/networkOptimization/exportWhiteList', 'NetworkOptimization\GSMNeighAnalysisController@exportWhiteList');

//xuyang 当前告警查询
Route::get('/currentAlarmQuery', function () {
    return view('alarmAnalysis.currentAlarmQuery');
})->middleware('auth');
Route::get('/currentAlarmQuery/getCitys', 'AlarmAnalysis\CurrentAlarmQueryController@getCitys');
Route::get('/currentAlarmQuery/getTableData', 'AlarmAnalysis\CurrentAlarmQueryController@getTableData');
Route::post('/currentAlarmQuery/getAllTableData', 'AlarmAnalysis\CurrentAlarmQueryController@getAllTableData');

//xuyang 历史告警查询
Route::get('/historyAlarmQuery', function () {
    return view('alarmAnalysis.historyAlarmQuery');
})->middleware('auth');
Route::get('/historyAlarmQuery/getCitys', 'AlarmAnalysis\HistoryAlarmQueryController@getCitys');
Route::get('/historyAlarmQuery/getTableData', 'AlarmAnalysis\HistoryAlarmQueryController@getTableData');
Route::post('/historyAlarmQuery/getAllTableData', 'AlarmAnalysis\HistoryAlarmQueryController@getAllTableData');
Route::get('/historyAlarmQuery/getHistoryAlarmTime', 'AlarmAnalysis\HistoryAlarmQueryController@getHistoryAlarmTime');

//xuyang PCI MOD 3分析
Route::get('/PCIMOD3Analysis', function () {
    return view('networkOptimization.PCIMOD3Analysis');
})->middleware('auth');
Route::post('/PCIMOD3Analysis/getAllCity', 'NetworkOptimization\PCIMOD3AnalysisController@getAllCity');
Route::post('/PCIMOD3Analysis/PCIMOD3Date', 'NetworkOptimization\PCIMOD3AnalysisController@getPCIMOD3Date');
Route::post('/PCIMOD3Analysis/getMroPCIMOD3DataHeader', 'NetworkOptimization\PCIMOD3AnalysisController@getMroPCIMOD3DataHeader');
Route::post('/PCIMOD3Analysis/getMroPCIMOD3Data', 'NetworkOptimization\PCIMOD3AnalysisController@getMroPCIMOD3Data');
Route::post('/PCIMOD3Analysis/getAllMroPCIMOD3Data', 'NetworkOptimization\PCIMOD3AnalysisController@getAllMroPCIMOD3Data');
Route::post('/PCIMOD3Analysis/getMroPCIMOD3GeniusDataHeader', 'NetworkOptimization\PCIMOD3AnalysisController@getMroPCIMOD3GeniusDataHeader');
Route::post('/PCIMOD3Analysis/getMroPCIMOD3GeniusData', 'NetworkOptimization\PCIMOD3AnalysisController@getMroPCIMOD3GeniusData');
Route::post('/PCIMOD3Analysis/getAllMroPCIMOD3GeniusData', 'NetworkOptimization\PCIMOD3AnalysisController@getAllMroPCIMOD3GeniusData');
Route::post('/PCIMOD3Analysis/PCIMOD3GeniusDate', 'NetworkOptimization\PCIMOD3AnalysisController@getPCIMOD3GeniusDate');

//zhujiaojiao A2门限分析
Route::get('/A2ThresholdAnalysis', function () {
    return view('networkOptimization.A2ThresholdAnalysis');
})->middleware('auth');
Route::get('/A2ThresholdAnalysis/getAllCity', 'NetworkOptimization\A2ThresholdAnalysisController@getAllCity');
Route::get('/fdfde', 'NetworkOptimization\A2ThresholdAnalysisController@getfdfde');
Route::get('/A2ThresholdAnalysis/getMreA2ThresholdDataHeader', 'NetworkOptimization\A2ThresholdAnalysisController@getMreA2ThresholdDataHeader');
Route::get('/A2ThresholdAnalysis/getMreA2ThresholdData', 'NetworkOptimization\A2ThresholdAnalysisController@getMreA2ThresholdData');
Route::get('/A2ThresholdAnalysis/getAllMreA2ThresholdData', 'NetworkOptimization\A2ThresholdAnalysisController@getAllMreA2ThresholdData');
//A5门限分析
Route::get('/A5ThresholdAnalysis', function () {
    return view('networkOptimization.A5ThresholdAnalysis');
})->middleware('auth');
Route::post('/A5ThresholdAnalysis/getAllCity', 'NetworkOptimization\A5ThresholdAnalysisController@getAllCity');
Route::post('/fdfdf', 'NetworkOptimization\A5ThresholdAnalysisController@getfdfdf');
Route::post('/A5ThresholdAnalysis/getMreA5ThresholdDataHeader', 'NetworkOptimization\A5ThresholdAnalysisController@getMreA5ThresholdDataHeader');
Route::post('/A5ThresholdAnalysis/getMreA5ThresholdData', 'NetworkOptimization\A5ThresholdAnalysisController@getMreA5ThresholdData');
Route::post('/A5ThresholdAnalysis/getAllMreA5ThresholdData', 'NetworkOptimization\A5ThresholdAnalysisController@getAllMreA5ThresholdData');

//同频补邻区
//MRO
Route::get('/MROServeNeighAnalysis', function () {
    return view('networkOptimization.MROServeNeighAnalysis');
})->middleware('auth');
Route::get('/MROServeNeighAnalysis/getAllCity', 'NetworkOptimization\MRONeighAnalysisController@getAllCity');
Route::get('/fdfdb', 'NetworkOptimization\MRONeighAnalysisController@getfdfdb');
Route::get('/MROServeNeighAnalysis/getMroServeNeighDataHeader', 'NetworkOptimization\MRONeighAnalysisController@getMroServeNeighDataHeader');
Route::get('/MROServeNeighAnalysis/getMroServeNeighData', 'NetworkOptimization\MRONeighAnalysisController@getMroServeNeighData');
Route::get('/MROServeNeighAnalysis/getAllMroServeNeighData', 'NetworkOptimization\MRONeighAnalysisController@getAllMroServeNeighData');
//MRE
Route::get('/fdfdc', 'NetworkOptimization\MRENeighAnalysisController@getfdfdC');
Route::get('/MREServeNeighAnalysis/getMreServeNeighDataHeader', 'NetworkOptimization\MRENeighAnalysisController@getMreServeNeighDataHeader');
Route::get('/MREServeNeighAnalysis/getMreServeNeighData', 'NetworkOptimization\MRENeighAnalysisController@getMreServeNeighData');
Route::get('/MREServeNeighAnalysis/getAllMreServeNeighData', 'NetworkOptimization\MRENeighAnalysisController@getAllMreServeNeighData');

//CDR补2G邻区
// Route::get('/CDRServeNeighAnalysis',function(){
//     return view('networkOptimization.CDRServeNeighAnalysis');
// });
Route::get('/CDRServeNeighAnalysis/getAllCity', 'NetworkOptimization\CDRNeighAnalysisController@getAllCity');
Route::get('/fdfda', 'NetworkOptimization\CDRNeighAnalysisController@getfdfda');
Route::get('/CDRServeNeighAnalysis/getCdrServeNeighDataHeader', 'NetworkOptimization\CDRNeighAnalysisController@getCdrServeNeighDataHeader');
Route::get('/CDRServeNeighAnalysis/getCdrServeNeighData', 'NetworkOptimization\CDRNeighAnalysisController@getCdrServeNeighData');
Route::get('/CDRServeNeighAnalysis/getAllCdrServeNeighData', 'NetworkOptimization\CDRNeighAnalysisController@getAllCdrServeNeighData');

//xuyang 失败原因分析
Route::get('/failureAnalysis', function () {
    return view('badCellAnalysis.failureAnalysis');
})->middleware('auth');
Route::get('/failureAnalysis/getDataBase', 'BadCellAnalysis\FailureAnalysisController@getDataBase');
Route::post('/failureAnalysis/getChartData', 'BadCellAnalysis\FailureAnalysisController@getChartData');
Route::get('/failureAnalysis/getTableData', 'BadCellAnalysis\FailureAnalysisController@getTableData');
Route::post('/failureAnalysis/exportFile', 'BadCellAnalysis\FailureAnalysisController@exportFile');
Route::post('/failureAnalysis/getdetailDataHeader', 'BadCellAnalysis\FailureAnalysisController@getdetailDataHeader');
Route::get('/failureAnalysis/getdetailData', 'BadCellAnalysis\FailureAnalysisController@getdetailData');

//xuyang 无切换邻区分析
Route::get('/relationNonHandover', function () {
    return view('networkOptimization.relationNonHandover');
})->middleware('auth');
Route::get('/relationNonHandover/getCitys', 'NetworkOptimization\RelationNonHandoverController@getCitys');
Route::get('/relationNonHandover/getDataHeader', 'NetworkOptimization\RelationNonHandoverController@getDataHeader');
Route::get('/relationNonHandover/getTableData', 'NetworkOptimization\RelationNonHandoverController@getTableData');
Route::post('/relationNonHandover/getAllTableData', 'NetworkOptimization\RelationNonHandoverController@getAllTableData');

Route::get('/relationNonHandover/allDate', 'NetworkOptimization\RelationNonHandoverController@getAllDate');

//xuyang 模板的复制功能
Route::get('/LTETemplateManage/copyMode', 'QueryAnalysis\LTETemplateController@copyMode');
Route::get('/GSMTemplateManage/copyMode', 'QueryAnalysis\GSMTemplateController@copyMode');
Route::get('/NBITemplateManage/copyMode', 'QueryAnalysis\NBITemplateController@copyMode');

//xuyang 通知功能
Route::post('/nav/addNotice', 'NavController@addNotice');
Route::get('/nav/getNotice', 'NavController@getNotice');
Route::get('/nav/readNotice', 'NavController@readNotice');
Route::post('/nav/readAllNotice', 'NavController@readAllNotice');

//xuyang 通知管理
Route::get('/noticeManage', function () {
    return view('systemManage.noticeManage');
})->middleware('auth');
Route::get('/noticeManage/getNotice', 'SystemManage\NoticeController@getNotice');
Route::get('/noticeManage/deleteNotice', 'SystemManage\NoticeController@deleteNotice');


//zhangyongcai  bulkcm留痕
Route::get('/bulkcmMark', function () {
    return view('parameterAnalysis.bulkcmMark');
})->middleware('auth');
Route::group(['prefix' => 'bulkcmMark', 'namespace' => 'ParameterAnalysis'], function () {
    Route::get('getParamTasks', 'BulkcmMarkController@getParamTasks');
    Route::get('getAllCity', 'BulkcmMarkController@getAllCity');
    Route::get('getBulkcmMarkDataHeader', 'BulkcmMarkController@getBulkcmMarkDataHeader');
    Route::get('getBulkcmMarkData', 'BulkcmMarkController@getBulkcmMarkData');
    Route::get('getAllBulkcmMarkData', 'BulkcmMarkController@getAllBulkcmMarkData');
});
//zhangyongcai  kgetpart留痕
Route::get('/kgetpartMark', function () {
    return view('parameterAnalysis.kgetpartMark');
})->middleware('auth');
Route::group(['prefix' => 'kgetpartMark', 'namespace' => 'ParameterAnalysis'], function () {
    Route::get('getParamTasks', 'KgetpartMarkController@getParamTasks');
    Route::get('getAllCity', 'KgetpartMarkController@getAllCity');
    Route::get('getKgetpartMarkDataHeader', 'KgetpartMarkController@getKgetpartMarkDataHeader');
    Route::get('getKgetpartMarkData', 'KgetpartMarkController@getKgetpartMarkData');
    Route::get('getAllKgetpartMarkData', 'KgetpartMarkController@getAllKgetpartMarkData');

});

//xuyang badCell LTE邻区补
Route::post('/badCell/getLTENeighborHeader', 'BadCellAnalysis\BadCellController@getLTENeighborHeader');
Route::get('/badCell/getLTENeighborData', 'BadCellAnalysis\BadCellController@getLTENeighborData');
Route::post('/badCell/getGSMNeighborHeader', 'BadCellAnalysis\BadCellController@getGSMNeighborHeader');
Route::get('/badCell/getGSMNeighborData', 'BadCellAnalysis\BadCellController@getGSMNeighborData');

//xuyang 切换差邻区分析
Route::get('/relationBadHandover', function () {
    return view('networkOptimization.relationBadHandover');
})->middleware('auth');
Route::post('/relationBadHandover/getCitys', 'NetworkOptimization\RelationBadHandoverController@getCitys');
Route::post('/relationBadHandover/getDataHeader', 'NetworkOptimization\RelationBadHandoverController@getDataHeader');
Route::post('/relationBadHandover/getTableData', 'NetworkOptimization\RelationBadHandoverController@getTableData');
Route::post('/relationBadHandover/getAllTableData', 'NetworkOptimization\RelationBadHandoverController@getAllTableData');

Route::post('/relationBadHandover/allDate', 'NetworkOptimization\RelationBadHandoverController@getAllDate');

//xuyang LTE指标查询双击模板后弹出指标列表
Route::get('/LTEQuery/getElementTree', 'QueryAnalysis\LTEQueryController@getElementTree');
Route::get('/LTEQuery/getKpiNamebyId', 'QueryAnalysis\LTEQueryController@getKpiNamebyId');
//xuyang GSM指标查询双击模板后弹出指标列表
Route::get('/GSMQuery/getElementTree', 'QueryAnalysis\GSMQueryController@getElementTree');
Route::get('/GSMQuery/getKpiNamebyId', 'QueryAnalysis\GSMQueryController@getKpiNamebyId');
//xuyang NBI指标查询双击模板后弹出指标列表
Route::get('/NBIQuery/getElementTree', 'QueryAnalysis\NBIQueryController@getElementTree');
Route::get('/NBIQuery/getKpiNamebyId', 'QueryAnalysis\NBIQueryController@getKpiNamebyId');

//zhangyongcai 重叠覆盖率
Route::get('/overlapCover', function () {
    return view('network.overlapCover');
})->middleware('auth');
Route::get('overlapCoverCity', 'OverlapCoverController@getAllCity');
Route::get('overlapCoverDataHeader', 'OverlapCoverController@getOverlapCoverDataHeader');
Route::get('overlapCoverData', 'OverlapCoverController@getOverlapCoverData');
Route::get('allOverlapCoverData', 'OverlapCoverController@getAllOverlapCoverData');
Route::get('overlapCoverDate', 'OverlapCoverController@overlapCoverDate');
Route::get('overlapCoverGeniusDataHeader', 'OverlapCoverController@overlapCoverGeniusDataHeader');
Route::get('overlapCoverGeniusData', 'OverlapCoverController@overlapCoverGeniusData');
Route::get('allOverlapCoverGeniusData', 'OverlapCoverController@allOverlapCoverGeniusData');
Route::get('overlapCoverGeniusDate', 'OverlapCoverController@overlapCoverGeniusDate');

//xuyang 2G邻区合理性分析
Route::get('/GSMNeighRationality', function () {
    return view('networkOptimization.GSMNeighRationalityAnalysis');
})->middleware('auth');
Route::get('/fdfdi', 'NetworkOptimization\GSMNeighRationalityAnalysisController@getfdfdi');
Route::get('/GSMNeighRationality/GSMNeighRationalityDistribute', 'NetworkOptimization\GSMNeighRationalityAnalysisController@getGSMNeighRationalityData');

//zhangyongcai
Route::get('/LTENeighRationality', function () {
    return view('networkOptimization.LTENeighRationalityAnalysis');
})->middleware('auth');
Route::get('/fdfdg', 'NetworkOptimization\LTENeighRationalityAnalysisController@getfdfdg');
Route::get('/LTENeighRationality/LTENeighRationalityDistribute', 'NetworkOptimization\LTENeighRationalityAnalysisController@getLTENeighRationalityData');

//xuyang 实时监控-信令回溯
Route::get('/xinlinghuisu', function () {
    return view('complaintHandling.xinlinghuisu1');
})->middleware('auth');
Route::post('/xinlinghuisu/getEventData', 'ComplaintHandling\XinlinghuisuController@getEventData');
Route::post('/xinlinghuisu/showMessage', 'ComplaintHandling\XinlinghuisuController@showMessage');
Route::post('/xinlinghuisu/getAllEventData', 'ComplaintHandling\XinlinghuisuController@getAllEventData');
Route::post('/xinlinghuisu/exportCSV', 'ComplaintHandling\XinlinghuisuController@exportCSV');
Route::post('/xinlinghuisu/getDataGroupByDate', 'ComplaintHandling\XinlinghuisuController@getDataGroupByDate');
Route::get('/xinlinghuisu/getCityDate', 'ComplaintHandling\XinlinghuisuController@getCityDate');


//xuyang 参数管理-Baseline任务管理
Route::get('/paramsManage/getDate', 'SystemManage\ParamsController@getDate');
Route::get('/paramsManage/getBaselineTaskTable', 'SystemManage\ParamsController@getBaselineTaskTable');
Route::get('/paramsManage/addTask', 'SystemManage\ParamsController@addTask');
Route::get('/paramsManage/deleteTask', 'SystemManage\ParamsController@deleteTask');
Route::get('/paramsManage/runTask', 'SystemManage\ParamsController@runTask');
Route::get('/paramsManage/stopTask', 'SystemManage\ParamsController@stopTask');

//xuyang 信令分析
Route::get('/signalingAnalysis/getChartData', 'ComplaintHandling\SignalingAnalysisController@getChartData');
Route::get('/signalingAnalysis/showMessage', 'ComplaintHandling\SignalingAnalysisController@showMessage');
Route::get('/signalingAnalysis/getDataBase', 'ComplaintHandling\SignalingAnalysisController@getDataBase');

//lijian ctr信令分析
Route::get('/ctrSignalingAnalysis', function () {
    return view('complaintHandling.ctrSignalingAnalysis');
})->middleware('auth');
Route::get('/ctrSignalingAnalysis/getChartData', 'ComplaintHandling\CtrSignalingAnalysisController@getChartData');
Route::get('/ctrSignalingAnalysis/showMessage', 'ComplaintHandling\CtrSignalingAnalysisController@showMessage');
Route::get('/ctrSignalingAnalysis/getDataBase', 'ComplaintHandling\CtrSignalingAnalysisController@getDataBase');
Route::get('/ctrSignalingAnalysis/getChartData_filter', 'ComplaintHandling\CtrSignalingAnalysisController@getChartDataFilter');
Route::get('/ctrSignalingAnalysis/showMessage_filter', 'ComplaintHandling\CtrSignalingAnalysisController@showMessageFilter');

//zhangyongcai 重叠覆盖概览
Route::get('/overlapCoverOverview', function () {
    return view('network.overlapCoverOverview');
})->middleware('auth');
Route::get('/SearchOverlapCoverOverview', 'OverlapCoverOverviewController@searchOverlapCoverOverview');
Route::get('/busyTime', 'OverlapCoverOverviewController@getBusyTime');

//zhangyongcai 干扰概览
Route::get('/interCoverRatio', function () {
    return view('network.interCoverRatio');
})->middleware('auth');
Route::get('/SearchInterCoverRatio', 'InterCoverRatioController@searchInterCoverRatio');
Route::get('/interTime', 'InterCoverRatioController@getInterTime');

//xuyang 点击管理
Route::get('/accessManage', function () {
    return view('systemManage.accessManage');
})->middleware('auth');
Route::post('/accessManage/getAccessData', 'SystemManage\AccessController@getAccessData');
Route::post('/accessManage/downloadAccessData', 'SystemManage\AccessController@downloadAccessData');

//zhangyongcai  RRU&载波统计
Route::get('/RRU', function () {
    return view('parameterAnalysis.RRU');
})->middleware('auth');

Route::get('/RRU/getData', 'ParameterAnalysis\RRUController@getData');
Route::get('/RRU/getCityList', 'ParameterAnalysis\RRUController@getCityList');
Route::get('/RRU/getRRUDataHeader', 'ParameterAnalysis\RRUController@getRRUDataHeader');
Route::get('/RRU/getRRUData', 'ParameterAnalysis\RRUController@getRRUData');
Route::get('/RRU/getAllRRUData', 'ParameterAnalysis\RRUController@getAllRRUData');
Route::post('/RRU/runProcedure','ParameterAnalysis\RRUController@runProcedure');

//xuyang 高干扰小区
Route::get('/highInterferenceCell', function () {
    return view('badCellAnalysis.highInterferenceCell');
})->middleware('auth');
Route::get('/highInterferenceCell/getCitys', 'BadCellAnalysis\HighInterferenceCellController@getCitys');
Route::get('/highInterferenceCell/getCellData', 'BadCellAnalysis\HighInterferenceCellController@getCellData');
Route::post('/highInterferenceCell/getAllCellData', 'BadCellAnalysis\HighInterferenceCellController@getAllCellData');
Route::get('/highInterferenceCell/getAlarmData', 'BadCellAnalysis\HighInterferenceCellController@getAlarmData');
Route::post('/highInterferenceCell/getTimeList', 'BadCellAnalysis\HighInterferenceCellController@getTimeList');
Route::post('/highInterferenceCell/getTimeChartData', 'BadCellAnalysis\HighInterferenceCellController@getTimeChartData');
Route::post('/highInterferenceCell/getFrequencyChartData', 'BadCellAnalysis\HighInterferenceCellController@getFrequencyChartData');
Route::get('/highInterferenceCell/highStartTime', 'BadCellAnalysis\HighInterferenceCellController@highTime');

//xuyang 终端查询
Route::get('/terminalQuery', function () {
    return view('userAnalysis.terminalQuery');
})->middleware('auth');
Route::get('/terminalQuery/getCitys', 'UserAnalysis\TerminalQueryController@getCitys');
Route::get('/terminalQuery/getUserInfoHead', 'UserAnalysis\TerminalQueryController@getUserInfoHead');
Route::get('/terminalQuery/getUserInfoData', 'UserAnalysis\TerminalQueryController@getUserInfoData');

//xuyang 市场分析
Route::get('/marketAnalysis', function () {
    return view('userAnalysis.marketAnalysis');
})->middleware('auth');
Route::get('/marketAnalysis/getCitys', 'UserAnalysis\MarketAnalysisController@getCitys');
Route::get('/marketAnalysis/getBrandData', 'UserAnalysis\MarketAnalysisController@getBrandData');
Route::get('/marketAnalysis/getModeData', 'UserAnalysis\MarketAnalysisController@getModeData');
Route::get('/marketAnalysis/getAllBrandData', 'UserAnalysis\MarketAnalysisController@getAllBrandData');
Route::get('/marketAnalysis/getAllModeData', 'UserAnalysis\MarketAnalysisController@getAllModeData');
Route::get('/marketAnalysis/getBrandChartData', 'UserAnalysis\MarketAnalysisController@getBrandChartData');
Route::get('/marketAnalysis/getModeChartData', 'UserAnalysis\MarketAnalysisController@getModeChartData');



//xuyang 轨迹查询
Route::get('/trailQuery', function () {
    return view('userAnalysis.trailQuery');
})->middleware('auth');
Route::get('/trailQuery/getCitys', 'UserAnalysis\TrailQueryController@getCitys');
Route::post('/trailQuery/getTrailData', 'UserAnalysis\TrailQueryController@getTrailData');
Route::post('/trailQuery/getDataGroupByDate', 'UserAnalysis\TrailQueryController@getDataGroupByDate');



//xuyang 重叠覆盖施主分析
Route::get('/overlappingDonorAnalysis', function () {
    return view('network.overlappingDonorAnalysis');
})->middleware('auth');
Route::get('/overlappingDonorAnalysis/getCitys', 'OverlappingDonorAnalysisController@getCitys');
Route::post('/overlappingDonorAnalysis/getData', 'OverlappingDonorAnalysisController@getData');
Route::post('/overlappingDonorAnalysis/getDetailData', 'OverlappingDonorAnalysisController@getDetailData');



//zhangyongcai 越区覆盖小区
Route::get('/areaCoverage', function () {
    return view('network.areaCoverage');
})->middleware('auth');
Route::get('areaCoverageCity', 'AreaCoverageController@getAllCity');
Route::get('areaCoverageDataHeader', 'AreaCoverageController@getAreaCoverageDataHeader');
Route::get('areaCoverageData', 'AreaCoverageController@getAreaCoverageData');
Route::get('allAreaCoverageData', 'AreaCoverageController@getAllAreaCoverageData');
Route::get('areaCoverageDate', 'AreaCoverageController@areaCoverageDate');



//xuyang 重叠覆盖受主分析
Route::get('/overlappingAcceptorAnalysis', function () {
    return view('network.overlappingAcceptorAnalysis');
})->middleware('auth');
Route::get('/overlappingAcceptorAnalysis/getCitys', 'OverlappingDonorAnalysisController@getCitys');
Route::post('/overlappingAcceptorAnalysis/getData', 'OverlappingAcceptorAnalysisController@getData');
Route::post('/overlappingAcceptorAnalysis/getDetailData', 'OverlappingAcceptorAnalysisController@getDetailData');



//xuyang 指标地理概览
Route::get('/indexGeographicOverview',function(){
    return view('network.indexGeographicOverview');
})->middleware('auth');
//xuyang RRC用户数云图-下行业务量云图
Route::get('/RRCUserCloud', function () {
    return view('network.RRCUserCloud');
})->middleware('auth');
Route::get('/downlinkTrafficCloud', function () {
    return view('network.downlinkTrafficCloud');

})->middleware('auth');
Route::post('/indexGeographicOverview/getData', 'IndexGeographicOverviewController@getData');
Route::post('/indexGeographicOverview/getCell', 'IndexGeographicOverviewController@getCell');


//zhangyongcai 小区PRB分析
Route::get('/cellPRBAnalysis', function () {
    return view('network.cellPRBAnalysis');
})->middleware('auth');
Route::post('/cellPRBAnalysis/getPRBAnalysisData', 'CellPRBAnalysisController@getPRBAnalysisData');
Route::get('/cellPRBAnalysis/getPRBTime', 'CellPRBAnalysisController@getPRBTime');

//xuyang 数据源管理 在线入库
Route::post('/dataSourceManage/onlineStorage', 'SystemManage\DataSourceController@onlineStorage');


//zhangyongcai 跨MO查询
Route::get('/StrideMOQuery', function () {
    return view('parameterAnalysis.StrideMOQuery');
})->middleware('auth');
Route::get('/StrideMOQuery/getData', 'ParameterAnalysis\StrideMOQueryController@getData');
Route::get('StrideMOQueryDataHeader', 'ParameterAnalysis\StrideMOQueryController@getStrideMOQueryDataHeader');
Route::get('StrideMOQueryData', 'ParameterAnalysis\StrideMOQueryController@getStrideMOQueryData');
Route::get('/StrideMOQuery/downloadFile', 'ParameterAnalysis\StrideMOQueryController@downloadFile');
Route::get('/StrideMOQuery/downloadFile', 'ParameterAnalysis\StrideMOQueryController@downloadFile');
Route::get('paramDataHeader', 'ParameterAnalysis\StrideMOQueryController@paramDataHeader');
Route::get('paramData', 'ParameterAnalysis\StrideMOQueryController@paramData');
Route::get('/StrideMOQuery/insertFile', 'ParameterAnalysis\StrideMOQueryController@insertFile');
Route::post('/StrideMOQuery/getFileContent', 'ParameterAnalysis\StrideMOQueryController@getFileContent');


//xuyang 下载管理
Route::get('/downloadManage', function () {
    return view('systemManage.downloadManage');
})->middleware('auth');
Route::get('/downloadManage/treeQuery', 'SystemManage\DownloadManageController@treeQuery');
Route::get('/downloadManage/getTableData', 'SystemManage\DownloadManageController@getTableData');
Route::post('/downloadManage/updateDownload', 'SystemManage\DownloadManageController@updateDownload');
Route::get('/downloadManage/deleteDownload', 'SystemManage\DownloadManageController@deleteDownload');

//xuyang 定位测距
Route::get('/locationAndRanging', function () {
    return view('systemManage.locationAndRanging');
})->middleware('auth');
Route::post('/locationAndRanging/getCoordinateByCell', 'SystemManage\LocationAndRangingController@getCoordinateByCell');

//xuyang 下载
Route::get('/downloadCourse', function () {
    return view('systemManage.downloadCourse');
})->middleware('auth');
Route::get('/downloadCourse/getVideo', 'SystemManage\DownloadCourseController@getVideo');
Route::get('/downloadCourse/getDoc', 'SystemManage\DownloadCourseController@getDoc');

//xuyang 能力分析
Route::get('/abilityAnalysis', function () {
    return view('userAnalysis.abilityAnalysis');
})->middleware('auth');

Route::get('/abilityAnalysis/bandEutraData', 'UserAnalysis\AbilityAnalysisController@getBandEutraData');
Route::get('/abilityAnalysis/FGIData', 'UserAnalysis\AbilityAnalysisController@getFGIData');
Route::get('/abilityAnalysis/getCitys', 'UserAnalysis\AbilityAnalysisController@getCitys');
Route::get('/abilityAnalysis/getTableData', 'UserAnalysis\AbilityAnalysisController@getTableData');
Route::get('/abilityAnalysis/getChartData', 'UserAnalysis\AbilityAnalysisController@getChartData');
Route::get('/abilityAnalysis/bandEutraChart', 'UserAnalysis\AbilityAnalysisController@getBandEutraChartData');
Route::get('/abilityAnalysis/FGIChart', 'UserAnalysis\AbilityAnalysisController@getFGIChartData');

//zhujiaojiao 信令分析-信令诊断
Route::get('/signalingDiagnose', function () {
    return view('complaintHandling.signalingDiagnose');
})->middleware('auth');
Route::group(['prefix' => 'signalingDiagnose', 'namespace' => 'ComplaintHandling'], function () {
    Route::get('coreNetworkDiagnose', 'SignalingDiagnoseController@getCoreNetworkDiagnoseData');
    Route::get('coreNetworkDates', 'SignalingDiagnoseController@getCoreNetworkDates');
    Route::get('coreNetworkDiagnoseDetailHeader', 'SignalingDiagnoseController@getCoreNetworkDiagnoseDetailHeader');
    Route::get('coreNetworkDiagnoseDetail', 'SignalingDiagnoseController@getCoreNetworkDiagnoseDetail');
    Route::get('timingDiagramChartData', 'SignalingDiagnoseController@getTimingDiagramChartData');
    Route::get('wlanNetworkDiagnose', 'SignalingDiagnoseController@getWlanNetworkDiagnoseData');
    Route::get('wlanNetworkDiagnoseDetailHeader', 'SignalingDiagnoseController@getWlanNetworkDiagnoseDetailHeader');
    Route::get('wlanNetworkDiagnoseDetail', 'SignalingDiagnoseController@getWlanNetworkDiagnoseDetail');
    Route::get('getCitys', 'SignalingDiagnoseController@getCitys');
});

//zhangyongcai 原因值分析
Route::get('/CauseValueAnalysis', function () {
    return view('badCellAnalysis.CauseValueAnalysis');
})->middleware('auth');
Route::get('/CauseValueAnalysis/getCitys', 'BadCellAnalysis\CauseValueAnalysisController@getCitys');
Route::post('/CauseValueAnalysis/getCauseValueAnalysisData', 'BadCellAnalysis\CauseValueAnalysisController@getCauseValueAnalysisData');
Route::get('/CauseValueAnalysis/getTableData', 'BadCellAnalysis\CauseValueAnalysisController@getTableData');
Route::post('/CauseValueAnalysis/getChartData', 'BadCellAnalysis\CauseValueAnalysisController@getChartData');
Route::post('/CauseValueAnalysis/getDrillDownChartData', 'BadCellAnalysis\CauseValueAnalysisController@getDrillDownChartData');
Route::get('/CauseValueAnalysis/getdetailDataHeader', 'BadCellAnalysis\CauseValueAnalysisController@getdetailDataHeader');
Route::get('/CauseValueAnalysis/getdetailData', 'BadCellAnalysis\CauseValueAnalysisController@getdetailData');
Route::post('/CauseValueAnalysis/exportFile', 'BadCellAnalysis\CauseValueAnalysisController@exportFile');


//xuyang 直连管理alarm部分
Route::get('/ENIQManage/QueryAlarm', 'SystemManage\ENIQController@queryAlarm');
Route::post('/ENIQManage/updateAlarm', 'SystemManage\ENIQController@updateAlarm');
Route::post('/ENIQManage/deleteAlarm', 'SystemManage\ENIQController@deleteAlarm');


//lijian
Route::get('/genius/getNowTime', 'NetworkChartsTimerController@getNowTime');


Route::get('/singal', function () {
    return view('network.singal');
})->middleware('auth');
Route::get('singalTrend', 'SingalChartsController@getSingalTrend');


//xuyang

Route::post('/storageManage/uploadFile', 'SystemManage\StorageController@uploadFile');
//zhujiaojiao
Route::get('/L3Analysis', function () {
    return view('complaintHandling.L3Analysis');
})->middleware('auth');
Route::group(['prefix' => 'L3Analysis', 'namespace' => 'ComplaintHandling'], function () {
    Route::get('getCitys', 'L3AnalysisController@getCitys');
    Route::post('getL3AnalysisData', 'L3AnalysisController@getL3AnalysisData');
    Route::post('getChartData', 'L3AnalysisController@getChartData');
    Route::get('getTableData', 'L3AnalysisController@getTableData');
    Route::get('getdetailDataHeader', 'L3AnalysisController@getdetailDataHeader');
    Route::get('getdetailData', 'L3AnalysisController@getdetailData');
    Route::post('exportFile', 'L3AnalysisController@exportFile');
    Route::post('getSolidgaugeData', 'L3AnalysisController@getSolidgaugeData');
});

//xuyang 低接入小区PCI类指标
Route::get('/badCell/conflictNum', 'BadCellAnalysis\BadCellController@getConflictNum');
Route::get('/highInterferenceCell/conflictNum', 'BadCellAnalysis\HighInterferenceCellController@getConflictNum');

//xuyang 当前告警导入文件
Route::post('/currentAlarmQuery/uploadFile', 'AlarmAnalysis\CurrentAlarmQueryController@uploadFile');
//xuyang 历史告警导入文件
Route::post('/historyAlarmQuery/uploadFile', 'AlarmAnalysis\HistoryAlarmQueryController@uploadFile');


//xuyang 数据源管理logType
Route::get('/dataSourceManage/getLogType', 'SystemManage\DataSourceController@getLogType');

//lijian
Route::get('csvZipDownload', 'CsvZipDownloadController@getZipFile');


//xuyang 重叠覆盖受主分析-可选日期
Route::post('/overlappingAcceptorAnalysis/getDataGroupByDate', 'OverlappingAcceptorAnalysisController@getDataGroupByDate');
//xuyang 重叠覆盖施主分析-可选日期
Route::post('/overlappingDonorAnalysis/getDataGroupByDate', 'OverlappingDonorAnalysisController@getDataGroupByDate');

//xuyang SQL语句查询
Route::get('/SQLQuery', function () {
    return view('parameterAnalysis.SQLQuery');
})->middleware('auth');
Route::get('/SQLQuery/getCustomTreeData', 'ParameterAnalysis\SQLQueryController@getCustomTreeData');
Route::get('/SQLQuery/searchCustomTreeData', 'ParameterAnalysis\SQLQueryController@getSearchCustomTreeData');
Route::get('/SQLQuery/getAllCity', 'ParameterAnalysis\SQLQueryController@getAllCity');
Route::get('/SQLQuery/getKpiFormula', 'ParameterAnalysis\SQLQueryController@getKpiFormula');
Route::post('/SQLQuery/getTableHeader', 'ParameterAnalysis\SQLQueryController@getTableHeader');
Route::post('/SQLQuery/getTableData', 'ParameterAnalysis\SQLQueryController@getTableData');
Route::post('/SQLQuery/getAllTableData', 'ParameterAnalysis\SQLQueryController@getAllTableData');
Route::get('/SQLQuery/deleteMode', 'ParameterAnalysis\SQLQueryController@deleteMode');
Route::get('/SQLQuery/insertMode', 'ParameterAnalysis\SQLQueryController@insertMode');
Route::get('/SQLQuery/saveMode', 'ParameterAnalysis\SQLQueryController@saveMode');
Route::post('/SQLQuery/saveModeChange', 'ParameterAnalysis\SQLQueryController@saveModeChange');


//lijian0223
//lijian0316
Route::post('/badCell/getLTENeighborHeader_model', 'BadCellAnalysis\BadCellController@getLTENeighborHeaderModel');
Route::get('/badCell/getLTENeighborData_model', 'BadCellAnalysis\BadCellController@getLTENeighborDataModel');
Route::get('badCell/getPrbNum', 'BadCellAnalysis\BadCellController@getPrbNum');
Route::post('/badCell/getLTENeighborHeader_1', 'BadCellAnalysis\BadCellController@getLTENeighborHeader1');
Route::get('/badCell/getLTENeighborData_1', 'BadCellAnalysis\BadCellController@getLTENeighborData1');
//lijian0322
Route::get('/badCell/getWeakCoverCell_model', 'BadCellAnalysis\BadCellController@getWeakCoverCellModel');
Route::get('/badCell/getzhichaCell_model', 'BadCellAnalysis\BadCellController@getzhichaCellModel');
Route::get('/badCell/getOverlapCover_model', 'BadCellAnalysis\BadCellController@getOverlapCoverModel');
//0331
Route::get('/badCell/getInterfereCell_model', 'BadCellAnalysis\BadCellController@getInterfereCellModel');
//20170411
Route::get('/badCell/getZhichaCell_chart', 'BadCellAnalysis\BadCellController@getZhichaCellChart');


//xuyang SQL语句查询
Route::get('/ENBAnalysis', function () {
    return view('complaintHandling.ENBAnalysis');
})->middleware('auth');
Route::get('ENBAnalysis/getCitys', 'ComplaintHandling\ENBAnalysisController@getCitys');
Route::post('ENBAnalysis/getENBAnalysisDate', 'ComplaintHandling\ENBAnalysisController@getENBAnalysisDate');
Route::post('ENBAnalysis/getChartData', 'ComplaintHandling\ENBAnalysisController@getChartData');
Route::post('ENBAnalysis/getdetailDataHeader', 'ComplaintHandling\ENBAnalysisController@getdetailDataHeader');
Route::get('ENBAnalysis/getdetailData', 'ComplaintHandling\ENBAnalysisController@getdetailData');
Route::post('ENBAnalysis/exportFile', 'ComplaintHandling\ENBAnalysisController@exportFile');
Route::post('ENBAnalysis/getSuccessChartData', 'ComplaintHandling\ENBAnalysisController@getSuccessChartData');

//xuyang 下载管理-城市
Route::get('/downloadManage/getCitys', 'SystemManage\DownloadManageController@getCitys');

//lijian 差小区-极地图
Route::get('/badCell/getPolarMapData', 'BadCellAnalysis\BadCellController@getPolarMapData');

//xuyang 丢包查询
Route::get('/packetLossAnalysis', function () {
    return view('badCellAnalysis.packetLossAnalysis');
})->middleware('auth');
Route::get('/packetLossAnalysis/getTableData', 'BadCellAnalysis\PacketLossAnalysisController@getTableData');
Route::post('/packetLossAnalysis/exportFile', 'BadCellAnalysis\PacketLossAnalysisController@exportFile');
Route::get('/packetLossAnalysis/getCitys', 'BadCellAnalysis\PacketLossAnalysisController@getCitys');
Route::post('/packetLossAnalysis/getCityDate', 'BadCellAnalysis\PacketLossAnalysisController@getCityDate');


//lijian 地图适配
Route::get('/nav/getOption', 'NavController@getOption');
//xuyang 实时监控-信令回溯
Route::get('/NASSignalingBacktrack', function () {
    return view('complaintHandling.NASSignalingBacktrack');
})->middleware('auth');

Route::post('/NASSignalingBacktrack/getEventData', 'ComplaintHandling\NASSignalingBacktrackController@getEventData');
Route::post('/NASSignalingBacktrack/showMessage', 'ComplaintHandling\NASSignalingBacktrackController@showMessage');
Route::post('/NASSignalingBacktrack/getAllEventData', 'ComplaintHandling\NASSignalingBacktrackController@getAllEventData');
Route::post('/NASSignalingBacktrack/exportCSV', 'ComplaintHandling\NASSignalingBacktrackController@exportCSV');
Route::post('/NASSignalingBacktrack/getDataGroupByDate', 'ComplaintHandling\NASSignalingBacktrackController@getDataGroupByDate');
Route::get('/NASSignalingBacktrack/getCityDate', 'ComplaintHandling\NASSignalingBacktrackController@getCityDate');

//xuyang 使用反馈
Route::get('/feedBack', function () {
    return view('systemManage.feedBack');
})->middleware('auth');

//zhangyongcai 存储管理
Route::get('/storeManage',function(){
    return view('systemManage.storeManage');
})->middleware('auth');
Route::post('/storeManage/treeQuery', 'SystemManage\StoreManageController@treeQuery');
Route::get('/storeManage/getCitys', 'SystemManage\StoreManageController@getCitys');
Route::post('/storeManage/getTableData', 'SystemManage\StoreManageController@getTableData');
Route::post('/storeManage/updateDownload', 'SystemManage\StoreManageController@updateDownload');
Route::get('/storeManage/deleteDownload', 'SystemManage\StoreManageController@deleteDownload');
Route::get('/storeManage/getTypes', 'SystemManage\StoreManageController@getTypes');

//zjj特色-参数对比
Route::get('/paramCompare',function(){
    return view('SpecialFunction.paramCompare');
})->middleware('auth');
Route::group(['prefix'=>'paramCompare','namespace'=>'SpecialFunction'],function(){
    Route::get('getAllCity','ParamCompareController@getAllCity');
    Route::post('getItems','ParamCompareController@getItems');
    Route::post('getItemsAdd','ParamCompareController@getItemsAdd');
    Route::post('getItemsLess','ParamCompareController@getItemsLess');
    Route::post('exportFile','ParamCompareController@exportFile');
    Route::get('getParamTasks', 'ParamCompareController@getParamTasks');
    Route::post('getParamData', 'ParamCompareController@getParamData');
    Route::post('getCompareResult', 'ParamCompareController@getCompareResult');
});

//xuyang 用户设置
Route::get('/UserSetting', function () {
    return view('systemManage.UserSetting');
})->middleware('auth');
Route::post('/UserSetting/updateUser', 'SystemManage\UserSettingController@updateUser');
Route::post('/UserSetting/updatePassword', 'SystemManage\UserSettingController@updatePassword');

//xuyang 覆盖率查询
Route::get('/CoverageQuery', function () {
    return view('network.CoverageQuery');
})->middleware('auth');
Route::get('/coverageQuery/getTableData', 'CoverageQueryController@getTableData');
Route::post('/coverageQuery/exportFile', 'CoverageQueryController@exportFile');
Route::get('/coverageQuery/getCitys', 'CoverageQueryController@getCitys');
Route::post('/coverageQuery/getCityDate', 'CoverageQueryController@getCityDate');

//zjj 文件系统
Route::group(['prefix'=>'file','namespace'=>'FileSystem'],function(){
    Route::post('uploadFile','FileController@uploadFile');
});

//历史小区查询
Route::get('/historyCellSearch', function () {
    return view('badCellAnalysis.historyCellSearch');
})->middleware('auth');
Route::get('historyCellSearch/selectCityOption', 'BadCellAnalysis\HistoryCellSearchController@getCityOption');
Route::get('historyCellSearch/historyCellDate', 'BadCellAnalysis\HistoryCellSearchController@getHistoryCellDate');
Route::post('historyCellSearch/historyCell', 'BadCellAnalysis\HistoryCellSearchController@getHistoryCell');
Route::get('historyCellSearch/getIndexCell', 'BadCellAnalysis\HistoryCellSearchController@getIndexCell');
Route::get('historyCellSearch/getChartDataHistory', 'BadCellAnalysis\HistoryCellSearchController@getChartDataHistory');
//04-01
// Route::get('badCell/getNumOfDiagnosisData', 'BadCellAnalysis\BadCellController@getNumOfDiagnosisData');
Route::get('badCell/getNumOfDiagnosisData_mr', 'BadCellAnalysis\BadCellController@getNumOfDiagnosisDataMR');
Route::get('badCell/getNumOfDiagnosisDataFilter_alarm', 'BadCellAnalysis\BadCellController@getNumOfDiagnosisDataFilter_alarm');
Route::get('badCell/getNumOfDiagnosisDataFilter_weakCover', 'BadCellAnalysis\BadCellController@getNumOfDiagnosisDataFilter_weakCover');
Route::get('badCell/getNumOfDiagnosisDataFilter_zhicha', 'BadCellAnalysis\BadCellController@getNumOfDiagnosisDataFilter_zhicha');
Route::get('badCell/getNumOfDiagnosisDataFilter_overlapCover', 'BadCellAnalysis\BadCellController@getNumOfDiagnosisDataFilter_overlapCover');
Route::get('badCell/getNumOfDiagnosisDataFilter_AvgPRB', 'BadCellAnalysis\BadCellController@getNumOfDiagnosisDataFilter_AvgPRB');
Route::get('badCell/getNumOfDiagnosisDataFilter_parameter', 'BadCellAnalysis\BadCellController@getNumOfDiagnosisDataFilter_parameter');
Route::get('badCell/getNumOfDiagnosisDataFilter_highTraffic', 'BadCellAnalysis\BadCellController@getNumOfDiagnosisDataFilter_highTraffic');

//xuyang 本地数据管理
Route::get('/LocalDataManage', function () {
    return view('systemManage.LocalDataManage');
})->middleware('auth');
Route::post('/LocalDataManage/uploadFile', 'SystemManage\LocalDataManageController@uploadFile');
Route::post('/LocalDataManage/addTask', 'SystemManage\LocalDataManageController@addTask');

//zhangyongcai 在线数据管理
Route::post('/dataSourceManage/storage', 'SystemManage\DataSourceController@storage');
Route::post('/dataSourceManage/uploadFile', 'SystemManage\DataSourceController@uploadFile');

//RRC原因值分布
Route::get('/badCell/rrcResult', 'BadCellAnalysis\BadCellController@getRrcResult');
Route::get('/badCell/getRrcResultTableData', 'BadCellAnalysis\BadCellController@getRrcResultTableData');
Route::post('/badCell/getRrcResultDetailTableField', 'BadCellAnalysis\BadCellController@getRrcResultDetailTableField');
Route::post('/badCell/getRrcResultDetailData', 'BadCellAnalysis\BadCellController@getRrcResultDetailData');
Route::post('/badCell/exportRrcResultDetail', 'BadCellAnalysis\BadCellController@exportRrcResultDetail');

//0524
Route::get('/badCell/rrcResult_erab', 'BadCellAnalysis\BadCellController@getRrcResult_erab');
Route::get('/badCell/getRrcResultTableData_rrcc', 'BadCellAnalysis\BadCellController@getRrcResultTableData_rrcc');


//zjj 翻频
Route::get('/modifyFrequency4g',function(){
    return view('SpecialFunction.modifyFrequency4g');
})->middleware('auth');
Route::group(['prefix'=>'modifyFrequency','namespace'=>'SpecialFunction'],function(){
    Route::get('getTasks','ModifyFrequency4gController@getTasks');
    Route::post('getFileContent','ModifyFrequency4gController@getFileContent');
    Route::post('downloadTemplateFile','ModifyFrequency4gController@downloadTemplateFile');
    Route::get('TreeQuery', 'ModifyFrequency4gController@getCityTree');
    Route::post('runProcedure','ModifyFrequency4gController@runProcedure');
    Route::post('getTableField','ModifyFrequency4gController@getTableField');
    Route::post('getItems','ModifyFrequency4gController@getItems');
    Route::post('downloadFile','ModifyFrequency4gController@downloadFile');
});

//xuyang 本地数据管理
Route::get('/RealTimeInterference', function () {
    return view('network.RealTimeInterference');
})->middleware('auth');
Route::post('/RealTimeInterference/getDateTime', 'RealTimeInterferenceController@getDateTime');
Route::post('/RealTimeInterference/getRealTimeData', 'RealTimeInterferenceController@getRealTimeData');
Route::post('/RealTimeInterference/getAllCity', 'RealTimeInterferenceController@getAllCity');

//菜单权限
Route::get('/nav/getMenuList', 'NavController@getMenuList');
Route::get('/userManage/getMenuList', 'SystemManage\UserController@getMenuList');
Route::post('/userManage/updatePermission', 'SystemManage\UserController@updatePermission');

//通知-用户组
Route::get('/nav/getUserGroup', 'NavController@getUserGroup');
Route::post('/noticeManage/getUserGroupById', 'SystemManage\NoticeController@getUserGroupById');
//查看全部通知
Route::get('/readAllNotice', function () {
    return view('systemManage.readAllNotice');
})->middleware('auth');
Route::get('/noticeManage/getAllNotice', 'SystemManage\NoticeController@getAllNotice');

//注册账号
Route::get('/register', function () {
    return view('auth.userRegister');
});
Route::post('/userRegister/userRegister', 'SystemManage\UserRegisterController@userRegister');

//操作查询
Route::get('/operationQuery',function(){
    return view('SpecialFunction.operationQuery');
})->middleware('auth');
Route::get('/operationQuery/getCitys', 'SpecialFunction\OperationQueryController@getCitys');
Route::get('/operationQuery/paramData', 'SpecialFunction\OperationQueryController@getparamData');
Route::get('/operationQuery/operationData', 'SpecialFunction\OperationQueryController@getOperationData');
Route::get('/operationQuery/getActionType', 'SpecialFunction\OperationQueryController@getActionType');
Route::get('/operationQuery/getActionSource', 'SpecialFunction\OperationQueryController@getActionSource');
Route::post('/operationQuery/uploadFile', 'SpecialFunction\OperationQueryController@uploadFile');

//日活用户-xuyang
Route::get('/activeUser',function(){
    return view('systemManage.activeUser');
})->middleware('auth');
Route::post('/activeUser/getAccessData', 'SystemManage\ActiveUserController@getAccessData');

//相关性分析：无线接通率&干扰/无线接通率&质差/RRC建立成功率/无线接通率&ERAB建立成功率
Route::get('/badCell/getWirelessCallRate_zhicha', 'BadCellAnalysis\BadCellController@getWirelessCallRate_zhicha');
Route::get('/badCell/getWirelessCallRate_interfere', 'BadCellAnalysis\BadCellController@getWirelessCallRate_interfere');
Route::get('/badCell/getWirelessCallRate_RRCEstSucc', 'BadCellAnalysis\BadCellController@getWirelessCallRate_RRCEstSucc');
Route::get('/badCell/getWirelessCallRate_ERABEstSucc', 'BadCellAnalysis\BadCellController@getWirelessCallRate_ERABEstSucc');
//任务管理-zhujj
Route::get('/taskManage',function(){
    return view('systemManage.taskManage');
})->middleware('auth');
Route::group(['prefix'=>'taskManage','namespace'=>'SystemManage'],function(){
    Route::post('openTaskFile', 'TaskController@openTaskFile');
    Route::post('saveTaskFile', 'TaskController@saveTaskFile');
});

//高掉线小区xuyang
Route::get('/highLostCell/templateQuery', 'BadCellAnalysis\HighLostCellController@templateQuery');
Route::get('/highLostCell/getPolarMapData', 'BadCellAnalysis\HighLostCellController@getPolarMapData');
Route::get('/highLostCell/getNumOfDiagnosisDataFilter_alarm', 'BadCellAnalysis\HighLostCellController@getNumOfDiagnosisDataFilter_alarm');
Route::get('/highLostCell/getNumOfDiagnosisDataFilter_weakCover', 'BadCellAnalysis\HighLostCellController@getNumOfDiagnosisDataFilter_weakCover');
Route::get('/highLostCell/getNumOfDiagnosisDataFilter_zhicha', 'BadCellAnalysis\HighLostCellController@getNumOfDiagnosisDataFilter_zhicha');
Route::get('/highLostCell/getNumOfDiagnosisDataFilter_overlapCover', 'BadCellAnalysis\HighLostCellController@getNumOfDiagnosisDataFilter_overlapCover');
Route::get('/highLostCell/getNumOfDiagnosisDataFilter_AvgPRB', 'BadCellAnalysis\HighLostCellController@getNumOfDiagnosisDataFilter_AvgPRB');
Route::get('/highLostCell/getNumOfDiagnosisDataFilter_highTraffic', 'BadCellAnalysis\HighLostCellController@getNumOfDiagnosisDataFilter_highTraffic');
Route::get('/highLostCell/getNumOfDiagnosisDataFilter_parameter', 'BadCellAnalysis\HighLostCellController@getNumOfDiagnosisDataFilter_parameter');
Route::get('/highLostCell/getNumOfDiagnosisData_mr', 'BadCellAnalysis\HighLostCellController@getNumOfDiagnosisDataMR');
Route::get('/highLostCell/getCellAlarmClassifyTable', 'BadCellAnalysis\HighLostCellController@getCellAlarmClassifyTable');
Route::get('/highLostCell/getErbsAlarmClassifyTable', 'BadCellAnalysis\HighLostCellController@getErbsAlarmClassifyTable');
Route::get('/highLostCell/getLTENeighborData_model', 'BadCellAnalysis\HighLostCellController@getLTENeighborDataModel');
Route::get('/highLostCell/getZhichaCell_chart', 'BadCellAnalysis\HighLostCellController@getZhichaCellChart');
Route::get('/highLostCell/getWeakCoverCell_model', 'BadCellAnalysis\HighLostCellController@getWeakCoverCellModel');
Route::get('/highLostCell/getzhichaCell_model', 'BadCellAnalysis\HighLostCellController@getzhichaCellModel');
Route::get('/highLostCell/getOverlapCover_model', 'BadCellAnalysis\HighLostCellController@getOverlapCoverModel');
Route::get('/highLostCell/getInterfereCell_model', 'BadCellAnalysis\HighLostCellController@getInterfereCellModel');
Route::get('/highLostCell/getBaselineCheckData', 'BadCellAnalysis\HighLostCellController@getBaselineCheckData');
Route::get('/highLostCell/getRrcResultTableData', 'BadCellAnalysis\HighLostCellController@getRrcResultTableData');
Route::get('/highLostCell/rrcResult', 'BadCellAnalysis\HighLostCellController@getRrcResult');
Route::post('/highLostCell/getRrcResultDetailTableField', 'BadCellAnalysis\HighLostCellController@getRrcResultDetailTableField');
Route::post('/highLostCell/getRrcResultDetailData', 'BadCellAnalysis\HighLostCellController@getRrcResultDetailData');
Route::post('/highLostCell/exportRrcResultDetail', 'BadCellAnalysis\HighLostCellController@exportRrcResultDetail');
Route::get('/highLostCell/getCounterLoseResultDistribution', 'BadCellAnalysis\HighLostCellController@getCounterLoseResultDistribution');
Route::get('/highLostCell/getIndexChartData', 'BadCellAnalysis\HighLostCellController@getIndexChartData');
Route::get('/highLostCell/getIndexTableData', 'BadCellAnalysis\HighLostCellController@getIndexTableData');

Route::get('/badCell/getRelatedTrends', 'BadCellAnalysis\BadCellController@getRelatedTrends');

//切换差小区
Route::get('/badHandoverCell/templateQuery', 'BadCellAnalysis\BadHandoverCellController@templateQuery');
Route::get('/badHandoverCell/getPolarMapData', 'BadCellAnalysis\BadHandoverCellController@getPolarMapData');
Route::get('/badHandoverCell/getNumOfDiagnosisDataFilter_alarm', 'BadCellAnalysis\BadHandoverCellController@getNumOfDiagnosisDataFilter_alarm');
Route::get('/badHandoverCell/getNumOfDiagnosisDataFilter_weakCover', 'BadCellAnalysis\BadHandoverCellController@getNumOfDiagnosisDataFilter_weakCover');
Route::get('/badHandoverCell/getNumOfDiagnosisDataFilter_zhicha', 'BadCellAnalysis\BadHandoverCellController@getNumOfDiagnosisDataFilter_zhicha');
Route::get('/badHandoverCell/getNumOfDiagnosisDataFilter_overlapCover', 'BadCellAnalysis\BadHandoverCellController@getNumOfDiagnosisDataFilter_overlapCover');
Route::get('/badHandoverCell/getNumOfDiagnosisDataFilter_AvgPRB', 'BadCellAnalysis\BadHandoverCellController@getNumOfDiagnosisDataFilter_AvgPRB');
Route::get('/badHandoverCell/getNumOfDiagnosisDataFilter_highTraffic', 'BadCellAnalysis\BadHandoverCellController@getNumOfDiagnosisDataFilter_highTraffic');
Route::get('/badHandoverCell/getNumOfDiagnosisDataFilter_parameter', 'BadCellAnalysis\BadHandoverCellController@getNumOfDiagnosisDataFilter_parameter');
Route::get('/badHandoverCell/getNumOfDiagnosisData_mr', 'BadCellAnalysis\BadHandoverCellController@getNumOfDiagnosisDataMR');
Route::get('/badHandoverCell/getCellAlarmClassifyTable', 'BadCellAnalysis\BadHandoverCellController@getCellAlarmClassifyTable');
Route::get('/badHandoverCell/getErbsAlarmClassifyTable', 'BadCellAnalysis\BadHandoverCellController@getErbsAlarmClassifyTable');
Route::get('/badHandoverCell/getLTENeighborData_model', 'BadCellAnalysis\BadHandoverCellController@getLTENeighborDataModel');
Route::get('/badHandoverCell/getZhichaCell_chart', 'BadCellAnalysis\BadHandoverCellController@getZhichaCellChart');
Route::get('/badHandoverCell/getWeakCoverCell_model', 'BadCellAnalysis\BadHandoverCellController@getWeakCoverCellModel');
Route::get('/badHandoverCell/getzhichaCell_model', 'BadCellAnalysis\BadHandoverCellController@getzhichaCellModel');
Route::get('/badHandoverCell/getOverlapCover_model', 'BadCellAnalysis\BadHandoverCellController@getOverlapCoverModel');
Route::get('/badHandoverCell/getInterfereCell_model', 'BadCellAnalysis\BadHandoverCellController@getInterfereCellModel');
Route::get('/badHandoverCell/getBaselineCheckData', 'BadCellAnalysis\BadHandoverCellController@getBaselineCheckData');
Route::get('/badHandoverCell/getRrcResultTableData', 'BadCellAnalysis\BadHandoverCellController@getRrcResultTableData');
Route::get('/badHandoverCell/rrcResult', 'BadCellAnalysis\BadHandoverCellController@getRrcResult');
Route::post('/badHandoverCell/getRrcResultDetailTableField', 'BadCellAnalysis\BadHandoverCellController@getRrcResultDetailTableField');
Route::post('/badHandoverCell/getRrcResultDetailData', 'BadCellAnalysis\BadHandoverCellController@getRrcResultDetailData');
Route::post('/badHandoverCell/exportRrcResultDetail', 'BadCellAnalysis\BadHandoverCellController@exportRrcResultDetail');
Route::post('/badHandoverCell/getNeighBadHandoverCellTable', 'BadCellAnalysis\BadHandoverCellController@getNeighBadHandoverCellTable');

Route::get('/badHandoverCell/getExecResultTableData', 'BadCellAnalysis\BadHandoverCellController@getExecResultTableData');
Route::get('/badHandoverCell/execResult', 'BadCellAnalysis\BadHandoverCellController@getExecResult');
Route::post('/badHandoverCell/getExecResultDetailTableField', 'BadCellAnalysis\BadHandoverCellController@getExecResultDetailTableField');
Route::post('/badHandoverCell/getExecResultDetailData', 'BadCellAnalysis\BadHandoverCellController@getExecResultDetailData');
Route::post('/badHandoverCell/exportExecResultDetail', 'BadCellAnalysis\BadHandoverCellController@exportExecResultDetail');
Route::get('/badHandoverCell/getIndexChartData', 'BadCellAnalysis\BadHandoverCellController@getIndexChartData');
Route::get('/badHandoverCell/getIndexTableData', 'BadCellAnalysis\BadHandoverCellController@getIndexTableData');
