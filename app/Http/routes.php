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

//注册账号
Route::get('/register', function () {
    return view('auth.userRegister');
});
Route::group(['prefix' => 'userRegister'], function () {
	Route::post('userRegister', 'SystemManage\UserRegisterController@userRegister');
});

//首页
Route::get('/home', function () {
    return view('network.survey');
});
// 菜单相关
Route::group(['prefix' => 'nav'], function () {
    //lijian语言切换功能
    Route::get('localeLang', 'NavController@localeLang');
    //xuyang 通知功能
    Route::post('addNotice', 'NavController@addNotice');
    Route::get('getNotice', 'NavController@getNotice');
    Route::get('readNotice', 'NavController@readNotice');
    Route::post('readAllNotice', 'NavController@readAllNotice');
    //lijian 地图适配
    Route::get('getOption', 'NavController@getOption');
    //菜单权限
    // Route::get('getMenuList', 'NavController@getMenuList');
    //通知-用户组
    Route::get('getUserGroup', 'NavController@getUserGroup');
    //xuyang 获得用户信息和登出
    Route::get('getUser', 'NavController@getUser');
    Route::get('signout', 'NavController@signout');
    //zjj sessions
    Route::get('getSessions', 'NavController@getSessions');
});
Route::get('csvZipDownload', 'CsvZipDownloadController@getZipFile')->middleware('auth');

//规模概览
Route::get('/scale', function () {
    return view('network.scale');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'scale'], function () {
	Route::get('getOption', 'NavController@getOption');
	Route::get('scaleExport', 'Exporter\ScaleExporter@export');
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
    Route::get('lowAccess', 'NetworkChartsController@getLowAccess');
    Route::get('highLost', 'NetworkChartsController@getHighLost');
    Route::get('badHandover', 'NetworkChartsController@getBadHandover');
});

// 指标概览
Route::get('/network', function () {
    return view('network.survey');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'network'], function () {
	Route::get('getNowTime', 'NetworkChartsTimerController@getNowTime');
	Route::get('getOption', 'NavController@getOption');
	Route::get('threeKeysGauge', 'NetworkChartsController@getThreeKeysGauge');
	Route::get('volteGauge', 'NetworkChartsController@getvolteGauge');
	Route::get('videosGauge', 'NetworkChartsController@getVideoGauge');
	Route::get('kpiExport', 'Exporter\KpiExporter@export');
	Route::get('lowAccessDefine', 'NetworkChartsController@getLowAccessDefine');
	Route::get('highLostDefine', 'NetworkChartsController@getHighLostDefine');
	Route::get('badHandoverDefine', 'NetworkChartsController@getBadHandoverDefine');
	Route::get('lowAccessTrendMore', 'NetworkChartsController@getLowAccessTrendMore');
	Route::get('highLostTrendMore', 'NetworkChartsController@getHighLostTrendMore');
	Route::get('badHandoverTrendMore', 'NetworkChartsController@getBadHandoverTrendMore');
	Route::get('lowAccessTrend', 'NetworkChartsController@getLowAccessTrend');
	Route::get('highLostTrend', 'NetworkChartsController@getHighLostTrend');
	Route::get('badHandoverTrend', 'NetworkChartsController@getBadHandoverTrend');
	Route::get('erabSuccessTrend', 'NetworkChartsController@getErabSuccessHandoverTrend');
	Route::get('erabLostTrend', 'NetworkChartsController@getErabsLostTrend');
	Route::get('wirelessSuccessTrend', 'NetworkChartsController@getWirelessSuccTrend');
	Route::get('volteHandoverTrend', 'NetworkChartsController@getVolteHandoverTrend');
	Route::get('chart1WireSuccTrend', 'NetworkChartsController@getChart1WireSuccTrend');
	Route::get('chart1ErbLostTrend', 'NetworkChartsController@getChart1ErbLostTrend');
	Route::get('chart1VideoSuccTrend', 'NetworkChartsController@getChart1VideoSuccTrend');
	Route::get('chart1EsrvccHanderTrend', 'NetworkChartsController@getChart1EsrvccHanderTrend');
	Route::get('erabSuccess', 'NetworkChartsController@getErabSuccessHandover');
	Route::get('erabLost', 'NetworkChartsController@getErabsLost');
	Route::get('wirelessSuccess', 'NetworkChartsController@getWirelessSucc');
	Route::get('volteHandover', 'NetworkChartsController@getVolteHandover');
	Route::get('chart1WireSucc', 'NetworkChartsController@getChart1WireSucc');
	Route::get('chart1ErbLost', 'NetworkChartsController@getChart1ErbLost');
	Route::get('chart1VideoSucc', 'NetworkChartsController@getChart1VideoSucc');
	Route::get('chart1EsrvccHander', 'NetworkChartsController@getChart1EsrvccHander');
});

// 短板概览
Route::get('/weak', function () {
    return view('network.weak');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'weak'], function () {
	Route::get('/weakExport', 'Exporter\NetworkChartsExporter@export');
    Route::get('baselineParamNum', 'WeakController@getBaselineParamNum');
    Route::get('baselineBSNum', 'WeakController@getBaselineBSNum');
    Route::get('consistencyParamNum', 'WeakController@getConsistencyParamNum');
    Route::get('consistencyBSNum', 'WeakController@getConsistencyBSNum');
    Route::get('interfereOverview', 'InterfereController@getInterfereData');
    Route::get('WeakCoverOverview', 'WeakCoverOverviewController@getWeakCoverData');
    Route::get('overlapCoverview', 'WeakoverlapCoverOverviewController@getOverlapCoverData');
    Route::get('currentAlarm', 'AlarmController@getCurrentAlarm');
    Route::get('historyAlarm', 'AlarmController@getHistoryAlarm');
    Route::get('historyAlarmDateData', 'AlarmController@getHistoryAlarmDateData');
    Route::get('currentAlarmdrillDownDonutPie', 'AlarmController@getDrillDownDonutPie');
    Route::get('historyAlarmDrillDownDonutPie', 'AlarmController@getHistoryDrillDownDonutPie');
    Route::get('badCellOverview', 'BadCellController@getBadCellData');
    Route::get('badCellOverviewDrillDownDonutPie', 'BadCellController@getDrillDownDonutPie');
});

// 信令概览
Route::get('/singal', function () {
    return view('network.singal');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'singal'], function () {
	Route::get('singalTrend', 'SingalChartsController@getSingalTrend');
});

Route::get('/LTEQueryHW', function(){
    return view('QueryAnalysis.LTEQueryHW');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'LTEQueryHW'], function () {
    Route::get('getLTETreeData', 'QueryAnalysis\LTEQueryHWController@getTreeData');
    Route::get('getAllCity', 'QueryAnalysis\LTEQueryHWController@getAllCity');
    Route::get('getAllSubNetwork', 'QueryAnalysis\LTEQueryHWController@getAllSubNetwork');
    Route::get('getFormatAllSubNetwork', 'QueryAnalysis\LTEQueryHWController@getFormatAllSubNetwork');
    Route::get('searchLTETreeData', 'QueryAnalysis\LTEQueryHWController@searchLTETreeData');
    Route::post('templateQuery', 'QueryAnalysis\LTEQueryHWController@templateQuery');
    // Route::post('uploadFile', 'QueryAnalysis\LTEQueryHWController@uploadFile');
    Route::post('uploadFile', 'FileSystem\FileController@Query_uploadFile');
    Route::get('LTETime', 'QueryAnalysis\LTEQueryHWController@LTETime');
    //xuyang LTE指标查询双击模板后弹出指标列表
    Route::get('getElementTree', 'QueryAnalysis\LTEQueryHWController@getElementTree');
    Route::get('getKpiNamebyId', 'QueryAnalysis\LTEQueryHWController@getKpiNamebyId');
});

Route::get('/LTETemplateManageHW', function () {
    return view('QueryAnalysis.LTETemplateManageHW');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'LTETemplateManageHW'], function () {
    Route::get('getLTETreeData', 'QueryAnalysis\LTETemplateHWController@getTreeData');
    Route::get('searchLTETreeData', 'QueryAnalysis\LTETemplateHWController@searchLTETreeData');
    Route::get('getElementTree', 'QueryAnalysis\LTETemplateHWController@getElementTree');
    Route::get('getKpiNamebyId', 'QueryAnalysis\LTETemplateHWController@getKpiNamebyId');
    Route::post('getTreeTemplate', 'QueryAnalysis\LTETemplateHWController@getTreeTemplate');
    Route::get('updateFormula', 'QueryAnalysis\LTETemplateHWController@updateFormula');
    Route::get('deleteFormula', 'QueryAnalysis\LTETemplateHWController@deleteFormula');
    Route::get('searchTreeTemplate', 'QueryAnalysis\LTETemplateHWController@searchTreeTemplate');
    Route::get('updateElement', 'QueryAnalysis\LTETemplateHWController@updateElement');
    Route::get('addMode', 'QueryAnalysis\LTETemplateHWController@addMode');
    Route::get('deleteMode', 'QueryAnalysis\LTETemplateHWController@deleteMode');
    Route::get('copyMode', 'QueryAnalysis\LTETemplateHWController@copyMode');
});

Route::get('/LTEQueryLocal', function () {
    return view('QueryAnalysis.LTEQueryLocal');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'LTEQueryLocal'], function () {
    Route::get('getLTETreeData', 'QueryAnalysis\LTEQueryLocalController@getTreeData');
    Route::get('getAllCity', 'QueryAnalysis\LTEQueryLocalController@getAllCity');
    Route::get('getAllSubNetwork', 'QueryAnalysis\LTEQueryLocalController@getAllSubNetwork');
    Route::get('getFormatAllSubNetwork', 'QueryAnalysis\LTEQueryLocalController@getFormatAllSubNetwork');
    Route::get('searchLTETreeData', 'QueryAnalysis\LTEQueryLocalController@searchLTETreeData');
    Route::post('templateQuery', 'QueryAnalysis\LTEQueryLocalController@templateQuery');
    Route::post('uploadFile', 'FileSystem\FileController@Query_uploadFile');
    Route::get('LTETime', 'QueryAnalysis\LTEQueryLocalController@LTETime');
    //xuyang LTE指标查询双击模板后弹出指标列表
    Route::get('getElementTree', 'QueryAnalysis\LTEQueryLocalController@getElementTree');
    Route::get('getKpiNamebyId', 'QueryAnalysis\LTEQueryLocalController@getKpiNamebyId');
});

//LTE指标查询 lijian
Route::get('/LTEQuery', function () {
    return view('QueryAnalysis.LTEQuery');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'LTEQuery'], function () {
	Route::get('getLTETreeData', 'QueryAnalysis\LTEQueryController@getTreeData');
	Route::get('getAllCity', 'QueryAnalysis\LTEQueryController@getAllCity');
	Route::get('getAllSubNetwork', 'QueryAnalysis\LTEQueryController@getAllSubNetwork');
	Route::get('getFormatAllSubNetwork', 'QueryAnalysis\LTEQueryController@getFormatAllSubNetwork');
	Route::get('searchLTETreeData', 'QueryAnalysis\LTEQueryController@searchLTETreeData');
	Route::post('templateQuery', 'QueryAnalysis\LTEQueryController@templateQuery');
	// Route::post('uploadFile', 'QueryAnalysis\LTEQueryController@uploadFile');
    Route::post('uploadFile', 'FileSystem\FileController@Query_uploadFile');
	Route::get('LTETime', 'QueryAnalysis\LTEQueryController@LTETime');
	//xuyang LTE指标查询双击模板后弹出指标列表
	Route::get('getElementTree', 'QueryAnalysis\LTEQueryController@getElementTree');
	Route::get('getKpiNamebyId', 'QueryAnalysis\LTEQueryController@getKpiNamebyId');
});
//流控指标查询 zhangguoli
Route::get('/FlowQuery', function () {
    return view('QueryAnalysis.FlowQuery');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'FlowQuery'], function () {
    Route::get('getLTETreeData', 'QueryAnalysis\FlowQueryController@getTreeData');
    Route::get('getAllCity', 'QueryAnalysis\FlowQueryController@getAllCity');
    Route::get('getAllSubNetwork', 'QueryAnalysis\FlowQueryController@getAllSubNetwork');
    Route::get('getFormatAllSubNetwork', 'QueryAnalysis\FlowQueryController@getFormatAllSubNetwork');
    Route::get('searchLTETreeData', 'QueryAnalysis\FlowQueryController@searchLTETreeData');
    Route::post('templateQuery', 'QueryAnalysis\FlowQueryController@templateQuery');
    Route::post('nbiQuery', 'QueryAnalysis\FlowNBIQueryController@templateQuery');
    // Route::post('uploadFile', 'QueryAnalysis\FlowQueryController@uploadFile');
    Route::post('uploadFlowQueryFile', 'QueryAnalysis\FlowQueryController@uploadFlowQueryFile');
    Route::post('uploadFile', 'FileSystem\FileController@Query_uploadFile');
    // Route::post('uploadFlowQueryFile', 'FileSystem\FileController@Query_uploadFile');
    Route::get('LTETime', 'QueryAnalysis\FlowQueryController@LTETime');
    Route::get('getElementTree', 'QueryAnalysis\FlowQueryController@getElementTree');
    Route::get('getKpiNamebyId', 'QueryAnalysis\FlowQueryController@getKpiNamebyId');
});
//模板管理zhangguoli
Route::get('/TemplateManage',function(){
    return view('systemManage.TemplateManage');
})->middleware('auth');
Route::group(['middleware'=>'auth','prefix'=>'TemplateManage'],function(){
    Route::get('getAllTypes','SystemManage\TemplateManageController@getAllTypes');
    Route::post('uplodeFile', 'SystemManage\TemplateManageController@uplodeFile');
    Route::get('downloadFile','SystemManage\TemplateManageController@downloadFile');
    Route::get('getManageDate','SystemManage\TemplateManageController@getManageDate');
    Route::get('deleteData','SystemManage\TemplateManageController@deleteData');
    Route::get('updateManage','SystemManage\TemplateManageController@updateManage');
});
//xuyang
Route::get('/LTETemplateManage', function () {
    return view('QueryAnalysis.LTETemplateManage');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'LTETemplateManage'], function () {
    Route::get('getLTETreeData', 'QueryAnalysis\LTETemplateController@getTreeData');
    Route::get('searchLTETreeData', 'QueryAnalysis\LTETemplateController@searchLTETreeData');
    Route::get('getElementTree', 'QueryAnalysis\LTETemplateController@getElementTree');
    Route::get('getKpiNamebyId', 'QueryAnalysis\LTETemplateController@getKpiNamebyId');
    Route::post('getTreeTemplate', 'QueryAnalysis\LTETemplateController@getTreeTemplate');
    Route::get('updateFormula', 'QueryAnalysis\LTETemplateController@updateFormula');
    Route::get('deleteFormula', 'QueryAnalysis\LTETemplateController@deleteFormula');
    Route::get('searchTreeTemplate', 'QueryAnalysis\LTETemplateController@searchTreeTemplate');
    Route::get('updateNewMode', 'QueryAnalysis\LTETemplateController@updateNewMode');
    Route::get('updateElement', 'QueryAnalysis\LTETemplateController@updateElement');
    Route::get('addMode', 'QueryAnalysis\LTETemplateController@addMode');
    Route::get('deleteMode', 'QueryAnalysis\LTETemplateController@deleteMode');
    Route::get('copyMode', 'QueryAnalysis\LTETemplateController@copyMode');
	//公式导入和导出
	Route::post('uplodeFile', 'QueryAnalysis\LTETemplateController@uplodeFile');
	Route::get('downloadFile', 'QueryAnalysis\LTETemplateController@downloadFile');
});

// NBI指标查询
Route::get('/NBIQuery', function () {
    return view('QueryAnalysis.NBIQuery');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'NBIQuery'], function () {
	Route::get('getNbiTreeData', 'QueryAnalysis\NBIQueryController@getTreeData');
	Route::post('templateQuery', 'QueryAnalysis\NBIQueryController@templateQuery');
    /*Route::get('getFormatAllSubNetwork', 'QueryAnalysis\NBIQueryController@getFormatAllSubNetwork');*/
	Route::post('templateQueryHeader', 'QueryAnalysis\NBIQueryController@templateQueryHeader');
	Route::get('NBIsTime', 'QueryAnalysis\NBIQueryController@NBITime');
	Route::get('searchNBITreeData', 'QueryAnalysis\NBIQueryController@searchNBITreeData');
	// Route::post('uploadFile', 'QueryAnalysis\LTEQueryController@uploadFile');
    Route::post('uploadFile', 'FileSystem\FileController@Query_uploadFile');
	Route::get('getAllCity', 'QueryAnalysis\LTEQueryController@getAllCity');
	//xuyang NBI指标查询双击模板后弹出指标列表
	Route::get('getElementTree', 'QueryAnalysis\NBIQueryController@getElementTree');
	Route::get('getKpiNamebyId', 'QueryAnalysis\NBIQueryController@getKpiNamebyId');
});

