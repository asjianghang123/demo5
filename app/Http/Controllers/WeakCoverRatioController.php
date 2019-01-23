<?php

/**
 * WeakCoverRatioController.php
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
use App\Models\MR\MroWeakCoverage_day;

/**
 * 弱覆盖比例
 * Class WeakCoverRatioController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class WeakCoverRatioController extends MyRedis
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
     * 获得弱覆盖比例
     *
     * @return string
     */
    public function searchWeakCoverRatio()
    {
        $date      = Input::get('date');
        $date=$this->check_input($date);
        $dataBases = [];
        $sql       = "SHOW DATABASES";
        $dbc       = new DataBaseConnection();
        $rs        = $dbc->getDB('MR')->query($sql, PDO::FETCH_ASSOC);
        if ($rs) {
            $rows = $rs->fetchall();
            foreach ($rows as $row) {
                if (substr($row['DATABASE'], 0, 2) === 'MR') {
                    array_push($dataBases, $row['DATABASE']);
                }
            }
        }

        $series = array();
        $return = array();

        $mrCity = array();
        $conn = new MroWeakCoverage_day;
        foreach ($dataBases as $dataBase) {
            $conn = $conn->setConnection($dataBase);
            $num = $conn->where('ratio110', '>', 20)->where('dateId', $date)->count();
            $total = $conn->where('dateId', $date)->count();
            if ($total == 0) {
                $temp = 0;
            } else {
                $temp = ($num / $total * 100);
            }

            $series['name']   = '';
            $series['data'][] = floatval($temp);
            $dbc = new DataBaseConnection();
            array_push($mrCity, $dbc->getMRToCHName($dataBase));
        }

        $return['date']     = Input::get('date');
        $return['category'] = $mrCity;
        $return['series'][] = $series;
        return json_encode($return);

    }//end SearchWeakCoverRatio()


    /**
     * 获得日期列表
     *
     * @return array
     */
    public function startTime()
    {
        $dataBases = [];
        $sql       = "SHOW DATABASES";
        $dbc       = new DataBaseConnection();
        $rs        = $dbc->getDB('MR')->query($sql, PDO::FETCH_ASSOC);
        if ($rs) {
            $rows = $rs->fetchall();
            foreach ($rows as $row) {
                if (substr($row['DATABASE'], 0, 2) === 'MR') {
                    array_push($dataBases, $row['DATABASE']);
                }
            }
        }

        foreach ($dataBases as $dataBase) {
            $dbc    = new DataBaseConnection();
            $db     = $dbc->getDB('MR', $dataBase);
            $result = array();
            $table  = 'mroWeakCoverage_day';
            $sql  = "select distinct dateId from $table where ratio110 > 0.2";
            $this->type = $dataBase.':weakCoverRatio';
            return $this->getValue($db, $sql);
        }//end foreach

    }//end startTime()


}//end class
