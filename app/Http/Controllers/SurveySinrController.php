<?php

/**
 * SurveySinrController.php
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
class SurveySinrController extends Controller
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

        $sum_num=37;
        $name='SinrUL';
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
            $sql = "select timeStamp,$searchArr,count(distinct ecgi) from mrs_sinrul where timeStamp >='$startTime' and timeStamp<='$endTime' group by timeStamp";
            $res = $db->query("select count(*) from ($sql) as total");
            $total = $res->fetchAll(PDO::FETCH_NUM)[0];

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
                    $result=$this->getueAvg($temp);
                    $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[38], "city" => $city, "total"=>$result['total'],"sinr"=>$result['sinr'],"sinr_avg"=>$result['sinr_avg'],"total_avg"=>$result['total_avg'],"date" => $timeStamp), $temp);
                    array_push($items, $temp);
                }
            }
            $result['total'] = $total;
            $result['records'] = $items;
            echo json_encode($result);
            return;
        } else if ($regionType == "city" && $timeType == "hour") {
            $stmt = '';
            if ($hour) {
                $sql = "select timeStamp,hourId,$searchArr,count(distinct ecgi) from mrs_sinrul where timeStamp >='$startTime' and timeStamp<='$endTime' and hourId in ($hour) group BY timeStamp,hourId";
            } else {
                $sql = "select timeStamp,hourId,$searchArr,count(distinct ecgi) from mrs_sinrul where timeStamp >='$startTime' and timeStamp<='$endTime' group BY timeStamp,hourId";
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
                    for ($i = 2; $i <= ($sum_num+1); $i++) {
                        if ($i-2<10) {
                            $k="0".($i-2);
                        } else {
                            $k=($i-2);
                        }
                        $temp[$name.$k] = $row[$i];
                    }
                    $result=$this->getueAvg($temp);
                    $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[39], "city" => $city,"total"=>$result['total'],"sinr"=>$result['sinr'],"sinr_avg"=>$result['sinr_avg'],"total_avg"=>$result['total_avg'],"date" => $timeStamp, "hourId" => $hourId), $temp);
                    array_push($items, $temp);
                }
            }
            $result['total'] = $total;
            $result['records'] = $items;
            echo json_encode($result);
            return;
        } else if (($regionType == "baseStation"||$regionType=="baseStationGroup") && $timeType == "day") {
            if ($baseStationStr) {
                $sql = "select timeStamp,userLabel,$searchArr,count(distinct ecgi) as count_ecgi from mrs_sinrul where timeStamp <='$endTime' and timeStamp>='$startTime' and userLabel in $baseStation group BY timeStamp,userLabel";
            } else {
                $sql = "select timeStamp,userLabel,$searchArr,count(distinct ecgi) as count_ecgi from mrs_sinrul where timeStamp >='$startTime' and timeStamp<='$endTime' group BY timeStamp,userLabel";
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
                    for ($i = 2; $i <= ($sum_num+1); $i++) {
                        if ($i-2<10) {
                            $k="0".($i-2);
                        } else {
                            $k=($i-2);
                        }
                        $temp[$name.$k] = $row[$i];
                    }
                    $result=$this->getueAvg($temp);
                    if ($regionType=='baseStationGroup') {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[39], "city" => $city, "total"=>$result['total'],"sinr"=>$result['sinr'],"sinr_avg"=>$result['sinr_avg'],"total_avg"=>$result['total_avg'], "date" => $timeStamp,"baseStationGroup"=>'baseStationGroup', "userLabel" => $userLabel), $temp);
                    } else {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[39], "city" => $city, "total"=>$result['total'],"sinr"=>$result['sinr'],"sinr_avg"=>$result['sinr_avg'],"total_avg"=>$result['total_avg'], "date" => $timeStamp, "userLabel" => $userLabel), $temp);
                    }
                    array_push($items, $temp);
                }
            }
            $result['total'] = $total;
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
                $sql = "select timeStamp,userLabel,ecgi,$searchArr,count(distinct ecgi) from mrs_sinrul where timeStamp>='$startTime' and timeStamp<='$endTime' and ecgi in $groupEcgi  group BY timeStamp,userLabel".$groupBy;
            } else {
                $sql = "select timeStamp,userLabel,ecgi,$searchArr,count(distinct ecgi) from mrs_sinrul where timeStamp>='$startTime' and timeStamp<='$endTime' group BY  timeStamp,userLabel".$groupBy;
            }
            $res = $db->query("select count(*) from ($sql) as total");
            $total = $res->fetchAll(PDO::FETCH_NUM)[0];

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
                    $result=$this->getueAvg($temp);
                    if ($regionType=="cellGroup") {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[40],"city" => $city, "total"=>$result['total'],"sinr"=>$result['sinr'],"sinr_avg"=>$result['sinr_avg'],"total_avg"=>$result['total_avg'], "date" => $timeStamp, "cellGroup"=>'cellGroup',"userLabel" => $userLabel, "ecgi" => $ecgi), $temp);
                    } else {
                        $temp = array_merge(array("id" => $id++, "city" => $city, "total"=>$result['total'],"sinr"=>$result['sinr'],"sinr_avg"=>$result['sinr_avg'],"total_avg"=>$result['total_avg'], "date" => $timeStamp, "userLabel" => $userLabel, "ecgi" => $ecgi), $temp);
                    }
                    array_push($items, $temp);
                }
            }
            $result['total'] = $total;
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
            $sql = "select timeStamp,hourId,userLabel,$searchArr,count(distinct ecgi) as count_ecgi from mrs_sinrul where timeStamp >='$startTime' and timeStamp<='$endTime' ".$filter." group BY timeStamp,userLabel,hourId";
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
                    for ($i = 3; $i <= ($sum_num+2); $i++) {
                        if ($i-3<10) {
                            $k="0".($i-3);
                        } else {
                            $k=($i-3);
                        }
                        $temp[$name.$k] = $row[$i];
                    }
                    $result=$this->getueAvg($temp);
                    if ($regionType=="baseStationGroup") {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[40], "city" => $city, "total"=>$result['total'],"sinr"=>$result['sinr'],"sinr_avg"=>$result['sinr_avg'],"total_avg"=>$result['total_avg'], "date" => $timeStamp, "hourId" => $hourId,"baseStationGroup"=>'baseStationGroup' ,"userLabel" => $userLabel), $temp);
                    } else {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[40], "city" => $city, "total"=>$result['total'],"sinr"=>$result['sinr'],"sinr_avg"=>$result['sinr_avg'],"total_avg"=>$result['total_avg'], "date" => $timeStamp, "hourId" => $hourId, "userLabel" => $userLabel), $temp);
                    }
                    array_push($items, $temp);
                }
            }
            $result['total'] = $total;
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

            $sql = "select timeStamp,hourId,userLabel,ecgi,$searchArr,count(distinct ecgi) from mrs_sinrul where timeStamp<='$endTime' and timeStamp>='$startTime' ".$filter." group BY timeStamp,userLabel,hourId".$groupBy;
            $res = $db->query("select count(*) from ($sql) as total");
            $total = $res->fetchAll(PDO::FETCH_NUM)[0];

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
                    $result=$this->getueAvg($temp);
                    if ($regionType=="cellGroup") {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[41], "city" => $city, "total"=>$result['total'],"sinr"=>$result['sinr'],"sinr_avg"=>$result['sinr_avg'],"total_avg"=>$result['total_avg'], "date" => $timeStamp, "hourId" => $hourId,"cellGroup"=>'cellGroup', "userLabel" => $userLabel, "ecgi" => $ecgi), $temp);
                    } else {
                        $temp = array_merge(array("id" => $id++, "city" => $city, "total"=>$result['total'],"sinr"=>$result['sinr'],"sinr_avg"=>$result['sinr_avg'],"total_avg"=>$result['total_avg'], "date" => $timeStamp, "hourId" => $hourId, "userLabel" => $userLabel, "ecgi" => $ecgi), $temp);
                    }
                    array_push($items, $temp);
                }
            }
            $result['total'] = $total;
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
        $hour        = input::get("hour");

        $sum_num = 37;
        $name='SinrUL';

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
            $sql = "select timeStamp,$searchArr,count(distinct ecgi) from mrs_sinrul where timeStamp <= '$endTime' and timeStamp >='$startTime' group by timeStamp order by timeStamp";
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
                        $result=$this->getueAvg($temp);
                    $temp = array_merge(array("date" => $timeStamp,"cellTotal"=>$row[38], "city" => $city, "total"=>$result['total'],"sinr"=>$result['sinr'],"sinr_avg"=>$result['sinr_avg'],"total_avg"=>$result['total_avg']), $temp);
                    array_push($items, $temp);
                }
            }
        } else if ($regionType == "city" && $timeType == "hour") {
            if ($hour) {
                $sql = "select timeStamp,hourId,$searchArr,count(distinct ecgi) from mrs_sinrul where timeStamp <='$startTime' and timeStamp>='$endTime' and hourId in ($hour) group BY timeStamp,hourId order by timeStamp,hourId";
            } else {
                $sql = "select timeStamp,hourId,$searchArr,count(distinct ecgi) from mrs_sinrul where timeStamp <='$startTime' and timeStamp >='$endTime' group BY timeStamp,hourId order by timeStamp,hourId";
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
                        $result=$this->getueAvg($temp);
                    
                    $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId,"cellTotal"=>$row[39],"city" => $city, "total"=>$result['total'],"sinr"=>$result['sinr'],"sinr_avg"=>$result['sinr_avg'],"total_avg"=>$result['total_avg']), $temp);
                    array_push($items, $temp);
                }
            }
        } else if (($regionType == "baseStation" ||$regionType=="baseStationGroup")&& $timeType == "day") {
            if ($baseStationStr) {
                $sql = "select timeStamp,userLabel,$searchArr,count(distinct ecgi) as count_ecgi from mrs_sinrul where timeStamp >='$startTime' and timeStamp<='$endTime' and userLabel in $baseStation group BY timeStamp,userLabel order by timeStamp";
            } else {
                $sql = "select timeStamp,userLabel,$searchArr,count(distinct ecgi) as count_ecgi from mrs_sinrul where timeStamp <='$endTime' and timeStamp>='$startTime' group BY timeStamp,userLabel order by timeStamp";
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
                            if ($i-2<10) {
                                $k="0".($i-2);
                            } else {
                                $k=($i-2);
                            }
                        $temp[$name.$k] = $row[$i];
                        }
                        $result=$this->getueAvg($temp);
                    if ($regionType=='baseStationGroup') {
                $temp = array_merge(array("date" => $timeStamp, "city" => $city, "baseStationGroup"=>'baseStationGroup',"userLabel" => $userLabel, "total"=>$result['total'],"sinr"=>$result['sinr'],"sinr_avg"=>$result['sinr_avg'],"total_avg"=>$result['total_avg']), $temp);
                    } else {

                    $temp = array_merge(array("date" => $timeStamp,"city" => $city, "userLabel" => $userLabel, "total"=>$result['total'],"sinr"=>$result['sinr'],"sinr_avg"=>$result['sinr_avg'],"total_avg"=>$result['total_avg']), $temp);
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
                $sql = "select timeStamp,userLabel,ecgi,$searchArr,count(distinct ecgi) from mrs_sinrul where timeStamp<='$endTime' and timeStamp>='$startTime' and ecgi in $groupEcgi group BY timeStamp,userLabel".$groupBy." order by timeStamp,userLabel";
            } else {
                $sql = "select timeStamp,userLabel,ecgi,$searchArr,count(distinct ecgi) from mrs_sinrul where timeStamp<='$endTime' and timeStamp>='$startTime' group BY timeStamp,userLabel".$groupBy." order by timeStamp,userLabel";
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
                    $result=$this->getueAvg($temp);
                    if ($regionType=="cellGroup") {
                        $temp = array_merge(array("date" => $timeStamp, "city" => $city,"cellGroup"=>'cellGroup', "userLabel" => $userLabel, "ecgi" => $ecgi, "total"=>$result['total'],"sinr"=>$result['sinr'],"sinr_avg"=>$result['sinr_avg'],"total_avg"=>$result['total_avg']), $temp);
                    } else {
                        $temp = array_merge(array("date" => $timeStamp, "city" => $city, "userLabel" => $userLabel, "ecgi" => $ecgi,"total"=>$result['total'],"sinr"=>$result['sinr'],"sinr_avg"=>$result['sinr_avg'],"total_avg"=>$result['total_avg']), $temp);
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
            $sql = "select timeStamp,hourId,userLabel,$searchArr,count(distinct ecgi) as count_ecgi from mrs_sinrul where timeStamp <='$endTime' and timeStamp >= '$startTime' ".$filter." group BY timeStamp,userLabel,hourId order by timeStamp,userLabel,hourId";
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
                    $result=$this->getueAvg($temp);
                    if ($regionType=="baseStationGroup") {
                        $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId, "city" => $city,"baseStationGroup"=>'baseStationGroup',"userLabel" => $userLabel, "total"=>$result['total'],"sinr"=>$result['sinr'],"sinr_avg"=>$result['sinr_avg'],"total_avg"=>$result['total_avg']), $temp);
                    } else {
                        $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId, "city" => $city, "userLabel" => $userLabel, "total"=>$result['total'],"sinr"=>$result['sinr'],"sinr_avg"=>$result['sinr_avg'],"total_avg"=>$result['total_avg']), $temp);
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
            $sql = "select timeStamp,hourId,userLabel,ecgi,$searchArr,count(distinct ecgi) from mrs_sinrul where timeStamp <='$endTime' and timeStamp>='$startTime' ".$filter." group BY timeStamp,userLabel,hourId".$groupBy." order by timeStamp,ecgi,hourId";
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
                    $result=$this->getueAvg($temp);
                    if ($regionType=="cellGroup") {
                        $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId, "city" => $city,"cellGroup"=>'cellGroup', "userLabel" => $userLabel, "ecgi" => $ecgi, "total"=>$result['total'],"sinr"=>$result['sinr'],"sinr_avg"=>$result['sinr_avg'],"total_avg"=>$result['total_avg']), $temp);
                    } else {
                        $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId, "city" => $city, "userLabel" => $userLabel, "ecgi" => $ecgi, "total"=>$result['total'],"sinr"=>$result['sinr'],"sinr_avg"=>$result['sinr_avg'],"total_avg"=>$result['total_avg']), $temp);
                    }
                    array_push($items, $temp);
                }
            }
        }//end if

        $filename = "files/".$survey.date('YmdHis').".csv";
        if ($regionType == "city" && $timeType == "day") {
            $text = "date,cellTotal,city,采样点总数,SINR<-3采样点数,SINR<-3的比例,平均上行SINR";
        } else if ($regionType == "city" && $timeType == "hour") {
            $text = "date,hourId,cellTotal,city,采样点总数,SINR<-3采样点数,SINR<-3的比例,平均上行SINR";
        } else if ($regionType == "baseStation" && $timeType == "day") {
            $text = "date,city,userLabel,采样点总数,SINR<-3采样点数,SINR<-3的比例,平均上行SINR";
        } else if ($regionType == "baseStation" && $timeType == "hour") {
            $text = "date,hourId,city,userLabel,采样点总数,SINR<-3采样点数,SINR<-3的比例,平均上行SINR";
        } else if ($regionType == "baseStationGroup" && $timeType == "day") {
            $text = "date,city,baseStationGroup,userLabel,采样点总数,SINR<-3采样点数,SINR<-3的比例,平均上行SINR";
        } else if ($regionType == "baseStationGroup" && $timeType == "hour") {
            $text = "date,hourId,city,baseStationGroup,userLabel,采样点总数,SINR<-3采样点数,SINR<-3的比例,平均上行SINR";
        } else if ($regionType == "groupEcgi" && $timeType == "day") {
            $text = "date,city,userLabel,ecgi,采样点总数,SINR<-3采样点数,SINR<-3的比例,平均上行SINR";
        } else if ($regionType == "groupEcgi" && $timeType == "hour") {
            $text = "date,hourId,city,userLabel,ecgi,采样点总数,SINR<-3采样点数,SINR<-3的比例,平均上行SINR";
        } else if ($regionType == "cellGroup" && $timeType == "day") {
            $text = "date,city,cellGroup,userLabel,ecgi,采样点总数,SINR<-3采样点数,SINR<-3的比例,平均上行SINR";
        } else if ($regionType == "cellGroup" && $timeType == "hour") {
            $text = "date,hourId,city,cellGroup,userLabel,ecgi,采样点总数,SINR<-3采样点数,SINR<-3的比例,平均上行SINR";
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

        $result =array();
        $sum=array_sum($data);


        $result['total']    =$sum;
        $result['sinr']  =     $data['SinrUL00']+
                                $data['SinrUL01']+
                                $data['SinrUL02']+
                                $data['SinrUL03']+
                                $data['SinrUL04']+
                                $data['SinrUL05']+
                                $data['SinrUL06']+
                                $data['SinrUL07'];




        if ($sum!=0) {
            $result['sinr_avg'] = $result['sinr']/$sum;
            $result['sinr_avg'] = round($result['sinr_avg']*100, 2).'%';

            $result['total_avg']=($data['SinrUL00']*(-10) +
                $data['SinrUL01']*(-9.5) +
                $data['SinrUL02']*(-8.5)+
                $data['SinrUL03']*(-7.5)+
                $data['SinrUL04']*(-6.5)+
                $data['SinrUL05']*(-5.5)+
                $data['SinrUL06']*(-4.5)+
                $data['SinrUL07']*(-3.5)+
                $data['SinrUL08']*(-2.5)+
                $data['SinrUL09']*(-1.5)+
                $data['SinrUL10']*(-0.5)+
                $data['SinrUL11']*(0.5)+
                $data['SinrUL12']*(1.5)+
                $data['SinrUL13']*2.5+
                $data['SinrUL14']*3.5+
                $data['SinrUL15']*4.5+
                $data['SinrUL16']*5.5+
                $data['SinrUL17']*6.5+
                $data['SinrUL18']*7.5+
                $data['SinrUL19']*8.5+
                $data['SinrUL20']*9.5+
                $data['SinrUL21']*10.5+
                $data['SinrUL22']*11.5+
                $data['SinrUL23']*12.5+
                $data['SinrUL24']*13.5+
                $data['SinrUL25']*14.6+
                $data['SinrUL26']*15.5+
                $data['SinrUL27']*16.5+
                $data['SinrUL28']*17.5+
                $data['SinrUL29']*18.5+
                $data['SinrUL30']*19.5+
                $data['SinrUL31']*20.5+
                $data['SinrUL32']*21.5+
                $data['SinrUL33']*22.5+
                $data['SinrUL34']*23.5+
                $data['SinrUL35']*24.5+
                $data['SinrUL36']*25)/$sum;
            $result['total_avg'] = round($result['total_avg'], 2); 

        } else {
            $result['total_avg']=0;
            $result['sinr_avg'] =0;
        }

        
        return $result;

    }
    

}//end class
