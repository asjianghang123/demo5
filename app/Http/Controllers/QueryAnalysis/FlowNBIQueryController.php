<?php

/**
 * FlowNBIQueryController.php
 *
 * @category QueryAnalysis
 * @package  App\Http\Controllers\QueryAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\QueryAnalysis;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use App\Models\Mongs\KpiTempCommon;

/**
 * FlowNBI指标查询
 * Class FlowNBIQueryController
 *
 * @category QueryAnalysis
 * @package  App\Http\Controllers\QueryAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class FlowNBIQueryController extends GetTreeData
{


    /**
     * 获得NBI指标模板列表
     *
     * @return mixed
     */
    public function searchNBITreeData()
    {
        $inputData  = Input::get('inputData');
        $inputData  = "%".$inputData."%";
        $users      = DB::select('select distinct user from templateNbi');
        $arrUser    = array();
        $items      = array();
        $itArr      = array();
        $login_user = Auth::user()->user;
        if ($login_user === 'admin') {
            foreach ($users as $user) {
                $userStr       = $user->user;
                $templateNames = DB::table('templateNbi')->where('templateName', 'like', $inputData)->where('user', '=', $userStr)->get();
                foreach ($templateNames as $templateName) {
                    array_push($arrUser, array("text" => $templateName->templateName, "id" => $templateName->id));
                }

                $items["text"]  = $userStr;
                $items["nodes"] = $arrUser;
                $arrUser        = array();
                array_push($itArr, $items);
            }
        } else {
            foreach ($users as $user) {
                if ($user->user === 'system') {
                    continue;
                } else if ($user->user == "admin" || $user->user == $login_user) {
                    $userStr       = $user->user;
                    $templateNames = DB::table('templateNbi')->where('templateName', 'like', $inputData)->where('user', '=', $userStr)->get();
                    foreach ($templateNames as $templateName) {
                        array_push($arrUser, array("text" => $templateName->templateName, "id" => $templateName->id));
                    }

                    $items["text"]  = $userStr;
                    $items["nodes"] = $arrUser;
                    $arrUser        = array();
                    array_push($itArr, $items);
                }
            }
        }//end if
        for ($i = 0; $i < count($itArr); $i++) {
            $user = $itArr[$i]['text'];
            if ($user == "admin") {
                $itArr[$i]['text'] = "通用模板";
            } else if ($user == "system") {
                $itArr[$i]['text'] = "系统模板";
            } else {
                $nameCNSql         = DB::table('users')->where('user', '=', $user)->get();
                $itArr[$i]['text'] = $nameCNSql[0]->name;
            }
        }

        return response()->json($itArr);

    }//end searchNBITreeData()


    /**
     * 获得全量NBI指标模板信息列表
     *
     * @return string
     */
    public function getTreeData()
    {
        if (!Auth::user()) {
            echo "login";
            return;
        }

        $login_user = Auth::user()->user;
        $users      = DB::select('select distinct user from templateNbi');
        $arrUser    = array();
        $items      = array();
        $itArr      = array();
        if ($login_user === 'admin') {
            foreach ($users as $user) {
                $userStr       = $user->user;
                $templateNames = DB::table('templateNbi')->where('user', '=', $userStr)->get();
                foreach ($templateNames as $templateName) {
                    array_push($arrUser, array("text" => $templateName->templateName, "id" => $templateName->id));
                }

                $items["text"]  = $userStr;
                $items["nodes"] = $arrUser;
                $arrUser        = array();
                array_push($itArr, $items);
            }
        } else {
            foreach ($users as $user) {
                if ($user->user === 'system') {
                    continue;
                } else if ($user->user == "admin" || $user->user == $login_user) {
                    $userStr       = $user->user;
                    $templateNames = DB::table('templateNbi')->where('user', '=', $userStr)->get();
                    foreach ($templateNames as $templateName) {
                        array_push($arrUser, array("text" => $templateName->templateName, "id" => $templateName->id));
                    }

                    $items["text"]  = $userStr;
                    $items["nodes"] = $arrUser;
                    $arrUser        = array();
                    array_push($itArr, $items);
                }
            }
        }//end if

        for ($i = 0; $i < count($itArr); $i++) {
            $user = $itArr[$i]['text'];
            if ($user == "admin") {
                $itArr[$i]['text'] = "通用模板";
            } else if ($user == "system") {
                $itArr[$i]['text'] = "系统模板";
            } else {
                $nameCNSql         = DB::table('users')->where('user', '=', $user)->get();
                $itArr[$i]['text'] = $nameCNSql[0]->name;
            }
        }

        return response()->json($itArr);

    }//end getTreeData()


    /**
     * 指标模板查询
     *
     * @return void
     */
    public function templateQuery()
    {   
        // print_r(input::all());
        $locationDim  = Input::get('locationDim');
        $timeDim      = Input::get('timeDim');
        $startTime    = Input::get('startTime');
        $endTime      = Input::get('endTime');
        $city         = Input::get('city');
        $templateName = Input::get('template');
        $templateId   = Input::get('templateId');
        $erbs         = Input::get('erbs');
        $cell         = Input::get('cell');
        $minutes      = Input::get('minute');
        $hours        = Input::get('hour');

        $result         = [];
        $nbiQuery       = new NbiQuery();
        $nbiKpis        = $nbiQuery->getNbiKpis();

        $getItems       = new GetItems();
        $resultText     = $getItems->getResultText($locationDim, $timeDim);
        $result['text'] = $resultText.','.$nbiKpis['names'];
        $useKpis        = "";
        for ($i = 0; $i < count(explode(",", $nbiKpis['ids'])); $i++) {
            $useKpis = $useKpis.'kpi'.$i.',';
        }

        if ($locationDim != "city" && $locationDim != "cellGroup") {
            if ($timeDim == 'day') {
                $resultIds = "day,city,location";
            } else if ($timeDim == 'hour') {
                $resultIds = "day,hour,city,location";
            } else if ($timeDim == 'quarter') {
                $resultIds = "day,hour,minute,city,location";
            } else if ($timeDim = 'hourgroup') {
                $resultIds = "day,hour,city,location";
            }
        } else if ($locationDim == "cellGroup") {
            if ($timeDim == 'day') {
                $resultIds = "day,city,location";
            } else if ($timeDim == 'hour') {
                $resultIds = "day,hour,city,location";
            } else if ($timeDim == 'quarter') {
                $resultIds = "day,hour,minute,city,location";
            } else if ($timeDim = 'hourgroup') {
                $resultIds = "day,hour,location,city";
            }
        } else {
            if ($timeDim == 'day') {
                $resultIds = "day,location";
            } else if ($timeDim == 'hour') {
                $resultIds = "day,hour,location";
            } else if ($timeDim == 'quarter') {
                $resultIds = "day,hour,minute,location";
            } else if ($timeDim == 'hourgroup') {
                $resultIds = "day,hour,city";
            }
        }//end if
        // $result['text'] = substr($result['text'], 0, (strlen($result['text']) - 1));
        $filename       = "common/files/".$templateName.date('YmdHis').".csv";
        $filename       = preg_replace('/[\\(\\)]/', '-', $filename);
        $fp         = fopen($filename, "w");
        $csvContent = mb_convert_encoding($result['text']."\n", 'gb2312', 'utf-8');
        fwrite($fp, $csvContent);
        if (is_array(json_decode($hours))) {
            $hour = implode(',', json_decode($hours));
        } else {
            $hour = $hours;
        }

        if (is_array(json_decode($minutes))) {
            $minute = implode(',', json_decode($minutes));
        } else {
            $minute = $minutes;
        }

        $citys    = json_decode($city);
        $nbiQuery = new NbiQuery();
        $dbn      = new DataBaseConnection();

        // 筛选项：模版
        $nbiKpis          = $nbiQuery->getNbiKpis();
        $nbiFormulaFilter = $nbiQuery->parseNbiKpiTest($nbiKpis);
        // print_r($nbiFormulaFilter);
        $nbiFormulaFilterPreision = explode(',', $nbiKpis['kpiPrecision']);
        $select = '';
        $db     = '';
        $rows   = [];
        $i      = 0;
        $items  = [];
        $dbc    = new DataBaseConnection();
        foreach ($citys as $city) {
            if ($city == '无锡' || $city == '苏州' || $city == '南通' || $city == '镇江' || $city == '常州') {
                $selectEutranCellTdd = $nbiQuery->getSelectEutranCellTddNbm($locationDim, $timeDim, $nbiFormulaFilter, $hour, $minute, $cell);
                $ThisDate            = '';
                for ($i = strtotime($startTime); $i <= strtotime($endTime); $i += 86400) {
                    $ThisDate = $ThisDate.'\''.date("Y-m-d", $i).'\',';
                }

                $ThisDate = substr($ThisDate, 0, (strlen($ThisDate) - 1));
                // 筛选项：日期
                $selectDateId = $selectEutranCellTdd." where date_id in ($ThisDate)";
                // 筛选项：时间维度
                $selectTimeDim = $nbiQuery->getSelectTimeDimNbm($timeDim, $hour, $minute, $selectDateId);
                // 筛选项：区域维度
                $nbiLocation       = $nbiQuery->getLocationNbm($city);
                $selectNbiLocation = $selectTimeDim." and (city = '$nbiLocation')";
                // 筛选项：基站or小区
                $selectNbiLocations = $nbiQuery->getSelectNbiLocationNbm($selectNbiLocation, $locationDim, $erbs, $cell);
                $select = $nbiQuery->getSelectNbm($locationDim, $timeDim, $selectNbiLocations);
            } else {
                $selectEutranCellTdd = $nbiQuery->getSelectEutranCellTdd($locationDim, $timeDim, $nbiFormulaFilter, $hour, $minute, $cell);
                $ThisDate            = '';
                for ($i = strtotime($startTime); $i <= strtotime($endTime); $i += 86400) {
                    $ThisDate = $ThisDate.'\''.date("Y-m-d", $i).'\',';
                }

                $ThisDate = substr($ThisDate, 0, (strlen($ThisDate) - 1));
                // 筛选项：日期
                $selectDateId = $selectEutranCellTdd." where DateId in ($ThisDate)";

                // 筛选项：时间维度
                $selectTimeDim = $nbiQuery->getSelectTimeDim($timeDim, $hour, $minute, $selectDateId);

                // 筛选项：区域维度
                $nbiLocation       = $nbiQuery->getLocationNbm($city);
                $selectNbiLocation = $selectTimeDim." and (City = '$nbiLocation')";

                // 筛选项：基站or小区
                $selectNbiLocations = $nbiQuery->getSelectNbiLocation($selectNbiLocation, $locationDim, $erbs, $cell);
                $select = $nbiQuery->getSelect($locationDim, $timeDim, $selectNbiLocations);
            }//end if
            // print_r($select);return;
            $db = '';

            if ($city == '无锡' || $city == '苏州' || $city == '南通' || $city == '镇江' || $city == '常州' ) {
                $db = $dbc->getDB('NBI', 'nbm');
            } else {
                $db = $dbc->getDB('NBI', 'nbi');
            }
            // print_r($select);
            $query = $db->query($select);
            if ($query) {
                $i = 0;
                while ($row = $query->fetch(PDO::FETCH_NUM)) {
                    fputcsv($fp, $row);
                    if ($i < 500) {
                        if($timeDim == 'hour' || $timeDim == 'quarter')
                            $row[1] = intval($row[1]);
                        array_push($items, $row);
                    }

                    $i++;
                }
            }
        }//end foreach
        $result['total']  = count($items);
        $result['rows']   = $items;
        $result['result'] = 'true';
        fclose($fp);
        $result['filename'] = $filename;
        if (count($items) > 1000) {
            $result['rows'] = array_slice($items, 0, 1000);
        }

        echo json_encode($result);

    }//end templateQuery()


    /**
     * 查询模板列名集合
     *
     * @return void
     */
    public function templateQueryHeader()
    {
        $locationDim    = Input::get('locationDim');
        $timeDim        = Input::get('timeDim');
        $nbiQuery       = new NbiQuery();
        $nbiKpis        = $nbiQuery->getNbiKpis();
        $getItems       = new GetItems();
        $resultText     = $getItems->getResultText($locationDim, $timeDim);
        $result['text'] = $resultText.','.$nbiKpis['names'];

        $useKpis = "";
        for ($i = 0; $i < count(explode(",", $nbiKpis['ids'])); $i++) {
            $useKpis = $useKpis.'kpi'.$i.',';
        }

        if ($locationDim != "city" && $locationDim != "cellGroup") {
            if ($timeDim == 'day') {
                $resultIds = "day,city,location,hourTotal";
            } else if ($timeDim == 'hour') {
                $resultIds = "day,hour,city,location";
            } else if ($timeDim == 'quarter') {
                $resultIds = "day,hour,minute,city,location";
            } else if ($timeDim = 'hourgroup') {
                $resultIds = "day,hour,city,location";
            }
        } else if ($locationDim == "cellGroup") {
            if ($timeDim == 'day') {
                $resultIds = "day,city,location,hourTotal";
            } else if ($timeDim == 'hour') {
                $resultIds = "day,hour,city,location";
            } else if ($timeDim == 'quarter') {
                $resultIds = "day,hour,minute,city,location";
            } else if ($timeDim = 'hourgroup') {
                $resultIds = "day,hour,location,city";
            }
        } else {
            if ($timeDim == 'day') {
                $resultIds = "day,location,hourTotal";
            } else if ($timeDim == 'hour') {
                $resultIds = "day,hour,location";
            } else if ($timeDim == 'quarter') {
                $resultIds = "day,hour,minute,location";
            } else if ($timeDim == 'hourgroup') {
                $resultIds = "day,hour,city";
            }
        }//end if
        $result['ids'] = $resultIds.','.$useKpis;
        $result['ids'] = substr($result['ids'], 0, (strlen($result['ids']) - 1));

        $result['text'] = substr($result['text'], 0, (strlen($result['text']) - 1));
        echo json_encode($result);

    }//end templateQueryHeader()


    /**
     * 获得模板的指标列表
     *
     * @return void
     */
    public function getElementTree()
    {
        $templateName = input::get('templateName');
        $dbc          = new DataBaseConnection();
        $db           = $dbc->getDB('mongs', 'mongs');
        $sql          = "select elementId from templateNbi where id = '$templateName'";
        $resTem       = $db->query($sql);
        if ($resTem) {
            $elementId = $resTem->fetchAll(PDO::FETCH_ASSOC);
        }

        echo json_encode($elementId[0]);

    }//end getElementTree()


    /**
     * 获得指标名
     *
     * @return void
     */
    public function getKpiNamebyId()
    {
        $dbc   = new DataBaseConnection();
        $db    = $dbc->getDB('mongs', 'mongs');
        $table = "kpiFormulaNbi";

        $idarr = explode(',', input::get("id"));
        $items = array();
        foreach ($idarr as $id) {
            $sql = "select * from $table where id='$id'";
            $res = $db->query($sql);
            if ($row = $res->fetchAll(PDO::FETCH_ASSOC)) {
                $data['text']    = $row[0]['kpiName'];
                $data['id']      = $row[0]['id'];
                $data['user']    = $row[0]['user'];
                $data['formula'] = $row[0]['kpiFormula'];
                array_push($items, $data);
            }
        }

        echo json_encode($items);

    }//end getKpiNamebyId()


    /**
     * 获得日期(天)列表
     *
     * @return array
     */
    public function NBITime()
    {
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('mongs', 'nbm');
        $table  = 'EutranCellTdd_cell_day';
        $result = array();
        $sql    = "select distinct date_id from $table";
        $this->type = 'nbm'.':NBIQuery';
        return $this->getValue($db, $sql);
        // $rs     = $db->query($sql, PDO::FETCH_ASSOC);
        // $test   = [];
        // if ($rs) {
        //     $rows = $rs->fetchall();
        //     if (count($rows) > 0) {
        //         foreach ($rows as $row) {
        //             $arr = explode(' ', $row['date_id']);
        //             if ($arr[0] == '0000-00-00') {
        //                 continue;
        //             }

        //             array_push($test, $arr[0]);
        //         }

        //         return $test;
        //     } else {
        //         $result['error'] = 'error';
        //         return $result;
        //     }
        // } else {
        //     $result['error'] = 'error';
        //     return $result;
        // }//end if

    }//end NBITime()


}//end class


