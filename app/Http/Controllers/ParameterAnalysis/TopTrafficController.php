<?php

namespace App\Http\Controllers\ParameterAnalysis;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\AutoKPI\TopTraffic;
use App\Http\Controllers\Utils\FileUtil;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Common\DataBaseConnection;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
/**
 * 小区自忙时分析
 */
class TopTrafficController extends Controller
{
    //

    public function getTableData()
    {
    	$city 	    = Input::get("city",'');
    	$startTime  = Input::get("startTime",'');
    	$endTime    = Input::get("endTime",'');
    	$dimensions = Input::get("dimensions",'');
    	$dsn = new DataBaseConnection();

    	$conname = $dsn->getENCity($city);
    	$result = array();
    	$text ="上下行总流量MB,PDCCH利用率,上行利用率PUSCH,下行PRB平均利用率,每ERAB流量KB,上行流量MB,下行流量MB,有数据传输的RRC数";
    	if($dimensions=="day"){
    	
    		$rows=TopTraffic::where('city',$conname)->whereBetween('day_id',[$startTime,$endTime])->get()->toArray();
    		$title="日期,小时,城市,子网,小区,".$text;//用于文件标题
    		$fileName="files/小区自忙时分析_天_".$conname."_".date('YmdHis').".csv";
    		
    	}elseif($dimensions=="dayGroup"){

            $rows = TopTraffic::select(array(\DB::raw("'DayGroup',city,subNetwork,cell,上下行总流量MB,PDCCH利用率,上行利用率PUSCH,下行PRB平均利用率,每ERAB流量KB,上行流量MB,下行流量MB,有数据传输的RRC数")))->where('city',$conname)->whereBetween('day_id',[$startTime,$endTime])->groupBy('city','subNetwork','cell')->get()->toArray();
            $title="天组,城市,子网,小区,".$text;//用于文件标题
            $fileName="files/小区自忙时分析_天组_".$conname."_".date('YmdHis').".csv";
        }

    	$result['rows']=$rows;
    	$result['text']=$text;
    	$result['fileName'] = $fileName;
    	$fileUtil = new FileUtil();
        $fileUtil->resultToCSV2($title, $rows, $fileName);

        if(count($rows)>1000){
            $newRows = array();
            $i=1;
            foreach ($rows as $key => $value) {
                $newRows[] = $value;
                if($i==1000){
                    break;
                }
                $i++;
            }
           $result['rows']=$newRows;
        }


    	echo json_encode($result);
    }
}
