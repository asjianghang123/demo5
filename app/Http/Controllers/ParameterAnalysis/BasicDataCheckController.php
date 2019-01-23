<?php

/**
 * BasicDataCheckController.php
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
use App\Models\Mongs\Task;
use App\Models\Mongs\Databaseconns;
use PHPExcel;
use PHPExcel_Writer_Excel2007;

/**
 * 基础数据检查
 * Class BasicDataCheckController
 *
 * @category ParameterAnalysis
 * @package  App\Http\Controllers\ParameterAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class BasicDataCheckController extends Controller
{
    /**
     * 获取日期（数据库名称）
     *
     * @return string
     */
    public function getTasks()
    {
        $user = Auth::user();//获取登录用户信息
        $userName = $user->user;
        $tasks = Task::where('type', 'parameter')->where('status', 'complete')->where('taskName', 'like', 'kget______');
        if ($userName != 'admin') {
            $tasks = $tasks->whereIn('owner', [$userName,'admin']);
        }
        $tasks = $tasks->orderBy('taskName', 'desc')->get()->toArray();
        $items = array();
        foreach ($tasks as $task) {
            $items[] = array("text"=>$task['taskName'], "id"=>$task['taskName']);
        }
        return json_encode($items);//需要通过response返回响应数据
    }

    /**
     * 获得城市列表
     *
     * @return string 城市列表
     */
    public function getCities()
    {
        $dbc = new DataBaseConnection();
        return $dbc->getCityOptions();
    }

    /**
     * 获得数据库连接信息
     *
     * @return array 数据库连接信息名列表
     */
    public function getCityList()
    {
        $rows = Databaseconns::groupBy('connName')->get()->toArray();
        $cityArr = array();
        foreach ($rows as $row) {
            if (!(substr($row['connName'], -1, 1) == '1')) {
                if (!array_search($row['connName'], $cityArr)) {
                    array_push($cityArr, $row['connName']);
                }
            }
        }
        return $cityArr;
    }

    /**
     * 获得分布数据
     *
     * @return array 分布数据
     */
    public function getDistributeData()
    {
        $db = Input::get('db');
        $dbc = new DataBaseConnection();
        $dbn = $dbc->getDB('mongs', $db);
        $YCategories = Input::get("YCategories");
        $tables = Input::get("tables");

        for ($i = 0; $i < count($tables); $i++) {
            $table = $tables[$i];
            for ($j = 0; $j < count($YCategories); $j++) {
                $city = $YCategories[$j];
                $cityCh = $dbc->getCHCity($city);
                $subNetwork = $dbc->getSubNets($cityCh);
                if ($table == 'TempEUtranCellRelationUnidirectionalNeighborCell') {
                    $sql = "select count(*) as occurs from $table t where status='ON' and subNetwork in(" . $subNetwork . ") and EUtranCellTDD not in (select EUtranCellTDD from mongs.UnidirectionalNeighborCell_Template where subNetwork in(" . $subNetwork . "))";
                } else if ($table == 'Angle_Check_table') {
                    $sql = "select count(*) as occurs from $table t where checkResult='error' and subNetwork in(" . $subNetwork . ")";
                } else if ($table == 'latloncheck') {
                    $sql = "select count(*) as occurs from $table t where checkResult='error' and subNetwork in(" . $subNetwork . ") and meContext not in (select meContext from mongs.latlonCheckWhiteList)";
                } else if ($table == 'TempEUtranCellRelationManyNeighborCell' || $table == 'TempEUtranCellRelationFewNeighborCell' || $table == 'TempMeasuringFrequencyTooMuch') {
                    $sql = "select count(distinct EUtranCellTDD) as occurs from $table t where subNetwork in(" . $subNetwork . ")";
                } else if ($table == 'TempParameterKgetExternalCompare_1') {
                    $sql = "select count(*) as occurs from $table t where subNetwork in (" . $subNetwork . ") or city='$city'";

                } else if ($table == 'TempEUtranCellFreqRelation') {
                    $sql = "select count(*) as occurs from $table t where remark is null and subNetwork in(" . $subNetwork . ")";
                } else if ($table == 'TempCVTooMany') {
                    $sql = "select count(distinct meContext) as occurs from $table t where subNetwork in(" . $subNetwork . ")";
                } else {
                    $sql = "select count(*) as occurs from $table t where subNetwork in(" . $subNetwork . ")";
                }
                if ($dbc->tableIfExists($db, $table)) {
                    $stmt = $dbn->prepare($sql);
                    if ($stmt->execute()) {
                        $occurs = $stmt->fetchColumn();
                        $arr = [$i, $j, floatval($occurs)];
                        $seriesData[] = $arr;
                    } else {
                        $occurs = 0;
                        $arr = [$i, $j, floatval($occurs)];
                        $seriesData[] = $arr;
                    }
                } else {
                    $occurs = 0;
                    $arr = [$i, $j, floatval($occurs)];
                    $seriesData[] = $arr;
                }
            }
        }
        return $seriesData;
    }

    /**
     * 获得表头字段
     *
     * @return array 表头字段集
     */
    public function getTableField()
    {
        $db = Input::get('db');
        $table = Input::get('table');
        $city = Input::get('city');
        $dbc = new DataBaseConnection();
        $dbn = $dbc->getDB('mongs', $db);
        $cityCh = $dbc->getCHCity($city);
        $subNetwork = $dbc->getSubNets($cityCh);
        $filter = '';
        if ($subNetwork != '') {
            $filter = " where subNetwork in(" . $subNetwork . ") ";
        }

        $query = "select * from " . $table . $filter . " limit 1";
        $stmt = $dbn->prepare($query);
        if ($stmt->execute()) {
            $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($rs) > 0) {
                return $rs[0];
            } else {
                $result = array();
                $result['result'] = 'error';
                return $result;
            }
        } else {
            $result = array();
            $result['result'] = 'error';
            return $result;
        }
    }

    /**
     * 获取记录数
     *
     * @return array
     */
    public function getItems()
    {
        $db = Input::get('db');
        $table = Input::get('table');
        $city = Input::get('city');
        $dbc = new DataBaseConnection();
        $dbn = $dbc->getDB('mongs', $db);

        $displayStart = Input::get('page');
        $displayLength = Input::get('limit');
        $offset = ($displayStart - 1) * $displayLength;
        $filter = '';
        $limit = " limit $offset,$displayLength ";
        $cityCh = $dbc->getCHCity($city);
        $subNetwork = $dbc->getSubNets($cityCh);
        /*if ($table == 'Angle_Check_table') {
            if ($subNetwork != '') {
                $filter = " where subNetwork in(" . $subNetwork . ")";
            } else {
                $filter = " where checkResult='error'";
            }
        } else */
        if ($table == 'latloncheck') {
            if ($subNetwork != '') {
                $filter = " where checkResult='error' and subNetwork in(" . $subNetwork . ") and meContext not in (select meContext from mongs.latlonCheckWhiteList)";
            } 
            else {
                $filter = " where checkResult='error' and meContext not in (select meContext from mongs.latlonCheckWhiteList)";
            }
        } 
        else if ($table == 'TempEUtranCellRelationUnidirectionalNeighborCell') {
            if ($subNetwork != '') {
                $filter = " where subNetwork in(" . $subNetwork . ") and EUtranCellTDD not in (select EUtranCellTDD from mongs.UnidirectionalNeighborCell_Template where subNetwork in(" . $subNetwork . "))";
            } 
            else {
                $filter = " where EUtranCellTDD not in (select EUtranCellTDD from mongs.UnidirectionalNeighborCell_Template)";
            }
        } 
        else if($table == 'TempParameterKgetExternalCompare_1'){
                $filter = " where subNetwork in (" . $subNetwork . ") or city=:city";
        } 
        else 
            {
            if ($subNetwork != '') {
                $filter = " where subNetwork in (" . $subNetwork . ")";
            }
        }
        $result = array();
        $sqlCount = "select count(*) from " . $table . $filter;
        $stmt = $dbn->prepare($sqlCount);
        if ($table == 'TempParameterKgetExternalCompare_1') {
            $stmt->bindParam(':city', $city);
        }
        $stmt->execute();
        $result["total"] = $stmt->fetchColumn();
        $sql = "select * from $table $filter $limit";
        $stmt = $dbn->prepare($sql);
        if ($table == 'TempParameterKgetExternalCompare_1') {
            $stmt->bindParam(':city', $city);
        }
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_OBJ);
        $items = array();
        if ($res) {
            foreach ($res as $row) {
                array_push($items, $row);
            }
            $result["records"] = $res;
        }
        return $result;
    }

    /**
     * 获取网元oss归属信息
     *
     * @return array 网元oss归属信息
     */
    public function getOssInfoItems()
    {

        $db = Input::get('db');
        $table = Input::get('table');
        $city = Input::get('city');
        $erbs = Input::get('erbs');
        $eNBId = Input::get('eNBId');
        $cell = Input::get('cell');
        $ecgi = Input::get('ecgi');
        $ip = Input::get('ip');
        $dbc = new DataBaseConnection();
        $dbn = $dbc->getDB('mongs', $db);
        $displayStart = Input::get('page');
        $displayLength = Input::get('limit');
        $offset = ($displayStart - 1) * $displayLength;
        $limit = " limit $offset,$displayLength ";
        $cityCh = $dbc->getCHCity($city);
        $subNetwork = $dbc->getSubNets($cityCh);
        $paramArr = array();
        $paramArr['siteName'] = $erbs;
        $paramArr['eNBId'] = $eNBId;
        $paramArr['cellName'] = $cell;
        $paramArr['ecgi'] = $ecgi;
        $paramArr['nodeIpAddress'] = $ip;
        $filter = $this->combineFilter($paramArr, $subNetwork);
        $result = array();
        $sqlCount = "select count(*) from " . $table . $filter;
        $rs = $dbn->query($sqlCount, PDO::FETCH_ASSOC);
        $result["total"] = $rs->fetchColumn();
        $sql = "select * from $table $filter $limit";
        $rs = $dbn->query($sql, PDO::FETCH_OBJ);
        $res = $rs->fetchAll();
        $items = array();
        if ($res) {
            foreach ($res as $row) {
                array_push($items, $row);
            }
            $result["records"] = $res;
        }
        return $result;
    }

    /**
     * 查询条件拼接
     *
     * @param array  $paramArr   参数集合
     * @param string $subNetwork 子网字串
     *
     * @return string SQL字串
     */
    function combineFilter($paramArr, $subNetwork)
    {
        $filter = "";
        $i = 0;
        if ($subNetwork != '') {
            foreach ($paramArr as $key => $value) {
                if ($value) {
                    if ($i == 0) {
                        $i = $i + 1;
                        $filter .= " where subNetwork in (" . $subNetwork . ") and $key='" . $value . "'";
                    } else {
                        $filter .= " and $key='" . $value . "'";
                    }
                }
            }
            if ($filter == '') {
                $filter = " where subNetwork in (" . $subNetwork . ") ";
            }
        } else {
            foreach ($paramArr as $key => $value) {
                if ($value) {
                    if ($i == 0) {
                        $i = $i + 1;
                        $filter .= " where $key='" . $value . "'";
                    } else {
                        $filter .= " and $key='" . $value . "'";
                    }
                }
            }
        }
        return $filter;
    }

    /**
     * 文件导出
     *
     * @return array 导出结果
     */
    public function exportFile()
    {
        $db = Input::get('db');
        $table = Input::get('table');
        $city = Input::get('city');
        $dbc = new DataBaseConnection();
        $dbn = $dbc->getDB('mongs', $db);
        $fileName = "files/" . $table . "_" . date('YmdHis') . ".csv";
        $filter = '';
        $cityCh = $dbc->getCHCity($city);
        $subNetwork = $dbc->getSubNets($cityCh);
       /* if ($table == 'Angle_Check_table') {
            if ($subNetwork != '') {
                $filter = " where checkResult='error' and subNetwork in(" . $subNetwork . ")";
            } else {
                $filter = " where checkResult='error'";
            }
        } else */
        if ($table == 'latloncheck') {
            if ($subNetwork != '') {
                $filter = " where checkResult='error' and subNetwork in(" . $subNetwork . ") and meContext not in (select meContext from mongs.latlonCheckWhiteList)";
            } 
            else {
                $filter = " where checkResult='error' and meContext not in (select meContext from mongs.latlonCheckWhiteList)";
            }
        } 
        else if ($table == 'TempEUtranCellRelationUnidirectionalNeighborCell') {
            if ($subNetwork != '') {
                $filter = " where subNetwork in(" . $subNetwork . ") and EUtranCellTDD not in (select EUtranCellTDD from mongs.UnidirectionalNeighborCell_Template where subNetwork in(" . $subNetwork . "))";
            } 
            else {
                $filter = " where EUtranCellTDD not in (select EUtranCellTDD from mongs.UnidirectionalNeighborCell_Template)";
            }
        } 
        else if($table == 'TempParameterKgetExternalCompare_1'){
                $filter = " where subNetwork in (" . $subNetwork . ") or city='$city'";
        }  
        else {
            if ($subNetwork != '') {
                $filter = " where subNetwork in (" . $subNetwork . ")";
            }
        }
        $result = array();
        $sql = "select * from $table $filter";
        $rs = $dbn->query($sql, PDO::FETCH_ASSOC);
        if ($rs) {
            $items = $rs->fetchAll();
            if (count($items) > 0) {
                $row = $items[0];
                $column = implode(",", array_keys($row));
                $fileUtil = new FileUtil();
                $fileUtil->resultToCSV2($column, $items, $fileName);
                $result['fileName'] = $fileName;
                $result['result'] = true;
            } else {
                $result['result'] = false;
            }
        } else {
            $result['result'] = false;
        }
        return $result;
    }

    /**
     * 网元归属信息导出
     *
     * @return array 导出结果
     */
    public function exportOssInfoFile()
    {
        $db = Input::get('db');
        $table = Input::get('table');
        $city = Input::get('city');
        $erbs = Input::get('erbs');
        $eNBId = Input::get('eNBId');
        $cell = Input::get('cell');
        $ecgi = Input::get('ecgi');
        $ip = Input::get('ip');
        $dbc = new DataBaseConnection();
        $dbn = $dbc->getDB('mongs', $db);
        $cityCh = $dbc->getCHCity($city);
        $subNetwork = $dbc->getSubNets($cityCh);
        $paramArr = array();
        $paramArr['siteName'] = $erbs;
        $paramArr['eNBId'] = $eNBId;
        $paramArr['cellName'] = $cell;
        $paramArr['ecgi'] = $ecgi;
        $paramArr['nodeIpAddress'] = $ip;
        $filter = $this->combineFilter($paramArr, $subNetwork);
        $result = array();
        $fileContent = array();
        $sqlCount = "select count(*) from " . $table . $filter;
        $stmt = $dbn->prepare($sqlCount);
        $stmt->execute();
        $result["total"] = $stmt->fetchColumn();
        $sql = "select * from $table $filter";
        $stmt = $dbn->prepare($sqlCount);
        $stmt->execute();
        $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($rs) > 0) {
            $row = $rs[0];
        } else {
            return 'null';
        }
        $fieldArr = array_keys($row);
        $csvContent = implode(",", $fieldArr);
        $csvContent = mb_convert_encoding($csvContent, 'gbk', 'utf-8');
        $fileContent[] = $csvContent . ",";

        foreach ($rs as $row) {
            $csvContent = "";
            foreach ($row as $column) {
                $column = trim($column);
                $column = str_replace(",", " ", $column);
                $csvContent = $csvContent . "," . $column;
            }
            $csvContent = substr($csvContent, 1, strlen($csvContent) - 1);
            $csvContent = $csvContent . ",";
            $csvContent = mb_convert_encoding($csvContent, 'gbk', 'utf-8');
            $fileContent[] = $csvContent;
        }
        $filename = "files/" . $table . "_" . date('YmdHis') . ".csv";
        $fp = fopen($filename, 'w+');
        foreach ($fileContent as $line) {
            $lineArr = explode(',', $line);
            fputcsv($fp, $lineArr);
        }
        fclose($fp);
        $result["result"] = 'true';
        $result["filename"] = $filename;
        return $result;
    }

    /**
     * 单向邻区白名单模板导入
     *
     * @return void
     */
    public function getFileContent()
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        $table = input::get("table");
        $city = input::get("city");
        $fileName = Input::get('fileName');
        $fileUtil = new FileUtil();
        $result = $fileUtil->parseFile($fileName);
        $len_result = count($result);
        if ($len_result == 0) {
            echo '没有任何数据！';
            exit;
        }
        $sql = "delete from $table where city =:city";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':city', $city);
        $stmt->execute();
        if ($table == 'UnidirectionalNeighborCell_Template') {

            $data_values = '';
            for ($i = 1; $i < $len_result; $i++) {
                $mo = $result[$i][0];
                $subNetwork = $result[$i][1];
                $Mecontext = $result[$i][2];
                $EutranCellTDD = $result[$i][3];
                $EUtranCellRelationId = $result[$i][4];
                $EUtranCellTDDNeigh = $result[$i][5];
                $distance = $result[$i][6];
                $status = $result[$i][7];
                $cityNeigh = $result[$i][8];
                $data_values .= "('$mo','$subNetwork','$Mecontext','$EutranCellTDD','$EUtranCellRelationId','$EUtranCellTDDNeigh','$distance','$status','$cityNeigh','$city'),";

            }
            if ($len_result == 1) {
                $mo = 'NULL';
                $subNetwork = 'NULL';
                $Mecontext = 'NULL';
                $EutranCellTDD = 'NULL';
                $EUtranCellRelationId = 'NULL';
                $EUtranCellTDDNeigh = 'NULL';
                $distance = 'NULL';
                $status = 'NULL';
                $cityNeigh = 'NULL';
                $data_values .= "('$mo','$subNetwork','$Mecontext','$EutranCellTDD','$EUtranCellRelationId','$EUtranCellTDDNeigh','$distance','$status','$cityNeigh','$city'),";
            }
            $data_values = mb_convert_encoding(substr($data_values, 0, -1), 'UTF-8', 'GBK');//解析文件编码是UTF-8无需转码
            $sql = "insert into $table (mo,subNetwork,Mecontext,EutranCellTDD,EUtranCellRelationId,EUtranCellTDDNeigh,distance,status,cityNeigh,city) values $data_values";
            $stmt = $db->prepare($sql);
            $stmt->execute();
        } else if ($table == "TempEUtranCellFreqRelationWhiteList") {
            $data_values = '';
            for ($i = 1; $i < $len_result; $i++) {
                $EUtranCellTDD = $result[$i][0];
                $EUtranFreqRelation = $result[$i][1];
                $data_values .= "('$EUtranCellTDD','$EUtranFreqRelation','$city'),";

            }
            // dump($data_values);
            if ($len_result == 1) {
                $EUtranCellTDD = 'NULL';
                $EUtranFreqRelation = 'NULL';
                $city = 'NULL';
                $data_values .= "('$EUtranCellTDD','$EUtranFreqRelation','$city'),";
            }
            $data_values = mb_convert_encoding(substr($data_values, 0, -1), 'UTF-8', 'GBK');//解析文件编码是UTF-8无需转码
            $sql = "insert into $table (EUtranCellTDD,EUtranFreqRelation,city) values $data_values";
            $stmt = $db->prepare($sql);
            $stmt->execute();
        } else {
            $data_values = '';
            for ($i = 1; $i < $len_result; $i++) {
                $meContext = $result[$i][0];
                $citys = $result[$i][1];
                $data_values .= "('$meContext','$citys'),";

            }
            // dump($data_values);
            if ($len_result == 1) {
                $meContext = 'NULL';
                $citys = 'NULL';
                $data_values .= "('$meContext','$citys',),";
            }
            $data_values = mb_convert_encoding(substr($data_values, 0, -1), 'UTF-8', 'GBK');//解析文件编码是UTF-8无需转码
            $sql = "insert into $table (meContext,city) values $data_values";
            $stmt = $db->prepare($sql);
            $stmt->execute();
        }

    }

    /**
     * 导出模板
     *
     * @return array 导出结果
     */
    public function exportTemplate()
    {
        $table = Input::get('table');
        $city = Input::get('city');
        $dbc = new DataBaseConnection();
        $dbn = $dbc->getDB('mongs', 'mongs');
        $fileName = "files/" . $table . "_template.csv";
        $filter = '';
        $templateTable = '';
        if ($table == 'TempEUtranCellRelationUnidirectionalNeighborCell') {
            $templateTable = 'UnidirectionalNeighborCell_Template';
            $filter = " where city=:city ";
        } else if ($table == 'TempEUtranCellFreqRelation'){
            $templateTable = 'TempEUtranCellFreqRelationWhiteList';
            $filter = " where city=:city ";
        } else {
            $templateTable = 'latlonCheckWhiteList';
            $filter = " where city=:city ";
        }
        $result = array();
        $sqlCount = "select count(*) from " . $templateTable . $filter;
        $stmt = $dbn->prepare($sqlCount);
        $stmt->bindParam(':city', $city);
        $stmt->execute();
        $result["total"] = $stmt->fetchColumn();
        $sql = "select * from $templateTable $filter";
        $stmt = $dbn->prepare($sql);
        $stmt->bindParam(':city', $city);
        if ($stmt->execute()) {
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($items) > 0) {
                $row = $items[0];
                $column = implode(",", array_keys($row));
                $column = mb_convert_encoding($column, 'gbk', 'utf-8');
                $fileUtil = new FileUtil();
                $fileUtil->resultToCSV2($column, $items, $fileName);
                $result['fileName'] = $fileName;
                $result['result'] = true;
            } else {
                $result['result'] = false;
            }
        } else {
            $result['result'] = false;
        }
        return $result;
    }

    /**
     * 导出DT数据
     *
     * @return array 导出结果
     */
    public function exportDT()
    {
        $db = Input::get('db');
        $table = Input::get('table');
        $city = Input::get('city');
        $dbc = new DataBaseConnection();
        $dbn = $dbc->getDB('mongs', $db);
        $fileName = "files/导出DT脚本_" . $db . "_" . $table . "_" . date('YmdHis') . ".csv";
        $filter = '';
        $cityCh = $dbc->getCHCity($city);
        $subNetwork = $dbc->getSubNets($cityCh);
        if ($subNetwork != '') {
            $filter = " where subNetwork in (" . $subNetwork . ")";
        }
        $sql = "select * from $table" . $filter;
        $stmt = $dbn->prepare($sql);
        if ($stmt->execute()) {
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($items) > 0) {
                $row = $items[0];
                $column = implode(",", array_keys($row));
                $column = mb_convert_encoding($column, 'gbk', 'utf-8');
                $fileUtil = new FileUtil();
                $fileUtil->resultToCSV2($column, $items, $fileName);
                $result['fileName'] = $fileName;
                $result['result'] = true;
            } else {
                $result['result'] = false;
            }
        } else {
            $result['result'] = false;
        }
        return $result;
    }

    /**
     * 读入文件
     *
     * @param mixed $handle CSV文件句柄
     *
     * @return array
     */
    protected function inputCsv($handle)
    {
        $out = array();
        $n = 0;
        while ($data = fgetcsv($handle, 10000)) {
            $num = count($data);
            for ($i = 0; $i < $num; $i++) {
                $out[$n][$i] = $data[$i];
            }
            $n++;
        }
        return $out;
    }


     public function exportFiles()
    {
        $db   = Input::get('db');
        $name = Input::get('treeName');
        $dbc  = new DatabaseConnection();
        $dbn  = $dbc->getDB('mongs', $db);
        $tables=Input::get("tables");
        //根据获取中文市名
        $citys = $dbc->getCNCityArr();
        
        $subNetwork=array();
        foreach ($citys as $k=>$v) {
            $subNetwork[]= $dbc->getSubNets($v);
        }
        
        $subNetwork=implode(',', $subNetwork);
        $data=array();
   
        $PHPExcel = new PHPExcel();
    //Excel表格式,这里简略写了26列
        $head=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
            //删除默认的sheet，对sheet重建
        $index ='0';
        $PHPExcel->removeSheetByIndex(0);
        foreach ($tables as $key=>$value) {
            $PHPExcel->createSheet();  
            $PHPExcel->setActiveSheetIndex($index);
            //sheet名称大小最大为31字节,所以做了一下映射
            $vans=trans('message.CCHECK.'.$value);
           
            $PHPExcel->getActiveSheet()->setTitle($vans);
            if ($dbc->tableIfExists($db, $value)) {
    
                $j =0;
                $sql = "desc $value";
                $res=$dbn->query($sql);
                if ($res) {
                    $row=$res->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($row as $key => $title) {
                        // var_dump($title);
                        // echo $head[$j].'1';
                        $PHPExcel->setActiveSheetIndex($index)->setCellValue($head[$j].'1', $title['Field']);
                        $j++;
                    }
                        $sql="select * from $value where subNetwork in ($subNetwork)";
                        $ress=$dbn->query($sql)->fetchALL(PDO::FETCH_NUM);
                        if ($ress) {
                              $k=2;
                            foreach ($ress as $key => $value) {
                                for ($i=0;$i<count($value);$i++) {
                                    //从第二行写入数据
                                     $PHPExcel->setActiveSheetIndex($index)->setCellValue($head[$i].$k, $value[$i]);
                                     }
                                     $k++;
                                 } //end foreach()
                                # code...
                    }
                }
               
                
            }
            $index++;
        }
        $PHPExcel->setActiveSheetIndex(0);
        $write = new PHPExcel_Writer_Excel2007($PHPExcel);   
     
        $filename="common/files/".$name.date('YmdHis').".xlsx";
        $write->save($filename);
        $data['filename']=$filename;
        return json_encode($data);
    }

}
?>