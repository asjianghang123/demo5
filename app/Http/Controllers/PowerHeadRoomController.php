<?php

/**
 * PowerHeadRoomController.php
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
 * PowerHeadRoom
 * Class PowerHeadRoomController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class PowerHeadRoomController extends Controller
{

    public function getPowerHeadRoomKey()
    {
        $key = [];
        for ($i = 0; $i < 64; $i++) {       
            if ($i < 10) {
                $k="0".$i;
            } else {
                $k=$i;
            }
            array_push($key, trans('message.PH'.'.PowerHeadRoom'.$k));
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

        $survey         = input::get("survey");
        $regionType     = input::get("regionType");
        $city          = input::get("citys");
        // $cityArr        = explode(",", $citys);
        $baseStationStr = input::get("baseStation");
        $baseStationArr = explode(",", $baseStationStr);
        $groupEcgiStr   = input::get("groupEcgi");
        $groupEcgiArr   = explode(",", $groupEcgiStr);
        $timeType       = input::get("timeType");
        $startT         = input::get("startTime");
        $endT           = input::get("endTime");

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
        $hour = input::get("hour");
        $sum_num=64;
        $name='PowerHeadRoom';
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
        $id=1;
        if ($regionType == "city" && $timeType == "day") {
            $sql = "select timeStamp,$searchArr,count(distinct ecgi) from mrs_power_headroom where timeStamp >='$startTime' and timeStamp<='$endTime' group by timeStamp";
            $res = $db->query("select count(*) from ($sql) as total");
            $total = $res->fetchAll(PDO::FETCH_NUM)[0];
            $result['total'] = $total;

            $sql = $sql." limit $limit offset $start";
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    $timeStamp=array();
                    $timeStamp[] = $row[0];
                    $temp = array();
                    $sum_all=0;
                    $sum_mid=0;
                    for ($i = 1; $i <= $sum_num; $i++) {
                        if ($i-1<10) {
                            $k="0".($i-1);
                        } else {
                            $k=($i-1);
                        }
                        if ($i<=24) {
                            $sum_mid+=$row[$i];
                        }
                        $sum_all+=$row[$i];
                        $temp[trans('message.PH'.'.PowerHeadRoom'.$k)] = $row[$i];
                    }
                    // $key=array_keys($temp);
                    if ($sum_all) {
                        $sum_avg=round($sum_mid/$sum_all, 4)*100;
                        $sum_avg.='%';
                    } else {
                        $sum_avg=0;
                    }
                    $temp = array_merge(array("id" => $id++, "cellTotal"=>$row[65], "city" => $city, "date" => $timeStamp,"PHR满功率发射比例"=>$sum_avg/* ,"key"=>$key */), $temp);
                    array_push($items, $temp);
                }
            }
            $result['records'] = $items;
            echo json_encode($result);
            return;
        } else if ($regionType == "city" && $timeType == "hour") {
            if ($hour) {
                $sql = "select timeStamp,hourId,$searchArr,count(distinct ecgi) from mrs_power_headroom where timeStamp >='$startTime' and timeStamp<='$endTime' and hourId in ($hour) group BY timeStamp,hourId";
            } else {
                $sql = "select timeStamp,hourId,$searchArr,count(distinct ecgi) from mrs_power_headroom where timeStamp >='$startTime' and timeStamp<='$endTime' group BY timeStamp,hourId";
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
                    $timeStamp=array();
                    $timeStamp[] = $row[0];
                    $hourId    = $row[1];
                    $temp      = array();
                    $sum_all=0;
                    $sum_mid=0;
                    for ($i = 2; $i <= ($sum_num+1); $i++) {
                        if ($i-2<10) {
                            $k="0".($i-2);
                        } else {
                            $k=($i-2);
                        }
                        if ($i<=25) {
                            $sum_mid+=$row[$i];
                        }
                        $sum_all+=$row[$i];
                        $temp[trans('message.PH'.'.PowerHeadRoom'.$k)] = $row[$i];
                    }
                    // $key=array_keys($temp);
                    if ($sum_all) {
                        $sum_avg=round($sum_mid/$sum_all, 4)*100;
                        $sum_avg.='%';
                    } else {
                        $sum_avg=0;
                    }
                    $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[66], "city" => $city, "date" => $timeStamp, "hourId" => $hourId,/* 'key'=>$key, */'PHR满功率发射比例'=>$sum_avg), $temp);
                    array_push($items, $temp);
                }
            }
            $result['records'] = $items;
            echo json_encode($result);
            return;
        } else if (($regionType == "baseStation"||$regionType=="baseStationGroup") && $timeType == "day") {
            if ($baseStationStr) {
                $sql = "select timeStamp,userLabel,$searchArr,count(distinct ecgi) as count_ecgi from mrs_power_headroom where timeStamp <='$endTime' and timeStamp>='$startTime' and userLabel in $baseStation group BY timeStamp,userLabel";
            } else {
                $sql = "select timeStamp,userLabel,$searchArr,count(distinct ecgi) as count_ecgi from mrs_power_headroom where timeStamp >='$startTime' and timeStamp<='$endTime' group BY timeStamp,userLabel";
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
                    $timeStamp=array();
                    $timeStamp = $row[0];
                    $userLabel = $row[1];
                    $temp      = array();
                    $sum_all=0;
                    $sum_mid=0;
                    for ($i = 2; $i <= ($sum_num+1); $i++) {
                        if ($i-2<10) {
                            $k="0".($i-2);
                        } else {
                            $k=($i-2);
                        }
                        if ($i<=25) {
                            $sum_mid+=$row[$i];
                        }
                        $sum_all+=$row[$i];
                        $temp[trans('message.PH'.'.PowerHeadRoom'.$k)] = $row[$i];
                    }
                    // $key=array_keys($temp);
                    if ($sum_all) {
                        $sum_avg=round($sum_mid/$sum_all, 4)*100;
                        $sum_avg.='%';
                    } else {
                        $sum_avg=0;
                    }                            
                    if ($regionType=='baseStationGroup') {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[66], "city" => $city, "date" => $timeStamp,"baseStationGroup"=>'baseStationGroup', "userLabel" => $userLabel,/* "key"=>$key, */"PHR满功率发射比例"=>$sum_avg), $temp);
                    } else {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[66], "city" => $city, "date" => $timeStamp, "userLabel" => $userLabel,/* "key"=>$key, */"PHR满功率发射比例"=>$sum_avg), $temp);
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
                $sql = "select timeStamp,userLabel,ecgi,$searchArr,count(distinct ecgi) from mrs_power_headroom where timeStamp>='$startTime' and timeStamp<='$endTime' and ecgi in $groupEcgi  group BY timeStamp,userLabel".$groupBy;
            } else {
                $sql = "select timeStamp,userLabel,ecgi,$searchArr,count(distinct ecgi) from mrs_power_headroom where timeStamp>='$startTime' and timeStamp<='$endTime' group BY  timeStamp,userLabel".$groupBy;
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
                    $sum_all=0;
                    $sum_mid=0;
                    for ($i = 3; $i <= ($sum_num+2); $i++) {
                        if ($i-3<10) {
                            $k="0".($i-3);
                        } else {
                            $k=($i-3);
                        }
                        if ($i<=26) {
                            $sum_mid+=$row[$i];
                        }
                        $sum_all+=$row[$i];
                            $temp[trans('message.PH'.'.PowerHeadRoom'.$k)] = $row[$i];

                        }
                        // $key=array_keys($temp);
                        if ($sum_all) {

                        $sum_avg=round($sum_mid/$sum_all, 4)*100;
                            $sum_avg.='%';
                    } else {
                        $sum_avg=0;
                    }
                    if ($regionType=="cellGroup") {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[67], "city" => $city, "date" => 
                            $timeStamp, "cellGroup"=>'cellGroup',"userLabel" => $userLabel, "ecgi" => $ecgi,/* "key"=>
                            $key, */"PHR满功率发射比例"=>$sum_avg), $temp);
                    } else {
                        $temp = array_merge(array("id" => $id++, "city" => $city, "date" => $timeStamp, "userLabel" => $userLabel, "ecgi" => $ecgi,/* "key"=>$key, */"PHR满功率发射比例"=>$sum_avg), $temp);
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
            $sql = "select timeStamp,hourId,userLabel,$searchArr,count(distinct ecgi) as count_ecgi from mrs_power_headroom where timeStamp >='$startTime' and timeStamp<='$endTime' ".$filter." group BY timeStamp,userLabel,hourId";
            $res = $db->query("select count(*) from ($sql) as total");
            $total = $res->fetchAll(PDO::FETCH_NUM)[0];
            $result['total'] = $total;

            $sql = $sql." order by timeStamp,userLabel,hourId limit $limit offset $start";
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
                    $sum_all=0;
                    $sum_mid=0;
                    for ($i = 3; $i <= ($sum_num+2); $i++) {
                        if ($i-3<10) {
                            $k="0".($i-3);
                        } else {
                            $k=($i-3);
                        }
                        if ($i<=26) {
                            $sum_mid+=$row[$i];
                        }
                        $sum_all+=$row[$i];
                        $temp[trans('message.PH'.'.PowerHeadRoom'.$k)] = $row[$i];
                    }
                    // $key=array_keys($temp);
                    if ($sum_all) {
                        $sum_avg=round($sum_mid/$sum_all, 4)*100;
                        $sum_avg.='%';
                    } else {
                        $sum_avg=0;
                    }                     
                    if ($regionType=="baseStationGroup") {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[67],"city" => $city, "date" => $timeStamp, "hourId" => $hourId,"baseStationGroup"=>'baseStationGroup' ,"userLabel" => $userLabel,/* "key"=>$key, */"PHR满功率发射比例"=>$sum_avg), $temp);
                    } else {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[67], "city" => $city, "date" => $timeStamp, "hourId" => $hourId, "userLabel" => $userLabel,/* "key"=>$key, */"PHR满功率发射比例"=>$sum_avg), $temp);
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
            $sql = "select timeStamp,hourId,userLabel,ecgi,$searchArr,count(distinct ecgi) from mrs_power_headroom where timeStamp<='$endTime' and timeStamp>='$startTime' ".$filter." group BY timeStamp,userLabel,hourId".$groupBy;
            $res = $db->query("select count(*) from ($sql) as total");
            $total = $res->fetchAll(PDO::FETCH_NUM)[0];
            $result['total'] = $total;

            $sql = $sql." order by timeStamp,ecgi,hourId limit $limit offset $start";
            $res = $db->query($sql);            
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    $timeStamp=array();
                    $timeStamp[] = $row[0];
                    $hourId    = $row[1];
                    $userLabel = $row[2];
                    $ecgi      = $row[3];
                    $temp      = array();
                    $sum_all=0;
                    $sum_mid=0;
                    for ($i = 4; $i <= ($sum_num+3); $i++) {
                        if ($i-4<10) {
                            $k="0".($i-4);
                        } else {
                            $k=($i-4);
                        }
                        if ($i<=27) {
                            $sum_mid+=$row[$i];
                        }
                        $sum_all+=$row[$i];
                        $temp[trans('message.PH'.'.PowerHeadRoom'.$k)] = $row[$i];
                    }
                    // $key=array_keys($temp);
                    if ($sum_all) {
                        $sum_avg=round($sum_mid/$sum_all, 4)*100;
                        $sum_avg.='%';
                    } else {
                        $sum_avg=0;
                    }
                    if ($regionType=="cellGroup") {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[68],"city" => $city, "date" => $timeStamp, "hourId" => $hourId,"cellGroup"=>'cellGroup', "userLabel" => $userLabel, "ecgi" => $ecgi,/* "key"=>$key, */"PHR满功率发射比例"=>$sum_avg), $temp);
                    } else {
                        $temp = array_merge(array("id" => $id++, "city" => $city, "date" => $timeStamp, "hourId" => $hourId, "userLabel" => $userLabel, "ecgi" => $ecgi,/* "key"=>$key, */"PHR满功率发射比例"=>$sum_avg), $temp);
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
        $sum_num=64;
        $name='PowerHeadRoom';
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
            $sql = "select timeStamp,$searchArr,count(distinct ecgi) from mrs_power_headroom where timeStamp <= '$endTime' and timeStamp >='$startTime' group by timeStamp order by timeStamp";
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    $timeStamp = $row[0];
                    $temp      = array();
                    $sum_all=0;
                    $sum_mid=0;
                    for ($i = 1; $i <= $sum_num; $i++) {
                        if ($i-1<10) {
                            $k="0".($i-1);
                        } else {
                            $k=($i-1);
                        }
                        $temp[$name.$k] = $row[$i];
                        if ($i<=24) {
                            $sum_mid+=$row[$i];
                        }
                        $sum_all+=$row[$i];
                        }                       
                    if ($sum_all) {
                        $sum_avg=round($sum_mid/$sum_all, 4)*100;
                        $sum_avg.='%';
                    } else {
                        $sum_avg=0;
                    }
                    $temp = array_merge(array("date" => $timeStamp,"cellTotal"=>$row[65],"city" => $city,"PHR满功率发射比例"=>$sum_avg), $temp);
                    array_push($items, $temp);
                }
            }
        } else if ($regionType == "city" && $timeType == "hour") {
            if ($hour) {
                $sql = "select timeStamp,hourId,$searchArr,count(distinct ecgi) from mrs_power_headroom where timeStamp <='$startTime' and timeStamp>='$endTime' and hourId in ($hour) group BY timeStamp,hourId order by timeStamp,hourId";
            } else {
                $sql = "select timeStamp,hourId,$searchArr,count(distinct ecgi) from mrs_power_headroom where timeStamp <='$startTime' and timeStamp >='$endTime' group BY timeStamp,hourId order by timeStamp,hourId";
            }
            $res = $db->query($sql);       
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    $timeStamp = $row[0];
                    $hourId    = $row[1];
                    $temp      = array();
                    $sum_all=0;
                    $sum_mid=0;
                    for ($i = 2; $i <= ($sum_num+1); $i++) {
                        if ($i-2<10) {
                            $k="0".($i-2);
                        } else {
                            $k=($i-2);
                        }
                        if ($i<=25) {
                            $sum_mid+=$row[$i];
                        }
                        $sum_all+=$row[$i];
                        $temp[$name.$k] = $row[$i];
                        }
                    if ($sum_all) {
                        $sum_avg=round($sum_mid/$sum_all, 4)*100;
                        $sum_avg.='%';
                    } else {
                        $sum_avg=0;
                    }
                    $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId,"cellTotal"=>$row[66],"city" => $city,"PHR满功率发射比例"=>$sum_avg), $temp);
                    array_push($items, $temp);
                }
            }
        } else if (($regionType == "baseStation" ||$regionType=="baseStationGroup")&& $timeType == "day") {
            if ($baseStationStr) {
                $sql = "select timeStamp,userLabel,$searchArr,count(distinct ecgi) as count_ecgi from mrs_power_headroom where timeStamp >='$startTime' and timeStamp<='$endTime' and userLabel in $baseStation group BY timeStamp,userLabel order by timeStamp";
            } else {
                $sql = "select timeStamp,userLabel,$searchArr,count(distinct ecgi) as count_ecgi from mrs_power_headroom where timeStamp <='$endTime' and timeStamp>='$startTime' group BY timeStamp,userLabel order by timeStamp";
            }
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    $timeStamp = $row[0];
                    $userLabel = $row[1];
                    $temp      = array();
                    $sum_all=0;
                    $sum_mid=0;
                    for ($i = 2; $i <= ($sum_num+1); $i++) {
                        if ($i-2<10) {
                            $k="0".($i-2);
                        } else {
                            $k=($i-2);
                        }
                        if ($i<=25) {
                        $sum_mid+=$row[$i];
                        }
                        $sum_all+=$row[$i];
                        $temp[$name.$k] = $row[$i];
                        }
                    if ($sum_all) {
                        $sum_avg=round($sum_mid/$sum_all, 4)*100;
                        $sum_avg.='%';
                    } else {
                        $sum_avg=0;
                    }
                    if ($regionType=='baseStationGroup') {
                        $temp = array_merge(array("date" => $timeStamp,"cellTotal"=>$row[66], "city" => $city, "baseStationGroup"=>'baseStationGroup',"userLabel" => $userLabel,"PHR满功率发射比例"=>$sum_avg), $temp);
                    } else {
                        $temp = array_merge(array("date" => $timeStamp,"cellTotal"=>$row[66], "city" => $city, "userLabel" => $userLabel,"PHR满功率发射比例"=>$sum_avg), $temp);
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
                $sql = "select timeStamp,userLabel,ecgi,$searchArr,count(distinct ecgi) from mrs_power_headroom where timeStamp<='$endTime' and timeStamp>='$startTime' and ecgi in $groupEcgi group BY timeStamp,userLabel".$groupBy." order by timeStamp,userLabel";
            } else {
                $sql = "select timeStamp,userLabel,ecgi,$searchArr,count(distinct ecgi) from mrs_power_headroom where timeStamp<='$endTime' and timeStamp>='$startTime' group BY timeStamp,userLabel".$groupBy." order by timeStamp,userLabel";
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
                    $sum_all=0;
                    $sum_mid=0;
                    for ($i = 3; $i <= ($sum_num+2); $i++) {
                        if ($i-3<10) {
                            $k="0".($i-3);
                        } else {
                            $k=($i-3);
                        }
                        if ($i<=26) {
                            $sum_mid+=$row[$i];
                        }
                        $sum_all+=$row[$i];
                        $temp[$name.$k] = $row[$i];
                        }
                    if ($sum_all) {
                        $sum_avg=round($sum_mid/$sum_all, 4)*100;
                        $sum_avg.='%';
                    } else {
                        $sum_avg=0;
                    }
                    if ($regionType=="cellGroup") {
                        $temp = array_merge(array("date" => $timeStamp,"cellTotal"=>$row[67], "city" => $city,"cellGroup"=>'cellGroup', "userLabel" => $userLabel, "ecgi" => $ecgi,"PHR满功率发射比例"=>$sum_avg), $temp);
                    } else {
                        $temp = array_merge(array("date" => $timeStamp, "city" => $city, "userLabel" => $userLabel, "ecgi" => $ecgi,"PHR满功率发射比例"=>$sum_avg), $temp);
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
            $sql = "select timeStamp,hourId,userLabel,$searchArr,count(distinct ecgi) as count_ecgi from mrs_power_headroom where timeStamp <='$endTime' and timeStamp >= '$startTime' ".$filter." group BY timeStamp,userLabel,hourId order by timeStamp,userLabel,hourId";
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
                    $sum_all=0;
                    $sum_mid=0;
                    for ($i = 3; $i <= ($sum_num+2); $i++) {
                        if ($i-3<10) {
                            $k="0".($i-3);
                        } else {
                            $k=($i-3);
                        } 
                        if ($i<=26) {
                            $sum_mid+=$row[$i];
                        }
                        $sum_all+=$row[$i];
                        $temp[$name.$k] = $row[$i];
                        }
                    if ($sum_all) {
                        $sum_avg=round($sum_mid/$sum_all, 4)*100;
                        $sum_avg.='%';
                    } else {
                        $sum_avg=0;
                    }               
                    if ($regionType=="baseStationGroup") {
                        $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId,"cellTotal"=>$row[67], "city" => $city,"baseStationGroup"=>'baseStationGroup',"userLabel" => $userLabel,"PHR满功率发射比例"=>$sum_avg), $temp);
                    } else {
                        $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId,"cellTotal"=>$row[67], "city" => $city, "userLabel" => $userLabel,"PHR满功率发射比例"=>$sum_avg), $temp);
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
            $sql = "select timeStamp,hourId,userLabel,ecgi,$searchArr,count(distinct ecgi) from mrs_power_headroom where timeStamp <='$endTime' and timeStamp>='$startTime' ".$filter." group BY timeStamp,userLabel,hourId".$groupBy." order by timeStamp,hourId";
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    // $timeStamp=array();
                    $timeStamp= $row[0];
                    $hourId    = $row[1];
                    $userLabel = $row[2];
                    $ecgi      = $row[3];
                    $temp      = array();
                    $sum_all=0;
                    $sum_mid=0;
                    for ($i = 4; $i <= ($sum_num+3); $i++) {
                        if ($i-4<10) {
                            $k="0".($i-4);
                        } else {
                            $k=($i-4);
                        }
                        if ($i<=27) {
                            $sum_mid+=$row[$i];
                        }
                        $sum_all+=$row[$i];

                        $temp[$name.$k] = $row[$i];
                        }
                    if ($sum_all) {
                        $sum_avg=round($sum_mid/$sum_all, 4)*100;
                            $sum_avg.='%';
                    } else {
                        $sum_avg=0;
                    }                   
                    if ($regionType=="cellGroup") {
                        $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId,"cellTotal"=>$row[68], "city" => $city,"cellGroup"=>'cellGroup', "userLabel" => $userLabel, "ecgi" => $ecgi,"PHR满功率发射比例"=>$sum_avg), $temp);
                    } else {
                        $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId, "city" => $city, "userLabel" => $userLabel, "ecgi" => $ecgi,"PHR满功率发射比例"=>$sum_avg), $temp);
                    }
                    array_push($items, $temp);
                }
            }
        }//end if

        $filename = "files/".$survey.date('YmdHis').".csv";
        if ($regionType == "city" && $timeType == "day") {
            $text = "date,cellTotal,city";
        } else if ($regionType == "city" && $timeType == "hour") {
            $text = "date,hourId,cellTotal,city";
        } else if ($regionType == "baseStation" && $timeType == "day") {
            $text = "date,cellTotal,city,userLabel";
        } else if ($regionType == "baseStation" && $timeType == "hour") {
            $text = "date,hourId,cellTotal,city,userLabel";
        } else if ($regionType == "baseStationGroup" && $timeType == "day") {
            $text = "date,cellTotal,city,baseStationGroup,userLabel";
        } else if ($regionType == "baseStationGroup" && $timeType == "hour") {
            $text = "date,hourId,cellTotal,city,baseStationGroup,userLabel";
        } else if ($regionType == "groupEcgi" && $timeType == "day") {
            $text = "date,city,userLabel,ecgi";
        } else if ($regionType == "groupEcgi" && $timeType == "hour") {
            $text = "date,hourId,city,userLabel,ecgi";
        } else if ($regionType == "cellGroup" && $timeType == "day") {
            $text = "date,cellTotal,city,cellGroup,userLabel,ecgi";
        } else if ($regionType == "cellGroup" && $timeType == "hour") {
            $text = "date,hourId,cellTotal,city,cellGroup,userLabel,ecgi";
        }
        $text.=",PHR满功率发射比例";
        for ($i = 0; $i < $sum_num; $i++) {
            if ($i<10) {
                $n="0".$i;
            } else {
                $n=$i;
            }
            $text = $text.","." ".trans('message.PH'.'.PowerHeadRoom'.$n);
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
    

}//end class
