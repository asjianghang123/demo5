<?php

/**
 * MarketAnalysisController.php
 *
 * @category UserAnalysis
 * @package  App\Http\Controllers\UserAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\UserAnalysis;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;

/**
 * 市场分析
 * Class MarketAnalysisController
 *
 * @category UserAnalysis
 * @package  App\Http\Controllers\UserAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class MarketAnalysisController extends Controller
{


    /**
     * 获得城市列表
     *
     * @return void
     */
    public function getCitys()
    {
        $dbc   = new DataBaseConnection();
        $db    = $dbc->getDB('CDR');
        $sql   = "show dataBases";
        $res   = $db->query($sql);
        $items = array();
        $row   = $res->fetchAll(PDO::FETCH_ASSOC);
        foreach ($row as $r) {
            if ($r['DATABASE'] != 'Global') {
                $CHCity = $dbc->getCDRToCHName($r['DATABASE']);
                array_push($items, $CHCity."-".$r['DATABASE']);
            }
        }

        echo json_encode($items);

    }//end getCitys()


    /**
     * 获得品牌数据
     *
     * @return void
     */
    public function getBrandData()
    {
        $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 20;
        $offset = (($page - 1) * $rows);
        $limit  = " limit $offset,$rows";
        $city   = input::get("city");
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('CDR', $city);

        $result = array();
        $sql    = "select brandName, count(*) as users from userInfo group by brandName";
        $res    = $db->query($sql);
        if (!$res) {
            $this->getBrandData();
            return;
        }

        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $result["total"] = count($row);

        $sql = "select brandName, count(*) as users from userInfo group by brandName ORDER BY users desc".$limit;
        $rs  = $db->query($sql);
        if (!$rs) {
            $this->getBrandData();
            return;
        }

        $items = array();
        $row   = $rs->fetchAll(PDO::FETCH_ASSOC);
        $i     = (1 + $offset);
        foreach ($row as $r) {
            $r["rank"] = $i++;
            array_push($items, $r);
        }

        $result["records"] = $items;
        echo json_encode($result);

    }//end getBrandData()


    /**
     * 获得Mode数据
     *
     * @return void
     */
    public function getModeData()
    {
        $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 20;
        $offset = (($page - 1) * $rows);
        $limit  = " limit $offset,$rows";
        $city   = input::get("city");
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('CDR', $city);

        $result = array();
        $sql    = "select modelName, count(*) as users from userInfo group by modelName";
        $res    = $db->query($sql);
        if (!$res) {
            $this->getModeData();
            return;
        }

        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $result["total"] = count($row);

        $sql   = "select modelName, count(*) as users from userInfo group by modelName ORDER BY users desc".$limit;
        $rs    = $db->query($sql);
        $items = array();
        if (!$rs) {
            $this->getModeData();
            return;
        }

        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        $i   = (1 + $offset);
        foreach ($row as $r) {
            $r["rank"] = $i++;
            array_push($items, $r);
        }

        $result["records"] = $items;
        echo json_encode($result);

    }//end getModeData()


    /**
     * 导出品牌分析数据
     *
     * @return void
     */
    public function getAllBrandData()
    {
        $city = input::get("city");
        $dbc  = new DataBaseConnection();
        $db   = $dbc->getDB('CDR', $city);

        $sql   = "select brandName, count(*) as users from userInfo group by brandName ORDER BY users desc";
        $res   = $db->query($sql);
        $row   = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) {
            array_push($items, $qr);
        }

        $result           = array();
        $result["text"]   = "brandName,users";
        $result['rows']   = $items;
        $result['total']  = count($items);
        $result['result'] = 'true';

        $filename = "common/files/市场分析_品牌排名_".date('YmdHis').".csv";
        $this->resultToCSV2($result, $filename);
        $result['filename'] = $filename;
        if (count($items) > 1000) {
            $result['rows'] = array_slice($items, 0, 1000);
        }

        echo json_encode($result);

    }//end getAllBrandData()


    /**
     * 写入CSV文件
     *
     * @param array  $result   查询结果
     * @param string $filename CSV文件名
     *
     * @return void
     */
    protected function resultToCSV2($result, $filename)
    {
        $csvContent = mb_convert_encoding($result['text']."\n", 'GBK', "UTF-8");
        $fp         = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result['rows'] as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

    }//end resultToCSV2()


    /**
     * 导出Mod数据
     *
     * @return void
     */
    public function getAllModeData()
    {
        $city = input::get("city");
        $dbc  = new DataBaseConnection();
        $db   = $dbc->getDB('CDR', $city);

        $sql   = "select modelName, count(*) as users from userInfo group by modelName ORDER BY users desc";
        $res   = $db->query($sql);
        $row   = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) {
            array_push($items, $qr);
        }

        $result           = array();
        $result["text"]   = "modeName,users";
        $result['rows']   = $items;
        $result['total']  = count($items);
        $result['result'] = 'true';

        $filename = "common/files/市场分析_型号排名_".date('YmdHis').".csv";
        $this->resultToCSV2($result, $filename);
        $result['filename'] = $filename;
        if (count($items) > 1000) {
            $result['rows'] = array_slice($items, 0, 1000);
        }

        echo json_encode($result);

    }//end getAllModeData()


    /**
     * 生成品牌数据图表
     *
     * @return void
     */
    public function getBrandChartData()
    {
        $limit = " limit 20";
        $city  = input::get("city");
        $dbc   = new DataBaseConnection();
        $db    = $dbc->getDB('CDR', $city);

        $result = array();
        $sql    = "select brandName, count(*) as users from userInfo group by brandName ORDER BY users desc".$limit;
        $rs     = $db->query($sql);
        if (!$rs) {
            $this->getBrandChartData();
            return;
        }

        $categories = array();
        $series     = array();
        $row        = $rs->fetchAll(PDO::FETCH_NUM);
        foreach ($row as $r) {
            if (!$r[0]) {
                $r[0] = ' ';
            }

            array_push($categories, $r[0]);
            array_push($series, intval($r[1]));
        }

        $result["categories"] = $categories;
        $result["series"]     = $series;
        echo json_encode($result);

    }//end getBrandChartData()


    /**
     * 生成Mod图表
     *
     * @return void
     */
    public function getModeChartData()
    {
        $limit = " limit 20";
        $city  = input::get("city");
        $dbc   = new DataBaseConnection();
        $db    = $dbc->getDB('CDR', $city);

        $result = array();
        $sql    = "select modelName, count(*) as users from userInfo group by modelName ORDER BY users desc".$limit;
        $rs     = $db->query($sql);
        if (!$rs) {
            $this->getModeChartData();
            return;
        }

        $categories = array();
        $series     = array();
        $row        = $rs->fetchAll(PDO::FETCH_NUM);
        foreach ($row as $r) {
            if (!$r[0]) {
                $r[0] = ' ';
            }

            array_push($categories, $r[0]);
            array_push($series, intval($r[1]));
        }

        $result["categories"] = $categories;
        $result["series"]     = $series;
        echo json_encode($result);

    }//end getModeChartData()


}//end class