/**
 * Class GetItems
 *
 * @category QueryAnalysis
 * @package  App\Http\Controllers\QueryAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class GetItems
{


    /**
     * 写入文件
     *
     * @param array  $result   结果集
     * @param string $filename CSV文件名
     *
     * @return void
     */
    public function resultToCSV2($result, $filename)
    {
        $csvContent = mb_convert_encoding($result['text']."\n", 'gb2312', 'utf-8');
        $fp         = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result['rows'] as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

    }//end resultToCSV2()


    /**
     * 计算相隔天数
     *
     * @param string $day1 DAY1
     * @param string $day2 DAY2
     *
     * @return float
     */
    public function diffBetweenTwoDays($day1, $day2)
    {
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);

        if ($second1 < $second2) {
            $tmp     = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }

        return (($second1 - $second2) / 86400);

    }//end diffBetweenTwoDays()


    /**
     * 获得查询结果表头
     *
     * @param string $locationDim 地域维度
     * @param string $timeDim     时间维度
     *
     * @return string 查询结果表头
     */
    public function getResultText($locationDim, $timeDim)
    {
        
        if ($locationDim != 'city') {
            $types = $locationDim . "Total";
        } else {
            $types = "cellTotal";
        }
        if ($locationDim != "city" && $locationDim != "cellGroup") {
            if ($timeDim == 'day') {
                return "day,city,location,$types,hourTotal";
            } else if ($timeDim == 'hour') {
                return "day,hour,city,location,$types";
            } else if ($timeDim == 'quarter') {
                return "day,hour,minute,city,location,$types";
            } else if ($timeDim = 'hourgroup') {
                return "day,hourgroup,city,location,$types";
            }
        } else if ($locationDim == "cellGroup") {
            if ($timeDim == 'day') {
                return "day,city,cellgroup,cellTotal,hourTotal";
            } else if ($timeDim == 'hour') {
                return "day,hour,city,cellgroup,cellTotal";
            } else if ($timeDim == 'quarter') {
                return "day,hour,minute,city,cellgroup,cellTotal";
            } else if ($timeDim = 'hourgroup') {
                return "day,hourgroup,cellgroup,city,cellTotal";
            }
        } else {
            if ($timeDim == 'day') {
                return "day,city,$types";
            } else if ($timeDim == 'hour' || $timeDim == 'hourgroup') {
                return "day,hour,city,$types";
            } else if ($timeDim == 'quarter') {
                return "day,hour,minute,city,$types";
            } else if ($timeDim = 'hourgroup') {
                return "day,hourgroup,city,location,$types";
            }
        }//end if

    }//end getResultText()


    /**
     * 写入查询结果
     *
     * @param string $locationDim              地域维度
     * @param string $timeDim                  时间维度
     * @param array  $rows                     原始数据
     * @param array  $nbiFormulaFilterPreision 指标公式精度
     *
     * @return array
     */
    public function getItems($locationDim, $timeDim, $rows, $nbiFormulaFilterPreision)
    {
        $items = array();
        if ($locationDim != "city" && $locationDim != "cellGroup") {
            if ($locationDim == 'day') {
                $types = $locationDim . "Total";
            } else {
                $types = 'cellTotal';
            }
            if ($timeDim == 'day') {
                for ($i = 0; $i < count($rows); $i++) {
                    $items[$i]['day']      = $rows[$i][0];
                    $items[$i]['city']     = $rows[$i][1];
                    $items[$i]['location'] = $rows[$i][2];
                    $items[$i][$types] = $rows[$i][3];
                    $items[$i]['hourTotal'] = $rows[$i][4];
                    $k    = 0;
                    $para = [];
                    for ($j = 5; $j < count($rows[$i]); $j++) {
                        $para[0] = $nbiFormulaFilterPreision[$k++];
                        $items[$i]['kpi'.($j - 5)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j - 5)] = round($items[$i]['kpi'.($j - 5)], $para[0]);
                    }
                }
            } else if ($timeDim == 'hour') {
                for ($i = 0; $i < count($rows); $i++) {
                    $items[$i]['day']      = $rows[$i][0];
                    $items[$i]['hour']     = $rows[$i][1];
                    $items[$i]['city']     = $rows[$i][2];
                    $items[$i]['location'] = $rows[$i][3];
                    $items[$i][$types] = $rows[$i][4];
                    $k    = 0;
                    $para = [];
                    for ($j = 5; $j < count($rows[$i]); $j++) {
                        $para[0] = $nbiFormulaFilterPreision[$k++];
                        $items[$i]['kpi'.($j - 5)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j - 5)] = round($items[$i]['kpi'.($j - 5)], $para[0]);
                    }
                }
            } else if ($timeDim == 'quarter') {
                for ($i = 0; $i < count($rows); $i++) {
                    $items[$i]['day']      = $rows[$i][0];
                    $items[$i]['hour']     = $rows[$i][1];
                    $items[$i]['minute']   = $rows[$i][2];
                    $items[$i]['city']     = $rows[$i][3];
                    $items[$i]['location'] = $rows[$i][4];
                    $items[$i][$types] = $rows[$i][5];
                    $k    = 0;
                    $para = [];
                    for ($j = 6; $j < count($rows[$i]); $j++) {
                        $para[0] = $nbiFormulaFilterPreision[$k++];
                        $items[$i]['kpi'.($j - 6)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j - 6)] = round($items[$i]['kpi'.($j - 6)], $para[0]);
                    }
                }
            } else if ($timeDim == 'hourgroup') {
                for ($i = 0; $i < count($rows); $i++) {
                    $items[$i]['day']      = $rows[$i][0];
                    $items[$i]['hour']     = $rows[$i][1];
                    $items[$i]['city']     = $rows[$i][2];
                    $items[$i]['location'] = $rows[$i][3];
                    $items[$i][$types] = $rows[$i][4];
                    $k    = 0;
                    $para = [];
                    for ($j = 5; $j < count($rows[$i]); $j++) {
                        $para[0] = $nbiFormulaFilterPreision[$k++];
                        $items[$i]['kpi'.($j - 5)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j - 5)] = round($items[$i]['kpi'.($j - 5)], $para[0]);
                    }
                }
            }//end if
        } else if ($locationDim == "cellGroup") {
            if ($locationDim == 'day') {
                $types = $locationDim . "Total";
            } else {
                $types = 'cellTotal';
            }
            if ($timeDim == 'day') {
                for ($i = 0; $i < count($rows); $i++) {
                    $items[$i]['day']      = $rows[$i][0];
                    $items[$i]['city']     = $rows[$i][1];
                    $items[$i]['location'] = $rows[$i][2];
                    $items[$i]['hourTotal'] = $rows[$i][3];
                    $items[$i][$types] = $rows[$i][4];
                    $k    = 0;
                    $para = [];
                    for ($j = 5; $j < count($rows[$i]); $j++) {
                        $para[0] = $nbiFormulaFilterPreision[$k++];
                        $items[$i]['kpi'.($j - 5)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j - 5)] = round($items[$i]['kpi'.($j - 5)], $para[0]);
                    }
                }
            } else if ($timeDim == 'hour') {
                for ($i = 0; $i < count($rows); $i++) {
                    $items[$i]['day']      = $rows[$i][0];
                    $items[$i]['hour']     = $rows[$i][1];
                    $items[$i]['city']     = $rows[$i][2];
                    $items[$i]['location'] = $rows[$i][3];
                    $items[$i][$types] = $rows[$i][4];
                    $k    = 0;
                    $para = [];
                    for ($j = 5; $j < count($rows[$i]); $j++) {
                        $para[0] = $nbiFormulaFilterPreision[$k++];
                        $items[$i]['kpi'.($j - 5)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j - 5)] = round($items[$i]['kpi'.($j - 5)], $para[0]);
                    }
                }
            } else if ($timeDim == 'quarter') {
                for ($i = 0; $i < count($rows); $i++) {
                    $items[$i]['day']      = $rows[$i][0]."                                                                                                         ";
                    $items[$i]['hour']     = $rows[$i][1];
                    $items[$i]['minute']   = $rows[$i][2];
                    $items[$i]['city']     = $rows[$i][3];
                    $items[$i]['location'] = $rows[$i][4];
                    $items[$i][$types] = $rows[$i][5];
                    $k    = 0;
                    $para = [];
                    for ($j = 6; $j < count($rows[$i]); $j++) {
                        $para[0] = $nbiFormulaFilterPreision[$k++];
                        $items[$i]['kpi'.($j - 6)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j - 6)] = round($items[$i]['kpi'.($j - 6)], $para[0]);
                    }
                }
            } else if ($timeDim == 'hourgroup') {
                for ($i = 0; $i < count($rows); $i++) {
                    $items[$i]['day']      = $rows[$i][0];
                    $items[$i]['hour']     = $rows[$i][1];
                    $items[$i]['city']     = $rows[$i][2];
                    $items[$i]['location'] = $rows[$i][3];
                    $items[$i][$types] = $rows[$i][4];
                    $k    = 0;
                    $para = [];
                    for ($j = 5; $j < count($rows[$i]); $j++) {
                        $para[0] = $nbiFormulaFilterPreision[$k++];
                        $items[$i]['kpi'.($j - 5)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j - 5)] = round($items[$i]['kpi'.($j - 5)], $para[0]);
                    }
                }
            }//end if
        } else {
            if ($locationDim == 'day') {
                $types = $locationDim . "Total";
            } else {
                $types = 'cellTotal';
            }
            
            if ($timeDim == 'day') {
                for ($i = 0; $i < count($rows); $i++) {
                    $items[$i]['day']      = $rows[$i][0];
                    $items[$i]['location'] = $rows[$i][1];
                    //$items[$i][$types] = $rows[$i][2];
                    $items[$i]['hourTotal'] = $rows[$i][2];
                    $k    = 0;
                    $para = [];
                    for ($j = 3; $j < count($rows[$i]); $j++) {
                        $para[0] = $nbiFormulaFilterPreision[$k++];
                        $items[$i]['kpi'.($j - 3)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j - 3)] = round($items[$i]['kpi'.($j - 3)], $para[0]);
                    }
                }
            } else if ($timeDim == 'hour') {
                for ($i = 0; $i < count($rows); $i++) {
                    $items[$i]['day']      = $rows[$i][0];
                    $items[$i]['hour']     = $rows[$i][1];
                    $items[$i]['location'] = $rows[$i][2];
                    $items[$i][$types] = $rows[$i][3];
                    $k    = 0;
                    $para = [];
                    for ($j = 4; $j < count($rows[$i]); $j++) {
                        $para[0] = $nbiFormulaFilterPreision[$k++];
                        $items[$i]['kpi'.($j - 4)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j - 4)] = round($items[$i]['kpi'.($j - 4)], $para[0]);
                    }
                }
            } else if ($timeDim == 'quarter') {
                for ($i = 0; $i < count($rows); $i++) {
                    $items[$i]['day']      = $rows[$i][0]."                                                                                                         ";
                    $items[$i]['hour']     = $rows[$i][1];
                    $items[$i]['minute']   = $rows[$i][2];
                    $items[$i]['location'] = $rows[$i][3];
                    $items[$i][$types] = $rows[$i][4];
                    $k    = 0;
                    $para = [];
                    for ($j = 5; $j < count($rows[$i]); $j++) {
                        $para[0] = $nbiFormulaFilterPreision[$k++];
                        $items[$i]['kpi'.($j - 5)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j - 5)] = round($items[$i]['kpi'.($j - 5)], $para[0]);
                    }
                }
            } else if ($timeDim == 'hourgroup') {
                for ($i = 0; $i < count($rows); $i++) {
                    $items[$i]['day']  = $rows[$i][0];
                    $items[$i]['hour'] = $rows[$i][1];
                    $items[$i]['city'] = $rows[$i][2];
                    $items[$i][$types] = $rows[$i][3];
                    $k    = 0;
                    $para = [];
                    for ($j = 4; $j < count($rows[$i]); $j++) {
                        $para[0] = $nbiFormulaFilterPreision[$k++];
                        $items[$i]['kpi'.($j - 4)] = $rows[$i][$j];
                        $items[$i]['kpi'.($j - 4)] = round($items[$i]['kpi'.($j - 4)], $para[0]);
                    }
                }
            }//end if
        }//end if
        return $items;

    }//end getItems()


}//end class


