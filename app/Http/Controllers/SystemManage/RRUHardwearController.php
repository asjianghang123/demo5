<?php

/**
 * RRUHardwearController.php
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\SystemManage;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\FileUtil;
use App\Http\Controllers\Common\MyRedis;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use Cache;
use App\Models\Mongs\RRUHardwearInfo;
use App\Models\Mongs\SiteLte;

/**
 * RRU硬件信息
 * Class SiteController
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class RRUHardwearController extends MyRedis
{
    public function getDate()
    {
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('mongs', 'mongs');
        $table  = 'ru_hardware_info';
        $sql   = "select distinct date_id from $table order by date_id";
        $this->type = 'mongs:ru_hardware_info';
        return json_encode($this->getValue($db, $sql));
    }

    public function getSubnetwork()
    {
        return SiteLte::distinct('subNetwork')->where('subNetwork','!=','')->whereNotNull('subNetwork')->get(['subNetwork'])->sortBy('subNetwork')->toArray();
    }

    public function searchData()
    {
        $date_id = input::get("date");
        $rulogicalid = input::get("rulogicalid");
        $ruserialno = input::get("ruserialno");
        $productName = input::get("productName");
        $serialNumber = input::get("serialNumber");
        $ecgi = input::get("ecgi");
        $cell_2g = input::get("cell_2g");
        $bsc = input::get("bsc");
        $subNetwork = input::get("subNetwork");
        $rows = input::get("limit") ? input::get("limit") : 10;

        $conn = new RRUHardwearInfo;
        if ($date_id) {
            $conn = $conn->where('date_id', $date_id);
        }
        if ($rulogicalid) {
            $conn = $conn->where('rulogicalid', 'like', "%".$rulogicalid."%");
        }
        if ($ruserialno) {
            $conn = $conn->where('ruserialno', 'like', "%".$ruserialno."%");
        }
        if ($productName) {
            $conn = $conn->where('productName', 'like', "%".$productName."%");
        }
        if ($serialNumber) {
            $conn = $conn->where('serialNumber', 'like', "%".$serialNumber."%");
        }
        if ($ecgi) {
            $conn = $conn->where('ecgi', 'like', "%".$ecgi."%");
        }
        if ($cell_2g) {
            $conn = $conn->where('cell_2g', 'like', "%".$cell_2g."%");
        }
        if ($bsc) {
            $conn = $conn->where('bsc', 'like', "%".$bsc."%");
        }
        if ($subNetwork) {
            $conn = $conn->whereIn('subNetwork', $subNetwork);
        }
        $row = $conn->paginate($rows)
                    ->toArray();

        $result["total"] = $row['total'];
        $result['rows'] = $row['data'];
        return json_encode($result);
    }

    public function downloadFile()
    {
        $date_id = input::get("date");
        $rulogicalid = input::get("rulogicalid");
        $ruserialno = input::get("ruserialno");
        $productName = input::get("productName");
        $serialNumber = input::get("serialNumber");
        $ecgi = input::get("ecgi");
        $cell_2g = input::get("cell_2g");
        $bsc = input::get("bsc");
        $subNetwork = input::get("subNetwork");

        $fileName = "common/files/RRU硬件信息_" . date('YmdHis') . ".csv";
        $columns = array_keys(RRUHardwearInfo::first()->toArray());

        $conn = new RRUHardwearInfo;
        if ($date_id) {
            $conn = $conn->where('date_id', $date_id);
        }
        if ($rulogicalid) {
            $conn = $conn->where('rulogicalid', 'like', "%".$rulogicalid."%");
        }
        if ($ruserialno) {
            $conn = $conn->where('ruserialno', 'like', "%".$ruserialno."%");
        }
        if ($productName) {
            $conn = $conn->where('productName', 'like', "%".$productName."%");
        }
        if ($serialNumber) {
            $conn = $conn->where('serialNumber', 'like', "%".$serialNumber."%");
        }
        if ($ecgi) {
            $conn = $conn->where('ecgi', 'like', "%".$ecgi."%");
        }
        if ($cell_2g) {
            $conn = $conn->where('cell_2g', 'like', "%".$cell_2g."%");
        }
        if ($bsc) {
            $conn = $conn->where('bsc', 'like', "%".$bsc."%");
        }
        if ($subNetwork) {
            $conn = $conn->whereIn('subNetwork', $subNetwork);
        }
        $items = $conn->get($columns)->toArray();

        $fileUtil = new FileUtil();
        $fileUtil->resultToCSV2(implode(",", $columns) , $items, $fileName);
        $result['fileName'] = $fileName;
        $result['result'] = 'true';
        return $result;
    }
}