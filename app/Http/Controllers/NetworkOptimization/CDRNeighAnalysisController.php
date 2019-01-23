<?php

/**
 * CDRNeighAnalysisController.php
 *
 * @category NetworkOptimization
 * @package  App\Http\Controllers\NetworkOptimization
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\NetworkOptimization;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use App\Models\CDR\Irat4to2;
use App\Models\Mongs\NeighOptimizationWhiteList;

/**
 * 邻区分析
 * Class CDRNeighAnalysisController
 *
 * @category NetworkOptimization
 * @package  App\Http\Controllers\NetworkOptimization
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class CDRNeighAnalysisController extends Controller
{


    /**
     * 获得邻区分析结果表头
     *
     * @return array 邻区分析结果表头
     */
    public function getCdrServeNeighDataHeader()
    {
        $dateTime = Input::get('dateTime');
        $dbname = $this->getCDRDatabase(Input::get('dataBase'));
        $result = array();
        $rs = Irat4to2::on($dbname)->where('date_id', $dateTime)->exists();
        if ($rs) {
            $keys = ['date_id', 'ecgi', 'cellName', 'cgi', 'cell', 'occurs', 'isdefined', 'distince', 'users'];
            $text = '';
            foreach ($keys as $key) {
                if ($key == 'id') {
                    continue;
                }
                $key = trans('message.CDR.' . $key);
                $text .= $key . ',';
            }

            $text = substr($text, 0, strlen($text) - 1);
            $result['text'] = $text;
            $result['field'] = "date_id,ecgi,cellName,cgi,cell,occurs,isdefined,distince,users";
            return $result;
        } else {
            $result['error'] = 'error';
            return $result;
        }

    }//end getCdrServeNeighDataHeader()


    /**
     * 获得CDR数据库名
     *
     * @param string $city 城市名
     *
     * @return string CDR数据库名
     */
    public function getCDRDatabase($city)
    {
        $dbc = new DataBaseConnection();
        return $dbc->getCDRDatabase($city);

    }//end getCDRDatabase()


    /**
     * 获得邻区分析结果(分页)
     *
     * @return mixed
     */
    public function getCdrServeNeighData_orm()
    {
        // 获取白名单
        $city = input::get("dataBase");
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

        // 查询去除白名单之后的记录
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;

        $dbname = $this->getCDRDatabase(Input::get('dataBase'));
        $dateTime = Input::get('dateTime');
        $sortBy = isset($_REQUEST['sortBy']) ? $_REQUEST['sortBy'] : "users";
        $direction = isset($_REQUEST['direction']) ? $_REQUEST['direction'] : 'desc';
        $result = array();
        $conn = Irat4to2::on($dbname)->where('date_id', $dateTime);
        if (count($whiteList) > 0) {
            $conn = $conn->whereNotIn('ecgi', $whiteList);
        }
        if ($conn->count() == 0) {
            $result['error'] = 'error';
            return json_encode($result);
        }
        $result["total"] = $conn->count();

        $rows = $conn->orderBy($sortBy, $direction)->get()->chunk($limit)->toArray()[$page - 1];
        $items = array();
        foreach ($rows as $qr) {
            array_push($items, $qr);
        }
        $result['records'] = $items;
        return json_encode($result);

    }//end getCdrServeNeighData_orm()

    /**
     * 获得邻区分析结果(分页)
     *
     * @return void
     */
    public function getCdrServeNeighData()
    {
        $dbc = new DataBaseConnection();
        // 获取白名单
        $filter   = "";
        $db       = $dbc->getDB('mongs', 'mongs');
        $city     = input::get("dataBase");
        $dataType = Input::get('dataType');
        $OptimizationType = input::get("OptimizationType");
        $sql = "select ecgi from NeighOptimizationWhiteList where OptimizationType='$OptimizationType' and dataType='$dataType' and city='$city'";
        $rs  = $db->query($sql, PDO::FETCH_ASSOC);
        if ($rs) {
            $rows = $rs->fetchAll();
            if (count($rows) > 0) {
                $whiteList = "";
                foreach ($rows as $row) {
                    $whiteList .= "'".$row['ecgi']."',";
                }

                $filter = "and ecgi not in (".substr($whiteList, 0, -1).")";
            }
        }

        // 查询去除白名单之后的记录
        $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $offset = (($page - 1) * $rows);
        $limit  = " limit $offset,$rows";

        $dbname    = $this->getCDRDatabase(Input::get('dataBase'));
        $dateTime  = Input::get('dateTime');
        $sortBy    = isset($_REQUEST['sortBy']) ? $_REQUEST['sortBy'] : "users";
        $direction = isset($_REQUEST['direction']) ? $_REQUEST['direction'] : 'desc';
        $order     = " order by $sortBy $direction ";
        $result    = array();
        $db        = $dbc->getDB('CDR', $dbname);
        $table     = 'irat4to2';

        $rs  = $db->query("select count(*) totalCount,ecgi,cgi from $table WHERE date_id = '".$dateTime."' $filter group by ecgi,cgi");
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        $result["total"] = count($row);

        $sql = "select date_id,ecgi,cellName,cgi,cell,sum(occurs) as occurs,isdefined,distince,sum(users) as users from $table WHERE date_id = '".$dateTime."' $filter group by ecgi,cgi $order".$limit;
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) == 0) {
            $result['error'] = 'error';
            return json_encode($result);
        }

        $items = array();
        foreach ($row as $qr) {
            array_push($items, $qr);
        }

        $result['records'] = $items;
        echo json_encode($result);

    }//end getCdrServeNeighData()


    /**
     * 获得CDR邻区分析结果(全量)
     *
     * @return mixed
     */
    public function getAllCdrServeNeighData_orm()
    {
        // 获取白名单
        $city = input::get("dataBase");
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

        // 查询去除白名单之后的记录
        $dbname = $this->getCDRDatabase(Input::get('dataBase'));
        $dateTime = Input::get('dateTime');

        $keys = ['date_id', 'ecgi', 'cellName', 'cgi', 'cell', 'occurs', 'isdefined', 'distince', 'users'];
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

        $conn = Irat4to2::on($dbname)->where('date_id', $dateTime);
        if (count($whiteList) > 0) {
            $conn = $conn->whereNotIn('ecgi', $whiteList);
        }
        if ($conn->count() == 0) {
            $result['error'] = 'error';
            return json_encode($result);
        }
        $result["total"] = $conn->count();

        $rows = $conn->orderBy('users', 'desc')->get($keys)->toArray();
        $result['rows'] = $rows;
        $result['result'] = 'true';

        $filename = "common/files/" . $dbname . "_Irat4to2_" . date('YmdHis') . ".csv";
        $this->resultToCSV2($result, $filename);
        $result['filename'] = $filename;
        if (count($rows) > 1000) {
            $result['rows'] = array_slice($result, 0, 1000);
        }

        return json_encode($result);

    }//end getAllCdrServeNeighData_orm()

    /**
     * 获得CDR邻区分析结果(全量)
     *
     * @return void
     */
    public function getAllCdrServeNeighData()
    {
        $dbc = new DataBaseConnection();
        // 获取白名单
        $filter   = "";
        $db       = $dbc->getDB('mongs', 'mongs');
        $city     = input::get("dataBase");
        $dataType = Input::get('dataType');
        $OptimizationType = input::get("OptimizationType");
        $sql = "select ecgi from NeighOptimizationWhiteList where OptimizationType='$OptimizationType' and dataType='$dataType' and city='$city'";
        $rs  = $db->query($sql, PDO::FETCH_ASSOC);
        if ($rs) {
            $rows = $rs->fetchAll();
            if (count($rows) > 0) {
                $whiteList = "";
                foreach ($rows as $row) {
                    $whiteList .= "'".$row['ecgi']."',";
                }

                $filter = "and ecgi not in (".substr($whiteList, 0, -1).")";
            }
        }

        // 查询去除白名单之后的记录
        $dbname   = $this->getCDRDatabase(Input::get('dataBase'));
        $dateTime = Input::get('dateTime');
        $db       = $dbc->getDB('CDR', $dbname);
        $table    = 'irat4to2';

        $sql = "select date_id,ecgi,cellName,cgi,cell,occurs,isdefined,distince,users from $table WHERE date_id = '".$dateTime."' $filter limit 1";
        $rs  = $db->query($sql, PDO::FETCH_ASSOC);
        if ($rs) {
            $rows = $rs->fetchall();
            if (count($rows) > 0) {
                $keys = array_keys($rows[0]);
            } else {
                $result['error'] = 'error';
                return json_encode($result);
            }
        } else {
            $result['error'] = 'error';
            return json_encode($result);
        }

        $text = '';
        foreach ($keys as $key) {
            if ($key == 'id') {
                continue;
            }
            $key = trans('message.CDR.'.$key);
            $text .= $key.',';
        }

        $text           = substr($text, 0, (strlen($text) - 1));
        $result['text'] = $text;

        $sql = "select date_id,ecgi,cellName,cgi,cell,sum(occurs) as occurs,isdefined,distince,sum(users) as users from $table WHERE date_id = '".$dateTime."' $filter group by ecgi,cgi";
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) == 0) {
            $result['error'] = 'error';
            return json_encode($result);
        }

        $items = array();
        foreach ($row as $qr) {
            array_push($items, $qr);
        }

        $result['rows']   = $items;
        $result['total']  = count($items);
        $result['result'] = 'true';

        $filename = "files/".$dbname."_".$table."_".date('YmdHis').".csv";
        $this->resultToCSV2($result, $filename);
        $result['filename'] = $filename;
        if (count($items) > 1000) {
            $result['rows'] = array_slice($items, 0, 1000);
        }

        echo json_encode($result);

    }//end getAllCdrServeNeighData()


    /**
     * 写入CSV文件
     *
     * @param array $result 检查结果
     * 
     * @param string $filename CSV文件名
     *
     * @return void
     */
    protected function resultToCSV2($result, $filename)
    {
        $csvContent = mb_convert_encoding($result['text'] . "\n", 'GBK');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result['rows'] as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

    }//end resultToCSV2()


    /**
     * 获取城市列表
     *
     * @return string 城市列表
     */
    public function getAllCity()
    {
        $dbc = new DataBaseConnection();
        return $dbc->getCityOptions();

    }//end getAllCity()


    /**
     * 获得日期列表(天)
     *
     * @return array 日期列表(天)
     */
    public function getfdfda()
    {
        $dbname = $this->getMRDatabase(Input::get('city'));
        $dbname = str_replace('MR', 'CDR', $dbname);
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CDR', $dbname);
        $sql = "show databases ";
        $rs = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $flag = 0;
        foreach ($rs as $database) {
            if ($dbname === $database['DATABASE']) {
                $flag = 1;
            }
        }

        if ($flag == 0) {
            $result['error'] = 'error';
            return $result;
        } else {
            $dbc = new DataBaseConnection();
            $db = $dbc->getDB('CDR', $dbname);
            $table = 'irat4to2';
            $sql = "select distinct date_id from $table";
            $rs = $db->query($sql, PDO::FETCH_ASSOC);
            $test = [];
            if ($rs) {
                $rows = $rs->fetchall();
                if (count($rows) > 0) {
                    foreach ($rows as $row) {
                        $arr = explode(' ', $row['date_id']);
                        if ($arr[0] == '0000-00-00') {
                            continue;
                        }

                        array_push($test, $arr[0]);
                    }

                    return $test;
                } else {
                    $result['error'] = 'error';
                    return $result;
                }
            } else {
                $result['error'] = 'error';
                return $result;
            }//end if
        }//end if

    }//end getfdfda()


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


}//end class
