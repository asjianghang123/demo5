<?php

/**
 * FailureAnalysisController.php
 *
 * @category BadCellAnalysis
 * @package  App\Http\Controllers\BadCellAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\BadCellAnalysis;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use Config;
use App\Models\Mongs\Task;
use App\Models\Mongs\Task_DataBase\InternalProcHoExecS1In;
use App\Models\Mongs\Task_DataBase\InternalProcHoExecS1Out;
use App\Models\Mongs\Task_DataBase\InternalProcHoExecX2In;
use App\Models\Mongs\Task_DataBase\InternalProcHoExecX2Out;
use App\Models\Mongs\Task_DataBase\InternalProcHoPrepS1In;
use App\Models\Mongs\Task_DataBase\InternalProcHoPrepS1Out;
use App\Models\Mongs\Task_DataBase\InternalProcHoPrepX2In;
use App\Models\Mongs\Task_DataBase\InternalProcHoPrepX2Out;
use App\Models\Mongs\Task_DataBase\InternalProcInitialCtxtSetup;
use App\Models\Mongs\Task_DataBase\InternalProcRrcConnSetup;
use App\Models\Mongs\Task_DataBase\InternalProcS1SigConnSetup;
use App\Models\Mongs\Task_DataBase\InternalProcUeCtxtRelease;

/**
 * 失败原因分析
 * Class FailureAnalysisController
 *
 * @category BadCellAnalysis
 * @package  App\Http\Controllers\BadCellAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class FailureAnalysisController extends Controller
{


    /**
     * 获得数据库列表
     *
     * @return void
     */
    public function getDataBase()
    {
        $type   = input::get("type");
        $conn = Task::where('type', $type)->where('status', 'complete');
        $user = Auth::user()->user;
        if ($user != 'admin') {
            $filter = " and owner='$user'";
            $conn = $conn->where('owner', $user);
        }
        $row = $conn->orderBy('taskName', 'asc')->get()->toArray();
        $items = array();
        foreach ($row as $qr) {
            array_push($items, ["id" => $qr["taskName"], "text" => $qr["taskName"]]);
        }
        echo json_encode($items);

    }//end getDataBase()


    /**
     * 获得报表数据
     *
     * @return void
     */
    public function getChartData()
    {
        $database = input::get("db");
        $table        = input::get('resultTable');
        $drillDown    = input::get('drillDown');
        $drillDownArr = explode(',', $drillDown);

        Config::set("database.connections.kget.database", $database);
        $conn = $this->switchTable($table);
        $total = $conn::count();

        $result = array();
        $items         = array();
        $items['type'] = 'bar';
        $items['name'] = 'shares';
        $data          = array();

        $row = $conn->selectRaw($drillDown.',count(*) as sum')->groupBy($drillDown)->orderBy('sum', 'desc')->get()->toArray();
        foreach ($row as $r) {
            $array = array();
            $share = number_format((100 * $r['sum'] / $total), 2);
            foreach ($drillDownArr as $key) {
                $record["$key"] = $r[$key];
            }
            array_push($array, $record["$key"]);
            $record["share"] = $share;
            array_push($array, floatval($share));
            $tooltip[]=$r['sum'];
            array_push($data, $array);
        }

        $items['data']        = $data;
        $result["resultData"] = $items;
        echo json_encode($result);

    }//end getChartData()


    /**
     * 获得表格数据
     *
     * @return void
     */
    public function getTableData()
    {
        $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;

        $database = input::get("db");
        $table        = input::get('resultTable');
        $drillDown    = input::get('drillDown');
        $drillDownArr = explode(',', $drillDown);

        Config::set("database.connections.kget.database", $database);
        $conn = $this->switchTable($table);
        $total = $conn::count();

        $result = array();
        $result["total"] = $conn->selectRaw($drillDown)->groupBy($drillDown)->count();

        $items = array();
        $row = $conn->selectRaw($drillDown.',count(*) as sum')->groupBy($drillDown)->orderBy('sum', 'desc')->get()->chunk($limit)->toArray()[$page-1];
        foreach ($row as $r) {
            $share = number_format((100 * $r['sum'] / $total), 2)."%";
            foreach ($drillDownArr as $key) {
                $record["$key"] = $r[$key];
            }
            $record["value"]    = $r["sum"];
            $record["total"]    = $total;
            $record["share"]    = $share;
            array_push($items, $record);
        }

        $result["records"] = $items;
        echo json_encode($result);

    }//end getTableData()


    /**
     * 获得详情数据表头
     *
     * @return void
     */
    public function getDetailDataHeader()
    {
        $database = input::get("db");
        $table    = input::get("table");

        Config::set("database.connections.kget.database", $database);
        $conn = $this->switchTable($table);
        $head = $conn->first()->toArray();
        $text = array();
        foreach ($head as $key => $value) {
            array_push($text, $key);
        }
        $result         = array();
        $result['text'] = implode(",", $text);
        echo json_encode($result);

    }//end getDetailDataHeader()


    /**
     * 获得详细数据
     *
     * @return void
     */
    public function getdetailData()
    {
        // $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;

        $database = input::get("db");
        $table  = input::get('table');
        $filter = input::get("result");

        Config::set("database.connections.kget.database", $database);
        $conn = $this->switchTable($table);

        if ($table == "internalProcUeCtxtRelease") {
            $flag = '3gppCause';
        } else {
            $flag = 'result';
        }
        $row = $conn->where($flag, $filter)->orderBy('eventTime', 'desc')->paginate($limit)->toArray();

        $result["total"]   = $row['total'];
        $result["records"] = $row['data'];
        echo json_encode($result);

    }//end getdetailData()


    /**
     * 导出详情
     *
     * @return string 导出结果
     */
    public function exportFile()
    {
        $drillDownText = "result";
        $database = input::get("db");
        $table         = input::get('table');
        $filter        = input::get("result");

        if ($table == "internalProcUeCtxtRelease") {
            $flag = '3gppCause';
        } else {
            $flag = 'result';
        }

        Config::set("database.connections.kget.database", $database);
        $conn = $this->switchTable($table);
        $row = $conn->where($flag, $filter)->orderBy('eventTime', 'desc')->get()->toArray();

        $result = array();
        $result["text"]   = $this->getTableField();
        $result['rows']   = $row;
        $result['total']  = count($row);
        $result['result'] = 'true';
        $filename         = "common/files/".$drillDownText."_".$table."_".date('YmdHis').".csv";

        $this->resultToCSV2($result, $filename);
        $result['filename'] = $filename;
        $result['rows'] = null ;

        echo json_encode($result);

    }//end exportFile()


    /**
     * 获得数据表列名列表
     *
     * @return string 列名列表
     */
    public function getTableField()
    {
        $database = input::get("db");
        $table    = input::get("table");

        Config::set("database.connections.kget.database", $database);
        $conn = $this->switchTable($table);
        $head = $conn->first()->toArray();
        $text = array();
        foreach ($head as $key => $value) {
            array_push($text, $key);
        }
        return implode(",", $text);

    }//end getTableField()


    /**
     * 导出CSV文件
     *
     * @param array  $result   导出数据
     * @param string $filename CSV文件名
     *
     * @return void
     */
    protected function resultToCSV2($result, $filename)
    {
        $csvContent = mb_convert_encoding($result['text']."\n", 'GBK');
        $fp         = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result['rows'] as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

    }//end resultToCSV2()


    /**
     * 获得对应表的链接
     *
     * @return void
     */
    public function switchTable($table)
    {
        switch($table)
        {
        case 'internalProcHoExecS1In':
            $conn = new InternalProcHoExecS1In;
            break;
        case 'internalProcHoExecS1Out':
            $conn = new InternalProcHoExecS1Out;
            break;
        case 'internalProcHoExecX2In':
            $conn = new InternalProcHoExecX2In;
            break;
        case 'internalProcHoExecX2Out':
            $conn = new InternalProcHoExecX2Out;
            break;
        case 'internalProcHoPrepS1In':
            $conn = new InternalProcHoPrepS1In;
            break;
        case 'internalProcHoPrepS1Out':
            $conn = new InternalProcHoPrepS1Out;
            break;
        case 'internalProcHoPrepX2In':
            $conn = new InternalProcHoPrepX2In;
            break;
        case 'internalProcHoPrepX2Out':
            $conn = new InternalProcHoPrepX2Out;
            break;
        case 'internalProcInitialCtxtSetup':
            $conn = new InternalProcInitialCtxtSetup;
            break;
        case 'internalProcRrcConnSetup':
            $conn = new InternalProcRrcConnSetup;
            break;
        case 'internalProcS1SigConnSetup':
            $conn = new InternalProcS1SigConnSetup;
            break;
        case 'internalProcUeCtxtRelease':
            $conn = new InternalProcUeCtxtRelease;
            break;
                
        }
        return $conn;
    }

}//end class