Route::get('/NBITemplateManage', function () {
    return view('QueryAnalysis.NBITemplateManage');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'NBITemplateManage'], function () {
    Route::get('getNBITreeData', 'QueryAnalysis\NBITemplateController@getTreeData');
    Route::get('searchNBITreeData', 'QueryAnalysis\NBITemplateController@searchNBITreeData');
    Route::get('getElementTree', 'QueryAnalysis\NBITemplateController@getElementTree');
    Route::get('getKpiNamebyId', 'QueryAnalysis\NBITemplateController@getKpiNamebyId');
    Route::post('getTreeTemplate', 'QueryAnalysis\NBITemplateController@getTreeTemplate');
    Route::get('updateFormula', 'QueryAnalysis\NBITemplateController@updateFormula');
    Route::get('deleteFormula', 'QueryAnalysis\NBITemplateController@deleteFormula');
    Route::get('searchTreeTemplate', 'QueryAnalysis\NBITemplateController@searchTreeTemplate');
    Route::get('updateElement', 'QueryAnalysis\NBITemplateController@updateElement');
    Route::get('addMode', 'QueryAnalysis\NBITemplateController@addMode');
    Route::get('deleteMode', 'QueryAnalysis\NBITemplateController@deleteMode');
    Route::get('copyMode', 'QueryAnalysis\NBITemplateController@copyMode');
});

//GSM指标查询 zhouyanqiu
Route::get('/GSMQuery', function () {
    return view('QueryAnalysis.GSMQuery');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'GSMQuery'], function () {
	Route::get('getGSMTreeData', 'QueryAnalysis\GSMQueryController@getTreeData');
	Route::get('GSMTime', 'QueryAnalysis\GSMQueryController@GSMTime');
	Route::get('searchGSMTreeData', 'QueryAnalysis\GSMQueryController@searchGSMTreeData');
	Route::get('getAllCity', 'QueryAnalysis\GSMQueryController@getAllCity');
	Route::post('templateQuery', 'QueryAnalysis\GSMQueryController@templateQuery');
    Route::post('uploadFile', 'FileSystem\FileController@Query_uploadFile');
	//xuyang GSM指标查询双击模板后弹出指标列表
	Route::get('getElementTree', 'QueryAnalysis\GSMQueryController@getElementTree');
	Route::get('getKpiNamebyId', 'QueryAnalysis\GSMQueryController@getKpiNamebyId');
});

//xuyang
Route::get('/GSMTemplateManage', function () {
    return view('QueryAnalysis.GSMTemplateManage');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'GSMTemplateManage'], function () {
    Route::get('getGSMTreeData', 'QueryAnalysis\GSMTemplateController@getTreeData');
    Route::get('searchGSMTreeData', 'QueryAnalysis\GSMTemplateController@searchGSMTreeData');
    Route::get('getElementTree', 'QueryAnalysis\GSMTemplateController@getElementTree');
    Route::get('getKpiNamebyId', 'QueryAnalysis\GSMTemplateController@getKpiNamebyId');
    Route::post('getTreeTemplate', 'QueryAnalysis\GSMTemplateController@getTreeTemplate');
    Route::get('updateFormula', 'QueryAnalysis\GSMTemplateController@updateFormula');
    Route::get('deleteFormula', 'QueryAnalysis\GSMTemplateController@deleteFormula');
    Route::get('searchTreeTemplate', 'QueryAnalysis\GSMTemplateController@searchTreeTemplate');
    Route::get('updateElement', 'QueryAnalysis\GSMTemplateController@updateElement');
    Route::get('addMode', 'QueryAnalysis\GSMTemplateController@addMode');
    Route::get('deleteMode', 'QueryAnalysis\GSMTemplateController@deleteMode');
    Route::get('copyMode', 'QueryAnalysis\GSMTemplateController@copyMode');
});

//指标分析-SQL语句查询 lijian
Route::get('/CustomQuery', function () {
    return view('QueryAnalysis.CustomQuery');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'CustomQuery'], function () {
	Route::get('getCustomTreeData', 'QueryAnalysis\CustomQueryController@getTreeData');
	Route::get('searchCustomTreeData', 'QueryAnalysis\CustomQueryController@getSearchCustomTreeData');
	Route::get('getAllCity', 'QueryAnalysis\CustomQueryController@getAllCity');
	Route::get('getKpiFormula', 'QueryAnalysis\CustomQueryController@getKpiFormula');
	Route::post('getTable', 'QueryAnalysis\CustomQueryController@getTable');
	Route::get('deleteMode', 'QueryAnalysis\CustomQueryController@deleteMode');
	Route::get('insertMode', 'QueryAnalysis\CustomQueryController@insertMode');
	Route::get('saveMode', 'QueryAnalysis\CustomQueryController@saveMode');
	Route::post('saveModeChange', 'QueryAnalysis\CustomQueryController@saveModeChange');
});

//丢包率查询 xuyang
Route::get('/packetLossAnalysis', function () {
    return view('badCellAnalysis.packetLossAnalysis');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'packetLossAnalysis'], function () {
    Route::get('getTableData', 'BadCellAnalysis\PacketLossAnalysisController@getTableData');
    Route::post('exportFile', 'BadCellAnalysis\PacketLossAnalysisController@exportFile');
      //覆盖率查询
    Route::get('CoverageQuery/getTableData', 'CoverageQueryController@getTableData');
    Route::post('CoverageQuery/exportFile', 'CoverageQueryController@exportFile');
    Route::post('CoverageQuery/getRSRPKey', 'CoverageQueryController@getRSRPKey');
     //MRORSRP
    Route::get('MRORSRPQuery/getTableData', 'BadCellAnalysis\MRORSRPQueryController@getTableData');
    Route::post('MRORSRPQuery/exportFile', 'BadCellAnalysis\MRORSRPQueryController@exportFile');
    Route::post('MRORSRPQuery/getCityDate', 'BadCellAnalysis\MRORSRPQueryController@getCityDate');
          //PowerHeadRoom
    Route::get('PowerHeadRoom/getTableData', 'PowerHeadRoomController@getTableData');
    Route::post('PowerHeadRoom/exportFile', 'PowerHeadRoomController@exportFile');
    Route::post('PowerHeadRoom/getPowerHeadRoomKey', 'PowerHeadRoomController@getPowerHeadRoomKey');
          //RSRQ,TADV,AOA,TadvRsrp
    Route::get('Survey/getTableData', 'SurveyController@getTableData');
    Route::post('Survey/exportFile', 'SurveyController@exportFile');
        //SinrUL
    Route::get('SurveySinr/getTableData', 'SurveySinrController@getTableData');
    Route::post('SurveySinr/exportFile', 'SurveySinrController@exportFile');
        //TADV
    Route::get('SurveyTADV/getTableData', 'SurveyTADVController@getTableData');
    Route::post('SurveyTADV/exportFile', 'SurveyTADVController@exportFile');
    //上传文件信息
    Route::post('uploadFile','FileSystem\FileController@Query_uploadFile');
    Route::get('getCitys', 'BadCellAnalysis\PacketLossAnalysisController@getCitys');
    Route::post('getCityDate', 'BadCellAnalysis\PacketLossAnalysisController@getCityDate');

});

//覆盖率查询 xuyang
Route::get('/CoverageQuery', function () {
    return view('network.CoverageQuery');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'CoverageQuery'], function () {
	Route::get('getTableData', 'CoverageQueryController@getTableData');
	Route::post('exportFile', 'CoverageQueryController@exportFile');
	Route::get('getCitys', 'CoverageQueryController@getCitys');
	Route::post('getCityDate', 'CoverageQueryController@getCityDate');
});

//参数查询 zjj
Route::get('/paramQuery', function () {
    return view('parameterAnalysis.paramQuery');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'paramQuery', 'namespace' => 'ParameterAnalysis'], function () {
    Route::post('getParamTasks', 'ParamQueryController@getParamTasks');
    Route::post('getParamItems', 'ParamQueryController@getParamItems');
    Route::post('getParamCitys', 'ParamQueryController@getParamCitys');
    Route::post('getParamTableField', 'ParamQueryController@getParamTableField');
    Route::post('exportParamFile', 'ParamQueryController@exportParamFile');
    Route::post('getParamData', 'ParamQueryController@getParamData');
    Route::post('getFeatureList', 'ParamQueryController@getFeatureList');
    Route::post('getAllSubNetwork', 'ParamQueryController@getAllSubNetwork');
});

//xuyang 参数分布
Route::get('/paramDistribution', function () {
    return view('parameterAnalysis.paramDistribution');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'paramDistribution'], function () {
	Route::post('getDate', 'ParameterAnalysis\ParamDistributionController@getDate');
	Route::post('getParameterList', 'ParameterAnalysis\ParamDistributionController@getParameterList');
	Route::post('getChartData', 'ParameterAnalysis\ParamDistributionController@getChartData');
	Route::post('getCitySelect', 'ParameterAnalysis\ParamDistributionController@getCitySelect');
	Route::post('getTableHeader', 'ParameterAnalysis\ParamDistributionController@getTableHeader');
	Route::post('getTableData', 'ParameterAnalysis\ParamDistributionController@getTableData');
	Route::post('exportCSV', 'ParameterAnalysis\ParamDistributionController@exportCSV');
	Route::post('getAllSubNetwork', 'ParameterAnalysis\ParamDistributionController@getAllSubNetwork');
	Route::get('updateSearchContext', 'ParameterAnalysis\ParamDistributionController@getTreeData');
	Route::get('updateSearch', 'ParameterAnalysis\ParamDistributionController@getUpdateSearch');
});

// 一致性检查
Route::get('/consistencyCheck', function () {
    return view('parameterAnalysis.consistencyCheck');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'consistencyCheck'], function () {
    Route::post('getTasks', 'ParameterAnalysis\ConsistencyCheckController@getTasks');
    Route::post('getCities', 'ParameterAnalysis\ConsistencyCheckController@getCities');
    Route::post('getCityList','ParameterAnalysis\ConsistencyCheckController@getCityList');
    Route::post('consistencyCheckDistribute', 'ParameterAnalysis\ConsistencyCheckController@getDistributeData');
    Route::post('getTableField', 'ParameterAnalysis\ConsistencyCheckController@getTableField');
    Route::post('getItems', 'ParameterAnalysis\ConsistencyCheckController@getItems');
    Route::post('exportFile', 'ParameterAnalysis\ConsistencyCheckController@exportFile');
    //导出全部信息
    Route::post('exportFiles', 'ParameterAnalysis\ConsistencyCheckController@exportFiles');
    Route::post('getOssInfoItems', 'ParameterAnalysis\ConsistencyCheckController@getOssInfoItems');
    Route::post('exportOssInfoFile', 'ParameterAnalysis\ConsistencyCheckController@exportOssInfoFile');
    Route::post('getFileContent', 'ParameterAnalysis\ConsistencyCheckController@getFileContent');
    Route::post('exportTemplate', 'ParameterAnalysis\ConsistencyCheckController@exportTemplate');
    Route::post('exportDT', 'ParameterAnalysis\ConsistencyCheckController@exportDT');
    Route::post('uploadFile','FileSystem\FileController@uploadFile');
});


// 基础数据检查 zhangguoli
Route::get('/BasicDataCheck', function () {
    return view('parameterAnalysis.BasicDataCheck');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'consistencyCheck'], function () {
    Route::post('getTasks', 'ParameterAnalysis\ConsistencyCheckController@getTasks');
    Route::post('getCities', 'ParameterAnalysis\ConsistencyCheckController@getCities');
    Route::post('getCityList','ParameterAnalysis\ConsistencyCheckController@getCityList');
    Route::post('consistencyCheckDistribute', 'ParameterAnalysis\ConsistencyCheckController@getDistributeData');
    Route::post('getTableField', 'ParameterAnalysis\ConsistencyCheckController@getTableField');
    Route::post('getItems', 'ParameterAnalysis\ConsistencyCheckController@getItems');
    Route::post('exportFile', 'ParameterAnalysis\ConsistencyCheckController@exportFile');
    //导出全部信息
    Route::post('exportFiles', 'ParameterAnalysis\ConsistencyCheckController@exportFiles');
    Route::post('getOssInfoItems', 'ParameterAnalysis\ConsistencyCheckController@getOssInfoItems');
    Route::post('exportOssInfoFile', 'ParameterAnalysis\ConsistencyCheckController@exportOssInfoFile');
    Route::post('getFileContent', 'ParameterAnalysis\ConsistencyCheckController@getFileContent');
    Route::post('exportTemplate', 'ParameterAnalysis\ConsistencyCheckController@exportTemplate');
    Route::post('exportDT', 'ParameterAnalysis\ConsistencyCheckController@exportDT');
    Route::post('uploadFile','FileSystem\FileController@uploadFile');
});

//Baseline检查 zhangyan
Route::get('/baselineCheck', function () {
    return view('parameterAnalysis.baselineCheck');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'baselineCheck'], function () {
	Route::post('getParamTableField', 'ParamQueryController@getParamTableField');
	Route::post('getBaseTree', 'ParameterAnalysis\BaselineCheckController@getBaseTree');
	Route::post('getParamTasks', 'ParameterAnalysis\BaselineCheckController@getParamTasks');
	Route::post('getParamCitys', 'ParameterAnalysis\BaselineCheckController@getParamCitys');
	Route::post('getAllCity', 'ParameterAnalysis\BaselineCheckController@getAllCity');
	Route::post('getChartDataCategory', 'ParameterAnalysis\BaselineCheckController@getChartDataCategory');
	Route::post('getTableField', 'ParameterAnalysis\BaselineCheckController@getTableField');
	Route::post('getParamItems', 'ParameterAnalysis\BaselineCheckController@getParamItems');
	Route::post('baselineFile', 'ParameterAnalysis\BaselineCheckController@baselineFile');
	Route::post('getFileContent', 'ParameterAnalysis\BaselineCheckController@getFileContent');
	Route::post('uploadFile','FileSystem\FileController@uploadFile');
});

//SC分场景核查 ZHUJJ
Route::get('/scCheck', function () {
    return view('parameterAnalysis.scCheck');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix'=>'scCheck'],function(){
    Route::get('getTasks','ParameterAnalysis\SCCheckController@getTasks');
    Route::post('getCityList', 'ParameterAnalysis\SCCheckController@getCityList');
    Route::post('getFileContent','ParameterAnalysis\SCCheckController@getFileContent');
    Route::post('downloadTemplateFile','ParameterAnalysis\SCCheckController@downloadTemplateFile');
    Route::post('runProcedure','ParameterAnalysis\SCCheckController@runProcedure');
    Route::post('getTableField','ParameterAnalysis\SCCheckController@getTableField');
    Route::post('getItems','ParameterAnalysis\SCCheckController@getItems');
    Route::post('downloadFile','ParameterAnalysis\SCCheckController@downloadFile');
    Route::post('uploadFile','FileSystem\FileController@uploadFile');
});

//zhangyongcai  扩容分析
Route::get('/RRU', function () {
    return view('parameterAnalysis.RRU');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'RRU'], function () {
	Route::get('getData', 'ParameterAnalysis\RRUController@getData');
	Route::get('getCityList', 'ParameterAnalysis\RRUController@getCityList');
	Route::get('getRRUDataHeader', 'ParameterAnalysis\RRUController@getRRUDataHeader');
	Route::get('getRRUData', 'ParameterAnalysis\RRUController@getRRUData');
	Route::get('getAllRRUData', 'ParameterAnalysis\RRUController@getAllRRUData');
	Route::post('runProcedure','ParameterAnalysis\RRUController@runProcedure');
});

//zhangyongcai 常用参数查询
Route::get('/StrideMOQuery', function () {
    return view('parameterAnalysis.StrideMOQuery');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'StrideMOQuery'], function () {
	Route::get('getData', 'ParameterAnalysis\StrideMOQueryController@getData');
	Route::get('StrideMOQueryDataHeader', 'ParameterAnalysis\StrideMOQueryController@getStrideMOQueryDataHeader');
	Route::get('StrideMOQueryData', 'ParameterAnalysis\StrideMOQueryController@getStrideMOQueryData');
	Route::get('downloadFile', 'ParameterAnalysis\StrideMOQueryController@downloadFile');
	Route::get('paramDataHeader', 'ParameterAnalysis\StrideMOQueryController@paramDataHeader');
	Route::get('paramData', 'ParameterAnalysis\StrideMOQueryController@paramData');
	Route::get('insertFile', 'ParameterAnalysis\StrideMOQueryController@insertFile');
	Route::post('getFileContent', 'ParameterAnalysis\StrideMOQueryController@getFileContent');
	Route::post('uploadFile','FileSystem\FileController@uploadFile');
});

