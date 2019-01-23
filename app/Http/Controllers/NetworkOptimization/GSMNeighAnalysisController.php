<?php
/**
 * GSMNeighAnalysisController.php
 *
 * @category NetworkOptimization
 * @package  App\Http\Controllers\NetworkOptimization
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\NetworkOptimization;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Utils\FileUtil;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Common\MyRedis;
use PDO;
use App\Models\MR\MreServerNeighIrat_day;
use App\Models\MR\MreServerNeighIrat4to3_day;
use App\Models\MR\MreServeNeigh_day;
use App\Models\Mongs\NeighOptimizationWhiteList;
use App\Models\TABLES;

/**
 * GSM閭诲尯鍒嗘瀽
 * Class GSMNeighAnalysisController
 *
 * @category NetworkOptimization
 * @package  App\Http\Controllers\NetworkOptimization
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 * @package App\Http\Controllers\NetworkOptimization
 */
class GSMNeighAnalysisController extends MyRedis
{


    /**
     * 获得GSM邻区分析结果(分页)
     *
     * @return array GSM邻区分析结果(分页)
     */
    public function getGSMNeighData()
    {
        $dateTime = Input::get('dateTime');
        $dbname = $this->getMRDatabase(Input::get('select'));
        $result = array();
        $rows = MreServerNeighIrat_day::on($dbname)->where('dateId', $dateTime)->exists();
        if ($rows) {
            $keys = ['dateId', 'ecgi', 'cellName', 'cell', 'cgi', 'isdefined', 'distance','mr_LteNcEarfcn','mr_LteNcPci', 'sc_session_num', 'nc_session_num', 'nc_session_ratio', 'sc_times_num', 'nc_times_num', 'nc_times_ratio', 'avg_mr_LteScRSRP', 'avg_mr_LteScRSRQ', 'avg_mr_GsmNcellCarrierRSSI'];
            $text = '';
            foreach ($keys as $key) {
                if ($key == 'id') {
                    continue;
                }
                $key = trans('message.Mre.' . $key);
                $text .= $key . ',';
            }

            $text = substr($text, 0, strlen($text) - 1);
            $result['text'] = $text;
            $result['field'] = "dateId,ecgi,cellName,cell,cgi,isdefined,distance,mr_LteNcEarfcn,mr_LteNcPci,sc_session_num,nc_session_num,nc_session_ratio,sc_times_num,nc_times_num,nc_times_ratio,avg_mr_LteScRSRP,avg_mr_LteScRSRQ,avg_mr_GsmNcellCarrierRSSI";
            return $result;
        } else {
            $result['error'] = 'error';
            return $result;
        }
    }//end getGSMNeighData()

    /**
     * 获得MR数据库名
     *
     * @param string $city 城市名
     *
     * @return string 数据库名
     */
    public function getMRDatabase($city)
    {
        $dbc = new DataBaseConnection();
        return $dbc->getMRDatabase($city);
    }//end getMRDatabase()

