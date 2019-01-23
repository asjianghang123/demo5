<?php

namespace App\Http\Controllers\SpecialFunction;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;

/**
 * 操作查询
 * Class OperationQueryController
 *
 * @package App\Http\Controllers\SpecialFunction
 */
class OperationQueryController extends Controller
{

    /**
     * 获得城市列表
     *
     * @return string
     */
    public function getCitys()
    {
        $dbc = new DataBaseConnection();
        return $dbc->getCityOptions();

    }//end getCitys()
    
    /**
     * 获得操作查询数据
     *
     * @return string 当页数据
     */
    public function getOperationData()
    {
        $startTime = Input::get('startTime');
        $endTime = Input::get('endTime');
        $city = Input::get('city');
        switch ($city) {
        case '常州':
            $citys = 'CHANGZHOU';
            break;
        case '南通':
            $citys = 'NANTONG';
            break;
        case '苏州':
            $citys = 'SUZHOU';
            break;
        case '无锡':
            $citys = 'WUXI';
            break;
        case '镇江':
            $citys = 'ZHENJIANG';
            break;
        }
        // dump($citys);
        $action_typeArr = Input::get('action_type');
        foreach ($action_typeArr as $key => $value) {
            $action_typeArr[$key] = "'".$value."'";
        }
        $action_type = implode(",", $action_typeArr);
        // dump($action_type);
        $action_sourceArr = Input::get('action_source');
        foreach ($action_sourceArr as $key => $value) {
            $action_sourceArr[$key] = "'".$value."'";
        }
        $action_source = implode(",", $action_sourceArr);
        // dump($action_source);
        $siteStr = Input::get('site');
        $siteArr = explode(",", $siteStr);
        foreach ($siteArr as $key => $value) {
            $siteArr[$key] = "'".$value."'";
        }
        $site = implode(",", $siteArr);
        // dump($site);
        // $site = Input::get('site');
        $params = Input::get('param');
        $arrStartTime=explode(" ", $startTime);
        $strStartTime=implode("T", $arrStartTime);
        $arrEndTime=explode(" ", $endTime);
        $strEndTime=implode("T", $arrEndTime);
        $result = array();
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('mongs', 'lgo');
        $table  = 'lgo_infomation';

        // $sql = "select * from lgo_infomation WHERE  datetime BETWEEN '".$strStartTime."%' AND '".$strEndTime."%' AND action_source in (".$action_source.") AND action_type in (".$action_type.") and para_name like '%".$params."%'"   and city = '".$citys."'
        if ($siteStr == "") {
            if ($params == "") {
            $sql = "select datetime,city,site,EUtranCellTDD,action_source,action_type,mo,para_name,para_value from lgo_infomation WHERE  datetime BETWEEN '".$strStartTime."%' AND '".$strEndTime."%' and city = '".$citys."' AND action_source in (".$action_source.") AND action_type in (".$action_type.")";
                $content = "时间日期,城市,站号,小区名,操作来源,操作类型,MO名称,参数,目标值";
                // dump($sql);
            } else {
                $sql = "select datetime,city,site,EUtranCellTDD,action_source,action_type,mo,para_name,para_value from lgo_infomation WHERE  datetime BETWEEN '".$strStartTime."%' AND '".$strEndTime."%' and city = '".$citys."' AND action_source in (".$action_source.") AND action_type in (".$action_type.") and para_name like '%".$params."%'";
                $content = "时间日期,城市,站号,小区名,操作来源,操作类型,MO名称,参数,目标值";
            }
        } else {
            if ($params == "") {
                $sql = "select datetime,city,site,EUtranCellTDD,action_source,action_type,mo,para_name,para_value from lgo_infomation WHERE  datetime BETWEEN '".$strStartTime."%' AND '".$strEndTime."%' and city = '".$citys."' AND action_source in (".$action_source.") AND action_type in (".$action_type.") AND site in (".$site.") ";
                $content = "时间日期,城市,站号,小区名,操作来源,操作类型,MO名称,参数,目标值";
                // dump($sql);
            } else {
                $sql = "select datetime,city,site,EUtranCellTDD,action_source,action_type,mo,para_name,para_value from lgo_infomation WHERE  datetime BETWEEN '".$strStartTime."%' AND '".$strEndTime."%' and city = '".$citys."' AND action_source in (".$action_source.") AND action_type in (".$action_type.") AND site in (".$site.") and para_name like '%".$params."%'";
                $content = "时间日期,城市,站号,小区名,操作来源,操作类型,MO名称,参数,目标值";
            }
        }
        // dump($sql);
        $items = array();
        $res = $db->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            array_push($items, $row);
        }
        $result["records"] = count($items);
        $result['rows'] = $items;
        $result["content"] = $content;
        $filename = "common/files/" . $table . date('YmdHis') . ".csv";
        $result['filename'] = $filename;
        $this->resultToCSV2($result, $filename);
        return $result;
        // echo json_encode($result);

    }//end getOperationData()

    /**
     * 导出lgo_infomation数据CSV文件
     *
     * @param array  $result   坏小区列表
     * @param string $filename CSV文件名
     *
     * @return void
     */
    protected function resultToCSV2($result, $filename)
    {
        $csvContent = mb_convert_encoding($result['content'] . "\n", 'gb2312', 'utf-8');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result["rows"] as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
    }

    /**
     * 获得操作类型列表
     *
     * @return array
     */
    public function getActionType()
    {
        $dbc   = new DataBaseConnection();
        $db    = $dbc->getDB('mongs', 'lgo_20170524');
        $sql   = "select distinct action_type from lgo_infomation";
        $res   = $db->query($sql);
        // $row   = $res->fetch(PDO::FETCH_ASSOC);
        $items = array();
        $arr = array();

        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            array_push($arr, $row['action_type']);
        }
        $items['action_type'] = $arr;

        return $arr;

    }//end getActionType()

    /**
     * 获得操作来源列表
     *
     * @return array
     */
    public function getActionSource()
    {
        $dbc   = new DataBaseConnection();
        $db    = $dbc->getDB('mongs', 'lgo_20170524');
        $sql   = "select distinct action_source from lgo_infomation";
        $res   = $db->query($sql);
        // $row   = $res->fetch(PDO::FETCH_ASSOC);
        $items = array();
        $arr = array();

        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            array_push($arr, $row['action_source']);
        }
        $items['action_source'] = $arr;
        
        return $arr;

    }//end getActionSource()

    /**
     * 上传文件
     *
     * @return void
     */
    public function uploadFile()
    {
        $filename = $_FILES['fileImport']['tmp_name'];
        if (empty($filename)) {
            echo '请选择要导入的文件！';
            exit;
        }

        if (file_exists("common/files/".$_FILES['fileImport']['name'])) {
            unlink("common/files/".$_FILES['fileImport']['name']);
        }

        move_uploaded_file($filename, "common/files/".$_FILES['fileImport']['name']);

        setlocale(LC_ALL, null);
        $files = file("common/files/".$_FILES['fileImport']['name']);
        $text  = array();
        foreach ($files as $txt) {
            array_push($text, $txt);
        }

        $textStr = implode(",", $text);
        print_r($textStr);

    }//end uploadFile()

    /**
     * 获得参数统计比例数据
     *
     * @return string 当页数据
     */
    public function getparamData()
    {
        $startTime = Input::get('startTime');
        $endTime = Input::get('endTime');
        $city = Input::get('city');
        switch ($city) {
        case '常州':
            $citys = 'CHANGZHOU';
            break;
        case '南通':
            $citys = 'NANTONG';
            break;
        case '苏州':
            $citys = 'SUZHOU';
            break;
        case '无锡':
            $citys = 'WUXI';
            break;
        case '镇江':
            $citys = 'ZHENJIANG';
            break;
        }
        // dump($citys);
        $action_typeArr = Input::get('action_type');
        foreach ($action_typeArr as $key => $value) {
            $action_typeArr[$key] = "'".$value."'";
        }
        $action_type = implode(",", $action_typeArr);
        // dump($action_type);
        $action_sourceArr = Input::get('action_source');
        foreach ($action_sourceArr as $key => $value) {
            $action_sourceArr[$key] = "'".$value."'";
        }
        $action_source = implode(",", $action_sourceArr);
        // dump($action_source);
        $siteStr = Input::get('site');
        $siteArr = explode(",", $siteStr);
        foreach ($siteArr as $key => $value) {
            $siteArr[$key] = "'".$value."'";
        }
        $site = implode(",", $siteArr);
        // dump($site);
        // $site = Input::get('site');
        $params = Input::get('param');
        $arrStartTime=explode(" ", $startTime);
        $strStartTime=implode("T", $arrStartTime);
        $arrEndTime=explode(" ", $endTime);
        $strEndTime=implode("T", $arrEndTime);
        $result = array();
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('mongs', 'lgo');
        $table  = 'lgo_infomation';

        if ($siteStr == "") {
            $rs  = $db->query("select count(DISTINCT site) totalCount from ".$table." where city = '".$citys."'");
            $row = $rs->fetchAll(PDO::FETCH_ASSOC);
            $num = intval($row[0]['totalCount']);
        } else {
            $num = substr_count($siteStr, ",")+1;
        }
        // dump($num);
        if ($siteStr == "") {
            if ($params == "") {
                $sql = "SELECT substring_index(SUBSTRING_INDEX(mo,',',-1),'=',1) MO1, para_name,count(para_name) 参数修改数量, count(DISTINCT site)/".$num."*100 涉及基站占比 from lgo_infomation WHERE  datetime BETWEEN '".$strStartTime."%' AND '".$strEndTime."%' and city = '".$citys."' AND action_source in (".$action_source.") AND action_type in (".$action_type.") GROUP BY para_name ORDER BY 参数修改数量 desc";
                $content = "MO名称,修改参数,参数修改数量,涉及基站占比(%)";
                // dump($sql);
            } else {
                $sql = "SELECT substring_index(SUBSTRING_INDEX(mo,',',-1),'=',1) MO1, para_name,count(para_name) 参数修改数量, count(DISTINCT site)/".$num."*100 涉及基站占比 from lgo_infomation WHERE  datetime BETWEEN '".$strStartTime."%' AND '".$strEndTime."%' and city = '".$citys."' AND action_source in (".$action_source.") AND action_type in (".$action_type.") AND para_name like '%".$params."%' GROUP BY para_name ORDER BY 参数修改数量 desc";
                $content = "MO名称,修改参数,参数修改数量,涉及基站占比(%)";
            }
        } else {
            if ($params == "") {
                $sql = "SELECT substring_index(SUBSTRING_INDEX(mo,',',-1),'=',1) MO1, para_name,count(para_name) 参数修改数量, count(DISTINCT site)/".$num."*100 涉及基站占比 from lgo_infomation WHERE  datetime BETWEEN '".$strStartTime."%' AND '".$strEndTime."%' and city = '".$citys."' AND action_source in (".$action_source.") AND action_type in (".$action_type.") AND site in (".$site.") GROUP BY para_name ORDER BY 参数修改数量 desc";
                $content = "MO名称,修改参数,参数修改数量,涉及基站占比(%)";
                // dump($sql);
            } else {
                $sql = "SELECT substring_index(SUBSTRING_INDEX(mo,',',-1),'=',1) MO1, para_name,count(para_name) 参数修改数量, count(DISTINCT site)/".$num."*100 涉及基站占比 from lgo_infomation WHERE  datetime BETWEEN '".$strStartTime."%' AND '".$strEndTime."%' and city = '".$citys."' AND action_source in (".$action_source.") AND action_type in (".$action_type.") AND site in (".$site.") AND para_name like '%".$params."%' GROUP BY para_name ORDER BY 参数修改数量 desc";
                $content = "MO名称,修改参数,参数修改数量,涉及基站占比(%)";
            }
        }
        // dump($sql);
        $items = array();
        $res = $db->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            array_push($items, $row);
        }
        $result["records"] = count($items);
        $result['rows'] = $items;
        $result["content"] = $content;
        $filename = "common/files/" . $table . date('YmdHis') . ".csv";
        $result['filename'] = $filename;
        $this->resultToCSV2($result, $filename);
        return $result;

        echo json_encode($result);

    }//end getparamData()

}
