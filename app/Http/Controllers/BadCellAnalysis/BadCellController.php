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
 * 坏小区处理
 * Class BadCellController
 *
 * @category BadCellAnalysis
 * @package  App\Http\Controllers\BadCellAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class BadCellController extends Controller
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
        // $filesName = [];
        // $erbsArr = [];
        // $filename = input::get('point');
        // array_push($filesName, $filename);
        // $city = Input::get('city');
        // $cell = Input::get('cell');
        // $rrcEst  = Input::get('rrc');
        // $erabEst = Input::get('erab');
        // $rows = SiteLte::where('cellName', $cell)->get();
        // $erab = '';
        // foreach ($rows as $row) {
        //     $erab = $row->siteName;
        // }
        // array_push($erbsArr, $erab);       
        // $dbc = new DataBaseConnection();
        // $conn = $dbc->getCtrConn($city);
        // $strServer = $conn['strServer'];
        // $strServerPort = $conn['strServerPort'];
        // $strServerUsername = $conn['strServerUsername'];
        // $strServerPassword = $conn['strServerPassword'];
        // $fileDir = $conn['fileDir'];
        // $resConnection     = ssh2_connect($strServer, $strServerPort);
        // $idNum         = 1;
        // $allCtr        = array();
        // $ctrTime       = array();
        // $childrengz    = array();
        // $allChildrengz = array();
        // $succFilesName = array();
        // if (ssh2_auth_password($resConnection, $strServerUsername, $strServerPassword)) {
        //     $resSFTP = ssh2_sftp($resConnection);
        //     $file    = scandir("ssh2.sftp://".intval($resSFTP).$fileDir);
        //     foreach ($filesName as $fileName) {
        //         foreach ($file as $value) {
        //             if ($value == '.' || $value == '..' || $fileName != $value) {
        //                 continue;
        //             } else {
        //                 array_push($succFilesName, $fileName);
        //                 $ctrTime['id']      = $idNum;
        //                 $ctrTime['kpiName'] = $value;
        //                 $ctrTime['RRC失败次数'] = $rrcEst;
        //                 $ctrTime['ERAB失败次数'] = $erabEst; 
        //                 $idNum++;
        //             }
        //             array_push($allCtr, $ctrTime);
        //         }
        //     }
        //     // gz压缩包匹配
        //     $idNum = 1;
        //     foreach ($succFilesName as $succFileName) {
        //         $childrenId = 1;
        //         $dirsgz     = "ssh2.sftp://".intval($resSFTP).$fileDir."/$succFileName";
        //         $filesgz    = $this->getFile($dirsgz);
        //         foreach ($filesgz as $filegz) {
        //             foreach ($erbsArr as $erb) {
        //                 $filePos = strpos($filegz, $erb);
        //                 if ($filePos == false) {
        //                     continue;
        //                 } else {
        //                     $allChildrengz['id']      = $idNum.$childrenId;
        //                     $allChildrengz['kpiName'] = str_replace($dirsgz, '', $filegz);                      
        //                     $allChildrengz['size'] = filesize($filegz).' B';
        //                     $childrenId++;
        //                     array_push($childrengz, $allChildrengz);
        //                 }
        //             }
        //         }

        //         $num = ($idNum - 1);
        //         $allCtr[$num]['children'] = $childrengz;
        //         $childrengz = array();

        //         $idNum++;
        //     }//end foreach
        // }//end if
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
        // var_dump($erbsArr);return;
        // $dbc = new DataBaseConnection();
        // $conn = $dbc->getCtrConn($city);
        // $strServer = $conn['strServer'];
        // $strServerPort = $conn['strServerPort'];
        // $strServerUsername = $conn['strServerUsername'];
        // $strServerPassword = $conn['strServerPassword'];
        // $fileDir = $conn['fileDir'];
        // var_dump($remoteIp);var_dump($type);var_dump($city);return;
        $rows =TraceServerInfo::where("type", $type)->where("city", $city)->get()->toArray();
        // var_dump($rows);return;
        $remoteIp = $rows[0]["ipAddress"];
        // var_dump($remoteIp);return;
        $fileDir = $rows[0]['fileDir'];
        $ftpUserName = $rows[0]['ftpUserName'];
        $ftpPassword = $rows[0]['ftpPassword'];
        Config::set("filesystems.disks.ftp.host", $remoteIp);
        Config::set("filesystems.disks.ftp.username", $ftpUserName);
        Config::set("filesystems.disks.ftp.password", $ftpPassword);
        // $resConnection     = ssh2_connect($strServer, $strServerPort);

        $idNum         = 1;
        $allCtr        = array();
        $ctrTime       = array();
        $childrengz    = array();
        $allChildrengz = array();
        $succFilesName = array();

        // if (ssh2_auth_password($resConnection, $strServerUsername, $strServerPassword)) {
        //     $resSFTP = ssh2_sftp($resConnection);
        //     $file    = scandir("ssh2.sftp://".intval($resSFTP).$fileDir);
        //     foreach ($filesName as $fileName) {
        //         foreach ($file as $value) {
        //             if ($value == '.' || $value == '..' || $fileName != $value) {
        //                 continue;
        //             } else {
        //                 array_push($succFilesName, $fileName);
        //                 $ctrTime['id']      = $idNum;
        //                 $ctrTime['kpiName'] = $value;
        //                 $ctrTime['准备切换失败数'] = $rrcEst;
        //                 $ctrTime['执行切换失败数'] = $erabEst; 
        //                 $idNum++;
        //             }

        //             array_push($allCtr, $ctrTime);
        //         }
        //     }
        $file = storage::disk("ftp")->directories($type."/autobackup/");
        // var_dump($file);return;
        // var_dump($filesName);return;
        foreach ($filesName as $fileName) {
            foreach ($file as $value) {
                if ($fileName != explode("/", $value)[count(explode("/", $value))-1]) {
                    continue;
                } else {
                    array_push($succFilesName, $fileName);
                    $ctrTime['id']      = $idNum;
                    $ctrTime['kpiName'] = $value;
                    $ctrTime['RRC失败次数'] = $rrcEst;
                    $ctrTime['ERAB失败次数'] = $erabEst; 
                    $idNum++;
                }
                array_push($allCtr, $ctrTime);
            }
        }
        // var_dump($succFilesName);return;
        $idNum = 1;
        foreach ($succFilesName as $succFileName) {
            $childrenId = 1;
            $dirsgz     = $type."/autobackup/".$succFileName;
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

    /**
     * 查询坏小区列表(分页)
     *
     * @return string JSON格式坏小区列表
     */
    public function templateQuery()
    {
        $startTime = Input::get('startTime');
        $endTime = Input::get('endTime');
        $cell = Input::get('cell');
        $table = Input::get('table');
        $hours = Input::get('hour');
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
        $cityFilter .= ")";
        $dbn = new DataBaseConnection();
        // $conn = $dbn->getConnDB('mongs');
        $conn = $dbn->getDB('mongs', 'AutoKPI');
        if ($conn == null) {
            die('Could not connect');
        }
        // var_dump($cityFilter);return;
        if (count($cityArr) == 0) {
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
        } else {
            if ($cell == '') {
                if (strcmp($startTime, $endTime) == 0) {
                    $filter = " where day_id='" . $endTime . "' and " . $cityFilter;
                } else if (strcmp($startTime, $endTime) < 0) {
                    $filter = " where day_id>='" . $startTime . "' and day_id<='" . $endTime . "' and " . $cityFilter;
                }
            } else {
                if (strcmp($startTime, $endTime) == 0) {
                    $filter = " where day_id='" . $endTime . "' and cell='" . $cell . "' and " . $cityFilter;
                } else if (strcmp($startTime, $endTime) < 0) {
                    $filter = " where day_id>='" . $startTime . "' and day_id<='" . $endTime . "' and cell='" . $cell . "' and " . $cityFilter;
                }
            }
        }

        if ($hours != '') {
            $hour = implode(',', $hours);
            $filter = $filter . " AND hour_id in($hour)";
        }
        // mysql_select_db('AutoKPI', $conn);
        $result = array();
        $result1 = array();
        if ($table == 'lowAccessCell_ex') {
            // $rs = $conn->query("SELECT a.id, a.city, a.subNetwork, a.cell, b.`前天小时数`, c.`昨天小时数`, d.`今天小时数`, a.`RRC建立失败次数`, a.`ERAB建立失败次数`, c.`昨天小时数` + d.`今天小时数` - b.`前天小时数` AS 小时数 FROM (SELECT id,   city,   subNetwork,   cell,   sum(RRC建立失败次数) AS RRC建立失败次数,   sum(ERAB建立失败次数) AS ERAB建立失败次数  FROM   lowAccessCell_ex  WHERE $cityFilter AND  day_id >= DATE_ADD(    DATE_FORMAT(NOW(), '%Y-%m-%d'),    INTERVAL - 2 DAY   )  AND day_id <= DATE_FORMAT(NOW(), '%Y-%m-%d')  GROUP BY   subNetwork,   cell ) a LEFT JOIN ( SELECT  cell,  COUNT(*) AS 前天小时数 FROM  lowAccessCell_ex WHERE  $cityFilter AND  day_id = DATE_ADD(   DATE_FORMAT(NOW(), '%Y-%m-%d'),   INTERVAL - 2 DAY  ) GROUP BY  subNetwork,  cell) b ON a.cell = b.cell LEFT JOIN ( SELECT  cell,  COUNT(*) AS 昨天小时数 FROM  lowAccessCell_ex WHERE  $cityFilter AND  day_id = DATE_ADD(   DATE_FORMAT(NOW(), '%Y-%m-%d'),   INTERVAL - 1 DAY  ) GROUP BY  subNetwork,  cell) c ON a.cell = c.cell LEFT JOIN ( SELECT  cell,  COUNT(*) AS 今天小时数 FROM  lowAccessCell_ex WHERE  $cityFilter AND  day_id = DATE_FORMAT(NOW(), '%Y-%m-%d') GROUP BY  subNetwork,  cell) d ON a.cell = d.cell WHERE d.`今天小时数` !=0 ORDER BY 小时数 DESC  LIMIT 30;");
            $rs = $conn->query("SELECT * FROM LowAccessCellTableEveryOneHour WHERE $cityFilter;");
            /*$rs = $conn->query(
                "SELECT
                    a.id,
                    a.city,
                    a.subNetwork,
                    a.cell,
                    e.hour_id,
                    b.`前天小时数`,
                    c.`昨天小时数`,
                    d.`今天小时数`,
                    e.`RRC建立失败次数(最新)`,
                    e.`ERAB建立失败次数(最新)`,
                    a.`RRC建立失败次数(总)`,
                    a.`ERAB建立失败次数(总)`,
                    d.`RRC建立失败次数(今日)`,
                    d.`ERAB建立失败次数(今日)`,
                    CASE WHEN c.`昨天小时数` IS NULL THEN 0 ELSE c.`昨天小时数` END + 
                    CASE WHEN d.`今天小时数` IS NULL THEN 0 ELSE d.`今天小时数` END - 
                    CASE WHEN b.`前天小时数` IS NULL THEN 0 ELSE b.`前天小时数` END + (a.`RRC建立失败次数(总)` + a.`ERAB建立失败次数(总)`)/1000 AS 严重程度
                FROM
                    (
                        SELECT
                            id,
                            city,
                            subNetwork,
                            cell,
                            sum(RRC建立失败次数) AS `RRC建立失败次数(总)`,
                            sum(ERAB建立失败次数) AS `ERAB建立失败次数(总)`
                        FROM
                            lowAccessCell_ex
                        WHERE
                            city = 'changzhou'
                        AND cell IN (SELECT DISTINCT cell FROM lowAccessCell_ex WHERE day_id=DATE_FORMAT(NOW(), '%Y-%m-%d') AND hour_id=(SELECT hour_id FROM lowAccessCell_ex ORDER BY id DESC LIMIT 1))
                        AND day_id >= DATE_ADD(
                            DATE_FORMAT(NOW(), '%Y-%m-%d'),
                            INTERVAL - 2 DAY
                        )
                        AND day_id <= DATE_FORMAT(NOW(), '%Y-%m-%d')
                        GROUP BY
                            subNetwork,
                            cell
                    ) a
                LEFT JOIN (
                    SELECT
                        cell,
                        COUNT(*) AS 前天小时数
                    FROM
                        lowAccessCell_ex
                    WHERE
                        city = 'changzhou'
                    AND cell IN (SELECT DISTINCT cell FROM lowAccessCell_ex WHERE day_id=DATE_FORMAT(NOW(), '%Y-%m-%d') AND hour_id=(SELECT hour_id FROM lowAccessCell_ex ORDER BY id DESC LIMIT 1))
                    AND day_id = DATE_ADD(
                        DATE_FORMAT(NOW(), '%Y-%m-%d'),
                        INTERVAL - 2 DAY
                    )
                    GROUP BY
                        subNetwork,
                        cell
                ) b ON a.cell = b.cell
                LEFT JOIN (
                    SELECT
                        cell,
                        COUNT(*) AS 昨天小时数
                    FROM
                        lowAccessCell_ex
                    WHERE
                        city = 'changzhou'
                    AND cell IN (SELECT DISTINCT cell FROM lowAccessCell_ex WHERE day_id=DATE_FORMAT(NOW(), '%Y-%m-%d') AND hour_id=(SELECT hour_id FROM lowAccessCell_ex ORDER BY id DESC LIMIT 1))
                    AND day_id = DATE_ADD(
                        DATE_FORMAT(NOW(), '%Y-%m-%d'),
                        INTERVAL - 1 DAY
                    )
                    GROUP BY
                        subNetwork,
                        cell
                ) c ON a.cell = c.cell
                LEFT JOIN (
                    SELECT
                        cell,
                        sum(RRC建立失败次数) AS `RRC建立失败次数(今日)`,
                        sum(ERAB建立失败次数) AS `ERAB建立失败次数(今日)`,
                        COUNT(*) AS 今天小时数
                    FROM
                        lowAccessCell_ex
                    WHERE
                        city = 'changzhou'
                    AND cell IN (SELECT DISTINCT cell FROM lowAccessCell_ex WHERE day_id=DATE_FORMAT(NOW(), '%Y-%m-%d') AND hour_id=(SELECT hour_id FROM lowAccessCell_ex ORDER BY id DESC LIMIT 1))
                    AND day_id = DATE_FORMAT(NOW(), '%Y-%m-%d')
                    GROUP BY
                        subNetwork,
                        cell
                ) d ON a.cell = d.cell
                                LEFT JOIN (
                                    SELECT
                        cell,
                                                hour_id,
                        sum(RRC建立失败次数) AS `RRC建立失败次数(最新)`,
                        sum(ERAB建立失败次数) AS `ERAB建立失败次数(最新)`
                    FROM
                        lowAccessCell_ex
                    WHERE
                        city = 'changzhou'
                    AND cell IN (SELECT DISTINCT cell FROM lowAccessCell_ex WHERE day_id=DATE_FORMAT(NOW(), '%Y-%m-%d') AND hour_id=(SELECT hour_id FROM lowAccessCell_ex ORDER BY id DESC LIMIT 1))
                    AND day_id = DATE_FORMAT(NOW(), '%Y-%m-%d')
                                        AND hour_id = (SELECT hour_id FROM lowAccessCell_ex ORDER BY id DESC LIMIT 1)
                    GROUP BY
                        subNetwork,
                        cell
                )e ON a.cell = e.cell
                ORDER BY
                    严重程度 DESC
                LIMIT 30;");*/
            // $content = "id,city,subNetwork,cell,前天小时数,昨天小时数,今天小时数,RRC建立失败次数,ERAB建立失败次数,小时数";
            $content = "id,city,subNetwork,cell,小区名,hour_id,前天小时数,昨天小时数,今天小时数,RRC建立失败次数(最新),ERAB建立失败次数(最新),RRC建立失败次数(今日),ERAB建立失败次数(今日),RRC建立失败次数(总),ERAB建立失败次数(总),严重程度";
            $content1 = "id,day_id,hour_id,subNetwork,cell,前天小时数,昨天小时数,今天小时数,VoLTE无线接通率(QCI=1),VoLTE建立请求次数";
        } else if ($table == 'badHandoverCell_ex') {
            $rs = $conn->query("select * from (select id,city,subNetwork,cell, count(*) as 小时数,sum(准备切换失败数) as 准备切换失败数,sum(执行切换失败数) as 执行切换失败数 ,sum(异频准备切换失败数) as 异频准备切换失败数,sum(同频准备切换失败数) as 同频准备切换失败数 ,sum(同频执行切换失败数) as 同频执行切换失败数,sum(异频执行切换失败数) as 异频执行切换失败数 from " . $table . $filter . " group by subNetwork,cell order by 小时数 desc) as tmp");
            $content = "id,city,subNetwork,cell,小时数,准备切换失败数,执行切换失败数,异频准备切换失败数,同频准备切换失败数,同频执行切换失败数,异频执行切换失败数";
        } else {
            $rs = $conn->query("select * from (select id,city,subNetwork,cell, count(*) as 小时数, SUM(无线掉线次数) as 无线掉线次数  from " . $table . $filter . " group by subNetwork,cell order by 小时数 desc) as tmp");
            $content = "id,city,subNetwork,cell,小时数,无线掉线次数";

        }
        $items = array();
        $items1 = array();
        $download =array();
        $row4 = array();
        if ($table == 'lowAccessCell_ex') {
            $hours = "";
            while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
                // $row['小时数'] = floatval($row['小时数']);
                // $hours = $row['hour_id'];
                $row3 = array();
                $db2 = new DataBaseConnection();
                $cell = $row['cell'];
                $db = $db2->getDB('mongs', 'mongs');
                $sqlsite = "select cellNameChinese from siteLte where cellName = '$cell'";
                $row3 = $db->query($sqlsite)->fetch(PDO::FETCH_NUM);
                $row['小区名'] = $row3[0];
                $row['严重程度'] = ROUND(floatval($row['严重程度']), 2);
                $row['昨天小时数'] = floatval($row['昨天小时数']);
                $row['前天小时数'] = floatval($row['前天小时数']);
                $row['今天小时数'] = floatval($row['今天小时数']);

                // $row['RRC建立失败次数'] = floatval($row['RRC建立失败次数']);
                // $row['ERAB建立失败次数'] = floatval($row['ERAB建立失败次数']);
                $row['RRC建立失败次数(最新)'] = floatval($row['RRC建立失败次数(最新)']);
                $row['ERAB建立失败次数(最新)'] = floatval($row['ERAB建立失败次数(最新)']);
                $row['RRC建立失败次数(总)'] = floatval($row['RRC建立失败次数(总)']);
                $row['ERAB建立失败次数(总)'] = floatval($row['ERAB建立失败次数(总)']);
                $row['RRC建立失败次数(今日)'] = floatval($row['RRC建立失败次数(今日)']);
                $row['ERAB建立失败次数(今日)'] = floatval($row['ERAB建立失败次数(今日)']);
                $row['严重程度'] = floatval($row['严重程度']);
                $row4["id"] = $row["id"];
                $row4["city"] = $row["city"];
                $row4["subNetwork"] = $row["subNetwork"];
                $row4["cell"] = $row["cell"];
                $row4["小区名"] = $row["小区名"];
                $row4["hour_id"] = $row["hour_id"];
                $row4["前天小时数"] = floatval($row["前天小时数"]);
                $row4["昨天小时数"] = floatval($row['昨天小时数']);
                $row4["今天小时数"] = floatval($row['今天小时数']);
                $row4['RRC建立失败次数(最新)'] = floatval($row['RRC建立失败次数(最新)']);
                $row4['ERAB建立失败次数(最新)'] = floatval($row['ERAB建立失败次数(最新)']);
                $row4['RRC建立失败次数(总)'] = floatval($row['RRC建立失败次数(总)']);
                $row4['ERAB建立失败次数(总)'] = floatval($row['ERAB建立失败次数(总)']);
                $row4['RRC建立失败次数(今日)'] = floatval($row['RRC建立失败次数(今日)']);
                $row4['ERAB建立失败次数(今日)'] = floatval($row['ERAB建立失败次数(今日)']);
                $row4['严重程度'] = floatval($row['严重程度']);
                array_push($download, $row4);
                array_push($items, $row);
            }
            $dates = date("Y-m-d");
            $dates2 = date("Y-m-d",strtotime("-1 day"));
            $dates3 = date("Y-m-d",strtotime("-2 day"));
            $hours = date("H");
            $hours = $hours+6;
            // var_dump($hours);return;
            // var_dump($dates2);var_dump($dates3);return;
            // $sql1 = "SELECT
            //             id,
            //             day_id,
            //             hour_id,
            //             subNetwork,
            //             cell,
            //           `VoLTE无线接通率(QCI=1)`,
            //             VoLTE建立请求次数
            //         FROM
            //             lowAccessCell
            //         WHERE
            //             day_id IN ('$dates')
            //         AND hour_id IN ($hours)
            //         AND $cityFilter
            //         AND `VoLTE无线接通率(QCI=1)`<=97
            //         AND VoLTE建立请求次数>50";
            $sql1 = "SELECT
    table1.*,
    CASE
WHEN table3.b IS NULL THEN
    0
ELSE
    table3.b
END AS '前天小时数',
    CASE
WHEN table2.a IS NULL THEN
    0
ELSE
    table2.a
END AS '昨天小时数',
 CASE
WHEN table4.c IS NULL THEN
    0
ELSE
    table4.c
END AS '今天小时数'
FROM
    (
        SELECT
            *
        FROM
            lowAccessCell
        WHERE
            day_id IN ('$dates')
        AND hour_id IN ($hours)
        AND $cityFilter
        AND `VoLTE无线接通率(QCI=1)` <= 97
        AND VoLTE建立请求次数 > 50
    ) AS table1
LEFT JOIN (
    SELECT
        cell,
        count(DISTINCT hour_id) AS a
    FROM
        lowAccessCell
    WHERE
        day_id IN ('$dates2')
    AND $cityFilter
    GROUP BY
        cell
) AS table2 ON table1.cell = table2.cell
LEFT JOIN (
    SELECT
        cell,
        count(DISTINCT hour_id) AS b
    FROM
        lowAccessCell
    WHERE
        day_id IN ('$dates3')
    AND $cityFilter
    GROUP BY
        cell
) AS table3 ON table1.cell = table3.cell
LEFT JOIN (
    SELECT
        cell,
        count(DISTINCT hour_id) AS c
    FROM
        lowAccessCell
    WHERE
        day_id IN ('$dates')
    AND $cityFilter
    GROUP BY
        cell
) AS table4 ON table1.cell = table4.cell";
// var_dump($sql1);return;
            $rs1 = $conn->query($sql1);
            // $row1 = $rs1->fetch(PDO::FETCH_ASSOC);
            while ($row1 = $rs1->fetch(PDO::FETCH_ASSOC)) {
                $row1['VoLTE无线接通率(QCI=1)'] = ROUND(floatval($row1['VoLTE无线接通率(QCI=1)']), 2);
                $row1['VoLTE建立请求次数'] = floatval($row1['VoLTE建立请求次数']);
                // var_dump($row1);return;
                array_push($items1, $row1);
            }
        } else if ($table == 'highLostCell_ex') {
            while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
                $row['小时数'] = floatval($row['小时数']);
                $row['无线掉线次数'] = floatval($row['无线掉线次数']);
                array_push($items, $row);
            }
        } else if ($table == 'badHandoverCell_ex') {
            while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
                $row['小时数'] = floatval($row['小时数']);
                $row['准备切换失败数'] = floatval($row['准备切换失败数']);
                $row['执行切换失败数'] = floatval($row['执行切换失败数']);
                $row['异频准备切换失败数'] = floatval($row['异频准备切换失败数']);
                $row['同频准备切换失败数'] = floatval($row['同频准备切换失败数']);
                $row['同频执行切换失败数'] = floatval($row['同频执行切换失败数']);
                $row['异频执行切换失败数'] = floatval($row['异频执行切换失败数']);
                array_push($items, $row);
            }
        }

        $result['records'] = count($items);
        $result["download"]=$download;
        $result["rows"] = $items;
        $result["content"] = $content;
        $filename = "common/files/" . $table . date('YmdHis') . ".csv";
        $result['filename'] = $filename;
        $result1['records1'] = count($items1);
        $result1["rows1"] = $items1;
        $result1["content1"] = $content1;
        $filename1 = "common/files/" . $table . "volte" . date('YmdHis') . ".csv";
        $result1['filename1'] = $filename1;
        $this->resultToCSV2($result1, $filename1);
        $this->resultToCSV2($result, $filename);
        $resultall = array_merge($result,$result1);
        // var_dump($resultall);return;
        echo json_encode($resultall);
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
        if (strstr($filename, "volte")) {
            $csvContent = mb_convert_encoding($result['content1'] . "\n", 'gb2312', 'utf-8');
            $fp = fopen($filename, "w");
            fwrite($fp, $csvContent);
            foreach ($result["rows1"] as $row) {
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
    }


    /**
     * 导出坏小区列表CSV文件
     *
     * @param array  $result   坏小区列表
     * @param string $filename CSV文件名
     *
     * @return void
     */
    protected function resultToCSV2_par($result, $filename)
    { 
    
        $csvContent = mb_convert_encoding($result['content'] . "\n", 'gb2312', 'utf-8');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result["rows"] as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
    }

    /**
     * 获得切换差小区指标数据
     *
     * @return string 切换差小区指标数据
     */
    public function getBadHandoverCellData()
    {
        $cell = Input::get('rowCell');
        $startTime = Input::get('startTime');
        $endTime = Input::get('endTime');
        $table = Input::get('table');

        $cityArr = Input::get('city');
        $city = array();
        $cityPY = new DataBaseConnection();
        foreach ($cityArr as $citys) {
            $cityStr = $cityPY->getCityByCityChinese($citys)[0]->connName;
            array_push($city, $cityStr);
        }

        // $dbn = new DataBaseConnection();
        // $conn = $dbn->getDB('mongs', 'AutoKPI');
        // if ($conn == null) {
        //     die('Could not connect: ' . mysql_error());
        // }

        // mysql_select_db('AutoKPI', $conn);

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
        $this->resultToCSV2_par($item, $filename);
        echo json_encode($item);
    }

    /**
     * 获得单个高掉线小区指标数据
     *
     * @return string 单个高掉线小区指标数据
     */
    public function getHighLostCellData()
    {
        $cell = Input::get('rowCell');
        $startTime = Input::get('startTime');
        $endTime = Input::get('endTime');
        $table = Input::get('table');
        $cityArr = Input::get('city');
        $city = array();
        $cityPY = new DataBaseConnection();
        foreach ($cityArr as $citys) {
            $cityStr = $cityPY->getCityByCityChinese($citys)[0]->connName;
            array_push($city, $cityStr);
        }
        // $dbn = new DataBaseConnection();
        // $conn = $dbn->getConnDB('mongs');
        // if (!$conn) {
        //     die('Could not connect: ' . mysql_error());
        // }
        // mysql_select_db('AutoKPI', $conn);
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
        $sql = "SELECT day_id,hour_id,city,subNetwork,cell,无线掉线率,无线掉线次数,上下文建立成功数,遗留上下文数,小区闭锁导致的掉线,切换导致的掉线,S1接口故障导致的掉线,UE丢失导致的掉线,预清空导致的掉线,上行干扰电平dBm,估算TAkm,估算PUSCH_SINR,平均下行RSRP,平均下行CQI,平均下行RSRQ,小区退服时长s,小区人为闭锁时长s,最大RRC连接用户数,资源争抢的TTI数,CQI拥塞,SR拥塞,UE功率受限,ERAB建立失败次数 FROM highLostCell $filter";
        $item = array();
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
        $item['record'] = count($rows);
        $item['content'] = "day_id,hour_id,city,subNetwork,cell,无线掉线率,无线掉线次数,上下文建立成功数,遗留上下文数,小区闭锁导致的掉线,切换导致的掉线,S1接口故障导致的掉线,UE丢失导致的掉线,预清空导致的掉线,上行干扰电平dBm,估算TAkm,估算PUSCH_SINR,平均下行RSRP,平均下行CQI,平均下行RSRQ,小区退服时长s,小区人为闭锁时长s,最大RRC连接用户数,资源争抢的TTI数,CQI拥塞,SR拥塞,UE功率受限,ERAB建立失败次数";
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
        $this->resultToCSV2_par($item, $filename);
        echo json_encode($item);
    }

    /**
     * 获得基站级告警数据概览
     *
     * @return string 基站级告警数据概览
     */
    public function getErbsAlarmClassify()
    {
        $day_from = Input::get('day_from');
        $day_to = Input::get('day_to');
        $cell = Input::get('cell');
        $rowData_2 = explode('_', $cell);
        $rowData_3 = $rowData_2[0];
        if ($rowData_3 == $cell) {
            $rowData_3 = substr($rowData_3, 0, strlen($rowData_3) - 1);
        }
        $erbs = $rowData_3;
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('alarm', 'Alarm');
        $sql = "SELECT
                    SP_text AS name,
                    COUNT(*) AS y
                FROM
                    FMA_alarm_log
                WHERE
                    DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $day_from . "'
                AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $day_to . "'
                AND meContext = '" . $erbs . "'
                GROUP BY SP_text";
        $rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        $i = 0;
        foreach ($rows as $row) {
            $row['y'] = intval($row['y']);
            $i++;
            $items['series'][] = $row;
        }
        echo json_encode($items);
    }

    /**
     * 获得小区级告警数据概览
     *
     * @return string 小区级告警数据概览
     */
    public function getCellAlarmClassify()
    {
        $day_from = Input::get('day_from');
        $day_to = Input::get('day_to');
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('alarm', 'Alarm');
        $sql = "SELECT
                    SP_text AS name,
                    COUNT(*) AS y
                FROM
                    FMA_alarm_log
                WHERE
                    DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $day_from . "'
                AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $day_to . "'
                AND eutranCell = '" . $cell . "'
                GROUP BY SP_text";
        $rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        $i = 0;
        foreach ($rows as $row) {
            $row['y'] = intval($row['y']);
            $i++;
            $items['series'][] = $row;
        }
        echo json_encode($items);
    }

    /**
     * 获得单个低接入小区指标
     *
     * @return string 单个低接入小区指标
     */
    public function getLowAccessCellData()
    {
        $cell = Input::get('rowCell');
        $startTime = Input::get('startTime');
        $endTime = Input::get('endTime');
        $table = Input::get('table');
        $cityArr = Input::get('city');
        $city = array();
        $cityPY = new DataBaseConnection();
        foreach ($cityArr as $citys) {
            $cityStr = $cityPY->getCityByCityChinese($citys)[0]->connName;
            array_push($city, $cityStr);
        }
        // $dbn = new DataBaseConnection();
        // $conn = $dbn->getConnDB('mongs');
        // if (!$conn) {
        //     die('Could not connect: ' . mysql_error());
        // }

        // mysql_select_db('AutoKPI', $conn);
        $filter = '';
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
        $sql = "SELECT day_id,hour_id,city,subNetwork,cell,无线接通率,RRC建立请求次数,RRC建立成功次数,RRC建立失败次数,RRC建立成功率,ERAB建立请求次数,ERAB建立成功次数,ERAB建立失败次数,ERAB建立成功率,License超限导致的RRC连接失败,承载准入拒绝导致的RRC连接失败,高负载导致的RRC连接失败,超载导致的RRC连接失败,无线进程失败导致的RRC连接失败,未指定的RRC连接失败,缺少资源导致的RRC连接失败,激活用户License超限导致的RRC连接失败,上行干扰电平dBm,估算TAkm,估算PUSCH_SINR,平均下行RSRP,平均下行CQI,平均下行RSRQ,小区退服时长s,小区人为闭锁时长s,最大RRC连接用户数,资源争抢的TTI数,SR拥塞比,动态超负荷导致RRC建立失败,静态超负荷导致RRC建立失败,大延迟导致RRC建立失败,DU或者基带连接控制导致的RRC建立失败,MP超载导致RRC建立被拒绝 FROM lowAccessCell $filter";
        $item = array();
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
        $item['record'] = count($rows);
        $item['content'] = "day_id,hour_id,city,subNetwork,cell,无线接通率,RRC建立请求次数,RRC建立成功次数,RRC建立失败次数,RRC建立成功率,ERAB建立请求次数,ERAB建立成功次数,ERAB建立失败次数,ERAB建立成功率,License超限导致的RRC连接失败,承载准入拒绝导致的RRC连接失败,高负载导致的RRC连接失败,超载导致的RRC连接失败,无线进程失败导致的RRC连接失败,未指定的RRC连接失败,缺少资源导致的RRC连接失败,激活用户License超限导致的RRC连接失败,上行干扰电平dBm,估算TAkm,估算PUSCH_SINR,平均下行RSRP,平均下行CQI,平均下行RSRQ,小区退服时长s,小区人为闭锁时长s,最大RRC连接用户数,资源争抢的TTI数,SR拥塞比,动态超负荷导致RRC建立失败,静态超负荷导致RRC建立失败,大延迟导致RRC建立失败,DU或者基带连接控制导致的RRC建立失败,MP超载导致RRC建立被拒绝";
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
    }

    /**
     * 获得分类(小区，基站)告警总数
     *
     * @return string 分类(小区，基站)告警总数
     */
    public function getAlarmNum()
    {
        $cell = Input::get('cell');
        $startTime = Input::get('startTime');
        $endTime = Input::get('endTime');
        $hours = Input::get('hours');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('alarm', 'Alarm');
        $table = 'FMA_alarm_log';
        $rowData_2 = explode('_', $cell);
        $rowData_3 = $rowData_2[0];
        if ($rowData_3 == $cell) {
            $rowData_3 = substr($rowData_3, 0, strlen($rowData_3) - 1);
        }
        $sql_erbs = "select count(*) as num from " . $table . " where meContext='" . $rowData_3 . "' and DATE_FORMAT(Event_time, '%Y-%m-%d')>='" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d')<='" . $endTime . "'";
        $sql = "select count(*) as num from " . $table . " where eutranCell='" . $cell . "' and DATE_FORMAT(Event_time, '%Y-%m-%d')>='" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d')<='" . $endTime . "'";
        $sql_erbs_hour = '';
        $sql_hour = '';
        if ($hours != '') {
            $hour = implode(',', $hours);
            $sql_erbs_hour = "select count(*) as num from " . $table . " where meContext='" . $rowData_3 . "' and DATE_FORMAT(Event_time, '%Y-%m-%d')>='" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d')<='" . $endTime . "' AND DATE_FORMAT(Event_time, '%H') IN ($hour)";
            $sql_hour = "select count(*) as num from " . $table . " where eutranCell='" . $cell . "' and DATE_FORMAT(Event_time, '%Y-%m-%d')>='" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d')<='" . $endTime . "' AND DATE_FORMAT(Event_time, '%H') IN ($hour)";
        }
        $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $row_cell = intval($row[0]['num']);
        $row = $db->query($sql_erbs)->fetchAll(PDO::FETCH_ASSOC);
        $row_erbs = intval($row[0]['num']);
        $row_cell_hour = 'null';
        $row_erbs_hour = 'null';
        if ($hours != '') {
            $row = $db->query($sql_hour)->fetchAll(PDO::FETCH_ASSOC);
            $row_cell_hour = intval($row[0]['num']);
            $row = $db->query($sql_erbs_hour)->fetchAll(PDO::FETCH_ASSOC);
            $row_erbs_hour = intval($row[0]['num']);
        }
        $items = array();
        array_push($items, $row_erbs, $row_cell, $row_erbs_hour, $row_cell_hour);
        echo json_encode($items);
    }

    /**
     * 获得基站级告警详细
     *
     * @return array 基站级告警详细
     */
    public function getErbsAlarmClassifyTable()
    {
        $cell = Input::get('cell');
        $startTime = Input::get('day_from');
        $endTime = Input::get('day_to');
        $result = array();
        /*$rowData_2 = explode('_', $cell);
        $rowData_3 = $rowData_2[0];
        if ($rowData_3 == $cell) {
            $rowData_3 = substr($rowData_3, 0, strlen($rowData_3) - 1);
        }*/
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('mongs', 'mongs');
        $sql = "SELECT siteName from siteLte WHERE cellName='$cell';";
        $row = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
        $erbs = $row[0]['siteName'];
        $sql = "SELECT COUNT(*) as num FROM FMA_alarm_log r LEFT JOIN mongs.AlarmInfo t ON r.SP_text= t.alarmNameE WHERE DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $endTime . "' AND meContext = '" . $erbs . "';";
        // $sql = "SELECT COUNT(*) as num FROM FMA_alarm_log r,mongs.AlarmInfo t WHERE r.SP_text= t.alarmNameE AND DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $endTime . "' AND meContext = '" . $erbs . "';";
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('alarm', 'Alarm');
        $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $result['records'] = $row[0]['num'];
        $result["content"] = "Event_time,meContext,eutranCell,SP_text,Problem_text,alarmNameE,alarmNameC,levelE,levelC,interfere,access,lost,handover,comments";
        $sql = "SELECT Event_time,meContext,eutranCell,SP_text,Problem_text,t.alarmNameE,t.alarmNameC,t.levelE,t.levelC,t.interfere,t.access,t.lost,t.handover,t.comments FROM FMA_alarm_log r LEFT JOIN mongs.AlarmInfo t ON r.SP_text= t.alarmNameE WHERE DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $endTime . "' AND meContext = '" . $erbs . "';";
        // $sql = "SELECT Event_time,meContext,eutranCell,SP_text,Problem_text,t.alarmNameE,t.alarmNameC,t.levelE,t.levelC,t.interfere,t.access,t.lost,t.handover,t.comments FROM FMA_alarm_log r,mongs.AlarmInfo t WHERE r.SP_text= t.alarmNameE AND DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $endTime . "' AND meContext = '" . $erbs . "';";
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchAll();
        $result["rows"] = $rows;
        return $result;
    }

    /**
     * 获得小区级告警详细
     *
     * @return array 小区级告警详细
     */
    public function getCellAlarmClassifyTable()
    {
        $cell = Input::get('cell');
        $startTime = Input::get('day_from');
        $endTime = Input::get('day_to');
        $result = array();
        /*$rowData_2 = explode('_', $cell);
        $rowData_3 = $rowData_2[0];
        if ($rowData_3 == $cell) {
            $rowData_3 = substr($rowData_3, 0, strlen($rowData_3) - 1);
        }*/
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('mongs', 'mongs');
        $sql = "SELECT siteName from siteLte WHERE cellName='$cell';";
        $row = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
        $erbs = $row[0]['siteName'];
        // $sql = "SELECT COUNT(*) as num FROM FMA_alarm_list r LEFT JOIN mongs.AlarmInfo t ON r.SP_text= t.alarmNameE WHERE DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $endTime . "' AND meContext = '" . $erbs . "';";
        $sql = "SELECT COUNT(*) as num FROM FMA_alarm_list r,mongs.AlarmInfo t WHERE r.SP_text= t.alarmNameE AND DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $endTime . "' AND meContext = '" . $erbs . "';";
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('alarm', 'Alarm');
        $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $result['records'] = $row[0]['num'];
        $result["content"] = "Event_time,meContext,eutranCell,SP_text,Problem_text,alarmNameE,alarmNameC,levelE,levelC,interfere,access,lost,handover,comments";
        // $sql = "SELECT Event_time,meContext,eutranCell,SP_text,Problem_text,t.alarmNameE,t.alarmNameC,t.levelE,t.levelC,t.interfere,t.access,t.lost,t.handover,t.comments FROM FMA_alarm_list r LEFT JOIN mongs.AlarmInfo t ON r.SP_text= t.alarmNameE WHERE DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $endTime . "' AND meContext = '" . $erbs . "';";
        $sql = "SELECT Event_time,meContext,eutranCell,SP_text,Problem_text,t.alarmNameE,t.alarmNameC,t.levelE,t.levelC,t.interfere,t.access,t.lost,t.handover,t.comments FROM FMA_alarm_list r,mongs.AlarmInfo t WHERE r.SP_text= t.alarmNameE AND DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $endTime . "' AND meContext = '" . $erbs . "';";
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchAll();
        $result["rows"] = $rows;
        return $result;
    }

    /**
     * 获得单小区指标趋势时序数据-分种类
     *
     * @return string 单小区指标趋势时序-分种类
     */
    public function getRelatedTrends() 
    {
        $dbn = new DataBaseConnection();
        $conn = $dbn->getDB('autokpi', 'AutoKPI'); 
        if ($conn == null) {
            echo 'Could not connect';
        }
        $cell = Input::get('cell');
        $data = Input::get('data');
        $table = 'lowAccessCell';
        $startTime = date("Y-m-d", strtotime("-1 day"));
        $endTime = date("Y-m-d");
        $yAxis_left = explode('_', $data)[0];
        $yAxis_right = explode('_', $data)[1];
        $categories = [];
        date_default_timezone_set('PRC');
        $time = date("Y-m-d", strtotime("-1 day"));
        $timestart = $time . ' 00:00:00';
        $timeend = date("Y-m-d H", strtotime("-1 hours"));
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
        $items = [];
        $returnData = [];
        // print_r($yAxis_right);return;
        if ($yAxis_right == '质差') {
            $sql = "select day_id,hour_id," . $yAxis_left . ", `RSRQ<-15.5的比例` from " . $table . " where day_id>='" . $startTime . "' and day_id<='" . $endTime . "' and cell='" . $cell . "' ORDER BY day_id,hour_id";
            // print_r($sql);return;
            $res_1 = $conn->query($sql);
            $arr_yAxis_1 = [];
            $arr_yAxis = [];                 
            while ($line_1 = $res_1->fetch(PDO::FETCH_NUM)) {
                $time = strval(strval($line_1[0]) . " " . strval($line_1[1])) . ":00";
                $time = mb_convert_encoding($time, 'gb2312', 'utf-8');
                $arr_yAxis_1[$time] =  $line_1[3];
                $arr_yAxis[$time] = $line_1[2];
            }
            $yAxis_1 = [];
            $yAxis = [];
            for ($i=0; $i<count($categories); $i++) {
                $key = $categories[$i];
                if (array_key_exists($key, $arr_yAxis_1)) {
                    array_push($yAxis_1, $arr_yAxis_1[$key]);
                } else {
                    array_push($yAxis_1, null);
                }
            }

            for ($i=0; $i<count($categories); $i++) {
                $key = $categories[$i];
                if (array_key_exists($key, $arr_yAxis)) {
                    array_push($yAxis, $arr_yAxis[$key]);
                } else {
                    array_push($yAxis, null);
                }
            }
            $series_1['name'] = $yAxis_right;  //质差
            $series_1['color'] = '#87CEFF';
            $series_1['type'] = 'spline4';
            $series_1['data'] = $yAxis_1;
            $series_1['yAxis'] = 1;
            array_push($items, $series_1);
            $series['name'] = $yAxis_left;  //无线接通率
            $series['color'] = '#89A54E';
            $series['type'] = 'spline5';
            $series['data'] = $yAxis;
            array_push($items, $series);
            $yAxis_set = [];
            $yAxis_set_2['labels'] = "{'format':'{value}%','style':{color: '#89A54E'}}";
            $yAxis_set_2['title'] = "{'text':'" . $yAxis_left . "','style':{color: '#89A54E'}}";
            $yAxis_set_2['max'] = 100;
            $yAxis_set_2['min'] = 0;
            array_push($yAxis_set, $yAxis_set_2);
            $yAxis_name_right_1 = '';
            $yAxis_set_1['labels'] = "{'format':'{value}','style':{color: '#87CEFF'}}";
            $yAxis_set_1['title'] = "{'text':'" . $yAxis_right . "','style':{color: '#87CEFF'}}";
            $yAxis_set_1['opposite'] = true;
            array_push($yAxis_set, $yAxis_set_1);          
            $returnData['categories'] = $categories;
            $returnData['series'] = $items;
            $returnData['yAxis_set'] = $yAxis_set;
            $returnData['cell'] = $cell;
            echo json_encode($returnData);
            return;
        } else if ($yAxis_right == '干扰') {
            // $sql = "select day_id,hour_id," . $yAxis_left . ", `RSRQ<-15.5的比例` from " . $table . " where day_id>='" . $startTime . "' and day_id<='" . $endTime . "' and cell='" . $cell . "' ORDER BY day_id,hour_id";
            $sql = "select day_id,hour_id," . $yAxis_left . " from " . $table . " where day_id>='" . $startTime . "' and day_id<='" . $endTime . "' and cell='" . $cell . "' ORDER BY day_id,hour_id";
            $sql_1 = "SELECT day_id,hour_id,
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
                GROUP BY day_id,hour_id;";
            // print_r($sql);return;
            $res = $conn->query($sql);
            $res_1 = $conn->query($sql_1);
            $arr_yAxis_1 = [];
            $arr_yAxis = [];                 
            while ($line = $res->fetch(PDO::FETCH_NUM)) {
                $time = strval(strval($line[0]) . " " . strval($line[1])) . ":00";
                $time = mb_convert_encoding($time, 'gb2312', 'utf-8');
                $arr_yAxis[$time] =  $line[2];
                // $arr_yAxis[$time] = $line_1[2];
            }
            $yAxis_1 = [];
            $yAxis = [];
            $arr_yAxis_2 = [];
            // $yAxis_1_left = [];
            // for($i=0; $i<count($categories); $i++) {
            //     $key = $categories[$i];
            //     if(array_key_exists($key,$arr_yAxis_1)) {
            //         array_push($yAxis_1_left, $arr_yAxis_1[$key]);
            //     }else {
            //         array_push($yAxis_1_left, null);
            //     }
            // }
            while ($rows = $res_1->fetch(PDO::FETCH_NUM)) {
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
                $avg = $num / $j;
                $arr_yAxis_2[$time] = $avg;
                // array_push($yAxis_2, $avg);
            }//return;

            for ($i=0; $i<count($categories); $i++) {
                $key = $categories[$i];
                if (array_key_exists($key, $arr_yAxis_2)) {
                    array_push($yAxis_1, round($arr_yAxis_2[$key], 2));
                } else {
                    array_push($yAxis_1, null);
                }
            }
            for ($i=0; $i<count($categories); $i++) {   //无线接通率
                $key = $categories[$i];
                if (array_key_exists($key, $arr_yAxis)) {
                    array_push($yAxis, $arr_yAxis[$key]);
                } else {
                    array_push($yAxis, null);
                }
            }
            $series_1['name'] = $yAxis_right;  //质差
            $series_1['color'] = '#87CEFF';
            $series_1['type'] = 'spline4';
            $series_1['data'] = $yAxis_1;
            $series_1['yAxis'] = 1;
            array_push($items, $series_1);

            $series['name'] = $yAxis_left;  //无线接通率
            $series['color'] = '#89A54E';
            $series['type'] = 'spline5';
            $series['data'] = $yAxis;
            array_push($items, $series);

            $yAxis_set = [];
            $yAxis_set_2['labels'] = "{'format':'{value}%','style':{color: '#89A54E'}}";
            $yAxis_set_2['title'] = "{'text':'" . $yAxis_left . "','style':{color: '#89A54E'}}";
            $yAxis_set_2['max'] = 100;
            $yAxis_set_2['min'] = 0;
            array_push($yAxis_set, $yAxis_set_2);

            $yAxis_name_right_1 = '';
            $yAxis_set_1['labels'] = "{'format':'{value}','style':{color: '#87CEFF'}}";
            $yAxis_set_1['title'] = "{'text':'" . $yAxis_right . "','style':{color: '#87CEFF'}}";
            $yAxis_set_1['opposite'] = true;
            array_push($yAxis_set, $yAxis_set_1);
            

            $returnData['categories'] = $categories;
            $returnData['series'] = $items;
            $returnData['yAxis_set'] = $yAxis_set;
            $returnData['cell'] = $cell;
            echo json_encode($returnData);
            return;
        }


    }

    /**
     * 获得单小区指标趋势时序数据
     *
     * @return string 单小区指标趋势时序
     */
    public function getChartData()
    {
        $dbn = new DataBaseConnection();
        $conn = $dbn->getDB('autokpi', 'AutoKPI'); 
        if ($conn == null) {
            echo 'Could not connect';
        }
        $table = Input::get('table');

        $cell = Input::get('rowCell');
        $startTime = Input::get('startTime');
        $endTime = Input::get('endTime');
        $yAxis_name_left_arr = Input::get('yAxis_name_left');
        $yAxis_name_left = implode(',', $yAxis_name_left_arr);
        $yAxis_name_right_arr = Input::get('yAxis_name_right');

        $categories = [];
        date_default_timezone_set('PRC');
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
            // array_push($categories, date("Y-m-d h:i", $strtimestart));
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
            // $sql = "select day_id,hour_id," . $yAxis_name_right_1 . "," . $yAxis_name_left ." from " . $table . " where day_id>='" . $startTime . "' and day_id<='" . $endTime . "' and cell='" . $cell . "' ORDER BY day_id,hour_id";
            $sql = "select day_id,hour_id,`RSRQ<-15.5的比例`," . $yAxis_name_left ." from " . $table . " where day_id>='" . $startTime . "' and day_id<='" . $endTime . "' and cell='" . $cell . "' ORDER BY day_id,hour_id";
            // print_r($sql);return;
            $res_1 = $conn->query($sql);
        }


        if (strcmp($startTime, $endTime) == 0) {
            $res = $conn->query("select day_id,hour_id," . $yAxis_name_left . "," . $yAxis_name_right . " from " . $table . " where day_id='" . $endTime . "' and cell='" . $cell . "' ORDER BY day_id,hour_id");
            // $res = mysql_query("select day_id,hour_id," . $yAxis_name_left . "," . $yAxis_name_right . " from " . $table . " where day_id='" . $endTime . "' and cell='" . $cell . "' ORDER BY day_id,hour_id");
        } else if (strcmp($startTime, $endTime) < 0) {
            // $res = $conn->query("select day_id,hour_id," . $yAxis_name_left . "," . $yAxis_name_right . " from " . $table . " where day_id>='" . $startTime . "' and day_id<='" . $endTime . "' and cell='" . $cell . "' ORDER BY day_id,hour_id");
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
        // $categories = array();

        $yAxis_l_RRC = array();
        $series_l_RRC = array();
        $yAxis_l_ERAB = array();
        $series_l_ERAB = array();

        $yAxis_2 = [];
        $arr_yAxis_2 = [];
        while ($rows = $res_ganrao->fetch(PDO::FETCH_NUM)) {
            $time = strval(strval($rows[0]) . " " . strval($rows[1])) . ":00";
            $time = mb_convert_encoding($time, 'gb2312', 'utf-8');

            /*$hour = $rows[1];
            if($hour < 10) {
                $hour = '0'.$hour;
            }
            $time = $rows[0] .' '. $hour;*/
            // print_r($rows);
            /*$flag = 0;
            foreach ($categories as $value) {
                if($value == $time) {
                    // print_r($value.' '.$time);
                    $num = 0;
                    $j = 100;
                    for ($i=2; $i < 102 ; $i++) { 
                        if($rows[$i] == null){
                            $j--;
                            continue;
                        }
                        $num = $num + $rows[$i];
                    }
                    $avg = $num / $j;
                    array_push($yAxis_2, $avg);
                    $flag = 1;
                    break;
                }
            }
            if($flag == 0) {
                array_push($yAxis_2, null);
            }*/

            $num = 0;
            $j = 100;
            for ($i=2; $i < 102 ; $i++) { 
                if ($rows[$i] == null) {
                    $j--;
                    continue;
                }
                $num = $num + $rows[$i];
            }
            $avg = $num / $j;
            $arr_yAxis_2[$time] = $avg;
            // array_push($yAxis_2, $avg);
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
            /*$hour = $line[1];
            if($hour < 10) {
                $hour = '0'.$hour;
            }
            $time = $line[0] .' '. $hour;

            for ($i=0; $i < count($categories); $i++) { 
            //foreach ($categories as $value) {
                if($categories[$i] == $time) {
                    array_push($yAxis, $line[2]);
                    array_push($yAxis_l_RRC, $line[3]);   
                    array_push($yAxis_l_ERAB, $line[4]);
                    break;
                }else {
                    array_push($yAxis, null);
                    array_push($yAxis_l_RRC, null);   
                    array_push($yAxis_l_ERAB, null);
                    $i++;
                    // print_r($flag);
                }
            }*/
            $arr_yAxis[$time] =  $line[2];
            $arr_yAxis_l_RRC[$time] = $line[3];
            $arr_yAxis_l_ERAB[$time] = $line[4];
            
            // array_push($yAxis, $line[2]);
            // array_push($yAxis_l_RRC, $line[3]);   
            // array_push($yAxis_l_ERAB, $line[4]);
            // array_push($yAxis_2, $line[5]);  //干扰
            // array_push($categories, $time);
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
                /*$hour = $line_1[1];
                if($hour < 10) {
                    $hour = '0'.$hour;
                }
                $time = $line_1[0] .' '. $hour;
                $flag = 0;
                foreach ($categories as $value) {                 
                    if($value == $time) {
                        // print_r($value.'='.$time);
                        array_push($yAxis_1, $line_1[2]);
                        $flag = 1;
                        break;
                    }
                }
                if($flag == 0) {
                    array_push($yAxis_1, null);
                }
                // array_push($yAxis_1, $line_1[2]);*/
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
            // $series_1['yAxis'] = 3;
            array_push($items, $series_1);
        }

        $series['name'] = $yAxis_name_left_arr[0];  //无线接通率
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
            $yAxis_set_1['title'] = "{'text':'" . $yAxis_name_right_1 ."(%)". "','style':{color: '#87CEFF'}}";
            $yAxis_set_1['opposite'] = true;
            // $yAxis_set_1['max'] = 1;
            // $yAxis_set_1['min'] = 0;
            array_push($yAxis_set, $yAxis_set_1);
        } else {
            // $yAxis_set_1['gridLineWidth'] = 0;
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
        // $yAxis_set_3['max'] = 100;
        // $yAxis_set_3['min'] = 0;
        array_push($yAxis_set, $yAxis_set_3);

        // $yAxis_set_2['gridLineWidth'] = 0;
        $yAxis_set_2['labels'] = "{'format':'{value}%','style':{color: '#AAAAAA'}}";
        $yAxis_set_2['title'] = "{'text':'" . $yAxis_name_left . "','style':{color: '#AAAAAA'}}";
        $yAxis_set_2['max'] = 100;
        $yAxis_set_2['min'] = 0;
        array_push($yAxis_set, $yAxis_set_2);

        // // $yAxis_set_l_RRC['gridLineWidth'] = 0;
        // $yAxis_set_l_RRC['labels'] = "{'format':'{value}%','style':{color: '#F4A460'}}";
        // $yAxis_set_l_RRC['title'] = "{'text':'" . $yAxis_name_left_arr[1] . "','style':{color: '#F4A460'}}";
        // $yAxis_set_l_RRC['max'] = 100;
        // $yAxis_set_l_RRC['min'] = 0;
        // array_push($yAxis_set, $yAxis_set_l_RRC);

        // // $yAxis_set_l_ERAB['gridLineWidth'] = 0;
        // $yAxis_set_l_ERAB['labels'] = "{'format':'{value}%','style':{color: '#D1EEEE'}}";
        // $yAxis_set_l_ERAB['title'] = "{'text':'" . $yAxis_name_left_arr[2] . "','style':{color: '#D1EEEE'}}";
        // $yAxis_set_l_ERAB['max'] = 100;
        // $yAxis_set_l_ERAB['min'] = 0;
        // array_push($yAxis_set, $yAxis_set_l_ERAB);


        $returnData['yAxis_set'] = $yAxis_set;

        $returnData['cell'] = $cell;
        echo json_encode($returnData);
    }

    /**
     * 获取单小区弱覆盖数据
     *
     * @return string 单小区弱覆盖数据
     */
    public function getWeakCoverageCell()
    {
        $startTime = Input::get('startTime');
        $endTime = Input::get('endTime');
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $dbname=$dsn->getMRDatabaseByCity(input::get("city"));
        $db = $dsn->getDB('MR', $dbname);
        $series = array();

        // $sql = "SELECT
        //             count(*) AS num
        //         FROM
        //             mroWeakCoverage t
        //         LEFT JOIN GLOBAL.siteLte s ON t.ecgi = s.ecgi
        //         WHERE
        //             substring_index(datetime_id, ' ', 1) BETWEEN '" . $startTime . "'
        //         AND '" . $endTime . "'
        //         AND s.cellName = '" . $cell . "'
        //         AND t.ratio110>0.2;";
        $sql = "SELECT
                    count(*) AS num
                FROM
                    mroWeakCoverage t
                , GLOBAL.siteLte s 
                WHERE
                    t.ecgi = s.ecgi
                AND substring_index(datetime_id, ' ', 1) BETWEEN '" . $startTime . "'
                AND '" . $endTime . "'
                AND s.cellName = '" . $cell . "'
                AND t.ratio110>0.2;";

        $num = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        // $sql = "SELECT
        //             sum(numLess80),
        //             sum(numLess80_90),
        //             sum(numLess90_100),
        //             sum(numLess100_110),
        //             sum(numLess110),
        //             cellName
        //         FROM
        //             mroWeakCoverage t
        //         LEFT JOIN GLOBAL.siteLte s
        //         on t.ecgi = s.ecgi
        //         WHERE
        //             substring_index(datetime_id, ' ', 1) BETWEEN '" . $startTime . "'
        //         AND '" . $endTime . "' AND cellName='" . $cell . "' group by cellName;";
        $sql = "SELECT
                    sum(numLess80),
                    sum(numLess80_90),
                    sum(numLess90_100),
                    sum(numLess100_110),
                    sum(numLess110),
                    cellName
                FROM
                    mroWeakCoverage t
                , GLOBAL.siteLte s
                WHERE
                t.ecgi = s.ecgi
                AND substring_index(datetime_id, ' ', 1) BETWEEN '" . $startTime . "'
                AND '" . $endTime . "' AND cellName='" . $cell . "' group by cellName;";

        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        if (count($rows) == 0) {
            $return['category'] = ['numLess80', 'numLess80_90', 'numLess90_100', 'numLess100_110', 'numLess110'];
            $return['num'][] = 0;
            $return['series'][] = '';
        } else {
            $temp = $rows[0];
            $temp_list = array();
            foreach ($temp as $key => $value) {
                if ($key == 'cellName') {
                    continue;
                }
                $temp_list[] = floatval($value);
            }
            $series['data'] = $temp_list;
            $return['category'] = ['numLess80', 'numLess80_90', 'numLess90_100', 'numLess100_110', 'numLess110'];
            $return['num'][] = $num[0]['num'];
            $return['series'][] = $series;
        }

        return json_encode($return);
    }

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
    }

    /**
     * 获取LTE补邻区数据表头
     *
     * @return array LTE补邻区数据表头
     */
    public function getLTENeighborHeader1()
    {
        $cityEN = Input::get('city');
        $cityCH = $this->encityToCHcity($cityEN);
        $dbname = $this->getMRDatabase($cityCH);
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('MR', $dbname);
        $table = 'mreServeNeigh';
        $result = array();
        $sql = "select * from $table limit 1";
        $rs = $db->query($sql, PDO::FETCH_ASSOC);
        if ($rs) {
            $rows = $rs->fetchall();
            if (count($rows) > 0) {
                return $rows[0];
            } else {
                $result['error'] = 'error';
                return $result;
            }
        } else {
            $result['error'] = 'error';
            return $result;
        }
    }//end getLTENeighborHeader1()

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
    }

    /**
     * 获取LTE补邻区数据表头
     *
     * @return array LTE补邻区数据表头
     */
    public function getLTENeighborHeader()
    {
        $cityEN = Input::get('city');
        $cityCH = $this->encityToCHcity($cityEN);
        $dbname = $this->getMRDatabase($cityCH);
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('MR', $dbname);
        $table = 'mreServeNeigh';
        $result = array();
        $sql = "select * from $table limit 1";
        $rs = $db->query($sql, PDO::FETCH_ASSOC);
        if ($rs) {
            $rows = $rs->fetchall();
            if (count($rows) > 0) {
                return $rows[0];
            } else {
                $result['error'] = 'error';
                return $result;
            }
        } else {
            $result['error'] = 'error';
            return $result;
        }
    }

    /**
     * 获得LTE补邻区数据
     *
     * @return string LTE补邻区数据
     */
    public function getLTENeighborData1()
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

        $dateTime = Input::get('dateTime');

        $result = array();
        $return = array();
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('MR', $dbname);
        $table = 'mreServeNeigh';
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
        $sql = "select * from $table where mr_LteScEarfcn = mr_LteNcEarfcn and ecgi = '$ecgi' and datetime_id like '" . $dateTime . "%'";
        $rs = $db->query($sql);
        if (!$rs) {
            $return["total"] = 0;
            $result['rows'] = [];
            $return['records'] = [];
            echo json_encode($return);
            return;
        }
        $rows = $rs->fetchall(PDO::FETCH_ASSOC);

        $return["total"] = count($rows);
        $rowsId = array();
        foreach ($rows as $row) {
            array_shift($row);
            array_push($rowsId, $row);
        }
        $result['rows'] = $rowsId;

        $sql = "select * from $table where mr_LteScEarfcn = mr_LteNcEarfcn and ecgi = '$ecgi' and datetime_id like '" . $dateTime . "%' order by datetime_id";
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $allData = array();
        foreach ($rows as $row) {
            array_push($allData, $row);
        }
        $return['records'] = $allData;
        echo json_encode($return);
    }//end getLTENeighborData1()


    /**
     * 获得LTE补邻区数据
     *
     * @return string LTE补邻区数据
     */
    public function getLTENeighborData()
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

        $dateTime = Input::get('dateTime');

        $result = array();
        $return = array();
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('MR', $dbname);
        $table = 'mreServeNeigh';
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
        $sql = "select * from $table where mr_LteScEarfcn != mr_LteNcEarfcn and ecgi = '$ecgi' and datetime_id like '" . $dateTime . "%'";
        $rs = $db->query($sql);
        if (!$rs) {
            $return["total"] = 0;
            $result['rows'] = [];
            $return['records'] = [];
            echo json_encode($return);
            return;
        }
        $rows = $rs->fetchall(PDO::FETCH_ASSOC);

        $sqlNum = "select count(*) as num from $table where ecgi = '$ecgi' and datetime_id like '" . $dateTime . "%' AND isdefined_direct=0 AND distance_direct<0.8;";
        $rs1 = $db->query($sqlNum);
        if (!$rs1) {
            $return["total"] = 0;
            $return["total_is0"] = 0;
            $result['rows'] = [];
            $return['records'] = [];
            echo json_encode($return);
            return;
        }
        $num = $rs1->fetchall(PDO::FETCH_ASSOC);
        $return["total_is0"] = $num[0]['num'];

        $return["total"] = count($rows);
        $rowsId = array();
        foreach ($rows as $row) {
            array_shift($row);
            array_push($rowsId, $row);
        }
        $result['rows'] = $rowsId;
        $sql = "select * from $table where mr_LteScEarfcn != mr_LteNcEarfcn and ecgi = '$ecgi' and datetime_id like '" . $dateTime . "%' order by datetime_id"; // $limit
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $allData = array();
        foreach ($rows as $row) {
            array_push($allData, $row);
        }
        $return['records'] = $allData;
        echo json_encode($return);
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
     * 获得GSM补邻区数据表头
     *
     * @return array GSM补邻区数据表头
     */
    public function getGSMNeighborHeader()
    {
        $cityEN = Input::get('city');
        $cityCH = $this->encityToCHcity($cityEN);
        $dbname = $this->getMRDatabase($cityCH);
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('MR', $dbname);
        $table = 'mreServerNeighIrat';
        $result = array();
        $sql = "select * from $table limit 1";
        $rs = $db->query($sql, PDO::FETCH_ASSOC);
        if ($rs) {
            $rows = $rs->fetchall();
            if (count($rows) > 0) {
                return $rows[0];
            } else {
                $result['error'] = 'error';
                return $result;
            }
        } else {
            $result['error'] = 'error';
            return $result;
        }
    }

    /**
     * 获得GSM补邻区数据
     *
     * @return string GSM补邻区数据
     */
    public function getGSMNeighborData()
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

        $dateTime = Input::get('dateTime');

        $result = array();
        $return = array();
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('MR', $dbname);
        $table = 'mreServerNeighIrat';
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
        $sql = "select * from $table where ecgi = '$ecgi' and datetime_id like '" . $dateTime . "%'";
        $rs = $db->query($sql);
        if (!$rs) {
            $return["total"] = 0;
            $result['rows'] = [];
            $return['records'] = [];
            echo json_encode($return);
            return;
        }
        $rows = $rs->fetchall(PDO::FETCH_ASSOC);
        $sqlNum = "select count(*) as num from $table where ecgi = '$ecgi' and datetime_id like '" . $dateTime . "%' AND isdefined=0;";
        $rs1 = $db->query($sqlNum);
        if (!$rs1) {
            $return["total"] = 0;
            $return["total_is0"] = 0;
            $result['rows'] = [];
            $return['records'] = [];
            echo json_encode($return);
            return;
        }
        $num = $rs1->fetchall(PDO::FETCH_ASSOC);
        $return["total_is0"] = $num[0]['num'];

        $return["total"] = count($rows);
        $rowsId = array();
        foreach ($rows as $row) {
            array_shift($row);
            array_push($rowsId, $row);
        }
        $result['rows'] = $rowsId;
        $sql = "select * from $table where ecgi = '$ecgi' and datetime_id like '" . $dateTime . "%' order by datetime_id"; // $limit
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $allData = array();
        foreach ($rows as $row) {
            array_push($allData, $row);
        }
        $return['records'] = $allData;
        echo json_encode($return);
    }

    /**
     * 获得低接入小区发生时间(天)列表
     *
     * @return array 低接入小区发生时间(天)列表
     */
    public function getlowTime()
    {
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $table = 'lowAccessCell';
        $result = array();
        $sql = "select distinct day_id from $table";
        $rs = $db->query($sql, PDO::FETCH_ASSOC);
        $test = [];
        if ($rs) {
            $rows = $rs->fetchall();
            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    $arr = explode(' ', $row['day_id']);
                    if ($arr[0] == '0000-00-00') {
                        continue;
                    }
                    array_push($test, $arr[0]);
                }
                return $test;
            } else {
                $result['error'] = 'error';
                return $result;
            }
        } else {
            $result['error'] = 'error';
            return $result;
        }
    }

    /**
     * 获得高掉线小区发生时间(天)列表
     *
     * @return array 高掉线小区发生时间(天)列表
     */
    public function getHighLostTime()
    {
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $table = 'highLostCell';
        $result = array();
        $sql = "select distinct day_id from $table";
        $rs = $db->query($sql, PDO::FETCH_ASSOC);
        $test = [];
        if ($rs) {
            $rows = $rs->fetchall();
            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    $arr = explode(' ', $row['day_id']);
                    if ($arr[0] == '0000-00-00') {
                        continue;
                    }
                    array_push($test, $arr[0]);
                }
                return $test;
            } else {
                $result['error'] = 'error';
                return $result;
            }
        } else {
            $result['error'] = 'error';
            return $result;
        }
    }

    /**
     * 获得切换差小区发生时间(天)列表
     *
     * @return array 切换差小区发生时间(天)列表
     */
    public function getBadHandoverTime()
    {
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $table = 'badHandoverCell';
        $result = array();
        $sql = "select distinct day_id from $table";
        $rs = $db->query($sql, PDO::FETCH_ASSOC);
        $test = [];
        if ($rs) {
            $rows = $rs->fetchall();
            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    $arr = explode(' ', $row['day_id']);
                    if ($arr[0] == '0000-00-00') {
                        continue;
                    }
                    array_push($test, $arr[0]);
                }
                return $test;
            } else {
                $result['error'] = 'error';
                return $result;
            }
        } else {
            $result['error'] = 'error';
            return $result;
        }
    }

    /**
     * 获得单小区重叠覆盖次数
     *
     * @return string 单小区重叠覆盖次数
     */
    public function getOverlapCeakCoverNum()
    {
        $city = Input::get('city');
        $cell = Input::get('cell');
        $startTime = Input::get('startTime');
        $endTime = Input::get('endTime');
        $dbc = new DataBaseConnection();
        $database = $dbc->getMRDatabaseByCity($city);
        $db = $dbc->getDB('MR', $database);
        // $sql = "SELECT COUNT(*) as num FROM mroOverCoverage t LEFT JOIN GLOBAL.siteLte s ON t.ecgi=s.ecgi WHERE t.datetime_id>='" . $startTime . " 08:00:00' AND t.datetime_id<='" . $endTime . " 08:00:00' AND s.cellName='" . $cell . "';";
        $sql = "SELECT COUNT(*) as num FROM mroOverCoverage t, GLOBAL.siteLte s WHERE t.ecgi=s.ecgi AND t.datetime_id>='" . $startTime . " 08:00:00' AND t.datetime_id<='" . $endTime . " 08:00:00' AND s.cellName='" . $cell . "';";
        $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        print_r($row[0]['num']);
    }

    /**
     * 获得单小区PCI冲突数目
     *
     * @return string 单小区PCI冲突数目
     */
    public function getConflictNum()
    {
        $cell = Input::get('cell');
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        $sql = "SELECT taskName FROM task WHERE type = 'parameter' AND STATUS = 'complete' ORDER BY createTime DESC LIMIT 1";
        $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $taskName = $row[0]['taskName'];
        $db1 = $dbc->getDB('mongs', $taskName);
        $sql1 = "select * from TempEUtranCellRelationNeighOfPci where EUtranCellTDD = '$cell'";
        $row1 = $db1->query($sql1)->fetchAll(PDO::FETCH_ASSOC);
        $firstConflictNum = count($row1);

        $sql2 = "select * from TempEUtranCellRelationNeighOfNeighPci where EUtranCellTDD = '$cell'";
        $row2 = $db1->query($sql2)->fetchAll(PDO::FETCH_ASSOC);
        $secondConflictNum = count($row2);

        $result = array();
        $result['firstConflictNum'] = $firstConflictNum;
        $result['secondConflictNum'] = $secondConflictNum;
        echo json_encode($result);
    }

    /**
     * 获得单小区PRB诊断数目
     *
     * @return string 单小区PRB诊断数目
     */
    public function getPrbNum()
    {
        $cell = Input::get('cell');
        $day_from = Input::get('day_from');
        $day_to = Input::get('day_to');
        $hours = Input::get('hours');
        $filter = "WHERE day_id >= '" . $day_from . "' AND day_id <= '" . $day_to . "' AND cell = '" . $cell . "'";
        $sql = "SELECT
                    PRB1上行干扰电平,PRB2上行干扰电平,PRB3上行干扰电平,PRB4上行干扰电平,PRB5上行干扰电平,PRB6上行干扰电平,PRB7上行干扰电平,PRB8上行干扰电平,PRB9上行干扰电平,PRB10上行干扰电平,PUCCH上行干扰电平,PRB11上行干扰电平,PRB12上行干扰电平,PRB13上行干扰电平,PRB14上行干扰电平,PRB15上行干扰电平,PRB16上行干扰电平,PRB17上行干扰电平,PRB18上行干扰电平,PRB19上行干扰电平,PRB20上行干扰电平,PRB21上行干扰电平,PRB22上行干扰电平,PRB23上行干扰电平,PRB24上行干扰电平,PRB25上行干扰电平,PRB26上行干扰电平,PRB27上行干扰电平,PRB28上行干扰电平,PRB29上行干扰电平,PRB30上行干扰电平,PRB31上行干扰电平,PRB32上行干扰电平,PRB33上行干扰电平,PRB34上行干扰电平,PRB35上行干扰电平,PRB36上行干扰电平,PRB37上行干扰电平,PRB38上行干扰电平,PRB39上行干扰电平,PRB40上行干扰电平,PRB41上行干扰电平,PRB42上行干扰电平,PRB43上行干扰电平,PRB44上行干扰电平,PRB45上行干扰电平,PRB46上行干扰电平,PRB47上行干扰电平,PRB48上行干扰电平,PRB49上行干扰电平,PRB50上行干扰电平,PRB51上行干扰电平,PRB52上行干扰电平,PRB53上行干扰电平,PRB54上行干扰电平,PRB55上行干扰电平,PRB56上行干扰电平,PRB57上行干扰电平,PRB58上行干扰电平,PRB59上行干扰电平,PRB60上行干扰电平,PRB61上行干扰电平,PRB62上行干扰电平,PRB63上行干扰电平,PRB64上行干扰电平,PRB65上行干扰电平,PRB66上行干扰电平,PRB67上行干扰电平,PRB68上行干扰电平,PRB69上行干扰电平,PRB70上行干扰电平,PRB71上行干扰电平,PRB72上行干扰电平,PRB73上行干扰电平,PRB74上行干扰电平,PRB75上行干扰电平,PRB76上行干扰电平,PRB77上行干扰电平,PRB78上行干扰电平,PRB79上行干扰电平,PRB80上行干扰电平,PRB81上行干扰电平,PRB82上行干扰电平,PRB83上行干扰电平,PRB84上行干扰电平,PRB85上行干扰电平,PRB86上行干扰电平,PRB87上行干扰电平,PRB88上行干扰电平,PRB89上行干扰电平,PRB90上行干扰电平,PRB91上行干扰电平,PRB92上行干扰电平,PRB93上行干扰电平,PRB94上行干扰电平,PRB95上行干扰电平,PRB96上行干扰电平,PRB97上行干扰电平,PRB98上行干扰电平,PRB99上行干扰电平,PRB100上行干扰电平
                FROM
                    interfereCell_one $filter";
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('autokpi', 'AutoKPI');
        $rows = $db->query($sql)->fetchall(PDO::FETCH_NUM);
        $num = 0;
        foreach ($rows as $value) {
            $flag = 0;
            foreach ($value as $v) {
                if ($v >= -105) {
                    $flag = $flag + 1;
                }
                if ($flag > 33) {
                    $num = $num + 1;
                    break;
                }
            }
        }
        $items = array();
        array_push($items, $num);

        $num = 'null';
        if ($hours != '') {
            $num = 0;
            $hour = implode(',', $hours);
            $filter = $filter . " AND hour_id IN ($hour)";
            $sql = "SELECT
                    PRB1上行干扰电平,PRB2上行干扰电平,PRB3上行干扰电平,PRB4上行干扰电平,PRB5上行干扰电平,PRB6上行干扰电平,PRB7上行干扰电平,PRB8上行干扰电平,PRB9上行干扰电平,PRB10上行干扰电平,PUCCH上行干扰电平,PRB11上行干扰电平,PRB12上行干扰电平,PRB13上行干扰电平,PRB14上行干扰电平,PRB15上行干扰电平,PRB16上行干扰电平,PRB17上行干扰电平,PRB18上行干扰电平,PRB19上行干扰电平,PRB20上行干扰电平,PRB21上行干扰电平,PRB22上行干扰电平,PRB23上行干扰电平,PRB24上行干扰电平,PRB25上行干扰电平,PRB26上行干扰电平,PRB27上行干扰电平,PRB28上行干扰电平,PRB29上行干扰电平,PRB30上行干扰电平,PRB31上行干扰电平,PRB32上行干扰电平,PRB33上行干扰电平,PRB34上行干扰电平,PRB35上行干扰电平,PRB36上行干扰电平,PRB37上行干扰电平,PRB38上行干扰电平,PRB39上行干扰电平,PRB40上行干扰电平,PRB41上行干扰电平,PRB42上行干扰电平,PRB43上行干扰电平,PRB44上行干扰电平,PRB45上行干扰电平,PRB46上行干扰电平,PRB47上行干扰电平,PRB48上行干扰电平,PRB49上行干扰电平,PRB50上行干扰电平,PRB51上行干扰电平,PRB52上行干扰电平,PRB53上行干扰电平,PRB54上行干扰电平,PRB55上行干扰电平,PRB56上行干扰电平,PRB57上行干扰电平,PRB58上行干扰电平,PRB59上行干扰电平,PRB60上行干扰电平,PRB61上行干扰电平,PRB62上行干扰电平,PRB63上行干扰电平,PRB64上行干扰电平,PRB65上行干扰电平,PRB66上行干扰电平,PRB67上行干扰电平,PRB68上行干扰电平,PRB69上行干扰电平,PRB70上行干扰电平,PRB71上行干扰电平,PRB72上行干扰电平,PRB73上行干扰电平,PRB74上行干扰电平,PRB75上行干扰电平,PRB76上行干扰电平,PRB77上行干扰电平,PRB78上行干扰电平,PRB79上行干扰电平,PRB80上行干扰电平,PRB81上行干扰电平,PRB82上行干扰电平,PRB83上行干扰电平,PRB84上行干扰电平,PRB85上行干扰电平,PRB86上行干扰电平,PRB87上行干扰电平,PRB88上行干扰电平,PRB89上行干扰电平,PRB90上行干扰电平,PRB91上行干扰电平,PRB92上行干扰电平,PRB93上行干扰电平,PRB94上行干扰电平,PRB95上行干扰电平,PRB96上行干扰电平,PRB97上行干扰电平,PRB98上行干扰电平,PRB99上行干扰电平,PRB100上行干扰电平
                FROM
                    interfereCell_one $filter";
            $rows = $db->query($sql)->fetchall(PDO::FETCH_NUM);
            foreach ($rows as $value) {
                $flag = 0;
                foreach ($value as $v) {
                    if ($v >= -105) {
                        $flag = $flag + 1;
                    }
                    if ($flag > 33) {
                        $num = $num + 1;
                        break;
                    }
                }
            }
        }
        array_push($items, $num);
        return json_encode($items);
    }

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
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $cell = input::get('cell');
        $sql = "SELECT * FROM LowAccessCellTableEveryOneHour WHERE cell='$cell';";
        $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
        array_push($returnData, intval($rows[0]['Polar-告警']), intval($rows[0]['Polar-弱覆盖']), intval($rows[0]['Polar-重叠覆盖']), intval($rows[0]['Polar-质差']), intval($rows[0]['Polar-邻区']), intval($rows[0]['Polar-干扰']), intval($rows[0]['Polar-参数']));
        array_push($polar, intval($rows[0]['Polar-高话务']));
        $return['data'] = $returnData;
        $return['polar'] = $polar;
        return $return;
    }
    // public function getPolarMapData()
    // {
    //     $return = [];
    //     $polar = [];
    //     $returnData = [];
    //     $cell = input::get('cell');
    //     $city = input::get('city');
    //     $startTime = input::get('startTime');
    //     $endTime = input::get('endTime');
    //     $dsn = new DataBaseConnection();
    //    /* $rowData_2 = explode('_', $cell);
    //     $rowData_3 = $rowData_2[0];
    //     if ($rowData_3 == $cell) {
    //         $rowData_3 = substr($rowData_3, 0, strlen($rowData_3) - 1);
    //     }*/
    //     $db = $dsn->getDB('mongs', 'mongs');
    //     $sql = "SELECT siteName from siteLte WHERE cellName='$cell';";
    //     $row = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
    //     $erbs = $row[0]['siteName'];
    //     $sql = "SELECT max(num) as num FROM( SELECT SP_text,t.access AS num,t.alarmNameE FROM FMA_alarm_log r LEFT JOIN mongs.AlarmInfo t ON r.SP_text= t.alarmNameE WHERE DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $endTime . "' AND meContext = '" . $erbs . "' GROUP BY access ORDER BY access DESC)t; ";
    //     // $sql = "SELECT max(num) as num FROM( SELECT SP_text,t.access AS num,t.alarmNameE FROM FMA_alarm_log r, mongs.AlarmInfo t WHERE r.SP_text= t.alarmNameE AND DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $endTime . "' AND meContext = '" . $erbs . "' GROUP BY access ORDER BY access DESC)t; ";
    //     // print_r($sql);return;
    //     $db = $dsn->getDB('mongs', 'Alarm');
    //     $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    //     if (count($row) == 0) {
    //         array_push($returnData, 0);
    //     } else {
    //         if ($row[0]['num'] > 100) {
    //             array_push($returnData, 100);
    //         } else {
    //             array_push($returnData, intval($row[0]['num']));
    //         }
    //     }
    //     $db = $dsn->getDB('mongs', 'AutoKPI');
    //     $sql = "SELECT
    //                 #ROUND(AVG(`RSRP<-116的比例`)*10, 0) AS num
    //                 AVG(`RSRP<-116的比例`) AS num
    //             FROM
    //                 lowAccessCell_ex
    //             WHERE
    //                 day_id >= '" . $startTime . "'
    //             AND day_id <= '" . $endTime . "'
    //             AND cell = '" . $cell . "';";
    //     $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);     //弱覆盖
    //     $weakCoverRateFlag = '';                                                        //弱覆盖
    //     if (count($row) == 0) {
    //         array_push($returnData, 0);
    //         $weakCoverRateFlag = 0;
    //     } else {
    //         if ($row[0]['num'] > 20) {
    //             array_push($returnData, 100);  
    //         } else if($row[0]['num'] < 2){
    //             array_push($returnData, 0);
    //         } else {
    //             $num = round($row[0]['num']*100/18, 2);
    //             if($num > 100) {
    //                 array_push($returnData, 100);  
    //             }else {
    //                 array_push($returnData, $num);
    //             }               
    //         }
    //         if($row[0]['num'] > 11){
    //             $weakCoverRateFlag = 1;
    //         }else {
    //             $weakCoverRateFlag = 0;
    //         }
    //     }

    //     $database = $dsn->getMRDatabaseByCity($city);
    //     $db = $dsn->getDB('MR', $database);
    //     // $sql = "SELECT
    //     //             ROUND(rate*100*5, 0) AS num
    //     //         FROM
    //     //             mroOverCoverage_day
    //     //         LEFT JOIN GLOBAL.siteLte ON mroOverCoverage_day.ecgi = siteLte.ecgi
    //     //         WHERE
    //     //             dateId = '" . $startTime . "' AND cellName='" . $cell . "';";
    //     $sql = "SELECT
    //                 ROUND(rate*100*5, 0) AS num
    //             FROM
    //                 mroOverCoverage_day
    //             , GLOBAL.siteLte
    //             WHERE
    //                 mroOverCoverage_day.ecgi = siteLte.ecgi
    //             AND dateId = '" . $startTime . "' AND cellName='" . $cell . "';";            
    //     $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);    //重叠覆盖
    //     $overCoverFlag = '';
    //     if (count($row) == 0) {
    //         array_push($returnData, 0);
    //         $overCoverFlag = 0;
    //     } else {
    //         if ($row[0]['num'] > 100) {
    //             array_push($returnData, 100);
    //         } else {
    //             array_push($returnData, intval($row[0]['num']));
    //         }
    //         if($row[0]['num'] > 5){
    //             $weakCoverRateFlag = 1;
    //         }else {
    //             $weakCoverRateFlag = 0;
    //         }
    //     }
    //     $db = $dsn->getDB('mongs', 'AutoKPI');
    //     $sql = "SELECT
    //                 AVG(`RSRQ<-15.5的比例`) AS num
    //                 #ROUND(AVG(`RSRQ<-15.5的比例`)*10, 0) AS num
    //             FROM
    //                 lowAccessCell_ex
    //             WHERE
    //                 day_id >= '" . $startTime . "'
    //             AND day_id <= '" . $endTime . "'
    //             AND cell = '" . $cell . "';";
    //     $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);  //质差
    //     // dump($row);
    //     if (count($row) == 0) {
    //         array_push($returnData, 0);
    //     } else {
    //         if($row[0]['num'] == 0){
    //             array_push($returnData, 0);
    //         }else {
    //             if($weakCoverRateFlag == 0) {   //非弱覆盖
       //              if($overCoverFlag == 0) {    //非重叠覆盖
       //                  $point = $row[0]['num']*5;
       //                  if($point >100) {
       //                      array_push($returnData, 100);
       //                  }else {
       //                      array_push($returnData, $point);
       //                  }        
       //              }else {
       //                  $point = 50 + $row[0]['num']*2.5;
       //                  if($point >100) {
       //                      array_push($returnData, 100);
       //                  }else {
       //                      array_push($returnData, $point);
       //                  }    
       //              }
       //          }else {
    //                 $point = 50 + $row[0]['num']*2.5;
    //                 if($point >100) {
    //                     array_push($returnData, 100);
    //                 }else {
    //                     array_push($returnData, $point);
    //                 }    
    //             }
    //         }
    //         /*if ($row[0]['num'] > 100) {
    //             array_push($returnData, 100);
    //         } else {
    //             array_push($returnData, intval($row[0]['num']));
    //         }*/
    //     }
    //     $db = $dsn->getDB('mongs', 'mongs');
    //     $sql = "select ecgi from siteLte where cellName = '$cell'";
    //     $res = $db->query($sql);
    //     $row = $res->fetchAll(PDO::FETCH_ASSOC);
    //     if (count($row) == 0) {
    //         array_push($returnData, 0);
    //     } else {
    //         $ecgi = $row[0]['ecgi'];
    //         $cityEN = Input::get('city');
    //         $cityCH = $this->encityToCHcity($cityEN);
    //         $dbname = $this->getMRDatabase($cityCH);
    //         $db = $dsn->getDB('MR', $dbname);
    //         $table = 'mreServeNeigh_day';
    //         $sqlNum = "select count(*) as num from $table where isdefined_direct=0 and ecgi = '$ecgi' AND distance_direct<1.5 and dateId >= '" . $startTime . "';";   //邻区
    //         $row = $db->query($sqlNum)->fetchAll(PDO::FETCH_ASSOC);
    //         // dump($sqlNum);
    //         if (count($row) == 0) {
    //             array_push($returnData, 0);
    //         } else {
    //             if($row[0]['num'] == 0) {
    //                 array_push($returnData, 0);
    //             }else {
    //                 if($weakCoverRateFlag == 1){    //非弱覆盖小区
       //                  $num = intval($row[0]['num']*10);
       //                  if($num > 100){
       //                      array_push($returnData, 100);
       //                  }else {
       //                      array_push($returnData, $num);
       //                  }                   
       //              }else {                       //弱覆盖高于11%呈现弱覆盖校区
       //                  $num = intval(50+$row[0]['num']*5);
       //                  if($num > 100){
       //                      array_push($returnData, 100);
       //                  }else {
       //                      array_push($returnData, $num);
       //                  } 
       //              }
    //             }
                
    //             /*if ($row[0]['num'] > 100) {
    //                 array_push($returnData, 100);
    //             } else {
    //                 array_push($returnData, intval($row[0]['num']));
    //             }*/
    //         }
    //     }
    //     $db = $dsn->getDB('mongs', 'AutoKPI');
    //     $sql = "SELECT
    //                 AVG(PRB1上行干扰电平) AS PRB1上行干扰电平,
    //                 AVG(PRB2上行干扰电平) AS PRB2上行干扰电平,
    //                 AVG(PRB3上行干扰电平) AS PRB3上行干扰电平,
    //                 AVG(PRB4上行干扰电平) AS PRB4上行干扰电平,
    //                 AVG(PRB5上行干扰电平) AS PRB5上行干扰电平,
    //                 AVG(PRB6上行干扰电平) AS PRB6上行干扰电平,
    //                 AVG(PRB7上行干扰电平) AS PRB7上行干扰电平,
    //                 AVG(PRB8上行干扰电平) AS PRB8上行干扰电平,
    //                 AVG(PRB9上行干扰电平) AS PRB9上行干扰电平,
    //                 AVG(PRB10上行干扰电平) AS PRB10上行干扰电平,
    //                 AVG(PRB11上行干扰电平) AS PRB11上行干扰电平,
    //                 AVG(PRB12上行干扰电平) AS PRB12上行干扰电平,
    //                 AVG(PRB13上行干扰电平) AS PRB13上行干扰电平,
    //                 AVG(PRB14上行干扰电平) AS PRB14上行干扰电平,
    //                 AVG(PRB15上行干扰电平) AS PRB15上行干扰电平,
    //                 AVG(PRB16上行干扰电平) AS PRB16上行干扰电平,
    //                 AVG(PRB17上行干扰电平) AS PRB17上行干扰电平,
    //                 AVG(PRB18上行干扰电平) AS PRB18上行干扰电平,
    //                 AVG(PRB19上行干扰电平) AS PRB19上行干扰电平,
    //                 AVG(PRB20上行干扰电平) AS PRB20上行干扰电平,
    //                 AVG(PRB21上行干扰电平) AS PRB21上行干扰电平,
    //                 AVG(PRB22上行干扰电平) AS PRB22上行干扰电平,
    //                 AVG(PRB23上行干扰电平) AS PRB23上行干扰电平,
    //                 AVG(PRB24上行干扰电平) AS PRB24上行干扰电平,
    //                 AVG(PRB25上行干扰电平) AS PRB25上行干扰电平,
    //                 AVG(PRB26上行干扰电平) AS PRB26上行干扰电平,
    //                 AVG(PRB27上行干扰电平) AS PRB27上行干扰电平,
    //                 AVG(PRB28上行干扰电平) AS PRB28上行干扰电平,
    //                 AVG(PRB29上行干扰电平) AS PRB29上行干扰电平,
    //                 AVG(PRB30上行干扰电平) AS PRB30上行干扰电平,
    //                 AVG(PRB31上行干扰电平) AS PRB31上行干扰电平,
    //                 AVG(PRB32上行干扰电平) AS PRB32上行干扰电平,
    //                 AVG(PRB33上行干扰电平) AS PRB33上行干扰电平,
    //                 AVG(PRB34上行干扰电平) AS PRB34上行干扰电平,
    //                 AVG(PRB35上行干扰电平) AS PRB35上行干扰电平,
    //                 AVG(PRB36上行干扰电平) AS PRB36上行干扰电平,
    //                 AVG(PRB37上行干扰电平) AS PRB37上行干扰电平,
    //                 AVG(PRB38上行干扰电平) AS PRB38上行干扰电平,
    //                 AVG(PRB39上行干扰电平) AS PRB39上行干扰电平,
    //                 AVG(PRB40上行干扰电平) AS PRB40上行干扰电平,
    //                 AVG(PRB41上行干扰电平) AS PRB41上行干扰电平,
    //                 AVG(PRB42上行干扰电平) AS PRB42上行干扰电平,
    //                 AVG(PRB43上行干扰电平) AS PRB43上行干扰电平,
    //                 AVG(PRB44上行干扰电平) AS PRB44上行干扰电平,
    //                 AVG(PRB45上行干扰电平) AS PRB45上行干扰电平,
    //                 AVG(PRB46上行干扰电平) AS PRB46上行干扰电平,
    //                 AVG(PRB47上行干扰电平) AS PRB47上行干扰电平,
    //                 AVG(PRB48上行干扰电平) AS PRB48上行干扰电平,
    //                 AVG(PRB49上行干扰电平) AS PRB49上行干扰电平,
    //                 AVG(PRB50上行干扰电平) AS PRB50上行干扰电平,
    //                 AVG(PRB51上行干扰电平) AS PRB51上行干扰电平,
    //                 AVG(PRB52上行干扰电平) AS PRB52上行干扰电平,
    //                 AVG(PRB53上行干扰电平) AS PRB53上行干扰电平,
    //                 AVG(PRB54上行干扰电平) AS PRB54上行干扰电平,
    //                 AVG(PRB55上行干扰电平) AS PRB55上行干扰电平,
    //                 AVG(PRB56上行干扰电平) AS PRB56上行干扰电平,
    //                 AVG(PRB57上行干扰电平) AS PRB57上行干扰电平,
    //                 AVG(PRB58上行干扰电平) AS PRB58上行干扰电平,
    //                 AVG(PRB59上行干扰电平) AS PRB59上行干扰电平,
    //                 AVG(PRB60上行干扰电平) AS PRB60上行干扰电平,
    //                 AVG(PRB61上行干扰电平) AS PRB61上行干扰电平,
    //                 AVG(PRB62上行干扰电平) AS PRB62上行干扰电平,
    //                 AVG(PRB63上行干扰电平) AS PRB63上行干扰电平,
    //                 AVG(PRB64上行干扰电平) AS PRB64上行干扰电平,
    //                 AVG(PRB65上行干扰电平) AS PRB65上行干扰电平,
    //                 AVG(PRB66上行干扰电平) AS PRB66上行干扰电平,
    //                 AVG(PRB67上行干扰电平) AS PRB67上行干扰电平,
    //                 AVG(PRB68上行干扰电平) AS PRB68上行干扰电平,
    //                 AVG(PRB69上行干扰电平) AS PRB69上行干扰电平,
    //                 AVG(PRB70上行干扰电平) AS PRB70上行干扰电平,
    //                 AVG(PRB71上行干扰电平) AS PRB71上行干扰电平,
    //                 AVG(PRB72上行干扰电平) AS PRB72上行干扰电平,
    //                 AVG(PRB73上行干扰电平) AS PRB73上行干扰电平,
    //                 AVG(PRB74上行干扰电平) AS PRB74上行干扰电平,
    //                 AVG(PRB75上行干扰电平) AS PRB75上行干扰电平,
    //                 AVG(PRB76上行干扰电平) AS PRB76上行干扰电平,
    //                 AVG(PRB77上行干扰电平) AS PRB77上行干扰电平,
    //                 AVG(PRB78上行干扰电平) AS PRB78上行干扰电平,
    //                 AVG(PRB79上行干扰电平) AS PRB79上行干扰电平,
    //                 AVG(PRB80上行干扰电平) AS PRB80上行干扰电平,
    //                 AVG(PRB81上行干扰电平) AS PRB81上行干扰电平,
    //                 AVG(PRB82上行干扰电平) AS PRB82上行干扰电平,
    //                 AVG(PRB83上行干扰电平) AS PRB83上行干扰电平,
    //                 AVG(PRB84上行干扰电平) AS PRB84上行干扰电平,
    //                 AVG(PRB85上行干扰电平) AS PRB85上行干扰电平,
    //                 AVG(PRB86上行干扰电平) AS PRB86上行干扰电平,
    //                 AVG(PRB87上行干扰电平) AS PRB87上行干扰电平,
    //                 AVG(PRB88上行干扰电平) AS PRB88上行干扰电平,
    //                 AVG(PRB89上行干扰电平) AS PRB89上行干扰电平,
    //                 AVG(PRB90上行干扰电平) AS PRB90上行干扰电平,
    //                 AVG(PRB91上行干扰电平) AS PRB91上行干扰电平,
    //                 AVG(PRB92上行干扰电平) AS PRB92上行干扰电平,
    //                 AVG(PRB93上行干扰电平) AS PRB93上行干扰电平,
    //                 AVG(PRB94上行干扰电平) AS PRB94上行干扰电平,
    //                 AVG(PRB95上行干扰电平) AS PRB95上行干扰电平,
    //                 AVG(PRB96上行干扰电平) AS PRB96上行干扰电平,
    //                 AVG(PRB97上行干扰电平) AS PRB97上行干扰电平,
    //                 AVG(PRB98上行干扰电平) AS PRB98上行干扰电平,
    //                 AVG(PRB99上行干扰电平) AS PRB99上行干扰电平,
    //                 AVG(PRB100上行干扰电平) AS PRB100上行干扰电平
    //             FROM
    //                 interfereCell
    //             WHERE
    //                 cell = '" . $cell . "'
    //             AND day_id >= '" . $startTime . "'
    //             AND day_id <= '" . $endTime . "';";
    //     $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    //     // dump($sql);
    //     $i = 0;
    //     $avg = 0;
    //     if (count($row) == 0) {
    //         array_push($returnData, 0);
    //     } else {
    //         foreach ($row[0] as $val) {
    //             if ($val == null) {
    //                 continue;
    //             }
    //             $avg = $avg + $val;
    //             $i++;
    //         }
    //         if ($i == 0) {
    //             array_push($returnData, 0);
    //         } else {
    //             $avg = $avg / $i;
    //             if($avg >= -102) {
    //                 array_push($returnData, 100);
    //             } elseif($avg <= -112) {
    //                 array_push($returnData, 0);
    //             } else {
    //                 $point = ($avg + 112)*10;
    //                 if($point > 100) {
    //                     array_push($returnData, 100);
    //                 }else {
    //                     array_push($returnData, $point);
    //                 }
    //             }
    //             /*$avg = ($avg / $i + 120) * 5;
    //             if ($avg > 100) {
    //                 array_push($returnData, 100);
    //             } else {
    //                 array_push($returnData, intval($avg));
    //             }*/
    //         }
    //     }

    //     $db = $dsn->getDB('mongs', 'AutoKPI'); //高话务
    //     $sql = "SELECT SUM(`最大RRC连接用户数`)/20 AS num
    //             FROM lowAccessCell_ex
    //             WHERE
    //                 cell = '" . $cell . "'
    //             AND day_id >= '" . $startTime . "'
    //             AND day_id <= '" . $endTime . "';";
    //     $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    //     if (count($row) == 0) {
    //         array_push($polar, 0);
    //     } else {
    //         if ($row[0]['num'] > 100) {
    //             array_push($polar, 100);
    //         } else {
    //             array_push($polar, intval($row[0]['num']));
    //         }
    //     }


    //     $date = new DateTime();    //参数
    //     $date->sub(new DateInterval('P1D'));
    //     $yesDate = $date->format('ymd');
    //     $dbname = 'kget' . $yesDate;
    //     $table = 'ParaCheckBaseline';
    //     $dbc = new DataBaseConnection();
    //     $db = $dbc->getDB('mongs', 'mongs');
    //     $sql = "select siteName from  siteLte where cellName='$cell';";
    //     $row = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
    //     $erbs = '';
    //     if (count($row) == 0) {
    //         array_push($returnData, 0);
    //     }else {
    //         $erbs = $row[0]['siteName'];
    //     }

    //     $db = $dbc->getDB('mongs', $dbname);
    //     $sql = "select highTraffic from  $table where meContext='$erbs';";
    //     $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    //     if (count($row) == 0) {
    //         array_push($returnData, 0);
    //     } else {
    //         if ($row[0]['highTraffic'] == 'YES') {
    //             array_push($returnData, 100);
    //         } else {
    //             $sql = "select count(*) as num from  $table where cellId='$cell';";
    //             $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    //             // print_r($row);
    //             if(count($row) == 0) {
    //                 array_push($returnData, 0);
    //             }else {
    //                 if($row[0]['num'] == 0) {
    //                     array_push($returnData, 0);
    //                 }else {
    //                     array_push($returnData, 50);
    //                 }
    //             }    
    //         }
    //     }

    //     $return['data'] = $returnData;
    //     $return['polar'] = $polar;
    //     return $return;
    // }

    /**
     * 获得LTE补邻区数据表头
     *
     * @return array LTE补邻区数据表头
     */
    public function getLTENeighborHeaderModel()
    {
        $cityEN = Input::get('city');
        $cityCH = $this->encityToCHcity($cityEN);
        $dbname = $this->getMRDatabase($cityCH);

        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('MR', $dbname);
        $table = 'mreServeNeigh';
        $result = array();
        $sql = "select * from $table limit 1";
        $rs = $db->query($sql, PDO::FETCH_ASSOC);
        if ($rs) {
            $rows = $rs->fetchall();
            if (count($rows) > 0) {
                return $rows[0];
            } else {
                $result['error'] = 'error';
                return $result;
            }
        } else {
            $result['error'] = 'error';
            return $result;
        }

    }//end getLTENeighborHeaderModel()


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

        $dateTime = Input::get('dateTime');

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

        $sql = "select * from $table where isdefined_direct=0 and ecgi = '$ecgi' AND distance_direct<0.8 and dateId >= '" . $dateTime . "';";
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
    }

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

        $day_from = Input::get('dateTime');
        $day_to = Input::get('endTime');

        $return = array();
        $sql = "select id,day_id,hour_id,city,subNetwork,cell,无线接通率,`RSRP<-116的比例` from lowAccessCell_ex where day_id >= '" . $day_from . "' AND day_id <= '" . $day_to . "' AND cell='" . $cell . "'";
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

        $day_from = Input::get('dateTime');
        $day_to = Input::get('endTime');

        $return = array();
        $sql = "select id,day_id,hour_id,city,subNetwork,cell,无线接通率,`RSRQ<-15.5的比例` from lowAccessCell_ex where day_id >= '" . $day_from . "' AND day_id <= '" . $day_to . "' AND cell='" . $cell . "'";
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $allData = array();
        foreach ($rows as $row) {
            array_push($allData, $row);
        }
        $return['content'] = "id,day_id,hour_id,city,subNetwork,cell,无线接通率,RSRQ<-15.5的比例";
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
        $day_from = Input::get('dateTime');
        $return = array();
        $dsn = new DataBaseConnection();
        $cityEN = Input::get('city');
        $cityCH = $this->encityToCHcity($cityEN);
        $dbname = $this->getMRDatabase($cityCH);
        $db = $dsn->getDB('MR', $dbname);

        // $sql = "SELECT
        //             dateId,mroOverCoverage_day.ecgi,sample,all_sample,rate,intensity,siteLte.dir,siteLte.tiltM,siteLte.tiltE,siteLte.antHeight,siteLte.tac
        //         FROM
        //             mroOverCoverage_day
        //         LEFT JOIN GLOBAL .siteLte ON mroOverCoverage_day.ecgi = siteLte.ecgi
        //         WHERE
        //             dateId = '" . $day_from . "'
        //         AND cellName = '" . $cell . "';";
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
    }

    /**
     * 获得诊断数据
     *
     * @return array 诊断数据
     */
    public function getNumOfDiagnosisDataMR() 
    {   //邻区
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $sql = "SELECT 需要加邻区数量 FROM LowAccessCellTableEveryOneHour WHERE cell='$cell';";
        $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $result = $row[0]['需要加邻区数量'];
        return $result;
    }
    // public function getNumOfDiagnosisDataMR() {
    //     $city = Input::get('city');
    //     $cell = Input::get('cell');
    //     $day_from = Input::get('dateTime');
    //     $day_to = Input::get('endTime');
    //     $result = array();
    //     $dsn = new DataBaseConnection();
    //     $db = $dsn->getDB('mongs', 'mongs');
    //     $sql = "select ecgi from siteLte where cellName = '$cell'";
    //     $res = $db->query($sql);
    //     $row = $res->fetchAll(PDO::FETCH_ASSOC);
    //     if (count($row) == 0) {
    //         array_push($result, 0);
    //     } else {
    //         $ecgi = $row[0]['ecgi'];
    //         $cityCH = $this->encityToCHcity($city);
    //         $dbname = $this->getMRDatabase($cityCH);
    //         $db = $dsn->getDB('MR', $dbname);
    //         $table = 'mreServeNeigh_day';
    //         $sql = "select count(*) as num from $table where isdefined_direct=0 and ecgi = '$ecgi' AND distance_direct<1.5 and dateId >= '" . $day_from . "';";
    //         $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
    //         array_push($result, round($row[0]['num'], 2));
    //     }
    //     return $result;
    // }

    /**
     * 获得诊断数据
     *
     * @return array 诊断数据-告警
     */
    public function getNumOfDiagnosisDataFilter_alarm() 
    {   //告警
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $sql = "SELECT 告警数量 FROM LowAccessCellTableEveryOneHour WHERE cell='$cell';";
        $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $result = $row[0]['告警数量'];
        return $result;
    }
    // public function getNumOfDiagnosisDataFilter_alarm()
    // {
    //     $city = Input::get('city');
    //     $cell = Input::get('cell');
    //     $day_from = Input::get('dateTime');
    //     $day_to = Input::get('endTime');
    //     $result = array();
    //     $dsn = new DataBaseConnection();
    //     $db = $dsn->getDB('mongs', 'mongs');
    //     $sql = "SELECT siteName from siteLte WHERE cellName='$cell';";
    //     $row = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
    //     $erbs = $row[0]['siteName'];
    //     $sql = "SELECT COUNT(*) as num FROM FMA_alarm_log r LEFT JOIN mongs.AlarmInfo t ON r.SP_text= t.alarmNameE WHERE DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $day_from . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $day_to . "' AND meContext = '" . $erbs . "';";
    //     // $sql = "SELECT COUNT(*) as num FROM FMA_alarm_log r, mongs.AlarmInfo t WHERE r.SP_text= t.alarmNameE AND DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $day_from . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $day_to . "' AND meContext = '" . $erbs . "';";
    //     $dsn = new DataBaseConnection();
    //     $db = $dsn->getDB('mongs', 'Alarm');
    //     $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
    //     array_push($result, round($row[0]['num'], 2));
    //     return $result;
    // }   

    /**
     * 获得诊断数据
     *
     * @return array 诊断数据-弱覆盖
     */
    public function getNumOfDiagnosisDataFilter_weakCover() 
    {   //弱覆盖
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $sql = "SELECT `RSRP<-116的比例` FROM LowAccessCellTableEveryOneHour WHERE cell='$cell';";
        $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $result = $row[0]['RSRP<-116的比例'];
        return $result;
    }
    // public function getNumOfDiagnosisDataFilter_weakCover()
    // {
    //     $city = Input::get('city');
    //     $cell = Input::get('cell');
    //     $day_from = Input::get('dateTime');
    //     $day_to = Input::get('endTime');
    //     $result = array();
    //     $dsn = new DataBaseConnection();
    //     $db = $dsn->getDB('mongs', 'AutoKPI');
    //     $sql = "select AVG(`RSRP<-116的比例`) as NUM from lowAccessCell_ex where day_id >= '" . $day_from . "' AND day_id <= '" . $day_to . "' AND cell='" . $cell . "'";
    //     $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
    //     if ($row[0]['NUM'] === null) {
    //         array_push($result, 0);
    //     } else {
    //         array_push($result, round($row[0]['NUM'], 2));
    //     }
    //     return $result;
    // } 

    /**
     * 获得诊断数据
     *
     * @return array 诊断数据-质差
     */
    public function getNumOfDiagnosisDataFilter_zhicha() 
    {   //质差
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $sql = "SELECT `RSRQ<-15.5的比例` FROM LowAccessCellTableEveryOneHour WHERE cell='$cell';";
        $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $result = $row[0]['RSRQ<-15.5的比例'];
        return $result;
    }
    // public function getNumOfDiagnosisDataFilter_zhicha()
    // {
    //     $city = Input::get('city');
    //     $cell = Input::get('cell');
    //     $day_from = Input::get('dateTime');
    //     $day_to = Input::get('endTime');
    //     $result = array();
    //     $dsn = new DataBaseConnection();
    //     $db = $dsn->getDB('mongs', 'AutoKPI');
    //     $sql = "select AVG(`RSRQ<-15.5的比例`) as NUM from lowAccessCell_ex where day_id >= '" . $day_from . "' AND day_id <= '" . $day_to . "' AND cell='" . $cell . "'";  //质差
    //     $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
    //     if ($row[0]['NUM'] == null) {
    //         array_push($result, 0);
    //     } else {
    //         array_push($result, round($row[0]['NUM'], 2));   //质差
    //     }
    //     return $result;
    // }

     /**
     * 获得诊断数据
     *
     * @return array 诊断数据-重叠覆盖
     */
    public function getNumOfDiagnosisDataFilter_overlapCover() 
    {   //重叠覆盖
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $sql = "SELECT `重叠覆盖度` FROM LowAccessCellTableEveryOneHour WHERE cell='$cell';";
        $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $result = $row[0]['重叠覆盖度'];
        return $result;
    }
    // public function getNumOfDiagnosisDataFilter_overlapCover()
    // {
    //     $city = Input::get('city');
    //     $cell = Input::get('cell');
    //     $day_from = Input::get('dateTime');
    //     $day_to = Input::get('endTime');
    //     $result = array();
    //     $cityCH = $this->encityToCHcity($city);
    //     $dbname = $this->getMRDatabase($cityCH);
    //     $dsn = new DataBaseConnection();
    //     $db = $dsn->getDB('MR', $dbname);
    //     // $sql = "SELECT
    //     //             avg(rate) as NUM
    //     //         FROM
    //     //             mroOverCoverage_day
    //     //         LEFT JOIN GLOBAL .siteLte ON mroOverCoverage_day.ecgi = siteLte.ecgi
    //     //         WHERE
    //     //             dateId = '" . $day_from . "'
    //     //         AND cellName = '" . $cell . "';";
    //     $sql = "SELECT
    //                 avg(rate) as NUM
    //             FROM
    //                 mroOverCoverage_day
    //             , GLOBAL .siteLte
    //             WHERE
    //                 mroOverCoverage_day.ecgi = siteLte.ecgi
    //             AND dateId = '" . $day_from . "'
    //             AND cellName = '" . $cell . "';";
    //     $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
    //     if ($row[0]['NUM'] == null || is_nan(intval($row[0]['NUM']))) {
    //         array_push($result, 0);
    //     } else {
    //         array_push($result, round($row[0]['NUM'] * 100, 2));
    //     }
    //     return $result;
    // }

    /**
     * 获得诊断数据
     *
     * @return array 诊断数据-干扰
     */
    public function getNumOfDiagnosisDataFilter_AvgPRB() 
    {   //干扰
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $sql = "SELECT `平均PRB` FROM LowAccessCellTableEveryOneHour WHERE cell='$cell';";
        $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $result = intval($row[0]['平均PRB']);
        return $result;
    }
    // public function getNumOfDiagnosisDataFilter_AvgPRB()
    // {
    //     $city = Input::get('city');
    //     $cell = Input::get('cell');
    //     $day_from = Input::get('dateTime');
    //     $day_to = Input::get('endTime');
    //     $result = array();
    //     $dsn = new DataBaseConnection();
    //     $db = $dsn->getDB('mongs', 'AutoKPI');
    //     $sql = "SELECT
    //                 AVG(PRB1上行干扰电平) AS PRB1上行干扰电平,
    //                 AVG(PRB2上行干扰电平) AS PRB2上行干扰电平,
    //                 AVG(PRB3上行干扰电平) AS PRB3上行干扰电平,
    //                 AVG(PRB4上行干扰电平) AS PRB4上行干扰电平,
    //                 AVG(PRB5上行干扰电平) AS PRB5上行干扰电平,
    //                 AVG(PRB6上行干扰电平) AS PRB6上行干扰电平,
    //                 AVG(PRB7上行干扰电平) AS PRB7上行干扰电平,
    //                 AVG(PRB8上行干扰电平) AS PRB8上行干扰电平,
    //                 AVG(PRB9上行干扰电平) AS PRB9上行干扰电平,
    //                 AVG(PRB10上行干扰电平) AS PRB10上行干扰电平,
    //                 AVG(PRB11上行干扰电平) AS PRB11上行干扰电平,
    //                 AVG(PRB12上行干扰电平) AS PRB12上行干扰电平,
    //                 AVG(PRB13上行干扰电平) AS PRB13上行干扰电平,
    //                 AVG(PRB14上行干扰电平) AS PRB14上行干扰电平,
    //                 AVG(PRB15上行干扰电平) AS PRB15上行干扰电平,
    //                 AVG(PRB16上行干扰电平) AS PRB16上行干扰电平,
    //                 AVG(PRB17上行干扰电平) AS PRB17上行干扰电平,
    //                 AVG(PRB18上行干扰电平) AS PRB18上行干扰电平,
    //                 AVG(PRB19上行干扰电平) AS PRB19上行干扰电平,
    //                 AVG(PRB20上行干扰电平) AS PRB20上行干扰电平,
    //                 AVG(PRB21上行干扰电平) AS PRB21上行干扰电平,
    //                 AVG(PRB22上行干扰电平) AS PRB22上行干扰电平,
    //                 AVG(PRB23上行干扰电平) AS PRB23上行干扰电平,
    //                 AVG(PRB24上行干扰电平) AS PRB24上行干扰电平,
    //                 AVG(PRB25上行干扰电平) AS PRB25上行干扰电平,
    //                 AVG(PRB26上行干扰电平) AS PRB26上行干扰电平,
    //                 AVG(PRB27上行干扰电平) AS PRB27上行干扰电平,
    //                 AVG(PRB28上行干扰电平) AS PRB28上行干扰电平,
    //                 AVG(PRB29上行干扰电平) AS PRB29上行干扰电平,
    //                 AVG(PRB30上行干扰电平) AS PRB30上行干扰电平,
    //                 AVG(PRB31上行干扰电平) AS PRB31上行干扰电平,
    //                 AVG(PRB32上行干扰电平) AS PRB32上行干扰电平,
    //                 AVG(PRB33上行干扰电平) AS PRB33上行干扰电平,
    //                 AVG(PRB34上行干扰电平) AS PRB34上行干扰电平,
    //                 AVG(PRB35上行干扰电平) AS PRB35上行干扰电平,
    //                 AVG(PRB36上行干扰电平) AS PRB36上行干扰电平,
    //                 AVG(PRB37上行干扰电平) AS PRB37上行干扰电平,
    //                 AVG(PRB38上行干扰电平) AS PRB38上行干扰电平,
    //                 AVG(PRB39上行干扰电平) AS PRB39上行干扰电平,
    //                 AVG(PRB40上行干扰电平) AS PRB40上行干扰电平,
    //                 AVG(PRB41上行干扰电平) AS PRB41上行干扰电平,
    //                 AVG(PRB42上行干扰电平) AS PRB42上行干扰电平,
    //                 AVG(PRB43上行干扰电平) AS PRB43上行干扰电平,
    //                 AVG(PRB44上行干扰电平) AS PRB44上行干扰电平,
    //                 AVG(PRB45上行干扰电平) AS PRB45上行干扰电平,
    //                 AVG(PRB46上行干扰电平) AS PRB46上行干扰电平,
    //                 AVG(PRB47上行干扰电平) AS PRB47上行干扰电平,
    //                 AVG(PRB48上行干扰电平) AS PRB48上行干扰电平,
    //                 AVG(PRB49上行干扰电平) AS PRB49上行干扰电平,
    //                 AVG(PRB50上行干扰电平) AS PRB50上行干扰电平,
    //                 AVG(PRB51上行干扰电平) AS PRB51上行干扰电平,
    //                 AVG(PRB52上行干扰电平) AS PRB52上行干扰电平,
    //                 AVG(PRB53上行干扰电平) AS PRB53上行干扰电平,
    //                 AVG(PRB54上行干扰电平) AS PRB54上行干扰电平,
    //                 AVG(PRB55上行干扰电平) AS PRB55上行干扰电平,
    //                 AVG(PRB56上行干扰电平) AS PRB56上行干扰电平,
    //                 AVG(PRB57上行干扰电平) AS PRB57上行干扰电平,
    //                 AVG(PRB58上行干扰电平) AS PRB58上行干扰电平,
    //                 AVG(PRB59上行干扰电平) AS PRB59上行干扰电平,
    //                 AVG(PRB60上行干扰电平) AS PRB60上行干扰电平,
    //                 AVG(PRB61上行干扰电平) AS PRB61上行干扰电平,
    //                 AVG(PRB62上行干扰电平) AS PRB62上行干扰电平,
    //                 AVG(PRB63上行干扰电平) AS PRB63上行干扰电平,
    //                 AVG(PRB64上行干扰电平) AS PRB64上行干扰电平,
    //                 AVG(PRB65上行干扰电平) AS PRB65上行干扰电平,
    //                 AVG(PRB66上行干扰电平) AS PRB66上行干扰电平,
    //                 AVG(PRB67上行干扰电平) AS PRB67上行干扰电平,
    //                 AVG(PRB68上行干扰电平) AS PRB68上行干扰电平,
    //                 AVG(PRB69上行干扰电平) AS PRB69上行干扰电平,
    //                 AVG(PRB70上行干扰电平) AS PRB70上行干扰电平,
    //                 AVG(PRB71上行干扰电平) AS PRB71上行干扰电平,
    //                 AVG(PRB72上行干扰电平) AS PRB72上行干扰电平,
    //                 AVG(PRB73上行干扰电平) AS PRB73上行干扰电平,
    //                 AVG(PRB74上行干扰电平) AS PRB74上行干扰电平,
    //                 AVG(PRB75上行干扰电平) AS PRB75上行干扰电平,
    //                 AVG(PRB76上行干扰电平) AS PRB76上行干扰电平,
    //                 AVG(PRB77上行干扰电平) AS PRB77上行干扰电平,
    //                 AVG(PRB78上行干扰电平) AS PRB78上行干扰电平,
    //                 AVG(PRB79上行干扰电平) AS PRB79上行干扰电平,
    //                 AVG(PRB80上行干扰电平) AS PRB80上行干扰电平,
    //                 AVG(PRB81上行干扰电平) AS PRB81上行干扰电平,
    //                 AVG(PRB82上行干扰电平) AS PRB82上行干扰电平,
    //                 AVG(PRB83上行干扰电平) AS PRB83上行干扰电平,
    //                 AVG(PRB84上行干扰电平) AS PRB84上行干扰电平,
    //                 AVG(PRB85上行干扰电平) AS PRB85上行干扰电平,
    //                 AVG(PRB86上行干扰电平) AS PRB86上行干扰电平,
    //                 AVG(PRB87上行干扰电平) AS PRB87上行干扰电平,
    //                 AVG(PRB88上行干扰电平) AS PRB88上行干扰电平,
    //                 AVG(PRB89上行干扰电平) AS PRB89上行干扰电平,
    //                 AVG(PRB90上行干扰电平) AS PRB90上行干扰电平,
    //                 AVG(PRB91上行干扰电平) AS PRB91上行干扰电平,
    //                 AVG(PRB92上行干扰电平) AS PRB92上行干扰电平,
    //                 AVG(PRB93上行干扰电平) AS PRB93上行干扰电平,
    //                 AVG(PRB94上行干扰电平) AS PRB94上行干扰电平,
    //                 AVG(PRB95上行干扰电平) AS PRB95上行干扰电平,
    //                 AVG(PRB96上行干扰电平) AS PRB96上行干扰电平,
    //                 AVG(PRB97上行干扰电平) AS PRB97上行干扰电平,
    //                 AVG(PRB98上行干扰电平) AS PRB98上行干扰电平,
    //                 AVG(PRB99上行干扰电平) AS PRB99上行干扰电平,
    //                 AVG(PRB100上行干扰电平) AS PRB100上行干扰电平
    //             FROM
    //                 interfereCell
    //             WHERE
    //                 cell = '" . $cell . "'
    //             AND day_id >= '" . $day_from . "'
    //             AND day_id <= '" . $day_to . "';";
    //     $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);   //干扰
    //     $i = 0;
    //     $avg = 0;
    //     if (count($row) == 0) {
    //         array_push($result, 0);
    //     } else {
    //         foreach ($row[0] as $val) {
    //             if ($val == null) {
    //                 continue;
    //             }
    //             $avg = $avg + $val;
    //             $i++;
    //         }
    //         if ($i == 0) {
    //             array_push($result, 0);
    //         } else {
    //             $avg = $avg / $i;
    //             array_push($result, round($avg, 2));   //干扰
    //         }
    //     }
    //     return $result;
    // }

    /**
     * 获得诊断数据
     *
     * @return array 诊断数据-高话务
     */
    public function getNumOfDiagnosisDataFilter_highTraffic() 
    {   //高话务
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $sql = "SELECT `关联度` FROM LowAccessCellTableEveryOneHour WHERE cell='$cell';";
        $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $result = $row[0]['关联度'];
        return $result;
    }
    // public function getNumOfDiagnosisDataFilter_highTraffic()
    // {
    //     $city = Input::get('city');
    //     $cell = Input::get('cell');
    //     $day_from = Input::get('dateTime');
    //     $day_to = Input::get('endTime');
    //     $result = array();
    //     $dsn = new DataBaseConnection();
    //     $db = $dsn->getDB('mongs', 'AutoKPI');  //高话务
    //     $sql = "SELECT SUM(`最大RRC连接用户数`)/20 AS NUM
    //             FROM lowAccessCell_ex
    //             WHERE
    //                 cell = '" . $cell . "'
    //             AND day_id >= '" . $day_from . "'
    //             AND day_id <= '" . $day_to . "';";
    //     $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    //     if (count($row) == 0) {
    //         array_push($result, 0);
    //     } else {
    //         array_push($result, round($row[0]['NUM'], 2));
    //     }
    //     return $result;
    // }

    /**
     * 获得诊断数据
     *
     * @return array 诊断数据-参数
     */
    public function getNumOfDiagnosisDataFilter_parameter() 
    {   //参数
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $sql = "SELECT `参数` FROM LowAccessCellTableEveryOneHour WHERE cell='$cell';";
        $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $result = $row[0]['参数'];
        return $result;
    }
    // public function getNumOfDiagnosisDataFilter_parameter()
    // {
    //     $city = Input::get('city');
    //     $cell = Input::get('cell');
    //     $day_from = Input::get('dateTime');
    //     $day_to = Input::get('endTime');
    //     $result = array();
    //     $date = new DateTime();    //参数
    //     $date->sub(new DateInterval('P1D'));
    //     $yesDate = $date->format('ymd');
    //     $dbname = 'kget' . $yesDate;
    //     $table = 'ParaCheckBaseline';
    //     $dbc = new DataBaseConnection();
    //     $db = $dbc->getDB('mongs', $dbname);
    //     $sql = "select count(*) as num from  $table where cellId='$cell';";
    //     $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    //     if (count($row) == 0) {
    //         array_push($result, 0);
    //     } else {
    //         array_push($result, intval($row[0]['num']));
    //     }
    //     return $result;
    // }

    /**
     * 获得诊断数据
     *
     * @return array 诊断数据
     */
    public function getNumOfDiagnosisData()
    {
        $city = Input::get('city');
        $cell = Input::get('cell');
        $day_from = Input::get('dateTime');
        $day_to = Input::get('endTime');
        $result = array();

        // $rowData_2 = explode('_', $cell);
        // $rowData_3 = $rowData_2[0];
        // if ($rowData_3 == $cell) {
        //     $rowData_3 = substr($rowData_3, 0, strlen($rowData_3) - 1);
        // }
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('mongs', 'mongs');
        $sql = "SELECT siteName from siteLte WHERE cellName='$cell';";
        $row = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
        $erbs = $row[0]['siteName'];
        // $sql = "SELECT COUNT(*) as num FROM FMA_alarm_log r LEFT JOIN mongs.AlarmInfo t ON r.SP_text= t.alarmNameE WHERE DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $day_from . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $day_to . "' AND meContext = '" . $erbs . "';";
        $sql = "SELECT COUNT(*) as num FROM FMA_alarm_log r ,mongs.AlarmInfo t WHERE r.SP_text= t.alarmNameE AND DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $day_from . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $day_to . "' AND meContext = '" . $erbs . "';";
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('alarm', 'Alarm');
        $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        array_push($result, round($row[0]['num'], 2));   //告警

        array_push($result, 0);
        /*$db = $dsn->getDB('mongs', 'mongs');
        $sql = "select ecgi from siteLte where cellName = '$cell'";
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) == 0) {
            array_push($result, 0);
        } else {
            $ecgi = $row[0]['ecgi'];
            $cityCH = $this->encityToCHcity($city);
            $dbname = $this->getMRDatabase($cityCH);
            $db = $dsn->getDB('MR', $dbname);
            $table = 'mreServeNeigh';
            $sql = "select count(*) as num from $table where isdefined_direct=0 and ecgi = '$ecgi' AND distance_direct<1.5 and DATE_FORMAT(datetime_id, '%Y-%m-%d') >= '" . $day_from . "';";
            $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
            array_push($result, round($row[0]['num'], 2));
        }*/

        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $sql = "select AVG(`RSRP<-116的比例`) as NUM from lowAccessCell_ex where day_id >= '" . $day_from . "' AND day_id <= '" . $day_to . "' AND cell='" . $cell . "'";
        $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        if ($row[0]['NUM'] === null) {
            array_push($result, 0);
        } else {
            array_push($result, round($row[0]['NUM'], 2));    //弱覆盖
        }

        $sql = "select AVG(`RSRQ<-15.5的比例`) as NUM from lowAccessCell_ex where day_id >= '" . $day_from . "' AND day_id <= '" . $day_to . "' AND cell='" . $cell . "'";  //质差
        $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        if ($row[0]['NUM'] == null) {
            array_push($result, 0);
        } else {
            array_push($result, round($row[0]['NUM'], 2));   //质差
        }

        $cityCH = $this->encityToCHcity($city);
        $dbname = $this->getMRDatabase($cityCH);
        $db = $dsn->getDB('MR', $dbname);
        // $sql = "SELECT
        //             avg(rate) as NUM
        //         FROM
        //             mroOverCoverage_day
        //         LEFT JOIN GLOBAL .siteLte ON mroOverCoverage_day.ecgi = siteLte.ecgi
        //         WHERE
        //             dateId = '" . $day_from . "'
        //         AND cellName = '" . $cell . "';";
        $sql = "SELECT
                    avg(rate) as NUM
                FROM
                    mroOverCoverage_day
                , GLOBAL .siteLte
                WHERE
                    mroOverCoverage_day.ecgi = siteLte.ecgi
                AND dateId = '" . $day_from . "'
                AND cellName = '" . $cell . "';";
        $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        if ($row[0]['NUM'] == null || is_nan(intval($row[0]['NUM']))) {
            array_push($result, 0);
        } else {
            array_push($result, round($row[0]['NUM'] * 100, 2));    //重叠覆盖
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
                AND day_id >= '" . $day_from . "'
                AND day_id <= '" . $day_to . "';";
        $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);   //干扰
        $i = 0;
        $avg = 0;
        if (count($row) == 0) {
            array_push($result, 0);
        } else {
            foreach ($row[0] as $val) {
                if ($val == null) {
                    continue;
                }
                $avg = $avg + $val;
                $i++;
            }
            if ($i == 0) {
                array_push($result, 0);
            } else {
                $avg = $avg / $i;
                array_push($result, round($avg, 2));   //干扰
            }
        }
        // return $result;
        $db = $dsn->getDB('autokpi', 'AutoKPI');  //高话务
        $sql = "SELECT SUM(`最大RRC连接用户数`)/20 AS NUM
                FROM lowAccessCell_ex
                WHERE
                    cell = '" . $cell . "'
                AND day_id >= '" . $day_from . "'
                AND day_id <= '" . $day_to . "';";
        $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) == 0) {
            array_push($result, 0);
        } else {
            array_push($result, round($row[0]['NUM'], 2));
        }

        $date = new DateTime();    //参数
        $date->sub(new DateInterval('P1D'));
        $yesDate = $date->format('ymd');
        $dbname = 'kget' . $yesDate;
        $table = 'ParaCheckBaseline';
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', $dbname);
        $sql = "select count(*) as num from  $table where cellId='$cell';";
        $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) == 0) {
            array_push($result, 0);
        } else {
            array_push($result, intval($row[0]['num']));
        }
        return $result;
    }

    /**
     * 获得质差图表数据
     *
     * @return void
     */
    public function getZhichaCellChart()
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
        $city = Input::get('city');
        $cell = Input::get('cell');
        // $date_from = Input::get('day_from');
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
                $ecgi = '-'.$rows[0]['ecgi'];
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
                        eventName = 'internalProcRrcConnSetup'
                    AND date_id = '$date_to'
                    AND ecgi = '$ecgi'
                    AND result !='EVENT_VALUE_SUCCESS';";
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
                        eventName = 'internalProcRrcConnSetup'
                    AND date_id = '$date_to'
                    AND ecgi = '$ecgi'
                    AND result !='EVENT_VALUE_SUCCESS'
                    GROUP BY result
                    ORDER BY ratio DESC;";
                $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    array_push($tooltip, intval($row['times']));
                    array_push($categories, $row['result']);
                    array_push($yAxis, round($row['ratio'], 2));
                }
            }
        }
        if (count($categories) == 0) {
            $result['error'] = '数据为空！';
        } else {
            $result['ecgi'] = $ecgi;
            $result['categories'] = $categories;
            $result['yAxis'] = $yAxis;
            $result['tooltip'] = implode('/', $tooltip);
            $result['date'] = $date_from;
            $result['error'] = 0;
        }
        return ($result);
    }

    /**
     * 获得INTERNAL_RRCC事件分析数据
     *
     * @return array INTERNAL_RRCC事件分析数据
     */
    public function getRrcResultRrcC()
    {
        $result = [];
        $categories = [];
        $tooltip = [];
        $yAxis = [];
        $city = Input::get('city');
        $cell = Input::get('cell');
        // $date_from = Input::get('day_from');
        $date_from = date("Y-m-d");
        $date_to = date("Y-m-d");
        // $date_to = Input::get('day_to');
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
                $ecgi = '-'.$rows[0]['ecgi'];
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
                        eventName = 'internalProcRrcConnSetup'
                    AND date_id = '$date_to'
                    AND ecgi = '$ecgi'
                    AND result !='EVENT_VALUE_SUCCESS';";
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
                        eventName = 'internalProcRrcConnSetup'
                    AND date_id = '$date_to'
                    AND ecgi = '$ecgi'
                    AND result !='EVENT_VALUE_SUCCESS'
                    GROUP BY result
                    ORDER BY ratio DESC;";
                $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    array_push($tooltip, intval($row['times']));
                    array_push($categories, $row['result']);
                    array_push($yAxis, round($row['ratio'], 2));
                }
            }
        }
        if (count($categories) == 0) {
            $result['error'] = '数据为空！';
        } else {
            $result['ecgi'] = $ecgi;
            $result['categories'] = $categories;
            $result['yAxis'] = $yAxis;
            $result['tooltip'] = implode('/', $tooltip);
            $result['date'] = $date_from;
            $result['error'] = 0;
        }
        return ($result);
    }

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
        // $date_from = Input::get('day_from');
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
                $ecgi = '-'.$rows[0]['ecgi'];
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
                        100 * sum(times)/sum(timesTotal) AS ratio,
                        sum(times) times ,sum(timesTotal) timesTotal 
                    FROM
                        causeCode_hour
                    WHERE
                        eventName = 'internalProcRrcConnSetup'
                    AND date_id = '$date_to'
                    AND ecgi = '$ecgi'
                    GROUP BY result
                    ORDER BY ratio DESC) t");
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
                        100 * sum(times)/sum(timesTotal) AS ratio ,
                        sum(times) times ,sum(timesTotal) timesTotal
                    FROM
                        causeCode_hour
                    WHERE
                        eventName = 'internalProcRrcConnSetup'
                    AND date_id = '$date_to'
                    AND ecgi = '$ecgi'
                    GROUP BY result
                    ORDER BY ratio DESC;";
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
    }

    /**
     * 获取baseline参数对比数据
     *
     * @return string
     */
    public function getBaselineCheckData()
    {
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        // $yesDate = $date->format('ymd');
        // $dbname = 'kget' . $yesDate;
        //判断是否用今天的数据
        $dbname = "kget".date("ymd");
        $dbc = new DataBaseConnection();
        $pdo = $dbc->getDB('mongs', $dbname);
        $sql = "SELECT COUNT(*) AS num FROM mongs.task WHERE taskName='$dbname';";
        $row = $pdo->query($sql, PDO::FETCH_ASSOC)->fetchall();
        if ($row[0]['num'] == 0) {
            $dbname = "kget".date("ymd", strtotime("-1 day"));
        }
        $cell = Input::get('rowCell');
        $table = Input::get('table');
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', $dbname);

        $sql = "select * from  $table where cellId='$cell';";
        $item = array();
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
                $filename = "common/files/" . $cell . "_" . $table . date('YmdHis') . ".csv";
                $item['filename'] = $filename;
                $this->resultToCSV2_par($item, $filename);
            }
        }
        echo json_encode($item);
    }

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
        $date_from = Input::get('day_from');
        $ecgi = Input::get('ecgi');
        $result = Input::get('result');
        $table = "internalProcRrcConnSetup";
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
    }

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
        $date_from = Input::get('day_from');
        $ecgi = Input::get('ecgi');
        $result = Input::get('result');
        $table = "internalProcRrcConnSetup";
        $filter = " where date_id='$date_from' and hour_id in (0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23) 
            and imsi=0 and result='$result' and ecgi='$ecgi'  ";
        $result = array();
        $sqlCount = "select count(*) from " . $table . $filter;
                    // print_r($sqlCount);return;
        $rs = $db->query($sqlCount, PDO::FETCH_ASSOC);
        $result["total"] = $rs->fetchColumn();
        $sql = "select * from $table $filter $limit";
        //dump($sql);
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
    }

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
        $date_from = Input::get('day_from');
        $ecgi = Input::get('ecgi');
        $resultValue = Input::get('result');
        $table = "internalProcRrcConnSetup";
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
                //$column = mb_convert_encoding($column, 'gbk', 'utf-8');
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
    }

    /**
     * 导出小区告警数据
     *
     * @return array
     */
    protected function getalarmWorstCell()
    {
        $dbn = new DataBaseConnection();
        // $conn = $dbn->getConnDB('mongs');
        $conn = $dbn->query('mongs', 'Alarm');
        if ($conn == null) {
            echo 'Could not connect';
        }
        // mysql_select_db('Alarm', $conn);
        $result = array();
        $table = Input::get('table');
        $cell = Input::get('rowCell');
        $rowData_2 = explode('_', $cell);
        $rowData_3 = $rowData_2[0];
        if ($rowData_3 == $cell) {
            $rowData_3 = substr($rowData_3, 0, strlen($rowData_3) - 1);
        }
        if ($table == 'lowAccessCell') {
            $tableF = 'FMA_alarm_log';
            $rs = $conn->query("select Event_time,meContext,eutranCell,Cease_time,SP_text,Problem_text from " . $tableF . " where meContext='" . $rowData_3 . "' order by Event_time DESC");
            // $rs = mysql_query("select Event_time,meContext,eutranCell,Cease_time,SP_text,Problem_text from " . $tableF . " where meContext='" . $rowData_3 . "' order by Event_time DESC");
        } else {
            $tableF = 'FMA_alarm_log';
            $rs = $conn->query("select Event_time,Problem_text,Cease_time,SP_text from " . $tableF . " where meContext='" . $rowData_3 . "'order by Event_time DESC");
            // $rs = mysql_query("select Event_time,Problem_text,Cease_time,SP_text from " . $tableF . " where meContext='" . $rowData_3 . "'order by Event_time DESC");
        }
        $items = array();
        while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
            array_push($items, $row);
        }
        $result['records'] = count($items);
        $result["rows"] = $items;
        if ($table == 'lowAccessCell') {
            $result["content"] = "Event_time,meContext,eutranCell,Cease_time,SP_text,Problem_text";
        } else {
            $result["content"] = "Event_time,Problem_text,Cease_time,SP_text";
        }
        $filename = "common/files/" . $table . date('YmdHis') . ".csv";
        $result['filename'] = $filename;
        return $result;
    }

    /**
     * 导出干扰分析数据
     *
     * @return array
     */
    protected function getInterfereAnalysis()
    {
        $dbn = new DataBaseConnection();
        // $conn = $dbn->getConnDB('mongs');
        $conn = $dbn->getDB('autokpi', 'AutoKPI');
        if ($conn == null) {
            echo 'Could not connect';
        }
        // mysql_select_db('AutoKPI', $conn);
        $result = array();
        $table = 'interfereCell';
        $cell = Input::get('cell');
        $day_from = Input::get('day_from');
        $day_to = Input::get('day_to');
        $hours = Input::get('hours');
        $recordsHour = 'null';
        if ($hours != '') {
            $hour = implode(',', $hours);
            $rs = $conn->query("SELECT COUNT(*) AS num FROM $table WHERE cell='" . $cell . "' AND day_id>='" . $day_from . "' AND day_id<='" . $day_to . "' AND hour_id IN ($hour);");
            // $rs = mysql_query("SELECT COUNT(*) AS num FROM $table WHERE cell='" . $cell . "' AND day_id>='" . $day_from . "' AND day_id<='" . $day_to . "' AND hour_id IN ($hour);");
            $row = $rs->fetchAll(PDO::FETCH_NUM);
            $recordsHour = $row[0]['num'];
        }

        $rs = $conn->query("select day_id,hour_id,city,subNetwork,cell,PUCCH上行干扰电平,PUSCH上行干扰电平,SF1上行干扰电平,SF2上行干扰电平,SF6上行干扰电平,SF7上行干扰电平,PRB1上行干扰电平,PRB2上行干扰电平,PRB3上行干扰电平,PRB4上行干扰电平,PRB5上行干扰电平,PRB6上行干扰电平,PRB7上行干扰电平,PRB8上行干扰电平,PRB9上行干扰电平,PRB10上行干扰电平,PRB11上行干扰电平,PRB12上行干扰电平,PRB13上行干扰电平,PRB14上行干扰电平,PRB15上行干扰电平,PRB16上行干扰电平,PRB17上行干扰电平,PRB18上行干扰电平,PRB19上行干扰电平,PRB20上行干扰电平,PRB21上行干扰电平,PRB22上行干扰电平,PRB23上行干扰电平,PRB24上行干扰电平,PRB25上行干扰电平,PRB26上行干扰电平,PRB27上行干扰电平,PRB28上行干扰电平,PRB29上行干扰电平,PRB30上行干扰电平,PRB31上行干扰电平,PRB32上行干扰电平,PRB33上行干扰电平,PRB34上行干扰电平,PRB35上行干扰电平,PRB36上行干扰电平,PRB37上行干扰电平,PRB38上行干扰电平,PRB39上行干扰电平,PRB40上行干扰电平,PRB41上行干扰电平,PRB42上行干扰电平,PRB43上行干扰电平,PRB44上行干扰电平,PRB45上行干扰电平,PRB46上行干扰电平,PRB47上行干扰电平,PRB48上行干扰电平,PRB49上行干扰电平,PRB50上行干扰电平,PRB51上行干扰电平,PRB52上行干扰电平,PRB53上行干扰电平,PRB54上行干扰电平,PRB55上行干扰电平,PRB56上行干扰电平,PRB57上行干扰电平,PRB58上行干扰电平,PRB59上行干扰电平,PRB60上行干扰电平,PRB61上行干扰电平,PRB62上行干扰电平,PRB63上行干扰电平,PRB64上行干扰电平,PRB65上行干扰电平,PRB66上行干扰电平,PRB67上行干扰电平,PRB68上行干扰电平,PRB69上行干扰电平,PRB70上行干扰电平,PRB71上行干扰电平,PRB72上行干扰电平,PRB73上行干扰电平,PRB74上行干扰电平,PRB75上行干扰电平,PRB76上行干扰电平,PRB77上行干扰电平,PRB78上行干扰电平,PRB79上行干扰电平,PRB80上行干扰电平,PRB81上行干扰电平,PRB82上行干扰电平,PRB83上行干扰电平,PRB84上行干扰电平,PRB85上行干扰电平,PRB86上行干扰电平,PRB87上行干扰电平,PRB88上行干扰电平,PRB89上行干扰电平,PRB90上行干扰电平,PRB91上行干扰电平,PRB92上行干扰电平,PRB93上行干扰电平,PRB94上行干扰电平,PRB95上行干扰电平,PRB96上行干扰电平,PRB97上行干扰电平,PRB98上行干扰电平,PRB99上行干扰电平,PRB100上行干扰电平,type from " . $table . " where cell='" . $cell . "' AND day_id>='" . $day_from . "' AND day_id<='" . $day_to . "' order by day_id DESC");
        // $rs = mysql_query("select day_id,hour_id,city,subNetwork,cell,PUCCH上行干扰电平,PUSCH上行干扰电平,SF1上行干扰电平,SF2上行干扰电平,SF6上行干扰电平,SF7上行干扰电平,PRB1上行干扰电平,PRB2上行干扰电平,PRB3上行干扰电平,PRB4上行干扰电平,PRB5上行干扰电平,PRB6上行干扰电平,PRB7上行干扰电平,PRB8上行干扰电平,PRB9上行干扰电平,PRB10上行干扰电平,PRB11上行干扰电平,PRB12上行干扰电平,PRB13上行干扰电平,PRB14上行干扰电平,PRB15上行干扰电平,PRB16上行干扰电平,PRB17上行干扰电平,PRB18上行干扰电平,PRB19上行干扰电平,PRB20上行干扰电平,PRB21上行干扰电平,PRB22上行干扰电平,PRB23上行干扰电平,PRB24上行干扰电平,PRB25上行干扰电平,PRB26上行干扰电平,PRB27上行干扰电平,PRB28上行干扰电平,PRB29上行干扰电平,PRB30上行干扰电平,PRB31上行干扰电平,PRB32上行干扰电平,PRB33上行干扰电平,PRB34上行干扰电平,PRB35上行干扰电平,PRB36上行干扰电平,PRB37上行干扰电平,PRB38上行干扰电平,PRB39上行干扰电平,PRB40上行干扰电平,PRB41上行干扰电平,PRB42上行干扰电平,PRB43上行干扰电平,PRB44上行干扰电平,PRB45上行干扰电平,PRB46上行干扰电平,PRB47上行干扰电平,PRB48上行干扰电平,PRB49上行干扰电平,PRB50上行干扰电平,PRB51上行干扰电平,PRB52上行干扰电平,PRB53上行干扰电平,PRB54上行干扰电平,PRB55上行干扰电平,PRB56上行干扰电平,PRB57上行干扰电平,PRB58上行干扰电平,PRB59上行干扰电平,PRB60上行干扰电平,PRB61上行干扰电平,PRB62上行干扰电平,PRB63上行干扰电平,PRB64上行干扰电平,PRB65上行干扰电平,PRB66上行干扰电平,PRB67上行干扰电平,PRB68上行干扰电平,PRB69上行干扰电平,PRB70上行干扰电平,PRB71上行干扰电平,PRB72上行干扰电平,PRB73上行干扰电平,PRB74上行干扰电平,PRB75上行干扰电平,PRB76上行干扰电平,PRB77上行干扰电平,PRB78上行干扰电平,PRB79上行干扰电平,PRB80上行干扰电平,PRB81上行干扰电平,PRB82上行干扰电平,PRB83上行干扰电平,PRB84上行干扰电平,PRB85上行干扰电平,PRB86上行干扰电平,PRB87上行干扰电平,PRB88上行干扰电平,PRB89上行干扰电平,PRB90上行干扰电平,PRB91上行干扰电平,PRB92上行干扰电平,PRB93上行干扰电平,PRB94上行干扰电平,PRB95上行干扰电平,PRB96上行干扰电平,PRB97上行干扰电平,PRB98上行干扰电平,PRB99上行干扰电平,PRB100上行干扰电平,type from " . $table . " where cell='" . $cell . "' AND day_id>='" . $day_from . "' AND day_id<='" . $day_to . "' order by day_id DESC");
        $items = array();
        while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
            array_push($items, $row);
        }

        $i = 0;
        foreach ($items as $item) {
            $items[$i]['hour_id'] = intval($item['hour_id']);
            $i++;
        }
        $result['records'] = count($items);
        $result['recordsHour'] = $recordsHour;
        $result["rows"] = $items;
        $result["content"] = "day_id,hour_id,city,subNetwork,cell,PUCCH上行干扰电平,PUSCH上行干扰电平,SF1上行干扰电平,SF2上行干扰电平,SF6上行干扰电平,SF7上行干扰电平,PRB1上行干扰电平,PRB2上行干扰电平,PRB3上行干扰电平,PRB4上行干扰电平,PRB5上行干扰电平,PRB6上行干扰电平,PRB7上行干扰电平,PRB8上行干扰电平,PRB9上行干扰电平,PRB10上行干扰电平,PRB11上行干扰电平,PRB12上行干扰电平,PRB13上行干扰电平,PRB14上行干扰电平,PRB15上行干扰电平,PRB16上行干扰电平,PRB17上行干扰电平,PRB18上行干扰电平,PRB19上行干扰电平,PRB20上行干扰电平,PRB21上行干扰电平,PRB22上行干扰电平,PRB23上行干扰电平,PRB24上行干扰电平,PRB25上行干扰电平,PRB26上行干扰电平,PRB27上行干扰电平,PRB28上行干扰电平,PRB29上行干扰电平,PRB30上行干扰电平,PRB31上行干扰电平,PRB32上行干扰电平,PRB33上行干扰电平,PRB34上行干扰电平,PRB35上行干扰电平,PRB36上行干扰电平,PRB37上行干扰电平,PRB38上行干扰电平,PRB39上行干扰电平,PRB40上行干扰电平,PRB41上行干扰电平,PRB42上行干扰电平,PRB43上行干扰电平,PRB44上行干扰电平,PRB45上行干扰电平,PRB46上行干扰电平,PRB47上行干扰电平,PRB48上行干扰电平,PRB49上行干扰电平,PRB50上行干扰电平,PRB51上行干扰电平,PRB52上行干扰电平,PRB53上行干扰电平,PRB54上行干扰电平,PRB55上行干扰电平,PRB56上行干扰电平,PRB57上行干扰电平,PRB58上行干扰电平,PRB59上行干扰电平,PRB60上行干扰电平,PRB61上行干扰电平,PRB62上行干扰电平,PRB63上行干扰电平,PRB64上行干扰电平,PRB65上行干扰电平,PRB66上行干扰电平,PRB67上行干扰电平,PRB68上行干扰电平,PRB69上行干扰电平,PRB70上行干扰电平,PRB71上行干扰电平,PRB72上行干扰电平,PRB73上行干扰电平,PRB74上行干扰电平,PRB75上行干扰电平,PRB76上行干扰电平,PRB77上行干扰电平,PRB78上行干扰电平,PRB79上行干扰电平,PRB80上行干扰电平,PRB81上行干扰电平,PRB82上行干扰电平,PRB83上行干扰电平,PRB84上行干扰电平,PRB85上行干扰电平,PRB86上行干扰电平,PRB87上行干扰电平,PRB88上行干扰电平,PRB89上行干扰电平,PRB90上行干扰电平,PRB91上行干扰电平,PRB92上行干扰电平,PRB93上行干扰电平,PRB94上行干扰电平,PRB95上行干扰电平,PRB96上行干扰电平,PRB97上行干扰电平,PRB98上行干扰电平,PRB99上行干扰电平,PRB100上行干扰电平,type";
        $filename = "common/files/" . $table . date('YmdHis') . ".csv";
        $result['filename'] = $filename;
        return $result;
    }

    /**
     * Counter失败原因值分布0524
     *
     * @return array
     */
    function getCounterLoseResultDistribution() 
    {
        $cell = Input::get('cell');
        $startTime = date("Y-m-d", strtotime("-1 day"));
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('autokpi', 'AutoKPI');
        $sql = "SELECT 
                   SUM(License超限导致的RRC连接失败) AS License超限导致的RRC连接失败,
                    SUM(承载准入拒绝导致的RRC连接失败) AS 承载准入拒绝导致的RRC连接失败,
                    SUM(高负载导致的RRC连接失败) AS 高负载导致的RRC连接失败,
                    SUM(超载导致的RRC连接失败) AS 超载导致的RRC连接失败,
                    SUM(`RAC-BB信号拥塞导致的RRC连接建立失败`) AS 'RAC-BB信号拥塞导致的RRC连接建立失败',
                    SUM(MME上主叫数据超载导致的RRC建立失败) AS MME上主叫数据超载导致的RRC建立失败,
                    SUM(MME上主叫信令超载导致的RRC建立失败) AS MME上主叫信令超载导致的RRC建立失败,
                    SUM(长时间高负载导致的RRC连接失败) AS 长时间高负载导致的RRC连接失败,
                    SUM(动态超负荷导致RRC建立失败) AS 动态超负荷导致RRC建立失败,
                    SUM(MP超载导致RRC建立被拒绝) AS MP超载导致RRC建立被拒绝,
                    SUM(软件模块高负载导致的RRC连接失败) AS 软件模块高负载导致的RRC连接失败,
                    SUM(大延迟导致RRC建立失败) AS 大延迟导致RRC建立失败,
                    SUM(DU或者基带连接控制导致的RRC建立失败) AS DU或者基带连接控制导致的RRC建立失败,
                    SUM(DU软件模块高负载导致的RRC连接失败) AS DU软件模块高负载导致的RRC连接失败,
                    SUM(小区静态流控导致RRC建立失败) AS 小区静态流控导致RRC建立失败,
                    SUM(UE动态准入控制导致的RRC连接失败) AS UE动态准入控制导致的RRC连接失败
                FROM rrcCauseCell WHERE day_id>='$startTime' AND cell='$cell';";
        $rs = $db->query($sql);
        $categories = [];
        $yAxis = [];
        while ($rows = $rs->fetch(PDO::FETCH_ASSOC)) {
            foreach ($rows as $key => $value) {
                if ($value == 0) {
                    continue;                
                }
                array_push($categories, $key);
                array_push($yAxis, floatval($value));
           }
        }
        $data = [];
        $data['categories'] = $categories;
        $data['yAxis'] = $yAxis;
        $data['date'] = $startTime;
        return $data;
    }

        /**
     * 获得INTERNAL_RRC事件分析数据 0524
     *
     * @return array INTERNAL_RRC事件分析数据
     */
    public function getRrcResult_erab()
    {
        $result = [];
        $categories = [];
        // $tooltip = [];
        $yAxis = [];
        $city = Input::get('city');
        $cell = Input::get('cell');
        $date_from = Input::get('day_from');
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
            // $rows = $db->query("SELECT SUBSTRING_INDEX(ecgi,'-',-2) AS ecgi FROM siteLte WHERE cellName = '$cell' LIMIT 1;")->fetchall(PDO::FETCH_ASSOC);
            $rows = $db->query("SELECT ecgi FROM siteLte WHERE cellName = '$cell' LIMIT 1;")->fetchall(PDO::FETCH_ASSOC);
            if (count($rows[0]) == 0) {
                $result['error'] = 'ecgi数据为空！';
                return;
            } else {
                $ecgi = $rows[0]['ecgi'];
            }
            $db = $dsn->getDB('CTR', $ctrCity);
            $sql = 'SHOW TABLES';
            $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if ($row['Tables in ' . $ctrCity] == 'internalprocerabsetup') {
                    $flag = 2;
                }
            }
            if ($flag == 1) {
                $result['error'] = '数据表不存在！';
                return $result;
            } else {       
                $sql = "SELECT
                            erabSetupfailure3ppCause,
                            COUNT(*) AS num
                        FROM
                            internalProcErabSetup
                        WHERE
                            erabSetupResult != 'EVENT_VALUE_SUCCESS'
                        AND date_id = '$date_to'
                        AND ecgi = '$ecgi'
                        GROUP BY erabSetupfailure3ppCause 
                        ORDER BY num DESC;";
                        // print_r($sql);return;
                $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    array_push($categories, $row['erabSetupfailure3ppCause']);
                    array_push($yAxis, intval($row['num']));
                }
            }
        }
        if (count($categories) == 0) {
            $result['error'] = '数据为空！';
        } else {
            $result['ecgi'] = $ecgi;
            $result['categories'] = $categories;
            $result['yAxis'] = $yAxis;
            // $result['tooltip'] = implode('/', $tooltip);
            $result['date'] = $date_to;
            $result['error'] = 0;
        }
        return ($result);
    }

       /**
     * 获取图形对应表数据   0524
     *
     * @return string
     */
    public function getRrcResultTableData_rrcc()
    {

        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limits = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $offset = ($page - 1) * $limits;

        $result = [];
        $city = Input::get('city');
        $cell = Input::get('cell');
        $date_from = Input::get('day_from');
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
            // $rows = $db->query("SELECT SUBSTRING_INDEX(ecgi,'-',-2) AS ecgi FROM siteLte WHERE cellName = '$cell' LIMIT 1;")->fetchall(PDO::FETCH_ASSOC);
            $rows = $db->query("SELECT ecgi FROM siteLte WHERE cellName = '$cell' LIMIT 1;")->fetchall(PDO::FETCH_ASSOC);
            if (count($rows[0]) == 0) {
                $result['error'] = 'ecgi数据为空！';
                return;
            } else {
                $ecgi = $rows[0]['ecgi'];
            }
            $db = $dsn->getDB('CTR', $ctrCity);
            $sql = 'SHOW TABLES';
            $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if ($row['Tables in ' . $ctrCity] == 'internalprocerabsetup') {
                    $flag = 2;
                }
            }
            if ($flag == 1) {
                $result['error'] = '数据表不存在！';
                return $result;
            } else {
                // $sql = "SELECT
                //         date_id,
                //         eventName,
                //         ecgi,
                //         result,
                //         100 * sum(times)/sum(timesTotal) AS ratio ,
                //         sum(times) times ,sum(timesTotal) timesTotal
                //     FROM
                //         causeCode_day
                //     WHERE
                //         eventName = 'internalProcErabConnSetup'
                //     AND date_id = '$date_from'
                //     AND ecgi = '$ecgi'
                //     GROUP BY result
                //     ORDER BY ratio DESC;";
                // $sql = "SELECT
                //         date_id,
                //         eventName,
                //         ecgi,
                //         result,
                //         100 * sum(times)/sum(timesTotal) AS ratio ,
                //         sum(times) times ,sum(timesTotal) timesTotal
                //     FROM
                //         causeCode_day
                //     WHERE
                //         eventName = 'internalProcErabConnSetup'
                //     AND date_id = '$date_from'
                //     AND ecgi = '$ecgi'
                //     GROUP BY result
                //     ORDER BY ratio DESC;";
                $sql = "SELECT
                            eventTime,
                            enbId,
                            eventTimeUt,
                            date_id,
                            hour_id,
                            imsi,
                            mTmsi,
                            ueRef,
                            enbS1apId,
                            mmeS1apId,
                            ecgi,
                            gummei,
                            recordingSessionReference,
                            erabSetupResult,
                            erabSetupReqQci,
                            erabSetupfailure3GppCauseGroup,
                            erabSetupfailure3ppCause
                        FROM
                            internalProcErabSetup
                        WHERE
                            erabSetupResult != 'EVENT_VALUE_SUCCESS'
                        AND date_id = '$date_to'
                        AND ecgi = '$ecgi'";
                $rs = $db->query($sql);
                $items = array();
                if ($rs) {
                    $row = $rs->fetchAll(PDO::FETCH_ASSOC);
                    for ($i = $offset; $i < $offset + $limits && $i < count($row); $i++) {
                        $r = $row[$i];
                        $record["eventTime"] = $r['eventTime'];
                        $record["enbId"] = $r['enbId'];
                        $record["eventTimeUt"] = $r["eventTimeUt"];
                        $record["date_id"] = $r["date_id"];
                        $record["hour_id"] = $r["hour_id"];
                        $record["imsi"] = $r["imsi"];
                        $record["mTmsi"] = $r["mTmsi"];
                        $record["ueRef"] = $r["ueRef"];
                        $record["mTmsi"] = $r["mTmsi"];
                        $record["enbS1apId"] = $r["enbS1apId"];
                        $record["mmeS1apId"] = $r["mmeS1apId"];
                        $record["ecgi"] = $r["ecgi"];
                        $record["gummei"] = $r["gummei"];
                        $record["recordingSessionReference"] = $r["recordingSessionReference"];
                        $record["erabSetupResult"] = $r["erabSetupResult"];
                        $record["erabSetupReqQci"] = $r["erabSetupReqQci"];
                        $record["erabSetupfailure3GppCauseGroup"] = $r["erabSetupfailure3GppCauseGroup"];
                        $record["erabSetupfailure3ppCause"] = $r["erabSetupfailure3ppCause"];
                        array_push($items, $record);
                    }
                    $result["records"] = $items;
                } else {
                    $result['error'] = '没有记录';
                }
                return $result;
            }
        }
    }

    /**
     * 获取相关性-质差  
     *
     * @return string
     */
    public function getWirelessCallRate_zhicha() 
    {
        $cell = Input::get('cell');
        $startTime = date('Y-m-d', strtotime("-1 day")); 
        $endTime = date('Y-m-d');
        $value_x = [];
        $value_y = [];
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $sql = "SELECT
                    day_id,
                    hour_id,
                    无线接通率,
                    AVG(`RSRQ<-15.5的比例`) AS num
                FROM
                    lowAccessCell
                WHERE
                    day_id >= '" . $startTime . "'
                AND day_id <= '" . $endTime . "'
                AND cell = '" . $cell . "'
                GROUP BY day_id, hour_id;";
        $res = $db->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            array_push($value_x, $row['无线接通率']);
            array_push($value_y, $row['num']);
        }
        $data['data'] = $this->getRelevanceData($value_x, $value_y);
        $data['date_from'] = $startTime;
        return $data;
    }

    /**
     * 获取相关性-干扰  
     *
     * @return string
     */
    public function getWirelessCallRate_interfere() 
    {
        $cell = Input::get('cell');
        $startTime = date('Y-m-d', strtotime("-1 day")); 
        $endTime = date('Y-m-d');
        $value_x = [];
        $value_y = [];
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $sql = "SELECT day_id,hour_id,
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
                GROUP BY day_id,hour_id;";
        $res = $db->query($sql);   //干扰
        $value_x = [];
        $value_y = [];
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            // $day = $row[0];
            // $hour = $row[1];
            // $time = $day.' '.$hour;
            $avg = 0;
            $j = 0;
            
            // array_push($value_x, $row[2]);
            for ($i=0; $i<count($row); $i++) {
                if ($i>1) {
                    if ($row[$i] == null) {
                        continue;
                    }
                    $avg = $avg + $row[$i];
                    $j++;
                }
            }
            if ($j == 0) {
                // $value_y[$time] = 0;
                array_push($value_y, 0);
            } else {
                $avg = $avg / $j;
                // $value_y[$time] = $avg;
                array_push($value_y, $avg); 
            }
        }

        $sql = " SELECT
                    day_id,
                    hour_id,
                    无线接通率
                FROM
                    lowAccessCell
                WHERE
                    cell = '$cell'
                AND day_id >= '" . $startTime . "'
                AND day_id <= '" . $endTime . "'
                GROUP BY
                    day_id,
                    hour_id";
        $res = $db->query($sql);
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            // $day = $row[0];
            // $hour = $row[1];
            // $time = $day.' '.$hour;
            // $value_x[$time] = $row[2];
            array_push($value_x, $row[2]);
        }            
        // print_r($value_x);print_r($value_y);return;
        $data['data'] = $this->getRelevanceData($value_x, $value_y);
        $data['date_from'] = $startTime;
        return $data;
    }

    /**
     * 获取相关性-RRC建立成功率  
     *
     * @return string
     */
    public function getWirelessCallRate_RRCEstSucc() 
    {
        $cell = Input::get('cell');
        $startTime = date('Y-m-d', strtotime("-1 day")); 
        $endTime = date('Y-m-d');
        $value_x = [];
        $value_y = [];
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $sql = "SELECT
                    day_id,
                    hour_id,
                    无线接通率,
                    RRC建立成功率 AS num
                FROM
                    lowAccessCell
                WHERE
                    day_id >= '" . $startTime . "'
                AND day_id <= '" . $endTime . "'
                AND cell = '" . $cell . "'
                GROUP BY day_id, hour_id;";
        $res = $db->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            array_push($value_x, $row['无线接通率']);
            array_push($value_y, $row['num']);
        }
        $data['data'] = $this->getRelevanceData($value_x, $value_y);
        $data['date_from'] = $startTime;
        return $data;
    }

    /**
     * 获取相关性-ERAB建立成功率  
     *
     * @return string
     */
    public function getWirelessCallRate_ERABEstSucc() 
    {
        $cell = Input::get('cell');
        $startTime = date('Y-m-d', strtotime("-1 day")); 
        $endTime = date('Y-m-d');
        $value_x = [];
        $value_y = [];
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $sql = "SELECT
                    day_id,
                    hour_id,
                    无线接通率,
                    ERAB建立成功率 AS num
                FROM
                    lowAccessCell
                WHERE
                    day_id >= '" . $startTime . "'
                AND day_id <= '" . $endTime . "'
                AND cell = '" . $cell . "'
                GROUP BY day_id, hour_id;";
        $res = $db->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            array_push($value_x, $row['无线接通率']);
            array_push($value_y, $row['num']);
        }
        $data['data'] = $this->getRelevanceData($value_x, $value_y);
        $data['date_from'] = $startTime;
        return $data;
    }

    /**
     * 相关性-公式
     *
     * @return string
     */
    protected function getRelevanceData($x, $y) 
    {
        // print_r($x);print_r($y);return;
        $xSquare = [];
        $ySquare = [];
        $xySquare = [];
        $xySum = 0;
        $xSum = 0;
        $ySum = 0;
        $xxSum = 0;
        $yySum = 0;

        $count = count($x);
        for ($i=0; $i<count($x); $i++) {
            $xx = $x[$i] * $x[$i];
            $yy = $y[$i] * $y[$i];
            $xy = $x[$i] * $y[$i];
            $xySum = $xySum + $xy;  //xy之和
            $xSum = $xSum + $x[$i];     //x之和
            $ySum = $ySum + $y[$i];     //y之和
            $xxSum = $xxSum + $xx;  //xx之和
            $yySum = $yySum + $yy;  //yy之和
        }
        if ($xSum == 0 || $ySum == 0) {
            // return '0';
            return '分母为0';
        }
        return abs(round((($xySum*$count-$xSum*$ySum)/(sqrt($xxSum*$count-$xSum*$xSum)*sqrt($yySum*$count-$ySum*$ySum))),2));
    }

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
        $dateTime = Input::get('dateTime');

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

    /**
     * 低接入和高掉线的干扰处理原则
     *
     * @return string 低接入和高掉线的干扰处理原则
     */
    public function getTreatmentPrinciple() 
    {   
        $data = [];
        $cell = Input::get("cell");
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('mongs', 'mongs');
        //判断是否符合查询条件 
        $sql = "SELECT 平均PRB FROM AutoKPI.LowAccessCellTableEveryOneHour WHERE cell='$cell';";
        $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $flag = $row[0]['平均PRB'];
        $data['flag'] = true;
        if ($flag < -100 || $flag == 0) {
            $data['flag'] = false;
        }
        //判断使用今/昨kget
        $kget = "kget".date("ymd");
        $now = date("Y-m-d");
        $sql = "SELECT COUNT(*) AS num FROM task WHERE taskName='$kget'";
        $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        if ($row[0]['num'] == 0) {
            $kget = "kget".date("ymd", strtotime("-1 day"));
            $now = date("Y-m-d", strtotime("-1 day"));
        }
        $db->query("use $kget");
        //1.修改功控参数/2.cellBarred干扰小区 
        $sql = "SELECT pZeroNominalPUCCH,alpha,cellBarred,qRxlevmin FROM EUtranCellTDD WHERE EUtranCellTDDId='$cell';";
        $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $data['pZeroNominalPUCCH'] = $row[0]['pZeroNominalPUCCH'];
        $data['alpha'] = $row[0]['alpha'];
        $data['cellBarred'] = $row[0]['cellBarred'];
        $data['qRxlevmin'] = $row[0]['qRxlevmin'];
        //3.限制干扰小区qRxlevmin在干扰值以上4db
        $sql = "SELECT 平均PRB FROM AutoKPI.LowAccessCellTableEveryOneHour WHERE cell='$cell';";
        $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $avgPRB = $row[0]['平均PRB'];
        $data['平均PRB'] = intval($avgPRB) + 4;
        //4.降低干扰小区的发射功率先降低一半，在根据情况降低功率
        $sql = "SELECT configuredMaxTxPower FROM SectorCarrier WHERE EUtranCell='$cell';";
        $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        if ($row[0]['configuredMaxTxPower'] == 0 || $row[0]['configuredMaxTxPower'] == '') {
            $sql = "SELECT configuredOutputPower FROM SectorEquipmentFunction WHERE EUtranCellTDD='$cell';";
            $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
            $data['config'] = $row[0]['configuredOutputPower'];
        } else {
            $data['config'] = $row[0]['configuredMaxTxPower'];
        }
        //5.关闭周边小区到干扰小区的isHoAllowed=false 开关
        $sql = "SELECT ecgi FROM mongs.siteLte WHERE cellName='$cell';";
        $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $ecgi = $row[0]['ecgi'];
        $sql = "SELECT mo,meContext,EUtranCellTDD,EUtranCellRelationId,isHoAllowed FROM EUtranCellRelation WHERE EUtranCellRelationId='$ecgi';";
        $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $txt = "common/files/".uniqid().".csv";
        foreach ($row as $key => $value) {
            $data['rows'][] = $value;
        }
        $data['content'] = "mo,meContext,EUtranCellTDD,EUtranCellRelationId,isHoAllowed";
        $data['file'] = $txt;
        $data['date'] = $now;
        $this->resultToCSV2_par($data, $txt);
        return $data;
    } 
}