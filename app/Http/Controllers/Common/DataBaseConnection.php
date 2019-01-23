<?php

/**
 * DataBaseConnection.php
 *
 * @category Common
 * @package  App\Http\Controllers\Common
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\Common;

use App\DatabaseConn;
use App\Task;
use Config;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use MongoClient;
use PDO;
use exception;

/**
 * 工具类
 * Class DataBaseConnection
 *
 * @category Common
 * @package  App\Http\Controllers\Common
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class DataBaseConnection
{


    /**
     * 获取城市--汉字转拼音
     *
     * @param array $citys 城市
     *
     * @return array 罗马字符城市名列表
     */
    public function getConnCity($citys)
    {
        $db = $this->getDB('mongs', 'mongs');
        $cityArr = array();
        foreach ($citys as $city) {
            $sql = "select connName from databaseconn where cityChinese='" . $city . "'";
            $rows = $db->query($sql)->fetchall();
            foreach ($rows as $row) {
                $cityStr = $row['connName'];
                array_push($cityArr, $cityStr);
            }
        }

        return $cityArr;

    }//end getConnCity()

    public function getConnCityHW($citys)
    {
        $db = $this->getDB('mongs', 'mongs');
        $cityArr = array();
        foreach ($citys as $city) {
            $sql = "select connName from databaseconn_HW where cityChinese='" . $city . "'";
            $rows = $db->query($sql)->fetchall();
            foreach ($rows as $row) {
                $cityStr = $row['connName'];
                array_push($cityArr, $cityStr);
            }
        }

        return $cityArr;

    }//end getConnCity()


    /**
     * 获取数据库连接句柄
     *
     * @param string $db 数据库名
     * 
     * @param null $dbName 数据库名
     *
     * @return PDO
     */
     public function getDB($db, $dbName = null)
    {

        if($dbName != null){
            Config::set("database.connections.$db.database",$dbName);
        }
        return DB::connection($db)->getPdo();

        // return $pdo;

    }//end getDB()

    public function getPGSQL($db, $dbName = null)
    {
        $config = Config::get("database.connections.$db");
        if ($dbName === null) {
            $dsn = "pgsql:host=" . $config['host'] . ";port=" . $config['port'] . ";dbname=".$config['database'];
        } else {
            $dsn = "pgsql:host=" . $config['host'] . ";port=" . $config['port'] . ";dbname=$dbName";
        }
        
        try {
            $pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                array(
                    PDO::ATTR_TIMEOUT => 3,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    // PDO::MYSQL_ATTR_LOCAL_INFILE => true
                )
            );
        }catch(exception $e) {
            echo 'Connection failed: ' . $e->getMessage();
            exit;
        }

        return $pdo;

    }

    public function getPGToCHName($pg)
    {
        $CHCity = '';
        switch ($pg) {
        case 'pgsql_cz':
            $CHCity = '常州';
            break;
        case 'pgsql_wx':
            $CHCity = '无锡';
            break;
        case 'pgsql_zj':
            $CHCity = '镇江';
            break;
        case 'pgsql_nt':
            $CHCity = '南通';
            break;
        case 'pgsql_sz':
            $CHCity = '苏州';
            break;
        }
        return $CHCity;
    }



    /**
     * 获取下拉列表--城市
     *
     * @return string 城市列表
     */
    public function getCityOptions()
    {
        $databaseConns = DB::select('select cityChinese from databaseconn group by cityChinese');
        $items = array();
        foreach ($databaseConns as $databaseConn) {
            $city = '{"text":"' . $databaseConn->cityChinese . '","value":"' . $databaseConn->cityChinese . '"}';
            array_push($items, $city);
        }

        return response()->json($items);

    }//end getCityOptions()

    /**
     * 获取下拉列表--城市
     *
     * @return string 城市列表
     */
    public function getCityGSMOptions()
    {
        $databaseConns = DB::select('select cityChinese from databaseconn_2G group by cityChinese');
        $items = array();
        foreach ($databaseConns as $databaseConn) {
            $city = '{"text":"' . $databaseConn->cityChinese . '","value":"' . $databaseConn->cityChinese . '"}';
            array_push($items, $city);
        }

        return response()->json($items);

    }//end getCityOptions()

    /**
     * 获取下拉列表--城市
     *
     * @return string 城市列表
     */
    public function getCityOptions_HW()
    {
        $databaseConns = DB::select('select cityChinese from databaseconn_HW group by cityChinese');
        $items = array();
        foreach ($databaseConns as $databaseConn) {
            $city = '{"text":"' . $databaseConn->cityChinese . '","value":"' . $databaseConn->cityChinese . '"}';
            array_push($items, $city);
        }

        return response()->json($items);

    }//end getCityOptions()


    /**
     * 获取数据库连接名称列表
     *
     * @return mixed 数据库连接名称列表
     */
    public function getCityCategories()
    {
        $databaseConns = DB::select(
            "select case cityChinese
                        when '常州' then 'changzhou'
                        when '南通' then 'nantong'
                        when '无锡' then 'wuxi'
                        when '苏州' then 'suzhou'
                        when '镇江' then 'zhenjiang'
                        end as category
                        from databaseconn group by cityChinese"
        );
        return $databaseConns;

    }//end getCityCategories()


    /**
     * 获得城市连接信息
     *
     * @param string $cityChinese 中文城市名
     *
     * @return array
     */
    public function getCityByCityChinese($cityChinese)
    {
         $pdo = $this->getDB('mongs', 'mongs');
        $rs = array();
        $sql = "select cityName as connName from city where cityNameChinese=:cityChinese";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':cityChinese', $cityChinese);
        if ($stmt->execute()) {
            $rs = $stmt->fetchall(PDO::FETCH_OBJ);
        }
        return $rs;
    }//end getCityByCityChinese()


    /**
     * 获得子网列表
     *
     * @return array 子网列表
     */
    public function getCitySubNetCategories()
    {
        $sql = "select connName,GROUP_CONCAT(if(subNetworkNbiot !='' ,if(subNetworkFDD != '',CONCAT(subNetwork,',',subNetworkFDD,',',subNetworkNbiot),CONCAT(subNetwork,',',subNetworkFDD)),subNetwork)) 
            subNetwork from databaseconn group by cityChinese";
        $rs = DB::select($sql);
        foreach ($rs as $row) {
            if (substr($row->connName, -1, 1) == '1') {
                $row->connName = substr($rs[0]->connName, 0, (strlen($rs[0]->connName) - 1));
            }
        }

        return $rs;

    }//end getCitySubNetCategories()


    /**
     * 获取城市子网
     *
     * @param string $city 城市
     *
     * @return string 子网
     */
    public function getSubNets($city)
    {

        $pdo = $this->getDB('mongs', 'mongs');
        $sql = "select TRIM(BOTH ',' FROM CONCAT_WS(',',subNetwork,subNetworkFdd,subNetworkNbiot)) subNetwork from databaseconn where cityChinese = :cityChinese";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':cityChinese', $city);
        if ($stmt->execute()) {
            $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
            $subNetworkStr = '';
            foreach ($rows as $row) {
                $subNetworkStr .= $row->subNetwork . ',';
            }
            $subNetworkStr = substr($subNetworkStr, 0, -1);
        }
        return $this->reCombine($subNetworkStr);

    }//end getSubNets()


    /**
     * 重新拼接子网 供in 查询
     *
     * @param string $subNetwork 子网字符串
     *
     * @return string
     */
    public function reCombine($subNetwork)
    {

        $subNetArr = explode(",", $subNetwork);
        $subNetworkArr = array();
        foreach ($subNetArr as $subNet) {
            if (array_search($subNet, $subNetworkArr) === false) {
                array_push($subNetworkArr, $subNet);
            }
        }

        $subNetsStr = '';
        foreach ($subNetworkArr as $subNet) {
            $subNetsStr .= "'" . $subNet . "',";
        }

        return substr($subNetsStr, 0, -1);

    }//end reCombine()


    /**
     * 获取MR数据库名
     *
     * @param string $cityChinese 中文城市名
     *
     * @return string MR数据库名
     */
    public function getMRDatabase($cityChinese)
    {
        $database = '';
        switch ($cityChinese) {
        case '常州':
            $database = 'MR_CZ';
            break;
        case '南通':
            $database = 'MR_NT';
            break;
        case '苏州':
            $database = 'MR_SZ';
            break;
        case '无锡':
            $database = 'MR_WX';
            break;
        case '镇江':
            $database = 'MR_ZJ';
            break;
        }

        return $database;

    }//end getMRDatabase()

    /**
     * 获得城市名
     *
     * @param string $MR MR数据库名
     *
     * @return string 城市名和MR数据库名映射
     */
    public function getMRToCity($MR)
    {
        $city = '';
        switch ($MR) {
        case 'MR_CZ':
            $city = 'changzhou';
            break;
        case 'MR_WX':
            $city = 'wuxi';
            break;
        case 'MR_ZJ':
            $city = 'zhenjiang';
            break;
        case 'MR_NT':
            $city = 'nantong';
            break;
        case 'MR_SZ':
            $city = 'suzhou';
            break;
        }

        return $city;

    }//end getMRToCity()
    /**
     * 获得CDR数据库名
     *
     * @param string $cityChinese 中文城市名
     *
     * @return string 数据库名
     */
    public function getCDRDatabase($cityChinese)
    {
        $database = '';
        switch ($cityChinese) {
        case '常州':
            $database = 'CDR_CZ';
            break;
        case '南通':
            $database = 'CDR_NT';
            break;
        case '苏州':
            $database = 'CDR_SZ';
            break;
        case '无锡':
            $database = 'CDR_WX';
            break;
        case '镇江':
            $database = 'CDR_ZJ';
            break;
        }

        return $database;

    }//end getCDRDatabase()


    /**
     * 获得MR数据库名
     *
     * @param string $city 英文城市名
     *
     * @return string MR数据库名
     */
    public function getMRDatabaseByCity($city)
    {
        $database = '';
        switch ($city) {
        case 'changzhou':
            $database = 'MR_CZ';
            break;
        case 'nantong':
            $database = 'MR_NT';
            break;
        case 'suzhou':
            $database = 'MR_SZ';
            break;
        case 'wuxi':
            $database = 'MR_WX';
            break;
        case 'zhenjiang':
            $database = 'MR_ZJ';
            break;
        }

        return $database;

    }//end getMRDatabaseByCity()


    /**
     * 获得NBI城市名
     *
     * @param string $city 中文城市名
     *
     * @return string NBI城市名
     */
    public function getNbiOptions($city)
    {
        $nbiCity = '';
        if ($city == '常州' || $city == '常州市') {
            $nbiCity = 'ERICSSON-CMJS-CZ';
        } else if ($city == '无锡' || $city == '无锡市') {
            $nbiCity = 'ERICSSON-CMJS-WX';
        } else if ($city == '镇江' || $city == '镇江市') {
            $nbiCity = 'ERICSSON-CMJS-ZJ';
        } else if ($city == '南通' || $city == '南通市') {
            $nbiCity = 'ERICSSON-CMJS-NT';
        } else if ($city == '苏州' || $city == '苏州市') {
            $nbiCity = 'ERICSSON-CMJS-SZ';
        }

        return $nbiCity;

    }//end getNbiOptions()


    /**
     * 获得中文城市名
     *
     * @param string $city 数据库连接信息名
     *
     * @return string 中文城市名
     */
    public function getCHCity($city)
    {
        $dbn = $this->getDB('mongs', 'mongs');
        $sql = "select cityChinese from databaseconn where connName='$city'";
        $row = $dbn->query($sql)->fetchcolumn();
        $CHCity = $row;
        return $CHCity;

    }//end getCHCity()


    /**
     * 获得罗马字符城市名
     *
     * @param string $city 中文城市名
     *
     * @return string 罗马字符城市名
     */
    public function getENCity($city)
    {
        $CHCity = '';
        switch ($city) {
        case '常州市':
        case '常州':
            $CHCity = 'changzhou';
            break;
        case '无锡市':
        case '无锡':
            $CHCity = 'wuxi';
            break;
        case '镇江市':
        case '镇江':
            $CHCity = 'zhenjiang';
            break;
        case '南通市':
        case '南通':
            $CHCity = 'nantong';
            break;
        case '苏州市':
        case '苏州':
            $CHCity = 'suzhou';
            break;
        }

        return $CHCity;

    }//end getENCity()


    /**
     * 获得城市着色
     *
     * @return array
     */
    public function getColors()
    {
        $cityArr = [];
        array_push($cityArr, ["常州市", '#ff0000'], ["无锡市", '#00ff00'], ["苏州市", '#0000ff'], ["镇江市", '#f00000'], ["南通市", '#0f0000']);
        return $cityArr;

    }//end getColors()


    /**
     * 根据数据库连接信息名获得城市名
     *
     * @return string 罗马字符城市名
     */
    public function getCityStr()
    {
        // $dsn = "mysql:host=localhost;dbname=mongs";
        // $db = new PDO($dsn, 'root', 'mongs');
        $db = $this->getDB('mongs', 'mongs');
        $cityStr = '';
        $sql = "select connName from databaseconn group by connName";
        $rows = $db->query($sql)->fetchall();
        foreach ($rows as $row) {
            $cityStr = $cityStr . $row['connName'] . ',';
        }

        $cityStr = substr($cityStr, 0, (strlen($cityStr) - 1));
        return $cityStr;

    }//end getCityStr()


    /**
     * 获得中文城市名
     *
     * @param string $ctr CTR数据库名
     *
     * @return string 中文城市名和CTR数据库名映射
     */
    public function getCHAndCtrName($ctr)
    {
        $CHCity = '';
        switch ($ctr) {
        case 'CTR_CZ':
            $CHCity = '常州';
            break;
        case 'CTR_WX':
            $CHCity = '无锡';
            break;
        case 'CTR_ZJ':
            $CHCity = '镇江';
            break;
        case 'CTR_NT':
            $CHCity = '南通';
            break;
        case 'CTR_SZ':
            $CHCity = '苏州';
            break;
        }

        $city = '{"label":"' . $CHCity . '","value":"' . $ctr . '"}';
        return json_decode($city);

    }//end getCHAndCtrName()


    /**
     * 获得中文城市名
     *
     * @param string $MR MR数据库名
     *
     * @return string 中文城市名和MR数据库名映射
     */
    public function getMRToCHName($MR)
    {
        $CHCity = '';
        switch ($MR) {
        case 'MR_CZ':
            $CHCity = '常州';
            break;
        case 'MR_WX':
            $CHCity = '无锡';
            break;
        case 'MR_ZJ':
            $CHCity = '镇江';
            break;
        case 'MR_NT':
            $CHCity = '南通';
            break;
        case 'MR_SZ':
            $CHCity = '苏州';
            break;
        }

        return $CHCity;

    }//end getMRToCHName()


    /**
     * 获得MYSQL数据库连接句柄
     *
     * @param string $db 数据库连接信息
     *
     * @return mixed MYSQL数据库连接句柄
     */
    public function getConnDB($db)
    {
        $config = Config::get("database.connections.$db");
        return @mysql_connect($config['host'], $config['username'], $config['password']);

    }//end getConnDB()

    /**
     * 获得MYSQL数据库连接句柄
     *
     * @param string $db 数据库连接信息
     *
     * @return mixed MYSQL数据库连接句柄
     */
    public function getConnDBi($db, $dbName)
    {
        $config = Config::get("database.connections.$db");
        return @mysqli_connect($config['host'], $config['username'], $config['password'], $dbName);

    }//end getConnDB()

    /**
     * 获得PDO对象
     *
     * @param string $host 主机名或地址
     * 
     * @param string $dbName 数据库名
     * 
     * @param string $username 用户名
     * 
     * @param string $password 密码
     *
     * @return PDO PDO对象
     */
    public function getDBFromWeb($host, $dbName, $username, $password)
    {
        $dsn = "mysql:host=$host;dbname=$dbName";
        $dbn = new PDO($dsn, $username, $password);
        return $dbn;

    }//end getDBFromWeb()


    /**
     * 获得MongoClient对象
     *
     * @param mixed $db MongoDB连接信息
     *
     * @return MongoClient MongoClient对象
     */
    public function getMongoDB($db)
    {
        $aOptions = array(
            'connectTimeoutMS' => 86400000,
            'socketTimeoutMS' => 86400000,
            'readPreference' => \MongoClient::RP_PRIMARY
        );
        $config = Config::get("database.connections.$db");
        $host = $config['host'];
        $port = $config['port'];
        return new MongoClient($host . ':' . $port, $aOptions);

    }//end getMongoDB()


    /**
     * 获得MongoDB属性
     *
     * @param string $db MongoDB连接信息
     *
     * @return array
     */
    public function getMongoDBArr($db)
    {
        $config = Config::get("database.connections.$db");
        $arr = [];
        foreach ($config as $key => $value) {
            $arr[$key] = $value;
        }

        return $arr;

    }//end getMongoDBArr()


    /**
     * 获得中文城市名
     *
     * @param string $CDR CDR数据库名
     *
     * @return string 中文城市名
     */
    public function getCDRToCHName($CDR)
    {
        $CHCity = '';
        switch ($CDR) {
        case 'CDR_CZ':
            $CHCity = '常州';
            break;
        case 'CDR_WX':
            $CHCity = '无锡';
            break;
        case 'CDR_ZJ':
            $CHCity = '镇江';
            break;
        case 'CDR_NT':
            $CHCity = '南通';
            break;
        case 'CDR_SZ':
            $CHCity = '苏州';
            break;
        }

        return $CHCity;

    }//end getCDRToCHName()


    /**
     * Check数据表是否存在
     *
     * @param string $schema 数据库名
     * 
     * @param string $table 数据表名
     *
     * @return boolean
     */
    public function tableIfExists($schema, $table)
    {
        $dbn = $this->getDB('mongs', 'information_schema');
        $sql = "select TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$schema' AND TABLE_NAME='$table'";
        $rs = $dbn->query($sql)->fetchcolumn();
        if ($rs) {
            return true;
        } else {
            return false;
        }

    }//end tableIfExists()


    /**
     * Check数据表中字段是否存在
     *
     * @param string $schema 数据库名
     * 
     * @param string $table 数据表名
     * 
     * @param string $column 列名
     *
     * @return boolean
     */
    public function columnIfExists($schema, $table, $column)
    {
        $dbn = $this->getDB('mongs', 'information_schema');
        $sql = "select TABLE_NAME FROM information_schema.`COLUMNS` WHERE TABLE_SCHEMA = '$schema' AND TABLE_NAME='$table' AND COLUMN_NAME='$column'";
        $rs = $dbn->query($sql)->fetchcolumn();
        return (bool)$rs;

    }//end columnIfExists()


    /**
     * 获得CTR数据库名
     *
     * @param string $ctr 英文城市名
     *
     * @return string CTR数据库名
     */
    public function getENCtrName($ctr)
    {
        $ctrCity = '';
        switch ($ctr) {
        case 'changzhou':
            $ctrCity = 'CTR_CZ';
            break;
        case 'wuxi':
            $ctrCity = 'CTR_WX';
            break;
        case 'zhenjiang':
            $ctrCity = 'CTR_ZJ';
            break;
        case 'nantong':
            $ctrCity = 'CTR_NT';
            break;
        case 'suzhou':
            $ctrCity = 'CTR_SZ';
            break;
        }

        return $ctrCity;

    }//end getENCtrName()


    /**
     * 获取MR数据库列表
     *
     * @return array
     */
    public function getMRDatabases()
    {
        $dataBases = [
            'MR_CZ',
            'MR_NT',
            'MR_SZ',
            'MR_WX',
            'MR_ZJ',
        ];
        return $dataBases;

    }//end getMRDatabases()


    /**
     * 获取城市中文名集合
     *
     * @return array
     */
    public function getCNCityArr()
    {
        $dataBases = [
            '常州',
            '南通',
            '苏州',
            '无锡',
            '镇江',
        ];
        return $dataBases;

    }//end getCNCityArr()


    /**
     * 获得城市英文名列表
     *
     * @return array
     */
    public function getENCityArr()
    {
        $dataBases = [
            'changzhou',
            'nantong',
            'suzhou',
            'wuxi',
            'zhenjiang',
        ];
        return $dataBases;

    }//end getENCityArr()


    /**
     * 获得单个MR数据库名
     *
     * @return string
     */
    public function getOneMRCity()
    {
        return 'MR_CZ';

    }//end getOneMRCity()


    /**
     * 获得ENIQ数据名
     *
     * @param string $city 城市名
     *
     * @return string
     */
    public function getENIQName($city)
    {
        $ctrCity = '';
        switch ($city) {
        case 'changzhou':
            $ctrCity = 'odbc:CZ_ENIQ';
            break;
        case 'wuxi':
            $ctrCity = 'odbc:WX_ENIQ';
            break;
        case 'zhenjiang':
            $ctrCity = 'odbc:ZJ_ENIQ';
            break;
        case 'nantong':
            $ctrCity = 'odbc:NT_ENIQ';
            break;
        case 'suzhou':
            $ctrCity = 'odbc:SZ_ENIQ';
            break;
        case 'nantong1':
            $ctrCity = 'odbc:NT_ENIQ1';
            break;
        case 'changzhou1':
            $ctrCity = 'odbc:CZ_ENIQ1';
            break;
        }//end switch

        return $ctrCity;

    }//end getENIQName()


    /**
     * 获得ENIQ DSN
     *
     * @param mixed $city 城市英文名
     *
     * @return string
     */
    public function getENIQName2G($city)
    {
        $ctrCity = '';
        switch ($city) {
        case 'changzhou':
            $ctrCity = 'odbc:CZ_ENIQ_2G';
            break;
        case 'wuxi':
            $ctrCity = 'odbc:WX_ENIQ_2G';
            break;
        case 'zhenjiang':
            $ctrCity = 'odbc:ZJ_ENIQ_2G';
            break;
        case 'nantong':
            $ctrCity = 'odbc:NT_ENIQ_2G';
            break;
        case 'suzhou':
            $ctrCity = 'odbc:SZ_ENIQ_2G';
            break;
        }

        return $ctrCity;

    }//end getENIQName2G()

    /**
     * 获得CTR入库信息
     *
     * @return Array
     */
    public function getCtrConn($city)
    {
        $arrCity = [];
        switch ($city) {
        case 'changzhou':
            $arrCity = array("strServer" => "10.40.57.190", "strServerPort" => 22, "strServerUsername" => "root", "strServerPassword" => "mongs123", "fileDir" => "/data/trace/ctr/autobackup/changzhou");
            break;
        case 'wuxi':
            $arrCity = array("strServer" => "10.40.61.186", "strServerPort" => 22, "strServerUsername" => "root", "strServerPassword" => "mongs123", "fileDir" => "/data/trace/ctr/autobackup/wuxi");
            break;
        case 'zhenjiang':
            $arrCity = array("strServer" => "10.40.83.221", "strServerPort" => 22, "strServerUsername" => "root", "strServerPassword" => "mongs123", "fileDir" => "/data/trace/ctr");
            break;
        case 'nantong':
            $arrCity = array("strServer" => "10.40.51.185", "strServerPort" => 22, "strServerUsername" => "root", "strServerPassword" => "mongs123", "fileDir" => "/data/trace/ctr/autobackup/");
            break;
        case 'suzhou':
            $arrCity = [];
            break;
        }

        return $arrCity;
    }

    /**
     * 获得task表内最新的kget
     */

    public function getKgetTime(){

        $dbn = $this->getDB('mongs','mongs');
        $sql = "select taskName from task where taskName like 'kget1%' order by endTime desc limit 1 ";
        $res = $dbn->query($sql)->fetch(PDO::FETCH_ASSOC);
        
        return $res['taskName']; 
    }

}//end class
