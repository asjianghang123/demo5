<?php

/**
 * CoverageQueryController.php
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use App\Http\Controllers\Utils\FileUtil;

/**
 * 覆盖查询
 * Class CoverageQueryController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class CoverageQueryController extends Controller
{


    /**
     * 获得城市列表
     *
     * @return void
     */
    public function getCitys()
    {
        $dbc   = new DataBaseConnection();
        $db    = $dbc->getDB('MR');
        $sql   = "show dataBases";
        $res   = $db->query($sql);
        $items = array();
        $row   = $res->fetchAll(PDO::FETCH_ASSOC);
        foreach ($row as $r) {
            if ($r['DATABASE'] != 'Global') {
                $CHCity = $dbc->getPGToCHName($r['DATABASE']);
                array_push($items, $CHCity."-".$r['DATABASE']);
            }
        }

        echo json_encode($items);

    }//end getCitys()


    /**
     * 获得日期列表
     *
     * @return void
     */
    public function getCityDate()
    {
        $dbname = input::get("dataBase");
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('MR', $dbname);

        $sql   = "select distinct timeStamp from mrs_rsrp order by timeStamp";
        $rs    = $db->query($sql);
        $row   = $rs->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $r) {
            $date = $r['timeStamp'];
            array_push($items, $date);
        }

        echo json_encode($items);

    }//end getCityDate()

    public function getRSRPKey()
    {
        $key = [];
        for ($i = 0; $i < 48; $i++) {                         
            array_push($key, trans('message.RSRP'.'.RSRP'.$i));
        }
        echo json_encode($key);
    }


    /**
     * 获得表格数据
     *
     * @return void
     */
    public function getTableData()
    {
        $regionType  = input::get("regionType");
        $city       = input::get("citys");
        // $cityArr     = explode(",", $citys);
        $baseStationStr = input::get("baseStation");
        $baseStationArr = explode(",", $baseStationStr);
        $groupEcgiStr   = input::get("groupEcgi");
        $groupEcgiArr   = explode(",", $groupEcgiStr);
        $timeType    = input::get("timeType");
        $startT   = input::get("startTime");
        $endT     = input::get("endTime");

        $limit = input::get("limit");
        $page = input::get("page");
        $start = ($page - 1) * $limit;

        $startTime=min($startT, $endT);
        $endTime=max($startT, $endT);
        $base='';
        foreach ($baseStationArr as $baseStation) {
            $base .= "'".$baseStation."',";
        }   
         $baseStation = "(".substr($base, 0, -1).")";
         $group='';
        if ($groupEcgiStr) {
            $dbg=new DataBaseConnection();
            $dbgs=$dbg->getDB('mongs', 'mongs');
            foreach ($groupEcgiArr as $key => $v) {              
                $sql="select ecgi from siteLte where cellName='$v' limit 1";
                $row=$dbgs->query($sql)->fetchAll(PDO::FETCH_NUM);
                if ($row) {
                    $group .= "'".$row[0][0]."',";
                } else {
                     $group .= "'".$v."',";                   
                }
            }
            $groupEcgi = "(".substr($group, 0, -1).")";
        }        
        $hour        = input::get("hour");
        $RSRPArr = array();
        for ($i = 0; $i < 48; $i++) {
            if ($i < 10) {
                $n = "0".$i;
            } else {
                $n = $i;
            }
            // if ($regionType == "city") {
            //     array_push($RSRPArr, "sum(mr_RSRP_".$n.") as mr_RSRP_".$n);
            // } else {
            //     array_push($RSRPArr, "mr_RSRP_".$n);
            // }
            array_push($RSRPArr, "sum(mr_RSRP_".$n.") as mr_RSRP_".$n);
        }
        $RSRP = implode(",", $RSRPArr);
        $dbc    = new DataBaseConnection();
        $db  = $dbc->getPGSQL($city);
        $result = array();
        $items  = array();
        $id     = 1;
        if ($regionType == "city" && $timeType == "day") {
            $sql = "select timeStamp,$RSRP,count(distinct ecgi) from mrs_rsrp where timeStamp >='$startTime' and timeStamp<='$endTime' group by timeStamp";
            $res = $db->query("select count(*) from ($sql) as total");
            $total = $res->fetchAll(PDO::FETCH_NUM)[0];

            $sql = $sql." limit $limit offset $start";
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    // print_r($row);
                    $timeStamp = $row[0];
                    $temp      = array();
                    $sum_RSRP  =0;
                    for ($i = 1; $i <= 48; $i++) {
                        $temp['RSRP'.($i - 1)] = $row[$i];
                        $sum_RSRP+=$row[$i];
                    }                       
                    $result = $this->getResult($temp, $sum_RSRP);
                    $temp=array();
                    for ($i = 1; $i <= 48; $i++) {                         
                        $temp[trans('message.RSRP'.'.RSRP'.($i-1))] = $row[$i];
                    }
                    // $key=array_keys($temp);
                    $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[49] ,"city" => $city,"总RSRP采样点总数"=>$sum_RSRP,"RSRP平均覆盖率"=>$result['avg_RSRP'],"RSRP的比例"=>$result['avgs'], "date" => $timeStamp), $temp);
                    array_push($items, $temp);
                }
            }
            $result['total'] = $total;
            // $result['key']=$key;
            $result['records'] = $items;
            // return json_encode($result);
            echo json_encode($result);
            return;
        } else if ($regionType == "city" && $timeType == "hour") {
            $stmt = '';
            if ($hour) {
                $sql = "select timeStamp,hourId,$RSRP,count(distinct ecgi) from mrs_rsrp where timeStamp >='$startTime' and timeStamp<='$endTime' and hourId in ($hour) group BY timeStamp,hourId";
            } else {
                $sql = "select timeStamp,hourId,$RSRP,count(distinct ecgi) from mrs_rsrp where timeStamp >='$startTime' and timeStamp<='$endTime' group BY timeStamp,hourId";
            }
            $res = $db->query("select count(*) from ($sql) as total");
            $total = $res->fetchAll(PDO::FETCH_NUM)[0];

            $sql = $sql." order by timeStamp,hourId limit $limit offset $start";
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    $timeStamp = $row[0];
                    $hourId    = $row[1];
                    $temp      = array();
                    $sum_RSRP  =0;
                    for ($i = 2; $i <= 49; $i++) {
                        $temp['RSRP'.($i - 2)] = $row[$i];
                        $sum_RSRP+=$row[$i];
                    }
                    $result = $this->getResult($temp, $sum_RSRP);                     
                    $temp=array();
                    for ($i = 2; $i <= 49; $i++) {                         
                        $temp[trans('message.RSRP'.'.RSRP'.($i-2))] = $row[$i];
                    }
                    // $key=array_keys($temp);
                    $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[50], "city" => $city,"总RSRP采样点总数"=>$sum_RSRP,"RSRP平均覆盖率"=>$result['avg_RSRP'],"RSRP的比例"=>$result['avgs'], "date" => $timeStamp, "hourId" => $hourId), $temp);
                    array_push($items, $temp);
                }
            }
            $result['total'] = $total;
            // $result['key']=$key;
            $result['records'] = $items;
            echo json_encode($result);
            return;
        } else if (($regionType == "baseStation"||$regionType=="baseStationGroup") && $timeType == "day") {
            if ($baseStationStr) {
                $sql = "select timeStamp,userLabel,$RSRP,count(distinct ecgi) as count_ecgi from mrs_rsrp where timeStamp <='$endTime' and timeStamp>='$startTime' and userLabel in $baseStation group BY timeStamp,userLabel";
            } else {
                $sql = "select timeStamp,userLabel,$RSRP,count(distinct ecgi) as count_ecgi from mrs_rsrp where timeStamp <='$endTime' and timeStamp>='$startTime' group BY timeStamp,userLabel";
            }
            $res = $db->query("select count(*) from ($sql) as total");
            $total = $res->fetchAll(PDO::FETCH_NUM)[0];

            $sql = $sql." order by timeStamp limit $limit offset $start";
            $res = $db->query($sql);               
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    $timeStamp = $row[0];
                    $userLabel = $row[1];
                    $temp      = array();
                    $sum_RSRP  =0;
                    for ($i = 2; $i <= 49; $i++) {
                        $temp['RSRP'.($i - 2)] = $row[$i];
                        $sum_RSRP+=$row[$i];
                    }                       
                    $result = $this->getResult($temp, $sum_RSRP);                         
                    $temp=array();
                    for ($i = 2; $i <= 49; $i++) {                          
                        $temp[trans('message.RSRP'.'.RSRP'.($i-2))] = $row[$i];
                    }
                    // $key=array_keys($temp);
                    if ($regionType=="baseStationGroup") {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[50],"city" => $city, "date" => $timeStamp, "baseStationGroup"=>'baseStationGroup', "userLabel" => $userLabel,"总RSRP采样点总数"=>$sum_RSRP, "RSRP平均覆盖率"=>$result['avg_RSRP'], "RSRP的比例"=>$result['avgs']), $temp);
                    } else {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[50],"city" => $city, "date" => $timeStamp, "userLabel" => $userLabel,"总RSRP采样点总数"=>$sum_RSRP,"RSRP平均覆盖率"=>$result['avg_RSRP'],"RSRP的比例"=>$result['avgs']), $temp);
                    }
                    array_push($items, $temp);
                }
            }
            $result['total'] = $total;
            // $result['key']=$key;
            $result['records'] = $items;
            echo json_encode($result);
            return;
        } else if (($regionType == "groupEcgi"||$regionType=="cellGroup") && $timeType == "day") {
            if ($regionType=="cellGroup") {
                $groupBy  =' ';
            } else {
                $groupBy = ',ecgi ';
            }
            if ($groupEcgiStr) {
                $sql = "select timeStamp,userLabel,ecgi,$RSRP,count(distinct ecgi) from mrs_rsrp  where timeStamp <='$endTime' and timeStamp>='$startTime' and ecgi in $groupEcgi group BY timeStamp,userLabel".$groupBy;
            } else {
                $sql = "select timeStamp,userLabel,ecgi,$RSRP,count(distinct ecgi) from mrs_rsrp where timeStamp <='$endTime' and timeStamp>='$startTime' group BY timeStamp,userLabel".$groupBy;
            }
            $res = $db->query("select count(*) from ($sql) as total");
            $total = $res->fetchAll(PDO::FETCH_NUM)[0];

            $sql = $sql." order by timeStamp limit $limit offset $start";
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    $timeStamp = $row[0];
                    $userLabel = $row[1];
                    $ecgi      = $row[2];
                
                    $temp      = array();
                    $sum_RSRP  =0;
                    for ($i = 3; $i <= 50; $i++) {
                        $temp['RSRP'.($i - 3)] = $row[$i];
                        $sum_RSRP+=$row[$i];
                    }                       
                    $result = $this->getResult($temp, $sum_RSRP);                         
                    $temp=array();
                    for ($i = 3; $i <= 50; $i++) {                          
                        $temp[trans('message.RSRP'.'.RSRP'.($i-3))] = $row[$i];
                    }
                    // $key=array_keys($temp);
                    if ($regionType=="cellGroup") {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[51],"city" => $city, "date" => $timeStamp, "cellGroup"=>'cellGroup' ,"userLabel" => $userLabel, "ecgi" => $ecgi,"总RSRP采样点总数"=>$sum_RSRP,"RSRP平均覆盖率"=>$result['avg_RSRP'],"RSRP的比例"=>$result['avgs']), $temp);
                    } else {
                        $temp = array_merge(array("id" => $id++, "city" => $city, "date" => $timeStamp, "userLabel" => $userLabel, "ecgi" => $ecgi,"总RSRP采样点总数"=>$sum_RSRP,"RSRP平均覆盖率"=>$result['avg_RSRP'],"RSRP的比例"=>$result['avgs']), $temp);
                    }
                    array_push($items, $temp);
                }
            }
            $result['total'] = $total;
            // $result['key']=$key;   
            $result['records'] = $items;
            echo json_encode($result);
            return;
        } else if (($regionType == "baseStation"||$regionType=="baseStationGroup") && $timeType == "hour") {
            $filter = "";
            if ($baseStationStr) {
                $filter = $filter." and userLabel in $baseStation ";
            }
            if ($hour) {
                $filter = $filter." and hourId in ($hour)";
            }
            $sql = "select timeStamp,hourId,userLabel,$RSRP,count(distinct ecgi) as count_ecgi from mrs_rsrp where timeStamp>='$startTime' and timeStamp <= '$endTime' ".$filter." group BY timeStamp,userLabel,hourId";
            $res = $db->query("select count(*) from ($sql) as total");
            $total = $res->fetchAll(PDO::FETCH_NUM)[0];

            $sql = $sql." order by timeStamp,userLabel,hourId limit $limit offset $start";
            $res = $db->query($sql);            
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    $timeStamp = $row[0];
                    $hourId    = $row[1];
                    $userLabel = $row[2];
                    $temp      = array();
                    $sum_RSRP  =0;
                    for ($i = 3; $i <= 50; $i++) {
                        $temp['RSRP'.($i - 3)] = $row[$i];
                        $sum_RSRP+=$row[$i];
                    }                       
                    $result = $this->getResult($temp, $sum_RSRP);                         
                    $temp=array();
                    for ($i = 3; $i <= 50; $i++) {                          
                        $temp[trans('message.RSRP'.'.RSRP'.($i-3))] = $row[$i];
                    }
                    // $key=array_keys($temp);
                    if ($regionType=="baseStationGroup") {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[51], "city" => $city, "date" => 
                        $timeStamp, "hourId" => $hourId,"baseStationGroup"=>'baseStationGroup' ,"userLabel" => 
                        $userLabel,"总RSRP采样点总数"=>$sum_RSRP,"RSRP平均覆盖率"=>$result['avg_RSRP'],"RSRP的比例"=>$result['avgs']), $temp);
                    } else {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[51], "city" => $city, "date" => $timeStamp, "hourId" => $hourId, "userLabel" => $userLabel  ,"总RSRP采样点总数"=>$sum_RSRP,"RSRP平均覆盖率"=>$result['avg_RSRP'],"RSRP的比例"=>$result['avgs']), $temp);
                    }
                    array_push($items, $temp);
                }
            }
            $result['total'] = $total;
            // $result['key']=$key;
            $result['records'] = $items;
            echo json_encode($result);
            return;
        } else if (($regionType == "groupEcgi"||$regionType=="cellGroup") && $timeType == "hour") {
            if ($regionType=="cellGroup") {
                $groupBy  =' ';
            } else {
                $groupBy = ',ecgi ';
            }
            $filter = "";
            if ($groupEcgiStr) {
                $filter = $filter." and ecgi in $groupEcgi";
            }
            if ($hour) {
                $filter = $filter." and hourId in ($hour)";
            }
            $sql = "select timeStamp,hourId,userLabel,ecgi,$RSRP,count(distinct ecgi)from mrs_rsrp  where timeStamp>='$startTime' and timeStamp<='$endTime' ".$filter." group BY timeStamp,hourId,userLabel".$groupBy;
            $res = $db->query("select count(*) from ($sql) as total");
            $total = $res->fetchAll(PDO::FETCH_NUM)[0];

            $sql = $sql." order by  timeStamp,ecgi,hourId limit $limit offset $start";
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    $timeStamp = $row[0];
                    $hourId    = $row[1];
                    $userLabel = $row[2];
                    $ecgi      = $row[3];
                    
                    $temp      = array();
                    $sum_RSRP  = 0;
                    for ($i = 4; $i <= 51; $i++) {
                        $temp['RSRP'.($i - 4)] = $row[$i];
                        $sum_RSRP+=$row[$i];
                    }
                    $result = $this->getResult($temp, $sum_RSRP);                        
                    $temp=array();
                    for ($i = 4; $i <= 51; $i++) {                         
                        $temp[trans('message.RSRP'.'.RSRP'.($i-4))] = $row[$i];
                    }
                    // $key=array_keys($temp);
                    if ($regionType=="cellGroup") {
                        $temp = array_merge(array("id" => $id++, "city" => $city, "date" => $timeStamp, "hourId" => $hourId,"cellGroup"=>'cellGroup',"cellTotal"=>$row[52],"userLabel" => $userLabel, "ecgi" => $ecgi,"总RSRP采样点总数"=>$sum_RSRP,"RSRP平均覆盖率"=>$result['avg_RSRP'],"RSRP的比例"=>$result['avgs']), $temp);
                    } else {
                        $temp = array_merge(array("id" => $id++, "city" => $city, "date" => $timeStamp, "hourId" => $hourId, "userLabel" => $userLabel, "ecgi" => $ecgi,"总RSRP采样点总数"=>$sum_RSRP,"RSRP平均覆盖率"=>$result['avg_RSRP'],"RSRP的比例"=>$result['avgs']), $temp);
                    }
                    array_push($items, $temp);
                }
            }
            $result['total'] = $total;
            // $result['key']=$key;
            $result['records'] = $items;
            echo json_encode($result);
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
        $city       = input::get("citys");
        // $cityArr     = explode(",", $citys);
        $baseStationStr = input::get("baseStation");
        $baseStationArr = explode(",", $baseStationStr);
        $groupEcgiStr   = input::get("groupEcgi");
        $groupEcgiArr   = explode(",", $groupEcgiStr);
        $timeType   = input::get("timeType");
        $startT     = input::get("startTime");
        $endT       = input::get("endTime");
        $startTime  = min($startT, $endT);
        $endTime    = max($startT, $endT);
        $base='';
        foreach ($baseStationArr as $baseStation) {
            $base .= "'".$baseStation."',";
        }   
        $baseStation = "(".substr($base, 0, -1).")";
        $group='';
        if ($groupEcgiStr) {
            $dbg=new DataBaseConnection();
            $dbgs=$dbg->getDB('mongs', 'mongs');
            foreach ($groupEcgiArr as $key => $v) {               
                $sql="select ecgi from siteLte where cellName='$v' limit 1";
                $row=$dbgs->query($sql)->fetchAll(PDO::FETCH_NUM);
                if ($row) {
                    $group .= "'".$row[0][0]."',";
                } else {
                    $group .= "'".$v."',";                    
                }
            }
            $groupEcgi = "(".substr($group, 0, -1).")";
        }
        $hour = input::get("hour");
        $RSRPArr = array();
        for ($i = 0; $i < 48; $i++) {
            if ($i < 10) {
                $n = "0".$i;
            } else {
                $n = $i;
            }
            // if ($regionType == "city") {
            //     array_push($RSRPArr, "sum(mr_RSRP_".$n.") as mr_RSRP_".$n);
            // } else {
            //     array_push($RSRPArr, "mr_RSRP_".$n);
            // }
            array_push($RSRPArr, "sum(mr_RSRP_".$n.") as mr_RSRP_".$n);
        }
        $RSRP = implode(",", $RSRPArr);
        $dbc    = new DataBaseConnection();
        $db  = $dbc->getPGSQL($city);
        $result = array();
        $items  = array();
        if ($regionType == "city" && $timeType == "day") {
            $sql = "select timeStamp,$RSRP,count(distinct ecgi) from mrs_rsrp where timeStamp >='$startTime' and timeStamp<='$endTime' group by timeStamp";
            $res =$db->query($sql);                
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    $timeStamp = $row[0];
                    $temp      = array();
                    $sum_RSRP  = 0;
                    for ($i = 1; $i <= 48; $i++) {
                        $temp['RSRP'.($i - 1)] = $row[$i];
                        $sum_RSRP+=$row[$i];
                    }
                    $result = $this->getResult($temp, $sum_RSRP);
                    $temp = array_merge(array("date" => $timeStamp,"cellTotal"=>$row[49], "city" => $city,"总RSRP采样点总数"=>$sum_RSRP,"RSRP平均覆盖率"=>$result['avg_RSRP'],"RSRP的比例"=>$result['avgs']), $temp);
                    array_push($items, $temp);
                }
            }
        } else if ($regionType == "city" && $timeType == "hour") {
            if ($hour) {
                $sql = "select timeStamp,hourId,$RSRP,count(distinct ecgi) from mrs_rsrp where timeStamp >='$startTime' and timeStamp<='$endTime' and hourId in ($hour) group BY timeStamp,hourId order by timeStamp,hourId";
            } else {
                $sql = "select timeStamp,hourId,$RSRP,count(distinct ecgi) from mrs_rsrp where timeStamp >='$startTime' and timeStamp<='$endTime' group BY timeStamp,hourId order by timeStamp,hourId";
            }
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    $timeStamp = $row[0];
                    $hourId    = $row[1];
                    $temp      = array();
                    $sum_RSRP  = 0;
                    for ($i = 2; $i <= 49; $i++) {
                        $temp['RSRP'.($i - 2)] = $row[$i];
                        $sum_RSRP+=$row[$i];
                    }
                    $result = $this->getResult($temp, $sum_RSRP);                      
                    $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId,"cellTotal"=>$row[50], "city" => $city,"总RSRP采样点总数"=>$sum_RSRP,"RSRP平均覆盖率"=>$result['avg_RSRP'],"RSRP的比例"=>$result['avgs']), $temp);
                    array_push($items, $temp);
                }
            }
        } else if (($regionType == "baseStation"||$regionType=="baseStationGroup") && $timeType == "day") {
            if ($baseStationStr) {
                $sql = "select timeStamp,userLabel,$RSRP,count(distinct ecgi) as count_ecgi from mrs_rsrp where timeStamp>='$startTime' and timeStamp<='$endTime' and userLabel in $baseStation group BY timeStamp,userLabel order by timeStamp";
            } else {
                $sql = "select timeStamp,userLabel,$RSRP,count(distinct ecgi) as count_ecgi from mrs_rsrp where timeStamp>='$startTime' and timeStamp<='$endTime' group BY timeStamp,userLabel order by timeStamp";
            }
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    $timeStamp = $row[0];
                    $userLabel = $row[1];
                    $temp      = array();
                    $sum_RSRP  = 0;
                    for ($i = 2; $i <= 49; $i++) {
                        $temp['RSRP'.($i - 2)] = $row[$i];
                        $sum_RSRP+=$row[$i];
                    }
                    $result=$this->getResult($temp, $sum_RSRP);
                    if ($regionType=="baseStationGroup") {
                        $temp = array_merge(array("date" => $timeStamp,"cellTotal"=>$row[50], "city" => $city,"baseStationGroup"=>'baseStationGroup', "userLabel" => $userLabel,"总RSRP采样点总数"=>$sum_RSRP,"RSRP平均覆盖率"=>$result['avg_RSRP'],"RSRP的比例"=>$result['avgs']), $temp);
                    } else {
                        $temp = array_merge(array("date" => $timeStamp,"cellTotal"=>$row[50], "city" => $city, "userLabel" => $userLabel,"总RSRP采样点总数"=>$sum_RSRP,"RSRP平均覆盖率"=>$result['avg_RSRP'],"RSRP的比例"=>$result['avgs']), $temp);
                    }
                    array_push($items, $temp);
                }
            }
        } else if (($regionType == "groupEcgi"||$regionType=="cellGroup") && $timeType == "day") {
            if ($regionType=="cellGroup") {
                $groupBy  =' ';
            } else {
                $groupBy = ',ecgi ';
            }
            if ($groupEcgiStr) {
                $sql = "select timeStamp,userLabel,ecgi,$RSRP,count(distinct ecgi) from mrs_rsrp  where timeStamp <='$endTime' and timeStamp>='$startTime' and ecgi in $groupEcgi group BY timeStamp".$groupBy." order by timeStamp";
            } else {
                $sql = "select timeStamp,userLabel,ecgi,$RSRP,count(distinct ecgi) from mrs_rsrp  where timeStamp <='$endTime' and timeStamp>='$startTime' group BY timeStamp,userLabel".$groupBy." order by timeStamp";
            }
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    $timeStamp = $row[0];
                    $userLabel = $row[1];
                    $ecgi      = $row[2];
                    // $cellName  = $row[51];
                    $temp      = array();
                    $sum_RSRP  = 0;
                    for ($i = 3; $i <= 50; $i++) {
                        $temp['RSRP'.($i - 3)] = $row[$i];
                        $sum_RSRP+=$row[$i];
                    }
                    $result = $this->getResult($temp, $sum_RSRP);
                    if ($regionType=="cellGroup") {
                        $temp = array_merge(array("date" => $timeStamp,"cellTotal"=>$row[51], "city" => $city,"cellGroup"=>'cellGroup' ,"userLabel" => $userLabel,"ecgi" => $ecgi,"总RSRP采样点总数"=>$sum_RSRP,"RSRP平均覆盖率"=>$result['avg_RSRP'],"RSRP的比例"=>$result['avgs']), $temp);
                    } else {
                        $temp = array_merge(array("date" => $timeStamp, "city" => $city, "userLabel" => $userLabel,"ecgi" => $ecgi,"总RSRP采样点总数"=>$sum_RSRP,"RSRP平均覆盖率"=>$result['avg_RSRP'],"RSRP的比例"=>$result['avgs']), $temp);
                    }
                    array_push($items, $temp);
                }
            }
        } else if (($regionType == "baseStation"||$regionType=="baseStationGroup") && $timeType == "hour") {
            $filter = "";
            if ($baseStationStr) {
                $filter = $filter." and userLabel in $baseStation";
            }
            if ($hour) {
                $filter = $filter." and hourId in ($hour)";
            }
            $sql = "select timeStamp,hourId,userLabel,$RSRP,count(distinct ecgi) as count_ecgi from mrs_rsrp where timeStamp >='$startTime' and timeStamp<='$endTime' ".$filter." group BY timeStamp,hourId,userLabel order by timeStamp,ecgi,hourId";
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    $timeStamp = $row[0];
                    $hourId    = $row[1];
                    $userLabel = $row[2];
                    $temp      = array();
                    $sum_RSRP  = 0;
                    for ($i = 3; $i <= 50; $i++) {
                        $temp['RSRP'.($i - 3)] = $row[$i];
                        $sum_RSRP+=$row[$i];
                    }
                    $result=$this->getResult($temp, $sum_RSRP);
                    if ($regionType=="baseStationGroup") {
                        $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId,"cellTotal"=>$row[51], "city" => $city,"baseStationGroup"=>'baseStationGroup',"userLabel" => $userLabel,"总RSRP采样点总数"=>$sum_RSRP,"RSRP平均覆盖率"=>$result['avg_RSRP'],"RSRP的比例"=>$result['avgs']), $temp);
                    } else {
                        $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId,"cellTotal"=>$row[51], "city" => $city, "userLabel" => $userLabel,"总RSRP采样点总数"=>$sum_RSRP,"RSRP平均覆盖率"=>$result['avg_RSRP'],"RSRP的比例"=>$result['avgs']), $temp);
                    }
                    array_push($items, $temp);
                }
            }
        } else if (($regionType == "groupEcgi"||$regionType=="cellGroup") && $timeType == "hour") {
            if ($regionType=="cellGroup") {
                $groupBy  =' ';
            } else {
                $groupBy = ',ecgi ';
            }
            $filter = "";
            if ($groupEcgiStr) {
                $filter = $filter." and ecgi in $groupEcgi";
            }
            if ($hour) {
                $filter = $filter." and hourId in ($hour)";
            }
            $sql = "select timeStamp,hourId,userLabel,ecgi,$RSRP,count(distinct ecgi) from mrs_rsrp where timeStamp>='$startTime' and timeStamp<='$endTime' ".$filter." group BY timeStamp,hourId,userLabel".$groupBy."  order by  timeStamp,ecgi,hourId";
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    $timeStamp = $row[0];
                    $hourId    = $row[1];
                    $userLabel = $row[2];
                    $ecgi      = $row[3];
                    // $cellName  =$row[52];
                    $temp      = array();
                    $sum_RSRP  =0;
                    for ($i = 4; $i <= 51; $i++) {
                        $temp['RSRP'.($i - 4)] = $row[$i];
                        $sum_RSRP+=$row[$i];
                    }
                    $result = $this->getResult($temp, $sum_RSRP);
                    if ($regionType=="cellGroup") {
                        $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId,"cellTotal"=>$row[52], "city" => $city,"cellGroup"=>'cellGroup', "userLabel" => $userLabel, "ecgi" => $ecgi,"总RSRP采样点总数"=>$sum_RSRP,"RSRP平均覆盖率"=>$result['avg_RSRP'],"RSRP的比例"=>$result['avgs']), $temp);
                    } else {
                        $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId, "city" => $city, "userLabel" => $userLabel, "ecgi" => $ecgi,"总RSRP采样点总数"=>$sum_RSRP,"RSRP平均覆盖率"=>$result['avg_RSRP'],"RSRP的比例"=>$result['avgs']), $temp);
                    }
                    array_push($items, $temp);
                }
            }
        }//end if

        $filename = "files/覆盖率查询".date('YmdHis').".csv";
        if ($regionType == "city" && $timeType == "day") {
            $text = "date,cellTotal,city,总RSRP采样点总数,RSRP平均覆盖率,RSRP>=-110的比例";
        } else if ($regionType == "city" && $timeType == "hour") {
            $text = "date,hourId,cellTotal,city,总RSRP采样点总数,RSRP平均覆盖率,RSRP>=-110的比例";
        } else if ($regionType == "baseStation" && $timeType == "day") {
            $text = "date,cellTotal,city,userLabel,总RSRP采样点总数,RSRP平均覆盖率,RSRP>=-110的比例";
        } else if ($regionType == "baseStation" && $timeType == "hour") {
            $text = "date,hourId,cellTotal,city,userLabel,总RSRP采样点总数,RSRP平均覆盖率,RSRP>=-110的比例";
        } else if ($regionType == "baseStationGroup" && $timeType == "day") {
            $text = "date,cellTotal,city,baseStationGroup,userLabel,总RSRP采样点总数,RSRP平均覆盖率,RSRP>=-110的比例";
        } else if ($regionType == "baseStationGroup" && $timeType == "hour") {
            $text = "date,hourId,cellTotal,city,baseStationGroup,userLabel,总RSRP采样点总数,RSRP平均覆盖率,RSRP>=-110的比例";
        } else if ($regionType == "groupEcgi" && $timeType == "day") {
            $text = "date,city,userLabel,ecgi,总RSRP采样点总数,RSRP平均覆盖率,RSRP>=-110的比例";
        } else if ($regionType == "groupEcgi" && $timeType == "hour") {
            $text = "date,hourId,city,userLabel,ecgi,总RSRP采样点总数,RSRP平均覆盖率,RSRP>=-110的比例";
        } else if ($regionType == "cellGroup" && $timeType == "day") {
            $text = "date,cellTotal,city,cellGroup,userLabel,ecgi,总RSRP采样点总数,RSRP平均覆盖率,RSRP>=-110的比例";
        } else if ($regionType == "cellGroup" && $timeType == "hour") {
            $text = "date,hourId,cellTotal,city,cellGroup,userLabel,ecgi,总RSRP采样点总数,RSRP平均覆盖率,RSRP>=-110的比例";
        }

        for ($i = 0; $i < 48; $i++) {
            // $text = $text.",RSRP".$i;
            $text = $text.","." ".trans('message.RSRP'.'.RSRP'.$i);
        }
        // $result['text']   = $text;
        // $result['rows']   = $items;
        $result['total']  = count($items);
        $result['result'] = 'true';
        // $this->resultToCSV2($result, $filename);
        $fileUtil = new FileUtil();
        $fileUtil->resultToCSV2($text, $items, $filename);
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


    /**
     * 获得加权和
     *
     * @param array $array 原始记录
     *
     * @return string
     */
    protected function getResultByWeight($array)
    {
        $weight = array(
                   0.1,
                   0.35,
                   0.75,
                   1.5,
                   2.5,
                   3.5,
                   4.5,
                   5.5,
                   6.5,
                   7.5,
                   8.5,
                   9.5,
                   11,
                   13,
                   15,
                   17,
                   19,
                   22.5,
                   27.5,
                   32.5,
                   37.5,
                   42.5,
                   47.5,
                   55,
                   65,
                   75,
                   85,
                   495,);
        $sum    = array_sum($array);
        $result = 0;
        if ($sum != 0) {
            foreach ($array as $key => $value) {
                $result = ($result + $value * ($weight[$key] / 100) / $sum);
            }
        }
        return number_format($result, 4);

    }//end getResultByWeight()

    /**
     * 获取参数值
     *
     * @param array $temp 参数数组
     * @param int $sum 参数个数
     * 
     * @return array()
     */
    public function getResult($temp,$sum)
    {   
        if ($sum) {
            $avg_RSRP=($temp['RSRP0']*(-124)+
                            $temp['RSRP1']*(-117.5)
                            +$temp['RSRP2']*(-114.5)
                            +$temp['RSRP3']*(-113.5)
                            +$temp['RSRP4']*(-112.5)
                            +$temp['RSRP5']*(-111.5)
                            +$temp['RSRP6']*(-110.5)
                            +$temp['RSRP7']*(-109.5)
                            +$temp['RSRP8']*(-108.5)
                            +$temp['RSRP9']*(-107.5)
                            +$temp['RSRP10']*(-106.5)
                            +$temp['RSRP11']*(-105.5)
                            +$temp['RSRP12']*(-104.5)
                            +$temp['RSRP13']*(-103.5)
                            +$temp['RSRP14']*(-102.5)
                            +$temp['RSRP15']*(-101.5)
                            +$temp['RSRP16']*(-100.5)
                            +$temp['RSRP17']*(-99.5)
                            +$temp['RSRP18']*(-98.5)
                            +$temp['RSRP19']*(-97.5)
                            +$temp['RSRP20']*(-96.5)
                            +$temp['RSRP21']*(-95.5)
                            +$temp['RSRP22']*(-94.5)
                            +$temp['RSRP23']*(-93.5)
                            +$temp['RSRP24']*(-92.5)
                            +$temp['RSRP25']*(-91.5)
                            +$temp['RSRP26']*(-90.5)
                            +$temp['RSRP27']*(-89.5)
                            +$temp['RSRP28']*(-88.5)
                            +$temp['RSRP29']*(-87.5)
                            +$temp['RSRP30']*(-86.5)
                            +$temp['RSRP31']*(-85.5)
                            +$temp['RSRP32']*(-84.5)
                            +$temp['RSRP33']*(-83.5)
                            +$temp['RSRP34']*(-82.5)
                            +$temp['RSRP35']*(-81.5)
                            +$temp['RSRP36']*(-80.5)
                            +$temp['RSRP37']*(-79)
                            +$temp['RSRP38']*(-77)
                            +$temp['RSRP39']*(-75)
                            +$temp['RSRP40']*(-73)
                            +$temp['RSRP41']*(-71)
                            +$temp['RSRP42']*(-69)
                            +$temp['RSRP43']*(-67)
                            +$temp['RSRP44']*(-65)
                            +$temp['RSRP45']*(-63)
                            +$temp['RSRP46']*(-61)
                            +$temp['RSRP47']*(-60))/$sum;
            $avgs =1-(($temp['RSRP0']
                            +$temp['RSRP1']
                            +$temp['RSRP2']
                            +$temp['RSRP3']
                            +$temp['RSRP4']
                            +$temp['RSRP5']
                            +$temp['RSRP6'])/$sum);
        } else {
            $avgs=0;
            $avg_RSRP=0;
        }
        $result['avg_RSRP'] = $avg_RSRP;
        $result['avgs']     = $avgs;
        return $result;
    }
}//end class
