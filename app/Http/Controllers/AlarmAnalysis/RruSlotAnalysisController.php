<?php

/**
 * RruSlotAnalysisController.php
 *
 * @category AlarmAnalysis
 * @package  App\Http\Controllers\AlarmAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\AlarmAnalysis;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\FileUtil;
use App\Http\Requests;
use DateInterval;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Models\Mongs\Databaseconns;
use App\Models\Mongs\RruSlotSerialNumberRecord;
use App\Models\Mongs\RruSlotSerialNumberRecordTotal;
use App\Models\Mongs\Task;

/**
 * 板卡在网时间分析
 * Class RruSlotAnalysisController
 *
 * @category AlarmAnalysis
 * @package  App\Http\Controllers\AlarmAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class RruSlotAnalysisController extends Controller
{
    /**
     * 获得城市列表
     *
     * @return string JSON格式城市列表
     */
    public function getAllCity()
    {
        $cityClass = new DataBaseConnection();
        return $cityClass->getCityOptions();
    }// end getAllCity()

    /**
     *获取所有板卡串号列表
     *
     * @return array
     *
     */
    public function getAllSlot()
    {
        $date = new DateTime();
        $curDate = $date->format('ymd');

        $taskName = 'kget' . $curDate;
        $count = Task::where('taskName', $taskName)->count();
        $result = array();
        if ($count == 0) {
            $date->sub(new DateInterval('P1D'));
        }
        $currTime = $date->format('Y-m-d');
        $result['currTime'] = $currTime;
        $currSlotNum = RruSlotSerialNumberRecordTotal::where('kgetTime', $currTime)->count();
        $result['currSlotNum'] = $currSlotNum;

        $limit = Input::get('limit', 50);
        $cities = Input::get('city');
        $subNetworkArr = $this->getSubNetworkArr($cities);
        $rows = RruSlotSerialNumberRecord::whereIn('subNetwork', $subNetworkArr)->paginate($limit)->toArray();

        if ($rows['total'] > 0) {
            $keys = array_keys($rows['data'][0]);
            $titles = array();
            foreach ($keys as $key) {
                array_push($titles, trans('message.slot.' . $key));
            }
            $result['keys'] = $keys;
            $result['titles'] = $titles;
        }
        $data = array();
        $data['total'] = $rows['total'];
        $data['records'] = $rows['data'];
        $result['data'] = $data;

        return $result;
    }//end getAllSlot()

    /**
     *
     *获取子网whereIn后面的条件
     *
     */
    private function getSubNetworkArr($cityArrs)
    {
        $subNetworkArr = array();
        $subNetworkStr = '';
        foreach ($cityArrs as $city) {
            $subNetworks = Databaseconns::select(DB::raw("if(subNetworkFDD != '',CONCAT(subNetwork,',',subNetworkFDD),subNetwork) subNetwork"))->where('cityChinese', $city)->get()->toArray();
            foreach ($subNetworks as $subNetwork) {
                $subNetworkStr = $subNetworkStr . $subNetwork['subNetwork'] . ',';
            }
            $subNetworkArr = explode(',', substr($subNetworkStr, 0, -1));
        }
        return $subNetworkArr;
    }//end exportAllSlot()

    /**
     * 导出所有板卡串号列表
     *
     * @return array
     */
    public function exportAllSlot()
    {
        $cityArrs = Input::get('city');
        $subNetworkArr = $this->getSubNetworkArr($cityArrs);
        $rows = RruSlotSerialNumberRecord::whereIn('subNetwork', $subNetworkArr)->get()->toArray();
        $result = array();
        $fileName = "files/板卡串号记录_" . date('YmdHis') . ".csv";;
        if (count($rows) > 0) {
            $keys = array_keys($rows[0]);
            $titles = array();
            foreach ($keys as $key) {
                array_push($titles, trans('message.slot.' . $key));
            }
            $column = implode(',', $titles);
            $fileUtil = new FileUtil();
            $fileUtil->resultToCSV2($column, $rows, $fileName);
            $result['fileName'] = $fileName;
            $result['result'] = 'true';
        }
        return $result;
    }//end getDisappearSlot()

    /**
     * 获取与前一天kget相比消失的板卡串号列表
     *
     * @return array
     *
     */
    public function getDisappearSlot()
    {
        $disppagelayStart = Input::get('page', 1);
        $displayLength = Input::get('limit', 50);
        $offset = ($disppagelayStart - 1) * $displayLength;
        $limit = " limit $offset,$displayLength ";
        $cities = Input::get('city');
        $dbc = new DataBaseConnection();
        $subNetwork = '';
        foreach ($cities as $city) {
            $subNetwork .= $dbc->getSubNets($city) . ',';
        }
        $subNetwork = substr($subNetwork, 0, -1);
        $subNetworkArr = $this->getSubNetworkArr($cities);

        $total = RruSlotSerialNumberRecord::whereNull('是否在网')->whereIn('subNetwork', $subNetworkArr)->count();
        $rows = DB::select("select * from rruSlotSerialNumberRecord where `是否在网` is null and subNetwork in (" . $subNetwork . ") " . $limit);
        $result = array();
        if (count($rows) > 0) {
            $keys = array_keys((array)$rows[0]);
            $titles = array();
            foreach ($keys as $key) {
                array_push($titles, trans('message.slot.' . $key));
            }
            $result['keys'] = $keys;
            $result['titles'] = $titles;
        }
        $data = array();
        $data['total'] = $total;
        $data['records'] = $rows;
        $result['data'] = $data;
        return $result;
    }//end exportDisappearSlot()

    /**
     * 导出与前一天kget相比消失的板卡串号列表
     *
     * @return array
     *
     */
    function exportDisappearSlot()
    {
        $cities = Input::get('city');
        $dbc = new DataBaseConnection();
        $subNetwork = '';
        foreach ($cities as $city) {
            $subNetwork .= $dbc->getSubNets($city) . ',';
        }
        $subNetwork = substr($subNetwork, 0, -1);
        $rows = DB::select("select * from rruSlotSerialNumberRecord where `是否在网` is null and subNetwork in (" . $subNetwork . ") ");
        $result = array();
        $fileName = "files/消失板卡串号记录_" . date('YmdHis') . ".csv";;
        if (count($rows) > 0) {
            $keys = array_keys((array)$rows[0]);
            $titles = array();
            foreach ($keys as $key) {
                array_push($titles, trans('message.slot.' . $key));
            }
            $column = implode(',', $titles);
            $fileUtil = new FileUtil();
            $fileUtil->resultToCSV2($column, $rows, $fileName);
            $result['fileName'] = $fileName;
            $result['result'] = 'true';
        }
        return $result;
    }// end getSlotTrendChart()

    /**
     *获取对应板卡串号的趋势图
     *
     * @return array
     *
     */
    function getSlotTrendChart()
    {
        $serialNumber = Input::get('serialNumber');
        $rows = DB::select('select meContext, kgetTime, productData_productionDate from (select meContext, kgetTime, DATE_FORMAT(productData_productionDate,"%Y-%m-%d") productData_productionDate from rruSlotSerialNumberRecordTotal 
                    where productData_serialNumber=:productData_serialNumber order by kgetTime)t group By meContext', ['productData_serialNumber' => $serialNumber]);
        $result = array();
        $categories = array();
        $series = array();
        $series['name'] = 'time';
        $data = array();
        if (count($rows) > 0) {
            $productionDate = $rows[0]->productData_productionDate;
            $arr = array();
            array_push($categories, $productionDate);
            $arr['x'] = 0;
            $arr['y'] = 0;
            $arr['name'] = '';
            array_push($data, $arr);
            for ($i = 0; $i < count($rows); $i++) {
                $arr = array();
                array_push($categories, $rows[$i]->kgetTime);
                $arr['x'] = $i + 1;
                $arr['y'] = 0;
                $arr['name'] = $rows[$i]->meContext;
                array_push($data, $arr);
            }
        }
        $series['data'] = $data;
        $result['categories'] = $categories;
        $result['series'] = $series;
        return $result;
    }// end getOneSlotInfo()

    /**
     *获取该串号板卡信息
     *
     * @return array
     */
    public function getOneSlotInfo()
    {
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 10;
        $serialNumber = Input::get('serialNumber');
        $rows = RruSlotSerialNumberRecordTotal::orderBy('kgetTime', 'asc')->where('productData_serialNumber', $serialNumber)->paginate($limit, array('*'), 'page', $page)->toArray();
        $result = array();
        if ($rows['total'] > 0) {
            $keys = array_keys($rows['data'][0]);
            $titles = array();
            foreach ($keys as $key) {
                array_push($titles, trans('message.slot.' . $key));
            }
            $result['keys'] = $keys;
            $result['titles'] = $titles;
        }
        $data = array();
        $data['total'] = $rows['total'];
        $data['records'] = $rows['data'];
        $result['data'] = $data;
        return $result;
    }// end exportOneSolt()

    /**
     * 导出该串号板卡信息
     * @return array
     */
    public function exportOneSolt()
    {
        $serialNumber = Input::get('serialNumber');
        $rows = RruSlotSerialNumberRecordTotal::orderBy('kgetTime', 'asc')->where('productData_serialNumber', $serialNumber)->get()->toArray();
        $result = array();
        $fileName = "files/板卡串号记录_" . $serialNumber . "_" . date('YmdHis') . ".csv";;
        if (count($rows) > 0) {
            $keys = array_keys($rows[0]);
            $titles = array();
            foreach ($keys as $key) {
                array_push($titles, trans('message.slot.' . $key));
            }
            $column = implode(',', $titles);
            $fileUtil = new FileUtil();
            $fileUtil->resultToCSV2($column, $rows, $fileName);
            $result['fileName'] = $fileName;
            $result['result'] = 'true';
        }
        return $result;
    }// end getSubNetworkArr()

}