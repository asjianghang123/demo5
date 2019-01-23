<?php

/**
 * AccessController.php
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
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use App\Models\Mongs\AccessDetail;

/**
 * 访问流量统计
 * Class AccessController
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class AccessController extends Controller
{
    /**
     * 获得用户记录
     *
     * @return mixed
     */
    public function getAllUsers()
    {
        $databaseConns = AccessDetail::query()->selectRaw('user')->groupBy('user')->orderBy('user', 'asc')->get()->toArray();
        return response()->json($databaseConns);
    }


    /**
     * 获得访问记录
     *
     * @return mixed
     */
    public function getAccessData()
    {
        $startDate = Input::get("startDate");
        $endDate = Input::get("endDate");
        $users = Input::get("users");
        $rows = AccessDetail::query()->selectRaw('url,urlChinese,count(*) as sum')
            ->where('date_id', '>=', $startDate)
            ->where('date_id', '<=', $endDate)
            ->whereIn('user', $users)
            ->groupBy('url')
            ->orderBy('sum', 'desc')
            ->get()
            ->toArray();
        $result['records'] = $rows;
        return json_encode($result);

    }//end getAccessData()


    /**
     * 下载访问记录
     *
     * @return mixed
     */
    public function downloadAccessData()
    {
        $startDate = input::get("startDate");
        $endDate = input::get("endDate");
        $users = input::get("users");
        $result['text'] = "url,urlChinese,sum";
        $accessDetail = [];
        $accessDetail = AccessDetail::query()->selectRaw('url,urlChinese,count(*) as sum')
            ->where('date_id', '>=', $startDate)
            ->where('date_id', '<=', $endDate)
            ->whereIn('user', $users)
            ->groupBy('url')
            ->orderBy('sum', 'desc')
            ->get()
            ->toArray();

        $count = count($accessDetail);
        if ($count == 0) {
            $result['error'] = 'error';
            return json_encode($result);
        }
        $result['rows'] = $accessDetail;
        $result['total'] = $count;
        $result['result'] = 'true';
        if ($startDate == $endDate) {
            $date = $startDate;
        } else {
            $date = $startDate . "-" . $endDate;
        }

        $filename = "common/files/点击管理_" . $date . ".csv";
        $this->resultToCSV2($result, $filename);
        $result['filename'] = $filename;
        $result['rows'] = [];
        return json_encode($result);

    }//end downloadAccessData()


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


}//end class
