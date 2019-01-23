<?php

/**
 * BulkcmMarkController.php
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
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use PDO;
use Config;
use App\Models\Bulkcm\TempParameterBulkcmCompare;
use App\Models\Bulkcm\TempParameterBulkcmCompareProcess;
use App\Models\TABLES;
use App\Models\SCHEMATA;

/**
 * BulkCM分析
 * Class BulkcmMarkController
 *
 * @category ParameterAnalysis
 * @package  App\Http\Controllers\ParameterAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class BulkcmMarkController extends Controller
{


    /**
     * 获得数据表头
     *
     * @return array bulkcm数据表头
     */
    public function getBulkcmMarkDataHeader()
    {
        $dbname = Input::get('dataBase');
        Config::set("database.connections.bulkcm.database", $dbname);
        $result = array();
        $count = TABLES::where("TABLE_SCHEMA","=",$dbname)->where("TABLE_NAME","=","TempParameterBulkcmCompare")->count();
        if ($count >= 1) {
            $process = TempParameterBulkcmCompareProcess::count();
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
        $rows = TempParameterBulkcmCompare::query()->selectRaw('subNetwork,meContext,EUtranCellTDDId,ecgi,DN,parameterName,parameter,oldValue,newValue')->first()->toArray();
        return $rows;

    }//end getBulkcmMarkDataHeader()


    /**
     * 获得BulkCM数据
     *
     * @return string BulkCM数据(JSON)
     */
    public function getBulkcmMarkData()
    {
        // $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $dbname = Input::get('dataBase');
        $citys  = Input::get('citys');
        Config::set("database.connections.bulkcm.database", $dbname);

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
            $row = TempParameterBulkcmCompare::whereIn('subNetwork', explode(",", $subNetwork))->paginate($rows)->toArray();
        } else {
            $row = TempParameterBulkcmCompare::paginate($rows)->toArray();
        }
        $result["total"] = $row['total'];
        $items = array();
        foreach ($row['data'] as $qr) {
            $qr = $this->substringParamData($qr);
            array_push($items, $qr);
        }

        $result['records'] = $items;
        return json_encode($result);

    }//end getBulkcmMarkData()


    /**
     * 获得DN字串
     *
     * @param array $row 元数据
     *
     * @return mixed 字串
     */
    function substringParamData($row)
    {
        foreach ($row as $key => $value) {
            if ($key == 'DN') {
                $row[$key] = substr($value, 0, 30);
            }
        }

        return $row;

    }//end substringParamData()


    /**
     * 导出全量BulkCM数据
     *
     * @return mixed 导出结果
     */
    public function getAllBulkcmMarkData()
    {
        $dbname     = Input::get('dataBase');
        $citys      = Input::get('citys');
        Config::set("database.connections.bulkcm.database", $dbname);
        $dbc        = new DataBaseConnection();
        $fileName   = "files/".$dbname."_TempParameterBulkcmCompare_".date('YmdHis').".csv";
        $column     = "subNetwork,meContext,EUtranCellTDDId,ecgi,DN,parameterName,parameter,oldValue,newValue";
        $subNetwork = '';
        if ($citys) {
            foreach ($citys as $city) {
                $subNetwork .= $dbc->getSubNets($city).',';
            }
            $subNetwork = substr($subNetwork, 0, -1);
            $subNetwork = str_replace("'", "", $subNetwork);
        }

        if ($citys) {
            $items = TempParameterBulkcmCompare::query()
                ->selectRaw($column)
                ->whereIn('subNetwork', explode(",", $subNetwork))
                ->get()
                ->toArray();
        } else {
            $items = TempParameterBulkcmCompare::query()
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
        return $result;

    }//end getAllBulkcmMarkData()


    /**
     * 获得任务列表
     *
     * @return void
     */
    public function getParamTasks()
    {
        $items = array();
        $type  = $_REQUEST['type'];
        $row = SCHEMATA::where('SCHEMA_NAME', 'like', $type."______")->get()->sortByDesc('SCHEMA_NAME')->toArray();
        foreach ($row as $task) {
            array_push($items, ["id" => $task["SCHEMA_NAME"], "text" => $task["SCHEMA_NAME"]]);
        }

        echo json_encode($items);

    }//end getParamTasks()


    /**
     * 获得城市列表
     *
     * @return string 城市列表
     */
    public function getAllCity()
    {
        $cityClass = new DataBaseConnection();
        return $cityClass->getCityOptions();

    }//end getAllCity()
}
