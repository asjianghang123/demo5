<?php
/**
* AppCoverageController.php
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
use App\Http\Controllers\Utils\FileUtil;
use App\Http\Requests;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use App\Models\Mongs\SiteLte;
use Illuminate\Support\Facades\Auth;
use App\Models\AutoKPI\App_CoverageJS;
use Illuminate\Support\Facades\Storage;
use Config;


/**
 * 自忙时小区处理
 * Class AppCoverageController
 *
 * @category BadCellAnalysis
 * @package  App\Http\Controllers\BadCellAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class AppCoverageController extends Controller
{   

    public function getAllCity(){
        $databaseConns = App_CoverageJS::select("city")->distinct()->get()->toArray();
        $items = array();
        foreach ($databaseConns as $databaseConn) {
            $city = '{"text":"' . $databaseConn["city"] . '","value":"' . $databaseConn["city"] . '"}';
            array_push($items, $city);
        }

        return json_encode($items);
    } 


     public function getAllHour(){
        $city = Input::get("city");
        if ($city == "") {
            $databaseConns = App_CoverageJS::select("hour_id")->distinct()->orderBy('hour_id', 'ASC')->get()->toArray();
        } else {
            $databaseConns = App_CoverageJS::select("hour_id")->distinct()->whereIn('App_CoverageJS.city', $city)->orderBy('hour_id', 'ASC')->get()->toArray();
        }
        $items = array();
        foreach ($databaseConns as $databaseConn) {
            $city = '{"text":"' . $databaseConn["hour_id"] . '","value":"' . $databaseConn["hour_id"] . '"}';
            array_push($items, $city);
        }

        return json_encode($items);
    }

    public function getAllDate() {
        $result = [];
        $city = Input::get("city");
        if ($city != "") {
            $date = App_CoverageJS::select("day_id")->distinct()->whereIn('App_CoverageJS.city', $city)->get()->toArray();
        } else {
            $date = App_CoverageJS::select("day_id")->distinct()->get()->toArray();
        }
        foreach ($date as $value) {
             array_push($result, $value["day_id"]);
        }
        return $result;
    }

    public function templateQuery() {
        $result=[];
        $table = "AppCoverage";
        $city = Input::get("city");
        $startTime = Input::get("startTime");
        $endTime = Input::get("endTime");
        $hour = Input::get("hour");
        $cell = Input::get("cell");
        $cellarr = explode(",", $cell);
        if ($cell == "") {
            $row = App_CoverageJS::select("App_CoverageJS.day_id", "App_CoverageJS.hour_id", "App_CoverageJS.city", "App_CoverageJS.subNetwork", "App_CoverageJS.cell", "siteLte.duplexMode", "siteLte.channelBandWidth", "siteLte.覆盖属性", "App_CoverageJS.pmRadioUeRepCqiDistr_0", "App_CoverageJS.pmRadioUeRepCqiDistr_1", "App_CoverageJS.pmRadioUeRepCqiDistr_2", "App_CoverageJS.pmRadioUeRepCqiDistr_3", "App_CoverageJS.pmRadioUeRepCqiDistr_4", "App_CoverageJS.pmRadioUeRepCqiDistr_5", "App_CoverageJS.pmRadioUeRepCqiDistr_6", "App_CoverageJS.pmRadioUeRepCqiDistr_7", "App_CoverageJS.pmRadioUeRepCqiDistr_8", "App_CoverageJS.pmRadioUeRepCqiDistr_9", "App_CoverageJS.pmRadioUeRepCqiDistr_10", "App_CoverageJS.pmRadioUeRepCqiDistr_11", "App_CoverageJS.pmRadioUeRepCqiDistr_12", "App_CoverageJS.pmRadioUeRepCqiDistr_13", "App_CoverageJS.pmRadioUeRepCqiDistr_14", "App_CoverageJS.pmRadioUeRepCqiDistr_15", "App_CoverageJS.无线下行流量GB", "App_CoverageJS.无线上行流量GB", "App_CoverageJS.上行PRB平均利用率", "App_CoverageJS.下行PRB平均利用率", "App_CoverageJS.下行利用率PDCCH", "App_CoverageJS.DLThroughput_kbps", "App_CoverageJS.ULThroughput_kbps", "App_CoverageJS.ActiveUE_DL", "App_CoverageJS.下行prb平均利用率QCI1", "App_CoverageJS.pmPrbUtilDlDistr_0", "App_CoverageJS.pmPrbUtilDlDistr_1", "App_CoverageJS.pmPrbUtilDlDistr_2", "App_CoverageJS.pmPrbUtilDlDistr_3", "App_CoverageJS.pmPrbUtilDlDistr_4", "App_CoverageJS.pmPrbUtilDlDistr_5", "App_CoverageJS.pmPrbUtilDlDistr_6", "App_CoverageJS.pmPrbUtilDlDistr_7", "App_CoverageJS.pmPrbUtilDlDistr_8", "App_CoverageJS.pmPrbUtilDlDistr_9", "App_CoverageJS.pmPrbUtilDlDistr_10", "App_CoverageJS.pmPrbUtilDlDistr_11", "App_CoverageJS.pmPrbUtilDlDistr_12", "App_CoverageJS.pmPrbUtilDlDistr_13", "App_CoverageJS.pmPrbUtilDlDistr_14", "App_CoverageJS.pmPrbUtilDlDistr_15", "App_CoverageJS.pmPrbUtilDlDistr_16", "App_CoverageJS.pmPrbUtilDlDistr_17", "App_CoverageJS.pmPrbUtilDlDistr_18", "App_CoverageJS.pmPrbUtilDlDistr_19")->whereRaw("App_CoverageJS.day_id>='$startTime' and App_CoverageJS.day_id<='$endTime'")->whereIn('App_CoverageJS.city', $city)->whereIn('App_CoverageJS.hour_id', $hour)->leftjoin("mongs.siteLte", "App_CoverageJS.cell", "=", "mongs.siteLte.cellName")->get()->toArray();
        } else {
            $cellstring = "";
            foreach (explode(",", $cell) as $cellvalue) {
                $cellstring = $cellstring . "'" . $cellvalue . "',";
            }
            $cellstring = rtrim($cellstring, ",");
            $row = App_CoverageJS::select("App_CoverageJS.day_id", "App_CoverageJS.hour_id", "App_CoverageJS.city", "App_CoverageJS.subNetwork", "App_CoverageJS.cell", "siteLte.duplexMode", "siteLte.channelBandWidth", "siteLte.覆盖属性", "App_CoverageJS.pmRadioUeRepCqiDistr_0", "App_CoverageJS.pmRadioUeRepCqiDistr_1", "App_CoverageJS.pmRadioUeRepCqiDistr_2", "App_CoverageJS.pmRadioUeRepCqiDistr_3", "App_CoverageJS.pmRadioUeRepCqiDistr_4", "App_CoverageJS.pmRadioUeRepCqiDistr_5", "App_CoverageJS.pmRadioUeRepCqiDistr_6", "App_CoverageJS.pmRadioUeRepCqiDistr_7", "App_CoverageJS.pmRadioUeRepCqiDistr_8", "App_CoverageJS.pmRadioUeRepCqiDistr_9", "App_CoverageJS.pmRadioUeRepCqiDistr_10", "App_CoverageJS.pmRadioUeRepCqiDistr_11", "App_CoverageJS.pmRadioUeRepCqiDistr_12", "App_CoverageJS.pmRadioUeRepCqiDistr_13", "App_CoverageJS.pmRadioUeRepCqiDistr_14", "App_CoverageJS.pmRadioUeRepCqiDistr_15", "App_CoverageJS.无线下行流量GB", "App_CoverageJS.无线上行流量GB", "App_CoverageJS.上行PRB平均利用率", "App_CoverageJS.下行PRB平均利用率", "App_CoverageJS.下行利用率PDCCH", "App_CoverageJS.DLThroughput_kbps", "App_CoverageJS.ULThroughput_kbps", "App_CoverageJS.ActiveUE_DL", "App_CoverageJS.下行prb平均利用率QCI1", "App_CoverageJS.pmPrbUtilDlDistr_0", "App_CoverageJS.pmPrbUtilDlDistr_1", "App_CoverageJS.pmPrbUtilDlDistr_2", "App_CoverageJS.pmPrbUtilDlDistr_3", "App_CoverageJS.pmPrbUtilDlDistr_4", "App_CoverageJS.pmPrbUtilDlDistr_5", "App_CoverageJS.pmPrbUtilDlDistr_6", "App_CoverageJS.pmPrbUtilDlDistr_7", "App_CoverageJS.pmPrbUtilDlDistr_8", "App_CoverageJS.pmPrbUtilDlDistr_9", "App_CoverageJS.pmPrbUtilDlDistr_10", "App_CoverageJS.pmPrbUtilDlDistr_11", "App_CoverageJS.pmPrbUtilDlDistr_12", "App_CoverageJS.pmPrbUtilDlDistr_13", "App_CoverageJS.pmPrbUtilDlDistr_14", "App_CoverageJS.pmPrbUtilDlDistr_15", "App_CoverageJS.pmPrbUtilDlDistr_16", "App_CoverageJS.pmPrbUtilDlDistr_17", "App_CoverageJS.pmPrbUtilDlDistr_18", "App_CoverageJS.pmPrbUtilDlDistr_19")->whereRaw("App_CoverageJS.day_id>=? and App_CoverageJS.day_id<=?", [$startTime, $endTime])->whereIn('App_CoverageJS.city', $city)->whereIn('App_CoverageJS.hour_id', $hour)->whereIn('App_CoverageJS.cell', $cellarr)->leftJoin("mongs.siteLte", function($join) {
                    $join->on("App_CoverageJS.cell", "=", "siteLte.cellName");
                })->get()->toArray(); 
        }
        if (count($row) > 0) {
            $result["rows"] = $row;
            $result["content"] = implode(",", array_keys($row[0]));
            $filename = "common/files/" . $table . date('YmdHis') . ".csv";
            $result['filename'] = $filename;
            $this->resultToCSV2($result, $filename);
        } else {
            $result["rows"] = "";
        }
        return $result;
    }

    //导出坏小区列表CSV文件
    protected function resultToCSV2($result, $filename)
    { 
        $csvContent = mb_convert_encoding($result['content'] . "\n", 'gb2312', 'utf-8');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result["rows"] as $row) {
            fputcsv($fp, $row);
        }
        // var_dump($fp);return;
        fclose($fp);
    }
}

    