//xuyang 参数分析-SQL语句查询
Route::get('/SQLQuery', function () {
    return view('parameterAnalysis.SQLQuery');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'SQLQuery'], function () {
	Route::get('getCustomTreeData', 'ParameterAnalysis\SQLQueryController@getCustomTreeData');
	Route::get('searchCustomTreeData', 'ParameterAnalysis\SQLQueryController@getSearchCustomTreeData');
	Route::get('getAllCity', 'ParameterAnalysis\SQLQueryController@getAllCity');
	Route::get('getKpiFormula', 'ParameterAnalysis\SQLQueryController@getKpiFormula');
	Route::post('getTableHeader', 'ParameterAnalysis\SQLQueryController@getTableHeader');
	Route::post('getTableData', 'ParameterAnalysis\SQLQueryController@getTableData');
	Route::post('getAllTableData', 'ParameterAnalysis\SQLQueryController@getAllTableData');
	Route::get('deleteMode', 'ParameterAnalysis\SQLQueryController@deleteMode');
	Route::get('insertMode', 'ParameterAnalysis\SQLQueryController@insertMode');
	Route::get('saveMode', 'ParameterAnalysis\SQLQueryController@saveMode');
	Route::post('saveModeChange', 'ParameterAnalysis\SQLQueryController@saveModeChange');
	Route::post('getParamTasks', 'ParameterAnalysis\BaselineCheckController@getParamTasks');
});
//Lte差小区 zhangguoli
Route::get("/LteTopCell",function(){
    return view("badCellAnalysis.LteTopCell");
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'LteTopCell'], function () {
    Route::get('getCities', 'BadCellAnalysis\LteTopCellController@getCities');
    Route::post('getCityDate', 'BadCellAnalysis\LteTopCellController@getCityDate');
    Route::post('getAllData', 'BadCellAnalysis\LteTopCellController@getAllData');
    Route::post('downloadFile', 'BadCellAnalysis\LteTopCellController@downloadFile');

});
//自忙时小区 zhaiguoliang
Route::get("/AppCoverage",function(){
    return view("badCellAnalysis.AppCoverage");
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'AppCoverage'], function () {
    Route::get('templateQuery', 'BadCellAnalysis\AppCoverageController@templateQuery');
    Route::get('getAllCity', 'BadCellAnalysis\AppCoverageController@getAllCity');
    Route::get('getAllHour', 'BadCellAnalysis\AppCoverageController@getAllHour');
    Route::get('getAllDate', 'BadCellAnalysis\AppCoverageController@getAllDate');

});
//低接入小区 zhouyanqiu
Route::get('/lowAccessCell', function () {
    return view('badCellAnalysis.lowAccessCell');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'lowAccessCell'], function () {
    Route::post('getTreatmentPrinciple', 'BadCellAnalysis\BadCellController@getTreatmentPrinciple');
    Route::post('deleteAutoDir', 'SystemManage\DataSourceController@deleteAutoDir');
    Route::get('monitor', 'SystemManage\StorageController@monitor');
    Route::get('runTask', 'SystemManage\StorageController@runTask');
    Route::post('addTask', 'SystemManage\LocalDataManageController@addTask');
    Route::post('storage', 'BadCellAnalysis\BadCellController@storage');
    Route::post('ctrTreeItems', 'BadCellAnalysis\BadCellController@ctrTreeItems');
	Route::get('lowStartTime', 'BadCellAnalysis\BadCellController@getlowTime');
	Route::get('setHighlostTime', 'BadCellAnalysis\BadCellController@getHighLostTime');
	Route::get('setBadHandoverTime', 'BadCellAnalysis\BadCellController@getBadHandoverTime');
	Route::get('getAllCity', 'BadCellAnalysis\BadCellController@getAllCity');
	Route::get('getCellAlarmClassifyTable', 'BadCellAnalysis\BadCellController@getCellAlarmClassifyTable');
	Route::get('getErbsAlarmClassifyTable', 'BadCellAnalysis\BadCellController@getErbsAlarmClassifyTable');
	Route::get('templateQuery', 'BadCellAnalysis\BadCellController@templateQuery');
	Route::get('rrcResult', 'BadCellAnalysis\BadCellController@getRrcResult');
	Route::get('rrcResult_erab', 'BadCellAnalysis\BadCellController@getRrcResult_erab');
     Route::get('getGanraoCell_chart','BadCellAnalysis\BadCellController@getGanraoCellChart');
	Route::get('getErbsAlarmClassify', 'BadCellAnalysis\BadCellController@getErbsAlarmClassify');
	Route::get('getCellAlarmClassify', 'BadCellAnalysis\BadCellController@getCellAlarmClassify');
	Route::get('getLowAccessCellData', 'BadCellAnalysis\BadCellController@getLowAccessCellData');
	Route::get('getHighLostCellData', 'BadCellAnalysis\BadCellController@getHighLostCellData');
	Route::get('getBadHandoverCellData', 'BadCellAnalysis\BadCellController@getBadHandoverCellData');
	Route::get('getChartData', 'BadCellAnalysis\BadCellController@getChartData');
	Route::post('getLTENeighborHeader_1', 'BadCellAnalysis\BadCellController@getLTENeighborHeader1');
	Route::post('getLTENeighborHeader', 'BadCellAnalysis\BadCellController@getLTENeighborHeader');
	Route::get('getLTENeighborData', 'BadCellAnalysis\BadCellController@getLTENeighborData');
	Route::post('getGSMNeighborHeader', 'BadCellAnalysis\BadCellController@getGSMNeighborHeader');
	Route::get('getGSMNeighborData', 'BadCellAnalysis\BadCellController@getGSMNeighborData');
	Route::get('getweakCoverageCell', 'BadCellAnalysis\BadCellController@getWeakCoverageCell');
	Route::get('switchData', 'SwitchController@getSwitchData');
	Route::get('switchDetail', 'SwitchController@getSwitchDetail');
	Route::get('switchSite', 'SwitchController@getSwitchSite');
	Route::get('handoverin', 'SwitchController@getHandOverIn');
	Route::get('handOverInDetail', 'SwitchController@getHandOverInDetail');
	Route::get('RRCusers', 'SwitchController@getRRCusers');
	Route::get('wireLessLost', 'SwitchController@getWireLessLost');
	Route::get('PUSCHInterfere', 'SwitchController@getPUSCHInterfere');
	Route::get('PUSCHInterferein', 'SwitchController@getPUSCHInterferein');
	Route::get('handoverSuccin', 'SwitchController@getHandoverSuccin');
	Route::get('RRCusersin', 'SwitchController@getRRCusersin');
	Route::get('wireLessLostin', 'SwitchController@getWireLessLostin');
    Route::get('getVolteAlarmNum', 'VolteCellAnalysis\VolteCellController@getVolteAlarmNum');
    Route::get('getVolteAvgPrb', 'VolteCellAnalysis\VolteCellController@getVolteAvgPrb');
    Route::get('lowaccesscellcanshu', 'VolteCellAnalysis\VolteCellController@lowaccesscellcanshu');
    Route::get('weakcover', 'VolteCellAnalysis\VolteCellController@weakcover');
    Route::get('overlapcover', 'VolteCellAnalysis\VolteCellController@overlapcover');
    Route::get('neighcell', 'VolteCellAnalysis\VolteCellController@neighcell');
    Route::get('getvolteZhichaCellChart', 'VolteCellAnalysis\VolteCellController@getvolteZhichaCellChart');
    Route::get('getvolteWeakCoverCellModel', 'VolteCellAnalysis\VolteCellController@getvolteWeakCoverCellModel');
    Route::get('avgta', 'VolteCellAnalysis\VolteCellController@avgta');
    // Route::get('getparameter', 'VolteCellAnalysis\VolteCellController@getparameter');
    Route::get('highrrcnum', 'VolteCellAnalysis\VolteCellController@highrrcnum');
	Route::get('getLTENeighborData_model', 'BadCellAnalysis\BadCellController@getLTENeighborDataModel');
	Route::get('getZhichaCell_chart', 'BadCellAnalysis\BadCellController@getZhichaCellChart');
	Route::get('getWeakCoverCell_model', 'BadCellAnalysis\BadCellController@getWeakCoverCellModel');
	Route::get('getzhichaCell_model', 'BadCellAnalysis\BadCellController@getzhichaCellModel');
	Route::get('getOverlapCover_model', 'BadCellAnalysis\BadCellController@getOverlapCoverModel');
	Route::get('getInterfereCell_model', 'BadCellAnalysis\BadCellController@getInterfereCellModel');
	Route::get('getNumOfDiagnosisData', 'BadCellAnalysis\BadCellController@getNumOfDiagnosisData');
	Route::get('getNumOfDiagnosisDataFilter_alarm', 'BadCellAnalysis\BadCellController@getNumOfDiagnosisDataFilter_alarm');
	Route::get('getNumOfDiagnosisDataFilter_weakCover', 'BadCellAnalysis\BadCellController@getNumOfDiagnosisDataFilter_weakCover');
	Route::get('getNumOfDiagnosisDataFilter_zhicha', 'BadCellAnalysis\BadCellController@getNumOfDiagnosisDataFilter_zhicha');
	Route::get('getNumOfDiagnosisDataFilter_overlapCover', 'BadCellAnalysis\BadCellController@getNumOfDiagnosisDataFilter_overlapCover');
	Route::get('getNumOfDiagnosisDataFilter_AvgPRB', 'BadCellAnalysis\BadCellController@getNumOfDiagnosisDataFilter_AvgPRB');
	Route::get('getNumOfDiagnosisDataFilter_highTraffic', 'BadCellAnalysis\BadCellController@getNumOfDiagnosisDataFilter_highTraffic');
	Route::get('getNumOfDiagnosisDataFilter_parameter', 'BadCellAnalysis\BadCellController@getNumOfDiagnosisDataFilter_parameter');
	Route::get('getNumOfDiagnosisData_mr', 'BadCellAnalysis\BadCellController@getNumOfDiagnosisDataMR');
	Route::get('getBaselineCheckData', 'BadCellAnalysis\BadCellController@getBaselineCheckData');
	Route::get('getRrcResultTableData_rrcc', 'BadCellAnalysis\BadCellController@getRrcResultTableData_rrcc');
	Route::get('getRrcResultTableData', 'BadCellAnalysis\BadCellController@getRrcResultTableData');
	Route::post('getRrcResultDetailTableField', 'BadCellAnalysis\BadCellController@getRrcResultDetailTableField');
	Route::post('getRrcResultDetailData', 'BadCellAnalysis\BadCellController@getRrcResultDetailData');
	Route::post('exportRrcResultDetail', 'BadCellAnalysis\BadCellController@exportRrcResultDetail');
	Route::get('switchOutTable', 'SwitchController@getSwitchOutTable');
	Route::get('switchOutTableIn', 'SwitchController@getSwitchOutTableIn');
	Route::get('getCounterLoseResultDistribution', 'BadCellAnalysis\BadCellController@getCounterLoseResultDistribution');
	Route::get('getWirelessCallRate_zhicha', 'BadCellAnalysis\BadCellController@getWirelessCallRate_zhicha');
	Route::get('getWirelessCallRate_interfere', 'BadCellAnalysis\BadCellController@getWirelessCallRate_interfere');
	Route::get('getWirelessCallRate_RRCEstSucc', 'BadCellAnalysis\BadCellController@getWirelessCallRate_RRCEstSucc');
	Route::get('getWirelessCallRate_ERABEstSucc', 'BadCellAnalysis\BadCellController@getWirelessCallRate_ERABEstSucc');
	Route::get('getRelatedTrends', 'BadCellAnalysis\BadCellController@getRelatedTrends');
    //0823 xuyang
    Route::post('getNeighborCellMapData', 'BadCellAnalysis\BadCellController@getNeighborCellMapData');
});

// 高掉线小区
Route::get('/highLostCell', function () {
    return view('badCellAnalysis.highLostCell');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'highLostCell'], function () {
    Route::post('getTreatmentPrinciple', 'BadCellAnalysis\HighLostCellController@getTreatmentPrinciple');
    Route::post('deleteAutoDir', 'SystemManage\DataSourceController@deleteAutoDir');
	Route::get('monitor', 'SystemManage\StorageController@monitor');
    Route::get('runTask', 'SystemManage\StorageController@runTask');
    Route::post('addTask', 'SystemManage\LocalDataManageController@addTask');
    Route::post('storage', 'BadCellAnalysis\HighLostCellController@storage');
    Route::post('ctrTreeItems', 'BadCellAnalysis\HighLostCellController@ctrTreeItems');
    Route::get('getAllCity', 'BadCellAnalysis\BadCellController@getAllCity');
	Route::get('templateQuery', 'BadCellAnalysis\HighLostCellController@templateQuery');
	Route::get('getPolarMapData', 'BadCellAnalysis\HighLostCellController@getPolarMapData');
    Route::get('highlostcellcanshu', 'VolteCellAnalysis\VolteCellController@highlostcellcanshu');
	Route::get('getNumOfDiagnosisDataFilter_alarm', 'BadCellAnalysis\HighLostCellController@getNumOfDiagnosisDataFilter_alarm');
	Route::get('getNumOfDiagnosisDataFilter_weakCover', 'BadCellAnalysis\HighLostCellController@getNumOfDiagnosisDataFilter_weakCover');
	Route::get('getNumOfDiagnosisDataFilter_zhicha', 'BadCellAnalysis\HighLostCellController@getNumOfDiagnosisDataFilter_zhicha');
	Route::get('getNumOfDiagnosisDataFilter_overlapCover', 'BadCellAnalysis\HighLostCellController@getNumOfDiagnosisDataFilter_overlapCover');
    Route::get('getGanraoCell_chart','BadCellAnalysis\HighLostCellController@getGanraoCellChart');
	Route::get('getNumOfDiagnosisDataFilter_AvgPRB', 'BadCellAnalysis\HighLostCellController@getNumOfDiagnosisDataFilter_AvgPRB');
	Route::get('getNumOfDiagnosisDataFilter_highTraffic', 'BadCellAnalysis\HighLostCellController@getNumOfDiagnosisDataFilter_highTraffic');
	Route::get('getNumOfDiagnosisDataFilter_parameter', 'BadCellAnalysis\HighLostCellController@getNumOfDiagnosisDataFilter_parameter');
	Route::get('getNumOfDiagnosisData_mr', 'BadCellAnalysis\HighLostCellController@getNumOfDiagnosisDataMR');
	Route::get('getCellAlarmClassifyTable', 'BadCellAnalysis\HighLostCellController@getCellAlarmClassifyTable');
	Route::get('getErbsAlarmClassifyTable', 'BadCellAnalysis\HighLostCellController@getErbsAlarmClassifyTable');
	Route::get('getLTENeighborData_model', 'BadCellAnalysis\HighLostCellController@getLTENeighborDataModel');
	Route::get('getZhichaCell_chart', 'BadCellAnalysis\HighLostCellController@getZhichaCellChart');
	Route::get('getWeakCoverCell_model', 'BadCellAnalysis\HighLostCellController@getWeakCoverCellModel');
	Route::get('getzhichaCell_model', 'BadCellAnalysis\HighLostCellController@getzhichaCellModel');
	Route::get('getOverlapCover_model', 'BadCellAnalysis\HighLostCellController@getOverlapCoverModel');
	Route::get('getInterfereCell_model', 'BadCellAnalysis\HighLostCellController@getInterfereCellModel');
	Route::get('getBaselineCheckData', 'BadCellAnalysis\HighLostCellController@getBaselineCheckData');
	Route::get('getRrcResultTableData', 'BadCellAnalysis\HighLostCellController@getRrcResultTableData');
	Route::get('rrcResult', 'BadCellAnalysis\HighLostCellController@getRrcResult');
	Route::post('getRrcResultDetailTableField', 'BadCellAnalysis\HighLostCellController@getRrcResultDetailTableField');
	Route::post('getRrcResultDetailData', 'BadCellAnalysis\HighLostCellController@getRrcResultDetailData');
	Route::post('exportRrcResultDetail', 'BadCellAnalysis\HighLostCellController@exportRrcResultDetail');
	Route::get('getCounterLoseResultDistribution', 'BadCellAnalysis\HighLostCellController@getCounterLoseResultDistribution');
	Route::get('getIndexChartData', 'BadCellAnalysis\HighLostCellController@getIndexChartData');
	Route::get('getIndexTableData', 'BadCellAnalysis\HighLostCellController@getIndexTableData');
	Route::get('switchDetail', 'SwitchController@getSwitchDetail');
	Route::get('switchData', 'SwitchController@getSwitchData');
	Route::get('RRCusers', 'SwitchController@getRRCusers');
	Route::get('wireLessLost', 'SwitchController@getWireLessLost');
	Route::get('PUSCHInterfere', 'SwitchController@getPUSCHInterfere');
	Route::get('handoverin', 'SwitchController@getHandOverIn');
	Route::get('PUSCHInterferein', 'SwitchController@getPUSCHInterferein');
	Route::get('RRCusersin', 'SwitchController@getRRCusersin');
	Route::get('wireLessLostin', 'SwitchController@getWireLessLostin');
	Route::get('handOverInDetail', 'SwitchController@getHandOverInDetail');
	Route::get('switchOutTable', 'SwitchController@getSwitchOutTable');
	Route::get('switchOutTableIn', 'SwitchController@getSwitchOutTableIn');
    //0823 xuyang
    Route::post('getNeighborCellMapData', 'BadCellAnalysis\HighLostCellController@getNeighborCellMapData');
});

