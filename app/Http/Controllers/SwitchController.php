<?php

/**
 * SwitchController.php
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Common\MyRedis;
use PDO;
use App\Models\Mongs\SiteLte;
use App\Models\AutoKPI\SysRelation_cell_day;
use App\Models\AutoKPI\SysCoreTemp_cell_day;
use App\Models\G\TempEUtranCellRelation;
use App\Http\Controllers\Utils\FileUtil;

/**
 * 切换分析
 * Class SwitchController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class SwitchController extends MyRedis
{


    /**
     * 获得切换分析视图
     *
     * @return mixed
     */
    public function index()
    {
        return view('network.switch');

    }//end index()


    /**
     * 获得切入分析视图
     *
     * @return mixed
     */
    public function indexIn()
    {
        return view('network.switchIn');

    }//end indexIn()


    /**
     * 获得切换定义视图
     *
     * @return mixed
     */
    public function indexDefine()
    {
        return view('network.switchDefine');

    }//end indexDefine()


    /**
     * 获得所有小区信息
     *
     * @return string
     */
    public function getswitchSite()
    {
        $result = SiteLte::query()->selectRaw('cellName,longitudeBD as longitude,latitudeBD as latitude,dir,band')->get();
        return json_encode($result);

    }//end getswitchSite()


    /**
     * 获得切换小区信息
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    public function getswitchData(Request $request)
    {
        $date   = $request['date'];
        $cell   = $request['cell'];
        $rows = SysRelation_cell_day::query()->selectRaw('id,cell as ServeCell, scell as NeighCell,mlongitude as ServeLongitude,mlatitude as ServeLatitude,mdir as ServeDir,mband as ServeBand,slongitude as NeighLongitude,slatitude as NeighLatitude,sdir as NeighDir,sband as NeighBand,执行切换失败数,准备切换尝试数,切换成功率,scell,mlongitude,mlatitude,mdir,mband,slongitude,slatitude,sdir,sband,执行切换失败数 as failCount,准备切换尝试数 as handoverAttemptCount,准备切换尝试数 as handoverAttemptCount1,切换成功率 as handoverSuccessRatio')
            ->where('day_id', $date)
            ->where('cell', $cell)
            ->whereNotNull('slongitude')
            ->whereNotNull('slatitude')
            ->whereNotNull('sdir')
            ->whereNotNull('sband')
            ->whereNotNull('scell')
            ->get()
            ->toArray();
        return json_encode($rows);

    }//end getswitchData()

    public function exportSwitchData(Request $request) {
        $date   = $request['date'];
        $cell   = $request['cell'];

        $row = SysRelation_cell_day::first()->toArray();
        $columns = [];
        foreach ($row as $key => $value) {
            array_push($columns, $key);
        }
        $rows = SysRelation_cell_day::where('day_id', $date)
                                    ->where('cell', $cell)
                                    ->whereNotNull('slongitude')
                                    ->whereNotNull('slatitude')
                                    ->whereNotNull('sdir')
                                    ->whereNotNull('sband')
                                    ->whereNotNull('scell')
                                    ->get($columns)
                                    ->toArray();
        
        $fileName = "common/files/邻区切出分析_" . $date . "_" . $cell . ".csv";
        $fileUtil = new FileUtil();
        $fileUtil->resultToCSV2(implode(",", $columns), $rows, $fileName);
        $result['fileName'] = $fileName;
        $result['result'] = 'true';
        return $result;
    }
    public function exportSwitchInData(Request $request) {
        $date   = $request['date'];
        $cell   = $request['cell'];

        $row = SysRelation_cell_day::first()->toArray();
        $columns = [];
        foreach ($row as $key => $value) {
            array_push($columns, $key);
        }
        $rows = SysRelation_cell_day::where('day_id', $date)
                                    ->where('scell', $cell)
                                    ->whereNotNull('mlongitude')
                                    ->whereNotNull('mlatitude')
                                    ->whereNotNull('mdir')
                                    ->whereNotNull('mband')
                                    ->whereNotNull('cell')
                                    ->get($columns)
                                    ->toArray();
        
        $fileName = "common/files/邻区切入分析_" . $date . "_" . $cell . ".csv";
        $fileUtil = new FileUtil();
        $fileUtil->resultToCSV2(implode(",", $columns), $rows, $fileName);
        $result['fileName'] = $fileName;
        $result['result'] = 'true';
        return $result;
    }

    /**
     * 获得切换指标数据
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    public function getYellowColor(Request $request)
    {
        // $date   = $request['date'];
        // $cell   = $request['cell'];
        // $sql    = 'select id,cell as ServeCell,scell as NeighCell,mlongitude as ServeLongitude,mlatitude as ServeLatitude,mdir as ServeDir,mband as ServeBand'.',slongitude as NeighLongitude,slatitude as NeighLatitude,sdir as NeighDir,sband as NeighBand,执行切换失败数,准备切换尝试数,切换成功率 from SysRelation_cell_day where day_id = \''.$date.'\' and cell = \''.$cell.'\' and slongitude is not null and slatitude is not null and sdir is not null and sband is not null and scell is not null;';
        // $result = DB::connection('autokpi')->select($sql);
        // return json_encode($result);

    }//end getYellowColor()


    /**
     * 获得切换定义数据
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    public function getSwitchDataDefine(Request $request)
    {
        $cell   = $request['cell'];
        $result = TempEUtranCellRelation::query()->selectRaw('EUtranCellTDD as cell,nc_cellName as scell,sc_longitude as mlongitude,sc_latitude as mlatitude,sc_dir as mdir,sc_band as mband,nc_longitude as slongitude,nc_latitude as slatitude,nc_dir as sdir,nc_band as sband,sc_channel,nc_channel')
            ->where('EUtranCellTDD', $cell)
            ->whereNotNull('nc_cellName')
            ->whereNotNull('nc_longitude')
            ->whereNotNull('nc_channel')
            ->get()
            ->toArray();
        return json_encode($result);

    }//end getSwitchDataDefine()


    /**
     * 获得切入数据
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    public function getHandOverIn(Request $request)
    {
        $date   = $request['date'];
        $cell   = $request['cell'];
        $result = SysRelation_cell_day::query()->selectRaw('id,cell as ServeCell,scell as NeighCell,mlongitude as ServeLongitude,mlatitude as ServeLatitude,mdir as ServeDir,mband as ServeBand,slongitude as NeighLongitude,slatitude as NeighLatitude,sdir as NeighDir,sband as NeighLBand,执行切换失败数,准备切换尝试数,切换成功率,cell,mlongitude,mlatitude,mdir,mband,slongitude,slatitude,sdir,sband,执行切换失败数 as failCount,准备切换尝试数 as handoverAttemptCount,准备切换尝试数 as handoverAttemptCount1,切换成功率 as handoverSuccessRatio')
            ->where('day_id', $date)
            ->where('scell', $cell)
            ->get()
            ->toArray();
        return json_encode($result);

    }//end getHandOverIn()


    /**
     * 获得切入指标
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    public function getYellowColorIn(Request $request)
    {
        // $date = $request['date'];
        // $cell = $request['cell'];
        // $sql  = 'select id,cell as ServeCell,scell as NeighCell,mlongitude as ServeLongitude,mlatitude as ServeLatitude,mdir as ServeDir,mband as ServeBand'.',slongitude as NeighLongitude,slatitude as NeighLatitude,sdir as NeighDir,sband as NeighBand,执行切换失败数,准备切换尝试数,准备切换尝试数,切换成功率 from SysRelation_cell_day where day_id = \''.$date.'\' and scell = \''.$cell.'\'';

        // $result = DB::connection('autokpi')->select($sql);
        // return json_encode($result);

    }//end getYellowColorIn()


    /**
     * 获得切换数据详细
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    public function getswitchDetail(Request $request)
    {
        $date   = $request['date'];
        $scells = $request['scells'];
        $cell   = $request['cell'];
        $result = SysRelation_cell_day::where('day_id', $date)->where('cell', $cell)->whereIn('scell', $scells)->get()->toArray();
        $reData['data'] = $result;
        return json_encode($reData);
    }//end getswitchDetail()


    /**
     * 获得切入数据详细
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    public function getHandOverInDetail(Request $request)
    {
        $date  = $request['date'];
        $cell  = $request['cell'];
        $cells = $request['cells'];
        $result = SysRelation_cell_day::where('day_id', $date)->where('scell', $cell)->whereIn('cell', $cells)->get()->toArray();
        $reData['data'] = $result;
        return json_encode($reData);

    }//end getHandOverInDetail()


    /**
     * 获得切换数据概览
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    public function getSwitchOutTable(Request $request)
    {
        $cell  = $request['cell'];
        $date  = $request['date'];
        $type  = input::get('type');

        $rows = SysRelation_cell_day::query()->selectRaw('scell,mlongitude,mlatitude,mdir,mband'.',slongitude,slatitude,sdir,sband,执行切换失败数 as failCount,准备切换尝试数 as handoverAttemptCount,切换成功率 as handoverSuccessRatio')
            ->where('day_id', $date)
            ->where('cell', $cell)
            ->whereNotNull('slongitude')
            ->whereNotNull('slatitude')
            ->whereNotNull('sdir')
            ->whereNotNull('sband')
            ->whereNotNull('scell')
            ->get()
            ->toArray();
        $scell = [];
        foreach ($rows as $row) {
            $scell[] = $row['scell'];
        }

        $numless100  = 0;
        $num100to200 = 0;
        $nummore200  = 0;

        $wireLostmore5 = 0;
        $wireLost1to5  = 0;
        $wireLostLess1 = 0;

        $phschless110 = 0;
        $phsch110to95 = 0;
        $phschmore95  = 0;

        foreach ($scell as $num) {
            $rows = SysCoreTemp_cell_day::where('day_id', $date)->where('cell', $num)->get()->toArray();
            if (count($rows) != 1) {
                continue;
            }
            // 最大RRC连接用户数
            if ($rows[0]['最大RRC连接用户数'] < 100) {
                $numless100++;
            } else if ($rows[0]['最大RRC连接用户数'] > 200) {
                $nummore200++;
            } else {
                $num100to200++;
            }
     
            // 无线掉线率
            $rows_cell = '';
            if ($type=='BadHandoverCell') {
                if ($rows[0]['切换成功率'] < 90) {
                    $wireLostLess1++;
                } else if ($rows[0]['切换成功率'] > 95) {
                    $wireLostmore5++;
                } else {
                    $wireLost1to5++;
                }
            } else if ($type == 'BadCell') {
                    if ($rows[0]['无线接通率'] < 90) {
                        $wireLostLess1++;
                    } else if ($rows[0]['无线接通率'] > 95) {
                        $wireLostmore5++;
                    } else {
                        $wireLost1to5++;
                    }
            } else {
          
             if ($rows[0]['无线掉线率'] < 1) {
                    $wireLostLess1++;
                } else if ($rows[0]['无线掉线率'] > 5) {
                    $wireLostmore5++;
                } else {
                    $wireLost1to5++;
                }
            }

            // if ($rows[0]['无线掉线率'] < 1) {
            //     $wireLostLess1++;
            // } else if ($rows[0]['无线掉线率'] > 5) {
            //     $wireLostmore5++;
            // } else {
            //     $wireLost1to5++;
            // }

            // PUSCH上行干扰电平
            if ($rows[0]['PUSCH上行干扰电平'] > -95) {
                $phschmore95++;
            } else if ($rows[0]['PUSCH上行干扰电平'] < -110) {
                $phschless110++;
            } else {
                $phsch110to95++;
            }
        }//end foreach

        $data = [];
        array_push($data, $nummore200, $num100to200, $numless100, $wireLostmore5, $wireLost1to5, $wireLostLess1, $phschless110, $phsch110to95, $phschmore95);
        return json_encode($data);

    }//end getSwitchOutTable()


    /**
     * 获得切入数据概览
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    function getSwitchOutTableIn(Request $request)
    {
        $date = $request['date'];
        $cell = $request['cell'];

        $rows = SysRelation_cell_day::query()->selectRaw('cell,mlongitude,mlatitude,mdir,mband'.',slongitude,slatitude,sdir,sband,执行切换失败数 as failCount,准备切换尝试数 as handoverAttemptCount,切换成功率 as handoverSuccessRatio')
            ->where('day_id', $date)
            ->where('scell', $cell)
            ->get()
            ->toArray();
        $scell = [];
        foreach ($rows as $row) {
            $scell[] = $row['cell'];
        }

        $numless100  = 0;
        $num100to200 = 0;
        $nummore200  = 0;

        $wireLostmore5 = 0;
        $wireLost1to5  = 0;
        $wireLostLess1 = 0;

        $phschless110 = 0;
        $phsch110to95 = 0;
        $phschmore95  = 0;
        foreach ($scell as $num) {
            $rows = SysCoreTemp_cell_day::where('day_id', $date)->where('cell', $num)->get()->toArray();
            if (count($rows) != 1) {
                continue;
            }
            // 最大RRC连接用户数
            if ($rows[0]['最大RRC连接用户数'] < 100) {
                $numless100++;
            } else if ($rows[0]['最大RRC连接用户数'] > 200) {
                $nummore200++;
            } else {
                $num100to200++;
            }

            // 无线掉线率
            if ($rows[0]['无线掉线率'] < 1) {
                $wireLostLess1++;
            } else if ($rows[0]['无线掉线率'] > 5) {
                $wireLostmore5++;
            } else {
                $wireLost1to5++;
            }

            // PUSCH上行干扰电平
            if ($rows[0]['PUSCH上行干扰电平'] > -95) {
                $phschmore95++;
            } else if ($rows[0]['PUSCH上行干扰电平'] < -110) {
                $phschless110++;
            } else {
                $phsch110to95++;
            }
        }//end foreach

        $data = [];
        array_push($data, $nummore200, $num100to200, $numless100, $wireLostmore5, $wireLost1to5, $wireLostLess1, $phschless110, $phsch110to95, $phschmore95);
        return json_encode($data);

    }//end getSwitchOutTableIn()


    /**
     * 获得RRC用户数
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    function getRRCusers(Request $request)
    {
        $date  = $request['date'];
        $cell  = $request['cell'];

        $rows = SysRelation_cell_day::query()->selectRaw('id,cell as ServeCell, scell as NeighCell,mlongitude as ServeLongitude,mlatitude as ServeLatitude,mdir as ServeDir,mband as ServeBand,slongitude as NeighLongitude,slatitude as NeighLatitude,sdir as NeighDir,sband as NeighBand,执行切换失败数,准备切换尝试数,切换成功率,scell,mlongitude,mlatitude,mdir,mband,slongitude,slatitude,sdir,sband,执行切换失败数 as failCount,准备切换尝试数 as handoverAttemptCount,准备切换尝试数 as handoverAttemptCount1,切换成功率 as handoverSuccessRatio')
            ->where('day_id', $date)
            ->where('cell', $cell)
            ->whereNotNull('slongitude')
            ->whereNotNull('slatitude')
            ->whereNotNull('sdir')
            ->whereNotNull('sband')
            ->whereNotNull('scell')
            ->get()
            ->toArray();
        $scell = [];
        foreach ($rows as $row) {
            $scell[] = $row['scell'];
        }

        $i = 0;
        foreach ($scell as $num) {
            $rows1 = SysCoreTemp_cell_day::where('day_id', $date)->where('cell', $num)->get()->toArray();
            if (count($rows1) != 1) {
                $i++;
                continue;
            }

            // 最大RRC连接用户数
            if ($rows1[0]['最大RRC连接用户数'] < 100) {
                $rows[$i]['handoverAttemptCount'] = 30;
                $i++;
            } else if ($rows1[0]['最大RRC连接用户数'] > 200) {
                $rows[$i]['handoverAttemptCount'] = 55;
                $i++;
            } else {
                $rows[$i]['handoverAttemptCount'] = 100;
                $i++;
            }
        }//end foreach

        return json_encode($rows);

    }//end getRRCusers()


    /**
     * 获得无限掉线率
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    function getWireLessLost(Request $request)
    {
        $date  = $request['date'];
        $cell  = $request['cell'];

        $rows = SysRelation_cell_day::query()->selectRaw('id,cell as ServeCell, scell as NeighCell,mlongitude as ServeLongitude,mlatitude as ServeLatitude,mdir as ServeDir,mband as ServeBand,slongitude as NeighLongitude,slatitude as NeighLatitude,sdir as NeighDir,sband as NeighBand,执行切换失败数,准备切换尝试数,切换成功率,scell,mlongitude,mlatitude,mdir,mband,slongitude,slatitude,sdir,sband,执行切换失败数 as failCount,准备切换尝试数 as handoverAttemptCount,准备切换尝试数 as handoverAttemptCount1,切换成功率 as handoverSuccessRatio')
            ->where('day_id', $date)
            ->where('cell', $cell)
            ->whereNotNull('slongitude')
            ->whereNotNull('slatitude')
            ->whereNotNull('sdir')
            ->whereNotNull('sband')
            ->whereNotNull('scell')
            ->get()
            ->toArray();
        $scell = [];
        foreach ($rows as $row) {
            $scell[] = $row['scell'];
        }

        $i = 0;
        foreach ($scell as $num) {
            $rows1 = SysCoreTemp_cell_day::where('day_id', $date)->where('cell', $num)->get()->toArray();
            if (count($rows1) != 1) {
                $i++;
                continue;
            }

            // 无线掉线率
            if ($rows1[0]['无线掉线率'] < 1) {
                $rows[$i]['handoverAttemptCount'] = 30;
                $i++;
            } else if ($rows1[0]['无线掉线率'] > 5) {
                $rows[$i]['handoverAttemptCount'] = 55;
                $i++;
            } else {
                $rows[$i]['handoverAttemptCount'] = 100;
                $i++;
            }
        }//end foreach

        return json_encode($rows);

    }//end getWireLessLost()


    /**
     * 获得PUSCH干扰指标
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    function getPUSCHInterfere(Request $request)
    {
        $date  = $request['date'];
        $cell  = $request['cell'];

        $rows = SysRelation_cell_day::query()->selectRaw('id,cell as ServeCell, scell as NeighCell,mlongitude as ServeLongitude,mlatitude as ServeLatitude,mdir as ServeDir,mband as ServeBand,slongitude as NeighLongitude,slatitude as NeighLatitude,sdir as NeighDir,sband as NeighBand,执行切换失败数,准备切换尝试数,切换成功率,scell,mlongitude,mlatitude,mdir,mband,slongitude,slatitude,sdir,sband,执行切换失败数 as failCount,准备切换尝试数 as handoverAttemptCount,准备切换尝试数 as handoverAttemptCount1,切换成功率 as handoverSuccessRatio')
            ->where('day_id', $date)
            ->where('cell', $cell)
            ->whereNotNull('slongitude')
            ->whereNotNull('slatitude')
            ->whereNotNull('sdir')
            ->whereNotNull('sband')
            ->whereNotNull('scell')
            ->get()
            ->toArray();
        $scell = [];
        foreach ($rows as $row) {
            $scell[] = $row['scell'];
        }

        $i = 0;
        foreach ($scell as $num) {
            $rows1 = SysCoreTemp_cell_day::where('day_id', $date)->where('cell', $num)->get()->toArray();
            if (count($rows1) != 1) {
                $i++;
                continue;
            }

            if ($rows1[0]['PUSCH上行干扰电平'] < -110) {
                $rows[$i]['handoverAttemptCount'] = 30;
                $i++;
            } else if ($rows1[0]['PUSCH上行干扰电平'] > -95) {
                $rows[$i]['handoverAttemptCount'] = 55;
                $i++;
            } else {
                $rows[$i]['handoverAttemptCount'] = 100;
                $i++;
            }
        }

        return json_encode($rows);

    }//end getPUSCHInterfere()


    /**
     * 获得PUSCH干扰指标
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    function getPUSCHInterferein(Request $request)
    {
        $date  = $request['date'];
        $cell  = $request['cell'];

        $rows = SysRelation_cell_day::query()->selectRaw('cell,mlongitude,mlatitude,mdir,mband'.',slongitude,slatitude,sdir,sband,执行切换失败数 as failCount,准备切换尝试数 as handoverAttemptCount,准备切换尝试数 as handoverAttemptCount1,切换成功率 as handoverSuccessRatio')
            ->where('day_id', $date)
            ->where('scell', $cell)
            ->get()
            ->toArray();
        $scell = [];
        foreach ($rows as $row) {
            $scell[] = $row['cell'];
        }

        $i = 0;
        foreach ($scell as $num) {
            $rows1 = SysCoreTemp_cell_day::where('day_id', $date)->where('cell', $num)->get()->toArray();
            if (count($rows1) != 1) {
                $i++;
                continue;
            }

            if ($rows1[0]['PUSCH上行干扰电平'] < -110) {
                $rows[$i]['handoverAttemptCount'] = 30;
                $i++;
            } else if ($rows1[0]['PUSCH上行干扰电平'] > -95) {
                $rows[$i]['handoverAttemptCount'] = 55;
                $i++;
            } else {
                $rows[$i]['handoverAttemptCount'] = 100;
                $i++;
            }
        }

        return json_encode($rows);

    }//end getPUSCHInterferein()


    /**
     * 获得切换成功率指标
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    function getHandoverSuccin(Request $request)
    {
        $date = $request['date'];
        $cell = $request['cell'];

        $rows = SysRelation_cell_day::query()->selectRaw('cell,mlongitude,mlatitude,mdir,mband'.',slongitude,slatitude,sdir,sband,执行切换失败数 as failCount,准备切换尝试数 as handoverAttemptCount,准备切换尝试数 as handoverAttemptCount1,切换成功率 as handoverSuccessRatio')
            ->where('day_id', $date)
            ->where('scell', $cell)
            ->get()
            ->toArray();
        $scell = [];
        foreach ($rows as $row) {
            $scell[] = $row['cell'];
        }

        $i = 0;
        foreach ($scell as $num) {
            $rows1 = SysCoreTemp_cell_day::where('day_id', $date)->where('cell', $num)->get()->toArray();
            if (count($rows1) != 1) {
                $i++;
                continue;
            }

            // 切换成功率
            if ($rows1[0]['切换成功率'] > 95) {
                $rows[$i]['handoverAttemptCount'] = 30;
                $i++;
            } else if ($rows1[0]['切换成功率'] < 85) {
                $rows[$i]['handoverAttemptCount'] = 55;
                $i++;
            } else {
                $rows[$i]['handoverAttemptCount'] = 100;
                $i++;
            }
        }//end foreach

        return json_encode($rows);

    }//end getHandoverSuccin()


    /**
     * 获得RRC用户数
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    function getRRCusersin(Request $request)
    {
        $date  = $request['date'];
        $cell  = $request['cell'];

        $rows = SysRelation_cell_day::query()->selectRaw('id,cell as ServeCell,scell as NeighCell,mlongitude as ServeLongitude,mlatitude as ServeLatitude,mdir as ServeDir,mband as ServeBand,slongitude as NeighLongitude,slatitude as NeighLatitude,sdir as NeighDir,sband as NeighLBand,执行切换失败数,准备切换尝试数,切换成功率,cell,mlongitude,mlatitude,mdir,mband,slongitude,slatitude,sdir,sband,执行切换失败数 as failCount,准备切换尝试数 as handoverAttemptCount,准备切换尝试数 as handoverAttemptCount1,切换成功率 as handoverSuccessRatio')
            ->where('day_id', $date)
            ->where('scell', $cell)
            ->get()
            ->toArray();
        $scell = [];
        foreach ($rows as $row) {
            $scell[] = $row['cell'];
        }

        $i = 0;
        foreach ($scell as $num) {
            $rows1 = SysCoreTemp_cell_day::where('day_id', $date)->where('cell', $num)->get()->toArray();
            if (count($rows1) != 1) {
                $i++;
                continue;
            }

            if ($rows1[0]['最大RRC连接用户数'] < 100) {
                $rows[$i]['handoverAttemptCount'] = 30;
                $i++;
            } else if ($rows1[0]['最大RRC连接用户数'] > 200) {
                $rows[$i]['handoverAttemptCount'] = 55;
                $i++;
            } else {
                $rows[$i]['最大RRC连接用户数'] = 100;
                $i++;
            }
        }

        return json_encode($rows);

    }//end getRRCusersin()


    /**
     * 获得无线掉线率
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    function getWireLessLostin(Request $request)
    {
        $date  = $request['date'];
        $cell  = $request['cell'];

        $rows = SysRelation_cell_day::query()->selectRaw('id,cell as ServeCell,scell as NeighCell,mlongitude as ServeLongitude,mlatitude as ServeLatitude,mdir as ServeDir,mband as ServeBand,slongitude as NeighLongitude,slatitude as NeighLatitude,sdir as NeighDir,sband as NeighLBand,执行切换失败数,准备切换尝试数,切换成功率,cell,mlongitude,mlatitude,mdir,mband,slongitude,slatitude,sdir,sband,执行切换失败数 as failCount,准备切换尝试数 as handoverAttemptCount,准备切换尝试数 as handoverAttemptCount1,切换成功率 as handoverSuccessRatio')
            ->where('day_id', $date)
            ->where('scell', $cell)
            ->get()
            ->toArray();
        $scell = [];
        foreach ($rows as $row) {
            $scell[] = $row['cell'];
        }

        $i = 0;
        foreach ($scell as $num) {
            $rows1 = SysCoreTemp_cell_day::where('day_id', $date)->where('cell', $num)->get()->toArray();
            if (count($rows1) != 1) {
                $i++;
                continue;
            }

            // 无线掉线率
            if ($rows1[0]['无线掉线率'] < 1) {
                $rows[$i]['handoverAttemptCount'] = 30;
                $i++;
            } else if ($rows1[0]['无线掉线率'] > 5) {
                $rows[$i]['handoverAttemptCount'] = 55;
                $i++;
            } else {
                $rows[$i]['无线掉线率'] = 100;
                $i++;
            }
        }//end foreach

        return json_encode($rows);

    }//end getWireLessLostin()


    /**
     * 获得日期列表
     *
     * @return array
     */
    public function getfdfdh()
    {
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('mongs', 'AutoKPI');
        $table  = 'SysRelation_cell_day';
        $sql    = "select distinct day_id from $table";
        $this->type = 'AutoKPI:switch';
        return $this->getValue($db, $sql);

    }//end getfdfdh()


    /**
     * 获得切换详细
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    public function getSwitchDefineDetail(Request $request)
    {
        $scells = $request['scells'];
        $cell   = $request['cell'];
        if (count($scells) == 0) {
            $result = SiteLte::query()->selectRaw('ecgi,cellName,siteName,cellNameChinese,longitudeBD as longitude,latitudeBD as latitudedir,pci,earfcn,cellType,tiltM,tiltE,antHeight,city,importDate,band,highTraffic,highInterference,HST')
            ->where('cellName', $cell)
            ->get()
            ->toArray();
        } else {
            $result = SiteLte::query()->selectRaw('ecgi,cellName,siteName,cellNameChinese,longitudeBD as longitude,latitudeBD as latitudedir,pci,earfcn,cellType,tiltM,tiltE,antHeight,city,importDate,band,highTraffic,highInterference,HST')
            ->whereIn('cellName', $scells)
            ->get()
            ->toArray();
        }
        $reData['data'] = $result;
        return json_encode($reData);

    }//end getSwitchDefineDetail()


}//end class
