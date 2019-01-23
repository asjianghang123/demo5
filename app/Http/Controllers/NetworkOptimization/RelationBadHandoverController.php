<?php

/**
 * RelationBadHandoverController.php
 *
 * @category NetworkOptimization
 * @package  App\Http\Controllers\NetworkOptimization
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\NetworkOptimization;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Common\MyRedis;
use PDO;
use App\Models\Mongs\Databaseconns;
use App\Models\AutoKPI\RelationBadHandover;

/**
 * 切换差分析
 * Class RelationBadHandoverController
 *
 * @category NetworkOptimization
 * @package  App\Http\Controllers\NetworkOptimization
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class RelationBadHandoverController extends MyRedis
{


    /**
     * 获得城市列表
     *
     * @return void
     */
    public function getCitys()
    {
        $row = Databaseconns::orderBy('cityChinese', 'asc')->get()->toArray();
        $items = array();
        foreach ($row as $qr) {
            if (!is_numeric(substr($qr['connName'], -1, 1))) {
                array_push($items, ["value" => $qr['connName'], "label" => $qr['cityChinese']]);
            }
        }

        echo json_encode($items);

    }//end getCitys()


    /**
     * 获得数据表头
     *
     * @return mixed
     */
    public function getDataHeader()
    {
        $conn = new RelationBadHandover;
        $text = $conn->getVisible();
        $result         = array();
        $result['text'] = implode(",", $text);
        return json_encode($result);

    }//end getDataHeader()


    /**
     * 获得分析结果(分页)
     *
     * @return void
     */
    public function getTableData()
    {
        $date    = input::get('date');
        $city = Input::get('city');

        $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;

        $row = RelationBadHandover::where('city', $city)->where('day_to', $date)->paginate($limit)->toArray();
        $result = array();
        $result["total"] = $row['total'];
        $result['records'] = $row['data'];
        return json_encode($result);

    }//end getTableData()


    /**
     * 导出全量检查结果
     *
     * @return mixed
     */
    public function getAllTableData()
    {
        $date = input::get("date");
        $city = Input::get('city');

        $conn = new RelationBadHandover;
        $text = $conn->getVisible();

        $result         = array();
        $result['text'] = implode(",", $text);

        $rows = $conn->where('city', $city)->where('day_to', $date)->get()->toArray();
        $result['rows']   = $rows;
        $result['total']  = count($rows);
        $result['result'] = 'true';

        $filename = "common/files/RelationBadHandover".$city."_".date('YmdHis').".csv";
        $this->resultToCSV2($result, $filename);
        $result['filename'] = $filename;
        $result['rows']   = null;
        return json_encode($result);

    }//end getAllTableData()


    /**
     * 写入CSV文件
     *
     * @param array  $result   检查结果
     * @param string $filename CSV文件名
     *
     * @return void
     */
    protected function resultToCSV2($result, $filename)
    {
        $csvContent = mb_convert_encoding($result['text']."\n", 'GBK');
        $fp         = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result['rows'] as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

    }//end resultToCSV2()


    /**
     * 获得日期列表
     *
     * @return array 日期列表
     */
    function getAllDate()
    {
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('autokpi', 'AutoKPI');
        $table  = 'RelationBadHandover';
        $sql    = "select distinct day_to from $table";
        $this->type = 'AutoKPI:relationBadHandover';
        return $this->getValue($db, $sql);
    }//end getAllDate()


}//end class
