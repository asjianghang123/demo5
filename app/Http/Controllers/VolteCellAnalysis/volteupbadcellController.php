<?php

/**
* volteupbadcellController.php
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
 * volte坏小区处理
 * Class volteupbadcellController
 *
 * @category volteCellAnalysis
 * @package  App\Http\Controllers\volteCellAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class volteupbadcellController extends Controller
{   
    // use DataBaseConnection_test;
    public $dbc;//DataBaseConnection对象
    public $a;//js城市信息
    public $b;//js小区信息
    public $mongsdb;//mongs数据库
    public $autokpidb;//autokpi数据库
    public $alarmdb;//alarm数据库

    /**
     * 构造函数
     *
     * @return void
     */
    public function __construct() {
        $this->dbc = new DataBaseConnection();
        $this->a = Input::get("city");
        $this->b = Input::get("cell");
        $this->mongsdb = $this->dbc->getDB("mongs", "mongs");
        $this->autokpidb = $this->dbc->getDB("autokpi", "AutoKPI");
        $this->alarmdb = $this->dbc->getDB("alarm", "Alarm");
    }

    /**
     * 查询坏小区列表(分页)
     *
     * @return string JSON格式坏小区列表
     */
    public function templateQuery() 
    {
        $cityArrs = $this->a;;
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
        // $dbc = new DataBaseConnection();
        // $db = $dbc->getDB("autokpi", "AutoKPI");
        $db = $this->autokpidb;
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
AND `VOLTE上行丢包率` > 5
AND `VOLTE上行总包数` > 1000
AND $cityFilter
GROUP BY
    cell)t WHERE t.num>=2) and day_id >= DATE_SUB(DATE_FORMAT(NOW(),'%Y-%m-%d'),INTERVAL 3 day) 
