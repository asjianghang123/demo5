<?php

/**
 * SurveyController.php
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
 * 
 * Class SurveyTADVController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class SurveyTADVController extends Controller
{

    /**
     * 获得表格数据
     *
     * @return void
     */
    public function getTableData()
    {   
        $survey      = input::get("survey");
        $regionType  = input::get("regionType");
        $city       = input::get("citys");
        // $cityArr     = explode(",", $citys);
        $baseStationStr = input::get("baseStation");
        $baseStationArr = explode(",", $baseStationStr);
        $groupEcgiStr   = input::get("groupEcgi");
        $groupEcgiArr =explode(",", $groupEcgiStr);
        $timeType    = input::get("timeType");
        // $date        = input::get("date");
        $startT      = input::get("startTime");
        $endT        = input::get("endTime");

        $limit = input::get("limit");
        $page = input::get("page");
        $start = ($page - 1) * $limit;

        $startTime   = min($startT, $endT);
        $endTime     = max($startT, $endT);
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
        $sum_num=45;
        $name='TADV';
        $searchArr = array();
        for ($i = 0; $i < $sum_num; $i++) {
            if ($i < 10) {
                $n = "0".$i;
            } else {
                $n = $i;
            }
            // if ($regionType == "city") {
            //     array_push($searchArr, "sum(mr_".$name."_".$n.") as mr_".$name."_".$n);
            // } else {
            //     array_push($searchArr, "mr_".$name."_".$n);
            // }
            array_push($searchArr, "sum(mr_".$name."_".$n.") as mr_".$name."_".$n);
        }  
        $searchArr = implode(",", $searchArr);
        $dbc    = new DataBaseConnection();
        $db  = $dbc->getPGSQL($city);
        $result = array();
        $items  = array();
        $id     = 1;
        if ($regionType == "city" && $timeType == "day") {
            $sql = "select timeStamp,$searchArr,count(distinct ecgi) from mrs_tadv where timeStamp >='$startTime' and timeStamp<='$endTime' group by timeStamp";
            $res = $db->query("select count(*) from ($sql) as total");
            $total = $res->fetchAll(PDO::FETCH_NUM)[0];
            $result['total'] = $total;

            $sql = $sql." limit $limit offset $start";
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {                   
                    $timeStamp = $row[0];
                    $temp      = array();
                    for ($i = 1; $i <= $sum_num; $i++) {
                        if ($i-1<10) {
                            $k="0".($i-1);
                        } else {
                            $k=($i-1);
                        }
                        $temp[$name.$k] = $row[$i];
                        }
                        $ue_avg=$this->getueAvg($temp);
                    $temp = array_merge(array("id" => $id++, "cellTotal"=>$row[46], "city" => $city, "终端平均距离"=>$ue_avg, "date" => $timeStamp), $temp);
                    array_push($items, $temp);
                }
            }
            $result['records'] = $items;
            echo json_encode($result);
            return;
        } else if ($regionType == "city" && $timeType == "hour") {
            $stmt = '';
            if ($hour) {
                $sql = "select timeStamp,hourId,$searchArr,count(distinct ecgi) from mrs_tadv where timeStamp >='$startTime' and timeStamp<='$endTime' and hourId in ($hour) group BY timeStamp,hourId";
            } else {
                $sql = "select timeStamp,hourId,$searchArr,count(distinct ecgi) from mrs_tadv where timeStamp >='$startTime' and timeStamp<='$endTime' group BY timeStamp,hourId";
            }
            $res = $db->query("select count(*) from ($sql) as total");
            $total = $res->fetchAll(PDO::FETCH_NUM)[0];
            $result['total'] = $total;

            $sql = $sql." order by timeStamp,hourId limit $limit offset $start";
            $res = $db->query($sql);               
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {                  
                    $timeStamp = $row[0];
                    $hourId    = $row[1];
                    $temp      = array();   
                    for ($i = 2; $i <= ($sum_num+1); $i++) {
                        if ($i-2<10) {
                            $k="0".($i-2);
                        } else {
                            $k=($i-2);
                        }
                        $temp[$name.$k] = $row[$i];
                        }
                    $ue_avg=$this->getueAvg($temp);
                    $temp = array_merge(array("id" => $id++, "cellTotal"=>$row[47], "city" => $city, "终端平均距离"=>$ue_avg, "date" => $timeStamp, "hourId" => $hourId), $temp);
                    array_push($items, $temp);
                }
            }
            $result['records'] = $items;
            echo json_encode($result);
            return;
        } else if (($regionType == "baseStation"||$regionType=="baseStationGroup") && $timeType == "day") {
            if ($baseStationStr) {
                $sql = "select timeStamp,userLabel,$searchArr,count(distinct ecgi) as count_ecgi from mrs_tadv where timeStamp <='$endTime' and timeStamp>='$startTime' and userLabel in $baseStation group BY timeStamp,userLabel";
            } else {
                $sql = "select timeStamp,userLabel,$searchArr,count(distinct ecgi) as count_ecgi from mrs_tadv where timeStamp >='$startTime' and timeStamp<='$endTime' group BY timeStamp,userLabel";
            }
            $res = $db->query("select count(*) from ($sql) as total");
            $total = $res->fetchAll(PDO::FETCH_NUM)[0];
            $result['total'] = $total;

            $sql = $sql." order by timeStamp limit $limit offset $start";
            $res = $db->query($sql);       
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {                    
                    $timeStamp = $row[0];
                    $userLabel = $row[1];
                    $temp      = array();
                        for ($i = 2; $i <= ($sum_num+1); $i++) {
                        if ($i-2<10) {
                            $k="0".($i-2);
                        } else {
                            $k=($i-2);
                        }
                        $temp[$name.$k] = $row[$i];
                        }
                        $ue_avg=$this->getueAvg($temp);
                    if ($regionType=='baseStationGroup') {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[47], "city" => $city,"终端平均距离"=>$ue_avg, "date" => $timeStamp,"baseStationGroup"=>'baseStationGroup', "userLabel" => $userLabel), $temp);
                    } else {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[47], "city" => $city,"终端平均距离"=>$ue_avg, "date" => $timeStamp, "userLabel" => $userLabel), $temp);
                    }
                    array_push($items, $temp);
                }
            }
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
                $sql = "select timeStamp,userLabel,ecgi,$searchArr,count(distinct ecgi) from mrs_tadv where timeStamp>='$startTime' and timeStamp<='$endTime' and ecgi in $groupEcgi  group BY timeStamp,userLabel".$groupBy;
            } else {
                $sql = "select timeStamp,userLabel,ecgi,$searchArr,count(distinct ecgi) from mrs_tadv where timeStamp>='$startTime' and timeStamp<='$endTime' group BY  timeStamp,userLabel".$groupBy;
            }
            $res = $db->query("select count(*) from ($sql) as total");
            $total = $res->fetchAll(PDO::FETCH_NUM)[0];
            $result['total'] = $total;

            $sql = $sql." order by timeStamp,userLabel limit $limit offset $start";
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {             
                    $timeStamp = $row[0];
                    $userLabel = $row[1];
                    $ecgi      = $row[2];
                    $temp      = array();                   
                    for ($i = 3; $i <= ($sum_num+2); $i++) {
                        if ($i-3<10) {
                            $k="0".($i-3);
                        } else {
                            $k=($i-3);
                        }
                        $temp[$name.$k] = $row[$i];
                        }
                        $ue_avg=$this->getueAvg($temp);
                    if ($regionType=="cellGroup") {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[48],"city" => $city,"终端平均距离"=>$ue_avg, "date" => $timeStamp, "cellGroup"=>'cellGroup',"userLabel" => $userLabel, "ecgi" => $ecgi), $temp);
                    } else {
                        $temp = array_merge(array("id" => $id++, "city" => $city, "终端平均距离"=>$ue_avg, "date" => $timeStamp, "userLabel" => $userLabel, "ecgi" => $ecgi), $temp);
                    }
                    array_push($items, $temp);
                }
            }
            $result['records'] = $items;
            echo json_encode($result);
            return;
        } else if (($regionType == "baseStation"||$regionType=="baseStationGroup") && $timeType == "hour") {
            $filter = "";
            if ($baseStationStr) {
                $filter = $filter." and userLabel in $baseStation";
            }

            if ($hour) {
                 $filter = $filter." and hourId in ($hour)";
            }
            $sql = "select timeStamp,hourId,userLabel,$searchArr,count(distinct ecgi) as count_ecgi from mrs_tadv where timeStamp >='$startTime' and timeStamp<='$endTime' ".$filter." group BY timeStamp,userLabel,hourId";
            $res = $db->query("select count(*) from ($sql) as total");
            $total = $res->fetchAll(PDO::FETCH_NUM)[0];
            $result['total'] = $total;

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
                    for ($i = 3; $i <= ($sum_num+2); $i++) {
                        if ($i-3<10) {
                            $k="0".($i-3);
                        } else {
                            $k=($i-3);
                        }
                        $temp[$name.$k] = $row[$i];
                        }
                        $ue_avg=$this->getueAvg($temp);
                    if ($regionType=="baseStationGroup") {
                        $temp = array_merge(array("id" => $id++, "cellTotal"=>$row[48], "city" => $city, "终端平均距离"=>$ue_avg, "date" => $timeStamp, "hourId" => $hourId, "baseStationGroup"=>'baseStationGroup', "userLabel" => $userLabel), $temp);
                    } else {
                        $temp = array_merge(array("id" => $id++, "cellTotal"=>$row[48], "city" => $city, "终端平均距离"=>$ue_avg, "date" => $timeStamp, "hourId" => $hourId, "userLabel" => $userLabel), $temp);
                    }
                    array_push($items, $temp);
                }
            }
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
                $filter = $filter." and ecgi in $groupEcgi ";
            }

            if ($hour) {
                $filter = $filter." and hourId in ($hour)";
            }
            $sql = "select timeStamp,hourId,userLabel,ecgi,$searchArr,count(distinct ecgi) from mrs_tadv where timeStamp<='$endTime' and timeStamp>='$startTime' ".$filter." group BY timeStamp,userLabel,hourId".$groupBy;
            $res = $db->query("select count(*) from ($sql) as total");
            $total = $res->fetchAll(PDO::FETCH_NUM)[0];
            $result['total'] = $total;

            $sql = $sql." order by timeStamp,ecgi,hourId limit $limit offset $start";
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
                    for ($i = 4; $i <= ($sum_num+3); $i++) {
                        if ($i-4<10) {
                            $k="0".($i-4);
                        } else {
                            $k=($i-4);
                        }
                        $temp[$name.$k] = $row[$i];
                        }
                    $ue_avg=$this->getueAvg($temp);
                    if ($regionType=="cellGroup") {
                        $temp = array_merge(array("id" => $id++, "cellTotal"=>$row[49], "city" => $city, "终端平均距离"=>$ue_avg, "date" => $timeStamp, "hourId" => $hourId, "cellGroup"=>'cellGroup', "userLabel" => $userLabel, "ecgi" => $ecgi), $temp);
                    } else {
                        $temp = array_merge(array("id" => $id++, "city" => $city, "终端平均距离"=>$ue_avg, "date" => $timeStamp, "hourId" => $hourId, "userLabel" => $userLabel, "ecgi" => $ecgi), $temp);
                    }
                    array_push($items, $temp);
                }
            }
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
        $survey         = input::get("survey");
        $regionType      = input::get("regionType");
        $city           = input::get("citys");
        // $cityArr         = explode(",", $citys);
        $baseStationStr = input::get("baseStation");
        $baseStationArr = explode(",", $baseStationStr);
        $groupEcgiStr   = input::get("groupEcgi");
        $groupEcgiArr    = explode(",", $groupEcgiStr);
        $timeType       = input::get("timeType");    
        $startT         = input::get("startTime");
        $endT           = input::get("endTime");
        $startTime      = min($startT, $endT);
        $endTime        = max($startT, $endT);
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
        if ($survey=="PowerHeadRoom") {
            $sum_num=64;
            $name='PowerHeadRoom';
            $table="mrs_power_headroom";
        } else if ($survey=="PSRQ") {
            $sum_num=18;
            $name='RSRQ';
            $table="mrs_rsrq";
        } else if ($survey=="TADV") {
            $sum_num=45;
            $name='TADV';
            $table="mrs_tadv";
        } else if ($survey=="AOA") {
            $sum_num=72;
            $name='AOA';
            $table="mrs_aoa";
        }
        $searchArr = array();
        for ($i = 0; $i < $sum_num; $i++) {
            if ($i < 10) {
                $n = "0".$i;
            } else {
                $n = $i;
            }
            // if ($regionType == "city") {
            //     array_push($searchArr, "sum(mr_".$name."_".$n.") as mr_".$name."_".$n);
            // } else {
            //     array_push($searchArr, "mr_".$name."_".$n);
            // }
            array_push($searchArr, "sum(mr_".$name."_".$n.") as mr_".$name."_".$n);
        }
        $searchArr = implode(",", $searchArr);
        $dbc    = new DataBaseConnection();
        $db  = $dbc->getPGSQL($city);
        $result = array();
        $items  = array();
        if ($regionType == "city" && $timeType == "day") {
            $sql = "select timeStamp,$searchArr,count(distinct ecgi) from $table where timeStamp <= '$endTime' and timeStamp >='$startTime' group by timeStamp order by timeStamp";
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    $timeStamp = $row[0];
                    $temp      = array();
                    for ($i = 1; $i <= $sum_num; $i++) {
                        if ($i-1<10) {
                            $k="0".($i-1);
                        } else {
                            $k=($i-1);
                        }
                        $temp[$name.$k] = $row[$i];
                        }
                        $ue_avg=$this->getueAvg($temp);
                    $temp = array_merge(array("date" => $timeStamp, "cellTotal"=>$row[46], "city" => $city, "终端平均距离"=>$ue_avg), $temp);
                    array_push($items, $temp);
                }
            }
        } else if ($regionType == "city" && $timeType == "hour") {
            if ($hour) {
                $sql = "select timeStamp,hourId,$searchArr,count(distinct ecgi) from $table where timeStamp <='$startTime' and timeStamp>='$endTime' and hourId in ($hour) group BY timeStamp,hourId order by timeStamp,hourId";
            } else {
                $sql = "select timeStamp,hourId,$searchArr,count(distinct ecgi) from $table where timeStamp <='$startTime' and timeStamp >='$endTime' group BY timeStamp,hourId order by timeStamp,hourId";
            }
            $res = $db->query($sql);              
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    $timeStamp = $row[0];
                    $hourId    = $row[1];
                    $temp      = array();
                
                    for ($i = 2; $i <= ($sum_num+1); $i++) {
                        if ($i-2<10) {
                            $k="0".($i-2);
                        } else {
                            $k=($i-2);
                        }
                        $temp[$name.$k] = $row[$i];
                        }
                        $ue_avg=$this->getueAvg($temp);                       
                    $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId, "cellTotal"=>$row[47], "city" => $city, "终端平均距离"=>$ue_avg), $temp);
                    array_push($items, $temp);
                }
            }
        } else if (($regionType == "baseStation" ||$regionType=="baseStationGroup")&& $timeType == "day") {
            if ($baseStationStr) {
                $sql = "select timeStamp,userLabel,$searchArr,count(distinct ecgi) as count_ecgi from $table where timeStamp >='$startTime' and timeStamp<='$endTime' and userLabel in $baseStation group BY timeStamp,userLabel order by timeStamp";
            } else {
                $sql = "select timeStamp,userLabel,$searchArr,count(distinct ecgi) as count_ecgi from $table where timeStamp <='$endTime' and timeStamp>='$startTime' group BY timeStamp,userLabel order by timeStamp";
            }
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    $timeStamp = $row[0];
                    $userLabel = $row[1];
                    $temp      = array();
                    for ($i = 2; $i <= ($sum_num+1); $i++) {
                        // print_r($row[$i]);
                        // return;
                        if ($i-2<10) {
                            $k="0".($i-2);
                        } else {
                            $k=($i-2);
                        }
                        $temp[$name.$k] = $row[$i];
                        }
                        $ue_avg=$this->getueAvg($temp);
                    if ($regionType=='baseStationGroup') {
                        $temp = array_merge(array("date" => $timeStamp, "cellTotal"=>$row[47], "city" => $city, "终端平均距离"=>$ue_avg, "baseStationGroup"=>'baseStationGroup', "userLabel" => $userLabel), $temp);
                    } else {
                        $temp = array_merge(array("date" => $timeStamp, "cellTotal"=>$row[47], "city" => $city, "终端平均距离"=>$ue_avg, "userLabel" => $userLabel), $temp);
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
                $sql = "select timeStamp,userLabel,ecgi,$searchArr,count(distinct ecgi) from $table where timeStamp<='$endTime' and timeStamp>='$startTime' and ecgi in $groupEcgi group BY timeStamp,userLabel".$groupBy." order by timeStamp,userLabel";
            } else {
                $sql = "select timeStamp,userLabel,ecgi,$searchArr,count(distinct ecgi) from $table where timeStamp<='$endTime' and timeStamp>='$startTime' group BY timeStamp,userLabel".$groupBy." order by timeStamp,userLabel";
            }
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    $timeStamp = $row[0];
                    $userLabel = $row[1];
                    $ecgi      = $row[2];
                    $temp      = array();
                    for ($i = 3; $i <= ($sum_num+2); $i++) {
                        if ($i-3<10) {
                            $k="0".($i-3);
                        } else {
                            $k=($i-3);
                        }
                        $temp[$name.$k] = $row[$i];
                        }
                        $ue_avg=$this->getueAvg($temp);
                    if ($regionType=="cellGroup") {
                        $temp = array_merge(array("date" => $timeStamp, "cellTotal"=>$row[48], "city" => $city, "终端平均距离"=>$ue_avg, "cellGroup"=>'cellGroup', "userLabel" => $userLabel, "ecgi" => $ecgi), $temp);
                    } else {
                        $temp = array_merge(array("date" => $timeStamp, "city" => $city,"终端平均距离"=>$ue_avg, "userLabel" => $userLabel, "ecgi" => $ecgi), $temp);
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

            $sql = "select timeStamp,hourId,userLabel,$searchArr,count(distinct ecgi) as count_ecgi from $table where timeStamp <='$endTime' and timeStamp >= '$startTime' ".$filter." group BY timeStamp,userLabel,hourId order by timeStamp,userLabel,hourId";
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    $timeStamp=array();
                    $timeStamp = $row[0];
                    $hourId    = $row[1];
                    $userLabel = $row[2];
                    $temp      = array();
                    for ($i = 3; $i <= ($sum_num+2); $i++) {
                    if ($i-3<10) {
                        $k="0".($i-3);
                    } else {
                        $k=($i-3);
                    }
                        $temp[$name.$k] = $row[$i];
                        }
                        $ue_avg=$this->getueAvg($temp);
                    if ($regionType=="baseStationGroup") {
                        $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId, "cellTotal"=>$row[48], "city" => $city, "终端平均距离"=>$ue_avg, "baseStationGroup"=>'baseStationGroup', "userLabel" => $userLabel), $temp);
                    } else {
                        $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId,"cellTotal"=>$row[48], "city" => $city,"终端平均距离"=>$ue_avg, "userLabel" => $userLabel), $temp);
                    }
                    array_push($items, $temp);
                }
            }
        } else if (($regionType == "groupEcgi"||$regionType=="cellGroup" )&& $timeType == "hour") {
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
            $sql = "select timeStamp,hourId,userLabel,ecgi,$searchArr,count(distinct ecgi) from $table where timeStamp <='$endTime' and timeStamp>='$startTime' ".$filter." group BY timeStamp,userLabel,hourId".$groupBy." order by timeStamp,ecgi,hourId";
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {                 
                    $timeStamp= $row[0];
                    $hourId    = $row[1];
                    $userLabel = $row[2];
                    $ecgi      = $row[3];
                    $temp      = array();
                    for ($i = 4; $i <= ($sum_num+3); $i++) {
                    if ($i-4<10) {
                        $k="0".($i-4);
                    } else {
                        $k=($i-4);
                    }
                        $temp[$name.$k] = $row[$i];
                        }
                    $ue_avg=$this->getueAvg($temp);
                    if ($regionType=="cellGroup") {
                        $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId,"cellTotal"=>$row[49], "city" => $city,"终端平均距离"=>$ue_avg,"cellGroup"=>'cellGroup', "userLabel" => $userLabel, "ecgi" => $ecgi), $temp);
                    } else {
                        $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId, "city" => $city,"终端平均距离"=>$ue_avg, "userLabel" => $userLabel, "ecgi" => $ecgi), $temp);
                    }
                    array_push($items, $temp);
                }
            }
        }//end if

        $filename = "files/".$survey.date('YmdHis').".csv";
        if ($regionType == "city" && $timeType == "day") {
            $text = "date,cellTotal,city,终端平均距离";
        } else if ($regionType == "city" && $timeType == "hour") {
            $text = "date,hourId,cellTotal,city,终端平均距离";
        } else if ($regionType == "baseStation" && $timeType == "day") {
            $text = "date,cellTotal,city,终端平均距离,userLabel";
        } else if ($regionType == "baseStation" && $timeType == "hour") {
            $text = "date,hourId,cellTotal,city,终端平均距离,userLabel";
        } else if ($regionType == "baseStationGroup" && $timeType == "day") {
            $text = "date,cellTotal,city,终端平均距离,baseStationGroup,userLabel";
        } else if ($regionType == "baseStationGroup" && $timeType == "hour") {
            $text = "date,hourId,cellTotal,city,终端平均距离,baseStationGroup,userLabel";
        } else if ($regionType == "groupEcgi" && $timeType == "day") {
            $text = "date,city,终端平均距离,userLabel,ecgi";
        } else if ($regionType == "groupEcgi" && $timeType == "hour") {
            $text = "date,hourId,city,终端平均距离,userLabel,ecgi";
        } else if ($regionType == "cellGroup" && $timeType == "day") {
            $text = "date,cellTotal,city,终端平均距离,cellGroup,userLabel,ecgi";
        } else if ($regionType == "cellGroup" && $timeType == "hour") {
            $text = "date,hourId,cellTotal,city,终端平均距离,cellGroup,userLabel,ecgi";
        }

        for ($i = 0; $i < $sum_num; $i++) {
            if ($i<10) {
                $n="0".$i;
            } else {
                $n=$i;
            }
              $text = $text.",".$name.$n;
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
                // print_r($r);
                array_push($item, mb_convert_encoding($r, 'GBK'));
            }
            fputcsv($fp, $item);
        }
        fclose($fp);

    }//end resultToCSV2()


    /**
     * 求出UE平均距离
     */
    public function getueAvg($data)
    {
        $sum=array_sum($data);
        if ($sum!=0) {
            $avg=0.5*4.89*(
            $data['TADV00']*8 +
            $data['TADV01']*24 +
            $data['TADV02']*40+
            $data['TADV03']*56+
            $data['TADV04']*72+
            $data['TADV05']*88+
            $data['TADV06']*104+
            $data['TADV07']*120+
            $data['TADV08']*136+
            $data['TADV09']*152+
            $data['TADV10']*168+
            $data['TADV11']*184+
            $data['TADV12']*208+
            $data['TADV13']*240+
            $data['TADV14']*272+
            $data['TADV15']*304+
            $data['TADV16']*336+
            $data['TADV17']*368+
            $data['TADV18']*400+
            $data['TADV19']*432+
            $data['TADV20']*464+
            $data['TADV21']*496+
            $data['TADV22']*528+
            $data['TADV23']*560+
            $data['TADV24']*592+
            $data['TADV25']*624+
            $data['TADV26']*656+
            $data['TADV27']*688+
            $data['TADV28']*720+
            $data['TADV29']*752+
            $data['TADV30']*784+
            $data['TADV31']*816+
            $data['TADV32']*848+
            $data['TADV33']*880+
            $data['TADV34']*912+
            $data['TADV35']*944+
            $data['TADV36']*976+
            $data['TADV37']*1008+
            $data['TADV38']*1152+
            $data['TADV39']*1408+
            $data['TADV40']*1664+
            $data['TADV41']*1920+
            $data['TADV42']*2560+
            $data['TADV43']*3584+
            $data['TADV44']*4096)/($sum*1000); 
        } else {
            $avg=0;
        }
        return $avg;
    }
}//end class
