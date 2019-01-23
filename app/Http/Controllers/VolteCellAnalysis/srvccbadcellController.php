<?php

/**
* srvccbadcellController.php
*
* @category volteCellAnalysis
* @package  App\Http\Controllers\volteCellAnalysis
* @author   ericsson <genius@ericsson.com>
* @license  MIT License
* @link     https://laravel.com/docs/5.4/controllers
*/
namespace App\Http\Controllers\volteCellAnalysis;

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
 * srvcc坏小区处理
 * Class srvccbadcellController
 *
 * @category volteCellAnalysis
 * @package  App\Http\Controllers\volteCellAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class srvccbadcellController extends Controller
{   
    //小区列表查询
    public function templateQuery() 
    {
        $cityArrs = Input::get('city');
        $cityArr = array();
        $cityPY = new DataBaseConnection();
        foreach ($cityArrs as $citys) {
            $cityStr = $cityPY->getCityByCityChinese($citys)[0]->connName;
            array_push($cityArr, $cityStr);
        }
        $cityFilter = '(';
        for ($i = 0; $i < count($cityArr); $i++) {
            $cityFilter .= "city='" . $cityArr[$i] . "' or ";
        }
        $cityFilter = substr($cityFilter, 0, strlen($cityFilter) - 3);
        $cityFilter .= ")";//城市条件
        // var_dump($cityFilter);return;
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB("autokpi", "AutoKPI");
        $sql = "SELECT * from voltebadcell_cell_day where cell in (
SELECT t.cell FROM 
(SELECT
    count(cell) AS num,
    cell
FROM
    voltebadcell_cell_day
WHERE
    day_id >= DATE_SUB(DATE_FORMAT(NOW(),'%Y-%m-%d'),INTERVAL 3 day) 
AND day_id <= DATE_SUB(DATE_FORMAT(NOW(),'%Y-%m-%d'),INTERVAL 1 day) 
AND `eSRVCC切换成功率` < 95
AND `ESRVCC失败总次数` > 3
AND $cityFilter
GROUP BY
    cell)t WHERE t.num>=2) and day_id >= DATE_SUB(DATE_FORMAT(NOW(),'%Y-%m-%d'),INTERVAL 3 day) 
AND day_id <= DATE_SUB(DATE_FORMAT(NOW(),'%Y-%m-%d'),INTERVAL 1 day) AND `eSRVCC切换成功率` < 95
AND `ESRVCC失败总次数` > 3";
// var_dump($sql);return;
        $res = $db->query($sql);
        $result = array();
        $items = array();
        $content = "day_id,city,cell,subNetwork,eSRVCC切换成功率,ESRVCC失败总次数";
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $row['eSRVCC切换成功率'] = floatval($row['eSRVCC切换成功率']);
            $row["ESRVCC失败总次数"] = floatval($row["ESRVCC失败总次数"]);
            $row["day_id"] = $row["day_id"];
            $row["cell"] = $row["cell"];
            $row['city'] = $row['city'];
            $row["subNetwork"] = $row["subNetwork"];
            array_push($items, $row);
        }
        $table = "SRVCC差小区";
        $result['records'] = count($items);
        $result["rows"] = $items;
        $result["content"] = $content;
        $filename = "common/files/" . $table . date('YmdHis') . ".csv";
        $result['filename'] = $filename;
        $this->resultToCSV2($result, $filename);
        // var_dump($result);return;
        // var_dump($result["rows"]);return;
        echo json_encode($result);

    }

    //获得城市列表
    public function getAllCity()
    {
        $cityClass = new DataBaseConnection();
        return $cityClass->getCityOptions();
    }

    //导出坏小区列表CSV文件
    protected function resultToCSV2($result, $filename)
    { 
        $csvContent = mb_convert_encoding($result['content'] . "\n", 'gb2312', 'utf-8');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result["rows"] as $row) {
            fputcsv($fp, $row);
        }
        // var_dump($fp);return;
        fclose($fp);
    } 


     /**
     * 获得ecgi
     *
     * @return string ecgi
     */
    public function getEcgi($cell) {
        $dsn = new DataBaseConnection();
        $dbc = $dsn->getDB("mongs", "mongs");
        // $db = $this->mongsdb;
        $sqlsite = "select ecgi from siteLte where cellName='$cell'";
        $ressite =$dbc->query($sqlsite);
        $rows = $ressite->fetch(PDO::FETCH_NUM);
        $ecgi = $rows[0];
        return $ecgi;
    }


     /**
     * 获得基站名称
     *
     * @return string 基站名称
     */
    public function getSitename($cell) 
    {
        $dsn = new DataBaseConnection();
        $dbc = $dsn->getDB("mongs", "mongs");
        $sqlsite = "select siteName from siteLte where cellName='$cell'";
        $ressite =$dbc->query($sqlsite);
        $rows = $ressite->fetch(PDO::FETCH_NUM);
        $site = $rows[0];
        return $site;
    }

    /**
     * 获得告警信息
     *
     * @return array 告警相关性
     */
    public function getcurrentAlarmNum() 
    {
        $date = date("Y-m-d");
        $cell = Input::get("cell");
        $city = Input::get("city");
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('alarm', 'Alarm');
        // $db = $this->alarmdb;
        $site = $this->getSitename($cell);
        $sql = "SELECT COUNT(*) AS num FROM FMA_alarm_list WHERE DATE_FORMAT(Event_time, '%Y-%m-%d') >= '$date' AND meContext ='$site' AND city = '$city'";
        // var_dump($sql);return;
        $result = array();
        $res = $db->query($sql);
        $polarnum = 0;
        if ($res) {
            $row = $res->fetch(PDO::FETCH_ASSOC);
            // var_dump($row);return;
            $alarmnum = $row["num"];
            if ($row["num"] > 0) {
                $polarnum = 50;
            }
        } else {
            $alarmnum = 0;
        }
        // $result = array();
        // var_dump($alarmnum);return;
        $result['当前告警数量'] = $alarmnum;
        $result['Polar-当前告警'] = $polarnum;
        // var_dump($result);return;
        return $result;
    }

    /**
     * 获得重叠覆盖信息
     *
     * @return array 重叠覆盖相关数据
     */
    public function overlapcover() 
    {
        // $cell = input::get('cell');
        $date_from = date("Y-m-d", strtotime("-1 day"));
        // $city = input::get('city');
        $cell = Input::get("cell");
        $city = Input::get("city");
        $sql = "SELECT cellName,ecgi FROM siteLte WHERE cellName = '$cell'";
        $dbc = new DataBaseConnection();
        $pdo = $dbc->getDB('mongs', 'mongs');
        // $pdo = $this->mongsdb;
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
        $overlapcovernum = 0;
        $polarValue = 0;
        $ecgi = "";
        if ($res) {
            $row = $res->fetch();
            $ecgi = $row['ecgi'];
            $db1 = new PDO("mysql:host=10.40.57.134;dbname=$database;port=8066", "mr", "mr");
            $sql = "SELECT ecgi,rate AS num FROM mroOverCoverage_day WHERE  dateId = '$date_from' AND ecgi = '$ecgi'";
            $res = $db1->query($sql, PDO::FETCH_ASSOC);
            if ($res) {
                $row = $res->fetchall();
                foreach ($row as $key => $value) {
                    $overlapcovernum = $value['num'];
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
        $temp = round($overlapcovernum, 2);
        $result['重叠覆盖度'] = $temp*100;
        $result['Polar-重叠覆盖'] = $polarValue;
        return $result;
    }

    /**
     * 获得下行质差信息
     *
     * @return array 下行质差相关数据
     */
    public function getRsrq()
    {
        $cell = Input::get("cell");
        $sql = "SELECT * FROM voltereportcell_cell_day WHERE cell='$cell'";
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB("autokpi", "AutoKPI");
        // $db = $this->autokpidb;
        $res = $db->query($sql);
        $result = array();
        if ($res) {
            $row = $res->fetch(PDO::FETCH_ASSOC);
            $result["RSRQ<-15.5的比例"] = $row["RSRQ<-15.5的比例"];
        }
        return $result;
    }


    /**
     * 获得2G和4G邻区数量
     *
     * @return array 邻区数量 
     */
    public function neightcell() 
    {
        $date = date("Y-m-d", strtotime("-1 day"));
        $city = Input::get("city");
        $cell = Input::get("cell");
        $table = Input::get("table");
        $flag = Input::get("flag");
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
        $ecgi = $this->getEcgi($cell);
        $db = "";
        try {
            $db = new PDO("mysql:host=10.40.57.134;dbname=$database;port=8066", "mr", "mr");
        } catch (Exception $e) {
            return;
        }
        $sql = "SELECT count(*) as num from $table where isdefined=0 and ecgi = '$ecgi' AND distance<0.8 and dateId = '$date'";
        // print_r($sql);
        $res = $db->query($sql);
        $num = 0;
        if ($res) {
            $row = $res->fetch(PDO::FETCH_ASSOC);
            // print_r($row);
            $num = $row["num"];
        }
        $result = array();
        $result["邻区数量"] = $num;
        return $result;
    }

     /**
     * 获得多个参数项数据
     *
     * @return array 参数项数据
     */
    public function parameter() 
    {
        $cell = $this->b;
        $city = $this->a;
        $date = date("Y-m-d", strtotime("-1 day"));
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
        try {
            $dbmr = new PDO("mysql:host=10.40.57.134;dbname=$database;port=8066", "mr", "mr");
        } catch (Exception $e) {
            return;
        }
        $dbc = new DataBaseConnection();
        $pdo = $dbc->getDB('mongs', 'mongs');
        $dbname = "kget".date("ymd");
        $sql = "SELECT COUNT(*) AS num FROM task WHERE taskName='$dbname';";
        $row = $pdo->query($sql, PDO::FETCH_ASSOC)->fetchall();
        if ($row[0]['num'] == 0) {
            $dbname = "kget".date("ymd",strtotime("-1 day"));
        }
        $db = "";
        try {
            $db = new PDO("mysql:host=10.39.148.186;dbname=$dbname", "root", "mongs");
        } catch (Exception $e) {
            return;
        }
        $site = $this->getSitename($cell);
        $value = 0;
        $canshu = 0;
        if ($value == 0) {
            $sql = "select count(*) from TempEUtranCellRelationFewNeighborCell where EutranCellTDD ='$cell'";
            $rs   = $db->query($sql);
            // var_dump($rs);return;
            if ($rs) {
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if ($row[0] > 0) {
                    $canshu=$row[0];
                    $value = 50;
                }
            }
        }//邻区过少
        if ($value == 0) {
            $sql = "select count(*) from TempParameter2GKgetCompare WHERE meContext ='$site'";
            $rs   = $db->query($sql1);
            if ($rs) {
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if ($row[0] > 0) {
                    $canshu=$row[0];
                    $value = 50;
                }
            }
        } //外部2G小区定义错误
        if ($value == 0) {
            $sql = "select count(*) from OptionalFeatureLicense WHERE OptionalFeatureLicenseId ='EnhancedPdcchLa' and featureState not like '%1 (ACTIVATED)%' and meContext = '$site'";
            $rs = $db->query($sql);
            if ($rs) {
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if (count($row)) {
                    $canshu = $row[0];
                    $value = 50;
                }
            }
        }//增强相关功能未开
        if ($value == 0) {
            $sql = "select count(*) from OptionalFeatureLicense WHERE keyId ='CXC4011482' and featureState not like '%1 (ACTIVATED)%' and meContext = '$site'";
            $rs = $db->query($sql);
            if ($rs) {
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if (count($row)) {
                    $canshu = $row[0];
                    $value = 50;
                }
            }
        }//增强相关功能未开
        if ($value == 0) {
            $sql = "SELECT
    count(*)
FROM
    (
        SELECT
            subNetwork,
            meContext,
            pdcchCfiMode
        FROM
            EUtranCellFDD
        WHERE
            meContext = '$site'
    ) AS TABLE1
INNER JOIN(
    SELECT
        subNetwork,
        meContext,
        pdcchCfiMode
    FROM
        EUtranCellFDD
    WHERE
        meContext = '$site'
) AS TABLE2 ";
            $rs = $db->query($sql);
            if ($rs) {
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if (count($row)) {
                    $canshu = $row[0];
                    $value = 50;
                }
            }
        }//PDCCH符号数设置低于2
        if ($value == 0) {
            $sql = "select count(*) FROM (select a1a2SearchThresholdRsrp as kpi1,subNetwork,meContext FROM ReportConfigSearch WHERE meContext='LE6H566') AS TABLE0 LEFT JOIN(select measReportConfigParams_a2ThresholdRsrpPrimOffset as kpi0,subNetwork,meContext FROM QciProfilePredefined WHERE qciProfilePredefinedId='qci1' and meContext='LE6H566')AS TABLE1 ON TABLE0.subNetwork = TABLE1.subNetwork
AND TABLE0.meContext = TABLE1.meContext
WHERE kpi0+kpi1<-110";
            $rs = $db->query($sql);
            if ($rs) {
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if (count($row)) {
                    $canshu = $row[0];
                    $value = 50;
                }
            }
        }//A2门限
        if ($value == 0) {
            $sql = "select count(*) FROM (select b2Threshold1Rsrp as kpi1,subNetwork,meContext FROM ReportConfigB2Geran WHERE meContext='LE6H566') AS TABLE0 LEFT JOIN(select measReportConfigParams_b2Threshold1RsrpGeranOffset as kpi0,subNetwork,meContext FROM QciProfilePredefined WHERE qciProfilePredefinedId='qci1' and meContext='LE6H566')AS TABLE1 ON TABLE0.subNetwork = TABLE1.subNetwork
AND TABLE0.meContext = TABLE1.meContext
WHERE kpi0+kpi1<-116";
            $sql1 = "select count(*) FROM (select b2Threshold2Geran as kpi1,subNetwork,meContext FROM ReportConfigB2Geran WHERE meContext='LE6H566') AS TABLE0 LEFT JOIN(select measReportConfigParams_b2Threshold2GeranOffset as kpi0,subNetwork,meContext FROM QciProfilePredefined WHERE qciProfilePredefinedId='qci1' and meContext='LE6H566')AS TABLE1 ON TABLE0.subNetwork = TABLE1.subNetwork
AND TABLE0.meContext = TABLE1.meContext
WHERE kpi0+kpi1<-95";
            $rs = $db->query($sql);
            $rs1 = $db->query($sql1);
            if ($rs) {
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if (count($row)) {
                    $canshu = $row[0];
                    $value = 50;
                }
            }
            if ($rs1) {
                $row  = $rs1->fetch(PDO::FETCH_NUM);
                if (count($row)) {
                    $canshu = $row[0];
                    $value = 50;
                }
            }
        }//B2门限
        if ($value == 0) {
            $sql = "select count(*) from DrxProfile WHERE DrxProfileId=1 and meContext='LE6H566' and (onDurationTimer!=drxInactivityTimer or drxInactivityTimer!=drxRetransmissionTimer)";
            $rs = $db->query($sql);
            if ($rs) {
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if (count($row)) {
                    $canshu = $row[0];
                    $value = 50;
                }
            }
        }//Timer设置和baseline不一致
        $result = array();
        $result['参数'] = $canshu;
        $result['Polar-参数'] = $value;
        return $result; 
    }


     /**
     * 获得多个参数项诊断报告
     *
     * @return array 参数项诊断报告 
     */
     public function getBaselineCheckData()
     {
        $cell = input::get('cell');
        $city = input::get('city');
        $date = Input::get("date");
        $date1= new DateTime();
        $date1->sub(new DateInterval('P1D'));
        // $yesDate = $date->format('ymd');
        // $dbname = 'kget' . $yesDate;//获取昨天的kget数据库
        //判断是否用今天的数据
        $dbname = "kget".date("ymd");
        $dbc = new DataBaseConnection();
        $pdo = $dbc->getDB('mongs', $dbname);
        $sql = "SELECT COUNT(*) AS num FROM mongs.task WHERE taskName='$dbname';";
        $row = $pdo->query($sql, PDO::FETCH_ASSOC)->fetchall();
        if ($row[0]['num'] == 0) {
            $dbname = "kget".date("ymd", strtotime("-1 day"));
        }
        $site = $this->getSitename($cell);
        $db = $dbc->getDB('mongs', $dbname);
        $result = array();
        $item = array();
        $sql = "select * from TempEUtranCellRelationFewNeighborCell where EutranCellTDD ='$cell'";
        $rs = $db->query($sql);
        if ($rs) {
            $rows = $rs->fetchall(PDO::FETCH_ASSOC);
            $item['record'] = count($rows);
            if (count($rows) > 0) {
                $row = $rows[0];
                $item['content'] = implode(",", array_keys($row));
                foreach ($rows as $row) {
                    $item['rows'][] = $row;
                }
            }
        }//邻区过少
        array_push($result, $item);
        $item = array();
        $sql = "select mo,subNetwork,meContext,parameter,KGET_Value,CDD_Value from TempParameter2GKgetCompare WHERE meContext ='$site'";
        $rs = $db->query($sql);
        if ($rs) {
            $rows = $rs->fetchall(PDO::FETCH_ASSOC);
            $item['record'] = count($rows);
            if (count($rows) > 0) {
                $row = $rows[0];
                $item['content'] = implode(",", array_keys($row));
                foreach ($rows as $row) {
                    $item['rows'][] = $row;
                }
            }
        }//外部2G小区定义错误
        array_push($result, $item);
        $item = array();
        $sql = "select TABLE0.subNetwork as subNetwork,TABLE0.meContext as meContext,TABLE0.kpi1 as a1a2SearchThresholdRsrp,TABLE1.kpi0 as measReportConfigParams_a2ThresholdRsrpPrimOffset FROM (select a1a2SearchThresholdRsrp as kpi1,subNetwork,meContext FROM ReportConfigSearch WHERE meContext='$site') AS TABLE0 LEFT JOIN(select measReportConfigParams_a2ThresholdRsrpPrimOffset as kpi0,subNetwork,meContext FROM QciProfilePredefined WHERE qciProfilePredefinedId='qci1' and meContext='$site')AS TABLE1 ON TABLE0.subNetwork = TABLE1.subNetwork
AND TABLE0.meContext = TABLE1.meContext
WHERE kpi0+kpi1<-110";
        $rs = $db->query($sql);
        if ($rs) {
            $rows = $rs->fetchall(PDO::FETCH_ASSOC);
            $item['record'] = count($rows);
            if (count($rows) > 0) {
                $row = $rows[0];
                $item['content'] = implode(",", array_keys($row));
                foreach ($rows as $row) {
                    $item['rows'][] = $row;
                }
            }
        }//A2门限
        array_push($result, $item);
        $item = array();
        $sql = "select TABLE0.subNetwork as subNetwork,TABLE0.meContext as meContext,TABLE0.kpi1 as b2Threshold1Rsrp,TABLE1.kpi0 as measReportConfigParams_b2Threshold1RsrpGeranOffset FROM (select b2Threshold1Rsrp as kpi1,subNetwork,meContext FROM ReportConfigB2Geran WHERE meContext='$site') AS TABLE0 LEFT JOIN(select measReportConfigParams_b2Threshold1RsrpGeranOffset as kpi0,subNetwork,meContext FROM QciProfilePredefined WHERE qciProfilePredefinedId='qci1' and meContext='$site')AS TABLE1 ON TABLE0.subNetwork = TABLE1.subNetwork
AND TABLE0.meContext = TABLE1.meContext
WHERE kpi0+kpi1<-116";
        $rs = $db->query($sql);
        if ($rs) {
            $rows = $rs->fetchall(PDO::FETCH_ASSOC);
            $item['record'] = count($rows);
            if (count($rows) > 0) {
                $row = $rows[0];
                $item['content'] = implode(",", array_keys($row));
                foreach ($rows as $row) {
                    $item['rows'][] = $row;
                }
            }
        }//B2门限Rsrp
        array_push($result, $item);
        $item = array();
        $sql = "select TABLE0.subNetwork as subNetwork,TABLE0.meContext as meContext,TABLE0.kpi1 as b2Threshold2Geran,TABLE1.kpi0 as measReportConfigParams_b2Threshold2GeranOffset FROM (select b2Threshold2Geran as kpi1,subNetwork,meContext FROM ReportConfigB2Geran WHERE meContext='$site') AS TABLE0 LEFT JOIN(select measReportConfigParams_b2Threshold2GeranOffset as kpi0,subNetwork,meContext FROM QciProfilePredefined WHERE qciProfilePredefinedId='qci1' and meContext='$site')AS TABLE1 ON TABLE0.subNetwork = TABLE1.subNetwork
AND TABLE0.meContext = TABLE1.meContext
WHERE kpi0+kpi1<-95";
        $rs = $db->query($sql);
        if ($rs) {
            $rows = $rs->fetchall(PDO::FETCH_ASSOC);
            $item['record'] = count($rows);
            if (count($rows) > 0) {
                $row = $rows[0];
                $item['content'] = implode(",", array_keys($row));
                foreach ($rows as $row) {
                    $item['rows'][] = $row;
                }
            }
        }//B2门限Geran
        array_push($result, $item);
        $item = array();
        $sql = "SELECT
    *
FROM
    (
        SELECT
            subNetwork,
            meContext,
            pdcchCfiMode
        FROM
            EUtranCellFDD
        WHERE
            meContext = '$site'
    ) AS TABLE1
INNER JOIN(
    SELECT
        subNetwork,
        meContext,
        pdcchCfiMode
    FROM
        EUtranCellFDD
    WHERE
        meContext = '$site'
) AS TABLE2 ";
        $rs   = $db->query($sql);
        if ($rs) {
            $rows = $rs->fetchall(PDO::FETCH_ASSOC);
            $item['record'] = count($rows);
            if (count($rows) > 0) {
                $row = $rows[0];
                $item['content'] = implode(",", array_keys($row));
                foreach ($rows as $row) {
                    $item['rows'][] = $row;
                }
            }
        }//PDCCH符号数设置低于2
        array_push($result, $item);
        $item = array();
        $sql = "select subNetwork,meContext,OptionalFeatureLicenseId,featureState from OptionalFeatureLicense WHERE OptionalFeatureLicenseId ='EnhancedPdcchLa' and featureState not like '%1 (ACTIVATED)%' and meContext = '$site'";
        $rs   = $db->query($sql);
        if ($rs) {
            $rows = $rs->fetchall(PDO::FETCH_ASSOC);
            $item['record'] = count($rows);
            if (count($rows) > 0) {
                $row = $rows[0];
                $item['content'] = implode(",", array_keys($row));
                foreach ($rows as $row) {
                    $item['rows'][] = $row;
                }
            }
        }//增强相关功能未开DU基站
        array_push($result, $item);
        $item = array();
        $sql = "select subNetwork,meContext,keyId,featureState from OptionalFeatureLicense WHERE keyId ='CXC4011482' and featureState not like '%1 (ACTIVATED)%' and meContext = '$site'";
        $rs   = $db->query($sql);
        if ($rs) {
            $rows = $rs->fetchall(PDO::FETCH_ASSOC);
            $item['record'] = count($rows);
            if (count($rows) > 0) {
                $row = $rows[0];
                $item['content'] = implode(",", array_keys($row));
                foreach ($rows as $row) {
                    $item['rows'][] = $row;
                }
            }
        }//增强相关功能未开Baseband-based 基站
        array_push($result, $item);
        $item = array();
        $sql = "select subNetwork,meContext,onDurationTimer,drxInactivityTimer,drxRetransmissionTimer from DrxProfile WHERE DrxProfileId=1 and meContext='$site' and (onDurationTimer!=drxInactivityTimer or drxInactivityTimer!=drxRetransmissionTimer)";
        $rs = $db->query($sql);
        if ($rs) {
            $rows = $rs->fetchall(PDO::FETCH_ASSOC);
            $item['record'] = count($rows);
            if (count($rows) > 0) {
                $row = $rows[0];
                $item['content'] = implode(",", array_keys($row));
                foreach ($rows as $row) {
                    $item['rows'][] = $row;
                }
            }
        }//Timer设置和baseline不一致
        array_push($result, $item);
        echo json_encode($result);
    }

     public function storage() 
    {
        $type     = input::get("type");
        $city = input::get('city');
        $gzFile   = explode(";;", input::get("gzFiles"));
        $dbc = new DataBaseConnection();
        $conn = $dbc->getCtrConn($city);
        $remoteIp = $conn['strServer'];
        $fileDir = $conn['fileDir'];
        if ($remoteIp == "10.197.132.33") {
            $city = "changzhou";
            $n    = 1;
        } else if ($remoteIp == "10.40.61.186") {
            $city = "wuxi";
            $n    = 1;
        } else if ($remoteIp == "10.40.51.185") {
            $city = "nantong";
            $n    = 2;
        }
        $rows = TraceServerInfo::where("type", $type)->where("city", $city)->first()->toArray();
        $fileDir = $rows['fileDir'];
        $ftpUserName = $rows['ftpUserName'];
        $ftpPassword = $rows['ftpPassword'];
        Config::set("filesystems.disks.ftp.host", $remoteIp);
        Config::set("filesystems.disks.ftp.username", $ftpUserName);
        Config::set("filesystems.disks.ftp.password", $ftpPassword);
        $fileName = $type."_".date("YmdHis")."_".md5(time());
        $user = Auth::user()->user;
        date_default_timezone_set("PRC");
        $dirName    = $type."_".$city."_".time();
        $type       = strtolower($type);
        $new_folder = "/data/trace/".$type."/".$user."/".$dirName;
        Config::set("filesystems.disks.commFile.root", $new_folder);
        mkdir($new_folder, 0777, true);
        chmod($new_folder, 0777);
        foreach ($gzFile as $file) {
            $folderName = explode(".", explode("/", $file)[$n])[0];
            $hour       = substr(explode(".", explode("/", $file)[$n])[1], 0, 2);
            $folderName = substr($folderName, 1);
            $folderName = $fileDir."/".$folderName.$hour.$file;
            Storage::disk('commFile')->put($file, Storage::disk('ftp')->get(str_replace("/data/trace/", "", $folderName)));
            // $scp        = "sudo scp -r root@".$remoteIp.":".$folderName." ".$new_folder;
            // exec($scp);
        }
        echo $new_folder;
    }


    public function ctrTreeItems() 
    { 
        $filesName = [];
        $erbsArr = [];
        $type = Input::get("type");
        $filename = input::get('point');
        // print_r($type);
        array_push($filesName, $filename);
        $city = Input::get('city');
        $cell = Input::get('cell');
        $kpi0  = Input::get('kpi0');
        $kpi1 = Input::get('kpi1');
        $rows = SiteLte::where('cellName', $cell)->get();
        $erab = '';
        foreach ($rows as $row) {
            $erab = $row->siteName;
        }
        array_push($erbsArr, $erab);
        $rows =TraceServerInfo::where("type", $type)->where("city", $city)->get()->toArray();
        // var_dump($rows);
        $ftpdir = $rows[0]['fileDir'];
        $ftpdir = explode("/", $ftpdir);
        $ftpdir = end($ftpdir);
        $remoteIp = $rows[0]["ipAddress"];
        $fileDir = $rows[0]['fileDir'];
        $ftpUserName = $rows[0]['ftpUserName'];
        $ftpPassword = $rows[0]['ftpPassword'];
        Config::set("filesystems.disks.ftp.host", $remoteIp);
        Config::set("filesystems.disks.ftp.username", $ftpUserName);
        Config::set("filesystems.disks.ftp.password", $ftpPassword);

        $idNum         = 1;
        $allCtr        = array();
        $ctrTime       = array();
        $childrengz    = array();
        $allChildrengz = array();
        $succFilesName = array();
        $file = Storage::disk('ftp')->directories($type."/".$ftpdir."/");
        // var_dump($file);return;
        // var_dump($filesName);
        foreach ($filesName as $fileName) {
            foreach ($file as $value) {
                if ($fileName != explode("/", $value)[count(explode("/", $value))-1]) {
                    continue;
                } else {
                    array_push($succFilesName, $fileName);
                    $ctrTime['id']      = $idNum;
                    $ctrTime['kpiName'] = $value;
                    $ctrTime['eSRVCC切换成功率'] = $kpi0;
                    $ctrTime['ESRVCC失败总次数'] = $kpi1; 
                    $idNum++;
                }
                array_push($allCtr, $ctrTime);
            }
        }
        // var_dump($succFilesName);
        $idNum = 1;
        foreach ($succFilesName as $succFileName) {
            $childrenId = 1;
            $dirsgz     = $type."/".$ftpdir."/".$succFileName;
            // var_dump($dirsgz);
            $filesgz    = $this->getFile($dirsgz);
            // var_dump($filesgz);
            foreach ($filesgz as $filegz) {
                foreach ($erbsArr as $erb) {
                    $filePos = strpos($filegz, $erb);
                    // var_dump($filePos);
                    if ($filePos == false) {
                        continue;
                    } else {
                        $allChildrengz['id']      = $idNum.$childrenId;
                        $allChildrengz['kpiName'] = str_replace($dirsgz, '', $filegz);
                        $allChildrengz['size'] = (round(Storage::disk("ftp")->size($filegz)/1024, 2))." KB";
                        $childrenId++;
                        array_push($childrengz, $allChildrengz);
                    }
                }
            }

            $num = ($idNum - 1);
            $allCtr[$num]['children'] = $childrengz;
            $childrengz = array();

            $idNum++;
        }
        echo json_encode($allCtr);
    }

    /**
     * 获得目录下GZ文件列表
     *
     * @param string $dir 目录名
     *
     * @return array
     */
    public function getFile($dir)
    {
        $fileArr = array();
        $file     = storage::disk("ftp")->files($dir);
        if ($file) {
            krsort($file);
            foreach ($file as $value) {
                if ($value != "." && $value != "..") {
                    if (!strpos($value, ".gz")) {
                        $fileArr = array_merge($fileArr, $this->getFile($value));
                    } else {
                        array_push($fileArr, $value);
                    }
                }
            }
        }

        return $fileArr;
    }//end getFile()

}