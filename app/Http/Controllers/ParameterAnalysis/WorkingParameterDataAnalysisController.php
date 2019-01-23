<?php

/**
 * WorkingParameterDataAnalysisController.php
 *
 * @category ParameterAnalysis
 * @package  App\Http\Controllers\ParameterAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\ParameterAnalysis;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Common\MyRedis;
use PDO;
use App\Models\Mongs\SiteLte;
use App\Models\WK_APP\Report_LatLonDirCheck_Combined;
use App\Models\WK_APP\Report_60degreeSectorDirCheck;
use App\Models\WK_APP\Report_100mCoEarfcnSiteCheck;


/**
 * RRU查询
 * Class WorkingParameterDataAnalysisController
 *
 * @category ParameterAnalysis
 * @package  App\Http\Controllers\ParameterAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class WorkingParameterDataAnalysisController extends MyRedis
{
    /**
     * 获得日期列表
     *
     * @return array
     */
    public function getDate()
    {
        $dbc    = new DataBaseConnection();
        $db    = $dbc->getDB('mongs', 'wk_app');
        $action = input::get('action');
        switch ($action) {
            case 1:
                $table = 'Report_100mCoEarfcnSiteCheck';
                break;
            case 2:
                $table = 'Report_60degreeSectorDirCheck';
                break;
            case 3:
                $table = 'Report_LatLonDirCheck_Combined';
                break;
            case 4:
                $table = 'Report_LatLonDirCheck_Combined';
                break;
        }
        $sql    = "select distinct date_id from $table";
        $this->type = 'wk_app:'.$table;
        return $this->getValue($db, $sql);
    }

    public function getTableData()
    {
        $action     = input::get("action");
        $dimension     = input::get("dimension");
        $date     = input::get("date");
        $city     = input::get("city");
        $station     = input::get("station");
        $cells     = input::get("cells");
        $keyWord     = input::get("keyWord");

        $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;

        $table = "";
        switch ($action) {
            case '1':
                $conn = new Report_100mCoEarfcnSiteCheck;
                $table = "Report_100mCoEarfcnSiteCheck";
                break;
            case '2':
                $conn = new Report_60degreeSectorDirCheck;
                $table = "Report_60degreeSectorDirCheck";
                break;
            case '3':
                $conn = Report_LatLonDirCheck_Combined::where('Result_chk_dir','>45degree');
                $table = "Report_LatLonDirCheck_Combined";
                break;
            case '4':
                $conn = Report_LatLonDirCheck_Combined::where('Result_chk_dist','>1km');
                $table = "Report_LatLonDirCheck_Combined";
                break;
            
        }
        if ($dimension == "city") {
            $cityPY = new DataBaseConnection();
            $citys  = $cityPY->getConnCity($city);
            $conn = $conn->whereIn('s.city',$citys);
        }
        if ($dimension == "station") {
            $conn = $conn->where('s.siteName',$station);
        }
        if ($dimension == "cell") {
            $cellArr = explode(",", $cells);
            $conn = $conn->whereIn('ECELL',$cellArr);
        }
        if ($dimension == "keyWord") {
            $conn = $conn->where(function ($query) use ($keyWord) {
                        $query->where('s.siteName', 'like', '%'.$keyWord.'%')
                            ->orWhere('ECELL', 'like', '%'.$keyWord.'%');
                    });
            
        }

        $conn = $conn->where('date_id', $date)
                        ->leftJoin('mongs.siteLte as s', 's.ecgi', '=', $table.'.ECGI');
        $columns = ['*'];
        if ($action == 1) {
            $conn = $conn->leftJoin('mongs.siteLte as t', 't.ecgi', '=', $table.'.n_ECGI');
            $columns = [$table.'.*','s.*','t.ECI as n_ECI','t.cellNameChinese as n_cellNameChinese','t.siteNameChinese as n_siteNameChinese','t.cluster as n_cluster'];
        }
        $rows = $conn->paginate($limit, $columns)
                    ->toArray();
        $result["total"] = $rows['total'];
        $result['records'] = $rows['data'];
        echo json_encode($result);
    }

    public function exportFile()
    {
        $action     = input::get("action");
        $dimension     = input::get("dimension");
        $date     = input::get("date");
        $city     = input::get("city");
        $station     = input::get("station");
        $cells     = input::get("cells");
        $keyWord     = input::get("keyWord");

        $table = "";
        switch ($action) {
            case '1':
                $conn = new Report_100mCoEarfcnSiteCheck;
                $table = "Report_100mCoEarfcnSiteCheck";
                $filename = "common/files/工参分析查询_跨站100米内同频检查_" . $date . ".csv";
                break;
            case '2':
                $conn = new Report_60degreeSectorDirCheck;
                $table = "Report_60degreeSectorDirCheck";
                $filename = "common/files/工参分析查询_站内同频小区方位角检查_" . $date .".csv";
                break;
            case '3':
                $conn = Report_LatLonDirCheck_Combined::where('Result_chk_dir','>45degree');
                $table = "Report_LatLonDirCheck_Combined";
                $filename = "common/files/工参分析查询_方位角检查_" . $date .".csv";
                break;
            case '4':
                $conn = Report_LatLonDirCheck_Combined::where('Result_chk_dist','>1km');
                $table = "Report_LatLonDirCheck_Combined";
                $filename = "common/files/工参分析查询_经纬度检查_" . $date .".csv";
                break;
            
        }
        if ($dimension == "city") {
            $cityPY = new DataBaseConnection();
            $citys  = $cityPY->getConnCity($city);
            $conn = $conn->whereIn('s.city',$citys);
        }
        if ($dimension == "station") {
            $conn = $conn->where('s.siteName',$station);
        }
        if ($dimension == "cell") {
            $cellArr = explode(",", $cells);
            $conn = $conn->whereIn('ECELL',$cellArr);
        }
        if ($dimension == "keyWord") {
            $conn = $conn->where(function ($query) use ($keyWord) {
                        $query->where('s.siteName', 'like', '%'.$keyWord.'%')
                            ->orWhere('ECELL', 'like', '%'.$keyWord.'%');
                    });
            
        }

        $fieldObj = input::get("field");
        $fieldArr = [];
        $textArr = [];
        foreach ($fieldObj as $field) {
            $f = $field['field'];
            if ($f == "ECGI" || $f == "cellType" || $f == "earfcn") {
                $f = $table.".".$f;
            }
            if ($action == 1) {
                if ($f == "ECI") {
                    $f = "s.ECI";
                }
                if ($f == "n_ECI") {
                    $f = "t.ECI as n_ECI";
                }
                if ($f == "cellNameChinese") {
                    $f = "s.cellNameChinese";
                }
                if ($f == "n_cellNameChinese") {
                    $f = "t.cellNameChinese as n_cellNameChinese";
                }
                if ($f == "siteNameChinese") {
                    $f = "s.siteNameChinese";
                }
                if ($f == "n_siteNameChinese") {
                    $f = "t.siteNameChinese as n_siteNameChinese";
                }
                if ($f == "cluster") {
                    $f = "s.cluster";
                }
                if ($f == "n_cluster") {
                    $f = "t.cluster as n_cluster";
                }
                if ($f == "duplexMode") {
                    $f = "s.duplexMode";
                }
            }
            array_push($fieldArr, $f);
            array_push($textArr, $field['title']);
        }

        $conn = $conn->where('date_id', $date)
                        ->leftJoin('mongs.siteLte as s', 's.ecgi', '=', $table.'.ECGI');
        $columns = $fieldArr;
        if ($action == 1) {
            $conn = $conn->leftJoin('mongs.siteLte as t', 't.ecgi', '=', $table.'.n_ECGI');
        }
        $rows = $conn->get($columns)
                    ->toArray();
        $result['text'] = implode(",", $textArr);

        $csvContent = mb_convert_encoding($result['text'] . "\n", 'GBK', 'UTF-8');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($rows as $row) {
            $temp = "";
            foreach ($row as $key => $value) {
                $temp .= $value.",";
            }
            $temp = substr($temp, 0, -1);
            fwrite($fp, mb_convert_encoding($temp. "\n", 'GBK', 'UTF-8'));
        }
        fclose($fp);

        $result['filename'] = $filename;
        $result['rows'] = [];
        return json_encode($result);
    }
}