/**
 * Class nbiQuery
 *
 * @category QueryAnalysis
 * @package  App\Http\Controllers\QueryAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class NbiQuery
{


    /**
     * 生成SELECT字串(NBM)
     *
     * @param string $locationDim        地域维度
     * @param string $timeDim            时间维度
     * @param string $selectNbiLocations 初始SQL
     *
     * @return string SELECT字串(NBM)
     */
    public function getSelectNbm($locationDim, $timeDim, $selectNbiLocations)
    {
        if ($locationDim == "erbs") {
            if ($timeDim == "day") {
                $select = $selectNbiLocations."group by date_id,City,ManagedElement";
                return $select;
            } else if ($timeDim == "hour") {
                $select = $selectNbiLocations."group by date_id,hour_id,City,ManagedElement";
                return $select;
            } else if ($timeDim == "quarter") {
                $select = $selectNbiLocations."group by date_id,hour_id,min_id,City,ManagedElement";
                return $select;
            } else if ($timeDim == "hourgroup") {
                $select = $selectNbiLocations."group by date_id,City,ManagedElement";
                return $select;
            }
        } else if ($locationDim == "cell") {
            if ($timeDim == "day") {
                $select = $selectNbiLocations."group by date_id,City,EutranCellTdd";
                return $select;
            } else if ($timeDim == "hour") {
                $select = $selectNbiLocations."group by date_id,hour_id,City,EutranCellTdd";
                return $select;
            } else if ($timeDim == "quarter") {
                $select = $selectNbiLocations."group by date_id,hour_id,min_id,City,EutranCellTdd";
                return $select;
            } else if ($timeDim == "hourgroup") {
                $select = $selectNbiLocations."group by date_id,City,EutranCellTdd";
                return $select;
            }
        } else if ($locationDim == "cellGroup") {
            if ($timeDim == "day") {
                $select = $selectNbiLocations."group by date_id,City";
                return $select;
            } else if ($timeDim == "hour") {
                $select = $selectNbiLocations."group by date_id,hour_id,City";
                return $select;
            } else if ($timeDim == "quarter") {
                $select = $selectNbiLocations."group by date_id,hour_id,min_id,City";
                return $select;
            } else if ($timeDim == "hourgroup") {
                $select = $selectNbiLocations."group by date_id,City";
                return $select;
            }
        } else {
            if ($timeDim == "day") {
                $select = $selectNbiLocations."group by date_id,City";
                return $select;
            } else if ($timeDim == "hour") {
                $select = $selectNbiLocations."group by date_id,hour_id,City";
                return $select;
            } else if ($timeDim == "quarter") {
                $select = $selectNbiLocations."group by date_id,hour_id,min_id,City";
                return $select;
            } else if ($timeDim == "hourgroup") {
                $select = $selectNbiLocations."group by date_id,City";
                return $select;
            }
        }//end if

    }//end getSelectNbm()


    /**
     * 生成SELECT字串(NBI)
     *
     * @param string $locationDim        地域维度
     * @param string $timeDim            时间维度
     * @param string $selectNbiLocations 初始SQL
     *
     * @return string SELECT字串(NBI)
     */
    public function getSelect($locationDim, $timeDim, $selectNbiLocations)
    {
        if ($locationDim == "erbs") {
            if ($timeDim == "day") {
                $select = $selectNbiLocations."group by DateId,City,ManagedElement";
                return $select;
            } else if ($timeDim == "hour") {
                $select = $selectNbiLocations."group by DateId,HourId,City,ManagedElement";
                return $select;
            } else if ($timeDim == "quarter") {
                $select = $selectNbiLocations."group by DateId,HourId,MinId,City,ManagedElement";
                return $select;
            } else if ($timeDim == "hourgroup") {
                $select = $selectNbiLocations."group by DateId,City,ManagedElement";
                return $select;
            }
        } else if ($locationDim == "cell") {
            if ($timeDim == "day") {
                $select = $selectNbiLocations."group by DateId,City,EutranCellTdd";
                return $select;
            } else if ($timeDim == "hour") {
                $select = $selectNbiLocations."group by DateId,HourId,City,EutranCellTdd";
                return $select;
            } else if ($timeDim == "quarter") {
                $select = $selectNbiLocations."group by DateId,HourId,MinId,City,EutranCellTdd";
                return $select;
            } else if ($timeDim == "hourgroup") {
                $select = $selectNbiLocations."group by DateId,City,EutranCellTdd";
                return $select;
            }
        } else if ($locationDim == "cellGroup") {
            if ($timeDim == "day") {
                $select = $selectNbiLocations."group by DateId,City";
                return $select;
            } else if ($timeDim == "hour") {
                $select = $selectNbiLocations."group by DateId,HourId,City";
                return $select;
            } else if ($timeDim == "quarter") {
                $select = $selectNbiLocations."group by DateId,HourId,MinId,City";
                return $select;
            } else if ($timeDim == "hourgroup") {
                $select = $selectNbiLocations."group by DateId,City";
                return $select;
            }
        } else {
            if ($timeDim == "day") {
                $select = $selectNbiLocations."group by DateId,City";
                return $select;
            } else if ($timeDim == "hour") {
                $select = $selectNbiLocations."group by DateId,HourId,City";
                return $select;
            } else if ($timeDim == "quarter") {
                $select = $selectNbiLocations."group by DateId,HourId,MinId,City";
                return $select;
            } else if ($timeDim == "hourgroup") {
                $select = $selectNbiLocations."group by DateId,City";
                return $select;
            }
        }//end if

    }//end getSelect()


    /**
     * 生成Location Filter字串(NBM)
     *
     * @param string $selectNbiLocation 初始Filter
     * @param string $locationDim       地域维度
     * @param string $erbs              基站列表
     * @param string $cell              小区列表
     *
     * @return string
     */
    public function getSelectNbiLocationNbm($selectNbiLocation, $locationDim, $erbs, $cell)
    {
        if ($locationDim == "erbs" && $erbs != '') {
            $erbsArr = explode(",", $erbs);
            $erbsStr = "";
            for ($i = 0; $i < count($erbsArr); $i++) {
                $erbsStr = $erbsStr."'".$erbsArr[$i]."',";
            }

            $erbsStr            = substr($erbsStr, 0, (strlen($erbsStr) - 1));
            $selectNbiLocations = $selectNbiLocation." and erbs in (".$erbsStr."))";
            return $selectNbiLocations;
        } else if ($locationDim == "cell" || $locationDim == "cellGroup" && $cell != '') {
            $cellArr = explode(",", $cell);
            $cellStr = "";
            for ($i = 0; $i < count($cellArr); $i++) {
                $cellStr = $cellStr."'".$cellArr[$i]."',";
            }

            $cellStr = substr($cellStr, 0, (strlen($cellStr) - 1));
            if ($cell != '') {
                $selectNbiLocations = $selectNbiLocation." and (UserLabel in (".$cellStr."))";
                return $selectNbiLocations;
            } else {
                $selectNbiLocations = $selectNbiLocation;
                return $selectNbiLocations;
            }
        } else {
            $selectNbiLocations = $selectNbiLocation;
            return $selectNbiLocations;
        }//end if

    }//end getSelectNbiLocationNbm()


    /**
     * 生成Location Filter字串(NBI)
     *
     * @param string $selectNbiLocation 初始Filter
     * @param string $locationDim       地域维度
     * @param string $erbs              基站列表
     * @param string $cell              小区列表
     *
     * @return string
     */
    public function getSelectNbiLocation($selectNbiLocation, $locationDim, $erbs, $cell)
    {
        if ($locationDim == "erbs" && $erbs != '') {
            $erbsArr = explode(",", $erbs);
            $erbsStr = "";
            for ($i = 0; $i < count($erbsArr); $i++) {
                $erbsStr = $erbsStr."'".$erbsArr[$i]."',";
            }

            $erbsStr            = substr($erbsStr, 0, (strlen($erbsStr) - 1));
            $selectNbiLocations = $selectNbiLocation." and (ManagedElement in (".$erbsStr."))";
            return $selectNbiLocations;
        } else if ($locationDim == "cell" || $locationDim == "cellGroup" && $cell != '') {
            $cellArr = explode(",", $cell);
            $cellStr = "";
            for ($i = 0; $i < count($cellArr); $i++) {
                $cellStr = $cellStr."'".$cellArr[$i]."',";
            }

            $cellStr = substr($cellStr, 0, (strlen($cellStr) - 1));
            if ($cell != '') {
                $selectNbiLocations = $selectNbiLocation." and (EutranCellTdd in (".$cellStr."))";
                return $selectNbiLocations;
            } else {
                $selectNbiLocations = $selectNbiLocation;
                return $selectNbiLocations;
            }
        } else {
            $selectNbiLocations = $selectNbiLocation;
            return $selectNbiLocations;
        }//end if

    }//end getSelectNbiLocation()


    /**
     * 获得城市列表(NBM)
     *
     * @param string $citys 城市信息
     *
     * @return string
     */
    public function getLocationNbm($citys)
    {
        $cityClass = new DataBaseConnection();
        $citysRows = $cityClass->getNbiOptions($citys);
        return $citysRows;

    }//end getLocationNbm()


    /**
     * 获得城市列表(NBI)
     *
     * @param array $citys 城市信息
     *
     * @return string
     */
    public function getLocation($citys)
    {
        $citysSql  = [];
        $citysRows = '';
        $cityClass = new DataBaseConnection();
        for ($i = 0; $i < count($citys); $i++) {
            $citysSql[$i] = $cityClass->getNbiOptions($citys[$i]);
            $citysRows    = $citysRows."'".$citysSql[$i]."'".' or City=';
        }

        $citysRows = substr($citysRows, 0, (strlen($citysRows) - 9));
        return $citysRows;

    }//end getLocation()


    /**
     * 生成时间字串(NBM)
     *
     * @param string $timeDim      时间维度
     * @param string $hourId       小时ID
     * @param string $minId        分钟ID
     * @param string $selectDateId 日期ID
     *
     * @return string
     */
    public function getSelectTimeDimNbm($timeDim, $hourId, $minId, $selectDateId)
    {
        if ($timeDim == "day") {
            $selectTimeDim = $selectDateId;
            return $selectTimeDim;
        } else if ($timeDim == "hour") {
            if ($hourId == 'null') {
                $hourId = '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23';
            }

            $hourId        = "(".$hourId.")";
            $selectTimeDim = $selectDateId." and hour_id in".$hourId;
            return $selectTimeDim;
        } else if ($timeDim == "quarter") {
            if ($hourId == 'null') {
                $hourId = '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23';
            }

            $hourId = "(".$hourId.")";
            if ($minId == 'null') {
                $minId = '0,15,30,45';
            }

            $minId         = "(".$minId.")";
            $selectTimeDim = $selectDateId." and hour_id in ".$hourId." and min_id in ".$minId;
            return $selectTimeDim;
        } else if ($timeDim == "hourgroup") {
            if ($hourId == 'null') {
                $hourId = '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23';
            }

            $hourId        = "(".$hourId.")";
            $selectTimeDim = $selectDateId." and hour_id in ".$hourId;
            return $selectTimeDim;
        }//end if

    }//end getSelectTimeDim_nbm()


    /**
     * 生成时间维度SELECT字串
     *
     * @param string $timeDim      时间维度
     * @param string $hourId       小时
     * @param string $minId        分钟
     * @param string $selectDateId 初始SELECT字串
     *
     * @return string
     */
    public function getSelectTimeDim($timeDim, $hourId, $minId, $selectDateId)
    {
        if ($timeDim == "day") {
            $selectTimeDim = $selectDateId;
            return $selectTimeDim;
        } else if ($timeDim == "hour") {
            if ($hourId == 'null') {
                $hourId = '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23';
            }

            $hourId        = "(".$hourId.")";
            $selectTimeDim = $selectDateId." and HourId in".$hourId;
            return $selectTimeDim;
        } else if ($timeDim == "quarter") {
            if ($hourId == 'null') {
                $hourId = '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23';
            }

            $hourId = "(".$hourId.")";
            if ($minId == 'null') {
                $minId = '0,15,30,45';
            }

            $minId         = "(".$minId.")";
            $selectTimeDim = $selectDateId." and HourId in ".$hourId." and MinId in ".$minId;
            return $selectTimeDim;
        } else if ($timeDim == "hourgroup") {
            if ($hourId == 'null') {
                $hourId = '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23';
            }

            $hourId        = "(".$hourId.")";
            $selectTimeDim = $selectDateId." and hourId in ".$hourId;
            return $selectTimeDim;
        }//end if

    }//end getSelectTimeDim()


    /**
     * 生成SELECT字串(nbm)
     *
     * @param string $locationDim      地域维度
     * @param string $timeDim          时间维度
     * @param string $nbiFormulaFilter NBI公式
     * @param string $hour             小时
     * @param string $minute           分钟
     * @param string $cell             小区
     *
     * @return string
     */
    public function getSelectEutranCellTddNbm($locationDim, $timeDim, $nbiFormulaFilter, $hour, $minute, $cell)
    {
        if ($locationDim == "erbs") {
            if ($timeDim == "day") {
                return "select date_id,city AS City,erbs AS ManagedElement,count(distinct erbs) as erbsTotal,countHour as hourTotal,$nbiFormulaFilter from EutranCellTdd_station_day";
            } else if ($timeDim == "hour") {
                return "select date_id,hour_id,city AS City,erbs AS ManagedElement,count(distinct erbs) as erbsTotal,$nbiFormulaFilter from EutranCellTdd_station_hour";
            } else if ($timeDim == "quarter") {
                return "select date_id,hour_id,min_id,city AS City,erbs AS ManagedElement,count(distinct erbs) as erbsTotal,$nbiFormulaFilter from EutranCellTdd";
            } else if ($timeDim == "hourgroup") {
                if ($hour == '') {
                    return "select date_id,'24h' as hourgroup,city AS City,erbs AS ManagedElement,count(distinct erbs) as erbsTotal,$nbiFormulaFilter from EutranCellTdd_station_hour";
                } else {
                    return "select date_id,('".$hour."') as hourgroup,city AS City,erbs AS ManagedElement,count(distinct erbs) as erbsTotal,$nbiFormulaFilter from EutranCellTdd_station_hour";
                }
            }
        } else if ($locationDim == "cell") {
            if ($timeDim == "day") {
                return "select date_id,city AS City,UserLabel AS EutranCellTdd,count(distinct UserLabel) as ecgiTotal,countHour as hourTotal,$nbiFormulaFilter from EutranCellTdd_cell_day";
            } else if ($timeDim == "hour") {
                return "select date_id,hour_id,city AS City,UserLabel AS EutranCellTdd,count(distinct UserLabel) as ecgiTotal,$nbiFormulaFilter from EutranCellTdd_cell_hour";
            } else if ($timeDim == "quarter") {
                return "select date_id,hour_id,min_id,city AS City,UserLabel AS EutranCellTdd,count(distinct UserLabel) as cellTotal,$nbiFormulaFilter from EutranCellTdd";
            } else if ($timeDim == "hourgroup") {
                if ($hour == '') {
                    // return "select date_id,'24h' as hourgroup,UserLabel AS EutranCellTdd,SUBSTRING_INDEX(Dn,',',1) AS City,$nbiFormulaFilter from EutranCellTdd_hour";
                    return "select date_id,'24h' as hourgroup,UserLabel AS EutranCellTdd,city AS City,count(distinct UserLabel) as cellTotal,$nbiFormulaFilter from EutranCellTdd_cell_hour";
                } else {
                    // return "select date_id,('".$hour."') as hourgroup,UserLabel AS EutranCellTdd,SUBSTRING_INDEX(Dn,',',1) AS City,$nbiFormulaFilter from EutranCellTdd_hour";
                    return "select date_id,('".$hour."') as hourgroup,UserLabel AS EutranCellTdd,city AS City,count(distinct UserLabel) as cellTotal,$nbiFormulaFilter from EutranCellTdd_cell_hour";
                }
            }
        } else if ($locationDim == "cellGroup") {
            if ($timeDim == "day") {
                return "select date_id,city AS City,('cellgroup') as cellgroup,count(distinct UserLabel) as cellTotal,avg(countHour) as hourTotal,$nbiFormulaFilter from EutranCellTdd_cell_day";
            } else if ($timeDim == "hour") {
                return "select date_id,hour_id,city AS City,('cellgroup') as cellgroup,count(distinct UserLabel) as cellTotal,$nbiFormulaFilter from EutranCellTdd_cell_hour";
            } else if ($timeDim == "quarter") {
                return "select date_id,hour_id,min_id,city AS City,('cellgroup') as cellgroup,count(distinct UserLabel) as cellTotal,$nbiFormulaFilter from EutranCellTdd";
            } else if ($timeDim == "hourgroup") {
                if ($hour == '') {
                    return "select date_id,'24h' as hourgroup,('cellgroup') as cellgroup,city AS City,count(distinct UserLabel) as cellTotal,$nbiFormulaFilter from EutranCellTdd_cell_hour";
                } else {
                    return "select date_id,('".$hour."') as hourgroup,('cellgroup') as cellgroup,city AS City,count(distinct UserLabel) as cellTotal,$nbiFormulaFilter from EutranCellTdd_cell_hour";
                }
            }
        } else {
            if ($timeDim == "day") {
                return "select date_id,city AS City,count(distinct UserLabel) as cellTotal,$nbiFormulaFilter from EutranCellTdd_cell_day";
            } else if ($timeDim == "hour") {
                return "select date_id,hour_id,city AS City,count(distinct UserLabel) as cellTotal,$nbiFormulaFilter from EutranCellTdd_cell_hour";
            } else if ($timeDim == "quarter") {
                return "select date_id,hour_id,min_id,city AS City,count(distinct UserLabel) as cellTotal,$nbiFormulaFilter from EutranCellTdd";
            } else if ($timeDim == "hourgroup") {
                if ($hour == '') {
                    return "select date_id,'24h' as hourgroup,city AS City,count(distinct UserLabel) as cellTotal,$nbiFormulaFilter from EutranCellTdd_cell_hour";
                } else {
                    return "select date_id,('".$hour."') as hourgroup,city AS City,count(distinct UserLabel) as cellTotal,$nbiFormulaFilter from EutranCellTdd_cell_hour";
                }
            }
        }//end if

    }//end getSelectEutranCellTdd_nbm()


    /**
     * 生成SELECT字串(NBI)
     *
     * @param string $locationDim      地域维度
     * @param string $timeDim          时间维度
     * @param string $nbiFormulaFilter NBI公式
     * @param string $hour             小时
     * @param string $minute           分钟
     * @param string $cell             小区
     *
     * @return string
     */
    public function getSelectEutranCellTdd($locationDim, $timeDim, $nbiFormulaFilter, $hour, $minute, $cell)
    {
        if ($locationDim == "erbs") {
            if ($timeDim == "day") {
                return "select DateId,City,ManagedElement,$nbiFormulaFilter from EutranCellTdd_station_day";
            } else if ($timeDim == "hour") {
                return "select DateId,HourId,City,ManagedElement,$nbiFormulaFilter from EutranCellTdd_station_hour";
            } else if ($timeDim == "quarter") {
                return "select DateId,HourId,MinId,City,ManagedElement,$nbiFormulaFilter from EutranCellTdd";
            } else if ($timeDim == "hourgroup") {
                if ($hour == '') {
                    return "select DateId,'24h' as hourgroup,City,ManagedElement,$nbiFormulaFilter from EutranCellTdd_station_hour";
                } else {
                    return "select DateId,('".$hour."') as hourgroup,City,ManagedElement,$nbiFormulaFilter from EutranCellTdd_station_hour";
                }
            }
        } else if ($locationDim == "cell") {
            if ($timeDim == "day") {
                return "select DateId,City,EutranCellTdd,$nbiFormulaFilter from EutranCellTdd_cell_day";
            } else if ($timeDim == "hour") {
                return "select DateId,HourId,City,EutranCellTdd,$nbiFormulaFilter from EutranCellTdd_cell_hour";
            } else if ($timeDim == "quarter") {
                return "select DateId,HourId,MinId,City,EutranCellTdd,$nbiFormulaFilter from EutranCellTdd";
            } else if ($timeDim == "hourgroup") {
                if ($hour == '') {
                    return "select DateId,'24h' as hourgroup,EutranCellTdd,City,$nbiFormulaFilter from EutranCellTdd_cell_hour";
                } else {
                    return "select DateId,('".$hour."') as hourgroup,EutranCellTdd,City,$nbiFormulaFilter from EutranCellTdd_cell_hour";
                }
            }
        } else if ($locationDim == "cellGroup") {
            if ($timeDim == "day") {
                return "select DateId,City,('cellgroup') as cellgroup,$nbiFormulaFilter from EutranCellTdd_cell_day";
            } else if ($timeDim == "hour") {
                return "select DateId,HourId,City,('cellgroup') as cellgroup,$nbiFormulaFilter from EutranCellTdd_cell_hour";
            } else if ($timeDim == "quarter") {
                return "select DateId,HourId,MinId,City,('cellgroup') as cellgroup,$nbiFormulaFilter from EutranCellTdd";
            } else if ($timeDim == "hourgroup") {
                if ($hour == '') {
                    return "select DateId,'24h' as hourgroup,('cellgroup') as cellgroup,City,$nbiFormulaFilter from EutranCellTdd_cell_hour";
                } else {
                    return "select DateId,('".$hour."') as hourgroup,('cellgroup') as cellgroup,City,$nbiFormulaFilter from EutranCellTdd_cell_hour";
                }
            }
        } else {
            if ($timeDim == "day") {
                return "select DateId,City,$nbiFormulaFilter from EutranCellTdd_city_Quarter";
            } else if ($timeDim == "hour") {
                return "select DateId,HourId,City,$nbiFormulaFilter from EutranCellTdd_city_Quarter";
            } else if ($timeDim == "quarter") {
                return "select DateId,HourId,MinId,City,$nbiFormulaFilter from EutranCellTdd_city_Quarter";
            } else if ($timeDim == "hourgroup") {
                if ($hour == '') {
                    return "select DateId,'24h' as hourgroup,City,$nbiFormulaFilter from EutranCellTdd_city_Quarter";
                } else {
                    return "select DateId,('".$hour."') as hourgroup,City,$nbiFormulaFilter from EutranCellTdd_city_Quarter";
                }
            }
        }//end if

    }//end getSelectEutranCellTdd()


    /**
     * KPI预处理
     *
     * @param array $nbiKpis KPI集合
     *
     * @return string
     */
    public function parseNbiKpiPre($nbiKpis)
    {   
        $nbiKpisIds        = $nbiKpis['ids'];
        $nbiKpiFormulaData = "select kpiPrecision from kpiFormulaNbi where id in ($nbiKpisIds)";
        $query = mysql_query($nbiKpiFormulaData);
        $nbiKpiFormulaRows = '';
        while ($row = mysql_fetch_row($query)) {
            $nbiKpiFormulaRows = $nbiKpiFormulaRows.$row[0].',';
        }

        return $nbiKpiFormulaRows;

    }//end parseNbiKpiPre()


    /**
     * What's it?
     *
     * @param array $nbiKpis nbi指标集合
     *
     * @return string
     */
    public function parseNbiKpiTest($nbiKpis)
    {
        // $nbiKpisIds        = $nbiKpis['ids'];

        // // $nbiKpiFormulaData = "select kpiFormula from kpiFormulaNbi where id in($nbiKpisIds)";
        // $nbiKpiFormulaData = "select kpiFormula,instr('$nbiKpisIds',CONCAT(id,',')) as sort  from kpiFormulaNbi where id in($nbiKpisIds) ORDER BY sort";

        // $res           = mysql_query($nbiKpiFormulaData);
        $nbiKpiFormula = '';
        // $i   = 0;
        // $row = [];
        // while ($rows = mysql_fetch_row($res)) {
        //     $row[$i++] = $rows[0];
        // }

        // $nbiKpis      = $this->getNbiKpis();
        $row =explode(',', $nbiKpis['kpiformula']);
        $nbiKpisNames = $nbiKpis['names'];
        $nbikpiPression=explode(',', $nbiKpis['kpiPrecision']);
        // $nbiKpisNames = substr($nbiKpisNames, 0, (strlen($nbiKpisNames) - 1));
        $nbiKpisNames = explode(',', $nbiKpisNames);
        for ($i = 0; $i<count($row);$i++) {	
        	$nbiKpiFormula.="round(".$this->formulaTransform($row[$i]).",".$nbikpiPression[$i].") as '".$nbiKpisNames[$i]."',";
        }

        $nbiKpiFormula = substr($nbiKpiFormula, 0, (strlen($nbiKpiFormula) - 1));
        return $nbiKpiFormula;

    }//end parseNbiKpiTest()


    /**
     * 获得KPI列表
     *
     * @return array
     */
    public function getNbiKpis()
    {
        $dbn = new DataBaseConnection();
        $dbn->getConnDB('mongs');
        if (!mysql_select_db('mongs')) {
            echo '连接失败!';
        }

        $templateName = Input::get('template');
        $templateId   = Input::get('templateId');
        $type         = Input::get('parent');
        $sql = "select id,kpiName,kpiFormula,kpiPrecision from kpiTemplateCommon where templateName='$templateName' and type='$type'";

        $res          = mysql_query($sql);
        while ($rows=mysql_fetch_assoc($res)) {	
        	$result['id'][]= $rows['id'];
        	$result['names'][] = $rows['kpiName'];
            $result['kpiformula'][]   = $rows['kpiFormula'];
            $result['kpiPrecision'][]=$rows['kpiPrecision'];  
        }
        $array          = array();
        $array['names'] = implode(',', $result['names']);
        $array['ids']   = implode(',', $result['id']);
        $array['kpiformula']=implode(',', $result['kpiformula']);
        $array['kpiPrecision']=implode(',', $result['kpiPrecision']);
        return $array;

    }//end getNbiKpis()


    /**
     * KPI公式转换
     *
     * @param string $formula KPI公式
     *
     * @return mixed|string
     */
    protected function formulaTransform($formula)
    {
        if (strpos($formula, '+') == false && strpos($formula, '-') == false && strpos($formula, '*') == false && strpos($formula, '/') == false && strpos($formula, '(') == false && strpos($formula, ')') == false) {
            $formula = "SUM(".$formula.")";
            return $formula;
        }

        $fields  = preg_split("/[-+*\/() ]+/", $formula);
        $formula = " ".$formula." ";
        $formula = str_replace("+", " + ", $formula);
        $formula = str_replace("-", " - ", $formula);
        $formula = str_replace("*", " * ", $formula);
        $formula = str_replace("/", " / ", $formula);
        $formula = str_replace("(", " ( ", $formula);
        $formula = str_replace(")", " ) ", $formula);

        foreach ($fields as $value) {
            if ($value == "" || is_numeric($value) || $value == "power" || $value == "log10" || $value == "max" || $value == "POWER" || $value == "LOG10" || $value == "MAX" || $value == "AVG" || $value == "avg") {
                continue;
            }

            $new_value = "SUM(".$value.")";
            $formula   = str_replace(" ".$value." ", $new_value, $formula);
        }

        $formula = str_replace(" ", "", $formula);

        if (strpos($formula, 'MAX(SUM(') !== false) {
            $formula = str_replace("MAX(SUM(", "(MAX(", $formula);
        }

        if (strpos($formula, 'max(SUM(') !== false) {
            $formula = str_replace("max(SUM(", "(MAX(", $formula);
        }

        if (strpos($formula, 'AVG(SUM(') !== false) {
            $formula = str_replace("AVG(SUM(", "(AVG(", $formula);
        }

        if (strpos($formula, 'avg(SUM(') !== false) {
            $formula = str_replace("avg(SUM(", "(avg(", $formula);
        }

        return $formula;

    }//end formulaTransform()


}//end class
