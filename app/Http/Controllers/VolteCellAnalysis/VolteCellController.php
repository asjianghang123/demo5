<?php

/**
* VolteCellController.php
*
* @category VolteCellAnalysis
* @package  App\Http\Controllers\VolteCellAnalysis
* @author   ericsson <genius@ericsson.com>
* @license  MIT License
* @link     https://laravel.com/docs/5.4/controllers
*/
namespace App\Http\Controllers\VolteCellAnalysis;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\FileUtil;
use App\Http\Requests;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use App\Models\Mongs\SiteLte;
use Illuminate\Support\Facades\Auth;
use App\Models\Mongs\TraceServerInfo;
use Illuminate\Support\Facades\Storage;
use Config;

/**
 * volte小区处理
 * Class VolteCellController
 *
 * @category VolteCellAnalysis
 * @package  App\Http\Controllers\VolteCellAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class VolteCellController extends Controller
{
    public function getVolteAlarmNum() {
        $date = Input::get("date");
        $cell = Input::get('cell');
        $city = input::get('city');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('alarm', 'Alarm');
        $sql = "SELECT COUNT(*) AS num FROM FMA_alarm_list WHERE DATE_FORMAT(Event_time, '%Y-%m-%d') >= '$date' AND meContext ='$cell' AND city = '$city'";
        // var_dump($sql);return;
        $result = array();
        $res = $db->query($sql);
        if ($res) {
            $row = $res->fetch(PDO::FETCH_ASSOC);
            // var_dump($row);return;
            $alarmnum = $row["num"];
        } else {
            $alarmnum = 0;
        }
        // $result = array();
        // var_dump($alarmnum);return;
        $result['告警数量'] = $alarmnum;
        $result['Polar-告警'] = $alarmnum;
        // var_dump($result);return;
        return $result;
    }
    public function getVolteAvgPrb() 
    {
        $date = Input::get("date");
        $cell = Input::get('cell');
        $hour = Input::get("hour");
        $city = input::get("city");
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $sql = "SELECT cell,PRB1上行干扰电平,PRB2上行干扰电平,PRB3上行干扰电平,PRB4上行干扰电平,PRB5上行干扰电平,PRB6上行干扰电平,PRB7上行干扰电平,PRB8上行干扰电平,PRB9上行干扰电平,PRB10上行干扰电平,PRB11上行干扰电平,PRB12上行干扰电平,PRB13上行干扰电平,PRB14上行干扰电平,PRB15上行干扰电平,PRB16上行干扰电平,PRB17上行干扰电平,PRB18上行干扰电平,PRB19上行干扰电平,PRB20上行干扰电平,PRB21上行干扰电平,PRB22上行干扰电平,PRB23上行干扰电平,PRB24上行干扰电平,PRB25上行干扰电平,PRB26上行干扰电平,PRB27上行干扰电平,PRB28上行干扰电平,PRB29上行干扰电平,PRB30上行干扰电平,PRB31上行干扰电平,PRB32上行干扰电平,PRB33上行干扰电平,PRB34上行干扰电平,PRB35上行干扰电平,PRB36上行干扰电平,PRB37上行干扰电平,PRB38上行干扰电平,PRB39上行干扰电平,PRB40上行干扰电平,PRB41上行干扰电平,PRB42上行干扰电平,PRB43上行干扰电平,PRB44上行干扰电平,PRB45上行干扰电平,PRB46上行干扰电平,PRB47上行干扰电平,PRB48上行干扰电平,PRB49上行干扰电平,PRB50上行干扰电平,PRB51上行干扰电平,PRB52上行干扰电平,PRB53上行干扰电平,PRB54上行干扰电平,PRB55上行干扰电平,PRB56上行干扰电平,PRB57上行干扰电平,PRB58上行干扰电平,PRB59上行干扰电平,PRB60上行干扰电平,PRB61上行干扰电平,PRB62上行干扰电平,PRB63上行干扰电平,PRB64上行干扰电平,PRB65上行干扰电平,PRB66上行干扰电平,PRB67上行干扰电平,PRB68上行干扰电平,PRB69上行干扰电平,PRB70上行干扰电平,PRB71上行干扰电平,PRB72上行干扰电平,PRB73上行干扰电平,PRB74上行干扰电平,PRB75上行干扰电平,PRB76上行干扰电平,PRB77上行干扰电平,PRB78上行干扰电平,PRB79上行干扰电平,PRB80上行干扰电平,PRB81上行干扰电平,PRB82上行干扰电平,PRB83上行干扰电平,PRB84上行干扰电平,PRB85上行干扰电平,PRB86上行干扰电平,PRB87上行干扰电平,PRB88上行干扰电平,PRB89上行干扰电平,PRB90上行干扰电平,PRB91上行干扰电平,PRB92上行干扰电平,PRB93上行干扰电平,PRB94上行干扰电平,PRB95上行干扰电平,PRB96上行干扰电平,PRB97上行干扰电平,PRB98上行干扰电平,PRB99上行干扰电平,PRB100上行干扰电平 FROM interfereCell WHERE cell = '$cell' AND day_id = '$date' AND hour_id = '$hour' AND city = '$city'";
        $res = $db->query($sql, PDO::FETCH_NUM);
        $result = array();
        $avgprb = 0;
        $polarValue = 0;
        if ($res) {
            $row = $res->fetchall();
            foreach ($row as $key => $value) {
                $cell = $value[0];
                $j = 0;
                for ($i=1; $i < count($value); $i++) { 
                    if ($value[$i] == null) {
                        continue;
                    }
                    $avgprb = $avgprb + $value[$i];
                    $j++;
                }
                if ($j == 0) {
                    $avgprb = 0;
                    $polarValue = 0; 
                } else {
                    $avgprb = round($avgprb / $j, 2);
                }
            }
        }
        if ($avgprb >= -98) {
            $polarValue = 100;
        } elseif ($avgprb <= -112) {
            $polarValue = 0;
        } else {
            $polarValue = 50;
        }
        $result['平均PRB'] = $avgprb;
        $result['Polar-干扰'] = $polarValue;
        return $result;
    }


    // public function getparameter()
    // {
    //     $date = new DateTime();
    //     $date->sub(new DateInterval('P1D'));
    //     // $yesDate = $date->format('ymd');
    //     // $dbname = 'kget' . $yesDate;
    //     //判断是否用今天的数据
    //     $dbname = "kget".date("ymd");
    //     $dbc = new DataBaseConnection();
    //     $pdo = $dbc->getDB('mongs', $dbname);
    //     $sql = "SELECT COUNT(*) AS num FROM mongs.task WHERE taskName='$dbname'";
    //     $row = $pdo->query($sql, PDO::FETCH_ASSOC)->fetchall();
    //     if ($row[0]['num'] == 0) {
    //         $dbname = "kget".date("ymd", strtotime("-1 day"));
    //     }
    //     $cell = Input::get('cell');
    //     $table = Input::get('table');
    //     $dbc = new DataBaseConnection();
    //     $db = $dbc->getDB('mongs', $dbname);

    //     $sql = "select * from $table where cellId='$cell';";
    //     $item = array();
    //     $rs = $db->query($sql);
    //     if ($rs) {
    //         $rows = $rs->fetchall(PDO::FETCH_ASSOC);
    //         $item['record'] = count($rows);
    //         if (count($rows) > 0) {
    //             $row = $rows[0];
    //             $item['content'] = implode(",", array_keys($row));
    //             foreach ($rows as $row) {
    //                 $item['rows'][] = $row;
    //             }
    //             // $filename = "common/files/" . $cell . "_" . $table . date('YmdHis') . ".csv";
    //             // $item['filename'] = $filename;
    //             // $this->resultToCSV2($item, $filename);
    //         }
    //     }
    //     var_dump($item);return;
    //     echo json_encode($item);
    // }

    public function highrrcnum()
    {
        $cell = input::get("cell");
        $date = input::get("day_id");
        $hour = input::get("hour");
        $table = input::get("table");
        $city = input::get('city');
        if ($table != "temp_badhandovercell") {
            $sql = "SELECT SUM(`最高RRC用户数`) AS NUM,MAC层时延 FROM $table WHERE cell ='$cell' AND hour_id='$hour' AND city = '$city'";
            $dbc = new DataBaseConnection();
            $db = $dbc->getDB('autokpi', 'AutoKPI');
            $res = $db->query($sql, PDO::FETCH_ASSOC);
            $rrcnum = 0;
            $value = 0;
            $mac = 0;
            if ($res) {
                $row = $res->fetch();
                $rrcnum = $row['NUM'];
                $mac = $row['MAC层时延'];
                if ($rrcnum > 100) {
                    $value = 100;
                } else {
                    $value = intval($rrcnum);
                }
            } else {
                $rrcnum = 0;
                $mac = 0;
                $value = 0;
            }
        } else {
            $rrcnum = 0;
            $mac = 0;
            $value = 0;
        }
        $result = array();
        $result['最高RRC用户数'] = $rrcnum;
        $result['MAC层时延'] = $mac;
        $result['Polar-高话务'] = $value;
        $result['Polar-最高RRC用户数'] = $value;
        return $result;
    }
    // public function neightcell() {
    //     $cell = input::get("cell");
    //     $tables = input::get("table");
    //     $date = input::get("day_id");
    //     $dbc = new DataBaseConnection();
    //     $db = $dbc->getDB('mongs', 'mongs');
    //     $sql = "select ecgi from siteLte where cellName in $cell";
    //     $res = $db->query($sql, PDO::FETCH_ASSOC);
    //     $ecgi = 0;
    //     if ($res) {
    //         $row = $res->fetchall();
    //         $ecgi = $row['ecgi'];
    //     }
    //     $sql = "SELECT ecgi,count(*) as num from $tables where isdefined_direct=0 and ecgi in $ecgi AND distance_direct<0.8 and dateId >= $date";
    //     $db1 = $dbc->getDB("mr", "mr");
    //     $res1 = $db->query($sql, PDO::FETCH_ASSOC);
    //     $polarValue = 0;
    //     if ($res1) {
    //         $row = $res1->fetchall();
    //         $neightnum = $row['num'];
    //         $polarValue =
    //     }
    // }
    public function weakcover() {
        $weakcovernum = input::get('num');
        $num2 = input::get('num2');
        // $cell = input::get("cell");
        // $hour = input::get("hour");
        // $date = input::get("date");
        // $city = input::get("city");
        // $sql = "SELECT `RSRP<-116的比例` AS num FROM badHandoverCell WHERE day_id ='$date' and hour_id= '$hour' AND cell ='$cell' AND city = '$city'";
        // $dbc = new DataBaseConnection();
        // $db = $dbc->getDB('autokpi', 'AutoKPI');
        // $res = $db->query($sql);
        // $weakcovernum = 0;
        // $polarValue = 0;
        // if ($res) {
        //     while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
        //         $weakcovernum = $row['num'];
        //         if ($weakcovernum > 20) {
        //             $polarValue = 100;
        //         } elseif ($weakcovernum < 2) {
        //             $polarValue = 0;
        //         } else {
        //             $polarValue = round(($weakcovernum-2)*100/18, 2); 
        //         }
        //     }
        // }
        // $result = array();
        // if ($weakcovernum = "") {
        //    $weakcovernum = 0;
        // }
        if ($weakcovernum > 20) {
            $polarValue = 100;
        } elseif ($weakcovernum < 2) {
            $polarValue = 0;
        } else {
            $polarValue = round(($weakcovernum-2)*100/18, 2); 
        }
        $result['RSRP<-116的比例'] = $num2;
        $result['Polar-弱覆盖'] = $polarValue;
        return $result;
    }

    public function zhicha() {
        $cell = input::get("cell");
        $hour = input::get("hour");
        $date = input::get("date");
        $city = input::get("city");
        $sql = "SELECT `RSRQ<-15.5的比例` AS num, `下行CQI<3的比例` AS num1 FROM badHandoverCell WHERE day_id ='$date' and hour_id= '$hour' AND cell ='$cell' AND city = '$city'";
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('autokpi', 'AutoKPI');
        $res = $db->query($sql);
        $zhichanum = 0;
        $zhichanum1 = 0;
        if ($res) {
            while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                $zhichanum = $row['num'];
                $zhichanum1 = $row["num1"];
            }
        }
        $result = array();
        // if ($zhichanum = "") {
        //     $zhichanum = 0;
        // }
        // if ($$zhichanum1 = "") {
        //     $zhichanum1 = 0;
        // }
        $result['RSRQ<-15.5的比例'] = $zhichanum;
        $rsult['下行CQI<3的比例'] = $zhichanum1;
        $result['Polar-质差'] = 0;
        return $result;
    }
    public function lowaccesscellcanshu() {
        $cell = input::get('cell');
        $txt = "common/txt/lowAccess.txt";
        $dbc = new DataBaseConnection();
        $pdo = $dbc->getDB('mongs', 'mongs');
        $dbname = "kget".date("ymd");
        $sql = "SELECT COUNT(*) AS num FROM task WHERE taskName='$dbname';";
        $row = $pdo->query($sql, PDO::FETCH_ASSOC)->fetchall();
        if ($row[0]['num'] == 0) {
            $dbname = "kget".date("ymd",strtotime("-1 day"));
        }
        // $yesDate = date("ymd",strtotime("-1 day"));
        // $dbname = 'kget' . $yesDate;
        $table = 'ParaCheckBaseline';
        // var_dump($dbname);
        // $db = new PDO("mysql:host=10.39.148.186;dbname=$dbname", "root", "mongs");
        // $db = '';
        try {
           $db = new PDO("mysql:host=10.39.148.186;dbname=$dbname", "root", "mongs");
        } catch (Exception $e) {
            return;
        }
        // $db = $dbc->getDB('kget', '$dbname');
        // var_dump($db);
        $sql = "select cellId,count(*) as num from $table where cellId = '$cell'";

        $res = $db->query($sql);
        // var_dump($res);
        $canshunum = 0;
        if ($res) {
            $row = $res->fetchall(PDO::FETCH_ASSOC);
            foreach ($row as $key => $value) {
                $canshunum = $value['num'];
            }
        }
        $result = array();
        // var_dump($db);
        $sql = "select cellId,highTraffic from $table where cellId = '$cell'";
        // var_dump($sql);
        $res = $db->query($sql);
        // var_dump($res);
        $srUserFlag = 0;
        $polarcanshu = 0;
        if($res) {
            $row = $res->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row as $key => $value) {
                $meContext = $value['cellId'];
                $flag = $value['highTraffic'];
                $file = fopen($txt, "r");
                fgets($file);
                while (!feof($file)) {
                    $arr = explode("=", trim(fgets($file)));
                    $arrMeContext = $arr[1];
                    if ($arrMeContext == $value['cellId']) {
                        $cell = $arr[1];
                        if ($flag == "YES") {
                           $polarcanshu = 100; 
                            $srUserFlag = 1;
                        } else {
                            $sql = "SELECT count(*) as num from $table where cellId='$cell';";
                            $row_count = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
                            if (count($row_count) == 0) {
                                $polarcanshu = 0;
                            } else {
                                if ($row_count[0]['num'] == 0) {
                                    $polarcanshu = 0;
                                } else {
                                    $polarcanshu = 50;
                                }
                            }
                        }
                        
                    }
                }
                fclose($file);
            }
        }

        if ($srUserFlag == 0) {
            $sql = "SELECT cellId,count(*) as num from  EUtranCellTDD where cellId ='$cell'";
            $res = $db->query($sql);
            if ($res) {
                $row = $res->fetchall(PDO::FETCH_ASSOC);
                foreach ($row as $key => $value) {
                    $cell = $value['cellId'];
                    $num = $value['num'];
                    $canshunum = $num;
                }
            }
            $sql = "SELECT EUtranCellTDDId,noOfPucchSrUsers FROM EUtranCellTDD WHERE EUtranCellTDDId ='$cell'";
            $res = $db->query($sql);
            if ($res) {
                $row = $res->fetchAll(PDO::FETCH_ASSOC);
                foreach ($row as $key => $value) {
                    $num = $value['noOfPucchSrUsers'];
                    $file = fopen($txt, "r");
                    fgets($file);
                    while (!feof($file)) {
                        $arr = explode("=", trim(fgets($file)));
                        $arrMeContext = $arr[1];
                        if ($arrMeContext == $value['EUtranCellTDDId']) {
                            $cell = $arr[1];
                            if ($num < 400) {
                                $srUserFlag = 1;
                                $polarcanshu = 50; 
                            }
                        }  
                    }
                }
            }
        }

        if ($srUserFlag == 0) {
            $sql = "SELECT cellId,count(*) as num from  EUtranCellFDD where cellId ='$cell'";
            $res = $db->query($sql);
            if ($res) {
                $row = $res->fetchall(PDO::FETCH_ASSOC);
                foreach ($row as $key => $value) {
                    $cell = $value['cellId'];
                    $num = $value['num'];
                    $canshunum = $num;
                }
            }
            $sql = "SELECT EUtranCellFDDId,noOfPucchSrUsers FROM EUtranCellFDD WHERE EUtranCellFDDId ='$cell'";
            $res = $db->query($sql);
            if ($res) {
                $row = $res->fetchAll(PDO::FETCH_ASSOC);
                foreach ($row as $key => $value) {
                    $num = $value['noOfPucchSrUsers'];
                    $file = fopen($txt, "r");
                    fgets($file);
                    while (!feof($file)) {
                        $arr = explode("=", trim(fgets($file)));
                        $arrMeContext = $arr[1];
                        if ($arrMeContext == $value['EUtranCellFDDId']) {
                            $cell = $arr[1];
                            if($num < 400) {
                                $srUserFlag = 1;
                                $polarcanshu = 50;
                            }
                        }  
                    }
                    fclose($file);
                }
            }
        }

        // $table1 = "OptionalFeatureLicense";
        // $sql = "SELECT meContext,serviceState,featureState,licenseState,OptionalFeatureLicenseId FROM $table1 WHERE meContext in $erbsStr AND (OptionalFeatureLicenseId = 'DynamicQosModification'OR OptionalFeatureLicenseId = 'InterFrequencyLteHandover' OR OptionalFeatureLicenseId = 'MultiErabsPerUser') AND serviceState='0 (INOPERABLE)'";
        // $res = $db->query($sql);
        // if ($res) {
        //     $row = $res->fetchAll(PDO::FETCH_ASSOC);
        //     foreach ($row as $key => $value) {
        //         $serviceState = $value['serviceState'];
        //         $featureState = $value['featureState'];
        //         $licenseState = $value['licenseState'];
        //         $OptionalFeatureLicenseId = $value['OptionalFeatureLicenseId'];
        //         $featureState = $featureState . "," . $OptionalFeatureLicenseId;
        //         $file = fopen($txt, "r");
        //         fgets($file);
        //         while (!feof($file)) {
        //             $arr = explode("=", trim(fgets($file)));
        //             $arrMeContext = $arr[2];
        //             if ($arrMeContext == $value['meContext']) {
        //                 $cell = $arr[1];
        //                 if ($serviceState == '0 (INOPERABLE)') {
        //                 // print_r("UPDATE $tables SET `featureState`=$featureState WHERE cell='$cell';");
        //                     $pdo->query("UPDATE $tables SET `featureState`='$featureState' WHERE cell='$cell';");
        //                     $pdo->query("UPDATE $tables SET `licenseState`='$licenseState' WHERE cell='$cell';");
        //                 }
        //             }
        //         }
        //         fclose($file);
        //     }
        // }
        $result['参数'] = $canshunum;
        $result['Polar-参数'] = $polarcanshu;
        return $result;
    }
    public function overlapcover() {
        $cell = input::get('cell');
        $date_from = date("Y-m-d", strtotime("-1 day"));
        $city = input::get('city');
        $sql = "SELECT cellName,ecgi FROM siteLte WHERE cellName = '$cell'";
        $dbc = new DataBaseConnection();
        $pdo = $dbc->getDB('mongs', 'mongs');
        $res = $pdo->query($sql, PDO::FETCH_ASSOC);
        $database = "";
        if ($city == "changzhou") {
            $database = "MR_CZ";
        } else if ($city == "suzhou") {
            $database = "MR_SZ";
        } else if ($city == "zhenjiang") {
            $database = "MR_ZJ";
        } else if ($city == "nantong") {
            $database = "MR_NT";
        } else if ($city == "wuxi") {
            $database = "MR_WX";
        }
        // var_dump($database);
        $overlapcovernum = 0;
        $polarValue = 0;
        $ecgi = "";
        // var_dump($res);
        if ($res) {
            $row = $res->fetch();
            // var_dump($row);return;
            $ecgi = $row['ecgi'];
            // $mr = $dbc->getDB('MR', 'mr');
            // $mr_pdo = $mr->query("use $database");
            $db1 = new PDO("mysql:host=10.40.57.134;dbname=$database;port=8066", "mr", "mr");
            // var_dump($db1);
            $sql = "SELECT ecgi,rate AS num FROM mroOverCoverage_day WHERE  dateId = '$date_from' AND ecgi = '$ecgi'";
            // var_dump($sql);
            $res = $db1->query($sql, PDO::FETCH_ASSOC);
            // var_dump($res);  
            if ($res) {
                $row = $res->fetchall();
                foreach ($row as $key => $value) {
                    $overlapcovernum = $value['num'];
                    // if ($num <= 0.2) {
                    //     $polarValue = 0;
                    // } elseif ($num > 0.2 && $num < 5) {
                    //     $polarValue = round(($num-0.2) * 100/4.8, 2);
                    // } else {
                    //     $polarValue = 100;
                    // }
                    if ($overlapcovernum <= 0.2) {
                        $polarValue = 0;
                    } elseif ($overlapcovernum > 0.2 && $overlapcovernum < 0.5) {
                        $polarValue = 50;
                    } else {
                        $polarValue = 100;
                    }
                } 
            }  
        }
        $result = array();
        $result['重叠覆盖度'] = $overlapcovernum;
        $result['Polar-重叠覆盖'] = $polarValue;
        return $result;
    }
    public function neighcell() {
        $flag = input::get('flag');
        $cell = input::get('cell');
        $city = input::get('city');
        $database = "";
        if ($city == "changzhou") {
            $database = "MR_CZ";
        } else if ($city == "suzhou") {
            $database = "MR_SZ";
        } else if ($city == "zhenjiang") {
            $database = "MR_ZJ";
        } else if ($city == "nantong") {
            $database = "MR_NT";
        } else if ($city == "wuxi") {
            $database = "MR_WX";
        }
        $date_from = date("Y-m-d", strtotime("-1 day"));
        $dbc = new DataBaseConnection();
        $sql = "select cellName,ecgi from siteLte where cellName = '$cell'";
        $pdo = $dbc->getDB('mongs', 'mongs');
        $res = $pdo->query($sql);
        $neighnum = 0;
        $polarValue = 0;
        if ($res) {
            $row = $res->fetch(PDO::FETCH_ASSOC);
            $ecgi = $row['ecgi'];
            $tables = 'mreServeNeigh_day';
            $sql = "SELECT ecgi,count(*) as num from $tables where isdefined_direct=0 and ecgi ='$ecgi' AND distance_direct<0.8 and dateId >= '$date_from'";
            $db1 = new PDO("mysql:host=10.40.57.134;dbname=$database;port=8066", "mr", "mr");
            $res = $db1->query($sql, PDO::FETCH_ASSOC);
            if ($res) {
                $row = $res->fetch();
                $neighnum = $row['num'];
                if ($flag == 1) {
                    $polarValue = intval($neighnum*50/12);
                    if ($polarValue > 50) {
                        $polarValue = 50;
                    }
                } else {
                    $polarValue = intval($neighnum*100/12);
                    if ($polarValue > 100) {
                        $polarValue = 100;
                    }
                }
            }
        }
        $result = array();
        $result['需要加邻区数量'] = $neighnum;
        $result['Polar-邻区'] = $polarValue;
        return $result;
    }
    public function getvolteZhichaCellChart()
    {
        $dbn = new DataBaseConnection();
        // $conn = $dbn->getConnDB('mongs');
        $conn = $dbn->getDB('autokpi', 'AutoKPI');
        if ($conn == null) {
            echo 'Could not connect';
        }
        // mysql_select_db('AutoKPI', $conn);
        $table = Input::get('table');

        $cell = Input::get('cell');
        $startTime = Input::get('dateTime');
        $endTime = Input::get('endTime');
        $yAxis_name_left = Input::get('yAxis_name_left');
        $yAxis_name_right = Input::get('yAxis_name_right');
        $res = $conn->query("select day_id,hour_id,`" . $yAxis_name_left . "`,`" . $yAxis_name_right . "` from " . $table . " where day_id>='" . $startTime . "' and day_id<='" . $endTime . "' and cell='" . $cell . "' ORDER BY day_id,hour_id");
        // $res = mysql_query("select day_id,hour_id,`" . $yAxis_name_left . "`,`" . $yAxis_name_right . "` from " . $table . " where day_id>='" . $startTime . "' and day_id<='" . $endTime . "' and cell='" . $cell . "' ORDER BY day_id,hour_id");
        $yAxis = array();
        $yAxis_2 = array();
        $items = array();
        $returnData = array();
        $series = array();
        $series_2 = array();
        $categories = array();
        // $line = $res->fetch(PDO::FETCH_NUM);
        // var_dump($line);return;

        while ($line = $res->fetch(PDO::FETCH_NUM)) {
            $time = strval(strval($line[0]) . " " . strval($line[1])) . ":00";

            $time = mb_convert_encoding($time, 'gb2312', 'utf-8');

            array_push($yAxis, $line[2]);
            array_push($yAxis_2, $line[3]);
            array_push($categories, $time);

        }

        $series['name'] = $yAxis_name_left;
        $series['color'] = '#89A54E';
        $series['type'] = 'spline';
        $series['data'] = $yAxis;


        $series_2['name'] = $yAxis_name_right;
        $series_2['color'] = '#4572A7';
        $series_2['type'] = 'column';
        $series_2['yAxis'] = 1;
        $series_2['data'] = $yAxis_2;

        array_push($items, $series_2);
        array_push($items, $series);

        $returnData['categories'] = $categories;
        $returnData['series'] = $items;

        $maxPos = array_search(max($yAxis), $yAxis);
        $max = $yAxis[$maxPos];
        $minPos = array_search(min($yAxis), $yAxis);
        $min = $yAxis[$minPos];
        $yAxis = $this->getYAxis($max, $min);
        $returnData['yAxis'] = $yAxis;
        $returnData['cell'] = $cell;
        echo json_encode($returnData);

    }//end getZhichaCellChart()

    protected function getYAxis($max, $min)
    {
        $yAxis = array();
        $max = ceil($max);
        $min = floor($min);
        $yAxis0 = $min;
        $yAxis2 = round(($min + $max) / 2, 2);
        $yAxis1 = round(($min + $yAxis2) / 2, 2);
        $yAxis4 = $max;
        $yAxis3 = round(($max + $yAxis2) / 2, 2);
        $yAxis5 = $yAxis4;
        array_push($yAxis, $yAxis0, $yAxis1, $yAxis2, $yAxis3, $yAxis4, $yAxis5);
        return $yAxis;
    }
    public function getvolteWeakCoverCellModel()
    {
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('mongs', 'AutoKPI');
        $table = input::get('table');
        $day_from = Input::get('dateTime');
        $day_to = Input::get('endTime');

        $return = array();
        $sql = "select id,day_id,hour_id,city,subNetwork,cell,无线接通率,`RSRP<-116的比例` from $table where day_id >= '" . $day_from . "' AND day_id <= '" . $day_to . "' AND cell='" . $cell . "'";
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $allData = array();
        foreach ($rows as $row) {
            array_push($allData, $row);
        }
        $return['content'] = "id,day_id,hour_id,city,subNetwork,cell,无线接通率,RSRP<-116的比例";
        $return['rows'] = $allData;
        $return['records'] = count($allData);
        return ($return);
    }
    public function avgta() {
        $cell = input::get('cell');
        $city = input::get('city');
        if ($city == "changzhou") {
            $database = "MR_CZ";
        } else if ($city == "suzhou") {
            $database = "MR_SZ";
        } else if ($city == "zhenjiang") {
            $database = "MR_ZJ";
        } else if ($city == "nantong") {
            $database = "MR_NT";
        } else if ($city == "wuxi") {
            $database = "MR_WX";
        }
        $date = date("Y-m-d");
        $dbc = new DataBaseConnection();
        $pdo = $dbc->getDB('mongs', 'mongs');
        $sql = "SELECT cellName,ecgi FROM siteLte WHERE cellName = '$cell'";
        $res = $pdo->query($sql, PDO::FETCH_ASSOC);
        $avgtanum = 0;
        if ($res) {
            $row = $res->fetch();
            $cellArr = [];
            $ecgi = $row['ecgi'];
            $db1 = new PDO("mysql:host=10.40.57.134;dbname=$database;port=8066", "mr", "mr");
            $sql = "SELECT max(hourId) AS hour FROM MRS WHERE timeStamp='$date';";
            $row = $db1->query($sql, PDO::FETCH_ASSOC)->fetch();
            $hour = $row['hour'];
            if ($hour == '') {
                $date = date("Y-m-d", strtotime("-1 day"));
                $sql = "SELECT max(hourId) AS hour FROM MRS WHERE timeStamp='$date';";
                $row = $db1->query($sql, PDO::FETCH_ASSOC)->fetcha();
                $hour = $row['hour'];
            }
            $sum_num=45;
            $name='TADV';
            $searchArr = array();
            for ($i = 0; $i < $sum_num; $i++) {
                if ($i < 10) {
                    $n = "0".$i;
                } else {
                    $n = $i;
                }
                array_push($searchArr, "sum(mr_".$name."_".$n.") as mr_".$name."_".$n);
            }
            $searchArr = implode(",", $searchArr);
            $sql = "SELECT ecgi,$searchArr from MRS where timeStamp='$date' AND hourId=$hour AND ecgi = '$ecgi'";
            $res = $db1->query($sql);
            if ($res) {
                $rows = $res->fetch(PDO::FETCH_NUM);
                for ($i = 1; $i <= $sum_num; $i++) {
                    if ($i-1<10) {
                        $k="0".($i-1);
                    } else {
                        $k=($i-1);
                    }
                    $temp[$name.$k] = $rows[$i];   
                }
                // var_dump($temp);
                $ue_avg = $this->getueAvg($temp);
                // if ($ue_avg == '') {

                // }
                $avgtanum = $ue_avg;
            }
        }
        $result = array();
        $result['avgTA'] = round($avgtanum, 2);
        return $result;
    }
    public function avgtabadhandover() {
        $cell = input::get('cell');
        $city = input::get('city');
        if ($city == "ERICSSON-CMJS-CZ") {
            $database = "MR_CZ";
        } else if ($city == "ERICSSON-CMJS-SZ") {
            $database = "MR_SZ";
        } else if ($city == "ERICSSON-CMJS-ZJ") {
            $database = "MR_ZJ";
        } else if ($city == "ERICSSON-CMJS-NT") {
            $database = "MR_NT";
        } else if ($city == "ERICSSON-CMJS-WX") {
            $database = "MR_WX";
        }
        $date = date("Y-m-d");
        $dbc = new DataBaseConnection();
        $pdo = $dbc->getDB('mongs', 'mongs');
        $sql = "SELECT cellName,ecgi FROM siteLte WHERE cellName = '$cell'";
        $res = $pdo->query($sql, PDO::FETCH_ASSOC);
        $avgtanum = 0;
        if ($res) {
            $row = $res->fetch();
            $cellArr = [];
            $ecgi = $row['ecgi'];
            $db1 = new PDO("mysql:host=10.40.57.134;dbname=$database;port=8066", "mr", "mr");
            $sql = "SELECT max(hourId) AS hour FROM MRS WHERE timeStamp='$date';";
            $row = $db1->query($sql, PDO::FETCH_ASSOC)->fetch();
            $hour = $row['hour'];
            if ($hour == '') {
                $date = date("Y-m-d", strtotime("-1 day"));
                $sql = "SELECT max(hourId) AS hour FROM MRS WHERE timeStamp='$date';";
                $row = $db1->query($sql, PDO::FETCH_ASSOC)->fetcha();
                $hour = $row['hour'];
            }
            $sum_num=45;
            $name='TADV';
            $searchArr = array();
            for ($i = 0; $i < $sum_num; $i++) {
                if ($i < 10) {
                    $n = "0".$i;
                } else {
                    $n = $i;
                }
                array_push($searchArr, "sum(mr_".$name."_".$n.") as mr_".$name."_".$n);
            }
            $searchArr = implode(",", $searchArr);
            $sql = "SELECT ecgi,$searchArr from MRS where timeStamp='$date' AND hourId=$hour AND ecgi = '$ecgi'";
            $res = $db1->query($sql);
            if ($res) {
                $rows = $res->fetch(PDO::FETCH_NUM);
                for ($i = 1; $i <= $sum_num; $i++) {
                    if ($i-1<10) {
                        $k="0".($i-1);
                    } else {
                        $k=($i-1);
                    }
                    $temp[$name.$k] = $rows[$i];   
                }
                // var_dump($temp);
                $ue_avg = $this->getueAvg($temp);
                // if ($ue_avg == '') {

                // }
                $avgtanum = $ue_avg;
            }
        }
        $result = array();
        $result['avgTA'] = round($avgtanum, 2);
        return $result;
    }
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
    public function highlostcellcanshu() {
        $cell = input::get('cell');
        $dbname = "kget".date("ymd");
        $dbc = new DataBaseConnection();
        $pdo = $dbc->getDB('mongs', 'mongs');
        $sql = "SELECT COUNT(*) AS num FROM task WHERE taskName='$dbname';";
        $row = $pdo->query($sql, PDO::FETCH_ASSOC)->fetchall();
        if ($row[0]['num'] == 0) {
            $dbname = "kget".date("ymd",strtotime("-1 day"));
        }
        // $table = 'ParaCheckBaseline';
        $db = '';
        try {
            $db = new PDO("mysql:host=10.39.148.186;dbname=$dbname", "root", "mongs");
        } catch (Exception $e) {
            return;
        }

        // $date = new DateTime();    
        // $date->sub(new DateInterval('P1D'));
        // $yesDate = $date->format('ymd');
        $txt = "common/txt/highlost.txt";
        $value = 0;
        $canshu=0;
        $file = fopen($txt, "r");
        fgets($file);
        $city = '';
        while (!feof($file)) {
            $arr = explode("=", trim(fgets($file)));
            $arrCell = $arr[1];
            if ($arrCell == $cell) {
                $city = $arr[0];
                break;
            }
        }
        $cityCh = $this->getCHCity($city, $pdo);
        $subNetwork = $this->getSubNets($cityCh, $pdo);
        $canshu = 0;
        $value = 0;
        fclose($file);
        if ($value == 0) {
            //掉线差小区参数关联cellbarred小区参数为0的，即EUtranCellTDD/EUtranCellFDD这个MO下面的参数cellBarred设置为0的，则进行输出报错，评分为100分
                //EUtranCellTDD
            $filter = " WHERE cellbarred='0 (BARRED)' AND EUtranCellTDDId = '$cell'";
            if ($subNetwork != '') {
                $filter = $filter." and subNetwork in (" . $subNetwork . ")";
            }
            $sql = "SELECT COUNT(*) AS num FROM EUtranCellTDD".$filter;
            // var_dump($sql);
            $rs = $db->query($sql);
            if ($rs) {
                $row  = $rs->fetch(PDO::FETCH_ASSOC);
                if ($row['num'] > 0) {
                    $canshu=$row['num'];
                    $value = 100;
                } else {
                    //EUtranCellFDD
                    $filter = " WHERE cellbarred='0 (BARRED)' AND EUtranCellFDDId = '$cell'";
                    if ($subNetwork != '') {
                        $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                    }
                    $sql = "SELECT COUNT(*) AS num FROM EUtranCellFDD".$filter;
                    $rs = $db->query($sql);
                    if ($rs) {
                        $row  = $rs->fetch(PDO::FETCH_ASSOC);
                        if ($row['num'] > 0) {
                            echo '1';
                            $canshu=$row['num'];
                            $value = 100;
                        } 
                    }
                }
            }

            //MRE邻区排名前30的邻区有PCI一二阶冲突
            $filter = " where EutranCellTDD = '$cell'";
            if ($subNetwork != '') {
                $filter = $filter." and subNetwork in (" . $subNetwork . ")";
            }
            //一阶冲突
            $sql1 = "select count(*) from TempEUtranCellRelationNeighOfPci".$filter;
            $rs   = $db->query($sql1);
            if ($rs) {       
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if ($row[0] > 0) {
                    echo '2';
                    $canshu=$row[0];
                    $value = 100;
                } else {
                    //二阶冲突
                    $sql2 = "select count(*) from TempEUtranCellRelationNeighOfNeighPci".$filter;
                    $rs   = $db->query($sql2);
                    $row  = $rs->fetch(PDO::FETCH_NUM);
                    if ($row[0] > 0) {
                        echo '3';
                        $canshu=$row[0];
                        $value = 100;
                    }
                }
            }
            //没有定义本小区freqrel
            if ($value == 0) {
                $filter = " where EutranCellTDDId = '$cell'";
                if ($subNetwork != '') {
                    $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                }
                $sql = "select count(*) from TempMissEqualFrequency ".$filter;
                $rs   = $db->query($sql);
                if ($rs) {
                    $row  = $rs->fetch(PDO::FETCH_NUM);
                    if ($row[0] > 0) {
                        echo '4';
                        $canshu=$row[0];
                        $value = 100;
                    }
                } 
            }
            //未定邻区
            if ($value == 0) {
                $filter = " where EutranCellTDD = '$cell' and remark3 = 'NoneCellRelation'";
                if ($subNetwork != '') {
                    $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                }
                $sql = "select count(*) from TempEUtranCellRelationFewNeighborCell ".$filter;
                $rs   = $db->query($sql);
                if ($rs) {
                    $row  = $rs->fetch(PDO::FETCH_NUM);
                    if ($row[0] > 0) {
                        echo '5';
                        $canshu=$row[0];
                        $value = 100;
                    }
                }
            }
            //没有定义同站同频邻区
            if ($value == 0) {
                $filter = " where EutranCellTDD = '$cell' and remark1 = 'co-SiteNeighborRelationMiss'";
                if ($subNetwork != '') {
                    $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                }
                $sql = "select count(*) from TempEUtranCellRelationFewNeighborCell ".$filter;
                $rs   = $db->query($sql);
                if ($rs) {
                    $row  = $rs->fetch(PDO::FETCH_NUM);
                    if ($row[0] > 0) {
                        echo '6';
                        $canshu=$row[0];
                        $value = 50;
                    }
                } 
            }
            //邻区过少
            if ($value == 0) {
                $filter = " where EutranCellTDD = '$cell'";
                if ($subNetwork != '') {
                    $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                }
                $sql = "select count(*) from TempEUtranCellRelationFewNeighborCell ".$filter;
                $rs   = $db->query($sql);
                if ($rs) {
                    $row  = $rs->fetch(PDO::FETCH_NUM);
                    if ($row[0] > 0) {
                        echo '7';
                        $canshu=$row[0];
                        $value = 50;
                    }
                }      
            }
            //baseline中A类参数配置不一致的
            if ($value == 0) {
                $sql = "select siteName from mongs.siteLte where cellName = '$cell'";
                $rs   = $pdo->query($sql);
                if ($rs) {
                    $row  = $rs->fetch(PDO::FETCH_NUM);
                    $meContext = $row[0];

                    $templateId = 53;
                    $filter = " where templateId='$templateId' and category = 'A' and subNetwork in (".$subNetwork.") and ( cellId = '$cell' or (meContext = '$meContext' and cellId = ''))";
                    $sql = "select count(*) from ParaCheckBaseline".$filter;
                    $rs   = $db->query($sql);
                    if ($rs) {
                        $row  = $rs->fetch(PDO::FETCH_NUM);
                        if ($row[0] > 0) {
                            echo '8';
                            $canshu=$row[0];
                            $value = 50;
                        }
                    }       
                } 
            }
        }
        $result = array();
        $result['参数'] = $canshu;
        $result['Polar-参数'] = $value;
        return $result;

    }
    public function getCHCity($city, $pdo)
    {
        $sql    = "select cityChinese from mongs.databaseconn where connName='$city'";
        $row    = $pdo->query($sql)->fetchcolumn();
        $CHCity = $row;
        return $CHCity;
    }

    public function getSubNets($city, $pdo)
    {
        $SQL           = "select if(subNetworkFDD != '',CONCAT(subNetwork,',',subNetworkFDD),subNetwork) subNetwork from mongs.databaseconn where cityChinese = '$city'";
        // $res           = DB::select($SQL);
        $res = $pdo->query($SQL)->fetchAll(PDO::FETCH_ASSOC);
        $subNetworkArr = array();
        $subNetworkStr = '';
        foreach ($res as $value) {

            $subNetworkStr .= '"'.str_replace(',', '","', $value['subNetwork']).'",';
        }
        $subNetworkStr = substr($subNetworkStr, 0, -1);
        // return $this->reCombine($subNetworkStr);
        return $subNetworkStr;
    }
    public function badHandovercellcanshu() {
        $cell = input::get('cell');
        $dbname = "kget".date("ymd");
        $dbc = new DataBaseConnection();
        $pdo = $dbc->getDB('mongs', 'mongs');
        $sql = "SELECT COUNT(*) AS num FROM task WHERE taskName='$dbname';";
        $row = $pdo->query($sql, PDO::FETCH_ASSOC)->fetchall();
        if ($row[0]['num'] == 0) {
            $dbname = "kget".date("ymd", strtotime("-1 day"));
        }
        $db = '';
        try {
            $db = new PDO("mysql:host=10.39.148.186;dbname=$dbname", "root", "mongs");
        } catch (Exception $e) {
            return;
        }
        $txt = "common/txt/badhandover.txt";
        $value = 0;
        $canshu=0;
        $file = fopen($txt, "r");
        // fgets($file);
        $city = '';
        $meContext = '';
        // while (!feof($file)) {
        //     $arr = explode("=", trim(fgets($file)));
        //     $arrCell = $arr[1];
        //     if ($arrCell == $cell) {
        //         $city = $arr[0];
        //         $meContext = $arr[2];
        //         break;
        //     }
        // }
        // fclose($file);
        $sqlsite = "SELECT siteName,city FROM siteLte WHERE cellName ='$cell'";
        $res = $pdo->query($sqlsite);
        $row1 = $res->fetch(PDO::FETCH_NUM);
        $meContext = $row1[0];
        $city = $row1[1];
        // print_r($meContext.'-');
        // print_r($city."-".$cell."|");
        $cityCh = $this->getCHCity($city, $pdo);
        $subNetwork = $this->getSubNets($cityCh, $pdo);

        //MRE邻区排名前30的邻区有PCI一二阶冲突
        $filter = " where EutranCellTDD = '$cell' ";
        if ($subNetwork != '') {
            $filter = $filter." and subNetwork in (" . $subNetwork . ")";
        }
        $sql1 = "select count(*) from TempEUtranCellRelationNeighOfPci".$filter;
        $rs   = $db->query($sql1);
        if (!$rs) {
            continue;
        }
        $row  = $rs->fetch(PDO::FETCH_NUM);
        if ($row[0] > 0) {
            $canshu=$row[0];
            $value = 100;
        } else {
            //二阶冲突
            $sql2 = "select count(*) from TempEUtranCellRelationNeighOfNeighPci".$filter;
            $rs   = $db->query($sql2);
            if (!$rs) {
                continue;
            }
            $row  = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0] > 0) {
                $canshu=$row[0];
                $value = 100;
            }
        }
        if ($value == 0) {
            //MME LIST定义不一致
            $filter = " where meContext = '$meContext'";
            if ($subNetwork != '') {
                $filter = $filter." and subNetwork in (" . $subNetwork . ")";
            }
            $sql = "select count(*) from TempTermPointToMme_S1_MMEGI_dif ".$filter;
            $rs   = $db->query($sql);
            if (!$rs) {
                continue;
            }
            $row  = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0] > 0) {
                $canshu=$row[0];
                $value = 100;
            }
        }
        if ($value == 0) {
            //X2接口定义不一致
            $filter = " where meContext = '$meContext'";
            if ($subNetwork != '') {
                $filter = $filter." and subNetwork in (" . $subNetwork . ")";
            }
            //X2 Used IP检查
            $sql1 = "select count(*) from TempTermPointToENB_ENBID_usedIpAddress".$filter;
            $rs   = $db->query($sql1);
            if (!$rs) {
                continue;
            }
            $row  = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0] > 0) {
                $canshu=$row[0];
                $value = 100;
            } else {
                //X2-邻区eNbID检查
                $sql2 = "select count(*) from TempTermPointToENB_IP".$filter;
                $rs   = $db->query($sql2);
                if (!$rs) {
                    continue;
                }
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if ($row[0] > 0) {
                    $canshu=$row[0];
                    $value = 100;
                }
            }
        }
        if ($value == 0) {
            //如果S1切换失败的占比高于50%且相关邻区ActivePLMNlist为空
            $filter = " where meContext = '$meContext'";
            if ($subNetwork != '') {
                $filter = $filter." and subNetwork in (" . $subNetwork . ")";
            }
            $sql = "select count(*) from TempExternalEUtranCellTDDActivePlmnListCheck ".$filter;
            $rs   = $db->query($sql);
            $row  = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0] > 0) {
                $canshu=$row[0];
                $value = 100;
            }
        }
        if ($value == 0) {
            //切换准备失败次数的占比50%以上发生在，邻区外部定义不一致的邻区
            // $filter = " where meContext = '$meContext' and ExternalEUtranCellTDDId = '$ecgi_nr'";
            // if ($subNetwork != '') {
            //     $filter = $filter." and subNetwork in (" . $subNetwork . ")";
            // }
            // $sql = "select count(*) from TempExternalNeigh4G ".$filter;
            // $rs   = $db->query($sql);
            // $row  = $rs->fetch(PDO::FETCH_NUM);
            // if ($row[0] > 0) {
            //     $value = 100;
            // }
        }
        if ($value == 0) {
            //如果S1切换失败的占比高于50%，相关TAC前三天定义过不同的MMEGI提示发生过TAC割接

        }

        if ($value == 0) {
            //4G测量频点数量多于5个
            $filter = " where EutranCellTDD = '$cell' and freqNum > 5";
            if ($subNetwork != '') {
                $filter = $filter." and subNetwork in (" . $subNetwork . ")";
            }
            $sql = "select count(*) from TempMeasuringFrequencyTooMuch ".$filter;
            $rs   = $db->query($sql);
            if (!$rs) {
               $value = 0;
            } else {
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if ($row[0] > 0) {
                    $canshu=$row[0];
                    $value = 50;
                }
            }
        }
        if ($value == 0) {
            //baseline中A类参数配置不一致的
            $templateId = 53;
            $filter = " where templateId='$templateId' and category = 'A' and subNetwork in (".$subNetwork.") and ( cellId = '$cell' or (meContext = '$meContext' and cellId = ''))";
            $sql = "select count(*) from ParaCheckBaseline".$filter;
            $rs   = $db->query($sql);
            if (!$rs) {
                continue;
            }
            $row  = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0] > 0) {
                $canshu=$row[0];
                $value = 50;
            }
        }
        if ($value == 0) {
            //内外部SN length检查不一致的
            $sqlsite = "SELECT siteName FROM siteLte WHERE cellName ='$cell'";
            $res = $pdo->query($sqlsite);
            $row1 = $res->fetch(PDO::FETCH_NUM);
            $site = $row1[0];
            $sql = "SELECT pdcpSNLength,rlcSNLength FROM QciProfilePredefined WHERE qciProfilePredefinedId='qci1' AND meContext ='$site'";
            $rs = $db->query($sql);
            $row = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0] != 12 || $row[1] != 10) {
                if ($row[0] != 12 && $row[1] != 10) {
                    $canshu = 2;
                    $value = 100;
                } else {
                    $canshu = 1;
                    $value = 50;
                }
            }

        }
        if ($value == 0) {
            //A5频率偏移核查1
            $sqlsite = "SELECT siteName FROM siteLte WHERE cellName ='$cell'";
            $res = $pdo->query($sqlsite);
            $row1 = $res->fetch(PDO::FETCH_NUM);
            $site = $row1[0];
            $sql = "select count(*) as occurs from TempA5Threshold1Rsrp where EUtranCellTDD ='$site'";
            // var_dump($sql);
            $rs = $db->query($sql);
            $row = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0]) {
                $canshu = $row[0];
                $value = 50;
            }

        }
        if ($value == 0) {
            //A5频率偏移核查2
            $sqlsite = "SELECT siteName FROM siteLte WHERE cellName ='$cell'";
            $res = $pdo->query($sqlsite);
            $row1 = $res->fetch(PDO::FETCH_NUM);
            $site = $row1[0];
            $sql = "select count(*) as occurs from TempA5Threshold2Rsrp where EUtranCellTDD ='$site'";
            $rs = $db->query($sql);
            $row = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0]) {
                $canshu = $row[0];
                $value = 50;
            }
        }
        if ($value == 0) {
            //B2频率偏移核查1
            $sqlsite = "SELECT siteName FROM siteLte WHERE cellName ='$cell'";
            $res = $pdo->query($sqlsite);
            $row1 = $res->fetch(PDO::FETCH_NUM);
            $site = $row1[0];
            $sql = "select count(*) as occurs from TempB2Threshold1RsrpGeranOffset where meContext ='$site'";
            $rs = $db->query($sql);
            $row = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0]) {
                $canshu = $row[0];
                $value = 50;
            }
        }
        if ($value == 0) {
            //B2频率偏移核查2
            $sqlsite = "SELECT siteName FROM siteLte WHERE cellName ='$cell'";
            $res = $pdo->query($sqlsite);
            $row1 = $res->fetch(PDO::FETCH_NUM);
            $site = $row1[0];
            $sql = "select count(*) as occurs from TempB2Threshold2GeranOffset where meContext ='$site'";
            $rs = $db->query($sql);
            $row = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0]) {
                $canshu = $row[0];
                $value = 50;
            }
        }
        $result['参数'] = $canshu;
        $result['Polar-参数'] = $value;
        return $result;
    }

}