    /**
     * 获得GSM邻区分析结果(白名单)
     *
     * @return string GSM邻区分析结果(JSON)
     */
    public function getGSMNeighDataSplit()
    {
        //获取白名单
        $city = input::get("select");
        $dataType = Input::get('dataType');
        $OptimizationType = input::get("OptimizationType");
        $rows = NeighOptimizationWhiteList::where('OptimizationType', $OptimizationType)->where('dataType', $dataType)->where('city', $city)->get();
        $whiteList = [];
        if ($rows) {
            $rows = $rows->toArray();
            foreach ($rows as $row) {
                array_push($whiteList, $row['ecgi']);
            }
        }

        //查询去除白名单之后的记录
        // $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;

        $input1 = Input::get('input1');
        $input2 = Input::get('input2');
        $input3 = Input::get('input3');
        $input4 = Input::get('input4');
        $input5 = Input::get('input5');
        $input6 = Input::get('input6');
        $input7 = Input::get('input7');
        $input8 = Input::get('input8');
        $input9 = Input::get('input9');
        $dbname = $this->getMRDatabase(Input::get('select'));
        $dateTime = Input::get('dateTime');
        $return = array();

        $rs = MreServerNeighIrat_day::on($dbname)
            ->where('nc_session_ratio', '>=', $input1)
            ->where('avg_mr_GsmNcellCarrierRSSI', '>=', $input6)
            ->where('avg_mr_LteScRSRQ', '>=', $input7)
            ->where(function ($query) 
            {
                $query->whereNull('isdefined')->orWhere('isdefined','!=',1);
            }
            )
            ->where('dateId', $dateTime);
        if (count($whiteList) > 0) {
            $rs = $rs->whereNotIn('ecgi', $whiteList);
        }
        if ($rs->count() == 0) {
            $result['error'] = 'error';
            return json_encode($result);
        }
        $rows = $rs->orderBy('ecgi')->paginate($limit)->toArray();
        $return["total"] = $rows['total'];
        $return['records'] = $rows['data'];
        return json_encode($return);
    }//end getGSMNeighDataSplit()

    /**
     * 获得LTE邻区分析结果(白名单)
     *
     * @return string LTE邻区分析结果(JSON)
     */
    public function getLTENeighDataSplit()
    {
        //获取白名单
        $city = input::get("select");
        $dataType = Input::get('dataType');
        $OptimizationType = input::get("OptimizationType");

        $rows = NeighOptimizationWhiteList::where('OptimizationType', $OptimizationType)->where('dataType', $dataType)->where('city', $city)->get();
        $whiteList = [];
        if ($rows) {
            $rows = $rows->toArray();
            foreach ($rows as $row) {
                array_push($whiteList, $row['ecgi']);
            }
        }
        //查询去除白名单之后的记录
        // $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;

        $input1 = Input::get('input1');
        $input2 = Input::get('input2');
        $input3 = Input::get('input3');
        $input4 = Input::get('input4');
        $input5 = Input::get('input5');
        $input6 = Input::get('input6');
        $input7 = Input::get('input7');
        $input8 = Input::get('input8');
        $dbname = $this->getMRDatabase(Input::get('select'));
        $dateTime = Input::get('dateTime');

        $result = array();
        $return = array();

        $rs = MreServeNeigh_day::on($dbname)
            ->whereRaw('mr_LteScEarfcn != mr_LteNcEarfcn')
            ->where('nc_session_ratio', '>=', $input1)
            ->where('avg_mr_LteScRSRP', '>=', $input6)
            ->where('avg_mr_LteScRSRQ', '>=', $input7)
            ->where('avg_mr_LteNcRSRP', '>=', $input8)
            ->where('isdefined_direct', '!=', 1)
            ->where('dateId', $dateTime);
        if (count($whiteList) > 0) {
            $rs = $rs->whereNotIn('ecgi', $whiteList);
        }
        if ($rs->count() == 0) {
            $result['error'] = 'error';
            return json_encode($result);
        }
        $rows = $rs->orderBy('ecgi')->paginate($limit)->toArray();
        $return["total"] = $rows['total'];
        $return['records'] = $rows['data'];

        return json_encode($return);
    }//end resultToCSV2()