// 切换差小区
Route::get('/badHandoverCell', function () {
    return view('badCellAnalysis.badHandoverCell');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'badHandoverCell'], function () {
    Route::post('deleteAutoDir', 'SystemManage\DataSourceController@deleteAutoDir');
	Route::get('getAllCity', 'BadCellAnalysis\BadCellController@getAllCity');
    Route::get('monitor', 'SystemManage\StorageController@monitor');
    Route::get('runTask', 'SystemManage\StorageController@runTask');
    Route::post('addTask', 'SystemManage\LocalDataManageController@addTask');
    Route::post('storage', 'BadCellAnalysis\BadHandoverCellController@storage');
    Route::post('ctrTreeItems', 'BadCellAnalysis\BadHandoverCellController@ctrTreeItems');
	Route::get('templateQuery', 'BadCellAnalysis\BadHandoverCellController@templateQuery');
	Route::get('getPolarMapData', 'BadCellAnalysis\BadHandoverCellController@getPolarMapData');
    Route::get('highrrcnum', 'VolteCellAnalysis\VolteCellController@highrrcnum');
    Route::get('getVolteAvgPrb', 'VolteCellAnalysis\VolteCellController@getVolteAvgPrb');
    Route::get('getVolteAlarmNum', 'VolteCellAnalysis\VolteCellController@getVolteAlarmNum');
    Route::get('weakcover', 'VolteCellAnalysis\VolteCellController@weakcover');
    Route::get('zhicha', 'VolteCellAnalysis\VolteCellController@zhicha');
    Route::get('avgtabadhandover', 'VolteCellAnalysis\VolteCellController@avgtabadhandover');
    Route::get('badHandovercellcanshu', 'VolteCellAnalysis\VolteCellController@badHandovercellcanshu');
	Route::get('getNumOfDiagnosisDataFilter_alarm', 'BadCellAnalysis\BadHandoverCellController@getNumOfDiagnosisDataFilter_alarm');
	Route::get('getNumOfDiagnosisDataFilter_weakCover', 'BadCellAnalysis\BadHandoverCellController@getNumOfDiagnosisDataFilter_weakCover');
	Route::get('getNumOfDiagnosisDataFilter_zhicha', 'BadCellAnalysis\BadHandoverCellController@getNumOfDiagnosisDataFilter_zhicha');
	Route::get('getNumOfDiagnosisDataFilter_overlapCover', 'BadCellAnalysis\BadHandoverCellController@getNumOfDiagnosisDataFilter_overlapCover');
	Route::get('getNumOfDiagnosisDataFilter_AvgPRB', 'BadCellAnalysis\BadHandoverCellController@getNumOfDiagnosisDataFilter_AvgPRB');
	Route::get('getNumOfDiagnosisDataFilter_highTraffic', 'BadCellAnalysis\BadHandoverCellController@getNumOfDiagnosisDataFilter_highTraffic');
	Route::get('getNumOfDiagnosisDataFilter_parameter', 'BadCellAnalysis\BadHandoverCellController@getNumOfDiagnosisDataFilter_parameter');
    Route::get('getGanraoCell_chart','BadCellAnalysis\BadHandoverCellController@getGanraoCellChart');
	Route::get('getNumOfDiagnosisData_mr', 'BadCellAnalysis\BadHandoverCellController@getNumOfDiagnosisDataMR');
	Route::get('getCellAlarmClassifyTable', 'BadCellAnalysis\BadHandoverCellController@getCellAlarmClassifyTable');
	Route::get('getErbsAlarmClassifyTable', 'BadCellAnalysis\BadHandoverCellController@getErbsAlarmClassifyTable');
	Route::get('getLTENeighborData_model', 'BadCellAnalysis\BadHandoverCellController@getLTENeighborDataModel');
	Route::get('getZhichaCell_chart', 'BadCellAnalysis\BadHandoverCellController@getZhichaCellChart');
	Route::get('getWeakCoverCell_model', 'BadCellAnalysis\BadHandoverCellController@getWeakCoverCellModel');
	Route::get('getzhichaCell_model', 'BadCellAnalysis\BadHandoverCellController@getzhichaCellModel');
	Route::get('getOverlapCover_model', 'BadCellAnalysis\BadHandoverCellController@getOverlapCoverModel');
	Route::get('getInterfereCell_model', 'BadCellAnalysis\BadHandoverCellController@getInterfereCellModel');
	Route::get('getBaselineCheckData', 'BadCellAnalysis\BadHandoverCellController@getBaselineCheckData');
	Route::get('getRrcResultTableData', 'BadCellAnalysis\BadHandoverCellController@getRrcResultTableData');
	Route::get('rrcResult', 'BadCellAnalysis\BadHandoverCellController@getRrcResult');
	Route::post('getRrcResultDetailTableField', 'BadCellAnalysis\BadHandoverCellController@getRrcResultDetailTableField');
	Route::post('getRrcResultDetailData', 'BadCellAnalysis\BadHandoverCellController@getRrcResultDetailData');
	Route::post('exportRrcResultDetail', 'BadCellAnalysis\BadHandoverCellController@exportRrcResultDetail');
	Route::post('getNeighBadHandoverCellTable', 'BadCellAnalysis\BadHandoverCellController@getNeighBadHandoverCellTable');
	Route::get('getExecResultTableData', 'BadCellAnalysis\BadHandoverCellController@getExecResultTableData');
	Route::get('execResult', 'BadCellAnalysis\BadHandoverCellController@getExecResult');
	Route::post('getExecResultDetailTableField', 'BadCellAnalysis\BadHandoverCellController@getExecResultDetailTableField');
	Route::post('getExecResultDetailData', 'BadCellAnalysis\BadHandoverCellController@getExecResultDetailData');
	Route::post('exportExecResultDetail', 'BadCellAnalysis\BadHandoverCellController@exportExecResultDetail');
	Route::get('getIndexChartData', 'BadCellAnalysis\BadHandoverCellController@getIndexChartData');
	Route::get('getIndexTableData', 'BadCellAnalysis\BadHandoverCellController@getIndexTableData');
	Route::get('switchDetail', 'SwitchController@getSwitchDetail');
	Route::get('switchData', 'SwitchController@getSwitchData');
	Route::get('RRCusers', 'SwitchController@getRRCusers');
	Route::get('wireLessLost', 'SwitchController@getWireLessLost');
	Route::get('PUSCHInterfere', 'SwitchController@getPUSCHInterfere');
	Route::get('handoverin', 'SwitchController@getHandOverIn');
	Route::get('PUSCHInterferein', 'SwitchController@getPUSCHInterferein');
	Route::get('RRCusersin', 'SwitchController@getRRCusersin');
	Route::get('wireLessLostin', 'SwitchController@getWireLessLostin');
	Route::get('handOverInDetail', 'SwitchController@getHandOverInDetail');
	Route::get('switchOutTable', 'SwitchController@getSwitchOutTable');
	Route::get('switchOutTableIn', 'SwitchController@getSwitchOutTableIn');
    //0823 xuyang
    Route::post('getNeighborCellMapData', 'BadCellAnalysis\BadHandoverCellController@getNeighborCellMapData');
});

//xuyang 高干扰小区
Route::get('/highInterferenceCell', function () {
    return view('badCellAnalysis.highInterferenceCell');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'highInterferenceCell'], function () {
	Route::get('getCitys', 'BadCellAnalysis\HighInterferenceCellController@getCitys');
	Route::get('getCellData', 'BadCellAnalysis\HighInterferenceCellController@getCellData');
	Route::post('getAllCellData', 'BadCellAnalysis\HighInterferenceCellController@getAllCellData');
	Route::get('getAlarmData', 'BadCellAnalysis\HighInterferenceCellController@getAlarmData');
	Route::post('getTimeList', 'BadCellAnalysis\HighInterferenceCellController@getTimeList');
	Route::post('getTimeChartData', 'BadCellAnalysis\HighInterferenceCellController@getTimeChartData');
	Route::post('getFrequencyChartData', 'BadCellAnalysis\HighInterferenceCellController@getFrequencyChartData');
	Route::get('highStartTime', 'BadCellAnalysis\HighInterferenceCellController@highTime');
	Route::get('conflictNum', 'BadCellAnalysis\HighInterferenceCellController@getConflictNum');
	Route::get('overlapCeakCoverNum', 'BadCellAnalysis\BadCellController@getOverlapCeakCoverNum');
	Route::get('getCellAlarmClassify', 'BadCellAnalysis\BadCellController@getCellAlarmClassify');
	Route::get('getErbsAlarmClassify', 'BadCellAnalysis\BadCellController@getErbsAlarmClassify');
	Route::get('getInterfereAnalysis', 'BadCellAnalysis\BadCellController@getInterfereAnalysis');
	Route::get('getPrbNum', 'BadCellAnalysis\BadCellController@getPrbNum');
	Route::get('getalarmWorstCell', 'BadCellAnalysis\BadCellController@getalarmWorstCell');
	Route::post('getLTENeighborHeader_1', 'BadCellAnalysis\BadCellController@getLTENeighborHeader1');
	Route::get('getLTENeighborData_1', 'BadCellAnalysis\BadCellController@getLTENeighborData1');
	Route::post('getLTENeighborHeader', 'BadCellAnalysis\BadCellController@getLTENeighborHeader');
	Route::get('getLTENeighborData', 'BadCellAnalysis\BadCellController@getLTENeighborData');
	Route::post('getGSMNeighborHeader', 'BadCellAnalysis\BadCellController@getGSMNeighborHeader');
	Route::get('getGSMNeighborData', 'BadCellAnalysis\BadCellController@getGSMNeighborData');
	Route::get('getweakCoverageCell', 'BadCellAnalysis\BadCellController@getWeakCoverageCell');
	Route::get('alarmNum', 'BadCellAnalysis\BadCellController@getAlarmNum');
});

//历史小区查询
Route::get('/historyCellSearch', function () {
    return view('badCellAnalysis.historyCellSearch');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'historyCellSearch'], function () {
	Route::get('selectCityOption', 'BadCellAnalysis\HistoryCellSearchController@getCityOption');
	Route::get('historyCellDate', 'BadCellAnalysis\HistoryCellSearchController@getHistoryCellDate');
	Route::post('historyCell', 'BadCellAnalysis\HistoryCellSearchController@getHistoryCell');
	Route::get('getIndexCell', 'BadCellAnalysis\HistoryCellSearchController@getIndexCell');
	Route::get('getChartDataHistory', 'BadCellAnalysis\HistoryCellSearchController@getChartDataHistory');
});

// 极端高话务小区
Route::get('/extremeHighTrafficCell',function(){
    return view('badCellAnalysis.extremeHighTrafficCell');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix'=>'extremeHighTrafficCell','namespace'=>'BadCellAnalysis'],function(){
    Route::get('getTasks','ExtremeHighTrafficCellController@getTasks');
    Route::get('getCitys','ExtremeHighTrafficCellController@getCitys');
    Route::post('getTableField','ExtremeHighTrafficCellController@getTableField');
    Route::get('getCellData','ExtremeHighTrafficCellController@getCellData');
    Route::post('getAllCellData','ExtremeHighTrafficCellController@getAllCellData');
});

//volte上行差小区
Route::get('/volteupbadcell', function () {
    return view('VolteCellAnalysis.volteupbadcell');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'volteupbadcell'], function () {
    Route::get('templateQuery', 'VolteCellAnalysis\volteupbadcellController@templateQuery');
    Route::get('getAllCity', 'VolteCellAnalysis\volteupbadcellController@getAllCity');
    Route::get('getcurrentAlarmNum', 'VolteCellAnalysis\volteupbadcellController@getcurrentAlarmNum');
    Route::get('getReportData', 'VolteCellAnalysis\volteupbadcellController@getReportData');
    Route::get('getavgPrb', 'VolteCellAnalysis\volteupbadcellController@getavgPrb');
    Route::get('overlapcover', 'VolteCellAnalysis\volteupbadcellController@overlapcover');
    Route::get('parameter', 'VolteCellAnalysis\volteupbadcellController@parameter');
    Route::get('neightcell', 'VolteCellAnalysis\volteupbadcellController@neightcell');
    Route::post('ctrTreeItems', 'VolteCellAnalysis\volteupbadcellController@ctrTreeItems');
    Route::post('storage', 'VolteCellAnalysis\volteupbadcellController@storage');
    Route::post('addTask', 'SystemManage\LocalDataManageController@addTask');
    Route::get('monitor', 'SystemManage\StorageController@monitor');
    Route::get('runTask', 'SystemManage\StorageController@runTask');
    Route::post('deleteAutoDir', 'SystemManage\DataSourceController@deleteAutoDir');
    Route::get('monitor', 'SystemManage\StorageController@monitor');
});

//volte差小区报告
Route::get('/voltereportcell', function () {
    return view('VolteCellAnalysis.voltereportcell');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix'=>'voltereportcell'],function(){
    Route::get('getInterfereCellModel', 'VolteCellAnalysis\voltereportcellController@getInterfereCellModel');
    Route::get('getGanraoCellChart', 'VolteCellAnalysis\voltereportcellController@getGanraoCellChart');
    Route::get('getzhichaCellModel', 'VolteCellAnalysis\voltereportcellController@getzhichaCellModel');
    Route::get('getZhichaCellChart', 'VolteCellAnalysis\voltereportcellController@getZhichaCellChart');
    Route::get('getOverlapCoverModel', 'VolteCellAnalysis\voltereportcellController@getOverlapCoverModel');
    Route::get('getWeakCoverCellModel', 'VolteCellAnalysis\voltereportcellController@getWeakCoverCellModel');
    Route::get('getBaselineCheckData', 'VolteCellAnalysis\voltereportcellController@getBaselineCheckData');
    Route::get('getAlarmCellModel', 'VolteCellAnalysis\voltereportcellController@getAlarmCellModel');
    Route::get('getPusch', 'VolteCellAnalysis\voltereportcellController@getPusch');
    Route::get('getPucch', 'VolteCellAnalysis\voltereportcellController@getPucch');
    Route::get('getLTENeighborDataModel', 'VolteCellAnalysis\voltereportcellController@getLTENeighborDataModel');
    Route::post('getNeighborCellMapData', 'VolteCellAnalysis\voltereportcellController@getNeighborCellMapData');
    Route::get('getGSMNeighborDataModel', 'VolteCellAnalysis\voltereportcellController@getGSMNeighborDataModel');
    Route::post('get2GNeighborCellMapData', 'VolteCellAnalysis\voltereportcellController@get2GNeighborCellMapData');
    Route::get('getAlarmErbsModel', 'VolteCellAnalysis\voltereportcellController@getAlarmErbsModel');
});

//volte下行差小区
Route::get('/voltedownbadcell', function () {
    return view('VolteCellAnalysis.voltedownbadcell');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'voltedownbadcell'], function () {
    Route::get('templateQuery', 'VolteCellAnalysis\voltedownbadcellController@templateQuery');
    Route::get('getAllCity', 'VolteCellAnalysis\voltedownbadcellController@getAllCity');
    Route::get('getcurrentAlarmNum', 'VolteCellAnalysis\voltedownbadcellController@getcurrentAlarmNum');
    Route::get('getReportData', 'VolteCellAnalysis\voltedownbadcellController@getReportData');
    Route::get('getavgPrb', 'VolteCellAnalysis\voltedownbadcellController@getavgPrb');
    Route::get('overlapcover', 'VolteCellAnalysis\voltedownbadcellController@overlapcover');
    Route::get('parameter', 'VolteCellAnalysis\voltedownbadcellController@parameter');
    Route::get('neightcell', 'VolteCellAnalysis\voltedownbadcellController@neightcell');
    Route::post('ctrTreeItems', 'VolteCellAnalysis\voltedownbadcellController@ctrTreeItems');
    Route::post('storage', 'VolteCellAnalysis\voltedownbadcellController@storage');
    Route::post('addTask', 'SystemManage\LocalDataManageController@addTask');
    Route::get('monitor', 'SystemManage\StorageController@monitor');
    Route::get('runTask', 'SystemManage\StorageController@runTask');
    Route::post('deleteAutoDir', 'SystemManage\DataSourceController@deleteAutoDir');
    Route::get('monitor', 'SystemManage\StorageController@monitor');
});

//srvcc差小区
Route::get('/srvccbadcell', function () {
    return view('VolteCellAnalysis.srvccbadcell');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'srvccbadcell'], function () {
    Route::get('templateQuery', 'VolteCellAnalysis\srvccbadcellController@templateQuery');
    Route::get('getAllCity', 'VolteCellAnalysis\srvccbadcellController@getAllCity');
    Route::get('getcurrentAlarmNum', 'VolteCellAnalysis\srvccbadcellController@getcurrentAlarmNum');
    Route::get('overlapcover', 'VolteCellAnalysis\srvccbadcellController@overlapcover');
    Route::get('getRsrq', 'VolteCellAnalysis\srvccbadcellController@getRsrq');
    Route::get('neightcell', 'VolteCellAnalysis\srvccbadcellController@neightcell');
    Route::get('parameter', 'VolteCellAnalysis\srvccbadcellController@parameter');
    Route::get('getBaselineCheckData', 'VolteCellAnalysis\srvccbadcellController@getBaselineCheckData');
    Route::post('ctrTreeItems', 'VolteCellAnalysis\srvccbadcellController@ctrTreeItems');
    Route::post('storage', 'VolteCellAnalysis\srvccbadcellController@storage');
    Route::post('addTask', 'SystemManage\LocalDataManageController@addTask');
    Route::get('monitor', 'SystemManage\StorageController@monitor');
    Route::get('runTask', 'SystemManage\StorageController@runTask');
    Route::post('deleteAutoDir', 'SystemManage\DataSourceController@deleteAutoDir');
    Route::get('monitor', 'SystemManage\StorageController@monitor');
});

//xuyang PCI MOD 3分析
Route::get('/PCIMOD3Analysis', function () {
    return view('networkOptimization.PCIMOD3Analysis');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'PCIMOD3Analysis'], function () {
	Route::post('getAllCity', 'NetworkOptimization\PCIMOD3AnalysisController@getAllCity');
	Route::post('PCIMOD3Date', 'NetworkOptimization\PCIMOD3AnalysisController@getPCIMOD3Date');
	Route::post('getMroPCIMOD3DataHeader', 'NetworkOptimization\PCIMOD3AnalysisController@getMroPCIMOD3DataHeader');
	Route::post('getMroPCIMOD3Data', 'NetworkOptimization\PCIMOD3AnalysisController@getMroPCIMOD3Data');
	Route::post('getAllMroPCIMOD3Data', 'NetworkOptimization\PCIMOD3AnalysisController@getAllMroPCIMOD3Data');
	Route::post('getMroPCIMOD3GeniusDataHeader', 'NetworkOptimization\PCIMOD3AnalysisController@getMroPCIMOD3GeniusDataHeader');
	Route::post('getMroPCIMOD3GeniusData', 'NetworkOptimization\PCIMOD3AnalysisController@getMroPCIMOD3GeniusData');
	Route::post('getAllMroPCIMOD3GeniusData', 'NetworkOptimization\PCIMOD3AnalysisController@getAllMroPCIMOD3GeniusData');
	Route::post('PCIMOD3GeniusDate', 'NetworkOptimization\PCIMOD3AnalysisController@getPCIMOD3GeniusDate');
});

