<?php

/**
 * PacketLossAnalysisController.php
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
use App\Http\Controllers\Utils\FileUtil;

/**
 * 丢包率分析
 * Class PacketLossAnalysisController
 *
 * @category BadCellAnalysis
 * @package  App\Http\Controllers\BadCellAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class PacketLossAnalysisController extends MyRedis
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
     * 获得单城市数据时间(天)列表
     *
     * @return void
     */
    public function getCityDate()
    {
        $dbname = input::get("dataBase");
        $dbname = $this->check_input($dbname);
        $dbc    = new DataBaseConnection();
        // print_r($dbname);return;
        // $db     = $dbc->getDB($dbname, $dbname);
        $db  = $dbc->getPGSQL($dbname);
        // print_r($db);
        $sql   = "select distinct timeStamp from mrs_packet_lossrate";
        $this->type = $dbname.':packetLossAnalysis';
        echo json_encode($this->getValue($db, $sql));

    }//end getCityDate()

    /**
     * 过滤非法字符
     *
     * @param string $value
     *
     * @return string $value
     */
    function check_input($value)
    {
        // $con=mysqli_connect("localhost", "root", "mongs", "mongs");
        // $dbc    = new DataBaseConnection();
        // $con     = $dbc->getDB('mongs', 'mongs');
        // 去除斜杠
        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }
        // 如果不是数字则加引号
        if (preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $value)) {
            $value = "'" . mysqli_real_escape_string($con, $value) . "'";
        }
        return $value;
    }

    /**
     * 获得丢包率聚合数据
     *
     * @return void
     */
    public function getTableData()
    {
        $regionType  = input::get("regionType");
        $city        = input::get("citys");
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
        if ($regionType=="cellGroup") {
            $groupBy  =' ';
        } else {
            $groupBy = ',ecgi ';
        }
        // $date        = input::get("date");
        $hour        = input::get("hour");

        $ULQciXArr = array();
        $DLQciXArr = array();
        for ($i = 0; $i < 28; $i++) {
            if ($i < 10) {
                $n = "0".$i;
            } else {
                $n = $i;
            }
            // if ($regionType == "city") {
            //     array_push($ULQciXArr, "sum(mr_PacketLossRateULQci1_".$n.") as mr_PacketLossRateULQci1_".$n);
            //     array_push($DLQciXArr, "sum(mr_PacketLossRateDLQci1_".$n.") as mr_PacketLossRateDLQci1_".$n);
            // } else {
            //     array_push($ULQciXArr, "mr_PacketLossRateULQci1_".$n);
            //     array_push($DLQciXArr, "mr_PacketLossRateDLQci1_".$n);
            // }
            array_push($ULQciXArr, "sum(mr_PacketLossRateULQci1_".$n.") as mr_PacketLossRateULQci1_".$n);
            array_push($DLQciXArr, "sum(mr_PacketLossRateDLQci1_".$n.") as mr_PacketLossRateDLQci1_".$n);
        }

        $ULQciX = implode(",", $ULQciXArr);
        $DLQciX = implode(",", $DLQciXArr);

        $dbc    = new DataBaseConnection();
        $db  = $dbc->getPGSQL($city);
        $result = array();
        $items  = array();
        $id     = 1;
        if ($regionType == "city" && $timeType == "day") {
            $sql = "select timeStamp,$ULQciX,$DLQciX,count(distinct ecgi) from mrs_packet_lossrate where timeStamp>='$startTime' and timeStamp <= '$endTime' group by timeStamp";
            $res = $db->query("select count(*) from ($sql) as total");
            $total = $res->fetchAll(PDO::FETCH_NUM)[0];
            $result['total'] = $total;

            $sql = $sql." limit $limit offset $start";
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $key=>$row) {
                    $timeStamp=array();
                    $timeStamp[] = $row[0];
                    $upArr     = array();
                    $downArr   = array();
                    $temp      = array();
                    for ($i = 1; $i <= 28; $i++) {
                        array_push($upArr, floatval($row[$i]));
                        array_push($downArr, floatval($row[($i + 28)]));
                        $temp['up'.$i]   = floatval($row[$i]);
                        $temp['down'.$i] = floatval($row[($i + 28)]);
                    }

                    $up       = $this->getResultByWeight($upArr);
                    $down     = $this->getResultByWeight($downArr);
                    $up_avg   = $this->getProportion($upArr);
                    $down_avg = $this->getProportion($downArr);
                    $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[57],"city" => $city, "date" => $timeStamp, "上行丢包率" => $up, "下行丢包率" => $down,"上行丢包率占比"=>$up_avg,"下行丢包率占比"=>$down_avg), $temp);
                    array_push($items, $temp);
                }
            }//end if

            $result['records'] = $items;
            echo json_encode($result);
            return;
        } else if ($regionType == "city" && $timeType == "hour") {
            if ($hour) {
                $sql = "select timeStamp,hourId,$ULQciX,$DLQciX,count(distinct ecgi) from mrs_packet_lossrate where timeStamp>='$startTime' and timeStamp <= '$endTime' and hourId in ($hour) group BY timeStamp,hourId";
            } else {
                $sql = "select timeStamp,hourId,$ULQciX,$DLQciX,count(distinct ecgi) from mrs_packet_lossrate where timeStamp>='$startTime' and timeStamp <= '$endTime' group BY timeStamp, hourId";
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
                    $upArr     = array();
                    $downArr   = array();
                    $temp      = array();
                    for ($i = 2; $i <= 29; $i++) {
                        array_push($upArr, floatval($row[$i]));
                        array_push($downArr, floatval($row[($i + 28)]));
                        $temp['up'.($i - 1)]   = floatval($row[$i]);
                        $temp['down'.($i - 1)] = floatval($row[($i + 28)]);
                    }

                    $up   = $this->getResultByWeight($upArr);
                    $down = $this->getResultByWeight($downArr);
                    $up_avg   = $this->getProportion($upArr);
                    $down_avg = $this->getProportion($downArr);
                    $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[58], "city" => $city, "date" => $timeStamp, "hourId" => $hourId, "上行丢包率" => $up, "下行丢包率" => $down,"上行丢包率占比"=>$up_avg,"下行丢包率占比"=>$down_avg), $temp);
                    array_push($items, $temp);
                }
            }//end if

            $result['records'] = $items;
            echo json_encode($result);
            return;
        } else if (($regionType == "baseStation"||$regionType=="baseStationGroup") && $timeType == "day") {
            if ($baseStationStr) {
                $sql = "select timeStamp,userLabel,$ULQciX,$DLQciX, count(distinct ecgi) as count_ecgi from mrs_packet_lossrate where timeStamp>='$startTime' and timeStamp <= '$endTime' and userLabel in $baseStation group BY timeStamp,userLabel";
            } else {
                $sql = "select timeStamp,userLabel,$ULQciX,$DLQciX, count(distinct ecgi) as count_ecgi from mrs_packet_lossrate where timeStamp>='$startTime' and timeStamp <= '$endTime' group BY timeStamp, userLabel";
            }

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
                    $userLabel = $row[1];
                    $upArr     = array();
                    $downArr   = array();
                    $temp      = array();
                    for ($i = 2; $i <= 29; $i++) {
                        array_push($upArr, floatval($row[$i]));
                        array_push($downArr, floatval($row[($i + 28)]));
                        $temp['up'.($i - 1)]   = floatval($row[$i]);
                        $temp['down'.($i - 1)] = floatval($row[($i + 28)]);
                    }

                    $up   = $this->getResultByWeight($upArr);
                    $down = $this->getResultByWeight($downArr);
                    if ($regionType=="baseStationGroup") {
                        $baseStationGroup='baseStationGroup';
                            $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[58],"city" => $city, "date" => $timeStamp,"baseStationGroup"=>$baseStationGroup, "userLabel" => $userLabel, "上行丢包率" => $up, "下行丢包率" => $down), $temp);
                    } else {
                            $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[58], "city" => $city, "date" => $timeStamp, "userLabel" => $userLabel, "上行丢包率" => $up, "下行丢包率" => $down), $temp);
                    }
                    array_push($items, $temp);
                }
            }//end if
            $result['records'] = $items;
            echo json_encode($result);
            return;
        } else if (($regionType == "groupEcgi"||$regionType=="cellGroup") && $timeType == "day") {
            if ($groupEcgiStr) {
                $sql = "select timeStamp,userLabel,ecgi,$ULQciX,$DLQciX,count(distinct ecgi) from mrs_packet_lossrate where timeStamp>='$startTime' and timeStamp <= '$endTime' and ecgi in $groupEcgi group BY timeStamp,userLabel".$groupBy;
            } else {
                $sql = "select timeStamp,userLabel,ecgi,$ULQciX,$DLQciX,count(distinct ecgi) from mrs_packet_lossrate where timeStamp>='$startTime' and timeStamp <= '$endTime' group BY timeStamp, userLabel".$groupBy;
            }

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
                    $userLabel = $row[1];
                    $ecgi      = $row[2];
                    $upArr     = array();
                    $downArr   = array();
                    $temp      = array();
                    for ($i = 3; $i <= 30; $i++) {
                        array_push($upArr, floatval($row[$i]));
                        array_push($downArr, floatval($row[($i + 28)]));
                        $temp['up'.($i - 2)]   = floatval($row[$i]);
                        $temp['down'.($i - 2)] = floatval($row[($i + 28)]);
                    }

                    $up   = $this->getResultByWeight($upArr);
                    $down = $this->getResultByWeight($downArr);
                        $up_avg   = $this->getProportion($upArr);
                    $down_avg = $this->getProportion($downArr);
                    if ($regionType=="cellGroup") {
                        $cellGroup='cellGroup';
                        $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[59],"city" => $city, "date" => $timeStamp, "cellGroup"=>$cellGroup,"userLabel" => $userLabel, "ecgi" => $ecgi, "上行丢包率" => $up, "下行丢包率" => $down), $temp);
                    } else {

                        $temp = array_merge(array("id" => $id++, "city" => $city, "date" => $timeStamp, "userLabel" => $userLabel, "ecgi" => $ecgi, "上行丢包率" => $up, "下行丢包率" => $down,"上行丢包率占比"=>$up_avg,"下行丢包率占比"=>$down_avg), $temp);
                        }
                    array_push($items, $temp);
                }
            }//end if

            $result['records'] = $items;
            echo json_encode($result);
            return;
        } else if (($regionType == "baseStation" || $regionType=="baseStationGroup")&& $timeType == "hour") {
            $filter = "";
            if ($baseStationStr) {
                $filter = $filter." and userLabel in $baseStation";
            }

            if ($hour) {
                $filter = $filter." and hourId in ($hour)";
            }

            $sql = "select timeStamp,hourId,userLabel,$ULQciX,$DLQciX,count(distinct ecgi) as count_ecgi from mrs_packet_lossrate where timeStamp>='$startTime' and timeStamp <= '$endTime' ".$filter." group BY timeStamp,userLabel,hourId";
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
                    $timeStamp[] = $row[0];
                    $hourId    = $row[1];
                    $userLabel = $row[2];
                    $upArr     = array();
                    $downArr   = array();
                    $temp      = array();
                    for ($i = 3; $i <= 30; $i++) {
                        array_push($upArr, floatval($row[$i]));
                        array_push($downArr, floatval($row[($i + 28)]));
                        $temp['up'.($i - 2)]   = floatval($row[$i]);
                        $temp['down'.($i - 2)] = floatval($row[($i + 28)]);
                    }

                    $up   = $this->getResultByWeight($upArr);
                    $down = $this->getResultByWeight($downArr);

                    if ($regionType=="baseStationGroup") {
                        $baseStationGroup='baseStationGroup';
                            $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[59],"city" => $city, "date" => $timeStamp, "hourId" => $hourId,"baseStationGroup"=>$baseStationGroup, "userLabel" => $userLabel, "上行丢包率" => $up, "下行丢包率" => $down), $temp);
                    } else {
                            $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[59],"city" => $city, "date" => $timeStamp, "hourId" => $hourId, "userLabel" => $userLabel, "上行丢包率" => $up, "下行丢包率" => $down), $temp);
                    }
                    array_push($items, $temp);
                }
            }//end if

            $result['records'] = $items;
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

            $sql = "select timeStamp,hourId,userLabel,ecgi,$ULQciX,$DLQciX,count(distinct ecgi) from mrs_packet_lossrate where timeStamp>='$startTime' and timeStamp <= '$endTime' ".$filter." group BY timeStamp,userLabel,hourId".$groupBy;

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
                    $userLabel = $row[2];
                    $ecgi      = $row[3];
                    $upArr     = array();
                    $downArr   = array();
                    $temp      = array();
                    for ($i = 4; $i <= 31; $i++) {
                        array_push($upArr, floatval($row[$i]));
                        array_push($downArr, floatval($row[($i + 28)]));
                        $temp['up'.($i - 3)]   = floatval($row[$i]);
                        $temp['down'.($i - 3)] = floatval($row[($i + 28)]);
                    }

                    $up   = $this->getResultByWeight($upArr);
                    $down = $this->getResultByWeight($downArr);
                    $up_avg   = $this->getProportion($upArr);
                    $down_avg = $this->getProportion($downArr);
                    $temp = array_merge(array("id" => $id++,"cellTotal"=>$row[60], "city" => $city, "date" => $timeStamp, "hourId" => $hourId, "userLabel" => $userLabel, "ecgi" => $ecgi, "上行丢包率" => $up, "下行丢包率" => $down,"上行丢包率占比"=>$up_avg,"下行丢包率占比"=>$down_avg), $temp);
                    array_push($items, $temp);
                }
            }//end if

            $result['records'] = $items;
            echo json_encode($result);
        }//end if

    }//end getTableData()


    /**
     * 获得权值总合
     *
     * @param array $array 计数器数组
     *
     * @return int 权值总和
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
                   495,
                  );
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
     * 上下行18-28占总的比例
     * @DateTime 2018-04-17
     * @param    
     * @return   int
     */
    public function getProportion($array){
         $sum    = array_sum($array);
         $result = 0;
         $num    = 0;
         if($sum!=0){
            for($i=17;$i<28;$i++){
                $num+=$array[$i];
            }
            $result=$num/$sum;
         }

         return number_format($result,4);
    } 

    /**
     * 导出文件
     *
     * @return void
     */
    public function exportFile()
    {

        $regionType  = input::get("regionType");
        $city        = input::get("citys");
        // $cityArr     = explode(",", $citys);
        // $baseStation = input::get("baseStation");
        $baseStationStr = input::get("baseStation");
        $baseStationArr = explode(",", $baseStationStr);

        $groupEcgiStr   = input::get("groupEcgi");
        $groupEcgiArr =explode(",", $groupEcgiStr);
        $timeType    = input::get("timeType");
        $startT      = input::get("startTime");
        $endT        = input::get("endTime");

        $startTime   =min($startT, $endT);
        $endTime     =max($startT, $endT);
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
        if ($regionType=="cellGroup") {
            $groupBy  =' ';
        } else {
            $groupBy = ',ecgi ';
        }
        $ULQciXArr = array();
        $DLQciXArr = array();
        for ($i = 0; $i < 28; $i++) {
            if ($i < 10) {
                $n = "0".$i;
            } else {
                $n = $i;
            }
            // if ($regionType == "city") {
            //     array_push($ULQciXArr, "sum(mr_PacketLossRateULQci1_".$n.")");
            //     array_push($DLQciXArr, "sum(mr_PacketLossRateDLQci1_".$n.")");
            // } else {
            //     array_push($ULQciXArr, "mr_PacketLossRateULQci1_".$n);
            //     array_push($DLQciXArr, "mr_PacketLossRateDLQci1_".$n);
            // }
            array_push($ULQciXArr, "sum(mr_PacketLossRateULQci1_".$n.")");
            array_push($DLQciXArr, "sum(mr_PacketLossRateDLQci1_".$n.")");
        }

        $ULQciX = implode(",", $ULQciXArr);
        $DLQciX = implode(",", $DLQciXArr);

        $dbc    = new DataBaseConnection();
        $db  = $dbc->getPGSQL($city);
        $result = array();
        $items  = array();
        if ($regionType == "city" && $timeType == "day") {
            $sql = "select timeStamp,$ULQciX,$DLQciX,count(distinct ecgi) from mrs_packet_lossrate where timeStamp>='$startTime' and timeStamp <= '$endTime' group by timeStamp";
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $key=>$row) {
                    
                    $timeStamp = $row[0];
                    $upArr     = array();
                    $downArr   = array();
                    $temp1     = array();
                    $temp2     = array();
                    for ($i = 1; $i <= 28; $i++) {
                        array_push($upArr, floatval($row[$i]));
                        array_push($downArr, floatval($row[($i + 28)]));
                        $temp1['up'.$i]   = floatval($row[$i]);
                        $temp2['down'.$i] = floatval($row[($i + 28)]);
                    }

                    $temp = array_merge($temp1, $temp2);
                    $up   = $this->getResultByWeight($upArr);
                    $down = $this->getResultByWeight($downArr);
                    $up_avg   = $this->getProportion($upArr);
                    $down_avg = $this->getProportion($downArr);
                    $temp = array_merge(array("date" => $timeStamp,"cellTotal"=>$row[57], "city" => $city, "上行丢包率" => $up, "下行丢包率" => $down,"上行丢包率占比"=>$up_avg,"下行丢包率占比"=>$down_avg), $temp);
                    array_push($items, $temp);
                
                }
            }//end if
        } else if ($regionType == "city" && $timeType == "hour") {
            if ($hour) {
                $sql = "select timeStamp,hourId,$ULQciX,$DLQciX,count(distinct ecgi) from mrs_packet_lossrate where timeStamp>='$startTime' and timeStamp <= '$endTime' and hourId in ($hour) group BY timeStamp,hourId";
            } else {
                $sql = "select timeStamp,hourId,$ULQciX,$DLQciX,count(distinct ecgi) from mrs_packet_lossrate where timeStamp>='$startTime' and timeStamp <= '$endTime' group BY timeStamp, hourId";
            }
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    
                    $timeStamp = $row[0];
                    $hourId    = $row[1];
                    $upArr     = array();
                    $downArr   = array();
                    $temp1     = array();
                    $temp2     = array();
                    for ($i = 2; $i <= 29; $i++) {
                        array_push($upArr, floatval($row[$i]));
                        array_push($downArr, floatval($row[($i + 28)]));
                        $temp1['up'.($i - 1)]   = floatval($row[$i]);
                        $temp2['down'.($i - 1)] = floatval($row[($i + 28)]);
                    }

                    $temp = array_merge($temp1, $temp2);
                    $up   = $this->getResultByWeight($upArr);
                    $down = $this->getResultByWeight($downArr);
                    $up_avg   = $this->getProportion($upArr);
                    $down_avg = $this->getProportion($downArr);
                    $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId,"cellTotal"=>$row[58], "city" => $city, "上行丢包率" => $up, "下行丢包率" => $down,"上行丢包率占比"=>$up_avg,"下行丢包率占比"=>$down_avg), $temp);
                    array_push($items, $temp);
                }
            }//end if
        } else if (($regionType == "baseStation"||$regionType=="baseStationGroup") && $timeType == "day") {
            if ($baseStationStr) {
                $sql = "select timeStamp,userLabel,$ULQciX,$DLQciX,count(distinct ecgi) as count_ecgi from mrs_packet_lossrate where timeStamp>='$startTime' and timeStamp <= '$endTime' and userLabel in $baseStation group BY timeStamp,userLabel order by timeStamp";
            } else {
                $sql = "select timeStamp,userLabel,$ULQciX,$DLQciX,count(distinct ecgi) as count_ecgi from mrs_packet_lossrate where timeStamp>='$startTime' and timeStamp <= '$endTime' group BY timeStamp,userLabel order by timeStamp";
            }

            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    
                    $timeStamp = $row[0];
                    $userLabel = $row[1];
                    $upArr     = array();
                    $downArr   = array();
                    $temp1     = array();
                    $temp2     = array();
                    for ($i = 2; $i <= 29; $i++) {
                        array_push($upArr, floatval($row[$i]));
                        array_push($downArr, floatval($row[($i + 28)]));
                        $temp1['up'.($i - 1)]   = floatval($row[$i]);
                        $temp2['down'.($i - 1)] = floatval($row[($i + 28)]);
                    }

                    $temp = array_merge($temp1, $temp2);
                    $up   = $this->getResultByWeight($upArr);
                    $down = $this->getResultByWeight($downArr);
                    if ($regionType=="baseStationGroup") {
                        $baseStationGroup='baseStationGroup';
                            $temp = array_merge(array("date" => $timeStamp,"cellTotal"=>$row[58],"city" => $city, "baseStationGroup"=>$baseStationGroup,"userLabel" => $userLabel, "上行丢包率" => $up, "下行丢包率" => $down), $temp);
                    } else {
                            $temp = array_merge(array("date" => $timeStamp,"cellTotal"=>$row[58], "city" => $city,"userLabel" => $userLabel, "上行丢包率" => $up, "下行丢包率" => $down), $temp);
                    }
                    
                    array_push($items, $temp);
                }
            }//end if
        } else if (($regionType == "groupEcgi"||$regionType=="cellGroup") && $timeType == "day") {
            if ($groupEcgiStr) {
                $sql = "select timeStamp,userLabel,ecgi,$ULQciX,$DLQciX,count(distinct ecgi) from mrs_packet_lossrate where timeStamp>='$startTime' and timeStamp <= '$endTime' and ecgi in $groupEcgi group BY timeStamp,userLabel".$groupBy." order by timeStamp,userLabel";
            } else {
                $sql = "select timeStamp,userLabel,ecgi,$ULQciX,$DLQciX,count(distinct ecgi) from mrs_packet_lossrate where timeStamp>='$startTime' and timeStamp <= '$endTime' group BY timeStamp,userLabel".$groupBy." order by timeStamp,userLabel";
            }

            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    
                    $timeStamp = $row[0];
                    $userLabel = $row[1];
                    $ecgi      = $row[2];
                    $upArr     = array();
                    $downArr   = array();
                    $temp1     = array();
                    $temp2     = array();
                    for ($i = 3; $i <= 30; $i++) {
                        array_push($upArr, floatval($row[$i]));
                        array_push($downArr, floatval($row[($i + 28)]));
                        $temp1['up'.($i - 2)]   = floatval($row[$i]);
                        $temp2['down'.($i - 2)] = floatval($row[($i + 28)]);
                    }

                    $temp = array_merge($temp1, $temp2);
                    $up   = $this->getResultByWeight($upArr);
                    $down = $this->getResultByWeight($downArr);
                    $up_avg   = $this->getProportion($upArr);
                    $down_avg = $this->getProportion($downArr);
                    if ($regionType=='cellGroup') {

                        $temp = array_merge(array("date" => $timeStamp,"cellTotal"=>$row[59],"city" => $city,"cellGroup"=>'cellGroup', "userLabel" => $userLabel, "ecgi" => $ecgi, "上行丢包率" => $up, "下行丢包率" => $down), $temp);
                    } else {
                        $temp = array_merge(array("date" => $timeStamp,"city" => $city, "userLabel" => $userLabel, "ecgi" => $ecgi, "上行丢包率" => $up, "下行丢包率" => $down,"上行丢包率占比"=>$up_avg,"下行丢包率占比"=>$down_avg), $temp);
                    }

                    array_push($items, $temp);
                }//end foreach
            }//end if
        } else if (($regionType == "baseStation"||$regionType=="baseStationGroup") && $timeType == "hour") {
            $filter = "";
            if ($baseStationStr) {
                $filter = $filter." and userLabel in $baseStation";
            }

            if ($hour) {
                $filter = $filter." and hourId in ($hour)";
            }

            $sql = "select timeStamp,hourId,userLabel,$ULQciX,$DLQciX,count(distinct ecgi) as count_ecgi from mrs_packet_lossrate where timeStamp>='$startTime' and timeStamp <= '$endTime' ".$filter." group BY timeStamp,userLabel,hourId order by timeStamp,hourId,userLabel";
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                
                    $timeStamp = $row[0];
                    $hourId    = $row[1];
                    $userLabel = $row[2];
                    $upArr     = array();
                    $downArr   = array();
                    $temp1     = array();
                    $temp2     = array();
                    for ($i = 3; $i <= 30; $i++) {
                        array_push($upArr, floatval($row[$i]));
                        array_push($downArr, floatval($row[($i + 28)]));
                        $temp1['up'.($i - 2)]   = floatval($row[$i]);
                        $temp2['down'.($i - 2)] = floatval($row[($i + 28)]);
                    }

                    $temp = array_merge($temp1, $temp2);
                    $up   = $this->getResultByWeight($upArr);
                    $down = $this->getResultByWeight($downArr);

                    if ($regionType=="baseStationGroup") {
                        $baseStationGroup='baseStationGroup';
                            $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId,"cellTotal"=>$row[59], "city" => $city,"baseStationGroup"=>$baseStationGroup, "userLabel" => $userLabel, "上行丢包率" => $up, "下行丢包率" => $down), $temp);
                    } else {
                            $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId,"cellTotal"=>$row[59],"city" => $city, "userLabel" => $userLabel, "上行丢包率" => $up, "下行丢包率" => $down), $temp);
                    }
                    array_push($items, $temp);
                }//end foreach
            }//end if
        } else if (($regionType == "groupEcgi"||$regionType=="cellGroup") && $timeType == "hour") {
            $filter = "";
            if ($groupEcgiStr) {
                $filter = $filter." and ecgi in $groupEcgi";
            }

            if ($hour) {
                $filter = $filter." and hourId in ($hour)";
            }

            $sql = "select timeStamp,hourId,userLabel,ecgi,$ULQciX,$DLQciX,count(distinct ecgi) from mrs_packet_lossrate where timeStamp>='$startTime' and timeStamp <= '$endTime' ".$filter." group BY timeStamp,userLabel,hourId".$groupBy." order by timeStamp,hourId";
            $res = $db->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                $city = $dbc->getPGToCHName($city);
                foreach ($rows as $row) {
                    
                    $timeStamp = $row[0];
                    $hourId    = $row[1];
                    $userLabel = $row[2];
                    $ecgi      = $row[3];
                    $upArr     = array();
                    $downArr   = array();
                    $temp1     = array();
                    $temp2     = array();
                    for ($i = 4; $i <= 31; $i++) {
                        array_push($upArr, floatval($row[$i]));
                        array_push($downArr, floatval($row[($i + 28)]));
                        $temp1['up'.($i - 3)]   = floatval($row[$i]);
                        $temp2['down'.($i - 3)] = floatval($row[($i + 28)]);
                    }

                    $temp = array_merge($temp1, $temp2);
                    $up   = $this->getResultByWeight($upArr);
                    $down = $this->getResultByWeight($downArr);
                    $up_avg   = $this->getProportion($upArr);
                    $down_avg = $this->getProportion($downArr);
                    if ($regionType=='cellGroup') {

                        $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId,"cellTotal"=>$row[60],"city" => $city,"cellGroup"=>'cellGroup', "userLabel" => $userLabel, "ecgi" => $ecgi, "上行丢包率" => $up, "下行丢包率" => $down), $temp);
                    } else {
                        $temp = array_merge(array("date" => $timeStamp, "hourId" => $hourId,"city" => $city, "userLabel" => $userLabel, "ecgi" => $ecgi, "上行丢包率" => $up, "下行丢包率" => $down,"上行丢包率占比"=>$up_avg,"下行丢包率占比"=>$down_avg), $temp);

                    }
                    array_push($items, $temp);
                }//end foreach
            }//end if
        }//end if

        $filename = "files/MRS".date('YmdHis').".csv";
        if ($regionType == "city" && $timeType == "day") {
            $text = "date,cellTotal,city,上行丢包率,下行丢包率,上行丢包率>20%的采样点占比,下行丢包率>20%的采样点占比";
        } else if ($regionType == "city" && $timeType == "hour") {
            $text = "date,hourId,cellTotal,city,上行丢包率,下行丢包率,上行丢包率>20%的采样点占比,下行丢包率>20%的采样点占比";
        } else if ($regionType == "baseStation" && $timeType == "day") {
            $text = "date,cellTotal,city,userLabel,上行丢包率,下行丢包率";
        } else if ($regionType == "baseStationGroup" && $timeType == "day") {
            $text = "date,cellTotal,city,baseStationGroup,userLabel,上行丢包率,下行丢包率";
        } else if ($regionType == "baseStationGroup" && $timeType == "hour") {
            $text = "date,hourId,cellTotal,city,baseStationGroup,userLabel,上行丢包率,下行丢包率";
        } else if ($regionType == "baseStation" && $timeType == "hour") {
            $text = "date,hourId,cellTotal,city,userLabel,上行丢包率,下行丢包率";
        } else if ($regionType == "groupEcgi" && $timeType == "day") {
            $text = "date,city,userLabel,ecgi,上行丢包率,下行丢包率,上行丢包率>20%的采样点占比,下行丢包率>20%的采样点占比";
        } else if ($regionType == "groupEcgi" && $timeType == "hour") {
            $text = "date,hourId,city,userLabel,ecgi,上行丢包率,下行丢包率,上行丢包率>20%的采样点占比,下行丢包率>20%的采样点占比";
        } else if ($regionType == "cellGroup" && $timeType == "day") {
            $text = "date,cellTotal,city,cellGroup,userLabel,ecgi,上行丢包率,下行丢包率";
        } else if ($regionType == "cellGroup" && $timeType == "hour") {
            $text = "date,hourId,cellTotal,city,cellGroup,userLabel,ecgi,上行丢包率,下行丢包率";
        }

        for ($i = 1; $i <= 28; $i++) {
            $text = $text.",上行丢包数".$i;
        }

        for ($i = 1; $i <= 28; $i++) {
            $text = $text.",下行丢包数".$i;
        }

        // $result['text'] = $text;
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
     * @param array  $result   导出数据
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