    /**
     * 获得LTE邻区分析结果
     *
     * @return array LTE邻区分析结果
     */
    public function getLTENeighData()
    {
        $dateTime = Input::get('dateTime');
        $dbname = $this->getMRDatabase(Input::get('select'));
        $result = array();
        $rs = MreServeNeigh_day::on($dbname)->where('dateId', $dateTime);
        if ($rs->exists()) {
            $keys = ['dateId', 'ecgi', 'ecgiNeigh_direct', 'isdefined_direct', 'distance_direct', 'mr_LteNcEarfcn','mr_LteNcPci','ncFreq_session', 'nc_session_num', 'nc_session_ratio', 'ncFreq_times_num', 'nc_times_num', 'nc_times_ratio', 'avg_mr_LteScRSRP', 'avg_mr_LteScRSRQ', 'avg_mr_LteNcRSRP', 'avg_mr_LteNcRSRQ'];
            $text = '';
            foreach ($keys as $key) {
                if ($key == 'id') {
                    continue;
                }
                $key = trans('message.GSM.' . $key);
                $text .= $key . ',';
            }

            $text = substr($text, 0, strlen($text) - 1);
            $result['text'] = $text;
            $result['field'] = "dateId,ecgi,ecgiNeigh_direct,isdefined_direct,distance_direct,mr_LteNcEarfcn,mr_LteNcPci,ncFreq_session,nc_session_num,nc_session_ratio,ncFreq_times_num,nc_times_num,nc_times_ratio,avg_mr_LteScRSRP,avg_mr_LteScRSRQ,avg_mr_LteNcRSRP,avg_mr_LteNcRSRQ";
            return $result;
        } else {
            $result['error'] = 'error';
            return $result;
        }
    }//end getLTENeighDataSplit()

    /**
     * 获得邻区分析结果(LTE,GSM)
     *
     * @return array 邻区分析结果
     */
    public function getGSMNeighDataLteAll()
    {
        $dbname = $this->getMRDatabase(Input::get('select'));
        $dateTime = Input::get('dateTime');
        $input1 = Input::get('input1');
        $input6 = Input::get('input6');
        $input7 = Input::get('input7');
        $input8 = Input::get('input8');
        $result = array();
        $return = array();
        $rs = MreServeNeigh_day::on($dbname);
        if ($rs->exists()) {
            $keys = ['id', 'dateId', 'ecgi', 'ecgiNeigh_direct', 'isdefined_direct', 'distance_direct', 'mr_LteNcEarfcn','mr_LteNcPci','ncFreq_session', 'nc_session_num', 'nc_session_ratio', 'ncFreq_times_num', 'nc_times_num', 'nc_times_ratio', 'avg_mr_LteScRSRP', 'avg_mr_LteScRSRQ', 'avg_mr_LteNcRSRP', 'avg_mr_LteNcRSRQ'];
        } else {
            $result['error'] = 'error';
            return $result;
        }
        $text = '';
        foreach ($keys as $key) {
            if ($key == 'id') {
                continue;
            }
            $key = trans('message.GSM.' . $key);
            $text .= $key . ',';
        }
        $text = substr($text, 0, strlen($text) - 1);
        $result['text'] = $text;

        $rows = MreServeNeigh_day::on($dbname)
            ->whereRaw('mr_LteScEarfcn != mr_LteNcEarfcn')
            ->where('nc_session_ratio', '>=', $input1)
            ->where('avg_mr_LteScRSRP', '>=', $input6)
            ->where('avg_mr_LteScRSRQ', '>=', $input7)
            ->where('avg_mr_LteNcRSRP', '>=', $input8)
            ->where('isdefined_direct', '!=', 1)
            ->where('dateId', $dateTime)
            ->get($keys)
            ->toArray();

        $csvContent = mb_convert_encoding($result['text'] . "\n", 'gb2312', 'utf-8');
        $filename = "common/files/GSMNeighborAnalysisLteAll" . date('YmdHis') . ".csv";
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($rows as $row) {
            array_shift($row);
            fputcsv($fp, $row);
        }
        fclose($fp);
        $return['filename'] = $filename;
        return $return;
    }//end getLTENeighData()

