<?php
/**
 * Created by PhpStorm.
 * User: ericsson
 * Date: 2018/4/24
 * Time: 11:18
 */

namespace App\Jobs\NewSitesChecker;
use App\Http\Controllers\Common\DataBaseConnection;
use App\Models\SiteCheck\SiteStatus;

use App\Models\Mongs\Databaseconns;
use PDO;
use DateTime;
trait KPIChecker
{
    /**
     * KPI check.
     *
     * @param $enbId
     */
    public static function doKPICheck($enbId)
    {   
        self::checkCounter($enbId);
        self::checkAccess($enbId);
        self::checkLost($enbId);
        // print_r($checkAccess);exit;
         self::checkHo($enbId);

    }


    public static function insert($enbId,$result,$type,$check_item){


        $builder = SiteStatus::where('enbId', $enbId)->where('check_item', $check_item);
        //判断检查条目是否存在


        $sign_result = self::checkResult($result,$type);//判断是否满足条件，满足返回false，不满足返回string

        if($builder->exists()){//记录存在
                if($result){ //结果存在
                    if($sign_result){//判断不成功
                            $item = $builder->get()->first();
                            $days =  $item->days + 1;
                            $item->status     = true;
                            $item->check_item = $check_item;
                            $item->days       = $days;
                            $item->check_detail=$sign_result;
                            $item->save();
                            return;
                         
                        }else{
                            $item = $builder->get()->first();
                            $days = 0;
                            $item->status =false;
                            $item->days = $days;
                            $item->check_detail="";
                            $item->save();
                            return;
                          
                        }
                    }else{//结果不存在
                            $item = $builder->get()->first();
                            $days =$item->days + 1;
                            $item->status =true;
                            $item->days = $days;
                            $item->check_detail="";
                            $item->save();
                            return;
                    }
        }else{
             if($result){ //结果存在
                    if($sign_result){
        
                            $item = new SiteStatus();
                            $item->enbId = $enbId;
                            $item->check_item = $check_item;
                            $item->status = true;
                            $item->up_time = new DateTime();
                            $item->days = 1;
                            $item->check_detail=$sign_result;
                            $item->save();
                            return;   
                        }else{
                            $item = new SiteStatus();
                            $item->enbId = $enbId;
                            $item->check_item = $check_item;
                            $item->status = false;
                            $item->up_time = new DateTime();
                            $item->days = 0;
                            $item->check_detail="";
                            $item->save();
                            return;   
                        }
                    }else{//结果不存在
                            $item = new SiteStatus();
                            $item->enbId = $enbId;
                            $item->check_item = $check_item;
                            $item->status = true;
                            $item->up_time = new DateTime();
                            $item->days = 1;
                            $item->save();
                            return;
                    }
        }      
    
    }

    public static function checkResult($result,$type){

        if(!$result){
            return false;
        }
    // 接入性能不达标 ==> '无线接通率=xxx,RRC建立请求次数=xxx,ERAB建立请求次数=xxx'
    // 掉线性能不达标 ==> '无线掉线率=xxx,无线掉线次数=xxx'
    // 切换性能不达标 ==>'切换成功率=xxx, 准备切换失败数=xxx,执行切换失败数=xxx'
        if($type == "checkCounter"){

            $counter = array("pmRrcConnEstabAtt","pmErabEstabSuccInit+pmErabEstabSuccAdded","pmCellDowntimeAuto","pmCellDowntimeMan");
            $str = "";
            for($i=0;$i<4;$i++){
                if(is_null($result['kpi'.$i])){
                    $str.=$counter[$i].",";
                }
            }
            if($str){
                return rtrim($str,",");
            }else{
                return false;
            }
        }else if($type == "checkAccess"){
            if($result['kpi0']<=97&&($result['kpi1']>50||$result['kpi2']>50)){
                return false;
            }else{
                $str = "无线接通率=".$result['kpi0'].",RRC建立请求次数=".$result['kpi1'].",ERAB建立请求次数=".$result['kpi2'];
                return $str;
            }
        }else if($type == "checkLost"){
            if($result['kpi0']>0.1&&$result['kpi1']>30){
                return false;
            }else{
                $str = "无线掉线率=".$result['kpi0'].",无线掉线次数=".$result['kpi1'];
                return $str;
            }
        }else if($type == "checkHo"){
            if($result['kpi0']<98&&($result['kpi1']>50||$result['kpi2']>50)){
                return false;
            }else{
                $str = "切换成功率=".$result['kpi0'].",准备切换失败数=".$result['kpi1'].",执行切换失败数=".$result['kpi2'];
                return $str;
            }
        }



    } 
    /**
     * 检查Counter是否激活
     *
     * @param $enbId
     */
    private static function checkCounter($enbId)
    {
                 // $enbId="LD5200B";
        $day = date('Y-m-d',strtotime("-1 day"));
        $city = Databaseconns::select('connName','host','port','dbName','userName','password','subNetwork','subNetworkFdd','subNetworkNbiot')->where("cityChinese","苏州")->get()->toArray();
        $dbc = new DataBaseConnection();
        $str = $dbc->getSubNets("苏州");

        if(!$city){
            return;
        }
         $link = "dblib:host=".$city[0]['host'].":".$city[0]['port'].";dbname=".$city[0]['dbName'];


        $db = new PDO($link,$city[0]['userName'],$city[0]['password']);

        $sql = "select  max(datetime_id) as day from dc.DC_E_ERBS_EUTRANCELLTDD_raw where datetime_id<(select  max(datetime_id) from dc.DC_E_ERBS_EUTRANCELLTDD_raw )";

        $row = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);

