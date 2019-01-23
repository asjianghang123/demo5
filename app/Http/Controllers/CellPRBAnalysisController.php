<?php

/**
 * CellPRBAnalysisController.php
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
use App\Http\Controllers\Common\MyRedis;
use PDO;

/**
 * 小区PROB分析
 * Class CellPRBAnalysisController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class CellPRBAnalysisController extends MyRedis
{


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
        $dbc    = new DataBaseConnection();
        $con     = $dbc->getConnDBi('mongs', 'mongs');
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
     * 获得PRB分析数据
     *
     * @return void
     */
    public function getPRBAnalysisData()
    {
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('mongs', 'AutoKPI');
        $cell   = input::get("cell");
        $cell=$this->check_input($cell);
        $day_id = input::get("day_id");
        $day_id=$this->check_input($day_id);
        $filter_categories = "select COLUMN_NAME from information_schema.COLUMNS where table_name='interfereCell'";
        $filter_series     = "select * from interfereCell where day_id='$day_id' and cell='$cell'";
        $subNetwork        = "select DISTINCT subNetwork from interfereCell where cell = '$cell'";
        $items      = array();
        $returnData = array();
        $series     = array();
        $categories = array();

        $res = $db->query($filter_categories);
        $row = $res->fetchAll(PDO::FETCH_NUM);
        foreach ($row as $line_categories) {
            if (strstr($line_categories[0], "PRB") == false) {
                continue;
            }

            array_push($categories, $line_categories[0]);
        }

        $res  = $db->query($filter_series);
        $row = $res->fetchAll(PDO::FETCH_NUM);
        foreach ($row as $line_categories) {
            if (strstr($line_categories[0], "PRB") == false) {
                continue;
            }
            array_push($categories, $line_categories[0]);
        }
        $res  = $db->query($filter_series);
        $rows = $res->fetchAll(PDO::FETCH_ASSOC);
        for ($j = 0; $j < count($rows); $j++) {
            $data = array();
            foreach ($rows[$j] as $key=>$line) {
                if ($key == "type" || $key == "day_id" || $key == "city" || $key == "subNetwork" || $key == "cell" || $key == "id" || $key == "hour_id") {
                    continue;
                }
                array_push($data, floatval($line));
            }

            $series['name'] = $rows[$j]["hour_id"].'时';
            $series['data'] = $data;
            array_push($items, $series);
        }

        $res = $db->query($subNetwork);
        $row = $res->fetch(PDO::FETCH_NUM);
        $returnData['categories'] = $categories;
        $returnData['series']     = $items;
        $returnData['subNetwork'] = $row[0];
        echo json_encode($returnData);

    }//end getPRBAnalysisData()


    /**
     * 获得时间列表
     *
     * @return array
     */
    public function getPRBTime()
    {
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('mongs', 'AutoKPI');
        $table  = 'interfereCell';
        $sql    = "select distinct day_id from $table";
        $this->type = 'AutoKPI:cellPRBAnalysis';
        return $this->getValue($db, $sql);

    }//end getPRBTime()


}//end class