//xuyang 当前告警查询
Route::get('/currentAlarmQuery', function () {
    return view('alarmAnalysis.currentAlarmQuery');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'currentAlarmQuery'], function () {
	Route::get('getCitys', 'AlarmAnalysis\CurrentAlarmQueryController@getCitys');
	Route::get('getTableData', 'AlarmAnalysis\CurrentAlarmQueryController@getTableData');
	Route::post('getAllTableData', 'AlarmAnalysis\CurrentAlarmQueryController@getAllTableData');
	Route::post('uploadFile', 'FileSystem\FileController@Query_uploadFile');
});

//zhangguoli LTE告警分析
Route::get('LteAlarmQuery',function(){
    return view('alarmAnalysis.LteAlarmQuery');
})->middleware('auth');
Route::group(['middleware'=>'auth','prefix'=>'LteAlarmQuery'],function(){
    Route::get('getCitys', 'AlarmAnalysis\LteAlarmQueryController@getCitys');
    Route::get('getTableData', 'AlarmAnalysis\LteAlarmQueryController@getTableData');
    Route::post('getAllTableData', 'AlarmAnalysis\LteAlarmQueryController@getAllTableData');
    Route::get('getLteAlarmTime', 'AlarmAnalysis\LteAlarmQueryController@getLteAlarmTime');
    Route::post('uploadFile', 'FileSystem\FileController@Query_uploadFile');
});

//zhangguoli NB告警分析
Route::get('NBAlarmQuery',function(){
    return view('alarmAnalysis.NBAlarmQuery');
})->middleware('auth');

Route::group(['middleware'=>'auth','prefix'=>'NBAlarmQuery'],function(){
    Route::get('getCitys', 'AlarmAnalysis\NBAlarmQueryController@getCitys');
    Route::get('getTableData', 'AlarmAnalysis\NBAlarmQueryController@getTableData');
    Route::post('getAllTableData', 'AlarmAnalysis\NBAlarmQueryController@getAllTableData');
    Route::get('getNBAlarmTime', 'AlarmAnalysis\NBAlarmQueryController@getNBAlarmTime');
    Route::post('uploadFile', 'FileSystem\FileController@Query_uploadFile');
});

//zhangguoli GSM告警分析
Route::get('GSMAlarmQuery',function(){
    return view('alarmAnalysis.GSMAlarmQuery');
})->middleware('auth');

Route::group(['middleware' => 'auth','prefix' => 'GSMAlarmQuery'], function () {
    Route::get('getCitys', 'AlarmAnalysis\GSMAlarmQueryController@getCitys');
    Route::get('getTableData', 'AlarmAnalysis\GSMAlarmQueryController@getTableData');
    Route::post('getAllTableData', 'AlarmAnalysis\GSMAlarmQueryController@getAllTableData');
    Route::get('getGSMAlarmTime', 'AlarmAnalysis\GSMAlarmQueryController@getGSMAlarmTime');
    Route::post('uploadFile', 'FileSystem\FileController@Query_uploadFile');
});

//xuyang 历史告警查询
Route::get('/historyAlarmQuery', function () {
    return view('alarmAnalysis.historyAlarmQuery');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'historyAlarmQuery'], function () {
	Route::get('getCitys', 'AlarmAnalysis\HistoryAlarmQueryController@getCitys');
	Route::get('getTableData', 'AlarmAnalysis\HistoryAlarmQueryController@getTableData');
	Route::post('getAllTableData', 'AlarmAnalysis\HistoryAlarmQueryController@getAllTableData');
	Route::get('getHistoryAlarmTime', 'AlarmAnalysis\HistoryAlarmQueryController@getHistoryAlarmTime');
	Route::post('uploadFile', 'FileSystem\FileController@Query_uploadFile');
});

//lijian 当前告警查询2G
Route::get('/currentAlarmQuery2G', function () {
    return view('alarmAnalysis.currentAlarmQuery2G');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'currentAlarmQuery2G'], function () {
    Route::get('getCitys', 'AlarmAnalysis\CurrentAlarmQuery2GController@getCitys');
    Route::get('getTableData', 'AlarmAnalysis\CurrentAlarmQuery2GController@getTableData');
    Route::post('getAllTableData', 'AlarmAnalysis\CurrentAlarmQuery2GController@getAllTableData');
    Route::post('uploadFile', 'FileSystem\FileController@Query_uploadFile');
});

//lijian 历史告警查询2G
Route::get('/historyAlarmQuery2G', function () {
    return view('alarmAnalysis.historyAlarmQuery2G');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'historyAlarmQuery2G'], function () {
    Route::get('getCitys', 'AlarmAnalysis\HistoryAlarmQuery2GController@getCitys');
    Route::get('getTableData', 'AlarmAnalysis\HistoryAlarmQuery2GController@getTableData');
    Route::post('getAllTableData', 'AlarmAnalysis\HistoryAlarmQuery2GController@getAllTableData');
    Route::get('getHistoryAlarmTime', 'AlarmAnalysis\HistoryAlarmQuery2GController@getHistoryAlarmTime');
    Route::post('uploadFile', 'FileSystem\FileController@Query_uploadFile');
});

//zhujiaojiao NAS原因值分析
Route::get('/L3Analysis', function () {
    return view('complaintHandling.L3Analysis');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'L3Analysis', 'namespace' => 'ComplaintHandling'], function () {
    Route::get('getCitys', 'L3AnalysisController@getCitys');
    Route::post('getL3AnalysisData', 'L3AnalysisController@getL3AnalysisData');
    Route::post('getChartData', 'L3AnalysisController@getChartData');
    Route::get('getTableData', 'L3AnalysisController@getTableData');
    Route::get('getdetailDataHeader', 'L3AnalysisController@getdetailDataHeader');
    Route::get('getdetailData', 'L3AnalysisController@getdetailData');
    Route::post('exportFile', 'L3AnalysisController@exportFile');
    Route::post('getSolidgaugeData', 'L3AnalysisController@getSolidgaugeData');
});

//xuyang ENB原因值分析
Route::get('/ENBAnalysis', function () {
    return view('complaintHandling.ENBAnalysis');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'ENBAnalysis'], function () {
	Route::get('getCitys', 'ComplaintHandling\ENBAnalysisController@getCitys');
	Route::post('getENBAnalysisDate', 'ComplaintHandling\ENBAnalysisController@getENBAnalysisDate');
	Route::post('getChartData', 'ComplaintHandling\ENBAnalysisController@getChartData');
	Route::post('getdetailDataHeader', 'ComplaintHandling\ENBAnalysisController@getdetailDataHeader');
	Route::get('getdetailData', 'ComplaintHandling\ENBAnalysisController@getdetailData');
	Route::post('exportFile', 'ComplaintHandling\ENBAnalysisController@exportFile');
	Route::post('getSuccessChartData', 'ComplaintHandling\ENBAnalysisController@getSuccessChartData');
});

//xuyang CTR信令分析
Route::get('/signalingBacktracking', function () {
    return view('complaintHandling.signalingBacktracking');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'signalingBacktracking'], function () {
	Route::get('getDataBase', 'ComplaintHandling\SignalingBacktrackingController@getDataBase');
	Route::get('getEventNameandEcgi', 'ComplaintHandling\SignalingBacktrackingController@getEventNameandEcgi');
	Route::post('getEventData', 'ComplaintHandling\SignalingBacktrackingController@getEventData');
	Route::get('getEventDataHeader', 'ComplaintHandling\SignalingBacktrackingController@getEventDataHeader');
	Route::post('getAllEventData', 'ComplaintHandling\SignalingBacktrackingController@getAllEventData');
	Route::get('showMessage', 'ComplaintHandling\SignalingBacktrackingController@showMessage');
	Route::post('exportCSV', 'ComplaintHandling\SignalingBacktrackingController@exportCSV');
});

//xuyang Volte信令分析
Route::get('/signalingAnalysis', function () {
    return view('complaintHandling.signalingAnalysis');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'signalingAnalysis'], function () {
	Route::get('getChartData', 'ComplaintHandling\SignalingAnalysisController@getChartData');
	Route::get('showMessage', 'ComplaintHandling\SignalingAnalysisController@showMessage');
	Route::get('getDataBase', 'ComplaintHandling\SignalingAnalysisController@getDataBase');
	Route::get('queryKeyword', 'ComplaintHandling\SignalingAnalysisController@queryKeyword');
});

//xuyang ENB原因值分析
Route::get('/failureAnalysis', function () {
    return view('badCellAnalysis.failureAnalysis');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'failureAnalysis'], function () {
	Route::get('getDataBase', 'BadCellAnalysis\FailureAnalysisController@getDataBase');
	Route::post('getChartData', 'BadCellAnalysis\FailureAnalysisController@getChartData');
	Route::get('getTableData', 'BadCellAnalysis\FailureAnalysisController@getTableData');
	Route::post('exportFile', 'BadCellAnalysis\FailureAnalysisController@exportFile');
	Route::post('getdetailDataHeader', 'BadCellAnalysis\FailureAnalysisController@getdetailDataHeader');
	Route::get('getdetailData', 'BadCellAnalysis\FailureAnalysisController@getdetailData');
});

//lijian CTR信令搜索
Route::get('/ctrSignalingAnalysis', function () {
    return view('complaintHandling.ctrSignalingAnalysis');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'ctrSignalingAnalysis'], function () {
	Route::get('getChartData', 'ComplaintHandling\CtrSignalingAnalysisController@getChartData');
	Route::get('showMessage', 'ComplaintHandling\CtrSignalingAnalysisController@showMessage');
	Route::get('getDataBase', 'ComplaintHandling\CtrSignalingAnalysisController@getDataBase');
	Route::get('getChartData_filter', 'ComplaintHandling\CtrSignalingAnalysisController@getChartDataFilter');
	Route::get('showMessage_filter', 'ComplaintHandling\CtrSignalingAnalysisController@showMessageFilter');
    Route::get('getNodeTable', 'ComplaintHandling\CtrSignalingAnalysisController@getNodeTable');
});

//zhangyongcai ESRVCC分析
Route::get('/CauseValueAnalysis', function () {
    return view('badCellAnalysis.CauseValueAnalysis');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'CauseValueAnalysis'], function () {
	Route::get('getCitys', 'BadCellAnalysis\CauseValueAnalysisController@getCitys');
	Route::post('getCauseValueAnalysisData', 'BadCellAnalysis\CauseValueAnalysisController@getCauseValueAnalysisData');
	Route::get('getTableData', 'BadCellAnalysis\CauseValueAnalysisController@getTableData');
	Route::post('getChartData', 'BadCellAnalysis\CauseValueAnalysisController@getChartData');
	Route::post('getDrillDownChartData', 'BadCellAnalysis\CauseValueAnalysisController@getDrillDownChartData');
	Route::get('getdetailDataHeader', 'BadCellAnalysis\CauseValueAnalysisController@getdetailDataHeader');
	Route::get('getdetailData', 'BadCellAnalysis\CauseValueAnalysisController@getdetailData');
	Route::post('exportFile', 'BadCellAnalysis\CauseValueAnalysisController@exportFile');
});

//xuyang 无切换邻区分析
Route::get('/relationNonHandover', function () {
    return view('networkOptimization.relationNonHandover');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'relationNonHandover'], function () {
	Route::get('getCitys', 'NetworkOptimization\RelationNonHandoverController@getCitys');
	Route::get('getDataHeader', 'NetworkOptimization\RelationNonHandoverController@getDataHeader');
	Route::get('getTableData', 'NetworkOptimization\RelationNonHandoverController@getTableData');
	Route::post('getAllTableData', 'NetworkOptimization\RelationNonHandoverController@getAllTableData');
	Route::get('allDate', 'NetworkOptimization\RelationNonHandoverController@getAllDate');
});

//xuyang 切换差邻区分析
Route::get('/relationBadHandover', function () {
    return view('networkOptimization.relationBadHandover');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'relationBadHandover'], function () {
	Route::post('getCitys', 'NetworkOptimization\RelationBadHandoverController@getCitys');
	Route::post('getDataHeader', 'NetworkOptimization\RelationBadHandoverController@getDataHeader');
	Route::post('getTableData', 'NetworkOptimization\RelationBadHandoverController@getTableData');
	Route::post('getAllTableData', 'NetworkOptimization\RelationBadHandoverController@getAllTableData');
	Route::post('allDate', 'NetworkOptimization\RelationBadHandoverController@getAllDate');
});

//lijian 弱覆盖概览
Route::get('/weakCoverRatio', function () {
    return view('network.weakCoverRatio');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'weakCoverRatio'], function () {
	Route::get('startTime', 'WeakCoverRatioController@startTime');
	Route::get('SearchWeakCoverRatio', 'WeakCoverRatioController@searchWeakCoverRatio');
});

//haile 弱覆盖点图
Route::get('/weakCover', function () {
    return view('network.weakCover');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'weakCover'], function () {
	Route::get('getAllCity', 'NetworkOptimization\GSMNeighAnalysisController@getAllCity');
	Route::get('weakCoverDatee', 'WeakCoverController@weakCoverDatee');
	Route::get('interCloudChannel', 'InterCloudController@getChannels');
	Route::get('weakCoverCells', 'WeakCoverController@getCells');
	Route::get('weakCoverCharts', 'WeakCoverController@getCharts');
	Route::post('getOneCell', 'WeakCoverController@getOneCell');
});

//shan 弱覆盖小区
Route::get('/weakCoverRate', function () {
    return view('network.weakCoverRate');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'weakCoverRate'], function () {
	Route::get('weakCoverRateDate', 'WeakCoverRateController@weakCoverRateDate');
	Route::get('getCitys', 'WeakCoverRateController@getCitys');
	Route::get('getMroWeakCoverageDataHeader', 'WeakCoverRateController@getMroWeakCoverageDataHeader');
	Route::get('getMroWeakCoverageData', 'WeakCoverRateController@getMroWeakCoverageData');
	Route::get('getAllMroWeakCoverageData', 'WeakCoverRateController@getAllMroWeakCoverageData');
});

//lijian 弱覆盖云图
Route::get('/weakCoverCloud', function () {
    return view('network.weakCoverCloud');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'weakCoverCloud'], function () {
	Route::get('interCloudChannel', 'InterCloudController@getChannels');
	Route::get('weakCoverCloudCells', 'InterCloudController@getweakCoverCells');
	Route::get('getAllCity', 'QueryAnalysis\LTEQueryController@getAllCity');
	Route::post('getDateWithData', 'InterCloudController@getDateWithData');
});

//shan 小区RSRP分析
Route::get('/RSRPAnalysis', function () {
    return view('network.RSRPAnalysis');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'RSRPAnalysis'], function () {
	Route::post('getRSRPdate', 'RSRPAnalysisController@getRSRPdate');
	Route::post('RSRPAnalysisdata', 'RSRPAnalysisController@getRSRPAnalysisData');
});

//zhangyongcai 重叠覆盖概览
Route::get('/overlapCoverOverview', function () {
    return view('network.overlapCoverOverview');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'overlapCoverOverview'], function () {
	Route::get('busyTime', 'OverlapCoverOverviewController@getBusyTime');
	Route::get('SearchOverlapCoverOverview', 'OverlapCoverOverviewController@searchOverlapCoverOverview');
});

//zhangyongcai 重叠覆小区
Route::get('/overlapCover', function () {
    return view('network.overlapCover');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'overlapCover'], function () {
	Route::get('overlapCoverDate', 'OverlapCoverController@overlapCoverDate');
	Route::get('overlapCoverGeniusDate', 'OverlapCoverController@overlapCoverGeniusDate');
	Route::get('overlapCoverCity', 'OverlapCoverController@getAllCity');
	Route::get('overlapCoverDataHeader', 'OverlapCoverController@getOverlapCoverDataHeader');
	Route::get('overlapCoverData', 'OverlapCoverController@getOverlapCoverData');
	Route::get('overlapCoverGeniusDataHeader', 'OverlapCoverController@overlapCoverGeniusDataHeader');
	Route::get('overlapCoverGeniusData', 'OverlapCoverController@overlapCoverGeniusData');
	Route::get('allOverlapCoverData', 'OverlapCoverController@getAllOverlapCoverData');
	Route::get('allOverlapCoverGeniusData', 'OverlapCoverController@allOverlapCoverGeniusData');
});

//zhangyongcai 越区覆盖小区
Route::get('/areaCoverage', function () {
    return view('network.areaCoverage');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'areaCoverage'], function () {
	Route::get('areaCoverageDate', 'AreaCoverageController@areaCoverageDate');
	Route::get('areaCoverageCity', 'AreaCoverageController@getAllCity');
	Route::get('areaCoverageDataHeader', 'AreaCoverageController@getAreaCoverageDataHeader');
	Route::get('areaCoverageData', 'AreaCoverageController@getAreaCoverageData');
	Route::get('allAreaCoverageData', 'AreaCoverageController@getAllAreaCoverageData');
});

