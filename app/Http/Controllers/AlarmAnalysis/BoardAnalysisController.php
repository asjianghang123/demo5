<?php

/**
* BoardAnalysisController.php
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
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use App\Models\Mongs\SiteLte;
use Illuminate\Support\Facades\Auth;

use App\Models\Mongs\Databaseconns;
use App\Models\Mongs\SlotSerialNumberRecord;
use App\Models\Mongs\SlotSerialNumberRecordLatest;
use App\Models\Mongs\SlotSerialNumberRecordTotal;
use App\Models\Mongs\RruSlotSerialNumberRecord;
use App\Models\Mongs\RruSlotSerialNumberRecordTotal;
use App\Models\Mongs\Task;

/**
 * 板卡在网时间分析
 * Class BoardAnalysisController
 *
 * @category AlarmAnalysis
 * @package  App\Http\Controllers\AlarmAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class BoardAnalysisController extends Controller
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
     *@return array
     *
     */
    public function getAllSlot()
    {

        $date          = new DateTime();
        $curDate       = $date->format('ymd');
        
        $type          = input::get('type');

        $taskName = 'kget'.$curDate;
        $count = Task::where('taskName', $taskName)->count();
        $result = array();
        if ($count == 0) {
            $date->sub(new DateInterval('P1D'));
            $taskName  = "kget".$date->format('ymd');
        }
        $currTime = $date->format('Y-m-d');
        $result['currTime']    = $currTime;

      
         
        $page          = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit         = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 10;
        $cityChArrs      = Input::get('city');
        $dbc = new DataBaseConnection();
        $cityArr = array();
        foreach ($cityChArrs as $cityCh) {
            $city = $dbc->getCityByCityChinese($cityCh)[0]->connName;
            array_push($cityArr, $city);
        }
        $cityStr = "('".implode("','", $cityArr)."')";
        if ($type =='du') {
            $currSlotNum       = SlotSerialNumberRecordTotal::where('kgetTime', $currTime)->count();
            $rows              = SlotSerialNumberRecord::whereRaw("meContext in (select meContext from $taskName.ENodeBFunction where city in  $cityStr)")
                                                    ->paginate($limit, array('*'), 'page', $page)->toArray();
        } else if ($type == "rru") {
                $currSlotNum      = RruSlotSerialNumberRecordTotal::where('kgetTime', $currTime)->count();
                $rows             = RruSlotSerialNumberRecord::whereRaw("meContext in (select meContext from $taskName.ENodeBFunction where city in  $cityStr)")
                                                        ->paginate($limit, array('*'), 'page', $page)->toArray();
        }
        $result['currSlotNum'] = $currSlotNum;

        // $rows   = SlotSerialNumberRecord::whereIn('subNetwork', $subNetworkArr)->paginate($limit,array('*'),'page',$page)->toArray();
        
        if ($rows['total'] > 0) {
            $keys             = array_keys($rows['data'][0]);
            $titles = array();
            foreach ($keys as $key) {
                array_push($titles, trans('message.slot.'.$key));
            }
            $result['keys'] = $keys;
            $result['titles'] = $titles;
        }
        $data = array();
        $data['total']   = $rows['total'];
        $data['records'] = $rows['data'];
        $result['data'] = $data;

        return $result;
    }//end getAllSlot()

    /**
     *导出所有板卡串号列表
     *
     *@return array
     */
    public function exportAllSlot()
    {   

        $date          = new DateTime();
        $curDate       = $date->format('ymd');
        $taskName = 'kget'.$curDate;
        $count = Task::where('taskName', $taskName)->count();
        $result = array();
        if ($count == 0) {
            $date->sub(new DateInterval('P1D'));
            $taskName  = "kget".$date->format('ymd');
        }
        $cityChArrs      = Input::get('city');
        $dbc = new DataBaseConnection();
        $cityArr = array();
        foreach ($cityChArrs as $cityCh) {
            $city = $dbc->getCityByCityChinese($cityCh)[0]->connName;
            array_push($cityArr, $city);
        }
        $cityStr = "('".implode("','", $cityArr)."')";

        $type     = input::get('type');

        if ($type == 'du') {
            $rows     = SlotSerialNumberRecord::whereRaw("meContext in (select meContext from $taskName.ENodeBFunction where city in  $cityStr)")->get()->toArray();
        } else if ($type == "rru") {
            $rows     = RruSlotSerialNumberRecord::whereRaw("meContext in (select meContext from $taskName.ENodeBFunction where city in  $cityStr)")->get()->toArray();
        }

        $column   = '';
        $result   = array();
        $fileName = "files/板卡串号记录_".date('YmdHis').".csv";;
        if (count($rows) > 0) {
            $keys     = array_keys($rows[0]);
            $titles = array();
            foreach ($keys as $key) {
                array_push($titles, trans('message.slot.'.$key));
            }
            $column   = implode(',', $titles);
            $fileUtil = new FileUtil();
            $fileUtil->resultToCSV2($column, $rows, $fileName);
            $result['fileName'] = $fileName;
            $result['result']   = 'true';
        }
        return $result;
    }//end exportAllSlot()
    /**
     *获取与前一天kget相比消失的板卡串号列表
     *
     *@return array
     *
     */
    public function getDisappearSlot()
    {
        $date          = new DateTime();
        $curDate       = $date->format('ymd');
        $taskName = 'kget'.$curDate;
        $count = Task::where('taskName', $taskName)->count();
        $result = array();
        if ($count == 0) {
            $date->sub(new DateInterval('P1D'));
            $taskName  = "kget".$date->format('ymd');
        }
        $disppagelayStart          = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $displayLength         = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 10;
        $offset = ($disppagelayStart - 1) * $displayLength;
        $limit = " limit $offset,$displayLength ";
        $type          = input::get('type');
        $cityChArrs      = Input::get('city');
        $dbc = new DataBaseConnection();
        $cityArr = array();
        foreach ($cityChArrs as $cityCh) {
            $city = $dbc->getCityByCityChinese($cityCh)[0]->connName;
            array_push($cityArr, $city);
        }
        $cityStr = "('".implode("','", $cityArr)."')";
        
        if ($type == "du") {
            $total = SlotSerialNumberRecord::whereNull('是否在网')
                                              ->whereRaw("meContext in (select meContext from $taskName.ENodeBFunction where city in  $cityStr)")->count();
            $rows  = DB::select("select * from slotSerialNumberRecord where `是否在网` is null and meContext in (select meContext from $taskName.ENodeBFunction where city in  $cityStr) ". $limit);
        } else if($type == "rru") {
             $total = RruSlotSerialNumberRecord::whereNull('是否在网')
                                             ->whereRaw("meContext in (select meContext from $taskName.ENodeBFunction where city in  $cityStr)")->count();
             $rows  = DB::select("select * from rruSlotSerialNumberRecord where `是否在网` is null and meContext in (select meContext from $taskName.ENodeBFunction where city in  $cityStr) ". $limit);
        }
      
    
        $result = array();
        if (count($rows) > 0) {
            $keys             = array_keys((array)$rows[0]);
            $titles = array();
            foreach ($keys as $key) {
                array_push($titles, trans('message.slot.'.$key));
            }
            $result['keys'] = $keys;
            $result['titles'] = $titles;
        }
        $data = array();
        $data['total']   = $total;
        $data['records'] = $rows;
        $result['data'] = $data;
        return $result;
    }//end getDisappearSlot()
    /**
     *导出与前一天kget相比消失的板卡串号列表
     *
     *@return array
     *
     */
    function exportDisappearSlot()
    {
        $date          = new DateTime();
        $curDate       = $date->format('ymd');
        $taskName = 'kget'.$curDate;
        $count = Task::where('taskName', $taskName)->count();
        $result = array();
        if ($count == 0) {
            $date->sub(new DateInterval('P1D'));
            $taskName  = "kget".$date->format('ymd');
        }
        $type          = Input::get('type');
        $cityChArrs      = Input::get('city');
        $dbc = new DataBaseConnection();
        $cityArr = array();
        foreach ($cityChArrs as $cityCh) {
            $city = $dbc->getCityByCityChinese($cityCh)[0]->connName;
            array_push($cityArr, $city);
        }
        $cityStr = "('".implode("','", $cityArr)."')";

        if ($type == "du") {
            $rows = DB::select("select * from slotSerialNumberRecord where `是否在网` is null and meContext in (select meContext from $taskName.ENodeBFunction where city in  $cityStr) ");
        } else if ($type == "rru") {
            $rows = DB::select("select * from rruSlotSerialNumberRecord where `是否在网` is null and meContext in (select meContext from $taskName.ENodeBFunction where city in  $cityStr) ");
        }

      
        $column   = '';
        $result   = array();
        $fileName = "files/消失板卡串号记录_".date('YmdHis').".csv";;
        if (count($rows) > 0) {
            $keys     = array_keys((array)$rows[0]);
            $titles = array();
            foreach ($keys as $key) {
                array_push($titles, trans('message.slot.'.$key));
            }
            $column   = implode(',', $titles);
            $fileUtil = new FileUtil();
            $fileUtil->resultToCSV2($column, $rows, $fileName);
            $result['fileName'] = $fileName;
            $result['result']   = 'true';
        }
        return $result;
    }//end exportDisappearSlot()

    /**
     *获取对应板卡串号的趋势图
     *
     *@return array
     *
     */
    function getSlotTrendChart()
    {
        $serialNumber = Input::get('serialNumber');
        $rows = DB::select('select meContext, kgetTime, productData_productionDate from (select meContext, kgetTime, DATE_FORMAT(productData_productionDate,"%Y-%m-%d") productData_productionDate from slotSerialNumberRecordTotal 
                    where productData_serialNumber=:productData_serialNumber order by kgetTime)t group By meContext', ['productData_serialNumber'=>$serialNumber]);


        if (empty($rows)) {
            $rows = DB::select('select meContext, kgetTime, productData_productionDate from (select meContext, kgetTime, DATE_FORMAT(productData_productionDate,"%Y-%m-%d") productData_productionDate from rruSlotSerialNumberRecordTotal 
                    where productData_serialNumber=:productData_serialNumber order by kgetTime)t group By meContext', ['productData_serialNumber'=>$serialNumber]);
        }


        $result = array();
        $categories = array();
        $series = array();
        $series['name'] = 'time';
        $data = array();
        if (count($rows) >0 ) {
            $productionDate = $rows[0]->productData_productionDate;
            $arr = array();
            array_push($categories, $productionDate);
            $arr['x'] = 0;
            $arr['y'] = 0;
            $arr['name'] = '';
            array_push($data, $arr);
            for ($i=0; $i < count($rows); $i++) { 
                $arr = array();
                array_push($categories, $rows[$i]->kgetTime);
                $arr['x'] = $i+1;
                $arr['y'] = 0;
                $arr['name'] = $rows[$i]->meContext;
                array_push($data, $arr);
            }
        }
        $series['data'] = $data;
        $result['categories'] = $categories;
        $result['series'] = $series;
        return $result;
    }// end getSlotTrendChart()
    /**
     *获取该串号板卡信息
     *
     *@return array
     */
    public function getOneSlotInfo()
    {
        $page          = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit         = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 10;
        $serialNumber = Input::get('serialNumber');

        $type = input::get('type');

        if ($type == "du") {
            $rows = SlotSerialNumberRecordTotal::orderBy('kgetTime', 'asc')
                                                ->where('productData_serialNumber', $serialNumber)
                                                ->paginate($limit, array('*'), 'page', $page)
                                                ->toArray();

        } else if($type =="rru") {
               $rows = RruSlotSerialNumberRecordTotal::orderBy('kgetTime', 'asc')
                                                        ->where('productData_serialNumber', $serialNumber)
                                                        ->paginate($limit, array('*'), 'page', $page)
                                                        ->toArray();

        }

        $result = array();
        if ($rows['total'] > 0) {
            $keys             = array_keys($rows['data'][0]);
            $titles = array();
            foreach ($keys as $key) {
                array_push($titles, trans('message.slot.'.$key));
            }
            $result['keys'] = $keys;
            $result['titles'] = $titles;
        }
        $data = array();
        $data['total']   = $rows['total'];
        $data['records'] = $rows['data'];
        $result['data'] = $data;
        return $result;
    }// end getOneSlotInfo()
    /**
     *导出该串号板卡信息
     *@return array
     */
    public function exportOneSolt()
    {
        $serialNumber = Input::get('serialNumber');
        $type         = Input::get('type');

        if ($type == "du") {
            $rows = SlotSerialNumberRecordTotal::orderBy('kgetTime', 'asc')->where('productData_serialNumber', $serialNumber)->get()->toArray();
        } else if ($type == "rru") {
            $rows = RruSlotSerialNumberRecordTotal::orderBy('kgetTime', 'asc')->where('productData_serialNumber', $serialNumber)->get()->toArray();
        }

        $column   = '';
        $result   = array();
        $fileName = "files/板卡串号记录_".$serialNumber."_".date('YmdHis').".csv";;
        if (count($rows) > 0) {
            $keys     = array_keys($rows[0]);
            $titles = array();
            foreach ($keys as $key) {
                array_push($titles, trans('message.slot.'.$key));
            }
            $column   = implode(',', $titles);
            $fileUtil = new FileUtil();
            $fileUtil->resultToCSV2($column, $rows, $fileName);
            $result['fileName'] = $fileName;
            $result['result']   = 'true';
        }
        return $result;
    }// end exportOneSolt()
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
            $subNetworks   = Databaseconns::select(DB::raw("if(subNetworkFDD != '',if(subNetworkNbiot!='',CONCAT(subNetwork,',',subNetworkFDD,',',subNetworkNbiot),CONCAT(subNetwork,',',subNetworkFDD)),if(subNetworkNbiot!='',CONCAT(subNetwork,',',subNetworkNbiot),subNetwork)) subNetwork"))->where('cityChinese', $city)->get()->toArray();
            foreach ($subNetworks as $subNetwork) {
                $subNetworkStr = $subNetworkStr.$subNetwork['subNetwork'].',';
            }
        }
        $subNetworkArr = explode(',', substr($subNetworkStr, 0, -1));
        return $subNetworkArr;
    }// end getSubNetworkArr()

}