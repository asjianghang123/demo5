<?php

/**
 * SingalChartsController.php
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;

/**
 * 信令概览
 * Class SingalChartsController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class SingalChartsController extends Controller
{


    /**
     * 获得信令成功率趋势
     *
     * @return void
     */
    public function getSingalTrend()
    {
        $dbn = new DataBaseConnection();
        $db  = $dbn->getDB("CDR");
        // 获取城市数据库
        $rows      = $db->query("SHOW DATABASES")->fetchall(PDO::FETCH_ASSOC);
        $dataBases = array();
        foreach ($rows as $row) {
            $dataBase = $row['DATABASE'];
            $pos      = strpos($dataBase, "CDR");
            if ($pos === false) {
                continue;
            } else {
                array_push($dataBases, $dataBase);
            }
        }

        // 获取eventName
        $db         = $dbn->getDB("CDR", $dataBases[0]);
        $rows       = $db->query("SELECT DISTINCT eventName FROM eventSuccess ORDER BY eventName")->fetchall(PDO::FETCH_ASSOC);
        $eventNames = array();
        foreach ($rows as $row) {
            array_push($eventNames, $row['eventName']);
        }

        $startTime = date('Y-m-d', strtotime('-60 days'));
        $result    = array();
        foreach ($eventNames as $eventName) {
            $items = array();
            foreach ($dataBases as $dataBase) {
                $db   = $dbn->getDB("CDR", $dataBase);
                $sql  = "SELECT eventName,(UNIX_TIMESTAMP(date_id)+8*3600)*1000 AS datetime_id,date_id,sum(timesSuccess) / sum(timesTotal)*100 AS kpi FROM eventSuccess WHERE date_id>='".$startTime."' and eventName = '$eventName' GROUP BY date_id,eventName;";
                $rows = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
                $data = array();
                foreach ($rows as $row) {
                    $datetime = $row["datetime_id"];
                    $kpi      = $row["kpi"];
                    array_push($data, array(floatval($datetime), round(floatval($kpi), 2)));
                }

                $item         = array();
                $item['data'] = $data;
                $item['name'] = $dbn->getCDRToCHName($dataBase);
                array_push($items, $item);
            }

            $result[$eventName] = $items;
        }//end foreach

        echo json_encode($result);

    }//end getSingalTrend()


    /**
     * 获得信令趋势
     *
     * @return string 结果
     */
    public function getSingalTrend1()
    {
        $startTime = date('Y-m-d', strtotime('-60 days'));
        $dbn       = new DataBaseConnection();
        $db        = $dbn->getDB("CDR");
        $rows      = $db->query("SHOW DATABASES")->fetchall(PDO::FETCH_ASSOC);
        $table     = [];
        foreach ($rows as $row) {
            $tables = $row['DATABASE'];
            $pos    = strpos($tables, "CDR");
            if ($pos === false) {
                continue;
            } else {
                array_push($table, $tables);
            }
        }

        $result = [];
        foreach ($table as $row) {
            $db     = $dbn->getDB("CDR", $row);
            $sql    = "SELECT eventName,(UNIX_TIMESTAMP(date_id)+8*3600)*1000 AS datetime_id,date_id,sum(timesSuccess) / sum(timesTotal)*100 AS kpi FROM eventSuccess WHERE date_id>='".$startTime."' GROUP BY date_id,eventName;";
            $result = $db->query($sql)->fetchall(PDO::FETCH_ASSOC);
        }

        return $this->getStrToTimeTest($result);

    }//end getSingalTrend1()


    /**
     * 生成时序数据
     *
     * @param array $result 结果集
     *
     * @return string 时序数据
     */
    public function getStrToTimeTest($result)
    {
        $series   = array();
        $category = array();
        $array    = array();
        foreach ($result as $item) {
            $city     = $item["eventName"];
            $datetime = $item["datetime_id"];
            $kpi      = $item["kpi"];
            $arr      = array();
            if (array_search($city, $category) === false) {
                $category[] = $city;
            }

            if (!array_key_exists($city, $series)) {
                $series[$city] = array();
            }

            array_push($arr, floatval($datetime));
            array_push($arr, round(floatval($kpi), 2));
            $series[$city][] = $arr;
            array_push($array, floatval($kpi));
        }

        $maxPos         = array_search(max($array), $array);
        $max            = $array[$maxPos];
        $minPos         = array_search(min($array), $array);
        $min            = $array[$minPos];
        $yAxis          = $this->getYAxis($max, $min);
        $data['yAxis']  = $yAxis;
        $data['series'] = array();
        foreach ($series as $key => $value) {
            $data['series'][] = [
                                 'name' => $key,
                                 'data' => $value,
                                ];
        }

        return json_encode($data);

    }//end getStrToTimeTest()


    /**
     * 生成YAxis
     *
     * @param float $max 最大值
     * @param float $min 最小值
     *
     * @return array YAxis
     */
    protected function getYAxis($max, $min)
    {
        $yAxis  = array();
        $max    = ceil($max);
        $min    = floor($min);
        $yAxis0 = $min;
        $yAxis2 = round((($min + $max) / 2), 2);
        $yAxis1 = round((($min + $yAxis2) / 2), 2);
        $yAxis4 = $max;
        $yAxis3 = round((($max + $yAxis2) / 2), 2);
        $yAxis5 = ($yAxis4 + 0.0001);
        array_push($yAxis, $yAxis0, $yAxis1, $yAxis2, $yAxis3, $yAxis4, $yAxis5);
        return $yAxis;

    }//end getYAxis()


}//end class
