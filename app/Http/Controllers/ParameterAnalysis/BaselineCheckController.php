<?php

/**
 * BaselineCheckController.php
 *
 * @category ParameterAnalysis
 * @package  App\Http\Controllers\ParameterAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\ParameterAnalysis;

use App\DatabaseConn;
use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\FileUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use PDO;
use Config;
use App\Models\Mongs\TemplateParaBaseline;
use App\Models\Mongs\Task;
use App\Models\Kget\ParaCheckBaseline;
use App\Models\TABLES;

/**
 * Baseline检查
 * Class BaselineCheckController
 *
 * @category ParameterAnalysis
 * @package  App\Http\Controllers\ParameterAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class BaselineCheckController extends Controller
{
    /**
     * 获取base检查的基本数据
     *
     * @return string 检查category(JSON)
     */
    public function getBaseTree()
    {
        $users = TemplateParaBaseline::distinct('user')->orderBy('user', 'asc')->get(['user']);
        $arrUser = array();
        $items = array();
        $itArr = array();
        foreach ($users as $user) {
            $userStr = $user->user;
            $templateNames = TemplateParaBaseline::where('user', $userStr)->get();
            foreach ($templateNames as $templateName) {
                array_push($arrUser, array("text" => $templateName->templateName, "id" => $templateName->id));
            }
            $items["text"] = $userStr;
            $items["nodes"] = $arrUser;
            $arrUser = array();
            array_push($itArr, $items);
        }
        return response()->json($itArr);
    }

    /**
     * 获取日期（数据库名称）
     *
     * @return string 日期列表
     */
    public function getParamTasks()
    {

        $tasks = TABLES::select('TABLE_SCHEMA')->where('TABLE_NAME', 'ParaCheckBaseline')->where('TABLE_SCHEMA', 'like', 'kget______')->orderBy('TABLE_SCHEMA', 'desc')->get()->toArray();
        $items = array();
        foreach ($tasks as $task) {
            $items[] = array("text"=>$task['TABLE_SCHEMA'], "id"=>$task['TABLE_SCHEMA']);
        }
        return json_encode($items);//需要通过response返回响应数据

    }

    /**
     * 获得城市列表
     *
     * @return string 获得城市列表
     */
    public function getParamCitys()
    {
        $dbc = new DataBaseConnection();
        return $dbc->getCityOptions();
    }

    /**
     * 获得城市列表
     *
     * @return string 城市列表(JSON)
     */
    public function getAllCity()
    {
        $cityClass = new DataBaseConnection();
        return $cityClass->getCityOptions();
    }

    /**
     * 获得列名集合
     *
     * @return mixed 列名集合
     */
    public function getTableField()
    {

        $dbname = Input::get('db');
        Config::set("database.connections.kget.database", $dbname);
        return ParaCheckBaseline::first()->toArray();
    }


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
     * 获取数据信息
     *
     * @return array
     */
    public function getChartDataCategory()
    {
        $dbc = new DataBaseConnection();
        $dbname = Input::get('db');
        $dbname=$this->check_input($dbname);
        Config::set("database.connections.kget.database", $dbname);
        $templateId = Input::get('templateId');

        $rs = ParaCheckBaseline::selectRaw('distinct category')->where('category','!=','')->whereRaw("SUBSTRING(category,1,4) != '!!!!'")->where('templateId',$templateId)->get();
        $categories = $this->getHighChartCategory($rs);
        $res = $dbc->getCitySubNetCategories();
        $series = array();
        foreach ($res as $items) {
            $city = $items->connName;
            $subNetwork = $items->subNetwork;
            $subNetworkArr = explode(",", $subNetwork);
            $rs = ParaCheckBaseline::selectRaw('DISTINCT category ,count(*) as num')->where('templateId', $templateId)->whereIn('subNetwork', $subNetworkArr)->groupBy('category')->orderBy('category', 'asc')->get();

            $series = $this->getHighChartSeries($rs, $city, $series, $categories);
        }
        $data['category'] = $categories;
        $data['series'] = array();
        foreach ($series as $key => $value) {
            $data['series'][] = ['name' => $key, 'data' => $value];
        }
        return $data;
    }

    /**
     * 获取category值
     *
     * @param array $rs 检查结果
     *
     * @return array
     */
    public function getHighChartCategory($rs)
    {
        $categories = array();
        foreach ($rs as $item) {
            $category = $item->category;
            if (array_search($category, $categories) === false) {
                $categories[] = $category;
            }
        }
        return $categories;

    }

    /**
     * 获得时序数据
     *
     * @param array  $rs         检查结果
     * @param string $seriesKey  时序键
     * @param mixed  $series     时序数据
     * @param array  $categories 分类列表
     *
     * @return mixed 时序数据
     */
    public function getHighChartSeries($rs, $seriesKey, $series, $categories)
    {
        foreach ($categories as $category) {
            $flag = false;
            foreach ($rs as $item) {
                if ($category == $item->category) {
                    $series[$seriesKey][] = floatval($item->num);
                    $flag = true;
                    break;
                }
            }
            if (!$flag) {
                $series[$seriesKey][] = floatval(0);
            }
        }
        return $series;
    }

    /**
     * 获取表格信息
     *
     * @return array
     */
    function getParamItems()
    {
        $dbc = new DataBaseConnection();
        $dbname = Input::get('db');
        Config::set("database.connections.kget.database", $dbname);
        $citys = Input::get('citys');
        $flag = Input::get('flag');
        $templateId = Input::get('templateId');
        $meContexts = Input::get('meContexts');

        if ($flag == 'file') {
            $meContextsString = str_replace("'", "", $meContexts);
            $meContextsArr = explode(",", $meContextsString);
        } else if ($flag == 'text') {
            $meContextsArr = explode(",", $meContexts);
        }
        $page = Input::get('page');
        $limit = Input::get('limit');
        $subNetwork = '';
        $subNetworkArr = [];
        if ($citys) {
            foreach ($citys as $city) {
                $subNetwork .= $dbc->getSubNets($city) . ',';
            }
            $subNetwork = substr($subNetwork, 0, -1);
            $subNetwork = str_replace("'", "", $subNetwork);
            $subNetworkArr = explode(",", $subNetwork);
        }
        $conn = new ParaCheckBaseline;
        if ($templateId) {
            if (trim($templateId) != '') {
                if ($subNetwork) {
                    $conn = $conn->where('templateId', $templateId)->whereIn('subNetwork', $subNetworkArr);
                } else {
                    $conn = $conn->where('templateId', $templateId);
                }
            } else {
                if ($subNetwork) {
                    $conn = $conn->wwhereIn('subNetwork', $subNetworkArr);
                }
            }
        } else {
            if ($subNetwork) {
                $conn = $conn->wwhereIn('subNetwork', $subNetworkArr);
            }
        }
        if ($meContexts) {
            $conn = $conn->whereIn('meContext', $meContextsArr);
        }
        $rows = $conn->paginate($limit)->toArray();
        $result = array();
        $result["total"] = $rows['total'];
        $result["records"] = $rows['data'];
        return $result;
    }

    /**
     * 导出检查结果
     * 
     * @return array 导出结果
     */
    function baselineFile()
    {
        $dbc = new DataBaseConnection();
        $dbname = Input::get('db');
        Config::set("database.connections.kget.database", $dbname);
        $citys = Input::get('citys');
        $templateId = Input::get('templateId');
        $flag = Input::get('flag');
        $meContexts = Input::get('meContexts');

        if ($flag == 'file') {
            $meContextsString = str_replace("'", "", $meContexts);
            $meContextsArr = explode(",", $meContextsString);
        } else if ($flag == 'text') {
            $meContextsArr = explode(",", $meContexts);
        }
        $fileName = "files/ParaCheckBaseline_" . date('YmdHis') . ".csv";
        $subNetwork = '';
        $subNetworkArr = [];
        if ($citys) {
            foreach ($citys as $city) {
                $subNetwork .= $dbc->getSubNets($city) . ',';
            }
            $subNetwork = substr($subNetwork, 0, -1);
            $subNetwork = str_replace("'", "", $subNetwork);
            $subNetworkArr = explode(",", $subNetwork);
        }

        $conn = new ParaCheckBaseline;
        if ($templateId) {
            if (trim($templateId) != '') {
                if ($subNetwork) {
                    $conn = $conn->where('templateId', $templateId)->whereIn('subNetwork', $subNetworkArr);
                } else {
                    $conn = $conn->where('templateId', $templateId);
                }
            } else {
                if ($subNetwork) {
                    $conn = $conn->wwhereIn('subNetwork', $subNetworkArr);
                }
            }
        } else {
            if ($subNetwork) {
                $conn = $conn->wwhereIn('subNetwork', $subNetworkArr);
            }
        }
        if ($meContexts) {
            $conn = $conn->whereIn('meContext', $meContextsArr);
        }
        $rows = $conn->get()->toArray();
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
        $result = array();
        $result['fileName'] = $fileName;
        $result['result'] = true;

        return $result;
    }
    /**
     * 获取文件内容入库
     *
     * @return string 入库结果
     */
    public function getFileContent()
    {
        $fileName = Input::get('fileName');
        $fileUtil = new FileUtil();
        $result = $fileUtil->parseFile($fileName);
        $len_result = count($result);
        $data_values = '';
        if ($len_result == 0) {
            return $data_values;
        }
        for ($i = 1; $i < $len_result; $i++) {
            $MeContext = $result[$i][0];
            $data_values .= "'$MeContext',";
        }
        $data_values = substr($data_values, 0, -1);
        return $data_values;
    }
}
