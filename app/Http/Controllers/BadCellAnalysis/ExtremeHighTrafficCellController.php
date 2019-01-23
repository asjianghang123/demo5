<?php

/**
 * HighInterferenceCellController.php
 *
 * @category BadCellAnalysis
 * @package  App\Http\Controllers\BadCellAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\BadCellAnalysis;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Utils\FileUtil;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use PDO;
use Config;
use App\Models\Mongs\Task;
use App\Models\Kget\EUtranCellTDD_ETH;
use App\Models\Kget\TempParaCheckBaselineCheck_ETH;
use App\Models\Kget\TempSystemConstantsCheck_ETH;

/**
 * 高干扰小区分析
 * Class HighInterferenceCellController
 *
 * @category BadCellAnalysis
 * @package  App\Http\Controllers\BadCellAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class ExtremeHighTrafficCellController extends Controller
{

    /**
     * 获取日期（数据库名称）
     *
     * @return string
     */
    public function getTasks()
    {
        $items = array();
        $user = Auth::user();//获取登录用户信息
        $userName = $user->user;//需要获取当前登录用户！！！！！
        $conn = Task::where('type', 'parameter')
                    ->where('status', 'complete')
                    ->where('taskName', 'like', 'kget______');
        if ($user != 'admin') {
            $conn = $conn->whereIn('owner', [$userName,'admin']);
        } 
        $tasks = $conn->orderBy('taskName', 'desc')->get()->toArray();         

        foreach ($tasks as $task) {
            $items[] = ["text"=>$task['taskName'],"id"=>$task['taskName']];
        }
        return response()->json($items);//需要通过response返回响应数据
    }
    /**
     * 获得城市名列表
     *
     * @return string 城市名列表
     */
    public function getCitys()
    {
        $dbc = new DataBaseConnection();
        return $dbc->getCityOptions();

    }//end getCitys()

    /**
     * 获得列名集合
     *
     * @return array
     */
    public function getTableField()
    {
        $dbc = new DataBaseConnection();
        $task = Input::get("task");
        $table = Input::get('table');
        $cell = Input::get('cell');
        $result = array();
        if ($dbc->tableIfExists($task, $table)) {
            $conn = $this->switchConnection($task, $table);
            if ($conn->exists()) {
                return $conn->first()->toArray();
            } else {
                $result['result'] = 'error';
            }
        } else {
            $result['result'] = 'error';
        }
        return $result;
    }

    /**
     * 获得单高干扰小区分页列表
     *
     * @return void
     */
    public function getCellData()
    {
        $dbc = new DataBaseConnection();
        $task = Input::get("task");
        $page = Input::get('page');
        $limit = Input::get('limit');
        $citys = Input::get("citys");
        $table = Input::get('table');
        $cell = Input::get('cell');

        $subNetwork = '';
        $subNetworkArr = [];
        $subNetworkIsNull = '';
        if ($citys != '') {
            foreach ($citys as $city) {
                if ($city == 'unknow') {
                    $subNetworkIsNull = 'null';
                } else {
                    $subNetwork .= $dbc->getSubNets($city).',';
                }
            }
            $subNetwork = substr($subNetwork, 0, -1);
            $subNetwork = str_replace('\'', '', $subNetwork);
            $subNetworkArr = explode(',', $subNetwork);
        }
        $conn = $this->switchConnection($task, $table);

        if ($subNetwork) {
            if ($subNetworkIsNull == 'null') {
                $conn = $conn->where(function($query) use($subNetworkArr) {
                    $query->whereNull('subNetwork')
                        ->orWhere('subNetwork', '')
                        ->orWhere('subNetwork', 'null')
                        ->orWhereIn('subNetwork', $subNetworkArr);
                });
            } else {
                $conn = $conn->whereIn('subNetwork', $subNetworkArr);
            }
        } else {
            if ($subNetworkIsNull == 'null') {
                $conn = $conn->where(function($query) {
                    $query->whereNull('subNetwork')
                        ->orWhere('subNetwork', '')
                        ->orWhere('subNetwork', 'null');
                });
            }
        }
        if ($table == 'TempParaCheckBaselineCheck_ETH') {
            if ($cell) {
                $conn = $conn->where('cellId', $cell);
            }
        }else{
            if ($cell) {
                $conn = $conn->where('EUtranCellTDDId', $cell);
            }
        }
        $rows = $conn->paginate($limit)->toArray();

        $result = array();
        $result["total"] = $rows['total'];
        $result['records'] = $rows['data'];

        return $result;

    }//end getCellData()


    /**
     * 获得全部高干扰小区列表
     *
     * @return void
     */
    public function getAllCellData()
    {
        $dbc = new DataBaseConnection();
        $task = Input::get("task");
        $citys = Input::get("citys");
        $table = Input::get('table');
        $cell = Input::get('cell');
        $type = Input::get('type');
        $subNetwork = '';
        $subNetworkArr = [];
        $subNetworkIsNull = '';
        if ($dbc->tableIfExists($task, $table)) {
            if ($citys != '') {
                foreach ($citys as $city) {
                    if ($city == 'unknow') {
                        $subNetworkIsNull = 'null';
                    } else {
                        $subNetwork .= $dbc->getSubNets($city).',';
                    }
                }
                $subNetwork = substr($subNetwork, 0, -1);
                $subNetwork = str_replace('\'', '', $subNetwork);
                $subNetworkArr = explode(',', $subNetwork);
            }
            $conn = $this->switchConnection($task, $table);

            if ($subNetwork) {
                if ($subNetworkIsNull == 'null') {
                    $conn = $conn->where(function($query) use($subNetworkArr) {
                        $query->whereNull('subNetwork')
                            ->orWhere('subNetwork', '')
                            ->orWhere('subNetwork', 'null')
                            ->orWhereIn('subNetwork', $subNetworkArr);
                    });
                } else {
                    $conn = $conn->whereIn('subNetwork', $subNetworkArr);
                }
            } else {
                if ($subNetworkIsNull == 'null') {
                    $conn = $conn->where(function($query) {
                        $query->whereNull('subNetwork')
                            ->orWhere('subNetwork', '')
                            ->orWhere('subNetwork', 'null');
                    });
                }
            }
            if ($table == 'TempParaCheckBaselineCheck_ETH') {
                if ($cell) {
                    $conn = $conn->where('cellId', $cell);
                }
            } else {
                if ($cell) {
                    $conn = $conn->where('EUtranCellTDDId', $cell);
                }
            }
            $rows = $conn->get()->toArray();
            if (count($rows) > 0) {
                $fileName = "files/" . $type . "_" . date('YmdHis') . ".csv";
                $fp = fopen($fileName, "w");
                $column = implode(",", array_keys($rows[0]));
                $csvContent = mb_convert_encoding($column . "\n", 'GBK');
                fwrite($fp, $csvContent);
                foreach ($rows as $row) {
                    $newRow = array();
                    foreach ($row as $key => $value) {
                        $newRow[$key] = mb_convert_encoding($value, 'GBK');
                    }
                    fputcsv($fp, $newRow);
                }
                fclose($fp);
                $result['fileName'] = $fileName;
                $result['result'] = true;
            } else {
                $result['result'] = false;
            }
        } else {
            $result['result'] = false;
        }
        return $result;

    }//end getAllCellData()

    public function switchConnection($task, $table)
    {
        Config::set("database.connections.kget.database", $task);
        switch($table) {
        case 'EUtranCellTDD_ETH' :
            $conn = new EUtranCellTDD_ETH;
            break;
        case 'TempParaCheckBaselineCheck_ETH' :
            $conn = new TempParaCheckBaselineCheck_ETH;
            break;
        case 'TempSystemConstantsCheck_ETH' :
            $conn = new TempSystemConstantsCheck_ETH;
            break;
        }
        return $conn;
    }

}//end class
