<?php
/**
 * BadCellAnalysis.php
 *
 * @category BadCellAnalysis
 * @package  App\Http\Controllers\BadCellAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\BadCellAnalysis;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Utils\FileUtil;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Common\MyRedis;
use PDO;
use App\Models\MR\MroWeakCoverage_hour;

/**
 * MRORSRP查询
 * Class MRORSRPQueryController
 *
 * @category BadCellAnalysis
 * @package  App\Http\Controllers\BadCellAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 * @package App\Http\Controllers\BadCellAnalysis
 */
class MRORSRPQueryController extends MyRedis
{


    /**
     * 获得MRORSRP分析结果字段
     *
     * @return array MRORSRP分析结果字段
     */
    public function getMRORSRPDataField()
    {
        $dateTime = Input::get('dateTime');
        $dbname = $this->getMRDatabase(Input::get('select'));
        $result = array();
        $rows = MroWeakCoverage_hour::on($dbname)->where('date', $dateTime)->exists();
        if ($rows) {
            $rs = MroWeakCoverage_hour::on($dbname)->where('date', $dateTime)->first()->toArray();
            $result["field"] = array_keys($rs);
            return $result;
        } else {
            $result['error'] = 'error';
            return $result;
        }
    }//end getMRORSRPDataField()

    /**
     * 获得MR数据库名
     *
     * @param string $city 城市名
     *
     * @return string 数据库名
     */
    public function getMRDatabase($city)
    {
        $dbc = new DataBaseConnection();
        return $dbc->getMRDatabase($city);
    }//end getMRDatabase()

    /**
     * 获得MRORSRP分析结果
     *
     * @return string MRORSRP分析结果
     */
    public function getMRORSRPDataSplit()
    {
        $city = input::get("select");
        // dd($city);
        $dataType = Input::get('dataType');
        $limit = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
       
        $dbname = $this->getMRDatabase(Input::get('select'));
        $dateTime = Input::get('dateTime');
        $return = array();

        $rows = MroWeakCoverage_hour::on($dbname)->where('date', $dateTime)->paginate($limit)->toArray();;
        $return["total"] = $rows['total'];
        $return['records'] = $rows['data'];
        return json_encode($return);
    }//end getMRORSRPDataSplit()

    /**
     * 导出MRORSRP数据
     *
     * @return array MRORSR数据
     */
    public function getAllData()
    {
        $dbname = $this->getMRDatabase(Input::get('select'));
        $dateTime = Input::get('dateTime');
        $result = array();
        $return = array();
        $rs = MroWeakCoverage_hour::on($dbname)->where('date', $dateTime)->exists();
        if ($rs) {
            $rs = MroWeakCoverage_hour::on($dbname)->where('date', $dateTime)->first()->toArray();
            $fileName = "common/files/MRORARPQuery_".$dateTime."_" . date('YmdHis') . ".csv";
            $column = implode(array_keys($rs), ',');
            $items = MroWeakCoverage_hour::on($dbname)->where('date', $dateTime)->get()->toArray();
            $fileUtil = new FileUtil();
            $fileUtil->resultToCSV2($column, $items, $fileName);
            $result['fileName'] = $fileName;
            $result['result'] = 'true';
        } else {
            $result['error'] = 'error';
            return $result;
        }
        
        return $result;
    }//end getAllData()



    /**
     * 获取城市列表
     *
     * @return string 城市列表(JSON)
     */
    public function getAllCity()
    {
        $dbc = new DataBaseConnection();
        return $dbc->getCityOptions();
    }//end resultToCSV2_All()

    function check_input($value)
    {
        //去除斜杠
        if(get_magic_quotes_gpc())
        {
            $value=stripslashes($value);
        }
        return $value;
    }

    /**
     * 获得日期列表(天)
     *
     * @return array 日期列表(天)
     */
    public function getDate()
    {
        $city = Input::get('city');
        $city = $this->check_input($city);
        $dbname = $this->getMRDatabase($city);
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('MR', $dbname);
        $table = 'mroWeakCoverage_hour';
        $sql = "select distinct date from $table";
        
        $this->type = $dbname . ':MRORSRPQuery:' . $table;
        return $this->getValue($db, $sql);

    }//end getGSMNeighDataAll()

    /**
     * 获得单城市数据时间(天)列表
     *
     * @return void
     */
    public function getCityDate()
    {
        $dbname = input::get("dataBase");
        $dbname = $this->check_input($dbname);
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('MR', $dbname);
        $sql   = "select distinct dateId from mroWeakCoverage_day";
        $this->type = $dbname.':packetLossAnalysis_mrorsrp';
        echo json_encode($this->getValue($db, $sql));

    }//end getCityDate()

