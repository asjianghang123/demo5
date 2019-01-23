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
 * Class SurveyController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class SurveyController extends Controller
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

        if ($survey=="TadvRsrp") {
            $table="mrs_tadvrsrp";
            $sum_num=132;
            $searchArr = array();
            for ($i = 0; $i < 11; $i++) {
                if ($i < 10) {
                    $n = "0".$i;
                } else {
                    $n = $i;
                }
                for ($j=0;$j<12;$j++) {
                    if ($j<10) {
                        $s="0".$j;
                    } else {
                        $s=$j;
                    }
                    // if ($regionType == "city") {
                    //     array_push($searchArr, "sum(mr_Tadv".$n."Rsrp_".$s.") as mr_Tadv".$n."Rsrp_".$s);
                    // } else {
                    //     array_push($searchArr, "mr_Tadv".$n."Rsrp_".$s);
                    // }
                    array_push($searchArr, "sum(mr_Tadv".$n."Rsrp_".$s.") as mr_Tadv".$n."Rsrp_".$s);
                }

            }
        } else if ($survey=="RipRsrp") {
            $sum_num=9*12;
            $table="mrs_riprsrp";
            $searchArr = array();
            for ($i = 0; $i < 9; $i++) {
                for ($j=0;$j<12;$j++) {
                    if ($j<10) {
                        $s="0".$j;
                    } else {
                        $s=$j;
                    }
                    // if ($regionType == "city") {
                    //     array_push($searchArr, "sum(mr_Rip0".$i."Rsrp".$s.") as mr_Rip0".$i."Rsrp".$s);
                    // } else {
                    //     array_push($searchArr, "mr_Rip0".$i."Rsrp".$s);
                    // }
                    array_push($searchArr, "sum(mr_Rip0".$i."Rsrp".$s.") as mr_Rip0".$i."Rsrp".$s);
                }
            }
        } else {
            if ($survey=="PowerHeadRoom") {
                $sum_num=64;
                $name='PowerHeadRoom';
                $table="mrs_power_headroom";
            } else if ($survey=="RSRQ") {
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
      
        }
        // print_r($searchArr);return;

        $searchArr = implode(",", $searchArr);

        $dbc    = new DataBaseConnection();
        $db  = $dbc->getPGSQL($city);
        $result = array();
        $items  = array();
        $id     = 1;
        if ($regionType == "city" && $timeType == "day") {
            $sql = "select timeStamp,$searchArr,count(distinct ecgi) from $table where timeStamp >='$startTime' and timeStamp<='$endTime' group by timeStamp";
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
                    if (isset($name)) {
                        for ($i = 1; $i <= $sum_num; $i++) {
                            if ($i-1<10) {
                                $k="0".($i-1);
                            } else {
                                $k=($i-1);
                            }
                            $temp[$name.$k] = $row[$i];
                        }
                    } else {
                        if ($survey=="RipRsrp") {
                            for ($i=0;$i<9;$i++) {
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['mr_Rip0'.$i.'Rsrp'.$s]=$row[9*$i+$j+1];
                                }
                            }
                        } else {
                            for ($i=1;$i<=11;$i++) {
                                if ($i-1<10) { 
                                    $n="0".($i-1);
                                } else {
                                    $n=($i-1);
                                }
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['Tadv'.$n.'Rsrp'.$s]=$row[12*($i-1)+$j+1];
                                }
                            }
                        }
                    }
                    $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[$sum_num+1],"city" => $city, "date" => $timeStamp), $temp);
                    array_push($items, $temp);
                }
            }

            $result['records'] = $items;
            echo json_encode($result);
            return;
        } else if ($regionType == "city" && $timeType == "hour") {
            $stmt = '';
            if ($hour) {
                $sql = "select timeStamp,hourId,$searchArr,count(distinct ecgi) from $table where timeStamp >='$startTime' and timeStamp<='$endTime' and hourId in ($hour) group BY timeStamp,hourId";
            } else {
                $sql = "select timeStamp,hourId,$searchArr,count(distinct ecgi) from $table where timeStamp >='$startTime' and timeStamp<='$endTime' group BY timeStamp,hourId";
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
                    if (isset($name)) {
                        for ($i = 2; $i <= ($sum_num+1); $i++) {
                            if ($i-2<10) {
                                $k="0".($i-2);
                            } else {
                                $k=($i-2);
                            }
                            $temp[$name.$k] = $row[$i];
                        }
                    } else {
                        if ($survey=="RipRsrp") {
                            for ($i=0;$i<9;$i++) {
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['mr_Rip0'.$i.'Rsrp'.$s]=$row[9*$i+$j+2];
                                }
                            }
                        } else {
                            for ($i=1;$i<=11;$i++) {
                                if ($i-1<10) {
                                    $n="0".($i-1);
                                } else {
                                    $n=($i-1);
                                }
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['Tadv'.$n.'Rsrp'.$s]=$row[12*($i-1)+$j+2];
                                }
                            }
                        }
                    }
                    $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[$sum_num+2], "city" => $city, "date" => $timeStamp, "hourId" => $hourId), $temp);
                    array_push($items, $temp);
                }
            }

            $result['records'] = $items;
            echo json_encode($result);
            return;
        } else if (($regionType == "baseStation"||$regionType=="baseStationGroup") && $timeType == "day") {
            if ($baseStationStr) {
                $sql = "select timeStamp,userLabel,$searchArr,count(distinct ecgi) as count_ecgi from $table where timeStamp <='$endTime' and timeStamp>='$startTime' and userLabel in $baseStation group BY timeStamp,userLabel";
            } else {
                $sql = "select timeStamp,userLabel,$searchArr,count(distinct ecgi) as count_ecgi from $table where timeStamp >='$startTime' and timeStamp<='$endTime' group BY timeStamp,userLabel";
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
                    if (isset($name)) {
                        for ($i = 2; $i <= ($sum_num+1); $i++) {
                            if ($i-2<10) {
                                $k="0".($i-2);
                            } else {
                                $k=($i-2);
                            }
                            $temp[$name.$k] = $row[$i];
                        }
                    } else {
                        if ($survey=="RipRsrp") {
                            for ($i=0;$i<9;$i++) {
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['mr_Rip0'.$i.'Rsrp'.$s]=$row[9*$i+$j+2];
                                }
                            }
                        } else {
                            for ($i=1;$i<=11;$i++) {
                                if ($i-1<10) {
                                    $n="0".($i-1);
                                } else {
                                    $n=($i-1);
                                }
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['Tadv'.$n.'Rsrp'.$s]=$row[12*($i-1)+$j+2];
                                }
                            }
                        }
                    }
                    if ($regionType=='baseStationGroup') {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[$sum_num+2], "city" => $city, "date" => $timeStamp,"baseStationGroup"=>'baseStationGroup', "userLabel" => $userLabel), $temp);
                    } else {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[$sum_num+2], "city" => $city, "date" => $timeStamp, "userLabel" => $userLabel), $temp);
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
                $sql = "select timeStamp,userLabel,ecgi,$searchArr,count(distinct ecgi) from $table where timeStamp>='$startTime' and timeStamp<='$endTime' and ecgi in $groupEcgi  group BY timeStamp,userLabel".$groupBy;
            } else {
                $sql = "select timeStamp,userLabel,ecgi,$searchArr,count(distinct ecgi) from $table where timeStamp>='$startTime' and timeStamp<='$endTime' group BY  timeStamp,userLabel".$groupBy;
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
                    if (isset($name)) {
                        for ($i = 3; $i <= ($sum_num+2); $i++) {
                            if ($i-3<10) {
                                $k="0".($i-3);
                            } else {
                                $k=($i-3);
                            }
                            $temp[$name.$k] = $row[$i];
                        }
                    } else {
                        if ($survey=="RipRsrp") {
                            for ($i=0;$i<9;$i++) {
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['mr_Rip0'.$i.'Rsrp'.$s]=$row[9*$i+$j+3];
                                }
                            }
                        } else {
                            for ($i=1;$i<=11;$i++) {
                                if ($i-1<10) {
                                    $n="0".($i-1);
                                } else {
                                    $n=($i-1);
                                }
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['Tadv'.$n.'Rsrp'.$s]=$row[12*($i-1)+$j+3];
                                }
                            }
                        }
                    }
                    if ($regionType=="cellGroup") {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[$sum_num+3], "city" => $city, "date" => $timeStamp, "cellGroup"=>'cellGroup',"userLabel" => $userLabel, "ecgi" => $ecgi), $temp);
                    } else {
                        $temp = array_merge(array("id" => $id++, "city" => $city, "date" => $timeStamp, "userLabel" => $userLabel, "ecgi" => $ecgi), $temp);
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
            $sql = "select timeStamp,hourId,userLabel,$searchArr,count(distinct ecgi) as count_ecgi from $table where timeStamp >='$startTime' and timeStamp<='$endTime' ".$filter." group BY timeStamp,userLabel,hourId";
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
                    if (isset($name)) {
                        for ($i = 3; $i <= ($sum_num+2); $i++) {
                            if ($i-3<10) {
                                $k="0".($i-3);
                            } else {
                                $k=($i-3);
                            }
                            $temp[$name.$k] = $row[$i];
                        }
                    } else {
                        if ($survey=="RipRsrp") {
                            for ($i=0;$i<9;$i++) {
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['mr_Rip0'.$i.'Rsrp'.$s]=$row[9*$i+$j+3];
                                }
                            }
                        } else {
                            for ($i=1;$i<=11;$i++) {
                                if ($i-1<10) {
                                    $n="0".($i-1);
                                } else {
                                    $n=($i-1);
                                }
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['Tadv'.$n.'Rsrp'.$s]=$row[12*($i-1)+$j+3];
                                }
                            }
                        }
                    }
                    if ($regionType=="baseStationGroup") {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[$sum_num+3], "city" => $city, "date" => $timeStamp, "hourId" => $hourId,"baseStationGroup"=>'baseStationGroup' ,"userLabel" => $userLabel), $temp);
                    } else {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[$sum_num+3], "city" => $city, "date" => $timeStamp, "hourId" => $hourId, "userLabel" => $userLabel), $temp);
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

            $sql = "select timeStamp,hourId,userLabel,ecgi,$searchArr,count(distinct ecgi) from $table where timeStamp<='$endTime' and timeStamp>='$startTime' ".$filter." group BY timeStamp,userLabel,hourId".$groupBy;
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
                    if (isset($name)) {
                        for ($i = 4; $i <= ($sum_num+3); $i++) {
                            if ($i-4<10) {
                                $k="0".($i-4);
                            } else {
                                $k=($i-4);
                            }
                            $temp[$name.$k] = $row[$i];
                        }
                    } else {
                        if ($survey=="RipRsrp") {
                            for ($i=0;$i<9;$i++) {
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['mr_Rip0'.$i.'Rsrp'.$s]=$row[9*$i+$j+4];
                                }
                            }
                        } else {
                            for ($i=1;$i<=11;$i++) {
                                if ($i-1<10) {
                                    $n="0".($i-1);
                                } else {
                                    $n=($i-1);
                                }
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['Tadv'.$n.'Rsrp'.$s]=$row[12*($i-1)+$j+4];
                                }
                            }
                        }
                    }
                    if ($regionType=="cellGroup") {
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[$sum_num+4], "city" => $city, "date" => $timeStamp, "hourId" => $hourId,"cellGroup"=>'cellGroup', "userLabel" => $userLabel, "ecgi" => $ecgi), $temp);
                    } else {
                        $temp = array_merge(array("id" => $id++, "city" => $city, "date" => $timeStamp, "hourId" => $hourId, "userLabel" => $userLabel, "ecgi" => $ecgi), $temp);
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

        if ($survey=="TadvRsrp") {
            $table="mrs_tadvrsrp";
            $sum_num=132;
            $searchArr = array();
            for ($i = 0; $i < 11; $i++) {
                if ($i < 10) {
                    $n = "0".$i;
                } else {
                    $n = $i;
                }
                for ($j=0;$j<12;$j++) {
                    if ($j<10) {
                        $s="0".$j;
                    } else {
                        $s=$j;
                    }
                    // if ($regionType == "city") {
                    //     array_push($searchArr, "sum(mr_Tadv".$n."Rsrp_".$s.") as mr_Tadv".$n."Rsrp_".$s);
                    // } else {
                    //     array_push($searchArr, "mr_Tadv".$n."Rsrp_".$s);
                    // }
                    array_push($searchArr, "sum(mr_Tadv".$n."Rsrp_".$s.") as mr_Tadv".$n."Rsrp_".$s);
                }
            }
        } else if ($survey=="RipRsrp") {
            $sum_num=9*12;
            $table="mrs_riprsrp";
            $searchArr = array();
            for ($i = 0; $i < 9; $i++) {
                for ($j=0;$j<12;$j++) {
                    if ($j<10) {
                        $s="0".$j;
                    } else {
                        $s=$j;
                    }
                    // if ($regionType == "city") {
                    //     array_push($searchArr, "sum(mr_Rip0".$i."Rsrp".$s.") as mr_Rip0".$i."Rsrp".$s);
                    // } else {
                    //     array_push($searchArr, "mr_Rip0".$i."Rsrp".$s);
                    // }
                    array_push($searchArr, "sum(mr_Rip0".$i."Rsrp".$s.") as mr_Rip0".$i."Rsrp".$s);
                }
            }
        } else {
            if ($survey=="PowerHeadRoom") {
                $sum_num=64;
                $name='PowerHeadRoom';
                $table="mrs_power_headroom";
            } else if ($survey=="RSRQ") {
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
                    if (isset($name)) {
                        for ($i = 1; $i <= $sum_num; $i++) {
                            if ($i-1<10) {
                                $k="0".($i-1);
                            } else {
                                $k=($i-1);
                            }
                            $temp[$name.$k] = $row[$i];
                        }
                    } else {
                        if ($survey=="RipRsrp") {
                            for ($i=0;$i<9;$i++) {
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['mr_Rip0'.$i.'Rsrp'.$s]=$row[9*$i+$j+1];
                                }
                            }
                        } else {
                            for ($i=1;$i<=11;$i++) {
                                if ($i-1<10) { 
                                    $n="0".($i-1);
                                } else {
                                    $n=($i-1);
                                }
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['Tadv'.$n.'Rsrp'.$s]=$row[12*($i-1)+$j+1];
                                }
                            }
                        }
                    }
                    $temp = array_merge(array("date" => $timeStamp,"cellTotal"=>$row[$sum_num+1], "city" => $city), $temp);
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
                    if (isset($name)) {
                        for ($i = 2; $i <= ($sum_num+1); $i++) {
                            if ($i-2<10) {
                                $k="0".($i-2);
                            } else {
                                $k=($i-2);
                            }
                            $temp[$name.$k] = $row[$i];
                        }
                    } else {
                        if ($survey=="RipRsrp") {
                            for ($i=0;$i<9;$i++) {
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['mr_Rip0'.$i.'Rsrp'.$s]=$row[9*$i+$j+2];
                                }
                            }
                        } else {
                            for ($i=1;$i<=11;$i++) {
                                if ($i-1<10) {
                                    $n="0".($i-1);
                                } else {
                                    $n=($i-1);
                                }
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['Tadv'.$n.'Rsrp'.$s]=$row[12*($i-1)+$j+2];
                                }
                            }
                        }
                    }
                    $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId,"cellTotal"=>$row[$sum_num+2], "city" => $city), $temp);
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
                    if (isset($name)) {
                        for ($i = 2; $i <= ($sum_num+1); $i++) {
                            if ($i-2<10) {
                                $k="0".($i-2);
                            } else {
                                $k=($i-2);
                            }
                            $temp[$name.$k] = $row[$i];
                        }
                    } else {
                        if ($survey=="RipRsrp") {
                            for ($i=0;$i<9;$i++) {
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['mr_Rip0'.$i.'Rsrp'.$s]=$row[9*$i+$j+2];
                                }
                            }
                        } else {
                            for ($i=1;$i<=11;$i++) {
                                if ($i-1<10) {
                                    $n="0".($i-1);
                                } else {
                                    $n=($i-1);
                                }
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['Tadv'.$n.'Rsrp'.$s]=$row[12*($i-1)+$j+2];
                                }
                            }
                        }
                    }
                    if ($regionType=='baseStationGroup') {
                        $temp = array_merge(array("date" => $timeStamp,"cellTotal"=>$row[$sum_num+2], "city" => $city, "baseStationGroup"=>'baseStationGroup',"userLabel" => $userLabel), $temp);
                    } else {
                        $temp = array_merge(array("date" => $timeStamp,"cellTotal"=>$row[$sum_num+2], "city" => $city, "userLabel" => $userLabel), $temp);
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
                    if (isset($name)) {
                        for ($i = 3; $i <= ($sum_num+2); $i++) {
                            if ($i-3<10) {
                                $k="0".($i-3);
                            } else {
                                $k=($i-3);
                            }
                            $temp[$name.$k] = $row[$i];
                        }
                    } else {
                        if ($survey=="RipRsrp") {
                            for ($i=0;$i<9;$i++) {
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['mr_Rip0'.$i.'Rsrp'.$s]=$row[9*$i+$j+3];
                                }
                            }
                        } else {
                            for ($i=1;$i<=11;$i++) {
                                if ($i-1<10) {
                                    $n="0".($i-1);
                                } else {
                                    $n=($i-1);
                                }
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['Tadv'.$n.'Rsrp'.$s]=$row[12*($i-1)+$j+3];
                                }
                            }
                        }
                    }
                    if ($regionType=="cellGroup") {
                        $temp = array_merge(array("date" => $timeStamp,"cellTotal"=>$row[$sum_num+3], "city" => $city,"cellGroup"=>'cellGroup', "userLabel" => $userLabel, "ecgi" => $ecgi), $temp);
                    } else {
                        $temp = array_merge(array("date" => $timeStamp, "city" => $city, "userLabel" => $userLabel, "ecgi" => $ecgi), $temp);
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
                    if (isset($name)) {
                        for ($i = 3; $i <= ($sum_num+2); $i++) {
                            if ($i-3<10) {
                                $k="0".($i-3);
                            } else {
                                $k=($i-3);
                            }
                            $temp[$name.$k] = $row[$i];
                        }
                    } else {
                        if ($survey=="RipRsrp") {
                            for ($i=0;$i<9;$i++) {
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['mr_Rip0'.$i.'Rsrp'.$s]=$row[9*$i+$j+3];
                                }
                            }
                        } else {
                            for ($i=1;$i<=11;$i++) {
                                if ($i-1<10) {
                                    $n="0".($i-1);
                                } else {
                                    $n=($i-1);
                                }
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['Tadv'.$n.'Rsrp'.$s]=$row[12*($i-1)+$j+3];
                                }
                            }
                        }
                    }
                    if ($regionType=="baseStationGroup") {
                        $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId,"cellTotal"=>$row[$sum_num+3], "city" => $city,"baseStationGroup"=>'baseStationGroup',"userLabel" => $userLabel), $temp);
                    } else {
                        $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId,"cellTotal"=>$row[$sum_num+3], "city" => $city, "userLabel" => $userLabel), $temp);
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
                    if (isset($name)) {
                            for ($i = 4; $i <= ($sum_num+3); $i++) {
                            if ($i-4<10) {
                                $k="0".($i-4);
                            } else {
                                $k=($i-4);
                            }
                            $temp[$name.$k] = $row[$i];
                        }
                    } else {
                        if ($survey=="RipRsrp") {
                            for ($i=0;$i<9;$i++) {
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['mr_Rip0'.$i.'Rsrp'.$s]=$row[9*$i+$j+4];
                                }
                            }
                        } else {
                            for ($i=1;$i<=11;$i++) {
                                if ($i-1<10) {
                                    $n="0".($i-1);
                                } else {
                                    $n=($i-1);
                                }
                                for ($j=0;$j<12;$j++) {
                                    if ($j<10) {
                                        $s="0".$j;
                                    } else {
                                        $s=$j;
                                    }
                                    $temp['Tadv'.$n.'Rsrp'.$s]=$row[12*($i-1)+$j+4];
                                }
                            }
                        }
                    }
                    if ($regionType=="cellGroup") {
                    $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId,"cellTotal"=>$row[$sum_num+4], "city" => $city,"cellGroup"=>'cellGroup', "userLabel" => $userLabel, "ecgi" => $ecgi), $temp);
                    } else {

                    $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId, "city" => $city, "userLabel" => $userLabel, "ecgi" => $ecgi), $temp);
                    }
                    array_push($items, $temp);
                }
            }
        }//end if

        $filename = "files/".$survey.date('YmdHis').".csv";
        if ($regionType == "city" && $timeType == "day") {
            $text = "date,cellTotal, city";
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

        if (isset($name))
        for ($i = 0; $i < $sum_num; $i++) {
            if ($i<10) {
                $n="0".$i;
            } else {
                $n=$i;
            }
            $text = $text.",".$name.$n;
        } else {
            if ($survey=="RipRsrp") {
                for ($i=0;$i<9;$i++) {
                    for ($j=0;$j<12;$j++) {
                        if ($j<10) {
                            $s="0".$j;
                        } else {
                            $s=$j;
                        }
                        $text=$text.",mr_Rip0".$i.'Rsrp'.$s;
                    }
                }
            } else {
                for ($i=1;$i<=11;$i++) {
                    if ($i-1<10) { 
                        $n="0".($i-1);
                    } else {
                        $n=($i-1);
                    }
                    for ($j=0;$j<12;$j++) {
                        if ($j<10) {
                            $s="0".$j;
                        } else {
                            $s=$j;
                        }
                        $text=$text.",Tadv".$n.'Rsrp'.$s;
                    }
                }
            }
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
