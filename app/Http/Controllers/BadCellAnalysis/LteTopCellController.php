<?php

/**
 * LteTopCellController.php
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
use App\Http\Controllers\Common\MyRedis;

/**
 * Lte Top小区
 * Class LteTopCellController
 *
 * @category BadCellAnalysis
 * @package  App\Http\Controllers\BadCellAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class LteTopCellController extends MyRedis
{


    /**
     * 获得城市列表
     *
     * @return void
     */
    public function getCities()
    {
        $cityClass = new DataBaseConnection();
    
        return $cityClass->getCityOptions();
 

    }//end getCitys()




    /**
     * 获得单城市数据时间(天)列表
     *
     * @return void
     */
    public function getCityDate()
    {   

        $dbname[] = input::get("dataBase");
        $dbc    = new DataBaseConnection();  
        $db     = $dbc->getDB('autokpi', 'AutoKPI'); 
        $cityChinese = $dbc->getConnCity($dbname);
   
        $sql   = "select distinct day_id from Top_cell_quarter where city='$cityChinese[0]' ";
        $this->type = $cityChinese[0].":Top_cell_quarter";
        echo json_encode($this->getValue($db, $sql));

    }//end getCityDate()


    public function getParam(){
        $dbc    = new DataBaseConnection();  
        $type       = Input::get("survey");
        $city       = Input::get("city");
        $citycon    = $dbc->getCityByCityChinese($city)[0]->connName;
        $timeType   = Input::get("timeType");
        $startTime  = Input::get("startTime");
        $endTime    = Input::get("endTime");
        $hour       = Input::get("hour");
        $quarter    = Input::get("quarter");


        $Sqlresult = $this->CreateSql($type,$citycon,$timeType,$startTime,$endTime,$hour,$quarter);
        return $Sqlresult;
    }
    public function getAllData()
    {

        $Sqlresult = $this->getParam();
        $result = array();
        $dbc    = new DataBaseConnection(); 
        $db     = $dbc->getDB('autokpi', 'AutoKPI'); 
        
        $result['text'] = $Sqlresult['text'];
        $sql = $Sqlresult['sql']." limit 1000";

        $rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $item = array();
        foreach ($rows as $key => $value) {
              $item[]= $value;
        }

        $result['rows'] = $item;
        return json_encode($result);


    }

    public function downloadFile(){

        $type   = input::get('survey');
        $Sqlresult = $this->getParam();
        $result = array();
        $dbc    = new DataBaseConnection(); 
        $db     = $dbc->getDB('autokpi', 'AutoKPI'); 
        
        $result['text'] = $Sqlresult['text'];
        $sql = $Sqlresult['sql'];
        // echo $sql;exit;
        $rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);


        $filename ="common/files/".$type.date("ymdhis").".csv";
        $csvContent = mb_convert_encoding($result['text']."\n", 'GBK');
        $fp         = fopen($filename, "w");
        fwrite($fp, $csvContent);
        if($rows){	
	        foreach ($rows as $key => $value) {
	             fputcsv($fp, $value);
	        }
        }

        fclose($fp);

        return json_encode($filename);
    }

    public function CreateSql($type,$city,$timeType,$startTime,$endTime,$hour,$quarter){

        $dbc    = new DataBaseConnection();  

        $db     = $dbc->getDB('mongs','mongs');
        $sql    = "select * from LteCell where type='".$type."'";
        $rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $sqlstr ='';
        $index =0;
        foreach($rows as $value){
            $sqlstr.= 'a.`'.$value['LteName'].'`'.$value['operator']."'".$value['value']."'";
            $index++;
            if($index<count($rows)){
                $sqlstr.= " and ";
            }
        }



        $sqlstr = "sum(case when $sqlstr then 1 else 0 end) as 出现次数";

         $db     = $dbc->getDB('autokpi','AutoKPI');
        $sql = "select day_id,hour_id,quarter_id from Top order by id desc limit 1";

        $rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $sqlTime = " where city='$city' and day_id='".$rows[0]['day_id']."' and hour_id ='".$rows[0]['hour_id']."' and quarter_id='".$rows[0]['quarter_id']."'";




        if($type == "LowCell"){
            $sqlSelect  = "$sqlstr,sum(a.`ERAB尝试次数`),sum(a.`ERAB失败次数`),sum(a.`RRC尝试次数`),sum(a.`RRC失败次数`), cast(avg(a.`无线接通率`) as decimal(10,2)) as 无线接通率,b.`ERAB尝试次数`,b.`ERAB失败次数`,b.`ERAB成功率`, b.`RRC尝试次数`,b.`RRC失败次数`,b.`RRC成功率`";
            $sqlLeft 	=  "(select cell,`ERAB尝试次数`,`ERAB失败次数`,`ERAB成功率`, `RRC尝试次数`,`RRC失败次数`,`RRC成功率` from Top $sqlTime)";
            $sqlText	= "出现次数,E-RAB尝试次数(筛选时间内求和),E-RAB失败次数(筛选时间内求和),RRC尝试次数(筛选时间内求和),RRC失败次数(筛选时间内求和),无线接通率(筛选时间内),E-RAB尝试次数,E-RAB失败次数,E-RAB成功率,RRC尝试次数,RRC失败次数,RRC成功率"  ;
        }elseif($type == "HighCell"){
            $sqlSelect  = "$sqlstr,sum(a.`无线掉线次数`),cast(avg(a.`无线掉线率`) as decimal(10,2)) as 无线掉线率和,b.`无线掉线次数`,b.`无线掉线率`";
            $sqlLeft	= "(select cell,`无线掉线次数`,`无线掉线率` from Top $sqlTime)";
            $sqlText	= "出现次数,无线掉线次数(筛选时间内求和),无线掉线率(筛选时间内),无线掉线次数,无线掉线率"  ;
        }elseif($type == "BadCell"){
            $sqlSelect  = "$sqlstr,sum(a.`准备切换尝试次数`),sum(a.`准备切换成功次数`),sum(a.`执行切换尝试次数`),sum(a.`执行切换成功次数`),cast(avg(a.`切换成功率`) as decimal(10,2)) as 切换成功率平均,b.`准备切换尝试次数`,b.`准备切换成功次数`,b.`执行切换尝试次数`,b.`执行切换成功次数`,b.`切换成功率`";
            $sqlLeft 	= "(select cell,`准备切换尝试次数`,`准备切换成功次数`,`执行切换尝试次数`,`执行切换成功次数`,`切换成功率` from Top $sqlTime)";
            $sqlText 	= "出现次数,准备切换尝试次数(筛选时间内求和),准备切换成功次数(筛选时间内求和),执行切换尝试次数(筛选时间内求和),执行切换成功次数(筛选时间内求和),切换成功率(筛选时间内),准备切换尝试次数,准备切换成功次数,执行切换尝试次数,执行切换成功次数,切换成功率" ;
        }elseif($type == "InterfereCell"){
            $sqlSelect  = "$sqlstr,cast(avg(a.`PUSCH上行干扰电平`) as decimal(10,2)) as PUSCH上行干扰电平和,b.`PUSCH上行干扰电平`";
            $sqlLeft 	= "(select cell,`PUSCH上行干扰电平` from Top $sqlTime)";
            $sqlText 	= "出现次数,PUSCH上行干扰电平(筛选时间内求平均),PUSCH上行干扰电平" ;
        }elseif($type == "RrcCell"){
            $sqlSelect  = "$sqlstr,cast(avg(a.`RRC连接最大用户数`) as decimal(10,2)) as RRC连接最大用户数和,b.RRC连接最大用户数";
            $sqlLeft 	= "(select cell,RRC连接最大用户数 from Top $sqlTime)";
            $sqlText 	= "出现次数,RRC连接最大用户数(筛选时间内求平均),RRC连接最大用户数" ;
        }
    
        if($hour){
            $sqlHour = implode($hour, ",");
        }else{
            $sqlHour = implode(range(0,23,1),",");
        }
        if($quarter){
            $sqlQuarter = implode($quarter, ",");
        }else{
            $sqlQuarter = implode(range(0,45,15),",");
        }

        // $sql = "select count(*) from Top_cell_quarter where day_id>='$startTime' and day_id <='$endTime'";

        // print_r($sql);exit;

        if($timeType =="day"){

            // $sql = "select day_id,city,subNetwork,cell,count(quarter_id), $sqlSelect from Top_cell_quarter where day_id>='$startTime' and day_id<='$endTime' and city='$city' group by day_id,city,cell order by day_id,cell,quarter_id desc";
            $sql = "select day_id,city,subNetwork,a.cell,count(quarter_id), $sqlSelect from Top_cell_quarter a left join $sqlLeft b on a.cell=b.cell where day_id>='$startTime' and day_id<='$endTime' and city='$city' group by day_id,city,a.cell order by day_id,a.cell,quarter_id desc";
            $sqlText ="日期,城市,子网,小区名,筛选时间内15分钟个数,".$sqlText;
        }else if($timeType == "daygroup"){

        	$sql = "select '$startTime 到 $endTime',city,subNetwork,a.cell,count(quarter_id),$sqlSelect from Top_cell_quarter a left join $sqlLeft b on a.cell=b.cell where day_id>='$startTime' and day_id<='$endTime' and city='$city' group by city,a.cell order by day_id,a.cell,quarter_id desc";
            $sqlText ="天组,城市,子网,小区名,筛选时间内15分钟个数,".$sqlText;
        }else if($timeType == "hour"){
            $sql = "select day_id,hour_id,city,subNetwork,a.cell,count(quarter_id),$sqlSelect from Top_cell_quarter a left join $sqlLeft b on a.cell=b.cell where day_id >='$startTime' and day_id <= '$endTime' and city='$city' and hour_id in($sqlHour) group by day_id,hour_id,a.cell order by day_id,hour_id,a.cell,quarter_id desc";
            $sqlText ="日期,小时,城市,子网,小区名,筛选时间内15分钟个数,".$sqlText;


        }else if($timeType == "hourgroup"){
            $num = count(explode(",", $sqlHour))*4;
        	$sql = "select day_id,'$sqlHour' as 小时组,city,subNetwork,a.cell,count(quarter_id),$sqlSelect from Top_cell_quarter a left join $sqlLeft b on a.cell=b.cell where day_id >='$startTime' and day_id <= '$endTime' and city='$city' and hour_id in ($sqlHour)  group by day_id,city,a.cell order by day_id,a.cell,quarter_id desc";

             $sqlText ="日期,小时组,城市,子网,小区名,筛选时间内15分钟个数,".$sqlText;

        }else if($timeType == "quarter"){
            $num = count(explode(",", $sqlQuarter));
        	$sql = "select day_id,hour_id,quarter_id,city,subNetwork,a.cell,count(quarter_id),$sqlSelect from Top_cell_quarter a left join $sqlLeft b on a.cell=b.cell where day_id >='$startTime' and city='$city' and day_id <= '$endTime' and hour_id in ($sqlHour) and quarter_id in ($sqlQuarter) group by day_id,hour_id,quarter_id,city,a.cell order by day_id,hour_id,quarter_id,a.cell";

             $sqlText ="日期,小时,分钟,城市,子网,小区名,筛选时间内15分钟个数,".$sqlText;
        }
        $result = array();

        $result['sql'] =$sql;
        $result['text'] =$sqlText;

        return $result;





    }

    /**
     * 写入CSV文件
     *
     * @param array  $result   Baseline模板内容
     * @param string $filename CSV文件名
     *
     * @return void
     */
    protected function resultToCSV2($result, $filename)
    {
        $csvContent = mb_convert_encoding($result['text']."\n", 'GBK');
        $fp         = fopen($filename, "w");
        fwrite($fp, $csvContent);
        if($result['rows']){

            foreach ($result['rows'] as $row) {
                fputcsv($fp, $row);
            }
        }

        fclose($fp);

    }//end resultToCSV2()

}//end class
