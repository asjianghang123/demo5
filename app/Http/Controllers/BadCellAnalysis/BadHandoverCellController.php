<?php

/**
* BadCellController.php
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
 * 高掉线小区处理
 * Class BadCellController
 *
 * @category BadCellAnalysis
 * @package  App\Http\Controllers\BadCellAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class BadHandoverCellController extends Controller
{

    /**
     * 获得城市列表
     *
     * @return string JSON格式城市列表
     */
    public function getAllCity()
    {
        $cityClass = new DataBaseConnection();
        return $cityClass->getCityOptions();
    }

   public function storage() 
    {   
        $type     = input::get("type");
        $remoteIp = input::get("remoteIp");
        $city = input::get('city');
        $gzFile   = explode(";;", input::get("gzFiles"));
        // var_dump($gzFile);return;
        $dbc = new DataBaseConnection();
        $conn = $dbc->getCtrConn($city);
        $remoteIp = $conn['strServer'];
        // var_dump($remoteIp);return;
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
        // var_dump($rows);return;
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
        // print_r(input::all());
        $remoteIp = input::get("remoteIp");
        $filesName = [];
        $erbsArr = [];
        $type = Input::get("type");
        $filename = input::get('point');
        array_push($filesName, $filename);
        $city = Input::get('city');
        $cell = Input::get('cell');
        $rrcEst  = Input::get('rrc');
        $erabEst = Input::get('erab');
        $rows = SiteLte::where('cellName', $cell)->get();
        $erab = '';
        foreach ($rows as $row) {
            $erab = $row->siteName;
        }
        array_push($erbsArr, $erab);
        $rows =TraceServerInfo::where("type", $type)->where("city", $city)->get()->toArray();
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

        $file = storage::disk("ftp")->directories($type."/autobackup/");
        foreach ($filesName as $fileName) {
            foreach ($file as $value) {
                if ($fileName != explode("/", $value)[count(explode("/", $value))-1]) {
                    continue;
                } else {
                    array_push($succFilesName, $fileName);
                    $ctrTime['id']      = $idNum;
                    $ctrTime['kpiName'] = $value;
                    $ctrTime['准备切换失败数'] = $rrcEst;
                    $ctrTime['执行切换失败数'] = $erabEst; 
                    $idNum++;
                }
                array_push($allCtr, $ctrTime);
            }
        }
        $idNum = 1;
        foreach ($succFilesName as $succFileName) {
            $childrenId = 1;
            $dirsgz     = $type."/autobackup/".$succFileName;
            $filesgz    = $this->getFile($dirsgz);
            foreach ($filesgz as $filegz) {
                foreach ($erbsArr as $erb) {
                    $filePos = strpos($filegz, $erb);
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

        }//end foreach
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

    /**
     * 查询坏小区列表(分页)
     *
     * @return string JSON格式坏小区列表
     */
    public function templateQuery()
    {
        $table = Input::get('table');
        $cityArrs = Input::get('city');
        $cityArr = array();
        $cityNBMname = array();
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
        $cityFilter .= ")";

        $dbn = new DataBaseConnection();
        $conn = $dbn->getDB('mongs', 'AutoKPI');
        if ($conn == null) {
            die('Could not connect');
        }
        $result = array();
        $result1 = array();
        $nbm = new DataBaseConnection();
        $connNBM = $nbm->getDB('mongs', 'nbm');
        if ($connNBM == null) {
            die('Could not connect');
        }
        $city1 = new DataBaseConnection();
        for ($i = 0; $i < count($cityArrs); $i++) {
            $city2 = $city1->getNbiOptions($cityArrs[$i]); 
            array_push($cityNBMname, $city2);
        }
        $cityFilternbm = '(';
        for ($i = 0; $i < count($cityNBMname); $i++) {
            $cityFilternbm .= "city='" . $cityNBMname[$i] . "' or ";
        }
        $cityFilternbm = substr($cityFilternbm, 0, strlen($cityFilternbm) - 3);
        $cityFilternbm .= ")";
        $items = array();
        $download =array();
        $hours = "";
        $content = "id,city,subNetwork,cell,小区名,hour_id,前天小时数,昨天小时数,今天小时数,准备切换失败数(最新),执行切换失败数(最新),准备切换失败数(今日),执行切换失败数(今日),准备切换失败数(总),执行切换失败数(总),严重程度";
        $sql = "select * from BadHandoverCellTableEveryOneHour where $cityFilter";
        $rs = $conn->query($sql);
        while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
            // var_dump($row);
            $row3 = array();
            $db2 = new DataBaseConnection();
            $cell = $row['cell'];
            $db = $db2->getDB('mongs', 'mongs');
            $sqlsite = "select cellNameChinese from siteLte where cellName = '$cell'";
            $res = $db->query($sqlsite);
            $row3 = $res->fetch(PDO::FETCH_NUM);
            $row['小区名'] = $row3[0];
            $row['严重程度'] = ROUND(floatval($row['严重程度']), 2);
            
            // $hours = $row['hour_id'];
            $row['前天小时数'] = floatval($row['前天小时数']);
            $row['昨天小时数'] = floatval($row['昨天小时数']);
            $row['今天小时数'] = floatval($row['今天小时数']);
            $row['准备切换失败数(最新)'] = floatval($row['准备切换失败数(最新)']);
            $row['执行切换失败数(最新)'] = floatval($row['执行切换失败数(最新)']);
            $row['准备切换失败数(今日)'] = floatval($row['准备切换失败数(今日)']);
            $row['执行切换失败数(今日)'] = floatval($row['执行切换失败数(今日)']);
            $row['准备切换失败数(总)'] = floatval($row['准备切换失败数(总)']);

            $row['执行切换失败数(总)'] = floatval($row['执行切换失败数(总)']);
    
            $rows['id'] = $row['id'];
            $rows['city']=$row['city'];
            $rows['subNetwork']=$row['subNetwork'];
            $rows['cell'] = $row['cell'];
            // $row3 = array();
            // $db2 = new DataBaseConnection();
            // // $cell = $row['cell'];
            // // var_dump($cell);
            // $cell = $rows['cell'];
            // $db = $db2->getDB('mongs', 'mongs');
            // $sqlsite = "select cellNameChinese from siteLte where cellName = '$cell'";
            // $row3 = $db->query($sqlsite)->fetchall(PDO::FETCH_NUM);
            // // var_dump($row3);
            // $row['小区名'] = $row3[0][0];
            $rows['hour_id'] = $row['hour_id'];
            $rows['前天小时数'] = floatval($row['前天小时数']);
            $rows['昨天小时数'] = floatval($row['昨天小时数']);
            $rows['今天小时数'] = floatval($row['今天小时数']);
            $rows['准备切换失败数(最新)'] = floatval($row['准备切换失败数(最新)']);
            $rows['执行切换失败数(最新)'] = floatval($row['执行切换失败数(最新)']);
            $rows['准备切换失败数(今日)'] = floatval($row['准备切换失败数(今日)']);
            $rows['执行切换失败数(今日)'] = floatval($row['执行切换失败数(今日)']);
            $rows['准备切换失败数(总)'] = floatval($row['准备切换失败数(总)']);

            $rows['执行切换失败数(总)'] = floatval($row['执行切换失败数(总)']);
            $rows['严重程度'] = floatval($row['严重程度']);
            $row4["id"] = $row["id"];
            $row4["city"] = $row["city"];
            $row4["subNetwork"] = $row["subNetwork"];
            $row4["cell"] = $row["cell"];
            $row4["小区名"] = $row["小区名"];
            $row4["hour_id"] = $row["hour_id"];
            $row4["前天小时数"] = floatval($row["前天小时数"]);
            $row4["昨天小时数"] = floatval($row['昨天小时数']);
            $row4["今天小时数"] = floatval($row['今天小时数']);
            $row4["准备切换失败数(最新)"] = floatval($row['准备切换失败数(最新)']);
            $row4["执行切换失败数(最新)"] = floatval($row['执行切换失败数(最新)']);
            $row4["准备切换失败数(今日)"] = floatval($row["准备切换失败数(今日)"]);
            $row4["执行切换失败数(今日)"] = floatval($row["执行切换失败数(今日)"]);
            $row4["准备切换失败数(总)"] = floatval($row["准备切换失败数(总)"]);
            $row4["执行切换失败数(总)"] = floatval($row["执行切换失败数(总)"]);
            
            $row4["严重程度"] = floatval($row["严重程度"]);
            array_push($download, $row4);
            array_push($items, $row);
        }
        // var_dump($items);return;
        $result['records'] = count($items);
        $result["rows"] = $items;
        $result["download"]=$download;
        $result["content"] = $content;
        $filename = "common/files/" . $table . date('YmdHis') . ".csv";
        $result['filename'] = $filename;
        $this->resultToCSV2_1($result, $filename);
        $dates = date("Y-m-d");
        $dates2 = date("Y-m-d",strtotime("-1 day"));
        $dates3 = date("Y-m-d",strtotime("-2 day"));
        $hours = date("H");
        $hours = $hours+6;
//         $sql1 = "SELECT
//     table1.date_id,
//     table1.hour_id,
//     table1.city AS City,
//     table1.EutranCellTdd,
//     table1.kpi0 as '切换成功率',
//     table1.kpi1 as '切换失败次数'
// FROM
//     (
//         SELECT
//             date_id,
//             hour_id,
//             city AS City,
//             UserLabel AS EutranCellTdd,
//             count(DISTINCT UserLabel) AS ecgiTotal,
//             100 * (
//                 HO_SuccOutInterEnbS1_1 + HO_SuccOutInterEnbX2_1 + HO_SuccOutIntraEnb_1
//             ) / (
//                 HO_AttOutInterEnbS1_1 + HO_AttOutInterEnbX2_1 + HO_AttOutIntraEnb_1
//             ) AS kpi0,
//             (
//                 HO_AttOutInterEnbS1_1 + HO_AttOutInterEnbX2_1 + HO_AttOutIntraEnb_1
//             ) - (
//                 HO_SuccOutInterEnbS1_1 + HO_SuccOutInterEnbX2_1 + HO_SuccOutIntraEnb_1
//             ) AS kpi1
//         FROM
//             EutranCellTdd_cell_hour
//         WHERE
//             date_id IN ('$dates')
//         AND hour_id IN ($hours)
//         AND $cityFilternbm
//         GROUP BY
//             date_id,
//             hour_id,
//             City,
//             EutranCellTdd
//     ) as table1
// WHERE
//     table1.kpi0 < 98 AND table1.kpi1 > 50
// ";   
        $hours = $hours-2;
        $sql1 = "SELECT
    table2.date_id,
    table2.hour_id,
    table2.city AS City,
    table2.EutranCellTdd,
    CASE
WHEN table5.c IS NULL THEN
    0
ELSE
    table5.c
END AS '前天小时数',
 CASE
WHEN table3.a IS NULL THEN
    0
ELSE
    table3.a
END AS '昨天小时数',
 CASE
WHEN table4.b IS NULL THEN
    0
ELSE
    table4.b
END AS '今天小时数',
 table2.kpi0 AS '切换成功率',
 table2.kpi1 AS '切换失败次数'
FROM
    (
        SELECT
            table1.date_id,
            table1.hour_id,
            table1.city,
            table1.EutranCellTdd,
            table1.kpi0,
            table1.kpi1
        FROM
            (
                SELECT
                    date_id,
                    hour_id,
                    city AS City,
                    UserLabel AS EutranCellTdd,
                    count(DISTINCT UserLabel) AS ecgiTotal,
                    100 * (
                        HO_SuccOutInterEnbS1_1 + HO_SuccOutInterEnbX2_1 + HO_SuccOutIntraEnb_1
                    ) / (
                        HO_AttOutInterEnbS1_1 + HO_AttOutInterEnbX2_1 + HO_AttOutIntraEnb_1
                    ) AS kpi0,
                    (
                        HO_AttOutInterEnbS1_1 + HO_AttOutInterEnbX2_1 + HO_AttOutIntraEnb_1
                    ) - (
                        HO_SuccOutInterEnbS1_1 + HO_SuccOutInterEnbX2_1 + HO_SuccOutIntraEnb_1
                    ) AS kpi1
                FROM
                    EutranCellTdd_cell_hour
                WHERE
                    date_id IN ('$dates')
                AND hour_id IN ($hours)
                AND $cityFilternbm
                GROUP BY
                    date_id,
                    hour_id,
                    City,
                    EutranCellTdd
            ) AS table1
        WHERE
            table1.kpi0 < 98
        AND table1.kpi1 > 50
    ) AS table2
LEFT JOIN (
    SELECT
        UserLabel AS EutranCellTdd,
        count(DISTINCT hour_id) AS a
    FROM
        EutranCellTdd_cell_hour
    WHERE
        date_id IN ('$dates2')
    AND $cityFilternbm
    GROUP BY
        EutranCellTdd
) AS table3 ON table2.EutranCellTdd = table3.EutranCellTdd
LEFT JOIN (
    SELECT
        UserLabel AS EutranCellTdd,
        count(DISTINCT hour_id) AS b
    FROM
        EutranCellTdd_cell_hour
    WHERE
        date_id IN ('$dates')
    AND $cityFilternbm
    GROUP BY
        EutranCellTdd
) AS table4 ON table2.EutranCellTdd = table4.EutranCellTdd
LEFT JOIN (
    SELECT
        UserLabel AS EutranCellTdd,
        count(DISTINCT hour_id) AS c
    FROM
        EutranCellTdd_cell_hour
    WHERE
        date_id IN ('$dates3')
    AND $cityFilternbm
    GROUP BY
        EutranCellTdd
) AS table5 ON table2.EutranCellTdd = table5.EutranCellTdd";
        $rs1 = $connNBM->query($sql1);
        $content1 = "date_id,hour_id,City,EutranCellTdd,前天小时数,昨天小时数,今天小时数,切换成功率,切换失败次数";
        $items1 = array();
        $download1 = array();
        while ($row1 = $rs1->fetch(PDO::FETCH_ASSOC)) {
            $row1['切换成功率'] = ROUND(floatval($row1['切换成功率']), 2);
            $row1['切换失败次数'] = ROUND(floatval($row1['切换失败次数']), 2);
            $rows1['date_id'] = $row1['date_id'];
            $rows1['hour_id']=$row1['hour_id'];
            $rows1['City']=$row1['City'];
            $rows1['EutranCellTdd'] = $row1['EutranCellTdd'];
            $rows1['hour_id'] = $row1['hour_id'];
            $rows1['前天小时数'] = $row1['前天小时数'];
            $rows1['昨天小时数'] = $row1['昨天小时数'];
            $rows1['今天小时数'] = $row1['今天小时数'];
            $rows1['切换成功率'] = floatval($row1['切换成功率']);
            $rows1['切换失败数'] = floatval($row1['切换失败次数']);
            array_push($download1, $rows1);
            array_push($items1, $row1);
        }
        $result1['records1'] = count($items1);
        $result1["rows1"] = $items1;
        $result1["download1"]=$download1;
        $result1["content1"] = $content1;
        $filename1 = "common/files/" . $table . "nbm" . date('YmdHis') . ".csv";
        $result1['filename1'] = $filename1;
        $this->resultToCSV2_1($result1, $filename1);
        $resultall = array_merge($result,$result1);
        echo json_encode($resultall);
    }//end templateQuery()

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
        fclose($fp);
    }//end resultToCSV2()
    protected function resultToCSV2_1($result, $filename)
    {
        if (strstr($filename, "nbm")) {
            $csvContent = mb_convert_encoding($result['content1'] . "\n", 'gb2312', 'utf-8');
            $fp = fopen($filename, "w");
            fwrite($fp, $csvContent);
            foreach ($result["download1"] as $row) {
            fputcsv($fp, $row);
            }
            fclose($fp);
        } else {
            // $result['content'] = str_replace("小区名,", "", $result['content']);
            $csvContent = mb_convert_encoding($result['content'] . "\n", 'gb2312', 'utf-8');
            $fp = fopen($filename, "w");
            fwrite($fp, $csvContent);
            foreach ($result["download"] as $row) {
            fputcsv($fp, $row);
            }
            fclose($fp);
        }
    }//end resultToCSV2()

    /**
     * 获得极地图数据
     *
     * @return array 极地图数据
     */
    public function getPolarMapData()
    {
        $return = [];
        $polar = [];
        $returnData = [];
        $cell = input::get('cell');
        $city = input::get('city');

        date_default_timezone_set('PRC');
        $startTime = date("Y-m-d", strtotime("-1 day"));
        $endTime = date("Y-m-d");

        $dsn = new DataBaseConnection();

        $db = $dsn->getDB('mongs', 'mongs');
        $sql = "SELECT siteName from siteLte WHERE cellName='$cell';";
        $row = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
        $erbs = $row[0]['siteName'];
        $sql = "SELECT max(num) as num FROM( SELECT SP_text,t.access AS num,t.alarmNameE FROM FMA_alarm_log r LEFT JOIN mongs.AlarmInfo t ON r.SP_text= t.alarmNameE WHERE DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $endTime . "' AND meContext = '" . $erbs . "' GROUP BY access ORDER BY access DESC)t; ";
        $db = $dsn->getDB('alarm', 'Alarm');
        $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) == 0) {
            array_push($returnData, 0);
        } else {
            if ($row[0]['num'] > 100) {
                array_push($returnData, 100);
            } else {
                array_push($returnData, intval($row[0]['num']));
            }
        }
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $sql = "SELECT
                    #ROUND(AVG(`RSRP<-116的比例`)*10, 0) AS num
                    AVG(`RSRP<-116的比例`) AS num
                FROM
                    badHandoverCell_ex
                WHERE
                    day_id >= '" . $startTime . "'
                AND day_id <= '" . $endTime . "'
                AND cell = '" . $cell . "';";
        $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);     //弱覆盖
        $weakCoverRateFlag = '';                                                        //弱覆盖
        if (count($row) == 0) {
            array_push($returnData, 0);
            $weakCoverRateFlag = 0;
        } else {
            if ($row[0]['num'] > 20) {
                array_push($returnData, 100);
            } else if ($row[0]['num'] < 2) {
                array_push($returnData, 0);
            } else {
                $num = round($row[0]['num']*100/18, 2);
                if ($num > 100) {
                    array_push($returnData, 100);
                } else {
                    array_push($returnData, $num);
                }
            }
            if ($row[0]['num'] > 11) {
                $weakCoverRateFlag = 1;
            } else {
                $weakCoverRateFlag = 0;
            }
        }

        $database = $dsn->getMRDatabaseByCity($city);
        $db = $dsn->getDB('MR', $database);
        $sql = "SELECT
                    ROUND(rate*100*5, 0) AS num
                FROM
                    mroOverCoverage_day
                , GLOBAL.siteLte
                WHERE
                    mroOverCoverage_day.ecgi = siteLte.ecgi
                AND dateId = '" . $startTime . "' AND cellName='" . $cell . "';";
        $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);    //重叠覆盖
        $overCoverFlag = '';
        if (count($row) == 0) {
            array_push($returnData, 0);
            $overCoverFlag = 0;
        } else {
            if ($row[0]['num'] > 100) {
                array_push($returnData, 100);
            } else {
                array_push($returnData, intval($row[0]['num']));
            }
            if ($row[0]['num'] > 5) {
                $weakCoverRateFlag = 1;
            } else {
                $weakCoverRateFlag = 0;
            }
        }
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $sql = "SELECT
                    AVG(`RSRQ<-15.5的比例`) AS num
                    #ROUND(AVG(`RSRQ<-15.5的比例`)*10, 0) AS num
                FROM
                    badHandoverCell_ex
                WHERE
                    day_id >= '" . $startTime . "'
                AND day_id <= '" . $endTime . "'
                AND cell = '" . $cell . "';";
        $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);  //质差
        if (count($row) == 0) {
            array_push($returnData, 0);
        } else {
            if ($row[0]['num'] == 0) {
                array_push($returnData, 0);
            } else {
                if ($weakCoverRateFlag == 0) {   //非弱覆盖
                    if ($overCoverFlag == 0) {   //非重叠覆盖
                        $point = $row[0]['num']*5;
                        if ($point >100) {
                            array_push($returnData, 100);
                        } else {
                            array_push($returnData, $point);
                        }
                    } else {
                        $point = 50 + $row[0]['num']*2.5;
                        if ($point >100) {
                            array_push($returnData, 100);
                        } else {
                            array_push($returnData, $point);
                        }
                    }
                } else {
                    $point = 50 + $row[0]['num']*2.5;
                    if ($point >100) {
                        array_push($returnData, 100);
                    } else {
                        array_push($returnData, $point);
                    }
                }
            }
        }
        $db = $dsn->getDB('mongs', 'mongs');
        $sql = "select ecgi from siteLte where cellName = '$cell'";
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) == 0) {
            array_push($returnData, 0);
        } else {
            $ecgi = $row[0]['ecgi'];
            $cityEN = Input::get('city');
            $cityCH = $this->encityToCHcity($cityEN);
            $dbname = $this->getMRDatabase($cityCH);
            $db = $dsn->getDB('MR', $dbname);
            $table = 'mreServeNeigh_day';
            $sqlNum = "select count(*) as num from $table where isdefined_direct=0 and ecgi = '$ecgi' AND distance_direct<1.5 and dateId >= '" . $startTime . "';";   //邻区
            $row = $db->query($sqlNum)->fetchAll(PDO::FETCH_ASSOC);
            if (count($row) == 0) {
                array_push($returnData, 0);
            } else {
                if ($row[0]['num'] == 0) {
                    array_push($returnData, 0);
                } else {
                    if ($weakCoverRateFlag == 1) {    //非弱覆盖小区
                        $num = intval($row[0]['num']*10);
                        if ($num > 100) {
                            array_push($returnData, 100);
                        } else {
                            array_push($returnData, $num);
                        }
                    } else {                       //弱覆盖高于11%呈现弱覆盖校区
                        $num = intval(50+$row[0]['num']*5);
                        if ($num > 100) {
                            array_push($returnData, 100);
                        } else {
                            array_push($returnData, $num);
                        }
                    }
                }
            }
        }
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $sql = "SELECT
                    AVG(PRB1上行干扰电平) AS PRB1上行干扰电平,
                    AVG(PRB2上行干扰电平) AS PRB2上行干扰电平,
                    AVG(PRB3上行干扰电平) AS PRB3上行干扰电平,
                    AVG(PRB4上行干扰电平) AS PRB4上行干扰电平,
                    AVG(PRB5上行干扰电平) AS PRB5上行干扰电平,
                    AVG(PRB6上行干扰电平) AS PRB6上行干扰电平,
                    AVG(PRB7上行干扰电平) AS PRB7上行干扰电平,
                    AVG(PRB8上行干扰电平) AS PRB8上行干扰电平,
                    AVG(PRB9上行干扰电平) AS PRB9上行干扰电平,
                    AVG(PRB10上行干扰电平) AS PRB10上行干扰电平,
                    AVG(PRB11上行干扰电平) AS PRB11上行干扰电平,
                    AVG(PRB12上行干扰电平) AS PRB12上行干扰电平,
                    AVG(PRB13上行干扰电平) AS PRB13上行干扰电平,
                    AVG(PRB14上行干扰电平) AS PRB14上行干扰电平,
                    AVG(PRB15上行干扰电平) AS PRB15上行干扰电平,
                    AVG(PRB16上行干扰电平) AS PRB16上行干扰电平,
                    AVG(PRB17上行干扰电平) AS PRB17上行干扰电平,
                    AVG(PRB18上行干扰电平) AS PRB18上行干扰电平,
                    AVG(PRB19上行干扰电平) AS PRB19上行干扰电平,
                    AVG(PRB20上行干扰电平) AS PRB20上行干扰电平,
                    AVG(PRB21上行干扰电平) AS PRB21上行干扰电平,
                    AVG(PRB22上行干扰电平) AS PRB22上行干扰电平,
                    AVG(PRB23上行干扰电平) AS PRB23上行干扰电平,
                    AVG(PRB24上行干扰电平) AS PRB24上行干扰电平,
                    AVG(PRB25上行干扰电平) AS PRB25上行干扰电平,
                    AVG(PRB26上行干扰电平) AS PRB26上行干扰电平,
                    AVG(PRB27上行干扰电平) AS PRB27上行干扰电平,
                    AVG(PRB28上行干扰电平) AS PRB28上行干扰电平,
                    AVG(PRB29上行干扰电平) AS PRB29上行干扰电平,
                    AVG(PRB30上行干扰电平) AS PRB30上行干扰电平,
                    AVG(PRB31上行干扰电平) AS PRB31上行干扰电平,
                    AVG(PRB32上行干扰电平) AS PRB32上行干扰电平,
                    AVG(PRB33上行干扰电平) AS PRB33上行干扰电平,
                    AVG(PRB34上行干扰电平) AS PRB34上行干扰电平,
                    AVG(PRB35上行干扰电平) AS PRB35上行干扰电平,
                    AVG(PRB36上行干扰电平) AS PRB36上行干扰电平,
                    AVG(PRB37上行干扰电平) AS PRB37上行干扰电平,
                    AVG(PRB38上行干扰电平) AS PRB38上行干扰电平,
                    AVG(PRB39上行干扰电平) AS PRB39上行干扰电平,
                    AVG(PRB40上行干扰电平) AS PRB40上行干扰电平,
                    AVG(PRB41上行干扰电平) AS PRB41上行干扰电平,
                    AVG(PRB42上行干扰电平) AS PRB42上行干扰电平,
                    AVG(PRB43上行干扰电平) AS PRB43上行干扰电平,
                    AVG(PRB44上行干扰电平) AS PRB44上行干扰电平,
                    AVG(PRB45上行干扰电平) AS PRB45上行干扰电平,
                    AVG(PRB46上行干扰电平) AS PRB46上行干扰电平,
                    AVG(PRB47上行干扰电平) AS PRB47上行干扰电平,
                    AVG(PRB48上行干扰电平) AS PRB48上行干扰电平,
                    AVG(PRB49上行干扰电平) AS PRB49上行干扰电平,
                    AVG(PRB50上行干扰电平) AS PRB50上行干扰电平,
                    AVG(PRB51上行干扰电平) AS PRB51上行干扰电平,
                    AVG(PRB52上行干扰电平) AS PRB52上行干扰电平,
                    AVG(PRB53上行干扰电平) AS PRB53上行干扰电平,
                    AVG(PRB54上行干扰电平) AS PRB54上行干扰电平,
                    AVG(PRB55上行干扰电平) AS PRB55上行干扰电平,
                    AVG(PRB56上行干扰电平) AS PRB56上行干扰电平,
                    AVG(PRB57上行干扰电平) AS PRB57上行干扰电平,
                    AVG(PRB58上行干扰电平) AS PRB58上行干扰电平,
                    AVG(PRB59上行干扰电平) AS PRB59上行干扰电平,
                    AVG(PRB60上行干扰电平) AS PRB60上行干扰电平,
                    AVG(PRB61上行干扰电平) AS PRB61上行干扰电平,
                    AVG(PRB62上行干扰电平) AS PRB62上行干扰电平,
                    AVG(PRB63上行干扰电平) AS PRB63上行干扰电平,
                    AVG(PRB64上行干扰电平) AS PRB64上行干扰电平,
                    AVG(PRB65上行干扰电平) AS PRB65上行干扰电平,
                    AVG(PRB66上行干扰电平) AS PRB66上行干扰电平,
                    AVG(PRB67上行干扰电平) AS PRB67上行干扰电平,
                    AVG(PRB68上行干扰电平) AS PRB68上行干扰电平,
                    AVG(PRB69上行干扰电平) AS PRB69上行干扰电平,
                    AVG(PRB70上行干扰电平) AS PRB70上行干扰电平,
                    AVG(PRB71上行干扰电平) AS PRB71上行干扰电平,
                    AVG(PRB72上行干扰电平) AS PRB72上行干扰电平,
                    AVG(PRB73上行干扰电平) AS PRB73上行干扰电平,
                    AVG(PRB74上行干扰电平) AS PRB74上行干扰电平,
                    AVG(PRB75上行干扰电平) AS PRB75上行干扰电平,
                    AVG(PRB76上行干扰电平) AS PRB76上行干扰电平,
                    AVG(PRB77上行干扰电平) AS PRB77上行干扰电平,
                    AVG(PRB78上行干扰电平) AS PRB78上行干扰电平,
                    AVG(PRB79上行干扰电平) AS PRB79上行干扰电平,
                    AVG(PRB80上行干扰电平) AS PRB80上行干扰电平,
                    AVG(PRB81上行干扰电平) AS PRB81上行干扰电平,
                    AVG(PRB82上行干扰电平) AS PRB82上行干扰电平,
                    AVG(PRB83上行干扰电平) AS PRB83上行干扰电平,
                    AVG(PRB84上行干扰电平) AS PRB84上行干扰电平,
                    AVG(PRB85上行干扰电平) AS PRB85上行干扰电平,
                    AVG(PRB86上行干扰电平) AS PRB86上行干扰电平,
                    AVG(PRB87上行干扰电平) AS PRB87上行干扰电平,
                    AVG(PRB88上行干扰电平) AS PRB88上行干扰电平,
                    AVG(PRB89上行干扰电平) AS PRB89上行干扰电平,
                    AVG(PRB90上行干扰电平) AS PRB90上行干扰电平,
                    AVG(PRB91上行干扰电平) AS PRB91上行干扰电平,
                    AVG(PRB92上行干扰电平) AS PRB92上行干扰电平,
                    AVG(PRB93上行干扰电平) AS PRB93上行干扰电平,
                    AVG(PRB94上行干扰电平) AS PRB94上行干扰电平,
                    AVG(PRB95上行干扰电平) AS PRB95上行干扰电平,
                    AVG(PRB96上行干扰电平) AS PRB96上行干扰电平,
                    AVG(PRB97上行干扰电平) AS PRB97上行干扰电平,
                    AVG(PRB98上行干扰电平) AS PRB98上行干扰电平,
                    AVG(PRB99上行干扰电平) AS PRB99上行干扰电平,
                    AVG(PRB100上行干扰电平) AS PRB100上行干扰电平
                FROM
                    interfereCell
                WHERE
                    cell = '" . $cell . "'
                AND day_id >= '" . $startTime . "'
                AND day_id <= '" . $endTime . "';";
        $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $i = 0;
        $avg = 0;
        if (count($row) == 0) {
            array_push($returnData, 0);
        } else {
            foreach ($row[0] as $val) {
                if ($val == null) {
                    continue;
                }
                $avg = $avg + $val;
                $i++;
            }
            if ($i == 0) {
                array_push($returnData, 0);
            } else {
                $avg = $avg / $i;
                if ($avg >= -102) {
                    array_push($returnData, 100);
                } else if ($avg <= -112) {
                    array_push($returnData, 0);
                } else {
                    $point = ($avg + 112)*10;
                    if ($point > 100) {
                        array_push($returnData, 100);
                    } else {
                        array_push($returnData, $point);
                    }
                }
            }
        }

        $db = $dsn->getDB('autokpi', 'AutoKPI'); //高话务
        $sql = "SELECT SUM(`最大RRC连接用户数`)/20 AS num
                FROM badHandoverCell_ex
                WHERE
                    cell = '" . $cell . "'
                AND day_id >= '" . $startTime . "'
                AND day_id <= '" . $endTime . "';";
        $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) == 0) {
            array_push($polar, 0);
        } else {
            if ($row[0]['num'] > 100) {
                array_push($polar, 100);
            } else {
                array_push($polar, intval($row[0]['num']));
            }
        }
        //参数-判断流程
        $value = 0;
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate = $date->format('ymd');
        $dbname = 'kget' . $yesDate;//获取昨天的kget数据库
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', $dbname);
        $cityCh = $dbc->getCHCity($city);
        $subNetwork = $dbc->getSubNets($cityCh);

        $db1 = $dbc->getDB('mongs', 'mongs');
        $sql = "select siteName from siteLte where cellName = '$cell'";
        $rs   = $db1->query($sql);
        $row  = $rs->fetch(PDO::FETCH_NUM);
        $meContext = $row[0];

        //MRE邻区排名前30的邻区有PCI一二阶冲突
        $filter = " where EutranCellTDD = '$cell'";
        if ($subNetwork != '') {
            $filter = $filter." and subNetwork in (" . $subNetwork . ")";
        }
        //一阶冲突
        $sql1 = "select count(*) from TempEUtranCellRelationNeighOfPci".$filter;
        $rs   = $db->query($sql1);
        $row  = $rs->fetch(PDO::FETCH_NUM);
        if ($row[0] > 0) {
            $value = 100;
        } else {
            //二阶冲突
            $sql2 = "select count(*) from TempEUtranCellRelationNeighOfNeighPci".$filter;
            $rs   = $db->query($sql2);
            $row  = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0] > 0) {
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
            $row  = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0] > 0) {
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
            $row  = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0] > 0) {
                $value = 100;
            } else {
                //X2-邻区eNbID检查
                $sql2 = "select count(*) from TempTermPointToENB_IP".$filter;
                $rs   = $db->query($sql2);
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if ($row[0] > 0) {
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
                $value = 100;
            }
        }
        if ($value == 0) {
            //切换准备失败次数的占比50%以上发生在，邻区外部定义不一致的邻区
            $filter = " where meContext = '$meContext' and ExternalEUtranCellTDDId = '$ecgi_nr'";
            if ($subNetwork != '') {
                $filter = $filter." and subNetwork in (" . $subNetwork . ")";
            }
            $sql = "select count(*) from TempExternalNeigh4G ".$filter;
            $rs   = $db->query($sql);
            $row  = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0] > 0) {
                $value = 100;
            }
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
            $row  = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0] > 0) {
                $value = 50;
            }
        }
        if ($value == 0) {
            //baseline中A类参数配置不一致的
            $templateId = 53;
            $filter = " where templateId='$templateId' and category = 'A' and subNetwork in (".$subNetwork.") and ( cellId = '$cell' or (meContext = '$meContext' and cellId = ''))";
            $sql = "select count(*) from ParaCheckBaseline".$filter;
            $rs   = $db->query($sql);
            $row  = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0] > 0) {
                $value = 50;
            }
        }

        array_push($returnData, $value);
        $return['data'] = $returnData;
        $return['polar'] = $polar;
        return $return;
    }// end getPolarMapData()

    /**
     * 获得城市中文名
     *
     * @param string $cityEN 城市英文名
     *
     * @return string 城市中文名
     */
    public function encityToCHcity($cityEN)
    {
        $dbc = new DataBaseConnection();
        return $dbc->getCHCity($cityEN);
    }//end encityToCHcity()


    /**
     * 获得MR数据库名
     *
     * @param string $city 城市
     *
     * @return string MR数据库名
     */
    public function getMRDatabase($city)
    {
        $dbc = new DataBaseConnection();
        return $dbc->getMRDatabase($city);
    }//end getMRDatabase()

    /**
     * 动态生成YAxis
     *
     * @param float $max 最大值
     *
     * @param float $min 最小值
     *
     * @return array YAxis
     */
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

    /**
     * 获得小区级告警详细
     *
     * @return array 小区级告警详细
     */
    public function getCellAlarmClassifyTable()
    {
        $cell = Input::get('cell');
        date_default_timezone_set('PRC');
        $startTime = date("Y-m-d", strtotime("-1 day"));
        $endTime = date("Y-m-d");
        $result = array();
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('mongs', 'mongs');
        $sql = "SELECT siteName from siteLte WHERE cellName='$cell';";
        $row = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
        $erbs = $row[0]['siteName'];

        $sql = "SELECT COUNT(*) as num FROM FMA_alarm_list r,mongs.AlarmInfo t WHERE r.SP_text= t.alarmNameE AND DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $endTime . "' AND meContext = '" . $erbs . "';";
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('alarm', 'Alarm');
        $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();

        $result['records'] = $row[0]['num'];
        $result["content"] = "Event_time,meContext,eutranCell,SP_text,Problem_text,alarmNameE,alarmNameC,levelE,levelC,interfere,access,lost,handover,comments";

        $sql = "SELECT Event_time,meContext,eutranCell,SP_text,Problem_text,t.alarmNameE,t.alarmNameC,t.levelE,t.levelC,t.interfere,t.access,t.lost,t.handover,t.comments FROM FMA_alarm_list r,mongs.AlarmInfo t WHERE r.SP_text= t.alarmNameE AND DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $endTime . "' AND meContext = '" . $erbs . "';";
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchAll();
        $result["rows"] = $rows;
        return $result;
    }//end getCellAlarmClassifyTable()

    /**
     * 获得基站级告警详细
     *
     * @return array 基站级告警详细
     */
    public function getErbsAlarmClassifyTable()
    {
        $cell = Input::get('cell');
        date_default_timezone_set('PRC');
        $startTime = date("Y-m-d", strtotime("-1 day"));
        $endTime = date("Y-m-d");
        $result = array();
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('mongs', 'mongs');
        $sql = "SELECT siteName from siteLte WHERE cellName='$cell';";
        $row = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
        $erbs = $row[0]['siteName'];

        $sql = "SELECT COUNT(*) as num FROM FMA_alarm_log r LEFT JOIN mongs.AlarmInfo t ON r.SP_text= t.alarmNameE WHERE DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $endTime . "' AND meContext = '" . $erbs . "';";
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('alarm', 'Alarm');
        $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();

        $result['records'] = $row[0]['num'];
        $result["content"] = "Event_time,meContext,eutranCell,SP_text,Problem_text,alarmNameE,alarmNameC,levelE,levelC,interfere,access,lost,handover,comments";

        $sql = "SELECT Event_time,meContext,eutranCell,SP_text,Problem_text,t.alarmNameE,t.alarmNameC,t.levelE,t.levelC,t.interfere,t.access,t.lost,t.handover,t.comments FROM FMA_alarm_log r LEFT JOIN mongs.AlarmInfo t ON r.SP_text= t.alarmNameE WHERE DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $endTime . "' AND meContext = '" . $erbs . "';";
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchAll();
        $result["rows"] = $rows;
        return $result;
    }//end getErbsAlarmClassifyTable()

    /**
     * 获得LTE补邻区数据
     *
     * @return array LTE补邻区数据
     */
    public function getLTENeighborDataModel()
    {
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('mongs', 'mongs');
        $sql = "select ecgi from siteLte where cellName = '$cell'";
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $ecgi = $row[0]['ecgi'];

        $cityEN = Input::get('city');
        $cityCH = $this->encityToCHcity($cityEN);
        $dbname = $this->getMRDatabase($cityCH);

        date_default_timezone_set('PRC');
        $dateTime = date("Y-m-d", strtotime("-1 day"));

        $result = array();
        $return = array();
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('MR', $dbname);
        $table = 'mreServeNeigh_day';
        $sql = "select * from $table limit 1";
        $rs = $db->query($sql, PDO::FETCH_ASSOC);
        $rows = $rs->fetchall();
        $keys = array_keys($rows[0]);
        $text = '';
        foreach ($keys as $key) {
            if ($key == 'id') {
                continue;
            }
            $text .= $key . ',';
        }
        $text = substr($text, 0, strlen($text) - 1);
        $result['text'] = $text;
        $rowsId = array();
        foreach ($rows as $row) {
            array_shift($row);
            array_push($rowsId, $row);
        }
        $result['rows'] = $rowsId;

        $sql = "select * from $table where isdefined_direct=0 and ecgi = '$ecgi' and dateId >= '" . $dateTime . "' AND distance_direct<0.8;";
        // print_r($sql);
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $allData = array();
        foreach ($rows as $row) {
            array_push($allData, $row);
        }
        $return['content'] = "id,dateId,ecgi,longitude,latitude,mr_LteScEarfcn,mr_LteNcEarfcn,mr_LteNcPci,eventType,ecgiNeigh_direct,isdefined_direct,distance_direct,ncFreq_session,nc_session_num,nc_session_ratio,ncFreq_times_num,nc_times_num,nc_times_ratio,avg_mr_LteScRSRP,avg_mr_LteScRSRQ,avg_mr_LteNcRSRP,avg_mr_LteNcRSRQ";
        $return['rows'] = $allData;
        $return['records'] = count($allData);
        return ($return);
    }//end getLTENeighborDataModel()

    /**
     * 获得质差图表数据
     *
     * @return void
     */
    public function getZhichaCellChart()
    {
        $dbn = new DataBaseConnection();
        $conn = $dbn->getDB('autokpi', 'AutoKPI');
        if ($conn == null) {
            echo 'Could not connect';
        }
        $table = Input::get('table');

        $cell = Input::get('cell');
        date_default_timezone_set('PRC');
        $startTime = date("Y-m-d", strtotime("-1 day"));
        $endTime = date("Y-m-d");
        $yAxis_name_left = Input::get('yAxis_name_left');
        $yAxis_name_right = Input::get('yAxis_name_right');

        $res = $conn->query("select day_id,hour_id,`" . $yAxis_name_left . "`,`" . $yAxis_name_right . "` from " . $table . " where day_id>='" . $startTime . "' and day_id<='" . $endTime . "' and cell='" . $cell . "' ORDER BY day_id,hour_id");
        $yAxis = array();
        $yAxis_2 = array();
        $items = array();
        $returnData = array();
        $series = array();
        $series_2 = array();
        $categories = array();

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

    /**
     * 获得单小区弱覆盖数据
     *
     * @return array 弱覆盖数据
     */
    public function getWeakCoverCellModel()
    {
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');

        date_default_timezone_set('PRC');
        $day_from = date("Y-m-d", strtotime("-1 day"));
        $day_to = date("Y-m-d");

        $return = array();
        $sql = "select id,day_id,hour_id,city,subNetwork,cell,准备切换失败数,执行切换失败数,`RSRP<-116的比例` from badHandoverCell_ex where day_id >= '" . $day_from . "' AND day_id <= '" . $day_to . "' AND cell='" . $cell . "'";
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $allData = array();
        foreach ($rows as $row) {
            array_push($allData, $row);
        }
        $return['content'] = "id,day_id,hour_id,city,subNetwork,cell,准备切换失败数,执行切换失败数,RSRP<-116的比例";
        $return['rows'] = $allData;
        $return['records'] = count($allData);
        return ($return);
    }//end getWeakCoverCellModel()

    /**
     * 获得质差数据
     *
     * @return array 质差数据
     */
    public function getzhichaCellModel()
    {
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');

        date_default_timezone_set('PRC');
        $day_from = date("Y-m-d", strtotime("-1 day"));
        $day_to = date("Y-m-d");

        $return = array();
        $sql = "select id,day_id,hour_id,city,subNetwork,cell,准备切换失败数,执行切换失败数,`RSRQ<-15.5的比例` from badHandoverCell_ex where day_id >= '" . $day_from . "' AND day_id <= '" . $day_to . "' AND cell='" . $cell . "'";
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $allData = array();
        foreach ($rows as $row) {
            array_push($allData, $row);
        }
        $return['content'] = "id,day_id,hour_id,city,subNetwork,cell,准备切换失败数,执行切换失败数,RSRQ<-15.5的比例";
        $return['rows'] = $allData;
        $return['records'] = count($allData);
        return ($return);

    }//end getzhichaCellModel()

    /**
     * 获得单小区重叠覆盖数据
     *
     * @return array 重叠覆盖数据
     */
    public function getOverlapCoverModel()
    {
        $cell = Input::get('cell');
        date_default_timezone_set('PRC');
        $day_from = date("Y-m-d", strtotime("-1 day"));
        $return = array();
        $dsn = new DataBaseConnection();
        $cityEN = Input::get('city');
        $cityCH = $this->encityToCHcity($cityEN);
        $dbname = $this->getMRDatabase($cityCH);
        $db = $dsn->getDB('MR', $dbname);

        $sql = "SELECT
                    dateId,mroOverCoverage_day.ecgi,sample,all_sample,rate,intensity,siteLte.dir,siteLte.tiltM,siteLte.tiltE,siteLte.antHeight,siteLte.tac
                FROM
                    mroOverCoverage_day
                , GLOBAL .siteLte
                WHERE
                    mroOverCoverage_day.ecgi = siteLte.ecgi
                AND dateId = '" . $day_from . "'
                AND cellName = '" . $cell . "';";
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $allData = array();
        foreach ($rows as $row) {
            array_push($allData, $row);
        }
        $return['content'] = "dateTime_id,ecgi,sample,all_sample,rate,intensity,dir,tiltM,tiltE,antHeight,tac";
        $return['rows'] = $allData;
        $return['records'] = count($allData);
        return ($return);

    }//end getOverlapCoverModel()

    /**
     * 获得单小区干扰数据
     *
     * @return array 干扰数据
     */
    public function getInterfereCellModel()
    {
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');

        date_default_timezone_set('PRC');
        $day_from = date("Y-m-d", strtotime("-1 day"));
        $day_to = date("Y-m-d");

        $return = array();

        $sql = "select day_id,hour_id,city,subNetwork,cell,PUCCH上行干扰电平,PUSCH上行干扰电平,SF1上行干扰电平,SF2上行干扰电平,SF6上行干扰电平,SF7上行干扰电平,PRB1上行干扰电平,PRB2上行干扰电平,PRB3上行干扰电平,PRB4上行干扰电平,PRB5上行干扰电平,PRB6上行干扰电平,PRB7上行干扰电平,PRB8上行干扰电平,PRB9上行干扰电平,PRB10上行干扰电平,PRB11上行干扰电平,PRB12上行干扰电平,PRB13上行干扰电平,PRB14上行干扰电平,PRB15上行干扰电平,PRB16上行干扰电平,PRB17上行干扰电平,PRB18上行干扰电平,PRB19上行干扰电平,PRB20上行干扰电平,PRB21上行干扰电平,PRB22上行干扰电平,PRB23上行干扰电平,PRB24上行干扰电平,PRB25上行干扰电平,PRB26上行干扰电平,PRB27上行干扰电平,PRB28上行干扰电平,PRB29上行干扰电平,PRB30上行干扰电平,PRB31上行干扰电平,PRB32上行干扰电平,PRB33上行干扰电平,PRB34上行干扰电平,PRB35上行干扰电平,PRB36上行干扰电平,PRB37上行干扰电平,PRB38上行干扰电平,PRB39上行干扰电平,PRB40上行干扰电平,PRB41上行干扰电平,PRB42上行干扰电平,PRB43上行干扰电平,PRB44上行干扰电平,PRB45上行干扰电平,PRB46上行干扰电平,PRB47上行干扰电平,PRB48上行干扰电平,PRB49上行干扰电平,PRB50上行干扰电平,PRB51上行干扰电平,PRB52上行干扰电平,PRB53上行干扰电平,PRB54上行干扰电平,PRB55上行干扰电平,PRB56上行干扰电平,PRB57上行干扰电平,PRB58上行干扰电平,PRB59上行干扰电平,PRB60上行干扰电平,PRB61上行干扰电平,PRB62上行干扰电平,PRB63上行干扰电平,PRB64上行干扰电平,PRB65上行干扰电平,PRB66上行干扰电平,PRB67上行干扰电平,PRB68上行干扰电平,PRB69上行干扰电平,PRB70上行干扰电平,PRB71上行干扰电平,PRB72上行干扰电平,PRB73上行干扰电平,PRB74上行干扰电平,PRB75上行干扰电平,PRB76上行干扰电平,PRB77上行干扰电平,PRB78上行干扰电平,PRB79上行干扰电平,PRB80上行干扰电平,PRB81上行干扰电平,PRB82上行干扰电平,PRB83上行干扰电平,PRB84上行干扰电平,PRB85上行干扰电平,PRB86上行干扰电平,PRB87上行干扰电平,PRB88上行干扰电平,PRB89上行干扰电平,PRB90上行干扰电平,PRB91上行干扰电平,PRB92上行干扰电平,PRB93上行干扰电平,PRB94上行干扰电平,PRB95上行干扰电平,PRB96上行干扰电平,PRB97上行干扰电平,PRB98上行干扰电平,PRB99上行干扰电平,PRB100上行干扰电平,type from interfereCell where cell='" . $cell . "' AND day_id>='" . $day_from . "' AND day_id<='" . $day_to . "' order by day_id DESC";
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $allData = array();
        foreach ($rows as $row) {
            array_push($allData, $row);
        }
        $return['content'] = "day_id,hour_id,city,subNetwork,cell,PUCCH上行干扰电平,PUSCH上行干扰电平,SF1上行干扰电平,SF2上行干扰电平,SF6上行干扰电平,SF7上行干扰电平,PRB1上行干扰电平,PRB2上行干扰电平,PRB3上行干扰电平,PRB4上行干扰电平,PRB5上行干扰电平,PRB6上行干扰电平,PRB7上行干扰电平,PRB8上行干扰电平,PRB9上行干扰电平,PRB10上行干扰电平,PRB11上行干扰电平,PRB12上行干扰电平,PRB13上行干扰电平,PRB14上行干扰电平,PRB15上行干扰电平,PRB16上行干扰电平,PRB17上行干扰电平,PRB18上行干扰电平,PRB19上行干扰电平,PRB20上行干扰电平,PRB21上行干扰电平,PRB22上行干扰电平,PRB23上行干扰电平,PRB24上行干扰电平,PRB25上行干扰电平,PRB26上行干扰电平,PRB27上行干扰电平,PRB28上行干扰电平,PRB29上行干扰电平,PRB30上行干扰电平,PRB31上行干扰电平,PRB32上行干扰电平,PRB33上行干扰电平,PRB34上行干扰电平,PRB35上行干扰电平,PRB36上行干扰电平,PRB37上行干扰电平,PRB38上行干扰电平,PRB39上行干扰电平,PRB40上行干扰电平,PRB41上行干扰电平,PRB42上行干扰电平,PRB43上行干扰电平,PRB44上行干扰电平,PRB45上行干扰电平,PRB46上行干扰电平,PRB47上行干扰电平,PRB48上行干扰电平,PRB49上行干扰电平,PRB50上行干扰电平,PRB51上行干扰电平,PRB52上行干扰电平,PRB53上行干扰电平,PRB54上行干扰电平,PRB55上行干扰电平,PRB56上行干扰电平,PRB57上行干扰电平,PRB58上行干扰电平,PRB59上行干扰电平,PRB60上行干扰电平,PRB61上行干扰电平,PRB62上行干扰电平,PRB63上行干扰电平,PRB64上行干扰电平,PRB65上行干扰电平,PRB66上行干扰电平,PRB67上行干扰电平,PRB68上行干扰电平,PRB69上行干扰电平,PRB70上行干扰电平,PRB71上行干扰电平,PRB72上行干扰电平,PRB73上行干扰电平,PRB74上行干扰电平,PRB75上行干扰电平,PRB76上行干扰电平,PRB77上行干扰电平,PRB78上行干扰电平,PRB79上行干扰电平,PRB80上行干扰电平,PRB81上行干扰电平,PRB82上行干扰电平,PRB83上行干扰电平,PRB84上行干扰电平,PRB85上行干扰电平,PRB86上行干扰电平,PRB87上行干扰电平,PRB88上行干扰电平,PRB89上行干扰电平,PRB90上行干扰电平,PRB91上行干扰电平,PRB92上行干扰电平,PRB93上行干扰电平,PRB94上行干扰电平,PRB95上行干扰电平,PRB96上行干扰电平,PRB97上行干扰电平,PRB98上行干扰电平,PRB99上行干扰电平,PRB100上行干扰电平,type";
        $return['rows'] = $allData;
        $return['records'] = count($allData);
        return ($return);
    }//end getInterfereCellModel()

    /**
     * 获取baseline参数对比数据
     *
     * @return string
     */
    public function getBaselineCheckData()
    {
        $cell = input::get('cell');
        $city = input::get('city');

        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
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
        // $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', $dbname);
        $cityCh = $dbc->getCHCity($city);
        $subNetwork = $dbc->getSubNets($cityCh);

        $db1 = $dbc->getDB('mongs', 'mongs');
        $sql = "select siteName from siteLte where cellName = '$cell'";
        $rs   = $db1->query($sql);
        $row  = $rs->fetch(PDO::FETCH_NUM);
        $meContext = $row[0];

        $result = array();
        //MRE邻区排名前30的邻区有PCI一二阶冲突
        $filter = " where EutranCellTDD = '$cell'";
        if ($subNetwork != '') {
            $filter = $filter." and subNetwork in (" . $subNetwork . ")";
        }
        //一阶冲突
        $item = array();
        $sql = "select * from TempEUtranCellRelationNeighOfPci".$filter;
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
        }
        array_push($result, $item);
        //二阶冲突
        $item = array();
        $sql = "select * from TempEUtranCellRelationNeighOfNeighPci".$filter;
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
        }
        array_push($result, $item);
        //MME LIST定义不一致
        $item = array();
        $filter = " where meContext = '$meContext'";
        if ($subNetwork != '') {
            $filter = $filter." and subNetwork in (" . $subNetwork . ")";
        }
        $sql = "select * from TempTermPointToMme_S1_MMEGI_dif ".$filter;
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
        }
        array_push($result, $item);
        //X2接口定义不一致
        $item = array();
        $filter = " where meContext = '$meContext'";
        if ($subNetwork != '') {
            $filter = $filter." and subNetwork in (" . $subNetwork . ")";
        }
        //X2 Used IP检查
        $item = array();
        $sql1 = "select * from TempTermPointToENB_ENBID_usedIpAddress".$filter;
        $rs   = $db->query($sql1);
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
        }
        array_push($result, $item);
        //X2-邻区eNbID检查
        $item = array();
        $sql2 = "select * from TempTermPointToENB_IP".$filter;
        $rs   = $db->query($sql2);
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
        }
        array_push($result, $item);
        //如果S1切换失败的占比高于50%且相关邻区ActivePLMNlist为空
        $item = array();
        $filter = " where meContext = '$meContext'";
        if ($subNetwork != '') {
            $filter = $filter." and subNetwork in (" . $subNetwork . ")";
        }
        $sql = "select * from TempExternalEUtranCellTDDActivePlmnListCheck ".$filter;
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
        }
        array_push($result, $item);
        //切换准备失败次数的占比50%以上发生在，邻区外部定义不一致的邻区
        $item = array();
        //获取前三邻区
        $dsn1 = new DataBaseConnection();
        $db1 = $dsn1->getDB('autokpi', 'AutoKPI');
        $sql = "select relation,(准备切换失败数+执行切换失败数) as 总失败数 from NeighBadHandoverCellTableEveryOneHour where cell = '$cell' order by 总失败数 desc limit 3";
        $rs = $db1->query($sql);
        $rows = $rs->fetchall(PDO::FETCH_NUM);
        $relations = [];
        foreach ($rows as $row) {
            array_push($relations, $row[0]);
        }
        $relationStr = implode("','", $relations);
        $relationStr = "('".$relationStr."')";

        $filter = " where meContext = '$meContext' and ExternalEUtranCellTDDId in $relationStr";
        if ($subNetwork != '') {
            $filter = $filter." and subNetwork in (" . $subNetwork . ")";
        }
        $sql = "select * from TempExternalNeigh4G ".$filter;
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
        }
        array_push($result, $item);
        //如果S1切换失败的占比高于50%，相关TAC前三天定义过不同的MMEGI提示发生过TAC割接
        $item = array();
        $item['record'] = 0;
        array_push($result, $item);

        //4G测量频点数量多于5个
        $item = array();
        $filter = " where EutranCellTDD = '$cell' and freqNum > 5";
        if ($subNetwork != '') {
            $filter = $filter." and subNetwork in (" . $subNetwork . ")";
        }
        $sql = "select * from TempMeasuringFrequencyTooMuch ".$filter;
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
        }
        array_push($result, $item);
        //baseline中A类参数配置不一致的
        $item = array();
        $templateId = 53;
        $filter = " where templateId='$templateId' and category = 'A' and subNetwork in (".$subNetwork.") and ( cellId = '$cell' or (meContext = '$meContext' and cellId = ''))";
        $sql = "select count(*) from ParaCheckBaseline".$filter;
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
        }
        array_push($result, $item);
        //内外部SN length检查不一致的
        $item = array();
        $dsn1 = new DataBaseConnection();
        $db1 = $dsn1->getDB('mongs', $dbname);
        $db2 = $dsn1->getDB('mongs', 'mongs');
        $sqlsite = "SELECT siteName FROM siteLte WHERE cellName ='$cell'";
        $res = $db2->query($sqlsite);
        $row1 = $res->fetch(PDO::FETCH_NUM);
        $site = $row1[0];
        $sql1 = "SELECT pdcpSNLength,rlcSNLength FROM QciProfilePredefined WHERE qciProfilePredefinedId='qci1' AND meContext = '$site'";
        // var_dump($sql1);return;
        $res1 = $db1->query($sql1);
        // $row = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
        $row2 = $res1->fetchall(PDO::FETCH_ASSOC);
        if ($row2[0]['pdcpSNLength'] == 12 || $row2[0]['rlcSNLength'] == 10) {
            $item['record'] = 0;
        }
        if ($row2[0]['pdcpSNLength'] != 12 || $row2[0]['rlcSNLength'] != 10) {
            // $row = $res1->fetchall(PDO::FETCH_ASSOC);
            $item['record'] = count($row2);
            $item['content'] = implode(",", array_keys($row2[0]));
            foreach ($row2 as $row) {
                    $item['rows'][] = $row;
            }
        }
        array_push($result, $item);
        //A5频率偏移核查1
        $item = array();
        $dsn1 = new DataBaseConnection();
        $db1 = $dsn1->getDB('mongs', $dbname);
        $db2 = $dsn1->getDB('mongs', 'mongs');
        $sqlsite = "SELECT siteName FROM siteLte WHERE cellName ='$cell'";
        $res = $db2->query($sqlsite);
        $row1 = $res->fetch(PDO::FETCH_NUM);
        $site = $row1[0];
        $sql1 = "SELECT subNetwork,EUtranCellTDD,a5Threshold1RsrpAdjust FROM TempA5Threshold1Rsrp WHERE EUtranCellTDD = '$cell'";
        $res1 = $db1->query($sql1);
        $row2 = $res1->fetchall(PDO::FETCH_ASSOC);
        $item['record'] = count($row2);
        if ($row2) {
            $item['content'] = implode(",", array_keys($row2[0]));
            foreach ($row2 as $row) {
                $item['rows'][] = $row;
            }
        }
        array_push($result, $item);
        //A5频率偏移核查2
        $item = array();
        $dsn1 = new DataBaseConnection();
        $db1 = $dsn1->getDB('mongs', $dbname);
        $db2 = $dsn1->getDB('mongs', 'mongs');
        $sqlsite = "SELECT siteName FROM siteLte WHERE cellName ='$cell'";
        $res = $db2->query($sqlsite);
        $row1 = $res->fetch(PDO::FETCH_NUM);
        $site = $row1[0];
        $sql1 = "SELECT subNetwork,EUtranCellTDD,a5Threshold2RsrpAdjust FROM TempA5Threshold2Rsrp WHERE EUtranCellTDD = '$cell'";
        $res1 = $db1->query($sql1);
        $row2 = $res1->fetchall(PDO::FETCH_ASSOC);
        $item['record'] = count($row2);
        if ($row2) {
            $item['content'] = implode(",", array_keys($row2[0]));
            foreach ($row2 as $row) {
                $item['rows'][] = $row;
            }
        }
        array_push($result, $item);
        //B2频率偏移核查1
        $item = array();
        $dsn1 = new DataBaseConnection();
        $db1 = $dsn1->getDB('mongs', $dbname);
        $db2 = $dsn1->getDB('mongs', 'mongs');
        $sqlsite = "SELECT siteName FROM siteLte WHERE cellName ='$cell'";
        $res = $db2->query($sqlsite);
        $row1 = $res->fetch(PDO::FETCH_NUM);
        $site = $row1[0];
        $sql1 = "SELECT subNetwork,meContext,b2Threshold1RsrpGeranAjust FROM TempB2Threshold1RsrpGeranOffset WHERE meContext = '$site'";
        $res1 = $db1->query($sql1);
        $row2 = $res1->fetchall(PDO::FETCH_ASSOC);
        $item['record'] = count($row2);
        if ($row2) {
            $item['content'] = implode(",", array_keys($row2[0]));
            foreach ($row2 as $row) {
                $item['rows'][] = $row;
            }
        }
        array_push($result, $item);
        //B2频率偏移核查2
        $item = array();
        $dsn1 = new DataBaseConnection();
        $db1 = $dsn1->getDB('mongs', $dbname);
        $db2 = $dsn1->getDB('mongs', 'mongs');
        $sqlsite = "SELECT siteName FROM siteLte WHERE cellName ='$cell'";
        $res = $db2->query($sqlsite);
        $row1 = $res->fetch(PDO::FETCH_NUM);
        $site = $row1[0];
        $sql1 = "SELECT subNetwork,meContext,b2Threshold2GeranAjust FROM TempB2Threshold2GeranOffset WHERE meContext = '$site'";
        $res1 = $db1->query($sql1);
        $row2 = $res1->fetchall(PDO::FETCH_ASSOC);
        $item['record'] = count($row2);
        if ($row2) {
            $item['content'] = implode(",", array_keys($row2[0]));
            foreach ($row2 as $row) {
                $item['rows'][] = $row;
            }
        }
        array_push($result, $item);

        echo json_encode($result);

    }//end getBaselineCheckData()

    /**
     * 获取图形对应表数据
     *
     * @return string
     */
    public function getRrcResultTableData()
    {

        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limits = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $offset = ($page - 1) * $limits;

        $result = [];
        $city = Input::get('city');
        $cell = Input::get('cell');
        date_default_timezone_set('PRC');
        // $date_from = date("Y-m-d",strtotime("-1 day"));
        $date_from = date("Y-m-d");
        $date_to = date("Y-m-d");
        $dsn = new DataBaseConnection();
        $ctrCity = $dsn->getENCtrName($city);
        $db = $dsn->getDB('CTR');
        $sql = 'SHOW DATABASES';
        $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
        $flag = 0;
        foreach ($rows as $row) {
            if ($row['DATABASE'] == $ctrCity) {
                $flag = 1;
            }
        }
        if ($flag == 0) {
            $result['error'] = '数据库不存在！';
            return $result;
        } else {
            $db = $dsn->getDB('mongs', 'mongs');
            $rows = $db->query("SELECT SUBSTRING_INDEX(ecgi,'-',-2) AS ecgi FROM siteLte WHERE cellName = '$cell' LIMIT 1;")->fetchall(PDO::FETCH_ASSOC);
            if (count($rows[0]) == 0) {
                $result['error'] = 'ecgi数据为空！';
                return;
            } else {
                $ecgi = '4600-'.$rows[0]['ecgi'];
            }
            $db = $dsn->getDB('CTR', $ctrCity);
            $sql = 'SHOW TABLES';
            $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if ($row['Tables in ' . $ctrCity] == 'causecode_hour') {
                    $flag = 2;
                }
            }
            if ($flag == 1) {
                $result['error'] = '数据表不存在！';
                return $result;
            } else {
                $rs = $db->query("select count(*) total
                    FROM
                        (SELECT
                            date_id,
                            eventName,
                            ecgi,
                            result,
                            100 * sum(times) / sum(timesTotal) AS ratio,
                            sum(times) times,
                            sum(timesTotal) timesTotal
                        FROM
                            causeCode_hour
                        WHERE
                            (
                                eventName = 'internalProcHoPrepS1Out'
                                OR eventName = 'internalProcHoPrepX2Out'
                            )
                        AND date_id = '$date_to'
                        AND ecgi = '$ecgi'
                        AND result !='EVENT_VALUE_SUCCESSFUL'
                        GROUP BY
                            eventName,
                            result
                        ORDER BY
                            ratio DESC) t");
                if ($rs) {
                    $result["total"] = $rs->fetchcolumn();
                } else {
                    $result['error'] = '没有记录';
                }
                $sql = "SELECT
                            date_id,
                            eventName,
                            ecgi,
                            result,
                            100 * sum(times) / sum(timesTotal) AS ratio,
                            sum(times) times,
                            sum(timesTotal) timesTotal
                        FROM
                            causeCode_hour
                        WHERE
                            (
                                eventName = 'internalProcHoPrepS1Out'
                                OR eventName = 'internalProcHoPrepX2Out'
                            )
                        AND date_id = '$date_to'
                        AND ecgi = '$ecgi'
                        AND result !='EVENT_VALUE_SUCCESSFUL'
                        GROUP BY
                            eventName,
                            result
                        ORDER BY
                            ratio DESC;";
                $rs = $db->query($sql);
                $items = array();
                if ($rs) {
                    $row = $rs->fetchAll(PDO::FETCH_ASSOC);
                    for ($i = $offset; $i < $offset + $limits && $i < count($row); $i++) {
                        $r = $row[$i];
                        $record["date_id"] = $r['date_id'];
                        $record["eventName"] = $r['eventName'];
                        $record["ecgi"] = $r["ecgi"];
                        $record["result"] = $r["result"];
                        $record["times"] = floatval($r["times"]);
                        $record["timesTotal"] = floatval($r["timesTotal"]);
                        $record["ratio"] = number_format(floatval($r["ratio"]), 2);
                        array_push($items, $record);
                    }
                    $result["records"] = $items;
                } else {
                    $result['error'] = '没有记录';
                }
                return $result;
            }
        }
    }//end getRrcResultTableData()

    /**
     * 获得INTERNAL_RRC事件分析数据
     *
     * @return array INTERNAL_RRC事件分析数据
     */
    public function getRrcResult()
    {
        $result = [];
        $categories = [];
        $tooltip = [];
        $yAxis = [];
        $eventName = [];
        $city = Input::get('city');
        $cell = Input::get('cell');
        date_default_timezone_set('PRC');
        // $date_from = date("Y-m-d",strtotime("-1 day"));
        $date_from = date("Y-m-d");
        $date_to = date("Y-m-d");
        $dsn = new DataBaseConnection();
        $ctrCity = $dsn->getENCtrName($city);
        $db = $dsn->getDB('CTR');
        $sql = 'SHOW DATABASES';
        $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
        $flag = 0;
        foreach ($rows as $row) {
            if ($row['DATABASE'] == $ctrCity) {
                $flag = 1;
            }
        }
        if ($flag == 0) {
            $result['error'] = '数据库不存在！';
            return $result;
        } else {
            $db = $dsn->getDB('mongs', 'mongs');
            $rows = $db->query("SELECT SUBSTRING_INDEX(ecgi,'-',-2) AS ecgi FROM siteLte WHERE cellName = '$cell' LIMIT 1;")->fetchall(PDO::FETCH_ASSOC);
            if (count($rows[0]) == 0) {
                $result['error'] = 'ecgi数据为空！';
                return;
            } else {
                $ecgi = '4600-'.$rows[0]['ecgi'];
            }
            $db = $dsn->getDB('CTR', $ctrCity);
            $sql = 'SHOW TABLES';
            $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if ($row['Tables in ' . $ctrCity] == 'causecode_hour') {
                    $flag = 2;
                }
            }
            if ($flag == 1) {
                $result['error'] = '数据表不存在！';
                return $result;
            } else {
                $sql = "SELECT
                        SUM(times) AS total
                    FROM
                        causeCode_hour
                    WHERE
                        (eventName = 'internalProcHoPrepS1Out' or eventName = 'internalProcHoPrepX2Out')
                    AND date_id = '$date_to'
                    AND ecgi = '$ecgi'
                    AND result !='EVENT_VALUE_SUCCESSFUL';";
                                        // print_r($sql);return;
                $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
                $total = intval($rows[0]['total']);
                $rows = [];
                $sql = "SELECT
                        date_id,
                        eventName,
                        ecgi,
                        result,
                        /*100 * sum(times)/sum(timesTotal) AS ratio */
                        sum(times) AS times,
                        100 * sum(times)/$total AS ratio
                    FROM
                        causeCode_hour
                    WHERE
                        (eventName = 'internalProcHoPrepS1Out' or eventName = 'internalProcHoPrepX2Out')
                    AND date_id = '$date_to'
                    AND ecgi = '$ecgi'
                    AND result !='EVENT_VALUE_SUCCESSFUL'
                    GROUP BY eventName,result
                    ORDER BY ratio DESC;";
                $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    array_push($tooltip, intval($row['times']));
                    array_push($categories, $row['result']);
                    array_push($yAxis, [$row['eventName'],round($row['ratio'], 2)]);
                    array_push($eventName, $row['eventName']);
                }
            }
        }
        if (count($categories) == 0) {
            $result['error'] = '数据为空！';
        } else {
            $result['ecgi'] = $ecgi;
            $result['categories'] = $categories;
            $result['yAxis'] = $yAxis;
            $result['eventName'] = $eventName;
            $result['tooltip'] = implode('/', $tooltip);
            $result['date'] = $date_from;
            $result['error'] = 0;
        }
        return ($result);
    }//end getRrcResult()

    /**
     * 获得RRC事件详情名
     *
     * @return array
     */
    public function getRrcResultDetailTableField()
    {
        $city = Input::get('city');
        $dsn = new DataBaseConnection();
        $ctrCity = $dsn->getENCtrName($city);
        $db = $dsn->getDB('CTR', $ctrCity);
        date_default_timezone_set('PRC');
        $date_from = date("Y-m-d", strtotime("-1 day"));
        $ecgi = Input::get('ecgi');
        $result = Input::get('result');
        $table = Input::get('table');
        $query = "select * from " . $table . " limit 1";
        $rs = $db->query($query, PDO::FETCH_ASSOC);
        if ($rs) {
            $rs = $rs->fetchAll();
            if (count($rs) > 0) {
                return $rs[0];
            } else {
                $result = array();
                $result['result'] = 'error';
                return $result;
            }
        } else {
            $result = array();
            $result['result'] = 'error';
            return $result;
        }
    }//end getRrcResultDetailTableField()

    /**
     * 获得RRC事件详情值
     *
     * @return array
     */
    public function getRrcResultDetailData()
    {   
        $displayStart = Input::get('page');
        $displayLength = Input::get('limit');
        $offset = ($displayStart - 1) * $displayLength;
        $limit = '';
        $filter = '';
        $limit = " limit $offset,$displayLength ";

        $city = Input::get('city');
        $dsn = new DataBaseConnection();
        $ctrCity = $dsn->getENCtrName($city);
        $db = $dsn->getDB('CTR', $ctrCity);
        date_default_timezone_set('PRC');
        $date_from = date("Y-m-d", strtotime("-1 day"));
        $ecgi = Input::get('ecgi');
        $result = Input::get('result');
        $table = Input::get('table');
        $filter = " where date_id='$date_from' and hour_id in (0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23)
            and imsi=0 and result='$result' and ecgi='$ecgi'  ";
        $result = array();
        $sqlCount = "select count(*) from " . $table . $filter;
        $rs = $db->query($sqlCount, PDO::FETCH_ASSOC);
        $result["total"] = $rs->fetchColumn();
        $sql = "select * from $table $filter $limit";
        $rs = $db->query($sql, PDO::FETCH_OBJ);
        $res = $rs->fetchAll();
        $items = array();
        if ($res) {
            foreach ($res as $row) {
                array_push($items, $row);
            }
            $result["records"] = $res;
        }
        return $result;
    }//end getRrcResultDetailData()

    /**
     * 导出RRC事件详细
     *
     * @return array
     */
    public function exportRrcResultDetail()
    {
        $city = Input::get('city');
        $dsn = new DataBaseConnection();
        $ctrCity = $dsn->getENCtrName($city);
        $db = $dsn->getDB('CTR', $ctrCity);
        date_default_timezone_set('PRC');
        $date_from = date("Y-m-d", strtotime("-1 day"));
        $ecgi = Input::get('ecgi');
        $resultValue = Input::get('result');
        $table = Input::get('table');
        $fileName = "files/" . $resultValue . "_" . date('YmdHis') . ".csv";
        $filter = " where date_id='$date_from' and hour_id in (0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23)
            and imsi=0 and result='$resultValue' and ecgi='$ecgi'";
        $sql = "select * from $table $filter";
        $rs = $db->query($sql, PDO::FETCH_ASSOC);
        $result = array();
        if ($rs) {
            $items = $rs->fetchAll();
            if (count($items) > 0) {
                $row = $items[0];
                $column = implode(",", array_keys($row));
                $fileUtil = new FileUtil();
                $fileUtil->resultToCSV2($column, $items, $fileName);
                $result['fileName'] = $fileName;
                $result['result'] = true;
            } else {
                $result['result'] = false;
            }
        } else {
            $result['result'] = false;
        }
        return $result;
    }//end exportRrcResultDetail()

    /**
     * 获取图形对应表数据-执行后
     *
     * @return string
     */
    public function getExecResultTableData()
    {

        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limits = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $offset = ($page - 1) * $limits;

        $result = [];
        $city = Input::get('city');
        $cell = Input::get('cell');
        date_default_timezone_set('PRC');
        // $date_from = date("Y-m-d",strtotime("-1 day"));
        $date_from = date("Y-m-d");
        $date_to = date("Y-m-d");
        $dsn = new DataBaseConnection();
        $ctrCity = $dsn->getENCtrName($city);
        $db = $dsn->getDB('CTR');
        $sql = 'SHOW DATABASES';
        $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
        $flag = 0;
        foreach ($rows as $row) {
            if ($row['DATABASE'] == $ctrCity) {
                $flag = 1;
            }
        }
        if ($flag == 0) {
            $result['error'] = '数据库不存在！';
            return $result;
        } else {
            $db = $dsn->getDB('mongs', 'mongs');
            $rows = $db->query("SELECT SUBSTRING_INDEX(ecgi,'-',-2) AS ecgi FROM siteLte WHERE cellName = '$cell' LIMIT 1;")->fetchall(PDO::FETCH_ASSOC);
            if (count($rows[0]) == 0) {
                $result['error'] = 'ecgi数据为空！';
                return;
            } else {
                $ecgi = '4600-'.$rows[0]['ecgi'];
            }
            $db = $dsn->getDB('CTR', $ctrCity);
            $sql = 'SHOW TABLES';
            $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if ($row['Tables in ' . $ctrCity] == 'causecode_hour') {
                    $flag = 2;
                }
            }
            if ($flag == 1) {
                $result['error'] = '数据表不存在！';
                return $result;
            } else {
                $rs = $db->query("select count(*) total
                    FROM
                        (SELECT
                            date_id,
                            eventName,
                            ecgi,
                            result,
                            100 * sum(times) / sum(timesTotal) AS ratio,
                            sum(times) times,
                            sum(timesTotal) timesTotal
                        FROM
                            causeCode_hour
                        WHERE
                            (
                                eventName = 'internalProcHoExecS1Out'
                                OR eventName = 'internalProcHoExecX2Out'
                            )
                        AND date_id = '$date_to'
                        AND ecgi = '$ecgi'
                        AND result !='EVENT_VALUE_SUCCESSFUL'
                        GROUP BY
                            eventName,
                            result
                        ORDER BY
                            ratio DESC) t");
                if ($rs) {
                    $result["total"] = $rs->fetchcolumn();
                } else {
                    $result['error'] = '没有记录';
                }
                $sql = "SELECT
                            date_id,
                            eventName,
                            ecgi,
                            result,
                            100 * sum(times) / sum(timesTotal) AS ratio,
                            sum(times) times,
                            sum(timesTotal) timesTotal
                        FROM
                            causeCode_hour
                        WHERE
                            (
                                eventName = 'internalProcHoExecS1Out'
                                OR eventName = 'internalProcHoExecX2Out'
                            )
                        AND date_id = '$date_to'
                        AND ecgi = '$ecgi'
                        AND result !='EVENT_VALUE_SUCCESSFUL'
                        GROUP BY
                            eventName,
                            result
                        ORDER BY
                            ratio DESC;";
                $rs = $db->query($sql);
                $items = array();
                if ($rs) {
                    $row = $rs->fetchAll(PDO::FETCH_ASSOC);
                    for ($i = $offset; $i < $offset + $limits && $i < count($row); $i++) {
                        $r = $row[$i];
                        $record["date_id"] = $r['date_id'];
                        $record["eventName"] = $r['eventName'];
                        $record["ecgi"] = $r["ecgi"];
                        $record["result"] = $r["result"];
                        $record["times"] = floatval($r["times"]);
                        $record["timesTotal"] = floatval($r["timesTotal"]);
                        $record["ratio"] = number_format(floatval($r["ratio"]), 2);
                        array_push($items, $record);
                    }
                    $result["records"] = $items;
                } else {
                    $result['error'] = '没有记录';
                }
                return $result;
            }
        }
    }//end getExecResultTableData()

    /**
     * 获得INTERNAL_RRC事件分析数据
     *
     * @return array INTERNAL_RRC事件分析数据-执行后
     */
    public function getExecResult()
    {
        $result = [];
        $categories = [];
        $tooltip = [];
        $yAxis = [];
        $eventName = [];
        $city = Input::get('city');
        $cell = Input::get('cell');
        date_default_timezone_set('PRC');
        // $date_from = date("Y-m-d",strtotime("-1 day"));
        $date_from = date("Y-m-d");
        $date_to = date("Y-m-d");
        $dsn = new DataBaseConnection();
        $ctrCity = $dsn->getENCtrName($city);
        $db = $dsn->getDB('CTR');
        $sql = 'SHOW DATABASES';
        $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
        $flag = 0;
        foreach ($rows as $row) {
            if ($row['DATABASE'] == $ctrCity) {
                $flag = 1;
            }
        }
        if ($flag == 0) {
            $result['error'] = '数据库不存在！';
            return $result;
        } else {
            $db = $dsn->getDB('mongs', 'mongs');
            $rows = $db->query("SELECT SUBSTRING_INDEX(ecgi,'-',-2) AS ecgi FROM siteLte WHERE cellName = '$cell' LIMIT 1;")->fetchall(PDO::FETCH_ASSOC);
            if (count($rows[0]) == 0) {
                $result['error'] = 'ecgi数据为空！';
                return;
            } else {
                $ecgi = '4600-'.$rows[0]['ecgi'];
            }
            $db = $dsn->getDB('CTR', $ctrCity);
            $sql = 'SHOW TABLES';
            $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if ($row['Tables in ' . $ctrCity] == 'causecode_hour') {
                    $flag = 2;
                }
            }
            if ($flag == 1) {
                $result['error'] = '数据表不存在！';
                return $result;
            } else {
                $sql = "SELECT
                        SUM(times) AS total
                    FROM
                        causeCode_hour
                    WHERE
                        (eventName = 'internalProcHoExecS1Out' or eventName = 'internalProcHoExecX2Out')
                    AND date_id = '$date_to'
                    AND ecgi = '$ecgi'
                    AND result !='EVENT_VALUE_SUCCESSFUL';";
                                        // print_r($sql);return;
                $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
                $total = intval($rows[0]['total']);
                $rows = [];
                $sql = "SELECT
                        date_id,
                        eventName,
                        ecgi,
                        result,
                        /*100 * sum(times)/sum(timesTotal) AS ratio */
                        sum(times) AS times,
                        100 * sum(times)/$total AS ratio
                    FROM
                        causeCode_hour
                    WHERE
                        (eventName = 'internalProcHoExecS1Out' or eventName = 'internalProcHoExecX2Out')
                    AND date_id = '$date_to'
                    AND ecgi = '$ecgi'
                    AND result !='EVENT_VALUE_SUCCESSFUL'
                    GROUP BY eventName,result
                    ORDER BY ratio DESC;";
                $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    array_push($tooltip, intval($row['times']));
                    array_push($categories, $row['result']);
                    array_push($yAxis, [$row['eventName'],round($row['ratio'], 2)]);
                    array_push($eventName, $row['eventName']);
                }
            }
        }
        if (count($categories) == 0) {
            $result['error'] = '数据为空！';
        } else {
            $result['ecgi'] = $ecgi;
            $result['categories'] = $categories;
            $result['yAxis'] = $yAxis;
            $result['eventName'] = $eventName;
            $result['tooltip'] = implode('/', $tooltip);
            $result['date'] = $date_from;
            $result['error'] = 0;
        }
        return ($result);
    }//end getExecResult()

    /**
     * 获得RRC事件详情名
     *
     * @return array
     */
    public function getExecResultDetailTableField()
    {
        $city = Input::get('city');
        $dsn = new DataBaseConnection();
        $ctrCity = $dsn->getENCtrName($city);
        $db = $dsn->getDB('CTR', $ctrCity);
        date_default_timezone_set('PRC');
        $date_from = date("Y-m-d", strtotime("-1 day"));
        $ecgi = Input::get('ecgi');
        $result = Input::get('result');
        $table = Input::get('table');
        $query = "select * from " . $table . " limit 1";
        $rs = $db->query($query, PDO::FETCH_ASSOC);
        if ($rs) {
            $rs = $rs->fetchAll();
            if (count($rs) > 0) {
                return $rs[0];
            } else {
                $result = array();
                $result['result'] = 'error';
                return $result;
            }
        } else {
            $result = array();
            $result['result'] = 'error';
            return $result;
        }
    }//end getExecResultDetailTableField()

    /**
     * 获得RRC事件详情值
     *
     * @return array
     */
    public function getExecResultDetailData()
    {
        $displayStart = Input::get('page');
        $displayLength = Input::get('limit');
        $offset = ($displayStart - 1) * $displayLength;
        $limit = '';
        $filter = '';
        $limit = " limit $offset,$displayLength ";

        $city = Input::get('city');
        $dsn = new DataBaseConnection();
        $ctrCity = $dsn->getENCtrName($city);
        $db = $dsn->getDB('CTR', $ctrCity);
        date_default_timezone_set('PRC');
        $date_from = date("Y-m-d", strtotime("-1 day"));
        $ecgi = Input::get('ecgi');
        $result = Input::get('result');
        $table = Input::get('table');
        $filter = " where date_id='$date_from' and hour_id in (0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23)
            and imsi=0 and result='$result' and ecgi='$ecgi'  ";
        $result = array();
        $sqlCount = "select count(*) from " . $table . $filter;
        $rs = $db->query($sqlCount, PDO::FETCH_ASSOC);
        $result["total"] = $rs->fetchColumn();
        $sql = "select * from $table $filter $limit";
        $rs = $db->query($sql, PDO::FETCH_OBJ);
        $res = $rs->fetchAll();
        $items = array();
        if ($res) {
            foreach ($res as $row) {
                array_push($items, $row);
            }
            $result["records"] = $res;
        }
        return $result;
    }//end getExecResultDetailData()

    /**
     * 导出RRC事件详细
     *
     * @return array
     */
    public function exportExecResultDetail()
    {
        $city = Input::get('city');
        $dsn = new DataBaseConnection();
        $ctrCity = $dsn->getENCtrName($city);
        $db = $dsn->getDB('CTR', $ctrCity);
        date_default_timezone_set('PRC');
        $date_from = date("Y-m-d", strtotime("-1 day"));
        $ecgi = Input::get('ecgi');
        $resultValue = Input::get('result');
        $table = Input::get('table');
        $fileName = "files/" . $resultValue . "_" . date('YmdHis') . ".csv";
        $filter = " where date_id='$date_from' and hour_id in (0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23)
            and imsi=0 and result='$resultValue' and ecgi='$ecgi'";
        $sql = "select * from $table $filter";
        $rs = $db->query($sql, PDO::FETCH_ASSOC);
        $result = array();
        if ($rs) {
            $items = $rs->fetchAll();
            if (count($items) > 0) {
                $row = $items[0];
                $column = implode(",", array_keys($row));
                $fileUtil = new FileUtil();
                $fileUtil->resultToCSV2($column, $items, $fileName);
                $result['fileName'] = $fileName;
                $result['result'] = true;
            } else {
                $result['result'] = false;
            }
        } else {
            $result['result'] = false;
        }
        return $result;
    }//end exportExecResultDetail()

    /* 获取邻区关系表
         *
         * @return array
         */
    public function getNeighBadHandoverCellTable()
    {
        $cell = Input::get('cell');
        date_default_timezone_set('PRC');
        $day=date("Y-m-d");
        $hour=input::get('hour');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $dbs = $dsn->getDB('mongs', 'mongs');
        $dba = $dsn->getDB('alarm', 'Alarm');
        $table = "NeighBadHandoverCellTableEveryOneHour";
        $result = array();
        $hours=date('H');
        $dates=$hours>=11?new DateTime():new DateTime('-1 day');
        $kget='kget'.$dates->format('ymd');
        $sql="select 
        a.relation,
        a.day_id,
        a.hour_id,
        b.latitudeBD,
        b.longitudeBD,
        a.`切换成功率`,
        a.`准备切换失败数`,
        a.`执行切换失败数`,
        a.`准备切换成功率`,
        a.`执行切换成功率`,
         a.`同频切换成功率`,
         a.`异频切换成功率`,
         t.cellName,
         (a.`准备切换失败数`+a.`执行切换失败数`) as sum1,
         t.pci,
         e.earfcn
         from 
         NeighBadHandoverCellTableEveryOneHour a 
         LEFT JOIN $kget.site t on a.relation=t.ecgi
         LEFT JOIN 
         mongs.siteLte b 
        on a.relation=b.ecgi   
        Left JOIN $kget.EUtranCellTDD e 
        on t.cellName=e.EUtranCellTDDId     
         WHERE a.cell='$cell' and a.hour_id=$hour and a.day_id='$day' and (a.`执行切换失败数`+a.`准备切换失败数`)>0 and (a.`执行切换失败数`>0 or a.`准备切换失败数`>0) 
        GROUP BY a.relation
        ORDER BY sum1 desc,a.`执行切换失败数` desc";
        $rs = $db->query($sql, PDO::FETCH_ASSOC);
        $ress = $rs->fetchall();
         // print_r($sql);
        if ($ress) {
            $res=array();
        foreach ($ress as $key => $value) {
            if ($value['cellName']==null) {
                    $sql="select cellName,pci,earfcn from siteLte where ecgi ='".$value['relation']."'";
                    $rs=$dbs->query($sql, PDO::FETCH_ASSOC)->fetchAll();
                if ($rs) {
                    $value['pci']=$rs[0]['pci'];
                    $value['earfcn']=$rs[0]['earfcn'];
                    $value['cellName']=$rs[0]['cellName'];
                } else {
                    $array=array();
                    $array=explode('-', $value['relation']);
                    $end=array_pop($array);
                if (strlen($end)<=3) {
                    $second=array_pop($array);
                    $value['cellName']=$second.'-'.$end;
                
                    } else {
                        $value['cellName']=$end;
                }
                       $sql="SELECT
                            t.cellName,
                            a.latitudeBD,
                            a.longitudeBD,
                            t.pci,
                            t.cellName as ts,
                            e.earfcn
                        FROM
                            siteLte a,$kget.site t ,$kget.EUtranCellTDD e 
                        WHERE
                            a.cellName = '".$value['cellName']."' 
                            and t.cellName='".$value['cellName']."' 
                            and e.EUtranCellTDDId='".$value['cellName']."'";
                    $rs=$dbs->query($sql, PDO::FETCH_ASSOC)->fetchAll();
                    // print_r($rs);
                    if ($rs) {
                        $value['latitudeBD']=$rs[0]['latitudeBD'];
                        $value['longitudeBD']=$rs[0]['longitudeBD'];
                        $value['pci']=$rs[0]['pci'];
                        $value['earfcn']=$rs[0]['earfcn'];
                    } else {
                        $value['cellName']=$value['relation'];
                    }

                }
                
            }
           if ($value['latitudeBD']==null&&$value['longitudeBD']==null) {
                $sql="select latitudeBD,longitudeBD,pci,earfcn from siteLte where cellName='".$value['cellName']."'";
                $sql="select latitudeBD,longitudeBD from siteLte where cellName='".$value['cellName']."'";
                $rs=$dbs->query($sql, PDO::FETCH_ASSOC)->fetchAll();
                // print_r($rs);
                if ($rs) {
                    $value['latitudeBD']=$rs[0]['latitudeBD'];
                    $value['longitudeBD']=$rs[0]['longitudeBD'];
                }
            }
            array_push($res, $value);
        }

        $items = array();
        $sum=0;
        $sqls2="select latitudeBD,longitudeBD from siteLte where cellName='$cell'";
                $rss2=$dbs->query($sqls2, PDO::FETCH_ASSOC);
                if ($rss2) {
                    $ress=$rss2->fetchall();
                    $lat1=$ress[0]['latitudeBD'];
                    $lng1=$ress[0]['longitudeBD'];
                    // print_r($ress);
                }

        if ($res) {
            foreach ($res as $row) {
                $sum+=$row['sum1'];
            }
            $prb='平均PRB干扰';
            $rrc='最高RRC连接用户数';
            $warn='告警数据';
            
               
            // print_r($sum);
            foreach ($res as $row) {

                if ($sum) {
                    $sqls3="select 最大RRC连接用户数 from badHandoverCell where cell='".$row['cellName']."' and day_id='".$row['day_id']."' and hour_id='".$row['hour_id']."'";
                    $sqls4="select SP_text from Alarm.FMA_alarm_list where eutranCell = '".$row['cellName']."'";
                    if ($row['sum1'] / $sum >0.2) {
                        $rss3 = $db->query($sqls3, PDO::FETCH_ASSOC);
             
                        if ($ress3=$rss3->fetchall()) {
                           $row[$rrc]=round($ress3[0]['最大RRC连接用户数']);
                        }
                        $rss4 = $dba->query($sqls4, PDO::FETCH_ASSOC);
                        
                        if ($ress4=$rss4->fetchall()) {
                            $row[$warn]=$ress4[0]['SP_text'];
                             } else {
                                $row[$warn]='无';
                             }

                        $row[$prb]=$this->getNumOfDiagnosisDataFilter_AvgPRB($row['cellName'], $row['day_id'], $row['hour_id'], $db);
                        
                    } else {
                        $row[$prb]='';
                        $row[$rrc]='';
                        $row[$warn]='';
                    }
                }
                $pci = 'PCI/EARFCN';
                if ($row['pci']!=null&&$row['earfcn']!=null) {
                    $row['PCI/EARFCN']=$row['pci'].'/'.$row['earfcn'];
                }
                $lat2=$row['latitudeBD'];
                $lng2=$row['longitudeBD'];
                    // print_r($ress1);
                if ($lat2&&$lng2) {
                    
               // print_r($row->准备切换成功率);
                  $row['distance']=round(6378.138*2*asin(sqrt(pow(sin( ($lat1*pi()/180-$lat2*pi()/180)/2), 2)+cos($lat1*pi()/180)*cos($lat2*pi()/180)* pow(sin( ($lng1*pi()/180-$lng2*pi()/180)/2), 2)))*1000).'米';
                }
                  
                        array_push($items, $row);
            }
            // print_r($row);
         //相同的PCI/EARFCN优先显示
            $new=array();
            $b= count($items);
            for ($i=0;$i<$b;$i++) {
                for ($j=$i+1;$j<$b;$j++) { 
                    if ($items[$i]['pci']==$items[$j]['pci'] &&$items[$i]['earfcn']==$items[$j]['earfcn']) {
                        if (in_array($items[$i], $new) ) {
                          if (in_array($items[$j], $new)) {

                          } else {
                             array_push($new, $items[$j]);  
                          }
                        } else {
                        array_push($new, $items[$i]);
                        array_push($new, $items[$j]);  
                        }
                        
                  } 
               
                }
            }

            foreach ($items as $key=>$value) {
                for ($i=0;$i<count($new);$i++) {
                    if ($value['pci']==$new[$i]['pci'] && $value['earfcn']==$new[$i]['earfcn']) {
                        unset($items[$key]);
                    }
                }
            }//end 相同的PCI/EARFCN优先显示
             $new=array_merge($new, $items);

            $result["records"] = $new;
            }
        } else {
            $result['result']='error';
        }
        $result["text"] = "day_id,hour_id,cellName,distance,PCI/EARFCN,平均PRB干扰,最高RRC连接用户数,告警数据,切换成功率,准备切换失败数,执行切换失败数,准备切换成功率,执行切换成功率,同频切换成功率,异频切换成功率";
        return $result;
    }//end getNeighBadHandoverCellTable()

       //干扰--平均PRB
    public function getNumOfDiagnosisDataFilter_AvgPRB($cell, $date, $hour, $pdo)
    {
        $sql = "SELECT  PRB1上行干扰电平,
                        PRB2上行干扰电平,                
                        PRB3上行干扰电平,                
                        PRB4上行干扰电平,                
                        PRB5上行干扰电平,                
                        PRB6上行干扰电平,                
                        PRB7上行干扰电平,                
                        PRB8上行干扰电平,                
                        PRB9上行干扰电平,                
                        PRB10上行干扰电平,
                        PRB11上行干扰电平,
                        PRB12上行干扰电平,
                        PRB13上行干扰电平,
                        PRB14上行干扰电平,
                        PRB15上行干扰电平,
                        PRB16上行干扰电平,
                        PRB17上行干扰电平,
                        PRB18上行干扰电平,
                        PRB19上行干扰电平,
                        PRB20上行干扰电平,
                        PRB21上行干扰电平,
                        PRB22上行干扰电平,
                        PRB23上行干扰电平,
                        PRB24上行干扰电平,
                        PRB25上行干扰电平,
                        PRB26上行干扰电平,
                        PRB27上行干扰电平,
                        PRB28上行干扰电平,
                        PRB29上行干扰电平,
                        PRB30上行干扰电平,
                        PRB31上行干扰电平,
                        PRB32上行干扰电平,
                        PRB33上行干扰电平,
                        PRB34上行干扰电平,
                        PRB35上行干扰电平,
                        PRB36上行干扰电平,
                        PRB37上行干扰电平,
                        PRB38上行干扰电平,
                        PRB39上行干扰电平,
                        PRB40上行干扰电平,
                        PRB41上行干扰电平,
                        PRB42上行干扰电平,
                        PRB43上行干扰电平,
                        PRB44上行干扰电平,
                        PRB45上行干扰电平,
                        PRB46上行干扰电平,
                        PRB47上行干扰电平,
                        PRB48上行干扰电平,
                        PRB49上行干扰电平,
                        PRB50上行干扰电平,
                        PRB51上行干扰电平,
                        PRB52上行干扰电平,
                        PRB53上行干扰电平,
                        PRB54上行干扰电平,
                        PRB55上行干扰电平,
                        PRB56上行干扰电平,
                        PRB57上行干扰电平,
                        PRB58上行干扰电平,
                        PRB59上行干扰电平,
                        PRB60上行干扰电平,
                        PRB61上行干扰电平,
                        PRB62上行干扰电平,
                        PRB63上行干扰电平,
                        PRB64上行干扰电平,
                        PRB65上行干扰电平,
                        PRB66上行干扰电平,
                        PRB67上行干扰电平,
                        PRB68上行干扰电平,
                        PRB69上行干扰电平,
                        PRB70上行干扰电平,
                        PRB71上行干扰电平,
                        PRB72上行干扰电平,
                        PRB73上行干扰电平,
                        PRB74上行干扰电平,
                        PRB75上行干扰电平,
                        PRB76上行干扰电平,
                        PRB77上行干扰电平,
                        PRB78上行干扰电平,
                        PRB79上行干扰电平,
                        PRB80上行干扰电平,
                        PRB81上行干扰电平,
                        PRB82上行干扰电平,
                        PRB83上行干扰电平,
                        PRB84上行干扰电平,
                        PRB85上行干扰电平,
                        PRB86上行干扰电平,
                        PRB87上行干扰电平,
                        PRB88上行干扰电平,
                        PRB89上行干扰电平,
                        PRB90上行干扰电平,
                        PRB91上行干扰电平,
                        PRB92上行干扰电平,
                        PRB93上行干扰电平,
                        PRB94上行干扰电平,
                        PRB95上行干扰电平,
                        PRB96上行干扰电平,
                        PRB97上行干扰电平,
                        PRB98上行干扰电平,
                        PRB99上行干扰电平,
                        PRB100上行干扰电平
            FROM
                interfereCell
            WHERE
                cell = '$cell'
            AND day_id >= '$date'
            AND hour_id = '$hour';";
           
        $row = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);  

        // print_r($row);
        if (!$row) {
            return '空';
        }
        $j = 100;
        $avg = 0;
        foreach ($row[0] as $key => $value) {
   
            if ($value==null) {
                $j--;
                continue;
            }
            $avg = $avg + $value;
        }
        if ($j==0) {
            return '空';
            
        } else {
            $avg=$avg/$j;
            return round($avg, 2);
           
        }
    } 
        /**
     * 获得小区的干扰信息
     */
    public function getGanraoCellChart()
    {   
        $cell = input::get('cell');
        $db   = new DataBaseConnection();
        $conn = $db->getDB('autokpi', 'AutoKPI');
        $startTime = date("Y-m-d", strtotime("-1 day"));
        $endTime = date("Y-m-d");
        $categories = [];

        $ganrao = $conn->query("SELECT day_id,hour_id,
                    AVG(PRB1上行干扰电平) AS PRB1上行干扰电平,
                    AVG(PRB2上行干扰电平) AS PRB2上行干扰电平,
                    AVG(PRB3上行干扰电平) AS PRB3上行干扰电平,
                    AVG(PRB4上行干扰电平) AS PRB4上行干扰电平,
                    AVG(PRB5上行干扰电平) AS PRB5上行干扰电平,
                    AVG(PRB6上行干扰电平) AS PRB6上行干扰电平,
                    AVG(PRB7上行干扰电平) AS PRB7上行干扰电平,
                    AVG(PRB8上行干扰电平) AS PRB8上行干扰电平,
                    AVG(PRB9上行干扰电平) AS PRB9上行干扰电平,
                    AVG(PRB10上行干扰电平) AS PRB10上行干扰电平,
                    AVG(PRB11上行干扰电平) AS PRB11上行干扰电平,
                    AVG(PRB12上行干扰电平) AS PRB12上行干扰电平,
                    AVG(PRB13上行干扰电平) AS PRB13上行干扰电平,
                    AVG(PRB14上行干扰电平) AS PRB14上行干扰电平,
                    AVG(PRB15上行干扰电平) AS PRB15上行干扰电平,
                    AVG(PRB16上行干扰电平) AS PRB16上行干扰电平,
                    AVG(PRB17上行干扰电平) AS PRB17上行干扰电平,
                    AVG(PRB18上行干扰电平) AS PRB18上行干扰电平,
                    AVG(PRB19上行干扰电平) AS PRB19上行干扰电平,
                    AVG(PRB20上行干扰电平) AS PRB20上行干扰电平,
                    AVG(PRB21上行干扰电平) AS PRB21上行干扰电平,
                    AVG(PRB22上行干扰电平) AS PRB22上行干扰电平,
                    AVG(PRB23上行干扰电平) AS PRB23上行干扰电平,
                    AVG(PRB24上行干扰电平) AS PRB24上行干扰电平,
                    AVG(PRB25上行干扰电平) AS PRB25上行干扰电平,
                    AVG(PRB26上行干扰电平) AS PRB26上行干扰电平,
                    AVG(PRB27上行干扰电平) AS PRB27上行干扰电平,
                    AVG(PRB28上行干扰电平) AS PRB28上行干扰电平,
                    AVG(PRB29上行干扰电平) AS PRB29上行干扰电平,
                    AVG(PRB30上行干扰电平) AS PRB30上行干扰电平,
                    AVG(PRB31上行干扰电平) AS PRB31上行干扰电平,
                    AVG(PRB32上行干扰电平) AS PRB32上行干扰电平,
                    AVG(PRB33上行干扰电平) AS PRB33上行干扰电平,
                    AVG(PRB34上行干扰电平) AS PRB34上行干扰电平,
                    AVG(PRB35上行干扰电平) AS PRB35上行干扰电平,
                    AVG(PRB36上行干扰电平) AS PRB36上行干扰电平,
                    AVG(PRB37上行干扰电平) AS PRB37上行干扰电平,
                    AVG(PRB38上行干扰电平) AS PRB38上行干扰电平,
                    AVG(PRB39上行干扰电平) AS PRB39上行干扰电平,
                    AVG(PRB40上行干扰电平) AS PRB40上行干扰电平,
                    AVG(PRB41上行干扰电平) AS PRB41上行干扰电平,
                    AVG(PRB42上行干扰电平) AS PRB42上行干扰电平,
                    AVG(PRB43上行干扰电平) AS PRB43上行干扰电平,
                    AVG(PRB44上行干扰电平) AS PRB44上行干扰电平,
                    AVG(PRB45上行干扰电平) AS PRB45上行干扰电平,
                    AVG(PRB46上行干扰电平) AS PRB46上行干扰电平,
                    AVG(PRB47上行干扰电平) AS PRB47上行干扰电平,
                    AVG(PRB48上行干扰电平) AS PRB48上行干扰电平,
                    AVG(PRB49上行干扰电平) AS PRB49上行干扰电平,
                    AVG(PRB50上行干扰电平) AS PRB50上行干扰电平,
                    AVG(PRB51上行干扰电平) AS PRB51上行干扰电平,
                    AVG(PRB52上行干扰电平) AS PRB52上行干扰电平,
                    AVG(PRB53上行干扰电平) AS PRB53上行干扰电平,
                    AVG(PRB54上行干扰电平) AS PRB54上行干扰电平,
                    AVG(PRB55上行干扰电平) AS PRB55上行干扰电平,
                    AVG(PRB56上行干扰电平) AS PRB56上行干扰电平,
                    AVG(PRB57上行干扰电平) AS PRB57上行干扰电平,
                    AVG(PRB58上行干扰电平) AS PRB58上行干扰电平,
                    AVG(PRB59上行干扰电平) AS PRB59上行干扰电平,
                    AVG(PRB60上行干扰电平) AS PRB60上行干扰电平,
                    AVG(PRB61上行干扰电平) AS PRB61上行干扰电平,
                    AVG(PRB62上行干扰电平) AS PRB62上行干扰电平,
                    AVG(PRB63上行干扰电平) AS PRB63上行干扰电平,
                    AVG(PRB64上行干扰电平) AS PRB64上行干扰电平,
                    AVG(PRB65上行干扰电平) AS PRB65上行干扰电平,
                    AVG(PRB66上行干扰电平) AS PRB66上行干扰电平,
                    AVG(PRB67上行干扰电平) AS PRB67上行干扰电平,
                    AVG(PRB68上行干扰电平) AS PRB68上行干扰电平,
                    AVG(PRB69上行干扰电平) AS PRB69上行干扰电平,
                    AVG(PRB70上行干扰电平) AS PRB70上行干扰电平,
                    AVG(PRB71上行干扰电平) AS PRB71上行干扰电平,
                    AVG(PRB72上行干扰电平) AS PRB72上行干扰电平,
                    AVG(PRB73上行干扰电平) AS PRB73上行干扰电平,
                    AVG(PRB74上行干扰电平) AS PRB74上行干扰电平,
                    AVG(PRB75上行干扰电平) AS PRB75上行干扰电平,
                    AVG(PRB76上行干扰电平) AS PRB76上行干扰电平,
                    AVG(PRB77上行干扰电平) AS PRB77上行干扰电平,
                    AVG(PRB78上行干扰电平) AS PRB78上行干扰电平,
                    AVG(PRB79上行干扰电平) AS PRB79上行干扰电平,
                    AVG(PRB80上行干扰电平) AS PRB80上行干扰电平,
                    AVG(PRB81上行干扰电平) AS PRB81上行干扰电平,
                    AVG(PRB82上行干扰电平) AS PRB82上行干扰电平,
                    AVG(PRB83上行干扰电平) AS PRB83上行干扰电平,
                    AVG(PRB84上行干扰电平) AS PRB84上行干扰电平,
                    AVG(PRB85上行干扰电平) AS PRB85上行干扰电平,
                    AVG(PRB86上行干扰电平) AS PRB86上行干扰电平,
                    AVG(PRB87上行干扰电平) AS PRB87上行干扰电平,
                    AVG(PRB88上行干扰电平) AS PRB88上行干扰电平,
                    AVG(PRB89上行干扰电平) AS PRB89上行干扰电平,
                    AVG(PRB90上行干扰电平) AS PRB90上行干扰电平,
                    AVG(PRB91上行干扰电平) AS PRB91上行干扰电平,
                    AVG(PRB92上行干扰电平) AS PRB92上行干扰电平,
                    AVG(PRB93上行干扰电平) AS PRB93上行干扰电平,
                    AVG(PRB94上行干扰电平) AS PRB94上行干扰电平,
                    AVG(PRB95上行干扰电平) AS PRB95上行干扰电平,
                    AVG(PRB96上行干扰电平) AS PRB96上行干扰电平,
                    AVG(PRB97上行干扰电平) AS PRB97上行干扰电平,
                    AVG(PRB98上行干扰电平) AS PRB98上行干扰电平,
                    AVG(PRB99上行干扰电平) AS PRB99上行干扰电平,
                    AVG(PRB100上行干扰电平) AS PRB100上行干扰电平
                FROM
                    interfereCell
                WHERE
                    cell = '" . $cell . "'
                AND day_id >= '" . $startTime . "'
                AND day_id <= '" . $endTime . "'
                GROUP BY day_id,hour_id;");
        while ($rows=$ganrao->fetch(PDO::FETCH_NUM)) {
            $num=0;
            $j=100;
            for ($i=2;$i<102;$i++) {
                if ($rows[$i]==null) {
                    $j--;
                    continue;
                }
                $num=$num+$rows[$i];
            }
            if ($j==0) {
                $avg=null;
            } else {
                $avg=round($num/$j, 2);
            }
            $key="'".$rows[0].' '.$rows[1].':00'."'";
            $data[$key]=$avg;
        }
        if ($data) {
            $result['key']=implode(',', array_keys($data));
            $result['data']=implode(',', $data);
            $result['result']='success';           
            echo json_encode($result);
        } else {
            $result['result']='error';
            echo json_encode($result);
        }
      

    }
    /**
     * 获得单小区指标趋势时序数据
     *
     * @return string 单小区指标趋势时序
     */
    public function getIndexChartData()
    {
        $dbn = new DataBaseConnection();
        $conn = $dbn->getDB('autokpi', 'AutoKPI');
        if ($conn == null) {
            echo 'Could not connect';
        }
        $table = Input::get('table');

        $cell = Input::get('rowCell');
        date_default_timezone_set('PRC');
        $startTime = date("Y-m-d", strtotime("-1 day"));
        $endTime = date("Y-m-d");
        $yAxis_name_left_arr = Input::get('yAxis_name_left');
        $yAxis_name_left = implode(',', $yAxis_name_left_arr);
        $yAxis_name_right_arr = Input::get('yAxis_name_right');

        $categories = [];
        $time = date("Y-m-d", strtotime("-1 day"));
        $timestart = $time . ' 00:00:00';

        $timeend=date("Y-m-d").' '.input::get('hour');
        $timeend = $timeend . ":00:00";

        $strtimestart = strtotime($timestart);
        $strtimeend = strtotime($timeend);

        while ($strtimeend >= $strtimestart) {
            $date = date("Y-m-d", $strtimestart);
            $hour = date("H", $strtimestart);
            if ($hour < 10) {
                $hour = $hour + 0;
            }
            $time = $date .' '.$hour.':00';
            array_push($categories, $time);
            $strtimestart = $strtimestart + 3600;
        }

        $yAxis_name_right = '';
        $yAxis_name_right_1 = '';
        $yAxis_1 = array();
        $series_1 = array();
        $yAxis_name_right = $yAxis_name_right_arr[0];

        if (count($yAxis_name_right_arr) == 2) {
            $yAxis_name_right = $yAxis_name_right_arr[0];
            $yAxis_name_right_1 = $yAxis_name_right_arr[1];
            $sql = "select day_id,hour_id,`RSRQ<-15.5的比例`," . $yAxis_name_left ." from " . $table . " where day_id>='" . $startTime . "' and day_id<='" . $endTime . "' and cell='" . $cell . "' ORDER BY day_id,hour_id";
            $res_1 = $conn->query($sql);
        }


        if (strcmp($startTime, $endTime) == 0) {
            $res = $conn->query("select day_id,hour_id," . $yAxis_name_left . "," . $yAxis_name_right . " from " . $table . " where day_id='" . $endTime . "' and cell='" . $cell . "' ORDER BY day_id,hour_id");
        } else if (strcmp($startTime, $endTime) < 0) {
            $res = $conn->query("select day_id,hour_id," . $yAxis_name_left . " from " . $table . " where day_id>='" . $startTime . "' and day_id<='" . $endTime . "' and cell='" . $cell . "' ORDER BY day_id,hour_id");

            $res_ganrao = $conn->query("SELECT day_id,hour_id,
                    AVG(PRB1上行干扰电平) AS PRB1上行干扰电平,
                    AVG(PRB2上行干扰电平) AS PRB2上行干扰电平,
                    AVG(PRB3上行干扰电平) AS PRB3上行干扰电平,
                    AVG(PRB4上行干扰电平) AS PRB4上行干扰电平,
                    AVG(PRB5上行干扰电平) AS PRB5上行干扰电平,
                    AVG(PRB6上行干扰电平) AS PRB6上行干扰电平,
                    AVG(PRB7上行干扰电平) AS PRB7上行干扰电平,
                    AVG(PRB8上行干扰电平) AS PRB8上行干扰电平,
                    AVG(PRB9上行干扰电平) AS PRB9上行干扰电平,
                    AVG(PRB10上行干扰电平) AS PRB10上行干扰电平,
                    AVG(PRB11上行干扰电平) AS PRB11上行干扰电平,
                    AVG(PRB12上行干扰电平) AS PRB12上行干扰电平,
                    AVG(PRB13上行干扰电平) AS PRB13上行干扰电平,
                    AVG(PRB14上行干扰电平) AS PRB14上行干扰电平,
                    AVG(PRB15上行干扰电平) AS PRB15上行干扰电平,
                    AVG(PRB16上行干扰电平) AS PRB16上行干扰电平,
                    AVG(PRB17上行干扰电平) AS PRB17上行干扰电平,
                    AVG(PRB18上行干扰电平) AS PRB18上行干扰电平,
                    AVG(PRB19上行干扰电平) AS PRB19上行干扰电平,
                    AVG(PRB20上行干扰电平) AS PRB20上行干扰电平,
                    AVG(PRB21上行干扰电平) AS PRB21上行干扰电平,
                    AVG(PRB22上行干扰电平) AS PRB22上行干扰电平,
                    AVG(PRB23上行干扰电平) AS PRB23上行干扰电平,
                    AVG(PRB24上行干扰电平) AS PRB24上行干扰电平,
                    AVG(PRB25上行干扰电平) AS PRB25上行干扰电平,
                    AVG(PRB26上行干扰电平) AS PRB26上行干扰电平,
                    AVG(PRB27上行干扰电平) AS PRB27上行干扰电平,
                    AVG(PRB28上行干扰电平) AS PRB28上行干扰电平,
                    AVG(PRB29上行干扰电平) AS PRB29上行干扰电平,
                    AVG(PRB30上行干扰电平) AS PRB30上行干扰电平,
                    AVG(PRB31上行干扰电平) AS PRB31上行干扰电平,
                    AVG(PRB32上行干扰电平) AS PRB32上行干扰电平,
                    AVG(PRB33上行干扰电平) AS PRB33上行干扰电平,
                    AVG(PRB34上行干扰电平) AS PRB34上行干扰电平,
                    AVG(PRB35上行干扰电平) AS PRB35上行干扰电平,
                    AVG(PRB36上行干扰电平) AS PRB36上行干扰电平,
                    AVG(PRB37上行干扰电平) AS PRB37上行干扰电平,
                    AVG(PRB38上行干扰电平) AS PRB38上行干扰电平,
                    AVG(PRB39上行干扰电平) AS PRB39上行干扰电平,
                    AVG(PRB40上行干扰电平) AS PRB40上行干扰电平,
                    AVG(PRB41上行干扰电平) AS PRB41上行干扰电平,
                    AVG(PRB42上行干扰电平) AS PRB42上行干扰电平,
                    AVG(PRB43上行干扰电平) AS PRB43上行干扰电平,
                    AVG(PRB44上行干扰电平) AS PRB44上行干扰电平,
                    AVG(PRB45上行干扰电平) AS PRB45上行干扰电平,
                    AVG(PRB46上行干扰电平) AS PRB46上行干扰电平,
                    AVG(PRB47上行干扰电平) AS PRB47上行干扰电平,
                    AVG(PRB48上行干扰电平) AS PRB48上行干扰电平,
                    AVG(PRB49上行干扰电平) AS PRB49上行干扰电平,
                    AVG(PRB50上行干扰电平) AS PRB50上行干扰电平,
                    AVG(PRB51上行干扰电平) AS PRB51上行干扰电平,
                    AVG(PRB52上行干扰电平) AS PRB52上行干扰电平,
                    AVG(PRB53上行干扰电平) AS PRB53上行干扰电平,
                    AVG(PRB54上行干扰电平) AS PRB54上行干扰电平,
                    AVG(PRB55上行干扰电平) AS PRB55上行干扰电平,
                    AVG(PRB56上行干扰电平) AS PRB56上行干扰电平,
                    AVG(PRB57上行干扰电平) AS PRB57上行干扰电平,
                    AVG(PRB58上行干扰电平) AS PRB58上行干扰电平,
                    AVG(PRB59上行干扰电平) AS PRB59上行干扰电平,
                    AVG(PRB60上行干扰电平) AS PRB60上行干扰电平,
                    AVG(PRB61上行干扰电平) AS PRB61上行干扰电平,
                    AVG(PRB62上行干扰电平) AS PRB62上行干扰电平,
                    AVG(PRB63上行干扰电平) AS PRB63上行干扰电平,
                    AVG(PRB64上行干扰电平) AS PRB64上行干扰电平,
                    AVG(PRB65上行干扰电平) AS PRB65上行干扰电平,
                    AVG(PRB66上行干扰电平) AS PRB66上行干扰电平,
                    AVG(PRB67上行干扰电平) AS PRB67上行干扰电平,
                    AVG(PRB68上行干扰电平) AS PRB68上行干扰电平,
                    AVG(PRB69上行干扰电平) AS PRB69上行干扰电平,
                    AVG(PRB70上行干扰电平) AS PRB70上行干扰电平,
                    AVG(PRB71上行干扰电平) AS PRB71上行干扰电平,
                    AVG(PRB72上行干扰电平) AS PRB72上行干扰电平,
                    AVG(PRB73上行干扰电平) AS PRB73上行干扰电平,
                    AVG(PRB74上行干扰电平) AS PRB74上行干扰电平,
                    AVG(PRB75上行干扰电平) AS PRB75上行干扰电平,
                    AVG(PRB76上行干扰电平) AS PRB76上行干扰电平,
                    AVG(PRB77上行干扰电平) AS PRB77上行干扰电平,
                    AVG(PRB78上行干扰电平) AS PRB78上行干扰电平,
                    AVG(PRB79上行干扰电平) AS PRB79上行干扰电平,
                    AVG(PRB80上行干扰电平) AS PRB80上行干扰电平,
                    AVG(PRB81上行干扰电平) AS PRB81上行干扰电平,
                    AVG(PRB82上行干扰电平) AS PRB82上行干扰电平,
                    AVG(PRB83上行干扰电平) AS PRB83上行干扰电平,
                    AVG(PRB84上行干扰电平) AS PRB84上行干扰电平,
                    AVG(PRB85上行干扰电平) AS PRB85上行干扰电平,
                    AVG(PRB86上行干扰电平) AS PRB86上行干扰电平,
                    AVG(PRB87上行干扰电平) AS PRB87上行干扰电平,
                    AVG(PRB88上行干扰电平) AS PRB88上行干扰电平,
                    AVG(PRB89上行干扰电平) AS PRB89上行干扰电平,
                    AVG(PRB90上行干扰电平) AS PRB90上行干扰电平,
                    AVG(PRB91上行干扰电平) AS PRB91上行干扰电平,
                    AVG(PRB92上行干扰电平) AS PRB92上行干扰电平,
                    AVG(PRB93上行干扰电平) AS PRB93上行干扰电平,
                    AVG(PRB94上行干扰电平) AS PRB94上行干扰电平,
                    AVG(PRB95上行干扰电平) AS PRB95上行干扰电平,
                    AVG(PRB96上行干扰电平) AS PRB96上行干扰电平,
                    AVG(PRB97上行干扰电平) AS PRB97上行干扰电平,
                    AVG(PRB98上行干扰电平) AS PRB98上行干扰电平,
                    AVG(PRB99上行干扰电平) AS PRB99上行干扰电平,
                    AVG(PRB100上行干扰电平) AS PRB100上行干扰电平
                FROM
                    interfereCell
                WHERE
                    cell = '" . $cell . "'
                AND day_id >= '" . $startTime . "'
                AND day_id <= '" . $endTime . "'
                GROUP BY day_id,hour_id;");
        }
        $yAxis = array();
        $yAxis_2 = array();
        $items = array();
        $returnData = array();
        $series = array();
        $series_2 = array();

        $yAxis_l_RRC = array();
        $series_l_RRC = array();
        $yAxis_l_ERAB = array();
        $series_l_ERAB = array();

        $yAxis_2 = [];
        $arr_yAxis_2 = [];
        while ($rows = $res_ganrao->fetch(PDO::FETCH_NUM)) {
            $time = strval(strval($rows[0]) . " " . strval($rows[1])) . ":00";
            $time = mb_convert_encoding($time, 'gb2312', 'utf-8');

            $num = 0;
            $j = 100;
            for ($i=2; $i < 102 ; $i++) {
                if ($rows[$i] == null) {
                    $j--;
                    continue;
                }
                $num = $num + $rows[$i];
            }
            if ($j == 0) {
                $avg = null;
            } else {
                $avg = $num / $j;
            }
            $arr_yAxis_2[$time] = $avg;
        }//return;
        for ($i=0; $i<count($categories); $i++) {
            $key = $categories[$i];
            if (array_key_exists($key, $arr_yAxis_2)) {
                array_push($yAxis_2, round($arr_yAxis_2[$key], 2));
            } else {
                array_push($yAxis_2, null);
            }
        }

        $arr_yAxis = [];
        $arr_yAxis_l_RRC = [];
        $arr_yAxis_l_ERAB = [];
        while ($line = $res->fetch(PDO::FETCH_NUM)) {
            $time = strval(strval($line[0]) . " " . strval($line[1])) . ":00";
            $time = mb_convert_encoding($time, 'gb2312', 'utf-8');
            $arr_yAxis[$time] =  $line[2];
            $arr_yAxis_l_RRC[$time] = $line[3];
            $arr_yAxis_l_ERAB[$time] = $line[4];
        }
        for ($i=0; $i<count($categories); $i++) {
            $key = $categories[$i];
            if (array_key_exists($key, $arr_yAxis)) {
                array_push($yAxis, $arr_yAxis[$key]);
                array_push($yAxis_l_RRC, $arr_yAxis_l_RRC[$key]);
                array_push($yAxis_l_ERAB, $arr_yAxis_l_ERAB[$key]);
            } else {
                array_push($yAxis, null);
                array_push($yAxis_l_RRC, null);
                array_push($yAxis_l_ERAB, null);
            }
        }

        $series_2['name'] = $yAxis_name_right;      //干扰
        $series_2['color'] = '#4572A7';
        $series_2['type'] = 'spline3';
        $series_2['yAxis'] = 1;
        $series_2['data'] = $yAxis_2;
        array_push($items, $series_2);

        $arr_yAxis_1 = [];
        if (count($yAxis_name_right_arr) == 2) {    //质差
            while ($line_1 = $res_1->fetch(PDO::FETCH_NUM)) {
                $time = strval(strval($line_1[0]) . " " . strval($line_1[1])) . ":00";
                $time = mb_convert_encoding($time, 'gb2312', 'utf-8');
                $arr_yAxis_1[$time] =  $line_1[2];
            }

            for ($i=0; $i<count($categories); $i++) {
                $key = $categories[$i];
                if (array_key_exists($key, $arr_yAxis_1)) {
                    array_push($yAxis_1, $arr_yAxis_1[$key]);
                } else {
                    array_push($yAxis_1, null);
                }
            }

            $series_1['name'] = $yAxis_name_right_1;
            $series_1['color'] = '#87CEFF';
            $series_1['type'] = 'spline4';
            $series_1['data'] = $yAxis_1;
            array_push($items, $series_1);
        }

        $series['name'] = $yAxis_name_left_arr[0];  //无线掉线率
        $series['color'] = '#89A54E';
        $series['type'] = 'spline5';
        $series['data'] = $yAxis;
        $series['yAxis'] = 2;
        array_push($items, $series);

        $series_l_RRC['name'] = $yAxis_name_left_arr[1];  //RRC建立成功率
        $series_l_RRC['color'] = '#F4A460';
        $series_l_RRC['type'] = 'spline1';
        $series_l_RRC['data'] = $yAxis_l_RRC;
        $series_l_RRC['yAxis'] = 2;
        array_push($items, $series_l_RRC);

        $series_l_ERAB['name'] = $yAxis_name_left_arr[2];  //ERAB建立成功率
        $series_l_ERAB['color'] = '#D1EEEE';
        $series_l_ERAB['type'] = 'spline2';
        $series_l_ERAB['data'] = $yAxis_l_ERAB;
        $series_l_ERAB['yAxis'] = 2;
        array_push($items, $series_l_ERAB);


        $returnData['categories'] = $categories;
        $returnData['series'] = $items;

        $yAxis_set = array();


        if (count($yAxis_name_right_arr) == 2) {
            // $yAxis_set_1['gridLineWidth'] = 0;
            $yAxis_set_1['labels'] = "{'format':'{value}','style':{color: '#87CEFF'}}";
            $yAxis_set_1['title'] = "{'text':'" . $yAxis_name_right_1  ."(%)". "','style':{color: '#87CEFF'}}";
            $yAxis_set_1['opposite'] = true;
            array_push($yAxis_set, $yAxis_set_1);
        } else {
            $yAxis_name_right_1 = '';
            $yAxis_set_1['labels'] = "{'format':'{value}','style':{color: '#87CEFF'}}";
            $yAxis_set_1['title'] = "{'text':'" . $yAxis_name_right_1 . "','style':{color: '#87CEFF'}}";
            $yAxis_set_1['opposite'] = true;
            array_push($yAxis_set, $yAxis_set_1);
        }
        // $yAxis_set_3['gridLineWidth'] = 0;
        $yAxis_set_3['labels'] = "{'format':'{value}','style':{color: '#4572A7'}}";
        $yAxis_set_3['title'] = "{'text':'" . $yAxis_name_right ."(dBm)". "','style':{color: '#4572A7'}}";
        $yAxis_set_3['opposite'] = true;
        array_push($yAxis_set, $yAxis_set_3);

        $yAxis_set_2['labels'] = "{'format':'{value}%','style':{color: '#AAAAAA'}}";
        $yAxis_set_2['title'] = "{'text':'" . $yAxis_name_left . "','style':{color: '#AAAAAA'}}";
        $yAxis_set_2['max'] = 100;
        $yAxis_set_2['min'] = 0;
        array_push($yAxis_set, $yAxis_set_2);

        $returnData['yAxis_set'] = $yAxis_set;

        $returnData['cell'] = $cell;
        echo json_encode($returnData);
    }//end getIndexChartData()

    /**
     * 获得单个高掉线小区指标数据
     *
     * @return string 单个高掉线小区指标数据
     */
    public function getIndexTableData()
    {
        $cell = Input::get('rowCell');
        date_default_timezone_set('PRC');
        $startTime = date("Y-m-d", strtotime("-1 day"));
        $endTime = date("Y-m-d");
        $table = Input::get('table');

        $cityArr = Input::get('city');
        $city = array();
        $cityPY = new DataBaseConnection();
        foreach ($cityArr as $citys) {
            $cityStr = $cityPY->getCityByCityChinese($citys)[0]->connName;
            array_push($city, $cityStr);
        }

        if ($cell == '') {
            if (strcmp($startTime, $endTime) == 0) {
                $filter = " where day_id='" . $startTime . "'";
            } else if (strcmp($startTime, $endTime) < 0) {
                $filter = " where day_id>='" . $startTime . "' and day_id<='" . $endTime . "'";
            }
        } else {
            if (strcmp($startTime, $endTime) == 0) {
                $filter = " where day_id='" . $endTime . "' and cell='" . $cell . "'";
            } else if (strcmp($startTime, $endTime) < 0) {
                $filter = " where day_id>='" . $startTime . "' and day_id<='" . $endTime . "' and cell='" . $cell . "'";
            }
        }

        $sql = "SELECT day_id,hour_id,city,subNetwork,cell,切换成功率,准备切换成功率,执行切换成功率,准备切换成功数,准备切换失败数,准备切换尝试数,执行切换成功数,执行切换失败数,执行切换尝试数,异频准备切换失败数,异频执行切换失败数,同频准备切换失败数,同频执行切换失败数 FROM badHandoverCell $filter";
        $item = array();

        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);

        $item['record'] = count($rows);
        $item['content'] = "day_id,hour_id,city,subNetwork,cell,切换成功率,准备切换成功率,执行切换成功率,准备切换成功数,准备切换失败数,准备切换尝试数,执行切换成功数,执行切换失败数,执行切换尝试数,异频准备切换失败数,异频执行切换失败数,同频准备切换失败数,同频执行切换失败数";
        foreach ($rows as $row) {
            $item['rows'][] = $row;
        }

        $i = 0;
        foreach ($item['rows'] as $value) {
            $item['rows'][$i]['hour_id'] = floatval($value['hour_id']);
            $i++;
        }

        $filename = "common/files/" . $table . date('YmdHis') . ".csv";
        $item['filename'] = $filename;
        $this->resultToCSV2($item, $filename);

        echo json_encode($item);
    }//end getIndexTableData()

    /**
     * 获取小区相关邻区
     *
     * @return json
     */
    public function getNeighborCellMapData()
    {
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('mongs', 'mongs');
        $sql = "select * from siteLte where cellName = '$cell'";
        $res = $db->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $ecgi = $row['ecgi'];
        $return = array();
        //目标小区
        $return['cell'] = $row;

        $cityEN = Input::get('city');
        $cityCH = $this->encityToCHcity($cityEN);
        $dbname = $this->getMRDatabase($cityCH);
        date_default_timezone_set('PRC'); 
        $dateTime = date("Y-m-d", strtotime("-1 day"));

        //获取缺失邻区
        $db = $dsn->getDB('MR', $dbname);
        // $sql = "select * from mreServeNeigh_day where isdefined_direct=0 and ecgi = '$ecgi' AND distance_direct<0.8 and dateId >= '$dateTime'";
        $sql = "/*!mycat:sql=select 1 from MRS*/
                SELECT
                    s.longitudeBD,s.latitudeBD,s.dir,s.band,s.cellName
                FROM
                    mreServeNeigh_day AS m
                LEFT JOIN `GLOBAL`.siteLte AS s ON m.ecgiNeigh_direct = s.ecgi
                WHERE
                    m.isdefined_direct = 0
                AND m.ecgi = '$ecgi'
                AND m.distance_direct < 0.8
                AND m.dateId >= '$dateTime'";
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $return['lostNeigh'] = $rows;

        //获取已有邻区
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $sql = "select slongitude,slatitude,sdir,sband,scell from SysRelation_cell_day where day_id = '$dateTime' and cell = '$cell' and slongitude is not null and slatitude is not null and sdir is not null and sband is not null";
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $return['Neigh'] = $rows;

        return ($return);
    }
}