AND day_id <= DATE_SUB(DATE_FORMAT(NOW(),'%Y-%m-%d'),INTERVAL 1 day) AND `VOLTE上行丢包率` > 5
AND `VOLTE上行总包数` > 1000";
// var_dump($sql);return;
        $res = $db->query($sql);
        $result = array();
        $items = array();
        $content = "day_id,city,cell,subNetwork,VOLTE上行丢包率,VOLTE上行总包数";
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $row['VOLTE上行丢包率'] = floatval($row['VOLTE上行丢包率']);
            $row["VOLTE上行总包数"] = floatval($row["VOLTE上行总包数"]);
            $row["day_id"] = $row["day_id"];
            $row["cell"] = $row["cell"];
            $row['city'] = $row['city'];
            $row["subNetwork"] = $row["subNetwork"];
            array_push($items, $row);
        }
        $table = "VOLTE上行丢包差小区";
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

    /**
     * 获得城市列表
     *
     * @return string 城市列表
     */
    public function getAllCity()
    {
        $cityClass = $this->dbc;
        return $cityClass->getCityOptions();
    }

    /**
     * 导出坏小区列表CSV文件
     *
     * @param array  $result   坏小区列表
     * @param string $filename CSV文件名
     *
     * @return void
     */
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
     * 获得基站名称
     *
     * @return string 基站名称
     */
    public function getSitename($cell) {
        // $dsn = new DataBaseConnection();
        // $dbc = $dsn->getDB("mongs", "mongs");
        $db = $this->mongsdb;
        $sqlsite = "select siteName from siteLte where cellName='$cell'";
        $ressite =$db->query($sqlsite);
        $rows = $ressite->fetch(PDO::FETCH_NUM);
        $site = $rows[0];
        return $site;
    }

    /**
     * 获得ecgi
     *
     * @return string ecgi
     */
    public function getEcgi($cell) {
        // $dsn = new DataBaseConnection();
        // $dbc = $dsn->getDB("mongs", "mongs");
        $db = $this->mongsdb;
        $sqlsite = "select ecgi from siteLte where cellName='$cell'";
        $ressite =$db->query($sqlsite);
        $rows = $ressite->fetch(PDO::FETCH_NUM);
        $ecgi = $rows[0];
        return $ecgi;
    }

    /**
     * 获得告警信息
     *
     * @return array 告警相关性
     */
    public function getcurrentAlarmNum() 
    {
        $date = date("Y-m-d");
        $cell = $this->b;
        $city = $this->a;
        // $dsn = new DataBaseConnection();
        // $db = $dsn->getDB('alarm', 'Alarm');
        $db = $this->alarmdb;
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
     * 获得部分的诊断报告数据项
     *
     * @return array 部分诊断报告数据项
     */
    public function getReportData() {
        // $cell = Input::get("cell");
        $cell = $this->b;
        $sql = "SELECT * FROM voltereportcell_cell_day WHERE cell='$cell'";
        // $dsn = new DataBaseConnection();
        // $db = $this->dbc->getDB("autokpi", "AutoKPI");
        $db = $this->autokpidb;
        $res = $db->query($sql);
        $result = array();
        if ($res) {
            $row = $res->fetch(PDO::FETCH_ASSOC);
            $result["RSRQ<-15.5的比例"] = $row["RSRQ<-15.5的比例"];
            $result["调度资源"] = $row["调度资源"];
            $result["PUCCHSINR低于-15的的比例"] = $row["PUCCHSINR低于-15的的比例"];
            $result["PUSCH-SINR低于-5dB的比例"] = $row["PUSCH-SINR低于-5dB的比例"];
            $result["RSRP<-116的比例"] = $row["RSRP<-116的比例"];
            $result["QCI1的用户平面下行平均时延(ms)"] = $row["QCI1的用户平面下行平均时延(ms)"];
        }
        return $result;

    }

    /**
     * 获得平均PRB数据
     *
     * @return array 平均PRB信息
     */
    public function getavgPrb()
    {
        $cell = $this->b;
        $city = $this->a;
        // $dsn = new DataBaseConnection();
        $sql = "select * from voltePRB_cell_day WHERE cell='$cell' AND city = '$city'";
        // $db = $dsn->getDB("autokpi", "AutoKPI");
        $db = $this->autokpidb;
        $res = $db->query($sql);
        $avgprb = 0;
        $allPRB = 0;
        $result = array();
        if ($res) {
            $row = $res->fetch(PDO::FETCH_NUM);
            $j = 0;
            // var_dump($row);return;
            for ($i=4; $i<count($row); $i++) {
                if ($row[$i] == null) {
                    continue;
                }
                
                $allPRB = $allPRB + $row[$i];
                $j++;
            }
            if ($j == 0) {
                $avgprb = 0;
            } else {
                $avgprb = round($allPRB / $j, 2);
            }

        }
        $result["上行干扰"] = $avgprb;
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
        $cell = $this->b;
        $city = $this->a;
        $sql = "SELECT cellName,ecgi FROM siteLte WHERE cellName = '$cell'";
        // $dbc = new DataBaseConnection();
        // $pdo = $dbc->getDB('mongs', 'mongs');
        $pdo = $this->mongsdb;
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
        // $dbc = new DataBaseConnection();
        // $pdo = $dbc->getDB('mongs', 'mongs');
        $pdo = $this->mongsdb;
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
            $sql = "SELECT pdcpSNLength,rlcSNLength FROM QciProfilePredefined WHERE qciProfilePredefinedId='qci1' AND meContext ='$site'";
            $rs = $db->query($sql);
            $row = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0] != $row[1]) {
                $canshu = 2;
                $value = 50;
            }
        }// SN length参数设置错误
        if ($value == 0) {
            $sql = "select count(*) from TempEUtranCellRelationFewNeighborCell where EutranCellTDD = '$cell' and remark1 = 'co-SiteNeighborRelationMiss'";
            $rs   = $db->query($sql);
            if ($rs) {
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if ($row[0] > 0) {
                    $canshu=$row[0];
                    $value = 50;
                }
            }
        }//没有定义同频站点 
        if ($value == 0) {
            $sql = "select count(*) from EUtranCellRelation where meContext = '$site'";
            $rs   = $db->query($sql);
            if ($rs) {
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if ($row[0] > 0) {
                    $canshu=$row[0];
                    $value = 100;
                }
            }
        }//未定邻区
        if ($value == 0) {
            $sql = "select count(*) from GeranCellRelation where meContext = '$site'";
            $rs   = $db->query($sql);
            if ($rs) {
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if ($row[0] > 0) {
                    $canshu=$row[0];
                    $value = 100;
                }
            }
        }//没有定义2G邻区 
        if ($value == 0) {
            $sql = "select count(*) from EUtranFreqRelation where EutranCellTDD = '$cell' AND EUtranFreqRelation is NULL";
            $rs   = $db->query($sql);
            if ($rs) {
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if ($row[0] > 0) {
                    $canshu=$row[0];
                    $value = 100;
                }
            }
        }//没有定义本小区freqrel
        if ($value == 0) {
            //一阶冲突
            $sql1 = "select count(*) from TempEUtranCellRelationNeighOfPci where EutranCellTDD = '$cell'";
            $rs   = $db->query($sql1);
            if ($rs) {       
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if ($row[0] > 0) {
                    $canshu=$row[0];
                    $value = 100;
                } else {
                    //二阶冲突
                    $sql2 = "select count(*) from TempEUtranCellRelationNeighOfNeighPci where EutranCellTDD = '$cell'";
                    $rs   = $db->query($sql2);
                    $row  = $rs->fetch(PDO::FETCH_NUM);
                    if ($row[0] > 0) {
                        $canshu=$row[0];
                        $value = 100;
                    }
                }
            }
        }//MRE邻区排名前30的邻区有PCI一二阶冲突
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
            $sql = "select count(*) from TempExternalNeigh4G WHERE meContext ='$site'";  
            $rs   = $db->query($sql);
            if ($rs) {
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if ($row[0] > 0) {
                    $canshu=$row[0];
                    $value = 50;
                }
            }  
        }//外部4G小区定义错误
        if ($value == 0) {
            $sql = "SELECT count(*) as num FROM mroPciMod3_day WHERE mr_LteScPciMod3 = mr_LteNcPciMod3 and EutrancellTddName = '$cell' AND dateId = '$date'";
            $rs = $dbmr->query($sql);
            if ($rs) {
                $row = $rs->fetch(PDO::FETCH_NUM);
                if ($row[0] > 0) {
                    $canshu = $row[0];
                    $value = 50;
                }
            }
        }//MRE排名前20名的小区mod3重叠覆盖
        if ($value == 0) {
            $sql = "select count(*) from ParaCheckBaseline where templateId = 53 and category = 'A' and ( cellId = '$cell' or (meContext = '$site' and cellId = ''))";
            $rs = $db->query($sql);
            if ($rs) {
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if ($row[0] > 0) {
                    $canshu=$row[0];
                    $value = 50;
                }
            } 
        }//baseline中A类参数配置不一致的
        if ($value == 0) {
            $sql = "select count(*) FROM OptionalFeatureLicense WHERE OptionalFeatureLicenseId='TCPOptimization' and featureState not like "%1%" and meContext = '$site'";
            $rs = $db->query($sql);
            if ($rs) {
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if (count($row)) {
                    $canshu = $row[0];
                    $value = 50;
                }
            }
        }//TCPO
        if ($value == 0) {
            $sql = "select count(*) from QciProfilePredefined WHERE qciProfilePredefinedId = 'qci1' and meContext = '$site'";
            $rs = $db->query($sql);
            if ($rs) {
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if (count($row)) {
                    $canshu = $row[0];
                    $value = 50;
                }
            }
        }//HARQ次数
        if ($value == 0) {
            $sql = "select count(*) from QciProfilePredefined WHERE qciProfilePredefinedId = 'qci1' and meContext = '$site' and rlcMode not like '%1 (UM)%'";
            $rs = $db->query($sql);
            if ($rs) {
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if (count($row)) {
                    $canshu = $row[0];
                    $value = 50;
                }
            }
        }//RLC reordering参数
        if ($value == 0) {
            $sql = "select count(*) from OptionalFeatureLicense WHERE OptionalFeatureLicenseId ='RlcUm' and featureState not like '%1 (ACTIVATED)%' and meContext = '$site'";
            $rs = $db->query($sql);
            if ($rs) {
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if (count($row)) {
                    $canshu = $row[0];
                    $value = 50;
                }
            }
        }//RLC reordering参数
        if ($value == 0) {
            $sql = "select count(*) from OptionalFeatureLicense WHERE keyId ='CXC4010961' and featureState not like '%1 (ACTIVATED)%' and meContext = '$site'";
            $rs = $db->query($sql);
            if ($rs) {
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if (count($row)) {
                    $canshu = $row[0];
                    $value = 50;
                }
            }
        }//RLC reordering参数
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
        }//PDCCH符号数设置低于2或增强相关功能未开
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
        }//PDCCH符号数设置低于2或增强相关功能未开
        if ($value == 0) {
            $sql = "select count(*) as occurs from TempA5Threshold1Rsrp where EUtranCellTDD ='$site'";
            $rs = $db->query($sql);
            $row = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0]) {
                $canshu = $row[0];
                $value = 50;
            }

        }//A5频率偏移核查1
        if ($value == 0) { 
            $sql = "select count(*) as occurs from TempA5Threshold2Rsrp where EUtranCellTDD ='$site'";
            $rs = $db->query($sql);
            $row = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0]) {
                $canshu = $row[0];
                $value = 50;
            }
        }//A5频率偏移核查2
        if ($value == 0) {
            $sql = "select count(*) as occurs from TempB2Threshold1RsrpGeranOffset where meContext ='$site'";
            $rs = $db->query($sql);
            $row = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0]) {
                $canshu = $row[0];
                $value = 50;
            }
        }//B2频率偏移核查1
        if ($value == 0) {
            $sql = "select count(*) as occurs from TempB2Threshold2GeranOffset where meContext ='$site'";
            $rs = $db->query($sql);
            $row = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0]) {
                $canshu = $row[0];
                $value = 50;
            }
        }//B2频率偏移核查2
        $result = array();
        $result['参数'] = $canshu;
        $result['Polar-参数'] = $value;
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
        $city = $this->a;
        $cell = $this->b;
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
        if ($flag == "4G") {
            $sql = "SELECT count(*) as num from $table where isdefined_direct=0 and ecgi = $ecgi AND distance_direct<0.8 and dateId >= '$date'";
        } else {
            $sql = "SELECT count(*) as num from $table where isdefined=0 and ecgi = $ecgi AND distance<0.8 and dateId >= '$date'";
        }
        $res = $db->query($sql);
        $num = 0;
        if ($res) {
            $row = $res->fetch(PDO::FETCH_ASSOC);
            $num = $row["num"];
        }
        $result = array();
        $result["邻区数量"] = $num;
        return $result;
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
        // var_dump($kpi0);var_dump($kpi1);
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
                    $ctrTime['丢包率'] = $kpi0;
                    $ctrTime['总包数'] = $kpi1; 
                    $idNum++;
                }
                array_push($allCtr, $ctrTime);
            }
        }
        // var_dump($succFilesName);return;
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
        // var_dump($allCtr);
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