    /**
     * 导出全量GSM邻区分析结果
     *
     * @return array 全量GSM邻区分析结果
     */
    public function getGSMNeighDataAll()
    {
        $dbname = $this->getMRDatabase(Input::get('select'));
        $dateTime = Input::get('dateTime');
        $input1 = Input::get('input1');
        $input6 = Input::get('input6');
        $input7 = Input::get('input7');
        $result = array();
        $return = array();

        $keys = ['id', 'dateId', 'ecgi', 'cellName', 'cell', 'cgi', 'isdefined', 'distance', 'mr_LteNcEarfcn','mr_LteNcPci','sc_session_num', 'nc_session_num', 'nc_session_ratio', 'sc_times_num', 'nc_times_num', 'nc_times_ratio', 'avg_mr_LteScRSRP', 'avg_mr_LteScRSRQ', 'avg_mr_GsmNcellCarrierRSSI'];
        $text = '';
        foreach ($keys as $key) {
            if ($key == 'id') {
                continue;
            }
            $key = trans('message.Mre.' . $key);
            $text .= $key . ',';
        }
        $text = substr($text, 0, strlen($text) - 1);
        $result['text'] = $text;

        $rows = MreServerNeighIrat_day::on($dbname)
            ->where('nc_session_ratio', '>=', $input1)
            ->where('avg_mr_GsmNcellCarrierRSSI', '>=', $input6)
            ->where('avg_mr_LteScRSRQ', '>=', $input7)
            ->where(function ($query) 
            {
                $query->where('isdefined', '!=', 1)
                    ->orWhere(function ($query) 
                    {
                        $query->whereNull('isdefined');
                    });
            }
            )
            ->where('dateId', $dateTime)
            ->orderBy('ecgi')
            ->get($keys)
            ->toArray();

        $csvContent = mb_convert_encoding($result['text'] . "\n", 'gb2312', 'utf-8');
        $filename = "common/files/GSMNeighborAnalysis" . date('YmdHis') . ".csv";
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($rows as $row) {
            array_shift($row);
            fputcsv($fp, $row);
        }
        fclose($fp);

        $return['filename'] = $filename;
        return $return;
    }//end getGSMNeighDataLteAll()

    /**
     * 获取城市列表
     *
     * @return string 城市列表(JSON)
     */
    public function getAllCity()
    {
        $dbc = new DataBaseConnection();
        return $dbc->getCityOptions();
    }//end resultToCSV2_All()

    function check_input($value)
    {
        //去除斜杠
        if(get_magic_quotes_gpc())
        {
            $value=stripslashes($value);
        }
        return $value;
    }

    /**
     * 获得日期列表(天)
     *
     * @return array 日期列表(天)
     */
    public function getfdfd()
    {
        $city = Input::get('city');
        $city = $this->check_input($city);
        $dbname = $this->getMRDatabase($city);
        $type = Input::get('type');
        $type = $this->check_input($type);
        if ($type == '2G') {
            $dbc = new DataBaseConnection();
            $db = $dbc->getDB('MR', $dbname);
            $table = 'mreServerNeighIrat_day';
            $sql = "select distinct dateId from $table";
        } else if ($type == '3G') {
            $dbc = new DataBaseConnection();
            $db = $dbc->getDB('MR', $dbname);
            $table = 'mreServerNeighIrat4to3_day';
            $sql = "select distinct dateId from $table";
            // $dbname = str_replace('MR', 'CDR', $dbname);
            // $dbc = new DataBaseConnection();
            // $db = $dbc->getDB('CDR', $dbname);
            // $table = 'irat4to2';
            // $sql = "select distinct date_id from $table";
        }
        $this->type = $dbname . ':GSMNeighborAnalysis:' . $table;
        return $this->getValue($db, $sql);

    }//end getGSMNeighDataAll()