//重叠覆盖点图
Route::get('/overlapCoverPoint', function () {
    return view('network.overlapCoverPoint');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'overlapCoverPoint'], function () {
	Route::get('getAllCity', 'NetworkOptimization\GSMNeighAnalysisController@getAllCity');
	Route::get('overlapCoverPointDate', 'WeakCoverController@overlapCoverPointDate');
	Route::get('interCloudChannel', 'InterCloudController@getChannels');
	Route::get('overlapCoverPointCells', 'WeakCoverController@getOverlapCoverPointCells');
	Route::get('weakCoverCharts', 'WeakCoverController@getCharts');
	Route::post('getCell', 'WeakCoverController@getCell');
});

//xuyang 重叠覆盖受主分析
Route::get('/overlappingAcceptorAnalysis', function () {
    return view('network.overlappingAcceptorAnalysis');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'overlappingAcceptorAnalysis'], function () {
	Route::get('getCitys', 'OverlappingDonorAnalysisController@getCitys');
	Route::post('getData', 'OverlappingAcceptorAnalysisController@getData');
	Route::post('getDetailData', 'OverlappingAcceptorAnalysisController@getDetailData');
	Route::post('getDataGroupByDate', 'OverlappingAcceptorAnalysisController@getDataGroupByDate');
    Route::get('switchSite', 'SwitchController@getSwitchSite');
    Route::post('exportData', 'OverlappingAcceptorAnalysisController@exportData');
});

//xuyang 重叠覆盖施主分析
Route::get('/overlappingDonorAnalysis', function () {
    return view('network.overlappingDonorAnalysis');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'overlappingDonorAnalysis'], function () {
	Route::get('getCitys', 'OverlappingDonorAnalysisController@getCitys');
	Route::post('getData', 'OverlappingDonorAnalysisController@getData');
	Route::post('getDetailData', 'OverlappingDonorAnalysisController@getDetailData');
	Route::post('getDataGroupByDate', 'OverlappingDonorAnalysisController@getDataGroupByDate');
    Route::get('switchSite', 'SwitchController@getSwitchSite');
    Route::post('exportData', 'OverlappingDonorAnalysisController@exportData');
});

//zhangyongcai 干扰概览
Route::get('/interCoverRatio', function () {
    return view('network.interCoverRatio');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'interCoverRatio'], function () {
	Route::get('interTime', 'InterCoverRatioController@getInterTime');
	Route::get('SearchInterCoverRatio', 'InterCoverRatioController@searchInterCoverRatio');
});

// 干扰云图
Route::get('/interCloud', function () {
    return view('network.interCloud');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'interCloud'], function () {
	Route::get('interfepointDate', 'InterCloudController@interfepointDate');
	Route::get('interCloudChannel', 'InterCloudController@getChannels');
	Route::get('interCloudCells', 'InterCloudController@getCells');
	Route::get('getAllCity', 'QueryAnalysis\LTEQueryController@getAllCity');
});

//lijian 干扰点图
Route::get('/interPointCloud', function () {
    return view('network.interPointCloud');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'interPointCloud'], function () {
	Route::get('interfepointDate', 'InterCloudController@interfepointDate');
	Route::get('interCloudChannel', 'InterCloudController@getChannels');
	Route::get('getAllCity', 'QueryAnalysis\LTEQueryController@getAllCity');
	Route::get('interPointCloudCells', 'InterCloudController@getCells');
	Route::post('getCell', 'InterCloudController@getCell');
});

//zhangyongcai 小区PRB分析
Route::get('/cellPRBAnalysis', function () {
    return view('network.cellPRBAnalysis');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'cellPRBAnalysis'], function () {
	Route::post('getPRBAnalysisData', 'CellPRBAnalysisController@getPRBAnalysisData');
	Route::get('getPRBTime', 'CellPRBAnalysisController@getPRBTime');
});

//xuyang 实时干扰
Route::get('/RealTimeInterference', function () {
    return view('network.RealTimeInterference');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'RealTimeInterference'], function () {
	Route::post('getDateTime', 'RealTimeInterferenceController@getDateTime');
	Route::post('getRealTimeData', 'RealTimeInterferenceController@getRealTimeData');
	Route::post('getAllCity', 'RealTimeInterferenceController@getAllCity');
});

//xuyang RRC用户数云图-
Route::get('/RRCUserCloud', function () {
    return view('network.RRCUserCloud');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'RRCUserCloud'], function () {
	Route::get('getCitys', 'SystemManage\DownloadManageController@getCitys');
	Route::post('getData', 'IndexGeographicOverviewController@getData');
	Route::post('getCell', 'IndexGeographicOverviewController@getCell');
});

// 下行业务量云图
Route::get('/downlinkTrafficCloud', function () {
    return view('network.downlinkTrafficCloud');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'downlinkTrafficCloud'], function () {
	Route::get('getCitys', 'SystemManage\DownloadManageController@getCitys');
	Route::post('getData', 'IndexGeographicOverviewController@getData');
	Route::post('getCell', 'IndexGeographicOverviewController@getCell');
});

//lijian 补2G邻区分析
Route::get('/GSMNeighborAnalysis', function () {
    return view('networkOptimization.NeighAnalysis');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'GSMNeighborAnalysis'], function () {
	Route::get('getAllCity', 'NetworkOptimization\GSMNeighAnalysisController@getAllCity');
	Route::get('getDate', 'NetworkOptimization\GSMNeighAnalysisController@getfdfd');
	Route::get('GSMNeighAnalysis', 'NetworkOptimization\GSMNeighAnalysisController@getGSMNeighData');
	Route::get('GSMNeighAnalysisSplit', 'NetworkOptimization\GSMNeighAnalysisController@getGSMNeighDataSplit');
	Route::get('GSMNeighAnalysisAll', 'NetworkOptimization\GSMNeighAnalysisController@getGSMNeighDataAll');
	Route::get('getCdrServeNeighDataHeader', 'NetworkOptimization\CDRNeighAnalysisController@getCdrServeNeighDataHeader');
	Route::get('getCdrServeNeighData', 'NetworkOptimization\CDRNeighAnalysisController@getCdrServeNeighData');
	Route::get('getAllCdrServeNeighData', 'NetworkOptimization\CDRNeighAnalysisController@getAllCdrServeNeighData');
	Route::post('uploadFile','FileSystem\FileController@uploadFile');
	Route::post('getMREFileContent', 'NetworkOptimization\GSMNeighAnalysisController@getMREFileContent');
	Route::post('exportWhiteList', 'NetworkOptimization\GSMNeighAnalysisController@exportWhiteList');
    //3G
    Route::get('GSMNeighAnalysis_3G', 'NetworkOptimization\GSMNeighAnalysisController@getGSMNeighData_3G');
    Route::get('GSMNeighAnalysisSplit_3G', 'NetworkOptimization\GSMNeighAnalysisController@getGSMNeighDataSplit_3G');
    Route::get('GSMNeighAnalysisAll_3G', 'NetworkOptimization\GSMNeighAnalysisController@getGSMNeighDataAll_3G');
});

// 补4G异频邻区分析
Route::get('/LTENeighborAnalysis', function () {
    return view('networkOptimization.LTENeighAnalysis');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'LTENeighborAnalysis'], function () {
	Route::get('getLTENeighborAnalysisDate', 'NetworkOptimization\GSMNeighAnalysisController@getLTENeighborAnalysisDate');
	Route::get('getAllCity', 'NetworkOptimization\GSMNeighAnalysisController@getAllCity');
	Route::get('LTENeighAnalysis', 'NetworkOptimization\GSMNeighAnalysisController@getLTENeighData');
	Route::get('LTENeighAnalysisSplit', 'NetworkOptimization\GSMNeighAnalysisController@getLTENeighDataSplit');
	Route::get('GSMNeighAnalysisLteAll', 'NetworkOptimization\GSMNeighAnalysisController@getGSMNeighDataLteAll');
	Route::post('uploadFile','FileSystem\FileController@uploadFile');
	Route::post('getMREFileContent', 'NetworkOptimization\GSMNeighAnalysisController@getMREFileContent');
	Route::post('exportWhiteList', 'NetworkOptimization\GSMNeighAnalysisController@exportWhiteList');
});

// 补4G同频补邻区分析
Route::get('/MROServeNeighAnalysis', function () {
    return view('networkOptimization.MROServeNeighAnalysis');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'MROServeNeighAnalysis'], function () {
	Route::get('getDate', 'NetworkOptimization\MRONeighAnalysisController@getfdfdb');
	Route::get('getAllCity', 'NetworkOptimization\MRONeighAnalysisController@getAllCity');
	Route::get('getMroServeNeighDataHeader', 'NetworkOptimization\MRONeighAnalysisController@getMroServeNeighDataHeader');
	Route::get('getMroServeNeighData', 'NetworkOptimization\MRONeighAnalysisController@getMroServeNeighData');
	Route::get('getAllMroServeNeighData', 'NetworkOptimization\MRONeighAnalysisController@getAllMroServeNeighData');
	Route::get('getMreServeNeighDataHeader', 'NetworkOptimization\MRENeighAnalysisController@getMreServeNeighDataHeader');
	Route::get('getMreServeNeighData', 'NetworkOptimization\MRENeighAnalysisController@getMreServeNeighData');
	Route::get('getAllMreServeNeighData', 'NetworkOptimization\MRENeighAnalysisController@getAllMreServeNeighData');
	Route::post('uploadFile','FileSystem\FileController@uploadFile');
	Route::post('getMREFileContent', 'NetworkOptimization\GSMNeighAnalysisController@getMREFileContent');
	Route::post('exportWhiteList', 'NetworkOptimization\GSMNeighAnalysisController@exportWhiteList');
});

//zhujiaojiao A2门限分析
Route::get('/A2ThresholdAnalysis', function () {
    return view('networkOptimization.A2ThresholdAnalysis');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'A2ThresholdAnalysis'], function () {
	Route::get('getDate', 'NetworkOptimization\A2ThresholdAnalysisController@getfdfde');
	Route::get('getAllCity', 'NetworkOptimization\A2ThresholdAnalysisController@getAllCity');
	Route::get('getMreA2ThresholdDataHeader', 'NetworkOptimization\A2ThresholdAnalysisController@getMreA2ThresholdDataHeader');
	Route::get('getMreA2ThresholdData', 'NetworkOptimization\A2ThresholdAnalysisController@getMreA2ThresholdData');
	Route::get('getAllMreA2ThresholdData', 'NetworkOptimization\A2ThresholdAnalysisController@getAllMreA2ThresholdData');
});

//A5门限分析
Route::get('/A5ThresholdAnalysis', function () {
    return view('networkOptimization.A5ThresholdAnalysis');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'A5ThresholdAnalysis'], function () {
	Route::post('getDate', 'NetworkOptimization\A5ThresholdAnalysisController@getfdfdf');
	Route::post('getAllCity', 'NetworkOptimization\A5ThresholdAnalysisController@getAllCity');
	Route::post('getMreA5ThresholdDataHeader', 'NetworkOptimization\A5ThresholdAnalysisController@getMreA5ThresholdDataHeader');
	Route::post('getMreA5ThresholdData', 'NetworkOptimization\A5ThresholdAnalysisController@getMreA5ThresholdData');
	Route::post('getAllMreA5ThresholdData', 'NetworkOptimization\A5ThresholdAnalysisController@getAllMreA5ThresholdData');
});

