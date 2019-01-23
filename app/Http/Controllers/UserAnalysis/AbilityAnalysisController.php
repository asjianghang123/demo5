<?php

/**
 * AbilityAnalysisController.php
 *
 * @category UserAnalysis
 * @package  App\Http\Controllers\UserAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\UserAnalysis;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use App\Models\CDR\UserInfo;
use App\Models\CTR\UeCapability;
use App\Http\Controllers\Common\MyRedis;
use App\Models\UeCapability\ueCapability as ueCapability_daily;

/**
 * 终端能力分析
 * Class AbilityAnalysisController
 *
 * @category UserAnalysis
 * @package  App\Http\Controllers\UserAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class AbilityAnalysisController extends MyRedis
{


    /**
     * 获得城市列表
     *
     * @return void
     */
    public function getCitys()
    {
        $dbc   = new DataBaseConnection();
        $db    = $dbc->getDB('CDR');
        $sql   = "show dataBases";
        $res   = $db->query($sql);
        $items = array();
        $row   = $res->fetchAll(PDO::FETCH_ASSOC);
        // foreach ($row as $r) {
        //     if ($r['DATABASE'] != 'Global') {
        //         $CHCity = $dbc->getCDRToCHName($r['DATABASE']);
        //         array_push($items, "CDR_".$CHCity."-".$r['DATABASE']);
        //     }
        // }

        $db    = $dbc->getDB('mongs', 'mongs');
        $sql   = "select DISTINCT cityChinese from databaseconn;";
        $res   = $db->query($sql);
        $row   = $res->fetchAll(PDO::FETCH_ASSOC);
        foreach ($row as $r) {
            $CHCity = $r['cityChinese'];
            $city = $dbc->getENCity($CHCity);
            array_push($items, $CHCity."-".$city);
        }

        echo json_encode($items);

    }//end getCitys()


    /**
     * 获得终端能力详情
     *
     * @return void
     */
    public function getTableData()
    {
        $city = input::get("city");
        $date = Input::get("date");

        // if(substr($city, 0, 3) == "CDR") {
            $userInfo = new UserInfo;
            $userInfo->setConnection($city);
            $total = $userInfo->distinct('imsi')->count('imsi');
            $FDD_1800M = $userInfo->where('Band', 'like', '%LTE FDD BAND 3%')->count();
            $FDD_900M = $userInfo->where('Band', 'like', '%LTE FDD BAND 8%')->count();
        // }
        

        $items  = array();
        $tempRow1          = array();
        $tempRow1['FDD']   = 'FDD_1800M';
        $tempRow1['Value'] = $FDD_1800M;
        $tempRow1['Total'] = $total;
        $tempRow1['Share'] = floatval(number_format(($FDD_1800M / $total * 100), 2));
        $tempRow2          = array();
        $tempRow2['FDD']   = 'FDD_900M';
        $tempRow2['Value'] = $FDD_900M;
        $tempRow2['Total'] = $total;
        $tempRow2['Share'] = floatval(number_format(($FDD_900M / $total * 100), 2));
        array_push($items, $tempRow1);
        array_push($items, $tempRow2);

        $result = array();
        $result["records"] = $items;
        echo json_encode($result);

    }//end getTableData()

    /**
     * 获取时间
     *
     * @return string
     */
    public function getTime() 
    {
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('UeCapability');
        $table  = 'ueCapability';
        $result = array();
        $sql    = "select DISTINCT dateId time from $table";
        $this->type = 'UeCapability:abilityAnalysis';
        return $this->getValue($db, $sql);
    }

    /**
     * 显示终端能力图表
     *
     * @return array
     */
    public function getChartData()
    {
        $city = input::get("city");
        $date = Input::get("date");
        $series = array();
        $data   = array();
        $result = array();
        if(substr($city, 0, 3) == "CDR") {
            $userInfo = new UserInfo;
            $userInfo->setConnection($city);
            $total = $userInfo->distinct('imsi')->count('imsi');
            if ($total == 0) {
                $result['flag'] = 'error';
            } else {
                $FDD_1800M = $userInfo->where('Band', 'like', '%LTE FDD BAND 3%')->count();
                $FDD_900M = $userInfo->where('Band', 'like', '%LTE FDD BAND 8%')->count();
                array_push($data, floatval(number_format(($FDD_1800M / $total * 100), 2)));
                array_push($data, floatval(number_format(($FDD_900M / $total * 100), 2)));
            }
        }else {
            $total = ueCapability_daily::where("dateId", $date)->count();
            $FDD_1800M = ueCapability_daily::select(DB::raw("sum(case when CONCAT(',',eutra,',') like '%,3,%' then 1 else 0 end) as FDD1800"))->where("dateId", $date)->get()->toArray()[0]['FDD1800'];
            $FDD_900M = ueCapability_daily::select(DB::raw("SUM(CASE WHEN CONCAT(',',eutra,',') like '%,8,%' then 1 else 0 end) as FDD900"))->where("dateId", $date)->get()->toArray()[0]['FDD900'];
            array_push($data, floatval(number_format(($FDD_1800M / $total * 100), 2)));
            array_push($data, floatval(number_format(($FDD_900M / $total * 100), 2)));
        }
        

        $categories           = [
                                 "FDD_1800M",
                                 "FDD_900M",
                                ];
        $series['data']       = $data;
        $series['name']       = '';
        $result['categories'] = $categories;
        $result['series']     = $series;
        $result['flag']       = 'true';
        return $result;

    }//end getChartData()


    /**
     * 显示终端能力图表
     *
     * @return array
     */
    
    public function getTddChartData(){
        $city = input::get("city");
        $date = Input::get("date");
        $series = array();
        $data   = array();
        $result = array();
        if(substr($city, 0, 3) == "CDR") {
            $userInfo = new UserInfo;
            $userInfo->setConnection($city);
            $total = $userInfo->distinct('imsi')->count('imsi');
            if ($total == 0) {
                $result['flag'] = 'error';
            } else {
                $total_38 = $userInfo->where('Band', 'like', '%LTE TDD BAND 38%')->count();
                $total_39 = $userInfo->where('Band', 'like', '%LTE TDD BAND 39%')->count();
                $total_40 = $userInfo->where('Band', 'like', '%LTE TDD BAND 40%')->count();
                $total_41 = $userInfo->where('Band', 'like', '%LTE TDD BAND 41%')->count();
 
                array_push($data, floatval(number_format(($total_38 / $total * 100), 2)));
                array_push($data, floatval(number_format(($total_39 / $total * 100), 2)));
                array_push($data, floatval(number_format(($total_40 / $total * 100), 2)));
                array_push($data, floatval(number_format(($total_41 / $total * 100), 2)));
            }
        }else {
            $total = ueCapability_daily::where("dateId", $date)->count();

            $total_38  = ueCapability_daily::select(DB::raw("sum(case when CONCAT(',',eutra,',') like '%,38,%' then 1 else 0 end) as Total38"))->where("dateId", $date)->get()->toArray()[0]['Total38'];
            $total_39  = ueCapability_daily::select(DB::raw("sum(case when CONCAT(',',eutra,',') like '%,39,%' then 1 else 0 end) as Total39"))->where("dateId", $date)->get()->toArray()[0]['Total39'];
            $total_40  = ueCapability_daily::select(DB::raw("sum(case when CONCAT(',',eutra,',') like '%,40,%' then 1 else 0 end) as Total40"))->where("dateId", $date)->get()->toArray()[0]['Total40'];
            $total_41  = ueCapability_daily::select(DB::raw("sum(case when CONCAT(',',eutra,',') like '%,41,%' then 1 else 0 end) as Total41"))->where("dateId", $date)->get()->toArray()[0]['Total41'];
            // $FDD_1800M = ueCapability_daily::select(DB::raw("sum(case when CONCAT(',',eutra,',') like '%,3,%' then 1 else 0 end) as FDD1800"))->where("dateId", $date)->get()->toArray()[0]['FDD1800'];
            // $FDD_900M = ueCapability_daily::select(DB::raw("SUM(CASE WHEN CONCAT(',',eutra,',') like '%,8,%' then 1 else 0 end) as FDD900"))->where("dateId", $date)->get()->toArray()[0]['FDD900'];
            array_push($data, floatval(number_format(($total_38 / $total * 100), 2)));
            array_push($data, floatval(number_format(($total_39 / $total * 100), 2)));
            array_push($data, floatval(number_format(($total_40 / $total * 100), 2)));
            array_push($data, floatval(number_format(($total_41 / $total * 100), 2)));
        }
        $sql = "select a.total_38/a.total as ratio_38,a.total_39/a.total as ratio_39,a.total_40/a.total as ratio_40,a.total_41/a.total as ratio_41 from 

        (select sum(case when CONCAT(',',eutra,',') like '%,38,%' then 1 else 0 end  ) as total_38,

               sum(case when CONCAT(',',eutra,',') like '%,39,%' then 1 else 0 end  ) as total_39,

        sum(case when CONCAT(',',eutra,',') like '%,40,%' then 1 else 0 end ) as total_40,

        sum(case when CONCAT(',',eutra,',') like '%,41,%' then 1 else 0 end  ) as total_41,

               count(*) as total

        from ueCapability where dateId='2017-12-11') a";

        $categories           = [
                                 "TDD_38",
                                 "TDD_39",
                                 "TDD_40",
                                 "TDD_41"
                                ];
        $series['data']       = $data;
        $series['name']       = '';
        $result['categories'] = $categories;
        $result['series']     = $series;
        $result['flag']       = 'true';
        return $result;
    }//end getTddChartData()

    /**
     * 获取能力分析图数据
     *
     * @return array
     */
    function getBandEutraChartData()
    {
        $city       = str_replace("CDR", "CTR", Input::get("city"));
        $series     = array();
        $categories = array();
        $data       = array();
        $result     = array();

        $ueCapability = new UeCapability;
        $ueCapability->setConnection($city);

        $totalCount = $ueCapability->distinct('imsi')->count('imsi');
        for ($i = 38; $i <= 41;$i++) {
            $value = $ueCapability->whereRaw('find_in_set('.$i.',eutra)')->distinct('imsi')->count('imsi');
            array_push($categories, $i);
            array_push($data, floatval(number_format(($value / $totalCount * 100), 2)));
        }

        $series['data']       = $data;
        $series['name']       = '';
        $result['categories'] = $categories;
        $result['series']     = $series;
        $result['flag']       = 'true';
        return $result;

    }//end getBandEutraChartData()


    /**
     * 获取能力分析表格数据
     *
     * @return string
     */
    function getBandEutraData()
    {
        $city       = str_replace("CDR", "CTR", Input::get("city"));

        $ueCapability = new UeCapability;
        $ueCapability->setConnection($city);

        $totalCount = $ueCapability->distinct('imsi')->count('imsi');
        $items = array();
        for ($i = 38; $i <= 41;$i++) {
            $value = $ueCapability->whereRaw('find_in_set('.$i.',eutra)')->distinct('imsi')->count('imsi');
            $tempRow          = array();
            $tempRow['EUTRA'] = $i;
            $tempRow['Value'] = $value;
            $tempRow['Total'] = $totalCount;
            $tempRow['Share'] = floatval(number_format(($value / $totalCount * 100), 2));
            array_push($items, $tempRow);
        }

        $result["records"] = $items;
        echo json_encode($result);

    }//end getBandEutraData()


    /**
     * 获取FGI能力分析图数据
     *
     * @return array
     */
    function getFGIChartData()
    {
        $city       = str_replace("CDR", "CTR", Input::get("city"));
        $series     = array();
        $categories = array();
        $data       = array();
        $result     = array();

        $ueCapability = new UeCapability;
        $ueCapability->setConnection($city);

        $totalCount = $ueCapability->distinct('imsi')->count('imsi');
        $items = array();
        for ($i = 1; $i <= 32;$i++) {
            $type = 'featGroupInd'.$i;
            $value = $ueCapability->where($type, 1)->distinct('imsi')->count('imsi');
            array_push($data, floatval(number_format(($value / $totalCount * 100), 2)));
            array_push($categories, $type);
        }
        $series['data']       = $data;
        $series['name']       = '';
        $result['categories'] = $categories;
        $result['series']     = $series;
        $result['flag']       = 'true';

        return $result;

    }//end getFGIChartData()


    /**
     * 获取FGI能力分析表数据
     *
     * @return string
     */
    function getFGIData()
    {
        $city   = str_replace("CDR", "CTR", Input::get("city"));
        $items  = array();
        $result = array();

        $ueCapability = new UeCapability;
        $ueCapability->setConnection($city);

        $totalCount = $ueCapability->distinct('imsi')->count('imsi');
        $items = array();
        for ($i = 1; $i <= 32;$i++) {
            $type = 'featGroupInd'.$i;
            $value = $ueCapability->where($type, 1)->distinct('imsi')->count('imsi');
            $tempRow          = array();
            $tempRow['FGI']   = $type;
            $tempRow['Value'] = floatval($value);
            $tempRow['Total'] = $totalCount;
            $tempRow['Share'] = floatval(number_format(($value / $totalCount * 100), 2));
            array_push($items, $tempRow);
        }

        $result["records"] = $items;
        echo json_encode($result);

    }//end getFGIData()


}//end class