        if(!$row){
            return;
        }

        $result = self::checkItem($enbId,"checkCounterSql.txt",$row[0]['day']);
        self::insert($enbId,$result,"checkCounter","counter未定义");

    }

    /**
     * 检查是否无切换
     *
     * @param $enbId
     */
    private function checkNoneHo($enbId)
    {
        // TODO
    }

    /**
     * 检查业务量是否过低
     *
     * @param $enbId
     */
    private function checkTraffic($enbId)
    {
        // TODO
    }

    /**
     * 检查小区状态(是否退服)
     *
     * @param $enbId
     */
    private function checkCellStatus($enbId)
    {
        // TODO
    }

    /**
     * 检查接入指标
     *
     * @param $enbId
     */
    private  static function checkAccess($enbId)
    {   

        $result = self::checkItem($enbId,"checkAccessSql.txt");
        // self::insert($enbId,$result,"checkAccess","接入性能不达标");
        self::insert($enbId,$result,"checkAccess","接入性能不达标");
        
    }

    /**
     * 检查掉线指标
     *
     * @param $enbId
     */
    private static function checkLost($enbId)
    {
        $result = self::checkItem($enbId,"checkLostSql.txt");
        self::insert($enbId,$result,"checkLost","掉线性能不达标");


    }

    /**
     * 检查切换指标
     *
     * @param $enbId
     */
    private static function checkHo($enbId)
    {
        $result = self::checkItem($enbId,"checkHoSql.txt");
        self::insert($enbId,$result,"checkHo","切换性能不达标");
    }


    private static function checkItem($enbId,$sqlTxt,$times=null){

         // $enbId="LD5200B";
        if($times){
            $day = $times;
        }else{
            $day = date('Y-m-d',strtotime("-1 day"));            
        }

        $city = Databaseconns::select('connName','host','port','dbName','userName','password','subNetwork','subNetworkFdd','subNetworkNbiot')->where("cityChinese","苏州")->get()->toArray();
        // print_r($city);
        // exit;

        $dbc = new DataBaseConnection();
        $str = $dbc->getSubNets("苏州");
                    // $sql = file_get_contents("./CheckSql/checkAccessTdd.txt");
        $sql = file_get_contents("/opt/lampp/htdocs/genius_zgl/app/Jobs/NewSitesChecker/CheckSql/".$sqlTxt);


                            // echo $sql;exit;
        $array = array();
        foreach($city as $value){
            $sql = str_replace('$day', $day, $sql);
            $sql = str_replace('$str', $str, $sql);
            $sql = str_replace('$enbId', $enbId, $sql);
            // echo $sql;exit;
            $link = "dblib:host=".$value['host'].":".$value['port'].";dbname=".$value['dbName'];
            try {

                $db = new PDO($link,$value['userName'],$value['password']);

            } catch (Exception $e) {
                continue;

            }

            try {
                  $row = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
                  
                    if($row){
                       return $row;
                    }

            } catch (Exception $e) {
                continue;
            }
        }
        // }

            $sql = str_replace('TDD', "FDD", $sql);
            $sql = str_replace('$day', $day, $sql);
            $sql = str_replace('$str', $str, $sql);
            $sql = str_replace('$enbId', $enbId, $sql);
        foreach($city as $value){
            $link = "dblib:host=".$value['host'].":".$value['port'].";dbname=".$value['dbName'];
            try {

                $db = new PDO($link,$value['userName'],$value['password']);

            } catch (Exception $e) {
                     continue;

            }

            try {
                  $row = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
                  
                    if($row){
                       return $row;
                    }

            } catch (Exception $e) {
                continue;
            }



        }

        return;

    }

}