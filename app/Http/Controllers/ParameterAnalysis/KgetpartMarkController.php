<?php

/**
 * KgetpartMarkController.php
 *
 * @category ParameterAnalysis
 * @package  App\Http\Controllers\ParameterAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\ParameterAnalysis;

use App\DatabaseConn;
use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\FileUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use PDO;
use Config;
use App\Models\Mongs\Task;
use App\Models\Kgetpart\TempParameterKgetpartCompare;
use App\Models\Kgetpart\TempParameterKgetParkCompareProcess;
use App\Models\TABLES;

/**
 * KgetPart分析
 * Class KgetpartMarkController
 *
 * @category ParameterAnalysis
 * @package  App\Http\Controllers\ParameterAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class KgetpartMarkController extends Controller
{


    /**
     * 获取任务列表
     *
     * @return void
     */
    public function getParamTasks()
    {
        $items = array();
        $type  = $_REQUEST['type'];
        $row = Task::where('taskName', 'like', $type.'______')->get()->sortByDesc('taskName')->toArray();
        foreach ($row as $task) {
            array_push($items, ["id" => $task["taskName"], "text" => $task["taskName"]]);
        }

        echo json_encode($items);

    }//end getParamTasks()


    /**
     * 获得城市列表
     *
     * @return string
     */
    public function getAllCity()
    {
        $cityClass = new DataBaseConnection();
        return $cityClass->getCityOptions();

    }//end getAllCity()


    /**
     * 获取表头
     *
     * @return array 表头
     */
    public function getKgetpartMarkDataHeader()
    {
        $dbname = Input::get('dataBase');
        Config::set("database.connections.kgetpart.database", $dbname);
        $result = array();
        $count = TABLES::where("TABLE_SCHEMA","=",$dbname)->where("TABLE_NAME","=","TempParameterKgetpartCompare")->count();
        if ($count >= 1) {
            $process = TempParameterKgetParkCompareProcess::count();
            if ($process >= 3) {
                $result['flag'] = "true";
            }else{
                $result['flag'] = "error";
                return $result;
            }
        }else{
            $result['flag'] = "error";
            return $result;
        }
        
        $rows = TempParameterKgetpartCompare::query()->selectRaw('subNetwork,meContext,EUtranCellTDDId,ecgi,DN,parameterName,parameter,oldValue,newValue')->first()->toArray();
        return $rows;

    }//end getKgetpartMarkDataHeader()


    /**
     * 获得KgetPart数据(分页)
     *
     * @return string 当页KgetPart数据(JSON)
     */
    public function getKgetpartMarkData()
    {
        // $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $dbname = Input::get('dataBase');
        $citys  = Input::get('citys');
        Config::set("database.connections.kgetpart.database", $dbname);
        $result = array();
        $dbc    = new DataBaseConnection();
        $subNetwork = '';
        if ($citys) {
            foreach ($citys as $city) {
                $subNetwork .= $dbc->getSubNets($city).',';
            }
            $subNetwork = substr($subNetwork, 0, -1);
            $subNetwork = str_replace("'", "", $subNetwork);
        }
        if ($citys) {
            $row = TempParameterKgetpartCompare::whereIn('subNetwork', explode(",", $subNetwork))->paginate($rows)->toArray();
        } else {
            $row = TempParameterKgetpartCompare::paginate($rows)->toArray();
        }
        $result["total"] = $row['total'];
        $items = array();
        foreach ($row['data'] as $qr) {
            $qr = $this->substringParamData($qr);
            array_push($items, $qr);
        }

        $result['records'] = $items;
        return json_encode($result);

    }//end getKgetpartMarkData()


    /**
     * 更新行信息
     *
     * @param array $row 行信息
     *
     * @return mixed 行信息
     */
    function substringParamData($row)
    {
        foreach ($row as $key => $value) {
            if ($key == 'DN') {
                $row[$key] = substr($value, 0, 30);
            }
        }

        return $row;

    }//end substring_paramData()


    /**
     * 导出全量KgetPart数据
     *
     * @return void
     */
    public function getAllKgetpartMarkData()
    {
        $dbname     = Input::get('dataBase');
        $citys      = Input::get('citys');
        Config::set("database.connections.kgetpart.database", $dbname);
        $dbc        = new DataBaseConnection();
        $fileName = "files/".$dbname."_TempParameterKgetpartCompare_".date('YmdHis').".csv";
        $column     = 'subNetwork,meContext,EUtranCellTDDId,ecgi,DN,parameterName,parameter,oldValue,newValue';
        $subNetwork = '';
        if ($citys) {
            foreach ($citys as $city) {
                $subNetwork .= $dbc->getSubNets($city).',';
            }
            $subNetwork = substr($subNetwork, 0, -1);
            $subNetwork = str_replace("'", "", $subNetwork);
        }
        if ($citys) {
            $items = TempParameterKgetpartCompare::query()
                ->selectRaw($column)
                ->whereIn('subNetwork', explode(",", $subNetwork))
                ->get()
                ->toArray();
        } else {
            $items = TempParameterKgetpartCompare::query()
                ->selectRaw($column)
                ->get()
                ->toArray();
        }
        if ($items) {
            $fileUtil = new FileUtil();
            $fileUtil->resultToCSV2($column, $items, $fileName);
            $result['fileName'] = $fileName;
            $result['result']   = true;
        } else {
            $result['result'] = false;
        }

        echo json_encode($result);

    }//end getAllKgetpartMarkData()


}//end class