    /**
     * 获得表格数据
     *
     * @return void
     */
    public function getTableData()
    {
        $displayStart = Input::get('page') == ""? 1 : Input::get('page');
        $displayLength = Input::get('limit') == ""? 10 : Input::get('limit');
        $offset = ($displayStart - 1) * $displayLength;
        $limit = " limit $offset,$displayLength ";
        $regionType  = input::get("regionType");
        $citys       = input::get("citys");
        $cityArr     = explode(",", $citys);
        $baseStationStr = input::get("baseStation");
        $baseStationArr = explode(",", $baseStationStr);
        $groupEcgiStr   = input::get("groupEcgi");
        $groupEcgiArr   = explode(",", $groupEcgiStr);
        $timeType    = input::get("timeType");
        $startT   = input::get("startTime");
        $endT     = input::get("endTime");
        $type = input::get('survey');
        // dd(input::get('survey'));
        $startTime=min($startT, $endT);
        $endTime=max($startT, $endT);
        $base='';
        foreach ($baseStationArr as $baseStation) {
            $base .= "'".$baseStation."',";
        }   
         $baseStation = "(".substr($base, 0, -1).")";
         $group='';

         if($groupEcgiStr){
            $dbg=new DataBaseConnection();
            $dbgs=$dbg->getDB('mongs', 'mongs');
             foreach ($groupEcgiArr as $key => $v) {
               
                $sql="select ecgi from siteLte where cellName='$v' limit 1";
                $row=$dbgs->query($sql)->fetchAll(PDO::FETCH_NUM);
                if($row){
                    $group .= "'".$row[0][0]."',";
                     }else{
                     $group .= "'".$v."',";
                    
                }
             }
            $groupEcgi = "(".substr($group, 0, -1).")";
         }
        $hour        = input::get("hour");

        $dbc    = new DataBaseConnection();
        $result = array();
        $items  = array();
        $id     = 1;
        // echo $type;
        if ( $type == 'RSRP频点级_MRO' ) {
            if ( $regionType == "city" && $timeType == "day" ) {
                foreach ($cityArr as $key => $city) {
                    $sql = "SELECT dateId, city, COUNT(DISTINCT ecgi) as cellTotal, (mr_LteNcEarfcn) as mr_LteNcEarfcn, (mr_LteScEarfcn) as mr_LteScEarfcn, band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                    --,SUM(mr_LteNcPci) mr_LteNcPci 
                    FROM mroRsrp_day WHERE dateId >= '$startTime' and dateId<='$endTime' and siteName is not null group by dateId order by dateId $limit;";
                    $db  = $dbc->getDB('MR', $city);
                    $res = $db->query($sql);
                    if ($res) {
                        $rows = $res->fetchAll(PDO::FETCH_ASSOC);
                        if (count($rows) >0) {
                            $city = $dbc->getMRToCHName($city);
                            $result['key']=array_keys($rows[0]);
                            $result['records'] = $rows;
                            $result['total'] =  100;
                        }else{
                            $result['result'] = "error";
                        }
                    }else{
                            $result['result'] = "error";
                    }
                }
                echo json_encode($result);
                return;
            } else if ($regionType == "city" && $timeType == "hour") { 
                foreach ($cityArr as $key => $city) {
                    if ($hour) {
                        $sql = "SELECT dateId, hourId, city, COUNT(DISTINCT ecgi) as cellTotal, (mr_LteNcEarfcn) as mr_LteNcEarfcn, (mr_LteScEarfcn) as mr_LteScEarfcn,band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                            --,SUM(mr_LteNcPci) mr_LteNcPci 
                            FROM mroRsrp_hour WHERE dateId >= '$startTime' and dateId<='$endTime' AND hourId IN($hour) and siteName is not null group by dateId,hourId order by dateId,hourId $limit;";
                    }else {
                        $sql = "SELECT dateId, hourId, city, COUNT(DISTINCT ecgi) cellTotal, (mr_LteNcEarfcn) mr_LteNcEarfcn, (mr_LteScEarfcn) mr_LteScEarfcn,band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                            --,SUM(mr_LteNcPci) mr_LteNcPci 
                            FROM mroRsrp_hour WHERE dateId >= '$startTime' and dateId<='$endTime' and siteName is not null group by dateId,hourId order by dateId,hourId $limit;";
                    }
                    $db  = $dbc->getDB('MR', $city);
                    $res = $db->query($sql);
                    if ($res) {
                        $rows = $res->fetchAll(PDO::FETCH_ASSOC);
                        if (count($rows) >0) {
                            $city = $dbc->getMRToCHName($city);
                            $result['key']=array_keys($rows[0]);
                            $result['records'] = $rows;
                            $result['total'] =  100;
                        }else{
                            $result['result'] = "error";
                        }
                        
                    }else{
                        $result['result'] = "error";
                    }
                }
               
                echo json_encode($result);
                return;
            } elseif(($regionType == "baseStation"||$regionType=="baseStationGroup") && $timeType == "day") {
                $group = '';
                if($regionType == "baseStation") {
                    $group = ',siteName';
                }
                foreach ($cityArr as $key => $city) {
                    if($baseStationStr) {
                        $sql = "SELECT dateId, city,subNetwork,siteName,COUNT(DISTINCT ecgi) cellTotal, (mr_LteNcEarfcn) mr_LteNcEarfcn, (mr_LteScEarfcn) mr_LteScEarfcn,band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                            --,SUM(mr_LteNcPci) mr_LteNcPci 
                            FROM mroRsrp_day WHERE dateId >= '$startTime' and dateId<='$endTime' and siteName in $baseStation and siteName is not null group by dateId $group order by dateId $limit;";
                    }else {
                        $sql = "SELECT dateId, city,subNetwork,siteName, COUNT(DISTINCT ecgi) cellTotal, (mr_LteNcEarfcn) mr_LteNcEarfcn, (mr_LteScEarfcn) mr_LteScEarfcn ,band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                            --,SUM(mr_LteNcPci) mr_LteNcPci 
                            FROM mroRsrp_day WHERE dateId >= '$startTime' and dateId<='$endTime' and siteName is not null group by dateId $group order by dateId $limit;";
                    }
                    // echo $sql;
                    $db  = $dbc->getDB('MR', $city);
                    $res = $db->query($sql);
                    if ($res) {
                        $rows = $res->fetchAll(PDO::FETCH_ASSOC);
                        if (count($rows) >0) {
                            $city = $dbc->getMRToCHName($city);
                            $result['key']=array_keys($rows[0]);
                            $result['records'] = $rows;
                            $result['total'] =  100;
                        }else{
                            $result['result'] = "error";
                        }
                    }else{
                        $result['result'] = "error";
                    }
                }
                echo json_encode($result);
                return;
            } elseif(($regionType == "groupEcgi"||$regionType=="cellGroup") && $timeType == "day") {
                $group = '';
                if($regionType == "groupEcgi") {
                    $group = ',cellName';
                }
                foreach ($cityArr as $key => $city) {
                    if ($groupEcgiStr) {
                        $sql = "SELECT dateId,city,subNetwork,siteName,cellName,ecgi, (mr_LteNcEarfcn) mr_LteNcEarfcn, (mr_LteScEarfcn) mr_LteScEarfcn, band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                            --,SUM(mr_LteNcPci) mr_LteNcPci 
                            FROM mroRsrp_day WHERE dateId >= '$startTime' and dateId<='$endTime' and ecgi in $groupEcgi and siteName is not null group by dateId $group order by dateId $limit;";
                    }else {
                        $sql = "SELECT dateId,city,subNetwork,siteName,cellName,ecgi, (mr_LteNcEarfcn) mr_LteNcEarfcn, (mr_LteScEarfcn) mr_LteScEarfcn ,band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                            --,SUM(mr_LteNcPci) mr_LteNcPci 
                            FROM mroRsrp_day WHERE dateId >= '$startTime' and dateId<='$endTime' and siteName is not null group by dateId $group order by dateId $limit;";
                    }
                    // echo $sql;
                    $db  = $dbc->getDB('MR', $city);
                    $res = $db->query($sql);
                    //$resCount = $db->query($sqlCount);
                    if ($res) {
                        $rows = $res->fetchAll(PDO::FETCH_ASSOC);
                        if (count($rows) >0) {
                            $city = $dbc->getMRToCHName($city);
                            $result['key']=array_keys($rows[0]);
                            $result['records'] = $rows;
                            $result['total'] =  count($rows);
                        }else{
                            $result['result'] = "error";
                        }
                    }else{
                            $result['result'] = "error";
                        }
                }
               
                echo json_encode($result);
                return;
            }else if (($regionType == "baseStation"||$regionType=="baseStationGroup") && $timeType == "hour") {
                $group = '';
                if($regionType == "baseStation") {
                    $group = ',siteName';
                }
                foreach ($cityArr as $key => $city) {
                    if($baseStationStr) {
                        $sql = "SELECT dateId,hourId ,city,subNetwork,siteName, COUNT(DISTINCT ecgi) cellTotal, (mr_LteNcEarfcn) mr_LteNcEarfcn, (mr_LteScEarfcn) mr_LteScEarfcn,band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                            --,SUM(mr_LteNcPci) mr_LteNcPci 
                            FROM mroRsrp_hour WHERE dateId >= '$startTime' and dateId<='$endTime' and siteName in $baseStation and siteName is not null group by dateId,hourId $group order by dateId,hourId $limit;";
                    }else {
                        $sql = "SELECT dateId,hourId, city,subNetwork,siteName, COUNT(DISTINCT ecgi) cellTotal, (mr_LteNcEarfcn) mr_LteNcEarfcn, (mr_LteScEarfcn) mr_LteScEarfcn,band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                            --,SUM(mr_LteNcPci) mr_LteNcPci 
                            FROM mroRsrp_hour WHERE dateId >= '$startTime' and dateId<='$endTime' and siteName is not null group by dateId,hourId $group order by dateId,hourId $limit;";
                    }
                    // echo $sql;
                    $db  = $dbc->getDB('MR', $city);
                    $res = $db->query($sql);
                    if ($res) {
                        $rows = $res->fetchAll(PDO::FETCH_ASSOC);
                        if (count($rows) >0) {
                            $city = $dbc->getMRToCHName($city);
                            $result['key']=array_keys($rows[0]);
                            $result['records'] = $rows;
                            $result['total'] =  100;
                        }else{
                            $result['result'] = "error";
                        }
                    }else{
                        $result['result'] = "error";
                    }
                }
                echo json_encode($result);
                return;
            }else if (($regionType == "groupEcgi"||$regionType=="cellGroup") && $timeType == "hour") { 
                $group = '';
                if($regionType == "groupEcgi") {
                    $group = ',cellName';
                }
                foreach ($cityArr as $key => $city) {
                    if($baseStationStr) {
                        $sql = "SELECT dateId, hourId,city ,subNetwork,siteName,cellName,ecgi, COUNT(DISTINCT ecgi) cellTotal, (mr_LteNcEarfcn) mr_LteNcEarfcn, (mr_LteScEarfcn) mr_LteScEarfcn,band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                            --,SUM(mr_LteNcPci) mr_LteNcPci 
                            FROM mroRsrp_hour WHERE dateId >= '$startTime' and dateId<='$endTime'  and ecgi in $groupEcgi and siteName is not null group by dateId,hourId $group order by dateId,hourId $limit;";
                    }else {
                        $sql = "SELECT dateId, hourId,city ,subNetwork,siteName,cellName,ecgi, COUNT(DISTINCT ecgi) cellTotal, (mr_LteNcEarfcn) mr_LteNcEarfcn, (mr_LteScEarfcn) mr_LteScEarfcn,band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                            --,SUM(mr_LteNcPci) mr_LteNcPci 
                            FROM mroRsrp_hour WHERE dateId >= '$startTime' and dateId<='$endTime' and siteName is not null group by dateId,hourId $group order by dateId,hourId $limit;";
                    }
                    $db  = $dbc->getDB('MR', $city);
                    $res = $db->query($sql);
                    if ($res) {
                        $rows = $res->fetchAll(PDO::FETCH_ASSOC);
                        if (count($rows) >0) {
                            $city = $dbc->getMRToCHName($city);
                            $result['key']=array_keys($rows[0]);
                            $result['records'] = $rows;
                            $result['total'] =  100;
                        }else{
                            $result['result'] = "error";
                        }
                    }else{
                        $result['result'] = "error";
                    }
                }
                echo json_encode($result);
                return;
            }
        }
        if ($regionType == "city" && $timeType == "day") {
            $sql = "SELECT dateId,a.city, count(distinct b.ecgi) cellTotal,round(avg(avgRSRP), 2) avgRSRP,sum(numTotal) TotalSample,sum(numTotal-numLess110) Sample110,round(   sum(numTotal-numLess110) / sum(numTotal),2) Rate110 FROM
                mroWeakCoverage_day FORCE INDEX (dateId) b left join siteLte a on a.ecgi= b.ecgi where dateId >='$startTime' and dateId<='$endTime' and siteName is not null group by dateId order by dateId $limit; ";
            foreach ($cityArr as $key => $city) {
                $db  = $dbc->getDB('MR', $city);
                $res = $db->query($sql);
                //$resCount = $db->query($sqlCount);
                if ($res) {
                    $rows = $res->fetchAll(PDO::FETCH_ASSOC);
                    if (count($rows) >0) {
                        $city = $dbc->getMRToCHName($city);
                        $result['key']=array_keys($rows[0]);
                        $result['records'] = $rows;
                        $result['total'] =  100;
                    }else{
                        $result['result'] = "error";
                    }
                }else{
                        $result['result'] = "error";
                    }
            }
           
            echo json_encode($result);
            return;
        } else if ($regionType == "city" && $timeType == "hour") {
            if ($hour) {
                 $sql = "SELECT dateId,a.city,hourId, count(distinct b.ecgi) cellTotal,round(avg(avgRSRP), 2) avgRSRP,sum(numTotal) TotalSample,sum(numTotal-numLess110) Sample110,round(   sum(numTotal-numLess110) / sum(numTotal),2) Rate110 FROM
                mroWeakCoverage_hour FORCE INDEX (dateId) b left join siteLte a on a.ecgi= b.ecgi where dateId >='$startTime' and dateId<='$endTime' and siteName is not null and hourId in ($hour)  group by dateId,hourId order by dateId,hourId $limit;  ";
            } else {
                 $sql = "SELECT dateId,a.city,hourId, count(distinct b.ecgi) cellTotal,round(avg(avgRSRP), 2) avgRSRP,sum(numTotal) TotalSample,sum(numTotal-numLess110) Sample110,round(   sum(numTotal-numLess110) / sum(numTotal),2) Rate110 FROM
                mroWeakCoverage_hour FORCE INDEX (dateId) b left join siteLte a on a.ecgi= b.ecgi where dateId >='$startTime' and dateId<='$endTime' and siteName is not null group by dateId,hourId order by dateId,hourId $limit;  ";
            }
            foreach ($cityArr as $key => $city) {
                $db  = $dbc->getDB('MR', $city);
                $res = $db->query($sql);
                //$resCount = $db->query($sqlCount);
                if ($res) {
                    $rows = $res->fetchAll(PDO::FETCH_ASSOC);
                    if (count($rows) >0) {
                        $city = $dbc->getMRToCHName($city);
                        $result['key']=array_keys($rows[0]);
                        $result['records'] = $rows;
                        $result['total'] =  100;
                    }else{
                        $result['result'] = "error";
                    }
                    
                }else{
                        $result['result'] = "error";
                    }
            }
           
            echo json_encode($result);
            return;
        } else if (($regionType == "baseStation"||$regionType=="baseStationGroup") && $timeType == "day") {
            if ($baseStationStr) {
                $sql ="SELECT dateId,a.city,a.siteName, count(distinct b.ecgi) cellTotal,round(avg(avgRSRP), 2) avgRSRP,sum(numTotal) TotalSample,sum(numTotal-numLess110) Sample110,round( sum(numTotal-numLess110) / sum(numTotal),2) Rate110 FROM
                mroWeakCoverage_day FORCE INDEX (dateId) b left join siteLte a on a.ecgi= b.ecgi where dateId >='$startTime' and dateId<='$endTime' and siteName is not null and siteName in $baseStation group by dateId,a.siteName order by dateId,a.siteName $limit;";
            } else {
                $sql = "SELECT dateId,a.city,a.siteName, count(distinct b.ecgi) cellTotal,round(avg(avgRSRP), 2) avgRSRP,sum(numTotal) TotalSample,sum(numTotal-numLess110) Sample110,round(    sum(numTotal-numLess110) / sum(numTotal),2) Rate110 FROM
                mroWeakCoverage_day FORCE INDEX (dateId) b left join siteLte a on a.ecgi= b.ecgi where dateId >='$startTime' and dateId<='$endTime' and siteName is not null  group by dateId,a.siteName order by dateId,a.siteName $limit;";
            }
             foreach ($cityArr as $key => $city) {
                $db  = $dbc->getDB('MR', $city);
                $res = $db->query($sql);
                //$resCount = $db->query($sqlCount);
                if ($res) {
                    $rows = $res->fetchAll(PDO::FETCH_ASSOC);
                    if (count($rows) >0) {
                        $city = $dbc->getMRToCHName($city);
                        $result['key']=array_keys($rows[0]);
                        $result['records'] = $rows;
                        $result['total'] =  100;
                    }else{
                        $result['result'] = "error";
                    }
                }else{
                        $result['result'] = "error";
                    }
            }
           
            echo json_encode($result);
            return;
        } else if (($regionType == "groupEcgi"||$regionType=="cellGroup") && $timeType == "day") {
             
            if ($groupEcgiStr) {
                $sql = "select * from mroWeakCoverage_day force index (dateId) where dateId >='$startTime' and dateId<='$endTime' and ecgi in $groupEcgi  order by dateId $limit";
                /*$sqlCount = "select count(*) from  mroWeakCoverage_day force index (date) where date >='$startTime' and date<='$endTime' and siteName is not null and ecgi in $groupEcgi";*/
            } else {
                $sql = "select * from mroWeakCoverage_day force index (dateId) where dateId >='$startTime' and dateId<='$endTime'  order by dateId $limit";
                /*$sqlCount = "select count(*) from mroWeakCoverage_day force index (date) where date >='$startTime' and date<='$endTime' and siteName is not null";*/
            }
            foreach ($cityArr as $key => $city) {
                $db  = $dbc->getDB('MR', $city);
                $res = $db->query($sql);
                //$resCount = $db->query($sqlCount);
                if ($res) {
                    $rows = $res->fetchAll(PDO::FETCH_ASSOC);
                    if (count($rows) >0) {
                        $city = $dbc->getMRToCHName($city);
                        $result['key']=array_keys($rows[0]);
                        $result['records'] = $rows;
                        $result['total'] =  100;
                    }else{
                        $result['result'] = "error";
                    }
                }else{
                        $result['result'] = "error";
                    }
            }
           
            echo json_encode($result);
            return;
        } else if (($regionType == "baseStation"||$regionType=="baseStationGroup") && $timeType == "hour") {
            $filter = "";
            if ($baseStationStr) {
                $filter = $filter." and siteName in $baseStation ";
            }
            if ($hour) {
                 $filter = $filter." and hourId in ($hour)";
            }
            $sql ="SELECT dateId,a.city,hourId,a.siteName, count(distinct b.ecgi) cellTotal,round(avg(avgRSRP), 2) avgRSRP,sum(numTotal) TotalSample,sum(numTotal-numLess110) Sample110,round(  sum(numTotal-numLess110) / sum(numTotal),2) Rate110 FROM
                mroWeakCoverage_hour FORCE INDEX (dateId) b left join siteLte a on a.ecgi= b.ecgi where dateId >='$startTime' and dateId<='$endTime' and siteName is not null  ".$filter." group BY dateId,hourId,siteName order by dateId,hourId,siteName $limit;";
            foreach ($cityArr as $key => $city) {
                $db  = $dbc->getDB('MR', $city);
                $res = $db->query($sql);
                //$resCount = $db->query($sqlCount);
                if ($res) {
                    $rows = $res->fetchAll(PDO::FETCH_ASSOC);
                    if (count($rows) >0) {
                        $city = $dbc->getMRToCHName($city);
                        $result['key']=array_keys($rows[0]);
                        $result['records'] = $rows;
                        $result['total'] =  100;
                    }else{
                        $result['result'] = "error";
                    }
                }else{
                        $result['result'] = "error";
                    }
            }
           
            echo json_encode($result);
            return;
        } else if (($regionType == "groupEcgi"||$regionType=="cellGroup") && $timeType == "hour") {
            $filter = "";
            if ($groupEcgiStr) {
                $filter = $filter." and ecgi in $groupEcgi";
            }
            if ($hour) {
                 $filter = $filter." and hourId in ($hour)";
            }
            $sql = "select * from mroWeakCoverage_hour force index (date) where dateId >='$startTime' and dateId<='$endTime' ".$filter." order by dateId $limit";
            /*$sqlCount = "select count(*) from mroWeakCoverage_hour force index (date) where date >='$startTime' and date<='$endTim' ".$filter;  */

            foreach ($cityArr as $key => $city) {
                $db  = $dbc->getDB('MR', $city);
                $res = $db->query($sql);
                //$resCount = $db->query($sqlCount);
                if ($res) {
                    $rows = $res->fetchAll(PDO::FETCH_ASSOC);
                    if (count($rows) >0) {
                        $city = $dbc->getMRToCHName($city);
                        $result['key']=array_keys($rows[0]);
                        $result['records'] = $rows;
                        $result['total'] =  100;
                    }else{
                        $result['result'] = "error";
                    }
                }else{
                        $result['result'] = "error";
                    }
            }
           
            echo json_encode($result);
            return;
        }//end if

    }//end getTableData()