    /**
     * 获取文件内容并入库
     *
     * @return string 执行结果 true:成功 false:失败
     */
    public function getMREFileContent()
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        $table = input::get("table");
        $city = input::get("city");
        $dataType = Input::get('dataType');
        $OptimizationType = input::get("OptimizationType");
        $fileName = Input::get('fileName');
        $fileUtil = new FileUtil();
        $result = $fileUtil->parseFile($fileName);
        $len_result = count($result);
        if ($len_result == 0) {
            echo '没有任何数据！';
            exit;
        }
        $sql = "delete from $table where OptimizationType = :OptimizationType and dataType=:dataType and city =:city";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':OptimizationType', $OptimizationType);
        $stmt->bindParam(':dataType', $dataType);
        $stmt->bindParam(':city', $city);
        $stmt->execute();
        $data_values = '';
        for ($i = 1; $i < $len_result; $i++) {
            $subNetwork = $result[$i][0];
            $site = $result[$i][1];
            $EUtranCellTDD = $result[$i][2];
            $ecgi = $result[$i][3];
            $data_values .= "('$subNetwork','$site','$EUtranCellTDD','$ecgi','$city','$dataType','$OptimizationType'),";

        }
        if ($len_result == 1) {
            $subNetwork = 'NULL';
            $site = 'NULL';
            $EUtranCellTDD = 'NULL';
            $ecgi = 'NULL';
            $data_values .= "('$subNetwork','$site','$EUtranCellTDD','$ecgi','$city','$dataType','$OptimizationType'),";
        }
        $data_values = substr($data_values, 0, -1);//去掉最后一个逗号
        $sql = "insert into $table (subNetwork,site,EUtranCellTDD,ecgi,city,dataType,OptimizationType) values $data_values";
        $query = $db->query($sql);
        if ($query) {
            return "true";
        } else {
            return "false";
        }
    }//end getAllCity()

    /**
     * 导出白名单
     *
     * @return array 导出结果
     */
    public function exportWhiteList()
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        $table = 'NeighOptimizationWhiteList';
        $city = input::get("city");
        $dataType = Input::get('dataType');
        $OptimizationType = input::get("OptimizationType");

        $fileName = "files/" . $OptimizationType . "_" . $dataType . "_whiteList.csv";
        $result = array();
        $filter = " where OptimizationType=:OptimizationType and dataType=:dataType and city=:city";
        $sqlCount = "select count(*) from " . $table . $filter;
        $stmt = $db->prepare($sqlCount);
        $paramArr = array(':OptimizationType' => $OptimizationType, ':dataType' => $dataType, ':city' => $city);
        if ($stmt->execute($paramArr)) {
            $result["total"] = $stmt->fetchColumn();
        }
        $sql = "select subNetwork,site,EUtranCellTDD,ecgi from $table $filter";
        $stmt = $db->prepare($sql);
        if ($stmt->execute($paramArr)) {
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
    }//end getfdfd()

    /**
     * 获得日期列表(天)
     *
     * @return array 日期列表(天)
     */
    public function getLTENeighborAnalysisDate()
    {
        $city = Input::get('city');
        $city = $this->check_input($city);
        $dbname = $this->getMRDatabase($city);
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('MR', $dbname);
        $table = 'mreServeNeigh_day';
        $sql = "select distinct dateId from $table";
        $this->type = $dbname . ':LTENeighborAnalysis';
        return $this->getValue($db, $sql);
    }//end getMREFileContent()
    /**
     * 写入CSV文件
     *
     * @param array $result 分析结果
     * 
     * @param string $filename CSV文件名
     *
     * @return void
     */
    protected function resultToCSV2($result, $filename)
    {
        $csvContent = mb_convert_encoding($result['text'] . "\n", 'gb2312', 'utf-8');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result['rows'] as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
    }//end exportWhiteList()

    /**
     * 写入CSV文件
     *
     * @param array $result 分析结果
     * 
     * @param string $filename CSV文件名
     * 
     * @param mixed $db 数据库连接句柄
     * 
     * @param string $sql SQL字串
     *
     * @param array $paramArr 数据数组
     *
     * @return void
     */
    protected function resultToCSV2_All($result, $filename, $db, $sql, $paramArr)
    {
        $csvContent = mb_convert_encoding($result['text'] . "\n", 'gb2312', 'utf-8');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        $stmt = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->execute($paramArr);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            array_shift($row);
            fputcsv($fp, $row);
        }
        fclose($fp);
    }//end getLTENeighborAnalysisDate()

    /**
     * 获得GSM邻区分析结果(分页)
     *
     * @return array GSM邻区分析结果(分页)
     */
    public function getGSMNeighData_3G()
    {
        $dateTime = Input::get('dateTime');
        $dbname = $this->getMRDatabase(Input::get('select'));
        $result = array();
        $rows = TABLES::on($dbname)->where('table_name','mreServerNeighIrat4to3_day')->exists();
        if ($rows) {
            $keys = ['dateId', 'ecgi', 'cellName', 'mr_LteScEarfcn','mr_LteNcPci','eventType', 'mr_utraCpichRSCP', 'mr_utraCellParameterId', 'cell', 'PSC', 'cid', 'isdefined', 'distance', 'sc_session_num', 'nc_session_num', 'nc_session_ratio', 'sc_times_num','nc_times_num','nc_times_ratio','avg_mr_LteScRSRP','avg_mr_LteScRSRQ'];
            $text = '';
            foreach ($keys as $key) {
                if ($key == 'id') {
                    continue;
                }
                $key = trans('message.Mre.' . $key);
                $text .= $key . ',';
            }

            $text = substr($text, 0, strlen($text) - 1);
            $result['text'] = $text;
            $result['field'] = "dateId,ecgi,cellName,mr_LteScEarfcn,mr_LteNcPci,eventType,mr_utraCpichRSCP,mr_utraCellParameterId,cell,PSC,cid,isdefined,distance,sc_session_num,nc_session_num,nc_session_ratio,sc_times_num,nc_times_num,nc_times_ratio,avg_mr_LteScRSRP,avg_mr_LteScRSRQ";
            return $result;
        } else {
            $result['error'] = 'error';
            return $result;
        }
    }//end getGSMNeighData_3G()

    /**
     * 获得GSM邻区分析结果(白名单)
     *
     * @return string GSM邻区分析结果(JSON)
     */
    public function getGSMNeighDataSplit_3G()
    {
        $city = input::get("select");
        $dataType = Input::get('dataType');
        $OptimizationType = input::get("OptimizationType");
        // $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;

        $dbname = $this->getMRDatabase(Input::get('select'));
        $dateTime = Input::get('dateTime');
        $return = array();

        $rs = MreServerNeighIrat4to3_day::on($dbname)->where('dateId', $dateTime);
        if ($rs->count() == 0) {
            $result['error'] = 'error';
            return json_encode($result);
        }
        $rows = $rs->paginate($limit)->toArray();
        $return["total"] = $rows['total'];
        $return['records'] = $rows['data'];
        return json_encode($return);
    }//end getGSMNeighDataSplit_3G()

    /**
     * 导出全量GSM邻区分析结果
     *
     * @return array 全量GSM邻区分析结果
     */
    public function getGSMNeighDataAll_3G()
    {
        $dbname = $this->getMRDatabase(Input::get('select'));
        $dateTime = Input::get('dateTime');
        $result = array();
        $return = array();

        $keys = ['dateId', 'ecgi', 'cellName', 'mr_LteScEarfcn','mr_LteNcPci', 'eventType', 'mr_utraCpichRSCP', 'mr_utraCellParameterId', 'cell', 'PSC', 'cid', 'isdefined', 'distance', 'sc_session_num', 'nc_session_num', 'nc_session_ratio', 'sc_times_num','nc_times_num','nc_times_ratio','avg_mr_LteScRSRP','avg_mr_LteScRSRQ'];
        $text = '';
        foreach ($keys as $key) {
            if ($key == 'id') {
                continue;
            }
            $key = trans('message.Mre.' . $key);
            $text .= $key . ',';
        }
        $text = substr($text, 0, strlen($text) - 1);
        $result['text'] = $text;

        $rows = MreServerNeighIrat4to3_day::on($dbname)->where('dateId', $dateTime)->get($keys)->toArray();

        $csvContent = mb_convert_encoding($result['text'] . "\n", 'gb2312', 'utf-8');
        $filename = "common/files/GSMNeighborAnalysis_3G" . date('YmdHis') . ".csv";
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($rows as $row) {
            array_shift($row);
            fputcsv($fp, $row);
        }
        fclose($fp);

        $return['filename'] = $filename;
        return $return;
    }//end getGSMNeighDataAll_3G()

}//end class

