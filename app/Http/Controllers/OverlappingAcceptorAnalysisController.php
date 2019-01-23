<?php

/**
 * OverlappingAcceptorAnalysisController.php
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Common\MyRedis;
use PDO;
use App\Models\Mongs\SiteLte;
use App\Models\MR\MroInterfereMatrix_day;

/**
 * 重叠覆盖受主分析
 * Class OverlappingAcceptorAnalysisController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class OverlappingAcceptorAnalysisController extends MyRedis
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
                $CHCity = $dbc->getMRToCHName($r['DATABASE']);
                array_push($items, $CHCity."-".$r['DATABASE']);
            }
        }

        echo json_encode($items);

    }//end getCitys()


    /**
     * 获得分析数据
     *
     * @return void
     */
    public function getData()
    {
        $dataBase = input::get("dataBase");
        $date     = input::get("date");
        $cellName = input::get("cellName");
        $result   = array();
        if (substr($cellName, 0, 3) != 460) {
            $siteLte = SiteLte::where('cellName', $cellName);
            if ($siteLte->exists()) {
                $ecgiNc = $siteLte->first()->ecgi;
            } else {
                return [];
            }
        } else {
            $ecgiNc = $cellName;
        }
        $items     = array();
        $ecgiNc = substr($ecgiNc, 5);
        $row = MroInterfereMatrix_day::on($dataBase)->where('dateId', $date)->where('ecgiNc', $ecgiNc)->orderBy('intensity', 'desc')->get()->toArray();
        $tableData = array();
        foreach ($row as $r) {
            array_push($items, "'".$r["ecgi"]."'");
            array_push($tableData, $r);
        }

        $cells = array();
        if (count($items) > 0) {
            $result['targetCell'] = SiteLte::whereRaw("substring_index(ecgi,'-',-2)='$ecgiNc'")->first()->toArray();
            $cells = SiteLte::whereRaw("substring_index(ecgi,'-',-2) in (".implode($items,',').")")->get()->toArray();
            $result['otherCells'] = $cells;
        }

        $result['tableData'] = $tableData;
        echo json_encode($result);

    }//end getData()


    /**
     * 获得详细数据
     *
     * @return void
     */
    public function getDetailData()
    {
        $dataBase = input::get("dataBase");
        $dbc      = new DataBaseConnection();
        $db       = $dbc->getDB('MR', $dataBase);

        $dbc1 = new DataBaseConnection();
        $db1  = $dbc1->getDB('MR', 'Global');

        $date     = input::get("date");
        $cellName = input::get("cellName");

        $sql    = "select ecgi from siteLte where cellName = '$cellName'";
        $rs     = $db1->query($sql);
        $row    = $rs->fetchAll(PDO::FETCH_NUM);
        $ecgiNc = $row[0][0];

        $cells = input::get("cells");
        $cells = explode(",", $cells);
        $ecgis = array();
        foreach ($cells as $cell) {
            $sql = "select ecgi from siteLte where cellName = '$cell'";
            $rs  = $db1->query($sql);
            $row = $rs->fetchAll(PDO::FETCH_NUM);
            array_push($ecgis, $row[0][0]);
        }

        $items = array();
        foreach ($ecgis as $ecgi) {
            $sql = "select * from mroInterfereMatrix_day where dateId = '".$date."' and ecgi = '$ecgi' and ecgiNc = '$ecgiNc'";
            $rs  = $db->query($sql);
            $row = $rs->fetchAll(PDO::FETCH_ASSOC);
            array_push($items, $row[0]);
        }

        echo json_encode($items);

    }//end getDetailData()


    /**
     * 过滤非法字符
     *
     * @param string $value
     *
     * @return string $value
     */
    function check_input($value)
    {
        $con=mysqli_connect("localhost", "root", "mongs", "mongs");
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
     * 获得日期列表
     *
     * @return void
     */
    public function getDataGroupByDate()
    {
        $dbname = input::get("dataBase");
        $dbname = $this->check_input($dbname);
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('MR', $dbname);
        $table  = 'mroInterfereMatrix_day';
        $sql   = "select distinct dateId from $table order by dateId";
        $this->type = $dbname.':overlappingAcceptorAnalysis';
        return json_encode($this->getValue($db, $sql));

    }//end getDataGroupByDate()


    /**
     * 检查数据
     *
     * @param string $value 数据字串
     *
     * @return string
     */
    /*function check_input($value)
    {
        //去除斜杠
        if (get_magic_quotes_gpc()) {
            $value=stripslashes($value);
        }
        return $value;
    }*/

    public function exportData()
    {
        $dataBase = input::get("dataBase");
        $date     = input::get("date");
        $cellName = input::get("cellName");
        $result   = array();
        if (substr($cellName, 0, 3) != 460) {
            $siteLte = SiteLte::where('cellName', $cellName);
            if ($siteLte->exists()) {
                $ecgiNc = $siteLte->first()->ecgi;
            } else {
                return [];
            }
        } else {
            $ecgiNc = $cellName;
        }
        $ecgiNc = substr($ecgiNc, 5);
        $row = MroInterfereMatrix_day::on($dataBase)->where('dateId', $date)->where('ecgiNc', $ecgiNc)->orderBy('intensity', 'desc')->get()->toArray();
     
        $tableData = array();
        foreach ($row as $r) {
            array_push($tableData, $r);
        }
        $filename = "common/files/重叠覆盖受主分析_" . $date .'_'.$cellName. ".csv";
        $result['text'] = "dateId,ecgi,mr_LteScEarfcn,mr_LteScPci,mr_LteScRSRP,ecgiNc,mr_LteNcEarfcn,mr_LteNcPci,mr_LteNcRSRP,intensity,overCoverageRateNtoS,overCoverageSampleNtoS";
        $result['rows'] = $tableData;
        $this->resultToCSV2($result, $filename);
        $result['filename'] = $filename;
        $result['rows'] = [];
        return json_encode($result);
    }

    /**
     * 写入CSV文件
     *
     * @param array $result 访问记录
     * @param string $filename CSV文件名
     *
     * @return void
     */
    protected function resultToCSV2($result, $filename)
    {
        $csvContent = mb_convert_encoding($result['text'] . "\n", 'GBK', 'UTF-8');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result['rows'] as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

    }//end resultToCSV2()
//end class
}