//zhujiaojiao 信令分析-信令诊断
Route::get('/signalingDiagnose', function () {
    return view('complaintHandling.signalingDiagnose');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'signalingDiagnose', 'namespace' => 'ComplaintHandling'], function () {
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

//xuyang CTR信令回溯
Route::get('/xinlinghuisu', function () {
    return view('complaintHandling.xinlinghuisu1');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'xinlinghuisu'], function () {
	Route::post('getEventData', 'ComplaintHandling\XinlinghuisuController@getEventData');
	Route::post('showMessage', 'ComplaintHandling\XinlinghuisuController@showMessage');
	Route::post('getAllEventData', 'ComplaintHandling\XinlinghuisuController@getAllEventData');
	Route::post('exportCSV', 'ComplaintHandling\XinlinghuisuController@exportCSV');
	Route::post('getDataGroupByDate', 'ComplaintHandling\XinlinghuisuController@getDataGroupByDate');
	Route::get('getCityDate', 'ComplaintHandling\XinlinghuisuController@getCityDate');
});

//xuyang NAS信令回溯
Route::get('/NASSignalingBacktrack', function () {
    return view('complaintHandling.NASSignalingBacktrack');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'NASSignalingBacktrack'], function () {
	Route::post('getEventData', 'ComplaintHandling\NASSignalingBacktrackController@getEventData');
	Route::post('showMessage', 'ComplaintHandling\NASSignalingBacktrackController@showMessage');
	Route::post('getAllEventData', 'ComplaintHandling\NASSignalingBacktrackController@getAllEventData');
	Route::post('exportCSV', 'ComplaintHandling\NASSignalingBacktrackController@exportCSV');
	Route::post('getDataGroupByDate', 'ComplaintHandling\NASSignalingBacktrackController@getDataGroupByDate');
	Route::get('getCityDate', 'ComplaintHandling\NASSignalingBacktrackController@getCityDate');
});

//xuyang 终端查询
Route::get('/terminalQuery', function () {
    return view('userAnalysis.terminalQuery');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'terminalQuery'], function () {
	Route::get('getCitys', 'UserAnalysis\TerminalQueryController@getCitys');
	Route::get('getUserInfoHead', 'UserAnalysis\TerminalQueryController@getUserInfoHead');
	Route::get('getUserInfoData', 'UserAnalysis\TerminalQueryController@getUserInfoData');
});

//xuyang 市场分析
Route::get('/marketAnalysis', function () {
    return view('userAnalysis.marketAnalysis');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'marketAnalysis'], function () {
	Route::get('getCitys', 'UserAnalysis\MarketAnalysisController@getCitys');
	Route::get('getBrandData', 'UserAnalysis\MarketAnalysisController@getBrandData');
	Route::get('getModeData', 'UserAnalysis\MarketAnalysisController@getModeData');
	Route::get('getAllBrandData', 'UserAnalysis\MarketAnalysisController@getAllBrandData');
	Route::get('getAllModeData', 'UserAnalysis\MarketAnalysisController@getAllModeData');
	Route::get('getBrandChartData', 'UserAnalysis\MarketAnalysisController@getBrandChartData');
	Route::get('getModeChartData', 'UserAnalysis\MarketAnalysisController@getModeChartData');
});

//xuyang 能力分析
Route::get('/abilityAnalysis', function () {
    return view('userAnalysis.abilityAnalysis');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'abilityAnalysis'], function () {
	Route::get('bandEutraData', 'UserAnalysis\AbilityAnalysisController@getBandEutraData');
	Route::get('FGIData', 'UserAnalysis\AbilityAnalysisController@getFGIData');
	Route::get('getCitys', 'UserAnalysis\AbilityAnalysisController@getCitys');
	Route::get('getTableData', 'UserAnalysis\AbilityAnalysisController@getTableData');
	Route::get('getChartData', 'UserAnalysis\AbilityAnalysisController@getChartData');
	Route::get('bandEutraChart', 'UserAnalysis\AbilityAnalysisController@getBandEutraChartData');
	Route::get('FGIChart', 'UserAnalysis\AbilityAnalysisController@getFGIChartData');
    Route::get('TDDChart','UserAnalysis\AbilityAnalysisController@getTddChartData');
    Route::get('getTime', 'UserAnalysis\AbilityAnalysisController@getTime');
});

//xuyang 轨迹查询
Route::get('/trailQuery', function () {
    return view('userAnalysis.trailQuery');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'trailQuery'], function () {
	Route::get('getCitys', 'UserAnalysis\TrailQueryController@getCitys');
	Route::post('getTrailData', 'UserAnalysis\TrailQueryController@getTrailData');
	Route::post('getDataGroupByDate', 'UserAnalysis\TrailQueryController@getDataGroupByDate');
});

//xuyang 定位测距
Route::get('/locationAndRanging', function () {
    return view('systemManage.locationAndRanging');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'locationAndRanging'], function () {
	Route::post('getCoordinateByCell', 'SystemManage\LocationAndRangingController@getCoordinateByCell');
});

// 邻区定义分析
Route::get('/switchdefine', function () {
    return view('network.switchDefine');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'switchdefine'], function () {
	Route::get('switchSite', 'SwitchController@getSwitchSite');
	Route::get('switchDataDefine', 'SwitchController@getSwitchDataDefine');
	Route::get('switchDefineDetail', 'SwitchController@getSwitchDefineDetail');
});

// 邻区切出分析
Route::get('/switch', function () {
    return view('network.switch');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'switch'], function () {
	Route::get('getDate', 'SwitchController@getfdfdh');
	Route::get('switchOutTable', 'SwitchController@getSwitchOutTable');
	Route::get('switchSite', 'SwitchController@getSwitchSite');
	Route::get('RRCusers', 'SwitchController@getRRCusers');
	Route::get('wireLessLost', 'SwitchController@getWireLessLost');
	Route::get('PUSCHInterfere', 'SwitchController@getPUSCHInterfere');
	Route::get('switchData', 'SwitchController@getSwitchData');
	Route::get('switchDetail', 'SwitchController@getSwitchDetail');
	Route::get('handoverin', 'SwitchController@getHandOverIn');
    Route::get('handOverInDetail', 'SwitchController@getHandOverInDetail');
    Route::post('exportSwitchData', 'SwitchController@exportSwitchData');
});

// 邻区切入分析
Route::get('/switchIn', function () {
    return view('network.switchIn');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'switchIn'], function () {
	Route::get('getDate', 'SwitchController@getfdfdh');
	Route::get('switchOutTableIn', 'SwitchController@getSwitchOutTableIn');
	Route::get('switchSite', 'SwitchController@getSwitchSite');
	Route::get('switchData', 'SwitchController@getSwitchData');
	Route::get('switchDetail', 'SwitchController@getSwitchDetail');
	Route::get('PUSCHInterferein', 'SwitchController@getPUSCHInterferein');
	Route::get('handoverSuccin', 'SwitchController@getHandoverSuccin');
	Route::get('RRCusersin', 'SwitchController@getRRCusersin');
	Route::get('wireLessLostin', 'SwitchController@getWireLessLostin');
	Route::get('handoverin', 'SwitchController@getHandOverIn');
    Route::get('handOverInDetail', 'SwitchController@getHandOverInDetail');
    Route::post('exportSwitchInData', 'SwitchController@exportSwitchInData');
});

//xuyang 站点管理
Route::get('/siteManage', function () {
    return view('systemManage.siteManage');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'siteManage'], function () {
	Route::post('TreeQuery', 'SystemManage\SiteController@treeQuery');
	Route::get('QuerySite4G', 'SystemManage\SiteController@querySite4G');
	Route::get('QuerySite2G', 'SystemManage\SiteController@querySite2G');
	Route::post('getFileContent', 'SystemManage\SiteController@getFileContent');
	Route::get('downloadFile', 'SystemManage\SiteController@downloadFile');
	Route::get('downloadTemplateFile', 'SystemManage\SiteController@downloadTemplateFile');
    Route::post('uploadFile','FileSystem\FileController@uploadFile');
    Route::get('QueryIP', 'SystemManage\SiteController@QueryIP');
    Route::post('uploadIPFile', 'SystemManage\SiteController@uploadIPFile');
    Route::get('QueryMajorActivities', 'SystemManage\SiteController@QueryMajorActivities');
    Route::get('QueryMajorActivities_2G','SystemManage\SiteController@QueryMajorActivities_2G');
    Route::post('getMAFileContent', 'SystemManage\SiteController@getMAFileContent');
    Route::post('majorActivities_uploadFile','FileSystem\FileController@majorActivities_uploadFile');
    Route::post('majorActivities_export','SystemManage\SiteController@majorActivities_export');
    Route::post('majorActivities_2G_export','SystemManage\SiteController@majorActivities_2G_export');
    Route::post('insertdata','SystemManage\SiteController@insertdata');
    Route::get('QuerySiteOther4G','SystemManage\SiteController@querySiteOther4G');
    Route::get('QuerySite3G', 'SystemManage\SiteController@querySite3G');

    Route::post('getNewSiteLteField','SystemManage\SiteController@getNewSiteLteField');
    Route::post('getNewSiteLte','SystemManage\SiteController@getNewSiteLte');
    Route::post('deleteNewSiteLte','SystemManage\SiteController@deleteNewSiteLte');
    Route::post('exportNewSiteLte','SystemManage\SiteController@exportNewSiteLte');

    Route::post('openIpListFile','SystemManage\SiteController@openIpListFile');
    Route::post('saveIpListFile','SystemManage\SiteController@saveIpListFile');
    Route::post('getIpListFileContent','SystemManage\SiteController@getIpListFileContent');
    Route::post('newUploadFile','FileSystem\FileController@newUploadFile');
    Route::post('exportIpListFile','SystemManage\SiteController@exportIpListFile');

    //异常基站站点数据 xuyang
    Route::post('getAbnormalStationCounts','SystemManage\SiteController@getAbnormalStationCounts');
    Route::post('exportAbnormalStation','SystemManage\SiteController@exportAbnormalStation');
});
//zhangguoli 告警管理
Route::get('/alarmManage', function () {
    return view('systemManage.alarmManage');
})->middleware('auth');

Route::group(['middleware'=>'auth','prefix'=>'alarmManage'],function(){
    Route::get('getAlarm','SystemManage\AlarmController@getAlarm');
    Route::get('downloadFile','SystemManage\AlarmController@downloadFile');
    Route::post('uploadFile','SystemManage\AlarmController@uploadFile');

});
//xuyang 入库管理
Route::get('/storageManage', function () {
    return view('systemManage.storageManage');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'storageManage'], function () {
	Route::get('taskQuery', 'SystemManage\StorageController@taskQuery');
	Route::get('getTaskTraceDir', 'SystemManage\StorageController@getTaskTraceDir');
	Route::post('addTask', 'SystemManage\StorageController@addTask');
	Route::get('deleteTask', 'SystemManage\StorageController@deleteTask');
	Route::get('monitor', 'SystemManage\StorageController@monitor');
	Route::get('runTask', 'SystemManage\StorageController@runTask');
	Route::get('stopTask', 'SystemManage\StorageController@stopTask');
	// Route::get('exportFile', 'SystemManage\StorageController@exportFile');
	// Route::post('uploadFile', 'SystemManage\StorageController@uploadFile');
});

//xuyang 参数管理
Route::get('/paramsManage', function () {
    return view('systemManage.paramsManage');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'paramsManage'], function () {
	Route::get('getBaselineTreeData', 'SystemManage\ParamsController@getBaselineTreeData');
    Route::get('searchBaselineTreeData', 'SystemManage\ParamsController@searchBaselineTreeData');
    Route::get('getBaselineTableData', 'SystemManage\ParamsController@getBaselineTableData');
    Route::get('downloadFile', 'SystemManage\ParamsController@downloadFile');
    Route::post('uploadFile', 'SystemManage\ParamsController@uploadFile');
    Route::post('addOrUpdateTemplate', 'SystemManage\ParamsController@addOrUpdateTemplate');
    Route::post('getTemplate', 'SystemManage\ParamsController@getTemplate');
    Route::get('deleteMode', 'SystemManage\ParamsController@deleteMode');
    Route::post('getWhiteList', 'SystemManage\ParamsController@getWhiteList');
    Route::post('exportWhiteList', 'SystemManage\ParamsController@exportWhiteList');
    Route::post('uploadWhiteListFile', 'FileSystem\FileController@uploadFile');
    Route::post('getFileContent', 'SystemManage\ParamsController@getFileContent');
	//xuyang 参数管理-Baseline任务管理
	Route::get('getDate', 'SystemManage\ParamsController@getDate');
	Route::get('getBaselineTaskTable', 'SystemManage\ParamsController@getBaselineTaskTable');
	Route::get('addTask', 'SystemManage\ParamsController@addTask');
	Route::get('deleteTask', 'SystemManage\ParamsController@deleteTask');
    Route::post('runTaskCheck', 'SystemManage\ParamsController@runTaskCheck');
	Route::get('runTask', 'SystemManage\ParamsController@runTask');
	Route::get('stopTask', 'SystemManage\ParamsController@stopTask');
    //zhujj 参数管理-baseline任务管理更新运行日志
    Route::post('updateTaskLog', 'SystemManage\ParamsController@updateTaskLog');
    Route::post('getCitySelect', 'SystemManage\ParamsController@getCitySelect');
});

//xuyang 在线数据管理
Route::get('/dataSourceManage', function () {
    return view('systemManage.dataSourceManage');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'dataSourceManage'], function () {
	Route::post('getNode', 'SystemManage\DataSourceController@getNode');
	Route::get('getFileName', 'SystemManage\DataSourceController@getFileName');
	Route::post('ctrTreeItems', 'SystemManage\DataSourceController@ctrTreeItems');
	//xuyang 数据源管理 在线入库
	Route::post('onlineStorage', 'SystemManage\DataSourceController@onlineStorage');
	//xuyang 数据源管理logType
	Route::get('getLogType', 'SystemManage\DataSourceController@getLogType');
	//zhangyongcai 在线数据管理
	Route::post('storage', 'SystemManage\DataSourceController@storage');
	// Route::post('uploadFile', 'SystemManage\DataSourceController@uploadFile');
    Route::post('uploadFile', 'FileSystem\FileController@Query_uploadFile');
	Route::post('addTask', 'SystemManage\LocalDataManageController@addTask');
	Route::get('runTask', 'SystemManage\StorageController@runTask');
	Route::get('monitor', 'SystemManage\StorageController@monitor');
    Route::post('deleteAutoDir', 'SystemManage\DataSourceController@deleteAutoDir');
    Route::post('scpfiles', 'SystemManage\DataSourceController@scpFiles');
});

//xuyang 本地数据管理
Route::get('/LocalDataManage', function () {
    return view('systemManage.LocalDataManage');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'LocalDataManage'], function () {
    Route::post('uploadFile', 'SystemManage\LocalDataManageController@uploadFile');
    // Route::post('uploadFile', 'FileSystem\FileController@Query_uploadFile');
    // Route::post('addTask', 'SystemManage\LocalDataManageController@addTask');
    // Route::get('runTask', 'SystemManage\StorageController@runTask');
    // Route::get('monitor', 'SystemManage\StorageController@monitor');
    Route::get('getDir', 'SystemManage\LocalDataManageController@getDir');
    Route::post('addDir', 'SystemManage\LocalDataManageController@addDir');
    Route::post('deleteDir', 'SystemManage\LocalDataManageController@deleteDir');
    Route::post('getFileByDir', 'SystemManage\LocalDataManageController@getFileByDir');
    Route::post('analysisLog', 'SystemManage\LocalDataManageController@analysisLog');
    Route::post('getLogByDir', 'SystemManage\LocalDataManageController@getLogByDir');
});

//xuyang 账号管理
Route::get('/userManage', function () {
    return view('systemManage.userManage');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'userManage'], function () {
    Route::get('templateQuery', 'SystemManage\UserController@templateQuery');
    Route::get('deleteUser', 'SystemManage\UserController@deleteUser');
    Route::get('updateUser', 'SystemManage\UserController@updateUser');
    Route::get('getType', 'SystemManage\UserController@getType');
    Route::get('treeQuery', 'SystemManage\UserController@treeQuery');
    Route::post('updateUserType', 'SystemManage\UserController@updateUserType');
    Route::get('deleteUserType', 'SystemManage\UserController@deleteUserType');
    Route::get('getMenuList', 'SystemManage\UserController@getMenuList');
    Route::post('updatePermission', 'SystemManage\UserController@updatePermission');
});

//xuyang 邮箱管理
Route::get('/emailManage', function () {
    return view('systemManage.emailManage');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'emailManage'], function () {
    Route::post('treeQuery', 'SystemManage\EmailController@treeQuery');
    Route::post('getTableData', 'SystemManage\EmailController@getTableData');
    Route::post('insertDownload', 'SystemManage\EmailController@insertDownload');
    Route::post('deleteDownload', 'SystemManage\EmailController@deleteDownload');
    Route::post('getAllCity', 'SystemManage\EmailController@getAllCity');
    Route::post('updateScope', 'SystemManage\EmailController@updateScope');
    Route::post('getScope', 'SystemManage\EmailController@getScope');
    Route::post('deleteScope', 'SystemManage\EmailController@deleteScope');
    Route::post('getRole', 'SystemManage\EmailController@getRole');
});

//xuyang 直连管理
Route::get('/ENIQManage', function () {
    return view('systemManage.ENIQManage');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'ENIQManage'], function () {
    Route::get('Query4G', 'SystemManage\ENIQController@query4G');
    Route::get('Query2G', 'SystemManage\ENIQController@query2G');
    Route::get('updateENIQ', 'SystemManage\ENIQController@updateENIQ');
    Route::get('deleteENIQ', 'SystemManage\ENIQController@deleteENIQ');
    //xuyang 直连管理alarm部分
    Route::get('Query4GAlarm', 'SystemManage\ENIQController@query4GAlarm');
    Route::get('Query2GAlarm', 'SystemManage\ENIQController@query2GAlarm');
    Route::post('updateAlarm', 'SystemManage\ENIQController@updateAlarm');
    // Route::post('deleteAlarm', 'SystemManage\ENIQController@deleteAlarm');
});

//xuyang 下载管理
Route::get('/downloadManage', function () {
    return view('systemManage.downloadManage');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'downloadManage'], function () {
    Route::get('treeQuery', 'SystemManage\DownloadManageController@treeQuery');
    Route::get('getTableData', 'SystemManage\DownloadManageController@getTableData');
    Route::post('updateDownload', 'SystemManage\DownloadManageController@updateDownload');
    Route::get('deleteDownload', 'SystemManage\DownloadManageController@deleteDownload');
    //xuyang 下载管理-城市
    Route::get('getCitys', 'SystemManage\DownloadManageController@getCitys');
});

//zhangyongcai 存储管理
Route::get('/storeManage',function(){
    return view('systemManage.storeManage');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'storeManage'], function () {
    Route::post('treeQuery', 'SystemManage\StoreManageController@treeQuery');
    Route::get('getCitys', 'SystemManage\StoreManageController@getCitys');
    Route::post('getTableData', 'SystemManage\StoreManageController@getTableData');
    Route::post('updateDownload', 'SystemManage\StoreManageController@updateDownload');
    Route::get('deleteDownload', 'SystemManage\StoreManageController@deleteDownload');
    Route::get('getTypes', 'SystemManage\StoreManageController@getTypes');
    Route::post('openTaskFile', 'SystemManage\TaskController@openTaskFile');
    Route::post('saveTaskFile', 'SystemManage\TaskController@saveTaskFile');
});

//xuyang 通知管理
Route::get('/noticeManage', function () {
    return view('systemManage.noticeManage');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'noticeManage'], function () {
    Route::get('getNotice', 'SystemManage\NoticeController@getNotice');
    Route::get('deleteNotice', 'SystemManage\NoticeController@deleteNotice');
    Route::post('getUserGroupById', 'SystemManage\NoticeController@getUserGroupById');
    // Route::get('getAllNotice', 'SystemManage\NoticeController@getAllNotice');
});

//xuyang 点击管理
Route::get('/accessManage', function () {
    return view('systemManage.accessManage');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'accessManage'], function () {
    Route::post('getAccessData', 'SystemManage\AccessController@getAccessData');
    Route::post('downloadAccessData', 'SystemManage\AccessController@downloadAccessData');
    Route::get('getAllUsers', 'SystemManage\AccessController@getAllUsers');
});

//日活用户-xuyang
Route::get('/activeUser',function(){
    return view('systemManage.activeUser');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'activeUser'], function () {
    Route::post('getAccessData', 'SystemManage\ActiveUserController@getAccessData');
});

//zhangyongcai  bulkcm留痕
Route::get('/bulkcmMark', function () {
    return view('parameterAnalysis.bulkcmMark');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'bulkcmMark', 'namespace' => 'ParameterAnalysis'], function () {
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
Route::group(['middleware' => 'auth','prefix' => 'kgetpartMark', 'namespace' => 'ParameterAnalysis'], function () {
    Route::get('getParamTasks', 'KgetpartMarkController@getParamTasks');
    Route::get('getAllCity', 'KgetpartMarkController@getAllCity');
    Route::get('getKgetpartMarkDataHeader', 'KgetpartMarkController@getKgetpartMarkDataHeader');
    Route::get('getKgetpartMarkData', 'KgetpartMarkController@getKgetpartMarkData');
    Route::get('getAllKgetpartMarkData', 'KgetpartMarkController@getAllKgetpartMarkData');
});

//zjj特色-参数对比
Route::get('/paramCompare',function(){
    return view('SpecialFunction.paramCompare');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix'=>'paramCompare','namespace'=>'SpecialFunction'],function(){
    Route::get('getAllCity','ParamCompareController@getAllCity');
    Route::post('getItems','ParamCompareController@getItems');
    Route::post('getItemsAdd','ParamCompareController@getItemsAdd');
    Route::post('getItemsLess','ParamCompareController@getItemsLess');
    Route::post('exportFile','ParamCompareController@exportFile');
    Route::get('getParamTasks', 'ParamCompareController@getParamTasks');
    Route::post('getParamData', 'ParamCompareController@getParamData');
    Route::post('getCompareResult', 'ParamCompareController@getCompareResult');
});

//操作查询
Route::get('/operationQuery',function(){
    return view('SpecialFunction.operationQuery');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'operationQuery'], function () {
    Route::get('getCitys', 'SpecialFunction\OperationQueryController@getCitys');
    Route::get('paramData', 'SpecialFunction\OperationQueryController@getparamData');
    Route::get('operationData', 'SpecialFunction\OperationQueryController@getOperationData');
    Route::get('getActionType', 'SpecialFunction\OperationQueryController@getActionType');
    Route::get('getActionSource', 'SpecialFunction\OperationQueryController@getActionSource');
    // Route::post('uploadFile', 'SpecialFunction\OperationQueryController@uploadFile');
    Route::post('uploadFile', 'FileSystem\FileController@Query_uploadFile');
});

//zjj 翻频
Route::get('/modifyFrequency4g',function(){
    return view('SpecialFunction.modifyFrequency4g');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix'=>'modifyFrequency4g','namespace'=>'SpecialFunction'],function(){
    Route::get('getTasks','ModifyFrequency4gController@getTasks');
    Route::post('getFileContent','ModifyFrequency4gController@getFileContent');
    Route::post('downloadTemplateFile','ModifyFrequency4gController@downloadTemplateFile');
    Route::get('TreeQuery', 'ModifyFrequency4gController@getCityTree');
    Route::post('runProcedure','ModifyFrequency4gController@runProcedure');
    Route::post('getTableField','ModifyFrequency4gController@getTableField');
    Route::post('getItems','ModifyFrequency4gController@getItems');
    Route::post('downloadFile','ModifyFrequency4gController@downloadFile');
});

//任务管理-zhujj
Route::get('/taskManage',function(){
    return view('systemManage.taskManage');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix'=>'taskManage','namespace'=>'SystemManage'],function(){
    Route::post('openTaskFile', 'TaskController@openTaskFile');
    Route::post('saveTaskFile', 'TaskController@saveTaskFile');
});

//xuyang 使用帮助
Route::get('/downloadCourse', function () {
    return view('systemManage.downloadCourse');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'downloadCourse'], function () {
    Route::get('getVideo', 'SystemManage\DownloadCourseController@getVideo');
    Route::get('getDoc', 'SystemManage\DownloadCourseController@getDoc');
});

//xuyang 使用反馈
Route::get('/feedBack', function () {
    return view('systemManage.feedBack');
})->middleware('auth');

//xuyang 用户设置
Route::get('/UserSetting', function () {
    return view('systemManage.UserSetting');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'UserSetting'], function () {
    Route::post('updateUser', 'SystemManage\UserSettingController@updateUser');
    Route::post('updatePassword', 'SystemManage\UserSettingController@updatePassword');
});

//查看全部通知
Route::get('/readAllNotice', function () {
    return view('systemManage.readAllNotice');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'readAllNotice'], function () {
    Route::get('getAllNotice', 'SystemManage\NoticeController@getAllNotice');
});

//zjj 文件系统
Route::group(['middleware' => 'auth','prefix'=>'file','namespace'=>'FileSystem'],function(){
    Route::post('uploadFile','FileController@uploadFile');
});

//lijian
Route::get('/equipmentStatistics2G', function(){
    return view('SpecialFunction.equipmentStatistics2G');
})->middleware('auth');
Route::group(['middleware' => 'auth', 'prefix'=>'equipmentStatistics2G', 'namespace'=>'SpecialFunction'], function(){
    Route::get('getTasks', 'EquipmentStatistics2GController@getTasks');
    Route::get('getSituationDataHeader', 'EquipmentStatistics2GController@getSituationDataHeader');
    Route::get('getSituationData', 'EquipmentStatistics2GController@getSituationData');
    Route::get('getStatisticDataHeader', 'EquipmentStatistics2GController@getStatisticDataHeader');
    Route::get('getStatisticData', 'EquipmentStatistics2GController@getStatisticData');
    Route::get('getAllSituationData', 'EquipmentStatistics2GController@getAllSituationData');
    Route::get('getAllStatisticsData', 'EquipmentStatistics2GController@getAllStatisticsData');
});

//zhangguoli 硬件分析-板卡分析

Route::get('/BoardAnalysis',function(){
    return view('alarmAnalysis.BoardAnalysis');
})->middleware('auth');
 Route::group(['middleware' => 'auth', 'prefix'=>'BoardAnalysis', 'namespace'=>'AlarmAnalysis'], function(){
    Route::post('getAllCity', 'BoardAnalysisController@getAllCity');
    Route::post('getAllSlot', 'BoardAnalysisController@getAllSlot');
    Route::post('exportAllSlot','BoardAnalysisController@exportAllSlot');
    Route::post('getDisappearSlot','BoardAnalysisController@getDisappearSlot');
    Route::post('exportDisappearSlot','BoardAnalysisController@exportDisappearSlot');
    Route::post('getSlotTrendChart','BoardAnalysisController@getSlotTrendChart');
    Route::post('getOneSlotInfo','BoardAnalysisController@getOneSlotInfo');
    Route::post('exportOneSolt','BoardAnalysisController@exportOneSolt');
 });

//zhujj 告警分析-DU板卡分析
Route::get('/slotOnlineTimeAnalysis',function(){
    return view('alarmAnalysis.slotOnlineTimeAnalysis');
})->middleware('auth');
 Route::group(['middleware' => 'auth', 'prefix'=>'slotOnlineTimeAnalysis', 'namespace'=>'AlarmAnalysis'], function(){
    Route::post('getAllCity', 'SlotOnlineTimeAnalysisController@getAllCity');
    Route::post('getAllSlot', 'SlotOnlineTimeAnalysisController@getAllSlot');
    Route::post('exportAllSlot','SlotOnlineTimeAnalysisController@exportAllSlot');
    Route::post('getDisappearSlot','SlotOnlineTimeAnalysisController@getDisappearSlot');
    Route::post('exportDisappearSlot','SlotOnlineTimeAnalysisController@exportDisappearSlot');
    Route::post('getSlotTrendChart','SlotOnlineTimeAnalysisController@getSlotTrendChart');
    Route::post('getOneSlotInfo','SlotOnlineTimeAnalysisController@getOneSlotInfo');
    Route::post('exportOneSolt','SlotOnlineTimeAnalysisController@exportOneSolt');
 });
  //zhujj 特色功能-参数-强干扰小区处理
 Route::get('/strongInterferenceCell', function(){
    return view('SpecialFunction.strongInterferenceCell');
 })->middleware('auth');
 Route::group(['middleware' => 'auth', 'prefix' => 'strongInterferenceCell'], function(){
    Route::post('getTasks','SpecialFunction\StrongInterferenceCellController@getTasks');
    Route::post('uploadFile','FileSystem\FileController@uploadFile');
    Route::post('getFileContent', 'SpecialFunction\StrongInterferenceCellController@getFileContent');
    Route::post('getTableField', 'SpecialFunction\StrongInterferenceCellController@getTableField');
    Route::post('getItems', 'SpecialFunction\StrongInterferenceCellController@getItems');
    Route::post('downloadFile', 'SpecialFunction\StrongInterferenceCellController@downloadFile');
    Route::post('insertCellList', 'SpecialFunction\StrongInterferenceCellController@insertCellList');
 });
 //zhujj 告警分析-RRU板卡分析
Route::get('/rruSlotAnalysis',function(){
    return view('alarmAnalysis.rruSlotAnalysis');
})->middleware('auth');
 Route::group(['middleware' => 'auth', 'prefix'=>'rruSlotAnalysis', 'namespace'=>'AlarmAnalysis'], function(){
    Route::post('getAllCity', 'RruSlotAnalysisController@getAllCity');
    Route::post('getAllSlot', 'RruSlotAnalysisController@getAllSlot');
    Route::post('exportAllSlot','RruSlotAnalysisController@exportAllSlot');
    Route::post('getDisappearSlot','RruSlotAnalysisController@getDisappearSlot');
    Route::post('exportDisappearSlot','RruSlotAnalysisController@exportDisappearSlot');
    Route::post('getSlotTrendChart','RruSlotAnalysisController@getSlotTrendChart');
    Route::post('getOneSlotInfo','RruSlotAnalysisController@getOneSlotInfo');
    Route::post('exportOneSolt','RruSlotAnalysisController@exportOneSolt');
 });

 //xuyang 路测分析-自动路测
Route::get('/autoRoadSurvey',function(){
    return view('network.autoRoadSurvey');
})->middleware('auth');
Route::group(['middleware' => 'auth', 'prefix'=>'autoRoadSurvey'], function(){
    Route::get('getCitys', 'AutoRoadSurveyController@getCitys');
    Route::get('getDate', 'AutoRoadSurveyController@getDate');
    Route::post('getData', 'AutoRoadSurveyController@getData');
    Route::post('getOneCell', 'AutoRoadSurveyController@getOneCell');
});

//xuyang 覆盖分析-覆盖栅格图
Route::get('/overlayRaster',function(){
    return view('network.overlayRaster');
})->middleware('auth');

//ZJJ 日常优化-指标分析-测量指标查询-RSRP_MRO
Route::get('/MRORSRP', function(){
    return view('QueryAnalysis.MRORSRPQuery');
})->middleware('auth');
Route::group(['middleware' => 'auth', 'prefix' => 'MRORSRPQuery'], function () {
    Route::get('getAllCity', 'QueryAnalysis\MRORSRPQueryController@getAllCity');
    Route::get('getDate', 'QueryAnalysis\MRORSRPQueryController@getDate');
    Route::post('getMRORSRPDataField', 'QueryAnalysis\MRORSRPQueryController@getMRORSRPDataField');
    Route::post('getMRORSRPDataSplit', 'QueryAnalysis\MRORSRPQueryController@getMRORSRPDataSplit');
    Route::post('getAllData', 'QueryAnalysis\MRORSRPQueryController@getAllData');
});

//ZJJ 专项研究-硬件分析-新型室分(DOT)站点分析
Route::get('/DOT',function(){
    return view('networkOptimization.DOTQuery');
})->middleware('auth');;
Route::group(['middleware' => 'auth','prefix' => 'DOT'], function(){
    Route::get('getData', 'NetworkOptimization\DOTController@getData');
    Route::get('getCityList', 'NetworkOptimization\DOTController@getCityList');
    Route::get('getDOTDataHeader', 'NetworkOptimization\DOTController@getDOTDataHeader');
    Route::get('getDOTData', 'NetworkOptimization\DOTController@getDOTData');
    Route::get('getAllDOTData', 'NetworkOptimization\DOTController@getAllDOTData');
});

//kget G2参数查询 xuyang
Route::get('/KgetG2', function () {
    return view('parameterAnalysis.KgetG2');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'KgetG2', 'namespace' => 'ParameterAnalysis'], function () {
    Route::post('getParamTasks', 'KgetG2Controller@getParamTasks');
    Route::post('getParamCitys', 'KgetG2Controller@getParamCitys');
    Route::post('getFeatureList', 'KgetG2Controller@getFeatureList');
	Route::post('getAllSubNetwork', 'KgetG2Controller@getAllSubNetwork');
	//mongodb
	Route::post('getParamItems_mongodb', 'KgetG2Controller@getParamItems_mongodb');
	Route::post('exportParamFile_mongodb', 'KgetG2Controller@exportParamFile_mongodb');
});

//ZJJ 专项研究-硬件分析-GSM板卡串号统计
Route::get('/GSMSlot',function(){
    return view('networkOptimization.GSMSlotQuery');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'GSMSlot'], function(){
    Route::get('getData', 'NetworkOptimization\GSMSlotController@getData');
    Route::get('getCityList', 'NetworkOptimization\GSMSlotController@getCityList');
    Route::get('getGSMSlotDataHeader', 'NetworkOptimization\GSMSlotController@getGSMSlotDataHeader');
    Route::get('getGSMSlotData', 'NetworkOptimization\GSMSlotController@getGSMSlotData');
    Route::get('getAllGSMSlotData', 'NetworkOptimization\GSMSlotController@getAllGSMSlotData');
});

//ZJJ 专项研究-硬件分析-GSM板卡串号统计
Route::get('/GSMSlot',function(){
    return view('networkOptimization.GSMSlotQuery');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'GSMSlot'], function(){
    Route::get('getData', 'NetworkOptimization\GSMSlotController@getData');
    Route::get('getCityList', 'NetworkOptimization\GSMSlotController@getCityList');
    Route::get('getGSMSlotDataHeader', 'NetworkOptimization\GSMSlotController@getGSMSlotDataHeader');
    Route::get('getGSMSlotData', 'NetworkOptimization\GSMSlotController@getGSMSlotData');
    Route::get('getAllGSMSlotData', 'NetworkOptimization\GSMSlotController@getAllGSMSlotData');
});

//zhangguoli 冗余数据清除
Route::get('/ReduantData',function(){
    return view('network.reduantData');
    
})->middleware('auth');
Route::group(['middleware'=>'auth','prefix'=>'ReduantData'],function(){
    Route::get('downloadTemplateFile','ReduantDataController@downloadTemplateFile');
    Route::post('uploadFile','ReduantDataController@uploadFile');


});
//zhangguoli WilliamTool对接
Route::get('/WilliamTool',function(){
    return view('network.WilliamTool');
    
})->middleware('auth');
Route::group(['middleware'=>'auth','prefix'=>'WilliamTool'],function(){
    Route::post('getTasks', 'WilliamToolController@getTasks');
    Route::post('getCarrierData', 'WilliamToolController@getCarrierData');
    Route::post('importCarrierData', 'WilliamToolController@importCarrierData');
    Route::post('getNeighborData', 'WilliamToolController@getNeighborData');
    Route::post('importNeighborData', 'WilliamToolController@importNeighborData');

});

//yugaoheng 新站跟踪
Route::get('/newSite', function () {
    return view('SpecialFunction.newSite');
})->middleware('auth');

Route::group(['middleware'=>'auth','prefix'=>'newSite'],function(){
    Route::post('getCitys', 'SpecialFunction\NewSiteController@getCitys');
    Route::post('getTableField', 'SpecialFunction\NewSiteController@getTableField');
    Route::post('getNewSite','SpecialFunction\NewSiteController@getNewSite');
    Route::post('exportNewSite','SpecialFunction\NewSiteController@exportNewSite');
    Route::post('uploadFile','FileSystem\FileController@uploadFile');
    Route::post('getFileContent','SpecialFunction\NewSiteController@getFileContent');
    Route::post('runTask','SpecialFunction\NewSiteController@runTask');
    Route::post('getDate','SpecialFunction\NewSiteController@getDate');
    Route::post('exportAllSearch','SpecialFunction\NewSiteController@exportAllSearch');
});
//yugaoheng 老站跟踪
Route::get('/oldSite', function () {
    return view('SpecialFunction.oldSite');
})->middleware('auth');

Route::group(['middleware'=>'auth','prefix'=>'oldSite'],function(){
    Route::post('getCitys', 'SpecialFunction\OldSiteController@getCitys');
    Route::post('getTableField', 'SpecialFunction\OldSiteController@getTableField');
    Route::post('getOldSite','SpecialFunction\OldSiteController@getOldSite');
    Route::post('exportOldSite','SpecialFunction\OldSiteController@exportOldSite');
    Route::post('uploadFile','FileSystem\FileController@uploadFile');
    Route::post('getFileContent','SpecialFunction\OldSiteController@getFileContent');
    Route::post('runTask','SpecialFunction\OldSiteController@runTask');
    Route::post('getDate','SpecialFunction\OldSiteController@getDate');
    Route::post('exportAllSearch','SpecialFunction\OldSiteController@exportAllSearch');
});

//工参数据分析 xuyang
Route::get('/workingParameterDataAnalysis', function () {
    return view('parameterAnalysis.workingParameterDataAnalysis');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'workingParameter'], function () {
    Route::get('getDate', 'ParameterAnalysis\WorkingParameterDataAnalysisController@getDate');
    Route::post('uploadFile', 'FileSystem\FileController@Query_uploadFile');
    Route::get('getAllCity', 'QueryAnalysis\LTEQueryController@getAllCity');
    Route::get('getTableData', 'ParameterAnalysis\WorkingParameterDataAnalysisController@getTableData');
    Route::post('exportFile', 'ParameterAnalysis\WorkingParameterDataAnalysisController@exportFile');
});
//小区自忙时分析 zhanggl
Route::get('/TopTraffic',function(){
    return view('parameterAnalysis.TopTraffic');
})->middleware('auth');
Route::group(['middleware'=>'auth','prefix'=>'TopTraffic'],function(){
    Route::get('getAllCity', 'QueryAnalysis\LTEQueryController@getAllCity');
    Route::get('getTableData', 'ParameterAnalysis\TopTrafficController@getTableData');
    
});
//帧差异点图 xuyang
Route::get('/frameDifferencePoint', function () {
    return view('network.frameDifferencePoint');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'frameDifferencePoint'], function () {
	Route::get('getDate', 'FrameDifferencePointController@getDate');
	Route::get('getChannel', 'FrameDifferencePointController@getChannels');
	Route::get('getAllCity', 'QueryAnalysis\LTEQueryController@getAllCity');
	Route::get('getCells', 'FrameDifferencePointController@getCells');
	Route::post('getCell', 'FrameDifferencePointController@getCell');
});

//License分析 zjj
Route::get('/licenseAnalysis', function () {
    return view('parameterAnalysis.licenseAnalysis');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'licenseAnalysis', 'namespace' => 'ParameterAnalysis'], function () {
    Route::post('getParamTasks', 'LicenseAnalysisController@getParamTasks');
    Route::post('getItems', 'LicenseAnalysisController@getItems');
    Route::post('getParamCitys', 'LicenseAnalysisController@getParamCitys');
    Route::post('getTableField', 'LicenseAnalysisController@getTableField');
    Route::post('exportFile', 'LicenseAnalysisController@exportFile');
    Route::post('getAllSubNetwork', 'LicenseAnalysisController@getAllSubNetwork');
    Route::post('getLicenseNameList','LicenseAnalysisController@getLicenseNameList');
    Route::post('getLicenseIdList','LicenseAnalysisController@getLicenseIdList');
    Route::post('getStateList','LicenseAnalysisController@getStateList');

});

// RRU硬件能力查询 xuyang
Route::get('/RRUHardwear', function () {
    return view('systemManage.RRUHardwear');
})->middleware('auth');
Route::group(['middleware' => 'auth','prefix' => 'RRUHardwear', 'namespace'=>'SystemManage'], function () {
    Route::post('getDate', 'RRUHardwearController@getDate');
    Route::post('searchData', 'RRUHardwearController@searchData');
    Route::post('downloadFile', 'RRUHardwearController@downloadFile');
    Route::post('getSubnetwork', 'RRUHardwearController@getSubnetwork');
});