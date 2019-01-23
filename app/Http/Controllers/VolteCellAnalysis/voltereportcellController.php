<?php

/**
* voltereportcellController.php
*
* @category volteCellAnalysis
* @package  App\Http\Controllers\volteCellAnalysis
* @author   ericsson <genius@ericsson.com>
* @license  MIT License
* @link     https://laravel.com/docs/5.4/controllers
*/
namespace App\Http\Controllers\volteCellAnalysis;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Common\DataBaseConnection_test;
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
 * Class voltereportcellController
 *
 * @category volteCellAnalysis
 * @package  App\Http\Controllers\volteCellAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class voltereportcellController extends Controller
{   
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
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('mongs', 'mongs');
        $sql = "SELECT siteName from siteLte WHERE cellName='$cell';";
        $row = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
        $erbs = $row[0]['siteName'];
        $sql = "SELECT COUNT(*) as num FROM FMA_alarm_list r,mongs.AlarmInfo t WHERE r.SP_text= t.alarmNameE AND DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $endTime . "' AND meContext = '" . $erbs . "';";
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('mongs', 'Alarm');
        $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $result['records'] = $row[0]['num'];
        $result["content"] = "Event_time,meContext,eutranCell,SP_text,Problem_text,alarmNameE,alarmNameC,levelE,levelC,interfere,access,lost,handover,comments";
        $sql = "SELECT Event_time,meContext,eutranCell,SP_text,Problem_text,t.alarmNameE,t.alarmNameC,t.levelE,t.levelC,t.interfere,t.access,t.lost,t.handover,t.comments FROM FMA_alarm_list r,mongs.AlarmInfo t WHERE r.SP_text= t.alarmNameE AND DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $endTime . "' AND meContext = '" . $erbs . "';";
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchAll();
        $result["rows"] = $rows;
        return $result;
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
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('mongs', 'mongs');
        $sql = "SELECT siteName from siteLte WHERE cellName='$cell';";
        $row = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
        $erbs = $row[0]['siteName'];
        $sql = "SELECT COUNT(*) as num FROM FMA_alarm_log r LEFT JOIN mongs.AlarmInfo t ON r.SP_text= t.alarmNameE WHERE DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $endTime . "' AND meContext = '" . $erbs . "';";
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('mongs', 'Alarm');
        $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $result['records'] = $row[0]['num'];
        $result["content"] = "Event_time,meContext,eutranCell,SP_text,Problem_text,alarmNameE,alarmNameC,levelE,levelC,interfere,access,lost,handover,comments";
        $sql = "SELECT Event_time,meContext,eutranCell,SP_text,Problem_text,t.alarmNameE,t.alarmNameC,t.levelE,t.levelC,t.interfere,t.access,t.lost,t.handover,t.comments FROM FMA_alarm_log r LEFT JOIN mongs.AlarmInfo t ON r.SP_text= t.alarmNameE WHERE DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $startTime . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $endTime . "' AND meContext = '" . $erbs . "';";
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchAll();
        $result["rows"] = $rows;
        return $result;
    }

    /**
     * 获得小区的干扰信息
     *
     * @return string json格式干扰
     */
    public function getGanraoCellChart()
    {   
        $cell = input::get('cell');
        $date = Input::get("date");
        $db   = new DataBaseConnection();
        $conn = $db->getDB('autokpi', 'AutoKPI');
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
                    cell = '$cell'
                    and day_id = '$date'
                    GROUP BY day_id,hour_id");
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
     * 获得单小区干扰数据
     *
     * @return array 干扰数据
     */
    public function getInterfereCellModel()
    {
        $cell = Input::get('cell');
        $date = Input::get("date");
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');

        // date_default_timezone_set('PRC');
        // $day_from = date("Y-m-d", strtotime("-1 day"));
        // $day_to = date("Y-m-d");

        $return = array();

        $sql = "select day_id,hour_id,city,subNetwork,cell,PRB1上行干扰电平,PRB2上行干扰电平,PRB3上行干扰电平,PRB4上行干扰电平,PRB5上行干扰电平,PRB6上行干扰电平,PRB7上行干扰电平,PRB8上行干扰电平,PRB9上行干扰电平,PRB10上行干扰电平,PRB11上行干扰电平,PRB12上行干扰电平,PRB13上行干扰电平,PRB14上行干扰电平,PRB15上行干扰电平,PRB16上行干扰电平,PRB17上行干扰电平,PRB18上行干扰电平,PRB19上行干扰电平,PRB20上行干扰电平,PRB21上行干扰电平,PRB22上行干扰电平,PRB23上行干扰电平,PRB24上行干扰电平,PRB25上行干扰电平,PRB26上行干扰电平,PRB27上行干扰电平,PRB28上行干扰电平,PRB29上行干扰电平,PRB30上行干扰电平,PRB31上行干扰电平,PRB32上行干扰电平,PRB33上行干扰电平,PRB34上行干扰电平,PRB35上行干扰电平,PRB36上行干扰电平,PRB37上行干扰电平,PRB38上行干扰电平,PRB39上行干扰电平,PRB40上行干扰电平,PRB41上行干扰电平,PRB42上行干扰电平,PRB43上行干扰电平,PRB44上行干扰电平,PRB45上行干扰电平,PRB46上行干扰电平,PRB47上行干扰电平,PRB48上行干扰电平,PRB49上行干扰电平,PRB50上行干扰电平,PRB51上行干扰电平,PRB52上行干扰电平,PRB53上行干扰电平,PRB54上行干扰电平,PRB55上行干扰电平,PRB56上行干扰电平,PRB57上行干扰电平,PRB58上行干扰电平,PRB59上行干扰电平,PRB60上行干扰电平,PRB61上行干扰电平,PRB62上行干扰电平,PRB63上行干扰电平,PRB64上行干扰电平,PRB65上行干扰电平,PRB66上行干扰电平,PRB67上行干扰电平,PRB68上行干扰电平,PRB69上行干扰电平,PRB70上行干扰电平,PRB71上行干扰电平,PRB72上行干扰电平,PRB73上行干扰电平,PRB74上行干扰电平,PRB75上行干扰电平,PRB76上行干扰电平,PRB77上行干扰电平,PRB78上行干扰电平,PRB79上行干扰电平,PRB80上行干扰电平,PRB81上行干扰电平,PRB82上行干扰电平,PRB83上行干扰电平,PRB84上行干扰电平,PRB85上行干扰电平,PRB86上行干扰电平,PRB87上行干扰电平,PRB88上行干扰电平,PRB89上行干扰电平,PRB90上行干扰电平,PRB91上行干扰电平,PRB92上行干扰电平,PRB93上行干扰电平,PRB94上行干扰电平,PRB95上行干扰电平,PRB96上行干扰电平,PRB97上行干扰电平,PRB98上行干扰电平,PRB99上行干扰电平,PRB100上行干扰电平,type from interfereCell where cell='" . $cell . "' AND day_id='" . $date . "' order by day_id DESC";
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $allData = array();
        foreach ($rows as $row) {
            array_push($allData, $row);
        }
        $return['content'] = "day_id,hour_id,city,subNetwork,cell,PRB1上行干扰电平,PRB2上行干扰电平,PRB3上行干扰电平,PRB4上行干扰电平,PRB5上行干扰电平,PRB6上行干扰电平,PRB7上行干扰电平,PRB8上行干扰电平,PRB9上行干扰电平,PRB10上行干扰电平,PRB11上行干扰电平,PRB12上行干扰电平,PRB13上行干扰电平,PRB14上行干扰电平,PRB15上行干扰电平,PRB16上行干扰电平,PRB17上行干扰电平,PRB18上行干扰电平,PRB19上行干扰电平,PRB20上行干扰电平,PRB21上行干扰电平,PRB22上行干扰电平,PRB23上行干扰电平,PRB24上行干扰电平,PRB25上行干扰电平,PRB26上行干扰电平,PRB27上行干扰电平,PRB28上行干扰电平,PRB29上行干扰电平,PRB30上行干扰电平,PRB31上行干扰电平,PRB32上行干扰电平,PRB33上行干扰电平,PRB34上行干扰电平,PRB35上行干扰电平,PRB36上行干扰电平,PRB37上行干扰电平,PRB38上行干扰电平,PRB39上行干扰电平,PRB40上行干扰电平,PRB41上行干扰电平,PRB42上行干扰电平,PRB43上行干扰电平,PRB44上行干扰电平,PRB45上行干扰电平,PRB46上行干扰电平,PRB47上行干扰电平,PRB48上行干扰电平,PRB49上行干扰电平,PRB50上行干扰电平,PRB51上行干扰电平,PRB52上行干扰电平,PRB53上行干扰电平,PRB54上行干扰电平,PRB55上行干扰电平,PRB56上行干扰电平,PRB57上行干扰电平,PRB58上行干扰电平,PRB59上行干扰电平,PRB60上行干扰电平,PRB61上行干扰电平,PRB62上行干扰电平,PRB63上行干扰电平,PRB64上行干扰电平,PRB65上行干扰电平,PRB66上行干扰电平,PRB67上行干扰电平,PRB68上行干扰电平,PRB69上行干扰电平,PRB70上行干扰电平,PRB71上行干扰电平,PRB72上行干扰电平,PRB73上行干扰电平,PRB74上行干扰电平,PRB75上行干扰电平,PRB76上行干扰电平,PRB77上行干扰电平,PRB78上行干扰电平,PRB79上行干扰电平,PRB80上行干扰电平,PRB81上行干扰电平,PRB82上行干扰电平,PRB83上行干扰电平,PRB84上行干扰电平,PRB85上行干扰电平,PRB86上行干扰电平,PRB87上行干扰电平,PRB88上行干扰电平,PRB89上行干扰电平,PRB90上行干扰电平,PRB91上行干扰电平,PRB92上行干扰电平,PRB93上行干扰电平,PRB94上行干扰电平,PRB95上行干扰电平,PRB96上行干扰电平,PRB97上行干扰电平,PRB98上行干扰电平,PRB99上行干扰电平,PRB100上行干扰电平";
        $return['rows'] = $allData;
        $return['records'] = count($allData);

        return ($return);
    }


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
        $date_from = date("Y-m-d");
        $table = Input::get('table');
        $cell = Input::get('cell');
        $date = Input::get('date');
        $city = Input::get('city');
        $yAxis_name = Input::get('yAxis_name');
        $res = $conn->query("select day_id,hour_id,`" . $yAxis_name . "` from " . $table . " where day_id>='" . $date . "' and day_id<='" . $date_from . "'and cell='" . $cell . "' and city = '$city' ORDER BY day_id,hour_id");
        // var_dump($res);
        // $res = mysql_query("select day_id,hour_id,`" . $yAxis_name_left . "`,`" . $yAxis_name_right . "` from " . $table . " where day_id>='" . $startTime . "' and day_id<='" . $endTime . "' and cell='" . $cell . "' ORDER BY day_id,hour_id");
        // print_r($res);return;
        $yAxis = array();
        $items = array();
        $returnData = array();
        $series = array();
        $categories = array();
        // $line = $res->fetch(PDO::FETCH_NUM);
        // var_dump($line);return;

        while ($line = $res->fetch(PDO::FETCH_NUM)) {
            // var_dump($line);
            $time = strval(strval($line[0]) . " " . strval($line[1])) . ":00";

            $time = mb_convert_encoding($time, 'gb2312', 'utf-8');

            array_push($yAxis, $line[2]);
            array_push($categories, $time);

        }


        $series['name'] = $yAxis_name;
        $series['color'] = '#4572A7';
        $series['type'] = 'column';
        // $series['yAxis'] = 1;
        $series['data'] = $yAxis;

        array_push($items, $series);

        $returnData['categories'] = $categories;
        $returnData['series'] = $items;
        // var_dump($items);return;
        $maxPos = array_search(max($yAxis), $yAxis);
        $max = $yAxis[$maxPos];
        $minPos = array_search(min($yAxis), $yAxis);
        $min = $yAxis[$minPos];
        $yAxis = $this->getYAxis($max, $min);
        $returnData['yAxis'] = $yAxis;
        $returnData['cell'] = $cell;
        // var_dump($$returnData['series']);return;
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
     * 获得下行质差数据
     *
     * @return array 质差数据
     */
    public function getzhichaCellModel()
    {
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');

        $date = Input::get('date');
        $city = Input::get('city');
        $result = array();
        $sql = "select id,day_id,hour_id,city,subNetwork,cell,`RSRQ<-15.5的比例` from lowAccessCell where day_id = '" . $date . "' AND cell='" . $cell . "' AND city = '$city'";
        // var_dump($sql);
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $allData = array();
        foreach ($rows as $row) {
            array_push($allData, $row);
        }
        $result['content'] = "id,day_id,hour_id,city,subNetwork,cell,RSRQ<-15.5的比例";
        $result['rows'] = $allData;
        $result['records'] = count($allData);
        return $result;

    }//end getzhichaCellModel()


     /**
     * 获得单小区重叠覆盖数据
     *
     * @return array 重叠覆盖数据
     */
    public function getOverlapCoverModel()
    {
        $cell = Input::get('cell');
        $date = Input::get('date');
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
                AND dateId = '$date'
                AND cellName = '$cell';";        
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
     * 获得单小区弱覆盖数据
     *
     * @return array 弱覆盖数据
     */
    public function getWeakCoverCellModel()
    {
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('mongs', 'AutoKPI');

        $date= Input::get('date');
        $table = Input::get("table");
        $city = Input::get("city");
        $return = array();
        $sql = "select id,day_id,hour_id,city,subNetwork,cell,`RSRP<-116的比例` from ".$table." where day_id = '$date' AND cell='$cell' AND city = '$city'";
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $allData = array();
        foreach ($rows as $row) {
            array_push($allData, $row);
        }
        $return['content'] = "id,day_id,hour_id,city,subNetwork,cell,RSRP<-116的比例";
        $return['rows'] = $allData;
        $return['records'] = count($allData);
        return ($return);
    }

    /**
     * 获取baseline参数对比数据
     *
     * @return string
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
        $dbmr = "";
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
        $sql = "SELECT subNetwork,ipAddr,meContext,pdcpSNLength,rlcSNLength FROM QciProfilePredefined WHERE qciProfilePredefinedId='qci1' AND meContext ='$site'";
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
        }//SN length参数设置错误
        array_push($result, $item);
        $item = array();
        $sql = "select * from TempEUtranCellRelationFewNeighborCell where EutranCellTDD = '$cell' and remark1 = 'co-SiteNeighborRelationMiss'";
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
        }//没有定义同频站点 
        array_push($result, $item);
        $item = array();
        $sql = "select subNetwork,meContext,ipAddr,eUtranCellRelationId,EUtranFreqRelation from EUtranCellRelation where meContext = '$site' GROUP by meContext";
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
        }//未定邻区
        array_push($result, $item);
        $item = array();
        $sql = "select subNetwork,meContext,ipAddr,geranCellRelationId from GeranCellRelation where meContext = '$site' GROUP by meContext";
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
        }//没有定义2G邻区 
        array_push($result, $item);
        $item = array();
        $sql = "select subNetwork,meContext,ipAddr,eUtranFreqRelationId from EUtranFreqRelation where EutranCellTDD = '$cell' AND EUtranFreqRelation is NULL GROUP by meContext";
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
        }//没有定义本小区freqrel
        array_push($result, $item);
        $item = array();
        $sql = "select subNetwork,meContext,EutranCellTDD,EUtranCellRelationId,EUtranCellTDDNeigh,pci from TempEUtranCellRelationNeighOfPci where EutranCellTDD = '$cell'";
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
        }//一阶冲突
        array_push($result, $item);
        $item = array();
        $sql = "select * from TempEUtranCellRelationNeighOfNeighPci where EutranCellTDD = '$cell'";
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
        }//二阶冲突
        array_push($result, $item);
        $item = array();
        $sql = "select subNetwork,meContext,parameter,KGET_Value,CDD_Value from TempParameter2GKgetCompare WHERE meContext ='$site'";
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
        }//外部2G小区定义错误
        array_push($result, $item);
        $item = array();
        $sql = "select subNetwork,meContext,parameter,ExternalValue,value from TempExternalNeigh4G WHERE meContext ='$site'";
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
        }//外部4G小区定义错误
        array_push($result, $item);
        $item = array();
        $sql = "SELECT *  FROM mroPciMod3_day WHERE mr_LteScPciMod3 = mr_LteNcPciMod3 and EutrancellTddName = '$cell' AND dateId = '$date'";
        $rs   = $dbmr->query($sql);
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
        }//MRE排名前20名的小区mod3重叠覆盖
        array_push($result, $item);
        $item = array();
        $sql = "select subNetwork,meContext,cellId,moName,parameter from ParaCheckBaseline where templateId = 53 and category = 'A' and ( cellId = '$cell' or (meContext = '$site' and cellId = ''))";
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
        }//baseline中A类参数配置不一致的
        array_push($result, $item);
        $item = array();
        $sql = "select subNetwork,meContext,OptionalFeatureLicenseId,featureState from OptionalFeatureLicense WHERE OptionalFeatureLicenseId = 'TCPOptimization' and meContext = '$site' and featureState not like '%1 (ACTIVATED)%'";
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
        }//TCPO
        array_push($result, $item);
        $item = array();
        $sql = "select subNetwork,meContext,ulMaxHARQTxQci,dlMaxHARQTxQci from QciProfilePredefined WHERE qciProfilePredefinedId = 'qci1' and meContext = '$site'";
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
        }//HARQ次数
        array_push($result, $item);
        $item = array();
        $sql = "select subNetwork,meContext,OptionalFeatureLicenseId,featureState from OptionalFeatureLicense WHERE OptionalFeatureLicenseId ='RlcUm' and featureState not like '%1 (ACTIVATED)%' and meContext = '$site'";
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
        }//RLC reordering参数
        array_push($result, $item);
        $item = array();
        $sql = "select subNetwork,meContext,qciProfilePredefinedId,rlcMode from QciProfilePredefined WHERE qciProfilePredefinedId = 'qci1' and meContext = '$site' and rlcMode not like '%1 (UM)%'";
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
        }//RLC reordering参数
        array_push($result, $item);
        $item = array();
        $sql = "select subNetwork,meContext,keyId,featureState from OptionalFeatureLicense WHERE keyId ='CXC4010961' and featureState not like '%1 (ACTIVATED)%' and meContext = '$site'";
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
        }//RLC reordering参数
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
        //A5频率偏移核查1
        $item = array();
        $sql = "SELECT subNetwork,EUtranCellTDD,a5Threshold1RsrpAdjust FROM TempA5Threshold1Rsrp WHERE EUtranCellTDD = '$cell'";
        $res = $db->query($sql);
        $rows = $res->fetchall(PDO::FETCH_ASSOC);
        $item['record'] = count($rows);
        if ($rows) {
            $item['content'] = implode(",", array_keys($rows[0]));
            foreach ($rows as $row) {
                $item['rows'][] = $row;
            }
        }
        array_push($result, $item);
        //A5频率偏移核查2
        $item = array();
        $sql = "SELECT subNetwork,EUtranCellTDD,a5Threshold2RsrpAdjust FROM TempA5Threshold2Rsrp WHERE EUtranCellTDD = '$cell'";
        $res = $db->query($sql);
        $rows = $res->fetchall(PDO::FETCH_ASSOC);
        $item['record'] = count($rows);
        if ($rows) {
            $item['content'] = implode(",", array_keys($rows[0]));
            foreach ($rows as $row) {
                $item['rows'][] = $row;
            }
        }
        array_push($result, $item);
        //B2频率偏移核查1
        $item = array();
        $sql = "SELECT subNetwork,meContext,b2Threshold1RsrpGeranAjust FROM TempB2Threshold1RsrpGeranOffset WHERE meContext = '$site'";
        $res = $db->query($sql);
        $rows = $res->fetchall(PDO::FETCH_ASSOC);
        $item['record'] = count($rows);
        if ($rows) {
            $item['content'] = implode(",", array_keys($rows[0]));
            foreach ($rows as $row) {
                $item['rows'][] = $row;
            }
        }
        array_push($result, $item);
        //B2频率偏移核查2
        $item = array();
        $sql = "SELECT subNetwork,meContext,b2Threshold2GeranAjust FROM TempB2Threshold2GeranOffset WHERE meContext = '$site'";
        $res = $db->query($sql);
        $rows = $res->fetchall(PDO::FETCH_ASSOC);
        $item['record'] = count($rows);
        if ($rows) {
            $item['content'] = implode(",", array_keys($rows[0]));
            foreach ($rows as $row) {
                $item['rows'][] = $row;
            }
        }
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
        
        echo json_encode($result);

    }//end getBaselineCheckData()

      /**
     * 获得基站名称
     *
     * @return string 基站名称
     */
    public function getSitename($cell) 
    {
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB("mongs", "mongs");
        $sqlsite = "select siteName from siteLte where cellName='$cell'";
        $ressite =$db->query($sqlsite);
        $rows = $ressite->fetch(PDO::FETCH_NUM);
        $site = $rows[0];
        return $site;
    }

       /**
     * 获得单小区告警数据
     *
     * @return array 告警数据
     */
    public function getAlarmCellModel()
    {
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('mongs', 'Alarm');
        $endtime = date("Y-m-d");
        $date= Input::get('date');
        $table = Input::get("table");
        $city = Input::get("city");
        $return = array();
        $site = $this->getSitename($cell);
        $sql = "SELECT Event_time,meContext,eutranCell,SP_text,Problem_text,t.alarmNameE,t.alarmNameC,t.levelE,t.levelC,t.interfere,t.access,t.lost,t.handover,t.comments FROM FMA_alarm_list r,mongs.AlarmInfo t WHERE r.SP_text= t.alarmNameE AND DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $date . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $endtime . "' AND meContext = '" . $site . "';";
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $allData = array();
        $return["content"] = "Event_time,meContext,eutranCell,SP_text,Problem_text,alarmNameE,alarmNameC,levelE,levelC,interfere,access,lost,handover,comments";
        foreach ($rows as $row) {
            array_push($allData, $row);
        }
        $return['rows'] = $allData;
        $return['records'] = count($rows);
        return ($return);
    }


     /**
     * 获得基站级告警详细
     *
     * @return array 基站级告警详细
     */
    public function getAlarmErbsModel()
    {
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('mongs', 'Alarm');
        $date= Input::get('date');
        $endtime = date("Y-m-d");
        // var_dump($endtime);
        $table = Input::get("table");
        $city = Input::get("city");
        $return = array();
        $site = $this->getSitename($cell);
        $return["content"] = "Event_time,meContext,eutranCell,SP_text,Problem_text,alarmNameE,alarmNameC,levelE,levelC,interfere,access,lost,handover,comments";
        $sql = "SELECT Event_time,meContext,eutranCell,SP_text,Problem_text,t.alarmNameE,t.alarmNameC,t.levelE,t.levelC,t.interfere,t.access,t.lost,t.handover,t.comments FROM FMA_alarm_log r LEFT JOIN mongs.AlarmInfo t ON r.SP_text= t.alarmNameE WHERE DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" . $date . "' AND DATE_FORMAT(Event_time, '%Y-%m-%d') <= '" . $endtime . "' AND meContext = '" . $site . "';";
        // print_r($sql);
        $allData = array();
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        foreach ($rows as $row) {
            array_push($allData, $row);
        }
        $return['rows'] = $allData;
        $return['records'] = count($rows);
        return ($return);
    }


    /**
    *获得单小区PUSCH低比例
    *
    *@return array 低比例数据
    **/
    public function getPusch()
    {
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $date_from = date("Y-m-d");
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $date = Input::get('date');
        $city = Input::get('city');
        $result = array();
        $sql = "select day_id,hour_id,city,subNetwork,cell,`PUSCH-SINR低于-5dB的比例` from voltereportcell where day_id >= '" . $date . "' and day_id <='".$date_from."' AND cell='" . $cell . "' AND city = '$city'";
        // var_dump($sql);
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $allData = array();
        foreach ($rows as $row) {
            array_push($allData, $row);
        }
        $result['content'] = "day_id,hour_id,city,subNetwork,cell,PUSCH-SINR低于-5dB的比例";
        $result['rows'] = $allData;
        $result['records'] = count($allData);
        return $result;
    }

     /**
    *获得单小区PUCCH低比例
    *
    *@return array 低比例数据
    **/
    public function getPucch()
    {
        $cell = Input::get('cell');
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('autokpi', 'AutoKPI');
        $date = Input::get('date');
        $city = Input::get('city');
        $date_from = date("Y-m-d");
        $result = array();
        $sql = "select day_id,hour_id,city,subNetwork,cell,`PUCCHSINR低于-15的的比例` from voltereportcell where day_id >= '" . $date . "' and day_id<='".$date_from."'AND cell='" . $cell . "' AND city = '$city'";
        // var_dump($sql);
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $allData = array();
        foreach ($rows as $row) {
            array_push($allData, $row);
        }
        $result['content'] = "day_id,hour_id,city,subNetwork,cell,PUCCHSINR低于-15的的比例";
        $result['rows'] = $allData;
        $result['records'] = count($allData);
        return $result;
    }

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

        $date = Input::get('date');

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

        $sql = "select * from $table where isdefined_direct=0 and ecgi = '$ecgi' AND distance_direct<0.8 and dateId = '" . $date. "';";
        // print_r($sql);
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        // var_dump($rows);return;
        $allData = array();
        foreach ($rows as $row) {
            array_push($allData, $row);
        }
        $return['content'] = implode(",", array_keys($rows[0]));
        $return['rows'] = $allData;
        $return['records'] = count($allData);
        return ($return);
    }


    /**
     * 获得GSM补邻区数据
     *
     * @return array GSM补邻区数据
     */
    public function getGSMNeighborDataModel()
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

        $date = Input::get('date');

        $result = array();
        $return = array();
        $dsn = new DataBaseConnection();
        $db = $dsn->getDB('MR', $dbname);
        $table = 'mreServerNeighIrat_day';
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

        $sql = "select * from $table where isdefined=0 and ecgi = '4600-163790-129' AND distance<0.8 and dateId = '" . $date. "';";
        // print_r($sql);
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        // var_dump($rows);return;
        $allData = array();
        foreach ($rows as $row) {
            array_push($allData, $row);
        }
        $return['content'] = implode(",", array_keys($rows[0]));
        $return['rows'] = $allData;
        $return['records'] = count($allData);
        return ($return);
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
        $date = Input::get('date');

        //获取缺失邻区
        $db = $dsn->getDB('MR', $dbname);
        // $sql = "select * from mreServeNeigh_day where isdefined_direct=0 and ecgi = '$ecgi' AND distance_direct<0.8 and dateId >= '$dateTime'";
        $sql = "/*!mycat:sql=select 1 from MRS*/
                SELECT
                    s.longitudeBD,s.latitudeBD,s.dir,s.band,s.cellName
                FROM
                    mreServeNeigh_day AS m
                LEFT JOIN `GLOBAL`.siteLte AS s ON m.ecgi = s.ecgi
                WHERE
                    m.isdefined_direct = 0
                AND m.ecgi = '$ecgi'
                AND m.distance_direct < 0.8
                AND m.dateId >= '$date'";
        // var_dump($sql);return;
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $return['lostNeigh'] = $rows;

        //获取已有邻区
        $db = $dsn->getDB('mongs', 'AutoKPI');
        $sql = "select slongitude,slatitude,sdir,sband,scell from SysRelation_cell_day where day_id = '$date' and cell = '$cell' and slongitude is not null and slatitude is not null and sdir is not null and sband is not null";
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $return['Neigh'] = $rows;

        return ($return);
    }

    /**
     * 获取2G小区相关邻区
     *
     * @return json
     */
    public function get2GNeighborCellMapData()
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
        $date = Input::get('date');

        //获取缺失邻区
        $db = $dsn->getDB('MR', $dbname);
        // $sql = "select * from mreServeNeigh_day where isdefined_direct=0 and ecgi = '$ecgi' AND distance_direct<0.8 and dateId >= '$dateTime'";
        $sql = "/*!mycat:sql=select 1 from MRS*/
                SELECT
                    s.longitudeBD,s.latitudeBD,s.dir,s.band,s.cellName
                FROM
                    mreServerNeighIrat_day AS m
                LEFT JOIN `GLOBAL`.siteLte AS s ON m.ecgi = s.ecgi
                WHERE
                    m.isdefined = 0
                AND m.ecgi = '$ecgi'
                AND m.distance < 0.8
                AND m.dateId >= '$date'";
        // var_dump($sql);return;
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $return['lostNeigh'] = $rows;

        //获取已有邻区
        $db = $dsn->getDB('mongs', 'AutoKPI');
        $sql = "select slongitude,slatitude,sdir,sband,scell from SysRelation_cell_day where day_id = '$date' and cell = '$cell' and slongitude is not null and slatitude is not null and sdir is not null and sband is not null";
        $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $return['Neigh'] = $rows;

        return ($return);
    }

}