    /**
     * 导出文件
     *
     * @return void
     */
    public function exportFile()
    {
        $regionType  = input::get("regionType");
        $citys       = input::get("citys");
        $cityArr     = explode(",", $citys);
        $baseStationStr = input::get("baseStation");
        $baseStationArr = explode(",", $baseStationStr);
        $groupEcgiStr   = input::get("groupEcgi");
        $groupEcgiArr   = explode(",", $groupEcgiStr);
        $timeType   = input::get("timeType");
        $startT     = input::get("startTime");
        $endT       = input::get("endTime");
        $type = input::get('survey');

        $startTime  = min($startT, $endT);
        $endTime    = max($startT, $endT);
        $base='';
        foreach ($baseStationArr as $baseStation) {
            $base .= "'".$baseStation."',";
        }   
         $baseStation = "(".substr($base, 0, -1).")";
         $group='';
        if($groupEcgiStr){
            $dbg=new DataBaseConnection();
            $dbgs=$dbg->getDB('mongs', 'mongs');
             foreach ($groupEcgiArr as $key => $v) {
               
                $sql="select ecgi from siteLte where cellName='$v' limit 1";
                $row=$dbgs->query($sql)->fetchAll(PDO::FETCH_NUM);
                if($row){
                    $group .= "'".$row[0][0]."',";
                     }else{
                     $group .= "'".$v."',";
                    
                }
             }
            $groupEcgi = "(".substr($group, 0, -1).")";
         }
        $hour        = input::get("hour");
        $dbc    = new DataBaseConnection();
        $result = array();
        $items  = array();
        $text = "";


        if ( $type == 'RSRP频点级_MRO' ) {
            if ( $regionType == "city" && $timeType == "day" ) {
                foreach ($cityArr as $key => $city) {
                    $sql = "SELECT dateId, city, COUNT(DISTINCT ecgi) as cellTotal, (mr_LteNcEarfcn) as mr_LteNcEarfcn, (mr_LteScEarfcn) as mr_LteScEarfcn, band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                    --,SUM(mr_LteNcPci) mr_LteNcPci 
                    FROM mroRsrp_day WHERE dateId >= '$startTime' and dateId<='$endTime' and siteName is not null group by dateId order by dateId;";
                    $db  = $dbc->getDB('MR', $city);
                    $res = $db->query($sql);
                    if ($res) {
                        $items = $res->fetchAll(PDO::FETCH_ASSOC);
                        $city = $dbc->getMRToCHName($city);
                        $text = implode(",", array_keys($items[0]));
                    }
                }
                // echo json_encode($result);
                // return;
            } else if ($regionType == "city" && $timeType == "hour") { 
                foreach ($cityArr as $key => $city) {
                    if ($hour) {
                        $sql = "SELECT dateId, hourId, city, COUNT(DISTINCT ecgi) as cellTotal, (mr_LteNcEarfcn) as mr_LteNcEarfcn, (mr_LteScEarfcn) as mr_LteScEarfcn,band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                            --,SUM(mr_LteNcPci) mr_LteNcPci 
                            FROM mroRsrp_hour WHERE dateId >= '$startTime' and dateId<='$endTime' AND hourId IN($hour) and siteName is not null group by dateId,hourId order by dateId,hourId ;";
                    }else {
                        $sql = "SELECT dateId, hourId, city, COUNT(DISTINCT ecgi) cellTotal, (mr_LteNcEarfcn) mr_LteNcEarfcn, (mr_LteScEarfcn) mr_LteScEarfcn,band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                            --,SUM(mr_LteNcPci) mr_LteNcPci 
                            FROM mroRsrp_hour WHERE dateId >= '$startTime' and dateId<='$endTime' and siteName is not null group by dateId,hourId order by dateId,hourId ;";
                    }
                    $db  = $dbc->getDB('MR', $city);
                    $res = $db->query($sql);
                    if ($res) {
                        $items = $res->fetchAll(PDO::FETCH_ASSOC);
                        $city = $dbc->getMRToCHName($city);
                        $text = implode(",", array_keys($items[0]));
                    }
                }
               
                // echo json_encode($result);
                // return;
            } elseif(($regionType == "baseStation"||$regionType=="baseStationGroup") && $timeType == "day") {
                $group = '';
                if($regionType == "baseStation") {
                    $group = ',siteName';
                }
                foreach ($cityArr as $key => $city) {
                    if($baseStationStr) {
                        $sql = "SELECT dateId, city,subNetwork,siteName,COUNT(DISTINCT ecgi) cellTotal, (mr_LteNcEarfcn) mr_LteNcEarfcn, (mr_LteScEarfcn) mr_LteScEarfcn,band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                            --,SUM(mr_LteNcPci) mr_LteNcPci 
                            FROM mroRsrp_day WHERE dateId >= '$startTime' and dateId<='$endTime' and siteName in $baseStation and siteName is not null group by dateId $group order by dateId ;";
                    }else {
                        $sql = "SELECT dateId, city,subNetwork,siteName, COUNT(DISTINCT ecgi) cellTotal, (mr_LteNcEarfcn) mr_LteNcEarfcn, (mr_LteScEarfcn) mr_LteScEarfcn ,band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                            --,SUM(mr_LteNcPci) mr_LteNcPci 
                            FROM mroRsrp_day WHERE dateId >= '$startTime' and dateId<='$endTime' and siteName is not null group by dateId $group order by dateId ;";
                    }
                    $db  = $dbc->getDB('MR', $city);
                    $res = $db->query($sql);
                    if ($res) {
                        $items = $res->fetchAll(PDO::FETCH_ASSOC);
                        $city = $dbc->getMRToCHName($city);
                        $text = implode(",", array_keys($items[0]));
                    }
                }
                // echo json_encode($result);
                // return;
            } elseif(($regionType == "groupEcgi"||$regionType=="cellGroup") && $timeType == "day") {
                $group = '';
                if($regionType == "groupEcgi") {
                    $group = ',cellName';
                }
                foreach ($cityArr as $key => $city) {
                    if ($groupEcgiStr) {
                        $sql = "SELECT dateId,city,subNetwork,siteName,cellName,ecgi, (mr_LteNcEarfcn) mr_LteNcEarfcn, (mr_LteScEarfcn) mr_LteScEarfcn, band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                            --,SUM(mr_LteNcPci) mr_LteNcPci 
                            FROM mroRsrp_day WHERE dateId >= '$startTime' and dateId<='$endTime' and ecgi in $groupEcgi and siteName is not null group by dateId $group order by dateId ;";
                    }else {
                        $sql = "SELECT dateId,city,subNetwork,siteName,cellName,ecgi, (mr_LteNcEarfcn) mr_LteNcEarfcn, (mr_LteScEarfcn) mr_LteScEarfcn ,band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                            --,SUM(mr_LteNcPci) mr_LteNcPci 
                            FROM mroRsrp_day WHERE dateId >= '$startTime' and dateId<='$endTime' and siteName is not null group by dateId $group order by dateId ;";
                    }
                    $db  = $dbc->getDB('MR', $city);
                    $res = $db->query($sql);
                    //$resCount = $db->query($sqlCount);
                    if ($res) {
                        $items = $res->fetchAll(PDO::FETCH_ASSOC);
                        $city = $dbc->getMRToCHName($city);
                        $text = implode(",", array_keys($items[0]));
                    }
                }
               
                // echo json_encode($result);
                // return;
            }else if (($regionType == "baseStation"||$regionType=="baseStationGroup") && $timeType == "hour") {
                $group = '';
                if($regionType == "baseStation") {
                    $group = ',siteName';
                }
                foreach ($cityArr as $key => $city) {
                    if($baseStationStr) {
                        $sql = "SELECT dateId,hourId ,city,subNetwork,siteName, COUNT(DISTINCT ecgi) cellTotal, (mr_LteNcEarfcn) mr_LteNcEarfcn, (mr_LteScEarfcn) mr_LteScEarfcn,band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                            --,SUM(mr_LteNcPci) mr_LteNcPci 
                            FROM mroRsrp_hour WHERE dateId >= '$startTime' and dateId<='$endTime' and siteName in $baseStation and siteName is not null group by dateId,hourId $group order by dateId,hourId ;";
                    }else {
                        $sql = "SELECT dateId,hourId, city,subNetwork,siteName, COUNT(DISTINCT ecgi) cellTotal, (mr_LteNcEarfcn) mr_LteNcEarfcn, (mr_LteScEarfcn) mr_LteScEarfcn,band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                            --,SUM(mr_LteNcPci) mr_LteNcPci 
                            FROM mroRsrp_hour WHERE dateId >= '$startTime' and dateId<='$endTime' and siteName is not null group by dateId,hourId $group order by dateId,hourId ;";
                    }
                    $db  = $dbc->getDB('MR', $city);
                    $res = $db->query($sql);
                    if ($res) {
                        $items = $res->fetchAll(PDO::FETCH_ASSOC);
                        $city = $dbc->getMRToCHName($city);
                        $text = implode(",", array_keys($items[0]));
                    }
                }
                // echo json_encode($result);
                // return;
            }else if (($regionType == "groupEcgi"||$regionType=="cellGroup") && $timeType == "hour") { 
                $group = '';
                if($regionType == "groupEcgi") {
                    $group = ',cellName';
                }
                foreach ($cityArr as $key => $city) {
                    if($baseStationStr) {
                        $sql = "SELECT dateId, hourId,city ,subNetwork,siteName,cellName,ecgi, COUNT(DISTINCT ecgi) cellTotal, (mr_LteNcEarfcn) mr_LteNcEarfcn, (mr_LteScEarfcn) mr_LteScEarfcn,band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                            --,SUM(mr_LteNcPci) mr_LteNcPci 
                            FROM mroRsrp_hour WHERE dateId >= '$startTime' and dateId<='$endTime'  and ecgi in $groupEcgi and siteName is not null group by dateId,hourId $group order by dateId,hourId ;";
                    }else {
                        $sql = "SELECT dateId, hourId,city ,subNetwork,siteName,cellName,ecgi, COUNT(DISTINCT ecgi) cellTotal, (mr_LteNcEarfcn) mr_LteNcEarfcn, (mr_LteScEarfcn) mr_LteScEarfcn,band,round(avg(avgRsrp),4) as avgRsrp,round(avg(avgRsrq),4) as avgRsrq,sum(numGt80) as numGt80,
                    sum(numBet80_90) numBet80_90,sum(numBet90_100) as numBet90_100,sum(numBet100_110) as numBet100_110,sum(numLt110) as numLt110,sum(numTotal) as numTotal, round(100*(sum(numLt110)/sum(numTotal))) as ratio110
                            --,SUM(mr_LteNcPci) mr_LteNcPci 
                            FROM mroRsrp_hour WHERE dateId >= '$startTime' and dateId<='$endTime' and siteName is not null group by dateId,hourId $group order by dateId,hourId ;";
                    }
                    $db  = $dbc->getDB('MR', $city);
                    $res = $db->query($sql);
                    if ($res) {
                        $items = $res->fetchAll(PDO::FETCH_ASSOC);
                        $city = $dbc->getMRToCHName($city);
                        $text = implode(",", array_keys($items[0]));
                    }
                }
                // echo json_encode($result);
                // return;
            }
            $filename = "files/MRO_RSRP频点级查询".date('YmdHis').".csv";

            $result['text']   = $text;
            $result['rows']   = $items;
            $result['total']  = count($items);
            $result['result'] = 'true';
            $this->resultToCSV2($result, $filename);
            $result['filename'] = $filename;
            $result['rows']     = null;
            return json_encode($result);
            // return;
        }




        if ($regionType == "city" && $timeType == "day") {
              $sql = "SELECT dateId,a.city, count(distinct b.ecgi) cellTotal,round(avg(avgRSRP), 2) avgRSRP,sum(numTotal) TotalSample,sum(numTotal-numLess110) Sample110,round( sum(numTotal-numLess110) / sum(numTotal),2) Rate110 FROM
                mroWeakCoverage_day  b left join siteLte a on a.ecgi= b.ecgi where dateId >='$startTime' and dateId<='$endTime' and siteName is not null group by dateId order by dateId ";
            foreach ($cityArr as $key => $city) {
                $db  = $dbc->getDB('MR', $city);
                $res =$db->query($sql);
                
                if ($res) {
                    $items = $res->fetchAll(PDO::FETCH_ASSOC);
                    $city = $dbc->getMRToCHName($city);
                    $text = implode(",", array_keys($items[0]));
                }
            }
        } else if ($regionType == "city" && $timeType == "hour") {
         if ($hour) {
                 $sql = "SELECT dateId,a.city,hourId, count(distinct b.ecgi) cellTotal,round(avg(avgRSRP), 2) avgRSRP,sum(numTotal) TotalSample,sum(numTotal-numLess110) Sample110,round(   sum(numTotal-numLess110) / sum(numTotal),2) Rate110 FROM
                mroWeakCoverage_hour  b left join siteLte a on a.ecgi= b.ecgi where dateId >='$startTime' and dateId<='$endTime' and siteName is not null and hourId in ($hour)  group by dateId,hourId order by dateId,hourId ;  ";
            } else {
                 $sql = "SELECT dateId,a.city,hourId, count(distinct b.ecgi) cellTotal,round(avg(avgRSRP), 2) avgRSRP,sum(numTotal) TotalSample,sum(numTotal-numLess110) Sample110,round(   sum(numTotal-numLess110) / sum(numTotal),2) Rate110 FROM
                mroWeakCoverage_hour  b left join siteLte a on a.ecgi= b.ecgi where dateId >='$startTime' and dateId<='$endTime' and siteName is not null group by dateId,hourId order by dateId,hourId ;  ";
            }
            foreach ($cityArr as $key => $city) {
                $db  = $dbc->getDB('MR', $city);
                $res =$db->query($sql);
                
                if ($res) {
                    $items = $res->fetchAll(PDO::FETCH_ASSOC);
                    $city = $dbc->getMRToCHName($city);
                    $text = implode(",", array_keys($items[0]));
                }
            }
        } else if (($regionType == "baseStation"||$regionType=="baseStationGroup") && $timeType == "day") {
            if ($baseStationStr) {
                $sql ="SELECT dateId,a.city,a.siteName, count(distinct b.ecgi) cellTotal,round(avg(avgRSRP), 2) avgRSRP,sum(numTotal) TotalSample,sum(numTotal-numLess110) Sample110,round( sum(numTotal-numLess110) / sum(numTotal),2) Rate110 FROM
                mroWeakCoverage_day  b left join siteLte a on a.ecgi= b.ecgi where dateId >='$startTime' and dateId<='$endTime' and siteName is not null and siteName in $baseStation group by dateId,a.siteName order by dateId,a.siteName;";
            } else {
                $sql = "SELECT dateId,a.city,a.siteName, count(distinct b.ecgi) cellTotal,round(avg(avgRSRP), 2) avgRSRP,sum(numTotal) TotalSample,sum(numTotal-numLess110) Sample110,round(    sum(numTotal-numLess110) / sum(numTotal),2) Rate110 FROM
                mroWeakCoverage_day  b left join siteLte a on a.ecgi= b.ecgi where dateId >='$startTime' and dateId<='$endTime' and siteName is not null  group by dateId,a.siteName order by dateId,a.siteName ;";
            }
            foreach ($cityArr as $key => $city) {
                $db  = $dbc->getDB('MR', $city);
                $res =$db->query($sql);
                
                if ($res) {
                    $items = $res->fetchAll(PDO::FETCH_ASSOC);
                    $city = $dbc->getMRToCHName($city);
                    $text = implode(",", array_keys($items[0]));
                }
            }
        } else if (($regionType == "groupEcgi"||$regionType=="cellGroup") && $timeType == "day") {
           if ($groupEcgiStr) {
                $sql = "select * from mroWeakCoverage_day FORCE INDEX (dateId) where dateId >='$startTime' and dateId<='$endTime'   and ecgi in $groupEcgi  order by dateId";
            } else {
                $sql = "select * from mroWeakCoverage_day FORCE INDEX (dateId) where dateId >='$startTime' and dateId<='$endTime'  order by dateId";

            }
            foreach ($cityArr as $key => $city) {
                $db  = $dbc->getDB('MR', $city);
                $res =$db->query($sql);
                
                if ($res) {
                    $items = $res->fetchAll(PDO::FETCH_ASSOC);
                    $city = $dbc->getMRToCHName($city);
                    $text = implode(",", array_keys($items[0]));
                }
            }
        } else if (($regionType == "baseStation"||$regionType=="baseStationGroup") && $timeType == "hour") {
            $filter = "";
            if ($baseStationStr) {
                $filter = $filter." and siteName in $baseStation ";
            }
            if ($hour) {
                 $filter = $filter." and hourId in ($hour)";
            }
            $sql ="SELECT dateId,a.city,hourId,a.siteName, count(distinct b.ecgi) cellTotal,round(avg(avgRSRP), 2) avgRSRP,sum(numTotal) TotalSample,sum(numTotal-numLess110) Sample110,round(  sum(numTotal-numLess110) / sum(numTotal),2) Rate110 FROM
                mroWeakCoverage_hour  b left join siteLte a on a.ecgi= b.ecgi where dateId >='$startTime' and dateId<='$endTime' and siteName is not null  ".$filter." group BY dateId,hourId,siteName order by dateId,hourId,siteName limit 100000000;";
            foreach ($cityArr as $key => $city) {
                $db  = $dbc->getDB('MR', $city);
                $res =$db->query($sql);
                
                if ($res) {
                    $items = $res->fetchAll(PDO::FETCH_ASSOC);
                    $city = $dbc->getMRToCHName($city);
                    $text = implode(",", array_keys($items[0]));
                }
            }
        } else if (($regionType == "groupEcgi"||$regionType=="cellGroup") && $timeType == "hour") {
              $filter = "";
            if ($groupEcgiStr) {
                $filter = $filter." and ecgi in $groupEcgi";
            }
            if ($hour) {
                 $filter = $filter." and hourId in ($hour)";
            }
            $sql = "select * from mroWeakCoverage_hour  where dateId >='$startTime' and dateId<='$endTime' ".$filter." order by dateId";
            foreach ($cityArr as $key => $city) {
                $db  = $dbc->getDB('MR', $city);
                $res =$db->query($sql);
                
                if ($res) {
                    $items = $res->fetchAll(PDO::FETCH_ASSOC);
                    $city = $dbc->getMRToCHName($city);
                    $text = implode(",", array_keys($items[0]));
                }
            }
        }//end if

        $filename = "files/MRO_RSRP查询".date('YmdHis').".csv";

        $result['text']   = $text;
        $result['rows']   = $items;
        $result['total']  = count($items);
        $result['result'] = 'true';
        $this->resultToCSV2($result, $filename);
        $result['filename'] = $filename;
        $result['rows']     = null;
        echo json_encode($result);

    }//end exportFile()
    /**
     * 写入CSV文件
     *
     * @param array  $result   查询结果
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
            $item = array();
            foreach ($row as $r) {
                array_push($item, mb_convert_encoding($r, 'GBK'));
            }

            fputcsv($fp, $item);
        }

        fclose($fp);

    }//end resultToCSV2()
   
}//end class

